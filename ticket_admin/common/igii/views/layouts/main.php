<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Igii ADMIN</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/uniform.css" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/select2.css" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/matrix-style.css" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/matrix-media.css" />
        <link href="<?php echo $this->module->assetsUrl; ?>/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo $this->module->assetsUrl; ?>/css/jquery.gritter.css" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    </head>
    <body>

        <!--Header-part-->
        <div id="header">
            <h1><a href="/igii/">Igii ADMIN</a></h1>
        </div>
        <!--close-Header-part--> 
        <!--top-Header-menu-->
        <div id="user-nav" class="navbar navbar-inverse">
        </div>
        <!--close-top-Header-menu-->

        <!--sidebar-menu-->
        <div id="sidebar"><a href="#" class="visible-phone"><i class="icon icon-home"></i></a>
            <ul>
                <li><a href="/gii/crud/" target="_blank"><i class="icon icon-home"></i> <span>Gii</span></a> </li>
                <li id='menu_default'><a href="/igii/"><i class="icon-inbox"></i> <span>一键后台简单CRUD生成</span></a> </li>
				<li id='menu_setting'><a href="/igii/setting/"><i class="icon-inbox"></i> <span>一键配置生成</span></a> </li>
            </ul>
        </div>
        <!--sidebar-menu-->

        <!--main-container-part-->
        <div id="content">
            <?php echo $content; ?>
        </div>
        <!--end-main-container-part-->
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.min.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.ui.custom.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/bootstrap.min.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/bootstrap-colorpicker.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/bootstrap-datepicker.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.toggle.buttons.html"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/masked.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.uniform.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/select2.min.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/matrix.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/wysihtml5-0.3.0.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.peity.min.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/bootstrap-wysihtml5.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/jquery.validate.js"></script> 
        <script type="text/javascript"  charset="gb2312" src="<?php echo $this->module->assetsUrl; ?>/js/matrix.form_validation.js"></script>
        <script type="text/javascript">
            $(function() {
                $('select').select2();
                $('#menu_<?php 
                echo str_replace('/','_',$this->id) ;
                ?>').addClass('active');
            });
        </script>
    </body>
</html>