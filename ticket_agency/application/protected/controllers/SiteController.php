<?php
require_once dirname(__FILE__) . '/../extensions/phpPasswordHashingLib/passwordLib.php';

class SiteController extends Controller {

    public $bm = 'passport';

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(// captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'height' => 23,
                'width' => 50,
                'padding' => 0,
                'transparent' => false,
                'minLength' => 4,
                'maxLength' => 4,
				'testLimit'=> 30,
            ), // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array('class' => 'CViewAction')
        );
    }

    public function actionIndex() {
        $redirectChild = CreateUrl::model()->getRedirectOne();
        if (empty($redirectChild['params'])) {
            $this->onUnauthorizedAccess('');
        }
        $this->redirect($redirectChild['params']['href']);
    }

    public function actionHeader($index) {
        $redirectChild = CreateUrl::model()->getListOne($index);
        $this->redirect($redirectChild['params']['href']);
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if (Yii::app()->user->isGuest) {
            $this->redirect('/site/login');
        }

        if ($error = Yii::app()->errorHandler->error) {
            //echo $error['message'];
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else {
                $this->layout = 'column_error';
                $this->render('error', $error);
            }
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $headers = "From: {$model->email}\r\nReply-To: {$model->email}";
                mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    public function actionRegister() {
        $user = null;
        if (isset($_POST['RegisterForm'])) {
            $attributes = $_POST['RegisterForm'];
            if ($attributes['code'] == Yii::app()->redis->get('code_for_register:' . Yii::app()->getSession()->getSessionId())) {
                unset($attributes['code']);
                $identity = new UserIdentity($attributes['account'], $attributes['password']);
				Users::$verifycodeAllowEmpty = false;
                $user = new Users();

                $user['password'] = $identity->getHashedPassword($attributes['password']);
                $user['repassword'] = $identity->getHashedPassword($attributes['repassword']);
                $user['account'] = $attributes['account'];
                $user['mobile'] = $attributes['mobile'];
                $user['verifycode'] = $attributes['verifycode'];
                $user['created_at'] = date('Y-m-d H:i:s');
                $user['updated_at'] = date('Y-m-d H:i:s');
                $user['is_super'] = 1;

				if($user->save()) {					
					Yii::import("common.models.ULoginForm");
					$model = new ULoginForm;
					$model->attributes = array(
						'username' => $attributes['account'],
						'password' => $attributes['password']
					);
					$this->validate_and_login($model);
					Yii::app()->end();
				}
            }
        }
        $this->renderPartial('reg_user', array('user' => $user));
    }

    public function actionSmsCode() {
        $mobile = Yii::app()->request->getParam('mobile');
        $type = Yii::app()->request->getParam('type');
        $code = mt_rand(100000, 999999);
        if($type=='1') { 
            $str_type = '提现验证码 '; 
            $str_name = 'fetchcash';
        }else{ 
            $str_type = '注册验证码 ';
            $str_name = 'register';
        }
        $SmsHandler = new SMS();
        if ($SmsHandler->sendSMS($mobile, $str_type . $code)) {
            Yii::app()->redis->setEx('code_for_'.$str_name.':' . Yii::app()->getSession()->getSessionId(), 600, $code);
            echo 1; //$code;
        } else {
            echo 0;
        }
    }

    public function actionPre() {
        $chk = Yii::app()->request->getParam('chk');
        $chk = substr($chk, 4);
		Users::$verifycodeAllowEmpty = false;
        $user = new Users();
        $val = Yii::app()->request->getParam('val');
        $val = trim($val);
        if ($chk == 'code') {
            if (strlen($val) != 6 || $val != Yii::app()->redis->get('code_for_register:' . Yii::app()->getSession()->getSessionId())) {
                echo '短信验证码输入错误';
                Yii::app()->end();
            }
        } else {
            $user[$chk] = $val;
            $user->validate();
        }
        echo isset($user->errors[$chk]) ? $user->errors[$chk][0] : 'ok';
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        if (!Yii::app()->user->isGuest) {
            $this->redirect('/');
        }
        
        $rec = Recommend::api()->lists(array('pos_id'=>1,'expire_time'=>'true','status'=>1,'items'=>10000));
        $rec = $rec['body']['data'];
        
        Yii::import("common.models.ULoginForm");
        $model = new ULoginForm;
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['ULoginForm'])) {
            $model->attributes = $_POST['ULoginForm'];
            $this->validate_and_login($model);
        }

        // display the login form
        if (isset($_GET['return_url']))
        	$this->renderPartial('rlogin', array('model' => $model,'rec'=>$rec,'count'=>count($rec)));
        else 
        	$this->renderPartial('login', array('model' => $model,'rec'=>$rec,'count'=>count($rec)));
    }

    private function validate_and_login(&$model) {
        // validate user input and redirect to the previous page if valid
        if ($model->validate() && $model->login()) {
//            $fields = array('id', 'account', 'name', 'organization_id', 'is_super', 'created_at');
//            $result = Users::model()->find(array(
//                'select' => $fields,
//                'condition' => 'account=:account AND deleted_at IS NULL',
//                'params' => array(':account' => Yii::app()->getUser()->account),
//            ));
//            $u = Yii::app()->user->account;
//            foreach ($fields as $field) {
//                $info[$field] = $result->$field;
//            }

//            $session_id = Yii::app()->getSession()->getSessionId();
//            Yii::app()->redis->hMset('session_' . $session_id, $info);
//            Yii::app()->redis->expire('session_' . $session_id, 3600 * 24);
            $this->redirect('/');
        }
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * 重设密码
     * @author grg
     */
    public function actionReset() {
        $fields = array('id', 'account', 'password', 'mobile');
        if (Yii::app()->request->getIsAjaxRequest()) {
            $action = Yii::app()->request->getParam('act');
            $account = Yii::app()->request->getParam('account');
            $user = Users::model()->find(array(
                'select' => $fields,
                'condition' => 'account=:account AND deleted_at IS NULL',
                'params' => array(':account' => trim($account)),
            ));
            if ($action == 'code') {
                if (is_null($user)) {
                    echo json_encode(array(
                        'code' => -1,
                        'msg' => '用户名不存在！'
                            ), JSON_UNESCAPED_UNICODE);
                    Yii::app()->end();
                }
                $code = mt_rand(1000000, 9999999);
                $SmsHandler = new SMS();
                if (strlen($user['mobile']) != 11) {
                    echo json_encode(array(
                        'code' => 0,
                        'msg' => '注册时手机号填写不正确！'
                            ), JSON_UNESCAPED_UNICODE);
                    Yii::app()->end();
                } else if ($SmsHandler->sendSMS($user['mobile'], '重设密码验证码 ' . $code)) {
                    Yii::app()->redis->setEx('code_for_reset:' . $account, 600, $code);
                    echo json_encode(array(
                        'code' => 1,
                        'msg' => '验证码已发送至！' . substr_replace($user['mobile'], '*****', 3, 5)
                            ), JSON_UNESCAPED_UNICODE);
                    Yii::app()->end();
                } else {
                    echo json_encode(array(
                        'code' => 0,
                        'msg' => '验证码发送失败！'
                            ), JSON_UNESCAPED_UNICODE);
                    Yii::app()->end();
                }
            } elseif ($action == 'verify') {
                //todo
            }
            Yii::app()->end();
        } elseif (Yii::app()->request->getIsPostRequest()) {
            $reset = Yii::app()->request->getParam('UResetForm');
            $account = $reset['tnuocca'];
            $password = $reset['drowssap'];
            $code = $reset['edoc'];
            $user = Users::model()->find(array(
                'select' => $fields,
                'condition' => 'account=:account AND deleted_at IS NULL',
                'params' => array(':account' => trim($account)),
            ));
//            if (is_null($user)) {
//                echo json_encode(array(
//                    'code' => -1,
//                    'msg' => '用户名不存在！'
//                        ), JSON_UNESCAPED_UNICODE);
//                Yii::app()->end();
//            }
            $code_err = null;
			$password_err = null;
			if(strlen($password) != 6) {
                $password_err = '密码必须为6位';
			} else {				
				if ($code != '' && $code == Yii::app()->redis->get('code_for_reset:' . $account)) {
					$user['password'] = $password;
					if ($user->validate()) {
						$user['password'] = password_hash($password, PASSWORD_BCRYPT, array('cost' => 8));
						if ($user->save()) {
							$this->redirect('/site/login');
						}
					}
				} else {
					$code_err = '验证码不正确';
				}
			}
            $this->renderPartial('reset', array('user' => $user, 'code_err' => $code_err, 'password_err'=>$password_err));
            Yii::app()->end();
        } else {
            $this->renderPartial('reset');
        }
    }

    public function actionEditorImageUpload() {
        try {
            //			$width = empty($_POST['imgWidth'])?640:intval($_POST['imgWidth']);
            //			$height = empty($_POST['imgHeight'])?0:intval($_POST['imgHeight']);
            ////			$siteId = $this->siteId;
            //			$thumbSizeSettings = array(array('width'=>$width,'height'=>$height));
            $thumbSizeSettings = array();
            $image = Yii::app()->UFile->uploadImage("imgFile", $thumbSizeSettings);

            //			$site = Site::model()->findByPk($siteId);
            //			if(empty($site)) throw new CHttpException(500,'找不到网站');

            $images = Yii::app()->UFile->getThumb($image, 'http://' . Yii::app()->UFile->rsync_module);
            $url = $images[0];
            echo CJSON::encode(array('error' => 0, 'url' => $url));
        } catch (Exception $e) {
            echo CJSON::encode(array('error' => 1, 'message' => $e->getMessage()));
        }
        Yii::app()->end();
    }

    /*     * *****
     * 又拍云表单提交代理
     * **** */

    public function actionUpyunAgent() {
        $model = array('code', 'message', 'url', 'time', 'image-width', 'image-height', 'image-frames', 'image-type');
        foreach ($model as $val)
            if (!isset($_GET[$val])) {
                echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"参数不全上传失败"});</script>';
                Yii::app()->end();
            }
        if (md5("{$_GET['code']}&{$_GET['message']}&{$_GET['url']}&{$_GET['time']}&" . Yii::app()->upyun->formApiSecret) != $_GET['sign']) {
            echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"密钥不正确上传失败"});</script>';
            Yii::app()->end();
        }

        echo '<script type="text/javascript">parent.upload_callback({status:200,msg:"' . Yii::app()->upyun->host . $_GET['url'] . '"});</script>';
        Yii::app()->end();
    }

}
