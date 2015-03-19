$(document).ready(function(){

    // 表单验证
    $('#monitor_add_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })

    $('#monitor_update_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })
    
    $('#btn-add').click(function(){
        var obj = $('#monitor_add_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=monitor&a=save', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>保存成功!</strong> 2 秒后跳转到监管机构列表页面..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='monitor_lists.html'", 2000);
                }
            },"json");

        };
        return false;
    })

    $('#btn-edit').click(function(){
        var obj = $('#monitor_update_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=monitor&a=save', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>更新信息失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>更新成功!</strong> 2 秒后跳转到监管机构列表页面..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='monitor_lists.html'", 2000);
                }
            },"json");

        };
        return false;
    })

});

function releaseMonitor(id, p_id) {
    if (window.confirm("确定要解除监管关系吗？\n解除之后可能使其成为一个孤立节点。\n推荐操作：通过编辑为其另选上级监管。")) {
        $.post('index.php?c=monitor&a=releaseMonitor', {id: id, p_id: p_id}, function (data) {
            if (typeof data.errors != 'undefined') {
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>解除失败!' + data.errors.msg + '</div>';
                $('#show_msg').html(warn_msg);
            } else {
                location.reload();
                var succss_msg = '<div class="alert alert-success"><strong>解除成功!</strong> 2 秒后更新本页..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.reload()", 2000);
            }
        }, "json");
    }
    return false;
}

function releaseScenic(id, m_id) {
    if (window.confirm("确定要解除监管关系吗？\n解除之后可能使其成为一个孤立节点。\n推荐操作：通过编辑为其另选上级监管。")) {
        $.post('index.php?c=monitor&a=releaseScenic', {id: id, m_id: m_id}, function (data) {
            if (typeof data.errors != 'undefined') {
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>解除失败!' + data.errors.msg + '</div>';
                $('#show_msg').html(warn_msg);
            } else {
                var succss_msg = '<div class="alert alert-success"><strong>解除成功!</strong> 1 秒后更新本页..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.reload()", 1000);
            }
        }, "json");
    }
    return false;
}
function catchMonitor(id, m_id, s_name, m_name) {
    if (window.confirm("确定将机构："+s_name+"\n置于监管机构："+m_name+" 的监管之下？")) {
        $.post('index.php?c=monitor&a=catchMonitor', {id: id, m_id: m_id}, function (data) {
            if (typeof data.errors != 'undefined') {
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>设置失败!' + data.errors.msg + '</div>';
                $('#show_msg').html(warn_msg);
            } else {
                var succss_msg = '<div class="alert alert-success"><strong>设置成功!</strong> 2 秒后更新本页..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.reload()", 2000);
            }
        }, "json");
    }
    return false;
}
function catchScenic(id, m_id, s_name, m_name) {
    if (window.confirm("确定将景区："+s_name+"\n置于监管机构："+m_name+" 的监管之下？")) {
        $.post('index.php?c=monitor&a=catchScenic', {id: id, m_id: m_id}, function (data) {
            if (typeof data.errors != 'undefined') {
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>设置失败!' + data.errors.msg + '</div>';
                $('#show_msg').html(warn_msg);
            } else {
                var succss_msg = '<div class="alert alert-success"><strong>设置成功!</strong> 2 秒后更新本页..</div>';
                $('#show_msg').html(succss_msg);
                setTimeout("location.reload()", 2000);
            }
        }, "json");
    }
    return false;
}
