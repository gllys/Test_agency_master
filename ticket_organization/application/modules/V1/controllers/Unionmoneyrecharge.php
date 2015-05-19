<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-15
 * Time: 下午6:09
 */

class UnionmoneyrechargeController  extends Base_Controller_Api
{

    public function addAction(){
        $params = array();
        $params['org_id'] = intval($this->body['org_id']);
        $params['op_uid'] =intval($this->body['user_id']);
        $params['op_account'] =trim(Tools::safeOutput($this->body['user_account']));
        $params['op_username'] =trim(Tools::safeOutput($this->body['user_name']));
        !$params['op_username'] && $params['op_username']=$params['op_account'];
        $params['money'] = doubleval($this->body['money']); //变动额度
        $params['pay_type'] = intval($this->body['pay_type']); //支付方式：1块钱2支付宝
        $params['is_credit'] = intval($this->body['is_credit']); //是否信用卡充值,1是,0否(默认)

        !$params['org_id'] && Lang_Msg::error('ERROR_SHOW_1'); //机构id不能为空
        !$params['op_uid'] && Lang_Msg::error('ERROR_EDIT_7'); //用户id不能为空
        !$params['op_account'] && Lang_Msg::error('ERROR_GLOBEL_2'); //用户帐号不能为空
        !$params['money'] && Lang_Msg::error('ERROR_RECHARGE_1'); //缺少平台资金变动额度

        !$params['pay_type'] && $params['pay_type']=1;
        $activity_charge_log = array();
        $params['activity_money'] = 0.00;
        $activity_charge_log_id = 0;
        // 判断优惠规则，新增优惠信息 事务操作
        if(isset($this->body['activity_id']) && $activity_id = $this->body['activity_id']){
            $activity = reset(ActivityChargeModel::model()->search(array('id'=>$activity_id,'status'=>1,'start_time|<='=>time(),'end_time|>='=>time())));
            !$activity && Lang_Msg::error('该优惠规则不能使用');
            // 判断优惠的钱数
            $params['activity_money'] = $coupon = floor($params['money']/$activity['num'])*$activity['coupon'];
            $coupon == 0 && Lang_Msg::error('未到达优惠所需最低钱数');
            $organization = OrganizationModel::model()->getById($params['org_id']);
            // 该机构的所有优惠钱数
            $data = UnionMoneyModel::model()->statics(array('org_id'=>$params['org_id']),1);
            $return = !empty($data)?reset($data):array('total_union_money'=>0,'total_frozen_money'=>0,'total_activity_money'=>0);
            $activity_charge_log = array(
                'organization_id'=>$params['org_id'],
                'organization_name'=>$organization['name'],
                'activity_id'=>intval($this->body['activity_id']),
                'activity_title'=>$activity['title'],
                'num'=>$activity['num'],
                'coupon'=>$coupon,
                'coupon_total'=>$return['total_activity_money']+$coupon,
                'created_by'=>$params['op_uid'],
                'created_name'=>$params['op_account'],
                'created_at'=>time(),
                'updated_at'=>time(),
                'pay_type'=>$params['pay_type'],
                'money'=>$params['money'],
            );
        }
        try {
            UnionMoneyRechargeModel::model()->begin();
            if($activity_charge_log){
                ActivityChargeLogModel::model()->add($activity_charge_log);
                $activity_charge_log_id = ActivityChargeLogModel::model()->getInsertId();
            }
            $params['activity_charge_log_id'] = $activity_charge_log_id;
            $res = UnionMoneyRechargeModel::model()->addNew($params);
            UnionMoneyRechargeModel::model()->commit();
            Tools::lsJson(true,'ok',$res);
        } catch (PDOException $e) {
            // 回滚事务
            UnionMoneyRechargeModel::model()->rollBack();
            Lang_Msg::error('操作失败');
        }
    }

    public function paidAction(){
        $id = intval($this->body['id']);
        !$id && Lang_Msg::error('ERROR_GLOBEL_3'); //缺少记录ID
        $UnionMoneyRechargeModel = new UnionMoneyRechargeModel();
        $info = $UnionMoneyRechargeModel->getById($id);
        !$info && Lang_Msg::error('ERROR_RECODE_NULL'); //记录不存在
        $info['paid_at'] && Lang_Msg::error('ERROR_RECHARGE_2'); //本次充值已成功
        try {
            $UnionMoneyRechargeModel->begin();
            $r = UnionMoneyRechargeModel::model()->updateById($id,array('paid_at'=>time()));
            if($r){
                if($info['activity_charge_log_id']>0){
                    // 该机构的所有优惠钱数
                    $unionMoney = UnionMoneyModel::model()->statics(array('org_id'=>$info['org_id']),1);
                    $unionMoney = !empty($unionMoney)?reset($unionMoney):array('total_union_money'=>0,'total_frozen_money'=>0,'total_activity_money'=>0);
                    ActivityChargeLogModel::model()->updateById($info['activity_charge_log_id'],array(
                        'coupon_total'=>$unionMoney['total_activity_money']+$info['activity_money']+$info['money'],
                        'paid_at'=>time())
                    );
                }
                $unionInfo = UnionMoneyModel::model()->getById($info['org_id']);
                $info['in_out'] = 1;
                $info['trade_type'] = 3;
                $info['frozen_type'] = 0;
                $info['remark'] = $id;
                $info = UnionMoneyModel::model()->checkInoutParams($info);
                if($unionInfo){
                    $r = UnionMoneyModel::model()->inoutUpdate($info,$unionInfo);
                } else {
                    $r = UnionMoneyModel::model()->inoutAdd($info);
                }
                if(!$r){
                    $UnionMoneyRechargeModel->rollback();
                }
                $UnionMoneyRechargeModel->commit();
                Tools::lsJson(true,'ok',array('result'=>1));
            }
        } catch (PDOException $e) {
            // 回滚事务
            $UnionMoneyRechargeModel->rollBack();
            Lang_Msg::error('操作失败');
        }
    }

    public function detailAction(){
        $id = intval($this->body['id']);
        !$id && Lang_Msg::error('ERROR_GLOBEL_3'); //缺少记录ID
        $return = UnionMoneyRechargeModel::model()->getById($id);
        !$return && Lang_Msg::error('ERROR_RECODE_NULL'); //记录不存在
        Tools::lsJson(true,'ok',$return);
    }

}