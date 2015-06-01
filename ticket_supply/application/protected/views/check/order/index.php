<?php

use common\huilian\utils\Format;

$this->breadcrumbs = array('订单', '订单管理');
?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
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
            <h4 class="panel-title">查询</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/check/order/">
                <div class="form-group">
                    <select name="time_type" class="select2" data-placeholder="Choose One" style="width:130px;padding:0 10px;">
                        <?php foreach ($timeTypes as $k => $v) { ?>
                            <option value="<?= $k ?>"<?= $k == $time_type ? ' selected' : '' ?>><?= $v ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group " style="width: 280px;">
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['start_date']) ? $get['start_date'] : ''; ?>" placeholder="开始日期"> ~
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['end_date']) ? $get['end_date'] : '' ?>" placeholder="结束日期">
                </div>
                <!-- form-group -->

                <!--div class="form-group" style="margin:0">
                        <select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                                <option value="">订单类型</option>
                        </select>
                </div-->
                <div class="form-group">
                    <select name="status" id="status_link" class="select2" data-placeholder="订单状态" style="width:150px;">
                        <option value="">订单状态</option>
                        <?php foreach ($status_labels as $status => $label) : ?>
                            <option <?php echo isset($get['status']) && $status == $get['status'] ? 'selected="selectd"' : '' ?> value="<?php echo $status ?>"><?php echo $label ?></option>
                            <?php
                        endforeach;
                        unset($status, $label)
                        ?>
                    </select>
                    <script>
//                            $('#status_link').change(function() {
//                                location.href = '/#'+ '/order/history/view/status/' + $(this).val();
//                            });
                    </script>
                </div>

                <div class="form-group">
                    <div class="input-group input-group-sm" style=" position: relative;">
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
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="id" id="">订单号</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="product_name" id="" aria-labelledby="search_label">门票名称</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="owner_name" id="">取票人</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="owner_mobile" id="">手机号</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="owner_card" id="">身份证</a></li>
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
                    <input type="hidden" name="is_export" class="is_export" value="0" />
                    <button class="btn btn-primary btn-sm" type="submit">查询</button>
                    <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
                </div>
            </form>
        </div>

    </div>
    <!-- panel-body -->
</div>


<ul class="nav nav-tabs">
    <?php
    $_status_labels = array('all' => '全部订单');
    if (!isset($get['status'])) {
        $get['status'] = 'all';
    }
    foreach ($_status_labels as $status => $label) :
        ?>
        <li class="<?php echo isset($get['status']) && $status == $get['status'] ? 'active' : '' ?>">
            <a href="/check/order/view/status/<?php echo $status ?>"><strong><?php echo $label ?></strong></a>
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
            <table class="table table-bordered mb30">
                <thead>
                    <tr>
                        <th style="width:120px;">订单号</th>
                        <th>门票名称</th>
                        <th>取票人</th>
                        <th>取票人手机号</th>
                        <th>预订日期</th>
                        <th>游玩日期</th>
                        <th>入园日期</th>
                        <th>预定票数</th>
                        <th>未使用票数</th>
                        <th>已使用票数</th>
                        <th>支付类型</th>
<!--                            <th>支付金额</th>-->
                        <th>订单状态</th>
                        <th>分销商</th>
                    </tr>
                </thead>
                <tbody>
<?php if (isset($lists['data'])) : foreach ($lists['data'] as $order) : ?>
                            <tr>
                                <td><a href="/check/order/detail/?id=<?php echo $order['id'] ?>"><?php echo (!empty($order['id'])) ? substr_replace($order['id'], "********", strlen($order['id']) / 2 - 4, 8) : $order['id']; ?></a></td>
                                <td style="text-align: left"><?php echo $order['name'] ?></td>
                                <td><?php echo $order['owner_name'] ?></td>
                                <td><?php echo (!empty($order['owner_mobile'])) ? substr_replace($order['owner_mobile'], "****", strlen($order['owner_mobile']) / 2 - 2, 4) : $order['owner_mobile']; ?></td>
                                <td><?php echo Format::date($order['created_at']) ?></td>
                                <td><?php echo $order['use_day'] ?></td>
                                <td><?php echo $order['used_nums'] ? Format::date($order['updated_at']) : '' ?></td>
                                <td style="text-align: center"><?php echo $order['nums'] ?></td>
                                <td><?php echo $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'] ?></td>
                                <td style="text-align: center"><?php echo $order['used_nums'] ?></td>
                                <td><?php echo empty($payTypes[$order['payment']]) ? '' : $payTypes[$order['payment']] ?></td>
        <!--                                    <td style="text-align: right">--><?php //echo number_format($order['amount'], 2)  ?><!--</td>-->
                                <td class="text-<?php echo isset($status_class[$order['status']]) ? $status_class[$order['status']] : ''; ?>"><?php
                                    echo '<span>';
                                    echo isset($status_labels[$order['status']]) ? $status_labels[$order['status']] : '';
                                    echo '</span>';
                                    ?>
                                </td>
                                <td><?php echo $order['distributor_name'] ?></td>
                            </tr>
                        <?php endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>

        <div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
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
//    
                    ?><!--&nbsp;&nbsp;&nbsp;-->
                    <!--                        总金额：-->
                    <!--    --><?php
//    $total_amount = intval(isset($lists['statics']['total_amount']) ? $lists['statics']['total_amount'] : "0");
//    $total_refunded = intval(isset($lists['statics']['total_refunded']) ? $lists['statics']['total_refunded'] : "0");
//    echo $total_amount - $total_refunded;
//    
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
            if ($('#start_date').val() == '')
            {
                $('#start_date').PWShowPrompt('请选择开始日期');
                return false;
            }
            if ($('#end_date').val() == '')
            {
                $('#end_date').PWShowPrompt('请选择结束日期');
                return false;
            }
            $('.is_export').attr('value', '1');
			$('form').addClass('clearPart');
            $('form').submit();
			$('form').removeClass('clearPart');
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
        $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
        yearRange: "1995:2065",
		beforeShow: function(d){
			setTimeout(function(){
				$('.ui-datepicker-title select').select2({
					minimumResultsForSearch: -1
				});
			},0)
		},
		onChangeMonthYear: function(){
			setTimeout(function(){
				$('.ui-datepicker-title select').select2({
					minimumResultsForSearch: -1
				});
			},0)
		},
        onClose: function(dateText, inst) { 
            $('.select2-drop').hide(); 
        }
    });
    });
</script>

