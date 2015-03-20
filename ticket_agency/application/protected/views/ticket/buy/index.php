<?php

$price = $_GET['price_type'] == 0 ? $info['fat_price'] : $info['group_price'];
if (isset($info['partner_price'])) {
    $price = $info['partner_price'];
}
?>
<div class="modal-dialog modal-lg" style="width:1000px">
    <form id="form" action="#" method="POST">
        <input type="hidden" name="ticket_template_id" value="<?Php echo $info['id'] ?>" />
        <input type="hidden" name="ticket_name" value="<?Php echo $info['name'] ?>" />
        <input type="hidden" name="price_type" value="<?Php echo $_GET['price_type'] ?>" />
        <input type="hidden" name="type" value="<?php echo $info['type']?>" />
        <input type="hidden" name="price" value="<?Php echo $price ?>"/>
        <input type="hidden" name="returnflag" id="returnflag" value="0"/>
        <input type="hidden" name="supply_type" id="supply_type" value="<?php echo $supply_type; ?>"/>
        <input type="hidden" name="receiver_organization" value="<?php echo $info['organization_id']?>" />
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" onclick="return false;" type="button">&times;</button>
                <h4 class="modal-title"><?Php echo $info['name'] ?></h4>
            </div>
            <div class="modal-body">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">预订信息</h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb30" id="table-info">
                                <thead>
                                    <tr>
                                        <th><span class="text-danger">*</span>游玩日期</th>
                                        <!--th>订购数</th-->
                                        <th>门市挂牌价</th>
                                        <th>网络销售价</th>
                                        <th><?php echo $_GET['price_type'] == 0 ? '散客结算价' : '团队结算价' ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="width:300px"><input style="background-position:90% 50%;" id="datepicker"  class="form-control datepicker" type="text" placeholder="游玩日期" name="use_day" readonly="readonly"></td>
                                        <!--td><input type="text" name="nums" id="order-count" value="<?php echo $info['mini_buy'] ?>"></td-->
                                        <td><del><?php echo $info['listed_price'] ?></del></td>
                                        <td><del><?php echo $info['sale_price'] ?></del></td>
                                        <td class="text-success"><?Php echo $price ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- panel-body -->
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">取票人信息</h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb30" id="take-ticket">
                                <tbody>
                                    <tr>
                                        <td style="font-weight:bold;width:70px;"><span class="text-danger">*</span>姓名</td>
                                        <td><input placeholder="必填" name="owner_name[]" type="text" class="form-control name" placeholder=""></td>
                                        <td style="padding-left:30px;font-weight:bold;width:90px;"><span class="text-danger">*</span>手机号码</td>
                                        <td><input placeholder="必填" name="owner_mobile[]" type="text" class="form-control phone" placeholder=""></td>
                                        <td style="padding-left:30px;font-weight:bold;width:80px;"><span class="text-danger">*</span>订购数</td>
                                        <td><input type="text" class="num" name="nums[]" id="order-count-0" value="<?php echo $info['mini_buy'] ?>"></td>
                                        <td style="padding-left:30px;font-weight:bold;width:100px;">身份证号码</td>
                                        <td><input placeholder="非必填" type="text" name="owner_card[]" class="form-control card" placeholder=""></td>
                                        <td style="padding-left:30px;"><a class="btn btn-success btn-xs" href="javascript:void(0)" id="take-ticket-add">增加</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- panel-body -->
                    <div class="panel-footer" style="text-align:right" id="take-ticket-footer">
                        <span>合计取票人:<b class="text-danger">1</b>位</span>
                        <span style="margin-left:30px">合计订单:<b class="text-danger">1</b>个</span>
                        <span style="margin-left:30px">合计票数:<b class="text-danger"><?php echo $info['mini_buy'] ?></b>张</span>
                        <span style="margin-left:30px">合计支付金额:<b class="text-danger"><?php echo $_GET['price_type'] == 0 ? $info['fat_price'] * $info['mini_buy'] : $info['group_price'] * $info['mini_buy'] ?></b>元</span>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">订单备注</h4>
                    </div>
                    <div class="panel-body">
                        <textarea rows="2" style="width:840px;margin:0 auto;" id="note" name='note' class="form-control" placeholder="限定200字以内" maxlength="200"></textarea>
                    </div><!-- panel-body -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id='place_order' type="button">确认下单</button>
                <button class="btn btn-primary" id='add_cart' type="button">加入购物车</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        //检查备注文本域是否有内容，判断是否是回传订单
        $(document).on('blur',"#note",function() {
                var fnum = $(this).val();
                var supply_type = $("#supply_type").val();
                if(fnum == '' || supply_type == '0'){
                    $("#returnflag").val('0');
                    $('#add_cart').css('cursor','pointer');
                    $('#add_cart').removeAttr('disabled','disabled');
                }else{
                    $("#returnflag").val('1');
                    $('#add_cart').css('cursor','not-allowed');
                    $('#add_cart').attr('disabled','disabled');
                }
            });
        $(document).on('keyup',"#note",function() {
                var fnum = $(this).val();
                var supply_type = $("#supply_type").val();
                if(fnum == '' || supply_type == '0'){
                    $("#returnflag").val('0');
                    $('#add_cart').css('cursor','pointer');
                    $('#add_cart').removeAttr('disabled','disabled');
                }else{
                    $("#returnflag").val('1');
                    $('#add_cart').css('cursor','not-allowed');
                    $('#add_cart').attr('disabled','disabled');
                }
            });
        //游玩时间改变，价格改变
        //var price = '<?php echo $price ?>';
        $('#datepicker').change(function(){
        	if($('#moneyNumber') && $('#moneyCount') && $('#day_price') && $('#day_count')){
        		$('#moneyNumber').remove();
        		$('#moneyCount').remove();
        		$('#day_price').remove();
        		$('#day_count').remove();
        	}

        		$('#place_order').removeAttr('disabled','disabled');
        		$('#add_cart').removeAttr('disabled','disabled');
        	

        	$.ajax({
        		type:  'post',
        		url:   '/ticket/buy/ticketInfo',
        		data:{  'ticket_id' : $('input[name=ticket_template_id]').val(),
        				'type' :$('input[name=price_type]').val(),
        				'use_day' : $('#datepicker').val()
        			},
        		dataType:'json',
        		success:function(data){
        			var moneytype = "<?php echo $_GET['price_type']?>";
        			var param = data.params;        			
        			var thInfo = $('#table-info tr:eq(0)');
        			var tdInfo = $('#table-info tr:eq(1)');        			
        			var thHtml = "<th id='day_price'>本日价格</th><th id='day_count'>本日库存</th>";
        			
        			thInfo.append(thHtml);
        			if(moneytype == 1 && param.day_group_price != null){
        				var tdNumber = Number($('#table-info tr:eq(1) td:eq(3)').text())+Number(param.day_group_price)+Number(param.group_discount);  //得到当日价格	
        			}else if(moneytype == 0 && param.day_fat_price != null){
        				var tdNumber = Number($('#table-info tr:eq(1) td:eq(3)').text())+Number(param.day_fat_price)+Number(param.fat_discount);  //得到当日价格	
        			}else if(moneytype == 1 && param.day_group_price == null && param.day_fat_price == null){
        				var tdNumber = Number($('#table-info tr:eq(1) td:eq(3)').text())+Number(param.group_discount);  //得到当日价格	
        			}else if(moneytype == 0 && param.day_group_price == null && param.day_fat_price == null){
        				var tdNumber = Number($('#table-info tr:eq(1) td:eq(3)').text())+Number(param.fat_discount);  //得到当日价格	
        			}   			       			
        			if(tdNumber < 0){
        				tdNumber = 0;
        			}
        			if(param.remain_reserve && Number(param.day_reserve) > 0){
        				var tdHtml = "<td class='text-danger' id='moneyNumber'>" + tdNumber.toFixed(2) + "</td><td id='moneyCount'>" + param.remain_reserve + "</td>";
        			}else if(Number(param.remain_reserve) == 0 && Number(param.day_reserve) > 0){
        				var tdHtml = "<td class='text-danger' id='moneyNumber'>" + tdNumber.toFixed(2) + "</td><td id='moneyCount'>0</td>";
        			}else{
        				var tdHtml = "<td class='text-danger' id='moneyNumber'>" + tdNumber.toFixed(2) + "</td><td id='moneyCount'>无限</td>";
        			}      			
        			tdInfo.append(tdHtml);
        			window.pay = tdNumber;
        			if(Number(param.day_reserve) > 0 && Number(param.remain_reserve) == 0){
        				$('#place_order').attr('disabled','disabled');
        				$('#add_cart').attr('disabled','disabled');
        			}
        			
        		}
        	})
       	})

        /*$('#datepicker').change(function() {
            $.get('/ticket/buy/dayPrice/', {id: '<?Php echo $info['id'] ?>', date: $(this).val()}, function(data) {
                if (data.error === 0 && data.params.length > 0) {
                    window.pay = data.params[0]['price'];
                } else {
                    window.pay = price;
                }
                total();
            }
            , 'json');
        });*/
        //姓名验证
        $('#form').delegate('.name','blur',function(){
            if ($(this).val() == '') {
                $(this).PWShowPrompt('取票人姓名不能为空');
                //$(this).focus();
                return false;
            }
            return false;
        });
        //手机号
        $('#form').delegate('.phone','blur',function(){
            var _str = $(this).val();
            if (_str.length !== 11 || !/^1\d{10}$/.test(_str)) {
            	$(this).PWShowPrompt('取票人手机号码为11位数字');
                //$(this).focus();
                return false;
            }
           return false;
        });
        //身份证
        $('#form').delegate('.card','blur',function(){
            var _card = $(this).val();
             if(_card != ''){
                    if(_card.length > 0){
                        var reg = /(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                        if(IdCardValidate(_card) === false){
                                $(this).PWShowPrompt('身份证号码错误');
                                //$(this).focus();
                                return false;
                        } 
                    }   
                    
                }   
                return false;
        });
        
        //确认下单
        $('#place_order').click(function() {
            //是否回传下单
            var returnflag = $('#returnflag').val();            
            //弹窗结束
            if ($('[name=use_day]').val() == '') {
            	$('[name=use_day]').PWShowPrompt('请输入产品有效期'); 
                $('[name=use_day]').focus();
                return false;
            }

            for (i = 0; i < $('.name').length; i++) {
                if ($('.name').eq(i).val() === '') {
                	$('.name').eq(i).PWShowPrompt('请输入取票人姓名'); 
                    $('.name').eq(i).focus();
                    return false;
                }
            }

            for (i = 0; i < $('.name').length; i++) {
                if ($('.name').eq(i).val().length > 13) {
                	$('.name').eq(i).PWShowPrompt('取票人姓名不能超过13个汉字'); 
                    $('.name').eq(i).focus();
                    return false;
                }
            }


            for (i = 0; i < $('.phone').length; i++) {
                var _str = $('.phone').eq(i).val();
                if (_str.length !== 11 || !/^1\d{10}$/.test(_str)) {
                	$('.phone').eq(i).PWShowPrompt('请输入取票人手机号码');
                    $('.phone').eq(i).focus();
                    return false;
                }
            }

            for (i = 0;i < $('.card').length; i++){
            	var _card = $('.card').eq(i).val();
            	if(_card != ''){
                    if(_card.length > 0){
                        var reg = /(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                        if(IdCardValidate(_card) === false){
                                $('.card').eq(i).PWShowPrompt('身份证号码错误');
                                $('.card').eq(i).focus();
                                return false;
                        } 
                    }   
                    
                }      
            }

            var _note = $('#note').val();
            if (_note.length > 200) {
            	$('[name=note]').PWShowPrompt('订单备注不能超过200个字');
                $('[name=note]').focus();
                return false;
            }

            $.post('/ticket/buy/placeOrder/', $('#form').serialize(), function(data) {
                if (data.error === 0) {
                    //提交订单返回成功后弹窗代码
                    if(returnflag == '1'){
                        //回传订单，弹提示后关闭
                        $("#verify-modal-buy").modal('hide');
                        $("#verify-modal-alert").html($("#modalalert")).modal('show');
                        $('#modalalert').show(); 
                        setTimeout(function(){
                            $('#modalalert').hide(); 
                            $("#verify-modal-alert").modal('hide');
                        },2000);
                        //setTimeout(function(){window.location.reload()},800);
                        return false;
                    }else{
                        //非回传订单，按老流程
                        window.location.href = '/order/payments/method/combine/' + data.params.join(',');
                    }
                    
                } else {
                    alert(data.msg);
                }
            }, 'json');
        });

        $('.num').live('blur',function(){
            var min = parseInt($(this).attr('aria-valuemin'));
            var max = parseInt($(this).attr('aria-valuemax'));
            min = isNaN(min)?1:min;
            max = isNaN(max)?999:max;
            $(this).val($(this).val()==""?0:$(this).val().replace(/\D/g,''));
            $(this).val($(this).val() < min?min:$(this).val());
            $(this).val($(this).val() > max?max:$(this).val());
        });

        //加入购物车
        $('#add_cart').click(function() {
            if ($('[name=use_day]').val() == '') {
            	$('[name=use_day]').PWShowPrompt('产品有效期不能为空'); 
                $('[name=use_day]').focus();
                return false;
            }

            for (i = 0; i < $('.name').length; i++) {
                if ($('.name').eq(i).val() === '') {
                    $('.name').eq(i).PWShowPrompt('取票人姓名不能为空'); 
                    $('.name').eq(i).focus();
                    return false;
                }
            }

            for (i = 0; i < $('.name').length; i++) {
                if ($('.name').eq(i).val().length > 13) {
                    $('.name').eq(i).PWShowPrompt('取票人姓名不能超过13个汉字'); 
                    $('.name').eq(i).focus();
                    return false;
                }
            }


            for (i = 0; i < $('.phone').length; i++) {
                var _str = $('.phone').eq(i).val();
                  if (_str.length !== 11 || !/^1\d{10}$/.test(_str)) {
                    $('.phone').eq(i).PWShowPrompt('取票人手机号码为11位数字'); 
                    $('.phone').eq(i).focus();
                    return false;
                }
            }

            for (i = 0;i < $('.card').length; i++){
            	var _card = $('.card').eq(i).val();
            	if(_card.length > 0){
                     var reg = /(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                    if(IdCardValidate(_card) === false){
                            $('.card').eq(i).PWShowPrompt('身份证号码错误'); 
                            $('.card').eq(i).focus();
                            return false;
                    } 
                }            
            }

			var _note = $('#note').val();
            if (_note.length > 200) {
                $('[name=note]').PWShowPrompt('订单备注不能超过200个字');
                $('[name=note]').focus();
                return false;
            }

            $.post('/ticket/buy/addCart/', $('#form').serialize(), function(data) {
                if (data.error === 0) {
                    alert('加入成功',function(){window.location.reload();});
                } else {
                    alert(data.msg);
                }
            }, 'json');
        });

        !function() {
            var takeTicketObj = $('#take-ticket'),
                    takeTicketFooter = $('#take-ticket-footer'),
                    takeTicketNum,
                    order = 1,
                    tickets;
            window.pay = <?php echo $price ?>;

            function updown(i) {
                var i = i | 0;
                spinner = $('#order-count-' + i).spinner()
                spinner.spinner({
                    'value': <?php echo $_GET['price_type'] == 0 ? 1 : $info['mini_buy'] ?>,
                    'min': <?php echo $_GET['price_type'] == 0 ? 1 : $info['mini_buy'] ?>,
                    'max': <?php echo $info['max_buy'] ?>,
                    'spin': function(event, ui) {
                        total();
                    }
                })
                spinner.spinner('value', 1);
                total();
            }
            updown(0);

            var addIndex = 0;
            $('#take-ticket-add').click(function() {
                addIndex++;
                var tpl = '<tr><th><span class="text-danger">*</span>姓名</th><td><input type="text" name="owner_name[]" class="form-control name" placeholder=""></td><th><span class="text-danger">*</span>手机号码</th><td><input type="text"  name="owner_mobile[]"  class="form-control phone" placeholder=""></td><th><span class="text-danger">*</span>订购数</th><td><input type="text" name="nums[]" class="num" id="order-count-' + addIndex + '" value="<?php echo $info['mini_buy'] ?>"></td><th>身份证号码</th><td><input type="text"  name="owner_card[]"   class="form-control" placeholder=""></td><td><a class="btn btn-success btn-xs take-ticket-del" href="javascript:void(0)">删除</a></td></tr>';
                takeTicketObj.append(tpl);
                updown(addIndex);
                total()
            })

            takeTicketObj.on('click', '.take-ticket-del', function() {
                $(this).parents('tr').remove()
                total()
            })

            $('#order-count').keyup(function() {
                $(this).val($(this).val() == "" ? 0 : $(this).val().replace(/\D/g, ''));
                orderCount = $(this).val();
                $(this).spinner();
                total();
            });

            //实时更新数据
            function total() {
                order = takeTicketObj.find('tr').length;

                var tickets = 0;
                for ($i = 0; $i < order; $i++) {
                    var $obj = takeTicketObj.find('tr').eq($i).find('.num') ;
                    var $num = $obj.val() ;
                    tickets += parseInt($num);
                    if(isNaN(tickets)){
                        tickets = 0;
                    }
                }
                takeTicketFooter.html('<span>合计取票人:<b class="text-danger">' + order + '</b>位</span><span style="margin-left:30px">合计订单:<b class="text-danger">' + order + '</b>张</span><span style="margin-left:30px">合计票数:<b class="text-danger">' + tickets + '</b>张</span><span style="margin-left:30px">合计支付金额:<b class="text-danger">' + (tickets * pay).toFixed(2) + '</b>元</span>')
            }
            total();
            window.setInterval(total,100);
            window.total = total;
        }();
	    <?php
	$now = time();
	if ($info['date_available']) {
		$dateAvailable = explode(',', $info['date_available']);
		$dateAvailable[1] = strtotime(strftime('%Y-%m-%d', $dateAvailable[1]).' 23:59:59');
	} else {
		$dateAvailable = array($now, $now + 3600 * 24 * 360 * 5);
	}
	$dateAvailable[0] = $dateAvailable[0] < $now ? $now:$dateAvailable[0] ;
	if ($info['scheduled_time']) {
		$b_time = $info['scheduled_time'] % 86400;
		$b_day = floor($info['scheduled_time'] / 86400);
		if ($b_time < (strtotime('1970-01-01 '.strftime('%H:%M').':00') + date('Z'))) {
			$b_day += 1;
		}
		if ($b_day) {
			$b_day = $now + $b_day * 86400;
			$dateAvailable[0] = max($dateAvailable[0], $b_day);
		}
	}
	?>
	    // Date Picker
        $.datepicker.regional["zh-CN"] = {closeText: "关闭", prevText: "&#x3c;上月", nextText: "下月&#x3e;", currentText: "今天", monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"], dayNames: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"], dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"], dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], weekHeader: "周", dateFormat: "yy-mm-dd", firstDay: 1, isRTL: !1, showMonthAfterYear: !0, yearSuffix: "年"}
        $.datepicker.setDefaults($.datepicker.regional["zh-CN"]);
        jQuery('#datepicker').datepicker({'option': 'yy-mm-dd', 'dateFormat': 'yy-mm-dd', minDate: '<?php echo date('Y-m-d', $dateAvailable[0]) ?>', maxDate: '<?php echo date('Y-m-d', $dateAvailable[1]) ?>',
                                          showOtherMonths: true, selectOtherMonths: true,
                                          beforeShowDay:function(date){
                                              var $days = '<?php echo $info['week_time'] ?>' ;
                                              var _day= date.getDay() ;
                                              if($days.indexOf(''+_day)!==-1){
                                                return [true, '']; 
                                              }else{
                                                return [false, 'CLOSED']; 
                                              }
                                          }
                                      });
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });



    });
    
    


