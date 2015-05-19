<?php
/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/17/14
 * Time: 4:19 PM
 * File: create.php
 */
$this->breadcrumbs = array('门票', '门票管理', '新增门票');
?>
<style>
    .col-sm-2 {
        width: 85px;
    }

    .picker {
        cursor: pointer !important;
        cursor: hand !important;
        background-color: #ffffff !important;
    }

    .datepicker {
        width: 120px;
        display: inline-block;
    }
    .ui-datepicker { z-index:9999!important }
</style>
<link rel="stylesheet" href="/css/validationEngine.jquery.css">
<div class="contentpanel">
    <?php if (isset($_GET['info']) && $_GET['info'] == 1) { ?>
        <div id="show_msg">
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                <i class="fa fa-check fa-lg"></i>保存失败！
            </div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">新增门票</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="single" type="hidden" name="type">
                    <input type="hidden" name="token" value="<?php echo $_SERVER['REQUEST_TIME']?>"/>

                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票名称</label>

                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required,maxSize[60]] form-control" name="name" data-errormessage-range-overflow="只能输入30个字">
                            </div>
                        </div>
                        <!-- form-group -->
                        <input type="hidden" name="province_id" id="province">
                        <input type="hidden" name="city_id" id="city">
                        <input type="hidden" name="district_id" id="district">

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景点</label>

                            <div class="col-sm-11">
                                <div class="panel" style="margin:0">
                                    <?php if (isset($poi) && is_array($poi)) {
                                        foreach ($poi as $p) {
                                            ?>
                                            <div class="ckbox ckbox-default mr20 mb5 inline-block">
                                                <input id="p_<?php echo $p['id'] ?>" type="checkbox" value="<?php echo $p['id'] ?>" name="view_point[]">
                                                <label for="p_<?php echo $p['id'] ?>"><?php echo $p['name'] ?></label>
                                            </div>
                                        <?php
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">
                                <table class="table table-bordered" id="ticket-type">
                                    <thead>
                                    <tr>
                                        <th style="width:100px">类型</th>
                                        <th style="width:100px">价格</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($price_type as $idx => $type) {?>
                                    <tr>
                                        <td><div class="ckbox ckbox-default mr20 mb5 inline-block">
                                                <input id="prices_<?php echo $idx?>" name="prices[]" type="checkbox" />
                                                <label for="prices_<?php echo $idx?>"><?php echo $type?>票</label></td>
                                            </div>
                                        <td><input data-id="prices_<?php echo $idx?>" data-type="<?php echo $idx?>" type="text" style="ime-mode:disabled" placeholder="" readonly class="multi-price form-control validate[custom[number]]" data-errormessage-custom-error="只能输入数字" /></td>
                                    </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 销售有效期</label>

                            <div class="col-sm-4" style="width: 320px">
                                <div class="input-group" style="width: 140px;float: left">
                                    <input type="text" class="form-control datepicker" name="date_available[]" readonly="readonly" disabled="disabled">
                                    <!--span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span-->
                                </div>
                                <div class="input-group" style="width: 15px;float: left;text-align: center;padding-top: 7px">
                                    ~
                                </div>
                                <div class="input-group" style="width: 140px;float: left">
                                    <input type="text" class="form-control datepicker" name="date_available[]" readonly="readonly" disabled="disabled">
                                    <!--span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span-->
                                </div>
                            </div>
                            <div class="ckbox ckbox-default mr20 inline-block" style="position: relative;top: 6px">
                                <input name="all_available" id="all_available" type="checkbox" checked="checked" value="1"/>
                                <label for="all_available">不限时间</label>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>

                            <div class="col-sm-10 days-checkbox">

                                <div class="btn-group days-check">
                                    <button class="btn btn-primary btn-xs" type="button">反选</button>
                                    <button class="btn btn-primary btn-xs" type="button">周末</button>
                                    <button class="btn btn-primary btn-xs" type="button">平日</button>
                                </div>
                                <div class="checkbox-group">
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d1" value="1" name="week_time[]">
                                        <label for="d1">周一</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d2" value="2" name="week_time[]">
                                        <label for="d2">周二</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d3" value="3" name="week_time[]">
                                        <label for="d3">周三</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d4" value="4" name="week_time[]">
                                        <label for="d4">周四</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d5" value="5" name="week_time[]">
                                        <label for="d5">周五</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d6" value="6" name="week_time[]">
                                        <label for="d6">周六</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d7" value="0" name="week_time[]">
                                        <label for="d7">周日</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">使用有效期</label>
                            <div class="col-sm-10">
                                <div class="rdio rdio-default">
                                    <input type="radio" checked="checked" value="0" id="valid0" name="valid">
                                    <label for="valid0">当天</label>
                                </div>
                                <div class="rdio rdio-default">
                                    <input type="radio" value="1" id="valid1" name="valid">
                                    <label for="valid1">当天及之后</label>
                                </div>
                                <input type="text" class="spinner-day" id="valid_day" style="ime-mode: disabled"> 天
                            </div>
                        </div><!-- form-group -->

                        <div class="panel-footer">
                            <img id="loader" src="/img/loaders/loader1.gif" style="display: none" alt=""/>
                            <button class="btn btn-primary mr5" type="button" id="form-button">发布</button>
                            <a href="/ticket/ticket/" class="btn btn-default">放弃，返回</a>
                        </div>
                </form>


            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->
</div><!-- contentpanel -->
<script>
    $("#repass-form").keypress(function (e) {
        if (e.which == 13) {
            return false;
        }
    });

    jQuery(document).ready(function () {
        $('#repass-form').validationEngine({
            autoHidePrompt: true,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });

        $('#all_available').click(function () {
            if (!$('#all_available').prop('checked')) {
                $('.datepicker').addClass("picker").addClass('validate[required]').removeAttr('disabled');
            } else {
                $('.datepicker').val("");
                $('.datepicker').removeClass("picker").removeClass('validate[required]').attr("disabled", "disabled");
            }
        });

        $('#form-button').click(function () {
            var obj = $('#repass-form');

            $('#form-button').hide();
            $('#loader').show();
            if (obj.validationEngine('validate') == true) {
                $.post('/ticket/ticket/save', obj.serialize(), function (data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-button').show();
                        $('#loader').hide();
                    } else {
                        $('#form-button').attr('disabled', 'disabled').removeClass('btn-primary').addClass('btn-success').text('保存成功！').show();
                        setTimeout(function(){
                            location.href = '/site/switch/#/ticket/ticket/';
                        }, 500);
                    }
                }, 'json');
            } else {
                alert("填写不完整，请检查");
                $('#form-button').show();
                $('#loader').hide();
            }

            return false;
        });

        //适用日期
        $('.days-check button').click(function () {
            var i = $(this).index();
            var obj = $(this).parents('.days-checkbox').find('.checkbox-group input')
            if (i == 0) {
                if ($(this).text() == '全部') {
                    obj.prop('checked', true);
                    $(this).text('反选')
                } else {
                    $(this).text('全部');
                    obj.prop('checked', false)
                }
            }

            if (i == 1) {
                obj.prop('checked', false);
                obj.eq(5).prop('checked', true);
                obj.eq(6).prop('checked', true)
            }

            if (i == 2) {
                obj.prop('checked', true);
                obj.eq(5).prop('checked', false);
                obj.eq(6).prop('checked', false)
            }
            return false
        });

        // Spinner
        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        spinnerDay.spinner('value', 1);

        $('.spinner-day').blur(function(){
            if($(this).val() < 0 || isNaN($(this).val()) || $(this).val() == ''){
                $(this).val('1');
            }
            $('#valid1').val($(this).val());
        });

        // Date Picker
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
            yearRange: "1995:2065",
            minDate: 0,
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

        //门票类型
        $('#ticket-type input[type=checkbox]').click(function(){
            var input = $(this).parents('tr').find('.form-control');
            if($(this).is(':checked')){
                input.removeAttr("readonly").addClass('validate[required]');
            }else{
                input.val("");
                input.attr("readonly", "readonly").removeClass('validate[required]');
            }
        });

        //价格类型
        $('.multi-price').change(function(){
            var id = $(this).attr('data-id');
            $('#'+id).val($(this).attr('data-type') + '_' + $(this).val());
        });

    });

</script>
