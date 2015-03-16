<?php $this->renderPartial('../_nav'); ?>

<div class="row">
	<div class="text">
		<form method="post">
			任务名称:<input type="text" class="form_text" name="task_name" style="margin-top:0px;">
			<input type="submit" class="xz_but" value="添加">
		</form>
		<br>
		<form action="" method="post" id="task_form">
			<table id="createList" class="table_s1">
			<thead>
				<tr>
					<th width="20"></th>
					<th>名称</th>
					<th>描述</th>
				</tr>
			</thead>
			<?php
			$i=0;
			foreach($tasks as $name => $desc) {
				$i++;
			?>
			<tr class="<?php echo $i%2==0 ? 'even' : 'odd';?>">
				<td class="citem"><input type="checkbox" name="item[]" value="<?php echo $name;?>"></td>
				<td><?php echo $name;?></td>
				<td class="idesc"><input type="text" name="desc[<?php echo $name;?>]" value="<?php echo $desc;?>" defaultValue="<?php echo $desc;?>" class="form_text"></td>
			</tr>
			<?php } ?>
			</table>
			<a href="javascript:;" class="xz_but" name="save">保存</a>
			<a href="javascript:;" class="xz_but" name="del">删除</a>
		</form>
	</div>
</div>

<script type="text/javascript">
	$("#createList .form_text").blur(function() {
		var checkbox = $(this).parents("tr").find("td.citem input");
		
		if($(this).val() == "" || $(this).val()==$(this).attr("defaultValue")) {
			$(this).val($(this).attr("defaultValue"));
			checkbox.removeAttr("checked");
		} else {
			checkbox.attr("checked", "checked");
		}
	});
	
	$("#task_form a.xz_but").click(function() {
		var name = $(this).attr("name"), _this = this;
		if(name == 'del') {
			var callback = function() {
				var successCall = function(data) {
					$("#task_form input[type=checkbox]").each(function() {
						if($(this).attr("checked") == "checked") {
							$(this).parents("tr").eq(0).remove();
						}
					});
				}
				$(_this).publicAjaxPost({url:"/urbac/item/manageTask/type/del", formId:"task_form", successCall:successCall});
			}
			$(this).miniConfirm({msg:'确认删除吗?', callback:callback});
		} else {
			$(this).publicAjaxPost({url:"/urbac/item/manageTask/type/"+name, formId:"task_form"});
		}
	});
</script>