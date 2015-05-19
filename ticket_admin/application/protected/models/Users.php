<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property string $id
 * @property string $account
 * @property string $password
 * @property string $name
 * @property string $gender
 * @property string $email
 * @property string $mobile
 * @property string $telephone
 * @property string $birthday
 * @property string $identity
 * @property string $position
 * @property string $biography
 * @property integer $status
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $is_super
 * @property integer $is_guide
 * @property integer $last_updated_source
 */
class Users extends UActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Users the static model class
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
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account', 'unique'),
            array('account', 'match', 'pattern'=>'/^[a-z0-9\-_]+$/','message'=>'账号不合法，必须为字母数字或下划线！'),  
			array('account, password, mobile', 'required'),
            array('mobile', 'match', 'pattern' => "/^1\d{10}$/", 'allowEmpty'=>false, 'message' => "手机号码格式不正确"),
			array('account', 'length', 'min'=>3, 'max'=>30),
			array('status, is_super, is_guide, last_updated_source', 'numerical', 'integerOnly'=>true),
			array('account, password, name, email, mobile, telephone, identity, position', 'length', 'max'=>100),
			array('created_by', 'length', 'max'=>10),
			array('gender', 'length', 'max'=>6),
			array('birthday, sell_role,biography, is_delete, created_at, deleted_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account, password, name, gender, email, mobile, telephone, birthday, identity, position, biography, status, is_delete, created_by, created_at, updated_at, deleted_at, is_super, is_guide, last_updated_source', 'safe', 'on'=>'search'),
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
			'account' => '用户名',
			'password' => '密码',
			'repassword' => '确认密码',
			'name' => 'Name',
			'sell_role' => '角色',
			'gender' => 'Gender',
			'email' => 'Email',
			'mobile' => '手机号',
			'telephone' => 'Telephone',
			'birthday' => 'Birthday',
			'identity' => 'Identity',
			'position' => 'Position',
			'biography' => 'Biography',
			'status' => 'Status',
			'created_by' => 'Created By',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
			'is_super' => 'Is Super',
			'is_guide' => 'Is Guide',
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
		$criteria->compare('account',$this->account,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sell_role',$this->sell_role,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('identity',$this->identity,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('biography',$this->biography,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_by',$this->created_by,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('deleted_at',$this->deleted_at,true);
		$criteria->compare('is_super',$this->is_super);
		$criteria->compare('is_guide',$this->is_guide);
		$criteria->compare('last_updated_source',$this->last_updated_source);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
