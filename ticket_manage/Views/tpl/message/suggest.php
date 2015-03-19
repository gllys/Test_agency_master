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

 <style>
div.selector{
	margin:0 10px;
	width:200px;
	}
.table-normal tbody td a{
	margin:0 5px;
	text-decoration:none;
	}
.table-normal button{
	min-width:inherit;
	}
.table-normal tbody td{
	text-align:center
	}
.btn-group ul {
    min-width: 80px;
}
#report textarea{
    width:100%;
    height:100px;
}
</style>
			<div class="content">
			<table class="table table-normal order-list">
				<thead>
				    <tr>
						<td>编号</td>
						<td>内容</td>
						<td>发布日期</td>
						<td>状态</td>
						<td>操作</td>
				    </tr>
				</thead>
				<tbody>
				
					<?php if($list):?>
						<?php foreach($list as $suggest):?>
							<tr>								
								<td width="10%"><?php echo $suggest['id'];?></td>
								<td width="50%"><a href="message_report.html?id=<?php echo $suggest['id']?>&state=<?php echo $suggest['state']?>" data-toggle="modal">
								<?php echo $suggest['content']?>
								</a></td>
								<td width="20%"><?php echo $suggest['created_at'];?></td>
								<td width="10%"><?php if($suggest['state'] === 0) { echo '未处理';} else{ echo '已处理';}?></td>		
								<td width="10%"><?php if($suggest['state'] ===0) {?>
								<a title="回复" href="message_report.html?id=<?php echo $suggest['id'];?>&state=0" data-toggle="modal" >
									<button class="btn btn-blue">回复</button>
								</a>
						 		<?php } else{ echo '已回复';}?>
								</td>
							</tr>
						<?php endforeach;?>
					<?php else:?>
						
						<tr>
							<td colspan="7">暂无记录</td>
						</tr>
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

<script src="Views/js/message/notice.js"></script>
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
</body>
</html>

