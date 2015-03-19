<?php
/**
 * SESSION操作
 * @author  mosen
 */
class Session_Proxy 
{	
	private $writer;
	private $started = false;

	public function start() {
		if (!$this->started) {
			$this->started = true;
			session_start();
		}
	}

	public function __get($name) {
		return $_SESSION[$name];
	}

	public function __set($name, $value) {
		return $_SESSION[$name] = $value;
	}

	/**
	 * [__construct description]
	 */
	public function __construct() {
		session_set_save_handler(
			array($this,'open'),
			array($this,'close'),
			array($this,'read'),
			array($this,'write'),
			array($this,'destroy'),
			array($this,'gc')
		);
	}
	
	/**
	 * [open description]
	 * @param  [type] $path [description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function open($path, $name) {
		return true;
	}

	/**
	 * [close description]
	 * @return [type] [description]
	 */
	public function close() {
		return true;
	}

	/**
	 * [read description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function read($id) {
		return $this->getWriter()->read($id);
	}

	/**
	 * [write description]
	 * @param  [type] $id   [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function write($id, $data) {
		return $this->getWriter()->write($id, $data);
	}

	/**
	 * [destroy description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function destroy($id) {
		return $this->getWriter()->destroy($id);
	}

	/**
	 * [gc description]
	 * @param  [type] $maxLifeTime [description]
	 * @return [type]              [description]
	 */
	public function gc($maxLifeTime) {
		return true;
	}

	public function getMaxLifeTime() {
		return $this->getWriter()->getMaxLifeTime();
	}

	/**
	 * [getWriter description]
	 * @return [type] [description]
	 */
	public function getWriter() {
		if (!$this->writer) {
			$config = Yaf_Registry::get("config");
        	$session = $config['session'];
			$cls = $session['proxy'] ? $session['proxy'] : 'Session_Writer_Redis';
			$this->writer = new $cls($session['writer']);
		}
		return $this->writer;
	}
}
