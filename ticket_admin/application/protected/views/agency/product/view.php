<?php

use common\huilian\utils\Format;
use common\huilian\utils\Time;

?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">供应商产品</h4>
		</div>
		<div class="panel-body">
			<table class="table table-bordered mb30">
				<thead>
					<tr>
						<th>供应商</th>
						<th>景区名称</th>
						<th>门票</th>
						<th>数量</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($product['items'] as $item) { ?>
					<tr>
						<td><?= $product['organization']['name'] ?></td>
						<td><?= $item['sceinc_name'] ?></td>
						<td><?= $item['base_name'] ?></td>
						<td><?= $item['num'] ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<table class="table table-bordered mb30" style="margin-top:30px;">	
				<tr>
					<td style="width:10%;">产品名称:</td>
					<td><?= $product['name'] ?></td>
				</tr>
				<?php if($product['is_fit']) { ?>
				<tr>
					<td>散客结算价:</td>
					<td>
						<?= $product['fat_price'] ?>
                        <span style="margin-left:30px;">是否一次验票:</span>
                        <span style=""><?= $product['is_fat_once_verificate'] ? '是' : '否'; ?></sapn>
                        <?php if(count($product['landscapes']) > 1) { // 当景区为1个时候，不必显示是否一次取票 ?>
                        <span style="margin-left:30px;">是否一次取票:</span>
                        <span style=""><?= $product['is_fat_once_taken'] ? '是' : '否'; ?></span>
                        <?php } ?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<?php $parts = Time::seconds($product['fat_scheduled_time']); ?>
					<td>提前预定时间：</td>
					<td>需在入园前<?= $parts['days'] ?>天<?= Time::prefixZero($parts['hours']) ?>:<?= Time::prefixZero($parts['minutes']) ?>以前购买</td>
				</tr>
				<tr>
					<td>散客产品说明:</td>
					<td><?= $product['fat_description'] ?></td>
				</tr>
				<tr>
					<td>团队结算价:</td>
					<td>
						<?= $product['group_price'] ?> 
						<span style="margin-left:30px;">最少订票<?= $product['mini_buy'] ?>张</span>
						<span style="margin-left:30px;">是否一次验票:</span>
                        <span style=""><?= $product['is_group_once_verificate'] ? '是' : '否'; ?></sapn>
                        <?php if(count($product['landscapes']) > 1) { // 当景区为1个时候，不必显示是否一次取票 ?>
                        <span style="margin-left:30px;">是否一次取票:</span>
                        <span style=""><?= $product['is_group_once_taken'] ? '是' : '否'; ?></span>
                        <?php } ?>
					</td>
				</tr>
				<tr>
					<?php $parts = Time::seconds($product['group_scheduled_time']); ?>
					<td>提前预定时间：</td>
					<td>需在入园前<?= $parts['days'] ?>天<?= Time::prefixZero($parts['hours']) ?>:<?= Time::prefixZero($parts['minutes']) ?>以前购买</td>
				</tr>
				<tr>
					<td>团队产品说明:</td>
					<td><?= $product['group_description'] ?></td>
				</tr>
				<tr>
					<td>是否允许退票:</td>
					<td>
						<?= $product['refund'] ? '是' : '否' ?>
						<span style="margin-left:30px;">是否允许短信:</span>
						<span style=""><?= $product['message_open'] ? '是' : '否'; ?></sapn>
						<span style="margin-left:30px;">是否门票验证:</span>
						<span style=""><?= $product['checked_open'] ? '是' : '否'; ?></sapn>
					</td>
				</tr>
				<tr>
					<td>产品销售日期：</td>
					<td><?= Format::date($product['sale_start_time']) ?> 至 <?= Format::date($product['sale_end_time']) ?></td>
				</tr>
				<tr>
					<td>使用有效期：</td>
					<td>
						<?= Format::date($product['expire_start']) ?> 至 <?= Format::date($product['expire_end']) ?>
						<?php
							echo $product['valid_flag'] ? '有效期不限 ' : '预定游玩日后' . $product['valid']. '天有效 ';
							
							$week = [
								0 => '周日',
								1 => '周一',
								2 => '周二',
								3 => '周三',
								4 => '周四',
								5 => '周五',
								6 => '周六',		
							];
							$days = explode(',', $product['week_time']);
							foreach($days as $day) {
								echo $week[$day], ' ';
							}
						?>
					</td>
				</tr>
				<tr>
					<td>短信模板：</td>
					<td>
						<?= $product['sms_template'] ?: '未设置' ?>
					</td>
				</tr>
			</table>
		</div>
		<div style="padding:10px;">			<?php if($product['force_out'] == 0 && $product['state'] == 1) { ?>
			<a class="clearPart" style="cursor: pointer; cursor: hand;" data-target=".modal-bank" data-toggle="modal" href="/agency/product/forceOut?id=<?=
			$product['id'] ?>" onclick="modal_jump(this);"><button class="btn btn-primary btn-sm clearPart" type="button" style="margin-right:20px;">强制下架</button></a>
			<?php } else if($product['force_out'] == 0 && $product['state'] != 1) { ?>
			<button class="btn btn-sm" type="button" style="margin-right:20px;">强制下架</button>		
			<?php } else if($product['force_out'] == 1 && $product['state'] != 1) { // ?>
			<a class="clearPart" href="javascript:;" onclick="clearForceOut(<?= $product['id'] ?>);"><button class="btn
			btn-primary
			btn-sm" type="button" style="margin-right:20px;">解除下架</button></a>
			<?php } else {?>
			<button class="btn btn-sm" type="button" style="margin-right:20px;">解除下架</button>
			<?php } ?>
			<button class="btn btn-primary btn-sm close_window clearPart" type="button">关闭窗口</button>
		</div>
	</div>
	
	<div class="panel-footer">
		
	</div>

</div>
<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>
<script type="text/javascript">
$(function() {

	$('.close_window').click(function() {
		window.close();
	});
	
})

function modal_jump(obj) {
    $('#verify-modal').html('');
    $.get($(obj).attr('href'), function(data) {
        $('#verify-modal').html(data);
    });
}

// 解除下架
function clearForceOut(id) {
	PWConfirm('确定解除强制下架吗？',function(){
		$.post('/agency/product/forceOut', {id:id,force_out:0, force_out_remark:''}, function(data) {
			if (data.error) {
				alert('解除强制下架成功');
				setTimeout("window.location.reload()", 2000);
			} else {
					alert(data.msg, function(){window.location.partReload();});

			}
		}, 'json');
	});
}  

</script>