<?php
/**
 * User: mosen
 * Date: 14-10-25
 */

class BillController extends Base_Controller_Api 
{

    /**
     * 账单列表
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function listsAction(){
        $where=array();
        $ids = trim(Tools::safeOutput($this->body['ids']));
        $ids && $where['id|in'] = explode(',',$ids);

        $agency_id = isset($this->body['agency_id']) ? intval($this->body['agency_id']) : null;
        $agency_id !== null && $where['agency_id'] = $agency_id;

        $supply_id = isset($this->body['supply_id']) ? $this->body['supply_id']: null;
        $supply_id && $where['supply_id'] = $supply_id;

        $bill_sd = $this->body['bill_sd'];
        $bill_sd && $where['created_at|>='] = strtotime($bill_sd.' 00:00:01'); //2014-12-11

        $bill_ed = $this->body['bill_ed'];
        $bill_ed && $where['created_at|<='] = strtotime($bill_ed.' 23:59:59');

        $pay_state = (!isset($this->body['pay_state']) || $this->body['pay_state']==='') ? null : intval($this->body['pay_state']);
        $pay_state !== null && $where['pay_status'] = $pay_state;

        $agency_name = trim(Tools::safeOutput($this->body['agency_name']));
        $agency_name && $where['agency_name|like'] = "%{$agency_name}%";

        $supply_name = trim(Tools::safeOutput($this->body['supply_name']));
        $supply_name && $where['supply_name|like'] = "%{$supply_name}%";
            
        $BillModel = BillModel::model();

        // 分页
        $data = array();
        $countRes = reset($BillModel->search($where,"count(*) as count,sum(bill_num) as order_nums,sum(bill_amount) as total_amount"));
        $this->count =$countRes['count'];
        $this->pagenation();
        if($this->count>0){
            $list = $BillModel->search($where,'*','created_at desc',$this->limit);
            foreach($list as $item) {
                $tmp = array();
                $tmp['id'] = $item['id'];
                $tmp['agency_name'] = $item['agency_name'];
                $tmp['supply_name'] = $item['supply_name'];
                $tmp['created_at'] = $item['created_at'];
                $tmp['bill_type'] = $item['bill_type'];
                $tmp['bill_amount'] = $item['bill_amount'];
                $tmp['bill_num'] = $item['bill_num'];
                $tmp['pay_status'] = $item['pay_status'];
                $tmp['receipt_status'] = $item['receipt_status'];
                $data[] = $tmp;
            }
        }

        $result = array(
            'data'=>$data,
            'order_nums'=>$countRes['order_nums'],
            'total_amount'=>$countRes['total_amount'],
            'pagination'=>array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items'=>$this->items,
                'total'=>$this->total,
            )
        );
        Lang_Msg::output($result);
    }

    /**
     * 账单详情
     * @return [type] [description]
     */
    public function detailAction(){
        $id = $this->body['id'];
        $id <=0 && Lang_Msg::error('ERROR_BILL_1');

        $billModel = BillModel::model();
        $bill = $billModel->getById($id);
        !$bill && Lang_Msg::error('ERROR_BILL_2');

        $billitemModel = BillitemModel::model();
        $where['bill_id'] = $id;
        $list = $billitemModel->share($bill['created_at'])->search($where);

        $result = array();
        $result['id'] = $id;
        $result['agency_id'] = $bill['agency_id'];
        $result['supply_id'] = $bill['supply_id'];
        $result['agency_name'] = $bill['agency_name'];
        $result['supply_name'] = $bill['supply_name'];
        $result['created_at'] = date('Y-m-d H:i:s', $bill['created_at']);
        $result['pay_status'] = $bill['pay_status'];
        $result['receipt_status'] = $bill['receipt_status'];
        $result['bill_type'] = $bill['bill_type'];
        $result['bill_amount'] = $bill['bill_amount'];
        $result['bill_num'] = $bill['bill_num'];
        $result['payed_at'] = $bill['payed_at'] ? date('Y-m-d H:i:s', $bill['payed_at']) : 0;
        $result['payed_img'] = $bill['payed_img'];
        $result['order_list'] = array();
        if ($list) {
            foreach($list as $item) {
                $tmp = array();
                $tmp['order_id'] = $item['order_id'];
                $tmp['ticket_name'] = $item['ticket_name'];
                $tmp['agency_id'] = $item['agency_id'];
                $tmp['supply_id'] = $item['supply_id'];
                $tmp['agency_name'] = $item['agency_name'];
                $tmp['supply_name'] = $item['supply_name'];
                $tmp['ordered_at'] = $item['ordered_at'] ? date('Y-m-d H:i:s', $item['ordered_at']) : 0;
                $tmp['use_day'] = $item['use_day'];
                $tmp['owner_name'] = $item['owner_name'];
                $tmp['owner_mobile'] = $item['owner_mobile'];
                $tmp['payed'] = $item['payed'];
                $tmp['refunded'] = $item['refunded'];
                $tmp['bill_amount'] = $item['bill_amount'];
                $result['order_list'][] = $tmp;
            }
        }
        Tools::lsJson(true, Lang_Msg::getLang('ERROR_OPERATE_0'), $result);
    }

