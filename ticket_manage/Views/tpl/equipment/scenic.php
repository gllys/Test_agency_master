<!DOCTYPE html>
<html>
<?php get_header();?>
<body>
<?php get_top_nav();?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu();?>
<div class="main-content">

    <?php get_crumbs();?>   

    <div id="show_msg"></div>

    <style>
    div.selector{
        margin:0 10px;
        width:200px;
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
        float:left
        }
    .btn-group ul {
        min-width: 80px;
    }
    </style>


    <div class="container-fluid padded">

        <div class="box">
            <div class="form-actions">
                <a href="javascript:;" onclick="history.go(-1);">
                    <button class="btn btn-lg btn-blue" type="button">返回</button>
                </a>
            </div>
            <table class="table table-normal table-1">
                <tr>
                    <td>序号</td>
                    <td><?php echo $equipment['id']?></td>
                    <td>设备编号</td>
                    <td><?php echo $equipment['code'];?></td>
                    <td>类型</td>
                    <td><?php echo EquipmentCommon::getEquipmentType($equipment['type']==0?"andriod":"gate");?></td>
                </tr>
                <tr>
                    <td>绑定景区</td>
                    <td><?php echo $landscape ? $landscape['name'] : '无';?></td>
                    <td>安装位置</td>
                    <td><?php echo $poi ? $poi['name'] : '无';?></td>
                    <td>更新时间</td>
                    <td><?php echo $equipment['updated_at'];?></td>
                </tr>
                <tr>
                    <td>设备名称</td>
                    <td><?php echo $equipment['name'];?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <?php if($poi):?>
                <div class="form-actions">
                    <button class="btn btn-lg btn-red" type="button" id="btn-remove">解除安装</button>
                </div>
            <?php endif;?>
        </div>

        <div class="box">
            <div class="content">
                <table class="table table-normal">
                    <thead>
                        <tr>
                            <td>编号</td>
                            <td>景区名称</td>
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
                                    <a href="javascript:;" onclick="bindScenic(<?php echo $poi['id']?>, '<?php echo $poi['name']?>')">
                                        <button class="btn btn-blue">安装</button>
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

            <div class="table-footer">
                <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination;?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //定义绑定landscape的方法
    function bindScenic(poi_id, poi_name)
    {
        var equipment_id = "<?php echo $equipment['id'];?>";
        if (window.confirm('确定安装到 '+poi_name+' 景区么？')) {
            $.post(
                'index.php?c=landscape&a=saveEScenic',
                {
                    eid : equipment_id,
                    pid : poi_id
                },
                function(data){
                    if(typeof data.errors != 'undefined'){
                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>安装设备失败!'+data.errors.msg+'</div>';
                        $('#show_msg').html(warn_msg);
                    }else{
                        var succss_msg = '<div class="alert alert-success"><strong>安装成功!</strong> 2 秒后跳转到设备管理页..</div>';
                        $('#show_msg').html(succss_msg);
                        setTimeout("location.href='landscape_equipments.html'", 2000);
                    }
                },
            "json");
        }
    }

$(document).ready(function(){
    //解除绑定的点击事件    
    $('#btn-remove').click(function(){    
        if (window.confirm('确定解除安装么？')) {
            $.post(
                'index.php?c=landscape&a=removeEScenic',
                {
                    eid : "<?php echo $equipment['id'];?>"
                },
                function(data){
                    if(typeof data.errors != 'undefined'){
                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>解除安装失败!'+data.errors.msg+'</div>';
                        $('#show_msg').html(warn_msg);
                    }else{
                        var succss_msg = '<div class="alert alert-success"><strong>解除安装成功!</strong> 2 秒后跳转到设备管理页..</div>';
                        $('#show_msg').html(succss_msg);
                        setTimeout("location.href='landscape_equipments.html'", 2000);
                    }
                },
            "json");
            return false;
        }
    });
});
</script>
</body>
</html>