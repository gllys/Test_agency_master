<?php
// var_dump($product);
?>
<div class="modal-dialog modal-lg" style="width: 1000px; margin-top:10%;">
	<div class="modal-content">
		<form id="form" action="#" method="POST">
			<div class="modal-body">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4>确认下架<span style="color:red;"><<?= $product['organization']['name'] ?>></span>的产品<span style="color:red;"><<?= $product['name'] ?>></span>吗？一旦强制下架，供应商不可上架此产品！</h4>
						<h7 class="panel-title">强制下架理由:</h7>
					</div>
					<div class="panel-body">
						<input name="id" type="hidden" value="<?= $product['id'] ?>" />
						<textarea rows="2" id="note" name='force_out_remark' class="form-control" placeholder="限定200字以内，不能为空" maxlength="200"></textarea>
					</div>
					<!-- panel-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" id="confirm_force_out" type="button">确认下架</button>
				<button data-dismiss="modal" class="btn btn-primary" onclick="return false;" id="close" type="button">取消操作</button>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
    $(function() {
        
        // 强制下架
        $('#confirm_force_out').click(function() {
            var id = $('input[name="id"]').val();
            var forceOutRemark = $('textarea').val();
			if(forceOutRemark == '') {
				alert('强制下架的原因不能为空');
				return false;
			}
            $.post('/agency/product/forceOut', {id:id,force_out:1, force_out_remark:forceOutRemark}, function(data) {
            	 if (data.error) {
	  	               alert('强制下架成功', function(){window.location.reload();});
	  	           } else {
	  	               alert(data.msg, function(){window.location.reload();});
	  	           }
            }, 'json');
        });
        
    });
    
</script>