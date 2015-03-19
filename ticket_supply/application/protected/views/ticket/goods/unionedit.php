<div class="pageheader">
    <div class="media">
        <div class="pageicon pull-left">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="media-body">
            <ul class="breadcrumb">
                <li><a href="#"><i class="glyphicon glyphicon-home"></i></a></li>
                <li><a href="#">门票管理</a></li>
                <li><a href="#">发布联票</a></li>
            </ul>
        </div>
    </div><!-- media -->
</div><!-- pageheader -->
<link rel="stylesheet" href="/css/validationEngine.jquery.css">
<div class="contentpanel">
    <div id="show_msg"> </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">发布联票</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="union" type="hidden" name="type">
                    <input value="<?php echo $ticket['id'] ?>" type="hidden" name="id">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 联票名称</label>
                            <div class="col-sm-4">
                                <input type="text" class="validate[required] form-control" name="name" value="<?php echo $ticket['name'] ?>">
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景区</label>
                            <div class="col-sm-4">
                                <select multiple data-placeholder="请输入景区名称" style="width:300px;padding:0 10px;" id="distributor-select" name="scenic_id[]">
                                    <option value=""  >请输入景区名称</option>                                     
                                    <?php foreach ($list as $key => $model): ?>
                                        <option value="<?php echo $model['id']; ?>" <?php
                                        $arr = explode(',', $ticket['scenic_id']);
                                        if (in_array($model['id'], $arr)) {
                                            echo "selected=selected";
                                        }
                                        ?>><?php echo $model['name']; ?></option>
<?php endforeach; ?>
                                </select>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4">
                                <div class="panel includes-scenic" style="margin:0">
                                    <style>
                                        .includes-scenic th{
                                            text-align:center
                                        }
                                        .includes-scenic th a{
                                            position:relative;
                                            padding-left:20px;
                                        }
                                        .includes-scenic th a i{
                                            display:none;
                                            position:absolute;
                                            left:0;
                                            top:3px;
                                        }
                                        .includes-scenic th a:hover i{
                                            display:inline-block
                                        }
                                    </style>
                                    <table class="table table-striped" id="appendto"></table>
                                </div>
                            </div>
                        </div><!-- form-group -->

                        <?php 
                            $result = Organizations::api()->show(array('id' => Yii::app()->user->org_id));
                            if($result['code'] == 'succ' && !empty($result['body'])){
                                $province_id = $result['body']['province_id'];
                                $city_id = $result['body']['city_id'];
                                $district_id = $result['body']['district_id'];
                            }

                        ?>
                        <input type="hidden" name="province_id" value="<?php echo $province_id?>">
                        <input type="hidden" name="city_id" value="<?php echo $city_id?>">
                        <input type="hidden" name="district_id" value="<?php echo $district_id?>">

                         <div class="form-group">
                            <label class="col-sm-2 control-label"><input name="is_fit" type="checkbox" value="1"  style="position: relative; top: 2px;" <?php if(!empty($ticket['is_fit'])){ echo "checked=checked";}?> />&nbsp; 散客价</label>
                             <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="散客价" class="form-control validate[custom[number]]" name="fat_price" value="<?php echo $ticket['fat_price']?>"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                        	<label class="col-sm-2 control-label"><input name="is_full" type="checkbox" value="1"  style="position: relative; top: 2px;" <?php if(!empty($ticket['is_full'])){ echo "checked=checked";}?> />&nbsp;  团队价</label>
                        	<div class="col-sm-10">
                        	<div class="col-sm-3"><input type="text" placeholder="团队价" class="form-control validate[custom[number]]" name="group_price" value="<?php echo $ticket['group_price']?>"/></div>
                                <div class="col-sm-3">
                                    最少订票 <input type="text" id="spinner-min" name="mini_buy" value="<?php echo $ticket['mini_buy'];?>"> 张
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 销售价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="销售价" class="validate[custom[number]] form-control" name="sale_price" value="<?php echo $ticket['sale_price'];?>"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                        	<label class="col-sm-2 control-label"> 挂牌价</label>
                        	<div class="col-sm-10">
                        		<div class="col-sm-3"><input type="text" placeholder="挂牌价" class="validate[custom[number]] form-control" name="listed_price" value="<?php echo $ticket['listed_price'];?>"/></div>
                        	</div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 是否允许退票</label>
                            <div class="col-sm-4">
                                <div class="rdio rdio-default inline-block">
                                    <input type="radio" value="1" id="radioDefault5" name="refund" <?php
if ($ticket['refund'] == 1) {
    echo 'checked="checked"';
}
?>>
                                    <label for="radioDefault5">是</label>
                                </div>
                                <div class="rdio rdio-default inline-block">
                                    <input type="radio" value="0" id="radioDefault6" name="refund" <?php
