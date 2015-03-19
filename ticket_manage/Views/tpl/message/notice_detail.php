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
	.table-1 td:nth-child(2n-1){
		font-weight:700;
		width:100px;
		}
	.table-1 td:nth-child(2n){
		float:left
		}
	.btn-group ul {
	    min-width: 80px;
	}
	</style>


	<div class="container-fluid padded">
		<div class="box">
			<table class="table table-normal table-1">
				<tr>
				<td>编号</td>
				<td><?php echo $message['id']?></td>
				<td>发布机构</td>
				<td><?php echo isset($message['from_organization']) ? $message['from_organization']['name'] : '汇联';?></td>
				<td>类型</td>
				<td><?php echo isset($message['from_organization']) ? MessageCommon::getOrganizationType($message['from_organization']['type']) : '系统';?></td>
				</tr>
				<tr>
				<td>发布者</td>
				<td><?php echo $message['publish']['name'];?></td>
				<td>发布时间</td>
				<td><?php echo $message['created_at'];?></td>
				<td>状态</td>
				<td><span class="label label-<?php echo $message['status'] == 'normal' ?  'green' : 'red'?>"><?php echo MessageCommon::getVerifyStatus($message['status']);?></span></td>
				</tr>
				<tr>
				<td>审核时间</td>
				<td><?php echo $message['verify_time'] ? $message['verify_time'] : '';?></td>
				<td>操作员</td>
				<td><?php echo $message['verify']['name'] ? $message['verify']['name'] : '';?></td>
				</tr>
			</table>
		</div>
		<div class="box">
			<table class="table table-normal order-list">
				<thead>
				  <tr>
						<td style="text-align:left">消息内容</td>
						<td width="100">操作</td>
				  </tr>

				</thead>
				<tbody>
				  <tr>
						<td style="text-align:left">
							<?php echo $message['content'];?>
						</td>
						<td>
							<div class="btn-group">
								<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i></button>
								<ul class="dropdown-menu">
									<li><a href="javascript:;" onclick="verifyNotice(<?php echo $message['id'];?>, 'normal')">通过</a></li>
								<?php if($message['status'] != 'failed'):?>
									<li><a href="javascript:;" onclick="verifyNotice(<?php echo $message['id'];?>, 'failed')">拒绝</a></li>
								<?php else:?>
									<li><a href="javascript:;" onclick="deleteNotice(<?php echo $message['id'];?>, 'delete')">删除</a></li>
								<?php endif;?>
								</ul>
							</div>
							<!--<a href="javascript:;" onclick="verifyNotice(<?php echo $message['id'];?>, 'failed')"><i class="icon-trash"></i></a>-->
						</td>
				  </tr>
				</tbody>
			</table>
            </div>
			

		</div>
	</div>
<script src="Views/js/common/common.js"></script>
<script type="text/javascript" src="Views/js/message/notice.js"></script>
</body>
</html>

