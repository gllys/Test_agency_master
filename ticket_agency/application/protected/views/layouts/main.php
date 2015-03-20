<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>智慧旅游票务平台-分销商</title>
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/style.default.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/jquery.tagsinput.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/toggles.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/bootstrap-timepicker.min.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/select2.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/colorpicker.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/dropzone.css') ?>" rel="stylesheet">
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/bootstrap-override.css') ?>" rel="stylesheet">
        <!--滚动公告样式开始-->
        <link href="<?php echo Yii::app()->versionUrl->changeUrl('/css/marquee.css') ?>" rel="stylesheet">
        <!--滚动公告样式结束-->
        <script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery-1.11.1.min.js') ?>"></script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/html5shiv.js') ?>"></script>
        <script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/respond.min.js') ?>"></script>
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
        <div class="modal fade" id="msg">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
                        <div id="advice_title" class="modal-title"></div>
                        <div id="advice_name" style="float:left;color:#999;font-size:12px;margin-left:30px"></div>
                        <div id="advice_time" style="float:left;margin-left:20px;color:#999;font-size:12px;"></div>
                    </div>
                    <div id="advice_content" class="modal-body" style="word-break:break-all;"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="close_advice">关闭</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php
        $this->beginContent('//layouts/footer');
        $this->endContent();
        ?>
    </body>
</html>
