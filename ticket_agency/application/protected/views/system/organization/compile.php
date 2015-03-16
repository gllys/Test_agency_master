<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/15/14
 * Time: 1:50 PM
 */
$this->breadcrumbs = array('系统管理', '注册分销商');
?>   
<link rel="stylesheet" href="/css/jquery.nailthumb.1.1.css">  
<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="Minimize Panel"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="Close Panel"><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">用户信息</h4>
                </div><!-- panel-heading -->

                <div class="panel-body nopadding">

                    <form id="form-data-agency" class="form-horizontal form-bordered">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div>
                                        <form class="form-horizontal form-bordered">

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 机构名称</label>
                                                <div class="col-sm-4">
                                                    <input type="text" placeholder="" class="form-control validate[required,minSize[4],maxSize[20]]" name="name"/>
                                                </div>
                                                <label class="col-sm-2 control-label">联系邮箱</label>
                                                <div class="col-sm-4">
                                                    <input type="text" placeholder="" class="form-control validate[custom[email]]" name="email"/>
                                                </div>
                                            </div><!-- form-group -->

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 联系人</label>
                                                <div class="col-sm-4"> 
                                                    <input type="text" placeholder="" class="form-control validate[required,custom[chinese],custom[NoSp],minSize[1],maxSize[20]]" name="contact"/>
                                                </div>
                                                <label class="col-sm-2 control-label">机构传真</label>
                                                <div class="col-sm-4">
                                                    <input type="text" placeholder="" class="form-control validate[maxSize[20]]" name="fax"/>
                                                </div>
                                            </div><!-- form-group -->

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 所在地区</label>
                                                <div class="col-sm-4">
                                                    <select class="select2 col-sm-4" data-placeholder="Choose One" id="province" name="province_id" >
                                                        <option value="__NULL__" selected="selected" >省</option>
                                                        <?php
                                                        $province = Districts::model()->findAllByAttributes(array("parent_id" => 0));
                                                        foreach ($province as $model) {
                                                            if ($model->id == 0) {
                                                                continue;
                                                            } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                                                        }
                                                        ?> 
                                                    </select>
                                                    <select class="select2 col-sm-4" data-placeholder="Choose One" id="city" name="city_id">
                                                        <option value="__NULL__" selected="selected" >市</option>
                                                    </select>
                                                    <select class="select2 col-sm-4" data-placeholder="Choose One" id="area"  name="district_id">
                                                        <option value="__NULL__" selected="selected">县</option>													
                                                    </select>
                                                </div>
                                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 手机号码</label>
                                                <div class="col-sm-4">
                                                    <input type="text" placeholder="" class="form-control validate[required,custom[mobile]]" name="mobile"/>
                                                </div>
                                            </div><!-- form-group -->

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 详细地址</label>
                                                <div class="col-sm-4">
                                                    <input type="text" placeholder="" class="form-control validate[required,minSize[4],maxSize[20]]" name="address"/>
                                                </div>
                                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 是否旅行社</label>
                                                <div class="col-sm-4">
                                                    <div class="rdio rdio-default">
                                                        <input type="radio" value="1" checked="checked" id="radioDefault" name="agency_type">
                                                        <label for="radioDefault">是</label>
                                                    </div>
                                                    <div class="rdio rdio-default">
                                                        <input type="radio"  value="0" id="radioDefault1" name="agency_type">
                                                        <label for="radioDefault1">否</label>
                                                    </div>
                                                </div>
                                            </div><!-- form-group -->


                                            <div class="form-group">
                                                <div class="col-sm-4" >
                                                    <h5 class="lg-title mb10"><!--span class="text-danger">*</span--> 营业执照</h5>
                                                    <div class="dropzone" id="business_license">
                                                        <div class="fallback">
                                                            <img id="business_license_img"  src="/img/uploadfile.png" style="max-width:150px;height:150px;position:absolute;left:0px;top:0px" />
                                                            <input type="hidden" class="sp_sxming validate[required]" name="business_license" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="tax">
                                                    <h5 class="lg-title mb10"><!--span class="text-danger">*</span--> 税务登记证</h5>
                                                    <div class="dropzone" id="tax_license">
                                                        <div class="fallback">
                                                            <img id="tax_license_img"  src="/img/uploadfile.png" style="max-width:150px;height:150px;position:absolute;left:0px;top:0px" />
                                                            <input type="hidden" class="sp_sxming" name="tax_license" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="certificate">
                                                    <h5 class="lg-title mb10"><!--span class="text-danger">*</span--> 经营许可证</h5>
                                                    <div class="dropzone" id="certificate_license">
                                                        <div class="fallback">
                                                            <img id="certificate_license_img"  src="/img/uploadfile.png" style="max-width:150px;height:150px;position:absolute;left:0px;top:0px" />
                                                            <input type="hidden" class="sp_sxming" name="certificate_license" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- form-group -->
                                            <div class="panel-footer">
                                                <button class="btn btn-primary mr5 submit">保存</button>
                                            </div>
                                        </form>    

                                    </div><!-- panel-body -->      

                                </div><!-- panel -->

                            </div><!-- col-md-6 -->
                        </div><!-- row -->
                </div><!-- contentpanel -->
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

                        jQuery('select option:first-child').text('');
                        $('#province option:first-child').html('省').val("__NULL__");
                        $('#city option:first-child').html('市').val("__NULL__");
                        $('#area option:first-child').html('县').val("__NULL__");

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
                        $("input[name=agency_type]").click(function() {
                            var vals = $("input[type='radio']:checked").val();
                            if (vals == 0) {
                                $("#tax").hide();
                                $("#certificate").hide();
                            }
                            if (vals == 1) {
                                $("#tax").show();
                                $("#certificate").show();
                            }
                        });

                        //$('.header-left').hide();
                        $('.header-right ul').hide();
                        $('.btn-group-list').hide();
                        $('.leftpanel').hide();
                        //省联动
                        $('#province').change(function() {

                            var code = $(this).val();

                            $('#city').html('<option value="__NULL__">市</option>');
                            $('#area').html('<option value="__NULL__">县</option>');
                            if (code == '__NULL__') {
                                $('#city').html('<option value="__NULL__">市</option>');
                            } else {
                                $('#city').html('<option value="__NULL__">市</option>');
                                var html = new Array();
                                $.post('/ajaxServer/GetChildern', {id: code}, function(data) {
                                    
                                    for (i in data) {
                                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                                    }
                                    $('#city').append(html.join(''));
                                    $('#city,#area').select2();
                                }, 'json');
                            }
                            return false;
                        });


                        //市切换
                        $('#city').change(function() {
                            var code = $(this).val();
                            if (code == '__NULL__') {
                                $('#area').html('<option value="__NULL__">县</option>');
                            } else {
                                $('#area').html('<option value="__NULL__">县</option>');
                                var html = new Array();
                                $.post('/ajaxServer/GetChildern', {id : code}, function(data) {
                                    
                                    for (i in data) {
                                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                                    }
                                    $('#area').append(html.join(''));
                                    $('#area').select2();
                                }, 'json');
                            }
                            return false;
                        });
                    });
                </script>
                <!-- 图片上传-->
                <script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
                <script type="text/javascript" src="/js/jquery.nailthumb.1.1.js"></script>
                <script type="text/javascript">
                    //上传
                    window.imgField = '';
                    new AjaxUpload('#business_license', {
                        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                        name: 'file',
                        onSubmit: function(file, ext) {
                            //上传文件格式限制
                            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                                alert('上传格式不正确');
                                return false;
                            }
                            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                            window.imgField = 'business_license';
                        },
                        onComplete: function(file, data) {
                        }
                    });

                    new AjaxUpload('#tax_license', {
                        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                        name: 'file',
                        onSubmit: function(file, ext) {
                            //上传文件格式限制
                            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                                alert('上传格式不正确');
                                return false;
                            }
                            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                            window.imgField = 'tax_license';
                        },
                        onComplete: function(file, data) {
                        }
                    });

                    new AjaxUpload('#certificate_license', {
                        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                        name: 'file',
                        onSubmit: function(file, ext) {
                            //上传文件格式限制
                            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                                alert('上传格式不正确');
                                return false;
                            }
                            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                            window.imgField = 'certificate_license';
                        },
                        onComplete: function(file, data) {
                        }
                    });

                    window.upload_callback = function(data) {
                        if (data.status != 200) {
                            alert('上传失败！');
                            return false;
                        }
                        $('input[name=' + window.imgField + ']').val(data.msg);
                        $('#' + window.imgField + '_img').attr('src', data.msg);

                    }

                    $('.submit').click(function() {
                        if ($('#form-data-agency').validationEngine('validate') == true) {
                            $.post('/system/organization/saveAgency', $('#form-data-agency').serialize(), function(data) {
                                if (data.errors) {
                                    var tmp_errors = data.errors;
                                    alert(tmp_errors);
                                } else if (data.succ) {
                                    alert('保存成功');
                                    setTimeout("location.href='/site/logout'", '1000');
                                }
                            }, "json")
                        }
                        return false;
                    })

                </script>
