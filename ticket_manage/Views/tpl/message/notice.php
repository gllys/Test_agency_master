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
</style>
	<div class="container-fluid padded">
		<div class="box">
			<div class="table-header" style="height:auto;padding-bottom:10px;">
			  <form action="message_notice.html">
				<div class="row-fluid" style="margin-bottom:10px;">
					发布时间：<input type="text" placeholder="" name="time" style="width:200px;margin:0 10px 0" class="form-time" value="<?php echo $get['time']?>">
					机构类型：<select class="uniform" name="organization_type">
								<option  selected="selected" value="">所有</option>
								<?php foreach($organization_status as $key => $value):?>
									<option value="<?php echo $key;?>" <?php if($get['organazition_type'] == $key){ echo "selected='selected'";}?>><?php echo $value;?></option>
								<?php endforeach;?>
							</select>
					状态：<select class="uniform" name="status">
								<option  selected="selected" value="">所有</option>
								<?php foreach($status as $key => $value):?>
									<option value="<?php echo $key;?>" <?php if($get['status'] == $key){ echo "selected='selected'";}?>><?php echo $value;?></option>
								<?php endforeach;?>
							</select>
				  <button class="btn btn-default" style="float:none;">查询</button>
				</div>
			  </form>
            </div>


			<div class="content">
			<table class="table table-normal order-list">
				<thead>
				    <tr>
						<td>编号</td>
						<td>发布机构</td>
						<td>类别</td>
						<td>发布日期</td>
						<td>内容</td>
						<td>状态</td>
						<td>操作</td>
				    </tr>
				</thead>
				<tbody>
					
					<?php if($data):?>
						<?php foreach($data as $notice):?>
							<tr>
								<td>
									<a href="message_detail_<?php echo $notice['id']?>.html"><?php echo $notice['id'];?></a>
								</td>
								<td><?php echo $notice['from_organization'] ? $notice['from_organization']['name'] : '汇联';?></td>
								<td><?php echo $notice['from_organization'] ? MessageCommon::getOrganizationType($notice['from_organization']['type']) : '系统';?></td>
								<td><?php echo $notice['created_at'];?></td>
								<td>
									<a href="message_detail_<?php echo $notice['id']?>.html">
										<?php echo msubstr($notice['content'], 18);?>
									</a>
								</td>
								<td><?php echo MessageCommon::getVerifyStatus($notice['status']);?></td>
								<td>
									<div class="btn-group">
										<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i></button>
										<ul class="dropdown-menu">
										<li><a href="javascript:;" onclick="verifyNotice(<?php echo $notice['id']?>, 'normal')">通过</a></li>
										<li><a href="javascript:;" onclick="verifyNotice(<?php echo $notice['id']?>, 'failed')">拒绝</a></li>
										</ul>
									</div>
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
<script>
$(document).ready(function() {
	$('.form-time').daterangepicker({
		format:'YYYY-MM-DD'
	})
})
</script>
</body>
</html>

