<?php

/**
 * 配置文件 Configs/Menu.php
 * 视图文件 Views/tpl/common/menu.php
 * 调用方法 Commons/ViewCommn/menu()
 * 调用函数 Libs/Function.php get_menu()
 * 2013-09-03
 *
 * @author  liuhe
 * @version 1.0
 */
// 欢迎页
// $menu['welcome']['title']                        = '欢迎页';
// $menu['welcome']['class']                        = 'icon-home';
// $menu['welcome']['url']                          = 'welcome.html';


/*
  // 分销管理
  $menu['shopping']['title']                             = '分销管理';
  $menu['shopping']['class']                             = 'icon-share-alt';
  $menu['shopping']['toggle']                            = '5Q69xRUl6j';
  $menu['shopping']['menu']['index']['title']            = '门票订购';
  $menu['shopping']['menu']['index']['url']              = 'shopping_index.html';
  $menu['shopping']['menu']['index']['class']            = 'icon-shopping-cart';
  $menu['shopping']['menu']['reserve']['title']          = '门票预订';
  $menu['shopping']['menu']['reserve']['url']            = '';
  $menu['shopping']['menu']['reserve']['class']          = 'icon-book';
  $menu['shopping']['menu']['reserve']['hidden']         = TRUE;
  $menu['shopping']['menu']['payment']['title']          = '订单支付';
  $menu['shopping']['menu']['payment']['url']            = '';
  $menu['shopping']['menu']['payment']['class']          = 'icon-money';
  $menu['shopping']['menu']['payment']['hidden']         = TRUE;
  $menu['shopping']['menu']['succ']['title']             = '预订成功';
  $menu['shopping']['menu']['succ']['url']               = '';
  $menu['shopping']['menu']['succ']['class']             = 'icon-legal';
  $menu['shopping']['menu']['succ']['hidden']            = TRUE;
 */


//景区管理
$menu['landscape']['title'] = '景区管理';
$menu['landscape']['class'] = 'icon-flag';
$menu['landscape']['toggle'] = '5Q58yRUkdf';
$menu['landscape']['menu']['lists']['title'] = '景区列表';
$menu['landscape']['menu']['lists']['url'] = 'landscape_lists.html';
$menu['landscape']['menu']['lists']['permission_id'] = 'landscape_lists';
$menu['landscape']['menu']['lists']['permission_name'] = '景区审核';
$menu['landscape']['menu']['lists']['class'] = 'icon-reorder';
//电子票务
$menu['tickets']['title'] = '电子票务';
$menu['tickets']['class'] = 'icon-globe';
$menu['tickets']['toggle'] = '5Q58yRUsdfdf';
$menu['tickets']['menu']['lists']['title'] = '打印模板';
$menu['tickets']['menu']['lists']['url'] = 'tickets_templates.html';
$menu['tickets']['menu']['lists']['permission_id'] = 'tickets_templates';
$menu['tickets']['menu']['lists']['permission_name'] = '打印模板';
$menu['tickets']['menu']['lists']['class'] = 'icon-bar-chart';
//机构管理
$menu['organization']['title'] = '机构管理';
$menu['organization']['class'] = 'icon-road';
$menu['organization']['toggle'] = '5Q58yR2U3k32';
$menu['organization']['menu']['supply']['title'] = '供应商管理';
$menu['organization']['menu']['supply']['url'] = 'organization_supply.html';
$menu['organization']['menu']['supply']['permission_id'] = 'organization_supply';
$menu['organization']['menu']['supply']['permission_name'] = '供应商管理';
$menu['organization']['menu']['supply']['class'] = 'icon-truck';
$menu['organization']['menu']['agency']['title'] = '分销商管理';
$menu['organization']['menu']['agency']['url'] = 'organization_agency.html';
$menu['organization']['menu']['agency']['permission_id'] = 'organization_agency';
$menu['organization']['menu']['agency']['permission_name'] = '分销商管理';
$menu['organization']['menu']['agency']['class'] = 'icon-shopping-cart';
$menu['organization']['menu']['attach']['title'] = '分销商归属';
$menu['organization']['menu']['attach']['url'] = 'organization_attach.html';
$menu['organization']['menu']['attach']['permission_id'] = 'organization_agency';
$menu['organization']['menu']['attach']['permission_name'] = '分销商归属';
$menu['organization']['menu']['attach']['class'] = 'icon-filter';

$menu['organization']['menu']['register']['title'] = '注册供应商';
$menu['organization']['menu']['register']['url'] = 'organization_register.html';
$menu['organization']['menu']['register']['permission_id'] = 'organization_supply';
$menu['organization']['menu']['register']['permission_name'] = '注册供应商';
$menu['organization']['menu']['register']['class'] = 'icon-filter';

