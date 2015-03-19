<?php $this->renderPartial('../_nav'); ?>
<form method="post" action="/urbac/item/createRole">
	组名称:<input type="text" class="form_text" name="role_name" style="margin-top:0px;">
	<input type="submit" class="xz_but" value="添加">
</form>
<br>
<div class="data_table">
	<form id="role_form">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_s1">
			<thead>
				<tr>
					<th></th>
					<th>组名称</th>
					<th>质检量</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$list = Tree::model()->getMyRoles();
				foreach($list as $name => $desc) {
				?>
				<tr>
					<td class="citem"><input type="checkbox" name="item[]" value="<?php echo $name;?>"></td>
					<td class="idesc"><input type="text" class="form_text" name="desc[<?php echo $name;?>]" value="<?php echo $desc;?>" defaultValue="<?php echo $desc;?>"></td>
					<td class="sy_a">
						<a href="/urbac/user/relation/name/<?php echo $name;?>">[权限设定]</a>
						<a href="/urbac/user/index/name/<?php echo $name;?>">[组用户]</a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<a href="javascript:;" class="xz_but" name="save">保存</a>
		<a href="javascript:;" class="xz_but" name="del">删除</a>
	</form>
</div>

<script type="text/javascript">
	$("#role_form .form_text").blur(function() {
		var checkbox = $(this).parents("tr:eq(0)").find("td.citem input");
		
		if($(this).val() == "" || $(this).val() == $(this).attr("defaultValue")) {
			$(this).val($(this).attr("defaultValue"));
			checkbox.removeAttr("checked");
		} else {
			checkbox.attr("checked", "checked");
		}
	});
	
	$("#role_form a.xz_but").click(function() {
		var name = $(this).attr("name"), _this = this, url = "/urbac/item/"+name+"Roles";
		if(name == 'del') {
			var callback = function() {
				var successCall = function(data) {
					$("#role_form input[type=checkbox]").each(function() {
						if($(this).attr("checked") == "checked") {
							$(this).parents("tr").eq(0).remove();
						}
					});
					$.fn.tableColor("role_form");
				}
				$(_this).publicAjaxPost({url:url, formId:"role_form", successCall:successCall});
			}
			
			$(this).miniConfirm({msg:'确认删除吗?', callback:callback});
			return false;
		}
		
		$(this).publicAjaxPost({url:url, formId:"role_form"});
		
		return false;
	});
	
	$.fn.tableColor("role_form");
</script>