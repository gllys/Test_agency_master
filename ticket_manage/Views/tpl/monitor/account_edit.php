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
				<span class="title"><i class="icon-edit"></i>编辑员工</span>
			</div>
			<div class="box-content">
				<form class="fill-up" action="index.php?c=monitor&a=saveStaff" method="post" id="staff-form">
					<div class="row-fluid">
						<?php if($account):?>
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="input">
									<lable>真实姓名： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text"  name="name" value="<?php echo $account['name'];?>"  placeholder="请输入真实姓名" class="validate[required,minSize[2],maxSize[16]]">
								</li>
								<li class="input">
									<lable>账号： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text" value="<?php echo $account['account'];?>"  name="account" placeholder="请输入账号" class="validate[required,minSize[6],maxSize[16]]">
								</li>
								<li class="input">
									<lable>邮箱：<strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text" value="<?php echo $account['email'];?>"  name="email" placeholder="请输入邮箱" class="validate[required,custom[email]">
								</li>
							</ul>
						</div>
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="input">
									<lable>手机号码： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text" value="<?php echo $account['mobile'];?>" name="mobile"  placeholder="请输入手机号码" class="validate[required,custom[mobile]">
								</li>
								<input type="hidden" value="<?php echo $account['id'];?>" name="id">
								<input type="hidden" id="supervise_id" name="supervise_id" value="<?php echo $account['supervise_id'];?>" >
								<li class="input"> 
									<lable>密码： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="password"  name="password" placeholder="请输入密码完成资料更新" class="validate[required,minSize[6],maxSize[16]]">
								</li>
							</ul>
						<?php endif;?>
						</div>
					</div>
					<div class="form-actions" style="text-align:right">
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

