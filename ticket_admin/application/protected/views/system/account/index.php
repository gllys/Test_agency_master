
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
                    <h4 class="panel-title">密码修改</h4>
                </div>
                <!-- panel-heading -->
                <div id="show_msg"></div>
                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="repass-form">
                        <div class="row">
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>输入原密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" autocomplete="off" id="old_pass" name="user[oldpass]" tag="原密码" class="validate[required,minSize[6],maxSize[16],custom[onlyLetterNumber]] error form-control" onkeypress="return IsOnlyNumLetter(event)">
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>输入新密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" autocomplete="off" id="password" name="user[password]" tag="新密码" class="validate[required,minSize[6],maxSize[16],custom[onlyLetterNumber]] form-control error" onkeypress="return IsOnlyNumLetter(event)">
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>确认新密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" autocomplete="off" name="user[confirm_password]" tag="确认新密码" class="validate[required,equals[password],minSize[6],maxSize[16],custom[onlyLetterNumber]] form-control error" onkeypress="return IsOnlyNumLetter(event)">
                                    </div>
                                </div>
                                <!-- form-group -->

                            </div>
                        </div>

                        <div class="panel-footer" style="padding:0 8% 0;">
                            <div class="form-actions">
                                <button class="btn btn-success" type="button" id="repass-form-button">保存修改</button>
                            </div>
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
    $(function() {
        $(document).keydown(function(e) {
            //回车键
            if (e.keyCode == 13) {
                //$('#repass-form').submit();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {

        // 表单验证
        $('#repass-form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });
        $('#repass-form-button').click(function() {
            var obj = $('#repass-form');
            if (obj.validationEngine('validate') == true) {
                $.post('/system/account/index', {"old": $('#old_pass').val(), "assword": $('#password').val()}, function(data) {
                    if (data.errors) {
                        var tmp_errors = '';
                        $.each(data.errors, function(i) {
                            tmp_errors += data.errors[i];
                        });
                        alert(tmp_errors);
                    } else {
                        alert('密码修改成功！', function() {
                            location.partReload();
                        });
                    }
                }, 'json');
            };

            return false;
        });

    });
</script>             
       
