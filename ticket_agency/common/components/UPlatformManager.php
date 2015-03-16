<?php
/**
 * 平台管理组件。
 * 有以下四种方式可以指定平台，优先级依次降低
 * 1、调用setPlatform()方法设置
 * 2、request值，包括get和post，key通过requestKey属性指定，默认为platform，如果要禁用此特性，可将此属性设为null
 * 3、设置cookie值，cookie的key通过cookieKey属性指定，默认为platform，如果要禁用此特性，可将此属性设为null 
 * 4、配置文件，在配置文件里设置本组件的platform属性
 * @package common.components
 */
class UPlatformManager extends CApplicationComponent{
	
	/**
	 * 保存当前的平台设置
	 *
	 * @var string
	 */
	private $_platform='uuzu';
	
	/**
	 * 如果检测到cookie里存在以该属性为key的值，则将cookie里的值设置为当前平台。可以通过将该属于设置为null禁用该特性
	 *
	 * @var string
	 */
	public $cookieKey = "platform";
	
	/**
	 * 如果检测到request变量里存在以该属性为key的值，则将request变量里的值设置为当前平台。可以通过将该属于设置为null禁用该特性
	 *
	 * @var string
	 */
	public $requestKey = "platform";
	
	/**
	 * cookie有效期,默认３０天，如果cookieKey不为null，并且此属性不为null，platform值将会保存在cookie里
	 *
	 * @var string
	 */
	public $cookieExpire = 2592000;
	
	public function init(){
		parent::init();	
		if($this->requestKey != null && (isset($_GET[$this->requestKey]) || isset($_POST[$this->requestKey]))){
			$platform = isset($_GET[$this->requestKey])?$_GET[$this->requestKey]:$_POST[$this->requestKey];
			$this->setPlatform($platform);
		}elseif($this->cookieKey !=null && isset($_COOKIE[$this->cookieKey])){
			$this->setPlatform($_COOKIE[$this->cookieKey]);
		}
	}
	
	public function setPlatform($platform){
		$this->_platform = $platform;
		if( $this->cookieKey!=null && $this->cookieExpire != null) setcookie($this->cookieKey, $platform, time()+$this->cookieExpire, "/");
	}
	
	public function getPlatform(){
		return $this->_platform;
	}
}