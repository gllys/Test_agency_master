<?php

/**
 * ota可访问的产品
 *
 * @Package controller
 * @Date 2015-5-4
 * @Author zhaqinfeng
 */
class OtaController extends Base_Controller_Api
{


    public function productListAction()
    {
        $agency_id = intval($this->body['agency_id']);
        $source = intval($this->body['source']);
        $where = " T.is_del=0 ";

        if ($agency_id > 0) {
            $where .= " AND A.agency_id={$agency_id} ";
        }
        if ($source > 0) {
            $where .= " AND A.source={$source} ";
        }

        $AgencyProduct = AgencyProductModel::model();
        $sql = 'select A.*,T.name,T.fat_price,T.group_price,T.sale_price,T.listed_price,T.rule_id,T.valid,T.max_buy,T.mini_buy,T.scenic_id,' .
            'T.view_point,T.state,T.scheduled_time,T.week_time,T.refund,T.is_del,T.remark,T.organization_id,T.type,T.date_available,T.policy_id,T.valid_flag,T.sms_template' .
            ' from ' . $AgencyProduct->getTable() . ' A join ' . TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id' .
            ' where ' . $where . ' order by A.' . $this->getSortRule('update_at');

        $data = $AgencyProduct->db->selectBySql($sql);
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $data[$k]['extra'] = unserialize($v['extra']);
                $data[$k]['id'] = $v['code'];
                unset($data[$k]['code']);
            }
        }
        Tools::lsJson(true, '', $data);
    }

    //产品详情
    public function productInfoAction()
    {
        $id = trim($this->body['id']);
        $product_id = intval($this->body['product_id']);
        $agency_id = intval($this->body['agency_id']);
        if (empty($id) && $product_id <= 0) {
            Tools::lsJson(false, '缺少产品ID');
        } else if (empty($id) && ($product_id > 0 && $agency_id <= 0)) {
            Tools::lsJson(false, '缺少分销商ID');

        } else if (!empty($id) && !preg_match("/^\w+$/", $id)) {
            Tools::lsJson(false, '产品ID不符合规范');
        }

        $AgencyProduct = AgencyProductModel::model();
        $where = ($product_id > 0 && $agency_id > 0) ? " A.product_id=" . $product_id . " AND A.agency_id=" . $agency_id : "A.code='" . $id . "'";
        $sql = "select A.*,T.name,T.fat_price,T.group_price,T.sale_price,T.listed_price,T.rule_id,T.valid,T.max_buy,T.mini_buy,T.scenic_id," .
            "T.view_point,T.state,T.scheduled_time,T.week_time,T.refund,T.is_del,T.remark,T.organization_id,T.type,T.date_available,T.policy_id,T.valid_flag,T.sms_template" .
            " from " . $AgencyProduct->getTable() . " A join " . TicketTemplateModel::model()->getTable() . " T on A.product_id=T.id" .
            " where " . $where . " AND T.is_del=0";

        $data = $AgencyProduct->db->selectBySql($sql);
        if (empty($data)) {
            Tools::lsJson(false, '该产品不存在');
        }
        $data = reset($data);
        $data['extra'] = unserialize($data['extra']);
        $data['id'] = $data['code'];
        unset($data['code']);

        $scenic = ScenicModel::model()->getScenicInfo(array('id' => $data['scenic_id']));
        $data['scenic_name'] = (empty($scenic) || empty($scenic['body']['name'])) ? '' : $scenic['body']['name'];

        $pois = ScenicModel::model()->getPoiList(array('ids' => $data['view_point'], 'show_all' => 1, 'show_deleted' => 1, 'fields' => 'name'));
        $data['scenic_poi_list'] = (empty($pois) || empty($pois['body']['data'])) ? array() : $pois['body']['data'];
        Tools::lsJson(true, '', $data);
    }

    //ota可购买产品的景区
    public function scenicListAction()
    {
        $agency_id = intval($this->body['agency_id']);
        $source = intval($this->body['source']);
        $where = " T.is_del=0 ";

        if ($agency_id > 0) {
            $where .= " AND A.agency_id={$agency_id} ";
        }
        if ($source > 0) {
            $where .= " AND A.source={$source} ";
        }

        $AgencyProduct = AgencyProductModel::model();
        $sql = 'select A.code as id,T.scenic_id ' .
            ' from ' . $AgencyProduct->getTable() . ' A join ' . TicketTemplateModel::model()->getTable() . ' T on A.product_id=T.id' .
            ' where ' . $where . ' order by A.' . $this->getSortRule('update_at');

        $data = $AgencyProduct->db->selectBySql($sql);
        $scenicList = $scenicIds2Products = array();
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $scenic_ids = explode(',', $v['scenic_id']);
                foreach ($scenic_ids as $scenicId) {
                    $scenicIds2Products[$scenicId][] = $v['id'];
                }
            }
            if (!empty($scenicIds2Products)) {
                $scenicList = ScenicModel::model()->getScenicList(array('ids' => implode(',', array_keys($scenicIds2Products)), 'items' => count($scenicIds2Products)));
                if (!empty($scenicList) && !empty($scenicList['body']['data'])) {
                    $scenicList = $scenicList['body']['data'];
                    foreach ($scenicList as $sck => $scV) {
                        $scenicList[$sck]['product_ids'] = $scenicIds2Products[$scV['id']];
                        unset(
                            $scenicList[$sck]['thumbnail_id'],
                            $scenicList[$sck]['organization_id'],
                            $scenicList[$sck]['audited_by'],
                            $scenicList[$sck]['audited_at'],
                            $scenicList[$sck]['created_by'],
                            $scenicList[$sck]['normal_before'],
                            $scenicList[$sck]['api_channel_id'],
                            $scenicList[$sck]['parent_id'],
                            $scenicList[$sck]['pinyin'],
                            $scenicList[$sck]['py'],
                            $scenicList[$sck]['has_bind_org'],
                            $scenicList[$sck]['district']
                        );
                    }
                }
            }
        }
        Tools::lsJson(true, '', $scenicList);
    }

    //ota可购买产品的景区的详情
    public function scenicInfoAction()
    {
        $id = intval($this->body['id']);
        if ($id <= 0) {
            Tools::lsJson(false, '缺少景区ID');
        }

        $detail = ScenicModel::model()->getScenicInfo(array('id' => $id));
        if (empty($detail) || empty($detail['body'])) {
            Tools::lsJson(false, '该景区不存在');
        }
        $detail = $detail['body'];
        if (!empty($detail['images'])) {
            $images = array();
            foreach ($detail['images'] as $img) {
                $images[] = $img['url'];
            }
            $detail['images'] = $images;
        }
        unset($detail['thumbnail_id'], $detail['thumbnail_img'], $detail['organization_id'], $detail['landscape_organization']);

        $agency_id = intval($this->body['agency_id']);
        $source = intval($this->body['source']);
        $where = " T.is_del=0 ";

        if ($agency_id > 0) {
            $where .= " AND A.agency_id={$agency_id} ";
        }
        if ($source > 0) {
            $where .= " AND A.source={$source} ";
        }
        $AgencyProduct = AgencyProductModel::model();
        $sql = "select A.code as id,T.view_point " .
            " from " . $AgencyProduct->getTable() . " A join " . TicketTemplateModel::model()->getTable() . " T on A.product_id=T.id" .
            " where FIND_IN_SET({$id},T.scenic_id) AND " . $where . " order by A." . $this->getSortRule('update_at');

        $products = $AgencyProduct->db->setListKey('id')->selectBySql($sql);
        $product_ids = array();
        foreach ($products as $v) {
            $product_ids = array_merge($product_ids, explode(',', $v['view_point']));
        }
        $product_ids = array_unique($product_ids);
        $detail['product_ids'] = empty($products) ? array() : array_keys($products);

        $pois = ScenicModel::model()->getPoiList(array('ids' => implode(',',$product_ids), 'show_all' => 1, 'show_deleted' => 1, 'fields' => 'name'));
        $detail['scenic_poi_list'] = (empty($pois) || empty($pois['body']['data'])) ? array() : $pois['body']['data'];
        Tools::lsJson(true, '', $detail);
    }

}