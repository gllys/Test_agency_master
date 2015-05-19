<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-11
 * Time: 下午4:56
 */

class UnionMoneyModel extends Base_Model_Abstract
{
    protected $dbname = 'itourism';
    protected $tblname = 'union_money';
    protected $pkKey = 'org_id';
    protected $preCacheKey = 'cache|UnionMoneyModel|';

    public function getTable() {
        return $this->tblname;
    }

    public function statics($where=array(),$isOrg = 0){
        if($isOrg){
            $select = 'sum(union_money) as total_union_money,sum(frozen_money) as total_frozen_money,sum(activity_money) as total_activity_money';
        }
        else{
            $select = 'sum(union_money + online_paid_money) as total_union_money,sum(frozen_money) as total_frozen_money,sum(activity_money) as total_activity_money';
        }
        return $this->select($where, $select);
    }

    public function checkInoutParams($params){
        !$params['org_id'] && Lang_Msg::error('ERROR_SHOW_1'); //机构id不能为空
        !$params['op_uid'] && Lang_Msg::error('ERROR_EDIT_7'); //用户id不能为空
        !$params['op_account'] && Lang_Msg::error('ERROR_GLOBEL_2'); //用户帐号不能为空
        //!$params['money'] && Lang_Msg::error('ERROR_UNION_1'); //缺少平台资金变动额度
        !$params['trade_type'] && Lang_Msg::error('ERROR_UNION_4'); //缺少交易类型

        !$params['orgInfo'] && $params['orgInfo'] = OrganizationModel::model()->getById($params['org_id']);
        if(!$params['orgInfo'])
            Lang_Msg::error('ERROR_SHOW_2'); //该机构不存在

        ($params['orgInfo']['type'] != 'supply' && $params['trade_type'] ==5) && Lang_Msg::error('ERROR_UNION_5');
        ($params['orgInfo']['type'] != 'agency' && in_array($params['trade_type'],array(1,2))) && Lang_Msg::error('ERROR_UNION_5');
        $params['param_checked']=1;
        return $params;
    }

    //添加收支记录
    public function inoutAdd($params){
        if(!$params) return false;
        !$params['param_checked'] && $params = $this->checkInoutParams($params);
        $this->begin();
        if($params['trade_type']==4 || ($params['trade_type']==1 && !$params['pay_type'])) {
            $this->rollback();
            Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
        }
        $union_money = $online_paid_money = $frozen_money = $credit_money = 0;
        if($params['trade_type']==1 && $params['pay_type']>0) {
            $online_paid_money = $params['money'];
        }
        if(in_array($params['trade_type'],array(2,3,5))){ //IN
            if(1==$params['frozen_type'] && in_array($params['trade_type'],array(2))){ //退款冻结
                $frozen_money = $params['money'];
            }
            else if(0==$params['allow_refund'] && in_array($params['trade_type'],array(2))){ //退款拒绝
            }
            else { //直接操作
                $union_money = $params['money'];
                if($params['trade_type']==5 && $params['distributor_id']){
                    $r = $this->doPay($params['distributor_id'],$params);
                    if(!$r){
                        $this->rollback();
                    }
                }
            }
        }
        $nowTime = time();
        $data = array(
            'org_id'=>$params['org_id'],
            'union_money'=> $union_money,
            'online_paid_money'=> $online_paid_money,
            'frozen_money'=> $frozen_money,         'credit_money'=> $credit_money,
            'op_uid'=> $params['op_uid'], //操作者UID
            'created_at'=> $nowTime,                'updated_at'=> $nowTime,
        );
        $r = $this->add($data);
        if($r) {
            $r = $this->addLog($params,$data);
            if($r) {
                $this->commit();
                return true;
            }
        }
        $this->rollback();
        return false;
    }

