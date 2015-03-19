<!DOCTYPE html>
<html>
	<?php get_header();?>
	<body>
		<?php get_top_nav();?>
		<div class="sidebar-background">
			<div class="primary-sidebar-background"></div>
		</div>
		<?php get_menu();?>
		<div class="main-content">
			<?php get_crumbs();?>

			<div id="show_msg">
			</div>
			<style>
				.thumbnail img{
					width:100%;
					height:300px
				}
				.label-green,.label-red{
					float:right
				}
			</style>
			<div class="container-fluid padded">
				<div class="box">
					<div class="box-header"><span class="title"><?php echo $scenicInfo['name'];?></span></div>
					<div class="box-content padded">
						<div class="row-fluid">
							<div class="span6 landscape-image">
								<a href="<?php echo $scenicInfo['thumbnail']['url']?>" class="thumbnail thumbs">
									<img src="<?php echo $scenicInfo['thumbnail']['url']?>" alt="">
								</a>
							</div>
							<div class="span6 box">
								<table class="table table-normal landscape-table">
									<tr>
										<td colspan="2">
											<h2>
												<?php if($pageType == 'preview'):?>
													<span class="label <?php if($scenicInfo['status'] == 'normal'):?>label-green<?php else:?>label-red<?php endif;?>"><?php echo ScenicCommon::getScenicStatus($scenicInfo['status']);?>
													</span>
												<?php endif;?>
												<?php echo $scenicInfo['name'];?>
											</h2>
											
											<?php echo $scenicInfo['districts'][0]['name'].$scenicInfo['districts'][1]['name'].$scenicInfo['districts'][2]['name'];?>
										</td>
									</tr>
									<tr>
										<th align="right">景区级别：</th>
										<td><?php echo $scenicInfo['level']['name'];?></td>
									</tr>
									<tr>
										<th align="right">开放时间：</th>
										<td><?php echo $scenicInfo['hours'];?></td>
									</tr>
									<tr>
										<th align="right">联系电话：</th>
										<td><?php echo $scenicInfo['phone'];?></td>
									</tr>
									<tr>
										<th align="right">取票地址：</th>
										<td><?php echo $scenicInfo['exaddress'];?></td>
									</tr>
									<tr>
										<th align="right">联系地址：</th>
										<td><?php echo $scenicInfo['address'];?></td>
									</tr>
								</table>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="container-fluid padded gap">
				<div class="box">
					<?php if($pageType == 'detail'):?><div class="box-header"><span class="title">门票订购</span></div><?php endif;?>
					<table class="table table-normal">
						<thead>
							<tr>
							<td>门票类型</td>
							<td>支付方式</td>
							<td>结算价</td>
							<td>销售价</td>
							<td>挂牌价</td>
							<?php if($pageType == 'detail'):?><td>操作</td><?php endif;?>
							</tr>
						</thead>
						<tbody>
							<?php if($ticketInfo):?>
								<?php foreach($ticketInfo as $ticketItem):?>
									<tr>
										<td><?php echo $ticketItem['name'];?>
											<?php if($pageType == 'preview'):?><span class="label <?php if($ticketItem['status'] == 'normal'):?>label-green<?php else:?>label-red<?php endif;?>"><?php echo TicketCommon::getTicketStatus($ticketItem['status']);?></span><?php endif;?>
										</td>
										<td class="center"><?php echo TicketCommon::getTicketPayment($ticketItem['payment']);?></td>
										<td class="center">
											<span <?php if($ticketItem['ticket_templates_id']):?>style="text-decoration:line-through;"<?php endif;?>><?php echo $ticketItem['sale_price'];?></span>
											<?php if($ticketItem['ticket_templates_id']){echo $ticketItem['pti_partner_price'];}?>
										</td>
										<td class="center"><?php echo $ticketItem['market_price'];?></td>
										<td class="center"><?php echo $ticketItem['brand_price'];?></td>
										<?php if($pageType == 'detail'):?><td class="center"><button class="btn btn-green" onclick="check_shopping_able(<?php echo $ticketItem['id'];?>)">预定</button></td><?php endif;?>
									</tr>
								<?php endforeach;?>
							<?php endif;?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">景点介绍</span></div>
						<div class="box-content padded">
							<?php echo $scenicInfo['biography'];?>
						</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">预订须知</span></div>
					<div class="box-content padded">
						<?php echo $scenicInfo['note'];?>
					</div>
				</div>
			</div>
			<div class="container-fluid padded gap">
				<div class="box">
					<div class="box-header"><span class="title">交通指南</span></div>
					<div class="box-content padded">
						<?php echo $scenicInfo['transit'];?>
					</div>
				</div>
			</div>

		<div class="container-fluid padded gap">
      		<button class="btn btn-lg btn-blue" type="button" id="submit-button-organization" onclick="javascript:history.go(-1)">返回</button>
         </div>
		</div>
		<?php if($pageType == 'detail'):?><script src="Views/js/shopping/index.js"></script><?php endif;?>
		<script type="text/javascript">
		    $(document).ready(function(){
		        //点击缩略图查看大图事件
		        $('.thumbs').touchTouch();
		    })
		</script>
	</body>
</html>
