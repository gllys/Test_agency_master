<?php $this->renderPartial('../_nav'); ?>

<div class="new_zucont">
	<p class="new_zuname">
		<label style="margin-top:5px;">组名称：</label>
		<input name="" type="text" class="inp_zuc" value="<?php echo $description;?>">
	</p>
	<form action="/urbac/user/changeRelation/name/<?php echo $name;?>" method="post" id="auth_form">
		<div>
		<?php foreach($roles as $name => $description) { ?>
			<span class="new_zl">
			  <input name="roles[<?php echo $name;?>]" type="checkbox" value="<?php echo $description;?>"<?php echo isset($hasRoles[$name]) ? ' checked="checked"' : '';?>>
			  <label><?php echo $description;?></label>
			</span>
		<?php } ?>
		</div>

<div class="new_zucot02" name="tree">
<?php
// 输出这棵树
foreach($tree as $item1) :
?>
	<!--任务-->
	<span name="task">
		<input name="" type="checkbox" value="" style="margin-top:5px;">
		<label><?php echo $item1['description'];?></label>
		
		<div class="new_zuctit">
			<?php
			// 操作Index菜单
			if(empty($item1['child'])) $item1['child'] = array();
			foreach($item1['child'] as $item2) :
			?>
			<span name="menu">
				<input name="" type="checkbox" value="" style="margin-top:5px;">
				<label><?php if(isset($item2['index']))echo $item2['index']['description'];?></label>

				<div class="menuChildrens">
					<?php
					if(empty($item2['child'])) $item2['child'] = array();
					foreach($item2['child'] as $operation) :
					?>
					<span class="new_zl" name="operation">
						<input name="operations[<?php echo $operation['name'];?>]" type="checkbox" value="<?php echo $operation['description'];?>"<?php echo $operation['has'] ? ' checked="checked"' : '';?>>
						<label><?php echo $operation['description'];?></label>
					</span>
					<?php endforeach; ?>
				</div>
			</span>
			<?php endforeach; ?>
		</div>
	</span>
<?php endforeach; ?>
</div>
<p class="new_baocun"><a href="javascript:;" onclick="$('#auth_form').submit();" class="but_baocun">保 存</a></p>
</form>
</div>
<script type="text/javascript">
	$("span[name=task]").children("input[type=checkbox]").click(function() {
		checkChildren(this);
	});
	
	$("span[name=menu]").children("input[type=checkbox]").click(function() {
		checkChildren(this);
		checkParent($(this).parents("div").eq(0));
	});
	
	$("span[name=operation]").find("input[type=checkbox]").click(function() {
		checkParent($(this).parents("div.menuChildrens"));
	});
	$(".new_zucot02").find("input[checked=checked]").each(function() {
		if($(this).parents("div").eq(0).siblings("input[type=checkbox]").attr("checked") != "checked") {
			$(this).trigger("click");
			$(this).attr("checked", "checked");
		}
	});
	
	// 选择子元素
	function checkChildren(obj) {
		var isChecked = $(obj).attr("checked") == "checked";
		
		$(obj).siblings("div").find("input[type=checkbox]").each(function() {
			isChecked ? $(this).attr("checked", "checked") : $(this).removeAttr("checked");
		});
	}
	
	// 检测
	function checkParent(obj) {
		if($(obj).attr("name") == 'tree') {
			return;
		}
		
		var isCheckParent = false,
				checkbox = $(obj).children("span").children("input[type=checkbox]"),
				len = checkbox.length;

		// 遍历 复选框，判断是否全部被选择
		for(var i = 0; i < len; i++) {
			if(checkbox.eq(i).attr("checked") == "checked") {
				isCheckParent = true;
				break;
			}
		}

		var parentCheckbox = $(obj).siblings("input[type=checkbox]");
		isCheckParent === true ? parentCheckbox.attr("checked", "checked") : parentCheckbox.removeAttr("checked");
		
		// 向上回朔, 直到树的顶端
		checkParent($(obj).parents("div").eq(0));
	}
	
</script>