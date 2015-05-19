<?php
/**
 * Created by PhpStorm
 * 提现操作
 * User: zqf
 * Date: 14-12-12
 * Time: 下午3:33
 */

class UnionmoneyencashController  extends Base_Controller_Api
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

        $apply_account = trim(Tools::safeOutput($this->body['apply_account'])); //申请者账号
        $apply_account && $where['apply_account|like'] = array("%{$apply_account}%");

        $apply_username = trim(Tools::safeOutput($this->body['apply_username'])); //申请者名称
        $apply_username && $where['apply_username|like'] = array("%{$apply_username}%");

        $status = intval($this->body['status']);
        isset($this->body['status']) && preg_match("/^\d+$/",$this->body['status']) && $where['status'] = $status;

        $org_role = intval($this->body['org_role']);
        isset($this->body['org_role']) && preg_match("/^\d+$/",$this->body['org_role']) && $where['org_role'] = $org_role;

        $start = trim(Tools::safeOutput($this->body['start_date']));
        $end = trim(Tools::safeOutput($this->body['end_date']));

        $start && $where['created_at|>='] = strtotime($start);
        $end && $where['created_at|<='] = strtotime($end." 23:59:59");

        $UnionMoneyEncashModel = new UnionMoneyEncashModel();
        $UnionMoneyEncashModel->cd = 60*5;
        $count = reset($UnionMoneyEncashModel->search($where,'count(*) as count'));
        $this->count = $count['count'];
        $this->pagenation();
        $res = $this->count?$UnionMoneyEncashModel->search($where, $this->getFields(), $this->getSortRule(), $this->limit):array();
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
        $return = UnionMoneyEncashModel::model()->getById($id);
        !$return && Lang_Msg::error('ERROR_RECODE_NULL'); //记录不存在
        $with_org_info && $return['org_info'] = OrganizationModel::model()->getById($return['org_id']);
        Tools::lsJson(true,'ok',$return);
    }

    public function applyAction() {
        $org_id = intval($this->body['org_id']);
        $apply_uid =intval($this->body['apply_uid']);
        !$apply_uid && $apply_uid = intval($this->body['user_id']);
        $apply_account =trim(Tools::safeOutput($this->body['apply_account']));
        !$apply_account && $apply_account = trim(Tools::safeOutput($this->body['user_account']));
        $apply_username =trim(Tools::safeOutput($this->body['apply_username']));
        !$apply_username && $apply_username = trim(Tools::safeOutput($this->body['user_name']));
        $apply_phone =trim(Tools::safeOutput($this->body['apply_phone']));
        $money = doubleval($this->body['money']); //取现额度
        $bank_id = intval($this->body['bank_id']);
        $bank_name =trim(Tools::safeOutput($this->body['bank_name']));
        $open_bank =trim(Tools::safeOutput($this->body['open_bank']));
        $account =trim(Tools::safeOutput($this->body['account']));
        $account_name =trim(Tools::safeOutput($this->body['account_name']));
        $remark = trim(Tools::safeOutput($this->body['remark'])); //备注

        !$org_id && Lang_Msg::error('ERROR_SHOW_1'); //机构id不能为空
        !$apply_uid && Lang_Msg::error('ERROR_EDIT_7'); //用户id不能为空
        !$apply_account && Lang_Msg::error('ERROR_GLOBEL_2'); //用户帐号不能为空
        !$money && Lang_Msg::error('ERROR_ENCASH_1'); //请输入提现额度
        !$open_bank && !$account && !$account_name && Lang_Msg::error('ERROR_UNION_6'); //用户银行账户信息不完整

        $orgInfo = OrganizationModel::model()->getById($org_id);
        if(!$orgInfo)
            Lang_Msg::error('ERROR_SHOW_2'); //该机构不存在
        $unionInfo = UnionMoneyModel::model()->getById($org_id);
        if(!$unionInfo || $unionInfo['union_money']<$money)
            Lang_Msg::error('ERROR_UNION_2'); //平台资金余额不足，无法继续操作


        $UnionMoneyEncashModel = new UnionMoneyEncashModel();
        $params = array(
            'org_id'=>$org_id,
            'org_role'=>$orgInfo['type']=='supply'?1:0,
            'money'=>$money,
            'union_money'=>$unionInfo['union_money'],
            'apply_uid'=>$apply_uid,
            'apply_account'=>$apply_account,
            'apply_username'=>$apply_username?$apply_username:$apply_account,
            'apply_phone'=>$apply_phone,
            'bank_id'=>$bank_id,
            'bank_name'=>$bank_name,
            'open_bank'=>$open_bank,
            'account'=>$account,
            'account_name'=>$account_name,
            'remark'=>$remark,
        );
        $UnionMoneyEncashModel->begin();
        $res = $UnionMoneyEncashModel->addNew($params);
        if(!$res){
            $UnionMoneyEncashModel->rollback();
            Lang_Msg::error('ERROR_GLOBEL_1'); //操作失败
        }
        else {
            $params2 = UnionMoneyModel::model()->checkInoutParams(array(
                'org_id'=>$org_id,
                'orgInfo'=>$orgInfo,
                'money'=>$money,
                'op_uid'=>$apply_uid,
                'op_account'=>$apply_account,
                'op_username'=>$apply_username,
                'trade_type'=>4,
                'frozen_type'=>1
            ));
            if(!$r = UnionMoneyModel::model()->inoutUpdate($params2)){
                $UnionMoneyEncashModel->rollback();
                Lang_Msg::error('ERROR_GLOBEL_1'); //操作失败
            }
            else{
                $UnionMoneyEncashModel->commit();
                Tools::lsJson(true,'ok',$res);
            }
        }
    }

    public function checkAction(){
        $id = intval($this->body['id']);
        $check_uid =  intval($this->body['check_uid']);
        !$check_uid && $check_uid =  intval($this->body['user_id']);
        $status = intval($this->body['status']); //状态：0未打款，1已打款，2驳回
        $paid_img = trim(Tools::safeOutput($this->body['paid_img'])); //打款凭证图片url
        $remark = trim(Tools::safeOutput($this->body['remark'])); //备注
        $paid_at = trim(Tools::safeOutput($this->body['paid_at']));
        ($paid_at && !preg_match("^\d+{10}$",$paid_at)) && $paid_at=strtotime($paid_at);

        !$id && Lang_Msg::error('ERROR_GLOBEL_3'); //缺少记录ID
        !$check_uid && Lang_Msg::error('ERROR_ENCASH_3'); //缺少提现审核人UID

        $data = UnionMoneyEncashModel::model()->getById($id);
        !$data && Lang_Msg::error('ERROR_RECODE_NULL'); //记录不存在

        if(1==$data['status']){
            Lang_Msg::error('ERROR_UNION_7'); //该提现申请已打款
        }
        else if(2==$data['status']){
            Lang_Msg::error('ERROR_UNION_9'); //该提现申请已驳回
        }
        else if(1==$status && !$paid_img){
            Lang_Msg::error('ERROR_UNION_8'); //请上传打款凭证图片
        }

        $check_uid && $data['check_uid'] = $check_uid;
        $status && $data['status'] = $status;
        $remark && $data['remark'] = $remark;
        $nowTime = time();
        if(1==$status){ //状态：已打款
//            $data['union_money'] = $data['union_money'] - $data['money'];
            $data['paid_at'] = $paid_at ?  $paid_at : $nowTime;
            $data['paid_img'] = $paid_img;
        }
        $data['updated_at'] = $nowTime;

        $UnionMoneyEncashModel = new UnionMoneyEncashModel();
        $UnionMoneyEncashModel->begin();
        $res = $UnionMoneyEncashModel->updateById($id,$data);
        if(!$res){
            $UnionMoneyEncashModel->rollback();
            Lang_Msg::error('ERROR_GLOBEL_1'); //操作失败
        }
        else if($status){
            $params2 = UnionMoneyModel::model()->checkInoutParams(array(
                'org_id'=>$data['org_id'],
                'money'=>$data['money'],
                'op_uid'=>$data['apply_uid'],
                'op_account'=>$data['apply_account'],
                'op_username'=>$data['apply_username'],
                'trade_type'=>4,
                'allow_encash'=> $status==1?1:0,
            ));
            if(!UnionMoneyModel::model()->inoutUpdate($params2)){
                $UnionMoneyEncashModel->rollback();
                Lang_Msg::error('ERROR_GLOBEL_1'); //操作失败
            }
        }
        $UnionMoneyEncashModel->commit();
        Tools::lsJson(true,'ok',array('result'=>1));
    }

}