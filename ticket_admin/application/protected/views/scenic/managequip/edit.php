<?php
$this->breadcrumbs = array('景区管理', '设备管理');
?>
<div class="panel-body">

	<div class="contentpanel">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="panel-btns">
							<a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
							<a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
						</div>
						<!-- panel-btns -->
						<h4 class="panel-title">编辑设备</h4>
					</div>
					<!-- panel-heading -->
					<div id="show_msg"></div>
					<div class="panel-body nopadding">
						<form class="form-horizontal" id="equipement_update" method="post" action="/scenic/managequip/upEquip">
							<div class="row">
								<div class="form-group">
									<label class="col-sm-2 control-label">绑定供应商：</label>
									<div class="col-sm-6" style="margin-top: 5px;">
										<a style="color: #0088CC;" href="/scenic/managequip/supply/id/<?php echo $equipment['id']; ?>">
											<?php $supply = Organizations::api()->show(array('id'=>$equipment['organization_id']));
											echo isset($supply['body']['name']) ? $supply['body']['name'] : '绑定供应商';?>
										</a>
									</div>
								</div>
									<!-- form-group -->
									<div class="form-group">
										<label class="col-sm-2 control-label">绑定景区：</label>
										<div class="col-sm-6" style="margin-top: 5px;">
											<?php if($equipment['organization_id']):?>
												<a style="color: #0088CC;" href="/scenic/managequip/landscape/id/<?php echo $equipment['id']; ?>"><?php echo $equipment['landscape'] ? $equipment['landscape']['name'] : '无'; ?></a>
											<?php else:?>
												无
											<?php endif;?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">安装位置（子景区）：</label>
										<div class="col-sm-6" style="margin-top: 5px;">
											<?php if ($equipment['landscape']): ?>
												<a style="color: #0088CC;padding-top:12px;"
												   href="/scenic/managequip/scenic/id/<?php
												echo $equipment['id']; ?>">
													<?php echo $equipment['poi'] ? $equipment['poi']['name'] : '全部'; ?>
												</a>
											<?php else: ?>
												无
											<?php endif; ?>
										</div>
									</div>
									<!-- form-group -->



									<div class="form-group">
										<label class="col-sm-2 control-label"><span class="text-danger">*</span>
											设备类别：</label>
										<div class="col-sm-6">
											<div class="rdio rdio-default">
												<input type="radio" value="1" id="radioDefault" name="type" class="validate[required]" <?php
												if ($equipment['type'] == 1) {
													echo 'checked="checked"';
												}
												?>>
												<label for="radioDefault">闸机</label>
											</div>
											<div class="rdio rdio-default">
												<input type="radio" value="0" id="radioDefault1" name="type" class="validate[required]" <?php
												if ($equipment['type'] == 0) {
													echo 'checked="checked"';
												}
												?>>
												<label for="radioDefault1">手持验票机</label>
											</div>
										</div>
									</div>
									<!-- form-group -->
									<div class="form-group">
										<label class="col-sm-2 control-label"><span
												class="text-danger">*</span>设备编号：</label>
										<div class="col-sm-6">
											<input type="text" data-prompt-position="topRight:10,-20" tag="设备编号"
												   class="form-control
											validate[required,minSize[2],maxSize[32]]" name="code" value="<?php echo $equipment['code']; ?>" />
										</div>
									</div>
									<!-- form-group -->
									<div class="form-group">
										<label class="col-sm-2 control-label"><span
												class="text-danger">*</span>设备名称：</label>
										<div class="col-sm-6">
											<input type="text" data-prompt-position="topRight:10,-10" tag="设备名称"
												   class="form-control validate[required,minSize[2],maxSize[32]]" name="name" value="<?php echo $equipment['name']; ?>"/>
										</div>
									</div>
									<!-- form-group -->
								</div>
							</div>
							<div class="panel-footer" style="padding-left:10%">
								<input type="hidden" name="id" value="<?php echo $equipment['id']; ?>">
								<button class="btn btn-primary mr20" id="equip-edit"  style="width: 100px;">更新</button>
								<img src="/img/select9-spinner.gif" class="load2" style="display: none;position:relative;left:30px;top:-33px;" >
								<!--button class="btn btn-default" >取消返回</button-->
							</div>
						</form>
					</div>
					<!-- panel-body -->
				</div>
				<!-- panel -->
			</div>
			<!-- col-md-6 -->
		</div>
		<!-- row -->
	</div>
</div>
<!-- contentpanel -->
<script>
	jQuery(document).ready(function() {
		//添加设备 表单验证
		$('#equipement_update').validationEngine('attach', {
			promptPosition: 'centerTop',
			scroll: false,
			showPrompts: true
		});

		//编辑闸机
		$('#equip-edit').click(function(){
			$('#equip-edit').hide();
			$(".load2").show();
			var obj = $('#equipement_update');
			if(obj.validationEngine('validate')== true){
				$.post('/scenic/managequip/upEquip', obj.serialize(),function(data){
					if(typeof data.errors != 'undefined'){
                        alert('更新设备失败!'+data.errors.msg);
					}else{
						$("#equip-edit").show();
						$(".load2").hide();
                        alert('更新成功!', function() {
                            location.href='/site/switch/#/scenic/managequip/';
                        });
					}
				},"json");

			}else{
				$("#equip-edit").show();
				$(".load2").hide();
			};
			return false;
		})


	});
</script>