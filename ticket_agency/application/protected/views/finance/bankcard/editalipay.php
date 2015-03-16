<div class="modal-dialog">
	  <div class="modal-content">
		  <div class="modal-header">
			  <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
			  <h4 class="modal-title">修改支付宝</h4>
		  </div>
		  <?php if($list):?>
			<?php foreach ($list as $bank_list):?>
	  		<form id="bank_mycard">
			<input type="hidden" value="<?php echo $bank_list['id']?>" name="id">
                        <input type="hidden" value="<?php echo $bank_list['type']?>" name="type">
                        
                  <div class="modal-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">支付宝账号:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control validate[required]" name="account" value="<?php echo $bank_list['account']?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">账户名:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control validate[required]" name="account_name" value="<?php echo $bank_list['account_name']?>">
					</div>
				</div>
		  </div>      

		  <div class="modal-footer">
				<button type="button" class="btn btn-success" id="edit_bank_card">修改</button>
		  </div>
	 	</form>
	 	<?php endforeach;?>
	<?php endif;?>
	  </div>
	</div>
<script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/jquery.validationEngine.js"></script>
<script src="/js/jquery.validationEngine-zh-CN.js"></script>

<script>
jQuery(document).ready(function() {
	$('#edit_bank_card').click(function(){
		if($('#bank_mycard').validationEngine('validate')==true){
		$.post('/finance/bankcard/editBank',$('#bank_mycard').serialize(),function(data){
			if(data.error===0){
                    alert('修改成功');
                    window.location.reload();
                }else{
                    alert(data.msg);
                }
            },'json')
	}
	})
});	
</script>
