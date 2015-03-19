<?php

use common\huilian\utils\Format;

$this->breadcrumbs = array('订单', '订单管理');
?>
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href=""
                   data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href=""
                   data-original-title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <h4 class="panel-title">订单管理</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/order/history/view">
                <div class="form-group">
                    <select name="time_type" class="select2" data-placeholder="Choose One" style="width:130px;padding:0 10px;">
                        <?php foreach ($timeTypes as $k => $v) { ?>
                            <option value="<?= $k ?>"<?= $k == $time_type ? ' selected' : '' ?>><?= $v ?></option>
                        <?php } ?>
                    </select>
                </div>

                 <div class="form-group " style="width: 335px;">
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['start_date']) ? $get['start_date'] : ''; ?>"> ~
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['end_date']) ? $get['end_date'] : '' ?>">
                </div>
                <!-- form-group -->

                <!--div class="form-group" style="margin:0">
                        <select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                                <option value="">订单类型</option>
                        </select>
                </div-->
                <div class="form-group">
                    <select name="status" id="status_link" class="select2" data-placeholder="订单状态" style="width:150px;padding:0 10px;">
                        <option value="">订单状态</option>
                        <?php foreach ($status_labels as $status => $label) : ?>
                            <option <?php echo isset($get['status']) && $status == $get['status'] ? 'selected="selectd"' : '' ?> value="<?php echo $status ?>"><?php echo $label ?></option>
                        <?php endforeach;
                        unset($status, $label)
                        ?>
                    </select>
                    <script>
//                            $('#status_link').change(function() {
//                                location.href = '/order/history/view/status/' + $(this).val();
//                            });
                    </script>
                </div>
                <div class="form-group">
                    <select name="landscape_id" class="select2" data-placeholder="景区" style="width:150px;padding:0 10px;">
                        <option  value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;景区&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     </option>
                        <?php foreach ($landscape_labels as $landscape => $label) : ?>
                            <option <?php echo isset($get['landscape_id']) && $landscape == $get['landscape_id'] ? 'selected="selectd"' : '' ?> value="<?php echo $landscape ?>"><?php echo $label ?></option>
                        <?php endforeach;
                        unset($landscape, $label)
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="distributor_id" class="select2" data-placeholder="分销商" style="width:150px;padding:0 10px;">
                        <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分销商&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                        <?php foreach ($distributors_labels as $distributor => $label) : ?>
                            <option <?php echo isset($get['distributor_id']) && $distributor == $get['distributor_id'] ? 'selected="selectd"' : '' ?> value="<?php echo $distributor ?>"><?php echo $label ?></option>
						<?php endforeach;
						unset($distributor, $label)
						?>
                    </select>
                </div>

                <div class="form-group">
                    <div class="input-group input-group-sm" style=" position: relative; top: -2px;">
                        <div class="input-group-btn">
                            <button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                                <?php
                                if (isset($get['id']))
                                    echo '订单号';
                                elseif (isset($get['product_name']))
                                    echo '门票名称';
                                elseif (isset($get['owner_name']))
                                    echo '取票人';
                                elseif (isset($get['owner_mobile']))
                                    echo '手机号';
                                elseif (isset($get['owner_card']))
                                    echo '身份证';
                                else
                                    echo '订单号';
                                ?>
                            </button>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    tabindex="-1">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a class="sec-btn" href="javascript:;" data-id="id" id="">订单号</a></li>
                                <li><a class="sec-btn" href="javascript:;" data-id="product_name" id="" aria-labelledby="search_label">门票名称</a></li>
                                <li><a class="sec-btn" href="javascript:;" data-id="owner_name" id="">取票人</a></li>
                                <li><a class="sec-btn" href="javascript:;" data-id="owner_mobile" id="">手机号</a></li>
                                <li><a class="sec-btn" href="javascript:;" data-id="owner_card" id="">身份证</a></li>
                            </ul>
                            <script>
                                $('.sec-btn').click(function() {
                                    $('#search_label').text($(this).text());
                                    $('#search_field').attr('name', $(this).attr('data-id'));
                                });
                            </script>


                        </div>
                        <!-- input-group-btn -->
                        <input id="search_field" name="<?php
                        if (isset($get['id']))
                            echo 'id';
                        elseif (isset($get['product_name']))
                            echo 'product_name';
                        elseif (isset($get['owner_name']))
                            echo 'owner_name';
                        elseif (isset($get['owner_mobile']))
                            echo 'owner_mobile';
                        elseif (isset($get['owner_card']))
                            echo 'owner_card';
                        else
                            echo 'id';
                        ?>" value="<?php
                               if (isset($get['id']))
                                   echo $get['id'];
                               elseif (isset($get['product_name']))
                                   echo $get['product_name'];
                               elseif (isset($get['owner_name']))
                                   echo $get['owner_name'];
                               elseif (isset($get['owner_mobile']))
                                   echo $get['owner_mobile'];
                               elseif (isset($get['owner_card']))
                                   echo $get['owner_card'];
                               else
                                   echo '';
                               ?>" type="text" class="form-control" style="z-index: 0"/>
                    </div>
                </div>
				<div class="form-group">
                    <input type="hidden" name="is_export" class="is_export" value="0">
					<button class="btn btn-primary btn-sm" type="submit">查询</button>
					<button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
				</div>
			</form>
        </div>

    </div>
    <!-- panel-body -->


