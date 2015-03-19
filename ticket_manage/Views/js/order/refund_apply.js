$(document).ready(function() {
	function selectNum(){
		var length = $('#select2 option').length;
		$('#selectnum').html(length);
	}
	$('#select-add').click(function(){
		var options = $('#select1 option:selected');
		var remove = options.remove();
		remove.appendTo('#select2');
		selectNum();
		return false;
	})
	  
	$('#select-remove').click(function(){  
		var removeOptions = $('#select2 option:selected');  
		removeOptions.appendTo('#select1');
		selectNum();
		return false;
	})
	  
	$('#select-addall').click(function(){  
		var options = $('#select1 option');  
		options.appendTo('#select2');
		selectNum();
		return false;
	})
	  
	$('#select-removeall').click(function(){
		var options = $('#select2 option');  
		options.appendTo('#select1');
		selectNum();
		return false;
	})
	  
	$('#select1').dblclick(function(){  
		var options = $('option:selected', this);
		options.appendTo('#select2');
		selectNum();
		return false;
	})
	  
	$('#select2').dblclick(function(){  
		$('#select2 option:selected').appendTo('#select1');
		selectNum();
		return false;
	})

	$('#refund-apply-button').click(function(){
		$('#model_show_msg').html('');
		var result = checkValid();
		if (result) 
		{
			var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+result+'</div>';
			$('#model_show_msg').html(warn_msg);
			$('#refund_apply_num').focus();
			return false;
		}
		$('#refund-apply-form').ajaxSubmit({dataType: 'json',success: function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'，申请退票失败！</div>';
				$('#model_show_msg').html(warn_msg);
			}else if(data['data'][0]['id']){
				var type_msg = '申请退票成功';
				var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>'+type_msg+'!</strong></div>';
				$('#model_show_msg').html(succss_msg);
				setTimeout(function(){window.location.reload();}, 2000);
			}
		}});
		return false;
	});
});

//define check refund ticket number function
var checkValid = function()
	{
		var refundInput = $('#refund_apply_num');
		if (refundInput.val() == '') {
			return '退票数量不能为空';
		}
		var maxNum = parseInt(refundInput.attr('max'));
		var curNum = parseInt(refundInput.val());
		if (curNum > maxNum)
		{
			return '申请退票数量的上限为：'+ maxNum + '，请更新退票数量！';
		} else if (curNum == 0) {
			return '退票数量不能为0 !';
		}
		return false;
	}


