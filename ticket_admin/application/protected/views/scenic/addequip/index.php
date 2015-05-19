<?php
$this->breadcrumbs = array('景区管理', '添加设备');
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
						<h4 class="panel-title">添加设备</h4>
					</div>
					<!-- panel-heading -->
					<div id="return_msg"></div>
					<div class="panel-body nopadding">
						<form class="form-horizontal" id="add_equipment" method="post" action="/scenic/saveEquip/">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-3 control-label"><span class="text-danger">*</span> 设备类别：</label>
										<div class="col-sm-6">
											<div class="rdio rdio-default">
												<input type="radio" value="1" id="radioDefault" name="type" class="validate[required]">
												<label for="radioDefault">闸机</label>
											</div>
											<div class="rdio rdio-default">
												<input type="radio" value="0" checked="checked" id="radioDefault1" name="type" class="validate[required]">
												<label for="radioDefault1">手持验票机</label>
											</div>
										</div>
									</div>
									<!-- form-group -->
									<div class="form-group">
										<label class="col-sm-3 control-label"><span class="text-danger">*</span>设备编号：</label>
										<div class="col-sm-6">
											<input type="text"  tag="设备编号" class="form-control  validate[required,minSize[2],maxSize[32]]" name="code"  />
										</div>
									</div>
									<!-- form-group -->
									<div class="form-group">
										<label class="col-sm-3 control-label"><span class="text-danger">*</span>设备名称：</label>
										<div class="col-sm-6">
											<input type="text" tag="设备名称" class="form-control validate[required,minSize[2],maxSize[32]]" name="name"/>
										</div>
									</div>
									<!-- form-group -->
								</div>
							</div>
							<div class="panel-footer" style="padding-left:10%">
								<button class="btn btn-primary mr20" id="addEquipment"  style="width: 100px;">确认添加</button>
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
		$('#add_equipment').validationEngine('attach', {
			scroll: false,
			showPrompts: true
		});
		/*
		* 防止多次点击添加
		* 表单验证提交
		* */
		$("#addEquipment").click(function(){
			$("#addEquipment").hide();   //.removeAttr("disabled"); 移除disabled属性    $("#addEquipment").attr("disabled",true);
			$(".load2").show();
			var obj = $('#add_equipment');
			if(obj.validationEngine('validate')== true){
				$.post('/scenic/addequip/saveEquip/', obj.serialize(),function(data){
					if(typeof data.errors != 'undefined'){
                        alert('添加设备失败!'+data.errors.msg, function() {
                            $("#addEquipment").show();
                            $(".load2").hide();
                        });
					}else{
						$("#addEquipment").show();
						$(".load2").hide();
						alert('保存成功! ', function() {
                            location.href='/site/switch/#/scenic/managequip/';
                        });
					}
				},"json");
			}else{
				$("#addEquipment").show();
				$(".load2").hide();
			};
			return false;
		});


	});
</script>