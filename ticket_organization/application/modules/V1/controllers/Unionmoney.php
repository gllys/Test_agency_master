<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-12-11
 * Time: 下午4:56
 */

class UnionmoneyController extends Base_Controller_Api
{
    public function listsAction(){
        $where = array('deleted_at'=>0);
        $org_ids = trim(Tools::safeOutput($this->body['org_ids']));
        $balance_type = trim(Tools::safeOutput($this->body['balance_type']));
        if($org_ids && preg_match("/^[\d,]+$/",$org_ids)){
            $org_ids = explode(',',$org_ids);
        }
        if($balance_type!='' && $balance_type!=null && preg_match("/^[\d,]+$/",$balance_type)){
            $balance_type = explode(',',$balance_type);
            $where['balance_type|IN'] = $balance_type;
        }
        $org_name = trim(Tools::safeOutput($this->body['org_name']));
        if($org_name){
            $orgs = OrganizationModel::model()->search(array('name|like'=>array("%{$org_name}%")),'id,name');
            $orgs && $org_ids = array_keys($orgs);
            if(!$org_ids){
                $result = array(
                    'data'=>array(),
                    'pagination'=>array( 'count'=>0,'current'=>0,'items'=>$this->items,'total'=>0)
                );
                Lang_Msg::output($result);
            }
        }
        $org_ids && $where['org_id|IN'] = $org_ids;

        $UnionMoneyModel = new UnionMoneyModel();
        $UnionMoneyModel->cd = 60*5;
        $count = reset($UnionMoneyModel->search($where,'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        $res = $this->count?$UnionMoneyModel->search($where, $this->getFields('org_id'), $this->getSortRule('org_id'), $this->limit):array();
        if($res){
            if(!$orgs){
                $orgs = OrganizationModel::model()->search(array('id|in'=>array_keys($res)),'id,name');
            }
            foreach($res as $k=>$v){
                $res[$k]['org_name'] = $orgs[$v['org_id']]['name'];
            }
        }
        $data['data'] = array_values($res);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    /*     * *
     * 得到整个平台，部分共应商，或单个供应商 可用余额和冻结金额
     * @author fangshixiang
     */
    public function totalAction() {
        $where = array();
        $org_ids = array();
        if ($this->body['org_ids']) {
            $org_ids = explode(',', trim($this->body['org_ids']));
            $org_ids && $where['org_id|IN'] = $org_ids; //按支付单的ID查找
        }
        UnionMoneyModel::model()->cd = 60 * 5;
        $data = UnionMoneyModel::model()->statics($where,$org_ids?1:0);
        $return = !empty($data)?reset($data):array('total_union_money'=>0,'total_frozen_money'=>0,'total_activity_money'=>0);
        Lang_Msg::output($return);
    }

    public function detailAction(){
        $org_id = intval($this->body['org_id']);
        !$org_id && Lang_Msg::error('ERROR_SHOW_1'); //机构id不能为空
        $return = UnionMoneyModel::model()->setCd(5)->getById($org_id);
        !$return && Lang_Msg::error('ERROR_RECODE_NULL'); //记录不存在
        Tools::lsJson(true,'ok',$return);
    }

    public function setAction(){
        $org_id = intval($this->body['org_id']);
        $op_uid =intval($this->body['user_id']);
        $op_username =trim(Tools::safeOutput($this->body['user_name']));
        $is_del = intval($this->body['is_del']);

        !$org_id && Lang_Msg::error('ERROR_SHOW_1'); //机构id不能为空
        !$op_uid && Lang_Msg::error('ERROR_EDIT_7'); //用户id不能为空
        !$op_username && Lang_Msg::error('ERROR_GLOBEL_2'); //用户帐号不能为空

        $nowTime = time();
        $UnionMoneyModel = new UnionMoneyModel();
        $data = $UnionMoneyModel->getById($org_id);
        if($data){
            $params = array(
                'union_money'=> isset($this->body['union_money'])?doubleval($this->body['union_money']):$data['union_money'],
                'frozen_money'=> isset($this->body['frozen_money'])?doubleval($this->body['frozen_money']):$data['frozen_money'],
                'credit_money'=> isset($this->body['credit_money'])?doubleval($this->body['credit_money']):$data['credit_money'],
                'balance_type'=> isset($this->body['balance_type'])?intval($this->body['balance_type']):$data['balance_type'],
                'balance_cycle'=> isset($this->body['balance_cycle'])?intval($this->body['balance_cycle']):$data['balance_cycle'],
            );
            if(isset($this->body['balance_type'])) {
                $params['admin_uid'] = $op_uid;
                $params['admin_name'] = $op_username;
            }
            else{
                $params['op_uid'] = $op_uid;
            }
            $is_del ? $params['deleted_at'] = $nowTime : $params['updated_at'] = $nowTime;
            $r = $UnionMoneyModel->updateById($org_id,$params);
        }
        else {
            $params = array(
                'org_id'=>$org_id,
                'union_money'=> doubleval($this->body['union_money']),
                'frozen_money'=> doubleval($this->body['frozen_money']),
                'credit_money'=> doubleval($this->body['credit_money']), //来自信用卡充值的额度，不能提现额度
                'balance_type'=> intval($this->body['balance_type']), //结算类型：0不限1周2月
                'balance_cycle'=> intval($this->body['balance_cycle']), //结算周期：周0～6，月1-31日
                'created_at'=> $nowTime,
                'updated_at'=> $nowTime,
            );
            if(isset($this->body['balance_type'])) {
                $params['admin_uid'] = $op_uid;
                $params['admin_name'] = $op_username;
            }
            else{
                $params['op_uid'] = $op_uid;
            }
            $r = $UnionMoneyModel->add($params);
        }
        !$r && Lang_Msg::error($is_del?'ERROR_DEL_1':'ERROR_EDIT_18'); //删除失败/更新失败
        Tools::lsJson(true,'ok',array('result'=>1));
    }

    //收支接口
    public function inoutAction(){
        $params = array();
        $params['org_id'] = intval($this->body['org_id']);
        $params['op_uid'] =intval($this->body['user_id']);
        $params['op_account'] =trim(Tools::safeOutput($this->body['user_account']));
        $params['op_username'] =trim(Tools::safeOutput($this->body['user_name']));
        !$params['op_username'] && $params['op_username'] = $params['op_account'];
        $params['money'] = doubleval($this->body['money']); //变动额度
        $params['in_out'] = intval($this->body['in_out']); //0出1入
        $params['trade_type'] =  intval($this->body['trade_type']); //交易类型:1支付,2退款,3充值,4提现,5应收账款
        $params['pay_type'] = intval($this->body['pay_type']); //支付方式：1块钱2支付宝
        $params['frozen_type'] = intval($this->body['frozen_type']); //是否冻结，0解冻或直接操作，1冻结
        $params['allow_refund'] = intval($this->body['allow_refund']); //是否允许退款：1是0否
        $params['allow_encash'] = intval($this->body['allow_encash']); //是否允许提现：1是0驳回
        $params['is_credit'] = intval($this->body['is_credit']); //是否信用卡充值：1是0否（充值需要）
        $params['remark'] = trim(Tools::safeOutput($this->body['remark'])); //备注
        $params['payment_id'] = trim(Tools::safeOutput($this->body['payment_id'])); //支付单，退款单对应订单的支付单
        $params['payment'] = trim(Tools::safeOutput($this->body['payment'])); //支付单支付方式，退款单对应订单的支付单
        $params['distributor_id'] = intval($this->body['distributor_id']);
        $params['activity_money'] = isset($this->body['activity_money'])?floatval($this->body['activity_money']):0;

        $UnionMoneyModel = new UnionMoneyModel();
        $params = $UnionMoneyModel->checkInoutParams($params);
        $info = $UnionMoneyModel->getById($params['org_id']);
        $UnionMoneyModel->begin();
        if($info){ //更改
            // 判断抵用券是否足够支付
            if($params['trade_type']==1 && $params['activity_money']){
                $union_money = reset(UnionMoneyModel::model()->search(array('org_id'=>$params['org_id'],'deleted_at'=>0)));
                if(!$union_money || $union_money['activity_money']< $params['money']){
                    Lang_Msg::error('抵用券余额不够支付');
                }
            }
            $r = $UnionMoneyModel->inoutUpdate($params,$info);
        }
        else {
            $r = $UnionMoneyModel->inoutAdd($params);
        }
        if(!$r) { //操作失败
            $UnionMoneyModel->rollback();
            Lang_Msg::error('ERROR_GLOBEL_1');
        }
        $UnionMoneyModel->commit();
        Tools::lsJson(true,'ok',array('result'=>1));
    }

    /**
     * 平台收支应付款打款接口
     * @author zqf
     * @date 2015-04-02
     * @param agency_money json //json数组，如：{"分销商id1":"分销商应付款1","分销商id2":"分销商应付款2",...}
     * @param supply_id int     //供应商id
     * @param bill_amount double //应付账款总额
     */
    public function inOut5Action(){
        $agency_money = json_decode($this->body['agency_money'],true);
        $supply_id = intval($this->body['supply_id']);
        $bill_amount = doubleval($this->body['bill_amount']);
        if(!$agency_money || !$bill_amount || $bill_amount!=array_sum(array_values($agency_money))){
            Lang_Msg::error('应付款金额有误');
        }
        if(!$supply_id)
            Lang_Msg::error('缺少供应商id');
        $UnionMoneyModel = new UnionMoneyModel();
        $UnionMoneyModel->begin();
        try {
            $org_ids = array_keys($agency_money);
            array_push($org_ids, $supply_id);
            $agUms = $UnionMoneyModel->search(array('org_id|in' => $org_ids));
            foreach ($agency_money as $ag_id => $money) {
                if (!$agUms[$ag_id]) {
                    Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
                } else if ($agUms[$ag_id]['online_paid_money'] >= $money) {
                    $r = $UnionMoneyModel->update(array("online_paid_money=online_paid_money-{$money}"), array('org_id' => $ag_id));
                    if (!$r) {
                        $UnionMoneyModel->rollback();
                        Lang_Msg::error('打款失败');
                    }
                } else if ($agUms[$ag_id]['union_money'] >= $money) {
                    $r = $UnionMoneyModel->update(array("union_money=union_money-{$money}"), array('org_id' => $ag_id));
                    if (!$r) {
                        $UnionMoneyModel->rollback();
                        Lang_Msg::error('打款失败');
                    }
                } else {
                    Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
                }
            }
            if (!empty($agUms[$supply_id])) {
                $r = $UnionMoneyModel->update(array("union_money=union_money+{$bill_amount}"), array('org_id' => $supply_id));
            } else {
                $r = $UnionMoneyModel->add(array('org_id' => $supply_id, 'union_money' => $bill_amount));
            }
            if (!$r) {
                $UnionMoneyModel->rollback();
                Lang_Msg::error('打款失败');
            }
            $union_money = $agUms[$supply_id]['union_money'] + $bill_amount;
            $r = $UnionMoneyModel->addLog(
                array(
                    'org_id' => $supply_id,
                    'org_role' => 1, //机构角色：0分销售，1供应商
                    'op_uid' => $this->body['user_id'] ? $this->body['user_id'] : 1,
                    'op_account' => $this->body['user_account'] ? $this->body['user_account'] : 'system',
                    'op_username' => $this->body['user_username'] ? $this->body['user_username'] : 'system',
                    'money' => $bill_amount,
                    'in_out' => 1,
                    'trade_type' => 5,
                    'pay_type' => 0,
                    'used_credit' => 0,
                    'union_money' => $union_money,
                    'frozen_money' => $agUms[$supply_id]['frozen_money'],
                    'remark' => '',
                ),
                array('union_money' => $union_money)
            );
            if (!$r) {
                $UnionMoneyModel->rollback();
                Lang_Msg::error('打款失败');
            }

            $UnionMoneyModel->commit();
            Tools::lsJson(true, 'ok', array('result' => 1));
        }
        catch(Exception $e){
            $UnionMoneyModel->rollback();
            Lang_Msg::error("打款失败");
        }
    }

}