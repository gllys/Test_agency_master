<?php
include 'Libs/UYouPai.php';
$upyun = new UYouPai();
$model = array('code', 'message', 'url', 'time', 'image-width', 'image-height', 'image-frames', 'image-type');
foreach ($model as $val)
    if (!isset($_GET[$val])) {
        echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"参数不全上传失败"});</script>';
        exit;
    }
if (md5("{$_GET['code']}&{$_GET['message']}&{$_GET['url']}&{$_GET['time']}&" . $upyun->formApiSecret) != $_GET['sign']) {
    echo '<script type="text/javascript">parent.upload_callback({status:1,msg:"密钥不正确上传失败"});</script>';
    exit;
}

echo '<script type="text/javascript">parent.upload_callback({status:200,msg:"' . $upyun->host . $_GET['url'] . '"});</script>';
exit;
