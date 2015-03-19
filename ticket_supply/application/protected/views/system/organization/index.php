<?php
$this->breadcrumbs = array('系统管理', '用户信息');
?>
<link rel="stylesheet" href="/css/jquery.nailthumb.1.1.css">
<div id="show_msg">

</div>
<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">用户信息</h4>
                </div><!-- panel-heading -->

                <div class="panel-body nopadding">

                    <form class="form-horizontal form-bordered" id="form-data-supply">
                        <?php if ($organizations): ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span>机构名称</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control validate[required,minSize[4],maxSize[20]]" name="name" value="<?php echo $organizations['name'] ?>"/>
                                </div>
                                <label class="col-sm-2 control-label">公司传真</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control validate[custom[phone],maxSize[20]]" name="fax" value="<?php echo $organizations['fax'] ?>"/>
                                </div>
                            </div><!-- form-group -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger" >*</span> 联系人</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control validate[required,custom[chinese],custom[NoSp],minSize[1],maxSize[20]]" name="contact" value="<?php echo $organizations['contact'] ?>"/>
                                </div>
                                <label class="col-sm-2 control-label">固定电话</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control validate[custom[phone],maxSize[20]]" name="telephone" value="<?php echo $organizations['telephone'] ?>"/>
                                </div>
                            </div><!-- form-group -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control  validate[required,custom[mobile]]" name="mobile" value="<?php if($organizations['mobile']) echo $organizations['mobile']; else{ $user = Users::model()->find('id=:id',['id' => Yii::app()->user->uid]); echo $user->mobile; }  ?>"/>
                                </div>
                                <label class="col-sm-2 control-label">审核状态</label>
                                <div class="col-sm-4">
                                    <?php if ($organizations['verify_status'] == 'checked'): ?><button class="btn btn-success btn-sm btn-bordered" disabled>已审核</button><?php elseif ($organizations['verify_status'] == 'reject'): ?><button class="btn btn-sm btn-bordered" style=" border-color: #ff0000; color: #ff0000;" disabled>拒绝</button><?php else: ?><button class="btn btn-sm btn-bordered" disabled style=" border-color: #ff0000; color: #ff0000;">未审核</button><?php endif; ?>
                                </div>
                            </div><!-- form-group -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 所在地区</label>
                                <div class="col-sm-4">
                                    <select class="select2 col-sm-4"  data-validation-engine="form-control validate[required]" data-placeholder="Choose One" id="province" name="province_id">
                                        <?php $pro = Districts::model()->findByPk($organizations['province_id']); ?>
                                        <option value="<?php echo $pro['id'] ?>" selected="selected" ><?php echo $pro['id'] == 0 ? "省" : $pro['name'] ?></option>
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
                                        <?php $city = Districts::model()->findByPk($organizations['city_id']); ?>
                                        <option value="<?php echo $city['id'] ?>" selected="selected" ><?php echo $city['id'] == 0 ? "市" : $city['name'] ?></option>
                                    </select>
                                    <select class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id">
                                        <?php $district = Districts::model()->findByPk($organizations['district_id']); ?>
                                        <option value="<?php echo $district['id'] ?>" selected="selected" ><?php echo $district['id'] == 0 ? "县" : $district['name'] ?></option>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">启用状态</label>
                                <div class="col-sm-4">
                                    <?php if ($organizations['status'] == 1): ?><button class="btn btn-success btn-sm btn-bordered" disabled>已启用</button><?php else: ?><button class="btn btn-sm btn-bordered" disabled style=" border-color: #ff0000; color: #ff0000;">未启用</button><?php endif; ?>
                                </div>
                            </div><!-- form-group -->



                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 详细地址</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="" class="form-control validate[required,minSize[4],maxSize[20]]" name="address" value="<?php echo $organizations['address'] ?>"/>
                                    <input type="hidden" name="organization_id" value="<?php echo $organizations['id'] ?>" />
                                </div>
                            </div><!-- form-group -->

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <h5 class="lg-title mb10"><span class="text-danger">*</span>营业执照</h5>
                                    <div class="dropzone" id="business_license">
                                        <div class="fallback">
                                            <img id="business_license_img"  src="<?php
                                            if (!empty($organizations['business_license'])) {
                                                echo $organizations['business_license'];
                                            } else {
                                                echo '/img/uploadfile.png';
                                            }
                                            ?>" style="max-width:150px;height:150px;position:absolute;left:0px;top:0px">
                                            <input type="hidden" class="sp_sxming" name="business_license" value="<?php echo $organizations['business_license'] ?>"/>
                                            <input type="hidden" name="id" value="<?php echo $organizations['id'] ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div><!-- form-group -->
<?php endif; ?>
                        <div class="panel-footer">
                            <button class="btn btn-primary mr5" id="putform">保存</button>
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

        //jQuery('select option:first-child').text('');
        //$('#province option:first-child').html('省').val("__NULL__");
        //$('#city option:first-child').html('市').val("__NULL__");
        //$('#area option:first-child').html('县').val("__NULL__");

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


        $('#province').change(function() {

            var code = $(this).val();

            $('#city').html('<option value="__NULL__">市</option>');
            $('#area').html('<option value="__NULL__">县</option>');
            if (code == '__NULL__') {
                $('#city').html('<option value="__NULL__">市</option>');
            } else {
                $('#city').html('<option value="__NULL__">市</option>');
                var html = new Array();
                $.post('/ajaxServer/GetChildern' ,  {id:code}, function(data) {
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
            //$('#uniform-area span:first-child').html('县');
            if (code == '__NULL__') {
                $('#area').html('<option value="__NULL__">县</option>');
            } else {
                $('#area').html('<option value="__NULL__">县</option>');
                var html = new Array();
                $.post('/ajaxServer/GetChildern',  {id:code}, function(data) {
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
<!-- 省市县三级联动-->
<script>
//省联动


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

    window.upload_callback = function(data) {
        if (data.status != 200) {
            alert('上传失败！');
            return false;
        }
        $('input[name=' + window.imgField + ']').val(data.msg);
        $('#' + window.imgField + '_img').attr('src', data.msg);
    }

    $('#putform').click(function() { 
        var license = $('input[name="business_license"]').val(); 
        if ($('#form-data-supply').validationEngine('validate') == true && license.length > 0) {
            $.post('/system/organization/saveSupply', $('#form-data-supply').serialize(), function(data) {
                if (data.errors) {
                    var tmp_errors = data.errors;
                    alert(tmp_errors);
                } else if (data.succ) {
                    alert('保存成功');
                    window.location.reload();
                }
            }, "json");
        }else if(license.length <= 0){
            alert('请上传营业执照');
        }

        return false;
    })


</script>
