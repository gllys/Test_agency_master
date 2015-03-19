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
				.thumbnail img{
					width:100%;
					height:300px
				}
				.label-green,.label-red{
					float:right
				}
			</style>
			<div class="container-fluid padded">
				<div class="box">
					<div class="box-header"><span class="title"><?php echo $scenicInfo[0]['name'];?></span></div>
					<div class="box-content padded">
						<div class="row-fluid">
							<div class="span6 landscape-image">
								<a href="<?php echo $scenicInfo[0]['thumbnail']['url'] ? $scenicInfo[0]['thumbnail']['url'] : '#';?>" class="thumbnail">
									<img src="<?php echo $scenicInfo[0]['thumbnail']['url']?>" alt="">
								</a>
							</div>
							<div class="span6 box">
								<table class="table table-normal landscape-table">
									<tr>
										<td colspan="2">
											<h2>
												<span class="label <?php if($scenicInfo[0]['status'] == 'normal'):?>label-green<?php else:?>label-red<?php endif;?>"><?php echo ScenicCommon::getScenicStatus($scenicInfo[0]['status']);?></span>
												<?php echo $scenicInfo[0]['name'];?>
												<?php if($scenicInfo[0]['is_partner']):?><span class="label label-green" style="margin-left:5px">合作景区</span><?php endif;?>
											</h2>
											
											<?php echo $scenicInfo[0]['districts'][0]['name'].$scenicInfo[0]['districts'][1]['name'].$scenicInfo[0]['districts'][2]['name'];?>
										</td>
									</tr>
									<tr>
										<th align="right">景区级别：</th>
										<td><?php echo $scenicInfo[0]['level']['name'];?></td>
									</tr>
									<tr>
										<th align="right">开放时间：</th>
										<td><?php echo $scenicInfo[0]['hours'];?></td>
									</tr>
									<tr>
										<th align="right">联系电话：</th>
										<td><?php echo $scenicInfo[0]['phone'];?></td>
									</tr>
									<tr>
										<th align="right">取票地址：</th>
										<td><?php echo $scenicInfo[0]['exaddress'];?></td>
									</tr>
									<tr>
										<th align="right">详细地址：</th>
										<td><?php echo $scenicInfo[0]['address'];?></td>
									</tr>
								</table>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">企业名称</span></div>
						<div class="box-content padded">
							<?php  echo $scenicInfo[1]['name'];?>
						</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">景区状态</span></div>
						<li style="list-style-type:none;">
                  			<div class="row-fluid">
                      		 	<div class="span4">
                        		 	<input type="radio" class="icheck" name="status" value="normal" onchange="change(<?php echo $scenicInfo[1]['id'];?>,'normal')" <?php if($scenicInfo[1]['status'] == 'normal'){ echo 'checked=checked';}?>>
                           			<label>启用</label>
                       			</div>
                      	    	<div class="span4">
                           		    <input type="radio" class="icheck" name="status" value="disable" onchange="change(<?php echo $scenicInfo[1]['id'];?>,'disable')" <?php if($scenicInfo[1]['status'] == 'disable'){ echo 'checked=checked';}?>>
                           			<label>禁用</label>
                        		</div>
                        		<div class="span4"></div>
                    		</div>
            			</li>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">企业Logo</span></div>
						<div class="box-content padded">
							<?php if($scenicInfo[2]['url']):?>
							<a href="<?php echo $scenicInfo[2]['url'];?>" class="editable-empty thumbs">
							<img src="<?php echo $scenicInfo[2]['url'];?>" width="100" id="logo">
							</a>
							<?php else:?>
                                暂无  
                            <?php endif;?>
						</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">景点介绍</span></div>
						<div class="box-content padded">
							<?php echo $scenicInfo[0]['biography'];?>
						</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">预订须知</span></div>
					<div class="box-content padded">
						<?php echo $scenicInfo[0]['note'];?>
					</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">交通指南</span></div>
					<div class="box-content padded">
						<?php echo $scenicInfo[0]['transit'];?>
					</div>
				</div>
			</div>
		</div>
		<?php if($pageType == 'detail'):?><script src="Views/js/shopping/index.js"></script><?php endif;?>
		<script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
    		$(document).ready(function(){
        		//点击缩略图查看大图事件
        		$('.thumbs').touchTouch();
   			})
   			function change(id,status){
	        	$.ajax({
	        		type:"GET",
	        		url:"index.php?id="+id+"&status="+status+"&c=organization&a=changestatus",
	        		success:function(data){
	        			alert("修改成功");
	        		}
	        	})
        	}

		</script>
	</body>
</html>
