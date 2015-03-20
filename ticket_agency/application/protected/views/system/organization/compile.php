<?php
//$this->breadcrumbs = array('系统管理', '注册分销商');
//?>
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
<!-- panel-heading -->
    <div id="show_msg"></div>
<div class="panel-body nopadding">
<form class="form-horizontal" id="form-data-agency">
<div class="row">
<div class="col-sm-6">
    <div class="form-group">
        <label class="col-sm-2 control-label"><span class="text-danger">*</span> 联系人</label>
        <div class="col-sm-6">
            <input type="text" placeholder="" tag="联系人"  class="form-control validate[required,custom[chinese],custom[NoSp],minSize[1],maxSize[20]]" name="contact" />
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label"><span class="text-danger">*</span>机构名称</label>
        <div class="col-sm-6">
            <input type="text" placeholder="" tag="机构名称" class="form-control validate[required,maxSize[20]]" name="name"/>
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
        <div class="col-sm-6">
            <input type="text" placeholder="" tag="手机号码" class="form-control  validate[required,custom[mobile]]" name="mobile"/>
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label">机构传真</label>
        <div class="col-sm-6">
            <input type="text" placeholder=""  tag="机构传真" class="form-control validate[custom[fax],maxSize[20]]" name="fax" />
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label">固定电话</label>
        <div class="col-sm-6">
            <input type="text" placeholder="" tag="固定电话" class="form-control validate[custom[phone],maxSize[20]]" name="telephone" />
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label">联系邮箱</label>
        <div class="col-sm-6">
            <input type="text" placeholder="" tag="联系邮箱" class="form-control validate[custom[email],maxSize[20]]" name="email"/>
        </div>
    </div>
    <!-- form-group -->


    <div class="form-group">
        <label class="col-sm-2 control-label"><span class="text-danger">*</span> 详细地址</label>
        <div class="col-sm-6">
            <input type="text" placeholder="" tag="详细地址" class="form-control validate[required,minSize[4],maxSize[20]]" name="address"/>
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label">所在地区</label>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-4">
                    <select class="select2 form-control validate[required]" tag="所在地区" data-placeholder="Choose One" id="province" name="province_id">
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
                    </select>
                </div>

                <div class="col-sm-4">
                    <select class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id">
                        <option value="__NULL__">县</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label">公司简介</label>
        <div class="col-sm-6">
            <textarea placeholder=""  name="description" class="form-control"></textarea>
        </div>
    </div>
    <!-- form-group -->

    <div class="form-group">
        <label class="col-sm-2 control-label"><span class="text-danger">*</span> 是否是旅行社</label>
        <div class="col-sm-6">
            <div class="rdio rdio-default">
                <input type="radio" value="1" id="radioDefault" name="agency_type"  class="validate[required]" checked="checked">
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
                    <img id="business_license_img"  src="/img/uploadfile.png" style="width:310px;height:180px;position:absolute;left:0px;top:0px">
                    <input type="hidden" class="sp_sxming" name="business_license" />
                </div>
            </div>
        </div>
    </div>

    <div class="form-group" id="dispalyn1">
        <label class="col-sm-2 control-label">税务登记证</label>
        <div class="col-sm-6">
            <div class="dropzone" id="tax_license">
                <div class="fallback nailthumb-container square-thumb">
                    <img id="tax_license_img"  src="/img/uploadfile.png" style="width:310px;height:180px;position:absolute;left:0px;top:0px">
                    <input type="hidden" class="sp_sxming" name="tax_license" />
                </div>
            </div>
        </div>
    </div>

    <div class="form-group" id="dispalyn2">
        <label class="col-sm-2 control-label">经营许可证</label>
        <div class="col-sm-6">
            <div class="dropzone" id="certificate_license">
                <div class="fallback nailthumb-container square-thumb">
                    <img id="certificate_license_img"  src="/img/uploadfile.png" style="width:310px;height:180px;position:absolute;left:0px;top:0px">
                    <input type="hidden" class="sp_sxming" name="certificate_license" />
                </div>
            </div>
        </div>
    </div><!-- form-group -->
</div>
</div>

<div class="panel-footer" style="padding-left:5%">
    <img src="/img/select2-spinner.gif" id="load" style="display: none" >
    <button type="button" class="btn btn-primary mr20" id="putform">确认添加</button>
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

        $("input[name=agency_type]").click(function() {
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
<script>
    jQuery(document).ready(function() {
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        jQuery('select option:first-child').text('');
        $('#province option:first-child').html('省').val("__NULL__");
        $('#city option:first-child').html('市').val("__NULL__");
        $('#area option:first-child').html('县').val("__NULL__");

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

    };
    $('#putform').click(function() {
        $(this).hide();
        $('#load').show();
        $('#form-data-agency').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });
        if ($('#form-data-agency').validationEngine('validate') == true) {
            $.post('/system/organization/saveAgency', $('#form-data-agency').serialize(), function(data) {
                if (data.errors) {
                    var tmp_errors = data.errors;
                    alert(tmp_errors);
                    $('#load').hide();
                    $('#putform').show();
                } else if (data) {
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>保存成功，请重新登录进行其他操作！</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='/site/logout'", '1000');
                }
            }, "json")
        }else{
            $('#load').hide();
            $('#putform').show();
        }

        return false;
    })

</script>

