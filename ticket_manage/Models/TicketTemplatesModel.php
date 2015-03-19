<?php

/**
 *
 * 2013-10-21 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class TicketTemplatesModel extends BaseModel {

    // 定义要操作的表名
    public $db = 'fx';
    public $table = 'ticket_templates';
    public $pk = 'id';
    //可关联的,对应value为model前缀
    public $relateAble = array(
        'landscape' => 'Landscapes',
    );
    //关联的字段,对应value为表字段
    public $relateField = array(
        'landscape' => 'landscape_id',
    );

    /**
     * 检查票是否可以销售
     * 条件：1.票状态正常 2.票上架 3.票未被删除 4.票不是当前登陆者发布的 5.景区状态正常 6.景区未被删除
     * @param int $id  票id
     * @return array
     */
    public function checkUseAble($id) {
        $landscapesTableName = 'landscapes';
        $where = array(
            $this->table . '.status' => 'normal',
            $this->table . '.marketable' => 'true',
            $this->table . '.deleted_at' => NULL,
            $this->table . '.organization_id|notin' => array($_SESSION['backend_userinfo']['organization_id']),
            $this->table . '.' . $this->pk => $id,
            $landscapesTableName . '.status' => 'normal',
            $landscapesTableName . '.deleted_at' => NULL,
        );

        $join = array(
            array(
                'left_join' => $landscapesTableName . ' ON ' . $landscapesTableName . '.id = ' . $this->table . '.landscape_id',
            ),
        );

        $result = $this->getListExtension($where, '', '', $this->table . '.*', '', $join);
        return $result;
    }

    /**
     * 获取门票订购列表
     * @param array $params  条件
     * @return array
     */
    public function getTicketShoppingList($params) {
        $landscapesModel = $this->load->model('landscapes');
        $param = array(
            'page' => $params['page'],
            'items' => 10,
            'relate' => 'thumbnail,level',
            'with' => 'districts',
            'order' => 'landscapes.updated_at DESC,landscapes.created_at DESC,landscapes.id DESC',
            'filter' => array(
                'landscapes.status' => 'normal',
                'tt.status' => 'normal',
                'tt.marketable' => 'true',
                'landscapes.deleted_at' => null,
                'tt.deleted_at' => null,
            ),
            'join' => array(
                array(
                    'join' => $this->table . '  tt ON ' . $landscapesModel->table . '.id=tt.landscape_id',
                ),
            ),
            'group' => $landscapesModel->table . '.id',
        );

        //假如有地区过滤
        if ($params['district_id']) {
            $param['filter'] = $this->getDistrictDeepChildFilter($param['filter'], $landscapesModel->table . '.district_id', $params['district_id']);
        }

        //景区名
        if ($params['landscape_name']) {
            $param['filter'][$landscapesModel->table . '.name'] = $params['landscape_name'];
        }

        $organizationPartnerModel = $this->load->model('organizationPartner');
        //只搜合作机构
        if ($params['partner_only'] == 'true') {
            $param['filter']['op.organization_partner_id'] = $_SESSION['backend_userinfo']['organization_id'];
            $param['filter']['op.status'] = 'normal';
            $param['join'][] = array(
                'join' => $organizationPartnerModel->table . ' op ON ' . $landscapesModel->table . '.organization_id=op.organization_main_id',
            );
        }

        $scenicInfo = $landscapesModel->commonGetList($param);
        $data['pagination'] = $scenicInfo['pagination'];
        $data['scenicInfo'] = $scenicInfo['data'];
        if ($data['scenicInfo']) {
            foreach ($data['scenicInfo'] as $key => $value) {
                if ($organizationPartnerModel->isOrganizationPartner($value['organization_id'], $_SESSION['backend_userinfo']['organization_id'])) {
                    $data['scenicInfo'][$key]['is_partner'] = true;
                }
                $data['scenicInfo'][$key]['ticketInfo'] = $this->getScenicTicketListById($value['id'], 'normal');
            }
        }
        return $data;
    }

    //获取景区和景区门票详情
    public function getScenicTicketDetail($id, $tag) {
        $scenicCommon = $this->load->common('scenic');
        $data = $scenicCommon->getScenicDetail($id);
        if ($data['scenicInfo']) {
            $organizationPartnerModel = $this->load->model('organizationPartner');
            if ($organizationPartnerModel->isOrganizationPartner($data['scenicInfo']['organization_id'], $_SESSION['backend_userinfo']['organization_id'])) {
                $data['scenicInfo']['is_partner'] = true;
            }
            $data['ticketInfo'] = $this->getScenicTicketListById($id, $tag);
        }
        return $data;
    }

    /**
     * 获取景区的票信息
     * @param int $id  景区id
     * @param string $tag  all:所有的，normal:只获取在售的票
     * @return array
     * TODO
     */
    public function getScenicTicketListById($id, $tag = 'all') {
        if ($tag == 'normal') {
            $organizationPartnerModel = $this->load->model('organizationPartner');
            $priceTemplatesModel = $this->load->model('priceTemplates');
            $priceTemplatesItemsModel = $this->load->model('priceTemplatesItems');
            $filter = array(
                $this->table . '.landscape_id' => $id,
                $this->table . '.status' => 'normal',
                $this->table . '.marketable' => 'true',
                $this->table . '.deleted_at' => null,
                $this->table . '.source' => 1,
            );

            //获取可用的价格模板sql语句
            $priceAbleSql = $this->_getPriceAbleTemplateSql($_SESSION['backend_userinfo']['organization_id']);
            $group = $this->table . '.id';

            //价格模板的价格
            $join = array(
                array(
                    'left_join' => '(' . $priceAbleSql . ') AS pa ON ' . $this->table . '.id=pa.ticket_templates_id'
                )
            );
            $fields = $this->table . '.*,pa.pti_partner_price,pa.ticket_templates_id';
            $result = $this->getListExtension($filter, '', '', $fields, $group, $join);
        } else {
            $result = $this->getList('landscape_id=' . $id . " AND deleted_at is NULL AND source=1");
        }
        return $result;
    }

    //获取可用的价格模板sql语句
    private function _getPriceAbleTemplateSql($organizationId) {
        //获取申请者是当前机构的可用的价格模板
        $priceAbleSql = 'SELECT pti.partner_price AS pti_partner_price,op.organization_main_id,pti.ticket_templates_id,pti.allow_credit FROM organization_partner op
							JOIN price_templates pt ON op.price_templates_id=pt.id
							JOIN price_templates_items pti ON pt.id=pti.price_templates_id
							WHERE pt.deleted_at IS NULL AND op.status=\'normal\' AND op.organization_partner_id=' . $organizationId;
        return $priceAbleSql;
    }

    public function getTicketPartnerPriceDetail($id, $organizationId) {
        $priceAbleSql = $this->_getPriceAbleTemplateSql($organizationId);
        $sql = $priceAbleSql . ' AND pti.ticket_templates_id=' . $id;
        return $this->getOneBySQL($sql);
    }

    /**
     * 获取价格模板门票列表
     * @return array
     */
    public function getTicketForPriceTemplate($organizationId) {
        $landscapesModel = $this->load->model('landscapes');
        $param = array(
            'filter' => array(
                'landscapes.deleted_at' => null,
                'tt.deleted_at' => null,
                'tt.organization_id' => $organizationId,
            ),
            'join' => array(
                array(
                    'join' => $this->table . '  tt ON ' . $landscapesModel->table . '.id=tt.landscape_id',
                ),
            ),
            'group' => $landscapesModel->table . '.id',
            'fields' => $landscapesModel->table . '.*',
        );

        $scenicInfo = $landscapesModel->commonGetList($param);
        $data['scenicInfo'] = $scenicInfo['data'];
        if ($data['scenicInfo']) {
            foreach ($data['scenicInfo'] as $key => $value) {
                $data['scenicInfo'][$key]['ticketInfo'] = $this->getList('landscape_id=' . $value['id'] . " AND deleted_at is NULL");
            }
        }
        return $data;
    }

}
