<?php

/**
 * API V2
 *
 * 2014-03-20 1.0 fangshixiang 创建
 * 文档地址：http://redmine.ihuilian.com/projects/fx-api/wiki/rpc
 *
 * @author  fangshixiang
 * @version 1.0
 */
class RpcController extends ServiceController {

    //闸机信息
    const TICKET_TYPE_BARCODE = 0;  //条码
    const TICKET_TYPE_IC = 1;  //IC卡
    const TICKET_TYPE_ID = 2;  //身份证

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

    public function qrcode($account, $pwd, $imei, $ticketNo, $ticketType) {
        //$account = 'demo';
        //$pwd = 'fe01ce2a7fbac8fafaed7c982a04e229';
        // 验证
        $validated = $this->_validation($account, $pwd);
        if (!$validated) {
            return null;
        }
        //$imei = '3432423423423';
        //$ticketNo = "432522198809020692"; //'GQ00019DDZL8U6J1';
        //$ticketNo = "000002FPUF2GBKIE"; //'GQ00019DDZL8U6J1';
        //检查设备信息
        $equipment = $this->_checkImei($imei);
        if (!$equipment) {
            return null;
        }
        //处理票据或订单编码
        $this->_dealCode($equipment, $ticketNo);
    }

    //记录二维码成功失败原因日志 
    private $recordParam = array();

    private function _recordOutput($code = 0, $log = '', $data = array()) {
        $ticketRecordModel = $this->load->model('ticketRecord');  //加载记录模块
        $this->recordParam['http_status'] = $code;
        $this->recordParam['status'] = $code == 200 ? 1 : 0;
        $this->recordParam['note'] = $log;
        $ticketRecordModel->add($this->recordParam);
        $num = isset($this->recordParam['num']) ? $this->recordParam['num'] : 0;
        $type = isset($this->recordParam['ticket_type_name']) ? $this->recordParam['ticket_type_name'] : 0;
        $result = $this->_code($code, $type, $num);
        $this->_setOutput($result);
    }