    /**
     * 上传打款凭证
     * @return [type] [description]
     */
    public function upimgAction(){
        $id = $this->body['id'];
        $payed_img = $this->body['payed_img'];
        
        ($id <=0 || empty($payed_img)) && Lang_Msg::error('ERROR_BILL_1');

        $billModel = BillModel::model();
        $bill = $billModel->getById($id);
        !$bill && Lang_Msg::error('ERROR_BILL_2');

        // 开始事务处理
        $billModel->begin();
        $now = time();
        $rt = $billModel->updateById($id, array('payed_img'=>$payed_img, 'payed_at'=>$now));
        if (!$rt) {
            $billModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }

        $billModel->commit();

        // 开始日志处理
        Log_Bill::model()->add(array('type'=>1, 'bill_id'=>$id, 'content'=>'upimg|' . $payed_img));

        $result = array();
        $result['id'] = $id;
        $result['payed_at'] = date('Y-m-d H:i:s', $now);
        Tools::lsJson(true, Lang_Msg::getLang('ERROR_OPERATE_0'), $result);
    }

    /**
     * 确认打款收款
     * type 0打款 1收款
     * @return [type] [description]
     */
    public function finishAction() {
        $id = $this->body['id'];
        $type = intval($this->body['type']); // 0打款 1收款
        $id <=0 && Lang_Msg::error('ERROR_BILL_1'); //参数错误

        $billModel = BillModel::model();
        $bill = $billModel->getById($id);
        !$bill && Lang_Msg::error('ERROR_BILL_2'); //账单不存在

        // 开始事务处理
        $field = $type == 1 ? 'receipt_status' : 'pay_status';
        try {
            if ($type == 0 && $bill['pay_status']==1){
                Lang_Msg::error('已成功打过款！');
            }
            else if ($type == 1 && $bill['receipt_status']==1){
                Lang_Msg::error('已成功收过款！');
            }
            $billModel->begin();
            if($bill['bill_type']==1 || $bill['bill_type']==4) {
                $updata = array(
                    'receipt_status' => 1,
                    'pay_status' => 1,
                );
            } else {
                $updata = array(
                    $field => 1,
                );
            }
            if ($type == 0) $updata['payed_at'] = time();
            $rt = $billModel->updateById($id, $updata);
            if ($rt) { // zqf 2015-04-02
                if ($type == 0 && $bill['pay_status']!=1 && in_array(intval($bill['bill_type']), array(1, 4))) { //在线支付、平台支付的结款单打款，处理平台资金
                    $agencyStatics = $this->agencyStatic($bill); //结款单内分销商应打款额统计，agency_id=>money
                    if (!$agencyStatics) {
                        $billModel->rollback();
                        Lang_Msg::error($type == 0?"打款失败":"收款失败");
                    }
                    $user = $this->getOperator();
                    $params = array( //平台收支应付款打款接口
                        'agency_money' => json_encode($agencyStatics),
                        'supply_id' => $bill['supply_id'],
                        'bill_amount' => $bill['bill_amount'],
                    );
                    $params = $params+$user;
                    $r = ApiUnionMoneyModel::model()->inOut5($params);
                    if (!$r || $r['code'] == 'fail') {
                        $billModel->rollback();
                        Lang_Msg::error(($r && $r['code']) ? $r['message'] : "打款失败");
                    }
                }
            } else {
                $billModel->rollback();
                Lang_Msg::error($type == 0?"打款失败":"收款失败");
            }

            $billModel->commit();

            // 开始日志处理
            Log_Bill::model()->add(array('type' => 2, 'bill_id' => $id, 'content' => 'finish ' . $type));

            $result = array();
            $result['id'] = $id;
            if($bill['bill_type']==1 || $bill['bill_type']==4) {

                $result['receipt_status'] = 1;
                $result['pay_status'] = 1;
            } else {
                $result[$field] = 1;
            }
            Tools::lsJson(true, $type == 0?"打款成功":"收款成功", $result);
        }
        catch(Exception $e){
            $billModel->rollback();
            Lang_Msg::error($type == 0?"打款失败":"收款失败");
        }
    }

