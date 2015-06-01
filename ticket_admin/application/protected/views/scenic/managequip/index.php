<?php
$this->breadcrumbs = array(
	'景区管理',
	'设备管理'
);
?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">
	<div id="show_msg"></div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">设备管理</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" method="get" action="/scenic/managequip/">
				<div class="form-group" style="width: 340px;">
					<label>更新时间:</label> <input style="cursor: pointer; cursor: hand; background-color: #ffffff" name="s_time" class="form-control datepicker" value="<?php echo (isset($get['s_time']))?$get['s_time']:"";?>" placeholder="<?php echo (isset($get['s_time']))?$get['s_time']:"";?>" type="text" readonly="readonly"> ~ <input style="cursor: pointer; cursor: hand; background-color: #ffffff" name="e_time" class="form-control datepicker" value="<?php echo (isset($get['e_time']))?$get['e_time']:"";?>"
						placeholder="<?php echo (isset($get['e_time']))?$get['e_time']:"";?>" type="text" readonly="readonly">
				</div>
				<div class="form-group" style="width: 95px; margin-right: 0px;">
					<label>是否绑定景区:</label>
				</div>
				<div class="form-group">
					<select class="select2" name="is_bind" style="width: 150px; padding: 0 10px;">
						<option value="" selected="selected">全部</option>
						<option value="1" <?= $get['is_bind'] === '1' ? ' selected' : '' ?>>是</option>
						<option value="0" <?= $get['is_bind'] === '0' ? ' selected' : '' ?>>否</option>
					</select>
				</div>
				<div class="form-group" style="width: 75px; margin-right: 0px;">
					<label>是否安装:</label>
				</div>
				<div class="form-group">
					<select class="select2" name="is_fix" style="width: 150px; padding: 0 10px;">
						<option value="" selected="selected">全部</option>
						<option value="1" <?= $get['is_fix'] === '1' ? ' selected' : '' ?>>是</option>
						<option value="0" <?= $get['is_fix'] === '0' ? ' selected' : '' ?>>否</option>
					</select>
				</div>
				<br />
				<div class="form-group" style="width: 64px; margin-right: 0px;">
					<label>供应商:</label>
				</div>
				<div class="form-group" style="width: 150px;">
					<select name="org_name" style="width: 150px; padding: 0 10px;" id="supplierSelect">
						<option></option>
						<?php
                                                  if(isset($supply)){
                                                      foreach($supply as $v){
                                                 ?>         
                                                <option value="<?=$v['name'];?>"><?=$v['name'];?></option>
                                                <?php
                                                      }                                                      
                                                  }
                                                ?>
					</select>
                                        
				</div>
                                <div class="form-group" style="width: 45px; margin-right: 0px;">
					<label>景区:</label>
				</div>
				<div class="form-group" style="width: 150px;">
					<select name="scenic_name" style="width: 150px; padding: 0 10px;" id="scenicNameSelect">
						<option></option>
                                                <?php
                                                  if(isset($landscape)){
                                                      foreach($landscape as $v){
                                                 ?>         
                                                <option value="<?=$v['name'];?>"><?=$v['name'];?></option>
                                                <?php
                                                      }                                                      
                                                  }
                                                ?>			
					</select>
                                        
				</div>
				<div class="form-group" style="width: 240px;">
					<label>设备编号:</label> <input type="text" class="form-control" name="code" value="<?php echo $get['code']?>" />
				</div>
				<div class="form-group">
					<button class="btn btn-primary btn-sm" type="submit">查询</button>
				</div>
			</form>
		</div>
		<!-- panel-body -->
	</div>
	<form action="/order/payments/method/" method="post">
		<table class="table table-bordered mb30 table1">
			<thead>
				<tr>
					<th>序号</th>
					<th>设备编号</th>
					<th>设备名称</th>
					<th>类型</th>
					<th>使用类型</th>
					<th>绑定供应商</th>
					<th>绑定景区</th>
					<th>安装位置（子景区）</th>
					<th>更新时间</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
			<?php if($list):?>
				<?php foreach($list as $equipment):?>
					<tr>
					<td><?php echo $equipment['id'];?></td>
					<td><?php echo $equipment['code'];?></td>
					<td><?php echo $equipment['name'];?></td>
					<td><?php echo $equipment['type']==0?"手持验票机":"闸机";?></td>
					<td><?php echo $equipment['scene']==0?"未选择":($equipment['scene']==1?"入园":"出园");?></td>
					<td><a href="/scenic/managequip/supply/?id=<?php echo $equipment['id'];?>">
								<?php
								echo isset($supply[$equipment['supply']]['name']) ?$supply[$equipment['supply']]['name'] : '绑定供应商';?>
							</a></td>
					<td>
							<?php if($equipment['supply']):?>
								<a href="/scenic/managequip/landscape/id/<?php echo $equipment['id'];?>">
									<?php echo $equipment['landscape'] ? $equipment['landscape']['name'] : '绑定景区';?>
								</a>
							<?php else:?>
								无
							<?php endif;?>
						</td>
					<td>
							<?php if($equipment['landscape']):?>
								<a href="/scenic/managequip/scenic/id/<?php echo $equipment['id'];?>">
									<?php echo $equipment['poi'] ? $equipment['poi']['name'] : '全部';?>
								</a>
							<?php else:?>
								无
							<?php endif;?>
						</td>
					<td><?php echo date('Y-m-d H:i:s',$equipment['updated_at']);?></td>
					<td><a class="btn btn-success btn-bordered btn-xs " href="/scenic/managequip/edit?id=<?php echo $equipment['id']?>">编辑</a> <a class="btn btn-danger btn-bordered btn-xs clearPart" href="javascript:;" onclick="delEquip(<?php echo $equipment['id']?>)">删除</a></td>
				</tr>
				<?php endforeach;?>
			<?php else:?>
				<tr>
					<td colspan="9" style="text-align: center !important;">暂无记录</td>
				</tr>
			<?php endif;?>

			</tbody>
		</table>
	</form>
	<div class="panel-footer pagenumQu" style="padding-top: 15px; text-align: right; border: 1px solid #ddd; border-top: 0">
			<?php
			if(! empty($list)) {
				$this->widget('common.widgets.pagers.ULinkPager', array(
					'cssFile' => '',
					'header' => '',
					'prevPageLabel' => '上一页',
					'nextPageLabel' => '下一页',
					'firstPageLabel' => '',
					'lastPageLabel' => '',
					'pages' => $pages,
					'maxButtonCount' => 3
				) // 分页数量
);
			}
			?>
		</div>
