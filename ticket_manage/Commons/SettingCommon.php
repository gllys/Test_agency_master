<?php
/**
 * 系统配置common
 *
 * @package common
 * @author cuiyulei
 **/
class SettingCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"post":["post data is null"]}}',
		'-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
		'-3'  => '{"errors":{"msg":["结算周期不能为空"]}}',
		'-4'  => '{"errors":{"msg":["结算日不能为空"]}}'
	);

	/**
	 * 保存系统配置
	 *
	 * @param array $post
	 *
	 * @return json
	 * @author cuiyulei
	 **/
	public function saveSettleSetting($post)
	{
		if($post){
			$msg = '';
			//验证信息
			if(!$this->_checkSettleData($post, $msg)){
				return $msg;
			}
			//转换成数据库字段
			$confMod = $this->load->model('config');
			$settle  = $confMod->getOne(array('type' => 'system', 'config_key' => 'system.settle'));
			$postData    = $this->_formSettleData($post, $settle);
			if ($settle) {
				$confMod->update($postData, array('id' => $settle['id']));
				$addId = $settle['id'];
			} else {
				$confMod->add($postData);
				$addId = $confMod->getAddID();
			}
			if($addId){
				$postData['id'] = $addId;
				return json_encode(array('data'=>array($postData)));
			}else{
				return  $this->_getUserError(-2);
			}
		}else{
			return  $this->_getUserError(-1);
		}
	}

	/**
	 * 检测用户输入
	 *
	 * @param array $post
	 * @param string $msg
	 *
	 * @return boolean
	 * @author cuiyulei
	 **/
	private function _checkSettleData($post, &$msg)
	{
		$account_cycle     = array('month', 'week');
		if(!$post['account_cycle'] || !in_array($post['account_cycle'], $account_cycle)){
			$msg = $this->_getUserError(-3);
			return false;
		}

		if ($post['account_cycle_day'] === '' || ($post['account_cycle_day']) < 0 || $post['account_cycle_day'] > 31) {
			$msg = $this->_getUserError(-4);
			return false;
		}

		return true;
	}

	/**
	 * 组织结算周期的数据
	 *
	 * @param array $post
	 *
	 * @return array
	 * @author cuiyulei
	 **/
	private function _formSettleData($post, $settle)
	{
		$setting = array('account_cycle' => $post['account_cycle'], 'account_cycle_day' => $post['account_cycle_day']);
		$time    = date('Y-m-d H:i:s');
		$data    = array(
			'type'         => 'system',
			'config_key'   => 'system.settle',
			'config_value' => serialize($setting),
			'created_by'   => $_SESSION['backend_userinfo']['id'],
			'updated_at'   => $time
		);

		//是否是第一次添加
		if (!$settle) {
			$data['created_at'] = $time;
		}

		return $data;
	}

} // END class 