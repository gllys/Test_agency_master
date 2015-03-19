<?php

/**
 *  
 * 
 * 2013-09-09
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class ItourismApiCommon extends BaseCommon {

    /**
     * 从数据中心获取景点信息
     * @param array $status 状态
     * @return string or array
     */
    public function getItourismLocationsInfo($param = array()) {
        $result = $this->commonItourismRequest('getLandscapes', $param);
        return $result;
    }

    /**
     * 与数据中心交互,创建票
     * @param array $orderItemsData 订单明细数据
     * @param array $ticketsData 票号数据
     * @return json
     */
    public function itourismApiCreateTickets($useday, $orderItemsData, $ticketsData) {
        $landscapeInfo = $this->load->model('landscapes')->getID($orderItemsData['landscape_id'], 'location_hash');
        $locationId = $this->getLocationIdByHash($landscapeInfo['location_hash']);
        if ($locationId) {
            $param = $this->_formatItourismApiCreateTickets($useday, $orderItemsData, $ticketsData, $locationId);
            $result = $this->commonItourismRequest('addTickets', $param);
            return $result;
        }
    }

    //组api需要的数据  todo locationid
    private function _formatItourismApiCreateTickets($useday, $orderItemsData, $ticketsData, $locationId) {
        $ticketsHashs = array();
        foreach ($ticketsData as $ticket) {
            $ticketsHashs[] = $ticket['hash'];
        }
        $itourTickets['ids'] = implode(',', $ticketsHashs);
        $landscapeInfo = $this->load->model('landscapes')->getID($orderItemsData['landscape_id'], 'name');
        $itourTickets['name'] = $landscapeInfo['name'] . '|' . $orderItemsData['name'];
        $itourTickets['price'] = $orderItemsData['price'];
        $itourTickets['expired_start_at'] = $useday;
        if ($orderItemsData['use_expire'] == 0) {
            $useExpire = strtotime($useday) + 86400 * $orderItemsData['use_expire'];
            $endAt = strtotime($orderItemsData['expired_end_at']);
            $endExpire = date('Y-m-d', min($useExpire, $endAt));
        } else {
            $endExpire = $useday;
        }
        $itourTickets['expired_end_at'] = $endExpire;
        $itourTickets['week'] = $orderItemsData['weekly'];
        $itourTickets['status'] = 'unpaid';
        $itourTickets['location'] = $locationId;
        return $itourTickets;
    }

    //与数据中心交互,更新票的状态为支付 todo 多个支付一个请求
    public function itourismApiPay($orderId) {
        $ticketsModel = $this->load->model('tickets');
        $ticketsList = $ticketsModel->getList('order_id=' . $orderId);
        $ticketsHashs = array();
        if ($ticketsList) {
            // foreach($ticketsList as $ticket) {
            // 	$ticketsHashs[] = $ticket['hash'];
            // }
            // $ids = implode(',', $ticketsHashs);
            // $result = $this->commonItourismRequest('payTickets', array(), $ids);

            foreach ($ticketsList as $ticket) {
                $result = $this->commonItourismRequest('payTickets', array(), $ticket['hash']);
            }

            return $result;
        }
    }

    //与数据中心交互,更新票的状态为退款成功
    public function itourismApiRefunded($refundInfo) {
        $refundApplyItemsModel = $this->load->model('refundApplyItems');
        $refundApplyItems = $refundApplyItemsModel->getList('refund_apply_id=' . $refundInfo['refund_apply_id'], '', '', 'ticket_hash');
        if ($refundApplyItems) {
            $ticketsHashs = array();
            foreach ($refundApplyItems as $item) {
                $ticketsHashs[] = $item['ticket_hash'];
            }
            $ids = implode(',', $ticketsHashs);
            $result = $this->commonItourismRequest('refundedTickets', array(), $ids);
            return $result;
        }
    }

    //与数据中心交互,更新票的状态为使用
    public function itourismApiUsed($locationHash, $ticketIds) {
        $locationId = $this->getLocationIdByHash($locationHash);
        $result = $this->commonItourismRequest('usedTickets', array(), $ticketIds, $locationId);
        return $result;
    }

    //与数据中心交互,更新票的状态为作废 todo 
    public function itourismApiUseless($ticketIds) {
        // $result     = $this->commonItourismRequest('uselessTickets', array(), $ticketIds);
        foreach ($ticketIds as $ticket) {
            $result = $this->commonItourismRequest('updateTickets', array('status' => 'deleted'), $ticket);
        }
        return $result;
    }

    //获取locationid
    public function getLocationIdByHash($hash) {
        $param = array(
            'filter' => 'hash:equal_' . $hash,
        );
        $result = $this->commonItourismRequest('getLandscapes', $param);
        $resultArr = json_decode($result, 1);
        if ($resultArr['status'] == 'succ') {
            return $resultArr['data']['data'][0]['id'];
        } else {
            return false;
        }
    }

    /* 获取Location的部分信息
     * @param array $param 参数
     * @param array $fields 只需返回的字段
     * @return string or array
     */

    public function getSomeLocations($param, $fields = array()) {
        $result = $this->commonItourismRequest('getLandscapes', $param);
        $resultArr = json_decode($result, 1);
        if ($resultArr && $resultArr['status'] == 'succ') {
            if ($fields) {
                $_data = array();
                $_data['data']['pagination'] = $resultArr['data']['pagination'] ;
                $data =  $resultArr['data']['data'];
                foreach ($data as $item) {
                    $__data = array();
                    foreach ($fields as $field) {
                        if (isset($item[$field])) {
                            $__data[$field] = $item[$field];
                        }
                    }
                    $_data['data']['data'][] = $__data;
                }
                return $_data['data'];
            } else {
                return $resultArr['data'];
            }
        } else {
            return false;
        }
    }

    //与数据中心交互,更新票的信息
    public function itourismApiUpdate($param, $ticketIds) {
        $result = $this->commonItourismRequest('updateTickets', $param, $ticketIds);
        return $result;
    }

}
