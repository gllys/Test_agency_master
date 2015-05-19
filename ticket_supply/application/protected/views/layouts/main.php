<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>智慧旅游票务平台-供应商</title>
       
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/style.default.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/toggles.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/bootstrap-timepicker.min.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/select2.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/colorpicker.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/dropzone.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/bootstrap-override.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/bootstrap-wysihtml5.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/marquee.css') ?>" rel="stylesheet">
        <script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery-1.11.1.min.js') ?>"></script>


        
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
        <script src="/js/respond.min.js"></script>
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/ie.css') ?>" rel="stylesheet">
        <![endif]-->
    </head>

    <body>
        <?php
        //$data = array('unread_msg' => Message::model()->getUnreadMessages());
        //$this->beginContent('//layouts/header', $data);
        $this->beginContent('//layouts/header');
        $this->endContent();
        ?>
        <section>
            <div class="mainwrapper">
                <?php
                //$this->beginContent('//layouts/left', $data);
                $this->beginContent('//layouts/left');
                $this->endContent();
                ?>
                <div class="mainpanel">
                    
                    <?php echo $content; ?>

                </div>
                <!-- mainpanel -->
            </div>
            <!-- mainwrapper -->
        </section>
        <?php
        $this->beginContent('//layouts/footer');
        $this->endContent();
        ?>
    </body>
</html>
