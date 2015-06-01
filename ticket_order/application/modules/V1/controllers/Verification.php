<?php
class VerificationController extends  Base_Controller_Api
{
    /*
     * 查询要核销的订单
     * @author chentao
     * 2014-11-06 zqf 修改
     * */
    public  function listsAction()
    {
        //查询方式 1=》身份证  2=》手机号 3=》订单号
        $type = intval($this->body[ 'type' ]);
        !in_array($type, array(1,2,3)) && $type = 3;

        $code = trim(Tools::safeOutput($this->body[ 'id' ]));
        !$code && Lang_Msg::error('ERROR_VERIFY_2'); //请输入要检票的手机号、身份证号或订单号
        //景点
        $poi_id = intval($this->body[ 'view_point' ]);
        $landscape_id = intval($this->body['landscape_id']);
        $supplier_id =  intval($this->body['supplier_id']);
        $equipment_code = trim(Tools::safeOutput($this->body[ 'equipment_code' ]));
        if($equipment_code) {
            $equipmentInfo = EquipmentModel::model()->getDevice($equipment_code);
            if($equipmentInfo){
                $landscape_id = $equipmentInfo['landscape_id'];
                $poi_id = $equipmentInfo['poi_id'];
            }
        }
        $OrderModel = OrderModel::model();

        switch($type) {
            case 1:
                $items = $OrderModel->getOrderByCard($code);
                $codes = $items ? array_keys($items) : array();
                break;
            case 2:
                $items = $OrderModel->getOrderByPhone($code);
                $codes = $items ? array_keys($items) : array();
                break;
            default:
                !preg_match("/^\w+$/",$code) && Lang_Msg::error('您输入的订单号有误或者非本系统订单号');
                $codes[] = $code;
        }
        if (!$codes) {
            Lang_Msg::error('该订单没有可以使用的门票');
        }

        try{
            $order = $OrderModel->search(array('OR'=>array('id|in'=>$codes,'code|in'=>$codes)));
            if(empty($order)){
                Lang_Msg::error('您输入的订单号有误或者非本系统订单号');
            }
            $data = $OrderModel->getOrderList($codes, $poi_id, $landscape_id,$supplier_id);
            if (!$data) {
                Lang_Msg::error('该订单没有可以使用的门票');
            }
            Lang_Msg::output( $data );
        } catch (Lang_Exception $e) {
            Lang_Msg::error($e->getMessage());
        } catch (Exception $e) {
            Lang_Msg::error('ERROR_GLOBAL_3');
        }
    }

    /**
     * 旧电子售票系统核销新分销平台门票
     * @return [type] [description]
     */
    public function updatev1Action() {
        $order_id = $this->body['order_id'];
        $num = intval($this->body['num']);
        if (!$order_id || $num<=0) Lang_Msg::error('ERROR_GLOBAL_1');

        $this->setParam('data',json_encode(array($order_id=>$num)));
        $this->setParam('channel',1);
        $this->setParam('view_point',0);
        $this->setParam('or_id',$this->body['organization_id']);
        $this->body = $this->getParams();

        $this->updateAction();
    }

