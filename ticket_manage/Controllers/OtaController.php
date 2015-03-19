<?php 
/**
 * OTA列表控制器
 * 2014-1-7
 * @package controller
 * @author yinjian
 **/
class OtaController extends BaseController
{
    /**
     * 门票上架列表
     * @author yinjian
     * @date   2014-08-28
     * @return [type]     [description]
     */
	public function ticket()
    {
        $ticket_ota_model = $this->load->model('ticketOta');
        //组织查询条件
        $page = $this->getGet('p') ? intval($this->getGet('p')) : 1;
        $param = array(
            'page' => $page,
            'items' => 10,
            'order' => 'ticket_ota.updated_at DESC',
            'fields' => "ticket_ota.*,attachments.url",
            'join' => array(
                array(
                    'left_join' => 'attachments ON ticket_ota.attachment = attachments.id'
                )
            ),
        );
        $get = $this->getGet();

        //下单时间
        if (!empty($get['created_at']) && isset($get['created_at'])) {
            $timeFilter = explode(' - ', $get['created_at']);
            $timeFilter[1] = date('Y-m-d', strtotime($timeFilter[1]) + 86400);
            $param['filter'][$ordersModel->table . '.created_at|between'] = $timeFilter;
        }
        //发布单位
        if (!empty($get['organization_name']) && isset($get['organization_name'])) {
            $param['filter']['ticket_ota.organization_name'] = urldecode($get['organization_name']);
        }
        //是否上架
        if (!empty($get['status']) && isset($get['status'])) {
            $param['filter']['ticket_ota.status'] = $get['status'];
        }
        //OTA
        if (!empty($get['ota_type']) && isset($get['ota_type'])) {
            $param['filter']['ticket_ota.ota_type'] = $get['ota_type'];
        }
        $ticketOtaList = $ticket_ota_model->commonGetList($param);
        $data['get'] = $get;
        $data['pagination'] = $this->getPagination($ticketOtaList['pagination']);
        $data['ticketOtaList'] = $ticketOtaList['data'];
        $this->load->view('ota/ticket',$data);
    }

    public function bill()
    {

    }	
}