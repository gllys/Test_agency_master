<?php
/**
 * 充值优惠
 * 2015-03-06
 * @author qiuling
 */
class RebateController extends Controller
{
    /**
     * 充值优惠列表
     */
	public function actionIndex(){        
        $datas = Coupon::api()->lists(array('items'=>10000,'can_use'=>'true'));
        $lists = ApiModel::getLists($datas);

       $this->render('index', compact('lists'));
	}
    /**
     * 充值优惠记录
     */
    public function actionIndex2(){       
        
        $param = $_REQUEST;

        if(isset($param['start_time']) && !empty($param['start_time']) && isset($param['end_time']) && empty($param['end_time'])){
            $param['paid_at'] = $param['start_time'].' - '.date('Y-m-d');
        }else if(isset($param['start_time']) && empty($param['start_time']) && isset($param['end_time']) && !empty($param['end_time'])){
            $param['paid_at'] = '2012-01-01'.' - '.$param['end_time'];
        }else if(isset($param['start_time']) && !empty($param['start_time']) && isset($param['end_time']) && !empty($param['end_time'])){
            $param['paid_at'] = $param['start_time'].' - '.$param['end_time'];
        }
        unset($param['start_time']);
        unset($param['end_time']);
        
        $param['current'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $param['items'] = 10;
        $datas = Coupon::api()->history($param);
        $lists = ApiModel::getLists($datas);

        //分页
        $pagination = ApiModel::getPagination($datas);

        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 10;
        $this->render('index2', compact('lists', 'pages'));
	}
    /**
     * 新增优惠方案
     */
    public function actionAdd(){
        //add
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            
            $param['uid'] = Yii::app()->user->uid;
            $param['start_time'] = strtotime($param['start_time']);
            $param['end_time'] = strtotime($param['end_time'])+86399;
            //Tickettemplatebase::api()->debug = true;
            $data = Coupon::api()->add($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $this->renderPartial('rebateadd');
    }
    /**
     * 编辑优惠方案
     */
    public function actionEdit(){
        //add
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            
            $param['uid'] = Yii::app()->user->uid;
            if(isset($param['start_time'])){
                $param['start_time'] = strtotime($param['start_time']);
                $param['end_time'] = strtotime($param['end_time'])+86399;
            }
            //Tickettemplatebase::api()->debug = true;
            $data = Coupon::api()->update($param);
            if (ApiModel::isSucc($data)) {
                $this->_end(0, $data['message']);
            } else {
                $this->_end(1, $data['message']);
            }
        }
        $data = Coupon::api()->lists($_GET);
        $items = ApiModel::getLists($data);
        $detail = array_pop($items);
        $this->renderPartial('rebateedit', compact('detail'));
    }
    
    /**
     *  开启关闭优惠方案
     * 15-01-20
     * xj
     */
    public  function  actionChange(){
        if (Yii::app()->request->isPostRequest) {
            $param = $_POST;
            $param['status'] =  $param['status'] == 1? 0:1;
            $res = Coupon::api()->update($param);
            if ($res['code'] != 'succ') {
                 echo '{"errors":{"msg":["'.$res['message'].'"]}}';
            } else{
                 echo '{"data":{"msg":["'.$res['message'].'"]}}';
            }
        }
    }
}
