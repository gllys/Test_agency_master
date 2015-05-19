<?php
use common\huilian\utils\Format;
?>
<div class="contentpanel" id="maincontent">
	<div class="row">
		<div class="panel panel-default col-md-6" style="padding:0;">
			<div class="panel-heading">
				<h4 class="panel-title"><?= $title ?></h4>
			</div>
			<div class="panel-body" style="padding-left:30px;">
				<div class="row">
					<div class="col-md-4 col-md-offset-2"><?= $publisher ?></div>
					<div class="col-md-4"><?= Format::date(time()) ?></div>
				</div>
				<div class="h5 lead" style="margin-top:20px;">公告内容：</div>
				<div class="lead">
					<?= $content ?>
				</div>
			</div>
			<div class="panel-footer">
				<button id="close" class="btn btn-default" style="margin-left:80%;">关闭</button>
			</div>
			
		</div>
	</div>
</div>
<script charset="utf-8" src="/js/kindeditor-4.1.10/kindeditor.js"></script>
<script charset="utf-8" src="/js/kindeditor-4.1.10/lang/zh_CN.js"></script>
<script charset="utf-8" src="/js/kindeditor.create.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('#close').click(function() {
			window.close();
		});
	});
</script>