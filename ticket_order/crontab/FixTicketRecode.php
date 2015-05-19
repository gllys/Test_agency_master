<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-04-08
 * Time: 上午11:11
 * 清理线上出现的重复验票记录
 */


require dirname(__FILE__) . '/Base.php';

class Crontab_FixTicketRecode extends Process_Base
{


    public function run()
    {
        //查找重复验票记录，成功核销记录中票数总和大于订单已使用票总和
        $OrderModel = new OrderModel();
        $TicketRecordModel = new TicketRecordModel();
        $TicketRecordModel->begin();
        $fields = "SELECT r.*,o.nums,o.used_nums,o.landscape_ids,o.ticket_infos ";
        $from = " FROM
            (
                SELECT count(id) as vcount,sum(num) as vnum,code
                FROM ".$TicketRecordModel->getTable()."
                WHERE poi_id=0 AND status=1 AND cancel_status=0 GROUP BY code
            ) r,".$OrderModel->getTable()." o ";
        $where = " WHERE o.id=r.code AND vcount>1 AND r.vnum>o.used_nums AND o.ticket_infos!='' ";
        $orderBy = " ORDER BY r.code asc";
        $sql = $fields . $from . $where . $orderBy;
        $data = $TicketRecordModel->getDb()->selectBySql($sql);

        $data2 = $noHxOrderIds = array();
        foreach ($data as $k => $v) {
            if ($v['used_nums'] == 0) {
                $noHxOrderIds[] = $v['code'];
            } else {
                $v['ticket_infos'] = json_decode($v['ticket_infos'], true);
                if($v['ticket_infos']){
                    $max_can_use = 0; //最大可核销的票(人)数
                    foreach ($v['ticket_infos'] as $ticketBase) {
                        $max_can_use += $v['used_nums'] * $ticketBase['num'];
                    }
                    $v['max_can_use'] = $max_can_use;
                    if ($v['vnum'] > $max_can_use) {
                        $data2[$v['code']] = $v;
                    }
                }
            }
        }

        if (!empty($noHxOrderIds)) { //使用张数为0，但有正常核销记录的订单
            echo "使用张数为0，但有多余核销记录的订单有：" . count($noHxOrderIds) . "笔\n";
            print_r($noHxOrderIds);
            echo "\n";
            $r = $TicketRecordModel->delete(array('poi_id' => 0, 'status' => 1, 'cancel_status' => 0, 'code|in' => $noHxOrderIds));
            if (!$r) {
                $TicketRecordModel->rollback();
                exit("删除使用张数为0的订单的核销记录失败！\n\n");
            } else {
                echo "成功删除这些订单的多余核销记录！\n\n";
            }
        }

        if (!empty($data2)) {
            echo "有核销记录异常的订单共有：" . count($data2) . "笔\n";
            $orderIds = array_keys($data2);
            print_r($orderIds);
            echo "\n";

            //查出异常订单的核销记录
            $hxData = $TicketRecordModel->search(array('poi_id' => 0, 'status' => 1, 'cancel_status' => 0, 'code|in' => $orderIds), "*", "code asc,num asc,id asc");
            echo "查出异常订单的核销记录共有：" . count($hxData) . "笔\n";
            $hxData2 = array();
            foreach ($hxData as $k => $v) {
                $hxData2[$v['code']][$k] = $v;
            }


            $normalHxIds = array(); //正常核销记录
            $badHxIds = array(); //错误核销记录
            $noEqOrderIds = array(); //没有軿凑齐的订单
            foreach ($data2 as $code => $v) {
                $hxNums = 0; //实际核销数
                foreach ($hxData2[$code] as $hxK => $hxV) {
                    if ($hxV['num'] > 0) {
                        $hxNums += $hxV['num']; //核销记录的核销张数軿凑订单已用最大可核销张数
                        if ($hxNums <= $v['max_can_use']) {
                            unset($hxData2[$code][$hxK]);
                            $normalHxIds[$code][] = $hxK;
                        }
                    }
                }
                if($hxNums<$v['max_can_use']){ //没有軿凑齐的订单
                    array_push($noEqOrderIds,$code);
                }
            }
            //处理多余核销记录
            foreach ($hxData2 as $code => $v) {
                $badHxIds[$code] = array_keys($v);
            }

            //print_r($normalHxIds);
            //print_r($badHxIds);
            if(!empty($badHxIds)){
                foreach($badHxIds as $code=>$ids) {
                    $delWhere = array('id|in'=>$ids);
                    if(!empty($noEqOrderIds)) $delWhere['code|not in']=$noEqOrderIds;
                    $r = $TicketRecordModel->delete($delWhere);
                    if (!$r) {
                        $TicketRecordModel->rollback();
                        echo "这些多余的核销记录没删除成功:".implode(' , ',$ids)."\n";
                    }
                }
                echo "多余的核销记录已清理完毕！\n";
            }

            if(!empty($noEqOrderIds)){
                echo "有以下订单的核销记录未处理，请人工查看一下核销记录和订单使用票数！\n";
                print_r($noEqOrderIds);
            }
        } else {
            echo "无核销异常的订单\n";
        }

        $TicketRecordModel->commit();
        exit;
    }

}

$test = new Crontab_FixTicketRecode;