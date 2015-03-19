$(document).ready(function() {
	function selectNum(){
		var length = $('#select2 option').length
		$('#selectnum').html(length)
	}
	$('#select-add').click(function(){
		var options = $('#select1 option:selected')
		var remove = options.remove()
		remove.appendTo('#select2')
		selectNum()
	})
	  
	$('#select-remove').click(function(){  
		var removeOptions = $('#select2 option:selected')
		removeOptions.appendTo('#select1')
		selectNum()
	})
	  
	$('#select-addall').click(function(){  
		var options = $('#select1 option')
		options.appendTo('#select2')
		selectNum()
	})
	  
	$('#select-removeall').click(function(){
		var options = $('#select2 option')
		options.appendTo('#select1')
		selectNum()
	})
	  
	$('#select1').dblclick(function(){  
		var options = $('option:selected', this)
		options.appendTo('#select2')
		selectNum()
	})
	  
	$('#select2').dblclick(function(){  
		$('#select2 option:selected').appendTo('#select1')
		selectNum()
	})


	$('#orders-num').keyup(function(){
		$(this).val($(this).val().replace(/[^\d]/g,''))
		
		var pay = $(this).val() * $('.pay-num').text()
		$('.pay').text($('.pay-num').text()+'×'+$(this).val()+'张='+pay)
	})
	

	$('.use-tickets-btn').click(function(){
		if($('#select2 option:selected').length!=0){
			if(confirm('是否使用该门票')){
				$.post( 
					'index.php?c=order&a=useTicket',
					{'tickets' : $('#select2').val()},
					function(data){
						if(data.errors){
							var tmp_errors = '';
							$.each(data.errors, function(i, n){
								tmp_errors += n;
							});
							var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
							$('#show_msg').html(warn_msg);
							location.href='#show_msg';
						}else if(data['data']){
							var type_msg = '使用门票成功';
							var succss_msg = '<div class="alert alert-success"><strong>'+type_msg+'!</strong></div>';
							$('#show_msg').html(succss_msg);
							location.href='#show_msg';
							setTimeout("location.reload()", 2000);
						}
				}, 'json');
			}
		}
		return false
	})
	
});