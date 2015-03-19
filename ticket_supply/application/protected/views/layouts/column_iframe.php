<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php Yii::app()->clientScript->registerCoreScript('jquery');?>
	<title>游族数据中心</title>
	<link rel="stylesheet" href="/css/common.css" />
	<link rel="stylesheet" href="/js/artDialog/skins/blue.css">
	<script  charset="gb2312" src="http://pic.uuzu.com/common/jquery.cookie.js" type="text/javascript"></script>
	
	<script  charset="gb2312" src="http://pic.uuzu.com/common/js/jQuery.publicBox.js" type="text/javascript"></script>
	<script  charset="gb2312" src="/js/jquery.form.js"></script>
	<script>
		var domainUrl = "<?php echo Yii::app()->params['domainUrl'];?>",
				sessionID = encodeURIComponent("<?php echo Yii::app()->session->sessionID;?>");
				chatServer = "<?php echo Yii::app()->params['chatServer']?>";
	</script>
	<style type="text/css">
		body{min-width:auto}
	</style>
</head>
<body>
<?php echo $content;?>
<script type="text/javascript">
	$(function() {
		$("table.table_s1 tbody tr").live('hover', function() {
			$("table.table_s1 tr.tr_hover").removeClass('tr_hover');
			$(this).addClass('tr_hover');
		});
	});
</script>
</body>
</html>