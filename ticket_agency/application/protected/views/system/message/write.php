<?php
$this->breadcrumbs = array('系统管理', '新消息');
?>
<link href="/css/bootstrap-wysihtml5.css" rel="stylesheet"/>
<div class="contentpanel">
	<div class="row">
		<div class="col-sm-3 col-md-3 col-lg-2">
			<a href="/system/message/write" class="btn btn-success btn-block btn-create-msg">新消息</a>
			<br/>
			<ul class="nav nav-pills nav-stacked nav-msg">
				<li>
					<a href="/system/message/">
						<span class="badge pull-right"></span>
						<i class="glyphicon glyphicon-inbox"></i> 全部
					</a>
				</li>
				<li><a href="/system/message/sys/"><i
							class="glyphicon glyphicon-star"></i> 通知</a></li>
				<li><a href="/system/message/org/"><i
							class="glyphicon glyphicon-bullhorn"></i> 消息</a></li>
				<li><a href="/system/message/sent/"><i
							class="glyphicon glyphicon-send"></i> 已发送</a></li>
			</ul>
		</div>

		<div class="col-sm-9 col-md-9 col-lg-10">
			<div class="panel panel-default">
				<form action="/system/message/send" method="post">
					<div class="panel-heading">
						<h4 class="panel-title">选择接收机构</h4>

						<div class="form-group">
							<label class="col-sm-3 control-label">合作机构</label>
							<br/>
							<select name="receiver_id" id="select-templating" data-placeholder="Choose One" class="width300">
								<option value="">选择</option>
								<option rel="fa-facebook" value="facebook">Facebook</option>
								<option rel="fa-twitter" value="twitter">Twitter</option>
								<option rel="fa-pinterest" value="pinterest">Pinterest</option>
								<option rel="fa-youtube" value="youtube">YouTube</option>
								<option rel="fa-linkedin" value="linkedin">LinkedIn</option>
							</select>
						</div>
					</div>
					<div class="panel-body">
						<textarea name="content" id="wysiwyg" placeholder="请输入消息正文…" class="form-control" rows="10"></textarea>
						<button class="btn btn-primary" style="margin-top: 20px">发送</button>
					</div>
				</form>
				<!-- panel-body -->
				<script src="/js/wysihtml5-0.3.0.min.js"></script>
				<script src="/js/bootstrap-wysihtml5.js"></script>
				<script>
					jQuery(document).ready(function () {
						function format(item) {
							return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel') ) + ' mr10"></i>' + item.text;
						}

						// HTML5 WYSIWYG Editor
						jQuery('#wysiwyg').wysihtml5({'font-styles': false, color: true, html: true});
						jQuery("#select-templating").select2({
							formatResult: format,
							formatSelection: format,
							escapeMarkup: function (m) {
								return m;
							}
						});

					});
				</script>

			</div>
		</div>
	</div>
</div><!-- contentpanel -->
