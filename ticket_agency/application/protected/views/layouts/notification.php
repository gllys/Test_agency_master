<?php
$sa = array(
    'fail' => 'remove',
    'success' => 'ok',
    'notification' => 'info'
);
$sc = array(
    'fail' => 'danger',
    'success' => 'success',
    'notification' => 'info'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>操作提示</title>

    <meta http-equiv="refresh" content="3; url=<?php echo $next_url?>" />

	<link href="../../../css/style.default.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->
</head>

<body>

<section>
	<div class="notfoundpanel">
		<h1><i class="glyphicon glyphicon-<?php echo isset($sa[$type]) ? $sa[$type] : 'info'?>-sign text-<?php echo isset($sc[$type]) ? $sc[$type] : 'info'?>"></i></h1>
		<h3><?php echo $message?></h3>
		<p><a href="<?php echo $next_url?>"><?php echo $next_title?></a></p>
	</div><!-- notfoundpanel -->
</section>


<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery-migrate-1.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/modernizr.min.js"></script>
<script src="js/pace.min.js"></script>
<script src="js/retina.min.js"></script>
<script src="js/jquery.cookies.js"></script>

<script src="js/custom.js"></script>

</body>
</html>
