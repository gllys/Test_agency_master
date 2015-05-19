<?php
$this->breadcrumbs = array('系统管理', '编辑角色');
?>
<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                        <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">编辑角色</h4>
                </div><!-- panel-heading -->

                <div class="panel-body nopadding">

                    <form id="form" class="form-horizontal form-bordered" action="#" method="post">
                        <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 角色名称</label>
                            <div class="col-sm-4">
                                <input name="name" type="text" maxlength="15" class="form-control validate[required]" placeholder="" value="<?php echo $model->name; ?>">
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 角色说明</label>
                            <div class="col-sm-4">
                                <textarea name="description" class="form-control validate[required,maxSize[50]]" rows="5"><?php echo $model->description; ?></textarea>
                            </div>
                        </div><!-- form-group -->



                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">角色权限设置</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <?php
                                        $titles = CreateUrl::model()->titles;
                                        $lists = CreateUrl::model()->lists;
                                        $permissions = json_decode($model['permissions'], true);
                                        $i = 0;
                                        foreach ($titles as $key => $title):
                                            $i++;
                                            ?>
                                            <tr>
                                                <td style="text-align:left;width:100px">
                                                   <div class="ckbox ckbox-default inline-block">
                                                        <input id="checkboxPrimary<?php echo $i ?>" type="checkbox" name="" value="" id="checkboxPrimary2">
                                                        <label for="checkboxPrimary<?php echo $i ?>"><?php echo $title['content'] ?></label>
                                                    </div>
                                                </td>
                                                <td style="text-align:left">
                                                    <?php
                                                    $_lists = $lists[$key];
                                                    foreach ($_lists as $item):
                                                        $i++;
                                                        ?>
                                                       <div class="ckbox ckbox-default inline-block" style=" margin-right: 15px;">
                                                            <input id="checkboxPrimary<?php echo $i ?>" type="checkbox" name="permissions[]" value="<?php echo $item['params']['href'] ?>" id="checkboxPrimary3" <?php if (in_array($item['params']['href'], $permissions)): ?>checked="checked"<?php endif; ?>>

                                                            <label for="checkboxPrimary<?php echo $i ?>"><?php echo $item['content'] ?></label>
                                                        </div> 
                                                        <?php
                                                    endforeach;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="panel-footer">
                                <button class="btn btn-primary mr5" type="submit">保存</button>
                            </div>
                    </form>          
                </div><!-- panel-body -->      



            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div>
<script type="text/javascript">
    $(document).ready(function() {

//权限设置
        $('td:nth-child(1) input').click(function() {
            if ($(this).is(':checked')) {
                $(this).parents('tr').find('td:nth-child(2) input').prop('checked', true);
            } else {
                $(this).parents('tr').find('td:nth-child(2) input').prop('checked', false);
            }
        });

        setInterval(function() {
            var trObjs = $('.table-bordered').find('tr');
            for (i in trObjs) {
                var _trObjs = trObjs.eq(i);
                var c = _trObjs.find('td:nth-child(2) input:checked').length
                var i = _trObjs.find('td:nth-child(2) input').length
                if (c == i) {
                    _trObjs.find('td:nth-child(1) input').prop('checked', true);
                } else {
                    _trObjs.find('td:nth-child(1) input').prop('checked', false);
                }
            }
        }, 200);
    });
</script>
<script src="/js/application.js" type="text/javascript"></script>
<link href="/css/formError.css" rel="stylesheet">
<script src="/js/jquery.validationEngine-zh-CN.js"></script>
<script type="text/javascript">
    $(function() {
        //表单赋值
        $('#form').submit(function() {
            // console.log(1);
            $(this).validationEngine({
                promptPosition: 'topRight',
                addFailureCssClassToField: 'error',
                autoHidePrompt: true,
                autoHideDelay: 3000
            });

            if ($(this).validationEngine('validate') === true) {
                return true;
            }
            return false;
        });
    });
</script>