</div>
<!-- contentpanel -->
<script>
	jQuery(document).ready(function() {

		$('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
            yearRange: "1995:2065",
            beforeShow: function(d){
                setTimeout(function(){
                    $('.ui-datepicker-title select').select2({
                        minimumResultsForSearch: -1
                    });
                },0)
            },
            onChangeMonthYear: function(){
                setTimeout(function(){
                    $('.ui-datepicker-title select').select2({
                        minimumResultsForSearch: -1
                    });
                },0)
            },
            onClose: function(dateText, inst) { 
                $('.select2-drop').hide(); 
            }
        });


		jQuery('.select2').select2({
			minimumResultsForSearch: -1
		});
		jQuery('#supplierSelect').select2();
                jQuery('#scenicNameSelect').select2();
                
  
	});
	//删除设备
	function delEquip(eid)
	{
		PWConfirm('确定要删除么？', function () {
			$.post('/scenic/managequip/delEquip/', {id: eid},function(data){
				if(typeof data.errors != 'undefined'){
                    setTimeout(function(){
                        alert('删除设备失败!'+data.errors.msg);
                    }, 500);
				}else{
                    setTimeout(function(){
                        alert('删除成功!', function() {
                            location.partReload();
                        });
                    }, 500);
				}
			},"json");
		});
	}
</script>