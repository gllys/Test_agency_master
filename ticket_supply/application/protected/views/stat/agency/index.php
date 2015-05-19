<?php
use common\huilian\utils\Format;
use common\huilian\utils\Color;
use common\huilian\utils\URL;

$colors = Color::h15();
$countAgencies = count($agencies);

?>

<style>
.ui-datepicker {
	z-index: 9999 !important;
}
</style>
<!--start contentpanel -->
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">统计</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" id="form1" method="get" action="/stat/agency/index/tab/<?= $tab ?>">
				<!--查询-->
				<div class="form-group" style="margin-right: 0;">年份:</div>
				<div class="form-group">
					<select class="select2" name="year" style="width: 80px;">
						<?php foreach(range(date('Y'), 2014) as $v) { ?>
						<option value="<?= $v ?>" <?= $v == $year ? ' selected' : '' ?>><?= $v ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group" style="margin-right: 0;">产品：</div>
				<div class="form-group" style="width:150px;">
					<select name="product_id" data-placeholder="Choose One" class="form-control" id="distributor-select-search" style="display: inline-block; width: 300px; padding: 0 10px;">
						<option value="">全部产品</option>
                        <?php foreach($productNames as $k => $v) { ?>
                        <option value="<?= $k ?>" <?= (isset($_GET['product_id']) && $_GET['product_id'] == $k) ? ' selected' : '' ?>><?= $v ?></option>
                        <?php } ?>
                    </select>
				</div>
				<div class="form-group" style="margin-right: 0;">分销商:</div>
				<div class="form-group" style="width:150px;">
					<select class="form-control" name="agency_id" style="width: 260px;" id="agency_names">
						<option value="">全部</option>
						<option value="-1"<?= (isset($_GET['agency_id']) && $_GET['agency_id'] == -1) ? ' selected' : '' ?>>平台分销商</option>
						<?php foreach($agencyNames as $k => $v) { ?>
						<option value="<?= $k ?>"<?= (isset($_GET['agency_id']) && $_GET['agency_id'] == $k) ? ' selected' : '' ?>><?= $v ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group" style="margin-right: 0;">分销商地区:</div>
				
				<!--省开始-->
				<div class="form-group" style="width: 100px;">
					<select class="select2" style="width: 100px;" id="province" name="province_id">
						<option value="">省</option>
                        <?php
                        $province = Districts::model()->findAllByAttributes(array("parent_id" => 0));
                        foreach ($province as $model) {
                            if ($model->id == 0) {
                                continue;
                            } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                        }
                        ?>
                    </select>
				</div>
				<!--省结束-->
				<!--市开始-->
				<div class="form-group" style="width: 100px;">
					<select style="width: 100px;" class="select2" data-placeholder="Choose One" id="city" name="city_id">
						<option value="">市</option>
                        <?php
                        if (!empty($_GET['province_id'])) {
                            $city_value = $_GET['province_id'];
                            $city = Districts::model()->findAllByAttributes(array("parent_id" => $city_value));
                            foreach ($city as $model) {
                                if ($model->id == 0) {
                                    continue;
                                } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                            }
                        }
                        ?>
                    </select>
				</div>
				<!--市结束-->
				<br>
				<div class="form-group" style="width: 600px;">
					<div class="col-sm-12">
						<label class="control-label">显示数据:</label>
						<div class="rdio rdio-default">
							<input type="radio" value="1" id="radioDefault" name="type" class="validate[required]" <?= $type == 1 ? ' checked' : '' ?>> <label for="radioDefault">入园人次（人次）</label>
						</div>
						<div class="rdio rdio-default">
							<input type="radio" value="2" id="radioDefault1" name="type" class="validate[required]" <?= $type == 2 ? ' checked' : '' ?>> <label for="radioDefault1">销售额（元）</label>
						</div>
						<div class="rdio rdio-default">
							<input type="radio" value="3" id="radioDefault2" name="type" class="validate[required]" <?= $type == 3 ? ' checked' : '' ?>> <label for="radioDefault2">门票销售数量（张）</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<button class="btn btn-primary btn-sm" type="submit">查询</button>
				</div>
			</form>
		</div>
		<!-- panel-body -->
	</div>
	<ul class="nav nav-tabs">
    	<?php 
    		$tabs = ['统计详情', '线状图', '饼图'];
    		foreach($tabs as $k => $v) {
		?>
        <li <?= $k == $tab ? 'class="active"' : '' ?>><a href="<?php
        		$url = URL::full();
        		if(preg_match('/tab\/\d+/i', $url)) {	// 如果url存在`tab/0`,则直接替0为响应的tab就可以
        			echo preg_replace('/tab\/(\d+)/i', 'tab/'.$k, $url);
        		} else {								// 如果没有tab，则追加参数
        			echo URL::addParam(URL::full(), 'tab', $k);
        		} 
        	
        	?>"><strong><?= $v ?></strong></a></li>
        <?php } ?>
    </ul>
	<div class="tab-content mb30">
		<div id="t1" class="tab-pane active">
			<?php if($tab == 0) { // 统计详情开始 ?>
			<table class="table table-bordered mb30">
				<thead>
					<tr>
						<th style="width: 10%;">分销商</th>
						<th>1月</th>
						<th>2月</th>
						<th>3月</th>
						<th>4月</th>
						<th>5月</th>
						<th>6月</th>
						<th>7月</th>
						<th>8月</th>
						<th>9月</th>
						<th>10月</th>
						<th>11月</th>
						<th>12月</th>
						<th>总计</th>
					</tr>
				</thead>
				<tbody id="staff-body">
					<?php foreach($agencies as $agency) { ?>
					<tr>
						<td><a href="/stat/product/index/tab/0?year=<?= $year ?>&agency_id=<?= $agency['agency_id'] ?>"><?= $agency['agency_name'] ?></a></td>
						<?php foreach($agency['stat'] as $v) { ?>
						<td><?= $v ?></td>
						<?php } ?>
					</tr>
					<?php } ?>
					<tr style="border: solid 2px rgb(236, 236, 236);">
						<td><b>合计（所有）</b></td>
						<?php foreach($amounts as $amount) { ?>
						<td><b><?= $amount ?></b></td>
						<?php } ?>
					</tr>
				</tbody>
			</table>
			<div class="panel-footer">
                <?php
                    if (isset($pages)) {
					$this->widget('common.widgets.pagers.ULinkPager', array(
                            'cssFile' => '',
                            'header' => '',
                            'prevPageLabel' => '上一页',
                            'nextPageLabel' => '下一页',
                            'firstPageLabel' => '',
                            'lastPageLabel' => '',
                            'pages' => $pages,
                            'maxButtonCount' => 5, //分页数量
                        ));
                    }
                ?>
            </div>
			<?php } else { // tab = 1 或 tab =2 时，需要引入图表 ?>
			<script src="/js/esl.js"></script>
			<script type="text/javascript">
				var echartsObj;
				// 路径配置
		        require.config({
		            paths: {
		                echarts: '/js/echarts/build/echarts-map',
		                'echarts/chart/map': '/js/echarts/src/map'
		            }
		        })
			</script>
			<?php if($tab == 1) { // 统计详情结束，线状图开始 }?>
			
			<?php  $types = [ 1 => '入园人次（人次）', 2 => '销售额（元）', 3 => '门票销售数量（张）',]; ?>
			<h4><?= $types[$type] ?>统计</h4>
			<div style="margin: 20px 0 0;">
				刻度单位： <a class="btn btn-sm <?= $unit == 1 ? 'btn-primary' : 'btn-default' ?>" href="<?= URL::addParam(URL::full(), 'unit', 1) ?>">日</a><a class="btn btn-sm <?= $unit == 0 ? 'btn-primary' : 'btn-default' ?>" href="<?= URL::addParam(URL::full(), 'unit', 0) ?>">月</a>
			</div>
			<div class="row">
				<div class="col-md-12">
					<!--------ri统计------>
					<div id="main" style="height: 420px"></div>
					<!-- panel -->
				</div>
			</div>
			<script type="text/javascript">

		       // 使用
		        require(
		            [
		                'echarts',
		                'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
		            ],
		            function (ec) {
		                // 基于准备好的dom，初始化echarts图表
		                echartsObj = ec.init(document.getElementById('main')); 

	                		/**
			                 * 折线图
			                 */
	                		var option = {
			                		legend: {
			                			data: [
						                	<?php foreach($agencies as $k => $agency) { ?>
						                	{	
				                				name:'<?= mb_substr($agency['agency_name'], 0, 12, 'UTF-8') ?>',
				                				textStyle:{color:'auto'}
				                	        }<?= $k < $countAgencies -1 ? ',' : '' ?>
				                	        <?php } ?>
			                	        ],
			                			selected: {
				                			<?php foreach($agencies as $k => $agency) { // 默认显示5个分销商 ?>
			                					'<?= mb_substr($agency['agency_name'], 0, 12, 'UTF-8') ?>' : <?= $k < 5 ? 'true' : 'false' ?><?= $k < $countAgencies -1 ? ',' : '' ?>
			                				<?php } ?>
			                			},
			                	        orient : 'vertical',
			                	        x: '0',
			                			y: '60'
			                		},
			                		grid:{
			                			x: '200'
			                		},
			                	    tooltip : {
			                	        trigger: 'axis'
			                	    },
			                		xAxis: [{
			                			type: 'category',
			                			boundaryGap: false,
			                			data : [
					                		<?php if($agencies) { // 为空的时候，是没有图的?>
						                		<?php if($unit == 0 ) { ?>
					                			'1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'
					                			<?php } else { ?>
					                			'1月1日', '1月2日', '1月3日', '1月4日', '1月5日', '1月6日', '1月7日', '1月8日', '1月9日', '1月10日', '1月11日', '1月12日', '1月13日', '1月14日', '1月15日', '1月16日', '1月17日', '1月18日', '1月19日', '1月20日', '1月21日', '1月22日', '1月23日', '1月24日', '1月25日', '1月26日', '1月27日', '1月28日', '1月29日', '1月30日', '1月31日', '2月1日', '2月2日', '2月3日', '2月4日', '2月5日', '2月6日', '2月7日', '2月8日', '2月9日', '2月10日', '2月11日', '2月12日', '2月13日', '2月14日', '2月15日', '2月16日', '2月17日', '2月18日', '2月19日', '2月20日', '2月21日', '2月22日', '2月23日', '2月24日', '2月25日', '2月26日', '2月27日', '2月28日', '3月1日', '3月2日', '3月3日', '3月4日', '3月5日', '3月6日', '3月7日', '3月8日', '3月9日', '3月10日', '3月11日', '3月12日', '3月13日', '3月14日', '3月15日', '3月16日', '3月17日', '3月18日', '3月19日', '3月20日', '3月21日', '3月22日', '3月23日', '3月24日', '3月25日', '3月26日', '3月27日', '3月28日', '3月29日', '3月30日', '3月31日', '4月1日', '4月2日', '4月3日', '4月4日', '4月5日', '4月6日', '4月7日', '4月8日', '4月9日', '4月10日', '4月11日', '4月12日', '4月13日', '4月14日', '4月15日', '4月16日', '4月17日', '4月18日', '4月19日', '4月20日', '4月21日', '4月22日', '4月23日', '4月24日', '4月25日', '4月26日', '4月27日', '4月28日', '4月29日', '4月30日', '5月1日', '5月2日', '5月3日', '5月4日', '5月5日', '5月6日', '5月7日', '5月8日', '5月9日', '5月10日', '5月11日', '5月12日', '5月13日', '5月14日', '5月15日', '5月16日', '5月17日', '5月18日', '5月19日', '5月20日', '5月21日', '5月22日', '5月23日', '5月24日', '5月25日', '5月26日', '5月27日', '5月28日', '5月29日', '5月30日', '5月31日', '6月1日', '6月2日', '6月3日', '6月4日', '6月5日', '6月6日', '6月7日', '6月8日', '6月9日', '6月10日', '6月11日', '6月12日', '6月13日', '6月14日', '6月15日', '6月16日', '6月17日', '6月18日', '6月19日', '6月20日', '6月21日', '6月22日', '6月23日', '6月24日', '6月25日', '6月26日', '6月27日', '6月28日', '6月29日', '6月30日', '7月1日', '7月2日', '7月3日', '7月4日', '7月5日', '7月6日', '7月7日', '7月8日', '7月9日', '7月10日', '7月11日', '7月12日', '7月13日', '7月14日', '7月15日', '7月16日', '7月17日', '7月18日', '7月19日', '7月20日', '7月21日', '7月22日', '7月23日', '7月24日', '7月25日', '7月26日', '7月27日', '7月28日', '7月29日', '7月30日', '7月31日', '8月1日', '8月2日', '8月3日', '8月4日', '8月5日', '8月6日', '8月7日', '8月8日', '8月9日', '8月10日', '8月11日', '8月12日', '8月13日', '8月14日', '8月15日', '8月16日', '8月17日', '8月18日', '8月19日', '8月20日', '8月21日', '8月22日', '8月23日', '8月24日', '8月25日', '8月26日', '8月27日', '8月28日', '8月29日', '8月30日', '8月31日', '9月1日', '9月2日', '9月3日', '9月4日', '9月5日', '9月6日', '9月7日', '9月8日', '9月9日', '9月10日', '9月11日', '9月12日', '9月13日', '9月14日', '9月15日', '9月16日', '9月17日', '9月18日', '9月19日', '9月20日', '9月21日', '9月22日', '9月23日', '9月24日', '9月25日', '9月26日', '9月27日', '9月28日', '9月29日', '9月30日', '10月1日', '10月2日', '10月3日', '10月4日', '10月5日', '10月6日', '10月7日', '10月8日', '10月9日', '10月10日', '10月11日', '10月12日', '10月13日', '10月14日', '10月15日', '10月16日', '10月17日', '10月18日', '10月19日', '10月20日', '10月21日', '10月22日', '10月23日', '10月24日', '10月25日', '10月26日', '10月27日', '10月28日', '10月29日', '10月30日', '10月31日', '11月1日', '11月2日', '11月3日', '11月4日', '11月5日', '11月6日', '11月7日', '11月8日', '11月9日', '11月10日', '11月11日', '11月12日', '11月13日', '11月14日', '11月15日', '11月16日', '11月17日', '11月18日', '11月19日', '11月20日', '11月21日', '11月22日', '11月23日', '11月24日', '11月25日', '11月26日', '11月27日', '11月28日', '11月29日', '11月30日', '12月1日', '12月2日', '12月3日', '12月4日', '12月5日', '12月6日', '12月7日', '12月8日', '12月9日', '12月10日', '12月11日', '12月12日', '12月13日', '12月14日', '12月15日', '12月16日', '12月17日', '12月18日', '12月19日', '12月20日', '12月21日', '12月22日', '12月23日', '12月24日', '12月25日', '12月26日', '12月27日', '12月28日', '12月29日', '12月30日', '12月31日'
					                			<?php } ?>
											<?php } ?>
			                	        ],
			                			borderColor:'#fff',
			                	        splitLine: {
			                	            lineStyle: {
			                	                color: ['#eee']
			                	            }
			                	        }
			                		}],
			                		yAxis: [{
			                			type: 'value',
			                	        splitLine: {
			                	            lineStyle: {
			                	                color: ['#eee']
			                	            }
			                	        }
			                		}],
			                		series: [
			 		                	<?php foreach($agencies as $k => $agency) { ?>
			 		                	{
				                			name: '<?= $agency['agency_name'] ?>',
				                			type: 'line',
				                			itemStyle: {
				                	            normal: {
				                	                borderWidth:2,
				                	                borderColor:'#80cfa6',
				                					color:'<?= $colors[$k] ?>',
				                	                lineStyle: {
				                	                    width: 2,
				                						color:'<?= $colors[$k] ?>'
				                	                },
				                	            },
				                	            emphasis: {
				                	                borderWidth:0
				                	            }
				                			},
				                	        symbol: 'circle',  // 拐点图形类型
				                	        symbolSize: 3, // 拐点图形大小
				                			data: [<?= implode(',', array_slice($agency['stat'], 0, -1)) ?>],
			                			}<?= $k < $countAgencies -1 ? ',' : '' ?>
			                			<?php } ?>
			                		]
			                	}; // option 结束
		        
		                // 为echarts对象加载数据 
		                echartsObj.setOption(option); 
		            }
		        );
		        
		    </script>
			
			<?php } else { // 线状图结束，饼图开始?>
						
			<div id="t3" class="tab-pane ">
				<style>
					#pies {
						position:relative;
						z-index:1;
						float:left;
						width: 400px;
						height: 350px;
					}
					#pie {
						width: 400px;
						height: 350px;
					}
					#pies:before{
						content: '';
						position: absolute;
						top: 87px;
						left: 113px;
						width: 175px;
						height: 176px;
						background-color: #fff;
						border-radius: 50%;
					}
					
					#data-table {
						display: none;
						margin-left: 200px;
						width: 400px;
						height:350px;
						float: left;
					}
					
					#data-table table{
						height: 100%;
					}
					
					#data-table table td {
						text-align: center !important;
						color: #8a8b8d;
						vertical-align: top !important;
						border: 0;
						background: none;
						border-bottom: 1px solid #fff;
					}
					
					#data-table table tr:first-child td:first-child {
						background-color: #2a84d2
					}
					
					#data-table table tr:first-child+tr td:first-child {
						background-color: #80b3e0
					}
					
					#data-table table tr:first-child+tr+tr td:first-child {
						background-color: #abceee
					}
					
					#data-table table tr td:first-child {
						color: #fff;
						background-color: #d5e6f6
					}
					
					#cbg {
						visibility:hidden;
						position:absolute;
						left:230px;
					}
					
					.sup1 {
						position: absolute;
						top: 60px;
						left: 20px;
						color: #999;
					}
					
					.sup2 {
						position: absolute;
						top: 140px;
						left: 350px;
						z-index: 1;
						color: #999;
					}
					
					.sup3 {
						position: absolute;
						top: 310px;
						left: 80px;
						z-index: 1;
						color: #999;
						display: block;
						color: #000;
						font-size: 28px;
						font-weight: 100
					}
					
					.sup1 b, .sup2 b {
						display: block;
						color: #000;
						font-size: 28px;
						font-weight: 100
					}
					
					.btn-groups {
						overflow: hidden
					}
					
					.btn-groups label {
						margin-left: -1px;
						float: left;
						width: 64px;
						height: 32px;
						line-height: 32px;
						text-align: center;
						border: 1px solid #e0e4e7
					}
					
					.btn-groups label.active, .btn-groups label.active a {
						color: #fff;
						background-color: #2a84d2
					}
					
					.btn-groups label:first-child {
						border-radius: 5px 0 0 5px
					}
					
					.btn-groups label:last-child {
						border-radius: 0 5px 5px 0
					}
					.table > tbody > tr > td{
						height:auto!important;
						padding:0!important;
					}
					</style>
				<div class="btn-groups">
					<?php 
						$url = URL::full();
						for($i = 0; $i<=12; $i++ ) { ?>
					<label <?= $month == $i ? ' class="active"' : '' ?>><a href="<?= URL::addParam($url, 'month', $i) ?>"><?= $i == 0 ? '全年' : $i . '月' ?></a></label>
					<?php } ?>
				</div>
				<div style="margin-top: 50px; height:60%;position: relative; overflow: hidden">
					<?php if($stack) { // 非空才显示，有的月份为空，则不需显示 ?>
					<div class="sup1">
						<b><?= $pie['other'] * 100 . '%' ?></b>其他公司
					</div>
					<div class="sup2">
						<b><?= $pie['current'] * 100 . '%' ?></b>当前页面分销商所所占比例
					</div>
					<?php $typeUnits = [1 => '人次', 2 => '元', 3 => '张', ]; ?>
					<div class="sup3">
						总计：<?= $amounts['total'] ?><?= $typeUnits[$type] ?>
					</div>
					<?php } ?>
					<!--yue统计-->
					<div id="pies"><div id="pie"></div></div>
					<div id="data-table" style="z-index:10000">
						<table class="table">
							<?php foreach($stack as $k => $v) { ?>
							<tr>
								<td style="height: <?= $v['percent'] > 0.01 ? $v['percent'] * 100 . '%' : '1px' ?>"><?= $v['percent'] * 100 . '%' ?></td>
								<td><?= $v['agency_name'] ?></td>
								<td><?= $agencies[$k]['stat']['subtotal'] ?><?= $typeUnits[$type] ?></td>
							</tr>
							<?php } ?>
						</table>
					</div>
					<!--[if IE 8]>
					<script type='text/javascript' src='/js/echarts/excanvas.js'></script>  
					<link rel="stylesheet" href="css/iefix.css" type="text/css" media="screen" />  
					<![endif]-->
					<canvas id="cbg" width="370" height="350"></canvas>
				</div>

				<script>
				$(function(){
					var myChart;
						function newPie(a,b){
							require(
								[
									'echarts',
									'echarts/chart/pie'
								],
								function(ec) {
									myChart = ec.init($('#pie')[0]);
									var obj = {
										title: '产品人次图',
										subtitle: '',
										xx: ['伽马超人旅行社'],
										yy: [{
											value: 200,
											name: '伽马超人旅行社'
										}]
									}	
					
									option = {
										animation: false,
										color: ['#2a84d2', '#e7e7e7'],
										calculable: false,
										series: [{
											name: '访问来源',
											type: 'pie',
											radius: ['50%', '70%'],
											startAngle: a / (a + b) * 180,
											itemStyle: {
												normal: {
													label: {
														show: false
													},
													labelLine: {
														show: false
													}
												}
											},
											data: [{
												value: a,
												name: '直接访问'
											}, {
												value: b,
												name: '邮件营销'
											}]
										}]
									};
									
									myChart.setOption(option);
								});
								
								var h = (2 * Math.PI * 87) * ( a / (a + b));
								var s = 0;
								if(h>270){
									h = 246
								}
								if(h<100){
									s = 100-h
								}
								if(h<220 && h>180){
									s = 100-h
								}
								var x1 =  (350 - h) / 2;
								var x2 =  (350 - h) / 2 + h;	
	
								var canvas=document.getElementById('cbg');
								var ctx=canvas.getContext("2d");
								ctx.fillStyle = '#fff';
								ctx.fillRect(0,0,370,350);
								ctx.fillStyle = '#e8f1f7';
								ctx.beginPath();
								ctx.moveTo(s,x1);
								ctx.lineTo(370,0);
								ctx.lineTo(370,350);
								ctx.lineTo(s,x2);
								ctx.closePath();
								ctx.fill();
								$(canvas).css({'visibility':'visible'});
								
								setTimeout(function(){
									$('#data-table').fadeIn();
								}, 100);
						};
						newPie(<?= $pie['current'] * 100 ?>, <?= $pie['other'] * 100 ?>);
				});
				</script>
			</div>
			
			<?php } // 饼图  ?>
			<?php } ?>
		</div>
		<!-- tab-pane -->
	</div>
