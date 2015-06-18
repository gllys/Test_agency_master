<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/1
 * Time: 11:06
 */

class SynchroController extends Controller{
    public function actionIndex() {
        $param = array();

        if(isset($_REQUEST['sent_start']) && isset($_REQUEST['sent_start'])){
            //时间判断
            if( $_REQUEST['sent_start'] == '' && $_REQUEST['sent_start'] != ''){
                $_REQUEST['sent_start'] = '2014-06-02';
            }
            if( $_REQUEST['sent_start'] != '' && $_REQUEST['sent_start'] == ''){
                $_REQUEST['sent_start'] = date('Y-m-d',now());
            }

            if(!empty($_REQUEST['sent_start']) && !empty($_REQUEST['sent_start'])){
                $param['startTime'] = strtotime($_REQUEST['sent_start']." 00:00:00");
                $param['endTime'] =strtotime($_REQUEST['sent_end']." 23:59:59");
                if($param['endTime'] < $param['startTime']){ //如果初始时间比介绍时间早 对换时间
                    $time = $param['startTime'];
                    $param['startTime'] = $param['endTime'];
                    $param['endTime'] = $time;
                }
            }
        }

        //是否在线
        if(isset($_REQUEST['state']) && ($_REQUEST['state'] !='')){
            $t =  time();
            $param['state'] = $_REQUEST['state'];
            $ids = Yii::app()->redis->zRangeByScore('live:landscapes', $t-5, $t); //在线的景区id
            $param['landscape_ids'] = implode(',',$ids) ;

        }

        if(isset($_REQUEST['landscape']) && $_REQUEST['landscape'] != ''){
            //判断景区  模糊查询
            $lands = Landscape::api()->lists(array('keyword' =>$_REQUEST['landscape'],'fields'=>'id,name'));
            $lis =ApiModel::getLists($lands);
            $ids=implode(',',ArrayColumn::i_array_column($lis,'id'));
            if(count($lis ) > 0){
                $param['landscape_id'] = $ids;
            }

        }

        $param['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
        $Info = Async::api()->list($param);
        
        $lists = ApiModel::getLists($Info);



        //获取景区id
        $LandId = implode(',',ArrayColumn::i_array_column($lists,'landscape_id'));
        $lanList = Landscape::api()->lists(array('ids'=>$LandId,'fields'=>'id,name'));
        $landlist = ApiModel::getLists($lanList);

        //分页
        $pagination = ApiModel::getPagination($Info) ;
        $pages = new CPagination($pagination['count']);
        $pages->pageSize = 15; #每页显示的数目


        $i = 0;
        foreach($lists as $v){
            foreach($Info['body']['user'] as $item){
                if($v['landscape_id'] == $item['landscape_id']){
                    $lists[$i]['user'] = empty($item['name'])?$item['account']:$item['name'];
                    $lists[$i]['user_id'] = $item['id'];
                    $lists[$i]['mobile'] = $item['mobile'];
                }
            }
            ++$i;
        }

        $infos =array(
            'data' =>$lists,
            'pages'=>$pages,
            'landlist'=>$landlist,
            'get'=>$_REQUEST
        );

        $this->render('index',$infos);
    }


//查看详情
    public  function  actionAsyncList(){
        $param['id'] = $_GET['id'];
        //获取景区名称//获取景区id
        $lanList = Landscape::api()->lists(array('ids'=>$param['id'],'fields'=>'id,name'));
        $landlist = ApiModel::getLists($lanList);
        $landscape = $landlist[$_GET['id']]['name'];

        //获取数据
        $alldetail = Async::api()->detail($param);
        $lists = $alldetail['body'];

        $data1 = $lists['data'];$data2 = $lists['data2'];

        if(!empty($lists['data'])){
            $modelup = ArrayColumn::i_array_column($data1,'model'); // 有对应改变的几个model
        }else{
            $modelup = array();
        }

        if(!empty($lists['data'])){
            $modeldo = ArrayColumn::i_array_column($data2,'model'); // 有对应改变的几个model
        }else{
            $modeldo = array();
        }

       $modelAll =array_unique(array_merge($modelup,$modeldo));


      //  print_r($lists['menu']);exit;
        $allNew = array(); //一级栏目 二级栏目
        $total=0;
        foreach($lists['menu']==''?array():$lists['menu']  as $v){
           if(count(array_intersect($v['items'],$modelAll)) > 0) {  //是 一级栏目 存档
               $allNew[$total] = $v;
               $v['items'] = array_intersect($v['items'],$modelAll);
               foreach($v['items']==''?array():$v['items'] as $value){ //model
                   $k =0 ;
                   foreach($lists['data']==''?array():$lists['data'] as $push){ //mode;
                        if($push['model'] == $value){
                            $allNew[$total]['model'][$value]['push'][$k]['name'] =$lists['model'][$value]['name'];
                            $allNew[$total]['model'][$value]['push'][$k]['time'] = $push['time'];

                        }
                       $k++;
                   }
                   $m =0 ;
                   foreach($lists['data2']==''?array():$lists['data2'] as $pull){ //mode;
                       if($pull['model'] == $value){
                           $allNew[$total]['model'][$value]['pull'][$m]['name'] =$lists['model'][$value]['name'];
                           $allNew[$total]['model'][$value]['pull'][$m]['time'] = $pull['time'];

                       }
                       $m++;
                   }
               }
           }
            $total++;
        }


        $data = array(
            'menu'=>$allNew,
            'landscape'=>$landscape,
            'push'=>$lists['data'],
            'pull'=>$lists['data2']
        );

      //  print_r($data);exit;
        $this->render('asyncList',$data);
    }

}
