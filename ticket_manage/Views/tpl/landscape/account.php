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
				<span class="title"><i class="icon-user"></i> <?php echo isset($landscape['name']) ? $landscape['name'] : '';?>  》 电子票务账号</span>
			</div>
			<div class="box-content">
			<form class="fill-up" action="organization_doStaff.html" method="post" id="staff-form">
				<table class="table table-normal">
					<thead>
					<tr>
						<td width="20%">账号</td>
                        <td width="30%">密码</td>
                        <td width="30%">去登录</td>
						<!--td width="20%">手机号码</td>
						<td width="10%">状态</td -->
						<td width="10%">启用/禁用</td>
					</tr>
					</thead>
					<tbody id="staff-body">
					<?php if(isset($data)):
                        $params = unserialize(PARAMS);
                        $params = $params['params'];
                        $url = $params['ticket-url']['url'];
                        ?>
					<?php foreach($data as $value):?>
					<tr class="status-pending" height="36px">
						<td><strong><?php echo $value['account'];?></strong><font color="<?php echo $value['status'] ? 'green' : 'red' ?>"><?php echo $value['status'] ? '（已启用）' : '（已禁用）' ?></font></td>
						<td><?php echo $value['password_str'];?></td>
                        <td>
                            <a target="new" href="<?php echo $params['ticket-url']['url']?>/site/login/?u=<?php echo $value['account'];?>">电子票务系统</a>
                            <a target="new" href="<?php echo $params['supply-url']['url']?>/site/login/?u=<?php echo $value['account'];?>">供应商系统</a>
                        </td>
						<!--td><?php //echo $value['mobile'] ? $value['mobile'] : $value['telephone'];?></td>
						<td class="icon"><?php //echo UserCommon::getStatus($value['status']);?></td-->
						<td class="icon">
						<?php if($value['status']):?>
							<button type="button" class="btn btn-danger"  onclick="update_status('<?php echo $value['id']?>','0')">禁用</button>
						<?php else:?>
							<button type="button" class="btn btn-success"  onclick="update_status('<?php echo $value['id']?>','1')">启用</button>
						<?php endif;?>
						</td>
					</tr>
					<?php endforeach;?>
					<?php endif;?>
					</tbody>
					<!--tfoot>
						<td class="icon"><button class="btn btn-default" id="allcheck" style="min-width:60px">全选</button></td>
						<td colspan="4">
							<input type="hidden" name="organization_id" id='organization_id' value="<?php //echo $id;?>" />
							<button class="btn btn-gray" id="staff-form-delete" type="submit" name="operate" value="del"><i class="icon-trash"></i> 删除</button>
							<button class="btn btn-black" id="staff-form-edit" type="submit" name="operate" value="status"><i class="icon-warning-sign"></i> 启用/停用</button>
							<a href="organization_addStaff_<?php //echo $id;?>.html" class="btn btn-green" style="min-width:40px"><i class="icon-plus"></i> 新增</a>
						</td>
                       <td></td>
					</tfoot-->
				</table>
				<input type="hidden" name="type" value="">
			</form>
		</div>
			<div class="table-footer">
                <button class="btn btn-primary" type="button" id="g_btn">生成新账号</button>
			  <!--<div class="dataTables_info" id="DataTables_Table_0_info">Showing 1 to 10 of 57 entries</div>-->
			  <?php //print_r($pagination) ;?>
			</div>
		
		</div>
	</div>
  
</div>
<script>
    $(function(){
        $('#g_btn').click(function(){
            $.ajax({
                url: '/landscape_account_0.html',
                type: 'POST',
                dataType: 'JSON',
                data: {landscape_id: '<?php echo $id?>', organization_id: <?php echo $organization_id?>},
                beforeSend: function() {
                    $('#g_btn').attr('disabled', 'disabled');
                },
                success: function(result) {
                    if (result.error == 0) {
                        location.reload();
                    }
                }
            });
        });
    });
</script>
<script type="text/javascript">
	function update_status(id,status){
		$.post('index.php?c=landscape&a=update_status',{id : id , status : status , landscape_id : '<?php echo $id?>' , organization_id: <?php echo $organization_id?>},function(data){
			if (typeof data.error != 'undefined') {
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>操作失败:' + data.error + '</div>';
				$('#show_msg').html(warn_msg);
			} else {
				var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				setTimeout(function() {
				window.location.reload();
				}, 2000);
			}
		},'json');
	}
</script>
</body>
</html>

