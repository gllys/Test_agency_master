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
                <span class="title"><i class="icon-edit"></i>添加首页推荐</span>
            </div>
            <div class="box-content">
                <form action="index.php?c=system&a=saveRec" method="post" id="scenic_add_form" class="fill-up">
                    <div class="row-fluid">
                        <div class="span10">
                            <ul class="padded separate-sections">
                                <input type="hidden" name="id" value="<?php echo isset($rec) ? $rec[id] : '' ?>">
                                <li class="input">
                                    <label>主题:<span class="note"></span>
                                        <input style="width: 200px" type="text" name="title" value="<?php echo isset($rec) ? $rec[title] : '' ?>"/>
                                    </label>
                                </li>
                                <li class="input">
                                    <label>位置:<span class="note"></span>
                                        <div class="row-fluid" style="margin-left: 20px;">
                                        	
                                        	<?php foreach ($pl as $k=>$p):?>
					                        <div class="span4">
					                            <input type="checkbox" class="icheck" name="pos_id[]" 
					                            value="<?php echo $k?>" <?php if ($rec && in_array($k, $rec['pos'])):?>checked="checked"<?php endif;?>>
					                            <label><?php echo $p?></label>
					                        </div>
					                        <?php endforeach;?>
					                        <div class="span4"></div>
					                </div>
                                    </label>
                                </li>
                                <li class="input">
                                    <label>简介:<span class="note"></span>
                                        <div class="row-fluid" style="margin-left: 20px;">
                                        	<textarea name="detail" rows="5" cols="50"><?php echo $rec?$rec['detail']:''?></textarea>
                                        	
					                        <div class="span4"></div>
					                </div>
                                    </label>
                                </li>
                                <li>
                                    <label>活动时段:<span class="note"></span>
                                    </label>
                                    <div name="ticket_template_base" style="margin-left: 20px;">
                                        <input type="text" placeholder="" name="sedate" class="form-time" value="<?php
                                           if ($rec) {
                                               echo $rec['sed'];
                                           }
                                    ?>">
                                    </div>
                                </li>
                                <li class="input">
                                    <label>链接地址:<span class="note">*</span>
                                        <input name="url" value="<?php echo $rec?$rec['url']:''?>" type="text" style="margin-left: 20px;"/>
                                    </label>
                                </li>
                                

                                <li>
                                    <label>图片上传:<span class="note">*</span>
                                        <div id="bimg" style="max-width:180px;height:150px;"> 
                                            <img id="bimg_img"  src="<?php if(!empty($rec)){echo $rec['bimg'];} else {?>/Views/images/uploadfile.png<?php }?>" class="hover" style="max-width:180px;height:150px;">
                                            <input type="hidden" class="sp_sxming" name="bimg" />
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
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>

<script type="text/javascript"  charset="utf8" src="/Views/js/ajaxUpload.js"></script>
<script>
    $(document).ready(function () {     

    	$('input.form-time').daterangepicker({format:'YYYY-MM-DD'});
       
           
        //上传
        window.imgField = '';
        new AjaxUpload('#bimg', {
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
                window.imgField = 'bimg';
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
            //alert(obj.serialize());
            if(obj.validationEngine('validate')== true){
                $.post('index.php?c=system&a=saveRec', obj.serialize(),function(data){
                    if(data.code != "succ")
                    {
                            var tmp_errors = '';
                            $.each(data.message ,function(i, n)
                            {
                                    tmp_errors += n;
                            });
                            var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                            $('#show_msg').html(warn_msg);
                            location.href='#show_msg';
                    }
                    else if(data.code == "succ")
                    {
                            var type_msg,redirect;
                            type_msg = data.message;

                            redirect = 'system_home.html';
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

