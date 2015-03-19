<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/main';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $nav = null; #主导航
    public $childNav = null; #子导航

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('reset', 'register', 'login', 'error', 'pre', 'smsCode', 'captcha', 'upyunAgent'),
                'users' => array('*'),
            ),
            array('allow', 'users' => array('@')),
            array('deny', 'users' => array('*'))
        );
    }

    public function filters() {
        return array('accessControl',);
    }

    public function init() {
        #表单提交xss过滤
        if (isset($_GET) && $_GET) {
            Filter::htmls($_GET);
        }

        if (isset($_POST) && $_POST) {
            Filter::htmls($_POST);
        }

        #得到主导航菜单
        if (!$this->nav) {
            $this->nav = substr($this->id, 0, strpos($this->id, '/'));
        }

        #得到子导航
        if (!$this->childNav) {
            $this->childNav = CreateUrl::model()->getChildNav('/' . $this->id . '/');
            if (!$this->childNav) {
                $this->childNav = '/' . $this->id . '/';
            }
        }
    }

    //  检测权限
    protected function beforeAction($action) {
        #完善机构信息url
        if (Yii::app()->user->id) {
            $org_id = Yii::app()->user->org_id;
            if ((empty($org_id) || !$org_id) &&
                !in_array($this->id, array('system/organization', 'ajaxServer', 'site'))) {
                $this->redirect('/system/organization/compile');
            } else if (!in_array($this->id, array('system/organization', 'ajaxServer', 'site'))) {
                $rs = Organizations::api()->show(array('id' => $org_id), 0);
                if (ApiModel::isSucc($rs)) {
                    $data = ApiModel::getData($rs);
                    if ($data['status'] == 0) {
                        $this->redirect('/system/organization/compile');
                    }
                } else {
                    $this->redirect('/system/organization/compile');
                }
            }
        }
        $access = $this->getAccess();
        $controllerAccess = $this->getControllerAccess();
        if (!($this->allowedAccess($access) || $this->allowedControllerAccess($controllerAccess)) &&
            (!CreateUrl::model()->checkAccess($this->childNav) || Yii::app()->user->isGuest)) {
            //return true;
            $this->onUnauthorizedAccess($access);
        }
        return true;
    }

    // 获取access 字符串
    protected function getAccess() {
        $mod = $this->module !== null ? $this->module->id . "/" : "";
        $id = strpos($this->id, '/') === false ? ucfirst($this->id) : preg_replace_callback("#/([\w])#", create_function('$matches', 'return ".".strtoupper($matches[1]);'), $this->id);

        return $mod . $id . '.' . ucfirst($this->action->id);
    }

    // 获取controller 字符串
    protected function getControllerAccess() {
        $mod = $this->module !== null ? $this->module->id . "/" : "";
        $id = strpos($this->id, '/') === false ? ucfirst($this->id) : preg_replace_callback("#/([\w])#", create_function('$matches', 'return ".".strtoupper($matches[1]);'), $this->id);

        return $mod . $id;
    }

    // 全站权限检测
    protected function allowedAccess($access) {
        $allows = array('Site.Register', 'Site.Pre', 'Site.SmsCode', 'Site.Reset', 'Site.Login', 'Site.Error', 'Site.Logout', 'Site.Captcha', 'Site.Index', 'Site.UpyunAgent');
        return in_array($access, $allows);
    }

    //全站权限检测
    protected function allowedControllerAccess($access) {
        $allows = array('Site', 'AjaxServer');
        return in_array($access, $allows);
    }

    // 无权限时返回
    protected function onUnauthorizedAccess($access) {
        $cod = '403';
        $message = '你没有权限进行此操作，如有需要，请联系上级管理员提供权限。';

        if (Yii::app()->request->isAjaxRequest)
            $this->_end(1, '没有操作权限!');
        else
            throw new CHttpException($cod, $message);
        return false;
    }

    public function _end($error, $msg, $params = array()) {
        echo CJSON::encode(array('error' => $error, 'msg' => $msg, 'params' => $params));
        Yii::app()->end();
    }

    public function hasItem($name, $show404 = true) {
        $parentItem = Yii::app()->authManager->getAuthItem($name);
        if (empty($parentItem) && $show404)
            throw new CHttpException(404, '没有该权限项');

        return empty($parentItem) ? false : $parentItem->description;
    }

    // 获取 Model 错误信息中的 第一条， 无错误时 返回 null
    public function getModelFirstError($model) {
        $errors = $model->getErrors();
        if (!is_array($errors))
            return $errors;

        $firstError = array_shift($errors);
        if (!is_array($firstError))
            return $firstError;

        return array_shift($firstError);
    }

    // 创建 选择列表
    public function createSelect($data, $params = array(), $default = null) {
        if (!is_array($data) || !is_array($params))
            return '';

        $string = '';
        foreach ($params as $key => $value) {
            $string .= sprintf(' %s="%s"', $key, $value);
        }

        $html = sprintf('<select%s>', $string);

        foreach ($data as $key => $value) {
            $selected = $this->equal($key, $default) ? ' selected="selected"' : '';
            $html .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
        }
        $html .= '</select>';
        return $html;
    }

    public function equal($a, $b) {
        if (!empty($a) || !empty($b))
            return $a == $b;

        if (($a === 0 || $a === '0') && ($b === 0 || $b === '0'))
            return true;
        if (($a === '' || $a === null) && ($b === '' || $b === null))
            return true;
        return false;
    }

    public function tips($content, $style = NULL, $url = NULL) {
        $this->render('/tips', array('content' => $content, 'url' => $url, 'infotitle' => $style));
        Yii::app()->end();
    }

}
