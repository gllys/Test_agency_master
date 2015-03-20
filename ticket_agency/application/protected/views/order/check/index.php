<?php
$this->breadcrumbs = array(
	'验票',
	'验票记录'
);
?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns" style="display: none;">
				<a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a> <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
			</div>
			<!-- panel-btns -->
			<h4 class="panel-title">验票记录</h4>

		</div>

		<!-- panel-body -->
	</div>
	<style>
.tab-content .table tr>* {
	text-align: center
}

.tab-content .ckbox {
	display: inline-block;
	width: 30px;
	text-align: left
}
</style>
	<table class="table table-bordered mb30">
		<thead>
			<tr>
				<th style="width:12%">订单编号</th>
				<th style="width:10%">验证时间</th>
				<th style="width:10%">验证数量</th>
                                <th style="width:10%">验证景区</th>
				<th style="width:10%">验证景点</th>
				<th style="width:7%">验证结果</th>
				<th style="width:10%">操作员</th>
				<th style="width:10%">设备类型</th>
				<th style="width:10%">设备编号</th>
				<th style="width:10%">设备名称</th>
			</tr>
		</thead>
		<tbody>
            <?php
												foreach( $lists as $item ) :
													?>
                <tr>
				<td  style="width:12%"><?php echo $item['record_code'] ?></td>
				<td style="width:10%"><?php echo date('Y-m-d H:i:s', $item['created_at']) ?></td>
				<td style="width:10%"><?php echo $item['num'] ?></td>
                                <?php
                                        //得到景区
                                        if (!isset($_landscapes)) { //单例
                                            $params = array();
                                            $params['items'] = 100000;
                                            $params['fields'] = 'id,name';
                                            $lanIds = PublicFunHelper::arrayKey($lists, 'landscape_id');
                                            $param['ids'] = join(',', $lanIds);
                                            $data = Landscape::api()->lists($param);
                                            $_landscapes = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
                                        }
                                        ?>
                                <td style="width:10%"><?php echo $_landscapes[$item['landscape_id']]['name'] ?></td>
				<td style="<?php echo empty($item['poi_id'])?'':'text-align: left;'?> width: 10%;"><?php
                                                    if(empty($item['poi_id'])){
                                                        echo '全部';
                                                    }else if(strlen($item['poi_id']) > 0) {
														$p_ids = explode(',', $item['poi_id']);
														$spans = array();
														foreach( $p_ids as $pid ) {
															$spans[] = sprintf('<span role="async-name" class="poi-%d" data-id="poi_%d"></span>', $pid, $pid);
														}
														echo implode(',', $spans);
                                                    }
													?>
                    </td>
				<td style="width:10%"><span class="text <?php echo $item['status'] ? 'text-success' : 'text-danger' ?>"><?php echo $item['status'] ? '成功' : '失败' ?></span></td>
				<td style="width:10%"><?php
													echo $item['user_name'];
													?>
                    </td>
				<td style="width:7%;"><?php
													if(isset($item['equipment_code']) && ! empty($item['equipment_code'])) {
														// todo optimize
														$lists = Equipments::api()->detail(array(
															'code' => $item['equipment_code']
														));
														$list = ApiModel::getData($lists);
														if($list) {
															echo $list['type'] == 1 ? '闸机' : '手持机';
														}
                                                    }else{
                                                        echo '无';
                                                    }
													?></td>
				<td style="width:10%;"><?php
													echo $item['equipment_code'] == 0 ? '无' : $item['equipment_code'];
                                                    ?><span></span></td>
				<td style="width:10%;"><?php
													if(isset($item['equipment_code']) && ! empty($item['equipment_code'])) {
														// todo optimize
														$lists = Equipments::api()->detail(array(
															'code' => $item['equipment_code']
														));
														$list = ApiModel::getData($lists);
														if(isset($list['name'])) {
															echo $list['name'];
														}
													}else{
                                                        echo '无';
                                                    }
													?></td>

			</tr>
                <?php
												endforeach
												;
												?>
        </tbody>
	</table>
	<div style="text-align: left;" class="panel-footer">
		订单数：&nbsp;<span style="color: red; font-size: 17px;"><?php echo $orderNums ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 总人次：&nbsp;<span style="color: blue; font-size: 17px;"><?php echo $totalNums ?></span>&nbsp;
		<div id="basicTable_paginate" class="pagenumQu">
            <?php
												$this->widget('common.widgets.pagers.ULinkPager', array(
													'cssFile' => '',
													'header' => '',
													'prevPageLabel' => '上一页',
													'nextPageLabel' => '下一页',
													'firstPageLabel' => '',
													'lastPageLabel' => '',
													'pages' => $pages,
													'maxButtonCount' => 5
												) // 分页数量
);
												?>
        </div>
	</div>
</div>
<!-- contentpanel -->


