<?php
/**
 * SQL操作日志
 * @author  mosen
 */
class Log_Base
{
	private static $instances = array();
	private static $config;
	protected $tblname;
	protected $prefix = 'log';
	protected $mode = 0; //0 queue, 1 file
    
    public static function model() {
        $className = get_called_class();
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
        }

        return self::$instances[$className];
    }

    public static function save($filename, $data) {
        try{
            if (!self::$config) {
                $config = Yaf_Registry::get("config");
                self::$config = $config['log'];
            }
            $file = self::$config['path'].'/' .$filename;
            $path = dirname($file);
            if (!is_dir($path)) {
                @mkdir($path, 0777, true);
            }
            file_put_contents($file, $data."\n", FILE_APPEND);
            return true;
        } catch(Exception $e){
            return false;
        }
	}
  
	public function getTable() {
        return $this->tblname;
    }

	public function write($arr) {
		$sql = '';
		if (Util_Common::is_arr2($arr)) {
			$values = array();
			$sql = 'INSERT INTO ' . $this->getTable();
			$i = 0;
			foreach ($arr as $key => $value) {
				$this->setBaseParams($value);
				if ($i++ == 0)
					$sql .= ' (`' . implode('`,`', array_keys($value)) .'`) values';
				$values[] = "('" . implode("','", $value) . "')";
			}
			$sql .= implode(',', $values);
		} else if (is_array($arr)) {
			$this->setBaseParams($arr);
			$sql = 'INSERT INTO ' . $this->getTable();
			$sql .= ' (`' . implode('`,`', array_keys($arr)) .'`) values ' . "('" . implode("','", $arr) . "')";
		} else {
			$sql = $arr;
		}

		if(!$this->mode) {
			return Util_Queue::send($this->prefix, $sql);
		} else {
			$file = $this->prefix .'_'. date('Ymd') . '.sql';
			$sql = rtrim($sql, ';').';';
			return self::save($file, $sql);
		}
	}

	protected function setBaseParams(&$item) {
		$params = Yaf_Application::app()->getDispatcher()->getRequest()->getParams();
		$item['organization_id'] = intval($params['organization_id']);
		$item['landscape_id'] = intval($params['landscape_id']);
		$item['user_id'] = intval($params['user_id']);
		$item['user_name'] = $params['user_name'];
		$item['created_at'] = date('Y-m-d H:i:s');
	}
}

