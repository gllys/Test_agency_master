<?php

class UWebUser extends CApplicationComponent implements IWebUser
{

	/**
	 * 保存认证信息的cookie
	 *
	 * @var string
	 */
	public $cookieKey = "huilian_SAUTH";

	/**
	 * cookie加密密钥
	 *
	 * @var string
	 */
	public $key          = "f&&*(*)*&%^&&%sdfsdf";
	public $cookieDomain = ".huilian.com";
	public $cookiePrefix = 'huilian_';

	/**
	 * @var array the property values (in name-value pairs) used to initialize the identity cookie.
	 * Any property of {@link CHttpCookie} may be initialized.
	 * This property is effective only when {@link allowAutoLogin} is true.
	 * @since 1.0.5
	 */
	public $identityCookie;
	public $allowAutoLogin = true;

	/**
	 * @var string|array the URL for login. If using array, the first element should be
	 * the route to the login action, and the rest name-value pairs are GET parameters
	 * to construct the login URL (e.g. array('/site/login')). If this property is null,
	 * a 403 HTTP exception will be raised instead.
	 * @see CController::createUrl
	 */
	public $loginUrl = array('/site/login');

	/**
	 * 是否验证密码
	 *
	 * @var boolen
	 */
	public $isVerifyPwd = false;

	private $_state  = array();
	private $_access = array();

	/**
	 * @param mixed $value the unique identifier for the user. If null, it means the user is a guest.
	 */
	public function setId($value) {
		$this->setState('uname', $value);
	}

	/**
	 * Returns a value that uniquely represents the identity.
	 * @return mixed a value that uniquely represents the identity (e.g. primary key value).
	 */
	public function getId() {
		return $this->getState('uname');
	}

	public function getDuration() {
		return $this->getState('duration', 0);
	}

	public function setDuration($duration) {
		return $this->setState('duration', $duration);
	}

	/**
	 * Returns the display name for the identity (e.g. username).
	 * @return string the display name for the identity.
	 */
	public function getName() {
		return isset($_COOKIE['huilian_UNICKNAME']) ? $_COOKIE['huilian_UNICKNAME'] : $this->getState('nkname');
	}

	/**
	 * Sets the unique identifier for the user (e.g. username).
	 * @param string $value the user name.
	 * @see getName
	 */
	public function setName($value) {
		$this->setState('nkname', $value);
	}

	/**
	 * Performs access check for this user.
	 * @param string $operation the name of the operation that need access check.
	 * @param array $params name-value pairs that would be passed to business rules associated
	 * with the tasks and roles assigned to the user.
	 * @return boolean whether the operations can be performed by this user.
	 */
	public function checkAccess($operation, $params = array(), $allowCaching = true) {
		if ($allowCaching && $params === array() && isset($this->_access[$operation])) return $this->_access[$operation];
		else
			return $this->_access[$operation] = Yii::app()->getAuthManager()->checkAccess($operation, $this->getId(), $params);
	}

	public function init() {
		parent::init();
		//		Yii::app()->attachEventHandler('onEndRequest',array($this,'saveToCookie'));
		$cookie = Yii::app()->getRequest()->getCookies()->itemAt($this->cookieKey);
		if ($cookie != null) {
			$this->_state = $this->decodeParam($cookie->value);

			//��֤����
			if ($this->isVerifyPwd) {
				$id            = $this->getId();
				$passportModel = UserPassport::model()->findByPk($id);
				$password      = $this->getState('password');
				if (empty($password) || $password != sha1($passportModel->password)) {
					$this->_state = array();
					setcookie("huilian_UNICKNAME", null, time() - 3600, '/', $this->cookieDomain);
				}
			}
		}
		if (!$this->isGuest) {
			$this->name = $this->getName();
		}
	}

	/**
	 * Saves necessary user data into a cookie.
	 * This method is used when automatic login ({@link allowAutoLogin}) is enabled.
	 * This method saves user ID, username, other identity states and a validation key to cookie.
	 * These information are used to do authentication next time when user visits the application.
	 * @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
	 * @see restoreFromCookie
	 */
	public function saveToCookie() {
		if (!$this->isGuest) {
			$expire = null;
			if ($this->duration > 0) {
				$expire = time() + $this->duration;
			}
			$strAuth = $this->encodeParam($this->_state);
			setcookie($this->cookieKey, $strAuth, $expire, '/', $this->cookieDomain);
			setcookie("huilian_UNICKNAME", $this->name, $expire, '/', $this->cookieDomain);
		}
	}

