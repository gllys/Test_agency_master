<?php
$this->breadcrumbs = array('订单', '订单详情');
?>
<div class="contentpanel">
<!--    <?php if (isset($landscape)): ?>
        <h4 class="lg-title"><?php echo $landscape['name'] ?></h4>
    <?php endif; ?> -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">可游玩景点</h4>
        </div>
        <div class="panel-body">
		<?php
            if (isset($ticket[0])) {
                    $linfo = explode(',', $ticket[0]['landscape_ids']);   //票景区
			        $result = Landscape::api()->lists(array('ids' => $ticket[0]['landscape_ids']));
			        $landscapeInfo = ApiModel::getLists($result);
			        $field['landscape_ids'] = $ticket[0]['landscape_ids'];
              		$field['ids'] = $ticket[0]['view_point'];
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

    <?php if (isset($detail)): ?>
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
            </style>
            <div class="panel-body">
                <div class="table-responsive mb10">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="150">订单号：</th>
                                <td><?php echo $detail['id'] ?></td>
                                <th width="150">订单状态：</th>
                                <td><?php echo $status_labels[$detail['status']] ?></td>
                            </tr>
                            <!--tr <?php echo $detail['status'] != 'paid' ? 'hidden' : '' ?>>
                                <th>支付时间：</th>
                                <td></td>
                                <th>支付方式：</th>
                                <td></td>
                            </tr-->
                            <tr>
                                <th>预定时间：</th>
                                <td><?php echo date('Y年m月d日', $detail['created_at']) ?></td>
                                <th>游玩时间：</th>
                                <td><?php echo date('Y年m月d日', strtotime($detail['use_day'])) ?></td>
                            </tr>
                            <tr>
                                <th>订单类型：</th>
                                <td>任务单</td>
                                <th>票种类型</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div class="table-responsive">
                        <form method="post">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>门票名称</th>
                                    <th>预订票数</th>
                                    <?php if($detail['status'] == 'paid'):?>
                                    	<th>实际使用</th>
                                    <?php elseif($detail['status'] == 'finish'):?>
                                    	<th>实际使用</th>
                                    <?php endif;?>
                                    <th>价钱</th>
                                    <th>当日价格</th>
                                    <th>总计</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $ticket[0]['name'] ?></td>
                                    <td><?php echo $ticket[0]['nums'] ?></td>
                                    <?php if($detail['status'] == 'paid'):?>
                                    	<td><input type="text" name="used_nums" style="width:100px"></td>
                                   	<?php elseif($detail['status'] == 'finish'):?>
                                   		<td><?php echo $ticket[0]['used_nums']?></td>
                                    <?php endif;?>
                                    <td><del><?php echo $ticket[0]['price_type'] == 0 ? $ticket[0]['fat_price'] : $ticket[0]['group_price'] ?></del></td>
                                    <td><?php echo $ticket[0]['price']?></td>
                                    <td>
                                        <?php if($detail['status'] == 'finish'):?>
                                            <?php echo number_format($ticket[0]['price']* $ticket[0]['used_nums'],2);?>
                                        <?php else:?> 
                                            <?php echo number_format($ticket[0]['price'] * $ticket[0]['nums'],2);?>
                                        <?php endif;?>
                                </tr>
                            </tbody>
                        </table>
                        </form>
                    </div>
                </div>

                <div class="panel-footer" id="take-ticket-footer">
                <?php if($detail['status'] == 'paid'):?>
                    <p style="color:red">注意事项：</p>
                    <p style="color:red">任务单一经确认,无法再次修改</p>
                    <div style="text-align:center">
                    	<button class="btn btn-default" onclick="finish(<?php echo $detail['id']?>)">确认任务单</button>
                    </div>
                <?php elseif($detail['status'] == 'unpaid'):?>
                	<div style="text-align:center">
                    	<button class="btn btn-warning" disabled="disabled">等待分销商确认</button>
                    </div>
                <?php elseif($detail['status'] == 'cancel'):?>
                	<div style="text-align:center">
                    	<button class="btn btn-warning" disabled="disabled">任务单已取消</button>
                    </div>
                <?php else:?>
                	<div style="text-align:center">
                    	<button class="btn btn-success" disabled="disabled">任务单已结束</button>
                    </div>
                <?php endif;?>
                </div>
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
                                <!--button onclick="againSms('<?php echo $detail['id'] ?>', '<?php echo $detail['owner_mobile'] ?>');" class="btn btn-primary btn-xs ml10" type="button">重发短信</button--></td>
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
        <?php $agencyInfo = Organizations::api()->show(array('id' => intval($ticket[0]['distributor_id']))); ?>  
                                <tr>
                                    <th width="150">公司名称：</th><td><?php echo isset($agencyInfo['body']['name']) ? $agencyInfo['body']['name'] : ''; ?></td>
                                </tr>
                                <tr>
                                    <th>地址：</th><td><?php echo isset($agencyInfo['body']['address']) ? $agencyInfo['body']['address'] : ''; ?></td>
                                </tr>
                                <tr><th>联系人：</th><td><?php echo isset($agencyInfo['body']['contact']) ? $agencyInfo['body']['contact'] : ''; ?></td>
								</tr>
                                <tr><th>联系电话：</th><td><?php echo isset($agencyInfo['body']['mail']) ? $agencyInfo['body']['mail'] : ''; ?></td>
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
        <?php $supplyInfo = Organizations::api()->show(array('id' => $ticket[0]['supplier_id'])); ?> 
                                <tr>
                                    <th width="150">公司名称：</th><td><?php echo isset($supplyInfo['body']['name'])? $supplyInfo['body']['name'] : '' ?></td>
                                </tr>
                                <tr>
                                    <th>地址：</th><td><?php echo isset($supplyInfo['body']['address'])? $supplyInfo['body']['address'] : '' ?></td>
                                </tr>
                                <tr>
                                	<th>联系人：</th><td><?php echo isset($supplyInfo['body']['contact'])? $supplyInfo['body']['contact'] : '' ?></td>
                                </tr>
                                <tr>
                                	<th>联系电话：</th><td><?php echo isset($supplyInfo['body']['mobile'])? $supplyInfo['body']['mobile'] : '' ?></td>
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
                        <textarea placeholder="暂无备注信息" class="form-control" rows="5" maxlength="200" readonly="readonly"><?php echo html_entity_decode($detail['remark']) ?></textarea>
                </div><!-- panel-body -->
            </div>

            <div class="panel panel-default">
                <table class="table table-bordered mb30">
                    <tbody>
                        <tr>
                            <th width="120">购买规定</th>
                            <td>
        <?php echo $ticket[0]['remark'] ?>
                               </td>
                        </tr>
                    </tbody>
                </table>
            </div>	
    <?php endif; ?>
</div><!-- contentpanel -->
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
<script>
    function finish(id) {
    	var nums = "<?php echo $ticket[0]['nums'];?>";
        var used_nums = $('input[name=used_nums]').val();
        var r = /^[0-9]+$/;
        if(r.test(used_nums) == false){
            alert('请输入正确的数字');
            return false;
        }
        if(used_nums == ''){
            alert('实际使用票数不能为空');
            return false;
        }
    	if(Number(used_nums) > Number(nums)){
    		alert('实际使用票数不得多于购买票数');
    		return false;
    	}else{
    		$.post('/order/newdetail/finish', {id: id,used_nums: used_nums}, function(data) {
	            if (data.error === 0) {
	                alert('订单已确认');
	                setTimeout("location.href='/order/renwu/'", '1000');
	            } else {
	                alert(data.msg);
	            }
	        }, 'json')
    	}

    }

</script>