    /**
     * 检查票据或订单编码
     *
     * @param array  $equipment 设备信息
     * @return mixed void | array
     * @author fangshixiang
     * */
    private function _dealCode($equipment, $ticketNo) {
        // 得到参数
        $get = $this->getGet();
        $codeNo = $ticketNo;
        $useNum = NULL; #使用数量
        $codeLength = strlen($codeNo); //号码的长度
        #加载模块
        $orderModel = $this->load->model('orders');  //加载订单模型
        $orderItemModel = $this->load->model('orderItems');  //加载订单模型
        $ticketModel = $this->load->model('tickets'); //加载票模型
        $ticketUsedModel = $this->load->model('ticketUsed');  //加载票务使用状况模型
        //设备未设置子景点
        if (!$equipment['poi_id']) {
            $result = $this->_code(800501);
            $this->_setOutput($result);
            return null;
        }

        #设置日志参数
        $this->recordParam = array(
            'code' => $codeNo,
            'organization_id' => $equipment['organization_id'],
            'landscape_id' => $equipment['landscape_id'],
            'poi_id' => $equipment['poi_id'],
            'equipment_code' => $equipment['code'],
            'created_at' => date('Y-m-d H:i:s'),
        );

        /*         * **添加,身份证验票*** */
        if ($codeLength == 11 || $codeLength == 18) {
            $orders = $this->load->model('orderMap')->getList("map LIKE '{$codeNo}' AND expire_start_at>'" . date('Y-m-d') . "'", '', '', 'DISTINCT order_id');
            if (count($orders) == 0) {
                $this->_recordOutput('800300', '该身份证下无订单');
                return null;
            }
            if (count($orders) >= 2) {
                $this->_recordOutput('800302', '该身份证下存在多个订单');
                return null;
            }
            $codeNo = $orders[0]['order_id'];
            $codeLength = 16;
        }

        //检查编码code是否正确,
        if ($codeLength != 16 && $codeLength != 19) {
            $this->_recordOutput('800104', '无效票'); #二维码不存在
            return null;
        }

        //需传参数定义
        $orderId = null;
        $ticketId = null;
        //如果是票号
        if ($codeLength == 19) {
            $useNum = 1;
            $rs = $ticketModel->getOne(array('id' => $codeNo), '', 'order_id,id'); #得到订单号
            if (!$rs) {
                $this->_recordOutput('800201', '票号不正确'); #二维码不存在
            }
            $orderId = $rs['order_id'];
            $ticketId = $rs['id'];
            $this->recordParam['tickets_code'] = $codeNo;
            $orderCode = $this->recordParam['record_code'] = $orderId;
        }

        //如果是订单号
        if ($codeLength == 16) {
            $orderId = $orderCode = $this->recordParam['record_code'] = $codeNo;
        }

        //得到票的订单情况信息
        $orderItem = $orderItemModel->getOne(array('order_id' => $orderId));
        if (!$orderItem) {
            //Todo::如果本地站没有该订单，则调用新分销的接口查询该订单是否存在
            $this->_recordOutput('800201', '订单号不正确'); #二维码不存在
            return null;
        }else{
            //得到可使用的票
            $ticketIds = $this->_getTicket($orderItem, $equipment, $useNum, $ticketId);
            if ($ticketIds === null) {
                return null;
            } else if (!$ticketIds) {
                $this->_recordOutput('800203', '票已使用完'); #二维码不存在
                return null;
            }

            $ticketNum = count($ticketIds);
            //使用该票
            $param = array(
                'order_id' => $orderId,
                'organization_id' => $equipment['organization_id'],
                'landscape_id' => $equipment['landscape_id'],
                'poi_id' => $equipment['poi_id'],
                'equipment_id' => $equipment['id'],
                'created_at' => date('Y-m-d H:i:s'),
            );
            foreach ($ticketIds as $ticketId) {
                $param['ticket_id'] = $ticketId;
                $ticketUsedModel->add($param);
            }

            $param = array(
                'orderCode' => $orderCode,
                'content' => $codeNo,
                'validation_time' => date('Y-m-d H:i:s'),
                'ticket' => array(
                    'number' => $ticketNum, //使用数量
                    'type' => $orderItem['name'],
                    'price' => $orderItem['price'],
                ),
            );
            $tickets = $ticketModel->getList('id in("' . join('","', $ticketIds) . '")', '', '', 'order_id');
            $tickethashs = arrayKey($tickets, 'order_id');
            $this->recordParam['tickets_code'] = join(',', $tickethashs);
            $this->recordParam['ticket_type_name'] = $orderItem['name'];
            $this->recordParam['num'] = $ticketNum;
            $this->_recordOutput(0, '成功', $param);
            return null;
        }
    }

