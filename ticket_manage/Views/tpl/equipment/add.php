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
    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header"><span class="title"><i class="icon-zoom-in"></i> 添加设备</span></div>
            <div class="box-content">
                <form action="index.php?c=landscape&a=saveEquip" method="post" id="equipement_add_form" class="fill-up">
                    <div class="row-fluid">
                        <div class="span6">
                            <ul class="padded separate-sections">
                                <li class="agency-type">
                                    <label>设备类别：<span class="note"></span></label>
                                    <div class="row-fluid">
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="type" value="1" checked>
                                            <label>闸机</label>
                                        </div>
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="type" value="0">
                                            <label>手持验票机</label>
                                        </div>
                                        <div class="span4"></div>
                                    </div>
                                </li>
                                <li class="input">
                                    <label>设备编号<strong class="status-error">*</strong><span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" class="validate[required,minSize[2],maxSize[32]]" name="code" placeholder="">
                                    </label>
                                </li>
                                <li class="input">
                                    <label>设备名称<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" class="validate[minSize[2],maxSize[32]]" name="name" placeholder="">
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="span6"></div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-lg btn-blue" type="button" id="btn-add">添加</button>
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