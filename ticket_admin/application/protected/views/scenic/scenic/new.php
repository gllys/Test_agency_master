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
                    <h4 class="panel-title">新建景区</h4>
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
                                        <input  type="text" tag="景区名称" class="form-control validate[required]" name="name" value=""/>
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
                                                <select class="select2 validate[required]" data-validation-engine="form-control
                                                validate[required]" data-placeholder="Choose One" id="province" name="province_id">
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
                                                <select class="select2 validate[required]" data-placeholder="Choose One" id="city"
                                                        name="city_id">
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
                                                <select class="select2 col-sm-4 validate[required]" data-placeholder="Choose One"
                                                        id="area" name="district_id">
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
                                        <input type="text" placeholder="" tag="详细地址" class="form-control validate[required,minSize[4],maxSize[20]]" name="address" value=""/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>联系电话</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <input type="text" id="phoneditribute" name="phone[]" tag="请正确输入该景区联系电话区号"
                                                       class="validate[required,req,custom[integer],minSize[3],
                                                       maxSize[5]] form-control"
                                                       placeholder="区号">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" id="phonenum" name="phone[]" tag="请正确输入该景区联系电话"
                                                       class="validate[required,req,custom[integer],minSize[7],
                                                       maxSize[8]] form-control"
                                                       placeholder="电话">
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="text" id="phoneextension" name="phone[]" tag="请正确输入该景区联系电话分机号"
                                                       class="validate[req,maxSize[3],custom[integer]] form-control"
                                                       placeholder="分机">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>开放时间</label>
                                    <div class="col-sm-7">
                                        <textarea rows="5" tag="请正确输入该景区开放时间" name="hours" class="form-control
                                        validate[required,req]"></textarea>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>景区经纬度</label>
                                    <div class="col-sm-3">
                                        <input type="text" placeholder="景区经度" tag="请正确输入该景区经度坐标" class="form-control
                                        validate[required,req,custom[number]]" name="lat"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" placeholder="景区纬度" tag="请正确输入该景区纬度坐标" class="form-control
                                        validate[required,req,custom[number]]" name="lng"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">全景ID</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="请正确输入该景区的ID" class="form-control
                                        validate[req,custom[number],minSize[1],maxSize[9]]" name="panorama_id" value=""/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">注意事项</label>
                                    <div class="col-sm-7">
                                        <textarea rows="5" name="note" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">交通情况</label>
                                    <div class="col-sm-7">
                                        <textarea rows="5" name="transit" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">景区介绍</label>
                                    <div class="col-sm-6">
                                        <textarea id="editor_id" name="biography" style="width:200px;height:100px;"></textarea>
                                        <script>
                                            $(function(){
                                                window.editor = KindEditor.create('#editor_id', {
                                                    resizeType: 1,
                                                    allowPreviewEmoticons: false,
                                                    afterBlur: function () { this.sync(); },
                                                    allowImageUpload: true,
                                                    items: [
                                                        'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                                                        'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                                                        'insertunorderedlist', '|', 'link','image']
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>

                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">景区图片</label>
                                    <div class="col-sm-6">
                                        <div class="dropzone" id="image_upload">
                                            <div class="fallback nailthumb-container square-thumb">
                                                <img id="image"  src="/img/uploadfile.png" style="width:310px;height:180px;position:absolute;left:0px;top:0px">
                                                <input type="hidden" class="sp_sxming" name="images[id]" value="" />
                                                <input type="hidden" id="image_upload_img" name="images[url]" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" id="getsc"><span class="text-danger">*</span>景区主题：</label>
                                    <div class="col-sm-6">
                                        <ul class="list-inline">
                                            <?php  if(isset($feature)&&!empty($feature)):
                                                foreach($feature as $_k =>$_v):
                                            ?>
                                            <li><input type="checkbox" value="<?php echo $_k;?>" name="feature[]"
                                                       id="checkboxPrimary<?php echo $_k;?>"><label
                                                    for="checkboxPrimary<?php echo $_k;?>"><?php echo $_v;?></label></li>
                                            <?php   endforeach; endif;?>
                                        </ul>
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
        if($('#province').val() == '__NULL__'){
            $('#save_scenic').show();
            $('#load').hide();
            $('#province').PWShowPrompt('请选择省份');
            return false;
        }
        if($('#city').val() == '__NULL__'){
            $('#save_scenic').show();
            $('#load').hide();
            $('#city').PWShowPrompt('请选择城市');
            return false;
        }
        if($('#area').val() == '__NULL__'){
            $('#save_scenic').show();
            $('#load').hide();
            $('#area').PWShowPrompt('请选择县区');
            return false;
        }

        if(!$('input[name="feature[]"]').is(':checked')){
            $('#save_scenic').show();
            $('#load').hide();
            $('#getsc').PWShowPrompt('请选择至少一个主题');
            return false;
        }

        if ($('#scenic_form').validationEngine('validate') == true) {
            $.post('/scenic/scenic/newScenic', $('#scenic_form').serialize(), function(data) {
                //console.log(data);
                if (data.error == 0) {
                    alert(data.msg, function() {
                        $('#save_scenic').removeAttr('disabled');
                        $('#load').hide();
                        $('#save_scenic').show();
                    });
                } else {
                    window.setTimeout(function(){
                      alert(data.msg, function() {
                        location.href='/site/switch/#/scenic/scenic/';
                      });
                    },400);
                }
            }, "json")
        }else{
            $('#load').hide();
            $('#save_scenic').show();
        }

        return false;
    })

</script>