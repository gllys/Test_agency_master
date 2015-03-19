<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h6 id="modal-formLabel"><?php echo $poi['name'];?>审核</h6>
</div>
<div id="verify_return"></div>
<div class="modal-body select">
	<div class="container-fluid">

		<div class="row-fluid">
			<!-- 已审核信息 -->
			<div class="span4">
				<div class="box">
					<div class="box-header">
						<?php if($poiLast && $poi['status'] == 'unaudited' || $poi['status'] != 'unaudited'):?>
							<span class="title" style="color:green;">已审核信息</span>
						<?php else: ?>
							<span class="title" style="color:red;">待审核信息</span>
						<?php endif;?>
					</div>
					<div class="box-content">
						景区名称：<?php echo $poi['name'];?><br/>
						景区级别：<?php echo $poi['level']['name']?><br/>
						所在地区：
								<?php if($poi['districts']):?>
									<?php foreach($poi['districts'] as $key => $val):?>
	                                    <?php echo $val['name'];?>
	                                <?php endforeach;?>
								<?php endif;?>
					</div>
				</div>
			</div>

			<!-- 未审核信息 -->
			<?php if($poiLast && $poi['status'] == 'unaudited'):?>
			<div class="span4">
				<div class="box">
					<div class="box-header">
						<span class="title" style="color:red;">待审核信息</span>
					</div>
					<div class="box-content">
						景区名称：<?php echo $poiLast['name'];?><br/>
						景区级别：<?php echo $poiLast['level']['name']?><br/>
						所在地区：
								<?php if($poiLast['districts']):?>
									<?php foreach($poiLast['districts'] as $key => $val):?>
	                                    <?php echo $val['name'];?>
	                                <?php endforeach;?>
								<?php endif;?>
					</div>
				</div>
			</div>
			<?php endif;?>


		</div>

		<?php if($poi['status'] == 'unaudited'):?>
			<div class="row-fluid">
				<a href="#" class="btn btn-green" onclick="verifyPoi(<?php echo $poi['id'];?>, 'normal')">审核通过</a>
				<a href="#" class="btn btn-red" onclick="verifyPoi(<?php echo $poi['id'];?>, 'failed')">拒绝</a>
			</div>
		<?php endif;?>
	</div>
</div>
<script>

	function verifyPoi(poi_id, status)
	{
		$.post('index.php?c=organization&a=poiVerify', {id:poi_id,status:status},function(data){
			if(data.errors){
				var tmp_errors = '';
				$.each(data.errors, function(i, n){
					tmp_errors += n;
				});
				var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
				$('#verify_return').html(warn_msg);
			}else if(data['data'][0]['id']){
				var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
				$('#verify_return').html(succss_msg);
				setTimeout("location.href='organization_poi.html'", '2000');
			}
		}, "json");
		return false;
	}
</script>