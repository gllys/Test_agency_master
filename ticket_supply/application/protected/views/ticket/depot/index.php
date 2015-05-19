<?php
$this->breadcrumbs = array('产品', '仓库管理');
?>
<div class="contentpanel">
    <div id="verify_return"></div>
    <ul class="nav nav-tabs">
       <?php
        foreach ($data['type_labels'] as $type => $label) :
                ?>
                <li class="<?php echo isset($param['type']) && $type == $param['type'] ? 'active' : '' ?>">
                        <a href="/ticket/depot/index/type/<?php echo $type ?>"><strong><?php echo $label ?></strong></a>
                </li>
        <?php endforeach;
        unset($type, $label); ?>
    </ul>

    <div class="tab-content mb30">
        <style>
            .tab-content .table tr>*{
                text-align:center
            }
            .tab-content .ckbox{
                display:inline-block;
                width:30px;
                text-align:left

            }

        </style>
        <div id="t1" class="tab-pane active">
      
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-inline" method="get" action="/ticket/depot/index">
                        <div class="form-group" style='margin: 0 5px 0 0;<?php if($param["type"] != 2){ echo "display:none;";}?>'>
                            <input class="form-control" placeholder="请输入联票名称" type="text" style="width:200px;" name="name" <?php
                                    if (isset($param['name']) && !empty($param['name'])) {
                                        echo 'value = '.$param["name"];
                                    }
                                    ?>>
                        </div>
                        <div class="form-group">
                            <select data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="distributor-select" name="scenic_id">
                                <option value="">请输入景区名称</option>
                                <?php
                                $lan=array();
                                $lan['status'] = 1;
                                $lan['organization_id'] = YII::app()->user->org_id;
                                if(isset($lan['page'])) {
                                    unset($lan['page']);
                                }
                                $lan['current'] = 1;
                                $lan['fields'] = "id,name";
                                $lan['items'] = 100000;
                                $rs = Landscape::api()->lists($lan);
                                $data = ApiModel::getLists($rs);

                                foreach ($data as $item) {
                                    ?>
                                    <option value="<?php echo $item['id']; ?>"  <?php
                                    if (isset($param['scenic_id']) && !empty($param['scenic_id'])) {
                                        echo $item['id'] == $param['scenic_id'] ? "selected" : '';
                                    }
                                    ?>><?php echo $item['name']; ?></option>
                                        <?php }
                                        ?>
                            </select>
                        </div>
                        <button class="btn btn-primary btn-xs" type="submit">查询</button>
                    </form>
                </div><!-- panel-body -->
            </div>
            <!---如果是联票 就是单数组 这样就要做判断了--->
            <?php if($param['type'] != 2){ // 单票和任务单 ?> 
            <!----非联票--->
            <?php
            if(isset($list)):
                foreach ($list as $key=>$model):
            ?>
            <div class="panel panel-default">
                <div class="panel-heading"><h4 class="panel-title">
                <?php
                //todo optimize
                    $details = Landscape::api()->detail(array('id'=>$key));
                    $detail = ApiModel::getData($details);
                    echo isset($detail['name'])?$detail['name']:''; // 景区名称
                ?>    
                </h4></div>
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>单票名称</th>
                            <th>团队结算价格</th>
                       <?php if($param['type'] != 1):?>  
                            <th>散客结算价格</th>
                            <th>价格/库存变动规则</th>
                        <?php endif;?>       
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($model)):
                             foreach ($model as $item):?>
                        <tr>
                            <td><?php echo $item['name'];?></td>
                            <td><?php echo $item['group_price'];?></td>
                             <?php if($param['type'] != 1){  ?> 
                            <td><?php echo $item['fat_price'];?></td>
                            <td><a style="cursor: pointer;cursor: hand;" data-toggle="modal" data-target=".bs-example-modal-static" onclick="add_rule('<?php echo $item['id']?>','<?php echo $param['type'];?>');">
                                <?php  if(isset($item['rule_id']) && !empty($item['rule_id'])){
                                    $par['id']=$item['rule_id'];
                                    $par['supplier_id']=Yii::app()->user->org_id;
	                                //todo optimize
                                    $detail = Ticketrule::api()->detail($par);
                                    echo  (isset($detail['body']['name'])&& !empty($detail['body']['name']))?$detail['body']['name']:'请选择变动规则';
                                }else{
                                    echo "请选择变动规则";
                                }?>
                                </a></td>
                             <?php  }  ?> 
                            <td>
                                <a onclick="downUp(<?php echo $item['id'];?>);" title="待上架"><i class="glyphicon glyphicon-arrow-up" style="cursor:pointer"></i></a>
                                <a title="修改" style="margin-left: 10px;" <?php if($param['type'] == 0){ echo " href='/ticket/goods/singleedit?id=$item[id]'";}else{ echo " href='/ticket/goods/taskedit?id=$item[id]'";}?>>
                                    <span class="glyphicon glyphicon-edit"></span>
                                </a>
                                <a title="删除" style="margin-left: 10px;" href="/ticket/depot/del/?id=<?php echo $item['id'] ?>" class="del">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </td>
                        </tr>
                          <?php  endforeach;  endif;?>
                    </tbody>
                </table>
            </div>
            <?php      
                endforeach;
               endif;
           ?>
            <?php  }else{  ?>
                <!------联票------->
                <div class="panel panel-default">
                   <!--div class="panel-heading"><h4 class="panel-title">浙江牛头山森林公园</h4></div-->
                     <table class="table table-bordered mb30">
                           <thead>
                             <tr>
                                   <th>联票名称</th>
                                   <th>团队结算价格</th>
                                   <th>散客结算价格</th>
                                   <th>价格/库存变动规则</th>
                                   <th>操作</th>
                             </tr>
                           </thead>
                           <tbody>
                             <?php if(isset($list)):
                                 foreach ($list as $item):?>
                             <tr>
                                   <td><?php echo $item['name'];?></td>
                                   <td><?php echo $item['group_price'];?></td>
                                   <td><?php echo $item['fat_price'];?></td>
                                   <td><a style="cursor: pointer;cursor: hand;" data-toggle="modal" data-target=".bs-example-modal-static" onclick="add_rule('<?php echo $item['id']?>','<?php echo $param['type'];?>');">
                                        <?php  if(isset($item['rule_id']) && !empty($item['rule_id'])){
                                                $par['id']=$item['rule_id'];
                                                $par['supplier_id']=Yii::app()->user->org_id;
	                                        //todo optimize
                                                $detail = Ticketrule::api()->detail($par);
                                                 echo  (isset($detail['body']['name'])&& !empty($detail['body']['name']))?$detail['body']['name']:'';
                                            }else{
                                                echo "请选择变动规则";
                                            }?>
                                       </a></td>
                                   <td>
                                        <a onclick="downUp(<?php echo $item['id'];?>);" title="待上架"><i class="glyphicon glyphicon-arrow-up" style="cursor:pointer"></i></a>
                                        <a title="修改" style="margin-left: 10px;" href="/ticket/goods/unionedit?id=<?php echo $item['id']?>">
                                               <span class="glyphicon glyphicon-edit"></span>
                                       </a>
                                       <a title="删除" style="margin-left: 10px;" href="/ticket/depot/del/?id=<?php echo $item['id'] ?>" class="del">
                                               <span class="glyphicon glyphicon-trash"></span>
                                       </a>
                                   </td>
                             </tr>
                                <?php                         
                                   endforeach;
                                    endif;
                              ?>
                             
                           </tbody>
                     </table>
           </div>
           <?php  }  ?>
            
            
            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    $this->widget('common.widgets.pagers.ULinkPager', array(
                        'cssFile' => '',
                        'header' => '',
                        'prevPageLabel' => '上一页',
                        'nextPageLabel' => '下一页',
                        'firstPageLabel' => '',
                        'lastPageLabel' => '',
                        'pages' => $pages,
                        'maxButtonCount' => 5, //分页数量
                            )
                    );
                    ?>
                </div>
            </div>
        </div><!-- tab-pane -->

        <div id="t2" class="tab-pane"></div><!-- tab-pane -->
    </div>

<div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static">
</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function() {
        !function() {
            $('#distributor-select').change(function() {
                var obj = $(this),
                        val = obj.val()
                b(obj, val)
            })
        }()

        // Select2
        jQuery("#distributor-select, #select-multi, #through-tickets-select").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        jQuery('select option:first-child').text('');

    });
    
   function add_rule(id,type){
        $('#verify-modal').html();
        $.get('/ticket/depot/rule/?id='+id+'&time='+parseInt(1000*Math.random()), function(data) {
            $('#verify-modal').html(data);
        });
    }
    
    
//上下架
    function downUp(id) {
        var id = id;
        $.post('/ticket/depot/DownUP/', {id: id}, function(data) {
            if (data.error) {
                alert(data.msg);
            }else{
                location.href = '/ticket/single/';
            }
        }, "json");
        return false;
    }
  
  
    $(function() {
        //选择
        $('a.del').click(function() {
			 PWConfirm('确定要删除?',function(){
			     $.post($(this).attr('href'), function() {
                window.location.reload();
            });
            });
           
            return false;
        });
    });
    
    
</script>
