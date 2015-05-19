<?php
/**
 * 图表统计
 * @authors Kelly (kalaiya@126.com)
 * @date    2015-01-19 11:38:10
 * @version $Id$
 */

class StatController extends Controller {
	private $data=array();
	private $params=array();
	private $debug=false;
	/**
	 * 图表统计
	 * return array
	 */
	public function actionIndex()
	{
		$this->params = $_REQUEST;
		$param = array();
		$org_id = Yii::app()->user->org_id; 
		if (intval($org_id) > 0) {
			if(isset($this->params['tab']) && $this->params['tab']=='1'){
				$this->actionAgencysales();
			}
			elseif(isset($this->params['tab']) && $this->params['tab']=='2'){
				$this->actionProductsales();
			}
			else{
				$this->actionAgencysales(true);
				$this->actionProductsales(true);
			}
			$this->data['param'] = $this->params; 
			$this->render('index', $this->data);
		}
	}

	/**
	 * 销售额与人次统计
	 * date 	是 	string 	“2015-01-21 - 2015-01-21”
	 * supplier_id 	是 	int 	供应商id
	 * distributor_id 	是 	int 	分销商id
	 * cooperation_type 	是 	int 	0合作的所有分销商，1未合作的所有分销商，2单个分销商
	 * return array
	 */
	public function actionAgencysales($return=false){
		$param1 = $param = $agency = array();
		$org_id = Yii::app()->user->org_id; 
		$agency_ids = '';
		

		if(isset($this->data['agency'])){
			$agency = $this->data['agency'];
		}else{
			$param1 = array('supplier_id' => $org_id,'items'=>1000);
			$rs = Credit::api()->lists($param1);
			$agency = empty($rs['body']) ? array() : $rs['body']['data'];	
		}
		
		/*已合作分销商*/
		if($return){
			$this->data['agency'] = $agency;
		}else{
			$this->params = $_REQUEST;
		}	

		foreach ($agency as $key => $value) {
			if(!empty($agency_ids)) $agency_ids .=',';
			$agency_ids .= $value['distributor_id'];
		}

		/*分乐商销售额统计*/
		$date = isset($this->params['start_date']) && isset($this->params['end_date']) ? $this->params['start_date'].' - '.$this->params['end_date'] : date('Y-m-01', strtotime('-1 month')).' - '.date('Y-m-t', strtotime('-1 month'));
		
		// if(isset($this->params['distributor_id'])){

		if(isset($this->params['distributor_id'])&&$this->params['distributor_id']=='m'){
			$this->params['distributor_id'] = $agency_ids;
			$type = 0;
		}elseif (isset($this->params['distributor_id'])&&$this->params['distributor_id']=='p') {
			$this->params['distributor_id'] = $agency_ids;
			$type = 1;
		}elseif(!isset($this->params['distributor_id'])){
			$this->params['distributor_id'] = $agency_ids;
			$type = 0;
		}
		else{
			$type = 2;
		}
		$param2 = array('supplier_id'=>$org_id,
			'distributor_id'=>$this->params['distributor_id'],
			'cooperation_type'=>$type,
			'date'=>$date );
			//var_dump($param2);exit;
			$result = Stat::api()->list($param2); //echo "string"; var_dump($result);exit;
			if ($result['code'] == 'succ') { 
				$this->data['flag'] = 0 ;
				$this->data['agencylists'] = $result['body'];
			}else{
				$this->data['agencylists'] = array();
				$this->data['flag'] = 0 ;
			}
		// }elseif($this->debug){
		// 	$this->data['agencylists'] = $this->testdata1();
		// }else
		// $this->data['agencylists'] = [];

			if($return) return $this->data;
			else{
       	// $this->data['param'] = $this->params; 
				echo json_encode($this->data);
			}
		}

