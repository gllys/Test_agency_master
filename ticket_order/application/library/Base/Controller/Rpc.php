<?php

require(APPLICATION_PATH . '/conf/code.php');

class RpcController extends Base_Controller_Server {

    public $body = array();

    public function indexAction() {
        $account = 'demo';
        $pwd = 'fe01ce2a7fbac8fafaed7c982a04e229';
        $imei = '865226010200991';
        $ticketNo = "166228522958825"; //'GQ00019DDZL8U6J1';
        $this->qrcode($account, $pwd, $imei, $ticketNo);
    }

    //闸机信息
    const TICKET_TYPE_BARCODE = 0;  //条码
    const TICKET_TYPE_IC = 1;  //IC卡
    const TICKET_TYPE_ID = 2;  //身份证

    protected $equipment;

    public function useTicket($order_id, $ticket_id, $num, $poi_id) {
        //返回验证结果
        $order = OrderModel::model()->getById($order_id);
        if (!$order) {
            $result = $this->_code(800201);
            $this->_setOutput($result);
        }

        $landscape_ids = explode(',', $order['landscape_ids']);
        if (!in_array($this->equipment['landscape_id'], $landscape_ids)) {
            $result = $this->_code(800206);
            $this->_setOutput($result);
        }

        $order_item = reset(OrderItemModel::model()->setTable($order_id)->search(array('order_id' => $order_id)));
        if (!$order_item) {
            $result = $this->_code(800201);
            $this->_setOutput($result);
        }

        if (!$poi_id) {
            $poiList = PoiModel::model()->getPoiList($this->equipment['landscape_id']);
            $poiIds = array_keys($poiList);
        } else {
            $poiIds = explode(',', $poi_id);
        }

        try {
            $now = time();
            TicketModel::model()->begin();
            $poi_used = array();
            foreach ($poiIds as $id) {
                $rt = TicketModel::model()->useTicket($order_id, $id, $num, $ticket_id);
                if ($rt)
                    $poi_used[] = $id;
            }
            // LOG
            $log['uid'] = '';
            $log['supplier_id'] = $this->equipment['organization_id'];
            $log['landscape_id'] = $this->equipment['landscape_id'];
            $log['poi_id'] = implode(',', $poi_used);
            $log['equipment_code'] = $this->equipment['code'];
            $log['created_at'] = time();
            $log['user_name'] = '';
            $log['channel'] = 1;
            $log['status'] = 1;
            $log['record_code'] = $order_id;
            $log['code'] = $this->body['content'];
            $log['num'] = $num;
            $log['ticket_type_name'] = $order_item['name'];
            $log['tickets_code'] = $order_item['ticket_template_id'];
            TicketRecordModel::model()->insert($log);
            TicketModel::model()->commit();
        } catch (Exception $e) {
            var_dump($e->getMessage());
            TicketModel::model()->rollback();
            $result = $this->_code(800502);
            $this->_setOutput($result);
        }


        $result = $this->_code(0, $order_item['name'], $num);
        $this->_setOutput($result);
    }

    /**
     * 二维码扫描接口
     *
     * 2013-9-26
     *
     * @param string $account   用户名
     * @param string $pwd        密码
     * @param string $imei       设备号
     * @param string $ticketNo    票号
     * @param int    $ticketType  票类型
     *
     * return string xml | json
     */
    public function qrcode($account, $pwd, $imei, $ticketNo, $ticketType = 0) {
        try {
            $imei = $imei;
            $this->body['content'] = $content = $ticketNo;
            $num = 0;
            if (!$imei) {
                $result = $this->_code(800500);
                $this->_setOutput($result);
            }

            if (!$content) {
                $result = $this->_code(800104);
                $this->_setOutput($result);
            }

            $this->equipment = EquipmentModel::model()->getDevice($imei);
            if (!$this->equipment) {
                $result = $this->_code(800500);
                $this->_setOutput($result);
            }

            if (!$this->equipment['poi_id']) {
                $result = $this->_code(800501);
                $this->_setOutput($result);
            }
            $poi_id = $this->equipment['poi_id'];
            $ticket_id = 0;

            $len = strlen($content); //号码的长度
            if ($len == 6 || $len == 18) {
                $order_ids = OrderModel::model()->getOrderByCard($content);
                if (!$order_ids) {
                    $result = $this->_code(800300);
                    $this->_setOutput($result);
                }

                if (count($order_ids) >= 2) {
                    $result = $this->_code(800302);
                    $this->_setOutput($result);
                }
                $content = $order_ids[0];
            } else if ($len == 11) {
                $order_ids = OrderModel::model()->getOrderByPhone($content);
                if (!$order_ids)
                    if (!$order_ids) {
                        $result = $this->_code(800303);
                        $this->_setOutput($result);
                    }

                if (count($order_ids) >= 2) {
                    $result = $this->_code(800304);
                    $this->_setOutput($result);
                }
                $content = $order_ids[0];
            }

            $type = substr($content, 0, 1);
            if ($type == 1) {
                $order_id = $content;
            } else {
                $ticket_id = $content;
                $ticket_info = TicketModel::model()->getById($ticket_id);
                $order_id = $ticket_info['order_id'];
                $num = 1;
            }

            //查看订单可存在
            $order = OrderModel::model()->getById($order_id);
            if (!$order) {
                $result = $this->_code(800201);
                $this->_setOutput($result);
            }

            //查看还有没有可用票
            $list = OrderModel::model()->getOrderList(array($order_id), $poi_id, $this->equipment['landscape_id']);
            if (!$list) {
                $result = $this->_code(800203);
                $this->_setOutput($result);
            }
            $list = reset($list);
            $num = $list['nums'];
            $this->useTicket($order_id, $ticket_id, $num, $poi_id);
        } catch (Exception $e) {
            $result = $this->_code(800502);
            $this->_setOutput($result);
        }
    }