    //使用门票
    public function updateAction()
    {
        $data =  json_decode($this->body[ 'data' ], true);
        if (!$data) Lang_Msg::error('参数错误');
        $poi_id = intval($this->body[ 'view_point' ]);
        $landscape_id = intval($this->body['landscape_id']);

        $record[ 'uid' ] = intval($this->body[ 'uid' ]) ;
        $record[ 'created_at' ] = time();
        $record[ 'equipment_code' ] = trim(Tools::safeOutput($this->body[ 'equipment_code' ]));
        $record[ 'user_name' ] = $this->body['user_name']?$this->body['user_name']:$this->body['user_account'];
        $record[ 'channel' ] = intval($this->body['channel']);
        if($record[ 'equipment_code' ]) {
            $equipmentInfo = EquipmentModel::model()->getDevice($record[ 'equipment_code' ]);
            if($equipmentInfo){
                $landscape_id = $equipmentInfo['landscape_id'] ? $equipmentInfo['landscape_id'] : $landscape_id;
                $poi_id = $equipmentInfo['poi_id'] ? $equipmentInfo['poi_id'] : $poi_id;
            }
        }
        if (!$landscape_id) Lang_Msg::error('景区信息有误');

        $now = time();
        $OrderModel = OrderModel::model();
        try {
            $OrderModel->begin();
            foreach( $data as $order_id => $nums ) {
                $orderInfo = $OrderModel->getById($order_id);
                if (!$orderInfo) {
                    throw new Lang_Exception('您输入的订单号有误或者非本系统订单号!');
                }
                if($orderInfo['checked_open'] == 0) {
                    throw new Lang_Exception('订单不支持核销');
                }
                if($orderInfo['nums'] <= $orderInfo['refunding_nums']+$orderInfo['refunded_nums']) {
                    throw new Lang_Exception('该订单已退款');
                }
                if($orderInfo['status'] == 'cancel') {
                    throw new Lang_Exception('该订单已取消');
                }

                // 检票记录
                $record[ 'supplier_id' ]  = $orderInfo['supplier_id'];
                $record[ 'distributor_id' ]  = $orderInfo['distributor_id'];
                $record[ 'status' ] = 1;
                $record[ 'record_code' ] = $order_id;
                $record[ 'code' ] = $order_id;
                $record[ 'num' ] = $nums;
                $record[ 'landscape_id' ]= $landscape_id;
                $record[ 'poi_id' ]  = $poi_id;
                $record[ 'ticket_type_name' ] = $orderInfo['name'];
                $record[ 'tickets_code' ] = $orderInfo['product_id'];
                $record[ 'local_source' ] = $orderInfo['local_source'];
                $record[ 'source' ] = $orderInfo['source'];
                TicketRecordModel::model()->insert( $record );
                $record['id'] = TicketRecordModel::model()->getInsertId();

                $use_num = $OrderModel->useTicket($orderInfo, $order_id, $landscape_id, $poi_id, $nums);

                // $orderInfo2 = $OrderModel->get(array('id'=>$order_id));
                // $use_num = $orderInfo2['used_nums']-$orderInfo['used_nums']; //核销产品份数

                // 此处硬编码判断订单是否来自淘宝, 需与淘宝订单状态保持一致.
                if ($use_num>0 && $orderInfo['local_source'] == 1) { //实际针对所有ota
                    if(isset($equipmentInfo))
                    {
                        $device = array('id' => $equipmentInfo['id']);
                    }
                    else
                    {
                        $device = array('id' => 'common');
                    }

                    if(!TaobaoOrderModel::model()->verificate($orderInfo, $use_num, $record, $device,$landscape_id)) {
                        $OrderModel->rollback();
                        throw new Lang_Exception('无法核销');
                    }
                }
            }
            $OrderModel->commit();
        } catch (Lang_Exception $e) {
            $OrderModel->rollback();
            Lang_Msg::error($e->getMessage());
        } catch ( Exception  $e) {
            $OrderModel->rollback();
            Lang_Msg::error('操作失败');
        }
		
		PvModel::model()->updateStatics($landscape_id, array_sum($data));

        $data = array();
        $data['result'] = 1;
        $data['poi_id'] = $poi_id;
        $data['landscape_id'] = $landscape_id;
        Lang_Msg::output($data);
    }

