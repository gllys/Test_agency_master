<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">编辑分销商</h4>
                </div><!-- panel-heading -->
                <div id="show_msg"></div>
                <div class="panel-body nopadding">

                    <form class="form-horizontal form-bordered" id="form-data-supply">
                        <div class="row">
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>联系人</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="联系人" class="form-control  validate[required]" value="<?php echo $organizations['contact'] ?>" name="contact" />
                                    </div>
                                </div>
                                <!-- form-group -->


                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 机构名称</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder=""  tag="机构名称" class="form-control validate[required,custom[chiMark],custom[NoSp],minSize[1],maxSize[20]]" value="<?php echo $organizations['name'] ?>" name="name"  />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">机构简称</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="最多八个字符,四个汉字" tag="机构简称"  class="form-control validate[custom[chinese],custom[NoSp],minSize[1],maxSize[8]]" name="abbreviation"  value="<?php echo $organizations['abbreviation'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                    <div class="col-sm-6">
                                        <input type="text" maxlength="11" tag="手机号码" placeholder="" class="form-control validate[required,custom[mobile]]" name="mobile" value="<?php echo $organizations['mobile'] ?>" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">公司传真</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="公司传真" class="form-control validate[custom[fax]]" name="fax"  value="<?php echo $organizations['fax'] ?>"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">固定电话</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="固定电话"  class="form-control validate[custom[phone]]" name="telephone"  value="<?php echo $organizations['telephone'] ?>" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">联系邮箱</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="联系邮箱"  class="form-control validate[custom[email]]" name="email"   value="<?php echo $organizations['email'] ?>" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>所在地区</label>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-4" id="province_tip">
                                                <select class="select2" data-placeholder="Choose One" id="province" name="province_id">
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
                                                    if (isset($organizations['province_id'])) {
                                                        $city_value = $organizations['province_id'];
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
                                                    if (isset($organizations['city_id'])) {
                                                        $area_value = $organizations['city_id'];
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
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>详细地址</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="详细地址" class="form-control validate[required,minSize[4]]" name="address"   value="<?php echo $organizations['address'] ?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger"></span>机构简介</label>
                                    <div class="col-sm-6">
                                        <textarea class="form-control" name="description" style="width:400px;height: 100px;"><?php echo $organizations['description'] ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>分销商状态</label>
                                    <div class="col-sm-6">
                                        <div class="rdio rdio-default">                    
                                            <input type="radio" checked="" id="radioDefault1" name="status" value="1" <?php if ($organizations['status'] == '1') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault1">启用</label>
                                        </div>
                                        <div class="rdio rdio-default">
                                            <input type="radio"  id="radioDefault2" name="status" value="0" <?php if ($organizations['status'] == '0') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault2">禁用</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>是否旅行社</label>
                                    <div class="col-sm-6">
                                        <div class="rdio rdio-default">                    
                                            <input type="radio" checked="" id="radioDefault3" name="agency_type" value="1" <?php if ($organizations['agency_type'] == '1') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault3">是&nbsp;&nbsp;&nbsp;</label>
                                        </div>
                                        <div class="rdio rdio-default">
                                            <input type="radio"  id="radioDefault4" name="agency_type" value="0" <?php if ($organizations['agency_type'] == '0') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault4">否</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>是否开通全平台散客票</label>
                                    <div class="col-sm-6">
                                        <div class="rdio rdio-default">                    
                                            <input type="radio" checked="" id="radioDefault5" name="is_distribute_person" value="1" <?php if ($organizations['is_distribute_person'] == '1') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault5">是&nbsp;&nbsp;&nbsp;</label>
                                        </div>
                                        <div class="rdio rdio-default">
                                            <input type="radio"  id="radioDefault6" name="is_distribute_person" value="0" <?php if ($organizations['is_distribute_person'] == '0') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault6">否</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>是否开通全平台团体票</label>
                                    <div class="col-sm-6">
                                        <div class="rdio rdio-default">                    
                                            <input type="radio" checked="" id="radioDefault7" name="is_distribute_group" value="1" <?php if ($organizations['is_distribute_group'] == '1') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault7">是&nbsp;&nbsp;&nbsp;</label>
                                        </div>
                                        <div class="rdio rdio-default">
                                            <input type="radio"  id="radioDefault8" name="is_distribute_group" value="0" <?php if ($organizations['is_distribute_group'] == '0') {
                                                        echo 'checked=checked';
                                                    } ?>>
                                            <label for="radioDefault8">否</label>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">营业执照</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="business_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="business_license_img" src="<?php echo!empty($organizations['business_license']) ? $organizations['business_license'] : '/img/uploadfile.png'; ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="business_license" value="<?php echo $organizations['business_license'] ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="dispalyn1">
                                    <label class="col-sm-2 control-label">税务登记证</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="tax_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="tax_license_img" src="<?php echo!empty($organizations['tax_license']) ? $organizations['tax_license'] : '/img/uploadfile.png'; ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" tag="税务登记证" name="tax_license" value="<?php echo $organizations['tax_license'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="dispalyn2">
                                    <label class="col-sm-2 control-label">经营许可证</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="certificate_license">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="certificate_license_img" src="<?php echo!empty($organizations['certificate_license']) ? $organizations['certificate_license'] : '/img/uploadfile.png'; ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" tag="经营许可证" name="certificate_license"  value="<?php echo $organizations['certificate_license'] ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->
                                    
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">企业logo：</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="logo">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="logo_img" src="<?php echo!empty($organizations['logo']) ? $organizations['logo'] : '/img/uploadfile.png'; ?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="logo" value="<?php echo $organizations['logo'] ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer" style="padding-left:5%">
                            <img src="/img/select2-spinner.gif" id="load" style="display: none" >
                            <button class="btn btn-primary mr20" id="putform">保存</button>
                        </div>

                    </form>          
                </div><!-- panel-body -->      



            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script>
    $(function() {
                <?php if ($organizations['agency_type'] === '0'):?>
                        $("#dispalyn1").hide();
                        $("#dispalyn2").hide();
                 <?php endif ;?>       
                $("input[name=agency_type]").click(function () {
                    var vals = $("input[name=agency_type]:checked").val();
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


<script>
    jQuery(document).ready(function() {
        /*
         省市县显示问题重置 create by ccq
         */
        $('#province,#city,#area').select2();
        $('#province').select2("val", "<?php echo isset($organizations['province_id']) && !empty($organizations['province_id']) ? $organizations['province_id'] : '__NULL__' ?>");
        $('#city').select2("val", "<?php echo isset($organizations['city_id']) && !empty($organizations['city_id']) ? $organizations['city_id'] : '__NULL__' ?>");
        $('#area').select2("val", "<?php echo isset($organizations['district_id']) && !empty($organizations['district_id']) ? $organizations['district_id'] : '__NULL__' ?>");


        //省联动
        $('#province').change(function() {

            var code = $(this).val();

            $('#city').html('<option value="__NULL__">市</option>');
            $('#area').html('<option value="__NULL__">县</option>');
            if (code == '__NULL__') {
                /*
                 需要重置页面上的显示
                 */
                $('#city').select2('val', '__NULL__');
                $('#area').select2('val', '__NULL__');
                $('#city').html('<option value="__NULL__">市</option>');
                $('#area').html('<option value="__NULL__">县</option>');
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
                $('#area').select2('val', '__NULL__');
                $('#area').html('<option value="__NULL__">县</option>');
            } else {
                $('#area').html('<option value="__NULL__">县</option>');
                var html = new Array();
                $.post('/ajaxServer/GetChildern', {id: code}, function(data) {
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
    
     new AjaxUpload('#logo', {
        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
        name: 'file',
        onSubmit: function(file, ext) {
            //上传文件格式限制
            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                alert('上传格式不正确');
                return false;
            }
            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
            window.imgField = 'logo';
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


//表单提交
$(function(){	
    $('#form-data-supply').validationEngine({
            autoHidePrompt: true,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true,
            success: false
     });
     
    $('#putform').click(function() {
        //账户名验证
        var username = $('input[name=account]').val();
        var reg = /^[a-zA-Z0-9]*$/g;
        if(!reg.test(username)) {
        $('input[name=account]').PWShowPrompt('用户名只能输入数字和英文');
        return false;
        }
        
        //表单整体验证
        if ($('#form-data-supply').validationEngine('validate') !== true) {
            return false;
        }
        
        //下拉框验证
        if($('#province').val()==='__NULL__'){
            $('#province_tip').PWShowPrompt('省市区至少选择一个');
            return false;
        }
        
        //图片验证
//        var license = $('input[name="business_license"]').val();
//        if(license.length <= 0){
//           $('#business_license').PWShowPrompt('请上传营业执照');
//           return false;
//        }       
        
        
        //表单提交
        $(this).hide();
        $('#load').show();
        $.post('/org/agency/edit/id/<?php echo $_GET['id']?>', $('#form-data-supply').serialize(), function(data) {
                if (data.errors) {
                    var tmp_errors = data.errors;
                    alert(tmp_errors);
                    $('#load').hide();
                    $('#putform').show();
                } else {
                    alert('保存成功', function() {
                        location.href="/site/switch/#/org/agency/" ;
                    });
                }
            }, "json");
        return false;
    })
});
</script>