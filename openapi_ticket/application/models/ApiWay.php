<?php

/**
 * 淘在路上的model
 *
 * @author wfdx1_000
 */
class ApiWayModel extends Base_Model_Api {

    const CONSUME_UPDATE = 1;
    const CONSUME_INIT = 2;
    const CONSUME_CANCEL = 3;

    /**
     * 
     * @param int $from
     * @param int $to
     */
    public static function getOrders($from, $to, $account) {
        $config = Yaf_Registry::get('config');
        $config = $config['way'];

        $params = array(
            'orderDateFrom' => date('Y-m-d H:i:s', $from),
            'appId' => $account['appid'],
            'orderDateTo' => date('Y-m-d H:i:s', $to ? $to : time())
        );
        $params['sign'] = self::getSign2(array(
                    $params['appId'],
                    $params['orderDateFrom'],
                    $params['orderDateTo']
                        ), $account['key']);

        $url = $config['order_url'] . '/GetAppOrderList/?' . http_build_query($params);

        try {
            $json = self::callApi($url, null);
            $list = $json['OrderList'];
        } catch (Exception $ex) {
            $list = array();
        }
        return $list;
    }

    /**
     * 
     * @param array $params
     * @param int $consumeType
     * @throws Exception
     */
    public static function consume($params, $consumeType, $account) {
        $sourceId = $params['order_id'];
        $owenOrderId = $voucherId = $params['verify_code'];
        $ret = ApiOrderModel::model()->detail(array('id' => $owenOrderId, 'show_order_items' => 1));
        if ($ret && $ret['code'] == 'succ') {
            $order = $ret['body'];

            $config = Yaf_Registry::get('config');
            $config = $config['way'];

            $url = $config['order_url'] . '/UpdateAppOrder';
            $post = array(
                'appId' => $account['appid'],
                'appOrderData' => array(
                    array(
                        'OrderId' => $sourceId,
                        'AppOrderId' => $owenOrderId,
                        'AppVoucher' => array()
                    )
                )
            );
            switch ($consumeType) {
                case self::CONSUME_UPDATE:
                    $orderStatus = 9;
                    break;
                case self::CONSUME_INIT:
                    $orderStatus = 6;
            }
            $post['appOrderData'][0]['AppOrderStatus'] = $orderStatus;

            if (isset($params['order_items']) && $params['order_items']) {
                $order_items = json_decode($params['order_items'], true);
            } else {
                $order_items = $order['order_items'];
            }

            foreach ($order_items as $row) {
                if ($orderStatus == self::CONSUME_UPDATE && $row['status'] != 2) {
                    continue;
                } elseif ($orderStatus == self::CONSUME_INIT && $row['status'] != 1) {
                    continue;
                } elseif ($orderStatus == self::CONSUME_CANCEL && $row['status'] != 0) {
                    continue;
                }

//                $row['status'] = 2;
//                $row['use_time'] = time();

                switch ($row['status']) {
                    case 2:
                        $status = 2;
//                        $useDay = $row['use_time'];
                        // 使用之前预约的时间
                        $useDay = 0;
                        break;
                    case 1:
                        $status = 1;
                        $useDay = strtotime($row['use_day'] . ' 23:58:50');
                        break;
                    case 0:
                        $useDay = $status = 0;
                }

                $arr = array(
                    'AppVoucherNo' => $row['id'],
                    'AppVoucherStatus' => $status,
                );
                if ($useDay > 0) {
                    $arr['AppVoucherUseTime'] = date('Y-m-d H:i:s', $useDay);
                }

                $post['appOrderData'][0]['AppVoucher'][] = $arr;
            }
            $post['appOrderData'] = json_encode($post['appOrderData']);
            $post['sign'] = self::getSign2(array(
                        $post['appId'],
                        $sourceId
                            ), $account['key']);
            
            return self::callApi($url, json_encode($post));
        } else {
            throw new Exception($ret['message']);
        }
    }

    /**
     * 
     * @param int $productId 淘在路上的产品ID
     * @param array $reserve 库存数组
     */
    public static function updateProductInventory($productId, $reserve, $account) {
        $config = Yaf_Registry::get('config');
        $config = $config['way'];

        $url = $config['product_url'] . '/UpdateProductInventory';

        $params = array(
            'appId' => $account['appid'],
            'sign' => self::getSign2(array(
                $account['appid'],
                $productId
                    ), $account['key']),
        );

        $productInventoryData = array(
            'ProductId' => $productId,
            'DateList' => array(
            )
        );
        foreach ($reserve as $date => $val) {
            $productInventoryData['DateList'][] = array(
                'InventoryDate' => $date,
                'InventoryMode' => $val['remain_reserve'] == 9999 ? 2 : 4,
                'Total' => $val['remain_reserve'],
                'InventoryType' => 1
            );
        }
        $params['productInventoryData'] = json_encode($productInventoryData);
        
//        Util_Logger::getLogger('way')->debug(__METHOD__, $params, '', '库存同步', $productId);

        return self::callApi($url, json_encode($params));
    }

    public static function updateProductPrice($productId, $reserve, $account) {
        $config = Yaf_Registry::get('config');
        $config = $config['way'];

        $url = $config['product_url'] . '/UpdateProductPrice';

        $params = array(
            'appId' => $account['appid'],
            'sign' => self::getSign2(array(
                $account['appid'],
                $productId
                    ), $account['key']),
        );

        $productPriceData = array(
            'ProductId' => $productId,
            'DateList' => array()
        );
        
        foreach ($reserve as $date => $val) {
            $productPriceData['DateList'][] = array(
                'PriceDate' => $date,
                'PriceModeList' => array(array(
                    'PriceModeId' => 4,
                    'OriginalPrice' => $val['price'],
                    'SalesPrice' => $val['price'],
                    'State' => 1,
                ))
            );
        }
        
        $params['productPriceData'] = json_encode($productPriceData);
//        Util_Logger::getLogger('way')->debug(__METHOD__, $params, '', '价格同步', $productId);

        return self::callApi($url, json_encode($params));
    }

    public static function getProducts($account) {
        $config = Yaf_Registry::get('config');
        $config = $config['way'];

        $params = array(
            'appId' => $account['appid'],
            'sign' => self::getSign2(array($account['appid']), $account['key'])
        );

        $url = $config['product_url'] . '/GetProductList/?' . http_build_query($params);

        try {
            $json = self::callApi($url);
            return $json['ProductList'];
        } catch (Exception $ex) {
            return array();
        }
    }

    /**
     * 调用api，出错的时候抛出异常
     * 
     * @param string $url
     * @param array $postData
     * @return array
     * @throws Exception
     */
    private static function callApi($url, $postData = null, $headers = null) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ));
        if ($postData) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($postData) ? $postData : http_build_query($postData));
        }

        $json = json_decode(curl_exec($ch), true);
        $errmsg = curl_error($ch);
        $code = curl_errno($ch);
        curl_close($ch);

        if (!$json) {
            throw new Exception($errmsg, $code);
        } elseif ($json['Code'] != 1) {
            throw new Exception(json_encode($json));
        }

        return $json;
    }

    private static function getSign2($params, $key) {
        $str = array_reduce($params, function($item, $str) {
            return $item . $str;
        });
        return md5($str . $key);
    }

}
