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
                    <h4 class="panel-title">修改景区</h4>
                </div>
                <!-- panel-heading -->
                <div id="show_msg"></div>
                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="scenic_form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 景区名称</label>
                                    <div class="col-sm-6">
                                        <input style="border: 0;background-color: #ffffff"  type="text" class="form-control" name="name" value="<?php echo isset($info['name']) && !empty($info['name']) ? $info['name'] : ''?>" readonly/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>景区级别</label>
                                    <div class="col-sm-6">
                                        <select class="select2 form-control" id="landscape_level_id" name="landscape_level_id">
                                            <option value="0">非A景区</option>
                                            <option value="1">A景区</option>
                                            <option value="2">AA景区</option>
                                            <option value="3">AAA景区</option>
                                            <option value="4">AAAA景区</option>
                                            <option value="5">AAAAA景区</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>所在地区</label>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select class="select2" data-validation-engine="form-control validate[required]" data-placeholder="Choose One" id="province" name="province_id">
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
                                                    if(isset($info['province_id']) && !empty($info['province_id'])){
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
                                                    if(isset($info['city_id']) && !empty($info['city_id'])){
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
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 详细地址</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="详细地址" class="form-control validate[required,minSize[4],maxSize[20]]" name="address" value="<?php echo isset($info['address']) && !empty($info['address']) ? $info['address'] : ''?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">景区介绍</label>
                                    <div class="col-sm-6">
                                        <textarea rows="5" name="description" class="form-control"><?php echo isset($info['biography']) && !empty($info['biography']) ? $info['biography'] : '' ?></textarea>
                                    </div>
                                </div>
                                <!-- form-group -->

                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">景区图片</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="image_upload">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="image"  src="<?php echo isset($info['images']) && !empty($info['images']) ? $info['images'][0]['url'] : '/img/uploadfile.png'?>"
                                                     style="width:310px;height:180px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="images[id]" value="<?php echo isset($info['images']) && !empty($info['images']) ? $info['images'][0]['id'] : ''?>" />
                                                <input type="hidden" id="image_upload_img" name="images[url]" value="<?php echo isset($info['images']) && !empty($info['images']) ? $info['images'][0]['url'] : ''?>">
                                                <input type="hidden" name="id" value="<?php echo $info['id']?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <img src="/img/select2-spinner.gif" id="load" style="display: none" >
                            <button type="button" class="btn btn-primary mr20" id="save_scenic">保存</button>
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

        $('#province').select2("val","<?php echo isset($info['province_id']) && !empty($info['province_id']) ? $info['province_id'] : '__NULL__'?>");
        $('#city').select2("val","<?php echo isset($info['city_id']) && !empty($info['city_id']) ? $info['city_id'] : '__NULL__'?>");
        $('#area').select2("val","<?php echo isset($info['district_id']) && !empty($info['district_id']) ? $info['district_id'] : '__NULL__'?>");

        $('#landscape_level_id').select2('val',<?php echo isset($info['landscape_level_id']) ? $info['landscape_level_id'] : 0?>);

        //jQuery('select option:first-child').text('');
        $('#province option:first-child').html('省').val("__NULL__");
        $('#city option:first-child').html('市').val("__NULL__");
        $('#area option:first-child').html('县').val("__NULL__");
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
    new AjaxUpload('#image_upload', {
        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
        name: 'file',
        onSubmit: function(file, ext) {
            //上传文件格式限制
            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                alert('上传格式不正确');
                return false;
            }
            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
            window.imgField = 'image_upload';
        },
        onComplete: function(file, data) {
        }
    });

    window.upload_callback = function(data) {
        if (data.status != 200) {
            alert('上传失败！');
            return false;
        }
        $('#' + window.imgField + '_img').val(data.msg);
        $('#image').attr('src', data.msg);

    };
    $('#save_scenic').click(function() {
        $(this).hide();
        $('#load').show();
        $('#scenic_form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });
        if ($('#scenic_form').validationEngine('validate') == true) {
            $.post('/scenic/scenic/saveScenic', $('#scenic_form').serialize(), function(data) {
                if (data.error) {
                    alert(data.msg, function() {
                        $('#save_scenic').removeAttr('disabled');
                        $('#load').hide();
                        $('#save_scenic').show();
                    });
                } else {
                    window.setTimeout(function(){
                      alert('更新成功！', function() {
                        location.href='/site/switch/#/scenic/scenic/';
                      });
                    },200);
                }
            }, "json")
        }else{
            $('#load').hide();
            $('#save_scenic').show();
        }

        return false;
    })

</script>