$menu['organization']['menu']['enroll']['title'] = '注册分销商';
$menu['organization']['menu']['enroll']['url'] = 'organization_enroll.html';
$menu['organization']['menu']['enroll']['permission_id'] = 'organization_agency';
$menu['organization']['menu']['enroll']['permission_name'] = '注册分销商';
$menu['organization']['menu']['enroll']['class'] = 'icon-filter';

//$menu['landscape']['menu']['add']['title']             = '添加景区';
//$menu['landscape']['menu']['add']['url']               = 'landscape_add.html';
//$menu['landscape']['menu']['add']['permission_id']     = 'landscape_add';
//$menu['landscape']['menu']['add']['permission_name']   = '添加景区';
//$menu['landscape']['menu']['add']['class']             = 'icon-plus';

$menu['landscape']['menu']['addequip']['title'] = '添加设备';
$menu['landscape']['menu']['addequip']['url'] = 'landscape_addEquip.html';
$menu['landscape']['menu']['addequip']['permission_id'] = 'landscape_addEquip';
$menu['landscape']['menu']['addequip']['permission_name'] = '添加设备';
$menu['landscape']['menu']['addequip']['class'] = 'icon-zoom-in';
$menu['landscape']['menu']['equipments']['title'] = '设备管理';
$menu['landscape']['menu']['equipments']['url'] = 'landscape_equipments.html';
$menu['landscape']['menu']['equipments']['permission_id'] = 'landscape_equipments';
$menu['landscape']['menu']['equipments']['permission_name'] = '设备管理';
$menu['landscape']['menu']['equipments']['class'] = 'icon-desktop';



