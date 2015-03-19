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

<div id="show_msg"></div>

  		<div class="container-fluid padded">
				<div class="box">
					<div class="box-header">
						<span class="title">系统配置</span>
					</div>
							<div class="content">
								<form  id="setting-form">
									<table class="table table-normal">
										<tbody>
											<tr>
												<th>平台统一结算周期配置:</th>
												<td>
													<select class="uniform" name="account_cycle" id="account_cycle"  onchange="changeDayShow(this.value)">
														<option value="undefined" <?php if($config['account_cycle'] == 'undefined'):?>selected="selected"<?php endif?>>请选择结算周期</option>
														<option value="month" <?php if($config['account_cycle'] == 'month'):?>selected="selected"<?php endif?>>月结算</option>
														<option value="week" <?php if($config['account_cycle'] == 'week'):?>selected="selected"<?php endif?>>周结算</option>
													</select>
												</td>
												<td>
													<select class="uniform" name="account_cycle_day" id="account_cycle_day">
														<option value="__NULL__">请选择结算日</option>
														<?php if($config['account_cycle'] == 'month'):?>
															<?php for($i = 1; $i <= 31; $i++):?>
																<option value="<?php echo $i;?>" <?php if($config['account_cycle_day'] == $i):?>selected="selected"<?php endif?>><?php echo $i;?></option>
															<?php endfor;?>
														<?php endif;?>

														<?php if($config['account_cycle'] == 'week'): ?>
															<?php foreach($weekArray as $key => $value):?>
																<option value="<?php echo $key;?>" <?php if($config['account_cycle_day'] == $key):?>selected="selected"<?php endif?>><?php echo $value;?></option>
															<?php endforeach;?>
														<?php endif;?>
													</select>
												</td>
											</tr>
											<tr>
												<th>强制结算：</th>
													<td colspan="2">
														<a title="强制结算" href="#settle" data-toggle="modal" class="btn btn-default" style="text-decoration:none;">结算并生成账单</a>
													</td>
												</tr>
										</tbody>
									</table>
									<div class="table-footer">
										<button class="btn btn-blue" id="setting-form-button">确定</button>
									</div>
								</form>
							</div>
				</div>
			</div>
		</div>

<!-- 点击结算生成账单的弹框开始 -->
<div id="settle" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h6 id="modal-formLabel">结算并生成账单</h6>
	</div>
	<div id="settle_show_msg"></div>
	<div class="modal-body select">
		<div class="container-fluid">
			<div class="box">
				<table class="table table-normal">
					<tbody>
					  <tr>
						<td>请输入密码：<input id='password' name='password'  type="password" /></td>
					  </tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-green" type="button" id="doSettle">结算并生成账单</button>
	</div>
</div>
<!-- 点击结算生成账单的弹框结束 -->

		<script>
			var phpvars       = {};
			phpvars.week_tpl  = '';
			phpvars.month_tpl = '';
			<?php foreach($weekArray as $weekkey => $weekvalue):?>
				phpvars.week_tpl += '<option value="<?php echo $weekkey;?>"><?php echo $weekvalue;?></option>';
			<?php endforeach;?>

			<?php for($j = 1; $j <= 31; $j++):?>
				phpvars.month_tpl += '<option value="<?php echo $j;?>"><?php echo $j;?></option>';
			<?php endfor;?>
		</script>
		<script src="Views/js/system/config.js"></script>
</body>
</html>

