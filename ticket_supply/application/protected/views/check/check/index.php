<?php
$this->breadcrumbs = array(
    '验票',
    '验票记录'
);
?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a> <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <ul class="list-inline">
                <li><h4 class="panel-title">验票记录</h4></li>
                <li><a href="/order/history/help?#5.3" title="帮助文档" class="clearPart" target="_blank">查看帮助文档</a> </li>
            </ul>
            <div class="inline-block" style="float:right; margin-top: -25px;">
                <a class="btn btn-primary btn-xs clearPart" onclick="setPrinter();
                        return false;">
                    <i class="fa fa-print"></i>
                    打印设置
                </a>
            </div>
            <div class="inline-block" style="float:right; margin-top: -25px; margin-right: 10px;">
                <a class="btn btn-primary btn-xs clearPart"  href="/check/check/setsimpleticket/" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">
                   小票设置
                </a>
            </div>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/check/check/index">
                <div class="mb10">
                    <div class="form-group" style="margin: 0">
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="begin_date" value="<?php if (isset($_GET['begin_date'])) echo $_GET['begin_date'] ?>" placeholder="开始日期" type="text" readonly="readonly">
                    ~
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="end_date" value="<?php if (isset($_GET['end_date'])) echo $_GET['end_date'] ?>" placeholder="结束日期" type="text" readonly="readonly">
                    </div>
                    <!-- form-group -->
                    <div class="form-group" style="margin: 0;width: 150px;<?php if (!Yii::app()->user->isGuest && Yii::app()->user->lan_id) {
    echo 'display:none';
} ?>">
                        <select class="select2" name="landscape_id" id="check-landscape" data-placeholder="Choose One" style="width: 150px;height:34px;">
                            <option value="">景区</option>
                            <?php
                            $lid = Yii::app()->user->lan_id;
                            foreach ($landscapes as $landscape) {
                                $selected = "";
                                if (isset($_GET['landscape_id']) && $_GET['landscape_id'] == $landscape['id']) {
                                    $selected = "selected='selected'";
                                }
                                echo '<option ' . $selected . ' value="' . $landscape['id'] . '">' . $landscape['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0px; width: 150px;">
                        <select class="select2" id="check-point" name="view_point" style="width: 150px;height:34px;">
                            <option value="">景点</option>
                            <?php
                            foreach ($pois as $poi) {
                                $selected = "";
                                if (isset($_GET['view_point']) && $_GET['view_point'] == $poi['id']) {
                                    $selected = "selected='selected'";
                                }
                                echo '<option ' . $selected . ' value="' . $poi['id'] . '">' . $poi['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0 5px 0 0">
                        <input class="form-control" name="order_id" value="<?php if (isset($_GET['order_id'])) echo $_GET['order_id'] ?>" placeholder="订单编号" type="text" style="width: 318px;">
                    </div>
                    <button class="btn btn-primary btn-xs" type="submit">查询</button>
                </div>
            </form>
        </div>
        <!-- panel-body -->
    </div>
    <style>
        .tab-content .table tr>* {
            text-align: center
        }

        .tab-content .ckbox {
            display: inline-block;
            width: 30px;
            text-align: left
        }
    </style>
    <table class="table table-bordered mb30">
        <thead>
            <tr>
                <th style="width:12%">订单编号</th>
                <th style="width:10%">验证时间</th>
                <th style="width:10%">验证数量</th>
                 <?php if (!Yii::app()->user->isGuest && Yii::app()->user->lan_id): ?>
                <?php else: ?>
                    <th style="width:10%">验证景区</th>
                <?php endif;?>
                <th style="width:10%">验证景点</th>
                <th style="width:7%">验证结果</th>
                <th style="width:10%">操作员</th>
                <th style="width:10%">设备类型</th>
                <th style="width:10%">设备编号</th>
                <th style="width:10%">设备名称</th>
                <th style="width:60px;">操作</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach ($lists as $item) :
    ?>
                <tr>
                    <td  style="width:12%"><?php echo $item['record_code'] ?></td>
                    <td style="width:10%"><?php echo date('Y-m-d H:i:s', $item['created_at']) ?></td>
                    <td style="width:10%"><?php echo $item['num'] ?></td>
                    <?php if (!Yii::app()->user->isGuest && Yii::app()->user->lan_id): ?>
                <?php else: 
                    //得到景区
                    if (!isset($_landscapes)) { //单例
                        $lanIds = PublicFunHelper::arrayKey($lists, 'landscape_id');
                        $_landscapes = Landscape::api()->getSimpleByIds($lanIds);
                    }
                 ?>
                    <td style="width:10%"><?php echo $_landscapes[$item['landscape_id']]['name'] ?></td>
                <?php endif;?>
                    <td style="<?php echo empty($item['poi_id']) ? '' : 'text-align: left;' ?> width: 10%;"><?php
                        if (empty($item['poi_id'])) {
                            echo '全部';
                        } else if (strlen($item['poi_id']) > 0) {
                            $p_ids = explode(',', $item['poi_id']);
                            $spans = array();
                            foreach ($p_ids as $pid) {
                                $spans[] = sprintf('<span role="async-name" class="poi-%d" data-id="poi_%d"></span>', $pid, $pid);
                            }
                            echo implode(',', $spans);
                        }
                        ?>
                    </td>
                    <td style="width:10%"><span class="text <?php echo $item['status'] ? 'text-success' : 'text-danger' ?>"><?php echo $item['status'] ? '成功' : '失败' ?></span></td>
                    <td style="width:10%"><?= $item['cancel_name'] ? '汇联运营客服' : $item['user_name'] ?></td>
                    <td style="width:7%;"><?php
                        if (isset($item['equipment_code']) && !empty($item['equipment_code'])) {
                            echo $item['device_type'] == 1 ? '闸机' : '手持机';
                        } else {
                            echo '无';
                        }
                        ?></td>
                    <td style="width:10%;"><?php
                        echo $item['equipment_code'] == 0 ? '无' : $item['equipment_code'];
                        ?><span></span></td>
                    <td style="width:10%;"><?php 
                        if (isset($item['equipment_code']) && !empty($item['equipment_code'])) {
                            if(isset($item['device_name'])) {
                                echo $item['device_name'];
                            }
                        } else {
                            echo '无';
                        }
                        ?></td>
                    <td style="width:6%"><?php
                        if ($item['cancel_status'] == 1) {
                            ?>
                            已撤销<br/>
                            <?php
                        } else {
                            if ($item['local_source'] <= 1&&$item['status'] == 1 && (time() - $item['created_at'] < 300)) { // 五分钟内票可以撤销
                                ?>
                                <a href="javascript:void(0)" onclick="cancel('<?php echo $item['id'] ?>')" class="btn
                                 btn-primary btn-xs clearPart" id="dell">撤销</a>
                                <?php
                            }
                        }
                        ?>

                        <a class="print_ticket" onclick="Reprint(this, '<?php echo $item['id'] ?>');
                                return false;" href="javascript:;">打印小票</a></td>

                </tr>
    <?php
endforeach
;
?>
        </tbody>
    </table>
    <div style="text-align: left;" class="panel-footer">
        订单数：&nbsp;<span style="color: red; font-size: 17px;"><?php echo $orderNums ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 总人次：&nbsp;<span style="color: blue; font-size: 17px;"><?php echo $totalNums ?></span>&nbsp;
        <div id="basicTable_paginate" class="pagenumQu">
            <?php
            $this->widget('common.widgets.pagers.ULinkPager', array(
                'cssFile' => '',
                'header' => '',
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'firstPageLabel' => '',
                'lastPageLabel' => '',
                'pages' => $pages,
                'maxButtonCount' => 5
                ) // 分页数量
            );
            ?>
        </div>
    </div>
</div>
<!-- contentpanel -->    

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>

<script src="/js/async.names.js"></script>
<script>
 function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }

window.cancel = function(id) {
    PWConfirm('您是否需要撤销该操作?', function() {
        $.post('/check/check/cancel/', {id: id}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                window.location.partReload();
            }
        }, "json");
    });
    return false;
}


jQuery(document).ready(function() {
    //撤销
    function cancel(id) {

    }




    $('#all-btn').click(function() {
        var obj = $(this).parents('table')
        if ($(this).is(':checked')) {
            obj.find('input').prop('checked', true)
            $(this).text('反选')
        } else {
            obj.find('input').prop('checked', false)
            $(this).text('全选')
        }
    })


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
    jQuery("#check-landscape, #check-point").select2();


    $('#check-landscape').change(function(event, $first) {
        var id = $(this).val();
        var optionStr = "<option value='' selected='selected'>景点</option>";
        $('#check-point').html(optionStr).select2();

        if (!id) {
            return false;
        }
        var view_point =<?php echo isset($_GET['view_point']) && !empty($_GET['view_point']) ? $_GET['view_point'] : 0 ?>;
        if ($first == null) {
            view_point = 0;
        }
        $.post('/check/check/getPoi', {id: id}, function(data) {
            var optionStr = "<option value='' selected='selected'>景点</option>";
            for (var i in data.result) {
                var item = data.result[i];
                if (view_point == item.id) {
                    optionStr += '<option value="' + item.id + '" selected="selected">' + item.name + '</option>';
                } else {
                    optionStr += '<option value="' + item.id + '">' + item.name + '</option>';
                }
            }
            $('#check-point').html(optionStr).select2();
        }, 'json');
    });
});

</script>

<script type="text/javascript" src="/js/jquery.cookies.js"></script>
<script language="javascript" src="/js/lodop/LodopFuncs.js?v=1"></script>
<object classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" codebase="/js/lodop/lodop.cab#version=6,1,8,7" width=0 height=0></object>
<script language="javascript" src="<?php echo Yii::app()->versionUrl->changeUrl('/js/lodop/lodopPrint.js') ?>"></script>
<script>
//小票打印    
jQuery(document).ready(function() {
    function Reprint(obj, id) {
        // 如果正在打印中，则终止执行
        if ($(obj).hasClass('active')) {
            return false;
        }
        // 设置打印进行中，防止重复点击
        $(obj).addClass('active');
        var oldText = $(obj).text();
        $(obj).html('<span style="color:gray;">等待3秒</span>');

        new lodopPrint(id,<?php echo Users::model()->findByPk(Yii::app()->user->uid)->print_type ?>);
        // 等待三秒
        var _obj = obj;
        setTimeout(function() {
            $(_obj).html(oldText);
            $(_obj).removeClass('active');
        }, 3000);
    }
    window.Reprint = Reprint;

    function setPrinter() {
        var username = 'print_' + "<?php echo Yii::app()->user->id; ?>";
        LODOP = getLodop();
        var cookValue = LODOP.SELECT_PRINTER();
        $.cookie(username, cookValue, {expires: 365});
    }
    window.setPrinter = setPrinter;
});
</script>