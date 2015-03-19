<?php

class DeviceController extends Base_Controller_Device
{
    /**
     * 新版本APK检查
     * @return [type] [description]
     */
    public function checkapkAction() {
        $version = $this->body['version'];
        $items = ConfigModel::model()->getConfig(array('device_version', 'device_force','device_url'));
        $data = array();
        $data['type'] = $version != $items['device_version'] ? 1 : 0;
        $data['force'] = $items['device_force'];
        $data['version'] = $items['device_version'];
        $data['url'] = $items['device_url'];

        Lang_Msg::device($data);
    }
    
    /**
     * 新版本南平APK检查
     * @return [type] [description]
     */
    public function checkNPapkAction() {
        $version = $this->body['version'];
        $items = ConfigModel::model()->getConfig(array('np_device_version', 'np_device_force','np_device_url'));
        $data = array();
        $data['type'] = $version != $items['np_device_version'] ? 1 : 0;
        $data['force'] = $items['np_device_force'];
        $data['version'] = $items['np_device_version'];
        $data['url'] = $items['np_device_url'];

        Lang_Msg::device($data);
    }

    private function outputList($ids, $poi_id) {
        $list = OrderModel::model()->getOrderList($ids, $poi_id);
        if (!$list) Lang_Msg::error('没有可使用的门票');
        if(count($ids)==1) {
            $this->outputTicket(reset($list));
        }
        $data = array();
        $data['tickets'] = array();
        foreach($list as $item) {
            $tmp = array();
            $tmp['ticket_name'] = $item['name'];
            $tmp['order_id'] = $item['order_id'];
            $tmp['owner_name'] = $item['owner_name'];
            $tmp['owner_mobile'] = $item['owner_mobile'];
            $tmp['owner_card'] = $item['owner_card'];
            $tmp['num'] = $item['nums'];
            $data['tickets'][] = $tmp;
        }
        $data['orderType'] = strlen($this->body['content']) == 11 ? 1 : 2;
        $data['content'] = $this->body['content'];
        $data['listType'] = 3;
        Lang_Msg::device($data);
    }

    private function outputTicket(&$item) {
        $data = array();
        $data['number'] = $item['nums'];
        $data['type'] = $item['name'];
        $data['source'] = 1;
        $data['listType'] = 1;
        Lang_Msg::device($data);
    }

    public function useTicket($order_id, $ticket_id, $num, $poi_id) {
        //返回验证结果
        $order = OrderModel::model()->getById($order_id);
        if (!$order) Lang_Msg::error('没有可使用的门票');
        $order_item = reset(OrderItemModel::model()->setTable($order_id)->search(array('order_id'=>$order_id)));
        if (!$order_item) Lang_Msg::error('没有可使用的门票');
        $unuseNum = OrderModel::model()->checkUnuseNum($order_item, $poi_id, $ticket_id);
        if ($num<=0 || $num>$unuseNum) Lang_Msg::error('可用门票数量不足');
        
        $data = array();
        $data['orderCode'] = $order_id;
        $data['content'] = $order_id;
        $data['listType'] = 2;
        $data['validation_time'] = date('Y-m-d H:i:s');
        $data['ticket'] = array(
            'number' => $order_item['nums'],
            'source' => 1,
            'type' => $order_item['name'],
            'price' => $order_item['price']
            );
        Lang_Msg::device($data);
    }

    public function qrcodeAction() {
        $imei = $this->body['imei'];
        $content = $this->body['content'];
        $num = intval($this->body['num']);
        if (!$imei) Lang_Msg::error('设备号不能为空');
        if (!$content) Lang_Msg::error('缺少二维码、身份证号或手机号');

        $equipment = EquipmentModel::model()->getDevice($imei);
        if (!$equipment) Lang_Msg::error('设备不存在');
        if (!$equipment['landscape_id']) Lang_Msg::error('设备未绑定');
        $poi_id = $equipment['poi_id'];
        $ticket_id = 0;

        //南平一元活动
        if(strcasecmp($content,'Np.jinglvtong.com/a1')){
           $content = '166129187502183'; 
        }
            
        $len = strlen($content); //号码的长度
        if ($len==6 || $len==18) {
            $order_ids = OrderModel::model()->getOrderByCard($content);
            if (!$order_ids) Lang_Msg::error('该身份证没有可使用的门票');
            $this->outputList($order_ids, $poi_id);
        } else if($len==11) {
            $order_ids = OrderModel::model()->getOrderByPhone($content);
            if (!$order_ids) Lang_Msg::error('该手机号没有可使用的门票');
            $this->outputList($order_ids, $poi_id);
        } else {
            $type = substr($content, 0, 1);
            if($type == 1){
                $order_id = $content;
                if ($num<=0) {
                    $this->outputList(array($order_id), $poi_id);
                }
            } else{
                $ticket_id = $content;
                $ticket_info = TicketModel::model()->getById($ticket_id);
                $order_id = $ticket_info['order_id'];
                $num=1;
            }
            $this->useTicket($order_id, $ticket_id, $num, $poi_id);
        }
    }

}
