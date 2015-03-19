<!DOCTYPE html>
<html>
<?php get_header(); ?>
<body>

<?php get_top_nav(); ?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu(); ?>

<div class="main-content">
    <?php get_crumbs(); ?>
    <div id="show_msg">

    </div>

    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title"><i class="icon-edit"></i> 设置模板</span>
            </div>
            <div class="box-content">
                <form action="index.php?c=tickets&a=save" method="post" id="scenic_add_form" class="fill-up">
                    <div class="row-fluid">
                        <div class="span10">
                            <ul class="padded separate-sections">
                                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                                <li class="input">
                                    <label>模板名称:<span class="note"></span>
                                        <input style="width: 200px" type="text" name="name" value="<?php echo isset($name) ? $name : '' ?>"/>
                                    </label>
                                </li>
                                <li class="input">
                                    <label>使用景区:<span class="note"></span>
                                        <select name="scenic_id">
                                            <option value="#">请选择景区</option>
                                            <?php foreach ($landscape_list as $v) :?>
                                            <option value="<?php echo $v['id'];?>"><?php echo $v['name'];?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </label>
                                </li>
                                <li>
                                    <label>使用门票:<span class="note"></span>
                                    </label>
                                    <div name="ticket_template_base" style="margin-left: 20px;">
                                        暂无门票选项
                                    </div>
                                </li>
                                <li class="input">
                                    <label>模板内容:<span class="note">*</span>
                                        <textarea name="content" id="" cols="120" rows="15"><?php
                                            echo isset($content) ? htmlspecialchars_decode($content) : file_get_contents(__DIR__.'/default.php')?>
                                        </textarea>
                                    </label>
                                </li>
                                <li>
                                    <label>材质：
                                        <input style="width: 140px" type="text" name="spec" value="<?php echo isset($spec) ? $spec : '' ?>"/>
                                    </label>

                                </li>
                                <li>
                                    <label>尺寸：
                                        <input style="width: 140px" type="text" name="size" value="<?php $size= (isset($height) ? $height : "")."*".(isset($width) ? $width : "");echo isset($size) ? $size : '' ?>"/>
                                        请输入您的门票的长度与宽度（单位：mm），例如：100*200
                                    </label>

                                </li>


                                <li>
                                    <label>模板样式:<span class="note">*</span>
                                        <div id="preview" style="max-width:180px;height:150px;"> 
                                            <img id="preview_img"  src="<?php if(!empty($image)){echo $image;} else {?>/Views/images/uploadfile.png<?php }?>" class="hover" style="max-width:180px;height:150px;">
                                            <input type="hidden" class="sp_sxming" name="preview" />
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>


                    </div>




                    <div class="form-actions">
                        <button class="btn btn-lg btn-blue" id="btn-add">保存</button>
                    </div>

                </form>






            </div>
        </div>
    </div>
</div>
<?php
 $upyun = new UYouPai();
?>
<script type="text/javascript"  charset="utf8" src="/Views/js/ajaxUpload.js"></script>
<script>
    $(document).ready(function () {        
        //上传
        window.imgField = '';
        new AjaxUpload('#preview', {
            action: 'http://v0.api.upyun.com/<?php echo $upyun->bucket ?>/',
            name: 'file',
            width: "150px",
            height: "180px",
            onSubmit: function(file, ext) {
                //上传文件格式限制
                if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                    alert('上传格式不正确');
                    return false;
                }
                this.setData(<?php echo $upyun->getCode() ?>);
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

        $('[name=scenic_id]').select2();
        
        $('[name=scenic_id]').change(function(){
            $.get("tickets_ticketsprint.html?scenic_id="+$(this).val()+"&pid="+$("input[name=id]").val(), null, function(result){
                console.log(result);
                if(result.code == 0) {
                    var selectOptions = "";
                    var ticketTemplatesData = result.data.tickettemplates;
                    var ticketPrintData = result.data.ticketprintrelate;
                    for(var k in ticketTemplatesData) {
                        if(ticketPrintData[k] == undefined) {                     
                            selectOptions += 
                                "<label style='margin-right: 8px;' class='checkbox inline'><input type='checkbox' name='ticket_template_base_id[]' style='width: 30px' value='"+k+"'/>"+ticketTemplatesData[k]+"</label>";
                        } else {
                            selectOptions += 
                                "<label style='margin-right: 8px;' class='checkbox inline'><input type='checkbox' name='ticket_template_base_id[]' style='width: 30px' checked='checked' value='"+k+"'/>"+ticketTemplatesData[k]+"</label>";                            
                        }
                    }
                    if(selectOptions.length == 0) {
                        $("[name=ticket_template_base]").html("暂无门票选项");
                    } else {
                        $("[name=ticket_template_base]").html(selectOptions);
                    }
                } else {
                    $("[name=ticket_template_base]").html("暂无门票选项");
                }
            }, 'json');
        });
        
        $('#btn-add').validationEngine({
		promptPosition : 'topLeft',
		addFailureCssClassToField: 'error',
		autoHidePrompt: true,
		autoHideDelay:3000
	});
        $('#btn-add').click(function(){
            var obj = $('#scenic_add_form');
            if(obj.validationEngine('validate')== true){
                $.post('index.php?c=tickets&a=save', obj.serialize(),function(data){
                    if(data.code != "succ"){
                            var tmp_errors = '';
                            $.each(data.message ,function(i, n){
                                    tmp_errors += n;
                            });
                            var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                            $('#show_msg').html(warn_msg);
                            location.href='#show_msg';
                    }else if(data.code == "succ"){
                            var type_msg,redirect;
                            type_msg = data.message;

                            redirect = 'tickets_templates.html';
                            var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong></div>';
                            $('#show_msg').html(succss_msg);
                            location.href='#show_msg';

                            setTimeout("location.href='"+redirect+"'", 2000);
                    }
                }, "json");
                return false;
            }
        });
        
    });


</script>

