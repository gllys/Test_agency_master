<?php
use common\huilian\utils\Format;
$this->breadcrumbs = array('订单管理', '订单管理');
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
                
                <!-- 订单时间开始 -->
                <div class="form-group " style="width: 266px;">
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" value="<?php echo isset($get['start_date']) ? $get['start_date'] : '' ?>" placeholder="开始日期" type="text" readonly="readonly"> ~
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker" value="<?php echo isset($get['end_date']) ? $get['end_date'] : '' ?>" placeholder="结束日期" type="text" readonly="readonly">
                </div>
                <!--订单时间结束-->
                
                <!--订单来源开始-->
                <div class="form-group">
                    <select name="source" id="source_link" class="select2" data-placeholder="Choose One" style="width:130px;padding:0 10px;">
                        <option value="">渠道</option>
                        <?php foreach ($source_labels as $source => $label) : ?>
                        <option <?php echo isset($get['source']) && $source == $get['source'] ? 'selected="selectd"' : '' ?> value="<?php echo $source ?>"><?php echo $label ?></option>
                            <?php
                        endforeach;
                        unset($source, $label)
                        ?>
                    </select>
                </div>
                <!--订单来源结束-->
                
                <!--订单状态开始-->
                <div class="form-group">
                    <select name="status" id="status_link" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                        <option value="">订单状态</option>
                        <?php foreach ($status_labels as $status => $label) : ?>
                            <option <?php echo isset($get['status']) && $status == $get['status'] ? 'selected="selectd"' : '' ?> value="<?php echo $status ?>"><?php echo $label ?></option>
                            <?php
                        endforeach;
                        unset($status, $label)
                        ?>
                    </select>
                </div>
                <!--订单状态结束-->


                <div class="form-group">
                    <div class="input-group input-group-sm">
                        <div class="input-group-btn">
                            <button id="search_label" type="button" class="btn btn-default" tabindex="-1"><?php
                                if (isset($get['id']))
                                    echo '订单号';
                                elseif (isset($get['ticket_name']))
                                    echo '门票名称';
                                elseif (isset($get['owner_name']))
                                    echo '取票人';
                                elseif (isset($get['owner_mobile']))
                                    echo '手机号';
                                elseif (isset($get['owner_card']))
                                    echo '身份证';
                                else
                                    echo '订单号';
                                ?></button>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    tabindex="-1">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a class="sec-btn" href="javascript:;" data-id="id" id="">订单号</a></li>
                                <li><a class="sec-btn" href="javascript:;" data-id="ticket_name" id="">门票名称</a></li>
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
                        elseif (isset($get['ticket_name']))
                            echo 'ticket_name';
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
                               elseif (isset($get['ticket_name']))
                                   echo $get['ticket_name'];
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
                <!-- input-group -->

                <!--查询-->
                <div class="form-group">
                    <input type="hidden" name="is_export" class="is_export" value="0">
                    <button class="btn btn-primary btn-xs" type="submit">查询</button>
                    <button class="btn btn-primary btn-xs" type="button" id="export">导出</button>
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
        <div id="t1" class="tab-pane active">
            <form action="/order/payments/method/" method="post">
                <div>
                    <?php if (!isset($get['status']) || $get['status'] == 'unpaid' || $get['status'] == 'all') : ?>
                        <button id="btn-combine-pay" class="btn btn-xs" style="margin-bottom: 15px">合并支付</button>
                        <script>
                            jQuery(document).ready(function() {
                                $('#btn-combine-pay').click(function() {
                                    return $('.need-to-pay:checked').length != 0;
                                });
                            });
                        </script>
                    <?php endif; ?>
                </div>
                
				<div style="overflow:auto;max-height:400px;margin-right: -15px;">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th style="width:10%;">
                    <div class="ckbox ckbox-primary" style="margin-left:17px;">
                        <input type="checkbox" id="checkbox-allcheck"  value="">
                        <label for="checkbox-allcheck" class="allcheck">全选</label>
                    </div>
                    </th>
                    <th style="width:12%;">订单号</th>
                    <th style="width:10%;">供应商</th>
                    <th style="width:10%;">门票名称</th>
                    <th style="width:8%;">来源</th>
                    <th style="width:8%;">取票人</th>
                    <th style="width:10%;">手机号</th>
                    <th style="width:8%;">预订日期</th>
                    <th style="width:8%;">游玩日期</th>
                    <th style="width:8%;">入园日期</th>
                    <th style="width:4%;">票数</th>
                    <th style="width:7%;">支付金额</th>
                    <th style="width:100px;">订单状态</th>
                    </tr>
                    </thead>
					<tbody id="staff-body">
                        <?php
                        if (isset($lists['data'])) : foreach ($lists['data'] as $order) :
                                $can_pay = strtotime($order['use_day'] . ' 10:00:00') >= strtotime('10:00:00');
                                ?>
                                <tr>
                                    <td style="text-align: center;width:10%;">
                                        <div class="ckbox ckbox-primary" style="margin-left: 17px;">
                                            <input <?php
                                            $_status = (bool) ($can_pay && $order['status'] == 'unpaid' && (int) $order['payment_id'] == 0);
                                            if ($_status) {
                                                echo 'class="need-to-pay" name="combine[]" value="' . $order['id'] . '"';
                                            } else {
                                                echo 'disabled="disabled" style="display:none"';
                                            }
                                            ?> type="checkbox" id="checkbox<?php echo $order['id'] ?>" />
                                            <label for="checkbox<?php echo $order['id'] ?>" <?php if (!$_status): ?>style="display:none;"<?php endif ?>></label>
                                        </div>
                                    </td>
                                  
                                    <td  style="width:12%;text-align: right;"><a href="/order/detail/index/id/<?php echo $order['id'] ?>"><?php echo $order['id'] ?></a></td>
                                    <td style="width:10%;"><?php echo $order['supplier_name'] ?></td>
                                    <td style="width:10%;text-align: left;<?php echo $can_pay ? '' : 'color:gray' ?>"><?php echo $order['name'] ?></td>
                                    <td  style="width:8%">
                                        <?php
                                            echo '<span>';
                                            echo is_numeric($order['source']) ? $source_labels[$order['source']] : '';
                                            echo '</span>';
                                        ?>
                                    </td>
                                    <td style="width:8%;"><?php echo $order['owner_name'] ?></td>
                                    <td style="width:10%;"><?php echo $order['owner_mobile'] ?></td>
                                    <td style="width:8%;"><?php echo Format::date($order['created_at']) ?></td>
                                    <td style="width:8%;"><?php echo $order['use_day'] ?></td>
                                    <td style="width:8%;"><?php echo $order['used_nums'] ? Format::date($order['updated_at']) : '' ?></td>
                                    <td  style="width:4%;"><?php echo $order['nums'] ?></td>
                                    <td style="width:5%;text-align: right"><?php echo number_format($order['amount'], 2) ?></td>
                                    <td   style="width:100px;" class="text-<?php echo $status_class[$order['status']] ?>">
                                        <?php
                                        if ($order['status'] == 'unaudited') {
                                            echo '<span>';
                                            echo $status_labels[$order['status']];
                                            echo '</span>';
                                        } else {
                                            echo '<span>';
                                            echo $order['status'] == 'unpaid' && (int) $order['payment_id'] > 0 ? '' : $status_labels[$order['status']];
                                            echo '</span>';
                                        }
                                        if ($order['status'] == 'unpaid' && $can_pay) {
                                            if ((int) $order['payment_id'] == 0) {
                                                ?>
                                                <a href="/order/payments/method/combine/<?php echo $order['id'] ?>" class="btn btn-xs btn-success" style="width: 68px">去支付</a>
                                                <?php
                                            } else {
                                                ?>
                                                <a href="/order/payments/cancel/pid/<?php echo $order['payment_id'] ?>" class="btn btn-xs">取消</a>
                                                <a href="/order/payments/method/pid/<?php echo $order['payment_id'] ?>" class="btn btn-xs btn-danger" title="支付中">继续支付</a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
				</div>
            </form>
            
            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
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
                            )
                    );}
                    ?>
                </div>
            </div>
            <!-- tab-pane -->

            <div id="t2" class="tab-pane">


            </div>
            <!-- tab-pane -->
           
        </div>
           <!-- <div class="panel-footer">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="pagenumQu">
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
                </div>
            </div>-->
    </div><!-- contentpanel -->
</div>
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
            $('.form-inline').submit();
            $('.is_export').attr('value', '0');
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


            $('.allcheck').click(function() {
                if ($(this).text() == '全选') {
                    $('#staff-body').find('input').prop('checked', true)
                    $(this).text('反选')
                } else {
                    $('#staff-body').find('input').prop('checked', false)
                    $(this).text('全选')
                }
                ;

            });

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
            jQuery('.datepicker').datepicker({showOtherMonths: true, selectOtherMonths: true});
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
            jQuery('.select2').select2({
                minimumResultsForSearch: -1
            });
             jQuery("#select-basic, #select-multi,#status_link").select2();

            function format(item) {
                return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel')) + ' mr10"></i>' + item.text;
            }

            // This will empty first option in select to enable placeholder
            //jQuery('select option:first-child').text('');

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

