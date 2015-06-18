<?php

/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-5-20
 * Time: 上午11:38
 * 分销商商品日价格日库存
 */
class AgencypdruleController extends Base_Controller_Api
{
    public $AgencyProductRuleModel = false;

    public function init()
    {
        parent::init();
        $this->AgencyProductRuleModel = new AgencyProductRuleModel();
    }

    //渠道产品的日价格，日库存列表
    public function itemsAction()
    {
        $code = trim($this->body['code']);
        $agency_id = intval($this->body['agency_id']);
        $product_id = intval($this->body['product_id']);
        $source = intval($this->body['source']); //外部来源

        if (empty($code) && ($agency_id <= 0 || $product_id <= 0 || $source <= 0)) {
            Tools::lsJson(false, '缺少渠道产品对接码，或分销商ID、产品ID、外部来源参数');
        }

        $where = array();
        if (!empty($code)) {
            $where['code'] = $code;
        }
        if ($agency_id > 0) {
            $where['agency_id'] = $agency_id;
        }
        if ($product_id > 0) {
            $where['product_id'] = $product_id;
        }
        if ($source > 0) {
            $where['source'] = $source;
        }

        $ym = trim($this->body['ym']);
        if ($ym && !preg_match("/^\d{4}-\d{2}$/", $ym)) {
            Lang_Msg::error('ERROR_YM_1');
        }
        $ym && $where['date|BETWEEN'] = array($ym . '-01', $ym . '-31');

        $date = trim($this->body['date']);
        if ($date && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            Lang_Msg::error('ERROR_DATE_2');
        }
        $date && $where['date'] = $date;

        $data = $this->AgencyProductRuleModel->search($where);
        if (intval($this->body['show_remain_reserve']) > 0 && !empty($data)) { //显示剩余库存
            foreach ($data as $k => $v) {
                $ticketDayUsedReserveKey = 'AgencyProductRule|' . $v['code'] . '|' . $v['date'];
                $ticketDayUsedReserve = Cache_Redis::factory()->get($ticketDayUsedReserveKey);
                $data[$k]['used_reserve'] = intval($ticketDayUsedReserve);
                $data[$k]['remain_reserve'] = $v['reserve'] == 0 ? -1 : $v['reserve'] - $data[$k]['used_reserve'];
            }
        }
        Lang_Msg::output(array('data' => $data));
    }

    //设置日价格日库存
    public function setitemAction()
    {
        $operator = $this->getOperator(); //获取操作者
        $data = array(
            'code' => trim($this->body['code']),
            'agency_id' => intval($this->body['agency_id']),
            'product_id' => intval($this->body['product_id']),
            'source' => intval($this->body['source']), //外部来源
            'sale_price' => doubleval($this->body['sale_price']),
            'price' => doubleval($this->body['price']),
            'reserve' => intval($this->body['reserve']),
            'created_by' => $operator['user_id'],
            'created_at' => time(),
        );
        if (empty($data['code']) || $data['agency_id'] <= 0 || $data['product_id'] <= 0 || $data['source'] <= 0) {
            Tools::lsJson(false, '参数渠道产品对接码、分销商ID、产品ID、外部来源都不能少');
        }

        $productInfo = TicketTemplateModel::model()->getById($data['product_id']);
        if (empty($productInfo)) {
            Tools::lsJson(false, '该产品不存在');
        }
        if (!is_numeric($this->body['sale_price'])) { //如果无销售价格参数则取产品默认
            $data['sale_price'] = $productInfo['sale_price'];
        }

        $agencyProductInfo = AgencyProductModel::model()->get(array('code' => $data['code']));
        if (empty($agencyProductInfo)) {
            Tools::lsJson(false, '该渠道产品记录不存在');
        }
        if (!is_numeric($this->body['price'])) { //如果无价格参数则取渠道产品默认
            $data['price'] = $agencyProductInfo['price'];
        }

        $days = trim($this->body['days']);
        if (empty($days)) {
            Tools::lsJson(false, '请选择日期');
        } else if (!preg_match("/^(\d{4}-\d{2}-\d{2},?)+$/", $days)) {
            Lang_Msg::error('ERROR_DATE_2');
        }
        $days = explode(',', $days);

        $daysReserve = array();
        if ($productInfo['rule_id'] > 0 && $data['reserve'] == 0) { //如果无库存参数则取产品设置的
            $ruleItems = TicketRuleItemModel::model()->search(array('rule_id' => $productInfo['rule_id'], 'date|in' => $days));
            if (!empty($ruleItems)) {
                foreach ($ruleItems as $v) {
                    $daysReserve[$v['date']] = $v['reserve'];
                }
            }
        }

        $this->AgencyProductRuleModel->begin();
        $r = $this->AgencyProductRuleModel->addList($data, $days, $daysReserve);
        if ($r) {
            $r = AgencyProductModel::model()->update(array('has_rule' => 1), array('code' => $data['code']));
            if ($r) {
                $this->AgencyProductRuleModel->commit();
                Log_Test::model()->add(
                    array(
                        'type' => Log_Test::$type['CREATE'],
                        'num' => count($days),
                        'content' => '设置了渠道产品日价格和日库存记录'
                            . '【code:' . $data['code'] . ',sale_price:' . $data['sale_price']
                            . ',price:' . $data['price'] . ',reserve:' . $data['reserve'] . ',days:' . implode(',', $days)
                            . (!empty($daysReserve) ? ',daysReserve' . implode(',', $daysReserve) : '') . '】'
                    )
                );
                Tools::lsJson(true, '操作成功');
            }
        }
        $this->AgencyProductRuleModel->rollback();
        Tools::lsJson(false, '操作失败');
    }

