<?php

class SmsController extends Controller {

    public function actionIndex() {

        $get = $_GET;
        #发送时间
        if (isset($get['sent_start']) && !empty($get['sent_start'])) {
            if (!isset($get['sent_end']) || empty($get['sent_end'])) {
                $this->_end(1, '请选择结束时间');
                exit;
            }
        }
        if (isset($get['sent_end']) && !empty($get['sent_end'])) {
            if (!isset($get['sent_start']) || empty($get['sent_start'])) {
                $this->_end(1, '请选择结束时间');
                exit;
            }
        }
        if (isset($get['sent_start']) && isset($get['sent_start']) && !empty($get['sent_end']) && !empty($get['sent_start'])) {
            $param = array(
                'sent_start' => $get['sent_start'],
                'sent_end' => $get['sent_end']
            );
        }
        #状态
        if (isset($get['state']) && !empty($get['state'])) {
            $param['state'] = intval($get['state']);
        }
        #手机号码
        if (isset($get['mobile']) && !empty($get['mobile'])) {
            $param['mobile'] = intval($get['mobile']);
        }
        #分页
        $param['items'] = 20;
        $param['current'] = isset($get['page']) ? $get['page'] : 1;

        $result = SmsLog::api()->lists($param);
        if ($result['code'] == 'succ') {
            $smsRs = $result['body'];
            $smsInfo = $result['body']['data'];
            $pages = new CPagination($smsRs['pagination']['count']);
            $pages->pageSize = $param['items'];
        }


        $data = array(
            'get' => isset($get) && !empty($get) ? $get : array(),
            'smsInfo' => isset($smsInfo) && !empty($smsInfo) ? $smsInfo : array(),
            'balance' => isset($smsRs) && !empty($smsRs) ? $smsRs['sms_balance'] : '',
            'remainder' => isset($smsRs) && !empty($smsRs) ? $smsRs['sms_remainder'] : '',
            'pages' => isset($pages) ? $pages : 1
        );
        $this->render('index', $data);
    }

}
