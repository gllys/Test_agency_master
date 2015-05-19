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
        return array(
            'alipay' => '支付宝',
            'kuaiqian' => '快钱',
            'union' => '平台支付',
            'credit' => '信用支付',
            'advance' => '储蓄支付',
		);
	}

}



?>