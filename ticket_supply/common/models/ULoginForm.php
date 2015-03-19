<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 * @author $Author: chengjian $
 * @copyright Copyright &copy; 2009-2011 uuzu
 * @version $Id: ULoginForm.php 55362 2011-10-16 14:46:05Z chengjian $
 */
class ULoginForm extends CFormModel
{
	public $username;
	public $password;
	public $verifyCode;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required','message'=>'请输入用户名和密码'),
			// rememberMe needs to be a boolean
			// password needs to be authenticated
			array('password', 'authenticate','message'=>'用户名或密码错误'),
//			array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')),

		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>'用户名',
			'password'=>'密码'
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate()){
                            if($this->_identity->errorCode  == 3 ){
				$this->addError('password','用户未启用，请与系统管理员联系');
                            }else{
                                $this->addError('password','用户名或密码错误');
                            }
                        }
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			Yii::app()->user->login($this->_identity,3600*24);
			return true;
		}
		else {
			Yii::app()->user->logout();
			return false;
		}
	}
}
