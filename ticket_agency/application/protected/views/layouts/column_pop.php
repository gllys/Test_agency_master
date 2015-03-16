<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>产品运营后台</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
                <title>激活码管理系统</title>
                <link rel="stylesheet" href="/css/common.css" />
                <link rel="stylesheet" href="/js/artDialog/skins/blue.css">
                        <link rel="stylesheet" href="/js/colorbox/colorbox.css" />


                        <script type="text/javascript"  charset="gb2312" src="http://pic.uuzu.com/common/jquery.cookie.js"></script>
                        <script type="text/javascript"  charset="gb2312" src="http://pic.uuzu.com/common/js/jQuery.publicBox.js"></script>
                        <script type="text/javascript"  charset="gb2312" src="/js/artDialog/jquery.artDialog.source.js"></script>
                        <script type="text/javascript"  charset="gb2312" src="/js/artDialog/plugins/iframeTools.js"></script>
                        <script type="text/javascript"  charset="gb2312" src="http://pic.uuzu.com/common/socket.io/dist/socket.io.js"></script>
                        <script type="text/javascript"  charset="gb2312" src="/js/colorbox/jquery.colorbox-min.js"></script>

                        <script type="text/javascript"  charset="gb2312" src="/js/My97DatePicker/WdatePicker.js"></script>
                        <script type="text/javascript"  charset="gb2312" src="/js/jquery.form.js"></script>

                        <script type="text/javascript">
                                var domainUrl = "<?php echo Yii::app()->params['domainUrl']; ?>",
                                sessionID = encodeURIComponent("<?php echo Yii::app()->session->sessionID; ?>");
                        </script>
        </head>
        <body>
                <?php echo $content; ?>
        </body>