//身份证验证
var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ];    // 加权因子   
var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ];            // 身份证验证位值.10代表X   
function IdCardValidate(idCard) { 
    idCard = trim(idCard.replace(/ /g, ""));               //去掉字符串头尾空格                     
    if (idCard.length == 15) {   
        return isValidityBrithBy15IdCard(idCard);       //进行15位身份证的验证    
    } else if (idCard.length == 18) {   
        var a_idCard = idCard.split("");                // 得到身份证数组   
        if(isValidityBrithBy18IdCard(idCard)&&isTrueValidateCodeBy18IdCard(a_idCard)){   //进行18位身份证的基本验证和第18位的验证
            return true;   
        }else {   
            return false;   
        }   
    } else {   
        return false;   
    }   
}   
/**  
 * 判断身份证号码为18位时最后的验证位是否正确  
 * @param a_idCard 身份证号码数组  
 * @return  
 */  
function isTrueValidateCodeBy18IdCard(a_idCard) {   
    var sum = 0;                             // 声明加权求和变量   
    if (a_idCard[17].toLowerCase() == 'x') {   
        a_idCard[17] = 10;                    // 将最后位为x的验证码替换为10方便后续操作   
    }   
    for ( var i = 0; i < 17; i++) {   
        sum += Wi[i] * a_idCard[i];            // 加权求和   
    }   
    valCodePosition = sum % 11;                // 得到验证码所位置   
    if (a_idCard[17] == ValideCode[valCodePosition]) {   
        return true;   
    } else {   
        return false;   
    }   
}   
/**  
  * 验证18位数身份证号码中的生日是否是有效生日  
  * @param idCard 18位书身份证字符串  
  * @return  
  */  
