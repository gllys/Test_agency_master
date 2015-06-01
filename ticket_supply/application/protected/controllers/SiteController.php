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
        $redirectUrl = '/#'.$redirectChild['params']['href'] ;
        $this->render('index',  compact('redirectUrl'));
    }

    public function actionHeader($index) {
        $redirectChild = CreateUrl::model()->getListOne($index);
        $this->redirect($redirectChild['params']['href']);
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if (!Yii::app()->user->id) {
            $this->redirect('site/login');
        }

        if ($error = Yii::app()->errorHandler->error) {
            //echo $error['message'];
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message']; else {
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
		$this->renderPartial('reg_user');
	}
	
	// 验证手机短信
	private function mobileCode($userCode, $mobile) {
		$returnArr = array();
		$mobileCodeAndMobile = Yii::app()->redis->get('code_for_register:' . Yii::app()->getSession()->getSessionId());
		$mobileCodeAndMobile = explode(',', $mobileCodeAndMobile);
		if($userCode != $mobileCodeAndMobile[0]) {
			$returnArr[1] = false;
			$returnArr[2] = "验证码输入错误";
		} else if($userCode == $mobileCodeAndMobile[0] && $mobile != $mobileCodeAndMobile[1]) {
			$returnArr[1] = false;
			$returnArr[2] = "验证码与手机号不对应，请重新接受验证码或更改手机号";
		} else {
			$returnArr[1] = true;
		}
		return $returnArr;
	}
	// ajax验证用户信息和注册
	public function actionValidatereg() {
		
		$returnArr = array();
		if(isset($_POST["fieldValue"]) && isset($_POST["fieldId"])) {
			$attribute = $_POST["fieldId"];
			$returnArr[0] = $attribute;
			// 如果attributes是code，说明验证手机验证码，同时验证手机号和对应的手机验证码
			if($attribute == "code") {	//验证短信
				$userCode = $_POST["fieldValue"];
				$mobile = $_POST["mobile"];
				$returnArr = array_merge($returnArr, $this->mobileCode($userCode, $mobile));
			} else {
				Users::$verifycodeAllowEmpty = false;
				$users = new Users();
				$users->$attribute = $_POST["fieldValue"];

				if($users->validate(array($attribute))) {
					$returnArr[1] = true;
				} else {
					$returnArr[1] = false;
				}
			}
		} else if (isset($_POST['RegisterForm'])){	// 提交时验证注册信息同时进行登录
            $attributes = $_POST['RegisterForm'];
			$identity = new UserIdentity($attributes['account'], $attributes['password']);
			Users::$verifycodeAllowEmpty = false;
			$user = new Users();
            $attributes = $_POST['RegisterForm'];
			$user['password'] = $identity->getHashedPassword($attributes['password']);
			$user['repassword'] = $identity->getHashedPassword($attributes['repassword']);
			$user['account'] = $attributes['account'];
			$user['mobile'] = $attributes['mobile'];
			$user['verifycode'] = $attributes['verifycode'];
			$user['created_at'] = date('Y-m-d H:i:s');
			$user['updated_at'] = date('Y-m-d H:i:s');
			$user['is_super'] = 1;
			
			$errors = $user->getErrors();
			$attributekeys = array_keys($attributes);
			$noError = $user->validate($attributekeys);
			foreach($attributekeys as $attributekey) {
				$ret = array();
				if(array_key_exists($attributekey, $errors)) {
					$ret[0] = $attributekey;
					$ret[1] = false;
					$noError = false;
					$ret[2] = isset($errors[$attributekey][0]) ? $errors[$attributekey][0] : "";
				} else {
					$ret[0] = $attributekey;
					if($attributekey == "code") {
						$userCode = $attributes["code"];
						$mobile = $attributes["mobile"];
						$ret = array_merge($ret, $this->mobileCode($userCode, $mobile));
						$noError = $ret[1];	// 验证码是否错误
					} else {
						$ret[1] = true;
					}
				}
				$returnArr[] = $ret;
			}
			// 验证是否有错误
			if ($noError && $user->insert()) {
				Yii::import("common.models.ULoginForm");
				$model = new ULoginForm;
				$model->attributes = array(
					'username' => $attributes['account'],
					'password' => $attributes['password']
				);
				$model->validate() && $model->login();
			} else {

			}
		}
		echo json_encode($returnArr);
	}
	
    public function actionSmsCode() {
        $mobile = Yii::app()->request->getParam('mobile');
        $type = Yii::app()->request->getParam('type');
        $code = mt_rand(100000, 999999);
        if($type=='1') {
            $types = 4;
            $str_type = '提现验证码 '; 
            $str_name = 'fetchcash';
        }else{
            $types = 2;
            $str_type = '注册验证码 ';
            $str_name = 'register';
        }
        $SmsHandler = new SMS();
       //var_dump($SmsHandler->sendSMS($mobile, $str_type . $code));die;
         if ($SmsHandler->sendSMS($mobile, $str_type . $code,$types)) {
            Yii::app()->redis->setEx('code_for_'.$str_name.':' . Yii::app()->getSession()->getSessionId(), 600, $code.','.$mobile);
            echo json_encode(array("code"=>"succ", "message"=>"发送信息成功"));
        } else {
            echo json_encode(array("code"=>"fail", "message"=>"发送信息失败"));
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        if (!Yii::app()->user->isGuest) {
            $this->redirect('/');
        }
        
        if(!isset($_POST['ajax']) && !isset($_POST['ULoginForm'])&&Yii::app()->request->getIsAjaxRequest()){
            $this->_end(3,'你已经退出') ;
        }
        
        $rec = Recommend::api()->lists(array('pos_id'=>1,'expire_time'=>'true','status'=>1,'items'=>10000),true,30);
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
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
//                $fields = array('id', 'account', 'name', 'organization_id', 'is_super', 'created_at');
//                $result = Users::model()->find(array(
//                    'select' => $fields,
//                    'condition' => 'account=:account AND deleted_at IS NULL',
//                    'params' => array(':account' => Yii::app()->getUser()->account),
//                ));
//                foreach ($fields as $field) {
//                    $info[$field] = $result->$field;
//                }
//
//                $session_id = Yii::app()->getSession()->getSessionId();
//                Yii::app()->redis->hMset('session_' . $session_id, $info);
//                Yii::app()->redis->expire('session_' . $session_id, 3600 * 24);
                $this->redirect('/');
            }
        }

        // display the login form
        if (isset($_GET['return_url']))
        	$this->renderPartial('rlogin', array('model' => $model,'rec'=>$rec,'count'=>count($rec)));
        else 
        	$this->renderPartial('login', array('model' => $model,'rec'=>$rec,'count'=>count($rec)));
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
				$errFlag = false;
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
            $account = $reset['account'];
            $password = $reset['password'];
            $code = $reset['code'];
            $user = Users::model()->find(array(
                'select' => $fields,
                'condition' => 'account=:account AND deleted_at IS NULL',
                'params' => array(':account' => trim($account)),
            ));
            if (is_null($user)) {
//                echo json_encode(array(
//                    'code' => -1,
//                    'msg' => '用户名不存在！'
//                        ), JSON_UNESCAPED_UNICODE);
//                Yii::app()->end();
            }
            $code_err = null;
			$password_err = null;
			if(strlen($password) < 6 || strlen($password) > 16) {
                $password_err = '密码必须为6-16位';
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
        foreach ($model as $val){
            if (!isset($_GET[$val])) {
                echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"参数不全上传失败"});</script>';
                Yii::app()->end();
            }
        }
        
        if($_GET['code']!=200){
             echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"'.$_GET['message'].'"});</script>';
            Yii::app()->end();
        }    
        
//        if (md5("{$_GET['code']}&{$_GET['message']}&{$_GET['url']}&{$_GET['time']}&" . Yii::app()->upyun->formApiSecret) != $_GET['sign']) {
//            echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"密钥不正确上传失败"});</script>';
//            Yii::app()->end();
//        }

        echo '<script type="text/javascript">parent.upload_callback({status:200,msg:"' . Yii::app()->upyun->host . $_GET['url'] . '"});</script>';
        Yii::app()->end();
    }

}
