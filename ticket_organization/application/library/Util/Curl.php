<?php
/**
 * CURL工具
 * @author  mosen
 */
class Util_Curl 
{
	const METHOD_POST   = 'POST'; 
	const METHOD_PUT   = 'PUT'; 
	const METHOD_GET    = 'GET'; 
	const METHOD_DELETE = 'DELETE';

	protected $url;
	protected $timeout = 30;
	protected $noHeader = true;
	protected $noBody = false;
	protected $cookieFile = '';

	protected $status;
	protected $rawbody;

	/**
	 * [setUrl description]
	 * @param [type] $val [description]
	 */
	public function setUrl($val) {
		$this->url = $val;
		return $this;
	}

	/**
	 * [setTimeout description]
	 * @param [type] $val [description]
	 */
	public function setTimeout($val) {
		$this->timeout = $val;
		return $this;
	}

	/**
	 * [setCookieFile description]
	 * @param [type] $val [description]
	 */
	public function setCookieFile($val) {
		$this->cookieFile = $val;
		return $this;
	}

	/**
	 * [setNoHeader description]
	 * @param [type] $val [description]
	 */
	public function setNoHeader ($val) {
		$this->noHeader = $val;
		return $this;
	}

	/**
	 * [setNoBody description]
	 * @param [type] $val [description]
	 */
	public function setNoBody ($val) {
		$this->noBody = $val;
		return $this;
	}

	/**
	 * [request description]
	 * @param  string  $url        [description]
	 * @param  array   $postData   [description]
	 * @param  integer $returnJson [description]
	 * @return [type]              [description]
	 */
	public function request($url = '', $postData = array(), $returnJson = 1) {
		if (!function_exists('curl_init')) {
			throw new Exception('curl not found.');
		}

		$request = Yaf_Dispatcher::getInstance()->getRequest();
		$s = curl_init();

		if ($url) {
			$this->url = $url;
		}
		
		curl_setopt($s, CURLOPT_URL, $this->url);
		curl_setopt($s, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($s, CURLOPT_HEADER, $this->noHeader);
		curl_setopt($s, CURLOPT_NOBODY, $this->noBody);

		if ($this->cookieFile) {
			curl_setopt($s, CURLOPT_COOKIEJAR, $this->cookieFile);
			curl_setopt($s, CURLOPT_COOKIEFILE, $this->cookieFile);
		}
		
		if($postData) {
			curl_setopt($s, CURLOPT_POST, true);
			curl_setopt($s, CURLOPT_POSTFIELDS, $postData);
		}

		curl_setopt($s, CURLOPT_USERAGENT, $request->getServer('HTTP_USER_AGENT'));
		curl_setopt($s, CURLOPT_REFERER, $request->getServer('HTTP_REFERER'));

		// if ($customRequest) {
			// curl_setopt($s, CURLOPT_CUSTOMREQUEST, $customRequest);
		// }
		
		$this->rawbody = curl_exec($s);
		$this->status = curl_getinfo($s);
		curl_close($s);

		if ($returnJson)
			return json_decode($this->rawbody, true);
		else 
			return $this->rawbody;
	}

	/**
	 * [getHttpCode description]
	 * @return [type] [description]
	 */
	public function getHttpCode() {
		return $this->status['http_code'];
	}

	/**
	 * [getRawbody description]
	 * @return [type] [description]
	 */
	public function getRawbody() {
		return $this->rawbody;
	}
} 