<ul class="nav nav-tabs">
    <?php
    $status_labels = array_merge(array('all' => '全部'), $status_labels);
    if (!isset($get['status'])) {
        $get['status'] = 'all';
    }
    foreach ($status_labels as $status => $label) :
        ?>
        <li class="<?php echo isset($get['status']) && $status == $get['status'] ? 'active' : '' ?>">
            <a href="/order/history/view/status/<?php echo $status ?>"><strong><?php echo $label ?></strong></a>
        </li>
<?php
endforeach;
unset($status, $label)
?>
</ul>

<div class="tab-content mb30">
	<style>
		.tab-content .table tr > * {
			text-align: center
		}
		.tab-content .ckbox {
			display: inline-block;
			width: 30px;
			text-align: left
		}
	</style>
    <div id="t1" class="tab-pane active">
        <div class="table-scrollable">
			<table class="table table-bordered" style="border-bottom:0">
				<thead>
					<tr>
						<th style="width:12%">订单号</th>
						<th style="width:6%">门票名称</th>
						<th style="width:5%">取票人</th>
						<th style="width:9%">取票人手机号</th>
						<th style="width:9%">预订日期</th>
						<th style="width:9%">游玩日期</th>
						<th style="width:9%">入园日期</th>
						<th style="width:6%">预定票数</th>
						<th style="width:7%">未使用票数</th>
						<th style="width:7%">已使用票数</th>
						<th style="width:6%">支付类型</th>
						<th style="width:6%">支付金额</th>
						<th style="width:6%">订单状态</th>
						<th style="width:6%">景区</th>
						<th style="width:6%">分销商</th>
					</tr>
				</thead>
			</table>
			<div style="overflow:auto;max-height:400px;">
			<table class="table table-bordered mb30">                
				<tbody>
					<?php if (isset($lists['data'])) : foreach ($lists['data'] as $order) : ?>
                            <tr>
								<td style="width:12%"><a href="/order/detail/?id=<?php echo $order['id'] ?>"><?php echo $order['id']; ?></td>
								<td style="width:6%"><?php echo $order['name'] ?></td>
								<td style="width:5%"><?php echo $order['owner_name'] ?></td>
								<td style="width:9%"><?php echo $order['owner_mobile']; ?></td>
								<td style="width:9%"><?php echo Format::date($order['created_at']) ?></td>
								<td style="width:9%"><?php echo $order['use_day'] ?></td>
								<td style="width:9%"><?php echo $order['used_nums'] ? Format::date($order['updated_at']) : '' ?></td>
								<td style="width:6%"><?php echo $order['nums'] ?></td>
								<td style="width:7%"><?php echo $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'] ?></td>
								<td style="width:7%"><?php echo $order['used_nums'] ?></td>
								<td style="width:6%"><?php echo empty($payTypes[$order['pay_type']]) ? '' : $payTypes[$order['pay_type']] ?></td>
								<td style="width:6%"><?php echo number_format($order['amount'], 2) ?></td>
								<td style="width:6%" class="text-<?php echo $status_class[$order['status']] ?>"><span><?php echo $status_labels[$order['status']];?></span></td>
								<td style="width:6%"><?php
                                    $landscapeArr = explode(',', $order['landscape_ids']);
                                    foreach ($landscapeArr as $landscapeId) {
                                        echo (isset($landscape_labels[$landscapeId]) ? $landscape_labels[$landscapeId] : "") . " ";
                                    }?> </td>
								<td style="width:6%"><?php echo $order['distributor_name'] ?></td>
                            </tr>
						<?php 
						endforeach;
						endif;
						?>
                </tbody>
            </table>
        </div>

        <div class="panel-footer" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
                <?php if (isset($lists)) { ?>
                <div style="height: 40px;">
                    订单数：
                    <?php
                    echo isset($lists['statics']['order_nums']) ? $lists['statics']['order_nums'] : 0;
                    ?>&nbsp;&nbsp;
                    总人次：
                    <?php
                    $total_nums = intval(isset($lists['statics']['total_nums']) ? $lists['statics']['total_nums'] : "0");
                    $total_refunded_nums = intval(isset($lists['statics']['total_refunded_nums']) ? $lists['statics']['total_refunded_nums'] : "0");
                    echo $total_nums - $total_refunded_nums;
                    ?>&nbsp;&nbsp;
                    使用人次：
                    <?php
                    echo isset($lists['statics']['total_used_nums']) ? $lists['statics']['total_used_nums'] : 0;
                    ?>&nbsp;&nbsp;&nbsp;
                    总金额：
                <?php
                $total_amount = intval(isset($lists['statics']['total_amount']) ? $lists['statics']['total_amount'] : "0");
                $total_refunded = intval(isset($lists['statics']['total_refunded']) ? $lists['statics']['total_refunded'] : "0");
                echo $total_amount - $total_refunded;
                ?>
                </div>
            <?php } ?>
            <?php
            if (isset($lists['data'])) {
                $this->widget('common.widgets.pagers.ULinkPager', array(
                    'cssFile' => '',
                    'header' => '',
                    'prevPageLabel' => '上一页',
                    'nextPageLabel' => '下一页',
                    'firstPageLabel' => '',
                    'lastPageLabel' => '',
                    'pages' => $pages,
                    'maxButtonCount' => 5, //分页数量
                ));
            }
            ?>
        </div>
    </div>
    <!-- tab-pane -->

    <div id="t2" class="tab-pane">


    </div>
    <!-- tab-pane -->

