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
	<div id="show_msg">

	</div>

	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header">
				<span class="title"><i class="icon-edit"></i>新增<?php echo $type == 1 ? '监管机构' : '景区';?>账号</span>
			</div>
			<div class="box-content">
				<form class="fill-up" action="index.php?c=monitor&a=saveAccount" method="post" id="staff-form">
					<div class="row-fluid">
						<div class="span6">
							<ul class="padded" style="list-style:none">
								<li class="input">
									<?php if ($type == 1) : ?>
									<label>选择所属机构<strong class="status-error">*</strong><span class="note"></span></label>
									<div class="row-fluid">
										<div class="span4">
											<?php if ($relation) : ?>
												<select class="uniform" id="supervise_id" name="supervise_id">
													<option value="0">无</option>
													<?php list($select, $_, $children) = output_monitor_select($relation, '', 0, '', $get['id']); echo $select ?>
												</select>
											<?php endif; ?>
										</div>
									</div>
									<?php else : ?>
										<label>选择所属景区<strong class="status-error">*</strong><span class="note"></span></label>
										<div class="row-fluid">
											<div class="span4">
												<?php if ($landscapes) : ?>
													<select class="uniform" id="supervise_id" name="supervise_id">
													<option value="0">无</option>
													<?php list($select, $_, $children) = output_monitor_select($landscapes, '', 0, '', $get['id']); echo $select ?>
												</select>
												<?php endif;?>
											</div>
										</div>
									<?php endif; ?>
								</li>
								<li class="input">
									<lable>账号： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text"  name="account" autocomplete="off" disableautocomplete placeholder="请输入账号" class="validate[required,minSize[6],maxSize[16]]">
								</li>
								<li class="input">
									<lable>密码： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="password"  name="password" autocomplete="off" disableautocomplete placeholder="请输入密码" class="validate[required,minSize[6],maxSize[16]]">
								</li>
								<li class="input">
									<lable>真实姓名： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text"  name="name"  placeholder="请输入真实姓名" class="validate[required,minSize[2],maxSize[16]]">
								</li>
								<li class="input">
									<lable>手机号码： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text" name="mobile" autocomplete="off" disableautocomplete placeholder="请输入手机号码" class="validate[required,custom[mobile]">
								</li>
							</ul>
						</div>
						<input type="hidden" name="type" value="<?php echo $type?>">
					</div>
					<div class="form-actions">
						<button type="button" id="staff-form-button" class="btn btn-blue">保存</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/monitor/account.js"></script>
</body>
</html>

