<?php
/**
 * ajax请求 
 * 主要用于一些多个地方数据一样的请求
 *
 * @author liuhe(liuhe009@gmail.com)
 * 2013-09-09
 * 
 * @version 1.0
 */
class AjaxController extends BaseController
{
	//文件上传   也是通过接口上传 
	public function fileUpload()
	{
		$this->doAction('attachments', 'saveAttachment', $_SESSION['backend_userinfo']['id']);
	}

	// 获取下级的地域信息
	public function getAreaChildByCode()
	{
		$scenicCommon     = $this->load->common('scenic');
		$data['code']     = $this->getGet('code');
		$data['type']     = $this->getGet('type');
		$data['current']  = $this->getGet('current');

		$cityInfo         = $scenicCommon->getCityInfo($data['code']);
		$data['cityInfo'] = $cityInfo;
		$this->load->view('common/get_city_info', $data);
	}

    /*     * *****
 * 又拍云表单提交代理
 * **** */

    public function upyunAgent() {
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
    }
}
