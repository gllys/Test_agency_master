<?php
$this->breadcrumbs = array('订单管理', '订单详情');
?>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">可游玩景点</h4>
        </div>
        <div class="panel-body">
            <?php 
            if (!empty($ticket)) {
                    $ticketFirst = current($ticket) ;
                    $linfo = explode(',', $ticketFirst['landscape_ids']);   //票景区
			        $result = Landscape::api()->lists(array('ids' => $ticketFirst['landscape_ids']));
			        $landscapeInfo = ApiModel::getLists($result);
			        $field['landscape_ids'] = $ticketFirst['landscape_ids'];
              		$field['ids'] = $ticketFirst['view_point'];
               		$datas = Poi::api()->lists($field);
               		$data = ApiModel::getLists($datas);
               		$str ='';
               	if(!empty($landscapeInfo)){
               		foreach ($linfo as $id){
                        foreach($landscapeInfo as $key=>$model){
                            if($id == $model['id']) {$str= '<span class="mr20">' . $str.$model['name']."：</span>";}
                      	}
                      	$vals = '';
                       	foreach ($data as $key=>$item){
                        	if($id == $item['landscape_id']){
                                     $vals = '<span class="mr20">' . $vals.$item['name'].'</span>';}
                                }
                        $str =$str.$vals.'<br>';
                    }
                    echo $str;
               	}else{
               		echo '该景区已被删除';	
               	}	         		
            }   
            ?>
        </div>
    </div>

    <?php if (isset($detail)): 
		$order_item = current($detail['order_items']);
	?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">订单信息</h4>
            </div>
            <style>
		.table-responsive th,.table-responsive td{
			vertical-align:middle!important
		}
		.panel-footer b{
			font-size:22px;
			padding:0 5px;
		}
		#t1 th{
			text-align:right
			}
		#t1 td{
			text-align:left
			}
		</style>
            <div class="panel-body">
                <div class="table-responsive mb10">
                    <table class="table table-bordered" id="t1">
                        <tbody>
                            <tr>
                                <th width="150">订单号：</th>
                                <td><?php echo $detail['id'] ?></td>
                                  </tr>
                                  <tr>
                                <th width="150">订单状态：</th>
                                <td><?php echo $status_labels[$detail['status']];?></td>
                            </tr>
                            
                            <tr <?php echo in_array($detail['status'],array('paid','finish','billed'),true)? '' : 'hidden'; ?>>
                                <th>支付时间：</th>
                                <td><?php echo date('Y-m-d H:i:s', $detail['pay_at'])?></td>
                                </tr>
                                  <tr <?php echo in_array($detail['status'],array('paid','finish','billed'),true)? '' : 'hidden'; ?>>
                                <th>支付方式：</th>
                                <td><?php if(!empty($detail['payment'])){echo $paid_type[$detail['payment']];}?></td>
                            </tr>
                            <tr>
                                <th>预定时间：</th>
                                <td><?php echo date('Y-m-d H:i:s', $detail['created_at']) ?></td>
                                </tr>
                                  <tr>
                                <th>游玩时间：</th>
                                <td><?php echo date('Y-m-d', strtotime($detail['use_day'])) ?></td>
                            </tr>
                            <tr>
                                <th>订单类型：</th>
                                <td colspan="3">电子票</td>
                            </tr>
                            <tr <?php if($detail['nums'] - $detail['used_nums'] - $detail['refunding_nums'] - $detail['refunded_nums'] == 0){ echo 'hidden';} ?>>
                                <th>未使用张数：</th>
                                <td><?php echo $detail['nums'] - $detail['used_nums'] - $detail['refunding_nums'] - $detail['refunded_nums'] ?>张</td>
                                </tr>
                                  <tr <?php echo $detail['used_nums'] == 0 ? 'hidden' : ''; ?>>
                                <th >已使用张数：</th>
                                <td><?php echo $detail['used_nums'] == 0 ? '0' : $detail['used_nums'] ?>张</td>
                            </tr>
                            <tr <?php echo $detail['refunding_nums'] == 0 ? 'hidden' : ''; ?>>
                                <th>退款中张数：</th>
                                <td><?php echo $detail['refunding_nums'] == 0 ? '0' : $detail['refunding_nums'] ?>张</td>
                                </tr>
                                  <tr <?php echo $detail['refunded_nums'] == 0 ? 'hidden' : ''; ?>>
                                <th>已退款张数：</th>
                                <td><?php echo $detail['refunded_nums'] == 0 ? '0' : $detail['refunded_nums'] ?>张</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>门票名称</th>
                                    <th>门票张数</th>
                                    <th><?php echo $detail['price_type'] == 0 ? '散客结算价' : '团队结算价' ?></th>
                                    <th>当日价格</th>
                                    <th>总计</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $detail['name'] ?></td>
                                    <td><?php echo $detail['nums'] ?></td>
                                    <td><del><?php echo $detail['price_type'] == 0 ? $detail['fat_price'] : $detail['group_price'] ?></del></td>
                                    <td><?php echo $detail['price']?></td>
                                    <td><?php echo number_format($detail['amount'],2);?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" style="text-align:right" id="take-ticket-footer">
                    <span style="margin-right:30px">合计票数:<b class="text-danger" id="totalnum"><?php echo $detail['nums'] ?></b>张</span>
                    <span style="margin-right:30px">合计支付金额:<b class="text-danger"><?php echo $detail['amount']?></b>元</span>


                    <!--div style="text-align:center;padding:10px">
                            <!iv class="rdio rdio-default inline-block mr20">
                                    <input type="radio" value="1" id="radio1" name="radio" checked>
                                    <label for="radio1">支付宝</label>
                            </div>
                            <div class="rdio rdio-default inline-block mr20">
                                    <input type="radio" value="1" id="radio2" name="radio">
                                    <label for="radio2">快钱</label>
                            </div>
                            <div class="rdio rdio-default inline-block mr20">
                                    <input type="radio" value="1" id="radio3" name="radio">
                                    <label for="radio3">平台支付（可用额度：1000.00元）</label>
                            </div>
                            <div class="rdio rdio-default inline-block mr20">
                                    <input type="radio" value="1" id="radio4" name="radio">
                                    <label for="radio4">信用支付（可用额度：1000.00元）</label>
                            </div>
                            <div class="rdio rdio-default inline-block">
                                    <input type="radio" value="1" id="radio5" name="radio">
                                    <label for="radio5">储蓄支付（可用额度：1000.00元）</label>
                            </div>
                    </div-->
                    <?php $can_pay = strtotime($detail['use_day'].' 10:00:00') >= strtotime('10:00:00');?>
                    <?php if ($detail['status'] == 'unaudited') : ?>
                        <button class="btn btn-danger" type="button" onclick="cancel(<?php echo $detail['id'] ?>)">取消订单</button>
                    <?php elseif ($can_pay && $detail['status'] == 'unpaid' && !empty($detail['payment_id'])) : ?>
                        <a href="/order/payments/method/pid/<?php echo $detail['payment_id'] ?>" class="btn btn-primary">继续支付</a>
                        <button class="btn btn-danger" type="button" onclick="cancel(<?php echo $detail['id'] ?>)">取消订单</button>
                    <?php elseif($can_pay && $detail['status'] == 'unpaid'):?>
                    	<a href="/order/payments/method/combine/<?php echo $detail['id'] ?>" class="btn btn-success">去支付</a>
                        <button class="btn btn-danger" type="button" onclick="cancel(<?php echo $detail['id'] ?>)">取消订单</button>
                    <?php elseif ($detail['status'] == 'cancel'): ?>
                        <button class="btn btn-danger" type="button" disabled="disabled">订单已取消</button>
                    <?php elseif ($detail['status'] == 'paid'): ?>
                        <button class="btn btn-success" type="button" disabled="disabled">订单已支付</button>
                    <?php elseif ($detail['status'] == 'finish'): ?>
                        <button class="btn btn-warning" type="button" disabled="disabled">订单已结束</button>
                    <?php elseif ($detail['status'] == 'billed'): ?>
                        <button class="btn btn-success" type="button" disabled="disabled">订单已支付</button>
                    <?php endif; ?>
                </div>
            </div>

    <div class="panel panel-default" style="display: <?php if ($detail['status'] != 'paid' || $ticket[key($ticket)]['refund'] != 1){ echo 'none'; } ?>">
                        <div class="panel-body form-inline">
                            <form action="/order/detail/apply" method="post" id="applyform">
                                <input type="hidden" name="order_id" value="<?php echo $detail['id'] ?>">
                                <div class="form-group">
                                    申请退票张数 <input type="text" name="nums" class="form-control" style="width:50px" id="ticketmun"> 当前未使用可退票张数：
                                <input  id="totalnums" type=text 
                                        value="<?php 
                                                    $nums = $detail['nums'] - $detail['used_nums'] - $detail['refunding_nums'] - $detail['refunded_nums']; 
                                                    if($refund_num >= $nums){
                                                        $nums = 0;
                                                    }else{
                                                        $nums = $nums - $refund_num;
                                                    }
                                                    echo $nums;
                                                ?>" 
                                        style="border-style:none;width:40px;" readonly>张
                                </div>
                                <div class="form-group">
                                    退票理由: <input type="text" name="remark" class="form-control" style="width:400px" id="applyremark"> <button class="btn btn-primary btn-xs" type="button" id="applysub">申请退款</button>
                                </div>
                            </form>
                        </div><!-- panel-body -->
                    </div> 

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">取票人信息</h4>
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tbody>
                        <tr>
                            <td>取票人姓名：<?php echo $detail['owner_name'] ?></td>
                            <td>取票人手机号码：<?php echo $detail['owner_mobile'] ?>
                                <?php if($detail['status'] == 'paid'){ ?>
                                <button onclick="againSms('<?php echo $detail['id'] ?>', '<?php echo $detail['owner_mobile'] ?>');" class="btn btn-primary btn-xs ml10" type="button">重发短信</button></td>
                                <?php  }  ?>
                            <td>取票人身份证号码：<?php echo $detail['owner_card'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row"> 
                <div class="col-sm-6">


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">分销商信息</h4>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tbody>
        <?php $agencyInfo = Organizations::api()->show(array('id' => intval($ticket[key($ticket)]['distributor_id']))); ?>
                                <tr>
                                    <th width="150">用户名称：</th><td><?php echo isset($agencyInfo['body']['name']) ? $agencyInfo['body']['name'] : ''; ?></td>
                                </tr>
                                <tr>
                                    <th>操作人：</th><td><?php echo $detail['user_name']?$detail['user_name']:$agencyInfo['body']['contact'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">供应商信息</h4>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tbody>
        <?php $supplyInfo = Organizations::api()->show(array('id' => $ticket[key($ticket)]['supplier_id'])); ?>
                                <tr>
                                    <th width="150">供应商名称：</th><td><?php echo $supplyInfo['body']['name'] ?></td>
                                </tr>
                                <tr>
                                    <th>操作人：</th><td><?php echo !empty($order_item['user_name'])?$order_item['user_name']:$supplyInfo['body']['contact'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>	
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">订单备注</h4>
                </div>
                <div class="panel-body">
                    <textarea id="remark" placeholder="暂无备注信息" class="form-control" rows="5" maxlength="200" <?php if ($detail['status'] != 'reject'){ echo 'readonly="readonly"'; } ?>><?php echo html_entity_decode($detail['remark']) ?></textarea>
                </div>
                <input type="hidden" id="remark_ori" value="<?php echo html_entity_decode($detail['remark']) ?>" />
                <?php if ($detail['status'] == 'reject') :?>
                <div class="panel-heading">
                    <h4 class="panel-title">驳回理由</h4>
                </div>
                <div class="panel-body">
                    <textarea class="form-control" rows="5" maxlength="200" readonly="readonly"><?php echo html_entity_decode($detail['reason']) ?></textarea>
                </div><!-- panel-body -->
                <div class="panel-footer" style="text-align:right" id="take-ticket-footer">  
                    <button class="btn btn-danger" type="button" onclick="cancel(<?php echo $detail['id'] ?>,'reject')">取消订单</button>
                    <button class="btn btn-success" type="button" onclick="repost(<?php echo $detail['id'] ?>)">重新提交</button>
                </div>
                <?php endif; ?>
            </div>

            <div class="panel panel-default">
                <table class="table table-bordered mb30">
                    <tbody>
                        <tr>
                            <th width="120">购买规定</th>
                            <td>
        						<?php echo $ticket[key($ticket)]['remark'] ?>
                        	</td>
                        </tr>
                    </tbody>
                </table>
            </div>	
    <?php endif; ?>
</div><!-- contentpanel -->
<script>
    function againSms(id, mobile) {
        if (id != "" && id != undefined && mobile != "" && mobile != undefined) {//Todo::手机格式验证
            mobile = window.prompt('请输入需要发送短信的手机号码', mobile)
            if (mobile != null) {
                $.post('/order/detail/againSms', {id: id, mobile: mobile}, function(data) {
                    alert(data.errors);
                }, "json");
            }
        }
    }
    $("#applysub").click(function() {
        $(this).attr('disabled',true);
        var mun = $("#ticketmun").val();
        var total = $("#totalnums").val();
        var leng = $("#applyremark").val().length;
        if (isNaN(mun)) {
            alert('申请退票数不可为非数字！');
            $('#applysub').attr('disabled',false);
        } else if (Number(mun) < 0 || Number(mun) > Number(total)) {
            alert('申请退票数填写出错了！');
            $('#applysub').attr('disabled',false);
        } else {
            if (leng > 0) {
                var obj = $('#applyform');
                $.post('/order/detail/apply', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        var url = "/order/detail/?id=<?php echo $detail['id'] ?>";
                        window.location.href = url;

                    } else {
                        //成功
                        alert('申请成功');
                        var url = "/order/refund/";
                        window.location.href = url;

                    }
                }, 'json');
            } else {
                alert("请填写退票理由！");
                $('#applysub').attr('disabled',false);
            }
        }

    });

    function cancel(id) {
        PWConfirm('您确认取消该订单吗？',function(){
             $.post('/order/detail/cancel', {id: id}, function(data) {
                 if (data.error === 0) {
                     alert('订单已取消');
                     setTimeout("location.href='/order/history/'", '1000');
                 } else {
                     alert(data.msg);
                 }
             }, 'json')
         });
    }
    function repost(id) {
        var remark_ori = $('#remark_ori').val().replace(/(^\s+)|(\s+$)/g,"");
        var remark = $('#remark').val().replace(/(^\s+)|(\s+$)/g,"");
        var receiver_organization = <?php echo $ticket[key($ticket)]['supplier_id']?>;
        if(remark_ori == remark){
            alert('请修改备注内容后提交');
        }else{
            $.post('/order/detail/repost', {id: id,remark: remark,receiver_organization : receiver_organization}, function(data) {
                if (data.error === 0) {
                    alert('订单已重新提交');
                    setTimeout("location.href='/order/history/'", '1000');
                } else {
                    alert(data.msg);
                }
            }, 'json')
        }
    }
</script>

<script>
    jQuery(document).ready(function() {


        !function() {
            var takeTicketFooter = $('#take-ticket-footer'),
                    allBtn = $('#all-btn'),
                    pay = 98.00

//全选
            allBtn.click(function() {
                var obj = $(this).parents('table')
                if ($(this).text() == '全选') {
                    obj.find('input').prop('checked', true)
                    obj.find('tbody tr').addClass('selected')
                    $(this).text('反选')
                } else {
                    obj.find('input').prop('checked', false)
                    obj.find('tbody tr').removeClass('selected')
                    $(this).text('全选')
                }
                total()
            })

            $('.del-btn').click(function() {
                $(this).parents('tr').remove()
                total()
                return false
            })

            $('.all-btn').click(function() {
                $(this).parents('tr').toggleClass('selected')
                total()
            })


            $('.num').keyup(function() {
                total()
            })


            function total() {
                var tickets,
                        takeTicketNum = 0
                tickets = $('#take-ticket tbody').find('.selected').length
                $('.selected .num').each(function() {
                    takeTicketNum += parseInt($(this).val())
                })
                takeTicketFooter.html('<span style="margin-right:30px">共计门票:<b class="text-danger">' + tickets + '</b>张</span><span style="margin-right:30px">	共计票数:<b class="text-danger">' + takeTicketNum + '</b>张</span><span style="margin-right:30px">总金额:<b class="text-danger">' + takeTicketNum * pay + '</b>元</span><button type="submit" class="btn btn-primary">生成订单</button>')
            }


        }()
        // Tags Input
        jQuery('#tags').tagsInput({width: 'auto'});

        // Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();



        // Form Toggles
        jQuery('.toggle').toggles({on: true});


        // Date Picker
        jQuery('#datepicker').datepicker();
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });

        // Input Masks
        jQuery("#date").mask("99/99/9999");
        jQuery("#phone").mask("(999) 999-9999");
        jQuery("#ssn").mask("999-99-9999");

        // Select2
        jQuery("#select-basic, #select-multi").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        function format(item) {
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel')) + ' mr10"></i>' + item.text;
        }

        // This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });

        // Color Picker
        if (jQuery('#colorpicker').length > 0) {
            jQuery('#colorSelector').ColorPicker({
                onShow: function(colpkr) {
                    jQuery(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function(colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function(hsb, hex, rgb) {
                    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
                    jQuery('#colorpicker').val('#' + hex);
                }
            });
        }

        // Color Picker Flat Mode
        jQuery('#colorpickerholder').ColorPicker({
            flat: true,
            onChange: function(hsb, hex, rgb) {
                jQuery('#colorpicker3').val('#' + hex);
            }
        });


    });

</script>