if ($ticket['refund'] == 0) {
    echo 'checked="checked"';
}
?>>
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
                                <input type="hidden" name="remark" value="<?php echo $ticket['remark'] ?>"/>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 预定时间</label>
                            <div class="col-sm-10"><?php $day = floor($ticket['scheduled_time'] / 86400);
                            $times = $ticket['scheduled_time'] % 86400;
                            $hour[0] = intval($times / 3600);
                            $minutes = $times % 3600;
                            $hour[1] = $minutes / 60;
                            $time = implode(':', $hour);
                            ?>
                            需在入园前 <input type="text" class="spinner-day" name="scheduled" value="<?php echo $day; ?>" readonly="readonly"> 天的
                                <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker2" type="text" class="form-control" style="width:50px" name="scheduledtime" value="<?php echo $time; ?>"></div>
                                以前购买
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 可用时间段</label>
                            <div class="col-sm-10"> <?php list($a, $b) = explode(',', $ticket['date_available']); ?>
                                <input type="text" class="validate[required] form-control datepicker" style="width:120px;display:inline-block" name="date_available[1]" value="<?php echo date('Y-m-d', $a); ?>"  readonly="readonly"> ~
                                <input type="text" class="validate[required] form-control datepicker" style="width:120px;display:inline-block" name="date_available[2]" value="<?php echo date('Y-m-d', $b); ?>" readonly="readonly">
                                预订游玩日期后 <input type="text" class="spinner-day" name="valid" value="<?php echo $ticket['valid']; ?>" readonly="readonly"> 天有效
                            </div>
                        </div><!-- form-group -->


                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 适用日期</label>
                            <div class="col-sm-10 days-checkbox">

                                <div class="btn-group days-check">
                                    <button class="btn btn-primary btn-xs" type="button">全部</button>
                                    <button class="btn btn-primary btn-xs" type="button">周末</button>
                                    <button class="btn btn-primary btn-xs" type="button">平日</button>
                                </div>
                                <div class="checkbox-group"><?php $arr = explode(',', $ticket['week_time']); ?>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox"  id="d1" value="1" name="week_time[]" <?php
                                        if (in_array(1, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d1">周一</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" id="d2" value="2" name="week_time[]" <?php
                                        if (in_array(2, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d2">周二</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" id="d3" value="3" name="week_time[]" <?php
                                        if (in_array(3, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d3">周三</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" id="d4" value="4" name="week_time[]" <?php
                                        if (in_array(4, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d4">周四</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" id="d5" value="5" name="week_time[]" <?php
                                        if (in_array(5, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d5">周五</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" id="d6" value="6" name="week_time[]" <?php
                                        if (in_array(6, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d6">周六</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" id="d7" value="0" name="week_time[]" <?php
                                        if (in_array(0, $arr)) {
                                            echo 'checked="checked"';
                                        }
                                        ?>>
                                        <label for="d7">周日</label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- form-group -->
                    </div><!-- panel-body --> 

                    <div class="panel-footer">
                        <button class="btn btn-primary mr5" type="buttom" id="form-button">保存</button>
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
                                           rows="10"><?php echo $ticket['remark'] ?></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" id="remark-data">保存</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!---panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->

<script>
    $("#repass-form").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    jQuery(document).ready(function() {
        $('#btnreset').click(function() {
            location.href = '/ticket/depot/index/type/2';
        });


        $('#distributor-select').change(function(event, flag) {
            var names = $("#distributor-select").val();
            if (names == null) {
                $('#appendto').html('');
            } else {
                if (names.length >= $('.landscape-box').length) {//如果增加景区
                    $.ajaxSetup({async: false});
                    for (var i = 0; i < names.length; i++) {
                        var id = names[i];
                        if ($('.landscape-box-' + id).length <= 0) {
                            $.post('/ticket/goods/union', {ids: id}, function(data) {
                                $('#appendto').append(data);
                            }, 'json');
                        }
                    }
                    $.ajaxSetup({async: true});
                    //首次初始化不选择的子景点
                    var flag = flag | false;
                    if (flag) {
                        var points = "<?php echo $ticket['view_point'] ?>".split(',');
                        $('.landscape-box').find('input').attr('checked', false);
                        for (i in points) {
                            var _point = points[i];
                             $('.landscape-box').find('input[value='+_point+']').trigger('click');
                        }
                    }

                } else {//减少景区
                    var arr = [];
                    for (var i = 0; i < names.length; i++) {//讲对象转化为数组
                        arr.push(names[i]);
                    }
                    $('.landscape-box').each(function() {
                        var obj = $(this);
                        if ($.inArray(obj.attr('box-by-id'), arr) == -1) {
                            obj.remove();
                        }
                    });
                }
            }
        }).trigger('change', true);

        $('#repass-form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });
        
                     //如果选择了团散客
        window.setInterval(function(){
            if($('[name=is_fit]').prop('checked')){
               $('input[name="fat_price"]').removeAttr("readonly").addClass('validate[required]'); 
            }else{
               $('input[name="fat_price"]').val("");
               $('input[name="fat_price"]').attr("readonly","readonly").removeClass('validate[required]'); 
            }
            
            if($('[name=is_full]').prop('checked')){
               $('input[name="group_price"]').removeAttr("readonly").addClass('validate[required]'); 
            }else{
               $('input[name="group_price"]').val("");
               $('input[name="group_price"]').attr("readonly","readonly").removeClass('validate[required]'); 
            }
        },200);
        //表单提交
        $('#form-button').click(function() {
            var obj = $('#repass-form');
             if (!$('[name=is_fit]').prop('checked')&& !$('[name=is_full]').prop('checked')) {
                alert('团队价和散客价至少选一个');
                return false;
            }
            //联票必须选择一个子景点开始
            var $pidObjs = $('.landscape-box');
            for (i = 0; i < $pidObjs.length; i++) {
                var $viewPoints = $pidObjs.eq(i).find('input:checked');
                if ($viewPoints.length < 1) {
                    alert($pidObjs.eq(i).find('a').html() + '景区至少选择一个景点');
                    return false;
                }
            }
            if (obj.validationEngine('validate') == true) {
                $.post('/ticket/goods/update', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                    } else {
                        location.href = '/ticket/depot/index/type/2';
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


        $('body').on('click', '.includes-scenic th a', function() {
            $(this).parents('tr').remove()
            return false
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
        var spinner = jQuery('#spinner').spinner({'min': 1});
        // spinner.spinner('value', 1);

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1});
        //spinnerMin.spinner('value', 1);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        //spinnerDay.spinner('value', 1);

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
