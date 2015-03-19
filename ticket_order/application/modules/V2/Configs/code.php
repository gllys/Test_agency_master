<?php

/**
 * code配置
 *
 * @author  lizi
 * @version 1.0
 */
// code 配置
define('PI_APP_CODES', serialize(array(
    '200' => array('status' => 200, 'msg' => 'OK'),
    '0001' => array('status' => 400, 'msg' => '错误的请求协议'),
    '0002' => array('status' => 400, 'msg' => '错误的请求方法'),
    '0011' => array('status' => 400, 'msg' => 'token验证错误'),
    '0012' => array('status' => 400, 'msg' => 'token不存在'),
    '0013' => array('status' => 400, 'msg' => 'token已过期'),
    #帐号验证
    '0100' => array('status' => 400, 'msg' => '账号参数错误'),
    '0101' => array('status' => 400, 'msg' => '对不起，您输入的账号不存在，请重新输入。'),
    '0102' => array('status' => 400, 'msg' => '对不起，您输入的账号密码不匹配，请重新输入。'),
    '0103' => array('status' => 400, 'msg' => '对不起，您输入的账号已被删除，请和管理员联系。'),
    '0104' => array('status' => 400, 'msg' => '对不起，您输入的账号已被停用，请和管理员联系。'),
    '0105' => array('status' => 400, 'msg' => '对不起，您所在机构的信息不存在，请和管理员联系。'),
    '0106' => array('status' => 400, 'msg' => '对不起，您所在机构的已停用，请和管理员联系。'),
    '0107' => array('status' => 400, 'msg' => '对不起，您所在机构的已停用，请和管理员联系。'),
    '0108' => array('status' => 400, 'msg' => '对不起，您输入的账号没有检票权限，请和管理员联系。'),
    #扫描二维码
    '0200' => array('status' => 400, 'msg' => '二维码参数错误'),
    '0201' => array('status' => 400, 'msg' => '对不起，此票号不存在，请重新扫描。'),
    '0202' => array('status' => 400, 'msg' => '对不起，此票号已使用，请重新扫描。'),
    '0203' => array('status' => 400, 'msg' => '对不起，您的票已使用完。'),
    '0204' => array('status' => 400, 'msg' => '对不起，您的票未付款或已申请退款,不能使用。'),
    '0205' => array('status' => 400, 'msg' => '对不起，该设备未绑定景点,请设置后再使用。'),
    '0206' => array('status' => 400, 'msg' => '对不起，该票不可在此景点使用。'),
    '0207' => array('status' => 400, 'msg' => '对不起，您的票未到使用时间。'),
    '0208' => array('status' => 400, 'msg' => '对不起，您的票已超过有效期。'),
    '0209' => array('status' => 400, 'msg' => '对不起，您的票今天不可使用，请检适用时间。'),
    '0210' => array('status' => 400, 'msg' => '对不起，此票号扫描失败，请重新扫描。'),
    #设备验证
    '0300' => array('status' => 400, 'msg' => '参数错误'),
    '0301' => array('status' => 400, 'msg' => '验证失败'),
    '0400' => array('status' => 400, 'msg' => '参数错误'),
    '0401' => array('status' => 400, 'msg' => '无历史记录'),
    '0402' => array('status' => 401, 'msg' => '用户凭据非法或无效'),
    '0403' => array('status' => 400, 'msg' => '设备未绑定'),
    '0404' => array('status' => 400, 'msg' => '帐号机构和设备机构不同，无权使用此设备号'),
    #绑定子景点
    '0501' => array('status' => 400, 'msg' => '您所选择的子景点所属机构和设备设置机构不同，不能绑定，请和管理员联系。'),
    #扫描二维码是身份证
    '0600' => array('status' => 400, 'msg' => '对不起，此身份证或手机号不存在，请重新扫描。'),
    '0601' => array('status' => 400, 'msg' => '对不起，此身份证或手机号下没有票了。'),
)));

// 状态码
define('PI_APP_CODES_STATUS', serialize(array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => '(Unused)',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported'
)));

//rpc code
define('PI_PHPRPC_CODES', serialize(array(
    '0' => array('ResultCode' => 0, 'ResultType'=>'1','ResultMessage' => 'OK'),
    '800101' => array('ResultCode' => 800101, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+类型传入错误'),
    '800102' => array('ResultCode' => 800102, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+已检'),
    '800103' => array('ResultCode' => 800103, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+失效'),
    '800104' => array('ResultCode' => 800104, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+二维码不存在'),
    '800105' => array('ResultCode' => 800105, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+重复使用'),
    '800301' => array('ResultCode' => 800301, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+密码错误'),
    '800500' => array('ResultCode' => 800500, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+设备异常'),
    '800501' => array('ResultCode' => 800501, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+子景点未设置'),
     #扫描二维码
    '800200' => array('ResultCode' => 800200, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+二维码错误'),
    '800201' => array('ResultCode' => 800201, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNEXIST+票号不存在'),
    '800202' => array('ResultCode' => 800202, 'ResultType'=>'21', 'ResultMessage' => '票已检+CHECKED+票号已使用'),
    '800203' => array('ResultCode' => 800203, 'ResultType'=>'21', 'ResultMessage' => '票已检+CHECKED+票已使用完。'),
    '800204' => array('ResultCode' => 800204, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+票未付款或退款'),
    '800205' => array('ResultCode' => 800205, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+子景点未设置'),
    '800206' => array('ResultCode' => 800206, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+此景点不能使用。'),
    '800207' => array('ResultCode' => 800207, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+未到使用时间。'),
    '800208' => array('ResultCode' => 800208, 'ResultType'=>'14', 'ResultMessage' => '已过期+HASEXPIRED+票已过有效期。'),
    '800209' => array('ResultCode' => 800209, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+票今天不可使用'),
    #身份证
    '800300' => array('ResultCode' => 800300, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+身份证下没有订单'),
    '800302' => array('ResultCode' => 800301, 'ResultType'=>'14', 'ResultMessage' => '无效票+UNVIOD+身份证下多个订单'),
)));
