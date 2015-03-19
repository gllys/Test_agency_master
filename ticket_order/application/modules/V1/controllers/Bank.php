<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 10/25/14
 * Time: 2:40 PM
 */
class BankController extends Base_Controller_Api {
    /**
     * 获取所有银行
     * author : yinjian
     */
    public function listAction()
    {
        $banks = BankModel::model()->search(array('deleted_at|exp'=>'is null'));
        Tools::lsJson(true,'ok',$banks);
    }

    /**
     * 我的银行卡列表
     * author : yinjian
     */
    public function list_ownAction()
    {
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error('机构id不能为空');
        if(isset($this->body['id']) && Validate::isString($this->body['id'])) $where['id|in'] = explode(',',$this->body['id']);
        $where['is_del'] = 0;
        $where['organization_id'] = intval($this->body['organization_id']);
        $this->count = BankAccountModel::model()->countResult($where);
        $this->pagenation();
        $data['data'] = BankAccountModel::model()->search($where,'*',null,$this->limit);
        $data['pagination'] = array(
            'count'=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    /**
     * 添加我的银行卡或支付宝
     * author : yinjian
     */
    public function add_ownAction()
    {
        (!in_array($this->body['type'],array('bank','alipay'))) && Lang_Msg::error('类型不存在');
        !Validate::isUnsignedId($this->body['organization_id']) && Lang_Msg::error('机构id缺失');
        !Validate::isString($this->body['account']) && Lang_Msg::error('账号缺失');
        !Validate::isString($this->body['account_name']) && Lang_Msg::error('账户名缺失');
        if($this->body['type'] == 'bank'){
            $bank = BankModel::model()->get(array('id'=>intval($this->body['bank_id']),'deleted_at|exp'=>'is null'));
            !$bank && Lang_Msg::error('暂不支持该银行');
            $data['bank_name'] = $bank['name'];
            $data['open_bank'] = trim(Tools::safeOutput($this->body['open_bank']));
            $data['type'] = "bank";
            $data['bank_id'] = $bank['id'];
        }else{
            $data['bank_name'] = "支付宝";
            $data['type'] = "alipay";
        }
        $data['open_bank'] = trim(Tools::safeOutput($this->body['open_bank']));
        $now = time();
        $data['account'] = $this->body['account'];
        $data['account_name'] = $this->body['account_name'];
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $data['organization_id'] = intval($this->body['organization_id']);
        if(BankAccountModel::model()->search(array('organization_id'=>$data['organization_id'],'is_del'=>0,'account'=>$data['account']))){
            Lang_Msg::error('该银行卡号或支付宝账号已添加');
        }
        $res = BankAccountModel::model()->add($data);
        !$res && Lang_Msg::error('添加失败');
        Tools::lsJson(true,'ok',array('id'=>BankAccountModel::model()->getInsertId()));
    }

    /**
     * 编辑我的银行卡
     * author : yinjian
     */
    public function edit_ownAction()
    {
        !Validate::isUnsignedId($this->body['id']) && Lang_Msg::error('id缺失');
        $bankcard_account = BankAccountModel::model()->get(array('id'=>intval($this->body['id']),'is_del'=>0));
        !$bankcard_account && Lang_Msg::error('该记录未找到');
        if(isset($this->body['is_del']) && in_array(intval($this->body['is_del']),array(0,1))) $data['is_del'] = intval($this->body['is_del']);
        if(isset($this->body['account']) && Validate::isString($this->body['account'])) $data['account'] = $this->body['account'];
        if(isset($this->body['account_name']) && Validate::isString($this->body['account_name'])) $data['account_name'] = $this->body['account_name'];
        if(isset($this->body['bank_id']) && $bankcard_account['type'] == 'bank'){
            $bank = BankModel::model()->get(array('id'=>intval($this->body['bank_id']),'deleted_at|exp'=>'is null'));
            !$bank && Lang_Msg::error('暂不支持该银行');
            $data['bank_name'] = $bank['name'];
            $data['open_bank'] = trim(Tools::safeOutput($this->body['open_bank']));
            $data['bank_id'] = $bank['id'];
        }
        if(BankAccountModel::model()->search(array('id|<>'=>intval($this->body['id']),'organization_id'=>$bankcard_account['organization_id'],'is_del'=>0,'account'=>$this->body['account']))){
            Lang_Msg::error('该银行卡号或支付宝账号已添加');
        }
        if(isset($this->body['status']) && $this->body['status']=='normal'){
            $data['status'] = $this->body['status'];
            BankAccountModel::model()->updateByAttr(array('status'=>'disable'),array('organization_id'=>$bankcard_account['organization_id']));
        }
        $data['open_bank'] = trim(Tools::safeOutput($this->body['open_bank']));
        $data['updated_at'] = time();
        $res = BankAccountModel::model()->updateByAttr($data,array('id'=>intval($this->body['id'])));
        !$res && Lang_Msg::error('更新失败');
        Tools::lsJson(true);
    }
}