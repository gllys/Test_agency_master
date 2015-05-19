<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="/css/style.default.css" rel="stylesheet">
        <title>创建供应商账号</title>
	</head>
	<body class="signin">
		<div class="panel panel-signup">
			<div class="panel panel-body">
				<div class="logo text-center">
                    <img src="/img/logo.png" style="height:50px" alt="">
                </div>
				<h4 class="text-center">创建供应商账号</h4>
                <p class="text-center">请填写账号资料</p>
				<!-- 注册表单 -->
				<form action="#" id="RegisterForm" class="form-horizontal" method="post">
					<div class="row">
			            <div class="col-sm-6 text-black">用户名 <br/>
				            <div class="input-group ">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i><span class="text-danger">*</span></span>
								<input id="account" name="RegisterForm[account]" tag="用户名" type="text" maxlength="20" placeholder="请输入用户名" class="form-control validate[required, minSize[6], maxSize[20], custom[numChinese],ajax[ajaxUsername]]">
				            </div><!-- input-group -->
			            </div>
						<div class="col-sm-6 text-black">手机号 <br/>
							<div class="input-group ">
								<span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i><span class="text-danger">*</span></span>
								<input id="mobile" name="RegisterForm[mobile]" tag="手机号" type="text" maxlength="11" placeholder="请输入手机号码" class="form-control validate[required,custom[mobile]]">
							</div><!-- input-group -->
						</div>
					</div><br/>
					<div class="row">
			            <div class="col-sm-6 text-black">密码 <br/>
				            <div class="input-group ">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i><span class="text-danger">*</span></span>
								<input id="password" name="RegisterForm[password]" tag="密码" onkeypress="this.type='password'" maxlength="16" placeholder="请输入密码" autocomplete="off" class="form-control validate[custom[onlyLetterNumber],required,minSize[6],maxSize[16]]">
				            </div><!-- input-group -->
			            </div>
						<div class="col-sm-6 text-black">确认密码 <br/>
							<div class="input-group ">
								<span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i><span class="text-danger">*</span></span>
								<input id="repassword" name="RegisterForm[repassword]" tag="确认密码" onkeypress="this.type='password'" maxlength="16" placeholder="请输入确认密码" autocomplete="off" class="form-control validate[custom[onlyLetterNumber],required,minSize[6],maxSize[16],equals[password]]">
							</div><!-- input-group -->
						</div>
					</div><br/>
					<div class="row">
			            <div class="col-sm-6 text-black">校验码 <br/>
				            <div class="input-group ">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i><span class="text-danger">*</span></span>
								<input id="verifycode" name="RegisterForm[verifycode]" tag="验证码" type="text" maxlength="10" placeholder="请输入校验码" class="form-control validate[required,ajax[ajaxCaptcha]]" style="display:inline-block">
					            <span class="input-group-addon" style="padding: 0">
									<?php $this->widget('CCaptcha',array(
										'showRefreshButton'=>false,
										'clickableImage'=>true,
										'imageOptions'=>array(
											'alt'=>'刷新换图',
											'title'=>'刷新换图',
											'style'=>'cursor:pointer;')
									)); ?>
								</span>
							</div><!-- input-group -->
			            </div>
						<div class="col-sm-6 text-black">短信验证码 <br/>
							<div class="input-group ">
								<span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i><span class="text-danger">*</span></span>
								<input id="code" name="RegisterForm[code]" tag="短信验证码" type="text" maxlength="10"  placeholder="请输入短信验证码" class="form-control validate[required, ajax[ajaxCode]]">
					            <span class="input-group-addon" style="padding: 0"><button type="button" disabled="disabled" id="sendCode" class="btn btn-default btn-xs">获取验证码</button></span>
							</div><!-- input-group -->
						</div>
					</div><br/>
		            <div class="clearfix">
			            <div class="pull-left">
				            <div class="ckbox ckbox-primary mt5">
					            <input name="RegisterForm[agree_term]" type="hidden" checked="checked" id="agree" value="1">
					            <!--<label for="agree">勾选此选择框，即表示您同意 <a href="#">《软件许可及服务协议》</a></label>-->
				            </div>
			            </div>
			            <div class="pull-right">
							<button id="btn-reg" type="submit" class="btn btn-success">创建账号 <i class="fa fa-angle-right ml5"></i></button>
			            </div>
		            </div>
				</form>
				<div>
	                <a href="/site/login/" class="btn btn-default btn-block">已经有账号？返回登录</a>
				</div>
			</div>
		</div>
		<!--百度统计开始-->
		<div style="display: none">
		<script type="text/javascript">
			var _hmt = _hmt || [];
			(function() {
				var hm = document.createElement("script");
				hm.src = "//hm.baidu.com/hm.js?e3b4121a80e8cd398f6f70079ae336fd";
				var s = document.getElementsByTagName("script")[0]; 
				s.parentNode.insertBefore(hm, s);
			})();
		</script>
		</div> 
		<script src="/js/jquery-1.11.1.min.js"></script>
	    <script src="/js/bootstrap.min.js"></script>
		<script src="/js/jquery.validationEngine.js"></script>
		<script src="/js/jquery.validationEngine-zh-CN.js"></script>
	    <script src="/js/pace.min.js"></script>
		<script type="text/javascript">

			$(document).ready(function() {
				
				// 对象合并函数，最简单对象合并
				function objMerger(obj1, obj2)
				{
					for(var r in obj2){
						if(typeof obj1[r] == "undefined") {
							obj1[r] = obj2[r];
						}
					}
					return obj1;
				}
				
				// 增加注册验证规则
                var newRules = {
					"ajaxCaptcha": { // 注册验证码规则
						"url": "/site/validatereg",
						"method": "post",
						"alertText": "{tag}输入不正确",
						"complete": false
					},
					// 检测用户是否存在
					"ajaxUsername": {
						"url": "/site/validatereg",
						"method": "post",
						"alertText": "{tag}已存在",
						"complete": false
					},
					"ajaxCode": {	// 手机验证码验证
						"url": "/site/validatereg",
						"method": "post",
						"alertText": "{tag}输入错误",
						"extraDataDynamic": ['#mobile'],
						"complete": false
					}
				}
				objMerger($.validationEngineLanguage.allRules, newRules);
				newRules = $.validationEngineLanguage.allRules;

				// 设置注册表单验证规则
				$('#RegisterForm').validationEngine("attach", {
					addFailureCssClassToField: 'failure',
					promptPosition: 'topRight',
					autoHidePrompt: true,
					autoHideDelay: 3000,
					maxErrorsPerField: 1,
					onAjaxFieldsComplete: onFieldsComplete,
					ajaxFormValidation: true,
					ajaxFormValidationURL: "/site/validatereg",
					onAjaxFormComplete: onComplete,
				});
				
				function onComplete(status, form, json, options) {
					if(status) {
						$("#btn-reg").attr("disabled", "disabled");
						window.location.href = "/site/index";
					}
				}
				
				var timeLimit = 0;	// 发送短信剩余事件
				function enableSendCode(){
					// 当短信没有正在发送时，可以激活发送短信按钮
					if(timeLimit == 0) {
						// 检测mobile和verifycode是否验证成功
						if($("#mobile").hasClass("success") && $("#verifycode").hasClass("success")) {
							$("#sendCode").removeAttr("disabled");	//激活短信发送按钮
						} else {
							$("#sendCode").attr("disabled", "disabled");	//激活短信发送按钮
						}
					}
				}
				
				// 是否激活短信按钮,验证码和手机号码同时填写确的时候激活短信按钮
				// 情况一：验证验证码，
				// setTimeout为了解决ajax异步问题
				function onFieldsComplete(fieldId, status) {
					if(fieldId == "verifycode") {
						setTimeout(enableSendCode,100);
					}
				}
				// 验证手机号
				// setTimeout为了解决blur事件列表执行顺序问题
				$("#mobile").blur(function() {
					// 延迟调用是为了保存最后执行
					setTimeout(enableSendCode,100);
				});
				
				// 发送短信
				$('#sendCode').click(function() {

					$.get('/site/smsCode/mobile/' + $('#mobile').val(), function(result) {
						if (result.code == "succ") {
							timeLimit = 60;
							$('#sendCode').attr('disabled', 'disabled');
							var handle = setInterval(function() {
								$('#sendCode').text('获取中('+(timeLimit)+')');
								if (timeLimit == 0) {
									clearInterval(handle);
									$('#sendCode').removeAttr('disabled');
									$('#sendCode').text('获取验证码');
								}
								timeLimit -= 1;
							}, 1000);
						} else if (result.code == "fail") {
							alert(result.message);
						}
					}, "json");
				});
				
				// 手机只能输入11位
				$("#mobile").keypress(function(event) {
					return IsNum(event);
				});
				
				// 刷新验证码函数
				function refreshVerifycode() {
					$.get('/site/captcha?refresh=1', function(result){
						$("#yw0").attr({src: result.url});
					},'json');
				}

				// 刷新验证码，进入刷新和点击刷新
				refreshVerifycode();
				$("#yw0").click(function() {
					refreshVerifycode();
				});
			});

		</script>
	</body>
</html>
