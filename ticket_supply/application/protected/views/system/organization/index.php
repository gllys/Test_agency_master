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
                    <ul class="list-inline">
                        <li><h4 class="panel-title">用户信息</h4></li>
                        <li><a href="/order/history/help?#9.1" title="帮助文档" class="clearPart"
                               target="_blank">查看帮助文档</a> </li>
                    </ul>
                </div>
                <!-- panel-heading -->
                <div id="show_msg"></div>

                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="form-data-supply" method="post" action="/system/organization/saveSupply">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 机构名称</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" class=" form-control" maxlength="20" readonly placeholder="请输入机构名称" tag="机构名称" name="name" value="<?php echo $organizations['name'] ?>" />
                                        <input type="hidden" name="id" value="<?php echo $organizations['id'] ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>联系人</label>
                                    <div class="col-sm-6">
                                        <input type="text" tag="联系人" class="validate[required] form-control" name="contact" placeholder="请输入联系人" value="<?php echo $organizations['contact'] ?>" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                    <div class="col-sm-6">
                                        <input type="text" maxlength="11" placeholder="" tag="手机号码"  class="form-control validate[required,custom[mobile]]" name="mobile" value="<?php echo $organizations['mobile'] ?>" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">公司传真</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="公司传真" class="form-control validate[custom[fax]]" name="fax" value="<?php echo $organizations['fax'] ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">固定电话</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="固定电话" class="form-control validate[custom[phone]]" name="telephone" value="<?php echo $organizations['telephone'] ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">审核状态</label>
                                    <div class="col-sm-6">
                                        <button class="btn btn-<?php echo $organizations['verify_status'] == 'checked' ? 'success' : 'danger'?> btn-bordered btn-sm " disabled>
                                            <?php echo $organizations['verify_status'] == 'checked' ? '已审核' : '未审核'?>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">启用状态</label>
                                    <div class="col-sm-6">
                                        <button class="btn btn-<?php echo $organizations['status'] == 1 ? 'success' : 'danger'?> btn-sm btn-bordered" disabled>
                                            <?php echo $organizations['status'] == 1 ? '已启用' : '未启用'?>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>所在地区</label>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="select2" data-validation-engine="form-control validate[required]" data-placeholder="Choose One" id="province" name="province_id" style="width: 120px;height:34px;">
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
                                                <select class="select2" data-placeholder="Choose One" id="city" name="city_id" style="width: 120px;height:34px;">
                                                    <option value="__NULL__">市</option>
                                                    <?php
                                                        if(isset($organizations['province_id'])){
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
                                                <select class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id" style="width: 120px;height:34px;">
                                                    <option value="__NULL__">县</option>
                                                    <?php
                                                        if(isset($organizations['city_id'])){
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
                                        <input type="text" placeholder="" tag="详细地址" class="form-control validate[required,minSize[4]]" name="address" value="<?php echo $organizations['address'] ?>" />
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
                                                <img id="business_license_img" src="<?php echo !empty($organizations['business_license']) ? $organizations['business_license'] : '/img/uploadfile.png';?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="business_license" value="<?php echo $organizations['business_license']?>"/>
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
    jQuery(document).ready(function() {
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });
        /*
        省市县显示问题重置 create by ccq
         */
        $('#province').select2("val","<?php echo isset($organizations['province_id']) && !empty($organizations['province_id']) ? $organizations['province_id'] : '__NULL__'?>");
        $('#city').select2("val","<?php echo isset($organizations['city_id']) && !empty($organizations['city_id']) ? $organizations['city_id'] : '__NULL__'?>");
        $('#area').select2("val","<?php echo isset($organizations['district_id']) && !empty($organizations['district_id']) ? $organizations['district_id'] : '__NULL__'?>");

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
            if (code == '__NULL__') {
                $('#area').select2('val','__NULL__');
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
        $('#putform').hide();
        $('#load').show();
        var license = $('input[name="business_license"]').val(); 
        if ($('#form-data-supply').validationEngine('validate') == true && license.length > 0) {
            $.post('/system/organization/saveSupply', $('#form-data-supply').serialize(), function(data) {
                if (data.errors) {
                    var tmp_errors = data.errors;
                    alert(tmp_errors);
                    $('#load').hide();
                    $('#putform').show();
                } else if (data.succ) {
                    var succss_msg = '<div class="alert alert-success"><strong>'+ data.succ +'</strong></div>';
                    $('#show_msg').html(succss_msg);
                    window.location.partReload();
                }
            }, "json");
        }else if(license.length <= 0){
            $('#business_license').validationEngine('showPrompt','请上传营业执照','error');
            $('#load').hide();
            $('#putform').show();
        }else{
            $('#load').hide();
            $('#putform').show();
        }

        return false;
    })


</script>
