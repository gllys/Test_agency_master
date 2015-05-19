<?php
/**
 * Created by PhpStorm.
 * 平台资金操作日志
 * User: zqf
 * Date: 14-12-11
 * Time: 下午4:56
 */

class UnionmoneylogController  extends Base_Controller_Api
{
    public function listsAction(){
        $where = array();
        $org_ids = trim(Tools::safeOutput($this->body['org_ids']));
        if($org_ids && preg_match("/^[\d,]+$/",$org_ids)){
            $org_ids = explode(',',$org_ids);
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

        $op_account = trim(Tools::safeOutput($this->body['op_account'])); //操作者账号
        $op_account && $where['op_account|like'] = array("%{$op_account}%");

        $trade_type = intval($this->body['trade_type']);
        $trade_type && $where['trade_type'] = $trade_type;

        $org_role = intval($this->body['org_role']);
        isset($this->body['org_role']) && $this->body['org_role']!==null && $this->body['org_role']!==''
            && $where['org_role'] = $org_role;

        $start = trim(Tools::safeOutput($this->body['start_date']));
        $end = trim(Tools::safeOutput($this->body['end_date']));

        $start && $where['created_at|>='] = strtotime($start);
        $end && $where['created_at|<='] = strtotime($end." 23:59:59");

        $UnionMoneyLogModel = new UnionMoneyLogModel();
        $UnionMoneyLogModel->cd = 60*5;
        $count = reset($UnionMoneyLogModel->search($where,'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        $res = $this->count?$UnionMoneyLogModel->search($where, $this->getFields(), $this->getSortRule(), $this->limit):array();
        if($res){
            if(!$orgs){
                if(!$org_ids){
                    foreach($res as $k=>$v){
                        $org_ids[] = $v['org_id'];
                    }
                }
                $orgs = OrganizationModel::model()->search(array('id|in'=>$org_ids),'id,name');
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

    public function detailAction(){
        $id = intval($this->body['id']);
        $with_org_info = intval($this->body['with_org_info']);
        !$id && Lang_Msg::error('ERROR_GLOBEL_3'); //缺少记录ID
        $return = UnionMoneyLogModel::model()->getById($id);
        !$return && Lang_Msg::error('ERROR_RECODE_NULL'); //记录不存在
        $with_org_info && $return['org_info'] = OrganizationModel::model()->getById($return['org_id']);
        Tools::lsJson(true,'ok',$return);
    }

    public function addAction(){
        $org_id = intval($this->body['org_id']);
        $op_uid =intval($this->body['user_id']);
        $op_account =trim(Tools::safeOutput($this->body['op_account']));
        $op_username =trim(Tools::safeOutput($this->body['op_username']));
        !$op_username && $op_username = $op_account;
        $money = doubleval($this->body['money']); //变动额度
        $in_out = intval($this->body['in_out']); //0出1入
        $trade_type =  intval($this->body['trade_type']); //交易类型:1支付,2退款,3充值,4提现,5应收账款
        $pay_type = intval($this->body['pay_type']); //支付方式：1块钱2支付宝
        $union_money = doubleval($this->body['union_money']);
        $frozen_money = doubleval($this->body['frozen_money']);
        $remark = trim(Tools::safeOutput($this->body['remark'])); //备注

        !$org_id && Lang_Msg::error('ERROR_SHOW_1'); //机构id不能为空
        !$op_uid && Lang_Msg::error('ERROR_EDIT_7'); //用户id不能为空
        !$op_account && Lang_Msg::error('ERROR_GLOBEL_2'); //用户帐号不能为空
        !$trade_type && Lang_Msg::error('ERROR_UNION_4'); //缺少交易类型

        $orgInfo = OrganizationModel::model()->getById($org_id);
        if(!$orgInfo)
            Lang_Msg::error('ERROR_SHOW_2'); //该机构不存在

        $params = array(
            'org_id'=>$org_id,
            'org_role'=>$orgInfo['type']=='supply'?1:0,
            'op_uid'=>$op_uid,
            'op_account'=>$op_account,
            'op_username'=>$op_username,
            'money'=>$money,
            'in_out'=>in_array($trade_type,array(2,3,5))?1:($in_out?1:0),
            'trade_type'=>$trade_type,
            'pay_type'=>$pay_type,
            'union_money'=>$union_money,
            'frozen_money'=>$frozen_money,
            'remark'=>$remark,
        );
        $res = UnionMoneyLogModel::model()->addNew($params);
        if(!$res){
            Lang_Msg::error('ERROR_GLOBEL_1'); //操作失败
        }
        Tools::lsJson(true,'ok',$res);
    }

}