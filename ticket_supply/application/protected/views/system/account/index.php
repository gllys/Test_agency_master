
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
                    <ul class="list-inline">
                        <li><h4 class="panel-title">密码修改</h4></li>
                        <li><a href="/order/history/help?#9.4" title="帮助文档" class="clearPart"
                               target="_blank">查看帮助文档</a> </li>
                    </ul>
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
                                        <input type="password" autocomplete="off" id="old_pass" name="user[oldpass]" tag="原密码" class="validate[required,minSize[6],maxSize[16]] error form-control">
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>输入新密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" autocomplete="off" id="password" name="user[password]" tag="新密码" class="validate[required,minSize[6],maxSize[16]] form-control error">
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>确认新密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" autocomplete="off" name="user[confirm_password]" tag="确认新密码" class="validate[required,equals[password],minSize[6],maxSize[16]] form-control error">
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
            $('#show_msg').empty();
            var obj = $('#repass-form');
            if (obj.validationEngine('validate') == true) {
                $.post('/system/account/index', {"old": $('#old_pass').val(), "assword": $('#password').val()}, function(data) {
                    if (data.errors) {
                        var tmp_errors = '';
                        $.each(data.errors, function(i) {
                            tmp_errors += data.errors[i];
                        });
                        var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button>' + tmp_errors + '</div>';
                        $('#show_msg').html(warn_msg);
                    } else {
                        type_msg = '密码修改成功';
                        var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>' + type_msg + '! </div>';
                        $('#show_msg').html(succss_msg);
                        setTimeout("location.href= '/#'+'/system/account/'", '2000');

                    }
                }, 'json');
            };

            return false;
        });

    });
</script>             
       
