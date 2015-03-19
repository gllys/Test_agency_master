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
					<div class="box-content padded">
						<div class="tab-content">
							<div class="tab-pane active">
								<div class="box">
									<div class="box-header">
										<span class="title">门票类型</span>
										<ul class="box-toolbar">
											<li>
												<a data-toggle="modal" href="#modal-dialog">
													<span class="label label-green">添加门票类型</span>
												</a>
											</li>
										</ul>
									</div>
									<div class="box-content">
										<table class="table table-normal">
											<thead>
											<tr>
												<td>类型名称</td>
												<td style="width: 60px">操作</td>
											</tr>
											</thead>
											<tbody>
											<?php if($ticketTypeInfo): ?>
											<?php foreach($ticketTypeInfo as $value):?>
											<tr class="status-pending">
												<td><input type="text" id="name-<?php echo $value['id'];?>" value="<?php echo $value['name'];?>"/></td>
												<td class="icon">
													<a href="###" onclick="update_ticket_type('<?php echo $value['id'];?>', 'name-<?php echo $value['id'];?>')"><i class="icon-save"></i></a> 
													<a href="###" onclick="delete_ticket_type('<?php echo $value['id'];?>')"><i class="icon-trash"></i></a>
												</td>
											</tr>
											<?php endforeach;?>
											<?php endif;?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="dataTables_paginate paging_full_numbers">
									<?php echo $pagination;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- 弹出框 -->
		<div id="modal-dialog" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h6 id="modal-formLabel">
					添加门票类型
				</h6>
			</div>
			<form action="index.php?c=ticket&a=addTicketType" method="post" id="ticket-type-form">
				<div class="modal-body">
					<div>
						<input type="text" name="name" placeholder="输入名称" class="validate[required]" data-prompt-position="topLeft"/>
					</div>
					<div class="divider"><span></span></div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">关闭</button>
					<button class="btn btn-blue" id="ticket-type-form-button">添加</button>
				</div>
			</form>
		</div>
		<script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
		<script src="Views/js/ticket/type.js" type="text/javascript" charset="utf-8"></script>
	</body>
</html>