<!DOCTYPE html>
<html>
<?php get_header(); ?>
<body>
<?php get_top_nav(); ?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu(); ?>

<div class="main-content">
    <?php get_crumbs(); ?>
    <div id="show_msg">

    </div>

    <style>
        .btn-green{
            min-width:inherit
        }
        .table-normal tbody td{
            text-align:center
        }
        .table-normal tbody td a{
            text-decoration:none
        }
        .table-normal tbody td i{
            margin-left:10px
        }
        .popover-content .btn-default{
            min-width:inherit;
            margin-left:10px;
        }
        .popover-content button i{
            margin:0!important
        }
    </style>

    <div class="container-fluid padded" style="padding-bottom: 0px;">
        <div class="box">
            <div class="box-header">
                <span class="title"><?php echo $label?>搜索</span>
            </div>
            <div class="box-content padded">
                <form class="fill-up separate-sections">
                    <div class="row-fluid" style="height: 20px;">
                        <div class="span1">员工姓名</div>
                        <div class="span3">
                            <input type="text" name="name" placeholder="员工姓名" value="<?php echo isset($get['name'])?$get['name']:"";?>">
                        </div>
                        <div class="span3">
                            <button class="btn btn-default" id="searchBtn" type="submit">搜索</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title"><i class="icon-user"></i>员工管理</span>
            </div>
            <div class="box-content">
                <form class="fill-up" action="system_doStaff.html" method="post" id="staff-form">
                    <table class="table table-normal">
                        <thead>
                        <tr>
                            <td width="20%" colspan="2">账号</td>
                            <td width="20%">姓名</td>
                            <td width="20%">角色</td>
                            <td width="20%">手机号码</td>
                            <td width="10%">状态</td>
                            <td width="10%">操作</td>
                        </tr>
                        </thead>
                        <tbody id="staff-body">
                        <?php if ($data): ?>
                            <?php foreach ($data as $value): ?>
                                <tr class="status-pending" height="36px">
                                    <td class="center">
                                        <?php if ($value['id'] > 1): ?>
                                            <input type="checkbox" class="icheck" name="id[]"
                                                   value="<?php echo $value['id']; ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $value['account'] . ($value['id'] == 1 ? '[管理员]' : ''); ?></td>
                                    <td><?php echo $value['name']; ?></td>
                                    <td><?php if ($value['is_super']) {
                                            echo '超级管理员';
                                        } else {
                                            echo $value['role']['name'];
                                        } ?></td>
                                    <td><?php echo $value['mobile']; ?></td>
                                    <td class="icon"><?php echo UserCommon::getStatus($value['status']); ?></td>
                                    <td>
                                        <a href="system_editStaff_<?php echo $value['id'] ?>.html" title="修改"><i
                                                class="icon-edit"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <td class="icon">
                            <button class="btn btn-default" id="allcheck" style="min-width:60px">全选</button>
                        </td>
                        <td colspan="4">
                            <button class="btn btn-gray" id="staff-form-delete" type="submit" name="operate"
                                    value="del"><i class="icon-trash"></i> 删除
                            </button>
                            <button class="btn btn-black" id="staff-form-edit" type="submit" name="operate"
                                    value="status"><i class="icon-warning-sign"></i> 启用/停用
                            </button>
                            <a href="system_addStaff.html" class="btn btn-green" style="min-width:40px"><i
                                    class="icon-plus"></i> 新增</a>
                        </td>
                        <td></td>
                        </tfoot>
                    </table>
                    <input type="hidden" name="type" value="">
                </form>
            </div>
            <div class="table-footer">
                <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/system/staff.js"></script>
</body>
</html>

