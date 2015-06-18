<?php

/**
 * Db_Mysql
 * @author  mosen
 */
class Db_Mysql
{
    protected static $instances = array();
    protected static $default = array('host' => 'localhost', 'port' => '3306', 'database' => 'test', 'user' => 'root', 'password' => '111111');
    
    protected $db;
    protected $connected = false;
    protected $config;
    protected $try = 0;
    protected $tryLimit = 3;
    protected $transCounter = 0;
    protected $listKey;
    protected $groupBy;
    public $sql;
    
    /**
     * [factory description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function factory($name = 'default') {
        if (!isset(self::$instances[$name])) {
            $cls = __CLASS__;
            self::$instances[$name] = new $cls(self::getConfig($name));
        }
        return self::$instances[$name];
    }
    
    /**
     * [getConfig description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function getConfig($name) {
        $config = Yaf_Registry::get("config");
        $items = $config['db'];
        return $items && isset($items['servers'][$name]) ? $items['servers'][$name] : self::$default;
    }
    
    /**
     * [__construct description]
     * @param [type] $options [description]
     */
    public function __construct($options) {
        $this->config = new MysqlConfig($options);
    }
    
    /**
     * [_connect description]
     * @return [type] [description]
     */
    private function _connect() {
        if (!$this->connected) {
            try {
                $this->connected = true;
                $dsn = 'mysql:host=' . $this->config->host . ';port=' . $this->config->port . ';dbname=' . $this->config->database;
                $this->db = new PDO($dsn, $this->config->user, $this->config->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                $this->db->exec('SET SQL_MODE=ANSI_QUOTES');
                $this->db->exec("SET NAMES '" . $this->config->charset . "'");
            }
            catch(PDOException $e) {
                $this->connected = false;
                throw new Exception($e->getMessage());
            }
        }
        return $this->connected;
    }
    
    /**
     * [connect description]
     * @return [type] [description]
     */
    public function connect() {
        try {
            $this->_connect();
            $this->try = 0;
        }
        catch(Exception $e) {
            $this->try++;
            if ($this->try > $this->tryLimit) {
                throw $e;
            } else {
                usleep(500);
                $this->connect();
            }
        }
    }
    
    public function get($tblname, $cond, $fields = '*') {
        return $this->doQuery($tblname, $cond, $fields)->fetch(PDO::FETCH_ASSOC);
    }

    public function setListKey($key = null) {
        $this->listKey = $key;
        return $this;
    }

    public function setGroupBy($groupBy = null){
        $this->groupBy = $groupBy;
        return $this;
    }
    
    public function select($tblname, $cond, $fields = '*', $order = null, $limit = null) {
        $data = array();
        $sth = $this->doQuery($tblname, $cond, $fields, $order, $limit);
        while ($row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $this->appendValue($row, $data);
        }
        $sth = null;
        $this->setListKey();
        $this->setGroupBy();
        return $data;
    }

    public function selectBySql($sql) {
        $this->connect();
        $data = array();
        $this->sql = $sql;
        $sth = $this->db->prepare($this->sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $this->appendValue($row, $data);
        }
        $sth = null;
        $this->setListKey();
        $this->setGroupBy();
        return $data;
    }

    protected function appendValue($row, &$data) {
        if (is_array($this->listKey)) {
            $this->_appendValue($this->listKey, $row, $data);
        } else if ($this->listKey) {
            $data[$row[$this->listKey]] = $row;
        } else {
            $data[] = $row;
        }
    }
    
    protected function _appendValue($key, &$row, &$data) {
        if ($key) {
            $k = array_shift($key);
            $v = $row[$k];
            if ($key) {
                if (!isset($data[$v])) $data[$v] = array();
                $this->_appendValue($key, $row, $data[$v]);
            } else {
                if (isset($data[$v])) {
                    if (!isset($data[$v][0])) $data[$v] = array($data[$v]);
                    $data[$v][] = $row;
                } else $data[$v] = $row;
            }
        }
    }
    
    protected function doQuery($tblname, $cond, $fields = '*', $order = null, $limit = null) {
        $this->connect();
        $params = array();

        // WHERE
        $where = $this->where($cond, $params);
        if ($where) $where = ' WHERE ' . $where;

        // ORDER
        $order = $this->getOrder($order);
        $order = $order ? ' ORDER BY ' . $order : '';

        // LIMIT
        $limit = $this->getLimit($limit);
        $limit = $limit ? ' LIMIT ' . $limit : '';

        $groupBy = $this->groupBy?' GROUP BY '.$this->groupBy:'';

        $this->sql = "SELECT " . $fields . " FROM `" . $tblname . "`" . $where . $groupBy . $order . $limit;
        $sth = $this->db->prepare($this->sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//        echo $this->sql;print_r($params);
        $sth->execute($params);
//         echo $this->sql;print_r($params);
        return $sth;
    }

    public function __call($command, $params = array()) {
        $this->connect();
        return call_user_func_array(array($this->db, $command), $params);
    }

    protected function getOrder($items) {
        if (is_array($items)) {
            foreach($items as $key => $val) {
                $asc = strtoupper($val) == 'ASC' ? 'ASC' : 'DESC';
                $items[$key] = "`{$key}` {$asc}"; 
            }
            return implode(',', $items);
        }
        return $items;
    }

    protected function getLimit($items) {
        if (is_array($items)) {
            foreach($items as $key => $val) {
                $items[$key] = intval($val);
            }
            return implode(',', $items);
        }
        return $items;
    }

    public function insert($tblname, $data) {
        $this->connect();
        $params = array();
        $mult = false;
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $mult = true;
                break;
            }
            $data[$key] = '?';
            $params[] = is_array($val) ? json_encode($val) : $val;
        }

        if (!$mult) {
            $fields = implode('`, `', array_keys($data));
            $values = '('. implode(',', $data) . ')';
        } else {
            $tmp_fields = array_shift($data);
            $fields = implode('`, `', $tmp_fields);
            for($i = 0;$i<count($data);$i++){
                for($j = 0;$j<count($tmp_fields);$j++){
                    $bind_param = $tmp_fields[$j];
                    $tmp_values[] = ':'.$bind_param.$i;
                    $params[$bind_param.$i] = $data[$i][$bind_param];
                }
                $values[] = "(". implode(", ", $tmp_values) . ")";
                unset($tmp_values);
            }
            $values = implode(',', $values);
        }

        $this->sql = "INSERT INTO `" . $tblname . "` (`$fields`) VALUES $values";
        $dbh = $this->db->prepare($this->sql);
        $rt = $dbh->execute($params);
        if (!$rt) {
            $err = $dbh->errorInfo();
            throw new Exception(array_pop($err));
        }
        return $rt;
    }

