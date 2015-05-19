<div class="contentpanel">
    <div id="show_msg"></div>
    <style>
    .pop-content ul{
        margin:0;
        list-style:none
    }
    .pop-content li{
        padding:0;
        line-height:2
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
            <h4 class="panel-title">绑定景区</h4>
        </div>
        <div class="panel-body">
            <div class="form-actions"  style="margin-bottom: 12px;">
                <a  class="clearPart" href="javascript:;" onclick="history.go(-1);">
                    <button class="btn btn-default "  type="button">返回</button>
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
                        echo $equipment ? $names[$equipment['organization_id']]['name'] : '无';
                        ?></td>
                    <td>绑定景区</td>
                    <td><?php echo $landscape ? $landscape['name'] : '无';?></td>
                    <td>安装位置</td>
                    <td><?php echo $poi ? $poi['name'] : '无';?></td>

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
            <?php if($landscape):?>
                <div class="form-actions " style="margin-top: 12px;">
                    <button class="btn btn-primary btn-sm clearPart" type="button" id="btn-remove">解除绑定</button>
                </div>
            <?php endif;?>
        </div>
    </div>

    <div class="panel panel-default">
        <form class="form-inline" method="get" action="/scenic/managequip/landscape/id/<?php echo $equipment['id'];?>">
            <div class="form-group" style="width: 270px; ">
                <label>景区名称:</label>
                <input type="text" class="form-control" name="landscape_name" value="<?php echo isset($get['landscape_name']) ? $get['landscape_name'] : '';?>">
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-sm" type="submit">查询</button>
            </div>
        </form>

    </div>


   <table class="table table-bordered mb30 table1">
        <thead>
            <tr>
                <td>编号</td>
                <td>景区名称</td>
                <td>状态</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
            <?php if($landscapes):?>
            <?php foreach($landscapes as $landscape):?>
                <tr>
                    <td><?php echo $landscape['id'];?></td>
                    <td><?php echo $landscape['name'];?></td>
                    <td><?php
                        echo $landscape['status']=='unaudited'?"未审核":($landscape['status']=="normal"?"审核通过":($landscape['status']=="failed"?"驳回":""));
                        ?></td>
                    <td>
                        <?php if($landscape['id'] == $equipment['landscape_id']):?>
                            已绑定
                        <?php else:?>
                            <a class="clearPart" href="javascript:;" onclick="bindLandscape(<?php echo
                            $landscape['id']?>,
                                '<?php echo
                            $landscape['name']?>')">
                                <button class="btn btn-primary btn-sm">绑定</button>
                            </a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
            <?php else: ?>
                <tr>
                    <td colspan="5">暂无景区记录</td>
                </tr>
            <?php endif;?>
        </tbody>
    </table>


    <div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
        <?php
        if (!empty($landscapes)) {
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
    function bindLandscape(landscape_id, landscape_name)
    {
        var equipment_id = "<?php echo $equipment['id'];?>";
        //if (window.confirm('确定绑定 '+landscape_name+' 景区么？'))
        PWConfirm('确定绑定 '+landscape_name+' 景区吗？', function (){
            $.post('/scenic/managequip/saveELandscape', {eid : equipment_id, lid : landscape_id}, function(data){
                    if(typeof data.errors != 'undefined'){
                        alert('绑定设备失败!'+data.errors.msg);
                    }else{
                        alert('绑定成功!');
                        setTimeout("window.location.reload();", 2000);
                    }
                },
            "json");
        })
    }

$(document).ready(function(){
    //解除绑定的点击事件    
    $('#btn-remove').click(function(){    
        //if (window.confirm('解除绑定将把安装位置一起解除，您确定解除绑定？'))
        PWConfirm('解除绑定将把安装位置一起解除，您确定解除绑定？', function (){
            $.post('/scenic/managequip/removeELandscape', {eid : "<?php echo $equipment['id'];?>"}, function(data){
                    if(typeof data.errors != 'undefined'){
                        alert('解除绑定失败!'+data.errors.msg);
                    }else{
                        alert('解除绑定成功!');
                        setTimeout("window.location.reload();", 2000);

                    }
                },
            "json");
            return false;
        })
    });
});
</script>
