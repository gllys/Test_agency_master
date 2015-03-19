//添加一条记录
$('#ticket-type-form-button').click(function() {
	$('#ticket-type-form').ajaxSubmit({dataType: 'json',success: function(data){
		if(data.errors){
			var tmp_errors = '';
			$.each(data.errors, function(i, n){
				tmp_errors += n;
			});
			alert('添加失败!'+tmp_errors);
		}else if(data.succ){
			alert('添加成功!');
			setTimeout("location.href='help_type.html'", 2000);
		}
	}});
	return false;
});

//更新一条记录
function update_type(id, name, type){
	var post_id = id;
	var post_name = $('#'+name).val();
	var post_type = $('#'+type).val();

	$.post('index.php?c=help&a=updateType', { name: post_name, type: post_type, id: post_id },function(data){
		if(data.errors){
			var tmp_errors = '';
			$.each(data.errors, function(i, n){
				tmp_errors += n;
			});
			alert('更新失败!'+tmp_errors);
		}else if(data.succ){
			alert('更新成功!');
			setTimeout("location.href='help_type.html'", 2000);
		}
	},'json');
}

//删除一条记录
function delete_type(id){
	if(window.confirm('你确定要删除此条记录吗？')){
		$.get('index.php?c=help&a=deleteType&id='+id,function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				alert('删除失败!'+tmp_errors);
			}else if(data.succ){
				alert('删除成功!');
				setTimeout("location.href='help_type.html'", 2000);
			}
		},'json');
	}else{
		return false;
	}
}