    private function agencyStatic($bill){
        if(is_numeric($bill)){
            $bill = BillModel::model()->getById($bill);
        }
        if(!$bill) return false;
        $billItems = BillitemModel::model()->share($bill['created_at'])->setGroupBy('agency_id')
            ->search(array('bill_id'=>$bill['id']),"id,agency_id,sum(bill_amount) as money");
        $ret = array();
        $bill_amount=0;
        foreach($billItems as $v){
            $ret[$v['agency_id']]=$v['money'];
            $bill_amount+=$v['money'];
        }
        return $bill_amount==$bill['bill_amount']?$ret:false;
    }


    /**
     * 获取平台结算设置
     * @return [type] [description]
     */
    public function getconfAction() {
        $result = ConfigModel::model()->getConfig(array('conf_bill_type', 'conf_bill_value'));
        Tools::lsJson(true, Lang_Msg::getLang('ERROR_OPERATE_0'), $result);
    }

    /**
     * 设置平台结算设置
     * @return [type] [description]
     */
    public function setconfAction() {
        $conf_bill_type = intval($this->body['conf_bill_type']);
        $conf_bill_type != 1 && $conf_bill_type = 0;

        $conf_bill_value = intval($this->body['conf_bill_value']);
        ($conf_bill_type==0 && ($conf_bill_value <=0 || $conf_bill_value >31)) && Lang_Msg::error('ERROR_BILL_1');
        ($conf_bill_type==1 && ($conf_bill_value <0 || $conf_bill_value >6)) && Lang_Msg::error('ERROR_BILL_1');

        $result = array(
            array('conf_bill_type',$conf_bill_type),
            array('conf_bill_value',$conf_bill_value)
            );
        $ConfigModel = ConfigModel::model();
        $ConfigModel->setConfig($result);

        // 开始日志处理
        Log_Bill::model()->add(array('type'=>3, 'content'=>json_encode($result)));
        
        $result = ConfigModel::model()->getConfig(array('conf_bill_type', 'conf_bill_value'));
        Tools::lsJson(true, Lang_Msg::getLang('ERROR_OPERATE_0'), $result);
    }

    /**
     * 立即结算
     * @author zqf
     * @param supplier_id int //供应商id
     * @param distributor_id int //分销商id
     * @param is_online int //1在线或平台支付，0信用支付
     */
    public function genbillAction(){
        $supplier_id = intval($this->body['supplier_id']);
        $distributor_id = intval($this->body['distributor_id']);
        $isOnline = intval($this->body['is_online']);

        !$supplier_id && Lang_Msg::error('ERROR_SALER_1'); //缺少供应商ID参数
        !$isOnline && !$distributor_id && Lang_Msg::error('ERROR_BUYER_1'); //缺少分销商ID参数

        $BillModel = new BillModel();
        $BillModel->begin();
        if($isOnline){
            $bill_ids = $BillModel->runOnlineBill($supplier_id,$this->body);
        }
        else {
            $bill_ids = $BillModel->runBill($supplier_id,$distributor_id,$this->body);
        }
        if($bill_ids) {
            $BillModel->commit();
            Yaf_Application::app()->getDispatcher()->getRequest()->setParam('organization_id',$supplier_id);
            foreach($bill_ids as $bill_id){
                Log_Bill::model()->add(array('type'=>1, 'bill_id'=>$bill_id, 'content'=>Lang_Msg::getLang('INFO_GEN_BILL_0')));
            }
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_GEN_BILL_0',array('n'=>count($bill_ids))),$bill_ids);
        }
        else {
            $BillModel->rollBack();
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_GEN_BILL_1'));
        }
    }

