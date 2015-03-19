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

<style>
.table-normal tbody td a{ text-decoration: none;}
#staff-body a{color:#5A6573;}
button i{ position:relative;top:-1px;}
</style>
	
	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header">
				<span class="title"><i class="icon-user"></i> <?php echo $organization['name'];?>  》 旅行社用户</span>
			</div>
			<div class="box-content">
			<form class="fill-up" action="organization_doStaff.html" method="post" id="staff-form">
				<table class="table table-normal">
					<thead>
					<tr>
						<td width="20%">姓名</td>
						<td width="20%">账号</td>
						<td width="20%">手机号码</td>
                                                <td width="20%">等级</td>
						<td width="20%">状态</td>
					</tr>
					</thead>
					<tbody id="staff-body">
					<?php if($data):?>
					<?php foreach($data as $value):
                                            ?>
					<tr class="status-pending" height="36px">
						<td><?php echo $value['name'];?></td>
						<td><?php echo $value['account'];?></td>
						<td><?php echo $value['mobile'] ;?></td>
                                                <td><?php echo $value['is_super']?'超级管理员':"普通员工" ;?></td>
						<td><?php echo $value['status']?'<font color="green">已启用</font>':'<font color="red">已禁用</font>'?></td>
					</tr>
					<?php endforeach;?>
					<?php endif;?>
					</tbody>
					<!--tfoot>
						<td class="icon"><button class="btn btn-default" id="allcheck" style="min-width:60px">全选</button></td>
						<td colspan="4">
							<input type="hidden" name="organization_id" id='organization_id' value="<?php echo $id;?>" />
							<button class="btn btn-gray" id="staff-form-delete" type="submit" name="operate" value="del"><i class="icon-trash"></i> 删除</button>
							<button class="btn btn-black" id="staff-form-edit" type="submit" name="operate" value="status"><i class="icon-warning-sign"></i> 启用/停用</button>
							<a href="organization_addStaff_<?php echo $id;?>.html" class="btn btn-green" style="min-width:40px"><i class="icon-plus"></i> 新增</a>
						</td>
                       <td></td>
					</tfoot-->
				</table>
				<input type="hidden" name="type" value="">
			</form>
		</div>
			<div class="table-footer">
			  <!--<div class="dataTables_info" id="DataTables_Table_0_info">Showing 1 to 10 of 57 entries</div>-->
			  <?php //print_r($pagination) ;?>
			</div>
		
		</div>
	</div>
  
</div>

<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/organization/staff.js"></script>
</body>
</html>