    //更改收支记录
    public function inoutUpdate($params,$info=array()){
        if(!$params) return false;
        !$params['param_checked'] && $params = $this->checkInoutParams($params);
        $this->begin();
        !$info && $info = $this->getById($params['org_id']);
        $union_money = $info['union_money'];
        $frozen_money = $info['frozen_money'];
        $credit_money = $info['credit_money'];
        $online_paid_money = $info['online_paid_money'];
        $activity_money = $info['activity_money'];
        // 1支付,2退款,3充值,4提现,5应收账款
        if($params['trade_type']==1 && $params['activity_money']){ //抵用券支付
            $params['money']=0;
        }
        if(in_array($params['trade_type'],array(2,3,5))){ //IN
            if(in_array($params['trade_type'],array(2))){
                if(1==$params['frozen_type']){ //退款申请冻结，退款金额从在线支付金额移到冻结余额
                    $union_money = $info['union_money'];
                    $frozen_money = $info['frozen_money']+$params['money'];   //冻结余额增加
                    $online_paid_money=$info['online_paid_money']-$params['money'];  //在线支付金额减少
                }
                else if(0==$params['allow_refund']){ //退款拒绝，退款金额从冻结余额到在线支付金额
                    $union_money = $info['union_money'];
                    $frozen_money = $info['frozen_money']-$params['money'];
                    $online_paid_money=$info['online_paid_money']+$params['money'];
                }
                else{ //退款解冻
                    //判断退款单中订单对应支付单是否信用卡资金支付
                    if($ricTmp = $this->paymentIsCredit($params['payment_id'])){
                        $params['used_credit'] = $params['money']>=$ricTmp['used_credit']?$ricTmp['used_credit']:$params['money'];
                        $credit_money = $info['credit_money']+$params['used_credit'];
                    }

                    $frozen_money=$info['frozen_money']-$params['money']; //退款金额从冻结余额到平台资金余额
                    $union_money=$info['union_money']+$params['money'];
                    if($params['activity_money']>0){ //退款到抵用券余额
                        $frozen_money=$frozen_money-$params['activity_money'];
                        $activity_money = $info['activity_money']+$params['activity_money'];
                        //$this->updateById($params['org_id'],array('activity_money = activity_money - '.$params['activity_money']));
                    }
                }
            }
            else { //直接操作
                $union_money = $info['union_money']+$params['money'];
                $frozen_money = $info['frozen_money'];
                if($params['trade_type']==3 && !empty($params['is_credit'])){ //信用卡充值
                    $credit_money = $info['credit_money']+$params['money'];
                    $params['used_credit'] = $params['money'];
                }
                //抵用券充值
                if($params['trade_type']==3 && $params['activity_charge_log_id']>0){
                    $union_money = $info['union_money'];
                    $activity_money = $info['activity_money']+$params['activity_money']+$params['money'];
                }
                if($params['trade_type']==5 && $params['distributor_id']){
                    $r = $this->doPay($params['distributor_id'],$params);
                    if(!$r){
                        $this->rollback();
                        Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
                    }
                }
            }
        }
        else if(in_array($params['trade_type'],array(1,4))) { //out
            if(in_array($params['trade_type'],array(4))){ //提现
                if(1==$params['frozen_type']){ //提现冻结
                    if($params['money'] > ($info['union_money']-$info['credit_money']) ) {
                        $this->rollback();
                        Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
                    }
                    $union_money = $info['union_money']-$params['money'];
                    $frozen_money = $info['frozen_money']+$params['money'];
                }
                else if(0==$params['allow_encash']) { //提现驳回
                    $union_money = $info['union_money']+$params['money'];
                    $frozen_money = $info['frozen_money']-$params['money'];
                }
                else { //提现解冻
                    if($info['frozen_money']<$params['money']) {
                        $this->rollback();
                        Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
                    }
                    $union_money = $info['union_money'];
                    $frozen_money = $info['frozen_money']-$params['money'];
                }
            }
            else { //支付
                if(!$params['pay_type']) { //平台支付
                    if($info['union_money']<$params['money']) {
                        $this->rollback();
                        Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作
                    }
                    $union_money = $info['union_money']- $params['money']; //平台余额减少
                    $frozen_money = $info['frozen_money'];
                    if(1==$params['trade_type'] && $info['credit_money'] >0) {
                        $credit_money = $info['credit_money']>= $params['money']?$info['credit_money']- $params['money']:0;
                        $params['used_credit'] = $info['credit_money']>= $params['money']?$params['money']:$info['credit_money'];
                    }
                }
                // 抵用券扣款
                if($params['trade_type']==1 && $params['activity_money']){
                    $activity_money = $info['activity_money'] - $params['activity_money'];//抵用券余额减少
                    $online_paid_money = $online_paid_money+$params['activity_money'];//在线支付余额增加
                }
                $online_paid_money = $online_paid_money+$params['money']; //在线支付余额增加
            }
        }
        else{
            $union_money = $info['union_money'];
            $online_paid_money = $info['online_paid_money'];
            $frozen_money = $info['frozen_money'];
            $credit_money = $info['credit_money'];
            $activity_money = $info['activity_money'];
        }

        $nowTime = time();
        $data = array(
            'union_money'=> $union_money,
            'online_paid_money'=> $online_paid_money,
            'frozen_money'=> $frozen_money,                 'credit_money'=> $credit_money,
            'activity_money'=>$activity_money,
            'op_uid'=> $params['op_uid'],                   'updated_at'=> $nowTime,
        );

        $r = $this->updateById($params['org_id'],$data);
        if($r){
            $r = $this->addLog($params,$data);
            if($r){
                $this->commit();
                return true;
            }
        }
        $this->rollback();
        return false;
    }

