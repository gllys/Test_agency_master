<?php

require dirname(__FILE__) . '/Base.php';

/**
 * 淘在路上
 *
 * @author wfdx1_000
 */
class Crontab_Way extends Process_Base {

    public function run() {
        $config = Yaf_Registry::get('config');

        foreach ($config['way']['account'] as $account) {
            $this->syncOrders($account);
        }
    }
    
    private function syncOrders($account) {
        $config = Yaf_Registry::get('config');
        $config = $config['way'];
        $fromTime = time() - 1800;
        $toTime = 0;
        
        $list = ApiWayModel::getOrders($fromTime, $toTime, $account);

        foreach ($list as $row) {
            // 只处理已成交的订单
            if (!in_array($row['OrderStatus'], array(6))) {
                continue;
            }
            $productCode = $row['GroupProductId'];

            $product = ApiProductModel::model()->getProductByCode(array(
                'code' => $productCode
            ));
            
            Util_Logger::getLogger('way')->info(__METHOD__, $row, var_export($product, true), 'order', $row['OrderId']);
            
            if ($product['code'] != 'succ' || empty($product['body'])) {
                continue;
            }
            $has = ApiOrderModel::model()->detail(array(
                'source_id' => $row['OrderId'],
                'source' => $config['source'],
            ));
            
            // 在成交状态的订单，当在我们这边已经存在的请客，忽略
            if ($has['code'] == 'succ' && !empty($has['body'])) {
                if ($row['OrderStatus'] == 6) {
                    continue;
                } else {
                    //取消订单
                    $order = $has['body'];
                    $params = array(
                        'nums' => $order['nums'],
                        'order_id' => $order['id'],
                        'user_id' => $order['user_id'],
                        'remark' => 'OTA客户：'.$order['user_account'].'[ID:'.$order['user_id'].']'
                    );
                    $r = ApiOrderModel::model()->cancelAndRefund($params);
                    continue;
                }
            }

            $product = $product['body'];
            $params = array(
                'ticket_template_id' => $product['product_id'],
                'source' => $config['source'],
                'source_id' => $row['OrderId'],
                'local_source' => 1,
                'distributor_id' => $config['distributor_id'],
                'nums' => $row['TotalCount'],
                'owner_name' => $row['ContactInfo']['ContactName'],
                'owner_mobile' => $row['ContactInfo']['ContactMobile'],
                'ota_type' => $config['name'],
                'ota_account' => $config['source'],
                'ota_name' => $config['name'],
                'user_id' => $config['id'],
                'user_account' => $config['name'],
                'user_name' => $config['name'],
                'remark' => '淘在路上订单',
                'payment' => 'credit',
            );
            if ($row['PassengerList']) {
                $visitors = array();
                foreach ($row['PassengerList'] as $visitor) {
                    $visitors[] = array(
                        'visitor_name' => $visitor['Name']
                    );
                }
                $params['visitors'] = json_encode($visitors);
            }

            $useDay = null;
            if ($row['DepartureDate'] && preg_match('/(\d+)+/', $row['DepartureDate'], $matches)) {
                $useDay = date('Y-m-d', ceil($matches[1] / 1000));
            }

            if (!$useDay) {
                $time = time();
                list($start, $end) = explode(',', $product['date_available']);
                $useDay = date('Y-m-d', $start > $time ? $start : $time);
            }

            if ($useDay) {
                $params['use_day'] = $useDay;
            }

            $response = ApiOrderModel::model()->create($params);
            if ($response && $response['code'] == 'succ') {
                $toOta = array(
                    'order_id' => $params['source_id'],
                    'verify_code' => $response['body']['id'],
                    'consume_num' => 1,
                    'used_num' => 0,
                    'num' => $params['nums'],
                    'refunded_nums' => 0,
                    'source' => $config['source']
                );
                try {
                    ApiWayModel::consume($toOta, ApiWayModel::CONSUME_INIT, $account);
                } catch (Exception $ex) {
                    Util_Logger::getLogger('way')->error(__METHOD__, $row, $ex->getMessage(), '订单', $row['OrderId']);
                }
            } else {
                Util_Logger::getLogger('way')->error(__METHOD__, $response, '', '订单', $row['OrderId']);
            }
        }
    }

}

new Crontab_Way();
