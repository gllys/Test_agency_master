<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>创建分销商账号</title>

<link href="/css/style.default.css" rel="stylesheet">
<style>
	.chk-ok {
		color: darkgreen;
	}

	.chk-fail {
		color: darkred;
	}

	.placeholder {
		color: #c0c0c0;
	}
</style>
<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/pace.min.js"></script>
<script>
	/*! http://mths.be/placeholder v2.0.8 by @mathias */
	;
	(function (window, document, $) {

		// Opera Mini v7 doesn’t support placeholder although its DOM seems to indicate so
		var isOperaMini = Object.prototype.toString.call(window.operamini) == '[object OperaMini]';
		var isInputSupported = 'placeholder' in document.createElement('input') && !isOperaMini;
		var isTextareaSupported = 'placeholder' in document.createElement('textarea') && !isOperaMini;
		var prototype = $.fn;
		var valHooks = $.valHooks;
		var propHooks = $.propHooks;
		var hooks;
		var placeholder;

		if (isInputSupported && isTextareaSupported) {

			placeholder = prototype.placeholder = function () {
				return this;
			};

			placeholder.input = placeholder.textarea = true;

		} else {

			placeholder = prototype.placeholder = function () {
				var $this = this;
				$this
					.filter((isInputSupported ? 'textarea' : ':input') + '[placeholder]')
					.not('.placeholder')
					.bind({
						'focus.placeholder': clearPlaceholder,
						'blur.placeholder': setPlaceholder
					})
					.data('placeholder-enabled', true)
					.trigger('blur.placeholder');
				return $this;
			};

			placeholder.input = isInputSupported;
			placeholder.textarea = isTextareaSupported;

			hooks = {
				'get': function (element) {
					var $element = $(element);

					var $passwordInput = $element.data('placeholder-password');
					if ($passwordInput) {
						return $passwordInput[0].value;
					}

					return $element.data('placeholder-enabled') && $element.hasClass('placeholder') ? '' : element.value;
				},
				'set': function (element, value) {
					var $element = $(element);

					var $passwordInput = $element.data('placeholder-password');
					if ($passwordInput) {
						return $passwordInput[0].value = value;
					}

					if (!$element.data('placeholder-enabled')) {
						return element.value = value;
					}
					if (value == '') {
						element.value = value;
						// Issue #56: Setting the placeholder causes problems if the element continues to have focus.
						if (element != safeActiveElement()) {
							// We can't use `triggerHandler` here because of dummy text/password inputs :(
							setPlaceholder.call(element);
						}
					} else if ($element.hasClass('placeholder')) {
						clearPlaceholder.call(element, true, value) || (element.value = value);
					} else {
						element.value = value;
					}
					// `set` can not return `undefined`; see http://jsapi.info/jquery/1.7.1/val#L2363
					return $element;
				}
			};

			if (!isInputSupported) {
				valHooks.input = hooks;
				propHooks.value = hooks;
			}
			if (!isTextareaSupported) {
				valHooks.textarea = hooks;
				propHooks.value = hooks;
			}

			$(function () {
				// Look for forms
				$(document).delegate('form', 'submit.placeholder', function () {
					// Clear the placeholder values so they don't get submitted
					var $inputs = $('.placeholder', this).each(clearPlaceholder);
					setTimeout(function () {
						$inputs.each(setPlaceholder);
					}, 10);
				});
			});

			// Clear placeholder values upon page reload
			$(window).bind('beforeunload.placeholder', function () {
				$('.placeholder').each(function () {
					this.value = '';
				});
			});

		}

		function args(elem) {
			// Return an object of element attributes
			var newAttrs = {};
			var rinlinejQuery = /^jQuery\d+$/;
			$.each(elem.attributes, function (i, attr) {
				if (attr.specified && !rinlinejQuery.test(attr.name)) {
					newAttrs[attr.name] = attr.value;
				}
			});
			return newAttrs;
		}

		function clearPlaceholder(event, value) {
			var input = this;
			var $input = $(input);
			if (input.value == $input.attr('placeholder') && $input.hasClass('placeholder')) {
				if ($input.data('placeholder-password')) {
					$input = $input.hide().next().show().attr('id', $input.removeAttr('id').data('placeholder-id'));
					// If `clearPlaceholder` was called from `$.valHooks.input.set`
					if (event === true) {
						return $input[0].value = value;
					}
					$input.focus();
				} else {
					input.value = '';
					$input.removeClass('placeholder');
					input == safeActiveElement() && input.select();
				}
			}
		}

		function setPlaceholder() {
			var $replacement;
			var input = this;
			var $input = $(input);
			var id = this.id;
			if (input.value == '') {
				if (input.type == 'password') {
					if (!$input.data('placeholder-textinput')) {
						try {
							$replacement = $input.clone().attr({'type': 'text'});
						} catch (e) {
							$replacement = $('<input>').attr($.extend(args(this), {'type': 'text'}));
						}
						$replacement
							.removeAttr('name')
							.data({
								'placeholder-password': $input,
								'placeholder-id': id
							})
							.bind('focus.placeholder', clearPlaceholder);
						$input
							.data({
								'placeholder-textinput': $replacement,
								'placeholder-id': id
							})
							.before($replacement);
					}
					$input = $input.removeAttr('id').hide().prev().attr('id', id).show();
					// Note: `$input[0] != input` now!
				}
				$input.addClass('placeholder');
				$input[0].value = $input.attr('placeholder');
			} else {
				$input.removeClass('placeholder');
			}
		}

		function safeActiveElement() {
			// Avoid IE9 `document.activeElement` of death
			// https://github.com/mathiasbynens/jquery-placeholder/pull/99
			try {
				return document.activeElement;
			} catch (exception) {
			}
		}

	}(this, document, jQuery));
