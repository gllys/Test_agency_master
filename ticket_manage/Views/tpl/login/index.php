<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>智慧旅游分销-供应管理平台</title>
        <link href="Views/css/application.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="Views/css/base.css" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="Views/js/vendor/html5shiv.js" type="text/javascript"></script>
        <script src="Views/js/vendor/excanvas.js" type="text/javascript"></script>
        <![endif]-->
        <!--[if lt IE 7 ]>
        <link href="Views/css/ie.css" rel="stylesheet" fuck="ie">
        <![endif]-->
        <script src="Views/js/application.js" type="text/javascript"></script>
        <style id="styl_i">body{background:url('Views/images/bg2.jpg');filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale')";-moz-background-size:100% 100%;background-size:100% 100%;}</style>
    </head>
    <body>
        <div class="navbar navbar-top navbar-inverse">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="#">智慧旅游分销-供应管理平台</a>
                    <ul class="nav pull-right">
                        <li class="toggle-primary-sidebar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-primary"><a><i class="icon-th-list"></i></a></li>
                        <li class="collapsed hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-top"><a><i class="icon-align-justify"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="span4 offset4">
                <div class="padded">
                    <div class="login box" style="margin-top: 80px;">
                        <div class="box-header">
                            <span class="title">帐号登录</span>
                        </div>
                        <div class="box-content padded">
                            <form class="separate-sections" action="index.php?c=login&a=authVerify" method="post" id="login-form">
                                <div class="input-prepend">
                                    <span class="add-on" href="#">
                                        <i class="icon-user"></i>
                                    </span>
                                    <input type="text" name="account" placeholder="用户名">
                                </div>
                                <div class="input-prepend">
                                    <span class="add-on" href="#">
                                        <i class="icon-key"></i>
                                    </span>
                                    <input type="password" name="password" placeholder="密码">
                                </div>
                                <div>
                                    <a class="btn btn-blue btn-block" id="btn-login" >
                                        登录 <i class="icon-signin"></i>
                                    </a>
                                </div>
                            </form>
                            <div>
                                <a href="http://www.acelinked.com" target="_blank">
                                    (C) 上海汇联皆景信息科技有限公司
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
        <script src="Views/js/login/index.js" type="text/javascript" charset="utf-8"></script>
    </body>
</html>