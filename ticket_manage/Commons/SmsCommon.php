<?php 
/**
 *  短信接口
 * 
 * 2014-1-14
 *
 * @author  cyl
 * @version 1.0
 */
class SmsCommon extends BaseCommon
{
	/**
	 * 发送短信
	 *
	 * @param string $mobile   订单code
	 * @param string $content 手机号
	 *
	 */
	public function sendSMS($mobile, $content)
	{
		$url = 'http://58.68.234.188/webservice/smsservice.asmx/SendSMS?mobile='.$mobile.'&FormatID=8&Content='.$content.'&ScheduleDate=2013-1-1&TokenID=7100583130895251';
		curl($url, array(), 'get');
	}

	//获取创建订单时的短信内容
	public function _getCreateOrderContent($orderInfo)
	{
		$str = "电子编码".$orderInfo['hash'].','.$orderInfo['owner_name'].'您已于'.$orderInfo['created_at'].'购买门票'.$orderInfo['order_item'][0]['name'].'一张，游玩日期为'.$orderInfo['useday'];
		return urlencode($str);
	}

	/**
	 * 创建订单时发送短信
	 *
	 * @param mixed $orderInfo 订单信息
	 *
	 */
	public function sendCreateOrderSms($orderInfo)
	{
		if($orderInfo) {
			$mobile       = $orderInfo['owner_mobile'];
			$orderInfo    = $this->load->model('orders')->getOneRelate($orderInfo, 'order_item');
			$validateTool = $this->load->tool('validate');
			if($validateTool->validate('mobile', $mobile)) {
				$content = $this->_getCreateOrderContent($orderInfo);
				$this->sendSMS($mobile, $content);
			}
		}
	}
}