    /**
     * 得到订单子景点多于一张的票信息或只有一张票是否可用的状态
     * @param array  $orderId 订单号
     * @param array  $equipment 设备
     * @param array  $useNum 根据使用数量得到可使用的票
     * @param array  $ticketId 根据票号得到可使用的票
     * @return mixed void | array
     * @author fangshixiang
     * */
    private function _getTicket($orderItem, $equipment, $useNum = NULL, $ticketId = NULL) {
        $orderItemId = $orderItem['order_id'];
        $ticketRelationsModel = $this->load->model('ticketRelations');  //加载票务订单子景点模型
        $ticketModel = $this->load->model('tickets'); //加载票模型
        $ticketUsedModel = $this->load->model('ticketUsed');  //加载票务使用状况模型
        //得到订单下可用票
        $param = array('order_id' => $orderItemId,
            'status' => 1,
        );
        if ($ticketId) {
            $param['id'] = $ticketId;
        }
        $tickets = $ticketModel->getList($param, '', '', 'id');
        //var_dump($tickets);
        if (!$tickets) {
            $this->_recordOutput('800204', '您的票未付款或退款中');
            return null;
        }
        $ticketIds = arrayKey($tickets, 'id');
        //得到可用票在子景点下可用票
        $poiTickets = $ticketRelationsModel->getList("relate_poi='{$equipment['poi_id']}' AND ticket_id in('" . join("','", $ticketIds) . "')", '', '', 'ticket_id');
        //var_dump($poiTickets);
        if (!$poiTickets) {
            $this->_recordOutput('800206', '不可在此景点使用');
            return null;
        }
        $ticketIds = arrayKey($poiTickets, 'ticket_id');
        //var_dump($ticketIds);

        /*         * ******订单有效期验证开始********* */
        //使用时间段初始化
        $beginTime = strtotime($orderItem['expire_start_at']);
        $endTime = strtotime($orderItem['expire_end_at']);

        //订单填写的游玩日期
        if ($orderItem['use_by'] == 1) {
            $beginTime = strtotime($orderItem['useday']);
            $endTime = strtotime($orderItem['useday']) + ($orderItem['use_expire'] + 1) * 86400; //84600一天秒数
        }

        //还未开始
        if ($orderItem['expire_start_at'] && time() < $beginTime) {
            $$this->_recordOutput('800207', '未到使用时间');
            return null;
        }

        //已结束
        if ($orderItem['expire_end_at'] && time() > $endTime) {
            $this->_recordOutput('800208', '已超过有效期');
            return null;
        }

        //适用日期,周一到周日
        $weekly = explode(',', $orderItem['weekly']);
        if (!in_array(date('w'), $weekly)) {
            $this->_recordOutput('800209', '今天不可使用');
            return null;
        }
        /*         * ********订单有效期验证结束********* */


        //验票过程
        $_ticketIds = array(); //可用票的id
        $i = 0;
        foreach ($ticketIds as $id) {
            //该票的有效期
            $useTicket = $ticketUsedModel->getOne(array('ticket_id' => $id), 'id asc'); #得到该票使用的第一条信息
            //var_dump($useTicket);
            if ($useTicket) {#已使用
                if ($orderItem['use_by'] == 2) { //使用决定结束时间
                    $endTime = strtotime(date('Y-m-d 23:59:59', strtotime($useTicket['created_at']))) + $orderItem['use_expire'] * 86400; #84600一天秒数
                }

                if (time() > $endTime) { #已经过期
                    continue;
                }

                if ($orderItem['use_type'] == 0 && date('Y-m-d', strtotime($useTicket['created_at'])) != date('Y-m-d')) {#使用当天有效,如果第一次使用不是当天了，则跳出
                    continue;
                }
            } else {#未使用
                if ($orderItem['use_by'] != 2 && time() < $beginTime) { #还未到使用日期
                    continue;
                }

                if ($orderItem['use_by'] != 2 && time() > $endTime) { #已经超过有效期
                    continue;
                }
            }

            //查找该票在该机构,或景区，或子景点，次数处理相关逻辑
            if ($orderItem['limit_type'] != 0) { #无限制票只查看检查过期时间
                //得到票已使用次数
                $where = "ticket_id = '{$id}' ";

                if ($orderItem['limit_type'] == 1) { //限制每天次数
                    $where .= " AND  created_at BETWEEN '" . date('Y-m-d 00:00:00') . "' AND '" . date('Y-m-d 23:59:59') . "'";
                }

                //统计次数标准
                $limitBys = array(
                    0 => 'organization_id', //按机构统计
                    1 => 'landscape_id', //按景点统计
                    2 => 'poi_id', //按子景点统计
                );
                $limitBy = $limitBys[$orderItem['limit_by']];
                $where .= " AND {$limitBy}='{$equipment[$limitBy]}'";
                //echo $where ;
                $limit = $ticketUsedModel->getCount($where, 'COUNT(id)');
                if ($limit && $limit >= $orderItem['limit_times']) {
                    continue;
                }
            }
            $_ticketIds[] = $id;

            //如果使用，超过了使用数，则直接跳出循环
            if ($useNum && $i >= $useNum - 1) {
                break;
            }
            $i++;
        }
        return $_ticketIds;
    }

    /**
     * 检查设备信息
     *
     * @param string $imei 设备编号
     * @param bool $updateFlag 记录设备号码标识
     * @return mixed void | array
     * @author fangshixiang
     * */
    private function _checkImei($imei, $updateFlag = false, $userinfo = array()) {

        $equipmentModel = $this->load->model('equipment');
        $equipment = $equipmentModel->getOne(array('code' => $imei));
        //设备不存在
        if (empty($equipment)) {
            $equipmentModel->add(array('code' => $imei, 'type' => 'gate'));
            $result = $this->_code(800501);
            $this->_setOutput($result);
        }
        return $equipment;
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
        $auth = unserialize(PI_PHPRPC_USER);
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
    }
    
    /*
     * 自动落杆接口
     */
    public function GateOnline($account,$pwd,$imei){
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
