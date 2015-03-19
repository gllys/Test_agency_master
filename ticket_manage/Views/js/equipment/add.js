/**
 * Created by yuanwei on 13-12-24.
 */
$(document).ready(function(){

    // 表单验证
    $('#equipement_add_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })

    $('#equipement_update_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })
    
    //添加闸机
    $('#btn-add').click(function(){
        var obj = $('#equipement_add_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=landscape&a=saveEquip', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>添加设备失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>保存成功!</strong> 2 秒后跳转到设备绑定景区页面..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='landscape_landscape_"+data['data'][0]['id']+".html'", 2000);
                }
            },"json");

        };
        return false;
    })

    //编辑闸机
    $('#btn-edit').click(function(){
        var obj = $('#equipement_update_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=landscape&a=upEquip', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>更新设备失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>更新成功!</strong> 2 秒后跳转到设备管理页..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='landscape_equipments.html'", 2000);
                }
            },"json");

        };
        return false;
    })

});

//删除设备
function delEquip(eid)
{
    if (window.confirm('确定要删除么？')) {
        $.post('index.php?c=landscape&a=delEquip', {id: eid},function(data){
            if(typeof data.errors != 'undefined'){
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>删除设备失败!'+data.errors.msg+'</div>';
                $('#show_msg').html(warn_msg);
            }else{
                var succss_msg = '<div class="alert alert-success"><strong>删除成功!</strong> 2 秒后跳转到设备管理页..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.href='landscape_equipments.html'", 2000);
            }
        },"json");
    }
}
