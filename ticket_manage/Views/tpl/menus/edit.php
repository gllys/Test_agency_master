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
            <div class="box-header"><span class="title"><i class="icon-zoom-in"></i> 添加菜单</span></div>
            <div class="box-content">
                <form action="#" method="post" id="form" class="fill-up">
                    <div class="row-fluid">
                        <div class="span6">
                            <ul class="padded separate-sections">
                                <li class="agency-type">
                                    <label>项目类别：<span class="note"></span></label>
                                    <div class="row-fluid">
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="app_id" value="scenic" checked />
                                            <label>景区</label>
                                        </div>
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="app_id" value="agency" />
                                            <label>旅行社</label>
                                        </div>
                                    </div>
                                </li>
                                 <li class="agency-type">
                                    <label>菜单类别：<span class="note"></span></label>
                                    <div class="row-fluid">
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="menu_type" value="workground" />
                                            <label>顶部导航</label>
                                        </div>
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="menu_type" value="menu" checked="checked" />
                                            <label>菜单</label>
                                        </div>
                                        
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="menu_type" value="permission" />
                                            <label>权限</label>
                                        </div>
                                    </div>
                                </li>
                                <li class="agency-type">
                                    <label>是否显示：<span class="note"></span></label>
                                    <div class="row-fluid">
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="display" value="true" checked />
                                            <label>是</label>
                                        </div>
                                        <div class="span4">
                                            <input type="radio" class="icheck" name="display" value="false"  />
                                            <label>否</label>
                                        </div>
                                    </div>
                                </li>
                                <li class="agency-type">
                                    <label>选择权限组：<span class="note"></span></label>
                                    <select id="select2-drop-mask" name="permission" class="chzn-select" style="opacity: 0;">
                                      <option value="">无</option>  
                                    <?php
                                    $_lists = Load::model('Menus')->getList(array('menu_type' => 'permission'));
                                    foreach ($_lists as $_list):
                                    ?> 
                                     <option value="<?php echo $_list['permission'] ?>"><?php echo $_list['menu_title'] ?></option>
                                    <?php endforeach; ?>   
                                    </select>
                                </li>
                                <script type="text/javascript">
                              
                                </script>
                                 
                                 <li class="input">
                                    <label>菜单或权限名称<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" name="menu_title" placeholder="">
                                    </label>
                                </li>
                                <li class="input">
                                    <label>图标样式<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft"  name="icon" placeholder="">
                                    </label>
                                </li>
                                <li class="input">
                                    <label>一级菜单<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" name="workground" placeholder="">
                                    </label>
                                </li>
                                <li class="input">
                                    <label>链接地址<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" name="menu_path" placeholder="">
                                    </label>
                                </li>
                                
                                <li class="input">
                                    <label>链接重写<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" name="path_rewrite" placeholder="">
                                    </label>
                                </li>
                                
                                <li class="input">
                                    <label>权限别名<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" name="permission" placeholder="">
                                    </label>
                                </li>
                                
                                <li class="input">
                                    <label>功能说明<span class="note"></span>
                                        <input type="text" value="" data-prompt-position="topLeft" name="notice" placeholder="">
                                    </label>
                                </li>
                                
                                <li class="input">
                                    <label>排序<span class="note"></span>
                                        <input type="text" value="0" data-prompt-position="topLeft" name="menu_order" placeholder="">
                                    </label>
                                </li>
                               
                            </ul>
                        </div>
                        <div class="span6"></div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-lg btn-blue" type="button" id="btn-add">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script type="text/javascript">
    //编辑
    $('#btn-add').click(function(){
        $('#form').submit();
    });    
    
    //赋值
    <?php
    foreach($list as $key=>$val):
    ?>
    $("select[name=<?php echo $key ?>],:text[name='<?php echo $key ?>']").val('<?php echo $val ?>');
    $(":radio[name=<?php echo $key ?>][value='<?php echo $val ?>']").attr("checked",true);
    <?php endforeach; ?>
</script>