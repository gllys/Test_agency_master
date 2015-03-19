<?php

/**
 * API V2
 *
 * 2014-03-20 1.0 fangshixiang 创建
 * 文档地址：http://redmine.ihuilian.com/projects/fx-api/wiki/device
 *
 * @author  fangshixiang
 * @version 1.0
 */
class DeviceController extends ServiceController {
    /**
     * 二维码扫描接口
     */
    public function qrcode() {
        //记录客户端输入的参数
        // 参数设置
        $get = $this->getGet();

        //检查设备信息
        $equipment = $this->_checkImei($get['imei']);

        //处理票据或订单编码
        $this->_dealCode($equipment);
    }

    /**
     * 门票验证记录
     */
    public function history() {
        //记录客户端输入的参数
        // 参数设置
        $get = $this->getGet();

        //检查设备信息
        $equipment = $this->_checkImei($get['imei']);

        //根据token得到用户信息
        $token = $this->load->model('token')->getOne(array('token' => $get['token']));

        //组织查询条件
        $apiCommon = $this->load->common('apiDevice');
        $where = 'equipment_code="' . $equipment['code'] . '" AND uid=' . $token['uid'];
        if (isset($get['start_time']) && !empty($get['start_time']))
            $where .= " AND created_at>='" . $get['start_time'] . "'";

        if (isset($get['end_time']) && !empty($get['end_time']))
            $where .= " AND created_at<='" . $get['end_time'] . "'";

        if (isset($get['code']) && !empty($get['code']))
            $where .= " AND code='" . $get['code'] . "'";

        //组织返回条数
        $limit_start = isset($get['start']) ? $get['start'] : 0;
        $limit_count = isset($get['count']) ? $get['count'] : 10;
        $limit = $limit_start . ',' . $limit_count;

        //记录查询参数
        //加载模块
        $history = $apiCommon->getRecord($where, $limit, 'created_at DESC');

        //判断是否有检票历史记录
        if (!$history['record']) {
            code('0401');
            json();
        }

        // 组织数据
        $data['count'] = $limit_count;
        $data['start'] = $limit_start;
        $data['total'] = $history['total'];
        $ticket = array();
        foreach ($history['record'] as $key => $record) {
            $ticket[$key]['id'] = $record['id'];
            $ticket[$key]['code'] = $record['code'];
            $ticket[$key]['record_code'] = $record['record_code'];
            $ticket[$key]['tickets_code'] = $record['tickets_code'];
            $ticket[$key]['type'] = $record['ticket_type_name'];
            $ticket[$key]['note'] = $record['note'];
            $ticket[$key]['validation'] = $record['status'] == 1 ? '成功' : '失败';
            $ticket[$key]['validation_num'] = $record['num'];
            $ticket[$key]['validation_time'] = $record['created_at'];
        }
        $data['ticket'] = $ticket;

        // 输出
        $this->_setOutput($data);
    }

    //记录二维码成功失败原因日志 
    private $recordParam = array();

    private function _recordOutput($code = 200, $log = '', $data = array(),$recordLog = true) {
        //记录验证日志
        if ($recordLog) {
            $ticketRecordModel = $this->load->model('ticketRecord');  //加载记录模块
            $this->recordParam['http_status'] = $code;
            $this->recordParam['status'] = $code == 200 ? 1 : 0;
            $this->recordParam['note'] = $log;
            $ticketRecordModel->add($this->recordParam);
        }
        
        //如果有订单号，则返回景区备注，旅行社备注
        if(isset($this->recordParam['record_code'])){
            $orderModel = $this->load->model('orders');  //加载订单模型
            $orderScenicRemarkModel = $this->load->model('orderScenicRemark');  //加载景区订单备注
            $orderId = $this->recordParam['record_code'] ;
            $order = $orderModel->getOne(array('id' => $orderId));
            if($order['source']==1){
            $orderScenicRemarks = $orderScenicRemarkModel->getList(array('order_id' => $orderId,'seller_organization_id'=>$this->recordParam['organization_id']),'','','s_remark');
            }else{
            $orderScenicRemarks = array();
            $order['remarks'] = '';
            }
            $data['remarks']=$order['remarks'];
            $data['order_scenic_remark']=$orderScenicRemarks;
        }
        code($code);
        json($data);
    }

