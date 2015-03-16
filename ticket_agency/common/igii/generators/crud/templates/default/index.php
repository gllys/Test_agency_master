<!--主体内容-开始-->
<!--二级目录 区Start-->
<div class="tongj_cont clearfix">
<ul id="op_nav">
        <li class="ton_now"><a href="/<?php echo $controller_id ?>/" title="<?php echo $tableName ?>管理"><?php echo $tableName ?>管理 </a></li>
        <li class="ton_none"><a href="/<?php echo $controller_id ?>/create/" title="添加<?php echo $tableName ?>">添加<?php echo $tableName ?></a></li>
        <!--<li class="ton_none"><a href="/shop/goodsexperience/view/id/1" title="商品管理">通用抽奖</a></li>-->
    </ul>
</div>
<!--二级目录 区End-->


<!--顶部搜索 区Start-->
<div class="select_box">
    <form method="GET" action="#" class="search">
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
        <div class="up_p">
            <p class="gap">
                <label >模糊查询:</label>
                <input type="text" style="width:150px;" class="form_text" placeholder="<?php
                $_names = array();
                foreach ($like as $item) {
                    $_names[] = $this->getName($tableName, $item['column_comment']);
                }
                echo join('/', $_names);
                ?>" name="_like" value="<?php echo '<?php ' ?>echo $model['_like'] <?php echo '?>' ?>"/>
            </p>


            <?php
            //选择查询
            foreach ($compare as $item):
                ?>
                <p class="gap">
                    <label for="商品分类"><?php echo $this->getName($tableName, $item['column_comment']) ?>:</label>
                    <select class="form_product"name="<?php echo $item['column_name'] ?>" >
                        <option value="null" selected="selected">选择</option>
                        <?php
                        $table = $this->getTable($item['column_comment']);
                        echo "<?php \$_s = $table::model()->findAll() ; foreach(\$_s as \$item) echo \"<option value='{\$item['id']}'>{\$item['name']}</option>\" ?>";
                        ?>
                    </select>
                </p>
            <?php endforeach; ?>

            <?php
            //时间查询
            foreach ($time as $item):
                ?>                
                <p class="gap" style="margin-right:10px;">
                    <label for="开始日期"><?php echo $this->getName($tableName, $item['column_comment']) ?>：</label>
                    <span class="qdate"> <a href="javascript:void(0)" onclick="WdatePicker({el: $dp.$('begin_<?php echo $item['column_name'] ?>'), dateFmt: 'yyyy-MM-dd', position: {left: -1, top: -1}})" class="btn_date"></a>
                        <input type="text"  onFocus="WdatePicker({dateFmt: 'yyyy-MM-dd', position: {left: -1, top: -1}})" class="qdate_form_text" name="begin_<?php echo $item['column_name'] ?>" value="<?php echo "<?php "; ?>echo $model['begin_<?php echo $item['column_name'] ?>']<?php echo "?>"; ?>" id="begin_<?php echo $item['column_name'] ?>"  maxlength="10" readonly="readonly" />
                    </span> </p>
                <p class="gap">
                    <label for="结束日期" style="width:22px;">至：</label>
                    <span class="qdate"> <a href="javascript:void(0)" onclick="WdatePicker({el: $dp.$('end_<?php echo $item['column_name'] ?>'), dateFmt: 'yyyy-MM-dd', position: {left: -1, top: -1}})" class="btn_date"></a>
                        <input type="text"  class="qdate_form_text" onFocus="WdatePicker({dateFmt: 'yyyy-MM-dd', position: {left: -1, top: -1}})" id="end_<?php echo $item['column_name'] ?>" name="end_<?php echo $item['column_name'] ?>" value="<?php echo "<?php "; ?>echo $model['end_<?php echo $item['column_name'] ?>']<?php echo "?>"; ?>" maxlength="10" readonly="readonly"/>
                    </span> </p>
            <?php endforeach; ?>    
            <p class="gap2"><a href="#" title="查询" id="submit" onclick="$('form').submit();" class ="btn_fenpei">查询</a> </p>
        </div>
    </form>
</div>
<!--顶部搜索 区End-->


<!--查询结果显示开始-->
<div class="data_table">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_s1">
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
            foreach($list as $key => $item):
            $class = $key % 2 == 0 ? '' : 'class="even"';
            <?php echo "?>\n"; ?>
            <tr <?php echo "<?php "; ?> echo $class <?php echo "?>"; ?>>
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
                        } else if ($this->searchFile($item['column_comment'])) {
                           echo "<img height='80px' src='<?php echo \$item['{$item['column_name']}'] ?>' />";
                        }
                        ?></td>
                    <?php
                endforeach;
                ?>
                <td style="text-align:center;" width="330">
                    <!--<a class="btn btn-danger" href="/<?php echo $controller_id ?>/view/id/<?php echo "<?php "; ?>echo $item['id'] <?php echo "?>"; ?>" >查看<?php echo $tableName ?></a>-->
                    <a class="btn btn-danger" href="/<?php echo $controller_id ?>/update/id/<?php echo "<?php "; ?>echo $item['id'] <?php echo "?>"; ?>" >编辑查看</a>
                    <a class="btn btn-danger" href="/<?php echo $controller_id ?>/del/id/<?php echo "<?php "; ?>echo $item['id'] <?php echo "?>"; ?>" >
                        <?php if($del_field){?><?php echo "<?php "; ?>echo $item['<?php echo $del_field ?>']?'<font color="green">已生效</font>':'<font color="red">已失效</font>' <?php echo "?>"; ?><?php }else{?>删除<?php }?></a>
                </td>
            </tr>
            <?php echo "<?php "; ?>
            endforeach;
            <?php echo "?>\n"; ?>


        </tbody>
    </table>
</div>
<!--查询结果显示结束-->

<!--分页开始-->
<div class="esop_pagenumQu">
    <div class="pagenumQu" id="pager">
        <?php echo "<?php "; ?>
        $this->widget('CLinkPager', array(
        'cssFile' => '',
        'header' => '<font style="position: relative; top: 50px;">共' . $pager->getItemCount() . '条，当前页显示第' . ($pager->getCurrentPage() * $pager->pageSize + 1) . '-' . ($pager->getCurrentPage() + 1) * $pager->pageSize . '条 &nbsp;</font>',
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

<script type="text/javascript">
    $(function() {
        //相关选中事件
<?php foreach ($compare as $item): ?>
            $('select[name=<?php echo $item['column_name'] ?>]').val('<?php echo "<?php "; ?>echo $model['<?php echo $item['column_name'] ?>'] <?php echo "?>"; ?>');
<?php endforeach ?>
    });
    //分页跳转
//    $('#submit,#pager a').click(function() {
//        $('form').attr('action', this.href).submit();
//        return false;
//    });
</script>    
