<?php 
	class TemplateController  extends Controller{
		public function actionIndex(){

			//$get = $this->getGet();
			$params   = $_REQUEST;
	        //$param['current'] = isset($params['p']) ? $params['p'] : 1;
	        
	        $params['current'] = isset($params['page']) ? $params['page'] : 1;
	        $params["items"] = 20;

	        $data["get"] = $params;
	        $result = Ticketsprints::api()->lists($params);

	        $data['list'] = isset($result['message']["list"]) ? $result['message']["list"] : array();
	       // $data["pages"] = isset($result['message']["pages"]) ? $result['message']["pages"] : array();
	        //$this->pre($result);
	         $data['pages'] = new CPagination($result['message']['pagination']['count']);
             $data['pages']->pageSize = $params['items'];
			$this->render('index',$data);
		}

		/**
	     * 新增
	     */
	    public function actionAdd() {
	        $params   = $_REQUEST;
	        $data = array();

	        $rs = Landscape::api()->usedList(array('show_all'=>1), 0);
	        $data['landscape_list'] = ApiModel::getLists($rs);
	        $this->render('add', $data);
	    }
	    /**
		 *修改
	     **/
	    public function actionEdit(){
	    	$params   = $_REQUEST;
	        $data = array();
	        if (!empty($params['id'])) {
	        	$params['current'] = isset($params['page']) ? $params['page'] : 1;
	        	$params["items"] = 20;
	            $result = Ticketsprints::api()->lists($params);
	            //$this->pre($result);die;
	            $data = isset($result['message']["list"][0]) ? $result['message']["list"][0] : array();
	        }
	        $rs = Landscape::api()->usedList(array('show_all'=>1), 0);
	        $data['landscape_list'] = ApiModel::getLists($rs);
	        //$this->pre($data);
	        $this->render('edit', $data);		
	    }
		public  function pre($arr){
			if(is_array($arr)){
				echo "<pre>";
				print_r($arr);
				echo("</pre>");
			}else{
				var_dump($arr);die;
			}
		}
		public function actionSave() {
	        $post = $_REQUEST;
	        $post['landscape_id'] = $post['scenic_id'];
	        $post['supplier_id'] = 0;
	        $post['path'] = 'path';
	        $ret = Ticketsprints::api()->template($post);
	        if($ret["code"]=="fail"){
	        	if(!empty($ret["message"]["name"][0])){
	        		$this->_end(1, $ret["message"]["name"][0]);  die;
	        	}else if(!empty($ret["message"]["image"][0])){
	        		$this->_end(1, $ret["message"]["image"][0]);  die;
	        	}
	        }elseif($ret["code"]=="succ"){
	        	$this->_end(0, $ret["message"]);  die;
	        }
    	}

}

?>