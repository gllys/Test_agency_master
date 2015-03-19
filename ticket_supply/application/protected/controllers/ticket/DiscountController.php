<?php

class DiscountController extends Controller
{
    private $types = array('+', '-', '%+', '%-');

    public function actionIndex()
    {
        $get = $_GET;
        $param = array('supplier_id'=>Yii::app()->user->org_id,
            'current'=>empty($get['page'])?1:$get['page'],'items'=>15);
        $result = Ticketdiscountrule::api()->lists($param);
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
        $param = array('type'=>0,//只需要白名单
            'supplier_id'=>Yii::app()->user->org_id,'items'=>150);
        $result = Ticketorgnamelist::api()->lists($param);
        if($result['code']=="succ"){
            $data['limitList'] = isset($result['body']['data'])?$result['body']['data']:array();
        }else{
            $data['limitList'] = array();
        }
        $this->render('add',$data);
    }

    public function actionEdit($id){
        if(!empty($id)){
            $rs = Ticketdiscountrule::api()->detail(array('id'=>$id,'supplier_id'=>Yii::app()->user->org_id));
            if($rs['code'] == "succ"){
                $data['info'] = $rs['body'];

                $param = array('type'=>0,//只需要白名单
                    'supplier_id'=>Yii::app()->user->org_id,'items'=>150);
                $result = Ticketorgnamelist::api()->lists($param);
                if($result['code']=="succ"){
                    $data['limitList'] = isset($result['body']['data'])?$result['body']['data']:array();
                }else{
                    $data['limitList'] = array();
                }

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
            $param['id'] = $_POST['id'];
            $param['supplier_id'] = Yii::app()->user->org_id;
            $param['user_id'] = Yii::app()->user->uid;
            $param['user_name'] = Yii::app()->user->account;
            $rs = Ticketdiscountrule::api()->del($param);
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
        $rs = Ticketdiscountrule::api()->detail(array('id'=>$id,'supplier_id'=>Yii::app()->user->org_id));
        if($rs['code'] == "succ"){
            $data['info'] = $rs['body'];

            $param = array('type'=>0,//只需要白名单
                'supplier_id'=>Yii::app()->user->org_id,'items'=>150);
            $result = Ticketorgnamelist::api()->lists($param);
            if($result['code']=="succ"){
                $data['limitList'] = isset($result['body']['data'])?$result['body']['data']:array();
            }else{
                $data['limitList'] = array();
            }
            $data['action'] = "add";
            $this->render('add',$data);
        }else{
            Throw new  CHttpException('404',$rs['message']);
        }
    }

    public function actionSave(){
        if (Yii::app()->request->isPOSTRequest) {
            $post = $_POST;
            $post['supplier_id'] = Yii::app()->user->org_id;
            $post['user_id'] = Yii::app()->user->uid;
            $post['user_name'] = Yii::app()->user->account;
           // $post['discount'] = $this->types[$post['g_type']] . $post['discount'];
            $post['fat_discount'] = 0 - $post['fat_discount'];
            $post['group_discount'] = 0 - $post['group_discount'];
            if($post['action']=='edit'&&!empty($post['id'])){
                $rs = Ticketdiscountrule::api()->update($post);
            }else{
                $rs = Ticketdiscountrule::api()->add($post);
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