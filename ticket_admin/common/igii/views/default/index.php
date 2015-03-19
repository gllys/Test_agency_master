<div id="content-header">
    <div id="breadcrumb">&nbsp;</div>
</div>
<?php
//echo $tableName;
//echo print_r($fields);
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">

            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                    <h5>填写gii生成的ActiveRecord</h5>
                </div>
                <div class="widget-content nopadding">
                    <form class="form-horizontal" method="post" action="#">
                        <div class="control-group">
                            <label class="control-label">Model Class:</label>
                            <div class="controls">
                                <input type="text" placeholder="" name="model" value="<?php if (isset($_POST['model'])) echo $_POST['model']; ?>" class="span4">
                                <button class="btn btn-success" type="submit">生成配置</button>
                                <span class="help-inline">*model能自动加载</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            if ($fields && $tableName):
                ?>
                <!--配置-->
                <div class="widget-box">
                    <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
                        <h5><?php echo $tableName ?>表配置选项</h5>
                    </div>
                    <div class="widget-content nopadding">
                        <form class="form-horizontal" method="post" target="_blank" action="/igii/default/create/">
                            <input type="hidden" placeholder="" name="model" value="<?php if (isset($_POST['model'])) echo $_POST['model']; ?>" class="span4">
                            <div class="control-group">
                                <label class="control-label">Controller ID:</label>
                                <div class="controls">
                                    <input type="text" placeholder="" name="controller_id" value="<?php if (isset($_POST['model'])) echo strtolower($_POST['model']);; ?>" class="span4">
                                    <span class="help-inline">* 例user(控制器名)或者admin/user</span>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Base Controller Class *</label>
                                <div class="controls">
                                    <input type="text" placeholder="" name="base_controller" value="Controller" class="span4">
                                    <span class="help-inline">* 确保能自动加载</span>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">模糊查询 </label>
                                <div class="controls">
                                    <?php
                                    foreach ($fields as $item):
                                        $name = $this->getName($tableName, $item['column_comment']);
                                        if (!$this->searchLike($item['column_comment'])) {
                                            continue;
                                        }
                                        $columnComment = $item['column_comment'];
                                        ?>
                                        <label style = "float: left; margin-right: 15px;">
                                            <div class = "checker" id = "uniform-undefined"><span>
                                                    <input type = "checkbox" name = "search[]" value="<?php echo $item['column_name'] ?>" <?php if (mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) != 'log'): ?>checked="checked"<?php endif; ?> style = "opacity: 0;">
                                                </span></div>
                                            <?php echo $name ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">下拉查询 </label>
                                <div class="controls">
                                    <?php
                                    foreach ($fields as $item):
                                        $name = $this->getName($tableName, $item['column_comment']);
                                        if (!$this->searchSelect($item['column_comment']))
                                            continue;
                                        ?>
                                        <label style = "float: left; margin-right: 15px;">
                                            <div class = "checker" id = "uniform-undefined"><span>
                                                    <input type = "checkbox" name = "search[]" value="<?php echo $item['column_name'] ?>" checked="checked" style = "opacity: 0;">
                                                </span></div>
                                            <?php echo $name ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">时间查询 </label>
                                <div class="controls">
                                    <?php
                                    foreach ($fields as $item):
                                        $name = $this->getName($tableName, $item['column_comment']);

                                        if (!$this->searchTime($item['column_comment']))
                                            continue;
                                        ?>
                                        <label style = "float: left; margin-right: 15px;">
                                            <div class = "checker" id = "uniform-undefined"><span>
                                                    <input type = "checkbox" name = "search[]" value="<?php echo $item['column_name'] ?>" checked="checked" style = "opacity: 0;">
                                                </span></div>
                                            <?php echo $name ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">显示查询项 </label>
                                <div class="controls">
                                    <?php
                                    foreach ($fields as $item):
                                        $name = $this->getName($tableName, $item['column_comment']);
                                        $columnComment = $item['column_comment'];
                                        ?>
                                        <label style = "float: left; margin-right: 15px;">
                                            <div class = "checker" id = "uniform-undefined"><span>
                                                    <input type = "checkbox" name = "show[]" value="<?php echo $item['column_name'] ?>" <?php if (mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) != 'log'): ?>checked="checked"<?php endif; ?> style = "opacity: 0;">
                                                </span></div>
                                            <?php echo $name ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">创建表单项 </label>
                                <div class="controls">
                                    <?php
                                    foreach ($fields as $item):
                                        $name = $this->getName($tableName, $item['column_comment']);
                                        $columnComment = $item['column_comment'];
                                        ?>
                                        <label style = "float: left; margin-right: 15px;">
                                            <div class = "checker" id = "uniform-undefined"><span>
                                                    <input type = "checkbox" name = "create[]" value="<?php echo $item['column_name'] ?>" <?php if ($item['column_key'] != 'PRI' && mb_substr($columnComment, mb_strpos($columnComment, ':') + 1) != 'log'): ?>checked="checked"<?php endif; ?> style = "opacity: 0;">
                                                </span></div>
                                            <?php echo $name ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">删除状态</label>
                               <div class="controls">
                                    <input type="text" placeholder="" name="del_field" value="status" class="span4">
                                    <span class="help-inline">* 如果不没填写或不存在，直接删除数据</span>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn-success" type="submit">生成</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--配置结束-->
                <?php
            endif;
            ?>
        </div>
    </div>
</div>