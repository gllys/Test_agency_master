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
			<div class="box-header"><span class="title"><i class="icon-zoom-in"></i> 新增文档</span></div>
			<div class="box-content">
				<form action="index.php?c=help&a=save" method="post" id="help_update_form" class="fill-up">
					<input type="hidden" name="id" value="<?php echo $help['id']?>">
					<div class="row-fluid">
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="agency-type">
									<span class="span3">
				                        <?php if ($allTypes) : ?>
					                        <select class="uniform validate[min[1]" name="type_id">
						                        <option value="0">类别</option>
						                        <?php foreach ($allTypes as $type) : ?>
							                        <option value="<?php echo $type['id']?>" <?php if($help['type_id'] == $type['id']){ echo "selected='selected'";}?>><?php echo $type['name']?></option>
						                        <?php endforeach; ?>
					                        </select>
				                        <?php endif; ?>
									</span>
								</li>
								<li class="input"><br>
									<label>文档标题<strong class="status-error">*</strong><span class="note"></span>
										<input type="text" value="" data-prompt-position="topLeft" class="validate[minSize[2],maxSize[32]]" name="name" placeholder="请输入文档标题">
									</label>
								</li>
								<li class="input">
									<label>文档内容<strong class="status-error">*</strong><span class="note"></span>
										<textarea rows="6" name="info" placeholder="" data-prompt-position="topLeft" class="validate[required,maxSize[10000]]"></textarea>
									</label>
								</li>
							</ul>
						</div>
					</div>
					<div class="form-actions">
						<button class="btn btn-lg btn-blue" type="button" id="btn-edit">保存</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/help/write.js"></script>
