<!--主体内容-开始-->
<!--二级目录 区Start-->
<div class="tongj_cont clearfix">
    <ul id="op_nav">
        <li class="ton_none"><a href="/<?php echo $controller_id ?>/" title="<?php echo $tableName ?>管理"><?php echo $tableName ?>管理 </a></li>
        <li class="ton_now"><a href="/<?php echo $controller_id ?>/create/" title="添加<?php echo $tableName ?>">添加<?php echo $tableName ?></a></li>
    </ul>
</div>
<!--二级目录 区End-->

<!--商品内容显示开始-->
<div class="sp_showQU">
    <form action="#" id="form" method="post" enctype="multipart/form-data">    
        <table border="0" cellspacing="0" cellpadding="0" class="sp_shuru_biao">
            <tbody>
                <?php
                foreach ($fields as $item):
                    if (!in_array($item['column_name'], $create))
                        continue;
                    ?>
                    <tr>
                        <td class="lie1"><?php echo $this->getName($tableName, $item['column_comment']) ?>:</td>
                        <td class="lie2">
                            <?php
                            if ($this->searchTime($item['column_comment'])) { //时间
                                ?>    
                                <input name="<?php echo $item['column_name'] ?>" readonly="readonly" onFocus="WdatePicker({startDate: '%y-%M-01 HH:mm', position: {left: -1, top: -2}, dateFmt: 'yyyy-MM-dd HH:mm', minDate: '%y-%M-%d'})" type="text" class="sp_sxming" value="" />
                            <?php } else if ($this->searchLog($item['column_comment'])) { //日志 ?>
                                <textarea name="<?php echo $item['column_name'] ?>"></textarea>
                            <?php } else if ($this->searchFile($item['column_comment'])) { //文件 ?>
                                <input type="hidden" class="sp_sxming" name="<?php echo $item['column_name'] ?>" />
                                <button id="<?php echo $item['column_name'] ?>">上传图片</button>
                                <span class="icon_bitianfu">*</span><span class="wanr_txt"></span>
                                <img src="" style="vertical-align: middle;" id="<?php echo $item['column_name'] ?>_img" height="100px" />
                                <script type="text/javascript">
                                    //上传图片
                                window.imgField  = '' ; 
                                new AjaxUpload('#<?php echo $item['column_name'] ?>', {
                                    action: 'http://v0.api.upyun.com/<?php echo "<?php "; ?> echo Yii::app()->upyun->bucket <?php echo "?>"; ?>/',
                                    name: 'file',
                                    onSubmit: function(file, ext) {
                                    //检查图片格式是否正确
                                    if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                                        alert('您上传的图片格式不正确');
                                        return false;
                                }
                                this.setData(<?php echo "<?php "; ?> echo Yii::app()->upyun->getCode() <?php echo "?>"; ?>);
                                window.imgField = '<?php echo $item['column_name'] ?>' ;
                                },
                                onComplete: function(file, data) {
                                }
                                });

                                window.upload_callback = function(data) {
                                    if(data.status!=200){
                                    alert('图片上传出错');
                                    return false;    
                                    }       
                                    $('input[name='+window.imgField+']').val(data.msg);
                                    $('#'+window.imgField+'_img').attr('src',data.msg);
                                }
                             </script>
                             <?php
                            } else if ($this->searchRadio($item['column_comment'])) { //单选 
                                $table = $this->getTable($item['column_comment']);
                                echo "<?php \$_list = $table::model()->findAll() ; ?>\n";
                                ?>
                                <?php echo "<?php "; ?>
                                foreach($_list as $key=>$item):
                                <?php echo "?>\n"; ?>
                                &nbsp;<input style="opacity: 3;margin: 0;" name="<?php echo $item['column_name'] ?>"  <?php echo "<?php "; ?> if($item['id']==<?php echo $item['column_default'] ? $item['column_default'] : 0 ?>):<?php echo "?>"; ?> checked="checked" <?php echo "<?php "; ?> endif; <?php echo "?>"; ?> type="radio" value="<?php echo "<?php "; ?> echo $item['id'] <?php echo "?>"; ?>" />
                                             <font style="position:relative;top:2px;"><?php echo "<?php "; ?> echo $item['name'] <?php echo "?>"; ?></font>
                                             <?php echo "<?php "; ?>
                                endforeach;
                                <?php echo "?>\n"; ?>
                                <?php
                            } else if ($this->searchSelect($item['column_comment'])) { //下拉
                                $table = $this->getTable($item['column_comment']);
                                echo "<?php \$_list = $table::model()->findAll() ; ?>\n";
                                ?>
                                <select class="form_product" name="<?php echo $item['column_name'] ?>">
                                    <?php echo "<?php "; ?>
                                    foreach($_list as $key=>$item):
                                    <?php echo "?>\n"; ?>
                                    <option value="<?php echo "<?php "; ?> echo $item['id'] <?php echo "?>"; ?>" <?php echo "<?php "; ?> if($item['id']==<?php echo $item['column_default'] ? $item['column_default'] : 0 ?>):<?php echo "?>"; ?> selected="selected" <?php echo "<?php "; ?> endif; <?php echo "?>"; ?> ><?php echo "<?php "; ?> echo $item['name'] <?php echo "?>"; ?></option>
                                    <?php echo "<?php "; ?>
                                    endforeach;
                                    <?php echo "?>\n"; ?>
                                </select>  
                            <?php } else { //其它   ?>
                                <input type="text" class="sp_sxming" name="<?php echo $item['column_name'] ?>" value="<?php echo $item['column_default'] ?>"/>
                            <?php } ?>

                            <?php
                            $isWrite = $item['column_default'] == '' && $item['is_nullable'] != 'YES';
                            ?>
                            <span class="icon_bitianfu"><?php if ($isWrite) echo '*' ?></span><span class="wanr_txt"><?php //if($isWrite)echo '注：'.$this->getName($tableName, $item['column_comment']).'必填！'      ?></span>
                        </td>
                        </tr>       
                    <?php endforeach; ?>
                <tr>
                    <td class="lie1">&nbsp;</td>
                    <td class="lie2"><input id="submit" type="submit"  class="but_xz" value="确定" title="确定"/></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<!--商品内容显示结束-->

