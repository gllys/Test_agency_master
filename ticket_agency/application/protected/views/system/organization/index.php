<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title">用户信息</h4>
                </div>
                <div id="show_msg"></div>
                <!-- panel-heading -->
                <?php if(isset($info) && !empty($info)):?>
                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="form-data-agency">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>编号</label>
                                    <div class="col-sm-6">
                                        <input style="cursor: pointer;cursor: hand;background-color: #ffffff" type="text" placeholder="" class="form-control" name="id" value="<?php echo $info['id']?>" readonly/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>机构名称</label>
                                    <div class="col-sm-6">
                                        <input style="cursor: pointer;cursor: hand;background-color: #ffffff" type="text" placeholder="" class="form-control validate[required,maxSize[20]]" name="name" value="<?php echo $info['name']?>" readonly/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 联系人</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="联系人" class="form-control validate[required,custom[chinese],custom[NoSp],minSize[1],maxSize[20]]" name="contact" value="<?php echo $info['contact']?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                    <div class="col-sm-6">
                                        <input maxlength="11" type="text" placeholder="" tag="手机号码" class="form-control  validate[required,custom[mobile]]" name="mobile" value="<?php echo $info['mobile']?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">机构传真</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="机构传真" class="form-control validate[custom[fax],maxSize[20]]" name="fax" value="<?php echo $info['fax']?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">固定电话</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="固定电话" class="form-control validate[custom[phone],maxSize[20]]" name="telephone" value="<?php echo $info['telephone']?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">联系邮箱</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="联系邮箱" class="form-control validate[custom[email],maxSize[20]]" name="email" value="<?php echo $info['email']?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->



                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 详细地址</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="详细地址" data-validation-engine="form-control validate[required,minSize[4],maxSize[50]]" class="form-control validate[required,minSize[4],maxSize[50]]" name="address" value="<?php echo $info['address']?>"/>
                                        <input type="hidden" name="organization_id" value="3" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">所在地区</label>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="select2 form-control validate[required]" data-placeholder="Choose One" id="province" name="province_id">
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
                                                <select class="select2" data-placeholder="Choose One" id="city" name="city_id">
                                                    <option value="__NULL__">市</option>
                                                    <?php
                                                        if(isset($info['province_id'])){
                                                            $city_value = $info['province_id'];
                                                            $city = Districts::model()->findAllByAttributes(array("parent_id" => $city_value));
                                                            foreach ($city as $model) {
                                                                if ($model->id == 0) {
                                                                    continue;
                                                                } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <select class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id">
                                                    <option value="__NULL__">县</option>
                                                    <?php
                                                        if(isset($info['city_id'])){
                                                            $area_value = $info['city_id'];
                                                            $area = Districts::model()->findAllByAttributes(array("parent_id" => $area_value));
                                                            foreach ($area as $model) {
                                                                if ($model->id == 0) {
                                                                    continue;
                                                                } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">公司简介</label>
                                    <div class="col-sm-6">
                                        <textarea maxlength="200" placeholder="仅限200字以内" tag="公司简介"  name="description" class="form-control"><?php echo $info['description']?></textarea>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">账号状态</label>
                                    <div class="col-sm-6">
                                        <button class="btn btn-success btn-sm btn-bordered" type="button"><?php echo $info['status'] == 1 ? '已启用' : '未启用' ?></button>
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
                                                <img id="business_license_img"  src="<?php echo !empty($info['business_license']) ? $info['business_license'] : '/img/uploadfile.png' ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="business_license"  value="<?php echo $info['business_license']?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="<?php echo $info['agency_type'] == 1 ? '' : 'display:none'?>">
                                    <label class="col-sm-2 control-label">税务登记证</label>
                                    <div class="col-sm-6" id="dispalyn1">
                                        <div class="dropzone" id="tax_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="tax_license_img"  src="<?php echo !empty($info['tax_license']) ? $info['tax_license'] : '/img/uploadfile.png' ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="tax_license" value="<?php echo $info['tax_license']?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="<?php echo $info['agency_type'] == 1 ? '' : 'display:none'?>">
                                    <label class="col-sm-2 control-label">经营许可证</label>
                                    <div class="col-sm-6" id="dispalyn2">
                                        <div class="dropzone" id="certificate_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="certificate_license_img"  src="<?php echo !empty($info['certificate_license']) ? $info['certificate_license'] : '/img/uploadfile.png' ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="certificate_license" value="<?php echo $info['certificate_license']?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- form-group -->
                            </div>
                        </div>

                        <div class="panel-footer" style="padding-left:5%">
                            <img src="/img/select2-spinner.gif" id="load" style="display: none" >
                            <button type="button" class="btn btn-primary mr20" id="putform">保存</button>
                        </div>

                    </form>
                </div>
                <!-- panel-body -->
                <?php endif;?>



            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->

</div>

<script>
    jQuery(document).ready(function() {
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });
        /*
        省市县显示问题重置 create by ccq
        */
        $('#province').select2("val","<?php echo isset($info['province_id']) && !empty($info['province_id']) ? $info['province_id'] : '__NULL__'?>");
        $('#city').select2("val","<?php echo isset($info['city_id']) && !empty($info['city_id']) ? $info['city_id'] : '__NULL__'?>");
        $('#area').select2("val","<?php echo isset($info['district_id']) && !empty($info['district_id']) ? $info['district_id'] : '__NULL__'?>");


        //省联动
        $('#province').change(function() {

            var code = $(this).val();

            $('#city').html('<option value="__NULL__">市</option>');
            $('#area').html('<option value="__NULL__">县</option>');
            if (code == '__NULL__') {
                /*
                 需要重置页面上的显示
                 */
                $('#city').select2('val','__NULL__');
                $('#area').select2('val','__NULL__');
                $('#city').html('<option value="__NULL__">市</option>');
                $('#area').html('<option value="__NULL__">县</option>');
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
            if (code == '__NULL__') {
                $('#area').select2('val','__NULL__');
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

        $('#putform').click(function(){
            $(this).hide();
            $('#load').show();
        	if($('#form-data-agency').validationEngine('validate')==true){
        		$.post('/system/organization/saveAgency',$('#form-data-agency').serialize(),function(data){
        			if(data.errors){
        				var tmp_errors = data.errors;
                        var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button>' + tmp_errors + '</div>';
                        $('#show_msg').html(warn_msg);
                        $('#load').hide();
                        $('#putform').show();
        			}else if(data){
                        var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>保存成功</div>';
                        $('#show_msg').html(succss_msg);
						window.location.reload();
        			}        				        			
        		},"json")
        	}else{
                $('#load').hide();
                $('#putform').show();
            }

        	return false;
        })

    });
</script>

<!-- 图片上传-->
<script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
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


