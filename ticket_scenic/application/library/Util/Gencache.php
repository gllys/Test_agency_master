<?php
/**
 * 缓存文件生成工具
 * @author  mosen
 */
class Util_Gencache extends Base_Model_Abstract
{
    private $db;
    private $tbl;
    protected $savedir;
    protected $setting;

    public function __construct() {
        $config = Yaf_Registry::get("config");
        $options = $config['cache'];
        $this->savedir = $options['savedir'];
        if (!is_dir($this->savedir)) {
            @mkdir($this->savedir, 0775, true);
        }
        $setting = new Yaf_Config_Ini($options['setting']);
        $this->setting = $setting->toArray();
    }

    /**
     * [create description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function create($key = null) {
        if ($key && !is_array($key)) $key = explode(',', $key);
        foreach ($this->setting['cache'] as $class => $item) {
            if ($key && !in_array($class, $key)) continue;
            list($this->db,$this->tbl) = explode('|', $item['db']);
            unset($item['db']);
            $code = $this->parseClass($class, $item);
            file_put_contents($this->savedir . ucfirst($class) . '.php', $code);
        }
    }
    
    /**
     * [parseClass description]
     * @param  [type] $class [description]
     * @param  [type] $funs  [description]
     * @return [type]        [description]
     */
    protected function parseClass($class, $funs){
        $className = 'Cache_'.ucfirst($class). 'Model';
        $code = array();
        $code[] = '<?php';
        $code[] = '';
        $code[] = '/**';
        $code[] = ' * @date '. date('Y-m-d H:i:s');
        $code[] = ' * @author Gencache';
        $code[] = ' */';
 
        $code[] = "class {$className} extends Base_Model_Abstract";
        $code[] = '{';

        foreach ($funs as $fun => $options) {
            $code[] = $this->parseFun($fun, $options);
        }
        
        $code[] = '}';
        
        return implode("\r\n", $code);
    }
    
    /**
     * [parseFun description]
     * @param  [type] $fun     [description]
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    protected function parseFun($fun, $options) {
        list($args,$field,$where) = explode('|', $options);
        if (!is_array($args)) $args = explode(',', $args);
        $params = $this->parseParams($args);
        $return = $this->parseReturn($args);
        $data = $this->parseData($args,$field,$where);
        
        $code = array();
        $code[] = "\tpublic function {$fun}({$params}) {";
        $code[] = "\t\tstatic \$configs = {$data};";
        $code[] = $return;
        $code[] = "\t}";
        $code[] = "";
        
        return implode("\r\n", $code);
    }
    
    /**
     * [parseParams description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function parseParams($params) {
        $code = array();
        foreach ($params as $v) {
            $code[] = "\${$v} = null";
        }
        return implode(",", $code);
    }
    
    /**
     * [parseData description]
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    protected function parseData($args, $field, $where = null) {
        $rows = Db_Mysql::factory($this->db)
            ->setListKey($args)
            ->select($this->tbl, $where, $field);

        $s = array(
            '/(array \(|=> |,)\\n/',
        );
        $r = array(
            '$1',
        );
        $code = preg_replace($s, $r, var_export($rows, true));
        return $code;
    }
    
    /**
     * [parseReturn description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function parseReturn($params) {
        $code = array();
        $i = 0;
        if ($params) {
            while($param = array_pop($params)) {
                if ($i++ > 0) {
                    $code[] = "\t\telseif (!is_null(\${$param}))";
                } else {
                    $code[] = "\t\tif (!is_null(\${$param}))";
                }
                $tmp = "\t\t\treturn \$configs";
                foreach ($params as $p) {
                    $tmp .= "[\${$p}]";
                }
                $tmp .= "[\${$param}];";
                $code[] = $tmp;
            }
            $code[] = "\t\telse\t";
        }
        $code[] = "\t\treturn \$configs;";
        return implode("\r\n", $code);
    }
}

