<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title>智慧旅游票务平台</title>

        <link href="/css/style.default.css" rel="stylesheet">
        
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
        <script src="/js/respond.min.js"></script>
        <![endif]-->
        
        <style>
        .carousel-control.left,.carousel-control.right{background-image:none;}
        </style>
    </head>

    <body>
        
        <header style="height:60px;">         
            <div class="headerwrapper">
                <div class="header-left">
                    <a href="/" class="logo">
                        	汇联皆景供应商系统
                    </a>
                    <div class="pull-right">
                        <a href="javascript:void(0)" class="menu-collapse">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                </div><!-- header-left -->
                
                <div class="header-right">
                	<div class="pull-left" style="width:950px;">
                    	<form action="" method="post" id="login-form">
                         <div class="clearfix" style="padding-top:15px; padding-left:10px;">
                            <div class="input-group pull-left" style="width:260px">
                                <span class="input-style pull-left"><i class="glyphicon glyphicon-user"></i></span>
                                <input type="text"  class="input-control" name="ULoginForm[username]" placeholder="用户名" autocomplete="on" style="width:160px;">
                            </div>
                            <div class="input-group pull-left" style="margin-left:-50px;width:260px">
                                <span class="input-style pull-left"><i class="glyphicon glyphicon-lock"></i></span>
                                <input type="password" autocomplete="off" name="ULoginForm[password]"  class="input-control" placeholder="密码" style="width:160px;">
                            </div>
                            <div class="pull-left" style="margin-left:-50px;">
                                <button class="btn btn-success pull-left" type="submit" style="margin-top:-3px;">登录 <i class="fa fa-angle-right ml5"></i></button>
                                <a class="btn btn-white pull-left"  style="height:37px; margin:-3px 10px 0 10px;" href="/site/reset/">忘记密码 <i class="fa fa-angle-right ml5"></i></a>
                                <a class="btn btn-primary pull-left" href="/site/register/">还没有账号？现在申请创建供应商账号</a>
                            </div>
                         </div> 
                        </form>
					</div>
                                    
                </div><!-- header-right -->
            </div><!-- headerwrapper -->
        </header>
        
        <section>
            <div style="position:absolute; top:60px; bottom:0; right:0; left:0;">
                <iframe width="100%" height="100%" frameborder="0" src="<?php echo $_GET['return_url']?>"></iframe>
            </div>
        </section>
        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/jquery-migrate-1.2.1.min.js"></script>
        <script src="/js/jquery-ui-1.10.3.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/modernizr.min.js"></script>
        <script src="/js/pace.min.js"></script>
        <script src="/js/retina.min.js"></script>
        <script src="/js/jquery.cookies.js"></script>
        
        
        <?php if (($_POST && isset($model->errors['password'][0]))):?>
        <div id="errorWin" class="modal fade modal-tips" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title">登录失败</h4>
                              <p>
                              <?php echo  '<div id="show_msg"><div class="alert alert-error" style="color:red"><i class="icon-warning-sign"></i>' . $model->errors['password'][0] . '</div></div>'; ?>
                              </p>
                          </div>
                      </div>
                    </div>
                </div> 
       <script type="text/javascript">
		$('#errorWin').modal('show');
       </script>
       <?php endif;?>
    </body>
    <style>
        .input-style {
			width:38px;
			height:31px;
			padding: 6px 12px;
			font-size: 13px;
			font-weight: 400;
			line-height: 1;
			color: #555;
			text-align: center;			
			background-color: #f7f7f7;
			border: 1px solid #ccc;
			border-right:none;
		}
		.input-control{
			width:180px;
			height:31px;
			outline:medium;
			padding-left:5px;
		}
        </style>
</html>