    /**
     * 结算订单
     * @author zqf
     * @date 2015-04-10
     * */
    public function orderAction(){
        $OrderModel = new OrderModel();
        $OrderItemModel = new OrderItemModel();

        $where = " WHERE ((o.status IN ('paid','finish') AND o.used_nums>0) OR o.status='billed') AND o.deleted_at=0 ";


        $distributor_id = intval($this->body['distributor_id']);
        $distributor_id && $where .= " AND o.distributor_id=".$distributor_id; //按分销商查找

        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where .= " AND o.supplier_id=".$supplier_id;  //按供应商查找

        $landscape_id = intval($this->body['landscape_id']);
        $landscape_id && $where .= " AND find_in_set({$landscape_id},o.landscape_ids)";  //按景区查找

        $time_type = intval($this->body['time_type']); //时间查找类型,0创建时间，1游玩日期，2入园日期

        $start_date = trim(Tools::safeOutput($this->body['start_date']));
        if($start_date){
            !preg_match("/^\d{4}-\d{2}-\d{2}$/",$start_date) && Lang_Msg::error('ERROR_START_DAY_1');
            $time_type==0 && $where .= " AND o.created_at>=".strtotime($start_date);
            $time_type==1 && $where .= " AND o.use_day>='{$start_date}' ";
            if($time_type==2){
                $where .= " AND o.updated_at>=".strtotime($start_date)." AND o.updated_at>o.created_at AND o.used_nums>0";
            }
        }

        $end_date = trim(Tools::safeOutput($this->body['end_date']));
        if($end_date){
            !preg_match("/^\d{4}-\d{2}-\d{2}$/",$end_date) && Lang_Msg::error('ERROR_END_DAY_1');
            $time_type==0 && $where .= " AND o.created_at<=".strtotime($end_date." 23:59:59");
            $time_type==1 && $where .= " AND o.use_day<='{$end_date}' ";
            if($time_type==2){
                $where .= " AND o.updated_at<=".strtotime($end_date." 23:59:59")." AND o.updated_at>o.created_at AND o.used_nums>0";
            }
        }

        $bill_status = trim(Tools::safeOutput($this->body['bill_status'])); //按结算状态查找
        if(preg_match("/^[\d,]+$/",$bill_status)){ //结算状态：0未结算，1已结算
            if($bill_status==1) { //已结款的订单,含部分结款
                $where .= " AND (o.status='billed' OR (o.status IN ('paid','finish') AND oi.bill_time>0)) ";
            } else if($bill_status==0) { //未结款支付过的订单,含部分未结款
                $where .= " AND o.status IN ('paid','finish') AND oi.bill_time=0 ";
            } else {
                $where .= " AND o.bill_status IN({$bill_status}) ";
            }
        } else { //暂留，准备弃用
            if ($bill_status == 'billed') { //已结款的订单,含部分结款
                $where .= " AND (o.status='billed' OR (o.status IN ('paid','finish') AND oi.bill_time>0)) ";
            } else if ($bill_status == 'unbill') { //未结款支付过的订单,含部分未结款
                $where .= " AND o.status IN ('paid','finish') AND oi.bill_time=0 ";
            }
        }

        $id = trim(Tools::safeOutput($this->body['id']));
        $id && $where .= " AND o.id='{$id}' "; //按订单号查找

        $ids = $this->body['ids'];
        (!is_array($ids) && preg_match("/^[\d,]+$/",$ids)) && $ids = explode(',',$ids);
        $ids && $where .= " AND o.id IN('".implode("','",$ids)."') "; //按订单ID查找

        $type = intval($this->body['type']) ? 1:0; //订单类型：0电子票订单1任务票订单
        isset($_POST['type']) && $where .= " AND o.type=".$type;

        $owner_name = trim(Tools::safeOutput($this->body['owner_name']));
        $owner_name && $where .= " AND o.owner_name LIKE '%{$owner_name}%' "; //按取票人

        $owner_mobile = trim(Tools::safeOutput($this->body['owner_mobile']));
        $owner_mobile && $where .= " AND o.owner_mobile='{$owner_mobile}' ";

        $owner_card = trim(Tools::safeOutput($this->body['owner_card']));
        $owner_card && $where .= " AND o.owner_card='{$owner_card}' "; //按取票人身份证

        $product_id = intval($this->body['product_id']);
        $product_id && $where .= " AND o.product_id=".$product_id; //按产品ID查找

        $source = intval($this->body['source']);
        isset($this->body['source']) && $where .= " AND o.source=".$source; //按外部来源查找 0默认 1淘宝 2八爪鱼 3同程 4途牛 5驴妈妈 6携程 7景点通 8度周末 9途家

        $local_source = intval($this->body['local_source']);
        $local_source && $where .= " AND o.local_source=".$local_source; //按内部来源查找 0分销系统 1OPENAPI 2浙风自由行 3微信

        $source_id = trim(Tools::safeOutput($this->body['source_id']));
        $source_id && $where .= " AND o.source_id='{$source_id}' "; //按外部来源ID查找

        $created_by = intval($this->body['created_by']);
        $created_by && $where .= " AND o.created_by=".$created_by;  //创建人

        $product_name = trim(Tools::safeOutput($this->body['product_name'])); //按产品票名称查询
        $product_name && $where .= " AND o.name LIKE '%{$product_name}%' ";

        $scenic_name = trim(Tools::safeOutput($this->body['scenic_name'])); //按景区名称查询
        if($scenic_name){
            $scenicIds = LandscapeModel::model()->getIdsByName($scenic_name);
            if($scenicIds){
                $scenicWhere = array();
                foreach($scenicIds as $scenicId){
                    array_push($scenicWhere,"find_in_set({$scenicId},o.landscape_ids)");
                }
                $where .= " AND (".implode(' OR ',$scenicWhere).")";
            } else {
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'statics'=>array()));
            }
        }

        $agency_name = trim(Tools::safeOutput($this->body['agency_name'])); //按分销商名称查询
        if($agency_name){
            $agencyIds = OrganizationModel::model()->getIdsByName($agency_name,'agency');
            if($agencyIds){
                $where .= " AND o.distributor_id IN('".implode("','",$agencyIds)."') ";
            }
            else{
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'statics'=>array()));
            }
        }

        $supply_name = trim(Tools::safeOutput($this->body['supply_name'])); //按供应商名称查询
        if($supply_name){
            $supplyIds = OrganizationModel::model()->getIdsByName($supply_name,'supply');
            if($supplyIds){
                $where .= " AND o.supplier_id IN('".implode("','",$supplyIds)."') ";
            }
            else{
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'statics'=>array()));
            }
        }

        $status = trim(Tools::safeOutput($this->body['status'])); //按状态查找
        if(in_array($status,$OrderModel->allStatus)) {
            $where .= " AND o.status='{$status}' ";
        }

        $fromTb = " FROM ".$OrderModel->getTable()." o LEFT JOIN ".$OrderItemModel->getTable()." oi ON(o.id=oi.order_id) ";
        $groupBy = " GROUP BY oi.order_id,oi.bill_time ";
        $orderBy = " ORDER BY o.id DESC";

        $countRes = $OrderModel->db->selectBySql( //订单金额统计
            "SELECT count(*) as order_nums, sum(nums) as total_nums,sum(used_nums) as total_used_nums,
            sum(refunding_nums) as total_refunding_nums,sum(refunded_nums) as total_refunded_nums,
            sum(refunded) as total_refunded,sum(payed) as total_payed,sum(amount) as total_amount
            FROM (SELECT o.* ".$fromTb .$where." GROUP BY o.id) t "
        );
        $countRes = empty($countRes)? array(
            "order_nums"=>0, "total_nums"=>0, "total_used_nums"=>0, "total_refunding_nums"=>0,
            "total_refunded_nums"=>0, "total_refunded"=>0, "total_payed"=>0, "total_amount"=>0
        ) : reset($countRes);

        $sql1 = "SELECT count(*) AS total FROM (SELECT o.* ".$fromTb.$where.$groupBy.$orderBy.") tb WHERE 1";
        $total = $OrderModel->db->selectBySql($sql1);
        $total = empty($total)?false:reset($total);
        $this->count = empty($total)?0:$total['total'];

        $this->pagenation();
        if ($this->count>0) {
            $fields = " SELECT o.*,count(case when oi.use_time=0 then oi.use_time end) as unused_num,
                    count(case when oi.use_time>0 then oi.use_time end) as used_num,oi.use_time,oi.bill_time ";
            $data = $OrderModel->db->selectBySql($fields.$fromTb.$where.$groupBy.$orderBy." LIMIT ".$this->limit);
        } else {
            $data = array();
        }

        foreach($data as $k=>$v) {
            if($v['bill_time']>0) $data[$k]['bill_status']=1;
        }
        $result = array(
            'data'=>array_values($data),
            'statics'=>$countRes,
            'pagination'=>array(  'count'=>$this->count,  'current'=>$this->current, 'items'=>$this->items, 'total'=>$this->total, )
        );
        Lang_Msg::output($result);
    }
}