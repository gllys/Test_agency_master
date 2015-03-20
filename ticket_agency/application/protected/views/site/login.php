<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>智慧旅游票务平台</title>
<!--        <link href="/css/style.css" rel="stylesheet">-->
        <link rel="stylesheet" type="text/css" href="http://www.jinglvtong.com/wjd/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="http://www.jinglvtong.com/jlt/css/validationEngine.jquery.css"/>
        <script src="http://www.jinglvtong.com/wjd/js/jquery-min.js"></script>
        <script src="http://www.jinglvtong.com/jlt/js/jquery.validationEngine-zh-CN.js"></script>
        <script src="http://www.jinglvtong.com/jlt/js/jquery.validationEngine.min.js"></script>
        <script src="http://www.jinglvtong.com/wjd/js/bootstrap.js"></script>
        <script src="/js/custom.js"></script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="http://supply.test.demo.org.cn/js/html5shiv.js"></script>
        <script src="http://supply.test.demo.org.cn/js/respond.min.js"></script>
        <![endif]-->

        <style>
        /* 2015-02-09 ----  begin */
        *,*:after,*:before{box-sizing: border-box;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;-ms-box-sizing: border-box;-o-box-sizing: border-box;}
        body,h1,h2,h3,h4,dl,dt,dd,ul,ol,li,a,div,em,span,img,form,input,p,i{ padding:0; margin:0; border:none;}
        a,a:hover{text-decoration: none!important;}
        em,i{word-wrap: break-word;font-style:normal;}
        ul,li {list-style: none;}
        h1,h2,h3,h4{font-weight:100;}
        article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {display:block; margin:0; padding:0;}
        img{max-width:100%;}
        body.signin{background:#FFF;font-size:14px;font-family: "Lucida Grande", "Lucida Sans Unicode", "Microsoft Yahei", Helvetica, Arial, Verdana, sans-serif;}
        .clearfix:after { content: ""; display: block; height: 0; overflow: hidden; clear: both; visibility: hidden; }
        .pace-inactive{display:none;}
        .new-wrap{position:relative;z-index:10;padding-top:60px;}
        .new-inner{margin:0 auto;width:1110px;position:relative;}
        .new-head{width:100%;background:#196BAF;position:fixed;top:0;left:0;z-index:100;}
        .new-nav{position:relative;height:60px;}
        .new-nav h1{height:40px;background:url(/img/new-logo.png) no-repeat;width:278px;text-indent:-9999px;position:absolute;top:10px;left:0;}
        .new-nav h1 a{display:block;height:100%;}
        .nav-inner{float:right;}
        .nav-inner a{color:#FFF;font-size:16px;margin-left:30px;line-height:60px;display:inline-block;}
        .nav-inner .nav-active,.nav-inner a:hover{background: url(/img/nav-icon.png) bottom center no-repeat;text-decoration: none;}

        .J-banner{position:absolute;width:100%;height:100%!important;top:0;left:0;right:0;overflow:hidden;}
        .carousel{height:100%;position:relative;}
        .carousel-inner{width:100%;height:100%!important;position:relative;overflow:hidden;}
        .carousel-inner .item{width:100%;float:left;height:100%;background-size: cover!important;background-position: 50% 50%!important;}
        .carousel-inner .active{left:0;}
        .carousel-control.left{background: url(/img/left-btn.png) 50% 50% no-repeat;}
        .carousel-control.right{background: url(/img/right-btn.png) 50% 50% no-repeat;}
        .carousel-indicators{position:absolute;bottom:0;left:0;height:95px;width:100%!important;margin:0;background:url(/img/mav-icon-bg.png) repeat-x;text-align:center;z-index:300;padding-top:60px;}
        .carousel-indicators li{display:inline-block;height:8px;width:30px;margin:0 5px;background:#FFF;}
        .carousel-indicators .active{background:#40A8D6;display:inline-block;height:8px;width:30px;margin:0 5px;}

        .panel-signin {width:285px;background:url(/img/new-opacity.png) repeat;border-radius:4px;overflow: hidden;float:right;margin:70px 70px 0 0;}
        .nav-tabs2{height:55px;position:relative;padding:0 12px;border-bottom:1px solid #A7A5A3;z-index:200;}
        .nav-tabs2 li{float:left;width:122px;height:55px;text-align:center;}
        .nav-tabs2 .active{border-bottom:1px solid #FFF;}
        .nav-tabs2 li a{font-size:18px;color:#A7A5A3;line-height:55px;margin:0;padding:0;background: none!important;border: none!important;}
        .nav-tabs2 .active a{color:#FFF!important;}
        .panel-signin .panel-body{padding:30px 20px 20px 20px;}
        .panel-signin .panel-body .input-group{width:100%;height:35px;border-radius:1px;}
        .panel-signin .panel-body .input-group input{float:left;height:35px;width:100%;padding-left:50px;line-height:35px;font-size:13px;color:#414644;}
		#login-form >div{
			position:relative
			}
		.panel-signin .panel-body .input-group1{
			margin-bottom:5px
			}
        .panel-signin .panel-body .input-group1:before,.panel-signin .panel-body .input-group2:before{
			content:'';
			position:absolute;
			bottom:0;
			width:50px;
			height:35px;
			background:url(/img/user-bg.jpg) no-repeat;
			}
		.panel-signin .panel-body .input-group2:before{
			background:url(/img/pwd-bg.jpg) no-repeat;
			}
		#login-form input:-webkit-autofill{
			-webkit-box-shadow: 0 0 0px 1000px white inset;
			}
		
        .panel-signin .panel-body input:focus{outline:0}
        .pull-left2{position:relative;}
        .pull-left2 a{position:absolute;right:0;top:0;color:#A7A5A3;line-height:25px;}
        .pull-left2 a:hover{text-decoration: underline;}
        .ckbox-primary{float:left;}
        .ckbox-primary label{color:#A7A5A3;font-size:14px;}
        .ckbox-primary input{position:relative;top:1px;}
        .btn-success{width:100%;height:40px;border:none;background:#1BAAFF;color:#FFF;border-radius:2px;cursor: pointer;}
        .new-account{display:block;line-height:40px;text-align:center;color:#FFF;background:#474F4F;border-radius:2px;margin-top:10px;}

        .new-aside{width:64px;position:fixed;bottom:10px;right:0;background:url(/img/aside.jpg) no-repeat;z-index:1000;}
        .new-aside div{height:64px;width:64px;position:relative;}
        .new-aside div a{display:block;height:100%;width:100%;text-indent: -9999px;}
        .new-aside .wh-pup{width:170px;height:131px;position:absolute;top:-30px;left:-180px;background:url(/img/wh-bg.png) no-repeat;display:none;}
        .new-footer{background:#3E495A;color:#7B8693;text-align:center;line-height:50px;}
        .new-footer a{color:#7B8693}
        .inner-bg{width:1120px;margin:0 auto;}
        .piaotai .inner-bg{height:795px;background:url(/img/piaotai.png) no-repeat; background-position:50% 60px;}
        .ruzhu,.hezuo{background: #F1F6F7}
        .ruzhu .inner-bg{height:500px;background:url(/img/ruzhu.png) no-repeat ;background-position:50% 70px;}
        .peitao .inner-bg{height:500px;background:url(/img/peitao.png) no-repeat; background-position:50% 70px;}
        .hezuo .inner-bg{height:440px;background:url(/img/hezuo_new.png) no-repeat ;background-position:50% 70px;}
        .huoban .inner-bg{height:450px;background:url(/img/huoban.png) no-repeat; background-position:50% 70px;}
        .huoban-list{margin-left:228px;padding-top:177px;width:510px;}
        .huoban-list a{height:50px;width:150px;float:left;overflow:hidden;margin-right:18px;margin-bottom:18px;}

        /* 2015-02-09 ----  end */
    </style>

    </head>
    <body class="signin  pace-done">
    <div class="new-head clearfix">
        <div class="new-inner new-nav">
            <h1 class="new-nav-logo"><a href="#">票台-景旅通</a></h1>
            <div class="nav-inner">
                <a href="#" class="nav-active">首页</a>
                <a href="#">票台介绍</a>
                <a href="#">如何入驻</a>
                <a href="#">配套设备</a>
                <a href="#">合作资质</a>
                <a href="#">联系我们</a>
            </div>
        </div>
    </div>
    <div class="new-wrap clearfix">
        <div class="new-bigBanner">
            <div class="J-banner">
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators clearfix">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
                        <li data-target="#carousel-example-generic" data-slide-to="3" class=""></li>
                        <?php if (isset($rec) && $count>0){ for($i=4;$i<$count+4;$i++){?>
                        <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i;?>" class=""></li>
                        <?php }}?>
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <div class="item active" style="background-image:url(/img/text1.jpg)"><a href=""></a></div>
                        <div class="item" style="background-image:url(/img/text2.jpg)"><a href=""></a></div>
                        <div class="item" style="background-image:url(/img/text3.jpg)"><a href=""></a></div>
                        <div class="item" style="background-image:url(/img/text4.jpg)"><a href=""></a></div>
                        <?php $k=0;if (isset($rec) && $count>0):foreach ($rec as $r):?>
                    <div <?php if ($r['url']):?>onclick="window.open('?return_url=<?php echo $r['url']?>','_blank')" <?php endif;?> class="item" style="cursor: pointer;background:url(<?php echo $r['bimg']?>) no-repeat center / 100%; min-width:850px; min-height:542px;background-size:100%;filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(
                    src='<?php echo $r['bimg']?>',sizingMethod='scale');"></div>
                    <?php $k++;endforeach;endif;?>
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class=""></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class=""></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="new-inner">
                <div class="panel-signin">
                    <ul class="nav-tabs2">
                        <li class="active" style="margin-right:17px;"><a href="#agency">分销商登录</a></li>
                        <li><a href="<?php $r = Yii::app()->getParams();
                            echo $r['supplyUrl'] ?>/site/login">供应商登录</a></li>
                    </ul>
                    <div class="panel-body">
                        <form action="" method="post" id="login-form">
                            <div class="input-group mb20 input-group1">
                                <label style="color: #ffffff">用户名</label>
                                <input id="name" class="validate[required]" autocomplete="off" type="text" name="ULoginForm[username]">
                            </div>
                            <div class="input-group mb15 input-group2">
                                <label style="color: #ffffff">密码</label>
                                <input id="password" class="validate[required]" autocomplete="off" type="password" name="ULoginForm[password]">
                            </div>
                            <div class="clearfix">
                                <div class="pull-left2 clearfix mb20">
                                    <div class="ckbox-primary">
<!--                                        <input id="rememberMe" value="1" type="checkbox">-->
                                        <div id="show_msg" style="color:red;margin-top:4px">
                                            <?php echo ($_POST && isset($model->errors['password'][0])) ?
                                                '
        												<i class="icon-warning-sign"></i>' . $model->errors['password'][0]
                                                : '&nbsp;&nbsp;'; ?>
                                        </div>
                                        <label for="rememberMe">&nbsp;</label>
                                    </div>
                                    <a href="/site/reset/" >忘记密码？</a>
                                </div>
                                <div class="btn-list">
                                    <button type="submit" class="btn btn-success submit">登录</button>
                                    <a href="/site/register/" class="new-account">申请账号</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="piaotai">
        <div class="inner-bg"></div>
    </div>
    <div class="ruzhu">
        <div class="inner-bg"></div>
    </div>
    <div class="peitao">
        <div class="inner-bg"></div>
    </div>
    <div class="hezuo">
        <div class="inner-bg"></div>
    </div>
    <div class="huoban">
        <div class="inner-bg">
            <div class="huoban-list">
                <a href="http://www.fjlyta.com/" target="_blank"><img src="/img/p-1.jpg"></a>
                <a href="http://www.xinhuanet.com/" target="_blank"><img src="/img/p-2.jpg"></a>
                <a href="http://www.lzsta.gov.cn/" target="_blank"><img src="/img/p-3.jpg"></a>
                <a href="http://www.yctravel.gov.cn/" target="_blank"><img src="/img/p-4.jpg"></a>
                <a href="http://www.smly.gov.cn/" target="_blank"><img src="/img/p-5.jpg"></a>
                <a href="http://www.pzhsta.gov.cn/" target="_blank"><img src="/img/p-6.jpg"></a>
            </div>
        </div>
    </div>
    <div class="new-footer">©2014-2015 <a href="http://www.piaotai.com">piaotai.com</a> 版权所有 ICP证：沪ICP备15006375号-2</div>
    <div class="new-aside">
        <div class="new-aside-qq"><a target="_blank" href="http://wpa.b.qq.com/cgi/wpa.php?ln=2&uin=4008883288#main">QQ</a></div>
        <div class="new-aside-wh" style="cursor: pointer;cursor: hand;">
            <span class="wh-pup"></span>
        </div>
        <div class="new-aside-sina"><a target="_blank" href="http://weibo.com/jinglvtong/home?topnav=1&wvr=6">微博</a></div>
    </div>
    <script>
        _h = $(window).height();
        $(".new-bigBanner").height(_h-60);

        $('#show_msg').click(function(){
            $(this).hide();
        });

        $('#name').focus(function(){
            $('#show_msg').hide();
        });
        $('#password').focus(function(){
            $('#show_msg').hide();
        });

        $(function(){
            $("#login-form").validationEngine('attach', {
                promptPosition: 'centerTop',
                scroll: false,
                autoHideDelay: 1000,
                autoHidePrompt: true

            });
            $(".nav-tabs2 li").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })
            $(".new-aside-wh").hover(function(){
                $(this).find("span").fadeToggle(10)
            });
            $(".nav-inner a").click(function(){
                var _index = $(this).index();
                $(this).addClass("nav-active").siblings("a").removeClass("nav-active");
                if(_index == 0) {
                    $("html,body").animate({scrollTop: $(".new-wrap").offset().top - 60}, 450);
                }else if( _index == 1) {
                    $("html,body").animate({scrollTop: $(".piaotai").offset().top - 60}, 450);
                }else if( _index == 2) {
                    $("html,body").animate({scrollTop: $(".ruzhu").offset().top - 60}, 450);
                }else if( _index == 3) {
                    $("html,body").animate({scrollTop: $(".peitao").offset().top - 60}, 450);
                }else if( _index == 4) {
                    $("html,body").animate({scrollTop: $(".hezuo").offset().top - 60}, 450);
                }else if( _index == 5) {
                    $("html,body").animate({scrollTop: $(".huoban").offset().top - 60}, 450);
                }
                return false;
            })
        });
    </script>
    <!--百度统计开始-->
<div style="display: none">
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?4457f8eb25299a7dfde85c6ce9fe98c5";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script> 
</div> 
<!--百度统计结束-->
    </body>
</html>
