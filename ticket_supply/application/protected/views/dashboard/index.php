<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/17/14
 * Time: 3:17 PM
 */
$this->breadcrumbs = array('首页','工作台');
?>
<div class="contentpanel">
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-default" style="height:100px">
				  <table class="table table-bordered"  style="height:100px">
					<tbody>
					<tr>
						<th width="130">待处理退款申请单</th>
                                                <td><a href="/order/refund/index/allow_status/0"><?php echo $mun1;?></a></td>
					</tr>
					<tr>
						<th>待查看消息</th>
                                                <td><a href="/system/message/all"><?php echo $mun2;?></a></td>
					</tr>
					<tr>
						<th>即将到期产品提醒</th>
						<td><a href="/system/message/rem/"><?php echo $mun3;?></td>
					</tr>
					</tbody>
				  </table>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel panel-default" style="height:100px">
				<div class="panel-body">
        <iframe allowtransparency="true" frameborder="0" width="385" height="96" scrolling="no" src="http://tianqi.2345.com/plugin/widget/index.htm?s=2&z=3&t=0&v=0&d=3&bd=0&k=&f=&q=1&e=1&a=1&c=54511&w=385&h=96&align=center"></iframe>
				</div>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">今日订单统计</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-primary-alt noborder">
						<div class="panel-heading noborder">
							<!--div class="panel-icon"><i class="fa fa-dollar"></i></div-->
							<div class="media-body">
								<h5 class="md-title nomargin">今日订票数量(张)</h5>
								<h1 class="mt5"><?php echo $mun4;?></h1>
							</div><!-- media-body -->
						</div><!-- panel-body -->
					</div><!-- panel -->
				</div>
				<div class="col-md-6">
					<div class="panel panel-primary-alt noborder">
						<div class="panel-heading noborder">
							<!--div class="panel-icon"><i class="fa fa-dollar"></i></div-->
							<div class="media-body">
								<h5 class="md-title nomargin">今日订单总额(元)</h5>
								<h1 class="mt5"><?php echo $mun5;?></h1>
							</div><!-- media-body -->
						</div><!-- panel-body -->
					</div><!-- panel -->
				</div>
			</div>
			<div class="row">
				<div id="d2" style="height:500px"></div>
			
			</div>		
		</div>
	</div>
</div><!-- contentpanel -->
<script src="/js/esl.js"></script>

<script>
	//window.onerror = function(){return true};
require.config({
	paths:{
		echarts:'/js/echarts/build/echarts-map',
		'echarts/chart/map':'/js/echarts/build/echarts-map',
		'echarts/chart/pie':'/js/echarts/build/echarts-map'
	}
})

require(
	[
		'echarts',
		'echarts/chart/pie',
		'echarts/chart/map'
	],
function(ec){
var myChart=ec.init(document.getElementById('d2'))
option = {
    title : {
        text: '供应商日销售量统计',
        subtext: '已付款产品销售量(张)',
        x:'center'
    },
    tooltip : {
       // trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        orient : 'vertical',
        x : 'left',
        data:['<?php if(isset($list[0])){ echo $list[0]['name'];}?>','<?php if(isset($list[1])){ echo $list[1]['name'];}?>','<?php if(isset($list[2])){ echo $list[2]['name'];}?>','<?php if(isset($list[3])){ echo $list[3]['name'];}?>','<?php if(isset($list[4])){ echo $list[4]['name'];}?>']
    },
    toolbox: {
      //  show : true,
        feature : {
            mark : {show: true},
            dataView : {show: true, readOnly: false},
            magicType : {
                show: true, 
                type: ['pie', 'funnel'],
                option: {
                    funnel: {
                        x: '25%',
                        width: '50%',
                        funnelAlign: 'left',
                        max: 1548
                    }
                }
            },
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    calculable : false,
	series: [
		{
			name: '日销售量比例',
			type: 'pie',
			radius: '55%',
			center: ['50%', '60%'],
			data: [<?php if(isset($total)){
				$va = array();
				foreach ($list as $item){
					$va[] = '{value:'.$item['ticket_nums'].', name:"'.$item['name'].'"}';
				}
				echo implode(',', $va);
			}
			?>]
		}
	]
};
                    
  myChart.setOption(option);
})

</script>
