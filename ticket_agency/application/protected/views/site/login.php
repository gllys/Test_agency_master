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
    </head>
    <body class="signin">
        <section>

            <div class="panel panel-signin">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified">
                    <li class="active"><a href="#agency" data-toggle="tab"><strong>分销商登录</strong></a></li>
                    <li><a href="<?php $r = Yii::app()->getParams();
echo $r['supplyUrl'] ?>/site/login"><strong>供应商登录</strong></a></li>
                </ul>
                <!-- Tab panes -->
                <div class="panel-body">
                    <div class="logo text-center">
                        <img src="/img/logo.png" style="height:50px" alt="">
                    </div>
                    <div class="mb30">
<?php echo ($_POST && isset($model->errors['password'][0])) ? '<div id="show_msg"><div class="alert alert-error" style="color:red"><button type="button" class="close" data-dismiss="alert">×</button><i class="icon-warning-sign"></i>' . $model->errors['password'][0] . '</div></div>' : ''; ?>
                    </div>

                    <form action="" method="post" id="login-form">
	                    用户名 <br/>
                        <div class="input-group mb15">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" class="form-control" name="ULoginForm[username]"
                                   autocomplete="on">
                        </div>
                        <!-- input-group -->
	                    密码 <br/>
                        <div class="input-group mb15">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" name="ULoginForm[password]" autocomplete="off"
                            />
                        </div>
                        <!-- input-group -->

                        <div class="clearfix">
                            <div class="pull-left">
                                <div class="ckbox ckbox-primary mt10 hide">
                                    <input type="checkbox" id="rememberMe" value="1">
                                    <label for="rememberMe">记住我的登录</label>
                                </div>
                            </div>
                            <div class="pull-right btn-list">
	                            <a href="/site/reset/" class="btn btn-primary btn-link btn-xs">忘记密码？</a>
                                <button type="submit" class="btn btn-success">登录 <i
                                        class="fa fa-angle-right ml5"></i></button>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- panel-body -->

                <div class="panel-footer">
                    <div class="tab-content" style="padding: 0">
                        <div class="tab-pane active" id="agency">
                            <a href="/site/register/" class="btn btn-primary btn-block">还没有账号？现在申请创建分销商账号</a>
                        </div>
                        <!-- agency -->
                        <div class="tab-pane" id="supplier">
                        </div>
                        <!-- supplier -->
                    </div>

                    <div style="padding-top: 30px;text-align: center">&copy;2014 上海汇联皆景信息科技有限公司</div>
                </div>
                <!-- panel-footer -->

            </div>
            <!-- panel -->

        </section>


        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/jquery-migrate-1.2.1.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/modernizr.min.js"></script>
        <script src="/js/pace.min.js"></script>
        <!--script src="/js/retina.min.js"></script-->
        <script src="/js/jquery.cookies.js"></script>

        <script src="/js/custom.js"></script>

    </body>
</html>
