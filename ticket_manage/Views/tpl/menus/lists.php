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
.btn-group ul {
    min-width: 80px;
}
</style>
    <div class="container-fluid padded">
        <div class="box">
            <div class="table-header" style="height:auto;padding-bottom:10px;">
              <form action="#">
                <div class="row-fluid" style="margin-bottom:10px;">
                    菜单名称：<input type="text" placeholder="" name="menu_title" style="width:200px;margin:0 10px 0" value="<?php echo $get['menu_title']?>">
                    项目类型：<select class="uniform" name="app_id">
                        <option  selected="selected" value="">全部</option>
                                <option value="scenic"<?php if($get['app_id'] == 'scenic'){ echo 'selected="selected"';}?>>景区</option>
                                <option value="agency" <?php if($get['app_id'] == 'agency'){ echo 'selected="selected"';}?>>旅行社</option>
                            </select>
                    菜单类型：<select class="uniform" name="menu_type">
                                <option value="menu"  <?php if($get['menu_type'] == 'menu'){ echo 'selected="selected"';}?>>菜单</option>
                                <option value="permission" <?php if($get['menu_type'] == 'permission'){ echo 'selected="selected"';}?>>权限</option>
                                <option value="workground" <?php if($get['menu_type'] == 'workground'){ echo 'selected="selected"';}?>>主菜单</option>
                         </select>
                    <button class="btn btn-default" type="submit" style="float:none;">查询</button>
                    <a class="btn btn-red" href="landscape_saveMenu.html" style="float:none; width: 40px; margin-left: 10px;">新建</a>
                </div>
              </form>
            </div>


            <div class="content">
            <table class="table table-normal order-list">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>排序</td>
                        <td>项目名称</td>
                        <td>菜单类型</td>
                        <td>菜单名称</td>
                        <td>一级菜单</td>
                        <td>连接地址</td>
                        <td>正则地址</td>
                        <td>权限组</td>
                        <td>功能说明</td>
                        <td>状态</td>
                        <td  style="width:90px;">操作</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php if($data):?>
                        <?php foreach($data as $item):?>
                            <tr>
                                <td><?php echo $item['id'];?></td>
                                <td><?php echo $item['menu_order'];?></td>
                                <td><?php echo $item['app_id']=='scenic'?'景区':'旅行社';?></td>
                                <td><?php 
                                    $menus = array('menu'=>'菜单','permission'=>'权限','workground'=>'主菜单');
                                echo $menus[$item['menu_type']] ?></td>
                                <td><?php echo $item['menu_title'];?></td>
                                <td><?php echo $item['workground'];?></td>
                                <td><?php echo $item['menu_path'];?></td>
                                <td><?php echo $item['path_rewrite'];?></td>
                                <td><?php $_model = Load::model('Menus')->getOne(array('menu_type'=>'permission','permission'=>$item['permission'])) ;echo $_model['menu_title']; ?></td>
                                <td><?php echo $item['notice'];?></td>
                                <td><?php echo $item['deleted_at']?'<font color="red">无效</font>':'有效';?></td>
                                <td><a href="landscape_upMenu.html?id=<?php echo $item['id'] ?>">编辑</a> <a href="landscape_delMenu.html?id=<?php echo $item['id'] ?>">删除</a></td>
                            </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr>
                            <td colspan="7">暂无记录</td>
                        </tr>
                    <?php endif;?>
                </tbody>
            </table>
            </div>
            
            <div class="dataTables_paginate paging_full_numbers">
                <?php echo $pagination;?>
            </div>

        </div>
    </div>
</div>

<script src="Views/js/equipment/add.js"></script>
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<script>
$(document).ready(function() {
    $('.form-time').daterangepicker({
        format:'YYYY-MM-DD'
    })
})
</script>
</body>
</html>