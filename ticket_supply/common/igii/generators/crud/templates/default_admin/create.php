<div id="content-header">
    <div id="breadcrumb"> <a href="/" title="导航" class="tip-bottom"><i class="icon-home"></i> 导航</a><a href="/<?php echo $controller_id ?>" class="current tip-bottom" title="<?php echo $tableName . '管理' ?>"><?php echo $tableName . '管理' ?></a> <a href="javascript:;" class="current" title="添加<?php echo $tableName ?>" class="tip-bottom">添加<?php echo $tableName ?></a></div>
</div>

<div class="container-fluid"><hr>
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-info-sign"></i> </span>
                    <h5>添加<?php echo $tableName ?></h5>
                </div>
                <div class="widget-content nopadding">
                    <form class="form-horizontal" method="post" action="#" name="basic_validate" id="form" novalidate="novalidate">
                        <?php
                        foreach ($fields as $item):
                            if (!in_array($item['column_name'], $create))
                                continue;
                            ?>
                            <div class="control-group">
                                <label class="control-label"><?php echo $this->getName($tableName, $item['column_comment']) ?></label>
                                <div class="controls">
                                    <?php
                                    if ($this->searchTime($item['column_comment'])) { //时间
                                        ?>    
                                        <input type="text" class="datepicker" name="<?php echo $item['column_name'] ?>"  data-date-format="yyyy-mm-dd"/>
                                    <?php } else if ($this->searchLog($item['column_comment'])) { //日志 ?>
                                        <textarea name="<?php echo $item['column_name'] ?>"></textarea>
                                        <?php
                                    } else if ($this->searchRadio($item['column_comment'])) { //单选 
                                        $table = $this->getTable($item['column_comment']);
                                        echo "<?php \$_list = $table::model()->findAll() ; ?>\n";
                                        ?>
                                        <?php echo "<?php "; ?>
                                        foreach($_list as $key=>$item):
                                        <?php echo "?>\n"; ?>

                                        <label style="float: left;padding-right: 5px;">
                                            <div id="uniform-undefined" class="radio"><span><input style="opacity: 3;margin: 0;" name="<?php echo $item['column_name'] ?>"  <?php echo "<?php "; ?> if($item['id']==<?php echo $item['column_default']?$item['column_default']:0 ?>):<?php echo "?>"; ?> checked="checked" <?php echo "<?php "; ?> endif; <?php echo "?>"; ?> type="radio" value="<?php echo "<?php "; ?> echo $item['id'] <?php echo "?>"; ?>" /></span></div>
                                            <?php echo "<?php "; ?> echo $item['name'] <?php echo "?>"; ?>
                                        </label>
                                        <?php echo "<?php "; ?>
                                        endforeach;
                                        <?php echo "?>\n"; ?>
                                    <?php } else if ($this->searchSelect($item['column_comment'])) { //下拉
                                         $table = $this->getTable($item['column_comment']);
                                        echo "<?php \$_list = $table::model()->findAll() ; ?>\n";
                                        ?>
                                        <select class="span2" name="<?php echo $item['column_name'] ?>">
                                        <?php echo "<?php "; ?>
                                        foreach($_list as $key=>$item):
                                        <?php echo "?>\n"; ?>
                                            <option value="<?php echo "<?php "; ?> echo $item['id'] <?php echo "?>"; ?>" <?php echo "<?php "; ?> if($item['id']==<?php echo $item['column_default']?$item['column_default']:0 ?>):<?php echo "?>"; ?> selected="selected" <?php echo "<?php "; ?> endif; <?php echo "?>"; ?> ><?php echo "<?php "; ?> echo $item['name'] <?php echo "?>"; ?></option>
                                        <?php echo "<?php "; ?>
                                        endforeach;
                                        <?php echo "?>\n"; ?>
                                    <?php } else { //其它  ?>
                                        <input type="text" name="<?php echo $item['column_name'] ?>" />
                                    <?php } ?>
                                        </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="form-actions">
                            <input type="submit" value="添加" class="btn btn-danger" />
                            <input type="button" value="上一页" onclick="history.go(-1);" class="btn btn-warning" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
<!--
    $("#form").validate({
        rules: {
            <?php
            foreach ($fields as $item):
                if (!in_array($item['column_name'], $create))
                                continue;
                if($item['column_default']||$item['is_nullable']=='YES'){
                continue;
                 }
            ?>
            <?php echo $item['column_name'] ?>: {
                required: true,
            },
            <?php  
            endforeach; ?>        
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        }
    });
//-->
    $(function() {
        $('.datepicker').datepicker();
    });
</script>
