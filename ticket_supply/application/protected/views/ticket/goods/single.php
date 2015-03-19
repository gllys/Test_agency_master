<div class="pageheader">
    <div class="media">
        <div class="pageicon pull-left">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="media-body">
            <ul class="breadcrumb">
                <li><a href="#"><i class="glyphicon glyphicon-home"></i></a></li>
                <li><a href="#">门票管理</a></li>
                <li><a href="#">发布单票</a></li>
            </ul>
        </div>
    </div><!-- media -->
</div><!-- pageheader -->
<link rel="stylesheet" href="/css/validationEngine.jquery.css">
<div class="contentpanel">
    <?php if (isset($_GET['info']) && $_GET['info'] == 1) { ?>
        <div id="show_msg">
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button> 
                <i class="fa fa-check fa-lg"></i>保存失败！</div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">发布单票</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="single" type="hidden" name="type">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">景区名称</label>
                            <div class="col-sm-4">
                                <select data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="distributor-select" name="scenic_id">
                                    <option value=""  >请输入景区名称</option>                                     
                                    <?php if (isset($list) && !empty($list)): foreach ($list as $key => $model): ?>
                                            <option value="<?php echo $model['id']; ?>"  ><?php echo $model['name']; ?></option>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票名称</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="name">
                            </div>
                        </div><!-- form-group -->
                        <input type="hidden" name="province_id" id="province">
                        <input type="hidden" name="city_id" id="city">
                        <input type="hidden" name="district_id" id="district">
                        <div class="form-group">        
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景点</label>
                            <div class="col-sm-4">
                                <div class="panel" style="margin:0">
                                    <div id="appendto"> </div><!-- panel-body -->
                                </div>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><input name="is_fit" type="checkbox" value="1" style=" position: relative;top: 2px;" />&nbsp;散客价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="散客价" readonly class=" form-control validate[custom[number]]" name="fat_price"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><input name="is_full" type="checkbox" value="1"  style="position: relative; top: 2px;" />&nbsp;团队价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="团队价" readonly class=" form-control validate[custom[number]]" name="group_price"/></div>
                                <div class="col-sm-3">
                                    最少订票 <input type="text" id="spinner-min" name="mini_buy"> 张
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 销售价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="销售价" class="validate[custom[number]] form-control" name="sale_price"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 挂牌价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="挂牌价" class="validate[custom[number]] form-control" name="listed_price"/></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 是否允许退票</label>
                            <div class="col-sm-4">
                                <div class="rdio rdio-default inline-block">
                                    <input type="radio" checked="checked" value="1" id="radioDefault5" name="refund">
                                    <label for="radioDefault5">是</label>
                                </div>
                                <div class="rdio rdio-default inline-block">
                                    <input type="radio" checked="checked" value="0" id="radioDefault6" name="refund">
                                    <label for="radioDefault6">否</label>
                                </div>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票说明</label>

                            <div class="col-sm-4">
                                <button class="btn btn-success btn-xs" type="button" data-target=".bs-example-modal-lg"
                                        data-toggle="modal">点击编辑
                                </button>
                                <input type="hidden" name="remark"/>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">预定时间</label>
                            <div class="col-sm-10">
                                需在入园前 <input type="text" class="spinner-day" name="scheduled" readonly="readonly"> 天的
                                <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker2" type="text" class="form-control" style="width:50px" name="scheduledtime"></div>
                                以前购买
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 可用时间段</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[1]" readonly> ~
                                <input type="text" placeholder="" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[2]" readonly>
                                预订游玩日期后 <input type="text" class="spinner-day" name="valid" readonly="readonly"> 天有效
                            </div>
                        </div><!-- form-group -->


                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 适用日期</label>
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
                        </div><!-- panel-body --> 

                        <div class="panel-footer">
                            <button class="btn btn-primary mr5" type="button" id="form-button">保存</button>
                            <button class="btn btn-default" type="button" id="btnreset">回撤</button>
                        </div>
                </form>

                <div role="dialog" tabindex="-1" class="modal fade bs-example-modal-lg">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close-remark">×</button>
                                <h4 class="modal-title">门票说明</h4>
                            </div>
                            <div class="modal-body">
                                <textarea  id="myremark" placeholder="请输入您的门票说明..." class="form-control validate[required]"
                                           rows="10"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" id="remark-data">保存</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script src="/js/jquery.validationEngine-zh-CN.js"></script> 
<script src="/js/jquery.validationEngine.js"></script> 
<script>
    $("#repass-form").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });

    jQuery(document).ready(function() {

        $('#btnreset').click(function() {
            location.href = '/ticket/goods/';
        });

        $('#distributor-select').change(function() {
            var names = $("#distributor-select").val();
            if (names == null) {
                $('#appendto').html('');
            } else {
                $.post('/ticket/goods/single', {'ids': names}, function(data) {
                    $('#appendto').html(data);
                }, 'json');
            }

        });

        $('#distributor-select').change(function() {
            var id = $("#distributor-select").val();
            if (id != null) {
                $.post('/ticket/goods/landscape', {id: id}, function(data) {
                    var pro_id = data.land.province_id;
                    var ci_id = data.land.city_id;
                    var di_id = data.land.district_id;
                    $('#province').val(pro_id);
                    $('#city').val(ci_id);
                    $('#district').val(di_id);
                }, 'json')
            }

        });

        $('#repass-form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });


        //如果选择了团散客
        window.setInterval(function() {
            if ($('[name=is_fit]').prop('checked')) {
                $('input[name="fat_price"]').removeAttr("readonly").addClass('validate[required]');
            } else {
                $('input[name="fat_price"]').val("");
                $('input[name="fat_price"]').attr("readonly", "readonly").removeClass('validate[required]');
            }

            if ($('[name=is_full]').prop('checked')) {
                $('input[name="group_price"]').removeAttr("readonly").addClass('validate[required]');
            } else {
                $('input[name="group_price"]').val("");
                $('input[name="group_price"]').attr("readonly", "readonly").removeClass('validate[required]');
            }
        }, 200);

        $('#form-button').click(function() {
            //$('#show_msg').empty();
            var obj = $('#repass-form');
            if (!$('[name=is_fit]').prop('checked') && !$('[name=is_full]').prop('checked')) {
                alert('团队价和散客价至少选一个');
                return false;
            }
            if ($('#spinner-min').val() > 100) {
                alert('最少预订票数不可超过100');
                return false;
            }
            if (obj.validationEngine('validate') == true) {
                $('#form-button').attr('disabled', true);
                $.post('/ticket/goods/add', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-button').attr('disabled', false);
                    } else {
                        location.href = '/ticket/depot/';
                    }
                }, 'json');
            }
            ;

            return false;
        });


        $('#remark-data').click(function() {
            $('input[name=remark]').val($('#myremark').val());
            $('#close-remark').trigger('click');

        })


        !function() {
            var sd = $('#selected-distributor'),
                    aab = $('#area-add-btn')

            function b(obj, p, val) {
                var i, a, s
                sd.find('th').each(function() {
                    if ($(this).text() == val) {
                        i = $(this).parent().index()
                    }
                })

                sd.find('.panel-heading').each(function() {
                    if ($(this).text() == p) {
                        a = $(this).parent('.panel')
                    }
                })


                if (i != undefined) {
                    return false
                }

                if (a) {
                    a.find('tbody').append('<tr><th width="100">' + val + '</th><td><a href="" class="btn btn-xs mr5">武义三峰 <i class="fa fa-times"></i></a></td></tr>')
                } else {
                    sd.find('> .panel-body').append('<div class="panel panel-info"><div class="panel-heading">' + p + '</div><div class="panel-body"><div class="table-responsive mb10"><table class="table table-bordered"><tbody><tr><th width="100">' + val + '</th><td><a href="" class="btn btn-xs mr5">武义三峰 <i class="fa fa-times"></i></a></td></tr></tbody></table></div></div></div>')
                }
            }


            aab.click(function() {
                var obj = $('#area-select'),
                        val = obj.val(),
                        p = obj.find(':selected').attr('data-p')
                b(obj, p, val)
            })

            $('#distributor-select').change(function() {
                var obj = $(this),
                        val = obj.val(),
                        p = obj.find(':selected').attr('data-p'),
                        t = obj.find(':selected').text(),
                        i,
                        d
                sd.find('td a').each(function() {
                    if ($(this).text() == t) {
                        d = true
                    }
                })

                b(obj, p, val)

                sd.find('th').each(function() {
                    if ($(this).text() == val) {
                        i = $(this)
                    }
                })
                if (typeof d == undefined) {
                    i.next('td').append('<a href="" class="btn btn-xs mr5">' + t + ' <i class="fa fa-times"></i></a>')
                }
            })
        }()




        //适用日期
        $('.days-check button').click(function() {
            var i = $(this).index()
            var obj = $(this).parents('.days-checkbox').find('.checkbox-group input')
            if (i == 0) {
                if ($(this).text() == '全部') {
                    obj.prop('checked', true)
                    $(this).text('反选')
                } else {
                    $(this).text('全部')
                    obj.prop('checked', false)
                }
            }

            if (i == 1) {
                obj.prop('checked', false)
                obj.eq(5).prop('checked', true)
                obj.eq(6).prop('checked', true)
            }

            if (i == 2) {
                obj.prop('checked', true)
                obj.eq(5).prop('checked', false)
                obj.eq(6).prop('checked', false)
            }
            return false
        })









        // Tags Input
        jQuery('#tags').tagsInput({width: 'auto'});

        // Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();

        // Spinner
        var spinner = jQuery('#spinner').spinner({'min': 0});
        spinner.spinner('value', 1);

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1, 'max': 99});
        spinnerMin.spinner('value', 1);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        spinnerDay.spinner('value', 1);





        // Form Toggles
        jQuery('.toggle').toggles({on: true});

        // Time Picker
        jQuery('#timepicker').timepicker({defaultTIme: false});
        jQuery('#timepicker2').timepicker({showMeridian: false});
        jQuery('#timepicker3').timepicker({minuteStep: 15});

        // Date Picker
        jQuery('.datepicker').datepicker();
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
        jQuery("#distributor-select, #select-multi").select2();
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

//        // Color Picker
//        if (jQuery('#colorpicker').length > 0) {
//            jQuery('#colorSelector').ColorPicker({
//                onShow: function(colpkr) {
//                    jQuery(colpkr).fadeIn(500);
//                    return false;
//                },
//                onHide: function(colpkr) {
//                    jQuery(colpkr).fadeOut(500);
//                    return false;
//                },
//                onChange: function(hsb, hex, rgb) {
//                    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
//                    jQuery('#colorpicker').val('#' + hex);
//                }
//            });
//        }
//
//        // Color Picker Flat Mode
//        jQuery('#colorpickerholder').ColorPicker({
//            flat: true,
//            onChange: function(hsb, hex, rgb) {
//                jQuery('#colorpicker3').val('#' + hex);
//            }
//        });


    });



</script>
