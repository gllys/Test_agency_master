<?php

/**
 * 游族平台所有model都应该继承此类
 * 1、实现分表的功能
 * 2、实现按主键缓存的功能
 * 3、实现按平台选择不台数据库的功能
 * 注意：
 * 1、实例化时不能使用构造函数，应该用model静态方法
 * 2、依赖于UPlatformManager
 * 
 * 一、如果要按平台分库，你需要做：
 * 		1、在config里配置platformManager，
 * 		2、将$isGlobal属性设置为false,
 * 		3、设置$baseDbId属性，
 * 		4、不要自覆盖getDbConnection方法
 * 二、如果要分表，你需要做：
 * 		1、设置$shardingType为你想要的类型，
 * 		2、设置$baseTableName的值，
 * 		3、如果是按某个字段分表的话设置$shardKey属性，
 * 		4、不要覆盖tableName方法
 * 		5、在一些场合，你需要手动调用setShardValue方法告诉UActiveRecord用来进行分表的值（比如说查询场合，UActiveRecord无法根据字段的值自动计算$shardValue）
 * 			如果shardKey是primary,在findbypk前无需要调用setShardValue
 * 		6、必须存在一个以$baseTableName为名的表，因为UAcriveRecord初始化时就要取表结构信息，这个时候UAcriveRecord无法获取足够的信息决定去读哪一张表。
 * 		
 * 三、如果要缓存findbypk的结果，你需要做设置$cacheId
 * 
 * @package common.components
 */
Yii::import("common.extensions.CDbShardType", true);

class UActiveRecord extends CActiveRecord {

    protected $dbId = 'db';
    protected $cacheId = 'cache';
    protected $isGlobal = true;

    /**
     * 如需分表逻辑，设置这个置，和cdbShardManager相关联
     *
     * @var string
     */
    protected $modelName;

    /**
     * 用来分表的字段,不设置表示禁用通过字段来分表。
     *
     * @var string
     */
    protected $shardKey;

    /**
     * 暂存用来分表的值
     *
     * @var string
     */
    protected $_shardValue;

    /**
     * 配置如何分表
     * 格式为
     * array(
     * 		'class'=>'shardTypeMd5',
     * 		'len'=>1,
     * 		...
     * )
     *
     * @var mixed
     */
    protected $shardingType;

    /**
     * 表基本名，如果$shardingType不为null的话，会根据分表类型的计算规则计算出最终的表名
     * 如果需要表前缀的功能，请用{{}}包裹
     *
     * @var string
     */
    protected $tableName;

    /**
     * 返回activeRecore的数据库连接
     * 如果$baseDbId没有设置则回返回app对象的db组件
     * 如果$baseDbId设置了并且$isGlobal不为true的话，会返回app的{$platform}_{$baseDbId}(@see UPlatformManager)组件
     * 如果$baseDbId设置了并且$isGlobal为true的话，会返回app的{$baseDbId}组件
     * @return CDbConnection the database connection used by active record.
     */
    public function getDbConnection() {
        if ($this->dbId == null) {
            self::$db = parent::getDbConnection();
        } elseif (!$this->isGlobal) {
            $db = Yii::app()->platformManager->platform . "_" . $this->dbId;
            self::$db = Yii::app()->$db;
        } elseif ($this->isGlobal) {
            $db = $this->dbId;
            self::$db = Yii::app()->$db;
        }
        if (self::$db instanceof CDbConnectionExt)
            self::$db->shardValue = $this->shardValue;
        if (self::$db instanceof CDbConnection)
            return self::$db;
        else
            throw new CDbException(Yii::t('yii', 'Active Record requires a "db" CDbConnection application component.'));
    }

    public function tableName() {
        if ($this->shardingType == null && !$this->tableName) {
            return Yii::app()->dbShardManager->getTable($this->modelName, $this->shardValue);
        } elseif (!isset($this->shardingType['class'])) {
            throw new CException('shardingType需要配置一个class');
        } else {
            $shardingType = $this->shardingType;
            $className = Yii::import($shardingType['class'], true);
            unset($shardingType['class']);
            $shardManager = new $className();
            if (!$shardManager instanceof CDbShardType) {
                throw new Exception('shardingType必须是UshardType的子类');
            }
            foreach ($shardingType as $name => $value)
                $shardManager->$name = $value;

            $shardValue = $this->getShardValue();
            if ($shardValue === null)
                return $this->tableName;
            $tableName = $shardManager->getTableName($this->tableName, $shardValue);
            return $tableName;
        }
    }