    /**
     * 检查票据或订单编码
     *
     * @param array  $equipment 设备信息
     * @return mixed void | array
     * @author fangshixiang
     * */
    private function _dealCode($equipment) {
        // 得到参数
        $get = $this->getGet();
        $codeNo = $get['content'];
        $useNum = isset($get['num']) ? $get['num'] : 0; #使用数量
        $codeLength = strlen($codeNo); //号码的长度
        #加载模块
        $orderModel = $this->load->model('orders');  //加载订单模型
        $orderItemModel = $this->load->model('orderItems');  //加载订单模型
        $ticketModel = $this->load->model('tickets'); //加载票模型
        $ticketUsedModel = $this->load->model('ticketUsed');  //加载票务使用状况模型
        
        //设备未设置子景点
        if (!$equipment['poi_id']) {
            code('0205'); #二维码不存在
            json();
        }
        
        /*         * **添加,身份证验票*** */
        if ($codeLength == 6||$codeLength == 11||$codeLength == 18) {
            $this->_cardOrderInfos($codeNo, $equipment);
        }


        //根据token得到用户信息
        $token = $this->load->model('token')->getOne(array('token' => $get['token']));

        #设置日志参数
        $this->recordParam = array(
            'uid' => $token['uid'],
            'code' => $codeNo,
            'organization_id' => $equipment['organization_id'],
            'landscape_id' => $equipment['landscape_id'],
            'poi_id' => $equipment['poi_id'],
            'equipment_code' => $equipment['code'],
            'created_at' => date('Y-m-d H:i:s'),
        );

        //检查编码code是否正确,
        if ($codeLength != 16 && $codeLength != 19) {
            $this->_recordOutput('0201', '二维码不存在'); #二维码不存在
        }

        //需传参数定义
        $orderId = null;
        $ticketId = null;
        //如果是票号
        if ($codeLength == 19) {
            $useNum = 1;
            $rs = $ticketModel->getOne(array('id' => $codeNo), '', 'order_id,id'); #得到订单号
            if (!$rs) {
                $this->_recordOutput('0201', '票号不正确'); #二维码不存在
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
            $this->_recordOutput('0201', '订单号不正确'); #二维码不存在
        }

        //得到可使用的票
        $ticketIds = $this->_getTicket($orderItem, $equipment, $useNum, $ticketId);
        if (!$ticketIds) {
            $this->_recordOutput('0203', '票已使用完'); #二维码不存在
        }

        $ticketNum = count($ticketIds);
        if (!$useNum && $ticketNum > 1) {#返回使用数量
            $data = array('listType'=>1,'number' => $ticketNum, 'type' => $orderItem['name'], 'source' => $this->load->common('platform')->findByPk($orderItem['source']),);
            $this->_recordOutput('200', '',$data,false); #此票号扫描失败
        } else {
            //使用该票
            $ticketUsedModel->begin();
            $param = array(
                'uid' => $token['uid'],
                'order_id' => $orderId,
                'organization_id' => $equipment['organization_id'],
                'landscape_id' => $equipment['landscape_id'],
                'poi_id' => $equipment['poi_id'],
                'equipment_id' => $equipment['id'],
                'created_at' => date('Y-m-d H:i:s'),
            );
            foreach ($ticketIds as $ticketId) {
                $param['ticket_id'] = $ticketId;
                $saveResult = $ticketUsedModel->add($param);
                if (!$saveResult) {
                    $ticketUsedModel->rollback();
                    $this->_recordOutput('0210', '此票号扫描失败'); #此票号扫描失败
                }
            }
            $ticketUsedModel->commit();
            
            //返回验证结果
            $param = array(
                'orderCode' => $orderCode,
                'content' => $codeNo,
                'listType'=>2, //票号直接验证
                'validation_time' => date('Y-m-d H:i:s'),
                'ticket' => array(
                    'number' => $ticketNum, //使用数量
                    'source' => $this->load->common('platform')->findByPk($orderItem['source']),
                    'type' => $orderItem['name'],
                    'price' => $orderItem['price'],
                ),
            );
            $tickets = $ticketModel->getList('id in("' . join('","', $ticketIds) . '")', '', '', 'order_id');
            $tickethashs = arrayKey($tickets, 'order_id');
            $this->recordParam['tickets_code'] = join(',', $tickethashs);
            $this->recordParam['ticket_type_name'] = $orderItem['name'];
            $this->recordParam['num'] = $ticketNum;
            $this->_recordOutput('200', '成功-订单备注:'.$order['remarks'], $param);
        }
    }

    /**
     * 得到订单子景点多于一张的票信息或只有一张票是否可用的状态
     * @param array  $orderId 订单号
     * @param array  $equipment 设备
     * @param array  $useNum 根据使用数量得到可使用的票
     * @param array  $ticketId 根据票号得到可使用的票
     * @more bool 如果是more的时候是收集信息,不直接打印
     * @return mixed void | array
     * @author fangshixiang
     * */
    private function _getTicket($orderItem, $equipment, $useNum = NULL, $ticketId = NULL, $more = false) {
        $orderItemId = $orderItem['order_id'];
        $ticketRelationsModel = $this->load->model('ticketRelations');  //加载票务订单子景点模型
        $ticketModel = $this->load->model('tickets'); //加载票模型
        $ticketUsedModel = $this->load->model('ticketUsed');  //加载票务使用状况模型

        /*         * * ***检查子景点是否有票开始***** */
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
            if ($more) {
                return false;
            } else {
                $this->_recordOutput('0204', '您的票未付款或退款中');
            }
        }
        $ticketIds = arrayKey($tickets, 'id');
        //var_dump($ticketIds);
        //得到可用票在子景点下可用票
        $poiTickets = $ticketRelationsModel->getList("relate_poi='{$equipment['poi_id']}' AND ticket_id in('" . join("','", $ticketIds) . "')", '', '', 'ticket_id');
        //var_dump($poiTickets);
        if (!$poiTickets) {
            if ($more) {
                return false;
            } else {
                $this->_recordOutput('0206', '不可在此景点使用');
            }
        }
        $ticketIds = arrayKey($poiTickets, 'ticket_id');
        //var_dump($ticketIds);
        /*         * *****检查子景点是否有票结束******* */

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
            if ($more) {
                return false;
            } else {
                $this->_recordOutput('0207', '未到使用时间');
            }
        }

