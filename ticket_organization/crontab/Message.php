<?php
/**
 * Created by PhpStorm.
 * User: yinjian
 * Date: 2014/11/26
 * Time: 18:45
 */
require dirname(__FILE__) . '/Base.php';

class Crontab_Message extends Process_Base
{
    public function run() {
        while (true) {
            // 判断当天的到期提醒有没生成过
            $today_start = strtotime(date('Y-m-d'));
            $today_end = $today_start+3600*24-1;
            $today_remaind = MessageModel::model()->get(array('sys_type'=>3,'created_at|between'=>array($today_start,$today_end)));
            if(!$today_remaind) {
                $supply = array_keys(OrganizationModel::model()->search(array('is_del' => 0, 'type' => 'supply'), 'id'));
                $now = time();
                while ($id = array_shift($supply)) {
                    $ticket = TicketTemplateModel::model()->getRemaindTicket($id);
                    if ($ticket['data']) {
                        foreach ($ticket['data'] as $k => $v) {
                            // 距离天数
                            $Date_1 = date("Y-m-d");
                            $Date_2 = date("Y-m-d", $v['sale_end_time']);
                            $d1 = strtotime($Date_1);
                            $d2 = strtotime($Date_2);
                            $Days = round(($d2 - $d1) / 3600 / 24);
                            // 您的产品：XXXXXX</a>即将于2015-01-27到期，请注意！
                            MessageModel::model()->add(array(
                                'content' => '您好，您的产品<a title="修改" href="/ticket/single/edit/?ticket_id=' . $v['id'] . '" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">' . $v['name'] . '</a>即将于' . $Date_2 . '到期，请注意！',
                                'sys_type' => 3,
                                'receiver_organization' => $v['organization_id'],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ));
                        }
                    }
                }
            }
            $sleep_time = strtotime(date('Y-m-d',strtotime("+1 day")))-time();
            sleep($sleep_time);
        }
    }
}

$test = new Crontab_Message();
