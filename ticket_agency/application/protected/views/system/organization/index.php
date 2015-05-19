<?php
$this->breadcrumbs = array('系统管理', '用户信息');
?>
<link rel="stylesheet" href="/css/jquery.nailthumb.1.1.css">
<style>
	img{position:absolute;top:0px;left:0px;}
</style>
<div id="show_msg"></div>
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
                <?php if($info):?>
                <div class="panel-body nopadding">
                    <form class="form-horizontal form-bordered"  id="form-data-agency">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">编号</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control" name="id" value="<?php echo $info['id']?>" readonly/>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>机构名称</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control validate[required,minSize[4],maxSize[20]]" readonly="readonly" name="name" value="<?php echo $info['name']?>"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label">机构简称</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control" name="abbreviation" value="<?php echo $info['abbreviation']?>"/>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 手机号码</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control validate[required,custom[mobile]]" name="mobile"  value="<?php echo $info['mobile']?>"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 联系人</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" name="contact" class="form-control validate[required,chinese,NoSp,minSize[1],maxSize[20]]" value="<?php echo $info['contact']?>"/>
                            </div>
                            <label class="col-sm-2 control-label">机构传真</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control validate[custom[phone],maxSize[20]]" name="fax" value="<?php echo $info['fax']?>"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系邮箱</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control validate[custom[email]]" name="email" value="<?php echo $info['email']?>"/>
                            </div>
                            <label class="col-sm-2 control-label">固定电话</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control validate[custom[phone],maxSize[20]]" name="telephone" value="<?php echo $info['telephone']?>"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
	                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 所在地区</label>
	                    <div class="col-sm-4">
							<select class="select2 col-sm-4" data-placeholder="Choose One" id="province" name="province_id" >
								<?php $pro = Districts::model()->findByPk($info['province_id']);?>
								<option value="<?php echo $pro['id']?>" selected="selected" ><?php echo $pro['id']==0?"省":$pro['name']?></option>
								<?php
								$province = Districts::model()->findAllByAttributes(array("parent_id"=>0));
									foreach ($province as $model) {
                                    	if ($model->id == 0 || $model->id == $info['province_id']) {
                                        	continue;
                                    	} echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                                	}
                                ?> 
							</select>
							<select class="select2 col-sm-4" data-placeholder="Choose One" id="city" name="city_id">
								<?php $city = Districts::model()->findByPk($info['city_id']);?>
								<option value="<?php echo $city['id']?>" selected="selected" ><?php echo $city['id']==0?"市":$city['name']?></option>
							</select>
							<select class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id">
								<?php $district = Districts::model()->findByPk($info['district_id']);?>
								<option value="<?php echo $district['id']?>" selected="selected" ><?php echo $district['id']==0?"县":$district['name']?></option>
							</select>
	                    </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 详细地址</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="form-control validate[required,minSize[4],maxSize[20]]" name="address" value="<?php echo $info['address']?>"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="disabledinput">公司简介</label>
                            <div class="col-sm-10">
                                <textarea class="form-control validate[maxSize[200]]]" rows="5" placeholder="仅限200字以内"  name="description" ><?php echo $info['description']?></textarea>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="disabledinput">账号状态</label>
                            <div class="col-sm-4" style="text-align:left">
                                <button class="btn btn-success btn-sm btn-bordered" disabled><?php if($info['status'] == 1){ echo '已启用';}else{ echo '未启用';}?></button>
                            </div>
                        </div><!-- form-group -->


                         <div class="form-group">
                            <div class="col-sm-4">
								<h5 class="lg-title mb10">营业执照</h5>
								<div class="dropzone" id="business_license">
									<div class="fallback">
										<img id="business_license_img" src="<?php if(!empty($info['business_license'])){ echo $info['business_license'];}else{ echo '/img/uploadfile.png';}?>" style="max-width:150px;height:150px;position:absolute;top:0px;left:0px;">
                                        <input type="hidden" class="sp_sxming form-control validate[required]" name="business_license" value="<?php echo $info['business_license']?>"/>
									</div>
								</div>
                            </div>
                            <div class="col-sm-4" <?php echo $info['agency_type'] == 1 ? '' : 'hidden'?>>
								<h5 class="lg-title mb10">税务登记证</h5>
								<div class="dropzone" id="tax_license">
									<div class="fallback">
										<img id="tax_license_img" src="<?php if(!empty($info['tax_license'])){ echo $info['tax_license'];}else{ echo '/img/uploadfile.png';}?>" style="max-width:150px;height:150px;position:absolute;top:0px;left:0px;">
                                        <input type="hidden" class="sp_sxming" name="tax_license" value="<?php echo $info['tax_license']?>"/>
									</div>
								</div>
                            </div>
                            <div class="col-sm-4"  <?php echo $info['agency_type'] == 1 ? '' : 'hidden'?>>
								<h5 class="lg-title mb10">经营许可证</h5>
								<div class="dropzone" id="certificate_license">
									<div class="fallback">
										<img id="certificate_license_img" src="<?php if(!empty($info['certificate_license'])){ echo $info['certificate_license'];}else{ echo '/img/uploadfile.png';}?>" style="max-width:150px;height:150px;position:absolute;top:0px;left:0px;">
                                        <input type="hidden" class="sp_sxming" name="certificate_license" value="<?php echo $info['certificate_license']?>"/>
									</div>
								</div>
                            </div>
                        </div><!-- form-group -->
                    <?php endif;?>
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

        $('.submit').click(function(){
        	if($('#form-data-agency').validationEngine('validate')==true){
        		$.post('/system/organization/saveAgency',$('#form-data-agency').serialize(),function(data){
        			if(data.errors){
        				var tmp_errors = data.errors;
						alert(tmp_errors);
        			}else if(data.succ){
        				alert('保存成功');
						window.location.reload();
        			}        				        			
        		},"json")
        	}
        	return false;
        })

    });
</script>
<!-- 省市县三级联动-->
<script>
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
            $.post('/ajaxServer/GetChildern',{id : code}, function(data) {
                
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
            $.post('/ajaxServer/GetChildern',{id : code}, function(data) {
                
                for (i in data) {
                    html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                }
                $('#area').append(html.join(''));
                $('#area').select2();                
            }, 'json');
        }
        return false;
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

</script>
<!--script type="text/javascript">
function AutoResizeImage(maxWidth,maxHeight,objImg){
	var img = new Image();
	img.src = objImg.src;
	var hRatio;
	var wRatio;
	var Ratio = 1;
	var w = img.width;
	var h = img.height;
	wRatio = maxWidth / w;
	hRatio = maxHeight / h;
	if (maxWidth ==0 && maxHeight==0){
	Ratio = 1;
	}else if (maxWidth==0){//
	if (hRatio<1) Ratio = hRatio;
	}else if (maxHeight==0){
	if (wRatio<1) Ratio = wRatio;
	}else if (wRatio<1 || hRatio<1){
	Ratio = (wRatio<=hRatio?wRatio:hRatio);
	}
	if (Ratio<1){
	w = w * Ratio;
	h = h * Ratio;
	}
	objImg.height = h;
	objImg.width = w;
}
</script-->


