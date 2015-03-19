<?php
/**
 * 
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class OrganizationsModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'organizations';
	public $pk         = 'id';
	public $hasDeleted = TRUE;
	
	//可关联的,对应value为model前缀
	public $relateAble = array(
			'licence' => 'Attachments',
			'certificate' => 'Attachments',
			'tax' => 'Attachments',
			'logo' => 'Attachments'
	);
	//关联的字段,对应value为表字段
	public $relateField = array(
			'licence' => 'licence_id',
			'certificate' => 'certificate_id',
			'tax' => 'tax_id',
			'logo' => 'logo_id',
	);
	//外键扩展树形信息,现在主要是地区信息，对应value为model前缀
	public $withAble = array(
			'districts'     => 'Districts',
	);
	
	//外键扩展树形信息,现在主要是地区信息，对应value为model前缀
	public $withField = array(
			'districts'     => 'district_id',
	);

	//获取机构的类型
	public function getOrganizationType($id)
	{
		$info = $this->getID($id, 'type');
		return $info['type'];
	}

	//获取机构的名称
	public function getOrganizationNames($organizationIds)
	{
		$list = $this->getList('id in('.implode(',', $organizationIds).')', '', '', 'name');
		$ids = array();
		if($list){
			foreach($list as $key => $value){
				$ids[] = $value['name'];
			}
		}
		return $ids;
	}
    
}