	public function getAuth() {
		return $this->encodeParam($this->_state);
	}

	/**
	 * @return boolean whether the current application user is a guest.
	 */
	public function getIsGuest() {
		return $this->getId() === null;
	}

	/**
	 * Returns a value indicating whether there is a state of the specified name.
	 * @param string $key state name
	 * @return boolean whether there is a state of the specified name.
	 * @since 1.0.3
	 */
	public function hasState($key) {
		return isset($this->_state[$key]);
	}

	/**
	 * Stores a variable in user session.
	 *
	 * This function is designed to be used by CWebUser descendant classes
	 * who want to store additional user information in user session.
	 * By storing a variable using this function, the variable may be retrieved
	 * back later using {@link getState}. The variable will be persistent
	 * across page requests during a user session.
	 *
	 * @param string $key variable name
	 * @param mixed $value variable value
	 * @param mixed $defaultValue default value. If $value===$defaultValue, the variable will be
	 * removed from the session
	 * @see getState
	 */
	public function setState($key, $value, $defaultValue = null) {
		if ($value === $defaultValue) unset($this->_state[$key]);
		else
			$this->_state[$key] = $value;

		$this->saveToCookie();
	}

	/**
	 * PHP magic method.
	 * This method is overriden so that persistent states can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @since 1.0.3
	 */
	public function __get($name) {
		if ($this->hasState($name)) return $this->getState($name);
		else
			return parent::__get($name);
	}

	/**
	 * PHP magic method.
	 * This method is overriden so that persistent states can be set like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 * @since 1.0.3
	 */
	public function __set($name, $value) {
		if ($this->hasState($name)) $this->setState($name, $value);
		else
			parent::__set($name, $value);
	}

	/**
	 * Renews the identity cookie.
	 * This method will set the expiration time of the identity cookie to be the current time
	 * plus the originally specified cookie duration.
	 * @since 1.1.3
	 */
	protected function renewCookie() {
		$strAuth = $this->encodeParam($this->_state);
		$expire  = 0;
		if ($this->duration > 0) {
			$expire = time() + $this->duration;
		}
		setcookie($this->cookieKey, $strAuth, $expire, '/', $this->cookieDomain);
	}

	/**
	 * Returns the value of a variable that is stored in user session.
	 *
	 * This function is designed to be used by CWebUser descendant classes
	 * who want to store additional user information in user session.
	 * A variable, if stored in user session using {@link setState} can be
	 * retrieved back using this function.
	 *
	 * @param string $key variable name
	 * @param mixed $defaultValue default value
	 * @return mixed the value of the variable. If it doesn't exist in the session,
	 * the provided default value will be returned
	 * @see setState
	 */
	public function getState($key, $defaultValue = null) {
		return isset($this->_state[$key]) ? $this->_state[$key] : $defaultValue;
	}

	/**
	 * 编码参数
	 *
	 * @param array $arr
	 * @return string
	 */
	protected function encodeParam($arr) {
		$str = "";
		foreach ($arr as $key => $val) {
			$str .= ($str == "") ? "" : "&";
			$str .= $key . "=" . base64_encode($val);
		}
		return $this->encrypt($str);
	}

	/**
	 * 解码参数
	 *
	 * @param string $str
	 * @return array
	 */
	protected function decodeParam($str) {
		if ($str == "") return false;

		$str     = $this->decrypt($str);
		$arrData = array();
		parse_str($str, $arrData);

		foreach ($arrData as $key => $value) {
			$arrData[$key] = base64_decode($value);
		}
		return $arrData;
	}

