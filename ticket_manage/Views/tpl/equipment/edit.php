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
            <div class="container-fluid padded">
                <div class="box">
                    <div class="box-header"><span class="title"><i class="icon-zoom-in"></i> 添加设备</span></div>
                    <div class="box-content">
                        <form action="index.php?c=landscape&a=upEquip" method="post" id="equipement_update_form" class="fill-up">
                            <div class="row-fluid">
                                <div class="span6">
                                    <ul class="padded separate-sections">

                                        <li class="agency-type">
                                            <div class="row-fluid">
                                                <div class="span4">绑定景点：
                                                    <span class="note"> <a style="color: #0088CC;" href="landscape_landscape_<?php echo $equipment['id']; ?>.html">
                                                            <?php echo $equipment['landscape'] ? $equipment['landscape']['name'] : '无'; ?>
                                                        </a></span>
                                                </div>

                                                <div class="span4">安装位置（子景区）：
                                                    <span class="note"> <?php if ($equipment['landscape']): ?>
                                                            <a style="color: #0088CC;" href="landscape_scenic_<?php echo $equipment['id']; ?>.html">
                                                                <?php echo $equipment['poi'] ? $equipment['poi']['name'] : '无'; ?>
                                                            </a>
                                                        <?php else: ?>
                                                            无
                                                        <?php endif; ?></span>
                                                </div>
                                                <div class="span4"></div>
                                            </div>
                                        </li>

                                        <li class="agency-type">
                                            <label>设备类别：<span class="note"></span></label>
                                            <div class="row-fluid">
                                                <div class="span4">
                                                    <input type="radio" class="icheck" name="type" value="1" <?php
                                                    if ($equipment['type'] == 1) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?>>
                                                    <label>闸机</label>
                                                </div>
                                                <div class="span4">
                                                    <input type="radio" class="icheck" name="type" value="0" <?php
                                                    if ($equipment['type'] == 0) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?>>
                                                    <label>手持验票机</label>
                                                </div>
                                                <div class="span4"></div>
                                            </div>
                                        </li>
                                        <li class="input">
                                            <label>设备编号<strong class="status-error">*</strong><span class="note"></span>
                                                <input type="text" value="<?php echo $equipment['code']; ?>" data-prompt-position="topLeft" class="validate[required,minSize[2],maxSize[32]]" name="code" placeholder="">
                                            </label>
                                        </li>
                                        <li class="input">
                                            <label>设备名称<span class="note"></span>
                                                <input type="text" value="<?php echo $equipment['name']; ?>" data-prompt-position="topLeft" class="validate[minSize[2],maxSize[32]]" name="name" placeholder="">
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="span6"></div>
                            </div>
                            <div class="form-actions">
                                <input type="hidden" name="id" value="<?php echo $equipment['id']; ?>">
                                <button class="btn btn-lg btn-blue" type="button" id="btn-edit">更新</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
        <script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
        <script src="Views/js/common/common.js"></script>
        <script src="Views/js/equipment/add.js"></script>