</script>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="/js/html5shiv.js"></script>
<script src="/js/respond.min.js"></script>
<link href="/css/ie.css" rel="stylesheet">
<![endif]-->
</head>

<body class="signin">


<section>

	<div class="panel panel-signup">
		<div class="panel-body">
			<div class="logo text-center">
				<img src="/img/logo.png" style="height:50px" alt="">
			</div>
			<br/>
			<h4 class="text-center mb5">创建分销商账号</h4>

			<p class="text-center">请填写账号资料</p>

			<form action="" method="post" id="RegisterForm">
				<div class="row">
					<div class="col-sm-6">用户名 <br/>
						<div class="input-group ">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i><span
									class="asterisk" style="color: #ff0000">*</span></span>
							<input id="reg_account" data-id="reg_account" name="RegisterForm[account]" type="text"
							       class="form-control validate[required]">
						</div>
						<!-- input-group -->
					</div>
					<div class="col-sm-6">手机号 <br/>
						<div class="input-group ">
							<span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i><span
									class="asterisk" style="color: #ff0000">*</span></span>
							<input id="reg_mobile" data-id="reg_mobile" name="RegisterForm[mobile]" type="text"
							       class="form-control validate[required,custom[mobile]]">
						</div>
						<!-- input-group -->
					</div>
				</div>
				<!-- row -->
				<div class="row" style="margin-bottom: 5px">
					<div class="col-sm-6">
						<span id="chk_account">
                            <?php
                            echo ($_POST && isset($user->errors['account'][0])) ? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $user->errors['account'][0] . '</div></div>' : '';
                            ?>
                        </span>
					</div>
					<div class="col-sm-6">
						<div class="input-group">
							<span id="chk_mobile">
                                <?php
                                echo ($_POST && isset($user->errors['mobile'][0])) ? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $user->errors['mobile'][0] . '</div></div>' : '';
                                ?>
                            </span>
						</div>
					</div>
				</div>
				<!-- row -->
				<div class="row">
					<div class="col-sm-6">密码 <br/>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i><span
									class="asterisk" style="color: #ff0000">*</span></span>
							<input type="text" class="hidden"/>
							<input id="reg_password" data-id="reg_password" name="RegisterForm[password]"
							       type="password"
							       class="form-control validate[required]">
						</div>
						<!-- input-group -->
					</div>
					<div class="col-sm-6">短信验证码 <br/>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i><span
									class="asterisk" style="color: #ff0000">*</span></span>
							<input id="reg_code" data-id="reg_code" name="RegisterForm[code]" type="text"
							       class="form-control">
							<span class="input-group-addon" style="padding: 0">
								<button type="button" disabled="disabled"
								        id="sendCode"
								        class="btn btn-default btn-xs">
									获取验证码
								</button>
							</span>
						</div>
						<!-- input-group -->
					</div>
				</div>
				<!-- row -->
				<div class="row" style="margin-bottom: 5px">
					<div class="col-sm-6">
                                <span id="chk_password">
                                </span>
					</div>
					<div class="col-sm-6">
						<div class="input-group">
                                    <span id="chk_code">
                                        <?php
                                        echo ($_POST && isset($user->errors['code'][0])) ? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $user->errors['code'][0] . '</div></div>' : '';
                                        ?>
                                    </span>
						</div>
					</div>
				</div>
				<!-- row -->
				<br/>

				<div class="clearfix">
					<div class="pull-left">
						<div class="ckbox ckbox-primary mt5">
							<input name="RegisterForm[agree_term]" type="hidden" checked="checked" id="agree" value="1">
							<!--<label for="agree">勾选此选择框，即表示您同意 <a href="#">《软件许可及服务协议》</a></label>-->
						</div>
					</div>
					<div class="pull-right">
						<button id="btn_reg" type="button" class="btn btn-success">创建账号 <i
								class="fa fa-angle-right ml5"></i></button>
					</div>
				</div>
			</form>

		</div>
		<!-- panel-body -->
		<div class="panel-footer">
			<a href="/site/login/" class="btn btn-primary btn-block">已经有账号？返回登录</a>
		</div>
		<!-- panel-footer -->
	</div>
	<!-- panel -->

