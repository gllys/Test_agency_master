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
					<div class="table-header" style="height:auto;padding-bottom:10px;">
						<div class="row-fluid" style="margin-bottom:10px;">
							<div>
								一级票务名称：<?php echo $landscapeInfo['name'];?>
							</div>
							<?php if($landscapeInfo['location_hash']):?>
							<div>
								已接入的景区hash：<?php echo $landscapeInfo['location_hash'];?>
							</div>
							<?php endif;?>
						</div>
						<form action=""> 
							<div class="row-fluid" style="margin-bottom:10px;">
								数据中心POI5位hash值：<input type="text" name="itour_ism_landscapes_hash" placeholder="数据中心POI5位hash值" value="<?php echo $get['itour_ism_landscapes_hash'];?>" style="width:180px;margin:0 10px 0">
								数据中心POI景区名：<input type="text"  placeholder="数据中心POI景区名"  name="itour_ism_landscapes_name" value="<?php echo $get['itour_ism_landscapes_name'];?>" style="width:180px;margin:0 10px 0">
								<button class="btn btn-default" style="float:none;">搜索</button>
							</div>
						</form>
					</div>

					<div class="content">
						<table class="table table-normal">
							<thead>
								<tr>
									<td>hash值</td>
									<td>景区名称</td>
									<td>操作</td>
								</tr>
							</thead>
							<tbody>
								<?php if($error_msg):?>
									<tr>
										<td colspan="3"><?php echo $error_msg;?></td>
									</tr>
								<?php else:?>
									<?php if($itour_ism_landscapes_list):?>
										<?php foreach($itour_ism_landscapes_list as $itour_ism_landscape):?>
										<tr>
											<td><?php echo $itour_ism_landscape['hash'];?></td>
											<td><?php echo $itour_ism_landscape['name'];?></td>
											<td><button class="btn btn-default" onclick="itourismLandscapeIn('<?php echo $itour_ism_landscape['hash'];?>')">接入</button></td>
										</tr>
										<?php endforeach;?>
									<?php else :?>
										<?php if(!$get['id']):?>
											<tr>
												<td colspan="3">未选择一级票务</td>
											</tr>
										<?php else :?>
										<tr>
											<td colspan="3">无数据</td>
										</tr>
										<?php endif;?>
									<?php endif;?>
								<?php endif;?>
							</tbody>
						</table>
					</div>

					<div class="table-footer">
						<div class="dataTables_paginate paging_full_numbers">
							<?php echo $pagination;?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function itourismLandscapeIn(hash){
				var msg = '您确定要将接入该景区吗？';
				if(window.confirm(msg)){
					$('#show_msg').empty();
					$.post('ticket_itourismLandscapeIn.html',{id:"<?php echo $get['id'];?>", itour_ism_landscape_hash: hash},function(data){
						if(data.errors){
							var tmp_errors = '';
							$.each(data.errors, function(i, n){
								tmp_errors += n;
							});
							console.log(tmp_errors);
							var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
							$('#show_msg').html(warn_msg);
							location.href='#show_msg';
						}else if(data['data'][0]['id']){
							var succss_msg = '<div class="alert alert-success"><strong>接入成功!</strong></div>';
							$('#show_msg').html(succss_msg);
							location.href='#show_msg';
							setTimeout("location.href='ticket_index.html'", '2000');
						}
					},'json');
				}else{
					return false;
				}
			}
		</script>
	</body>
</html>