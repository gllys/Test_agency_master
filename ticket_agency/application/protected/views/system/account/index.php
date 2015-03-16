<?php
$this->breadcrumbs = array('系统管理', '修改密码');
?>
  <style>
            .form-control{width:300px;}
            .col-lg-10{float: left;width: 400px;display: block;}
             .col-lg-10 input{float: left;display: block;}
             .status-error{ color:red;}
        </style>   
<div class="main-content" style="padding-top:40px;">
    <div id="show_msg"></div>
    <div class="container-fluid padded" >
        <div class="box">
            <div class="box-content" style="width:600px">
                <form id="repass-form" action="" method="post">
                    <div class="panel-body">

                        <div class="form-group">
                            <label class="control-label col-lg-2"><strong class="status-error">*</strong>输入原密码 <span  class="note"></span></label>

                            <div class="col-lg-10">
                                <input type="password" class="validate[required,minSize[6],maxSize[16]] error form-control" name="user[oldpass]" id="old_pass" autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-lg-2"><strong class="status-error">*</strong>输入新密码 <span class="note"></span></label>
                            <div class="col-lg-10">
                                <input type="password" class="validate[required,minSize[6],maxSize[16]] form-control error" name="user[password]" id="password" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2"><strong class="status-error">*</strong>确认新密码 <span class="note"></span></label>
                            <div class="col-lg-10">
                                <input type="password" class="validate[required,equals[password],minSize[6],maxSize[16]] form-control error" name="user[confirm_password]" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-success" type="button" id="repass-form-button">保存修改</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


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
                    // alert(warn_msg);
                    $('#show_msg').html(warn_msg);
                     setTimeout("location.href='/system/account/'", '2000');
                } else {
                    type_msg = '密码修改成功';
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>' + type_msg + '! </div>';
                    $('#show_msg').html(succss_msg);
                   // location.href = '#show_msg';
                   setTimeout("location.href='/system/account/'", '2000');

                }
            }, 'json');
        };

        return false;
    });

});
</script>             
       
