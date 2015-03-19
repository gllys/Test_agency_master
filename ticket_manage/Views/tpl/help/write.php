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
	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header"><span class="title"><i class="icon-zoom-in"></i> 添加文档</span></div>
			<div class="box-content">
				<form action="index.php?c=help&a=saveFile" method="post" id="help_add_form" class="fill-up">
					<div class="row-fluid">
						<div class="span6">
							<ul class="padded separate-sections">
							<?php if($files): ?>
								<li class="input"><br>
									<label>资料名称<span class="note"><strong class="status-error">*</strong></span>
										<input type="text" value="<?php echo $files['name'] ?>" data-prompt-position="topLeft" class="form-control validate[required,minSize[5],maxSize[100]]" name="file_name" placeholder="">
									</label>
								</li>
					        	<li class="input"><br>
									<label>资料说明<span class="note"><strong class="status-error">*</strong></span>
										<input type="text" value="<?php echo $files['desc'] ?>" data-prompt-position="topLeft" class="form-control validate[required,minSize[5],maxSize[100]]" name="desc" placeholder="">
										<input type="hidden" name="id" value="<?php echo $files['id']?>" >
									</label>
								</li>
								<li class="input">
					                <label>帮助资料<strong class="status-error"></strong></label><span class="note"></span>
					                    <input type="hidden" name="file_id" value="<?php echo $files['file_id']?>">
					                    <a href="#upload" data-toggle="modal" class="btn btn-blue">请选择上传资料..</a>       
					        	</li>
					        <?php else:?>
					        	<li class="input"><br>
									<label>资料名称<span class="note"><strong class="status-error">*</strong></span>
										<input type="text" value="" data-prompt-position="topLeft" class="form-control validate[required,minSize[5],maxSize[100]]" name="file_name" placeholder="请输入您的资料名称">
									</label>
								</li>
					        	<li class="input"><br>
									<label>资料说明<span class="note"><strong class="status-error">*</strong></span>
										<input type="text" value="" data-prompt-position="topLeft" class="form-control validate[required,minSize[5],maxSize[100]]" name="desc" placeholder="请输入您的资料说明">
									</label>
								</li>
								<li class="input">
					                <label>帮助资料<strong class="status-error"></strong></label><span class="note"></span>
					                    <input type="hidden" name="file_id" value="">
					                    <a href="#upload" data-toggle="modal" class="btn btn-blue">请选择上传资料..</a>       
					        	</li>
					        <?php endif;?>
					        	
							</ul>
						</div>

						<div class="span6"></div>
					</div>
					<div class="form-actions">
						<button class="btn btn-lg btn-blue" type="button" id="btn-add">保存</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="upload" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">帮助资料上传</h6>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <form action="index.php?c=ajax&a=fileUpload" method="post" id="help_upload_form">
				<span class="span9">
					<input type="file" name="attachments">
				</span>	
				<span class="span3">
					<button class="btn btn-blue" data-dismiss="modal" id="btn-upload"><i class="icon-cloud-upload"></i> 上传文档</button>
				</span>
            </form>
        </div>
    </div>
</div>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/help/write.js"></script>
