<?php

/**
 * This is the model class for table "log".
 *
 * The followings are the available columns in table 'log':
 * @property integer $id
 * @property string $user_account
 * @property string $user_name
 * @property string $controller
 * @property string $action
 * @property string $url
 * @property string $param
 * @property string $data
 * @property string $msg
 * @property integer $dateline
 */
class Log extends UActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Log the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dateline', 'required'),
			array('dateline', 'numerical', 'integerOnly'=>true),
			array('user_account, user_name', 'length', 'max'=>30),
			array('controller, action', 'length', 'max'=>50),
			array('url', 'length', 'max'=>100),
			array('param, data, msg', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_account, user_name, controller, action, url, param, data, msg, dateline', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_account' => 'User Account',
			'user_name' => 'User Name',
			'controller' => 'Controller',
			'action' => 'Action',
			'url' => 'Url',
			'param' => 'Param',
			'data' => 'Data',
			'msg' => 'Msg',
			'dateline' => 'Dateline',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_account',$this->user_account,true);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('controller',$this->controller,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('param',$this->param,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('dateline',$this->dateline);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}