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
			<div class="box-header"><span class="title"><i class="icon-edit"></i> 添加监管机构</span></div>
			<div class="box-content">
				<form action="index.php?c=monitor&a=save" method="post" id="monitor_update_form" class="fill-up">
					<div class="row-fluid">
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="input">
									<label>机构名称<strong class="status-error">*</strong><span class="note"></span></label>
										<input type="text"
										       data-prompt-position="topLeft" class="validate[minSize[2],maxSize[32]]"
										       name="name" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>选择上级机构<strong class="status-error">*</strong><span class="note"></span></label>
									<div class="row-fluid">
										<div class="span4">
											<?php if ($relation) : ?>
												<select class="uniform" name="relation[p_id]">
													<option value="0">无</option>
													<?php list($select, $_, $children) = output_monitor_select($relation, '', $get['id']); echo $select ?>
												</select>
											<?php endif; ?>
										</div>
									</div>
								</li>
								<li class="input">
									<label>简介<strong class="status-error">*</strong><span class="note"></span></label>
										<textarea rows="6" name="brief" placeholder="" style="width:400px" data-prompt-position="topLeft"
										          class="validate[required,maxSize[10000]]"><?php echo $monitor['info'] ?></textarea>
								</li>
							</ul>
						</div>
						<div class="span6">
							<ul class="padded">
								<li class="input">
									<label>联系人<span class="note"></span></label>
									<input type="text"
									       data-prompt-position="topLeft"
									       name="contact" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>移动电话<span class="note"></span></label>
									<input type="text"
									       data-prompt-position="topLeft"
									       name="phone" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>固定电话<span class="note"></span></label>
									<input type="text"
									       data-prompt-position="topLeft"
									       name="telephone" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>传真<span class="note"></span></label>
									<input type="text"
									       data-prompt-position="topLeft"
									       name="fax" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>电子邮箱<span class="note"></span></label>
									<input type="text"
									       data-prompt-position="topLeft"
									       name="email" placeholder="" style="width:300px">
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
<script src="Views/js/monitor/write.js"></script>
