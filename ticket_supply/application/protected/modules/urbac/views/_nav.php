<style>
	table .form_text{width:250px; background-color:#fafafa}
	input.xz_but{border:0px; cursor:pointer}
</style>
<div class="tongj_cont clearfix">
	<ul id="op_nav">
		<li class="ton_none"><a name="itemindex" href="/urbac/item/">用户组管理</a></li>
		<li class="ton_none"><a name="usermanageUser" href="/urbac/user/manageUser">用户管理</a></li>
		<li class="ton_none"><a name="itemcreateTask" href="/urbac/item/createTask">任务管理</a></li>
		<li class="ton_none"><a name="itemmanageAction" href="/urbac/item/manageAction">操作管理</a></li>
		<li class="ton_none"><a name="itemautoCreate" href="/urbac/item/autoCreate">添加操作</a></li>
		<li class="ton_none" style="display:none"><a name="userrelation" href="javascript:;">权限设定</a></li>
		<li class="ton_none" style="display:none"><a name="userindex" href="javascript:;">组用户</a></li>
	</ul>
</div>

<script type="text/javascript">
	var aid = "<?php echo $this->id.$this->action->id;?>";
	$("#op_nav a[name="+aid+"]").parent("li").removeClass().addClass("ton_now").show();
</script>
