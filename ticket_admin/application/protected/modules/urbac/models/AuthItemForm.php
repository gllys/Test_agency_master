<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 * @author $Author: chengjian $
 * @copyright Copyright &copy; 2009-2011 uuzu
 * @version $Id: AuthItemForm.php 55359 2011-10-16 07:25:12Z chengjian $
 */
class AuthItemForm extends CFormModel
{
	public $name;
	public $type;
	public $description;
	public $bizrule;
	public $data;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('name, type', 'required'),
			array('type', 'numerical'),
			array('name,type,data,description,bizRule','safe')
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>'名称',
			'type'=>'类型',
			'description'=>'描述',
			'bizrule'=>'业务规则',
			'data'=>'数据'
		);
	}
}