	/**
	 * 加密
	 *
	 * @param str txt
	 * @param str encrypt_key     加密key
	 * @return string
	 * */
	protected function encrypt($txt) {
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr         = 0;
		$tmp         = '';
		for ($i = 0, $len = strlen($txt); $i < $len; $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode($this->passportKey($tmp));
	}

	/**
	 * 解密
	 *
	 * @param str txt
	 * @param str encrypt_key     加密key
	 * @return string
	 * */
	protected function decrypt($txt) {
		$txt = $this->passportKey(base64_decode($txt));
		$tmp = '';
		for ($i = 0, $len = strlen($txt); $i < $len; $i++) {
			$md5  = $txt[$i];
			$next = ++$i;
			$tmp .= isset($txt[$next]) ? $txt[$next] ^ $md5 : "";
		}
		return $tmp;
	}

	/**
	 * 对字符串做加密
	 *
	 * @param str txt
	 * @param str encrypt_key     加密key
	 * @return string
	 * */
	protected function passportKey($txt) {
		$encrypt_key = md5($this->key);
		$ctr         = 0;
		$tmp         = '';
		for ($i = 0, $len = strlen($txt); $i < $len; $i++) {
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}

	/**
	 * @param string $value the URL that the user should be redirected to after login.
	 */
	public function setReturnUrl($value) {
		$this->setState('__returnUrl', $value);
	}

	/**
	 * Redirects the user browser to the login page.
	 * Before the redirection, the current URL (if it's not an AJAX url) will be
	 * kept in {@link returnUrl} so that the user browser may be redirected back
	 * to the current page after successful login. Make sure you set {@link loginUrl}
	 * so that the user browser can be redirected to the specified login URL after
	 * calling this method.
	 * After calling this method, the current request processing will be terminated.
	 */
	public function loginRequired() {
		$app     = Yii::app();
		$request = $app->getRequest();


		if (!$request->getIsAjaxRequest()) $rurl = $request->getHostInfo() . $request->getRequestUri();
		else
			$rurl = '';

		if (($url = $this->loginUrl) !== null) {
			if (is_array($url)) {
				$route = isset($url[0]) ? $url[0] : $app->defaultController;
				$url   = $app->createUrl($route, array_splice($url, 1));
			}
			$request->redirect($url . "?rurl=" . urlencode($rurl));
		}
		else
			throw new CHttpException(403, Yii::t('yii', 'Login Required'));
	}

	/**
	 * Changes the current user with the specified identity information.
	 * This method is called by {@link login} and {@link restoreFromCookie}
	 * when the current user needs to be populated with the corresponding
	 * identity information. Derived classes may override this method
	 * by retrieving additional user-related information. Make sure the
	 * parent implementation is called first.
	 * @param mixed $id a unique identifier for the user
	 * @param string $name the display name for the user
	 * @param array $states identity states
	 */
	protected function changeIdentity($id, $name, $states) {
		$this->setId($id);
		$this->setName($name);
		$this->loadIdentityStates($states);
	}

	/**
	 * Loads identity states from an array and saves them to persistent storage.
	 * @param array $states the identity states
	 */
	protected function loadIdentityStates($states) {
		if (is_array($states)) {
			foreach ($states as $name => $value) {
				$this->setState($name, $value);
			}
		}
	}

	/**
	 * Creates a cookie to store identity information.
	 * @param string $name the cookie name
	 * @return CHttpCookie the cookie used to store identity information
	 * @since 1.0.5
	 */
	protected function createIdentityCookie($name) {
		$cookie = new CHttpCookie($name, '');
		if (is_array($this->identityCookie)) {
			foreach ($this->identityCookie as $name => $value) $cookie->$name = $value;
		}
		return $cookie;
	}

	/**
	 * Logs in a user.
	 *
	 * The user identity information will be saved in storage that is
	 * persistent during the user session. By default, the storage is simply
	 * the session storage. If the duration parameter is greater than 0,
	 * a cookie will be sent to prepare for cookie-based login in future.
	 *
	 * Note, you have to set {@link allowAutoLogin} to true
	 * if you want to allow user to be authenticated based on the cookie information.
	 *
	 * @param IUserIdentity $identity the user identity (which should already be authenticated)
	 * @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
	 * If greater than 0, cookie-based login will be used. In this case, {@link allowAutoLogin}
	 * must be set true, otherwise an exception will be thrown.
	 */
	public function login($identity, $duration = 0) {

		$id     = $identity->getId();
		$states = $identity->getPersistentStates();

		$this->changeIdentity($id, $identity->getName(), $states);
		$this->duration = $duration;
	}

	public function logout() {
		setcookie($this->cookieKey, NULL, time() - 100, '/', $this->cookieDomain);
		setcookie("huilian_UNICKNAME", NULL, time() - 100, '/', $this->cookieDomain);
	}

}
