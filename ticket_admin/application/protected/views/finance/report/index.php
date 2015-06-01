<?php

use common\huilian\utils\Format;

$this->breadcrumbs = array('财务管理', '交易报表');
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
            <h4 class="panel-title">交易报表</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/finance/report/view">

                <div class="form-group " style="width: 380px; top: 2px;">
                    <label>查询日期：</label>
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['start_date']) ? $get['start_date'] : ''; ?>"> ~
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['end_date']) ? $get['end_date'] : '' ?>">
                </div>
                <!-- form-group -->

                <!--div class="form-group" style="margin:0">
                        <select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                                <option value="">订单类型</option>
                        </select>
                </div-->
                <div class="form-group"  style="width: 250px; top: 2px;">
                    <label>报表类型：</label>
                    <label style="width:150px;">
                    <select name="report_type" id="status_link" class="select2" data-placeholder="订单状态" >
                        
                        <?php 
                        print_r($status_labels);
                        //exit;
                        $type_labels=array("全部","收入报表","退款报表");
                        foreach ($type_labels as $type => $label) : 
                        ?>
                            <option <?php echo isset($get['report_type']) && $type == $get['report_type'] ? 'selected="selectd"' : '' ?> value="<?php echo $type ?>"><?php echo $label ?></option>
                        <?php
                        endforeach;
                        unset($type, $label)
                        ?>
                    </select>
                    </label>
                </div>
                <div class="form-group"  style="width: 250px; top: 2px;">
                    <label>支付方式：</label>
                    <label style="width:150px;">
                    <select name="pay_type" id="status_link" class="select2" data-placeholder="支付方式" style="width:150px;padding:0 10px;">
                        <?php
                        $pay_labels=array_merge(array("全部"),$payTypes);
                        
                        foreach ($pay_labels as $pay => $label): 
                            if($label=="线下" || $label=="签单") continue;
                        ?>
                            <option <?php echo isset($get['pay_type']) && $pay == $get['pay_type'] ? 'selected="selectd"' : '' ?> value="<?php echo $pay ?>"><?php echo $label ?></option>
                        <?php
                        endforeach;
                        unset($pay, $label)
                        ?>
                    </select>
                    </label>
                </div>

                <div class="form-group">
                    <div class="input-group input-group-sm" style=" position: relative;">
                        <div class="input-group-btn">
                            <button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                                <?php
                                if (isset($get['id']))
                                    echo '订单号';
                                elseif (isset($get['landscape_name']))
                                    echo '景区';
                                elseif (isset($get['product_name']))
                                    echo '门票名称';
                                elseif (isset($get['distributor_name']))
                                    echo '分销商';
                                elseif (isset($get['supplier_name']))
                                    echo '供应商';
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
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="landscape_name" id="">景区</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="product_name" id="" aria-labelledby="search_label">门票名称</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="distributor_name" id="">分销商</a></li>
                                <li><a class="sec-btn clearPart" href="javascript:;" data-id="supplier_name" id="">供应商</a></li>
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
                        elseif (isset($get['landscape_name']))
                            echo 'landscape_name';
                        elseif (isset($get['product_name']))
                            echo 'product_name';
                        elseif (isset($get['distributor_name']))
                            echo 'distributor_name';
                        elseif (isset($get['supplier_name']))
                            echo 'supplier_name';
                        else
                            echo 'id';
                        ?>" value="<?php
                               if (isset($get['id']))
                                   echo $get['id'];
                               elseif (isset($get['product_name']))
                                   echo $get['product_name'];
                               elseif (isset($get['landscape_name']))
                                   echo $get['landscape_name'];
                               elseif (isset($get['distributor_name']))
                                   echo $get['distributor_name'];
                               elseif (isset($get['supplier_name']))
                                   echo $get['supplier_name'];
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
    <ul class="nav nav-tabs"></ul>
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
        html{
            width:2100px;
        }
        </style>
        <div id="t1" class="tab-pane active">
            <div class="table-scrollable">
                <table class="table table-bordered" style="min-width: 1800px;border-bottom:0">
                    <thead>
                        <tr>
                            <th style="width:9%">订单号</th>
                            <th style="width:7%">分销商名称</th>
                            <th style="width:7%">门票名称</th>
                            <th style="width:7%">所属景区</th>
                            <th style="width:7%">供应商名称</th>
                            <th style="width:5%">预订时间</th>
                            <th style="width:5%">支付时间</th>
                            <th style="width:5%">游玩时间</th>
                            <th style="width:4%">张数</th>
                            <th style="width:5%">单价</th>
                            <th style="width:5%">结算金额</th>
                            <th style="width:5%">订单类型</th>
                            <th style="width:5%">订单类别</th>
                            <th style="width:5%">支付方式</th>
                            <th style="width:5%">支付金额</th>
                            <th style="width:5%">手续费</th>
                            <th style="width:5%">退款金额</th>
                            <th style="width:5%">订单状态</th>
                        </tr>
                    </thead>
                </table>
                <div style="overflow:auto;max-height:400px;margin-right: -17px;">
                    <table class="table table-bordered mb30" style="min-width: 1800px;">                
                        <tbody>
<?php if (isset($lists['data'])) : foreach ($lists['data'] as $order) : 
    ?>
                                    <tr>
                                        <?php
                                       // print_r($order);
                                      //  exit;
                                        ?>
                                        <td style="width:9%"><?php echo $order['id']; ?></td>
                                        <td style="width:7%"><?php echo $order['distributor_name'];//分销商名称 ?></td>
                                        <td style="width:7%"><?php echo $order['name'];//门票名称 ?></td>
                                        <td style="width:7%"><?php
                                            $landscapeArr = explode(',', $order['landscape_ids']);
                                            foreach ($landscapeArr as $landscapeId) {
                                                echo (isset($landscape_labels[$landscapeId]) ? $landscape_labels[$landscapeId] : "") . " ";
                                            }//景区
                                            ?> </td>
                                        <td style="width:7%"><?php echo $order['supplier_name'];//供应商名称 ?></td>
                                        <td style="width:5%"><?php echo Format::date($order['created_at']);//生成时间 ?></td>
                                        <td style="width:5%"><?php echo $order['pay_at']>0?Format::date($order['pay_at']):"";//支付时间 ?></td>
                                        
                                        <td style="width:5%"><?php echo $order['use_day'];//游玩时间 ?></td>
                                        <td style="width:4%"><?php echo $order['nums'];//张数 ?></td>
                                        <td style="width:5%"><?php echo $order['price'];//单价 ?></td>
                                        <td style="width:5%"><?php echo $order['amount'];//结算金额 ?></td>
                                        <td style="width:5%"><?php echo $order_types[$order['type']];//订单类型 ?></td>
                                        <td style="width:5%"><?php echo $order_kind_types[$order['kind']];//订单类别 ?></td>
                                        <td style="width:5%"><?php echo empty($payTypes[$order['pay_type']]) ? '' : $payTypes[$order['pay_type']];//支付方式 ?></td>
                                        <td style="width:5%"><?php echo $order['payed'];//支付金额 ?></td>
                                        
                                        <td style="width:5%"><?php echo Format::money($order['pay_rate']*$order['payed'],1); //手续费?></td>
                                        <td style="width:5%"><?php echo $order['refunded'];//退款金额 ?></td>
                                        <td style="width:5%" class="text-<?php echo $status_class[$order['status']] ?>"><span><?php echo $status_labels[$order['status']]; //订单状态?></span></td>
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
    });
</script>

