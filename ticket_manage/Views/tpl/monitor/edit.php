<!DOCTYPE html>
<html>
<?php get_header();?>
<body>
<?php get_top_nav();?>
<div class="sidebar-background">
	<div class="primary-sidebar-background"></div>
</div>
<?php get_menu();?>
<link rel="stylesheet" href="Views/css/jquery-ui.css">
<style>
	.title .ui-helper-hidden-accessible {
		display: none;
	}
</style>
<div class="main-content">
	<?php get_crumbs();?>
	<div id="show_msg"></div>
	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header"><span class="title"><i class="icon-edit"></i> 编辑监管机构信息</span></div>
			<div class="box-content">
				<form action="index.php?c=monitor&a=save" method="post" id="monitor_update_form" class="fill-up">
					<input type="hidden" name="id" value="<?php echo $monitor['id']?>">
					<div class="row-fluid">
						<div class="span6">
							<ul class="padded separate-sections">
								<li class="input">
									<label>机构名称<strong class="status-error">*</strong><span class="note"></span></label>
										<input type="text" value="<?php echo $monitor['name'] ?>"
										       data-prompt-position="topLeft" class="validate[minSize[2],maxSize[32]]"
										       name="name" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>选择上级机构<strong class="status-error">*</strong><span class="note"></span></label>
									<div class="row-fluid">
										<div class="span4">
											<?php if ($relation) : ?>
												<select class="uniform" name="relation[p_id]">
													<option value="0">无</option>
													<?php list($select, $_, $children) = output_monitor_select($relation, '', $get['id']); echo $select ?>
												</select>
											<?php endif; ?>
										</div>
									</div>
								</li>
								<li class="input">
									<label>简介<strong class="status-error">*</strong><span class="note"></span></label>
										<textarea rows="6" name="brief" placeholder="" style="width:400px" data-prompt-position="topLeft"
										          class="validate[required,maxSize[10000]]"><?php echo $monitor['brief'] ?></textarea>
								</li>
							</ul>
						</div>
						<div class="span6">
							<ul class="padded">
								<li class="input">
									<label>联系人<span class="note"></span></label>
									<input type="text" value="<?php echo $monitor['contact'] ?>"
									       data-prompt-position="topLeft"
									       name="contact" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>移动电话<span class="note"></span></label>
									<input type="text" value="<?php echo $monitor['phone'] ?>"
									       data-prompt-position="topLeft"
									       name="phone" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>固定电话<span class="note"></span></label>
									<input type="text" value="<?php echo $monitor['telephone'] ?>"
									       data-prompt-position="topLeft"
									       name="telephone" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>传真<span class="note"></span></label>
									<input type="text" value="<?php echo $monitor['fax'] ?>"
									       data-prompt-position="topLeft"
									       name="fax" placeholder="" style="width:300px">
								</li>
								<li class="input">
									<label>电子邮箱<span class="note"></span></label>
									<input type="text" value="<?php echo $monitor['email'] ?>"
									       data-prompt-position="topLeft"
									       name="email" placeholder="" style="width:300px">
								</li>
							</ul>
						</div>
					</div>
					<div class="form-actions">
						<button class="btn btn-lg btn-blue" type="button" id="btn-edit">保存</button>
					</div>
				</form>
			</div>
		</div>
		<div class="box">
			<div class="box-header"><span class="title"><i class="icon-reorder"></i> 直属机构</span>
				<span class="title" style="float:right">
					查找孤立机构并设置监管
                    <input id="monitor_auto_complete" />
				</span></div>
			<div class="box-content">
				<table class="table table-normal">
					<thead>
					<tr>
						<td style="width: 30px">编号</td>
						<td>机构名称</td>
						<td>状态</td>
						<td>操作</td>
					</tr>
					</thead>

					<tbody>
					<?php if ($children) : ?>
						<?php foreach ($children as $m) : ?>
							<tr>
								<td><?php echo $m['id']?></td>
								<td><?php echo $m['name']?></td>
								<td><?php echo $m['status']?></td>
								<td class="icon">
									<div class="span2">
										<a href="monitor_lists_<?php echo $m['id'];?>.html" title="浏览"><button class="btn btn-blue">浏览</button></a>
										<a href="monitor_edit_<?php echo $m['id'];?>.html" title="编辑"><button class="btn btn-blue">编辑</button></a>
										<a href="javascript:;" onclick="releaseMonitor(<?php echo $m['id']?>,<?php echo $monitor['id']?>);" title="解除"><button class="btn btn-blue">解除</button></a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="box">
			<div class="box-header"><span class="title"><i class="icon-exchange"></i> 直属景区</span>
				<span class="title" style="float:right">
					查找景区并设置监管
                    <input id="scenic_auto_complete" />
				</span></div>
			<div class="box-content">
				<table class="table table-normal">
					<thead>
					<tr>
						<td style="width: 50px">机构编号</td>
						<td>景区名称</td>
						<td>操作</td>
					</tr>
					</thead>

					<tbody>
					<?php if ($scenic) : ?>
						<?php foreach ($scenic as $s) : $s['id'] = 2147483647 - $s['id']?>
							<tr>
								<td><?php echo $s['id']?></td>
								<td><?php echo $s['name']?></td>
								<td class="icon">
									<div class="span2">
										<a href="landscape_edit_<?php echo $s['id'];?>.html" title="编辑"><button class="btn btn-blue">编辑</button></a>
										<a href="javascript:;" onclick="releaseScenic(<?php echo $s['id']?>,<?php echo $monitor['id']?>);" title="解除"><button class="btn btn-blue">解除</button></a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>
</div>

<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/jquery-ui.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/monitor/write.js"></script>
<script>
	$(function() {
		$( "#monitor_auto_complete" ).autocomplete({
			source: "/monitor_monitor.html",
			minLength: 1,
			focus: function( event, ui ) {
				$( "#monitor_auto_complete" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( "#monitor_auto_complete" ).val( ui.item.label );
				catchMonitor(ui.item['id'], <?php echo $monitor['id']?>, ui.item.label, '<?php echo $monitor['name'] ?>');
			}
		})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
		};
		$( "#scenic_auto_complete" ).autocomplete({
			source: "/monitor_scenic.html",
			minLength: 1,
			focus: function( event, ui ) {
				$( "#scenic_auto_complete" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( "#scenic_auto_complete" ).val( ui.item.label );
				catchScenic(ui.item['id'], <?php echo $monitor['id']?>, ui.item.label, '<?php echo $monitor['name'] ?>');
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
		};
	});
</script>
