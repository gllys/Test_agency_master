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
						<a href="system_roleAdd.html"><span class="label label-green" style="float:right;margin:10px 10px 0" ><i class="icon-plus"></i> 添加</span></a> <span class="title"><i></i> 角色权限</span>
					</div>

					<div class="content">
						<table class="table table-normal" >
							<thead>
								<tr>
									<td>角色编号</td>
									<td>角色名称</td>
									<td>角色说明</td>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								<?php if($list):?>
									<?php foreach($list as $role):?>
										<tr>
											<td><?php echo $role['id'];?></td>
											<td><?php echo $role['name'];?></td>
											<td><?php echo $role['description'];?></td>
											<td class="center">
												<a href="system_roleEdit_<?php echo $role['id'];?>.html"><button class="btn btn-green"><i class="icon-edit"></i> 编辑</button></a>
												<a href="###" onclick="common_delete('<?php echo $role['id'];?>','index.php?c=system&a=roleDelete','system_role.html')"><button class="btn btn-gray"><i class="icon-trash"></i> 删除</button></a>
											</td>
										</tr>
									<?php endforeach;?>
								<?php endif;?>
							</tbody>
						</table>
					</div>
					<div class="dataTables_paginate paging_full_numbers">
						<?php echo $pagination;?>
					</div>
				</div>
			</div>
	</div>
<script src="Views/js/common/common.js"></script>
</body>
</html>