	/**
	 * 产品人次图
	 * date_type 	是 	int 	时间类型，1年份，2月份
	 * date 	是 	string 	按年传年“2015”,按月传月“2015-12”
	 * product_id 	是 	int 	产品id
	 * supplier_id 	是 	int 	供应商id
	 * return array
	 */ 
	public function actionProductsales($return=false){ 
		$para = array();
		$para1 = array();
		$param = array();
		$org_id = Yii::app()->user->org_id; 
		if($return){
			$para = array('or_id'=>$org_id,'state'=>'1,2','show_all'=>1,'fields'=>'id,name', 'show_items'=>0);
			$resultproduct = Tickettemplate::api()->lists($para);
			if ($resultproduct['code'] == 'succ') { 
				$this->data['productlists'] = $resultproduct['body']['data'];
			}
		}else{
			$this->params = $_REQUEST;
		}

		if(isset($this->data['agency'])){
			$agency = $this->data['agency'];
		}else{
			$param1 = array('supplier_id' => $org_id,'items'=>1000);
			$rs = Credit::api()->lists($param1);
			$agency = empty($rs['body']) ? array() : $rs['body']['data'];	
		}
		
		$agency_ids = '';
		foreach ($agency as $key => $value) {
			if(!empty($agency_ids)) $agency_ids .=',';
			$agency_ids .= $value['distributor_id'];
			$this->data['agencys'][$value['distributor_id']] = $value['distributor_name'];
		}
		//echo $agency_ids;exit;
		if($this->debug) {
			$agency_ids .=',5,8,9,10';
			$this->params['productid'] = '731,732';
			$this->data['agencys']['5'] = '合作分销商test1';
			$this->data['agencys']['8'] = '合作分销商test2';
			$this->data['agencys']['9'] = '合作分销商test3';
			$this->data['agencys']['10'] = '合作分销商test4';
		}

		//debug::ee($this->data['productlists']);
		if(!empty($agency_ids)){
				$month = isset($this->params['mouth']) ? $this->params['mouth'] : date('Y-m', strtotime('-1 month'));
				$this->params['mouth'] = $month;
				if(isset($this->params['productid']) && !empty($this->params['productid'])) 
					$productid = $this->params['productid'];
				else{
					$productid = '';
					if(isset($this->data['productlists'])) 
						foreach ($this->data['productlists'] as $key => $value) {
							if(!empty($productid)) $productid .= ',';
							$productid .= $value['id'];
						}
				}
				
				if(!empty($productid)){
					$param = array('supplier_id' => $org_id,'product_id'=>$productid,'date_type'=>'2','date'=>$month,'distributor_id'=>$agency_ids);
					$result = Stat::api()->product($param); //
					if ($result['code'] == 'succ') {
						// $this->data['param'] = $param;
						$this->data['rencilists'] = $result['body'];
						$this->data['flag'] = 0; 
					}elseif($this->debug){
						$this->data['rencilists'] = $this->testdata2();
					}
				}else{
					$this->data['flag'] = 1; 
					$this->data['msg'] = '统计出错,请联系管理员'; 
					$this->data['rencilists'] = array();
				}
			}else{
				$this->data['flag'] = 1; 
				$this->data['msg'] = '分销商ID为空,请联系管理员'; 
				$this->data['rencilists'] = array();
			}


			if($return) return $this->data;
			else{
       	// $this->data['param'] = $this->params; 
				echo json_encode($this->data);
			}
		}

	/**
	 * 测试数据
	 */
	private function testdata1(){
		$agencylists = array(
			"2015-01-22"=>array(
			"supplier_id"=>"123",
			"supplier_name"=>"供应商",
			"distributor_id"=>"23",
			"distributor_name"=>"分销商1",
			"num_total"=>"60",
			"price_total"=>"100.00"
			),
			"2015-01-23"=>array(
			"supplier_id"=>"123",
			"supplier_name"=>"供应商",
			"distributor_id"=>"23",
			"distributor_name"=>"分销商2",
			"num_total"=>"80",
			"price_total"=>"120.00"
			),
			"2015-01-25"=>array(
			"supplier_id"=>"123",
			"supplier_name"=>"供应商3",
			"distributor_id"=>"23",
			"distributor_name"=>"分销商",
			"num_total"=>"50",
			"price_total"=>"130.00"
			),
			"2015-01-26"=>array(
			"supplier_id"=>"123",
			"supplier_name"=>"供应商4",
			"distributor_id"=>"23",
			"distributor_name"=>"分销商",
			"num_total"=>"20",
			"price_total"=>"343.00"
			)												
			); 
		return $agencylists;
	}

	/**
	 * 测试数据
	 */
	private function testdata2(){
		$rencilists = array(
			"top8" =>array(
				"122"=>array(
					"distributor_id"=>"23",
					"distributor_name"=>"合作分销商1",
					"num"=>"25",
					),
				"123"=>array(
					"distributor_id"=>"24",
					"distributor_name"=>"合作分销商3",
					"num"=>"25",
					)
				),
			"other"=>array(
				"num"=>"25",
				),
			"other_all"=>array(
				"num"=>"20"
				)
			);

		return $rencilists;
	}
}