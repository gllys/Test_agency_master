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
						<a href="system_recAdd.html"><span class="label label-green" style="float:right;margin:10px 10px 0" ><i class="icon-plus"></i> 添加</span></a> <span class="title"><i></i> 首页推荐</span>
					</div>

					<div class="content">
						<table class="table table-normal" >
							<thead>
								<tr>
									<td>推荐编号</td>
									<td>发布时间</td>
									<td>活动时间</td>
									<td>主题</td>
									<td>展示区域</td>
									<td>状态</td>
									<td>操作人</td>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								<?php if($list):?>
									<?php foreach($list as $role):?>
										<tr>
											<td><?php echo $role['id'];?></td>
											<td><?php echo date('Y-m-d',$role['created_at']);?></td>
											<td><?php echo date('Y-m-d',$role['start_time']);?>至<?php echo date('Y-m-d',$role['end_time']);?></td>
											<td><a href="system_recEdit_<?php echo $role['id'];?>.html"><?php echo $role['title'];?></a></td>
											<td><?php echo $role['pos_id'];?></td>
											
											<td><?php echo $status[$role['status']];?></td>
											<td><?php echo $role['admin'];?></td>
											<td class="center">
												<a href="###" onclick="publish(<?php echo $role['id'];?>,1)"><button class="btn btn-blue" <?php echo $role['status']?'disabled="disabled"':''?>>发布</button></a>
												<a href="###" onclick="publish('<?php echo $role['id'];?>',0)"><button class="btn btn-red" <?php echo !$role['status']?'disabled="disabled"':''?>>撤销</button></a>
												
												<a href="system_recEdit_<?php echo $role['id'];?>.html"><button class="btn btn-green" <?php echo $role['status']?'disabled="disabled"':''?>><i class="icon-edit"></i> 编辑</button></a>
												<a href="###" onclick="common_delete('<?php echo $role['id'];?>','index.php?c=system&a=recDelete','system_home.html')"><button class="btn btn-gray"><i class="icon-trash"></i> 删除</button></a>
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
<script type="text/javascript">
function publish(id,ispub)
{
	var msg = ispub?'你确定要发布这条推荐吗？':'你确定要撤销这条推荐吗？';
	var error = ispub?'发布失败！':'撤销失败！';
	var succ = ispub?'发布成功！':'撤销成功！';
	if(window.confirm(msg)){
		$.post('index.php?c=system&a=recPub',{id:id,'status':ispub},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				tmp_errors = error+tmp_errors;
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
				location.href='#show_msg';
			}else if(data.succ){
				var succss_msg = '<div class="alert alert-success"><strong>'+succ+'</strong></div>';
				$('#show_msg').html(succss_msg);
				location.href='#show_msg';
				setTimeout("location.href='system_home.html'", 2000);
			}
		},'json');
	}else{
		return false;
	}
}
</script>
</body>
</html>