//用户管理
//$menu['user']['title']                                  = '用户管理';
//$menu['user']['class']                                  = 'icon-cogs';
//$menu['user']['toggle']                                 = '5Q58yRUkd0';
//$menu['user']['menu']['supply']['title']                 = '供应商管理';
//$menu['user']['menu']['supply']['url']                   = 'supply_lists.html';
//$menu['user']['menu']['supply']['permission_id']         = 'supply_lists';
//$menu['user']['menu']['supply']['permission_name']       = '供应商管理';
//$menu['user']['menu']['supply']['class']       = 'icon-reorder';
//$menu['user']['menu']['agency']['title']                 = '分销商管理';
//$menu['user']['menu']['agency']['url']                   = 'agency_lists.html';
//$menu['user']['menu']['agency']['permission_id']         = 'agency_lists';
//$menu['user']['menu']['agency']['permission_name']       = '分销商管理';
//$menu['user']['menu']['agency']['class']       = 'icon-reorder';
//// 门票管理
//$menu['ticket']['title']                               = '门票管理';
//$menu['ticket']['class']                               = 'icon-qrcode';
//$menu['ticket']['toggle']                              = '5Q58yRUk3j';
//$menu['ticket']['menu']['index']['title']              = '景区门票';
//$menu['ticket']['menu']['index']['url']                = 'ticket_index.html';
//$menu['ticket']['menu']['index']['permission_id']      = 'ticket_index';
//$menu['ticket']['menu']['index']['permission_name']    = '门票列表';
//$menu['ticket']['menu']['index']['class']              = 'icon-list';
//$menu['ticket']['menu']['agency']['title']              = '旅行社门票';
//$menu['ticket']['menu']['agency']['url']                = 'ticket_agency.html';
//$menu['ticket']['menu']['agency']['permission_id']      = 'ticket_agency';
//$menu['ticket']['menu']['agency']['permission_name']    = '旅行社门票列表';
//$menu['ticket']['menu']['agency']['class']              = 'icon-list';
//$menu['ticket']['menu']['type']['title']               = '门票类型';
//$menu['ticket']['menu']['type']['url']                 = 'ticket_type.html';
//$menu['ticket']['menu']['type']['permission_id']       = 'ticket_type';
//$menu['ticket']['menu']['type']['permission_name']     = '门票类型';
//$menu['ticket']['menu']['type']['class']               = 'icon-magic';
//
//
//
//// 订单管理
//$menu['order']['title']                                = '订单管理';
//$menu['order']['class']                                = 'icon-file';
//$menu['order']['toggle']                               = '5Q69yRUk6j';
//$menu['order']['menu']['search']['title']              = '订单查询';
//$menu['order']['menu']['search']['url']                = 'order_search.html';
//$menu['order']['menu']['search']['permission_id']      = 'order_search';
//$menu['order']['menu']['search']['permission_name']    = '订单查询';
//$menu['order']['menu']['search']['class']              = 'icon-search';
//$menu['order']['menu']['detail']['title']              = '门票使用查询';
//$menu['order']['menu']['detail']['url']                = 'order_detail.html';
//$menu['order']['menu']['detail']['permission_id']      = 'order_detail';
//$menu['order']['menu']['detail']['permission_name']    = '门票使用查询';
//$menu['order']['menu']['detail']['class']              = 'icon-list-alt';
//$menu['order']['menu']['check']['title']               = '验票';
//$menu['order']['menu']['check']['url']                 = 'order_check.html';
//$menu['order']['menu']['check']['permission_id']       = 'order_check';
//$menu['order']['menu']['check']['permission_name']     = '验票';
//$menu['order']['menu']['check']['class']               = 'icon-check';
//
//
//// 退票管理
//$menu['refund']['title']                               = '退票管理';
//$menu['refund']['class']                               = 'icon-hdd';
//$menu['refund']['toggle']                              = '5Q69yACk6i';
//$menu['refund']['menu']['verify']['title']             = '退票审核';
//$menu['refund']['menu']['verify']['url']               = 'refund_verify.html';
//$menu['refund']['menu']['verify']['permission_id']     = 'refund_verify';
//$menu['refund']['menu']['verify']['permission_name']   = '退票审核';
//$menu['refund']['menu']['verify']['class']             = 'icon-sitemap';
//$menu['refund']['menu']['record']['title']             = '退票记录';
//$menu['refund']['menu']['record']['url']               = 'refund_record.html';
//$menu['refund']['menu']['record']['permission_id']     = 'refund_record';
//$menu['refund']['menu']['record']['permission_name']   = '退票记录';
//$menu['refund']['menu']['record']['class']             = 'icon-share';
//
// 财务统计
$menu['bill']['title'] = '财务管理';
$menu['bill']['class'] = 'icon-money';
$menu['bill']['toggle'] = '5Q69yACk6j';
$menu['bill']['menu']['payable']['title'] = '应付账款';
$menu['bill']['menu']['payable']['url'] = 'bill_payable.html';
$menu['bill']['menu']['payable']['permission_id'] = 'bill_payable';
$menu['bill']['menu']['payable']['permission_name'] = '应付账款';
$menu['bill']['menu']['payable']['class'] = 'icon-share';
$menu['bill']['menu']['receivable']['title'] = '供应商应收账款单查询';
$menu['bill']['menu']['receivable']['url'] = 'bill_receivable.html';
$menu['bill']['menu']['receivable']['permission_id'] = 'bill_receivable';
$menu['bill']['menu']['receivable']['permission_name'] = '供应商应收账款单查询';
$menu['bill']['menu']['receivable']['class'] = 'icon-share';
$menu['bill']['menu']['config']['title'] = '结算配置';
$menu['bill']['menu']['config']['url'] = 'bill_config.html';
$menu['bill']['menu']['config']['permission_id'] = 'bill_config';
$menu['bill']['menu']['config']['permission_name'] = '结算设置';
$menu['bill']['menu']['config']['class'] = 'icon-road';
$menu['bill']['menu']['report']['title'] = '交易报表';
$menu['bill']['menu']['report']['url'] = 'bill_report.html';
$menu['bill']['menu']['report']['permission_id'] = 'bill_report';
$menu['bill']['menu']['report']['permission_name'] = '交易报表';
$menu['bill']['menu']['report']['class'] = 'icon-list-alt';
$menu['bill']['menu']['fund']['title'] = '资产管理';
$menu['bill']['menu']['fund']['url'] = 'bill_fund.html';
$menu['bill']['menu']['fund']['permission_id'] = 'bill_fund';
$menu['bill']['menu']['fund']['permission_name'] = '资产管理';
$menu['bill']['menu']['fund']['class'] = 'icon-share';


