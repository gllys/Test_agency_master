<?php
/**
 * Created by PhpStorm.
 * User: zhaqinfeng
 * Date: 14-10-17
 * Time: 下午7:03
 */


Class LandscapeImageController extends Base_Controller_Api {

    public function listsAction(){
        $landscape_id = intval($this->getParam('landscape_id'));
        !$landscape_id && Lang_Msg::error("ERROR_LANDIMG_1");
        $order = $this->getSortRule();

        $where['landscape_id'] = $landscape_id;
        $imgs = LandscapeImageModel::model()->search($where,"*",$order);
        Lang_Msg::output(array('data'=>array_values($imgs)));
    }

    public function addAction(){
        $operator = $this->getOperator(); //获取操作者

        $data = array(
            'landscape_id'=>intval($this->getParam('landscape_id')),
            'url'=>trim(Tools::safeOutput($this->getParam('url'))),
            'created_by'=>$operator['user_id'],
        );
        $LandscapeImageModel =  new LandscapeImageModel();
        $LandscapeImageModel->begin();
        $r = $LandscapeImageModel->addNew($data);
        if($r){
            $LandscapeImageModel->commit();
            Log_Landscape::model()->add(array('type'=>1,'num'=>1,'content'=>Lang_Msg::getLang('INFO_LANDIMG_1').'[ID:'.$r['id'].']【'.$data['url'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang('ERROR_ADD_0'), $r);
        }
        else{
            $LandscapeImageModel->rollback();
            Lang_Msg::error("ERROR_ADD_1");
        }

    }

    public function updateAction(){
        $operator = $this->getOperator(); //获取操作者

        $where = array();
        $id = intval($this->getParam('id'));
        !$id && Lang_Msg::error("ERROR_DEL_2");
        $where['id'] = $id;

        $landscape_id = intval($this->getParam('landscape_id'));
        !$landscape_id && Lang_Msg::error("ERROR_LANDIMG_1");
        $where['landscape_id'] = $landscape_id;

        $LandscapeImageModel = new LandscapeImageModel();
        $detail = $LandscapeImageModel->search($where);
        !$detail && Lang_Msg::error("ERROR_DETAIL_2");
        $data = $detail[$id];

        $url = trim(Tools::safeOutput($this->getParam('url')));
        $deleted = intval($this->getParam('deleted'));
        if($url){
            $data['url'] = $url;
            $data['updated_at'] = date("Y-m-d H:i:s");
        }
        $deleted && $data['deleted_at'] = date("Y-m-d H:i:s");

        $LandscapeImageModel->begin();
        $r = $LandscapeImageModel->updateById($id,$data);
        if($r){
            $LandscapeImageModel->commit();
            Log_Landscape::model()->add(array('type'=>($deleted?3:2),'num'=>1,'content'=>Lang_Msg::getLang('INFO_LANDIMG_2').'[ID:'.$id.']【landscape_id:'.$data['landscape_id'].',url:'.$data['url'].'】'));
            Tools::lsJson(true,Lang_Msg::getLang($deleted?'ERROR_DEL_0':'ERROR_UPDATE_0'),$data);
        }
        else{
            $LandscapeImageModel->rollback();
            Lang_Msg::error($deleted?'ERROR_DEL_1':'ERROR_UPDATE_1');
        }
    }

}