</div>
<!--end contentpanel -->
<script>
$(function() {
	$("#distributor-select-search").select2(); //景区查询下拉框 
	$("#agency_names").select2()

	jQuery('.select2').select2({
        minimumResultsForSearch: -1
    });
    /*
  	  省市县显示问题重置 create by ccq
     */
    $('#province').select2('val', '<?= isset($_GET['province_id']) ? $_GET['province_id'] : '' ?>');
    $('#city').select2('val', '<?= isset($_GET['city_id']) ? $_GET['city_id'] : '' ?>');

	//省联动
	$('#province').change(function() {

       var code = $(this).val();

       $('#city').html('<option value="">市</option>');
       $('#area').html('<option value="">县</option>');
       if (code == '') {
           /*
            需要重置页面上的显示
            */
           $('#city').select2('val', '');
           $('#area').select2('val', '');
           $('#city').html('<option value="">市</option>');
           $('#area').html('<option value="">县</option>');
       } else {
           $('#city').html('<option value="">市</option>');
           var html = new Array();
            $.ajaxSetup({  async : false});  
           $.post('/ajaxServer/GetChildern', {id: code}, function(data) {
               for (i in data) {
                   html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
               }
               $('#city').append(html.join(''));
               $('#city,#area').select2();
           }, 'json');
           $.ajaxSetup({  async : true});  
       }
       return false;
	});

	//市切换
	$('#city').change(function() {
       var code = $(this).val();
       if (code == '') {
           $('#area').select2('val', '');
           $('#area').html('<option value="">县</option>');
       } else {
           $('#area').html('<option value="">县</option>');
           var html = new Array();
           $.ajaxSetup({  async : false});  
           $.post('/ajaxServer/GetChildern', {id: code}, function(data) {
               for (i in data) {
                   html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
               }
               $('#area').append(html.join(''));
               $('#area').select2();
           }, 'json');
            $.ajaxSetup({  async : true});  
       }
       return false;
	});
      
});
</script>
