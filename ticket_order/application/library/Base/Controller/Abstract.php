<?php
/**
 * 控制器基类
 * @author  mosen
 */
class Base_Controller_Abstract extends Yaf_Controller_Abstract
{
    protected $yafAutoRender = true;
    protected $rawbody;

    /**
     * [__get description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) return $this->$getter();
        else if (Yaf_Registry::has($name)) return Yaf_Registry::get($name);
        
        throw new Exception("Property " . get_called_class() . ".{$name} is not defined.");
    }
    
    /**
     * [__set description]
     * @param [type] $name  [description]
     * @param [type] $value [description]
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) return $this->$setter($value);
        else Yaf_Registry::set($name, $value);
    }
    
    /**
     * [__isset description]
     * @param  [type]  $name [description]
     * @return boolean       [description]
     */
    public function __isset($name) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) return $this->$getter() !== null;
        
        return Yaf_Registry::has($name);
    }
    
    /**
     * [__unset description]
     * @param [type] $name [description]
     */
    public function __unset($name) {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) $this->$setter(null);
        else Yaf_Registry::del($name);
    }
    
    /**
     * [__call description]
     * @param  [type] $name      [description]
     * @param  [type] $arguments [description]
     * @return [type]            [description]
     */
    public function __call($name, $arguments) {
        if (method_exists($this, $name)) return call_user_method_array($name, $this, $arguments);
        else if (method_exists($this->getRequest(), $name)) return call_user_method_array($name, $this->getRequest(), $arguments);
        
        throw new Exception("Method " . get_called_class() . ".{$name} is not defined.");
    }
    
    /**
     * [getRawBody description]
     * @return [type] [description]
     */
    public function getRawBody() {
        if (!$this->rawbody) {
            $body = trim(file_get_contents('php://input'));
            $this->rawbody = strlen($body) > 0 ? $body : false;
        }
        return $this->rawbody;
    }
    
    /**
     * [getIp description]
     * @return [type] [description]
     */
    public function getIp() {
        return $this->getServer('REMOTE_ADDR');
    }
}
