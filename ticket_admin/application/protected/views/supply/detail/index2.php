<?php
$this->breadcrumbs = array('平台销量统计详情', '详情图表');
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
            <form class="form-inline" id="form1" method="get" action="/supply/detail/view2">
            <!--查询-->

                <div class="form-group " >
                    <label>预定日期:</label>
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['start_date']) ? $get['start_date'] : ''; ?>"> ~
                    <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['end_date']) ? $get['end_date'] : '' ?>">
                </div>
               
                <div class="form-group">
                </div>

                <div class="form-group">
                    <select name="type" class="select2" data-placeholder="Choose One" style="width:30px;padding:0 10px;">
                        <?php foreach ($supplyType as $k => $v) { ?>
                            <option value="<?= $k ?>"<?= $k == $type ? ' selected' : '' ?>><?= $v ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-sm">   
                        <input id="search_field" name="name" value="<?php echo isset($get["name"])?$get['name']:'' ?>" type="text" class="form-control" style="z-index: 0" />
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="is_export" class="is_export" value="0">
                    <button class="btn btn-primary btn-sm" type="submit">查询</button>
                    <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
                </div>
            </form>
        </div>
    </div>
    <ul class="nav nav-tabs">
        <li  >
            <a href="/supply/detail/index<?php echo $url; ?>"><strong>列表数据</strong></a>
        </li>
        <li class="active">
            <a href="/supply/detail/index2<?php echo $url; ?>"><strong>图表数据</strong></a>
        </li>
    </ul>
    <div class="tab-content mb30">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-11">    
                       <?php //if(!empty($lists["data"])){?>   
                        <div class="panel panel-primary-alt noborder" id="c1" style="height:350px">

                        </div>
                        <?php //}else{?>
                         <!--div class="panel panel-primary-alt noborder"  style="height:50px">
                            <center><h6 style=""> 没有相关数据</h6></center>
                        </div-->
                        <?php //} ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    /**
     *$day              //日期    
     *$order_num        //订单数量
     *$person_num       //订购人数
     *$amount           //订单金额
     *$receive_amount   //收入金额
     *$refunded         //退款金额
    */
    $day=array();
    $order_num=array();
    $person_num = array();
    $amount=array();
    $receive_amount=array();   
    $refunded=array();
    if (isset($lists['data'])){
        foreach ($lists['data'] as $order){
            $day[] =  $order['day'];
            $order_num[] = intval($order["order_num"]);
            $person_num[] = intval($order["person_num"]);
            $amount[] = round($order["amount"],2);
            $receive_amount[] = round($order["receive_amount"],2); 
            $refunded[] = round($order["refunded"],2);
        }
    }

?>
<script type="text/javascript">
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
                    var myChart=ec.init(document.getElementById('c1'))
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
                                data : <?php echo !empty($day)?json_encode($day): json_encode(array(gmdate("Y-m-d",time()+3600*8))) ; ?>
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
                                data: <?php echo !empty($order_num)?json_encode($order_num):json_encode(array(0)); ?>,
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
                                data: <?php echo !empty($person_num)?json_encode($person_num):json_encode(array(0)); ?>,
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
                                data: <?php echo !empty($amount)?json_encode($amount):json_encode(array(0));  ?>,
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
                                data: <?php echo  !empty($receive_amount)?json_encode($receive_amount):json_encode(array(0));  ?>,
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
                                data: <?php echo !empty($refunded)?json_encode($refunded):json_encode(array(0));  ?>,
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
            })
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
            $('#form1').addClass('clearPart');
            $('#form1').submit();
			$('#form1').removeClass('clearPart');
            $('.is_export').attr('value', '0');
        });
    })
</script>

