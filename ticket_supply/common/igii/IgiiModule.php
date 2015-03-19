<?php

/*
 * 简单代码生成工具
 * 
 */

class IgiiModule extends CWebModule {
    /*     * *
     * 过滤IP
     */

    public $ipFilters = array('127.0.0.1', '::1');
    private $_assetsUrl;

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'igii.models.*',
            'igii.components.*',
        ));
    }

    /**
     * @return string the base URL that contains all published asset files of gii.
     */
    public function getAssetsUrl() {
        if ($this->_assetsUrl === null) {
            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('common.igii.assets'), false, -1, YII_DEBUG);
        }
        return $this->_assetsUrl;
    }

    /**
     * @param string $value the base URL that contains all published asset files of gii.
     */
    public function setAssetsUrl($value) {
        $this->_assetsUrl = $value;
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            if (!$this->allowIp(Yii::app()->request->userHostAddress))
                throw new CHttpException(403, "访问非法");
        }
        return true;
    }

    /**
     * Checks to see if the user IP is allowed by {@link ipFilters}.
     * @param string $ip the user IP
     * @return boolean whether the user IP is allowed by {@link ipFilters}.
     */
    protected function allowIp($ip) {
        if (empty($this->ipFilters))
            return true;
        foreach ($this->ipFilters as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos)))
                return true;
        }
        return false;
    }

}