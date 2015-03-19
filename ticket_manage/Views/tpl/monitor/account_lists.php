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
				<span class="title"><i class="icon-user"></i> <?php echo $post['type'] == 1 ? '监管机构' : '景区';?>用户</span>
			</div>
			<div class="box-content">
			<form class="fill-up" action="landscape_doStaff.html" method="post" id="staff-form">
				<table class="table table-normal">
					<thead>
					<tr>
						<td width="30%" colspan="2">姓名</td>
						<td width="30%">账号</td>
						<td width="20%"><?php echo $post['type'] == 1 ? '监管机构' : '景区';?></td>
						<td width="10%">状态</td>
						<td width="10%">操作</td>
					</tr>
					</thead>
					<tbody id="staff-body">
					<?php if($data):?>
						<?php foreach($data as $value):?>
					<tr class="status-pending" height="36px">
						<td class="center"><input type="checkbox" class="icheck" name="id[]" value="<?php echo $value['id'];?>"></td>
						<td><?php echo $value['name'];?></td>
						<td><?php echo $value['account'];?></td>
						<?php if($post['type'] == 0){ ?><td><?php echo $name; ?></td><?php }else{?><td><?php echo $value['org_name']; ?></td><?php }?>
						<td class="icon"><?php echo UserCommon::getStatus($value['status']);?></td>
						<td class="icon">
							<a href="monitor_editAccount_<?php echo $value['id']?>.html" title="修改"><i class="icon-edit"></i></a>
						</td>
					</tr>
						<?php endforeach;?>
					<?php else:?>
						<tr>
							<td colspan="5" style="text-align:center"><?php echo "暂无数据，请点击新增，添加新用户";?></td>
						</tr>
					<?php endif;?>
					</tbody>
					<tfoot>
						<td class="icon"><button class="btn btn-default" id="allcheck" style="min-width:60px">全选</button></td>
						<td colspan="4">
							<input type="hidden" name="supervise_id" id='supervise_id' value="<?php echo $value['supervise_id'];?>" >
							<input type="hidden" name="ty" id="ty" value="<?php echo $post['type']?>" >
							<button class="btn btn-gray" id="staff-form-delete" type="submit" name="operate" value="del"><i class="icon-trash"></i> 删除</button>
							<button class="btn btn-black" id="staff-form-edit" type="submit" name="operate" value="status"><i class="icon-warning-sign"></i> 启用/禁用</button>
							<a href="monitor_createAccount_<?php if(isset($value['supervise_id'])){ echo $value['supervise_id']; }else{ echo $_REQUEST['id'];}?>.html?type=<?php echo $post['type']?>" class="btn btn-green" style="min-width:40px"><i class="icon-plus"></i> 新增</a>
						</td>
                       <td></td>
					</tfoot>
				</table>
				<input type="hidden" name="type" value="">
			</form>
		</div>
			<div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination; ?>
                </div>
		
		</div>
	</div>
  
</div>

<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/monitor/staff.js"></script>
</body>
</html>

