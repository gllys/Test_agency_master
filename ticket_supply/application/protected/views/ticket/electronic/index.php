<?php
$this->breadcrumbs = array('门票管理', '发布电子票');
?>
<section>
    <div id="show_msg"></div>
    <div class="contentpanel">

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">发布任务单</h4>
                    </div>
                    <!-- panel-heading -->
                    <form class="form-horizontal form-bordered" method="post" id="form-data-ticket">
                        <div class="panel-body nopadding">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票名称</label>

                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control validate[required]" name="name">
                                </div>
                            </div>
                            <!-- form-group -->
                            <?php if ($poi): ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景点</label>

                                    <div class="col-sm-4" style="width:810px">
                                        <div class="panel" style="margin:0">
                                            <div>
                                                <?php foreach ($poi as $value): ?>
                                                    <div class="ckbox ckbox-default mr20 inline-block">
                                                        <input type="checkbox" value="<?php echo $value['id'] ?>"
                                                               id="<?php echo $value['id'] ?>" name="view_point[]">
                                                        <label for="<?php echo $value['id'] ?>"><?php echo $value['name']; ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <!-- panel-body -->
                                        </div>
                                    </div>
                                </div><!-- form-group -->
                            <?php else: ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景点</label>
                                    <div>&nbsp;&nbsp;该景区下暂无可游玩景点</div>
                                </div>
                            <?php endif; ?>
                            <?php if ($landscape): ?>
                                <input type="hidden" name="scenic_id" value="<?php echo $landscape['id'] ?>"/>
                                <input type="hidden" value="<?php echo $landscape['province_id'] ?>" name="province_id"/>
                                <input type="hidden" value="<?php echo $landscape['city_id'] ?>" name="city_id"/>
                                <input type="hidden" value="<?php echo $landscape['district_id'] ?>" name="district_id"/>
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 散客价</label>

                                <div class="col-sm-10">
                                    <div class="col-sm-3"><input type="text" placeholder="散客价" class="form-control validate[custom[number],required]"
                                                                 name="fat_price"/></div>               
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 团队价</label>
                                <div class="col-sm-10">    
                                    <div class="col-sm-3"><input type="text" placeholder="团队价" class="form-control validate[custom[number],required]"
                                                                 name="group_price"/></div>
                                    <div class="col-sm-5">
                                        <span class="text-danger">*</span> 最少订票 <input type="text" id="spinner-min"
                                                                                       class="validate[required,custom[integer]]" name="mini_buy"> 张（不超过100张）
                                    </div>
                                </div>
                            </div>
                            <!-- form-group -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">销售价</label>

                                <div class="col-sm-10">
                                    <div class="col-sm-3"><input type="text" placeholder="销售价" class="form-control validate[number]"
                                                                 name="sale_price"/></div>
                                </div>   
                            </div>    
                            <div class="form-group">
                                <label class="col-sm-2 control-label">挂牌价</label>
                                <div class="col-sm-10">                              
                                    <div class="col-sm-3"><input type="text" placeholder="挂牌价" class="form-control validate[number]"
                                                                 name="listed_price"/></div>
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
                            </div>
                            <!-- form-group -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票说明</label>

                                <div class="col-sm-4">
                                    <button class="btn btn-success btn-xs" type="button" data-target=".bs-example-modal-lg"
                                            data-toggle="modal">点击编辑
                                    </button>
                                    <input type="hidden" name="remark"/>
                                </div>
                            </div>
                            <!-- form-group -->


                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 预定时间</label>

                                <div class="col-sm-10">
                                    需在入园前 <input type="text" class="spinner-day validate[required]" name="more_time[0]"> 天的
                                    <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle;position:relative;z-index:1"><input
                                            name="more_time[1]" style="width:60px" id="timepicker2" type="text" class="form-control validate[required]" style="width:50px;"></div>
                                    以前购买
                                </div>
                            </div>
                            <!-- form-group -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 可用时间段</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="" class="form-control datepicker"
                                           style="width:120px;display:inline-block" name="from_to_time[0]"> ~
                                    <input type="text" placeholder="" class="form-control datepicker"
                                           style="width:120px;display:inline-block" name="from_to_time[1]">

                                </div>
                                <label class="col-sm-2 control-label"></label>

                                <div class="col-sm-10 days-checkbox">

                                    <div class="btn-group days-check" style="margin-top:10px">
                                        <button class="btn btn-primary btn-xs" type="button">全部</button>
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
                            <!-- form-group -->


                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                购买后 <input type="text" class="spinner-day validate[required]" name="valid"> 天有效

                            </div>
                            <!-- form-group -->
                        </div>
                        <!-- panel-body -->

                        <div class="panel-footer">
                            <button type="button" class="btn btn-primary mr5 submit">保存</button>
                            <button class="btn btn-default" type="button" id="return">取消</button>
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


                    <div role="dialog" tabindex="-1" class="modal fade" id="exception-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <input type="hidden" id="exception-input">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title">例外</h4>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal form-bordered">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">地区:</label>

                                            <div class="col-sm-10">
                                                <select class="select2" data-placeholder="" style="width:150px;padding:0 10px;"
                                                        id="province-select">
                                                    <option value="__NULL__">省</option>
                                                    <?php if ($province): ?>
                                                        <?php foreach ($province as $value): ?>
                                                            <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <select class="select2" data-placeholder="" style="width:150px;padding:0 10px;"
                                                        id="area-select">
                                                    <option value="__NULL__">市</option>
                                                </select>
                                                <button type="button" class="btn btn-success btn-xs" id="area-add-btn">添加</button>
                                            </div>

                                        </div>
                                        <!-- form-group -->

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">分销商:</label>

                                            <div class="col-sm-10">
                                                <input type="hidden" class="bigdrop" id="distributor-select" style="width:350px"/>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- form-group -->

                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">已选分销商</h3>
                                            </div>
                                            <style>
                                                td i {
                                                    display: none
                                                }

                                                td a:hover i {
                                                    display: inline-block
                                                }

                                                th {
                                                    vertical-align: middle;
                                                }
                                            </style>
                                            <div class="panel-body" id="selected-distributor">


                                                <div class="panel panel-info none">
                                                    <div style="text-align:center;padding:10px">暂无</div>
                                                </div>

                                                <!-- <div class="table-responsive">
                                                  <table class="table table-bordered mb30" id="selected-distributor">
                                                    <tbody>
                                                        <tr class="none"><td style="text-align:center;padding:10px">暂无</td></tr>
                                                    </tbody>
                                                  </table>
                                                </div>-->

                                            </div>
                                            <!-- panel-body -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" id="form-success">保存</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <!-- panel -->

            </div>
            <!-- col-md-6 -->
        </div>
        <!-- row -->
    </div>
    <!-- contentpanel -->
