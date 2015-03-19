<div class="row">
	<div class="text">
		<table id="createList" class="table_s1">
		<thead>
			<tr>
				<th width="20"></th>
				<th>名称</th>
				<th>描述</th>
				<th>归属任务</th>
			</tr>
		</thead>
		
		<?php
		$tasks = Tree::model()->getAllTask();
		foreach($tasks as $name => $desc) {
			$childrens[$name] = Tree::model()->getOwnOperationsOfRole($name);
		}
		
		foreach($list as $k => $item) {
			if(is_string($item)) {
				$name = $item;
				$description = $item;
			} else {
				$name = $item->name;
				$description = $item->description;
			}
		?>
		<tr>
			<td class="citem"><input type="checkbox" name="item[]" value="<?php echo $name;?>"></td>
			<td><?php echo $name;?></td>
			<td class="idesc"><input type="text" name="desc[<?php echo $name;?>]" value="<?php echo $description;?>" defaultValue="<?php echo $description;?>" class="form_text"></td>
			<td class="cparents">
				<input type="hidden" name="oldparent[<?php echo $name;?>]" class="oldparent" value="">
				<select name="parent[<?php echo $name;?>]" defaultValue="">
					<option value="">&nbsp;</option>
					<?php
					foreach($tasks as $key => $taskDesc) {
						echo '<option value="'.$key.'"'.(isset($childrens[$key][$name]) ? ' selected' : '').'>'.$taskDesc.'</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<?php } ?>
		</table>
	</div>
</div>

<script type="text/javascript">
	$("td.cparents select").each(function() {
		$(this).attr("defaultValue", $(this).val());
		$(this).siblings("input.oldparent").val($(this).val());
	}).change(function() {
		var checkbox = $(this).parents("tr").find("td.citem input"),
				input = $(this).parents("tr").find("td.idesc input"),
				isChange = $(this).val() != $(this).attr("defaultValue");
		
		isChange || input.val() != input.attr("defaultValue") ? checkbox.attr("checked", "checked") : checkbox.removeAttr("checked");
	});
	
	$("#createList .form_text").change(function() {
		var checkbox = $(this).parents("tr").find("td.citem input"),
				task = $(this).parents("tr").find("td.cparents select"),
				defaultVal = $(this).attr("defaultValue");
		
		if($(this).val() == "") $(this).val(defaultVal);
		
		$(this).val() == defaultVal && task.val() == task.attr("defaultValue") ? checkbox.removeAttr("checked") : checkbox.attr("checked", "checked");
	});
	$.fn.tableColor('createList');
</script>