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
        $this->count = $BillModel->countResult($where);
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
        $result['created_at'] = date('Y-m-d H:i:s', $bill['created_at']);
        $result['pay_status'] = $bill['pay_status'];
        $result['receipt_status'] = $bill['receipt_status'];
        $result['bill_amount'] = $bill['bill_amount'];
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
        $id <=0 && Lang_Msg::error('ERROR_BILL_1');

        $billModel = BillModel::model();
        $bill = $billModel->getById($id);
        !$bill && Lang_Msg::error('ERROR_BILL_2');

        // 开始事务处理
        $field = $type == 1 ? 'receipt_status' : 'pay_status';
        $billModel->begin();
        $updata = array(
            $field => 1,
            );
        if($type==0) $updata['payed_at'] = time();
        $rt = $billModel->updateById($id, $updata);
        if (!$rt) {
            $billModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }

        $billModel->commit();

        // 开始日志处理
        Log_Bill::model()->add(array('type'=>2, 'bill_id'=>$id, 'content'=>'finish '.$type));

        $result = array();
        $result['id'] = $id;
        $result[$field] = 1;
        Tools::lsJson(true, Lang_Msg::getLang('ERROR_OPERATE_0'), $result);
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

}