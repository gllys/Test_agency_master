<?php

require dirname(__FILE__) . '/Base.php';

/**
 * 淘在路上
 *
 * @author wfdx1_000
 */
class Crontab_WayStock extends Process_Base {

    public function run() {
        $config = Yaf_Registry::get('config');
        
        foreach ($config['way']['account'] as $account) {
            $this->syncStock($account);
        }
    }
    
    private function syncStock($account) {
        $config = Yaf_Registry::get('config');
        $config = $config['way'];
        $products = ApiWayModel::getProducts($account);
        
        $time = time();
        $from = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+59 day', $time));
        
        foreach ($products as $wayProd) {
            $productCode = $wayProd['GroupProductId'];
            
            // 测试
//            $productCode = '42d9fa548e8da76aec8538432b4feda2';
            
            $product = ApiProductModel::model()->getProductByCode(array(
                'code' => $productCode
            ));
            
            if ($product['code'] != 'succ' || empty($product['body'])) {
                Util_Logger::getLogger('way')->error(__METHOD__, $wayProd, '找不到该产品', '库存同步', $wayProd['ProductId']);
                continue;
            }
            
            $product = ApiProductModel::model()->detail(array(
                'ticket_id' => $product['body']['product_id'],
                'range' => "$from,$end",
                'distributor_id' => $config['distributor_id'],
                'type' => 0
            ));
            $product = $product['body'];
            
            if (isset($product['reserve'])) {
                //更新价格
                if ($account['appid'] == '1244') {
                    try {
                        ApiWayModel::updateProductPrice($wayProd['ProductId'], $product['reserve'], $account);
                    } catch (Exception $ex) {
                        Util_Logger::getLogger('way')->error(__METHOD__, $wayProd, json_decode($ex->getMessage()), '价格同步', $wayProd['ProductId']);
                    }
                }
                
                //更新库存
                try {
                    ApiWayModel::updateProductInventory($wayProd['ProductId'], $product['reserve'], $account);
                } catch (Exception $ex) {
                    Util_Logger::getLogger('way')->error(__METHOD__, $wayProd, $ex->getMessage(), '库存同步', $productCode);
                }
            }
        }
    }

}

$a = new Crontab_WayStock();
