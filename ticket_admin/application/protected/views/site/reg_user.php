<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>创建供应商账号</title>

        <link href="/css/style.default.css" rel="stylesheet">
	    <style>
		    .chk-ok {
			    color: darkgreen;
		    }
		    .chk-fail {
			    color: darkred;
		    }
	    </style>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
        <script src="/js/respond.min.js"></script>
        <link href="/css/ie.css" rel="stylesheet">
        <![endif]-->
    </head>

</head>

<body class="signin">


    <section>

        <div class="panel panel-signup">
            <div class="panel-body">
                <div class="logo text-center">
                    <img src="/img/logo.png" style="height:50px" alt="">
                </div>
                <br />
                <h4 class="text-center mb5">创建供应商账号</h4>
                <p class="text-center">请填写账号资料</p>

	            <form action="" method="post" id="RegisterForm">
		            <div class="row">
			            <div class="col-sm-6">用户名 <br/>
				            <div class="input-group ">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i><span class="asterisk" style="color: #ff0000">*</span></span>
					            <input id="reg_account" data-id="reg_account" name="RegisterForm[account]" type="text" class="form-control">
				            </div><!-- input-group -->
			            </div>
			            <div class="col-sm-6">手机号 <br/>
				            <div class="input-group ">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i><span class="asterisk" style="color: #ff0000">*</span></span>
					            <input id="reg_mobile" data-id="reg_mobile" name="RegisterForm[mobile]" type="text" class="form-control">
				            </div><!-- input-group -->
			            </div>
		            </div><!-- row -->
		            <div class="row" style="margin-bottom: 5px">
			            <div class="col-sm-6">
						<span id="chk_account">
							<?php echo ($_POST && isset($user->errors['account'][0]))
								? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $user->errors['account'][0] . '</div></div>'
								: ''; ?>
						</span>
			            </div>
			            <div class="col-sm-6">
				            <div class="input-group">
							<span id="chk_mobile">
								<?php echo ($_POST && isset($user->errors['mobile'][0]))
									? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $user->errors['mobile'][0] . '</div></div>'
									: ''; ?>
							</span>
				            </div>
			            </div>
		            </div><!-- row -->
		            <div class="row">
			            <div class="col-sm-6">密码 <br/>
				            <div class="input-group">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i><span class="asterisk" style="color: #ff0000">*</span></span>
					            <input type="text" class="hidden"/>
					            <input id="reg_password" data-id="reg_password" name="RegisterForm[password]" type="password"
					                   class="form-control">
				            </div><!-- input-group -->
			            </div>
			            <div class="col-sm-6">确认密码 <br/>
				            <div class="input-group">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i><span class="asterisk" style="color: #ff0000">*</span></span>
					            <input type="text" class="hidden"/>
					            <input id="reg_repassword" data-id="reg_repassword" name="RegisterForm[repassword]" type="password"
					                   class="form-control">
				            </div><!-- input-group -->
			            </div>
		            </div><!-- row -->
		            <div class="row" style="margin-bottom: 5px">
			            <div class="col-sm-6">
						<span id="chk_password">
						</span>
			            </div>
			            <div class="col-sm-6">
						<span id="chk_repassword">
						</span>
			            </div>
		            </div><!-- row -->
					<div class="row">
						<div class="col-sm-6"> 验证码 <br/>
							<div class="input-group">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i><span class="asterisk" style="color: #ff0000">*</span></span>
								<input id="reg_verifycode" data-id="reg_verifycode" name="RegisterForm[verifycode]" type="text" class="form-control" style="display:inline-block">
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
							</div>
						</div>
			            <div class="col-sm-6">短信验证码 <br/>
				            <div class="input-group">
					            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i><span class="asterisk" style="color: #ff0000">*</span></span>
					            <input id="reg_code" data-id="reg_code" name="RegisterForm[code]" type="text" class="form-control">
					            <span class="input-group-addon" style="padding: 0"><button type="button" disabled="disabled" id="sendCode" class="btn btn-default btn-xs">获取验证码</button></span>
				            </div><!-- input-group -->
			            </div>
		            </div><!-- row -->
		            <div class="row" style="margin-bottom: 5px">
						<div class="col-sm-6">
						<span id="chk_verifycode">
						</span>
			            </div>			            
						<div class="col-sm-6">
				            <div class="input-group">
							<span id="chk_code">
								<?php echo ($_POST && isset($user->errors['code'][0]))
									? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $user->errors['code'][0] . '</div></div>'
									: ''; ?>
							</span>
				            </div>
			            </div>
		            </div><!-- row -->
		            <br />
		            <div class="clearfix">
			            <div class="pull-left">
				            <div class="ckbox ckbox-primary mt5">
					            <input name="RegisterForm[agree_term]" type="hidden" checked="checked" id="agree" value="1">
					            <!--<label for="agree">勾选此选择框，即表示您同意 <a href="javascript:void(0)">《软件许可及服务协议》</a></label>-->
				            </div>
			            </div>
			            <div class="pull-right">
				            <button id="btn_reg" type="button" class="btn btn-success">创建账号 <i class="fa fa-angle-right ml5"></i></button>
			            </div>
		            </div>
	            </form>

            </div><!-- panel-body -->
            <div class="panel-footer">
                <a href="/site/login/" class="btn btn-default">已经有账号？返回登录</a>
            </div><!-- panel-footer -->
        </div><!-- panel -->

    </section>

    <?php if(!empty($error)){
        echo "<script>alert('".$error."');</script>";}
    ?>
    <script src="/js/jquery-1.11.1.min.js"></script>
    <script src="/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/modernizr.min.js"></script>
    <script src="/js/pace.min.js"></script>
    <!--script src="/js/retina.min.js"></script-->
    <script src="/js/jquery.cookies.js"></script>
    <script src="/js/custom.js"></script>

    <script>
	    $(function() {
		var _placeholderSupport = function() {
    	var t = document.createElement("input");
   		t.type = "text";
    	return (typeof t.placeholder !== "undefined");
		}();

		window.onload = function() {
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
		            oTextbox.onfocus = function() {
		                this.style.color = this.getAttribute("old_color");
		                if (this.value === curPlaceholder)
		                this.value = "";
            };
            oTextbox.onblur = function() {
                if (this.value === "") {
                    this.style.color = "#c0c0c0";
                    this.value = curPlaceholder;
		                }
		            }
		        }
		    }
		}	
			/**
			 * @author xuejian
			 * @desc 检查确认密码情况 
			 * @param {object} chk 触发检查确认密码的元素对象=>password or repassword
			 * @return {bool} true 校验成功 false校验失败
			 */
		    function checkRepassword(chk) {
				/* 如果是密码框和重复密码框的触发检查ok 再确认密码是否成功 */
				var chk_password = $('#chk_password');
				var chk_repassword = $('#chk_repassword');
				var password = $('#reg_password').val();
				var repassword = $('#reg_repassword').val();
				if(($(chk).attr('id') == 'chk_password' || $(chk).attr('id') == 'chk_repassword') && 
						(password.length==6) && (repassword.length==6)) {

					chk_password.removeClass('chk-fail');
					chk_password.addClass('chk-ok');
					chk_password.html('<i class="glyphicon glyphicon-ok"></i>');
					/* 确认密码是否相等 */
					if(password != repassword) {
						chk_repassword.removeClass('chk-ok');
						chk_repassword.addClass('chk-fail');
						chk_repassword.html('<i class="glyphicon glyphicon-remove"></i>您输入的密码不一致。');
						return false;
					} else {
						chk_repassword.removeClass('chk-fail');
						chk_repassword.addClass('chk-ok');
						chk_repassword.html('<i class="glyphicon glyphicon-ok"></i>');
						return true;
					}
				}
				
				return true;
			}
		    function chk_field(obj) {
			    var id = $(obj).attr('id') || $(obj).attr('data-id') || $(obj).data('id');
			    var chk= id.replace('reg', 'chk');
			    chk = $('#'+chk);
			    var val = $(obj).val();
			    if (val == $(obj).attr('placeholder') && id == 'reg_password') {
				    val = $('input[data-id="reg_password"]')[0].value;
			    }
			    if (val == $(obj).attr('placeholder')) {
				    val = '';
			    }
			    $.ajax({
				    type:'GET',
				    url:'/site/pre/chk/' + id,
				    data:{val: val},
				    async: false,
				    beforeSend:function(){
					    $(chk).html('<img alt="" src="/img/loaders/loader1.gif">');
				    },
				    success:function(result){
					    if (result == 'ok') {
							if(checkRepassword(chk)){								
								$(chk).removeClass('chk-fail');
								$(chk).addClass('chk-ok');
								$(chk).html('<i class="glyphicon glyphicon-ok"></i>');
								/**
								 * 两种情况可以启动发送短信按钮
								 * 1 验证码验证成功且手机号下为 对 号
								 * 2 手机号验证成功且验证码下为 对 号
								 */
								if ((id == 'reg_verifycode' && $("#chk_mobile").html() == '<i class="glyphicon glyphicon-ok"></i>')|| 
									(id == 'reg_mobile' && $('#chk_verifycode').html() == '<i class="glyphicon glyphicon-ok"></i>')) {
									$('#sendCode').removeAttr('disabled');
								}
							}
					    } else {
							/**
							 * 如果是验证码或者手机号码验证错误
							 * 确保发送短信按钮不可用
							 */
							if(id == 'reg_verifycode' || id == 'reg_mobile') {
								$('#sendCode').attr('disabled', 'disabled');
							}
							
						    $(chk).removeClass('chk-ok');
						    $(chk).addClass('chk-fail');
						    $(chk).html('<i class="glyphicon glyphicon-remove"></i>' + result);
					    }
				    },
				    complete:function(){
					    if ($('.glyphicon-ok').length == 6) {
					    }
				    }
			    });

		    }
		    $('#reg_account').blur(function(){
			    chk_field($(this));
		    });
		    $('#reg_mobile').blur(function(){
			    chk_field($(this));
		    });
		    $('#reg_password').blur(function(){
				if($(this).val().length != 6) {
					var id = $(this).attr('id') || $(this).attr('data-id') || $(this).data('id');
					var chk= id.replace('reg', 'chk');
					chk = $('#'+chk);
					$(chk).removeClass('chk-ok');
					$(chk).addClass('chk-fail');
					$(chk).html('<i class="glyphicon glyphicon-remove"></i>' + '密码 长度错误 (应为 6 字符串).');
				} else {				
					chk_field($(this));
				}
		    });
		    $('#reg_repassword').blur(function(){
				if($(this).val().length != 6) {
					var id = $(this).attr('id') || $(this).attr('data-id') || $(this).data('id');
					var chk= id.replace('reg', 'chk');
					chk = $('#'+chk);
					$(chk).removeClass('chk-ok');
					$(chk).addClass('chk-fail');
					$(chk).html('<i class="glyphicon glyphicon-remove"></i>' + '密码 长度错误 (应为 6 字符串).');
				} else {				
				    chk_field($(this));
				}
		    });
		    $('#reg_verifycode').blur(function(){
			    chk_field($(this));
		    });
		    $('#reg_code').blur(function(){
			    chk_field($(this));
		    });
		    $('#btn_reg').click(function(){
			    if ($('.glyphicon-ok').length < 6) {
				    $('.form-control').each(function(){
					    $(this).trigger('change');
				    });
			    }
			    if ($('.glyphicon-ok').length == 6) {
				    $('#RegisterForm').submit();
			    }
                return false;
		    });
		    $('#RegisterForm')[0].reset();

		    $('#sendCode').click(function() {
			    $.get('/site/smsCode/mobile/' + $('#reg_mobile').val(), function(result) {
				    if (result == 1) {
					    $('#sendCode').attr('disabled', 'disabled');
					    var time_limit = 60;
					    var handle = setInterval(function() {
						    $('#sendCode').text('获取验证码('+(time_limit)+')');
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
			
			/**
			 * @author xuejian
			 * @desc ajax 刷新验证码 
			 * @return {void} 
			 */
			function refreshVerifycode() {
				$.get('/site/captcha?refresh=1', function(result){
					$("#yw0").attr({src: result.url});
				},'json');
			}
			
			// 进入页面刷新验证码
			refreshVerifycode();
			
			// 单击鼠标刷新验证码
			$("#yw0").click(function() {
				refreshVerifycode();
			});
	    });
		
    </script>
<!--百度统计开始-->
<div style="display: none">
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?e3b4121a80e8cd398f6f70079ae336fd";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>

</div> 
<!--百度统计结束-->
</body>
</html>
