<?php

/**
 * 短信记录控制器
 *
 * @Package controller
 * @Date 2015-3-10
 * @Author Joe
 */
class SmslogController extends Base_Controller_Api {
    /**
     * 日志列表
     * @param  [type] $fields [description]
     * @return [type]         [description]
     */
    public function listsAction(){
        $where = [];

        // 开始时间
        $sent_start = $this->body['sent_start'];
        $sent_start && $where['sent_at|>='] = strtotime($sent_start.' 00:00:00');
        // 结束时间
        $sent_end = $this->body['sent_end'];
        $sent_end && $where['sent_at|<='] = strtotime($sent_end.' 23:59:59');
        // 成功状态
        if (isset($this->body['state']) && $this->body['state'] > 0) {
            $where['status'] = $this->body['state'];
        }
        // 手机号码
        isset($this->body['mobile']) && $where['mobile'] = $this->body['mobile'];
        // 订单号
        isset($this->body['order_id']) && $where['order_id'] = $this->body['order_id'];
        
        $SmsLog = SmsLogModel::model();

        // 分页
        $data = [];
        $countRes = reset($SmsLog->search($where, "count(*) as count"));
        $this->count = $countRes['count'];
        $this->pagenation();
		$list = [];
        if($this->count > 0) {
            $list = $SmsLog->search($where, '*', 'sent_at desc', $this->limit);
        }

		$smsBalance = Sms::getBalance();
        $result = [
            'data'         => $list,
            'sms_remainder'=> $smsBalance, //  短信可用条数
            'sms_balance'  => round($smsBalance * 0.06), //短信可用余额
            'order_nums'   => $countRes['order_nums'],
            'total_amount' => $countRes['total_amount'],
            'pagination'   => [
                'count'    => $this->count,
                'current'  => $this->current,
                'items'    => $this->items,
                'total'    => $this->total
            ]
        ];
        Lang_Msg::output($result);
    }
}