<script type="text/javascript">
    $(function() {
        //post提交后,记住已填信息
        <?php echo "<?php "; ?>
         if(!empty($_GET)){
             foreach($_GET as $key=>$val):
         <?php echo "?>\n"; ?>     
         $('form [name=<?php echo "<?php "; ?>  echo $key <?php echo "?>"; ?>]').not(":radio").val('<?php echo "<?php "; ?> echo $val <?php echo "?>"; ?>') ;        
	 $(':radio[name=<?php echo "<?php "; ?>  echo $key <?php echo "?>"; ?>][value="<?php echo "<?php "; ?> echo $val <?php echo "?>"; ?>"]').attr('checked',true);
         $('#<?php echo "<?php "; ?>  echo $key <?php echo "?>"; ?>_img').attr('src','<?php echo "<?php "; ?> echo $val <?php echo "?>"; ?>');
         <?php echo "<?php "; ?>
             endforeach;
         }
         <?php echo "?>\n"; ?>
        
        //错误验证
        function is_null($name) {
            if ($.trim($('input[name=' + $name + ']').val()) == '')
                return true;
            return false;
        }

        function is_num($name) {
            if (/^\d+$/.test($('input[name=' + $name + ']').val()))
                return true;
            return false;
        }
        $('#form').submit(function() {
        <?php
        foreach ($fields as $item):
        if (!in_array($item['column_name'], $create)) {
            continue;
        }
        if ($item['column_default'] || $item['is_nullable'] == 'YES') {
            continue;
        }
        ?>
          if (is_null('<?php echo $item['column_name'] ?>')) {
                alert('<?php echo $this->getName($tableName, $item['column_comment']) ?>不能为空');
                return false;
           }   
         <?php            
        endforeach;
         ?>
            
            //if($('textarea[name=details]').val().length>1000){alert('礼包详情1000字符以内') ;return false;}   
        });
    });
</script>


