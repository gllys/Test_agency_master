<?php
/**
 * 数据库操作模型基类
 *
 * 2012-12-12 1.0 lizi 创建
 *
 * @author  lizi
 * @version 1.0
 */
class Model
{
	private $link   = NULL;    // 数据库连接句柄
	private $query  = NULL;    // 查询句柄
	// private $db     = NULL;    // db
	private $chipId = NULL;    // 分库ID
	public  $load   = NULL;    // load类实例
	public  $transDepth = 0;
	protected $keyField = null;

	/**
	 * 构造函数,自动连接数据库
	 */
	function __construct($chipId = 0)
	{
		$this->chipId = $chipId;
		if (!isset(PI::$data['db'][$this->db])) $this->_getDB($this->db);
		$this->load = new Load();
	}

	/**
	 * 得到实例对象
	 */
	private function _getDB($db = 'default')
	{
		// 获取数值
		$dbs = unserialize(PI_DBS);
		$dba = $dbs[$db];

		// 数据检验
		if(!is_array($dba)) die('err:dba');
		if(PI::$data['debug']) $beginTime = microtime(TRUE);

		// 链接数据库服务器
		$host       = empty($dba['port']) ? $dba['host'] : $dba['host'].':'.$dba['port'];
		$this->link = mysql_connect($host, $dba['user'], $dba['password'], true) or die("err:not connect".mysql_error());
		mysql_query('SET NAMES UTF8', $this->link);

		// 链接数据库
		mysql_select_db($dba['database'], $this->link) or die("err:db select db".mysql_error());

		// DEBUG
		if(PI::$data['debug']) PI::$data['flow']['db'][] = array('sql'=>'Connect DB Server.', 'time'=>microtime(TRUE)-$beginTime);

		// 赋值
		PI::$data['db'][$db]   = true;
		PI::$data['link'][$db] = $this->link;

		// 输出
		return true;
	}

	/**
	 * 统一执行SQL (debug模式未加)
	 */
	public function query($sql)
	{
		if(PI::$data['debug']) $beginTime = microtime(TRUE);
		$this->query = mysql_query($sql, PI::$data['link'][$this->db]) or die("err:db query ".mysql_error().' sql:'.$sql);
		// $this->query = mysql_query($sql, PI::$data['link'][$this->db]);
		if(PI::$data['debug']) PI::$data['flow']['db'][] = array('sql'=>$sql, 'time'=>microtime(TRUE)-$beginTime);
		outputLog($sql, 'mysql');
		// echo $sql;
		return $this->query;
	}

	/**
	 * 解析字段名,防止字段名是关键字
	 *
	 * @param  unknown $value
	 * @return string
	 */
	private function _returnField($fieldName)
	{
		return '`'.$fieldName.'`';
	}

	/**
	 * 根据值的类型返回SQL语句式的值
	 *
	 * @param  unknown $value
	 * @return string
	 */
	private function _returnValue($value)
	{
		if (is_int($value) || is_float($value)) return $value;
		else return $this->_returnStr($value);
	}

	/**
	 * 格式化用于数据库的字符串
	 *
	 * @param  unknown $value
	 * @return string
	 */
	private function _returnStr($value)
	{
		$value = mysql_real_escape_string($value, PI::$data['link'][$this->db]);
		return "'{$value}'";
	}

	/**
	 * 解析 SQL WHERE 条件
	 *
	 * @param  mixed  $where
	 * @return string
	 */
	public  function _where($where)
	{
		$whereSql = '';
		if (is_array($where))
		{
			$count = count($where);
			$i = 0;
			foreach ($where as $k => $v)
			{
				$prefix    = $i==0 ? '' : ' and ';
				if (is_numeric($k)) {
					$whereSql .= $prefix.$v;
				} else {
					$whereSql .= $prefix.$this->_returnField($k).'='.$this->_returnValue($v);
				}
				
				$i++;
			}
		}
		else
		{
			$whereSql = $where;
		}
		return $whereSql;
	}