</div>


</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function() {
        $('#export').click(function() {
		if($('#start_date').val()=='')
                {
                    $('#start_date').PWShowPrompt('请选择开始日期');
                    return false;
                }
                if($('#end_date').val()=='')
                {
                    $('#end_date').PWShowPrompt('请选择结束日期');
                    return false;
                }
            $('.is_export').attr('value', '1');
            $('form').submit();
            $('.is_export').attr('value', '0');
        });
        // Tags Input
        jQuery('#tags').tagsInput({width: 'auto'});

// Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();

// Spinner
        var spinner = jQuery('#spinner').spinner();
        spinner.spinner('value', 0);

// Form Toggles
        jQuery('.toggle').toggles({on: true});

// Time Picker
        jQuery('#timepicker').timepicker({defaultTIme: false});
        jQuery('#timepicker2').timepicker({showMeridian: false});
        jQuery('#timepicker3').timepicker({minuteStep: 15});

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
// jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });

        $('[name=status],[name=landscape_id],[name=distributor_id]').select2();
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
        $('#all-btn').click(function() {
            var obj = $(this).parents('table')
            if ($(this).is(':checked')) {
                obj.find('input').prop('checked', true)
                $(this).text('反选')
            } else {
                obj.find('input').prop('checked', false)
                $(this).text('全选')
            }
        });
        jQuery('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>

