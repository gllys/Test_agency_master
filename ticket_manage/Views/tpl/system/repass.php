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
      <div class="box-header">
        <span class="title"><i class="icon-key"></i> 修改密码</span>
      </div>
      <div class="box-content">
		<form id="repass-form" action="index.php?c=system&a=repass" method="post">
			<div class="padded">
				<div class="form-group">
					<label class="control-label col-lg-2">输入原密码 <strong class="status-error">*</strong><span class="note"></span></label>
					<div class="col-lg-10">
					<input type="password" class="validate[required,minSize[6],maxSize[16]]" name="oldpass">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-2">输入新密码 <strong class="status-error">*</strong><span class="note"></span></label>
					<div class="col-lg-10">
					<input type="password" class="validate[required,minSize[6],maxSize[16]]" name="password" id="password">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-2">确认新密码 <strong class="status-error">*</strong><span class="note"></span></label>
					<div class="col-lg-10">
					<input type="password" class="validate[required,equals[password],minSize[6],maxSize[16]]" name="confirm_password">
					</div>
				</div>
			</div>

			<div class="form-actions">
				<button class="btn btn-blue" type="submit" id="repass-form-button">保存修改</button>
			</div>
		</form>
      </div>
    </div>
  </div>
</div>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/system/repass.js"></script>
</body>
</html>

