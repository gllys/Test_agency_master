<?php
/**
 *
 *
 * 2014-1-9
 *
 * @author  cyl
 * @version 1.0
 */
class organizationPartnerModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'organization_partner';
	public $pk         = 'id';


	//可关联的,对应value为model前缀
	public $relateAble = array(
		'organization_main'    => 'Organizations',
		'organization_partner' => 'Organizations',
		'price_templates'      => 'PriceTemplates'
	);

	//关联的字段,对应value为表字段
	public $relateField = array(
		'organization_main'    => 'organization_main_id',
		'organization_partner' => 'organization_partner_id',
		'price_templates'      => 'price_templates_id'
	);

	static public $weekArrayForPartner = array(
		'1' => '周一',
		'2' => '周二',
		'3' => '周三',
		'4' => '周四',
		'5' => '周五',
		'6' => '周六',
		'0' => '周日',
	);

	//获取星期
	static public function getWeekDay($day = '')
	{
		if(!empty($day) && $day !== 0){
			return self::$weekArrayForPartner[$day];
		}else{
			return self::$weekArrayForPartner;
		}
	}

	/**
	 * 检查2个机构是否是合作机构
	 *
	 * @param int $organizationMainId 主机构
	 * @param int $organizationPartnerId  申请者
	 * @return array
	 */ 
	public function isOrganizationPartner($organizationMainId, $organizationPartnerId)
	{
		$result = $this->getPartnerInfo($organizationMainId, $organizationPartnerId, true);
		return $result;
	}

	/**
	 * 获取合作机构信息
	 *
	 * @param int $organizationMainId 主机构
	 * @param int $organizationPartnerId  申请者
	 * @param bool $normalOnly  只查通过的合作机构
	 * @return array
	 */ 
	public function getPartnerInfo($organizationMainId, $organizationPartnerId, $normalOnly = false)
	{
		$filter = array(
				'organization_main_id'    => $organizationMainId,
				'organization_partner_id' => $organizationPartnerId,
		);

		if($normalOnly){
			$filter['status'] = 'normal';
		}
		$result = $this->getOne($filter);
		return $result;
	}
}