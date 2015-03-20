<?php
use common\huilian\utils\Header;

/**
 * 淘宝对接
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/13/15
 * Time: 2:42 PM
 * File: TbController.php
 */

class TbController extends Controller
{
    public function actionIndex()
    {
        $org_id = Yii::app()->user->org_id;
        $data = array();
        if ($org_id) {
            $tb = Organizations::api()->taobaoOrgList(array(
                'organization_id' => $org_id

            ));
            if (ApiModel::isSucc($tb)) {
                $info = ApiModel::getData($tb);
                $info = reset($info['data']);
                if ($info['account'] != '') {
                    $data['tb'] = array(
                        'id' => $info['id'],
                        'account' => $info['account'],
                        'status' => $info['status']
                    );
                }
            }

            $products = Agencyproduct::api()->lists(array(
                'source' => 1,
                'agency_id' => $org_id,
                'current' => isset($_GET['page']) ? $_GET['page'] : 1
            ));
            if (ApiModel::isSucc($products)) {
                $data['lists'] = ApiModel::getLists($products);
                $pagination = ApiModel::getPagination($products) ;
                $pages = new CPagination($pagination['count']);
                $pages->pageSize = 15; #每页显示的数目
                $data['pages'] = $pages;

            }
        }
        $this->render('index', $data);
    }

    /**
     * 淘宝卖家账号设置
     * @author grg
     */
    public function actionSeller()
    {
        $account = Yii::app()->request->getPost('account');
        $tid = Yii::app()->request->getPost('tid');
        if (isset($account) && $account != '') {
            if (isset($tid) && intval($tid) != 0) {
                Organizations::api()->taobaoOrgUpdate(array(
                    'id' => $tid,
                    'organization_id' => Yii::app()->user->org_id,
                    'account' => $account,
                    'uid' => Yii::app()->user->uid
                ));
            } else {
                Organizations::api()->taobaoOrgAdd(array(
                    'organization_id' => Yii::app()->user->org_id,
                    'account' => $account,
                    'uid' => Yii::app()->user->uid
                ));
            }
        }

        $this->redirect('/channel/tb/');
    }

