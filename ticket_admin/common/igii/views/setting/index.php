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
                    <h5>填写表名称</h5>
                </div>
                <div class="widget-content nopadding">
                    <form class="form-horizontal" method="post" action="#">
                        <div class="control-group">
                            <label class="control-label">Table Name:</label>
                            <div class="controls">
                                <input type="text" placeholder="" name="tableName" value="<?php echo $tableName; ?>" class="span4">
                                <span class="help-inline">*填写生成的表</span>
                            </div>
                        </div>	

                        <div class="control-group">
                            <label class="control-label">生成的目录:</label>
                            <div class="controls">
                                <input type="text" placeholder="" name="dir" value="<?php echo $dir; ?>" class="span4">
                                <span class="help-inline">*生成的model目录</span>
                            </div>
                        </div>		
                        <button style="margin-left:95px;" class="btn btn-success" type="submit">生成配置</button>

                    </form>
                </div>
            </div>

            <?php
            if ($arr):
                ?>
                <div class="control-group">
                    <label class="control-label">生成文件路径</label>
                    <div class="controls">
                        <?php echo YiiBase::getPathOfAlias($dir.'.'.$this->underline2camel($tableName)).".php" ?>
                    </div>
                </div>
                <?php
            endif;
            ?>

        </div>
    </div>
</div>