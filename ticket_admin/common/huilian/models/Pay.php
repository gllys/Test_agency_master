<?php
/**
 * @link
 */

namespace common\huilian\models;

/**
 * 支付相关类
 * 
 * @author LRS
 */
class Pay {
	
	/**
	 * 支付类型
	 * @return array 返回预定义的支付类型
	 */
	public static function types() {
		return [
			'online' => '线上',
			'offline' => '线下',
			'sign' => '签单',
			'credit' => '信用支付',
			'advance' => '储蓄支付',
			'union' => '平台支付',
			'alipay' => '支付宝',
			'kuaiqian' => '快钱',
		];
	}
	
}



?>