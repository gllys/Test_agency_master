<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"  aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">还款</h4>
    <div id="show_msg"></div>
</div>
<div class="modal-body">
	<form action="/finance/payable/uploadprove" method="post" id="file-upload-form" style="background:#fff">
		<table class="table table-normal">
			<tbody>
				<tr><th width="100">结算单号：</th><td><?php echo $billInfo['id'];?></td></tr>
				<tr><th>账单类型：</th>
                    <td><?php if($billInfo['bill_type'] == 'credit'){echo '信用支付应付账款';}else{echo '在线支付应付账款';}?></td>
                </tr>
				<tr><th>账单日期：</th><td><?php echo $billInfo['created_at'];?></td></tr>
				<tr><th>应付金额：</th><td>￥<?php echo $billInfo['bill_amount'];?></td></tr>
				<!--tr><th>上传凭证：</th><td><input type="file" name="attachments"></td></tr-->
			</tbody>
		</table>
        <input type="hidden" name="bill_id" value="<?php echo $billInfo['id']?>">
		<div style="text-align:center;padding:20px 0;">
            <button class="btn btn-primary btn-sm" type="button" id="file-upload-button" <?php if(1 == $billInfo['pay_status']){ echo 'disabled="disabled"';} ?>>确认打款</button>
        </div>
	</form>
</div>
</div></div>
<script>
$('#file-upload-button').click(function() {
    $('#file-upload-button').attr('disabled',"true");
    
    var obj = $('#file-upload-form');
    $.post('/finance/payable/setprove', obj.serialize(),function(data){
        if(typeof data.errors != 'undefined'){
            alert(data.errors);
             $('#file-upload-button').attr('disabled',"false");
        }else{
            alert('打款成功!', function() {
                location.partReload();
            });
        }
    },"json");


    return false; 
});
</script>