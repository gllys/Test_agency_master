<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <ul class="list-inline">
                        <li><h4 class="panel-title">添加分销商</h4></li>
                        <li><a href="/order/history/help?#7.1" title="帮助文档" class="clearPart"
                               target="_blank">查看帮助文档</a> </li>
                    </ul>
                </div><!-- panel-heading -->

                <div class="panel-body nopadding">                  
                    <form class="form-horizontal form-bordered" action="" method="post" id="agencyadd">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 用户名</label>
                                    <div class="col-sm-6">
                                        <input type="hidden" name="agencyname" disabled>
                                        <input type="text" placeholder="" class="form-control validate[required, minSize[6], maxSize[20], custom[numChinese]]" maxlength="20" tag="用户名" name="agencyname" id="agencyname" />
									</div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>密码</label>
                                    <div class="col-sm-6">
										<input type="password" class="form-control validate[custom[onlyLetterNumber],required,minSize[6],maxSize[16]]" maxlength="16" tag="密码" name="password" id="password"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" class="form-control  validate[required,custom[mobile]]" tag="手机号码" name="mobile" />
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>公司名称</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" class="form-control validate[required,custom[chiMark],minSize[4],maxSize[40]]" maxlength="40" tag="公司名称" name="name"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>联系人</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" class="form-control validate[required,custom[chinese],minSize[4],maxSize[20]]" maxlength="20" tag="联系人" name="contact" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>所在地区</label>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="select2" data-placeholder="Choose One" id="province" name="province_id" style="width:120px;height:34px;">
                                                    <option value="__NULL__">省</option>
                                                    <?php
                                                    $province = Districts::model()->findAllByAttributes(array("parent_id" => 0));
                                                    foreach ($province as $model) {
                                                        if ($model->id == 0) {
                                                            continue;
                                                        } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="select2" data-placeholder="Choose One" id="city" name="city_id" style="width:120px;height:34px;">
                                                    <option value="__NULL__">市</option>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <select class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id" style="width:120px;height:34px;">
                                                    <option value="__NULL__">县</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">电话</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input type="text" placeholder="区号" class="validate[custom[integer],minSize[3],maxSize[4]] form-control" tag="区号" name="phoneditribute" id="phoneditribute"/>
                                            </div>
											<div class="col-md-6">
												<input type="text" placeholder="电话" class="validate[custom[phone]] form-control" tag="电话" name="phonenum" id="phonenum"/>
											</div>
											<div class="col-sm-3">
                                                <input type="text" placeholder="分机" class="validate[custom[integer],maxSize[6]] form-control" tag="分机" name="phoneextension" id="phoneextension"/>
											</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">传真</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input type="text" placeholder="区号" class="validate[custom[integer],minSize[3],maxSize[4]] form-control" tag="区号" name="faxditribute" id="phoneditribute"/>
                                            </div>
											<div class="col-sm-6">
												<input type="text" placeholder="传真" class="validate[custom[fax]] form-control" tag="传真" name="faxnum" id="phonenum"/>
											</div>
											<div class="col-sm-3">
												<input type="text" placeholder="分机" class="validate[custom[integer],maxSize[6]] form-control" tag="分机" name="faxextension" id="phoneextension"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>详细地址</label>
                                    <div class="col-sm-6">
										<input type="text" placeholder="" class="form-control validate[required,custom[NoSp],minSize[4]]" maxlength="100" tag="详细地址" name="address"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 是否旅行社</label>
                                    <div class="col-sm-6">
                                        <div class="rdio rdio-default">
                                            <input type="radio" value="1" id="radioDefault" name="agency_type" class="validate[required]" checked="checked">
                                            <label for="radioDefault">是</label>
                                        </div>
                                        <div class="rdio rdio-default">
                                            <input type="radio" value="0" id="radioDefault1" name="agency_type" class="validate[required]">
                                            <label for="radioDefault1">否</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">营业执照</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="business_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="business_license_img" src="/img/uploadfile.png" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" tag="营业执照" name="business_license" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="dispalyn1">
                                    <label class="col-sm-2 control-label">税务登记证</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="tax_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="tax_license_img" src="/img/uploadfile.png" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" tag="税务登记证" name="tax_license" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="dispalyn2">
                                    <label class="col-sm-2 control-label">经营许可证</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="certificate_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="certificate_license_img" src="/img/uploadfile.png" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" tag="经营许可证" name="certificate_license" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->
                            </div>
                        </div>

                        <div class="panel-footer" style="padding-left:5%">
                            <button class="btn btn-primary mr20" id="agencysub">确认添加</button>
                            <a class="btn btn-default" href="/agency/account/">取消返回</a>
                        </div>

                    </form>
                </div>
                <!-- panel-body -->



            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->

</div>
<script>
    $(function() {

                $("input[name=agency_type]").click(function () {
                    var vals = $("input[type='radio']:checked").val();
                    if (vals == 0) {
                        $("#dispalyn1").hide();
                        $("#dispalyn2").hide();
                    }
                    if (vals == 1) {
                        $("#dispalyn1").show();
                        $("#dispalyn2").show();
                    }
                });
            });
		</script>


		<!-- jquery.validationEngine-zh-CN.js 为配置文件，可根据需求自行调整或增加，也可以更换为其他语言配置文件 --> 
		<script>
            $(document).ready(function () {                
                // 表单验证
                $('#agencyadd').validationEngine('attach', {
                    promptPosition: 'topRight',
                    addFailureCssClassToField: 'error',
                    autoHidePrompt: true,
		            maxErrorsPerField: 1,
                    autoHideDelay: 3000
                });

                $('#agencysub').click(function () {
                    $('#show_msg').empty();
                    var obj = $('#agencyadd');
                    if (obj.validationEngine('validate') == true) {
                        $.post('/agency/account/add', obj.serialize(), function (data) {
                            if (data.error == 1) {
                                var msg = "";
                                if (data.msg.constructor == Object) {
                                    for (k in data.msg) {
                                        msg += data.msg[k][0];
                                    }
                                } else {
                                    msg = data.msg;
                                }
                                alert(msg);
                            } else if(data.error == 2) {
								$('#agencyadd [name=province_id]').PWShowPrompt(data.msg); 
							} else if(data.error == 3) {
								$('#agencyadd [name=agencyname]').PWShowPrompt(data.msg); 
							} else {
                                alert('保存成功');
                                location.href = '/#'+ '/agency/manager/';
                            }
                        }, 'json');

                    };
                    return false;
                });
                $('#agencyname').val(' ');
                setTimeout(function(){$('#agencyname').val($('#agencyname').val().trim());},50);
            });

            jQuery(document).ready(function () {
                // Tags Input
                jQuery('#tags').tagsInput({width: 'auto'});

                // Spinner
                var spinner = jQuery('#spinner').spinner();
                spinner.spinner('value', 0);

                // Form Toggles
                jQuery('.toggle').toggles({on: true});

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
                    escapeMarkup: function (m) {
                        return m;
                    }
                });

                // Color Picker
                if (jQuery('#colorpicker').length > 0) {
                    jQuery('#colorSelector').ColorPicker({
                        onShow: function (colpkr) {
                            jQuery(colpkr).fadeIn(500);
                            return false;
                        },
                        onHide: function (colpkr) {
                            jQuery(colpkr).fadeOut(500);
                            return false;
                        },
                        onChange: function (hsb, hex, rgb) {
                            jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
                            jQuery('#colorpicker').val('#' + hex);
                        }
                    });
                }
                jQuery('select option:first-child').text('');
                $('#province option:first-child').html('省').val("__NULL__");
                $('#city option:first-child').html('市').val("__NULL__");
                $('#area option:first-child').html('县').val("__NULL__");
                // Color Picker Flat Mode
                jQuery('#colorpickerholder').ColorPicker({
                    flat: true,
                    onChange: function (hsb, hex, rgb) {
                        jQuery('#colorpicker3').val('#' + hex);
                    }
                });


                //省联动
                $('#province').change(function () {
                    var code = $(this).val();
                    $('#city').html('<option value="__NULL__" selected>市</option>');
                    $('#area').html('<option value="__NULL__" selected>县</option>');
                    if (code == '__NULL__') {
                        $('#city').html('<option value="__NULL__" selected>市</option>');
                    } else {
                        $('#city').html('<option value="__NULL__" selected>市</option>');
                        var html = new Array();
                        $.get('/ajaxServer/GetChildern/id/' + code, function (data) {
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
                $('#city').change(function () {
                    var code = $(this).val();
                    if (code == '__NULL__') {
                        $('#area').html('<option value="__NULL__" selected>县</option>');
                    } else {
                        $('#area').html('<option value="__NULL__" selected>县</option>');
                        var html = new Array();
                        $.get('/ajaxServer/GetChildern/id/' + code, function (data) {
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
		<script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
		<script type="text/javascript">
            //上传
            window.imgField = '';
            new AjaxUpload('#business_license', {
                action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                name: 'file',
                onSubmit: function (file, ext) {
                    //上传文件格式限制
                    if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                        alert('上传格式不正确');
                        return false;
                    }
                    this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                    window.imgField = 'business_license';
                },
                onComplete: function (file, data) {
                }
            });

            new AjaxUpload('#tax_license', {
                action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                name: 'file',
                onSubmit: function (file, ext) {
                    //上传文件格式限制
                    if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                        alert('上传格式不正确');
                        return false;
                    }
                    this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                    window.imgField = 'tax_license';
                },
                onComplete: function (file, data) {
                }
            });

            new AjaxUpload('#certificate_license', {
                action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                name: 'file',
                onSubmit: function (file, ext) {
                    //上传文件格式限制
                    if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                        alert('上传格式不正确');
                        return false;
                    }
                    this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                    window.imgField = 'certificate_license';
                },
                onComplete: function (file, data) {
                }
            });

            window.upload_callback = function (data) {
                if (data.status != 200) {
                    alert('上传失败！');
                    return false;
                }
                $('input[name=' + window.imgField + ']').val(data.msg);
                $('#' + window.imgField + '_img').attr('src', data.msg);

            }
		</script>