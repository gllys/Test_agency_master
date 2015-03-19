<!DOCTYPE html>
<html>
<?php get_header();?>
<body>
<?php get_top_nav();?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu();?>
<div class="main-content">
<?php get_crumbs();?>	

<div id="show_msg"></div>

<div class="box">
<div class="box-header">
    <span class="title"><i class="icon-edit"></i> 建议回复</span>
</div>
<?php if($data):?>
<div class="box-content">
<form action="index.php?c=message&a=addReport" method="post" id="scenic_add_form" class="fill-up">
<input type="hidden" name="id" value="<?php echo $data['suggest']['id'];?>">
<div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
            <li class="agency-type">
                <label>旅行社名称：</br>
                    <span><?php echo $data['organzation']['name'];?></span>
                </label>
            </li>
            <li class="agency-type">
                <label>建议内容：</br>
                    <span><?php echo $data['suggest']['content'];?></span>
                </label>
            </li>
    		<li class="input">
                <label>回复内容：
                    <textarea rows="4" name="content" placeholder="" class="validate[maxSize[200]]"><?php if($data['report']):echo $data['report']['content']?><?php endif;?></textarea>
                </label>
            </li>
    	</ul>
    	<div class="form-actions">
    		<button class="btn btn-lg btn-blue" id="btn-add" <?php if($data['report']):echo "disabled";?><?php endif;?>>回复</button>
		</div>
    </div>
</form>
</div>
<?php endif;?>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/message/add.js"></script>
</script>
</body>
</html>	