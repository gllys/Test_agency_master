<?php

/**
 * This is the model class for table "user_message_status".
 *
 * The followings are the available columns in table 'user_message_status':
 * @property string $id
 * @property string $user_id
 * @property string $message_id
 * @property string $user_type
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $last_updated_source
 * @property string $sync_id
 */
class UserMessageStatus extends UActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserMessageStatus the static model class
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
		return 'user_message_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, message_id, user_type, created_at', 'required'),
			array('last_updated_source', 'numerical', 'integerOnly'=>true),
			array('user_id, message_id', 'length', 'max'=>10),
			array('user_type', 'length', 'max'=>6),
			array('status', 'length', 'max'=>7),
			array('sync_id', 'length', 'max'=>40),
			array('created_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, message_id, user_type, status, created_at, updated_at, deleted_at, last_updated_source, sync_id', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'message_id' => 'Message',
			'user_type' => 'User Type',
			'status' => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
			'last_updated_source' => 'Last Updated Source',
			'sync_id' => 'Sync',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('message_id',$this->message_id,true);
		$criteria->compare('user_type',$this->user_type,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);
		$criteria->compare('last_updated_source',$this->last_updated_source);
		$criteria->compare('sync_id',$this->sync_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
