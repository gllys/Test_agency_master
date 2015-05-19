<style>
                .table-bordered th {
                    line-height: 2em !important;
                }
                .table-bordered th,
                .table-bordered td {
                    vertical-align: middle !important;
                }
                .table-bordered a:hover {
                    text-decoration: none;
                }
            </style>
<div class="contentpanel">
		<link rel="stylesheet" href="/css/prettyPhoto.css">
		<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">应收账款单明细</h4>
                    </div>
                </div>

		<?php if($detail):?>
			<input type="hidden" value="<?php echo $detail['id']?>" />
			<div class="panel panel-default"  style="margin-top: 20px;">
				<div class="panel-heading">
					<h4 class="panel-title"><span class="mr20">账单日期：<?php echo $detail['created_at']?></span>  
						<span>应收账款单支付状态：
                                                  
						<?php if($detail['pay_status'] == 0 && $detail['bill_amount'] != 0):?>
							<b class="text-danger">未打款</b>
						<?php elseif($detail['bill_amount'] == 0):?>
							<b class="text-warning">无需打款</b>
						<?php else:?>
							<b class="text-success">已打款</b>
						<?php endif;?>
						</span>
					</h4>
				</div>
				
				  <table class="table table-bordered">
					<thead>
						<tr>
							<th>单号</th>
							<th>门票名称</th>
							<th>预订日期</th>
							<th>游玩日期</th>
							<th>取票人</th>
							<th>取票人手机</th>
							<th>支付金额</th>
							<th>退款金额</th>
							<th>结款金额</th>
						</tr>
					</thead>
					<tbody>
					<?php if($detail['order_list']):?>
						<?php foreach ($detail['order_list'] as $value):?>
					  <tr>
						<td><?php echo $value['order_id'];?></td>
						<td><?php echo $value['ticket_name']?></td>
						<td><?php echo $value['ordered_at']?></td>
						<td><?php echo $value['use_day']?></td>
						<td><?php echo $value['owner_name']?></td>
						<td><?php echo $value['owner_mobile']; ?></td>
						<td><?php echo $value['payed']?></td>
						<td><?php echo $value['refunded']?></td>
						<td><?php echo $value['bill_amount']?></td>
					  </tr>
						<?php endforeach;?>
					<?php endif;?>
					</tbody>
				  </table>
				</div>
				<div class="panel panel-default">
				  <table class="table table-bordered">
					<tbody>
					  <tr>
						<th style="width:100px">应收账款金额:</th>
						<td><?php echo $detail['bill_amount']?></td>
					  </tr>
					  <tr <?php if($detail['bill_amount'] == 0){echo "style='display:none'";}?>>
						<th>打款日期:</th>
						<td><?php echo $detail['payed_at'] == 0 ? '未打款' : $detail['payed_at']?></td>
					  </tr>
					<?php  if($detail['bill_type'] != 4):?>
					  <tr <?php if($detail['bill_amount'] == 0){echo "style='display:none'";}?>>

						  <th>打款凭证:</th>
							<?php
							$detail['payed_img'] = str_replace('piaowu.b0.upaiyun.com', 'upload.piaotai.com',$detail['payed_img']);
							?>
						<td>
							<div class="dropzone">
								<div class="fallback" <?php echo $detail['payed_img'] ?  '' : 'disabled'?> >
									<a target="_blank" href="<?php echo $detail['payed_img'] ?  $detail['payed_img'] : '/img/nopic
									.png'?>" rel="prettyPhoto[gallery]" class="item-media" >
										<img  src="<?php echo $detail['payed_img']?   $detail['payed_img'] :'/img/nopic.png' ?>" >
									</a>
								</div>
							</div>
						</td>
					  </tr>
					<?php  endif ;?>
					</tbody>
				  </table>
				  <div class="panel-footer">
					<a  id="bill_finish" <?php if($detail['receipt_status'] == 1 || $detail['pay_status'] == 0){echo "style='display:none'";} ?>><button class="btn btn-primary btn-sm" type="button">确认收款</button></a>
					<a  href="/finance/bill" class="btn btn-primary btn-sm">返回</a>
				  </div>
				</div>
		<?php endif;?>		
		
		
</div>
<script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
<script>
jQuery(document).ready(function() {
	$('#bill_finish').click(function(){
		$('bill_finish').attr('disabled',true);
		$.post('/finance/detail/finish',{ id: $('input[type=hidden]').val()},function(data){
			if(data.error===0){
                    alert(data.msg,function(){window.location.href = '/finance/bill';});
                }else{
                    alert(data.msg);
                }
            },'json')
	})
});	
</script>
<script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>