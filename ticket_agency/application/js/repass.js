$(document).ready(function() {

// 表单验证
    $('#repass-form').validationEngine('attach', {
        promptPosition: 'topRight',
        addFailureCssClassToField: 'error',
        autoHidePrompt: true,
        autoHideDelay: 3000
    });

    $('#repass-form-button').click(function() {
//console.log(11);
        $('#show_msg').empty();
        var obj = $('#repass-form');
        if (obj.validationEngine('validate') == true) {
            $.post('/system/account/index', {"old": $('#old_pass').val(), "assword": $('#password').val()}, function(data) {
                if (data.errors) {
                    alert(data.errors);
                    var tmp_errors = '';
                    $.each(data.errors, function(i) {
                        tmp_errors += data.errors[i];
                    });
                    var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button>' + tmp_errors + '</div>';
                    $('#show_msg').html(warn_msg);
                    location.href = '#show_msg';
                } else {
                    type_msg = '密码修改成功';
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button>' + type_msg + '! </div>';
                    $('#show_msg').html(succss_msg);
                    location.href = '#show_msg';

                }
            }, 'json');
        }
        ;
        return false;
    });

});