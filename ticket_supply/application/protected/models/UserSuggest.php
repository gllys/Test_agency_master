<?php

/**
 * This is the model class for table "user_suggest".
 *
 * The followings are the available columns in table 'user_suggest':
 * @property integer $id
 * @property integer $organization_id
 * @property string $content
 * @property integer $state
 * @property integer $user_id
 * @property string $user_account
 * @property string $user_name
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $last_updated_source
 */
class UserSuggest extends UActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserSuggest the static model class
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
		return 'user_suggest';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('organization_id, updated_at', 'required'),
			array('organization_id, state, user_id, last_updated_source', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>512),
			array('user_account', 'length', 'max'=>100),
			array('user_name', 'length', 'max'=>50),
			array('created_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, organization_id, content, state, user_id, user_account, user_name, created_at, updated_at, deleted_at, last_updated_source', 'safe', 'on'=>'search'),
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
			'organization_id' => 'Organization',
			'content' => 'Content',
			'state' => 'State',
			'user_id' => 'User',
			'user_account' => 'User Account',
			'user_name' => 'User Name',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
			'last_updated_source' => 'Last Updated Source',
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
		$criteria->compare('organization_id',$this->organization_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('state',$this->state);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('user_account',$this->user_account,true);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);
		$criteria->compare('last_updated_source',$this->last_updated_source);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}