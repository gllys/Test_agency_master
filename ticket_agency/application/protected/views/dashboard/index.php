<?php
$this->breadcrumbs = array('首页', '工作台');
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<div class="contentpanel">
    <!--div class="panel panel-default">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th width="130">可用余额</th>
                    <td style="font-size:18px;color:red;width:200px">3700.00</td>
                    <td style="text-align:left">
                        <a href="" class="btn btn-primary">充值</a> 
                        <a href="" class="btn btn-success">提现</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div-->
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="130">未支付订单</th>
                            <td><a href="/order/history/view/status/unpaid"><?php echo isset($num1) ? $num1 : '0';?></a></td>
                        </tr>
                        <tr>
                            <th>未退款订单</th>
                            <td><a href="/order/refund/?orderat[]=&orderat[]=&pay_app_id=&status=0&order_id="><?php echo isset($num2) ? $num2 : '0';?></a></td>
                        </tr>
                        <tr>
                            <th>购物车</th>
                            <td><a href="/ticket/cart/"><?php echo isset($num3) ? $num3 : '0';?></a></td>
                        </tr>
                        <tr>
                            <th>我的收藏</th>
                            <td><a href="/ticket/favorites/"><?php echo isset($num4) ? $num4 : '0';?></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default" style="height:102px">
                <div class="panel-body">
                     <iframe allowtransparency="true" frameborder="0" width="385" height="96" scrolling="no" src="http://tianqi.2345.com/plugin/widget/index.htm?s=2&z=3&t=0&v=0&d=3&bd=0&k=&f=&q=1&e=1&a=1&c=54511&w=385&h=96&align=center"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-11">
                    <!--------ri统计------>
                      
                    <div class="panel panel-primary-alt noborder" id="c1" style="height:350px">

                    </div><!-- panel -->
                </div>
                <div class="col-md-10" style="margin-top: 50px">
                    <!--------yue统计------>
                    <div class="panel panel-primary-alt noborder" id="c2" style="height:350px">

                    </div><!-- panel -->
                </div>
            </div>
        </div>
    </div>

</div><!-- contentpanel -->
	<script src="/js/esl.js"></script>
<script>
require.config({
	paths:{
		echarts:'/js/echarts/build/echarts-map',
		'echarts/chart/map':'/js/echarts/build/echarts-map'
	}
})

require(
	[
		'echarts',
		'echarts/chart/bar',
		'echarts/chart/map'
	],
function(ec){
var myChart=ec.init(document.getElementById('c1'))
option = {
    title : {
        text: '日统计',
        subtext: '已结款订单'
    },
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['总金额(元)','产品总量(张)'],
        selectedMode:false
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            data : [<?php  for($i=1;$i<=date('t');$i++){if($i != date('t')){ echo "'".$i."'".',';}else{ echo "'".$i."'";} }?>]
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'总金额(元)',
            type:'bar',
            data:[<?php for($i=1;$i<=date('t');$i++){if($i != date('t')){ echo "'".$d[$i]['money_amount']."'".',';}else{ echo "'".$d[$i]['money_amount']."'";} };?>],
            calculable : false,
            markPoint : {
                data : [
                   // {type : 'max', name: '最大值'},
                    //{type : 'min', name: '最小值'}
                ]
            },
            markLine : {
                data : [
                  //  {type : 'average', name: '平均值'}
                ]
            }
        },
        {
            name:'产品总量(张)',
            type:'bar',
            data:[<?php for($i=1;$i<=date('t');$i++){if($i != date('t')){ echo "'".$d[$i]['ticket_nums']."'".',';}else{ echo "'".$d[$i]['ticket_nums']."'";} };?>],
            calculable : false,
            markPoint : {
                data : [
                  //  {type : 'max', name: '最大值'},
                   // {type : 'min', name: '最小值'}
                ]
            },
            markLine : {
                data : [
                  //  {type : 'average', name : '平均值'}
                ]
            }
        }
    ]
};
                    
                    
  myChart.setOption(option);
})


require(
	[
		'echarts',
		'echarts/chart/bar',
		'echarts/chart/map'
	],
function(ec){
var myChart=ec.init(document.getElementById('c2'))
option = {
    title : {
        text: '月统计',
        subtext: '已结款订单'
    },
    tooltip : {
        trigger: 'axis'
    },
    legend: {
       data:['总金额(元)','产品总量(张)'],
       selectedMode:false
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'总金额(元)',
            type:'bar',
            data:[<?php for($i=1;$t=12,$i<=$t;$i++){if($i != $t){ echo "'".$m[$i]['money_amount']."'".',';}else{ echo "'".$m[$i]['money_amount']."'";} };?>],
            calculable : false,
            markPoint : {
                data : [
                   // {type : 'max', name: '最大值'},
                   // {type : 'min', name: '最小值'}
                ]
            },
            markLine : {
                data : [
                  //  {type : 'average', name: '平均值'}
                ]
            }
        },
        {
            name:'产品总量(张)',
            type:'bar',
            data:[<?php for($i=1;$t=12,$i<=$t;$i++){if($i != $t){ echo "'".$m[$i]['ticket_nums']."'".',';}else{ echo "'".$m[$i]['ticket_nums']."'";} };?>],
            calculable : false,
            markPoint : {
                data : [
                   // {type : 'max', name: '最大值'},
                   // {type : 'min', name: '最小值'}
                ]
            },
            markLine : {
                data : [
                   // {type : 'average', name : '平均值'}
                ]
            }
        }
    ]
};
                                     
  myChart.setOption(option);
})
</script>
