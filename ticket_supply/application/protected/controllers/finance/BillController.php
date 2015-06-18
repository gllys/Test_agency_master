<?php

class BillController extends Controller
{
	public function actionIndex()
	{	
		$org_id = Yii::app()->user->org_id;
		if(!empty($org_id) && intval($org_id) > 0){
			$param = $_REQUEST;
			$param['is_export'] = empty($param['is_export']) ? false : true;
			$param['supply_id'] = $org_id;
			if(isset($param['agency_name']) && trim($param['agency_name']) == '汇联' && strlen(trim($param['agency_name'])) == 6){
				$param['agency_name'] = '-';
			}
			$param['current'] = isset($param['page']) ? $param['page'] : 1;
			$param['items'] = $param['is_export'] ==true?1000:20;
			
			$data = $this->getApiLists($param,$param['is_export'],$data = array());  //导出数据处理
			if ($data['lists']['result']['code'] == 'succ') {
				if($param['is_export'] != true) {
					$data['pages'] = new CPagination($data['lists']['pagination']['count']);
					$data['pages']->pageSize = $param['items'];
				}
			}
		}
		
		$this->render('index',$data);
	}


	private function getApiLists($param,$is_export,$data)
	{     
		$pagination =null;
		$result = null;
		if($is_export==true)
		{
			$this->renderPartial("excelTop",$data);
		}
		do{

			$result = Bill::api()->lists($param);
			$param["current"] = ((int)trim($param["current"]))+1;
			$param["page"] = $param["current"];
			if($result['code'] == 'succ') {
				$pagination = $result['body']['pagination'];
				$data['lists'] = array("data"=>$result['body']["data"],"pagination"=>$pagination,"result"=>$result);

				if($is_export ==true)
				{
					$this->renderPartial("excelBody",$data);
				}
			}

		}while($param["current"]<1000 && $is_export==true && $result['code'] == 'succ' && $pagination['current']<$pagination['total']);
		if($is_export==true)
		{

			$this->renderPartial("excelBottom",$data);
			exit;
		}
		return $data;
	}

}
