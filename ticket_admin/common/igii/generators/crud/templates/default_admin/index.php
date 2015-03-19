<div id="content-header">
    <div id="breadcrumb"> <a href="/" title="导航" class="tip-bottom"><i class="icon-home"></i> 导航</a><a href="javascript:;" class="current tip-bottom" title="<?php echo $tableName . '管理' ?>"><?php echo $tableName . '管理' ?></a> </div>
</div>
<div class="container-fluid">
    <hr>
    <!--查询开始-->
    <div class="row-fluid">
    <form method="post" action="#" class="search">
        <?php
        $like = array(); //模糊查询的字段
        $time = array(); //时间范围查询的字段
        $compare = array(); //精确查询
        $pri = 'id';
        foreach ($fields as $item) {
            if (!in_array($item['column_name'], $search))
                continue;
            if ($this->searchTime($item['column_comment'])) {
                $time[] = $item;
                continue;
            }

            if ($this->searchLike($item['column_comment'])) {
                $like[] = $item;
                continue;
            }

            if ($this->searchSelect($item['column_comment'])) {
                $compare[] = $item;
                continue;
            }
        }
        ?>
        <?php
        if ($like):
            ?>
            <label class="control-label">
                <label>模糊查询:<input type="text" placeholder="<?php
                    $_names = array();
                    foreach ($like as $item) {
                        $_names[] = $this->getName($tableName, $item['column_comment']);
                    }
                    echo join('/', $_names);
                    ?>" name="_like" value="<?php echo '<?php ' ?>echo $model['_like'] <?php echo '?>' ?>"/></label>
            </label>
        <?php endif; ?>

        <?php
        foreach ($compare as $item):
            ?>
            <label class="control-label">
                <label style="line-height: 30px;">&nbsp;<?php echo $this->getName($tableName, $item['column_comment']) ?>:
                    <select class="span" name="<?php echo $item['column_name'] ?>">
                        <option value="null" selected="selected">选择</option>
                        <?php
                        $table = $this->getTable($item['column_comment']);
                        echo "<?php \$_s = $table::model()->findAll() ; foreach(\$_s as \$item) echo \"<option value='{\$item['id']}'>{\$item['name']}</option>\" ?>";
                        ?>
                    </select>
                </label>
            </label>
        <?php endforeach; ?>

        <?php
        foreach ($time as $item):
            ?>
            <label class="control-label" style="float: left;padding-left: 5px;">
                <label><?php echo $this->getName($tableName, $item['column_comment']) ?>: 
                    <div class="input-append date datepicker" data-date="<?php echo "<?php "; ?>echo $model['begin_<?php echo $item['column_name'] ?>']<?php echo "?>"; ?>">
                        <input class="span11" type="text" name="begin_<?php echo $item['column_name'] ?>" data-date-format="yyyy-dd-mm" value="<?php echo "<?php "; ?>echo $model['begin_<?php echo $item['column_name'] ?>']<?php echo "?>"; ?>">
                        <span class="add-on">
                            <i class="icon-th"></i>
                        </span>
                    </div>
                </label>
            </label>
            <label class="control-label" style="float: left;padding-left: 5px;">
                <label>至: 
                    <div class="input-append date datepicker" data-date="<?php echo "<?php "; ?>echo $model['end_<?php echo $item['column_name'] ?>']<?php echo "?>"; ?>">
                        <input class="span11" type="text" data-date-format="yyyy-dd-mm" name="end_<?php echo $item['column_name'] ?>" value="<?php echo "<?php "; ?>echo $model['end_<?php echo $item['column_name'] ?>']<?php echo "?>"; ?>">
                        <span class="add-on">
                            <i class="icon-th"></i>
                        </span>
                    </div>
                </label>
            </label>
        <?php endforeach; ?>
        <div>
        <label class="control-label" style="margin-left:30px;"><button class="btn btn-danger" type="submit">查询</button></label>

        <label class="control-label"><a class="btn btn-danger" href="/<?php echo $controller_id ?>/create/">添加<?php echo $tableName ?></a></label>
        </div>
    </form>
    </div>
    <!--查询结束-->
    
    
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
                    <h5><?php echo $tableName ?>列表</h5>
                </div>

                <div class="widget-content nopadding">
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <?php
                                foreach ($fields as $item):
                                    if (!in_array($item['column_name'], $show))
                                        continue;
                                    ?>
                                    <th><?php echo $this->getName($tableName, $item['column_comment']) ?></th>
                                    <?php ?>
                                    <?php
                                endforeach;
                                ?>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo "<?php "; ?>
                            foreach($list as $item):
                            <?php echo "?>\n"; ?>
                            <tr class="gradeA">
                                <?php
                                foreach ($fields as $item):
                                    if (!in_array($item['column_name'], $show))
                                        continue;
                                    ?>

                                    <td style="text-align:center;"><?php
                                        if ($this->searchLike($item['column_comment']))
                                            echo "<?php echo \$item['{$item['column_name']}'] ?>\n";
                                        else if ($this->searchTime($item['column_comment']))
                                            echo "<?php echo date('Y-m-d H:i:s',\$item['{$item['column_name']}']) ?>\n";
                                        else if ($this->searchSelect($item['column_comment'])) {

                                            $table = $this->getTable($item['column_comment']);
                                            echo "<?php if(\$_model = $table::model()->findByPk(\$item['{$item['column_name']}'])) echo  \$_model['name'] ; ?>\n";
                                        }
                                        ?></td>
                                    <?php
                                endforeach;
                                ?>
                                <td style="text-align:center;" width="330">
                                    <!--<a class="btn btn-danger" href="/<?php echo $controller_id ?>/view/id/<?php echo "<?php "; ?>echo $item['id'] <?php echo "?>\n"; ?>" >查看<?php echo $tableName ?></a>-->
                                    <a class="btn btn-danger" href="/<?php echo $controller_id ?>/update/id/<?php echo "<?php "; ?>echo $item['id'] <?php echo "?>\n"; ?>" >修改<?php echo $tableName ?></a>
                                    <a class="btn btn-danger" href="/<?php echo $controller_id ?>/del/id/<?php echo "<?php "; ?>echo $item['id'] <?php echo "?>\n"; ?>" >删除<?php echo $tableName ?></a>
                                </td>
                            </tr>
                            <?php echo "<?php "; ?>
                            endforeach;
                            <?php echo "?>\n"; ?>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--查询结果显示结束-->

<!--分页开始-->
<div class="pagenumQu">
    <div id="pager" class="page_area" style="padding:10px 0;text-align:center;">
        <?php echo "<?php "; ?>
        $this->widget('CLinkPager', array(
        'header' => '共' . $pager->getItemCount() . '条，当前页显示第' . ($pager->getCurrentPage() * $pager->pageSize + 1) . '-' . ($pager->getCurrentPage() + 1) * $pager->pageSize . '条 &nbsp;',
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'pages' => $pager,
        'maxButtonCount' => 15
        ));
        <?php echo "?>\n"; ?>
    </div>
</div>
<!--分页结束-->
<!--页面内容结束-->
<link rel="stylesheet" href="/css/colorpicker.css" />
<script type="text/javascript">
    $(function() {
        //相关选中事件
<?php foreach ($compare as $item): ?>
            $('select[name=<?php echo $item['column_name'] ?>]').val('<?php echo "<?php "; ?>echo $model['<?php echo $item['column_name'] ?>'] <?php echo "?>"; ?>');
<?php endforeach ?>
    });
    //分页跳转
    $('#submit,#pager a').click(function() {
        $('form').attr('action', this.href).submit();
        return false;
    });
    $(function(){
        $('.datepicker').datepicker();
    });
</script>    
