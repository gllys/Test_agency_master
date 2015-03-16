<?php
$this->breadcrumbs = array('订单管理', '退票查询');
?>
<!--div class="pageheader">
    <div class="media">
        <div class="pageicon pull-left">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="media-body">
            <ul class="breadcrumb">
                <li><a href="#"><i class="glyphicon glyphicon-home"></i></a></li>
                <li><a href="#">订单管理</a></li>
            </ul>
        </div>
    </div><media>
</div><pageheader -->

<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
            </div><!-- panel-btns -->
            <h4 class="panel-title">退票查询</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" action="" method="get">
                <div class="mb20">
                    <div class="form-group" style="margin:0">
                        <input class="form-control datepicker" style="cursor: pointer;cursor: hand;background-color: #ffffff" readonly placeholder="交易日期" type="text" name="createdat[]" value="<?php echo isset($getval['createdat'][0])?$getval['createdat'][0]:'';?>"> ~
                        <input class="form-control datepicker" style="cursor: pointer;cursor: hand;background-color: #ffffff" readonly placeholder="交易日期" type="text" name="createdat[]" value="<?php echo isset($getval['createdat'][1])?$getval['createdat'][1]:'';?>">
                    </div><!-- form-group -->

                    <div class="form-group" style="margin:0">
                        <select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;" name="pay_app_id">
                            <option value="">支付方式</option>
                            <!--option value="cash" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='cash'?"selected":''):'';?>>现金</option>
                            <option value="offline" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='offline'?"selected":''):'';?>>线下</option-->
                            <option value="credit" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='credit'?"selected":''):'';?>>信用支付</option>
                            <!--option value="pos" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='pos'?"selected":''):'';?>>pos机</option>
                            <option value="alipay" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='alipay'?"selected":''):'';?>>支付宝</option-->
                            <option value="advance" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='advance'?"selected":''):'';?>>储值支付</option>
                            <option value="union" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='union'?"selected":''):'';?>>平台支付</option>
                            <option value="kuaiqian" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='kuaiqian'?"selected":''):'';?>>快钱</option>
                            <!--option value="taobao" <?php echo isset($getval['pay_app_id'])?($getval['pay_app_id']=='taobao'?"selected":''):'';?>>淘宝支付</option-->
                        </select>
                    </div>
                    <div class="form-group" style="margin:0">
                        <select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;" name="status">
                            <option value="">退款状态</option>
                             <option value="0" <?php echo isset($getval['status'])?($getval['status']=='0'?"selected":''):'';?>>退款中</option>
                              <option value="1" <?php echo isset($getval['status'])?($getval['status']=='1'?"selected":''):'';?>>退款成功</option>
                               <option value="2" <?php echo isset($getval['status'])?($getval['status']=='2'?"selected":''):'';?>>退款失败</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="form-group" style="margin: 0 5px 0 0">
                        <input class="form-control" placeholder="订单号"type="text" style="width:320px;" name="order_id">
                    </div>
                    <button class="btn btn-primary btn-xs" type="submit">查询</button>
                </div>
            </form>
        </div><!-- panel-body -->
    </div>
    <table class="table table-bordered mb30">
        <thead>
            <tr>
                <th>订单号</th>
                <th>门票名称</th>
                <th>申请时间</th>
                <th>票数</th>
                <th>金额</th>
                <th>支付方式</th>
                <th>退款状态</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($list as $key => $item):
                ?>
            <tr data-target=".bs-example-modal-static"  onclick="point('<?php echo $item['order_id'];?>','<?php echo $item['id'];?>')"data-toggle="modal">
                    <td><a href="#"><?php echo $item['order_id'] ?></a></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo date("Y-m-d", $item['created_at']); ?></td>
                    <td><?php echo $item['nums']; ?></td>
                    <td><?php echo $item['money']; ?></td>
                    <td ><?php 
                    switch ($item['pay_app_id']){
                        case 'cash':echo '现金';break;
                        case 'offline':echo '先下';break;
                        case 'credit':echo '信用支付';break;
                        case 'pos':echo 'pos机';break;
                        case 'alipay':echo '支付宝';break;
                        case 'advance':echo '储值支付';break;
                        case 'union':echo '平台支付';break;
                        case 'kuaiqian':echo '快钱';break;
                        case 'taobao':echo '淘宝支付';break;
                    }
                    ?></td>
                    <td><?php echo $item['status']==0 ? "退款中" : ($item['status']==1 ?'退款成功':'退款失败'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<div style="text-align:center" class="panel-footer">
    <div id="basicTable_paginate" class="pagenumQu">
        <?php
        $this->widget('CLinkPager', array(
            'cssFile' => '',
            'header' => '',
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页',
            'firstPageLabel' => '',
            'lastPageLabel' => '',
            'pages' => $pages,
            'maxButtonCount' => 5, //分页数量
                )
        );
        ?>
    </div>
</div>
 <div class="modal fade bs-example-modal-static" id="verify-modal-point" tabindex="-1" role="dialog"></div>


<script>
function point(pointid,id){
   // alert(111)
         $('#verify-modal-point').html();
        $.get('/order/refund/point/?id='+id+'&order_id='+pointid, function(data) {
            $('#verify-modal-point').html(data);
        });
}
</script>

<script>
    jQuery(document).ready(function() {
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
