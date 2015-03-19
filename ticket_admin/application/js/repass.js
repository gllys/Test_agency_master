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
                     setTimeout("location.href='/system/account/'", '2000');
                }
            }, 'json');
        };

        return false;
    });

});