<?php
/**
 *  景区相关数据
 * 
 * 2013-09-04
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class ScenicCommon extends BaseCommon
{
	protected $_code = array(
		'-1'  => '{"errors":{"post":["post data is null"]}}',
		'-2'  => '{"errors":{"msg":["保存至数据库失败"]}}',
		'-3'  => '{"errors":{"msg":["景区id不能为空且必须为数字"]}}',
		'-4'  => '{"errors":{"msg":["景区名称不能为空且限制50个字符"]}}',
		'-5'  => '{"errors":{"msg":["景区级别必选"]}}',
		'-6'  => '{"errors":{"msg":["所在地必选"]}}',
		'-7'  => '{"errors":{"msg":["开放时间必填且限制100个字符"]}}',
		'-8'  => '{"errors":{"msg":["联系电话必填"]}}',
		'-9'  => '{"errors":{"msg":["无效的联系电话"]}}',
		'-10' => '{"errors":{"msg":["联系地址必填且限制100个字符"]}}',
		'-11' => '{"errors":{"msg":["取票地址必填且限制100个字符"]}}',
		'-12' => '{"errors":{"msg":["景点主题至少选一个"]}}',
		'-13' => '{"errors":{"msg":["适合人群至少选一个"]}}',
		'-14' => '{"errors":{"msg":["景区介绍必填且限制5000个字符"]}}',
		'-15' => '{"errors":{"msg":["购票须知必填且限制5000个字符"]}}',
		'-16' => '{"errors":{"msg":["交通指南必填且限制500个字符"]}}',
		'-17' => '{"errors":{"msg":["删除景区失败"]}}',
		'-18' => '{"errors":{"msg":["请选择景区"]}}',
		'-19' => '{"errors":{"msg":["请上传证书"]}}',
	);

	//景区状态对应
	static public $scenicStatus = array(
		'unaudited' => '未审核',
		'normal'    => '审核通过',
		'failed'    => '驳回',
	);

	/**
	 * 添加景区  
	 *
	 *     景区字段               含义
	 *     name                   景区名称
	 *     landscape_level_id     景区级别  0,1,2,3,4,5(非A。。。5A)
	 *     district_id            景区所在行政区域
	 *     address                联系地址
	 *     thumbnail_id           景区图片
	 *     phone                  联系电话
	 *     hours                  开放时间
	 *     exaddress              兑换取票地址
	 *     description            简要描述
	 *     biography              景区介绍
	 *     note                   购票须知
	 *     rank                   景点排序 ？？
	 *     transit                交通指南
	 *     status                 景点状态 enum('normal','unaudited')
	 *     audience               适合人群 如 1,2,3
	 *     theme                  景点主题 如 1,2,3
	 * @param array $post 景区数据 
	 * @return json
	 */
	public function addScenic($post)
	{
		if($post){
			$msg = '';

			//验证信息
			if(!$this->checkData($post, $msg)){
				return $msg;
			}

			//转换成数据库字段
			$postData        = $this->_formatScenicInfo($post, 'add');
			$landscapesModel = $this->load->model('landscapes');
			$landscapesModel->add($postData);
			$addId           = $landscapesModel->getAddID();
			if($addId){
				$postData['id'] = $addId;
				$this->saveLandscapeAudience($addId, $post['audience']);
				$this->saveLandscapeTheme($addId, $post['theme']);
				return json_encode(array('data'=>array($postData)));
			}else{
				return  $this->_getUserError(-2);
			}
		}else{
			return  $this->_getUserError(-1);
		}
	}

	/**
	 * 修改景区  
	 * @param array $post 景区数据 
	 * @return json
	 */
	public function updateScenic($post)
	{
		if($post){
			if(!$post['id'] || !is_numeric($post['id'])){
				return  $this->_getUserError(-3);
			}

			$msg = '';
			//验证信息
			if(!$this->checkData($post, $msg)){
				return $msg;
			}

			//转换成数据库字段
			$postData        = $this->_formatScenicInfo($post, 'update');
			$landscapesModel = $this->load->model('landscapes');
			$lastEditModel   = $this->load->model('landscapeLastEdit');
			$id = $postData['id'];
			unset($postData['id']);
			$result          = $landscapesModel->update($postData, array('id'=>$id));
			$lastResult      = $lastEditModel->update($postData, array('landscape_id'=>$id));
			$affectedRows    = $landscapesModel->affectedRows();
			if($result && $affectedRows >= 1){
				$this->saveLandscapeAudience($postData['id'], $post['audience']);
				$this->saveLandscapeTheme($postData['id'], $post['theme']);
				return json_encode(array('data'=>array($postData)));
			}else{
				return  $this->_getUserError(-2);
			}
		}else{
			return  $this->_getUserError(-1);
		}
	}

	/**
	 * 删除景区  逻辑删除
	 * @param array $post 景区数据 
	 * @return json
	 */
	public function deleteScenic($post)
	{
		$landscapesModel = $this->load->model('landscapes');
		$now             = date('Y-m-d H:i:s', time());
		$result          = $landscapesModel->update(array('deleted_at' => $now,'updated_at' => $now),array('id'=> $post['id']));
		$affectedRows    = $landscapesModel->affectedRows();
		if($result && $affectedRows >= 1){
			return '{"succ":"succ"}';
		}else{
			return $this->_getUserError(-17);
		}
	}

	/**
	 * 景区数据检测
	 * @param array $post 景区数据 
	 * @param string $msg 返回的错误信息
	 * @return json
	 */
	public function checkData($post, &$msg)
	{
		$validateTool = $this->load->tool('validate');

		$organizationType = $this->load->model('organizations')->getOrganizationType($_SESSION['backend_userinfo']['organization_id']);
		//假如是景区的话，需要poi，假如是机构的话需要授权书
		if($organizationType == 'landscape'){
			if(!$post['poi_id'] || !is_numeric($post['poi_id'])){
				$msg = $this->_getUserError(-18);
				return false;
			}
		}
		if($organizationType == 'agency'){
			if(!$post['impower_id'] || !is_numeric($post['impower_id'])){
				$msg = $this->_getUserError(-19);
				return false;
			}
		}

		//景区名称
		if(!$validateTool->validate('required', $post['name']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 50, 'value' => $post['name']))){
			$msg = $this->_getUserError(-4);
			return false;
		}

		//景区级别
		if($post['landscape_level_id'] == '__NULL__'){
			$msg = $this->_getUserError(-5);
			return false;
		}

		//省市区
		if($post['area'] != '__NULL__' && $post['area']){
			$postData['district_id'] = $post['area'];
		}elseif($post['city'] != '__NULL__' && $post['city']){
			$postData['district_id'] = $post['city'];
		}elseif($post['province'] != '__NULL__' && $post['province']){
			$postData['district_id'] = $post['province'];
		}else{
			$postData['district_id'] = '';
		}
		if(!$post['city']){
			$msg = $this->_getUserError(-6);
			return false;
		}

		//开放时间 
		if(!$validateTool->validate('required', $post['hours']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 100, 'value' => $post['hours']))){
			$msg = $this->_getUserError(-7);
			return false;
		}

		//联系电话
		if(!$validateTool->validate('required', $post['phone'])){
			$msg = $this->_getUserError(-8);
			return false;
		}
		if(!$validateTool->validate('mobile', $post['phone']) && 
			!$validateTool->validate('phone', $post['phone'])){
			$msg = $this->_getUserError(-9);
			return false;
		}

		//联系地址 
		if(!$validateTool->validate('required', $post['address']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 100, 'value' => $post['address']))){
			$msg = $this->_getUserError(-10);
			return false;
		}

		//取票地址 
		if(!$validateTool->validate('required', $post['exaddress']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 100, 'value' => $post['exaddress']))){
			$msg = $this->_getUserError(-11);
			return false;
		}

		//景点主题
		// if(!$post['theme']){
		// 	$msg = $this->_getUserError(-12);
		// 	return false;
		// }

		//适合人群 
		// if(!$post['audience']){
		// 	$msg = $this->_getUserError(-13);
		// 	return false;
		// }

		//景区介绍 
		if(!$validateTool->validate('required', $post['biography']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 5000, 'value' => $post['biography']))){
			$msg = $this->_getUserError(-14);
			return false;
		}

		//购票须知 
		if(!$validateTool->validate('required', $post['note']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 5000, 'value' => $post['note']))){
			$msg = $this->_getUserError(-15);
			return false;
		}

		//交通指南
		if(!$validateTool->validate('required', $post['transit']) || 
			!$validateTool->validate('lengthBetweenAnd', array('minSize' => 1,'maxSize' => 500, 'value' => $post['transit']))){
			$msg = $this->_getUserError(-16);
			return false;
		}

		return true;
	}

	//将表单数据转换为表里需要的数据
	private function _formatScenicInfo($post, $formatType)
	{
		$postData = array(
			'name'               => $post['name'],
			'landscape_level_id' => $post['landscape_level_id'],
			'address'            => $post['address'],
			'thumbnail_id'       => $post['thumbnail_id'],
			'phone'              => $post['phone'],
			'hours'              => $post['hours'],
			'exaddress'          => $post['exaddress'],
			// 'description'        => $post['description'],
			'biography'          => $post['biography'],
			'note'               => $post['note'],
			// 'rank'               => $post['rank'],
			'transit'            => $post['transit'],
			'lat'            	 => $post['lat'],
			'lng'           	 => $post['lng'],
			'sys'           	 => intval($post['sys']),
		);

		//省市区
		if($post['area'] != '__NULL__' && $post['area']){
			$postData['district_id'] = $post['area'];
		}elseif($post['city'] != '__NULL__' && $post['city']){
			$postData['district_id'] = $post['city'];
		}elseif($post['province'] != '__NULL__' && $post['province']){
			$postData['district_id'] = $post['province'];
		}else{
			$postData['district_id'] = '';
		}

		$organizationType = $this->load->model('organizations')->getOrganizationType($_SESSION['backend_userinfo']['organization_id']);

		if($organizationType == 'agency'){
			$postData['impower_id'] = $post['impower_id'];
		}

		//添加和更新的数据结构
		$now = date('Y-m-d H:i:s', time());
		if($formatType == 'update'){
			$postData['updated_at']      = $now;
			$postData['id']              = $post['id'];
		}else{
			$postData['created_at']      = $now;
			$postData['created_by']      = $_SESSION['backend_userinfo']['id'];
			$postData['organization_id'] = $post['organization_id'] ? $post['organization_id'] :$_SESSION['backend_userinfo']['organization_id'];
		}

		return $postData;
	}

	//保存景区的适合人群
	public function saveLandscapeAudience($landscapeId, $audienceIds)
	{
		$landscapeAudienceModel = $this->load->model('landscapeAudience');
		return $landscapeAudienceModel->saveLandscapeAudience($landscapeId, $audienceIds);
	}

	//保存景区的景点主题
	public function saveLandscapeTheme($landscapeId, $themeIds)
	{
		$landscapeThemeModel = $this->load->model('landscapeTheme');
		return $landscapeThemeModel->saveLandscapeTheme($landscapeId, $themeIds);
	}

	/**
	 * 获取景区列表信息 带分页
	 * @return json
	 */
	public function getScenicInfo($param = array(), $id = 0)
	{
		$landscapesModel = $this->load->model('Landscapes');
		if($id != 0){
			if(is_numeric($id)){
				$param['filter']['id']    = $id;
			}else{
				$param['filter']['id|in'] = explode(',', $id);
			}
		}
		$result          = $landscapesModel->commonGetList($param);
		return $result;
	}

	/**
	 * 加入到function.php中，方便全局调用
	 * 通过parent的code获取城市信息 默认0 表示获取省级的
	 * @param int $code 城市的code，default 0 
	 * @return array
	 */
	public function getCityInfo($code = '0')
	{
		$districtsModel = $this->load->model('districts');
		return $districtsModel->findChildById($code);
	}

	/**
	 * 适合人群 暂时取全部的
	 * 
	 * @todo 加上其他条件
	 * @return array
	 */
	public function getAudienceInfo($param = array(), $all = '')
	{
		//取全部
		if($all == 'all'){
			$landscapeAudiencesModel = $this->load->model('landscapeAudiences');
			$param                   = array(
				'filter' => array(
					'deleted_at' => null
				)
			);
			return $landscapeAudiencesModel->commonGetList($param);
		}
	}

	/**
	 * 景点主题
	 * 
	 * @return array
	 */
	public function getThemeInfo($param = array(), $all = '')
	{
		//取全部
		if($all == 'all'){
			$landscapeThemesModel = $this->load->model('landscapeThemes');
			$param                   = array(
				'filter' => array(
					'deleted_at' => null
				)
			);
			return $landscapeThemesModel->commonGetList($param);
		}
	}

	/**
	 * 景区级别
	 * 
	 * @return array
	 */
	public function getLandscapeLevels($param = array())
	{
		$landscapeLevelsModel = $this->load->model('landscapeLevels');
		$param                   = array(
			'filter' => array(
				'deleted_at' => null
			)
		);
		return $landscapeLevelsModel->commonGetList($param);
	}

	//获取景区相关的一些可变信息
	public function getScenicRelation()
	{
		//获取景区级别信息
		$levelInfo            = $this->getLandscapeLevels();
		$data['levelInfo']    = $levelInfo['data'];

		//获取城市信息
		$cityInfo             = $this->getCityInfo();
		$data['cityInfo']     = $cityInfo;

		//适合人群
		$audienceInfo         = $this->getAudienceInfo(array(), 'all');
		$data['audienceInfo'] = $audienceInfo['data'];

		//景点主题
		$themeInfo            = $this->getThemeInfo(array(), 'all');
		$data['themeInfo']    = $themeInfo['data'];
		return $data;
	}

	//获取景区的详情
	public function getScenicDetail($id)
	{
		$data               = $this->getScenicRelation();
		$param              = array(
			'relate'   => 'audience,theme,thumbnail,level,impower',
			'with'     => 'districts',
			'filter'   => array(
				'deleted_at' => null,
			),
		);
		$scenicInfo         = $this->getScenicInfo($param, $id);
		$data['scenicInfo'] = $scenicInfo['data'][0];

		//所在2级区域所有信息
		if($firstCityCode = $data['scenicInfo']['districts'][0]['id']){
			$secondArea = $this->getCityInfo($firstCityCode);
			$data['secondArea'] = $secondArea;
		}

		//所在3级区域所有信息
		if($secondCityCode = $data['scenicInfo']['districts'][1]['id']){
			$thirdArea = $this->getCityInfo($secondCityCode);
			$data['thirdArea'] = $thirdArea;
		}

		//图片的真实url地址
		if($data['scenicInfo']['thumbnail']['url']){
			$data['scenicInfo']['thumbnail']['url'] = $this->load->common('attachments')->getFileHttpUrl($data['scenicInfo']['thumbnail']['url']);
		}

		return $data;
	}

	//获取景区的状态
	public static function getScenicStatus($status = '')
	{
		if($status){
			return self::$scenicStatus[$status];
		}else{
			return self::$scenicStatus;
		}
	}

	public function saveLandscapes($post) {
		$m = $this->load->model('landscapes');
        $oc     = $this->load->common('organization');  
        // 开启事务处理 
        $m->begin();
		$relation = $post['relation'];
		unset($post['relation']);
		if ($relation['monitor_id']) {
			$monitorScenicModel = $this->load->model('monitorScenic');
			$monitorScenicModel->update($relation, array('scenic_id' => $post['id']));
			if ($monitorScenicModel->affectedRows() == 0) {
				$relation['scenic_id'] = $post['id'];
				$monitorScenicModel->add($relation);
			}
		}
		// 保存机构信息
        $rt = $oc->save($post);
        if (!$this->hasError($rt)) {
	        if($post['pageType'] == 'edit'){
            	$oid = intval($post['id']);
            	$row = $m->getOne(array('organization_id'=>intval($oid)));
        		// 更新景区信息
        		$post['id'] = $row['id'];
        		unset($post['status']);
            	$rt = $this->updateScenic($post);  
	        } else {
	        	// print_r($post);
	        	// 新建景区信息
            	$post['organization_id'] = $post['id'];
	            unset($post['status'],$post['id']);
	            $rt = $this->addScenic($post);
	        }
    	}

        // $m->rollback();

        if ($this->hasError($rt))
        	$m->rollback();
        else
        	$m->commit();

        return $rt;
        
	}
}
