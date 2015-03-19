<?php

/**
 * This is the model class for table "message_receive".
 *
 * The followings are the available columns in table 'message_receive':
 * @property string $id
 * @property string $message_id
 * @property string $to_organization_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $last_updated_source
 */
class MessageReceive extends UActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return MessageReceive the static model class
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
		return 'message_receive';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('message_id, updated_at', 'required'),
			array('last_updated_source', 'numerical', 'integerOnly'=>true),
			array('message_id, to_organization_id', 'length', 'max'=>10),
			array('created_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, message_id, to_organization_id, created_at, updated_at, deleted_at, last_updated_source', 'safe', 'on'=>'search'),
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
			'message_id' => 'Message',
			'to_organization_id' => 'To Organization',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('message_id',$this->message_id,true);
		$criteria->compare('to_organization_id',$this->to_organization_id,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);
		$criteria->compare('last_updated_source',$this->last_updated_source);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}