<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h6 id="modal-formLabel">还款</h6>
</div>
<div id="show_msg">
</div>
<div class="modal-body">
	<form action="index.php?c=bill&a=uploadProve" method="post" id="file-upload-form" style="background:#fff">
		<table class="table table-normal">
			<tbody>
				<tr><th width="100">结算单号：</th><td><?php echo $billInfo['id'];?></td></tr>
				<tr><th>账单类型：</th><td><?php if($billInfo['bill_type'] == 'credit'){echo '信用支付应付账款';}else{echo '在线支付应付账款';}?></td></tr>
				<tr><th>账单日期：</th><td><?php echo $billInfo['created_at'];?></td></tr>
				<tr><th>应付金额：</th><td>￥<?php echo $billInfo['bill_amount'];?></td></tr>
				<!--tr><th>上传凭证：</th><td><input type="file" name="attachments"></td></tr-->
			</tbody>
		</table>
		<input type="hidden" name="bill_id" value="<?php echo $billInfo['id']?>">
		<div style="text-align:center;padding:20px 0;"><button class="btn btn-green" type="button" id="file-upload-button">确认还款</button></div>
	</form>
</div>
<script>
$('#file-upload-button').click(function() {
//	$('#file-upload-form').ajaxSubmit({dataType: 'json',success: function(data){
//		if(data.errors){
//			var tmp_errors = '';
//			$.each(data.errors, function(i, n){
//				tmp_errors += n;
//			});
//			var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
//			$('#show_msg').html(warn_msg);
//			location.href='#show_msg';
//		}else if(data['data'][0]['id']){
//			var succss_msg = '<div class="alert alert-success"><strong>账款单'+data['data'][0]['id']+'上传凭证成功</strong></div>';
//			$('#show_msg').html(succss_msg);
//			location.href='#show_msg';
//			setTimeout("location.href='bill_payable.html'", 2000);
//		}
//	}});
       var obj = $('#file-upload-form');
        
            $.post('index.php?c=bill&a=setProve', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>打款失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>打款成功!</strong> 2 秒后跳转到机构列表页..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='bill_payable.html'", 3000);
                }
            },"json");

    
        return false; 
});
</script>