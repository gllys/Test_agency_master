//省切换
$('#province').change(function(){
	var code = $(this).val();
	$('#uniform-city span:first-child').html('市');
	$('#uniform-area span:first-child').html('县');
	$('#area').html('<option value="__NULL__">县</option>');
	if(code == '__NULL__'){
		$('#city').html('<option value="__NULL__">市</option>');
	}else{
		$.get('index.php?c=ajax&a=getAreaChildByCode',{code:code, type:"city"}, function(data){
			$('#city').html(data);
		});
	}
});

//市切换
$('#city').change(function(){
	var code = $(this).val();
	$('#uniform-area span:first-child').html('县');
	if(code == '__NULL__'){
		$('#area').html('<option value="__NULL__">县</option>');
	}else{
		$('#area').html('<option value="__NULL__">县</option>');
		$.get('index.php?c=ajax&a=getAreaChildByCode',{code:code, type:"area"}, function(data){
			$('#area').html(data);
		});
	}
});


//删除一条记录
function common_delete(id,target_url,redirect_url){
	if(window.confirm('你确定要删除此条记录吗？')){
		$.post(target_url,{id:id},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				tmp_errors = '删除失败!'+tmp_errors;
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#show_msg').html(warn_msg);
				location.href='#show_msg';
			}else if(data.succ){
				var succss_msg = '<div class="alert alert-success"><strong>删除成功!</strong></div>';
				$('#show_msg').html(succss_msg);
				location.href='#show_msg';
				setTimeout("location.href='"+redirect_url+"'", 2000);
			}
		},'json');
	}else{
		return false;
	}
}

//判断一个值是否在数组中
function in_array(needle,array,bool){
	if(typeof needle=="string"||typeof needle=="number"){
		var len = array.length;
		for(var i=0;i < len;i++){
			if(needle == array[i]){
				if(bool){
					return i;
				}
				return true;
			}
		}
		return false;
	}    
}

//右键取消弹出框
 /**鼠标事件隐藏弹出框**/
  $(document).bind('mouseup',function(){
		$('div.popover').hide() ;
  }) ;
  /*取消自身隐藏*/
  $(document).delegate("div.popover",'mouseup',function(){
		   return false;
  });