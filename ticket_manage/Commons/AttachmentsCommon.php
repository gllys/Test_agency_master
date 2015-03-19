<?php

/**
 *  文件相关数据 
 * 
 * 2013-09-09
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class AttachmentsCommon extends BaseCommon {

    protected $_code = array(
        '-1' => '{"errors":{"msg":["未选择文件"]}}',
        '-2' => '{"errors":{"msg":["保存至数据库失败"]}}',
    );

    /**
     * 保存文件 单个上传。多个的话请另写方法
     *
     * @return json
     */
    public function saveAttachment($uid) {
    	 $attachments = $_FILES['attachments'];
        if ($attachments) {
            $md5File = md5_file($attachments['tmp_name']);
            $attachmentsModel = $this->load->model('attachments');
            $exist = $attachmentsModel->getOne(array('md5' => $md5File));
            $file = $this->getFileSavePath() . $md5File . '.' . $this->getFileExtension($attachments['name']);
            $fileExist = file_exists($file);
            $move = move_uploaded_file($attachments['tmp_name'], $file);

            //数据表里的数据
            if (!$exist && !$fileExist){
            	if($move != false){
            		return $this->addAttachment($md5File,$attachments,$uid,$file);
            	}	
            }elseif($exist && $fileExist){
            	return $this->updateAttachment($md5File,$attachments,$exist,$file);
            }elseif($exist && !$fileExist){
            	echo $this->move;
            	return $this->updateAttachment($md5File,$attachments,$exist,$file);
            }elseif(!$exist && $fileExist){
            	return $this->addAttachment($md5File,$attachments,$uid,$file);
            }else{
            	return $this->_getUserError(-1);
            }
        }    
    }
       
            //文件和数据表里的文件数据
            /*if (!$exist && !$fileExist) {
                $UploadFileTool = $this->load->tool('UploadFile');
                $this->setUploadConfig($md5File, $UploadFileTool);
                if ($UploadFileTool->upload()) {
                    $info = $UploadFileTool->getUploadFileInfo();
                    return $this->addAttachment($md5File, $attachments, $uid, $info);
                } else {
                    return '{"errors":{"msg":["' . $UploadFileTool->getErrorMsg() . '"]}}';
                }
            } elseif ($exist && $fileExist) {
                $exist['url'] = $this->getFileHttpUrl($exist['url']);
                return json_encode(array('data' => array($exist)));
            } elseif ($exist && !$fileExist) {
                $UploadFileTool = $this->load->tool('UploadFile');
                $this->setUploadConfig($md5File, $UploadFileTool);
                if ($UploadFileTool->upload()) {
                    $info = $UploadFileTool->getUploadFileInfo();
                    return $this->updateAttachment($md5File, $attachments, $info, $exist);
                } else {
					return '{"errors":{"msg":["' . $UploadFileTool->getErrorMsg() . '"]}}';
                }
            } elseif (!$exist && $fileExist) {
                $info[0]['extension'] = $this->getFileExtension($attachments['name']);
                $info[0]['savename'] = $md5File . "." . $info[0]['extension'];
                return $this->addAttachment($md5File, $attachments, $uid, $info);
            }
        } else {
            return $this->_getUserError(-1);
        }*/
    

    /**
     * 保存文件
     * @param string $md5File md5_file得到的MD5值
     * @param object $UploadFileTool 上传工具类
     * @return 无返回值
     */
    public function setUploadConfig($md5File, &$UploadFileTool) {
        $UploadFileTool->savePath = $this->getFileSavePath($md5File);
        $UploadFileTool->saveRule = 'md5_file';
        $UploadFileTool->saveName = $md5File;
        $UploadFileTool->uploadReplace = true;
    }

    //保存文件后，添加到数据库
    public function addAttachment($md5File, $attachments, $uid, $file){
        $uploadInfo = $this->uploadfiles($file);
	  	if($uploadInfo[0]){
		  	$backFile = $uploadInfo[0]['url'];
		  	$lastStr = substr($backFile,-1);
		  	if($lastStr=="."){
		  		$picUrl = substr($backFile,0,strlen($backFile)-1);
		  	}else {
		  		$picUrl = $backFile;
		  	}
		}
        $fileInfo = array(
            'md5' => $md5File,
            'url' => $picUrl,
            'mime' => $attachments['type'],
            'ext' => $uploadInfo[0]['ext'],
            'created_by' => $uid,
            'created_at' => date('Y-m-d H:i:s', time()),
        );
        $attachmentsModel = $this->load->model('attachments');
        $result = $attachmentsModel->add($fileInfo);
        if ($addId = $attachmentsModel->getAddID()) {
            $fileInfo['id'] = $addId;
            $fileInfo['url'] = $this->getFileHttpUrl($fileInfo['url']);
            return json_encode(array('data' => array($fileInfo)));
        } else {
            return $this->_getUserError(-2);
        }
    }
        
    

    //更新文件
    public function updateAttachment($md5File, $attachments, $exist, $file) {
        $uploadInfo = $this->uploadfiles($file);
	  	if($uploadInfo[0]){
		  	$backFile = $uploadInfo[0]['url'];
		  	$lastStr = substr($backFile,-1);
		  	if($lastStr=="."){
		  		$picUrl = substr($backFile,0,strlen($backFile)-1);
		  	}else {
		  		$picUrl = $backFile;
		  	}
		}
        $fileInfo = array(
            'md5' => $md5File,
            'url' => $picUrl,
            'mime' => $attachments['type'],
            'ext' => $uploadInfo[0]['ext'],
            'updated_at' => date('Y-m-d H:i:s', time()),
        );
        $attachmentsModel = $this->load->model('attachments');
        $result = $attachmentsModel->update($fileInfo, array('md5' => $md5File));
        $affectedRows = $attachmentsModel->affectedRows();
        if ($affectedRows >= 1 && $result) {
            $fileInfo['id'] = $exist['id'];
            $fileInfo['url'] = $this->getFileHttpUrl($fileInfo['url']);
            return json_encode(array('data' => array($fileInfo)));
        } else {
            return $this->_getUserError(-2);
        }
    }

    //获取文件的绝对路径到文件夹的路径
    public function getFileSavePath($md5File) {
        //$extendPath = substr($md5File, 0, 2) . '/' . substr($md5File, 2, 2) . '/';
        //$realPath = realpath(PI_APP_ROOT . '/../fx/Uploads/') . '/' . $extendPath;
        $realPath = realpath(PI_APP_ROOT . '/Uploads/') . '/';
        return $realPath;
    }

    //获取文件的保存路径，不包括域名
    public function getFilePath($savename) {
        $extendPath = substr($savename, 0, 2) . '/' . substr($savename, 2, 2) . '/';
        $realUrl = $extendPath . $savename;
        return $realUrl;
    }

    //获取文件的保存路径，包括域名
    public function getFileHttpUrl($filepath) {
        return $filepath;
    }

    public function getFileExtension($filename) {
        $pathinfo = pathinfo($filename);
        return $pathinfo['extension'];
    }

    public function uploadfiles($dates){
		$url = SET_UPLOADS_URL;
		$fields['attachments'] = '@'.$dates;
		$getdata = $this->_curl_request($url,$fields,' ',"file");
		$content = json_decode($getdata, true);
		return $content["data"];
	}
	
	private function _curl_request($url, $params,$suffix, $method = 'get'){
	
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 90);
		curl_setopt($curl, CURLOPT_USERPWD, SET_UPLOADS_USER. ":" .SET_UPLOADS_PWD);
		$method = strtoupper($method);
		switch($method){
			case 'POST':
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'FILE':
				// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
				break;
			case 'PUT':
			case 'PATCH':
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PATCH','Content-Type: application/json'));
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			case 'GET':
	
				$suffix2 = trim($suffix).$params;
				
				curl_setopt($curl, CURLOPT_URL, $url.'?'.$suffix2);
		}
	
		$curl_response_content = curl_exec($curl);
		$curl_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		return $curl_response_content;
	
	}

}
