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
				<form class="fill-up" action="index.php?c=system&a=saveStaff" method="post" id="staff-form">
					<div class="row-fluid">
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="input">
									<lable>真实姓名： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text"  name="name" value="<?php echo $info['name'];?>"  placeholder="请输入真实姓名" class="validate[required,minSize[2],maxSize[16]]">
								</li>
								<?php if($type == 'add'):?>
								<li class="input">
									<lable>账号： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text" value="<?php echo $info['account'];?>"  name="account" placeholder="请输入账号" class="validate[required,minSize[6],maxSize[16]]">
								</li>
								<?php else:?>
									<li class="input">
										<lable>账号：<?php echo $info['account'];?></lable>
									</li>
								<?php endif;?>
							</ul>
						</div>
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="input">
									<lable>手机号码：<span class="note"></span></lable>
									<input type="text" value="<?php echo $info['mobile'];?>" name="mobile"  placeholder="请输入手机号码" class="validate[custom[mobile]">
								</li>
								<?php if($type=='edit'){?>
								<li class="input">
									<lable>重置密码：<span class="note"></span></lable>
									<input type="password"  name="password" placeholder="请输入密码" class="validate[minSize[6],maxSize[16]]">
								</li>
								<input type="hidden" value="<?php echo $info['id'];?>" name="id">
								<?php }else{?>
								<li class="input">
									<lable>密码： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="password"  name="password" placeholder="请输入密码" class="validate[required,minSize[6],maxSize[16]]">
								</li>
								<?php }?>
							</ul>
						</div>
					</div>
					
					<?php if(!$info['is_super']):?>
						<div class="row-fluid">
							<div class="span6">
								<ul class="padded separate-sections">
									<li class="input">
										<lable>角色权限： <strong class="status-error">*</strong><span class="note"></span></lable>
										<select class="uniform" name="role_id">
											<option value=''>请选择角色权限</option>
											<?php if($roles):?>
												<?php foreach($roles as $key => $value): ?>
													<option value="<?php echo $value['id']?>" <?php if($value['id'] == $info['role_id']):?>selected="selected"<?php endif;?>><?php echo $value['name'];?></option>
												<?php endforeach;?>
											<?php endif;?>
										</select>
									</li>
								</ul>
							</div>
						</div>
					<?php endif;?>

					<div class="form-actions" style="text-align:right">
						<input type="hidden" value="<?php echo $type;?>" name="type">
						<button type="button" id="staff-form-button" class="btn btn-blue">保存</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/system/staff_add.js"></script>
</body>
</html>