    //显示列表
    public function recordAction()
    {
        $where='';
        if( isset( $this->body[ 'begin_date' ]  )) {
            $start_time = strtotime($this->body[ 'begin_date' ] . ' 00:00:01');
            $where['created_at|>='] = $start_time;
        }

        if( isset( $this->body[ 'end_date' ]  )) {
            $end_time = strtotime($this->body[ 'end_date' ] . ' 23:59:59');
            $where['created_at|<='] = $end_time;
        }
        if( !empty( $this->body[ 'landscape_id' ]  ))$where[ 'landscape_id' ]= $this->body[ 'landscape_id' ];
        if( !empty( $this->body[ 'view_point' ]  ))$where[ 'poi_id' ]= $this->body[ 'view_point' ];
        if( !empty( $this->body[ 'order_id' ]  ))$where[ 'record_code' ]= $this->body[ 'order_id' ];
        if( isset( $this->body[ 'channel' ]  ))$where[ 'channel' ]= intval($this->body[ 'channel' ]);
        if( !empty( $this->body[ 'supplier_id' ] ))$where[ 'supplier_id' ]= $this->body[ 'supplier_id' ];
        if( !empty( $this->body[ 'distributor_id' ] )) {
            $where[ 'distributor_id' ]= $this->body[ 'distributor_id' ];
            $where['cancel_status'] = 0; //分销商不统计撤销的核销记录
        }

        if(isset($this->body['cancel_status'])) { //是否按撤销状态查询
            $cancel_status = intval($this->body['cancel_status']);
            if($cancel_status>=0 && $cancel_status<2) {
                $where['cancel_status'] = $cancel_status>0?1:0;
            }
        }

        $scenic_name = trim(Tools::safeOutput($this->body['scenic_name'])); //按景区名称查询
        if(!empty($scenic_name)) {
            $scenicIds = LandscapeModel::model()->getIdsByName($scenic_name);
            if($scenicIds!==false){
                $where['landscape_id|in'] = $scenicIds;
            } else {
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'order_nums'=>0,'total_nums'=>0));
            }
        }

        $supply_name = trim(Tools::safeOutput($this->body['supply_name'])); //按供应商名称查询
        if(!empty($supply_name)) {
            $supplyIds = OrganizationModel::model()->getIdsByName($supply_name,'supply');
            if($supplyIds!==false){
                $where['supplier_id|in'] = $supplyIds;
            }
            else{
                Lang_Msg::output(array('data'=>array(),'pagination'=>array('count'=>0),'order_nums'=>0,'total_nums'=>0));
            }
        }
        
        $this->count = TicketRecordModel::model()->countResult($where);
        $this->pagenation();
        $tmp= $this->count > 0 ? TicketRecordModel::model()->search( $where, '*', $this->getSortRule(), $this->limit ):array();
        $sort = array();
        foreach( $tmp as $key => $value )
        {
            $sort[ $key ] =  $value  ;
        }

        $statWhere = $where;
        $statWhere['cancel_status'] = 0; //不统计撤销的核销记录
        $countRes = reset(TicketRecordModel::model()->search(
            $statWhere, "count(distinct record_code) as order_nums,sum(num) as total_nums"
        ));

        $data[ 'data' ] = $sort;
        $data[ 'order_nums' ] = intval($countRes['order_nums']);
        $data[ 'total_nums' ] = intval($countRes['total_nums']);
        $data['pagination'] = array(
            'count'	=>$this->count,
            'current'=>$this->current,
            'items' => $this->items,
            'total' => $this->total,
        );
        Tools::lsJson(true,'ok',$data);
    }

    //核销记录详情
    public function recordDetailAction(){
        $id = intval($this->body['id']);
        $record = TicketRecordModel::model()->getById($id);
        if (!$record) {
            Lang_Msg::error('不存在该条记录');
        }
        Tools::lsJson(true,'ok',$record);
    }

     //撤销
    public function cancelAction() {
        $id = intval($this->body['id']);
        $supplier_id = intval($this->body['supplier_id']);
        $landscape_id = intval($this->body['landscape_id']);
        $is_force = intval($this->body['is_force']);
        $cancel_source = intval($this->body['cancel_source'])?intval($this->body['cancel_source']):0;
        $cancel_uid = intval($this->body['cancel_uid']);
        $cancel_account = trim($this->body['cancel_account'])?trim($this->body['cancel_account']):"";
        $cancel_name = $this->body['cancel_name']?trim($this->body['cancel_name']):$cancel_account;
        $now = time();

        //查找该条记录
        $record = TicketRecordModel::model()->getById($id);
        if (!$record) {
            Lang_Msg::error('不存在该条记录');
        }

        /*if ($record['supplier_id']!=$supplier_id && $record['landscape_id']!=$landscape_id) { //供应商或景区有权撤销
            Lang_Msg::error('非该供应商记录，不能撤销');
        }*/

        if ($record['cancel_status'] == 1) {
            Lang_Msg::error('已撤销');
        }

        if ($is_force<=0 && ($now - $record['created_at']) > 300) {
            Lang_Msg::error('撤销时间已过期');
        }

        $landscape_id = $record['landscape_id'];
        $poi_id = $record['poi_id'];
        $OrderModel = OrderModel::model();
        try {
            $OrderModel->begin();

            $orderInfo = $OrderModel->get(array('id'=>$record['code']));
            if (!$orderInfo) {
                throw new Lang_Exception('订单不存在');
            } else if($orderInfo['status']=='billed' || ($orderInfo['billed_nums']>0 && $orderInfo['billed_nums']>=$orderInfo['nums']-$orderInfo['refunding_nums']-$orderInfo['refunded_nums'])) {
                throw new Lang_Exception('该订单已结算，不能撤销验票记录');
            } else if($orderInfo['source']>0 && in_array($orderInfo['source'], OtaAccountModel::model()->sourceOfDisCancel)) {
                throw new Lang_Exception('该订单不允许撤销验票记录');
            }

            $OrderModel->cancelTicket($record['code'], $landscape_id, $poi_id, $record['num']);
            TicketRecordModel::model()->updateByAttr(
                array(
                    'cancel_status' =>1 ,
                    'updated_at' => $now,
                    'cancel_source'=>$cancel_source,
                    'cancel_uid' => $cancel_uid,
                    'cancel_account' => $cancel_account,
                    'cancel_name' => $cancel_name,
                ),
                array('id' => $id)
            );

            $orderInfo2 = $OrderModel->get(array('id'=>$record['code']),"used_nums");
            $use_num = $orderInfo['used_nums'] - $orderInfo2['used_nums']; //核销产品份数

            // 撤销核销之前先判断订单是否来自淘宝
            if ($use_num > 0 && $orderInfo['local_source'] == 1) { //实际针对所有ota
                if (array_key_exists('equipment_code', $record) && !empty($record['equipment_code']))
                    $device = array('id' => $record['equipment_code']);
                else
                    $device = array('id' => 'common');

                if(!TaobaoOrderModel::model()->cancel($orderInfo, $record, $device )) {
                    $OrderModel->rollback();
                    throw new Lang_Exception('撤销失败');
                }
            }

            $OrderModel->commit();
        } catch (Lang_Exception $e) {
            $OrderModel->rollback();
            Lang_Msg::error($e->getMessage());
        } catch (Exception $e) {
            $OrderModel->rollback();
            Lang_Msg::error('撤销失败');
        }
        Tools::lsJson(true,'ok',array());
    }

    /*
     * 大漠（合作伙伴）核销接口
     * */
    public function partnerUseAction() {
        $OrderModel = new OrderModel();
        try {
            $partner_order_id = trim(Tools::safeOutput($this->body['partner_order_id']));
            $partner_product_code = trim(Tools::safeOutput($this->body['partner_product_code']));
            $nums = intval($this->body['nums']);

            if (empty($partner_order_id)) {
                Lang_Msg::error('外部订单号不能为空');
            } else if(!preg_match("/^\w+$/", $partner_order_id)) {
                Lang_Msg::error('您输入的订单号有误或者非本系统订单号');
            } else if (empty($partner_product_code)) {
                Lang_Msg::error('外部票种编号不能为空');
            } else if ($nums<1) {
                Lang_Msg::error('请指定取票张数');
            }

            $orderInfo = $OrderModel->get(array('partner_order_id'=>$partner_order_id));
            if(empty($orderInfo)) {
                Lang_Msg::error('该订单不存在');
            }
            if(!in_array($partner_product_code,explode(',',$orderInfo['partner_product_code']))) {
                Lang_Msg::error('票种编号有误');
            }
            if($orderInfo['nums'] <= $orderInfo['refunding_nums']+$orderInfo['refunded_nums']) {
                Lang_Msg::error('该订单已退款');
            }
            if($orderInfo['status'] == 'cancel') {
                Lang_Msg::error('该订单已取消');
            }
            $OrderModel->begin();
            $landscape_id = intval($orderInfo['landscape_ids']);
            // 检票记录
            $record[ 'supplier_id' ]  = $orderInfo['supplier_id'];
            $record[ 'distributor_id' ]  = $orderInfo['distributor_id'];
            $record[ 'status' ] = 1;
            $record[ 'record_code' ] = $orderInfo['id'];
            $record[ 'code' ] = $orderInfo['id'];
            $record[ 'num' ] = $nums;
            $record[ 'landscape_id' ]= $orderInfo['landscape_ids'];
            $record[ 'poi_id' ]  = '';
            $record[ 'ticket_type_name' ] = $orderInfo['name'];
            $record[ 'tickets_code' ] = $orderInfo['product_id'];
            $record[ 'local_source' ] = $orderInfo['local_source'];
            $record[ 'source' ] = $orderInfo['source'];
            TicketRecordModel::model()->insert( $record );
            $record['id'] = TicketRecordModel::model()->getInsertId();

            $OrderModel->useTicket($orderInfo['id'], $landscape_id, 0, $nums);

            PvModel::model()->updateStatics($landscape_id, $nums);
            $OrderModel->commit();
        } catch (Lang_Exception $e) {
            $OrderModel->rollback();
            Lang_Msg::error($e->getMessage());
        } catch (Exception $e) {
            $OrderModel->rollback();
            Lang_Msg::error('核销失败');
        }

        $data = array();
        $data['result'] = 1;
        $data['poi_id'] = 0;
        $data['landscape_id'] = $landscape_id;
        Lang_Msg::output($data);
    }
}
