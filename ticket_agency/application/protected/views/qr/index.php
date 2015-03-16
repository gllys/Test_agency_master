<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/6/14
 * Time: 11:52 AM
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>智慧旅游票务平台</title>
	<style>
		#code {
			height: 100%;
			text-align: center;
			font-size: 0;
		}

		#code:after, #code span {
			display: inline-block;
			*display: inline;
			*zoom: 1;
			width: 0;
			height: 100%;
			vertical-align: middle;
		}

		#code:after {
			content: '';
		}

		#code p {
			width: 70%;
			display: inline-block;
			*display: inline;
			*zoom: 1;
			vertical-align: middle;
			font-size: 16px;
		}

		img {
			width: 70%;
		}
	</style>
</head>
<body>
<div id="code">
	<p><img src="<?php
		$this->widget('application.extensions.qrcode.QRCodeGenerator', array(
			'data' => $code,
			'subfolderVar' => false,
			'displayImage' => false, // default to true, if set to false display a URL path
			'errorCorrectionLevel' => 'H', // available parameter is L,M,Q,H
			'matrixPointSize' => 10, // 1 to 10 only
		));?>" alt=""/></p>
	<!--[if lt IE 8]><span></span><![endif]-->
</div>

</body>
</html>