    //删除日价格日库存
    public function delAction()
    {
        $operator = $this->getOperator(); //获取操作者
        $code = trim($this->body['code']);
        $agency_id = intval($this->body['agency_id']);
        $product_id = intval($this->body['product_id']);
        $source = intval($this->body['source']); //外部来源

        if (empty($code) && ($agency_id <= 0 || $product_id <= 0 || $source <= 0)) {
            Tools::lsJson(false, '缺少渠道产品对接码，或分销商ID、产品ID、外部来源参数');
        }

        $where = array();
        if (!empty($code)) {
            $where['code'] = $code;
        }
        if ($agency_id > 0) {
            $where['agency_id'] = $agency_id;
        }
        if ($product_id > 0) {
            $where['product_id'] = $product_id;
        }
        if ($source > 0) {
            $where['source'] = $source;
        }

        $where2 = $where;

        $days = trim($this->body['days']);
        if (!empty($days)) {
            if (!preg_match("/^(\d{4}-\d{2}-\d{2},?)+$/", $days)) {
                Lang_Msg::error('ERROR_DATE_2');
            }
            $days = explode(',', $days);
            $where['date|IN'] = $days;
        }

        $this->AgencyProductRuleModel->begin();
        $r = $this->AgencyProductRuleModel->delete($where);
        if ($r) {
            $num = $this->AgencyProductRuleModel->countResult($where2);
            if(empty($num)) {
                $r = AgencyProductModel::model()->update(array('has_rule' => 0),$where2);
                if(!$r) {
                    $this->AgencyProductRuleModel->rollback();
                    Tools::lsJson(false, '操作失败');
                }
            }
            $this->AgencyProductRuleModel->commit();
            Log_Test::model()->add(
                array(
                    'type' => Log_Test::$type['DEL'],
                    'num' => count($days),
                    'content' => '删除了渠道产品日价格和日库存记录'
                        . '【code:' . $code . ',agency_id:' . $agency_id
                        . ',product_id:' . $product_id . ',source:' . $source . (empty($days)?'':',days:' . implode(',', $days)) . '】'
                )
            );
            Tools::lsJson(true, '操作成功',array('has_rule'=> empty($num)?0:1 ));
        } else {
            $this->AgencyProductRuleModel->rollback();
            Tools::lsJson(false, '操作失败');
        }
    }

    //更改已使用库存
    public function chgusedreserveAction()
    {
        $code = trim($this->body['code']);
        $agency_id = intval($this->body['agency_id']);
        $product_id = intval($this->body['product_id']);
        $source = intval($this->body['source']); //外部来源
        $nums = intval($this->body['nums']); //使用数
        $is_refund = intval($this->body['is_refund']);

        if (empty($code) && ($agency_id <= 0 || $product_id <= 0 || $source <= 0)) {
            Tools::lsJson(false, '缺少渠道产品对接码，或分销商ID、产品ID、外部来源参数');
        }

        $where = array();
        if (!empty($code)) {
            $where['code'] = $code;
        }
        if ($agency_id > 0) {
            $where['agency_id'] = $agency_id;
        }
        if ($product_id > 0) {
            $where['product_id'] = $product_id;
        }
        if ($source > 0) {
            $where['source'] = $source;
        }

        $date = trim($this->body['date']);
        if (empty($date)) {
            Tools::lsJson(false, '请选择日期');
        } else if ($date && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            Tools::lsJson(false, '日期格式必须是xxxx-xx-xx');
        }
        $where['date'] = $date;

        $info = $this->AgencyProductRuleModel->search($where);
        if (!$info) {
            Tools::lsJson(false, '该价格规则记录不存在');
        }
        $info = reset($info);

        $ticketDayUsedReserveKey = 'AgencyProductRule|' . $info['code'] . '|' . $date;
        $ticketDayUsedReserve = Cache_Redis::factory()->get($ticketDayUsedReserveKey);
        $info['used_reserve'] = intval($ticketDayUsedReserve);
        if ($is_refund == 0 && $info['reserve'] > 0 && $nums > $info['reserve'] - $info['used_reserve']) {
            Tools::lsJson(false, '购票张数不能超出当日库存剩余数');
        }

        $info['used_reserve'] = $is_refund ? $info['used_reserve'] - $nums : $info['used_reserve'] + $nums;
        $info['used_reserve'] = $info['used_reserve'] < 0 ? 0 : $info['used_reserve'];
        $info['remain_reserve'] = $info['reserve'] == 0 ? -1 : $info['reserve'] - $info['used_reserve'];
        $r = Cache_Redis::factory()->setex($ticketDayUsedReserveKey, 172800, $info['used_reserve']);
        if ($r) {
            Lang_Msg::output($info);
        } else
            Tools::lsJson(false, '操作失败');
    }

}