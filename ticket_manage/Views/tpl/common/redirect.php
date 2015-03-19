<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Message</title>
<script type="text/javascript">
	var Url = "<?php echo $url;?>";
	function goToPage() {
		if (Url == '') history.back();
		else if (Url == '-1') window.close();
		else location.href = Url;
	}

	var MaxTime = <?php echo $time;?>;
	function autoTimeShow() {
		--MaxTime;
		if(MaxTime < 0) {
			goToPage();
			return;
		}
		var obj = document.getElementById('showTime');
		obj.innerHTML = MaxTime;
		setTimeout(autoTimeShow,1000); 
	}
</script> 
<style type="text/css">
body{
    width:100%;
	text-align: center;
	padding-top: 100px;
	font-size:13px;
}
#msgbox{
	border: 1px solid #cccccc;
	width: 500px;
	margin:0 auto;
}
#msgbox-title{
	background: #eeeeee;
	border-bottom: 1px solid #cccccc;
	padding-left: 4px;
	height: 25px;
	line-height: 25px;
	text-align: center;
	font-weight: bold;
	color: #ff0000;
	text-align: left;
}
#content{
 border-left: 4px solid #eeeeee;
 border-right: 4px solid #eeeeee;
 border-bottom: 4px solid #eeeeee;
 padding-top: 30px;
}
#msgbox-text{
  
}
#msgbox-button{
	margin-top: 20px;
	margin-bottom: 15px;
}
#but-def{
	text-decoration:none;
}
a:link {
color: #0000FF;
text-decoration: none;
}
a:visited {
text-decoration: none;
color: #003399;
}
a:hover {
text-decoration: underline;
color: #0066FF;
}
a:active {
text-decoration: none;
color: #0066FF;
}
</style>
</head>
<body onload="autoTimeShow()">
	<div id="msgbox" align="center">
		<div id="msgbox-title"><span style="float:left;">::Message::</span><span style="float:right; color:#0000FF;"><span id="showTime"><?php echo $sec;?></span>&nbsp;</span></div>
		<div id="content">
			<div id="msgbox-text"><?php echo $message;?></div>
			<div id="msgbox-button"><a href="javascript:void(goToPage());" id="but-def">如果长时间没有自动跳转请点这里</a></div>
		</div>
	</div>
</body>
</html>
        