<?php $this->renderPartial('../_nav'); ?>

<style>
	.user_list{width: 440px; margin-left:15px;}
	.user{float:left; width:80px; margin-bottom:5px;}
	.user span{padding-left:5px;}
	.form_text{margin-top:0px;}
</style>

<div class="data_table">
	<form id="user_form" action="/urbac/user/add/name/<?php echo $name;?>" method="post">
		<h3>选择组用户</h3>
		<div class="user_list">
			<?php
			foreach($allUser as $item) {
				echo '<div class="user">
				<input type="checkbox" name="item[]" value="'.$item->account.'"'.(in_array($item->account, $users) ? ' checked="checked"' : '').'>
				<span'.($item->status==0 ? ' style="color:#09f"' : '').'>'.$item->username.'</span>
				</div>';
			}
			?>
		</div>
		<div class="clear"></div>
		<div>
			<input type="button" class="xz_but" name="save" value="保存">
		</div>
	</form>
</div>
<script type="text/javascript">
	$("#user_form .xz_but").click(function() {
		var name = $(this).attr("name");
		$(this).publicAjaxPost({url:"/urbac/user/"+name+"User/name/<?php echo $name;?>", formId:"user_form"});
	});
</script>