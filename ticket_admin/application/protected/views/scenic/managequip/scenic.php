<div class="contentpanel">
    <div id="show_msg"></div>
    <style>
    .table-normal tbody td a{
        margin:0 5px;
        text-decoration:none;
        }
    .table-normal tbody td a{
        margin:0 5px;
        text-decoration:none;
    }
    .table-normal button{
        min-width:inherit;
    }
    .table-normal tbody td{
        text-align:center
    }
    .table-1 td:nth-child(2n-1){
        font-weight:700;
        width:100px;
    }
    .table-1 td:nth-child(2n){
        text-align: left;
    }
    .btn-group ul {
        min-width: 80px;
    }
    </style>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">安装位置（子景区）</h4>
        </div>
        <div class="panel-body">
            <div class="form-actions"  style="margin-bottom: 12px;">
                <a href="javascript:;" class="clearPart" onclick="history.go(-1);">
                    <button class="btn btn-default"  type="button">返回</button>
                </a>
            </div>
            <table class="table table-normal table-1">
                <tr>
                    <td>序号</td>
                    <td><?php echo $equipment['id']?></td>
                    <td>设备编号</td>
                    <td><?php echo $equipment['code'];?></td>
                    <td>类型</td>
                    <td><?php echo $equipment['type']==0?"手持机":"闸机";?></td>
                </tr>
                <tr>
                    <td>绑定供应商</td>
                    <td><?php
                        $getName = Organizations::api()->list(array('type'=>'supply','id'=>$equipment['organization_id']));
                        $names = ApiModel::getLists($getName);
                        echo $equipment ? "<a href='/scenic/managequip/supply/id/$equipment[id]'>". $names[$equipment['organization_id']]['name']."</a>": '无';
                        ?></td>
                    <td>绑定景区</td>
                    <td><?php echo $landscape ? "<a href='/scenic/managequip/landscape/id/$equipment[id]'>" .$landscape['name']."</a>" : '无';?></td>
                    <td>安装位置</td>
                    <td><?php echo $poi ? $poi['name'] : '全部';?></td>
                </tr>
                <tr>
                    <td>设备名称</td>
                    <td><?php echo $equipment['name'];?></td>
                    <td>更新时间</td>
                    <td><?php echo date("Y-m-d H:i:s",$equipment['updated_at']);?></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <?php if($poi):?>
                <div class="form-actions" style="margin-top: 12px;">
                    <button class="btn btn-primary btn-sm clearPart" type="button" id="btn-remove">解除安装</button>
                </div>
            <?php endif;?>
        </div>
    </div>

    <div class="panel panel-default">
        <table class="table table-normal">
            <thead>
                <tr>
                    <td>编号</td>
                    <td>子景区（安装位置）</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
            <?php if($pois):?>
                <?php foreach($pois as $poi):?>
                <tr>
                    <td><?php echo $poi['id'];?></td>
                    <td><?php echo $poi['name'];?></td>
                    <td>
                        <?php if($poi['id'] == $equipment['poi_id']):?>
                            已安装
                        <?php else:?>
                            <a class="clearPart" href="javascript:;" onclick="bindScenic(<?php echo $poi['id']?>, '<?php echo
                            $poi['name']?>')">
                                <button class="btn btn-primary btn-sm">安装</button>
                            </a>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            <?php else:?>
                <tr>
                    <td colspan="3">暂无子景点记录</td>
                </tr>
            <?php endif;?>
            </tbody>
        </table>
</div>

    <div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
        <?php
        if (!empty($pois)) {
            $this->widget('common.widgets.pagers.ULinkPager', array(
                    'cssFile' => '',
                    'header' => '',
                    'prevPageLabel' => '上一页',
                    'nextPageLabel' => '下一页',
                    'firstPageLabel' => '',
                    'lastPageLabel' => '',
                    'pages' => $pages,
                    'maxButtonCount' => 3, //分页数量
                )
            );
        }
        ?>
    </div>

</div>
<script type="text/javascript">
    //定义绑定landscape的方法
    function bindScenic(poi_id, poi_name)
    {
        var equipment_id = "<?php echo $equipment['id'];?>";
        var landscape_id = "<?php echo isset($landscape['id'])?$landscape['id']:''; ?>";
       // if (window.confirm('确定安装到 '+poi_name+' 景区么？'))
        PWConfirm('确定安装到 '+poi_name+' 景区吗？', function (){
            $.post('/scenic/managequip/saveEScenic', {eid : equipment_id, pid : poi_id,landscapeid : landscape_id}, function(data){
                    if(typeof data.errors != 'undefined'){
                        setTimeout(function(){
                            alert('安装设备失败!');
                        }, 500);
                    }else{

                        alert('安装成功!');
                        setTimeout("window.location.reload();",2000);

                    }
                },
            "json");
        })
    }

$(document).ready(function(){
    //解除绑定的点击事件    
    $('#btn-remove').click(function(){    
        //if (window.confirm('确定解除安装么？'))
        PWConfirm('确定解除安装么？', function (){
            $.post('/scenic/managequip/removeEScenic', {eid : "<?php echo $equipment['id'];?>"}, function(data){
                    if(typeof data.errors != 'undefined'){
                        alert('解除安装失败!'+data.errors.msg);
                    }else{
                        alert('解除安装成功!');
                        setTimeout("window.location.reload();",2000);
                    }
                },
            "json");
            return false;
        })
    });
});
</script>