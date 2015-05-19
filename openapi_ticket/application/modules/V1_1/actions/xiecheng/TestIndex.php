<?php
/**
 * Created by PhpStorm.
 * User: bee
 * Date: 15-1-8
 * Time: 上午11:36
 * Used: 测试使用
 */
class IndexAction extends Yaf_Action_Abstract{
    //若访问：~/V1/Test/index?partner=xiecheng&sign=debug
    //因对应控制器中没有相应动作，所有会访问动作分发器定义这个动作
    public function execute(){

        $this->body = $request = Yaf_Dispatcher::getInstance()->getRequest()->getParams();;

        //参数转换处理
        $params = $this->body;
        if(isset($this->body['district'])) $params['district_ids'] =$this->body['district'];

        //调用接口查询数据
        $response = ApiScenicModel::model()->lists($params);
        if($response['code'] == 'fail'){
            Lang_Msg::error('ERROR_GLOBEL_1');
        }else if($response['code'] == 'succ'){

            //字段转换处理
            $result = array();
            $data   = $response['body']['data'];
            foreach($data as $ll){

                $res['id']          = $ll['id'];
                $res['name']        = $ll['name'];
                $res['star']        = $ll['landscape_level_id'];
                $res['address']     = $ll['address'];
                $res['thumbnail']   = $ll['thumbnail_img'];
                $res['province']    = array('id'=>$ll['province_id'],   'name'=>$ll['district'][0]);
                $res['city']        = array('id'=>$ll['city_id'],       'name'=>$ll['district'][1]);
                $res['region']      = array('id'=>$ll['district_id'],   'name'=>$ll['district'][2]);

                $result['data'][]   = $res;
            }
            $result['pagination'] = $response['body']['pagination'];

            Lang_Msg::output($result);
        }

    }
}