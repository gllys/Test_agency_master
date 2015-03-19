<?php

class UrbacModule extends CWebModule
{
	public $userClass;
	
	/* @var $userid String The primary column of the users table*/
	public $userId = "userId";
	
	/* @var $username String The username column of the users table*/
	public $username = "username";
	
	public $resourceClass;
	
	public $resourceId;
	
	public $resourceName;
	
	public $resourceLabel;
	
	public $resourceKey;
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'urbac.models.*',
			'urbac.components.*',
			'urbac.helpers.*'
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
