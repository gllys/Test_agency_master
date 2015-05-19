<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 14-12-30
 * Time: 下午4:12
 * 分销策略
 */


class TicketpolicyController extends Base_Controller_Api
{

    public function addAction(){
        $params = $this->getOperator(); //获取操作者

        $params['supplier_id'] = intval($this->body['supplier_id']);
        $params['name']= trim(Tools::safeOutput($this->body['name']));
        $params['note']= trim(Tools::safeOutput($this->body['note']));
        $params['other_fat_price']= doubleval($this->body['other_fat_price']); //其他散客价
        $params['other_group_price']= doubleval($this->body['other_group_price']); //其他团客价
        $params['other_blackname_flag'] = intval($this->body['other_blackname_flag'])?1:0; //不合作分销商黑名单开关：0关闭 1开启
        $params['other_credit_flag'] = intval($this->body['other_credit_flag'])?1:0; //不合作分销商信用支付开关：0关闭 1开启
        $params['other_advance_flag'] = intval($this->body['other_advance_flag'])?1:0; //不合作分销商储值支付开关：0关闭 1开启
        $params['new_fat_price']= doubleval($this->body['new_fat_price']); //新合作散客价
        $params['new_group_price']= doubleval($this->body['new_group_price']); //新合作团客价
        $params['new_blackname_flag'] = intval($this->body['new_blackname_flag'])?1:0; //新合作分销商黑名单开关：0关闭 1开启
        $params['new_credit_flag'] = intval($this->body['new_credit_flag'])?1:0; //新合作分销商信用支付开关：0关闭 1开启
        $params['new_advance_flag'] = intval($this->body['new_advance_flag'])?1:0; //新合作分销商储值支付开关：0关闭 1开启
        $policy_items = json_decode($this->body['policy_items'],true);

        !$params['supplier_id'] && Lang_Msg::error('ERROR_SUPPLIER_1');
        !$params['name'] && Lang_Msg::error('ERR_TKT_POLICY_1'); //分销策略名称不能为空
        !$policy_items && Lang_Msg::error('ERR_TKT_POLICY_2'); //请设置分销策略明细

        $TicketPolicyModel = new TicketPolicyModel();
        $TicketPolicyModel->begin();
        $r = $TicketPolicyModel->addNew($params);
        if($r){
            $items= array();
            foreach($policy_items as $k=>$v){
                if($v['distributor_id']){
                    $items[] = array(
                        'policy_id'=>$r['id'],
                        'distributor_id'=>$v['distributor_id'],
                        'fat_price'=>doubleval($v['fat_price']),
                        'group_price'=>doubleval($v['group_price']),
                        'blackname_flag'=>intval($v['blackname_flag'])?1:0, //黑名单开关：0关闭 1开启
                        'credit_flag'=>intval($v['credit_flag'])?1:0,       //信用支付开关：0关闭 1开启
                        'advance_flag'=>intval($v['advance_flag'])?1:0,     //储值支付开关：0关闭 1开启
                    );
                }
            }
            !$items && Lang_Msg::error('ERR_TKT_POLICY_2'); //请设置分销策略明细

            $ir = TicketPolicyItemModel::model()->addList($r['id'],$items);
            if(!$ir){
                $TicketPolicyModel->rollback();
                Lang_Msg::error("ERROR_OPERATE_1");
            }
            $TicketPolicyModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['CREATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_TKT_POLICY_1').'[policy_id:'.$r['id'].']【'.$r['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'),$r);
        }
        else{
            $TicketPolicyModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

    public function updateAction(){
        $params = $this->getOperator(); //获取操作者

        $where = array();
        $where['id'] = intval($this->body['id']);
        $where['supplier_id'] = intval($this->body['supplier_id']);

        !$where['id'] && Lang_Msg::error("ERR_TKT_POLICY_3"); //缺少分销策略ID参数
        !$where['supplier_id'] && Lang_Msg::error('ERROR_SUPPLIER_1');

        $params['name']= trim(Tools::safeOutput($this->body['name']));
        $params['note']= trim(Tools::safeOutput($this->body['note']));
        $params['other_fat_price']= doubleval($this->body['other_fat_price']); //其他散客价
        $params['other_group_price']= doubleval($this->body['other_group_price']); //其他团客价
        $params['other_blackname_flag'] = intval($this->body['other_blackname_flag'])?1:0; //不合作分销商黑名单开关：0关闭 1开启
        $params['other_credit_flag'] = intval($this->body['other_credit_flag'])?1:0; //不合作分销商信用支付开关：0关闭 1开启
        $params['other_advance_flag'] = intval($this->body['other_advance_flag'])?1:0; //不合作分销商储值支付开关：0关闭 1开启
        $params['new_fat_price']= doubleval($this->body['new_fat_price']); //新合作散客价
        $params['new_group_price']= doubleval($this->body['new_group_price']); //新合作团客价
        $params['new_blackname_flag'] = intval($this->body['new_blackname_flag'])?1:0; //新合作分销商黑名单开关：0关闭 1开启
        $params['new_credit_flag'] = intval($this->body['new_credit_flag'])?1:0; //新合作分销商信用支付开关：0关闭 1开启
        $params['new_advance_flag'] = intval($this->body['new_advance_flag'])?1:0; //新合作分销商储值支付开关：0关闭 1开启
        $policy_items = json_decode($this->body['policy_items'],true);

        !$policy_items && Lang_Msg::error('ERR_TKT_POLICY_2'); //请设置分销策略明细

        $TicketPolicyModel = new TicketPolicyModel();
        $detail = $TicketPolicyModel->search($where);
        !$detail && Lang_Msg::error("ERR_TKT_POLICY_4"); //该分销策略记录不存在
        $data = reset($detail);

        isset($_POST['name']) && $data['name'] = $params['name'];
        isset($_POST['note']) && $data['note'] = $params['note'];
        isset($_POST['other_fat_price']) && $data['other_fat_price'] = $params['other_fat_price'];
        isset($_POST['other_group_price']) && $data['other_group_price'] = $params['other_group_price'];
        isset($_POST['other_blackname_flag']) && $data['other_blackname_flag'] = $params['other_blackname_flag'];
        isset($_POST['other_credit_flag']) && $data['other_credit_flag'] = $params['other_credit_flag'];
        isset($_POST['other_advance_flag']) && $data['other_advance_flag'] = $params['other_advance_flag'];
        isset($_POST['new_fat_price']) && $data['new_fat_price'] = $params['new_fat_price'];
        isset($_POST['new_group_price']) && $data['new_group_price'] = $params['new_group_price'];
        isset($_POST['new_blackname_flag']) && $data['new_blackname_flag'] = $params['new_blackname_flag'];
        isset($_POST['new_credit_flag']) && $data['new_credit_flag'] = $params['new_credit_flag'];
        isset($_POST['new_advance_flag']) && $data['new_advance_flag'] = $params['new_advance_flag'];

        !$data['name'] && Lang_Msg::error('ERR_TKT_POLICY_1');

        $data['updated_at'] = time();
        $data['user_id'] = $params['user_id'];
        $data['user_account'] = $params['user_account'];
        $data['user_name'] = $params['user_name'];

        $TicketPolicyModel->begin();
        $r = $TicketPolicyModel->updateById($where['id'],$data);
        if($r){
            $items= array();
            foreach($policy_items as $k=>$v){
                if($v['distributor_id']){
                    $items[] = array(
                        'policy_id'=>$data['id'],
                        'distributor_id'=>$v['distributor_id'],
                        'fat_price'=>doubleval($v['fat_price']),
                        'group_price'=>doubleval($v['group_price']),
                        'blackname_flag'=>intval($v['blackname_flag'])?1:0, //黑名单开关：0关闭 1开启
                        'credit_flag'=>intval($v['credit_flag'])?1:0,       //信用支付开关：0关闭 1开启
                        'advance_flag'=>intval($v['advance_flag'])?1:0,     //储值支付开关：0关闭 1开启
                    );
                }
            }
            !$items && Lang_Msg::error('ERR_TKT_POLICY_2'); //请设置分销策略明细

            $ir = TicketPolicyItemModel::model()->addList($data['id'],$items);
            if(!$ir){
                $TicketPolicyModel->rollback();
                Lang_Msg::error("ERROR_OPERATE_1");
            }
            $TicketPolicyModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['UPDATE'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_TKT_POLICY_2').'[policy_id:'.$data['id'].']【'.$data['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketPolicyModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }

    }

    public function listsAction(){
        $where = array();
        $supplier_id = intval($this->body['supplier_id']);
        $supplier_id && $where['supplier_id'] = $supplier_id;

        $name = trim(Tools::safeOutput($this->body['name']));
        $name && $where['name|LIKE'] = array("%{$name}%");

        $show_items = intval($this->body['show_items']);
        $show_all = $this->body['show_all'];

        $TicketPolicyModel = new TicketPolicyModel();
        if($show_all){
            $data = $TicketPolicyModel->search($where,$this->getFields(),$this->getSortRule());
        } else {
            $this->count = $TicketPolicyModel->countResult($where);
            $this->pagenation();
            $data = $this->count>0  ? $TicketPolicyModel->search($where,$this->getFields(),$this->getSortRule(),$this->limit) : array();
        }

        if($data && $show_items){
            $policy_ids = array_keys($data);
            $items = TicketPolicyItemModel::model()->search(array('policy_id|in'=>$policy_ids));
            $policy_items = array();
            foreach($items as $v){
                $policy_items[$v['policy_id']][] = $v;
            }
            unset($items);
            foreach($data as $k=>$v){
                $data[$k]['items'] =  $policy_items[$k];
            }
        }

        $result = array(
            'data'=>array_values($data),
            'pagination'=>$show_all? array('count'=>count($data)):array(
                'count'=>$this->count,
                'current'=>$this->current,
                'items'=>$this->items,
                'total'=>$this->total,
            )
        );
        Lang_Msg::output($result);
    }

    public function detailAction(){
        $where = array();
        $where['id'] = intval($this->body['id']);
        $where['supplier_id'] = intval($this->body['supplier_id']);

        !$where['id'] && Lang_Msg::error("ERR_TKT_POLICY_3"); //缺少分销策略ID参数
        !$where['supplier_id'] && Lang_Msg::error('ERROR_SUPPLIER_1');

        $TicketPolicyModel = new TicketPolicyModel();
        $detail = $TicketPolicyModel->search($where);
        !$detail && Lang_Msg::error("ERR_TKT_POLICY_4"); //该分销策略记录不存在
        $detail = reset($detail);
        intval($this->body['show_items']) && $detail['items'] = TicketPolicyItemModel::model()->search(array('policy_id'=>$where['id']));
        Lang_Msg::output($detail);
    }

    public function delAction(){
        $where = array();
        $where['id'] = intval($this->body['id']);
        $where['supplier_id'] = intval($this->body['supplier_id']);

        !$where['id'] && Lang_Msg::error("ERR_TKT_POLICY_3"); //缺少分销策略ID参数
        !$where['supplier_id'] && Lang_Msg::error('ERROR_SUPPLIER_1');

        $TicketPolicyModel = new TicketPolicyModel();
        $detail = $TicketPolicyModel->search($where);
        !$detail && Lang_Msg::error("ERR_TKT_POLICY_4"); //该分销策略记录不存在
        $detail = reset($detail);

        if(TicketTemplateModel::model()->search(array('policy_id'=>$where['id'],'state|>'=>0,'is_del'=>0))) {
            Lang_Msg::error('ERR_TKT_POLICY_5'); //删除失败！有产品票在使用此分销策略，请取消后再删除
        }
        $r = $TicketPolicyModel->deleteById($where['id']);
        if($r){
            $ir = TicketPolicyItemModel::model()->delete(array('policy_id'=>$where['id']));
            if(!$ir){
                $TicketPolicyModel->rollback();
                Lang_Msg::error("ERROR_OPERATE_1");
            }
            $TicketPolicyModel->commit();
            Log_Test::model()->add(array('type'=>Log_Test::$type['DEL'],'num'=>1,'content'=>Lang_Msg::getLang('INFO_TKT_POLICY_3').'[policy_id:'.$detail['id'].']【'.$detail['name'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_OPERATE_0'));
        }
        else{
            $TicketPolicyModel->rollback();
            Lang_Msg::error("ERROR_OPERATE_1");
        }
    }

	/**
	 * 解绑分销商-删除策略绑定数据
	 */
    public function unbindDistributorAction(){
        $distributor_id = intval($this->body['distributor_id']);
        !$distributor_id && Lang_Msg::error("缺少分销商ID");
        $supply_id = intval($this->body['supply_id']);
        !$supply_id && Lang_Msg::error("缺少供应商ID");
		
		$list = TicketPolicyModel::model()->search([
			'supplier_id' => $supply_id
		]);

		empty($list) && $list = [0=>0];
		TicketPolicyItemModel::model()->delete([
			'distributor_id' => $distributor_id,
			'policy_id|in'    => array_keys($list)
		]);
		Lang_Msg::output(true);
    }
}