</div>
</div><!-- mainwrapper -->
</section>
<script>
    function delAgency(obj) {
        var obj = $(obj);
        if (obj.siblings().length == 0) {//除自己外没有分销商
            var trCount = obj.closest('tr').siblings().length;
            if (trCount == 0) {//该省下也只有自己所在的城市
                //直接删除省
                obj.closest('.agency-panel').remove();
                if ($('.agency-panel').length <= 0) {
                    $('#selected-distributor').html('<div class="panel panel-info none"><div style="text-align:center;padding:10px">暂无</div></div>');
                }
            } else {
                obj.closest('tr').remove();
            }
        } else {
            obj.remove();
        }
    }
    jQuery(document).ready(function() {

        // HTML5 WYSIWYG Editor
        $('#wysiwyg').wysihtml5({color: true, html: true})

        $('#province-select').change(function() {
            var code = $(this).val();
            $('#area-select').html('<option value="__NULL__">市</option>');
            if (code != '__NULL__') {
                var html = new Array();
                $.get('/ajaxServer/getChildern/id/' + code, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }
                    $('#area-select').append(html.join(''));
                }, 'json');
            }
            return false;
        });

        $('#area-add-btn').click(function() {
            var province_id = $('#province-select').val();
            if (province_id == "__NULL__") {
                alert("请选择地区!");
                return false;
            }
            var province_text = $('#province-select').find("option:selected").text();
            var city_id = $('#area-select').val();
            var city_text = "";
            if (city_id != "__NULL__") {
                city_text = $('#area-select').find("option:selected").text();
            }
            b(province_id, city_id, province_text, city_text);
        });

        $('#exception-btn-1,#exception-btn-2').click(function() {
            //设置要保存分销商的目标
            $("#exception-input").val($(this).attr('form'));
            var ids = $('#' + $(this).attr('form')).val();
            if (ids != "") {
                $.post('/ajaxServer/agencyByIds', {ids: ids}, function(data) {
                    if (data.results.length > 0) {
                        $('.none').hide();
                        for (i in data.results) {
                            var item = data.results[i];
                            if ($(".province_" + item.province_id).length <= 0) {
                                var html = '<div class="panel panel-info agency-panel"><div class="panel-heading">' + item.province_text +
                                        '</div><div class="panel-body province_' + item.province_id + '">';
                                html += '<div class="table-responsive"><table class="table table-bordered mb30"><tbody></tbody></table></div></div></div>';
                                $('#selected-distributor').append(html);
                            }
                            if ($(".city_" + item.city_id).length <= 0) {
                                $('.province_' + item.province_id).find('table').append('<tr class="city_' + item.city_id + '"><th width="100">' + item.city_text + '</th><td></td></tr>');
                            }
                            if ($('#agency_' + item.id).length <= 0) {
                                html = '<a href="javascript:void(0);" class="btn btn-xs mr5 agency_label" onclick="delAgency(this);" val=' + item.id + ' id="agency_' + item.id + '">' + item.text + ' <i class="fa fa-times"></i></a>';
                                $('.city_' + item.city_id).find('td').append(html);
                            }
                        }
                    }
                }, "json")
            }
        });

        $('#form-success').click(function() {//保存选择的分销商
            var agencyList = [];
            var exc = $("#exception-input").val();
            if (exc == "" || exc == undefined) {
                alert("保存失败,请重试!");
                $('#exception-1').modal('hide');
            }
            $('.agency_label').each(function() {
                agencyList.push($(this).attr('val'));
            });
            $("#" + exc).val(agencyList.join(','));
            $('#exception-1').modal('hide');
        });

        $('#exception-1').on('hidden.bs.modal', function(e) {
            $('#province-select').val('__NULL__');
            $("#distributor-select").val();
            $('#selected-distributor').html('<div class="panel panel-info none"><div style="text-align:center;padding:10px">暂无</div></div>');
        })

        function b(pid, cid, ptext, ctext) {
            $('.none').hide();
            $.post('/ajaxServer/getAgency/', {pid: pid, cid: cid}, function(data) {
                if (data.results.length <= 0) {
                    alert("选择的地区下暂无分销商!");
                    $('#selected-distributor').html('<div class="panel panel-info none"><div style="text-align:center;padding:10px">暂无</div></div>');
                    return false;
                }
                //先判断是否已存在该省,没有则增加
                if ($(".province_" + pid).length <= 0) {
                    var html = '<div class="panel panel-info agency-panel"><div class="panel-heading">' + ptext + '</div><div class="panel-body province_' + pid + '">';
                    html += '<div class="table-responsive"><table class="table table-bordered mb30"><tbody></tbody></table></div></div></div>';
                    $('#selected-distributor').append(html);
                }

                for (i in data.results) {
                    var item = data.results[i];
                    if ($(".city_" + item.city_id).length <= 0) {
                        $('.province_' + pid).find('table').append('<tr class="city_' + item.city_id + '"><th width="100">' + item.city_text + '</th><td></td></tr>');
                    }
                    if ($('#agency_' + item.id).length <= 0) {
                        html = '<a href="javascript:void(0);" class="btn btn-xs mr5 agency_label" onclick="delAgency(this);" val=' + item.id + ' id="agency_' + item.id + '">' + item.text + ' <i class="fa fa-times"></i></a>';
                        $('.city_' + item.city_id).find('td').append(html);
                    }
                }
            }, 'json');
        }

        /*!function(){
         var sd = $('#selected-distributor'),
         aab= $('#area-add-btn')
         
         function b(obj,val){
         var i
         sd.find('th').each(function(){
         if($(this).text()==val){
         i = $(this).parent().index()
         }
         })
         $('.none').hide()
         if(i){
         sd.find('tr').eq(i).find('td').append('<a href="" class="btn btn-xs mr5">'+obj.find('option:selected').text()+' <i class="fa fa-times"></i></a>')
         }else{
         sd.append('<tr><th width="100">'+val+'</th><td><a href="" class="btn btn-xs mr5">武义三峰 <i class="fa fa-times"></i></a></td></tr>')
         }
         }
         
         
         aab.click(function(){
         var obj = $('#area-select'),
         val = obj.find("option:selected").text()
         b(obj,val)
         })
         
         $('#distributor-select').change(function(){
         var obj = $(this),
         val = obj.val()
         b(obj,val)
         })
         }()*/


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
        spinner.spinner('value', 1);

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1, 'max': 100});
        spinnerMin.spinner('value', 1);

        var spinnerMax = jQuery('#spinner-max').spinner({'min': 1, 'max': 999});
        spinnerMax.spinner('value', 100);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        spinnerDay.spinner('value', 1);


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

        $("#distributor-select").select2({
            placeholder: "请输入例外的分销商",
            minimumInputLength: 1,
            ajax: {
                url: "/ajaxServer/agency",
                dataType: 'json',
                data: function(term, page) {
                    return {
                        term: term,
                        page_limit: 20
                    };
                },
                results: function(data, page) {
                    return {results: data.results};
                },
                type: 'post'
            },
            formatSelection: function(item) {
                var id = item.id;
                var name = '请输入例外的分销商';
                if (id != 0 && id != undefined) {
                    name = item.text;
                    var pid = item.province_id;
                    var cid = item.city_id;
                    var province = item.province_text;
                    var city = item.city_text;
                    $('.none').hide();
                    if ($(".province_" + pid).length <= 0) {
                        var html = '<div class="panel panel-info agency-panel"><div class="panel-heading">' + province + '</div><div class="panel-body province_' + pid + '">';
                        html += '<div class="table-responsive"><table class="table table-bordered mb30"><tbody></tbody></table></div></div></div>';
                        $('#selected-distributor').append(html);
                    }

                    if ($(".city_" + cid).length <= 0) {
                        $('.province_' + pid).find('table').append('<tr class="city_' + cid + '"><th width="100">' + city + '</th><td></td></tr>');
                    }
                    if ($('#agency_' + id).length <= 0) {
                        html = '<a href="javascript:void(0);" class="btn btn-xs mr5 agency_label" onclick="delAgency(this);" val=' + item.id + ' id="agency_' + id + '">' + name + ' <i class="fa fa-times"></i></a>';
                        $('.city_' + cid).find('td').append(html);
                    }
                }
                return name;
            },
            dropdownCssClass: "bigdrop",
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

        $('.ui-icon-triangle-1-n').remove();
        $('.ui-icon-triangle-1-s').remove();

        $('#return').click(function() {
            location.href = '/ticket/single';
        })

    });
</script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#remark-data').click(function() {
            $('input[name=remark]').val($('#myremark').val());
            $('#close-remark').trigger('click');

        })
    });
    jQuery(document).ready(function() {
        $('.submit').click(function() {
	        $('.submit').attr('disabled', 'disabled');
	        if ($('#form-data-ticket').validationEngine('validate') == true) {
                $.post('/ticket/electronic/saveElectronic', $('#form-data-ticket').serialize(), function(data) {
                    if (data.errors) {
	                    $('.submit').removeAttr('disabled');
                        var tmp_errors = data.errors;
                        var warn_msg = '<div class="alert alert-danger"> <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button> <i class="fa fa-ban-circle fa-lg"></i>' + tmp_errors + '</div>';
                        $('#show_msg').html(warn_msg);
                        location.href = '#show_msg';
                    } else if (data.succ) {
                        var succss_msg = '<div class="alert alert-success"> <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button> <i class="fa fa-check fa-lg"></i>保存成功</div>';
                        $('#show_msg').html(succss_msg);
                        location.href = '#show_msg';
                        setTimeout("location.href='/ticket/single/'", '2000');
                    }

                }, "json");
            }
	        $('.submit').removeAttr('disabled');
	        return false;
        })
    });
</script>
