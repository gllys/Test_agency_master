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
				<span class="title"><i class="icon-edit"></i><?php echo $organization['name'];?>  》 <?php echo ($type=='edit')?'编辑':'新增'?>员工</span>
			</div>
			<div class="box-content">
				<form class="fill-up" action="index.php?c=organization&a=saveStaff" method="post" id="staff-form">
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
									<lable>手机号码： <strong class="status-error">*</strong><span class="note"></span></lable>
									<input type="text" value="<?php echo $info['mobile'];?>" name="mobile"  placeholder="请输入手机号码" class="validate[required,custom[mobile]">
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
						<input type="hidden" name="organization_id" id="organization_id" value="<?php echo $organization['id'];?>">
						<input type="hidden" value="<?php echo $type;?>" name="type">
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
<script src="Views/js/organization/staff_add.js"></script>
</body>
</html>

