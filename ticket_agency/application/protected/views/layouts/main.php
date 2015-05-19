<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>智慧旅游票务平台-分销商</title>
        <link href="/css/style.default.css" rel="stylesheet">
        <link href="/css/yiipager.css" rel="stylesheet">
        <link href="/css/jquery.tagsinput.css" rel="stylesheet">
        <link href="/css/toggles.css" rel="stylesheet">
        <link href="/css/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="/css/select2.css" rel="stylesheet">
        <link href="/css/colorpicker.css" rel="stylesheet">
        <link href="/css/dropzone.css" rel="stylesheet">
        <link href="/css/yiipager.css" rel="stylesheet">
        <link href="/css/bootstrap-override.css" rel="stylesheet">
        <script src="/js/jquery-1.11.1.min.js"></script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
        <script src="/js/respond.min.js"></script>
        <link href="/css/ie.css" rel="stylesheet">
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
                    <div class="pageheader">
                        <div class="media">
                            <div class="pageicon pull-left">
                                <i class="fa fa-home"></i>
                            </div>
                            <?php
                            if (!empty($this->breadcrumbs)):
                                ?>
                                <div class="media-body">
                                    <ul class="breadcrumb">
                                        <li><a href="/dashboard/"><i class="glyphicon glyphicon-home"></i></a></li>
                                        <li><?php echo $this->breadcrumbs[0] ?></li>
                                        <li><?php echo $this->breadcrumbs[1] ?></li>
                                    </ul> 
                                </div>
                                <?php
                            endif;
                            ?>
                        </div>
                        <!-- media -->
                    </div>
                    <!-- pageheader -->

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
