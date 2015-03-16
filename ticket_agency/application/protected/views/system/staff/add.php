<?php
$this->breadcrumbs = array('系统管理', '员工管理');
?>
<link rel="stylesheet" type="text/css" href="/css/validationEngine.jquery.css" />    
<div class="contentpanel">
    <?php
    if ($showError):
        ?>
        <div id="show_msg">
            <div class="alert alert-error">
                <button data-dismiss="alert" class="close" type="button">×</button>该帐号已经存在
            </div>
        </div>
        <script type="text/javascript">
            $(function() {
                window.setTimeout(function() {
                    $('#show_msg').slideUp(1000);
                }, 2000)
            });
        </script>   
        <?php
    endif;
    ?>


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">用户信息</h4>
                </div><!-- panel-heading -->

                <div class="panel-body nopadding">

                    <form class="form-horizontal form-bordered " id="repass-form" method="post" action="">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 账号</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="account" id="account"/>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 密码</label>
                            <div class="col-sm-4">
                                <input type="password" placeholder="" class="validate[required,minSize[6],maxSize[16]] form-control" name="password" id="password"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 员工姓名</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="name" id="name"/>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 手机号码</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required,custom[mobile]] form-control" name="mobile" id="mobile"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 角色</label>
                            <div class="col-sm-4">
                                <select class="validate[required] select2 col-sm-12" data-placeholder="Choose One" name="role_id" id="role_id">
                                    <option value=''>选择</option>
                                    <?php
                                    $data = Role::model()->findAllByAttributes(array('status' => 1, 'organization_id' => Yii::app()->user->org_id));
                                    foreach ($data as $item):
                                        ?>
                                        <option style="height: 37px" value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 状态</label>
                            <div class="col-sm-4">
                                <select class="validate[required] select2 col-sm-12" data-placeholder="Choose One" name="status" id="status">
                                    <option value="1" style="height: 37px">启用</option>
                                    <option value="0" style="height: 37px">禁用</option>
                                </select>
                            </div>
                        </div><!-- form-group -->
                        <div class="panel-footer">
                            <button class="btn btn-primary mr5" type="button" id="buttomsub">保存</button>
                            <a class="btn btn-default" href="/system/staff/">取消</a>
                        </div>
                    </form>          
                </div><!-- panel-body -->      
            </div><!-- panel -->
        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script src="/js/jquery.validationEngine.js"></script>  
<script src="/js/jquery.validationEngine-zh-CN.js"></script>
<script>
            $(function() {
                $('#buttomsub').click(function() {
                    $('#buttomsub').attr('disabled', 'disabled');
                    $('#repass-form').validationEngine({
                        promptPosition: 'topLeft',
                        addFailureCssClassToField: 'error',
                        autoHidePrompt: true,
                        autoHideDelay: 3000
                    });

                    if ($('#repass-form').validationEngine('validate') === true) {
                        $.post('/system/staff/add/', $('#repass-form').serialize(), function(data) {
                            alert(data.msg);
                            if (data.error == 0) {
                                location.href = '/system/staff/';
                            } else {
                                $('#buttomsub').removeAttr('disabled');
                            }
                        }, 'json');
                    } else {
                        $('#buttomsub').removeAttr('disabled');
                    }
                    return false;
                });

                $('[name=account]').blur(function() {
                    if ($('[name=account]').val() === '') {
                        return false;
                    }
                    $.post('/system/staff/accountExist', $(this).serialize(), function(data) {
                        if (data.error === 0) {
                            alert(data.msg);
                        }
                    }, 'json');
                });
            });

</script>