function isValidityBrithBy18IdCard(idCard18){   
    var year =  idCard18.substring(6,10);   
    var month = idCard18.substring(10,12);   
    var day = idCard18.substring(12,14);   
    var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
    // 这里用getFullYear()获取年份，避免千年虫问题   
    if(temp_date.getFullYear()!=parseFloat(year)   
          ||temp_date.getMonth()!=parseFloat(month)-1   
          ||temp_date.getDate()!=parseFloat(day)){   
            return false;   
    }else{   
        return true;   
    }   
}   
  /**  
   * 验证15位数身份证号码中的生日是否是有效生日  
   * @param idCard15 15位书身份证字符串  
   * @return  
   */  
  function isValidityBrithBy15IdCard(idCard15){   
      var year =  idCard15.substring(6,8);   
      var month = idCard15.substring(8,10);   
      var day = idCard15.substring(10,12);   
      var temp_date = new Date(year,parseFloat(month)-1,parseFloat(day));   
      // 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法   
      if(temp_date.getYear()!=parseFloat(year)   
              ||temp_date.getMonth()!=parseFloat(month)-1   
              ||temp_date.getDate()!=parseFloat(day)){   
                return false;   
        }else{   
            return true;   
        }   
  }   
//去掉字符串头尾空格   
function trim(str) {   
    return str.replace(/(^\s*)|(\s*$)/g, "");   
}    
</script>
<style type="text/css">
    /***可选颜色设置***/
    #ui-datepicker-div .ui-state-disabled{ color: #eeeeee;}
    #ui-datepicker-div .ui-state-highlight
</style>
