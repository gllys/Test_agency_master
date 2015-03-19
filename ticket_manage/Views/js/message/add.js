/**
 * Created by yuanwei on 13-12-24.
 */
$(function(){

    $('#scenic_add_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })
    
    $('#scenic_add_form').submit(function(e){
    	e.preventDefault();
        var obj = $(this);
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=message&a=addReport', obj.serialize(),function(data){

                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>回复失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>回复成功!</strong> 2 秒后跳转至建议管理页面...</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='message_suggest.html'", 3000);
                }
            },'json');

        };
        return false;
    });
});