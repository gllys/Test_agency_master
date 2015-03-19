<?php

/**
 * 电子票务系统
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/29/14
 * Time: 2:24 PM
 * File: TicketsController.php
 */

class TicketsController extends BaseController
{
    /**
     * 所有打印模板
     */
    public function templates() {
        $get = $this->getGet();
        $param['current'] = isset($get['p']) ? $get['p'] : 1;
        if (isset($get['name'])) {
            $param['name'] = $get['name'];
        }
        $result = TicketsPrints::api()->lists($param);
        $data['list'] = isset($result['message']) ? $result['message'] : array();

        $this->load->view('tickets/templates', $data);
    }

    /**
     * 新增修改模板
     */
    public function prints() {
        $get = $this->getGet();
        $data = array();
        if (!empty($get['id'])) {
            $result = TicketsPrints::api()->lists(array(
                'id' => intval($get['id'])
            ));
            $data = isset($result['message']) ? $result['message'][0] : array();
        }
        $rs = Landscape::api()->lists(array(), 0);
        $data['landscape_list'] = ApiModel::getLists($rs);
        $this->load->view('tickets/prints', $data);
    }

    /**
     * author: xuejian
     * desc: 根据景区id获取基础票
     */
    public function ticketsprint() {
        $retData = array();
        $get = $this->getGet();
        $scenicId = $get['scenic_id'];
        $params['scenic_id'] = $get['scenic_id'];
        $params['item'] = 1000;
        if(isset($params['scenic_id'])) {
            $rs = Tickettemplatebase::api()->lists($params);
            if(isset($rs['code']) && $rs['code'] == 'succ') {
                $ticketTemplates = array();
                $ticketTemplateLists = ApiModel::getLists($rs);
                if(is_array($ticketTemplateLists[$scenicId]))  {
                    foreach ($ticketTemplateLists[$scenicId] as $v) {
                        $ticketTemplates[$v['id']] = $v['name'];
                    }
                }
                
                $retData['data']['tickettemplates'] = $ticketTemplates;
                $params['pid'] = $get['pid'];
                $rs = TicketsPrints::api()->printtacketlists($params);
                if(isset($rs['code']) && $rs['code'] == 'succ') {
                    $retData['code'] = 0;
                    $ticketPrints = array();
                    $ticketPrintLists = $rs['data'];
                    if(is_array($ticketPrintLists))  {
                        foreach ($ticketPrintLists as $v) {
                            $ticketPrints[$v['id']] = 1;
                        }
                    }
                    $retData['data']['ticketprintrelate'] = $ticketPrints;
                } else {
                    $retData['code'] = -3;
                }
            } else {
                $retData['code'] = -1;
                $retData['msg'] = "Tickettemplatebase api lists failed!";
            }
        } else {
            $retData['code'] = -2;
            $retData['msg'] = "scenic_id is null!";
        }
        echo json_encode($retData);
    }
    
    public function save() {
        $post = $this->getPost();
        $post['landscape_id'] = $post['scenic_id'];
        $post['supplier_id'] = 0;
        $post['path'] = 'path';
//        TicketsPrints::api()->debug = true;
        $ret = TicketsPrints::api()->template($post);
        echo json_encode($ret);
//        redirect('/tickets_templates.html');
    }
} 
