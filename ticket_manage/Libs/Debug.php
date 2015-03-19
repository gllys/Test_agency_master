<?php
// 消耗内存
function convert($size)
{ 
	$unit = array('b','k','m','g','t','p'); 
	return @round($size/pow(1024, ($i=floor(log($size,1024)))),2).''.$unit[$i];
}
// 
function microtime_float($microtime = NULL)
{
	list($usec, $sec) = explode(' ', !$microtime ? microtime(TRUE) : $microtime);
	return ((float)$usec + (float)$sec);
}
// 赋值
$runTime    = @round(microtime_float() - microtime_float(PI_BEGIN_TIME), 8);
$runMem     = convert(memory_get_usage() - PI_START_MEMS);
$dbFlow     = PI::get('flow', 'db');
$moduleFlow = PI::get('flow', 'module');
$tplFlow    = PI::get('flow', 'tpl');
$debug      = PI::get('flow');
unset($debug['db'], $debug['module'], $debug['tpl']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>debug</title>
</head>
<body>
<div style="width:85%; margin:0 auto;">
	<br />
	<table border=0 width=100%>
		<tr><th style="color:#000000;background-color:#FFFFDD;padding:10px 10px 10px 36px;border:0.1em solid #CC6633">页面执行 调试信息</th></tr>
		<tr align="left" bgcolor=#FFFFFF>
			<td>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">执行时间:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo $runTime; ?>(s)</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">内存消耗:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo $runMem;?></span>
			</td>
		</tr>
	</table>

	<?php if (!empty($dbFlow)) {?>
	<table border=0 width=100%>
		<tr><th style="color:#000000;background-color:#FFFFDD;padding:10px 10px 10px 36px;border:0.1em solid #CC6633">数据库 SQL 调试信息</th></tr>

		<?php
		foreach($dbFlow as $k=>$v)
		{
			$totalTime += $v['time'];
		?>
		<tr align="left" bgcolor=#FFFFFF>
			<td>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">执行时间:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo round($v['time'], 8);?>(s)</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">累计时间:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo round($totalTime, 8);?>(s)</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">SQL:</span><span style="color:blue;border-bottom:1px solid #DADADA;"><?php echo $v['sql'];?>;</span>
			</td>
		</tr>
		<?php
		}
		?>

	</table>
	<?php }?>

	<table border=0 width=100%>
		<tr bgcolor=#cccccc><th colspan=2 style="color:#000000;background-color:#FFFFDD;padding:10px 10px 10px 36px;border:0.1em solid #CC6633">功能模块 调试信息</th></tr>

		<tr bgcolor=#cccccc><td colspan=2><b>common Data:</b></td></tr>
		<?php
		if ($moduleFlow['common'])
		{
		?>
		<?php
		foreach($moduleFlow['common'] as $k => $v)
		{
			$mem = $v['mem'] < 0 ? 0 : round(($v['mem']/1024), 3);
		?>
		<tr align="left" bgcolor=#FFFFFF>
			<td>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">执行时间:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo round($v['time'], 8);?>(s)</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">消耗内存:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo $mem;?>k</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">common:</span><span style="color:blue;border-bottom:1px solid #DADADA;"><?php echo $v['txt'];?></span>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		}
		else
		{
		?>
		<tr bgcolor=#eeeeee><td colspan=2><tt><i>no debug data</i></tt></td></tr>
		<?php
		}
		?>

		<tr bgcolor=#cccccc><td colspan=2><b>view Data:</b></td></tr>
		<?php
		if ($moduleFlow['view'])
		{
		?>
		<?php
		foreach($moduleFlow['view'] as $k => $v)
		{
			$mem = $v['mem'] < 0 ? 0 : round(($v['mem']/1024), 3);
		?>
		<tr align="left" bgcolor=#FFFFFF>
			<td>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">执行时间:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo round($v['time'], 8);?>(s)</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">消耗内存:</span><span style="color:red;border-bottom:1px solid #DADADA;"><?php echo $mem;?>k</span>
				<span style="color:#339966;border-bottom:1px solid #DADADA;background:#F7F7F7">tpl:</span><span style="color:blue;border-bottom:1px solid #DADADA;"><?php echo $v['txt'];?>.php</span>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		}
		else
		{
		?>
		<tr bgcolor=#eeeeee><td colspan=2><tt><i>no debug data</i></tt></td></tr>
		<?php
		}
		?>
	</table>

	<table border=0 width=100%>
		<tr bgcolor=#cccccc><th colspan=2 style="color:#000000;background-color:#FFFFDD;padding:10px 10px 10px 36px;border:0.1em solid #CC6633">Debug 调试信息</th></tr>
		<tr bgcolor=#cccccc><td colspan=2><b>Debug Data:</b></td></tr>
		<?php
		if ($debug)
		{
		?>
		<tr align="left" bgcolor=#eeeeee><td colspan=2 ><tt><?php dump($debug);?><font size=-1></font></tt></td></tr>
		<?php
		}
		else
		{
		?>
		<tr bgcolor=#eeeeee><td colspan=2><tt><i>no debug data</i></tt></td></tr>
		<?php
		}
		?>
	</table>

	<table border=0 width=100%>
		<tr bgcolor=#cccccc><th colspan=2 style="color:#000000;background-color:#FFFFDD;padding:10px 10px 10px 36px;border:0.1em solid #CC6633">视图 调试信息</th></tr>
		<tr bgcolor=#cccccc><td colspan=2><b>Templates Data:</b></td></tr>
		<?php
		if ($tplFlow)
		{
		?>
		<tr align="left" bgcolor=#eeeeee><td colspan=2 ><tt><?php dump($tplFlow);?><font size=-1></font></tt></td></tr>
		<?php
		}
		else
		{
		?>
		<tr bgcolor=#eeeeee><td colspan=2><tt><i>no templates included</i></tt></td></tr>
		<?php
		}
		?>
	</table>
</div>

</body>
</html>