    /**
     * 淘宝产品绑定
     * @author grg
     */
    public function actionBinding()
    {
        $data = array(
            'product_id' => Yii::app()->request->getPost('product_id'),
            'agency_id' => Yii::app()->user->org_id,
            'product_name' => Yii::app()->request->getPost('product_name'),
            'product_price' => Yii::app()->request->getPost('product_price'),
            'source' => Yii::app()->request->getPost('source'),
            'payment' => Yii::app()->request->getPost('payment'),
            'payment_list' => Yii::app()->request->getPost('payment_list')
        );
        if (Yii::app()->request->getPost('is_update') == 1) {
            $data['id'] = Yii::app()->request->getPost('pid');
            $rs = Agencyproduct::api()->update($data);
        } else {
            $rs = Agencyproduct::api()->add($data);
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode(array(
            'code' => intval(ApiModel::isSucc($rs))
        ));
    }

    /**
     * 解除绑定
     * @author grg
     */
    public function actionUnbind()
    {
        $rs = Agencyproduct::api()->del(array(
            'id' => intval(Yii::app()->request->getPost('product_id'))
        ));
        header('Content-type: application/json; charset=utf-8');
        echo json_encode(array(
            'code' => intval(ApiModel::isSucc($rs))
        ));
    }

    /**
     * 查询绑定状态
     * @author grg
     */
    public function actionCode()
    {
        $rs = Agencyproduct::api()->lists(array(
            'source' => Yii::app()->request->getPost('source'),
            'product_id' => intval(Yii::app()->request->getPost('product_id')),
            'agency_id' => Yii::app()->user->org_id
        ));
        header('Content-type: application/json; charset=utf-8');
        if (ApiModel::isSucc($rs)) {
            $lists = ApiModel::getLists($rs);
            if(!empty($lists)){
                $data = $lists[0];
                echo json_encode(array(
                    'code' => 1,
                    'data' => array(
                        'id' => $data['id'],
                        'code' => $data['code'],
                        'payment' => $data['payment']
                    )
                ));
            }else{
                echo json_encode(array(
                    'code' => 0
                ));
            }
        } else {
            echo json_encode(array(
                'code' => 0
            ));
        }
    }

    /**
     * 产品选择
     * @author grg
     */
    public function actionTicket()
    {	Header::utf8();
        $param = $_GET;
        $param['is_fit']= 1;
        $param['state'] = 1;
        $param['agency_id'] = Yii::app()->user->org_id;
        $param['expire_end'] = time();
        $param['current'] = isset($param['page']) ? $param['page'] : 0;
        $reserve_list = TicketTemplate::api()->reserve_list($param,true,5);
        $lists = ApiModel::getLists($reserve_list);// 票array
		// 此处要查询出该产品在当前分销商的分销策略
        foreach($lists as $k => $v) {
        	$params = [
        		'ticket_id' => $v['id'],
        		'distributor_id' => Yii::app()->user->org_id,
        		'type' => 0,	// 0散客，1团客
        	];
        	$data = Tickettemplate::api()->ticketinfo($params);
        	if(!empty($data['body']['payment'])) {
        		$lists[$k]['payment'] = $data['body']['payment'];
        	}
        	
        }
        $lists = $this->CheckTb($lists);  //检查是否已经绑定过
        $pagination = ApiModel::getPagination($reserve_list) ;
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目
//         var_dump($lists);
//         exit;
        $this->render('ticket', compact('lists','pages','param'));
    }

    /*
     * 帮助文档
     * @author ccq
     */
    public function actionHelp(){
        $this->renderPartial('help');
    }

    /*
     * 查询该票是否已经绑定淘宝
     * @author ccq
     */
    public function CheckTb($lists = array()){
        $products = Agencyproduct::api()->lists(array(
            'source' => 1,
            'agency_id' => Yii::app()->user->org_id
        ));
        if (ApiModel::isSucc($products)) {
            $productLists = ApiModel::getLists($products);
        }

        if(isset($productLists) && !empty($productLists)){
            if(is_array($productLists)){
                foreach($productLists as $value){
                    $productArray[] = $value['product_id'];
                }
                $productIds = implode(',',$productArray);
            }

            if(is_array($lists) && !empty($lists)){
                foreach($lists as $key => $item){
                    if(strstr($productIds,$item['id']) != FALSE){
                        $lists[$key]['is_bind'] = 1;
                    }else{
                        $lists[$key]['is_bind'] = 0;
                    }
                }
            }
        }
        return $lists;

    }

    /*
     * 查询供应商与分销商之间是否绑定，确认信用以及储值是否可以使用
     * @author ccq
     */
    public function actionSupply(){
        $rs = Credit::api()->lists(array(
            'supplier_id' => Yii::app()->request->getPost('supplier_id'),
            'distributor_id' => Yii::app()->user->org_id
        ));

        if (ApiModel::isSucc($rs)) {
            $lists = ApiModel::getLists($rs);
            if(!empty($lists)){
                //分销策略
                $rs = Ticketpolicy::api()->detail(array(
                    'supplier_id' => Yii::app()->request->getPost('supplier_id'),
                    'id' => Yii::app()->request->getPost('policy_id'),
                    'show_items' => 1
                ));
                if (ApiModel::isSucc($rs)) {
                    $d = ApiModel::getData($rs);
                    $detail = $d['items'][0];
                    $p = array();
                    if ($detail['credit_flag'] == 1) {
                        $p[] = 2;
                    }
                    if ($detail['advance_flag'] == 1) {
                        $p[] = 3;
                    }
                    $p[] = 4;
                    echo json_encode(array(
                        'code' => 1,
                        'data' => implode(',', $p)
                    ));

                }
                echo json_encode(array(
                    'code' => 1
                ));
            }else{
                echo json_encode(array(
                    'code' => 0
                ));
            }
        }else{
            echo json_encode(array(
                'code' => 0
            ));
        }
    }
}
