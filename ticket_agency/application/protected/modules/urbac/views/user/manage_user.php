<?php $this->renderPartial('../_nav'); ?>

<style>
	.user_list{width: 440px; margin-left:15px;}
	.user{float:left; width:80px; margin-bottom:5px;}
	.user span{padding-left:5px;}
	.form_text{margin-top:0px;}
</style>

<div class="data_table">
	<h3>添加用户</h3>
	<form action="/urbac/user/addUser/" method="post" id="add_user_form">
		用户帐号:<input type="text" name="account" class="form_text" value="">
		用户姓名:<input type="text" name="username" class="form_text" value="">
		<input type="button" class="xz_but" value="保存">
	</form>
	<br>
	
	<form id="user_form" action="/urbac/user/add/name/<?php if(isset($name))echo $name;?>" method="post">
		<h3>停用帐号</h3>
		<div class="user_list">
			<?php
			foreach($allUser as $item) {
				if($item->status==0) continue;
				echo '<div class="user">
				<input type="checkbox" name="item[]" value="'.$item->account.'">
				<span>'.$item->username.'</span>
				</div>';
			}
			?>
		</div>
		<div class="clear"></div>
		<div>
			<input type="button" class="xz_but" name="del" value="停用">
		</div>
	</form>
	<br>
	<form id="user_form_2" action="/urbac/user/add/name/<?php if(isset($name))echo $name;?>" method="post">
		<h3>启用帐号</h3>
		<div class="user_list">
			<?php
			foreach($allUser as $item) {
				if($item->status==1) continue;
				
				echo '<div class="user">
				<input type="checkbox" name="item[]" value="'.$item->account.'">
				<span>'.$item->username.'</span>
				</div>';
			}
			?>
		</div>
		<div class="clear"></div>
		<div>
			<input type="button" class="xz_but" name="open" value="启用">
		</div>
	</form>
</div>
<script type="text/javascript">
	$("#add_user_form .xz_but").click(function() {
		var successCall = function(data) {
			var account = $("#add_user_form input[name=account]").val(),
			username = $("#add_user_form input[name=username]").val(),
			html = '<div class="user">'+
			'<input type="checkbox" name="item[]" value="'+account+'">'+
			'<span>'+username+'</span>'+
			'</div>';
			
			$("#user_form .user_list").append(html);
			$("#add_user_form input[type=text]").val("");
		};
		
		$(this).publicAjaxPost({url:"/urbac/user/addUser", formId:"add_user_form", successCall:successCall});
	});
	
	$("#user_form .xz_but, #user_form_2 .xz_but").click(function() {
		var name = $(this).attr("name"), _this = this;
		if(name == 'del') {
			var callback = function() {
				var successCall = function(data) {
					$("#user_form input[type=checkbox]").each(function() {
						if($(this).attr("checked") == "checked") {
							$(this).parent("div.user").appendTo($("#user_form_2 .user_list"));
						}
					});
				}
				$(_this).publicAjaxPost({url:"/urbac/user/"+name+"User", formId:"user_form", successCall:successCall});
			}
			
			$(this).miniConfirm({msg:'确认停用吗?', callback:callback});
			return false;
		} else if(name == 'open') {
			var callback = function() {
				var successCall = function(data) {
					$("#user_form_2 input[type=checkbox]").each(function() {
						if($(this).attr("checked") == "checked") {
							$(this).parent("div.user").appendTo($("#user_form .user_list"));
						}
					});
				}
				$(_this).publicAjaxPost({url:"/urbac/user/"+name+"User", formId:"user_form_2", successCall:successCall});
			}
			
			$(this).miniConfirm({msg:'确认启用吗?', callback:callback});
			return false;
		}
		
	});
</script>