    //记录二维码成功失败原因日志 
    private $recordParam = array();

    private function _recordOutput($code = 0, $log = '', $data = array()) {
//        $ticketRecordModel = $this->load->model('ticketRecord');  //加载记录模块
//        $this->recordParam['http_status'] = $code;
//        $this->recordParam['status'] = $code == 200 ? 1 : 0;
//        $this->recordParam['note'] = $log;
//        $ticketRecordModel->add($this->recordParam);
//        $num = isset($this->recordParam['num']) ? $this->recordParam['num'] : 0;
//        $type = isset($this->recordParam['ticket_type_name']) ? $this->recordParam['ticket_type_name'] : 0;
        $result = $this->_code($code);
        $this->_setOutput($result);
    }

    /**
     * 验证登陆
     * 2013-9-26
     * @param string $account
     * @param string $pwd
     * 
     * return bool
     */
    private function _validation($account, $pwd) {
        $auth = unserialize(SET_API_PHPRPC_USER);
        if ($account != $auth['account'] || $pwd != $auth['password']) {
            $result = $this->_code(800301);
            $this->_setOutput($result);
            return false;
        }
        return true;
    }

    /**
     * 获取返回结果状态和说明信息
     * 
     * 2013-9-26
     *
     * @param int $code 编码
     * @param int $ResultType 票种信息
     * @param int $ResultSum  人数
     *
     * @return array
     * @author cuiyulei
     * */
    private function _code($code, $ResultMessage = 0, $ResultSum = 0) {
        // 获取数据
        $codes = unserialize(PI_PHPRPC_CODES);

        // 返回结果
        $codeArr = $codes[$code];

        $result['ResultCode'] = $codeArr['ResultCode'];

        //票种类型
        $result['ResultType'] = $codeArr['ResultType'];

        //人数
        $result['ResultSum'] = $ResultSum;

        //返回信息
        $result['ResultMessage'] = $ResultMessage ? $ResultMessage : $codeArr['ResultMessage'];

        return $result;
    }

    /**
     * 输出xml文档
     * 2013-9-26
     *
     * @param int $code 编码
     * @param string $ResultType 票种信息
     * @param int $ResultSum  人数
     *
     * @return string xml 文档
     * @author cuiyulei
     * */
    private function _setOutput($results) {
        //纪录输出的数据
        //定义xml根结点
        ob_end_clean() ;
        header('Content-Type: text/xml');
        $xml = "<?xml version='1.0' encoding='utf-8'?>  
				<CheckTicketDataResult></CheckTicketDataResult>";
        //定义xml对象
        $xmlObj = simplexml_load_string($xml);

        //添加子节点
        foreach ($results as $key => $rt) {
            $xmlObj->addChild($key, $rt);
        }
        //输出xml
        echo $xmlObj->asXML();
        exit();
    }

    /*
     * 自动落杆接口
     */

    public function GateOnline($account, $pwd, $imei) {
        // 验证
        $validated = $this->_validation($account, $pwd);
        if (!$validated) {
            return null;
        }
        //纪录输出的数据
        //定义xml根结点
        header('Content-Type: text/xml');
        echo '<?xml version="1.0"?>
                <GateOnline>
                <ResultCode>1</ResultCode>
                <ResultMessage>自行通过</ResultMessage>
                </GateOnline>';
    }

}
?>



