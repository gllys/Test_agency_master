<?php
/**
 * @link
 */
namespace common\huilian\models;

use Yii;
use Organizations;
use ApiModel;
use Districts;
use Message;
use Exception;

/**
 * 更改发消息类
 * 当供应商的更改自身的信息时，发送消息，记录更改的详情。
 * 
 * 记录消息遵循以下规则：
 * - 对可更改的信息记录更改详情（包括更改之前和之后），不可更改的不必记录。
 * - 图片更改的不必记录详情。
 * 实现方案：
 * - 查询出更改的信息，在本处直接生成所需的消息文本。
 * 
 * 修改时POST的数据信息如下：
 *  name:蒙牛集团
 *	id:3
 *	contact:老齐坏人ewe999
 *	mobile:18980909988
 *	fax:
 *	telephone:
 *	province_id:110000
 *	city_id:__NULL__
 *	district_id:__NULL__
 *	address:459977
 *	business_license:http://piaowu.b0.upaiyun.com/gongying/2015/03/03/a83892622dcf2415f67a1ce763d8d608.jpg
 *
 * 
 */
class ModificationMessage 
{
	/**
	 * @var boolean 是否成功发送消息
	 */
	public $success = false;
	
	/**
	 * @var string 错误信息
	 */
	public $errorMessage;
	
	/**
	 * @var array $checks 将要检查比对的数组
	 */
	public $checks = [
		'name' => '机构名称', 
		'contact' => '联系人', 
		'mobile' => '手机号码', 
		'fax' => '公司传真', 
		'telephone' => '固定电话', 
		'province_id' => '省', 
		'city_id' => '市', 
		'district_id' => '县', 
		'address' => '详细地址', 
		'business_license' => '营业执照', 
	];
	
	/**
	 * 初始化
	 * 注意：
	 * - 如果$param参数中没有id，则认为不是修改，不予以处理
	 * @param array $param 一般为调用处传递的$_POST
	 */
	public function __construct(array $param) 
	{
		try {
			//var_dump($param);
			if(empty($param['id'])) {
				return false;
			}
			
			$this->convert($param);
			$this->diffs();
			
			if($this->diffs) {
				$this->text();
				$this->message();
			}
			$this->success = true;
		} catch(Exception $e) {
			$this->errorMessage = $e->getMessage();
		}
	}
	
	/**
	 * 如果请求时没有选择省、市、县，则数据的形式为  `__NULL__`，而相应存储在数据库中的整形字段为0。
	 * 因此需要转换
	 * @param array $param
	 */
	public function convert(array $param) 
	{	
		$keys = [
			'province_id',
			'city_id',
			'district_id',
		];
		foreach($keys as $key) {
			if(!is_numeric($param[$key])) {
				$param[$key] = 0;
			}
		}
		$this->param = $param;
	}
	
	/**
	 * 是否出现变更
	 * 注意：
	 * - post过来的数据和数据库存储的数据差集比较，得出变更条目。空数组，意味着没有变更。
	 */
	public function diffs() 
	{
		$res = Organizations::api()->show(['id' => $this->param['id']]);
		$this->organization = ApiModel::getData($res);
		$this->diffs = array_diff_assoc($this->param, $this->organization);
	}
	
	/**
	 * 组装消息文本
	 * 注意：
	 * - 每条修改项目后分行
	 * - 省、市、县和详细地址做为一个整体看待。
	 *   因此diffs中存在任何一个 `province_id`, `city_id`, `district_id`, `address`，则认为地址被修改
	 */
	public function text() 
	{
		$diffs = $this->diffs;
		$this->text = '供应商：' .Yii::app()->user->id. ', 在' .date('Y-m-d H:i:s');
		
		if(isset($diffs['province_id']) || isset($diffs['city_id']) || isset($diffs['district_id']) || isset($diffs['address'])) {
			$newAddress = District::addressOfArr($this->param);
			$oldAddress = District::addressOfArr($this->organization);
			unset($diffs['province_id'], $diffs['city_id'], $diffs['district_id'], $diffs['address']);
			$this->text .= '修改了地址:' .$oldAddress.', 为'.$newAddress .'<br>';
		}
		
		if(isset($diffs['business_license'])) {
			unset($diffs['business_license']);
			$this->text .= '修改了营业执照<br>';
		}
		
		foreach($diffs as $k => $v) {
			$this->text .= '修改了' .$this->checks[$k] . ':' .$this->organization[$k]. ', 为' .$v. '<br>';
		}
		
		//var_dump($this->text);
	}
	
	/**
	 * 发送消息
	 * 根据发送消息接口规则，传递参数要求如下：
	 * sms_type 2
	 * sys_type 7
	 * send_status 1
	 * send_organization
	 * organization_name
	 * content
	 */
	public function message() 
	{
		$params = [
			'sms_type' => 2,
			'sys_type' => 7,
			'send_status' => 1,
			'send_organization' => $this->organization['id'],
			'organization_name' => $this->organization['name'],
			'content' => $this->text,
		];
		Message::api()->add($params);
	}
	
	
	
}

?>