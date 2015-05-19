<?php    
/*
 * homerec
 * created by xuejian
 * */
class HomerecController extends Controller {

	/**
	 * 显示首页推荐列表
	 */
	public function actionIndex() {
	    $params = $_REQUEST;
		$params['current'] = empty($params['p'])?1:$params['p'];
		$params['items'] = 15;
        $rs = Recommend::api()->lists($params);
		$prs = Recommend::api()->poslist();
		$data = array();
		$pos = array();
		if(ApiModel::isSucc($prs)) {
			$poslists = ApiModel::getData($prs);
			foreach ($poslists as $v) {
				$pos[$v['id']] = $v['name'];
			}
			$data['posinfo'] = $pos;
		}

		if(ApiModel::isSucc($rs)) {
			$body = ApiModel::getData($rs);
			$data['pages'] = new CPagination($body['pagination']['count']);
			$data['pages']->pageSize = $params['items'];
			$data['datas'] = $body['data'];
			
			$this->render("index", $data);
		}
	}
	
	/**
	 * 添加推荐
	 */
	public function actionAdd() {
		
		$this->render("add");
	}
	
	/**
	 * 编辑推荐
	 */
	public function actionEdit() {
		$id = $_GET['id'];
		$rs = Recommend::api()->lists(array('ids'=>$id));
		$data = current(ApiModel::getLists($rs));
		$this->render("edit", $data);
	}
	
	/**
	 * 保存首页推荐
	 */
	public function actionSaveRec() {

		$_POST['uid'] = Yii::app()->user->uid;
		$_POST['pos_id'] = join(',', $_POST['pos_id']);
		$_POST['start_time'] = strtotime($_POST['start_time']);
		$_POST['end_time'] = strtotime($_POST['end_time'])+3600*24-1;
		$rs = $_POST['id'] ? Recommend::api()->update($_POST):Recommend::api()->add($_POST);
		echo json_encode($rs);
	}
	
	/**
	 * 删除推荐
	 */
	public function actionDel()
	{
		$id = $_POST['id'];
		$rs = Recommend::api()->update(array('id'=>$id,'deleted_at'=>time(),'uid'=>Yii::app()->user->uid));
		echo json_encode($rs);
	}
	
	/**
	 * 发布首页推荐
	 */
	public function actionPub() {

		$id = $_POST['id'];
		$status = $_POST['status'];
		$rs = Recommend::api()->update(array('id'=>$id,'status'=>$status,'uid'=>Yii::app()->user->uid));
		echo json_encode($rs);
	}
	
}
