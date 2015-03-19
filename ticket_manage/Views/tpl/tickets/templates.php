<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/29/14
 * Time: 2:38 PM
 * File: templates.php
 */
?>
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

    <div id="show_msg"></div>

    <div class="container-fluid padded" style="padding-bottom: 0px;">
        <div class="box">
            <div class="box-header">
                <span class="title">搜索</span>
            </div>
            <div class="box-content padded">
                <form class="fill-up separate-sections" method="get" action="#">
                    <div class="row-fluid" style="height: 30px;">
                        <div class="span1">模板名称：</div>
                        <div class="span2">
                            <input type="text" name="name" value="<?php if (isset($get['keyword'])) echo $get['keyword']; ?>" placeholder="请输入模板名称">
                        </div>

                        <div class="span3">
                            <button class="btn btn-default" id="searchBtn" type="submit">搜索</button>
                            <a href="/tickets_prints_0.html" class="btn btn-blue" style="width:50px;">新增</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title">模板列表</span>
            </div>
            <div class="box-content">
                <table class="table table-normal">
                    <thead>
                    <tr>
                        <td>编号</td>
                        <td style="width:150px;">模板名称</td>
                        <td>材质</td>
                        <td>尺寸</td>
                        <td style="width: 200px;">操作</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (isset($list)): ?>

                        <?php foreach ($list as $value): ?>
                            <tr class="status-pending" height="36px">
                                <td><?php echo $value['id']; ?></td>
                                <td class="icon">
                                    <a href="/tickets_prints_<?php echo $value['id']; ?>.html" data-toggle="modal"><?php echo $value['name']; ?></a>
                                </td>
                                <td><?php echo $value['spec']; ?></td>
                                <td><?php
                                    echo (isset($value['height']) ? $value['height'] : "") .'*'.(isset($value['width']) ? $value['width'] : "");
                                    ?></td>
                                <td><a title="编辑" href="/tickets_prints_<?php echo $value['id']; ?>.html">
                                        <button class="btn btn-blue">编辑</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dataTables_paginate paging_full_numbers">
            <?php echo $pagination; ?>
        </div>
    </div>
</div>

</body>
</html>
