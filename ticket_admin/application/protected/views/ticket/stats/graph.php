<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/18/15
 * Time: 4:18 PM
 * File: index.php
 */
?>
<div class="contentpanel">
    <ul class="nav nav-tabs">
        <li class="<?php echo $param['type'] == 'detail' ? 'active' : ''?>"><a href="/ticket/stats/detail/range/<?php echo "{$param['range']}/landscape_id/{$param['landscape_id']}/first_date/{$param['first_date']}/last_date/{$param['last_date']}/"?>"><strong>详情</strong></a></li>
        <li class="<?php echo $param['type'] != 'detail' ? 'active' : ''?>"><a href="/ticket/stats/graph/range/<?php echo "{$param['range']}/landscape_id/{$param['landscape_id']}/first_date/{$param['first_date']}/last_date/{$param['last_date']}/"?>"><strong>图表</strong></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="home">
            <div id="t1" class="tab-pane active">
                <p>
                    <select id="menu">
                        <option value="tickets_total">销售数量</option>
                        <option value="sale_money">销售额</option>
                        <option value="used_total">入园数量</option>
                        <option value="refunded_total">退款数</option>
                        <option value="refund_money">退款额</option>
                    </select>
                    景区：<?php echo isset($landscape_name) ? $landscape_name : ''?>
                    日期：<?php echo $param['first_date'] . ' ~ ' .$param['last_date']?>
                </p>
                <div class="panel panel-default" style="height: 375px;padding-top: 15px">
                    <div id="placeholder" style="float:left; width:95%;height:350px;"></div>
                    <p id="choices" style="float:right; width:5%;"></p>
                </div>
            </div>
        </div><!-- tab-pane -->
    </div>
</div>
<script src="/js/echarts-2.2.1/build/dist/echarts-all.js"></script>
<script>

    var data = <?php echo json_encode($lists, JSON_UNESCAPED_UNICODE)?>;

    var lineChart = echarts.init(document.getElementById('placeholder'));
    var option = {
        title: {
            text: '销售数量',
            subtext: '<?php echo isset($landscape_name) ? $landscape_name : ''?>'
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['销售数量']
        },
        toolbox: {
            show: true,
            feature: {
                mark: {show: true},
                dataView: {show: true, readOnly: false},
                magicType: {show: true, type: ['line', 'bar']},
                restore: {show: true},
                saveAsImage: {show: true}
            }
        },
        calculable: false,
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: data['day']
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: [
            {
                name: '销售数量',
                type: 'line',
                data: data['tickets_total']
            }
        ]
    };

    lineChart.setOption(option);

    $(function(){
		$('#menu').select2({});
        $('#menu').change(function(){
            var self = $(this);
            var id = self.val();
            var t = self.find("option:selected").text();
            lineChart.setOption({
                title: {
                    text: t
                },
                legend: {
                    data: [t]
                },
                series: [
                    {
                        name: t,
                        data: data[id]
                    }
                ]
            })
        })
    })
</script>

