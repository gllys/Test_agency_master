<?php
/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-1-9
 * Time: 下午1:18
 */

class TokenController extends Base_Controller_Ota
{
    //获取Token
    public function createAction(){
        if($this->userinfo){
            if($this->userinfo['token']){
                session_id($this->userinfo['token']);
                $this->sess->start();
                if($this->sess->userinfo && $this->sess->userinfo['expires_at'] > date('Y-m-d H:i:s',time())){
                    $data = array();
                    $data["token"] = $this->sess->userinfo['token'];
                    $data["expires_at"] = $this->sess->userinfo['expires_at'];
                    Lang_Msg::output(array(
                        'code' => 200,
                        'message' => '',
                        'result' => $data,
                    ));
                }
            }
        }

        $token = strtoupper(md5(Util_Common::uniqid('token')));

        try{
            $this->sess->destroy($this->userinfo['token']);
            $this->userinfo['token'] = $token;
            OtaAccountModel::model()->updateById($this->userinfo['id'], array('token'=>$token));

            session_id($token);
            $this->sess->start();
            $this->sess->userinfo = $this->userinfo;

        } catch(Exception $e) {
            Lang_Msg::output(array(
                'code' => 400,
                'message' => Lang_Msg::getLang('ERROR_TOKEN_1'),
                'result' => array(),
            ));
        }

        $data = array();
        $data["token"] = $token;
        $data["expires_at"] = date('Y-m-d H:i:s',time()+$this->sess->getMaxLifeTime());

        OtaAccountModel::model()->updateById($this->userinfo['id'], array('expires_at'=>$data["expires_at"]));
        Lang_Msg::output(array(
            'code' => 200,
            'message' => '',
            'result' => $data,
        ));
    }


}
