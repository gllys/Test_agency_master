$(document).ready(function(){

    // 表单验证
    $('#help_add_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })

    $('#help_update_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })

    
    $('#btn-add').click(function(){
        var obj = $('#help_add_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=help&a=saveFile', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>保存成功!</strong> 2 秒后跳转到资料列表页面..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='help_file.html'", 2000);
                }
            },"json");

        };
        return false;
    })

    $('#btn-edit').click(function(){
        var obj = $('#help_update_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=help&a=save', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>更新失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>更新成功!</strong> 2 秒后跳转到文档列表页面..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='help_lists.html'", 2000);
                }
            },"json");

        };
        return false;
    })


});

function deleteHelp(id)
{
    if (window.confirm('确定要删除么？')) {
        $.post('index.php?c=help&a=delete', {id: id},function(data){
            if(typeof data.errors != 'undefined'){
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>删除失败!'+data.errors.msg+'</div>';
                $('#show_msg').html(warn_msg);
            }else{
                var succss_msg = '<div class="alert alert-success"><strong>删除成功!</strong> 2 秒后跳转到文档列表页面..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.href='landscape_equipments.html'", 2000);
            }
        },"json");
    }
}

function deleteFile(id)
{
    if (window.confirm('确定要删除么？')) {
        $.post('index.php?c=help&a=deleteFile', {id: id},function(data){
            if(typeof data.errors != 'undefined'){
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>删除失败!'+data.errors.msg+'</div>';
                $('#show_msg').html(warn_msg);
            }else{
                var succss_msg = '<div class="alert alert-success"><strong>删除成功!</strong> 2 秒后跳转到资料列表页面..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.href='help_file.html'", 2000);
            }
        },"json");
    }
}
//上传帮助文档
	$('#btn-upload').click(function() {
        $('#help_upload_form').ajaxSubmit({dataType: 'json',success: function(data){

            if(data.errors){
                var tmp_errors = '';
                $.each(data.errors, function(i, n){
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                $('#show_msg').html(warn_msg);
                location.href='#show_msg';
            }else if(data['data'][0]['id'] && data['data'][0]['url']){
                $("input[name='file_id']").val(data['data'][0]['id']);
            }
        }});
    });
