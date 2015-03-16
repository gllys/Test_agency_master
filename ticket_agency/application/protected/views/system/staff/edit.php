<?php
$this->breadcrumbs = array('系统管理','员工管理');
?>
<link rel="stylesheet" type="text/css" href="/css/validationEngine.jquery.css" /> 
<div class="contentpanel">
<?php
if ($showError):
    ?>
    <div id="show_msg">
        <div class="alert alert-sucess">
            <button data-dismiss="alert" class="close" type="button">×</button>保存成功
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

                    <form class="form-horizontal form-bordered" method="post" action="" id="repass-form">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 账号</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="account" value="<?php echo $user['account']; ?>"/>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 密码</label>
                            <div class="col-sm-4">
                                <input type="password" placeholder="" class="validate[minSize[6],maxSize[16]] form-control" name="password" autocomplete="off"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 员工姓名</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="name" value="<?php echo $user['name']; ?>"/>
                            </div>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 手机号码</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required,custom[mobile]] form-control" name="mobile" value="<?php echo $user['mobile']; ?>"/>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 角色</label>
                            <div class="col-sm-4">
                                <?php if(!$user['is_super']): ?>
                                <select class="validate[required] select2 col-sm-12" data-placeholder="Choose One" name="role_id">
                                    <option value='' style="height: 37px;">选择</option>
                                    <?php 
                                   // $roles = Role::model()->findAll('status=:status',array(':status'=>1));
                                    $roles = Role::model()->findAllByAttributes(array('status'=>1,'organization_id'=>Yii::app()->user->org_id)) ;
                                    $roleUser = RoleUser::model()->findByAttributes(array('uid' => $user->id));
                                    foreach($roles as $item):
                                    ?>
                                    <option value='<?php echo $item['id'] ?>' <?php if($roleUser&&$item['id']==$roleUser['role_id']): ?>selected="selected"<?php endif ?>><?php echo $item['name'] ?></option>
                                    <?php endforeach;?>
                                </select>
                                <?php else:?>
                                <select><option>系统管理员</option></select>
                                <?php endif?>
                            </div>
                            <?php if(!$user['is_super']): ?>
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 状态</label>
                            <div class="col-sm-4">
                                <select class="validate[required] select2 col-sm-12" data-placeholder="Choose One" name="status">
                                    <?php
                                    if ($user['status'] == 1) {
                                        echo "<option  style='height:37px;' value='1' selected='selected'>启用</option><option style='height:37px;' value='0' >禁用</option>";
                                    } else {
                                        echo "<option value='1' style='height:37px;' >启用</option><option style='height:37px;' value='0' selected='selected'>禁用</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php endif?>
                        </div><!-- form-group -->
                        <div class="panel-footer">
                            <button class="btn btn-primary mr5" type="">保存</button>
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
    // 表单验证
     $(function() {
            $('#repass-form').submit(function() {
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