//$menu['bill']['menu']['union']['title']             = '取现管理';
//$menu['bill']['menu']['union']['url']               = 'bill_lists.html';
//$menu['bill']['menu']['union']['permission_id']     = 'bill_lists';
//$menu['bill']['menu']['union']['permission_name']   = '取现管理';
//$menu['bill']['menu']['union']['class']             = 'icon-road';
//
//// 消息管理
//$menu['message']['title']                               = '消息管理';
//$menu['message']['class']                               = 'icon-envelope-alt';
//$menu['message']['toggle']                              = '5Q69yACk7j';
//$menu['message']['menu']['notice']['title']             = '公告管理';
//$menu['message']['menu']['notice']['url']               = 'message_notice.html';
//$menu['message']['menu']['notice']['permission_id']     = 'message_notice';
//$menu['message']['menu']['notice']['permission_name']   = '公告管理';
//$menu['message']['menu']['notice']['class']             = 'icon-envelope';
//$menu['message']['menu']['publish']['title']            = '发布消息';
//$menu['message']['menu']['publish']['url']              = 'message_publish.html';
//$menu['message']['menu']['publish']['permission_id']    = 'message_publish';
//$menu['message']['menu']['publish']['permission_name']  = '发布消息';
//$menu['message']['menu']['publish']['class']            = 'icon-edit';
//$menu['message']['menu']['suggest']['title']             = '建议管理';
//$menu['message']['menu']['suggest']['url']               = 'message_suggest.html';
//$menu['message']['menu']['suggest']['permission_id']     = 'message_suggest';
//$menu['message']['menu']['suggest']['permission_name']   = '建议管理';
//$menu['message']['menu']['suggest']['class']             = 'icon-envelope';
//
//
//// 帮助文档
//$menu['help']['title']                               = '帮助文档';
//$menu['help']['class']                               = 'icon-leaf';
//$menu['help']['toggle']                              = '5Q69yACk7k';
//$menu['help']['menu']['type']['title']             = '类别设置';
//$menu['help']['menu']['type']['url']               = 'help_type.html';
//$menu['help']['menu']['type']['permission_id']     = 'help_type';
//$menu['help']['menu']['type']['permission_name']   = '类别设置';
//$menu['help']['menu']['type']['class']             = 'icon-reorder';
//$menu['help']['menu']['lists']['title']             = '文档列表';
//$menu['help']['menu']['lists']['url']               = 'help_lists.html';
//$menu['help']['menu']['lists']['permission_id']     = 'help_lists';
//$menu['help']['menu']['lists']['permission_name']   = '文档列表';
//$menu['help']['menu']['lists']['class']             = 'icon-list-alt';
//$menu['help']['menu']['file']['title']            = '资料列表';
//$menu['help']['menu']['file']['url']              = 'help_file.html';
//$menu['help']['menu']['file']['permission_id']    = 'help_file';
//$menu['help']['menu']['file']['permission_name']  = '资料列表';
//$menu['help']['menu']['file']['class']            = 'icon-list-alt';
//
//$menu['help']['menu']['write']['title']            = '添加资料';
//$menu['help']['menu']['write']['url']              = 'help_write.html';
//$menu['help']['menu']['write']['permission_id']    = 'help_write';
//$menu['help']['menu']['write']['permission_name']  = '添加资料';
//$menu['help']['menu']['write']['class']            = 'icon-edit';
//
//
////系统管理
$menu['system']['title'] = '系统管理';
$menu['system']['class'] = 'icon-cog';
$menu['system']['toggle'] = '6658yRUk32j2';
$menu['system']['menu']['role']['title'] = '角色权限';
$menu['system']['menu']['role']['url'] = 'system_role.html';
$menu['system']['menu']['role']['permission_id'] = 'system_role';
$menu['system']['menu']['role']['permission_name'] = '角色权限';
$menu['system']['menu']['role']['class'] = 'icon-list';
$menu['system']['menu']['staff']['title'] = '员工管理';
$menu['system']['menu']['staff']['url'] = 'system_staff.html';
$menu['system']['menu']['staff']['permission_id'] = 'system_staff';
$menu['system']['menu']['staff']['permission_name'] = '员工管理';
$menu['system']['menu']['staff']['class'] = 'icon-user';
$menu['system']['menu']['repass']['title'] = '修改密码';
$menu['system']['menu']['repass']['url'] = 'system_repass.html';
$menu['system']['menu']['repass']['permission_id'] = 'system_repass';
$menu['system']['menu']['repass']['permission_name'] = '修改密码';
$menu['system']['menu']['repass']['class'] = 'icon-key';

$menu['system']['menu']['home']['title'] = '首页推荐';
$menu['system']['menu']['home']['url'] = 'system_home.html';
$menu['system']['menu']['home']['permission_id'] = 'system_home';
$menu['system']['menu']['home']['permission_name'] = '首页推荐';
$menu['system']['menu']['home']['class'] = 'icon-list';
//
////OTA管理
//$menu['ota']['title']                             = "OTA管理";
//$menu['ota']['class']                             = 'icon-cog';
//$menu['ota']['toggle']                            = '3358yRUk32j2';
//$menu['ota']['menu']['ticket']['title']           = "门票上架";
//$menu['ota']['menu']['ticket']['url']             = 'ota_ticket.html';
//$menu['ota']['menu']['ticket']['permission_id']   = 'ota_ticket';
//$menu['ota']['menu']['ticket']['permission_name'] = '门票上架';
//$menu['ota']['menu']['ticket']['class']           = 'icon-list';
//$menu['ota']['menu']['bill']['title']             = '应收账单';
//$menu['ota']['menu']['bill']['url']               = 'ota_bill.html';
//$menu['ota']['menu']['bill']['permission_id']     = 'ota_bill';
//$menu['ota']['menu']['bill']['permission_name']   = '应收账单';
//$menu['ota']['menu']['bill']['class']             = 'icon-list';
// 定义常量
define('PI_MENU', serialize($menu));
unset($menu);

/* End */
