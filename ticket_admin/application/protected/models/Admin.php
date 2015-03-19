<?php

/**
 * This is the model class for table "admin".
 *
 * The followings are the available columns in table 'admin':
 * @property string $id
 * @property integer $rid
 * @property string $account
 * @property string $password
 * @property string $salt
 * @property string $name
 * @property string $gender
 * @property string $email
 * @property string $mobile
 * @property string $role_id
 * @property integer $status
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $is_super
 * @property integer $last_updated_source
 */
class Admin extends UActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Admin the static model class
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
		return 'admin';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account, password, updated_at', 'required'),
			array('rid, status, last_updated_source', 'numerical', 'integerOnly'=>true),
			array('account, password, name, email, mobile', 'length', 'max'=>100),
			array('salt', 'length', 'max'=>64),
			array('gender', 'length', 'max'=>6),
			array('role_id, created_by', 'length', 'max'=>10),
			array('is_super', 'length', 'max'=>1),
			array('created_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, rid, account, password, salt, name, gender, email, mobile, role_id, status, created_by, created_at, updated_at, deleted_at, is_super, last_updated_source', 'safe', 'on'=>'search'),
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
			'rid' => 'Rid',
			'account' => 'Account',
			'password' => 'Password',
			'salt' => 'Salt',
			'name' => 'Name',
			'gender' => 'Gender',
			'email' => 'Email',
			'mobile' => 'Mobile',
			'role_id' => 'Role',
			'status' => 'Status',
			'created_by' => 'Created By',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
			'is_super' => 'Is Super',
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
		$criteria->compare('rid',$this->rid);
		$criteria->compare('account',$this->account,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('role_id',$this->role_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_by',$this->created_by,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);
		$criteria->compare('is_super',$this->is_super,true);
		$criteria->compare('last_updated_source',$this->last_updated_source);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}