	/**
	 * 获取数据
	 * 
	 * $this->get('id', 1);
	 * $this->getID(1);
	 * $this->getOne(array('id'=>1)); $this->getOne('id=1');
	 * $this->getList('id>1', '0,5', 'id desc', '*');
	 * $this->getCount('id>1');
	 *
	 * @param  int     $id     :ID
	 * @param  string  $fields :字段
	 * @param  string  $order  :排序字段
	 * @param  mixed   $where  :条件
	 * @return mixed
	 */
	public function get($field, $value)
	{
		$sql = 'select * from `'.$this->table.'` where `'.$field.'`='.$this->_returnValue($value). ' limit 1';
		$this->query($sql);
		return $this->_mysql_fetch_assoc(1);
	}
	public function getID($id, $fields = '*')
	{
		// $id  = intval($id);
		$sql = 'select '.$fields.' from '.$this->table.' where `'.$this->pk.'`='.$this->_returnValue($id). ' limit 1';
		$this->query($sql);
		return $this->_mysql_fetch_assoc(1);
	}
	public function getOne($where = '', $order = '', $fields = '*')
	{
		$sql = 'select '.$fields.' from '.$this->table;
		if (!empty($where)) $sql .= ' where '.$this->_where($where);
		if (!empty($order)) $sql .= ' order by '.$order. ' limit 1';
		$this->query($sql);
		return $this->_mysql_fetch_assoc(1);
	}
	public function setKeyField($val = null) {
		$this->keyField = $val;
		return $this;
	}

	public function getList($where = '', $limit = '', $order = '', $fields = '*', $group = '')
	{
		$sql = 'select '.$fields.' from '.$this->table;
		if (!empty($where)) $sql .= ' where '.$this->_where($where);
		if (!empty($group)) $sql .= ' group by '.$group;
		if (!empty($order)) $sql .= ' order by '.$order;
		if (!empty($limit)) $sql .= ' limit '.$limit;

		$this->sqlstr = $sql;
		// echo $sql;
		$result = $this->query($sql);
		$data   = array();
		while ($array = $this->_mysql_fetch_assoc(1))
		{
			$this->appendValue($array, $data); 
			
		}
		$this->setKeyField();
		return $data;
	}

	protected function appendValue($row, &$data) 
	{
		if (is_array($this->keyField)) {
			$this->_appendValue($this->keyField, $row, $data);
		} else if ($this->keyField) {
			$data[$row[$this->keyField]] = $row;
		} else {
			$data[] = $row;
		}
	}

	protected function _appendValue($key, &$row, &$data)
	{
		if ($key) {
			$k = array_shift($key);
			$v = $row[$k];
			if ($key) 
			{
				if(!isset($data[$v]))
					$data[$v] = array();
				$this->_appendValue($key, $row, $data[$v]);
			}
			else
			{
				if (isset($data[$v])) 
				{
					if (!isset($data[$v][0]))
						$data[$v] = array($data[$v]);
					$data[$v][] = $row;
				}
				else
					$data[$v] = $row;
			}
		}
 	}
	public function getCount($where = '', $countField = 'COUNT(*)', $group = '')
	{
		$sql = sprintf("select %s from %s", $countField, $this->table);
		if (!empty($where)) $sql .= ' where '.$this->_where($where);
		if (!empty($group)) $sql .= ' group by '.$group;
		$this->query($sql);
		$count = mysql_fetch_array($this->query);
		return $count[0];
	}
	public function getOneBySQL($sql)
	{
		$this->query($sql);
		return $this->_mysql_fetch_assoc(1);
	}
	public function getListBySQL($sql)
	{
		$this->query($sql);
		$data = array();
		while ($array = $this->_mysql_fetch_assoc(1))
		{
			$data[] = $array;
		}
		return $data;
	}
	private function _mysql_fetch_assoc($type = 0)
	{
		$result = mysql_fetch_assoc($this->query);
		if ($type == 1 && !empty($result))
		{
			$i = 0;
			foreach ($result as $key => $value)
			{
				$fieldType  = mysql_field_type($this->query, $i);
				$data[$key] = $fieldType == 'int' ? intval($value) : $value;
				$i++;
			}
			unset($result);
			return $data;
		}
		return $result;
	}

	/**
	 * 插入数据
	 *
	 * $this->add($array);
	 * $this->add(array('id'=>8, 'title'=>'HTML'));
	 *
	 * @param  array $array  :数组
	 * @return int
	 */
	public function add($array)
	{
		foreach ($array as $k => $v)
		{
			$value = $this->_returnValue($v);
			if(is_scalar($value))
			{
				$values[] = $value;
				$fields[] = $this->_returnField($k);
			}
		}

		//add by  liuhe
		//创建时间，假如表里有此字段并且添加的时候未定义
		if(!array_key_exists('created_at', $array) && $this->columnExist('created_at') !== false) {
			$fields[] = 'created_at';
			$values[] = $this->_returnValue(date('Y-m-d H:i:s'));
		}

		//更新时间
		if(!array_key_exists('updated_at', $array) && $this->columnExist('updated_at') !== false) {
			$fields[] = 'updated_at';
			$values[] = $this->_returnValue(date('Y-m-d H:i:s'));
		}

		//最后更新地,假如表里有此字段并且添加的时候未定义
		if(!array_key_exists('last_updated_source', $array) && $this->columnExist('last_updated_source') !== false) {
			$fields[] = 'last_updated_source';
			$values[] = $_SERVER['FX_REMOTE'];
		}

		$sql = 'insert into '.$this->table.' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
		return $this->query($sql);
	}

