<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <title>UpYun API接入点测速</title>
    </head>
    <body>
<div>
<?php

function conn_service($url,$dns){
    echo "<div><strong>测试".$dns."服务器:$url</strong></div>";
    echo "<div>返回结果:</div>";
    $sum = 0;
    $spendtime = 0;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    for($i=0;$i<5;$i++){
        $starttime = microtime(TRUE);
        $contents = trim(curl_exec($ch));  
        $endtime = microtime(TRUE);
        if($endtime-$starttime>=1){
            echo "<div>连接超时</div>";
            echo "<br/>";
            return 2000;
        }
        if($i>0){
            $spendtime = round(($endtime-$starttime)*1000);
            $sum += $spendtime;
            echo "<div>第".$i."次连接花费时间:".$spendtime." ms</div>";
        }
    }
    curl_close($ch);  
    echo "<br/>";
    return $sum/4;
}

function compare($v1,$v2,$v3){
    if($v1<=$v2){
        if($v1<=$v3){
            echo "<p>建议您选择<strong>电信服务器:v1.api.upyun.com</strong></p>";
        }else{
            echo "<p>建议您选择<strong>移动铁通服务器:v3.api.upyun.com</strong></p>";
        }
    }else{
        if($v2<=$v3){
            echo "<p>建议您选择<strong>联通网通服务器:v2.api.upyun.com</strong></p>";
        }else{
            echo "<p>建议您选择<strong>移动铁通服务器:v3.api.upyun.com</strong></p>";
        }
    }
}
$url1 = "v1.api.upyun.com";
$url2 = "v2.api.upyun.com";
$url3 = "v3.api.upyun.com";
$dns1 = "电信";
$dns2 = "联通网通";
$dns3 = "移动铁通";
$v1 = conn_service($url1,$dns1);
$v2 = conn_service($url2,$dns2);
$v3 = conn_service($url3,$dns3);
compare($v1,$v2,$v3);
echo "<p>考虑到偶尔正常的网络不稳定，建议多测试几次再做选择<p>"

?>
</div> 
    </body>
</html>