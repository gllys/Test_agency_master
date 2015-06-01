/**
 * lodop打印
 * 
 * @author LRS
 */
/**
 * 打印小票
 * @param integer id 验票记录的列表的id，注意不是订单编号
 */
function lodopPrint(id,printType) {
	this.id = id;
        this.printType = printType;
	this.style();
	this.data(this.id);
	this.ajax();
	
}

/**
 * 打印样式
 */
lodopPrint.prototype.style = function() {
	this.style = '<div><div style="width: 48mm; padding-bottom: 5px; font-size: 12px; position: relative;"> ' +
        '<div style="font-size: 14px;top:-5mm;">{print_type}</div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">景区名称:</span><span style="display: inline-block; *display: inline; zoom: 1; width: 30mm; vertical-align: middle;">{lan_name}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">产品名称:</span><span style="display: inline-block; *display: inline; zoom: 1; width: 30mm; vertical-align: middle;">{ticket_name}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">订单号:</span><span>{order_id}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">验证张数:</span><span>{num}张</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">验证时间:</span><span>{date}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">取票人:</span><span>{owner_name}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">电话:</span><span>{owner_mobile}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">验证结果:</span><span>验证成功</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">操作员:</span><span>{op_name}</span></div>' +
	'<div><span style="display: inline-block; *display: inline; zoom: 1; width: 15mm;">备注:</span><span></span></div></div></div>';
}

/**
 * 返回$.post请求的需要的参数数据，即第二个参数
 * @param integer id
 * @return str
 */
lodopPrint.prototype.data = function(id) {
	var json = {
		id:id
	}
	this.data = $.param(json)
}

/**
 * 返回$.post请求的需要的参数数据，即第二个参数
 * @param integer id
 * @return str
 */
lodopPrint.prototype.ajax = function() {
	var _this = this;
	$.post('/check/check/reprint', this.data, function(data) {
        if (data.error === 0) {
            //alert(data);return false;
            if (_this.printType == 1) {
                data.params['print_type'] = '正联';
                _this.printLodop(data.params);
                setTimeout(function() {
                    data.params['print_type'] = '副联';
                    _this.printLodop(data.params);
                    alert('重打小票成功');
                }, 1000);
            } else {
                data.params['print_type'] = '正联';
                _this.printLodop(data.params);
                alert('重打小票成功');
            }
        } else {
            alert(data.msg);
        }
    }, 'json');
}

/**
 * 调用lodop打印
 * @params params 要打印的数据
 */
lodopPrint.prototype.printLodop = function(params) {
	//this.username = 'LRS';
    var $content = '<div><img style="display:block; margin:10mm auto; margin-bottom:5mm;" src="/img/xiaopiao_logo.png" /></div>' + this.style;
    for (i in params) {
    	$content = $content.replace('{' + i + '}', params[i]);
    }
    //console.log($content);return false;
    LODOP = getLodop();
    LODOP.PRINT_INIT("打印任务名");               //首先一个初始化语句
    LODOP.SET_PRINTER_INDEXA(this.getPrinter(params.operator));
    LODOP.SET_PRINT_PAGESIZE(3, '48mm', '5mm', 'sd');//alert($content);return;
    LODOP.ADD_PRINT_HTM(0, 0, '48mm', '100%', $content);
    //LODOP.PREVIEW();
    LODOP.PRINT();
}

/**
 * 获取打印机
 * 注意：
 * - 如果之前没有$.cookie(username)，则会调起浏览器配置打印机弹窗。该名称任意命名并不终止打印行为。
 * @param string userId 该值为Yii::app()->user->id，沿用之前的规则
 */
lodopPrint.prototype.getPrinter = function(userId) {
    var username = 'print_' + userId;
	var cookValue = $.cookie(username);
    if (cookValue == null || cookValue == "") {
        LODOP = getLodop();
        var cookValue = LODOP.SELECT_PRINTER();
        $.cookie(username, cookValue, {expires: 365});
    }
    return cookValue;
}
