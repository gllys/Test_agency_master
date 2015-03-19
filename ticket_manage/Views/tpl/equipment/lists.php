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
              <form action="landscape_equipments.html">
                <div class="row-fluid" style="margin-bottom:10px;">
                    更新时间：<input type="text" placeholder="" name="update_time" style="width:200px;margin:0 10px 0" class="form-time" value="<?php echo $get['update_time']?>">
                    是否绑定景区：<select class="uniform" name="landscape">
                                <option  selected="selected">全部</option>
                                <option value="yes" <?php if($get['landscape'] == 'yes'){ echo 'selected="selected"';}?>>是</option>
                                <option value="no" <?php if($get['landscape'] == 'no'){ echo 'selected="selected"';}?>>否</option>
                            </select>
                    是否安装：<select class="uniform" name="poi">
                                <option  selected="selected">全部</option>
                                <option value="yes"<?php if($get['poi'] == 'yes'){ echo 'selected="selected"';}?>>是</option>
                                <option value="no" <?php if($get['poi'] == 'no'){ echo 'selected="selected"';}?>>否</option>
                            </select>
                  <button class="btn btn-default" style="float:none;">查询</button>
                </div>
              </form>
            </div>


            <div class="content">
            <table class="table table-normal order-list">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>设备编号</td>
                        <td>设备名称</td>
                        <td>类型</td>
                        <td>绑定供应商</td>
                        <td>绑定景区</td>
                        <td>安装位置（子景区）</td>
                        <td>更新时间</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php if($list):?>
                        <?php foreach($list as $equipment):?>
                            <tr>
                                <td><?php echo $equipment['id'];?></td>
                                <td><?php echo $equipment['code'];?></td>
                                <td><?php echo $equipment['name'];?></td>
                                <td><?php echo EquipmentCommon::getEquipmentType($equipment['type']==0?"andriod":"gate");?></td>
                                <td>
                                    <a href="landscape_supply_<?php echo $equipment['id'];?>.html">
                                        <?php $supply = Organizations::api()->show(array('id'=>$equipment['supply'])); ?>
                                        <?php echo isset($supply['body']['name']) ? $supply['body']['name'] : '绑定供应商';?>
                                    </a>
                                </td>
                                <td>
                                    <?php if($equipment['supply']):?>
                                        <a href="landscape_landscape_<?php echo $equipment['id'];?>.html">
                                            <?php echo $equipment['landscape'] ? $equipment['landscape']['name'] : '绑定景区';?>
                                        </a>
                                    <?php else:?>
                                        无
                                    <?php endif;?>
                                </td>
                                <td>
                                    <?php if($equipment['landscape']):?>
                                        <a href="landscape_scenic_<?php echo $equipment['id'];?>.html">
                                            <?php echo $equipment['poi'] ? $equipment['poi']['name'] : '全部';?>
                                        </a>
                                    <?php else:?>
                                        无
                                    <?php endif;?>
                                </td>
                                <td><?php echo date('Y-m-d H:i:s',$equipment['updated_at']);?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i></button>
                                        <ul class="dropdown-menu">
                                        <li><a href="landscape_editEquip_<?php echo $equipment['id']?>.html">编辑</a></li>
                                        <li><a href="javascript:;" onclick="delEquip(<?php echo $equipment['id']?>)">删除</a></li>
                                        </ul>
                                    </div>
                                </td>
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

<script src="Views/js/equipment/add.js?v=1"></script>
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js?v=2"></script>
<script>
$(document).ready(function() {
    $('.form-time').daterangepicker({
        format:'yyyy-MM-dd'
    })
})
</script>
</body>
</html>