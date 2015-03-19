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
                .label-green{
                    cursor:pointer
                }
                .pop{
                    position:relative;
                    display:inline-block;
                }
                .pop-content{
                    display:none;
                    width:300px;
                    position:absolute;
                    top:20px;
                    left:0;
                    z-index:1;
                    background-color:#fff;
                    padding:10px;
                    border-radius:5px;
                    border:1px solid #ccc;
                    box-shadow:0 5px 10px rgba(0,0,0,.1)
                }
                .pop:hover .pop-content{
                    display:block;
                    text-align:left;
                }
                .pop-content ul{
                    margin:0;
                    list-style:none
                }
                .pop-content li{
                    padding:0;
                    line-height:2
                }
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
            <?php if($landscape):?>
                <div class="form-actions">
                    <button class="btn btn-lg btn-red" type="button" id="btn-remove">解除绑定</button>
                </div>
            <?php endif;?>
        </div>

        <div class="box">
            <div class="table-header" style="height:auto;padding-bottom:10px;">
                <form action="">
                    <div class="row-fluid" style="margin-bottom:10px;">
                        景区名称：<input type="text" placeholder="" name="landscape_name"    value="<?php echo $get['landscape_name'];?>" style="width:300px;margin:0 10px 0">
                        <button class="btn btn-default" style="float:none;">查询</button>
                    </div>
                 </form>
            </div>
            <div class="content">
                <table class="table table-normal">
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
                                <td><?php echo $landscape['status']?LandscapeCommon::getLandscapeStatus($landscape['status']):""?></td>
                                <td>
                                    <?php if($landscape['id'] == $equipment['landscape_id']):?>
                                        已绑定
                                    <?php else:?>
                                        <a href="javascript:;" onclick="bindLandscape(<?php echo $landscape['id']?>, '<?php echo $landscape['name']?>')">
                                            <button class="btn btn-blue">绑定</button>
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
    function bindLandscape(landscape_id, landscape_name)
    {
        var equipment_id = "<?php echo $equipment['id'];?>";
        if (window.confirm('确定绑定 '+landscape_name+' 景区么？')) {
            $.post(
                'index.php?c=landscape&a=saveELandscape',
                {
                    eid : equipment_id,
                    lid : landscape_id
                },
                function(data){
                    if(typeof data.errors != 'undefined'){
                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>绑定设备失败!'+data.errors.msg+'</div>';
                        $('#show_msg').html(warn_msg);
                    }else{
                        var succss_msg = '<div class="alert alert-success"><strong>绑定成功!</strong> 2 秒后刷新本页..</div>';
                        $('#show_msg').html(succss_msg);
                        setTimeout("location.href='landscape_landscape_<?php echo $equipment['id'];?>.html'", 2000);
                    }
                },
            "json");
        }
    }

$(document).ready(function(){
    //解除绑定的点击事件    
    $('#btn-remove').click(function(){    
        if (window.confirm('解除绑定将把安装位置一起解除，您确定解除绑定？')) {
            $.post(
                'index.php?c=landscape&a=removeELandscape',
                {
                    eid : "<?php echo $equipment['id'];?>"
                },
                function(data){
                    if(typeof data.errors != 'undefined'){
                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>解除绑定失败!'+data.errors.msg+'</div>';
                        $('#show_msg').html(warn_msg);
                    }else{
                        var succss_msg = '<div class="alert alert-success"><strong>解除绑定成功!</strong> 2 秒后刷新本页..</div>';
                        $('#show_msg').html(succss_msg);
                        setTimeout("location.href='landscape_landscape_<?php echo $equipment['id'];?>.html'", 2000);
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