</section>


<script>
	$(function () {
		var _placeholderSupport = function () {
			var t = document.createElement("input");
			t.type = "text";
			return (typeof t.placeholder !== "undefined");
		}();

		window.onload = function () {
			var arrInputs = document.getElementsByTagName("input");
			for (var i = 0; i < arrInputs.length; i++) {
				var curInput = arrInputs[i];
				if (!curInput.type || curInput.type == "" || curInput.type == "text" || curInput.type == "password")
					HandlePlaceholder(curInput);
			}
		};

		function HandlePlaceholder(oTextbox) {
			if (!_placeholderSupport) {
				var curPlaceholder = oTextbox.getAttribute("placeholder");
				if (curPlaceholder && curPlaceholder.length > 0) {
					oTextbox.value = curPlaceholder;
					oTextbox.setAttribute("old_color", oTextbox.style.color);
					oTextbox.style.color = "#c0c0c0";
					oTextbox.onfocus = function () {
						this.style.color = this.getAttribute("old_color");
						if (this.value === curPlaceholder)
							this.value = "";
					};
					oTextbox.onblur = function () {
						if (this.value === "") {
							this.style.color = "#c0c0c0";
							this.value = curPlaceholder;
						}
					}
				}
			}
		}

		$('input').placeholder();
		function chk_field(obj) {
			var id = $(obj).attr('id') || $(obj).attr('data-id') || $(obj).data('id');
			var chk = id.replace('reg', 'chk');
			chk = $('#' + chk);
			var val = $(obj).val();
			if (val == $(obj).attr('placeholder') && id == 'reg_password') {
				val = $('input[data-id="reg_password"]')[0].value;
			}
			if (val == $(obj).attr('placeholder')) {
				val = '';
			}
			$.ajax({
				type: 'GET',
				url: '/site/pre/chk/' + id,
				data: {val: val},
				async: false,
				beforeSend: function () {
					$(chk).html('<img alt="" src="/img/loaders/loader1.gif">');
				},
				success: function (result) {
					if (result == 'ok') {
						$(chk).removeClass('chk-fail');
						$(chk).addClass('chk-ok');
						$(chk).html('<i class="glyphicon glyphicon-ok"></i>');
						if (id == 'reg_mobile') {
							$('#sendCode').removeAttr('disabled');
						}
					} else {
						$(chk).removeClass('chk-ok');
						$(chk).addClass('chk-fail');
						$(chk).html('<i class="glyphicon glyphicon-remove"></i>' + result);
					}
				},
				complete: function () {
					if ($('.glyphicon-ok').length == 4) {
					}
				}
			});

		}

		$('#reg_account').change(function () {
			chk_field($(this));
		});
		$('#reg_mobile').change(function () {
			chk_field($(this));
		});
		$('#reg_password').change(function () {
			chk_field($(this));
		});
		$('#reg_code').change(function () {
			chk_field($(this));
		});
		$('#btn_reg').click(function () {
			if ($('.glyphicon-ok').length < 4) {
				$('.form-control').each(function () {
					$(this).trigger('change');
				});
			}
			if ($('.glyphicon-ok').length == 4) {
				$('#RegisterForm').submit();
			}
			return false;
		});
		$('#RegisterForm')[0].reset();

		$('#sendCode').click(function () {
			$.get('/site/smsCode/mobile/' + $('#reg_mobile').val(), function (result) {
				if (result == 1) {
					$('#sendCode').attr('disabled', 'disabled');
					var time_limit = 60;
					var handle = setInterval(function () {
						$('#sendCode').text('获取验证码(' + (time_limit) + ')');
						if (time_limit == 0) {
							clearInterval(handle);
							$('#sendCode').removeAttr('disabled');
							$('#sendCode').text('获取验证码');
						}
						time_limit -= 1;
					}, 1000);
				} else if (result > 1) {
					alert(result);
				}
			});
		});
	});
</script>

</body>
</html>