    //记录操作日志及交易流水
    public function addLog($params,$data){
        if(!$params) return false;
        if((0==$params['frozen_type'] && !in_array($params['trade_type'],array(2,4))) || !empty($params['allow_refund']) || !empty($params['allow_encash'])){
            //写入平台资金变动记录
            $statics = $this->statics(array('org_id'=>$params['org_id']),1);
            $statics = !empty($statics)?reset($statics):array();
            // 优惠券充值 额度是总额度
            if(isset($params['activity_money']) && $params['activity_money']>0 && $params['trade_type']==1){
                $params['remark'] = $params['remark'].'（抵用券支付）';
                $params['money'] = $params['activity_money'];
            }
            if(isset($params['activity_money']) && $params['activity_money']>0 && $params['trade_type']==3){
                $params['remark'] = $params['remark'].'（抵用券充值）';
                $params['money'] = $params['activity_money']+$params['money'];
            }
            $res = UnionMoneyLogModel::model()->addNew(array(
                'org_id'=>$params['org_id'],              'org_role'=>$params['orgInfo']['type']=='supply'?1:0,
                'op_uid'=>$params['op_uid'],              'op_account'=>$params['op_account'],
                'op_username'=>$params['op_username'],
                'money'=>$params['money'],                'in_out'=>in_array($params['trade_type'],array(2,3,5))?1:($params['in_out']?1:0),
                'trade_type'=>$params['trade_type'],      'pay_type'=>$params['pay_type'],
                'used_credit'=>empty($params['used_credit'])?0:$params['used_credit'],
                'union_money'=>empty($statics['total_union_money'])?0:$statics['total_union_money'],
                'frozen_money'=>empty($statics['total_frozen_money'])?0:$statics['total_frozen_money'],
                'remark'=>$params['remark'],
            ));
            if(!$res){
                return false;
            }
            if(in_array($params['trade_type'],array(3,4))) {//写入交易流水
                $res = TransflowModel::model()->addflow(
                    $params['money'],
                    $params['orgInfo']['type'] == 'supply' ? $params['org_id'] : 0,
                    $params['orgInfo']['type'] == 'agency' ? $params['org_id'] : 0,
                    $params['op_uid'],
                    'union',
                    $params['trade_type'],
                    $params['op_username'],
                    $data['union_money'],
                    $params['remark']?$params['remark']:''
                );
                if (!$res) {
                    return false;
                }
            }
        }
        return true;
    }

    //判断退款单的订单对应支付单是否使用了credit_money信用卡资金
    public function paymentIsCredit($payment_id){
        if(!$payment_id) return false;
        $r = UnionMoneyLogModel::model()->search(array('trade_type'=>1,'used_credit|>'=>0,'remark'=>$payment_id));
        return $r ? reset($r): false;
    }

    public function doPay($distributor_id,$params){
        $info = $this->getById($distributor_id);
        if(!$info) return false;
        $data = array();
        if($info['online_paid_money']>=$params['money']){
            $data['online_paid_money'] = $info['online_paid_money']-$params['money'];
        }
        else if($info['union_money']>=$params['money']){
            $data['union_money'] = $info['union_money']-$params['money'];
        }
        else{
            return false;
        }
        $r = $this->updateById($params['distributor_id'],$data);
        return $r;
    }


}