	// 获取add id
	public function getAddID()
	{
		return mysql_insert_id();
	}

	// 前一次 MySQL 操作所影响的记录行数
	public function affectedRows()
	{
		$ret = mysql_affected_rows();
		return $ret;
	}

	/**
	 * 编辑数据
	 *
	 * $this->update($array, 'id>1');
	 * $this->update(array('title'=>'HTM'), array('id'=>8));
	 * 
	 * @param  int      $id     :ID
	 * @param  array    $array  :数组
	 * @param  string   $where  :条件
	 * @param  string   $order  :排序字段
	 * @param  string   $limit  :数量
	 * @return mixed
	 */
	public function update($array, $where, $order = '', $limit = '')
	{
		foreach ($array as $k=>$v)
		{
			$value = $this->_returnValue($v);
			if(is_scalar($value))
			{
				$set[] = $this->_returnField($k).'='.$value;
			}
		}

		//add by  liuhe
		//最后更新地,假如表里有此字段并且添加的时候未定义
		if(!array_key_exists('last_updated_source', $array) && $this->columnExist('last_updated_source') !== false) {
			$set[] = '`last_updated_source`='.$this->_returnValue($_SERVER['FX_REMOTE']);
		}

		//更新时间
		if(!array_key_exists('updated_at', $array) && $this->columnExist('updated_at') !== false) {
			$set[] = '`updated_at`='.$this->_returnValue(date('Y-m-d H:i:s'));
		}

		$setSql = 'set '.implode(',', $set);
		$sql    = sprintf("update %s %s", $this->table, $setSql);
		if (!empty($where)) $sql .= ' where '.$this->_where($where);
		if (!empty($order)) $sql .= ' order by '.$order;
		if (!empty($limit)) $sql .= ' limit '.$limit;
		return $this->query($sql);
	}

	/**
	 * 删除数据
	 * $this->delID(1);
	 * $this->del('id>1'); $this->del(array('id'=>2));
	 *
	 * @param  int $id    :ID
	 * @param  string   $where  :条件
	 * @param  string   $order  :排序字段
	 * @param  string   $limit  :数量
	 * @return mixed
	 */
	public function delID($id)
	{
		// $id  = __intval($id);
		// $sql = 'delete from `'.$this->table.'` where `'.$this->pk.'`='.$this->_returnValue($id). ' limit 1';
		return $this->update(array('deleted_at' => date('Y-m-d H:i:s')), array($this->pk => $id), '', 1);
	}

	public function del($where, $order = '', $limit = 1)
	{
		// $sql = 'delete from `'.$this->table.'`';
		return $this->update(array('deleted_at' => date('Y-m-d H:i:s')), $where, $order, $limit);
		// $sql = 'update `'.$this->table.'` set deleted_at=\''.date('Y-m-d H:i:s').'\'';
		// if (!empty($where)) $sql .= ' where '.$this->_where($where);
		// if (!empty($order)) $sql .= ' order by '.$order;
		// if (!empty($limit)) $sql .= ' limit '.$limit;
		// return $this->query($sql);
	}


	// 事务处理

	/**
	 * 启动事务
	 * @return bool
	 */
	public function begin()
	{
		if ($this->transDepth == 0)
		{
			$this->query('START TRANSACTION');
		}
		$this->transDepth++;
		return TRUE;
	}

	/**
	 * 事务提交
	 * @return bool
	 */
	public function commit()
	{
		if ($this->transDepth > 0)
		{
			$result = $this->query('COMMIT');
			$this->transDepth = 0;
			if(!$result) die("err:trans commit ".mysql_error());
		}
		return TRUE;
	}

	/**
	 * 事务回滚
	 * @return bool
	 */
	public function rollback()
	{
		if ($this->transDepth > 0)
		{
			$result = $this->query('ROLLBACK');
			$this->transDepth = 0;
			if(!$result) die("err:trans commit ".mysql_error());
		}
		return TRUE;
	}

	//查看某个字段是否存在
	public function columnExist($columnName)
	{
		$sql = 'Describe '.$this->table.' '.$this->_returnField($columnName);
		return $this->getOneBySQL($sql);
	}
}

/* End */
