<?php

class LimitagencyController extends Controller
{
	public function actionIndex()
	{
        $get = $_GET;
        $param = array(
            'supplier_id'=>Yii::app()->user->org_id,
            'current'=>empty($get['page'])?1:$get['page'],'items'=>15);
        $result = Ticketorgnamelist::api()->lists($param);
        if($result['code']=="succ"){
            $data['lists'] = isset($result['body']['data'])?$result['body']['data']:array();
            $pagination = ApiModel::getPagination($result);
            $pages = new CPagination($pagination['count']);
            $pages->pageSize = 15; #每页显示的数目
            $data['pages'] = $pages;
            $this->render('index',$data);
        }else{
            Throw new CException("获取数据失败,".$result['message']);
        }
    }

    public function actionAdd(){
        $data['province'] = Districts::model()->findAllByAttributes(array("level" => 1));
        $this->render('add',$data);
    }

    public function actionEdit($id){
        if(!empty($id)){
            $rs = Ticketorgnamelist::api()->detail(array('id'=>$id));
            if($rs['code'] == "succ"){
                $data['info'] = $rs['body'];
                $data['province'] = Districts::model()->findAllByAttributes(array("level" => 1));
                $this->render('add',$data);
            }else{
                Throw new  CHttpException('404',$rs['message']);
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }

    public function actionDel(){
        if (Yii::app()->request->isPOSTRequest) {
            $id = $_POST['id'];
            $param = array(
                'id'    => $id,
                'supplier_id' => Yii::app()->user->org_id,
                'user_id' => Yii::app()->user->uid,
                'user_name' => Yii::app()->user->account
            );
            $rs = Ticketorgnamelist::api()->del($param);
            if($rs['code'] == "succ"){
                $this->_end(0,"删除成功");
            }else{
                $this->_end(1,$rs['message']);
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }

    public function actionCopy(){
            $id = $_GET['id'];
            $rs = Ticketorgnamelist::api()->detail(array('id'=>$id));
            if($rs['code'] == "succ"){
                $data['info'] = $rs['body'];
                $data['action'] = 'add';
                $data['province'] = Districts::model()->findAllByAttributes(array("level" => 1));
                $this->render('add',$data);
            }else{
                Throw new  CHttpException('404',$rs['message']);
            }
    }

    public function actionSave(){
        if (Yii::app()->request->isPOSTRequest) {
            $post = $_POST;
            $post['agency_ids'] = isset($post['agency_ids'])?implode(",",$post['agency_ids']):"";
            $post['supplier_id'] = Yii::app()->user->org_id;
            $post['user_id'] = Yii::app()->user->uid;
            $post['user_name'] = Yii::app()->user->account;
            if($post['action']=='edit'&&!empty($post['id'])){
                $rs = Ticketorgnamelist::api()->update($post);
            }else{
                $rs = Ticketorgnamelist::api()->add($post);
            }
            if($rs['code']=="succ"){
                $this->_end(0,"保存成功");
            }else{
                $this->_end(1,$rs['message']);
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }
}