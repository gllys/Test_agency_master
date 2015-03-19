$(document).ready(function() {


});

// 定义操作按钮的点击事件
function verifyNotice(msg_id, status) 
{
    $.post('index.php?c=message&a=verify', {id:msg_id,status:status},function(data){
        if(data.errors){
            var tmp_errors = '';
            $.each(data.errors, function(i, n){
                tmp_errors += n;
            });
            var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
            $('#show_msg').html(warn_msg);
            location.href='#show_msg';
        }else if(data['data'][0]['verify_time']){
            var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
            $('#show_msg').html(succss_msg);
            location.href='#show_msg';
            setTimeout("location.href='message_notice.html'", '2000');
        }
    }, "json");
    return false;
}

// 定义操作按钮的删除事件
function deleteNotice(msg_id, status){
	$.post('index.php?c=message&a=deleteNotice',{id:msg_id,status:status},function(data){
		if(data.errors){
            var tmp_errors = '';
            $.each(data.errors, function(i, n){
                tmp_errors += n;
            });
            var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
            $('#show_msg').html(warn_msg);
            location.href='#show_msg';
        }else if(data['data'][0]['verify_time']){
            var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
            $('#show_msg').html(succss_msg);
            location.href='#show_msg';
            setTimeout("location.href='message_notice.html'", '2000');
        }
    }, "json");
    return false;
}