        //已结束
        if ($orderItem['expire_end_at'] && time() > $endTime) {
            if ($more) {
                return false;
            } else {
                $this->_recordOutput('0208', '已超过有效期');
            }
        }

        //适用日期,周一到周日
        $weekly = explode(',', $orderItem['weekly']);
        if (!in_array(date('w'), $weekly)) {
            if ($more) {
                return false;
            } else {
                $this->_recordOutput('0209', '今天不可使用');
            }
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
     * 得到身份证信息
     * 
     * 
     */
    private function _cardOrderInfos($codeNo, $equipment) {
        $orders = $this->load->model('orderMap')->getList("map LIKE '%{$codeNo}' AND expire_start_at>'" . date('Y-m-d') . "'", '', '', 'DISTINCT order_id');
        if (!$orders) {
            code('0600'); #对不起，此身份证或手机号不存在，请重新扫描
            json();
        }

        $orderItemModel = $this->load->model('orderItems');  //加载订单模型
        $orderModel = $this->load->model('orders');  //加载订单模型
        $data = array();
        foreach ($orders as $order) {
            $orderItem = $orderItemModel->getOne(array('order_id' => $order['order_id']));
            //得到可使用的票
            $ticketIds = $this->_getTicket($orderItem, $equipment, NULL, NULL, true);
            if (!$ticketIds) {
                continue;
            }
            $order = $orderModel->getOne(array('id' => $order['order_id']));
            $data['tickets'][] = array(
                'ticket_name'=>$orderItem['name'],
                'order_id'=>$order['id'],
                'owner_name'=>$order['owner_name'],
                'owner_mobile'=>$order['owner_mobile'],
                'owner_card'=>$order['owner_card'],
                'num'=>count($ticketIds),
            );
        }
        
        if (!$data) {
            code('0601'); #对不起，此身份证或手机号不存在，请重新扫描
            json();
        }
        if(count($codeNo)==11){
           $data['orderType'] = 1 ; 
        }else{
           $data['orderType'] = 2 ;  
        }
        $data['content'] = $codeNo ;
        $data['listType'] = 3 ; //身份证
        code(200);
        json($data);
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
        EquipmentModel::model()->url = "v1/";
        $equipmentModel = $this->load->model('equipment');
        $equipment = $equipmentModel->getOne(array('code' => $imei));

        //设备不存在
        if (empty($equipment)) {
            $equipmentModel->add(array('code' => $imei, 'type' => 'andriod'));
            code('0404'); #设备号不存在
            json();
        }
        return $equipment;
    }

    /**
     * 记录输出
     *
     * @return void
     * @author fangshixiang
     * */
    private function _setOutput($data) {

        json($data);
    }

    /**
     * base auth
     *
     * @return mixed void | boolean
     * @author fangshixiang
     * */
    private function _authenticate() {
        //初始化用户、密码参数
        $username = null;
        $password = null;

        //base auth 授权的用户
        $authUser = unserialize(PI_AUTH_USER);

        //获取客户端 传递的 base auth用户名、密码
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        } elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'basic') === 0)
                list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        }
        //检测客户端传入的base auth用户
        if (is_null($username) || ($username != $authUser['username'] || $password != $authUser['password'])) {
            code('0402');
            header('WWW-Authenticate: Basic realm="HuiLianJieJing Area"');
            json();
        } else {
            return true;
        }
    }

    /**
     * 检查token
     *
     * @param boolean $add 是否添加、更新
     *
     * @return mixed void | array
     * @author fangshixiang
     * */
    private function _checkToken($add = false) {
        $apiCommon = $this->load->common('apiDevice');
        if ($add) {

            //验证用户名、密码
            $get = $this->getPost();
            if (!$get['account'] || !$get['password']) {
                code('0100'); #参数不正确
                json();
            }
            $userInfo = $apiCommon->getUserInfo($get['account'], $get['password']); #得到用户信息
            if (!$userInfo) {
                code('0101'); #帐号不存在
                json();
            }

            if (!password_verify($get['password'], $userInfo['password'])) {
                code('0102'); #账号密码不匹配
                json();
            }

            if ($userInfo['deleted_at']) {
                code('0103'); #账号已被删除
                json();
            }

            if ($userInfo['status'] != 1) {
                code('0104'); #账号已被停用
                json();
            }

            //验证机构信息
            $info = $this->load->model('organizations')->getOne("id='" . $userInfo['organization_id'] . "'");
            if (!$info) { #机构不存在
                code('0105'); #账号已被停用
                json();
            }

            if ($info['status'] != 'normal') {#机构已停用
                code('0106'); #账号已被停用
                json();
            }

            //角色是否是售票员或管理员
            if ($userInfo['is_super'] != 1) {
                $flag = $this->load->common('permission')->checkPermissions($userInfo['id'], $userInfo['organization_id']);
                if (!$flag) {
                    code('0108'); #账号已被停用
                    json();
                }
            }

            $_SESSION['api_uid'] = $userInfo['id'];

            //验证用户的token，如果没有就生成，否则就更新
            $token = $apiCommon->getToken($userInfo);
            if (!$token) {
                $newToken = $apiCommon->createToken($userInfo);
            } else {
                $newToken = $apiCommon->updateToken($userInfo);
            }
            $userInfo['token'] = $newToken['token'];

            //记录用户登录日志

            return $userInfo;
        }

        //获取token
        $token = $this->getGet('token') ? $this->getGet('token') : $this->getPost('token');

        if (!$token) {
            code('0012');
            json();
        }

        //验证token
        $checked = $apiCommon->checkToken($token);
        if ($checked == 0) {
            code('0011');
            json();
        } elseif ($checked == 2) {
            code('0013');
            json();
        }
    }

    /**
     * code
     *
     * @param  string $value  code短语
     * @param  bool   $exit  是否中止输出
     * @return string
     */
    function code($value = '200', $exit = FALSE) {
        // 获取数据
        $codes = unserialize(PI_APP_CODES);
        $status = unserialize(PI_APP_CODES_STATUS);

        // 数据检测
        if (empty($codes[$value]))
            return FALSE;

        // HTTP状态
        $statusCode = $codes[$value]['status'];
        $reasonPhrase = $status[$statusCode];
        header("HTTP/1.1 $statusCode $reasonPhrase");

        // 数据
        PI::set('code', $value);
        PI::set('msg', $codes[$value]['msg']);

        // exit
        if ($exit == TRUE)
            exit;

        // 输出
        unset($codes, $status);
        return $value;
    }

}
