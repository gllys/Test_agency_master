<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>智慧旅游票务平台</title>
        <link href="/css/style.default.css" rel="stylesheet">
	    <style>
		    .chk-ok {
			    color: darkgreen;
		    }
		    .chk-fail {
			    color: darkred;
		    }
	    </style>
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
        <script src="/js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="signin">
        <section>

            <div class="panel panel-signin">
                <!-- Tab panes -->
                <div class="panel-body">
                    <div class="logo text-center">
                        <img src="/img/logo.png" style="height:50px" alt="">
                    </div>
                    <form action="" method="post" id="login-form">
	                    用户名 <br/>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" class="form-control" id="reset_account" name="UResetForm[account]"
                                   value="<?php echo ($_POST && isset($user->account)) ? $user->account : ''?>"
                                   autocomplete="off">
                        </div>
                        <!-- input-group -->
	                    <div class="input-group mb15">
		                    <div class="col-sm-12">
                                <span id="chk_account">
                                </span>
		                    </div>
	                    </div>
	                    <!-- input-group -->
	                    验证码 <br/>
	                    <div class="input-group">
		                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
		                    <input type="text" class="form-control" id="reset_code" name="UResetForm[code]"
		                           autocomplete="off">
		                    <span class="input-group-addon" style="padding: 0"><button type="button" id="sendCode" class="btn btn-default btn-xs">获取验证码</button></span>
	                    </div>
	                    <!-- input-group -->
	                    <div class="input-group mb15">
		                    <div class="col-sm-12">
                                <span id="chk_code" class="<?php echo ($_POST && isset($code_err)) ? 'chk-fail' : '';?>">
	                                <?php
	                                echo ($_POST && isset($code_err)) ? '<i class="glyphicon glyphicon-remove"></i>'.$code_err : '';
	                                ?>
                                </span>
		                    </div>
	                    </div>
	                    <!-- input-group -->
	                    新密码
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
	                        <input type="text" class="hidden"/>
                            <input type="password" class="form-control" id="reset_password" name="UResetForm[password]"
                                   autocomplete="off"/>
                        </div>
                        <!-- input-group -->
	                    <div class="input-group mb15">
		                    <div class="col-sm-12">
                                <span id="chk_password" class="<?php echo ($_POST && isset($password_err)) ? 'chk-fail' : '';?>">
	                                <?php
	                                echo ($_POST && isset($password_err)) ? '<i class="glyphicon glyphicon-remove"></i>'.$password_err : '';
	                                ?>
                                </span>
		                    </div>
	                    </div>
	                    <!-- input-group -->

                        确认密码 <br/>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" id="reset_password_again"
                                   autocomplete="off"/>
                        </div>
                        <!-- input-group -->
                        <div class="input-group mb15">
                            <div class="col-sm-12">
                                <span id="password_message" class="chk-fail">
                                </span>
                            </div>
                        </div>

                        <div class="clearfix">
                            <div class="pull-right btn-list">
                                <button type="button" id="reset_pwd" class="btn btn-success">重设密码 <i
                                        class="fa fa-angle-right ml5"></i></button>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- panel-body -->

                <div class="panel-footer">
	                <a href="/site/login/" class="btn btn-default btn-block">返回登录</a>
                </div>
                <!-- panel-footer -->

            </div>
            <!-- panel -->

        </section>


        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/jquery-migrate-1.2.1.min.js"></script>
        <script src="/js/pace.min.js"></script>
        <!--script src="/js/retina.min.js"></script-->
        <script>

            jQuery(document).ready(function() {
                $('#reset_pwd').click(function(){
                    //判断密码重复是否正确
                    var once_pwd = $('#reset_password').val();
                    var twice_pwd = $('#reset_password_again').val();
                    if(once_pwd != twice_pwd){
                        var html = '<i class="glyphicon glyphicon-remove"></i> 两次密码输入不一致';
                        $('#password_message').html(html);
                        return false;
                    }
                    $('#login-form').submit();
                })
            });

        </script>

        <script>
	        $(function() {
		        $('#sendCode').click(function() {
			        var account = $('#reset_account').val();
			        var password = $('#reset_password').val();
					if (account == '') {
				        return false;
			        }
			        $.ajax({
				        type: 'POST',
				        url: '/site/reset/act/code/',
				        data: {account: account, password: password},
				        dataType: 'json',
				        beforeSend: function () {
					        $('#chk_code').html('<img alt="" src="/img/loaders/loader1.gif">');
				        },
				        success: function (result) {
					        if (result.code == 1) {
						        $('#chk_account').removeClass('chk-fail');
						        $('#chk_account').addClass('chk-ok');
						        $('#chk_account').html('<i class="glyphicon glyphicon-ok"></i>');
						        $('#chk_code').removeClass('chk-fail');
						        $('#chk_code').addClass('chk-ok');
						        $('#chk_password').removeClass('chk-fail');
						        $('#chk_password').addClass('chk-ok');
						        $('#chk_code').html('<i class="glyphicon glyphicon-ok"></i>' + result.msg);
						        $('#sendCode').attr('disabled', 'disabled');
						        var time_limit = 60;
						        var handle = setInterval(function() {
							        $('#sendCode').text('获取验证码(' + (time_limit) + ')');
							        if (time_limit == 0) {
								        clearInterval(handle);
								        $('#sendCode').removeAttr('disabled');
								        $('#sendCode').text('获取验证码');
							        }
							        time_limit -= 1;
						        }, 1000);
					        } else if (result.code == 0) {
						        $('#chk_account').html('');
						        $('#chk_password').html('');
						        $('#chk_code').removeClass('chk-ok');
						        $('#chk_code').addClass('chk-fail');
						        $('#chk_code').html('<i class="glyphicon glyphicon-remove"></i>' + result.msg);
					        } else if (result.code == -1) {
						        $('#chk_code').html('');
						        $('#chk_password').html('');
						        $('#chk_account').removeClass('chk-ok');
						        $('#chk_account').addClass('chk-fail');
						        $('#chk_account').html('<i class="glyphicon glyphicon-remove"></i>' + result.msg);

					        }else if (result.code == -2) {
						        $('#chk_code').html('');
						        $('#chk_account').html('');
						        $('#chk_password').removeClass('chk-ok');
						        $('#chk_password').addClass('chk-fail');
						        $('#chk_password').html('<i class="glyphicon glyphicon-remove"></i>' + result.msg);

					        }
				        }
			        });
			        return false;
		        });
	        });
        </script>
    </body>
</html>
