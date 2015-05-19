<?php

/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-25
 * Time: 下午2:37
 */
class PlatformController extends Base_Controller_Api {
    /*     * *
     * 得到整个平台，部分共应商，或单个供应商 可用余额和冻结金额
     */
    public function totalAction() {

        $fields = trim(Tools::safeOutput($this->body['fields']));
        $where = array();
        if ($this->body['org_ids']) {
            $org_ids = explode(',', $this->body['org_ids']);
            $org_ids && $where['org_id|IN'] = $org_ids; //按支付单的ID查找
        }
        UnionMoneyModel::model()->cd = 60 * 5;
        $data = UnionMoneyModel::model()->select($where, 'sum(union_money) as total_union_money,sum(frozen_money) as total_frozen_money');
        !$data[0]['total_union_money'] && $data[0]['total_union_money'] = 0;
        !$data[0]['total_frozen_money'] && $data[0]['total_frozen_money'] = 0;
        Lang_Msg::output($data);
    }

    
    public function listsAction() {
        $fields = trim(Tools::safeOutput($this->body['fields']));
        $fields = $fields ? $fields : "*"; //要获取的字段
        $fieldArr = array();
        if ($fields != "*") {
            $fieldArr = explode(',', $fields);
            !in_array('id', $fieldArr) && array_unshift($fieldArr, 'id');
            $fields = implode(',', $fieldArr);
        }
        $order = $this->getSortRule();

        //根据一个或多个Id查询
        $ids = $this->getIds($this->body['id'], $this->body['ids']);
        $ids && $where['id|IN'] = $ids;

        //根据一个或多个机构查询
        $org_ids = $this->getIds($this->body['org_id'], $this->body['org_ids']);
        $org_ids && $where['org_id|IN'] = $org_ids;

        //开始时间查询
        $start_date = trim(Tools::safeOutput($this->body['start_date']));
        ($start_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $start_date)) && Lang_Msg::error('ERROR_START_DAY_1');
        $start_date && $where['created_at|>='] = strtotime($start_date);

        //结尾时间查询
        $end_date = trim(Tools::safeOutput($this->body['end_date']));
        ($end_date && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $end_date)) && Lang_Msg::error('ERROR_END_DAY_1');
        $end_date && $where['created_at|<='] = strtotime($end_date . " 23:59:59");

        //交易类型
        $status = trim(Tools::safeOutput($this->body['trade_type']));
        ($status && !preg_match("/^[\d]+$/", $status)) && Lang_Msg::error("ERROR_PLATFORM_1"); //状态参数有错
        $status && $where['trade_type'] = $status;

        //角色
        $status = trim(Tools::safeOutput($this->body['org_role']));
        ($status && !preg_match("/^[\d]+$/", $status)) && Lang_Msg::error("ERROR_PLATFORM_2"); //状态参数有错
        $status && $where['org_role'] = $status;

        //like
        $search = trim(Tools::safeOutput($this->body['search'])); //订单号
        $search && $where['OR'] = array(
                    'op_uid|LIKE' => array("%{$search}%"),
                    'org_name|LIKE' => array("%{$search}%"),
                    'op_account|LIKE' => array("%{$search}%"),
                );
        //得到数据
        $count = UnionMoneyLogModel::model()->countResult($where);
        $pagination = Tools::getPagination($this->getParams(), $count);
        $data = $count === 0 ? UnionMoneyLogModel::model()->search($where, $fields, $order, '0,15') : array();

        $result = array(
            'data' => array_values($data),
            'pagination' => array(
                'count' => $count,
                'current' => $pagination['current'],
                'items' => $pagination['items'],
                'total' => $pagination['total'],
            )
        );
        Lang_Msg::output($result);
    }

}