    public function replace($tblname, $data) {
        $this->connect();
        $params = array();
        $mult = false;
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $mult = true;
                break;
            }
            $data[$key] = '?';
            $params[] = is_array($val) ? json_encode($val) : $val;
        }

        if (!$mult) {
            $fields = implode('`, `', array_keys($data));
            $values = '('. implode(',', $data) . ')';
        } else {
            $fields = implode('`, `', array_shift($data));
            $values = array();
            foreach ($data as $row) {
                $values[] = "('". implode("','", $row) . "')";
            }
            $values = implode(',', $values);
        }

        $this->sql = "REPLACE INTO `" . $tblname . "` (`$fields`) VALUES $values";
        $dbh = $this->db->prepare($this->sql);
        $rt = $dbh->execute($params);
        if (!$rt) {
            $err = $dbh->errorInfo();
            throw new Exception(array_pop($err));
        }
        return $rt;
    }

    public function getInsertId() {
        return $this->db->lastInsertId();
    }
    
    public function update($tblname, $data, $cond = null) {
        $this->connect();
        $params = array();
        foreach ($data as $key => $val) {
            $data[$key] = "`$key`=?";
            $params[] = is_array($val) ? json_encode($val) : $val;
        }
        $where = $this->where($cond, $params);
        if ($where) $where = ' WHERE ' . $where;

        $this->sql = 'UPDATE `' . $tblname . '` SET ' . implode(',', $data) . $where;
        return $this->db->prepare($this->sql)->execute($params);
    }
    
    public function delete($tblname, $cond) {
        $this->connect();
        $params = array();
        $where = $this->where($cond, $params);
        if ($where) $where = ' WHERE ' . $where;

        $this->sql = 'DELETE FROM `' . $tblname . '` ' . $where;
        return $this->db->prepare($this->sql)->execute($params);
    }
    
    /**
     * [$where description]
     * @var array
     *
     *  $where = array(
     *      'id'=>10,
     *      'deleted_at|exp'=>'is null',
     *      'created_at|between'=>array(1,2),
     *      'state|>'=>1,
     *      'type|in'=>array(1,2,3),
     *      'name|like'=>array('1%','%2%'),
     *      );
     */
    private function where($cond, &$params = array(), $op = 'AND') {
        if (empty($cond)) return '';
        if (!is_array($cond)) return $cond;

        $parts = array();
        foreach($cond as $key => $values) {
            if (is_numeric($key)) continue;
            $check = strtoupper($key);
            if ($check === 'OR' || $check === 'AND') {
                $sql = $this->where($values, $params, $check);
            } else {
                $sql = $this->_where($key, $values, $params);
            }
            if ($sql !== '') $parts[] = '(' . $sql . ')';
        }
        return empty($parts) ? '' : implode(' ' . $op . ' ', $parts);
    }

    private function _where($column, $cond, &$params) {
        if (strpos($column, '|') > 0) {
            list($column, $op) = explode('|', $column);
        } else {
            $op = '=';
        }
        $op = strtoupper($op);
        switch ($op) {
            case 'EXP':
                $sql = $column . ' ' . $cond;
                break;
            case 'IN':
            case 'NOT IN':
                if(is_array($cond)){
                    foreach ($cond as $i => $value) {
                        $cond[$i] = '?';
                        $params[] = $value;
                    }
                    $sql = $column . ' ' . $op . ' (' . implode(', ', $cond) . ')';
                }
                else{
                    $sql = $column . ' '. $op  . $cond;
                }
                break;
            case 'BETWEEN':
                foreach ($cond as $i => $value) {
                    $cond[$i] = '?';
                    $params[] = $value;
                }
                $sql = $column . ' ' . $op . ' ' . implode(' AND ', $cond);
                break;
            default:
                if (is_array($cond)) {
                    foreach ($cond as $i => $value) {
                        $cond[$i] = $column . ' ' . $op . ' ? ';
                        $params[] = $value;
                    }
                    $sql = implode(' OR ', $cond);
                } else {
                    $sql = $column . ' ' . $op . ' ? ';
                    $params[] = $cond;
                }
                break;
        }

        return $sql;
    }
    
    public function begin() {
        if ($this->transCounter++ == 0) {
            $this->connect();
            $this->db->beginTransaction();
        }
        return true;
    }
    
    public function commit() {
        if ($this->connected && --$this->transCounter == 0) {
            $this->db->commit();
        }
        return true;
    }
    
    public function rollback() {
        if ($this->connected && $this->transCounter > 0) {
            $this->transCounter = 0;
            $this->db->rollback();
        }
        return true;
    }
    
    public function error() {
        $err = $this->db->errorInfo();
        return $err ? array_pop($err) : 'none';
    }
    
    public function lastQuery() {
        return $this->sql;
    }
}

/**
 * Mysql CONFIG
 * @author mosen
 */
class MysqlConfig
{
    public $host = 'localhost';
    public $port = '3306';
    public $database = 'test';
    public $user = 'root';
    public $password = '111111';
    public $charset = 'utf8';
    
    public function __construct($config) {
        foreach ($config as $key => $item) $this->$key = $item;
    }
}