    /**
     * 手动设置分表值
     *
     * @param string $value
     * @return UActiveRecord
     */
    public function setShardValue($value) {
        $this->_shardValue = $value;
        $this->refreshMetaData();

        return $this;
    }

    /**
     * 获取分表值
     *
     * @return string
     */
    public function getShardValue() {
        if ($this->_shardValue !== null)
            return $this->_shardValue;
        elseif ($this->shardKey != null) {
            $shardKey = $this->shardKey;
            if (!isset($this->$shardKey))
                return null;
//			$this->setShardValue($this->$shardKey);
            return $this->_shardValue = $this->$shardKey;
//			return $this->_shardValue;
        } else
            return null;
    }

    /**
     * 获取缓存组件
     * 如果$cacheId没有设置则回返回nul，表示不进行对象缓存
     * 如果$cacheId设置了并且$isGlobal不为true的话，会返回app的{$platform}_{$cacheId}(@see UPlatformManager)组件
     * 如果$cacheId设置了并且$isGlobal为true的话，会返回app的{$cacheId}组件
     * @return CDbConnection the database connection used by active record.
     *
     */
    protected function getCache() {
        if (!$this->cacheId)
            return null;
        elseif (!$this->isGlobal) {
            $cacheId = Yii::app()->platformManager->platform . "_" . $this->cacheId;
        } elseif ($this->isGlobal) {
            $cacheId = $this->cacheId;
        }
        if (!isset(Yii::app()->$cacheId))
            return null;
        $cache = Yii::app()->$cacheId;
        if ($cache instanceof CCache)
            return $cache;
        else
            throw new CException(Yii::t('yii', 'Active Record requires a "cache" CCache application component.'));
    }

    /**
     * 保存删除缓存
     *
     */
    public function afterSave() {
        parent::afterSave();
        if ($this->cache !== null) {
            $key = get_class($this) . "_" . $this->primaryKey;
            $this->cache->delete($key);
        }
    }

    /**
     * 根据主键查询
     *
     * @param string $pk
     * @param mixd $condition
     * @param mixd $params
     * @return model
     */
    public function findByPk($pk, $condition = '', $params = array()) {
        if ($this->tableSchema->primaryKey == $this->shardKey)
            $this->setShardValue($pk);
        if ($this->cache !== null) {
            $key = get_class($this) . "_" . $pk;
            $data = $this->cache->get($key);
            if (empty($data)) {
                $data = parent::findByPk($pk, $condition, $params);
                $this->cache->set($key, $data);
            }
            return $data;
        } else {
            return parent::findByPk($pk, $condition, $params);
        }
    }

    public function save($runValidation = true, $attributes = null) {
        if (!empty($this->shardKey)) {
            $shardValue = $this->{$this->shardKey};
//			echo "shardValue=".$shardValue;exit;
            $this->setShardValue($shardValue);
        }
        return parent::save($runValidation = true, $attributes = null);
    }

    /**
     * 手动清除缓存
     */
    public function deleteCache($pk, $nsDelete = true) {
        if ($this->cache !== null) {
            //删除主键缓存
            $key = get_class($this) . "_" . $pk;
            $this->cache->delete($key);

            //删除列表缓存和其它缓存
            if ($nsDelete) {
                //$this->setCacheNS();
            }
        }
    }

    //清除主键更新缓存 *自动主键分表，支持手动其它分表
    public function updateByPk($pk, $attributes, $condition = '', $params = array()) {
        if ($this->tableSchema->primaryKey == $this->shardKey)
            $this->setShardValue($pk);
        $rs = parent::updateByPk($pk, $attributes, $condition, $params);
        $this->deleteCache($pk);
        return $rs;
    }

}
