<?php
use common\huilian\utils\GET;
use common\huilian\models\Widgets;
use common\huilian\utils\TwoDimensionalArray;

?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
<!--start contentpanel -->
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">平台销量统计</h4>
        </div>
        <div class="panel-body">
			<form class="form-inline" id="form1" method="get" action="/agency/sales/index">
            <!--查询-->
                <div class="form-group " >
                    <label>预定日期:</label>
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?= $startDate ?>"> ~
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?= $endDate ?>">
                </div>
               
                <div class="form-group">
                    <select name="type" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                        <?php foreach($typeNames as $k => $v) { ?>
                        <option value="<?= $k ?>"<?= $type == $k ? ' selected' : '' ?>><?= $v ?>名称</option>
                    	<?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-sm">   
                        <input id="search_field" name="name" value="<?= GET::name('name') ?>" type="text" class="form-control" style="z-index: 0" />
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="is_export" class="is_export" value="0">
                    <button class="btn btn-primary btn-sm" type="submit">查询</button>
                </div>
            </form>
        </div>
        <!-- panel-body -->
    </div>


    <ul class="nav nav-tabs">
    	<li<?= $tab == 0 ? ' class="active"' : '' ?>>
            <a href="/agency/sales/view?type=<?= $type; ?>&id=<?= GET::required('id') ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&tab=0&name=<?= GET::name('name') ?>"><strong>列表数据</strong></a>
        </li>
        <li<?= $tab == 1 ? ' class="active"' : '' ?>>
            <a href="/agency/sales/view?type=<?= $type; ?>&id=<?= GET::required('id') ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&tab=1&name=<?= GET::name('name') ?>"><strong>图表数据</strong></a>
        </li>
    </ul>

    <div class="tab-content mb30">
    	<!-- 列表数据开始 -->
    	<?php if($tab == 0) { ?>
        <div id="t1" class="tab-pane active">
            <table class="table table-bordered mb30">
	            <thead>
	                <tr>   
	                    <th>日期</th>
	                    <th>订单数量</th>
	                    <th>订购人数</th>
	                    <th>已使用人数</th>
	                    <th>未使用人数</th>
	                    <th>退款人数</th>
	                    <th>订单金额</th>
	                    <th>收入金额</th>
	                    <th>退款金额</th>              
	                </tr>
	            </thead>
	            <tbody id="staff-body">
	           		<?php foreach($lists as $v) { ?>
					<tr> 
	                    <td><?= $v['day'] ?></td>
	                    <td><?= $v['order_num'] ?></td>
	                    <td ><?= $v['person_num'] ?></td>
	                    <td><?= $v['used_person_num'] ?></td>
	                    <td><?= $v['unused_person_num'] ?></td>
	                    <td><?= $v['refunded_person_num'] ?></td>
	                    <td><?= $v['amount'] ?></td>
	                    <td ><?= $v['receive_amount'] ?></td>
	                    <td ><?= $v['refunded'] ?></td>
					</tr>                
					<?php }?>
	            </tbody>
	        </table>
            <div class="panel-footer">
            	<?= Widgets::pagenation($pages) ?>
            </div>
        </div>
        <!-- 列表数据结束 -->
        <!-- 图表数据开始 -->
        <?php } else { ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-11">    
                        <div class="panel panel-primary-alt noborder" id="c1" style="height:350px"></div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">						
				<a class="btn btn-primary btn-sm" type="button" href="/agency/sales/index/type/<?= $type ?>">返回</a>
			</div>
        </div>	
        <?php } ?>
 		<!-- 图表数据结束 -->
    </div>
</div>
<!--end contentpanel -->
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
        
        /* 图表开始 */
		<?php if($tab == 1) { ?>
        $.getScript("/js/esl.js",function(){
            require.config({
                paths:{
                    echarts:'/js/echarts/build/echarts-map',
                    'echarts/chart/line':'/js/echarts/build/echarts-map'
                }
            })
            require(
                [
                    'echarts',
                    'echarts/chart/line'
                ],
                function(ec){
                    var myChart=ec.init(document.getElementById('c1'));
                    option = {
                        title : {
                            text: '',
                            subtext: ''
                        },
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            data:["订单数量","订购人数","订单金额","收入金额","退款金额"],
                            selectedMode:'single',
                            selected:{
                                "订购人数":false,
                                "订单金额":false,
                                "收入金额":false,
                                "退款金额":false
                            }
                        },
                        calculable : true,
                        xAxis : [
                            {
                                type : 'category',
                                boundaryGap : false,
                                data : [<?= TwoDimensionalArray::implodeQuoteColumns($lists, 'day') ?>]
                            }
                        ],
                        yAxis : [
                            {
                                type : 'value',
                                
                            }
                        ],
                        series : [
                            {
                                name:'订单数量',
                                type:'line',
                                data: [<?= TwoDimensionalArray::implodeQuoteColumns($lists, 'order_num') ?>],
                                markPoint : {
                                    data : [
                                        {type : 'max', name: '最大值'},
                                        {type : 'min', name: '最小值'}
                                    ]
                                },
                                markLine : {
                                    data : [
                                        //{type : 'average', name: '平均值'}
                                    ]
                                }
                            },
                            {
                                name:'订购人数',
                                type:'line',
                                data: [<?= TwoDimensionalArray::implodeQuoteColumns($lists, 'person_num') ?>],
                                markPoint : {
                                    data : [
                                        {type : 'max', name: '最大值'},
                                        {type : 'min', name: '最小值'}
                                    ]
                                },
                                markLine : {
                                    data : [
                                        //{type : 'average', name : '平均值'}
                                    ]
                                },

                            },
                            {
                                name:'订单金额',
                                type:'line',
                                data: [<?= TwoDimensionalArray::implodeQuoteColumns($lists, 'amount') ?>],
                                markPoint : {
                                    data : [
                                        {type : 'max', name: '最大值'},
                                        {type : 'min', name: '最小值'}
                                    ]
                                },
                                markLine : {
                                    data : [
                                        //{type : 'average', name : '平均值'}
                                    ]
                                },

                            },
                            {
                                name:'收入金额',
                                type:'line',
                                data: [<?= TwoDimensionalArray::implodeQuoteColumns($lists, 'receive_amount') ?>],
                                markPoint : {
                                    data : [
                                        {type : 'max', name: '最大值'},
                                        {type : 'min', name: '最小值'}
                                    ]
                                },
                                markLine : {
                                    data : [
                                        //{type : 'average', name : '平均值'}
                                    ]
                                },

                            },
                            {
                                name:'退款金额',
                                type:'line',
                                data: [<?= TwoDimensionalArray::implodeQuoteColumns($lists, 'order_num') ?>],
                                markPoint : {
                                    data : [
                                        {type : 'max', name: '最大值'},
                                        {type : 'min', name: '最小值'}
                                    ]
                                },
                                markLine : {
                                    data : [
                                        //{type : 'average', name : '平均值'}
                                    ]
                                },

                            },
                            
                        ]
                    };                                                   
                    myChart.setOption(option);
                })
        	});
    	<?php } ?>
    	/* 图表结束 */
    	
    });
</script>
<script type="text/javascript">
    $('#child_nav a[href="/supply/sales/"]').parent().addClass('active').parent().show();
</script>
<script type="text/javascript">
    $(function(){
        $('#export').click(function() {
            if ($('#start_date').val() == '')
            {
                $('#start_date').PWShowPrompt('请选择开始日期');
                return false;
            }
            if ($('#end_date').val() == '')
            {
                $('#end_date').PWShowPrompt('请选择结束日期');
                return false;
            }
            $('.is_export').attr('value', '1');
            $('#form1').submit();
            $('.is_export').attr('value', '0');
        });
    })
</script>