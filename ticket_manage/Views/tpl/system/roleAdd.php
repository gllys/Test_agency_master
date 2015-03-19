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

			<style>
				.table td label{
					margin-right:20px;
				}
			</style>
			<div id="show_msg">
			</div>
			<div class="container-fluid padded">
				<div class="box">
					<div class="box-header">
						<span class="title"><?php echo $pageType == 'add' ? '添加' : '修改';?>角色</span>
					</div>
					<?php if($error_msg):?>
						<div class="box-content padded">
							<?php echo $error_msg;?>
						</div>
					<?php else:?>
					<div class="box-content padded">
						<form id="addrole-form"  method="post">
							<input type="hidden" name="id" value="<?php echo $info['id'];?>">
							<input type="hidden" name="pageType" value="<?php echo $pageType;?>"/>

							<ul class="landscape-add">
								<li>
									<div class="row-fluid">
										<div class="span6">
										<label>
											角色名称 <strong class="status-error">*</strong> 
											<input type="text" name="name" value="<?php echo $info['name'];?>"  placeholder="" class="validate[required]">
											</label>
										</div>
									</div>
								</li>
								<li>
									<div class="row-fluid">
										<div class="span6">
										<label>
											角色说明 <strong class="status-error">*</strong> 
											<textarea placeholder="" name="description"  class="summary validate[required]"><?php echo $info['description'];?></textarea>
										</label>
										</div>
									</div>
								</li>
								<li>
									<div class="row-fluid permission">
										<label>权限设置</label> <strong class="status-error"></strong> <span class="note"></span>
										<table class="table">
											<?php if($menuList):?>
												<?php foreach($menuList as $first):?>
													<tr>
														<th style="width: 100px;"><?php echo $first['title'];?></th>
														<td></td>
													</tr>
													<tr>
														<th></th>
														<td>
															<?php if($first['menu']):?>
																<?php foreach($first['menu'] as $second):?>
																	<?php if(isset($second['permission_name'])) :?>
																		<input type="checkbox" class="icheck" name="permissions[]" value="<?php echo $second['permission_id'];?>" <?php if($info['permissions'] && in_array($second['permission_id'], explode(',', $info['permissions']))):?>checked="checked"<?php endif;?> >
																		<?php echo $second['permission_name'];?>
																	<?php endif;?>
																<?php endforeach;?>
															<?php endif;?>
														</td>
													</tr>
												<?php endforeach;?>
											<?php endif;?>
										</table>
									</div>
								</li>
							</ul>
							<div align="right">
								<button class="btn btn-green" type="submit" id="addrole-form-button"><?php if($pageType == 'add'):?>保存<?php else:?>修改<?php endif;?></button>
							</div>
						</form>
					</div>
					<?php endif;?>
				</div>
			</div>
		</div>
		<script>
			var phpvars   = {};
			phpvars.pageType = "<?php echo $pageType;?>";

		</script>
		<script src="Views/js/system/role.js"></script>
		<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
	</body>
</html>