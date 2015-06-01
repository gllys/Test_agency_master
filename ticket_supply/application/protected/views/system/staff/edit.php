<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title">用户信息</h4>
                </div>
                <!-- panel-heading -->
                <div id="show_msg"></div>

                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="repass-form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 账号</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="账号" data-validation-engine="form-control validate[required]" class="form-control validate[required]" name="account" value="<?php echo $user['account']; ?>" readonly/>
                                        <input type="hidden" name="id" value="<?php echo $user['id']?>" >
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" placeholder="" tag="密码" data-validation-engine="form-control  validate[minSize[6],maxSize[16]]" class="form-control  validate[minSize[6],maxSize[16]]" onclick="this.name='password'" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>员工姓名</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="员工姓名" data-validation-engine="form-control validate[required]" class="form-control validate[required]" name="name" value="<?php echo $user['name']; ?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="手机号码" maxlength="11" data-validation-engine="form-control validate[required,custom[phone]]" class="form-control validate[required,custom[phone]]" name="mobile" value="<?php echo $user['mobile']; ?>"/>
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>角色</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php if(!$user['is_super']): ?>
                                                    <select class="select2 col-sm-12 form-control" tag="角色" data-prompt-position="topRight:250" name="role_id" id="role_id">
                                                        <option value=''>选择</option>
                                                        <?php
                                                        $roles = Role::model()->findAllByAttributes(array('status'=>1,'organization_id'=>Yii::app()->user->org_id)) ;
                                                        $roleUser = RoleUser::model()->findByAttributes(array('uid' => $user->id));
                                                        foreach($roles as $item):
                                                            ?>
                                                            <option tag="角色" value='<?php echo $item['id'] ?>' <?php if($roleUser&&$item['id']==$roleUser['role_id']): ?>selected="selected"<?php endif ?>><?php echo $item['name'] ?></option>
                                                        <?php endforeach;?>
                                                    </select>
                                                <?php else:?>
                                                    <select class="select2 col-sm-12 form-control validate[required]" tag="角色"><option>系统管理员</option></select>
                                                <?php endif?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>状态</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <select class="select2 col-sm-12  form-control validate[required]" tag="角色状态" data-placeholder="Choose One" id="status" name="status">
                                                    <option value="1">启用</option>
                                                    <option value="0">禁用</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer" style="padding-left:8%">
                            <button class="btn btn-primary mr20" type="button" id="buttomsub">保存</button>
                            <a class="btn btn-default" href="/system/staff/">取消</a>
                        </div>

                    </form>
                </div>
                <!-- panel-body -->
            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->
</div>
<!-- contentpanel -->

<script>
    // 表单验证
    $(function() {

        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        $('#status').select2('val',<?php echo $user['status']?>);

        $('#role_id').change(function(){
            $('#buttomsub').removeAttr('disabled');
        });

        $('#buttomsub').click(function () {
            $('#buttomsub').attr('disabled', 'disabled');
            $('#repass-form').validationEngine({
                promptPosition: 'topRight',
                addFailureCssClassToField: 'error',
                autoHidePrompt: true,
                autoHideDelay: 3000
            });

            if($('#role_id').val() == ''){
                $('#role_id').validationEngine('showPrompt','请选择角色权限','error');
                return false;
            }

            if ($('#repass-form').validationEngine('validate') === true) {
                $.post('/system/staff/saveStaff/', $('#repass-form').serialize(), function (data) {
                    if (data.error) {
                        var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+data.msg+'</div>';
                        $('#show_msg').html(warn_msg);
                        location.href= '/#'+'#show_msg';
                        $('#buttomsub').removeAttr('disabled');
                    } else {
                        var succss_msg = '<div class="alert alert-success"><strong>更新成功！</strong></div>';
                        $('#show_msg').html(succss_msg);
                        location.href= '/#'+'/system/staff/';
                    }
                }, 'json');
            } else {
                $('#buttomsub').removeAttr('disabled');
            }
            return false;
        });
    });

</script>
