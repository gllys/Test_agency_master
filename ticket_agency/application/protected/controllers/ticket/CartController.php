<?php

class CartController extends Controller
{
	public function actionIndex()
	{
		$data = array();
        $rs = Cart::api()->lists(array('user_id'=>Yii::app()->user->uid));
        if($rs['code'] == 'succ'){
        	$result = $rs['body'];
        }else{
        	$data = array();
        }
        if(isset($result) && !empty($result)){
        	foreach ($result as $value) {
        		$param['ticket_id'] = $value['ticket_id'];
        		$param['distributor_id'] = Yii::app()->user->org_id;
        		$param['price_type'] = $value['price_type'];
        		$param['use_day'] = $value['date'];
        		$rs = TicketTemplate::api()->ticketinfo($param);
        		if($rs['code'] == 'succ'){
        			$tinfo = $rs['body']; 
        			if($value['price_type'] == 1 && isset($tinfo['day_group_price']) && !empty($tinfo['day_group_price']) && isset($tinfo['group_discount']) && !empty($tinfo['group_discount'])){
        				$value['price'] = $value['price'] + $tinfo['day_group_price'] + $tinfo['group_discount'];
        			}elseif($value['price_type'] == 1 && isset($tinfo['day_group_price']) && !empty($tinfo['day_group_price']) && empty($tinfo['group_discount'])){
        				$value['price'] = $value['price'] + $tinfo['day_group_price'];
        			}elseif($value['price_type'] == 1 && empty($tinfo['day_group_price']) && isset($tinfo['group_discount']) && !empty($tinfo['group_discount'])){
        				$value['price'] = $value['price'] + $tinfo['group_discount'];
        			}elseif($value['price_type'] == 0 && isset($tinfo['day_fat_price']) && !empty($tinfo['day_fat_price']) && isset($tinfo['fat_discount']) && !empty($tinfo['fat_discount'])){
        				$value['price'] = $value['price'] + $tinfo['fat_discount'] + $tinfo['day_fat_price'];
        			}elseif($value['price_type'] == 0 && isset($tinfo['day_fat_price']) && !empty($tinfo['day_fat_price']) && empty($tinfo['fat_discount'])){
        				$value['price'] = $value['price'] + $tinfo['day_fat_price'];
        			}else{
        				$value['price'] = $value['price'] + $tinfo['fat_discount'];
        			}
        			
        		}
	        	if($value['type'] == 1){
                    $data['renwu'][] = $value;
                }else{
                    $data['cartList'][] = $value; 
                }
       		}	
        }
                //print_r($data);exit;
		$this->render('index',$data);
	}

    public function actionDelCart(){
        if(Yii::app()->request->isPostRequest){
            $ids = $_POST['ids'];
            if(!empty($ids)){
                $result = Cart::api()->delete(array('ids'=>$ids));
                if(Cart::isSucc($result)){
                    $this->_end(0,$result['message'] );
                }else{
                    $this->_end(1,$result['message'] );
                }
            }else{
                $this->_end(1,"删除失败,未知的信息");
            }
        }else{
            throw new CHttpException('400',"请求的页面不存在!");
        }
    }

    public function actionCreateOrders(){
        if(Yii::app()->request->isPostRequest){
            $data = json_decode($_POST['data']);
            if(is_array($data)){
                $error_msg = "";
                $order_ids = array();
                foreach($data as $cart){
                    $cart = is_object($cart)?get_object_vars($cart):$cart;
                    $result = Cart::api()->detail(array('id'=>$cart['id']));
                    $cartInfo = Cart::api()->getData($result);
                    if($cartInfo['ticket_name']!=$cart['name'] || //如果页面内容有变化。先更新
                        $cartInfo['phone']!=$cart['phone'] || $cartInfo['num']!=$cart['num']){
                        $cart['id'] = $cartInfo['id'];
                        $rs = Cart::api()->update($cart);
                        if(!Cart::isSucc($rs)){
                            //Todo::这里购物车记录更新失败,不在继续生成订单。记录错误，继续下一轮生成
                            //Todo::后期这里先验证购物车能否生成订单
                            $error_msg .= "\n门票:".$cartInfo['ticket_name']."更新信息失败.";
                            continue;
                        }else{
                            //更新成功则修改购物车详情，等待生成订单使用
                            $cartInfo['name'] = $cart['name'];
                            $cartInfo['phone'] = $cart['phone'];
                            $cartInfo['num'] = $cart['num'];
                        }
                    }

                    //生成订单
                    $order = array(
                        'ticket_template_id' => $cartInfo['ticket_id'],
                        'price_type' => $cartInfo['price_type'],
                        'distributor_id' => Yii::app()->user->org_id,
                        'use_day' => date('Y-m-d',strtotime($cartInfo['date'])),
                        'nums' => $cartInfo['num'],
                        'owner_name' => $cartInfo['name'],
                        'owner_mobile' => $cartInfo['phone'],
                        'owner_card' => $cartInfo['card'],
                        'user_id' => $cartInfo['user_id'],
                        'user_name' => Yii::app()->user->account,
                    );
                    $return = Order::api()->add($order);
                    //Todo::这里实现合并支付
                    if($return['code']=='succ'){
                        //生成订单删除购物车
                        Cart::api()->delete(array('ids'=>$cartInfo['id']));
                        //保存所有生成的订单id
                        $order_ids[] = $return['body']['id'];
                    }else{
                        $error_msg .= "\n门票:".$cartInfo['ticket_name']."生成失败,".$return['message'];
                    }
                }

                echo json_encode(array(
                    'error' => $error_msg,
                    'ids' => implode(",",$order_ids)
                ));
            }else{
                echo json_encode(array('error'=>"生成订单失败",'ids'=>array()));
                //$this->_end(1,"生成订单失败!");
            }
        }else{
            throw new CHttpException('400',"请求的页面不存在!");
        }
    }
}
