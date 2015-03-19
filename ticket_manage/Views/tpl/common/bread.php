<div class="container-fluid">
	<div class="row-fluid">
		<div class="area-top clearfix">
			<div class="pull-left header">
				<h3 class="title">
					<i class="<?php echo $menu[$c]['menu'][$a]['class'] ? $menu[$c]['menu'][$a]['class'] : $menu[$c]['class'];?> icon-2x"></i>
					<?php echo $menu[$c]['menu'][$a]['title'] ? $menu[$c]['menu'][$a]['title'] : $menu[$c]['title'];?>
				</h3>
				<h5 style="margin-left:36px">上海汇联皆景</h5>
			</div>
			<!-- <ul class="inline pull-right sparkline-box">
				<li class="sparkline-row">
					<h4 class="blue"><span>今日出票</span> 1,230</h4>
					<div class="sparkline big" data-color="blue"><!-4,10,19,25,5,7,9,7,5,12,17,19-></div>
				</li>
				<li class="sparkline-row">
					<h4 class="green"><span>本月出票</span> 50,065</h4>
					<div class="sparkline big" data-color="green"><!-96,92,93,96,92,92,91,99,83,86,81,99-></div>
				</li>
				<li class="sparkline-row">
					<h4 class="red"><span>本月收入</span> ￥2,235,562</h4>
					<div class="sparkline big"><!-96,92,93,96,92,92,91,99,83,86,85,99-></div>
				</li>
			</ul> -->
		</div>
	</div>
</div>

<div class="container-fluid padded">
	<div class="row-fluid">
		<!-- Breadcrumb line -->
		<div id="breadcrumbs">
			<div class="breadcrumb-button blue">
				<span class="breadcrumb-label"><i class="icon-leaf"></i> 智慧旅游管理平台</span>
				<span class="breadcrumb-arrow"><span></span></span>
			</div>
			<div class="breadcrumb-button">
				<span class="breadcrumb-label">
					<i class="<?php echo $menu[$c]['class'];?>"></i> <?php echo $menu[$c]['title'];?>
				</span>
				<span class="breadcrumb-arrow"><span></span></span>
			</div>
			<?php if($menu[$c]['menu'][$a]['title']): ?>
			<div class="breadcrumb-button">
				<span class="breadcrumb-label">
					<i class="<?php echo $menu[$c]['menu'][$a]['class'];?>"></i> <?php echo $menu[$c]['menu'][$a]['title'];?>
				</span>
				<span class="breadcrumb-arrow"><span></span></span>
			</div>
			<?php endif;?>
		</div>
	</div>
</div>