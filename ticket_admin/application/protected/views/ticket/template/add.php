
    

                <!-- pageheader -->
                <div class="contentpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                          
                                    <!-- panel-btns -->
                                    <h4 class="panel-title">模版信息</h4>
                                </div>
                                <!-- panel-heading -->
                                <div id="show_msg">
                                   
                                </div>
                                <div class="panel-body nopadding">
                                    <form action="/ticket/template/save" method="post" id="scenic_add_form"  id="form-data-supply">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">*</span> 模板名称</label>
													<div class="col-sm-6">
														<input type="text" placeholder="" data-prompt-position="centerLeft" class="form-control validate[required,custom[chinese],custom[NoSp],minSize[1],maxSize[20]]" name="name" value="" />
													</div>
												</div>
												<!-- form-group -->

												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">&nbsp;&nbsp;</span>使用景区</label>
													<div class="col-sm-6">
				                                        <select class="select2 form-control validate[required]" name="scenic_id">
				                                            <option value="#">请选择景区</option>
				                                            <?php foreach ($landscape_list as $v) :?>
				                                            <option value="<?php echo $v['id'];?>" ><?php echo $v['name'];?></option>
				                                            <?php endforeach;?>
				                                        </select>
													</div>
												</div>
												<!-- form-group -->
												
												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">&nbsp;&nbsp;</span>使用门票</label>
													<div class="col-sm-6">
														<input type="text" readonly placeholder="" class="form-control validate[custom[phone],maxSize[20]]" value="暂无门票选项" />
													</div>
												</div>
												<!-- form-group -->
												
												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">&nbsp;&nbsp;</span>模板内容</label>
													<div class="col-sm-6">
														<textarea name="content" id="" class="form-control validate[custom[phone],maxSize[20]]" cols="120" rows="15"><?php echo isset($content) ? htmlspecialchars_decode($content) : file_get_contents(__DIR__.'/default.php')?></textarea>
													</div>
												</div>
												<!-- form-group -->
												
												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">&nbsp;&nbsp;</span>材质</label>
													<div class="col-sm-6">
														<input type="text" placeholder="" class="form-control validate[custom[email],maxSize[20]]" name="spec" value="<?php echo isset($spec) ? $spec : '' ?>" />
													</div>
												</div>
												<!-- form-group -->
												
												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">&nbsp;&nbsp;</span>尺寸</label>
													<div class="col-sm-6">
														<input type="text" placeholder="" class="form-control validate[required,maxSize[20]]" name="size" value="<?php $size= (isset($height) ? $height : "")."*".(isset($width) ? $width : "");echo isset($size) ? $size : '' ?>" /> <span style="color:red;">请输入您的门票的长度与宽度（单位：mm），例如：100*200</span>
													</div>
												</div>
												<!-- form-group -->
												
												
											</div>

											
											<div class="col-sm-6">
												<div class="form-group">
													<label class="col-sm-2 control-label"><span class="text-danger">*</span>模版样式</label>
													<div class="col-sm-6">
														<div class="dropzone" id="preview_license">
															<div  id="preview"  class="fallback nailthumb-container square-thumb">
																<img id="preview_img"  src="<?php if(!empty($image)){echo $image;} else {?>/img/uploadfile.png<?php }?>" style="width:150px;height:150px;position:absolute;left:0px;top:0px"> 
																 <input type="hidden" class="sp_sxming" name="preview" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
	
                                        <div class="panel-footer" style="padding-left:5%">
                                            <button class="btn btn-primary mr20" id="btn-add">确认添加</button>
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
                <!-- contentpanel -->
<script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
<script>
    $(document).ready(function () {    
        $('.select2').select2();
        
        //上传
        window.imgField = '';
        new AjaxUpload('#preview_license', {
            action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
            name: 'file',
            width: "150px",
            height: "180px",
            onSubmit: function(file, ext) {
                //上传文件格式限制
                if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                    alert('上传格式不正确');
                    return false;
                }
                this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                window.imgField = 'preview';
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

        
        
        $('#btn-add').validationEngine({
    		promptPosition : 'topLeft',
    		addFailureCssClassToField: 'error',
    		autoHidePrompt: true,
    		autoHideDelay:3000
    	});
        $('#btn-add').click(function(){
            var obj = $('#scenic_add_form');
            //if(obj.validationEngine('validate')== true){
                $.post('/ticket/template/save', obj.serialize(),function(data){
                       // console.log(data);return false;
                    if(data.error == 1){
                        alert(data.msg);
                    }else if(data.error == 0){
                        var type_msg;
                        alert(data.msg, function() {
                            location.href = '/site/switch/#/ticket/template/'
                        });
                    }
                }, "json");
                return false;
            //}
        });
        
    });


</script>



