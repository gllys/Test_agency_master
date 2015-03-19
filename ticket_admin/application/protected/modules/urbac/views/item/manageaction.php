<?php $this->renderPartial('../_nav'); ?>
<?php
$tasks = Tree::model()->getAllTask();
foreach($tasks as $name => $desc) {
	$childrens[$name] = Tree::model()->getOwnOperationsOfRole($name);
}
?>
<div class="form">
	<div id="search_form">
		<p class="gap">
			<label for="关键字">关键字：</label>
			<input type="text" class="form_text" name="keyword" style="margin-top: 0px;">

			<label for="归属任务">归属任务：</label>
			<select name="task" class="sel_wor" style="margin-top: 0px;">
				<option value="0">全部</option>
				<?php
				foreach($tasks as $key => $taskDesc) {
					echo '<option value="'.$key.'"'.(isset($childrens[$key][$name]) ? ' selected' : '').'>'.$taskDesc.'</option>';
				}
				?>
			</select>
			<a href="javascript:;" class="xz_but" title="查询">查询</a>
		</p>
		<br>
	</div>
	
	<form method="post" action="" id="operation_form">
		<?php $this->renderPartial('_operationTable', array('list'=>$list)); ?>
		<div class="row buttons">
			<a href="javascript:;" class="xz_but" name="save">保存</a>
			<a href="javascript:;" class="xz_but" name="del">删除</a>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(".buttons a.xz_but").click(function() {
		var name = $(this).attr("name"), successCall = '';
		if(name == 'del') {
			successCall = function(data) {
				$("#operation_form input[type=checkbox]").each(function() {
					if($(this).attr("checked") == "checked") {
						$(this).parents("tr").eq(0).remove();
					}
				});
				$.fn.tableColor('createList');
			}
		}
		
		$(this).publicAjaxPost({url:"/urbac/item/manageAction/type/"+name, formId:"operation_form", successCall:successCall});
	});
	
	$("#search_form a.xz_but").click(function() {
		var keyword = $.trim($("#search_form input[name=keyword]").val().toLocaleLowerCase());
		var task = $("#search_form select[name=task]").val();
		
		if(task == 0 && keyword == '') {
			$("#operation_form tr").show();
		} else if(task != 0 && keyword == '') {
			$("#operation_form select").each(function() {
				var obj = $(this).parents("tr").eq(0);
				$(this).val() == task ? obj.show() : obj.hide();
			});
			
		} else if(task == 0 && keyword != '') {
			$("#operation_form tbody tr").each(function() {
				var nameString = $(this).find("td.citem input").val().toLocaleLowerCase();
				var descString = $(this).find("td.idesc input").val().toLocaleLowerCase();
				(nameString.indexOf(keyword) == -1 && descString.indexOf(keyword) == -1) ? $(this).hide() : $(this).show();
			});
			
		} else {
			$("#operation_form tbody tr").each(function() {
				var nameString = $(this).find("td.citem input").val().toLocaleLowerCase();
				var descString = $(this).find("td.idesc input").val().toLocaleLowerCase();
				var selectString = $(this).find("select").val();
				((nameString.indexOf(keyword) != -1 || descString.indexOf(keyword) != -1) && selectString == task) ? $(this).show() : $(this).hide();
			});
		}
		$.fn.tableColor('createList');
	});
</script>