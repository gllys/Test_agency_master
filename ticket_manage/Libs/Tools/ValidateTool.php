<?php
/**
 * 验证工具
 *
 * 2013-11-28 liuhe 创建
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class ValidateTool
{
	//验证的消息
	private $_msg = array(
		"n"     => "请填写数字！",
		"s"     => "不能输入特殊字符！",
		"p"     => "请填写邮政编码！",
		"m"     => "请填写手机号码！",
		"e"     => "邮箱地址格式不对！",
		"phone" => "请填写座机号码！",
	);

	//验证的正则
	private $_matches = array(
		"n"     => "/^\d+$/",
		"s"     => "/^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]+$/",
		"p"     => "/^[0-9]{6}$/",
		"m"     => "/^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}$/",
		"e"     => "/^\w+([-+.'']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",
		"phone" => "/^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/",
	);

	/**
	 * 
	 *
	 * @param string     $type 总的记录数
	 * @param unknown   $value  
	 * @return bool
	 */
	public function validate($type, $value)
	{
		$method = 'validate'.ucfirst($type);
		return $this->$method($value);
	}

	/**
	 * 不能为空
	 * @param unknown   $value  
	 * @return bool
	 */
	public function validateRequired($value)
	{
		if(strlen($value) > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 验证字符长度
	 * @param array  $value  例 array('minSize'=> 4,'maxSize'=> 16,'value'=> 'dfdsfsfs')
	 * @return bool
	 */
	public function validateLengthBetweenAnd($value)
	{
		if($this->_utf8_strlen($value['value']) >= $value['minSize'] && $this->_utf8_strlen($value['value']) <= $value['maxSize']){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 手机（不包括座机）
	 * @param unknown   $value  
	 * @return bool
	 */
	public function validateMobile($value)
	{
		if(preg_match($this->_matches['m'], $value)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 座机(不包括手机)
	 * @param unknown   $value  
	 * @return bool
	 */
	public function validatePhone($value)
	{
		if(preg_match($this->_matches['phone'], $value)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 邮政编码
	 * @param unknown   $value  
	 * @return bool
	 */
	public function validatePostalcode($value)
	{
		if(preg_match($this->_matches['m'], $value)){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * email
	 * @param unknown   $value  
	 * @return bool
	 */
	public function validateEmail($value)
	{
		if(preg_match($this->_matches['e'], $value)){
			return true;
		}else{
			return false;
		}
	}

	//utf8 字符的长度
	private function _utf8_strlen($string = null) 
	{
		// 将字符串分解为单元  
		preg_match_all("/./us", $string, $match);
		// 返回单元个数  
		return count($match[0]);  
	}
}

/* End */