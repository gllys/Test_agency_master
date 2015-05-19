<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">销售额与人次统计</h3>
            <div class="panel-body">
                <form id="query1Form" class="form-inline" method="post" >
                    <div class="form-group" style="margin-right:0">请选择分销商:</div>
                        <div class="form-group" style="width: 200px;">
                            <select name="distributor_id" class="select2" data-placeholder="分销商" style="width: 200px;height:34px;">
                                <option <?php echo ( isset($param['distributor_id']) && $param['distributor_id'] == 'm') ? 'selected="selected"':''; ?> value="m">我的分销商</option>
                                <option <?php echo ( isset($param['distributor_id']) && $param['distributor_id'] == 'p') ? 'selected="selected"':''; ?> value="p">平台分销商</option>
                                <?php  if(isset($agency))  foreach ($agency as $key => $value) {
                                   // $agencys[$value['distributor_id']] = $value['distributor_name'];
                                    $select1 = ( isset($param['distributor_id']) && $param['distributor_id'] == $value['distributor_id']) ? 'selected="selected"':'';
                                    echo '<option '.$select1.' value="'.$value['distributor_id'].'">'.$value['distributor_name'].'</option>';
                                } ?>

                            </select>
                            <input type="hidden" name="tab" value="1">
                        </div>

                        <div class="form-group">
                            <div class="control-label" style="display:inline-block; line-height: 28px;margin-right:10px">订单时间:</div>
                            <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($param['start_date'])?$param['start_date']:date('Y-m-01', strtotime('-1 month'));?>"> ~
                            <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($param['end_date'])?$param['end_date']:date('Y-m-t', strtotime('-1 month'));?>">
                        </div>
                        <!-- form-group -->
                    <div class="form-group">
                        <button type="button" id="query1" class="btn btn-primary btn-sm">确定</button>
                    </div>
                </form>
            </div>


        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-11">
                    <!--------ri统计------>

                    <div class="panel panel-primary-alt noborder" id="c1" style="height:350px">

                    </div>
                    <!-- panel -->
                </div>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">产品人次图</h3>
            <div class="panel-body">
                <form class="form-inline" id="query2Form" method="post" >
                    <div class="form-group" style="margin-right:0;">请选择产品：</div>
                        <div class="form-group">

                            <select name="productid" data-placeholder="Choose One" class="select2" style="display:inline-block;width:300px;padding:0 10px;" id="distributor-select-search">

                               <?php $opids = ''; $option = '';
                               if(isset($productlists)) 
                                foreach ($productlists as $key => $value) {  
                                 $select1 = ( isset($param['productid']) && $param['productid'] == $value['id']) ? 'selected="selected"':'';
                                 $value_id = isset($value['id'])?$value['id']:'';
                                 $value_name = isset($value['name'])?$value['name']:'';
                                 if(!empty($opids)) $opids .= ',';
                                 $opids .= $value_id;
                                 $option .= '<option '.$select1.' value="'.$value_id.'">'.$value_name.'</option>';
                             }?>
                             <option value="<?php echo $opids;?>">全部产品</option>
                             <?php echo $option;?>
                         </select>
                         <input type="hidden" name="tab" value="1">
                     </div>

                     <div class="form-group" style="margin-right:0;width:100px;">
                         <div class="pull-right">
                            <div class="rdio rdio-default mr20 inline-block">
                               <input type="radio" checked="checked" value="1" id="radioDefault" name="radio">
                               <label for="radioDefault">月份</label>
                           </div>
								<!-- <div class="rdio rdio-default mr20 inline-block">
                                    <input type="radio" checked="checked" value="1" id="radioDefault1" name="radio">
                                    <label for="radioDefault1">年份</label>
                                </div> -->

                            </div>
                        </div>
                        <div class="form-group" style="width:100px">
                            <select name="mouth" class="select2" data-placeholder="" style="width:150px;padding:0 10px;">
                                <?php 
                                $month = date('m',time()); 

                                $lastmouth = date('m', strtotime('-1 month'));
                                $daxie=array('1'=>"一",'2'=>"二",'3'=>"三",'4'=>"四",'5'=>"五",'6'=>"六",'7'=>"七",'8'=>"八",'9'=>"九",'10'=>"十",'11'=>"十一",'12'=>"十二");
                                if($month == 1) { $year = date('Y', strtotime('-1 year')); ?>-
                                <option <?php echo ( isset($param['mouth']) && $param['mouth'] == $year.'-12' || !isset($param['mouth']) && $lastmouth==$year.'-12') ? 'selected="selected"':''; ?> value="<?php echo $year.'-12';?>"><?php echo $daxie['12'];?>月</option>
                                <?php }
                                $year = date('Y', time());
                                for ($i=0; $i < 12; $i++) {?>

                                    <option <?php if($i+1 == $month-1){ echo "selected=selected";}?> value="<?php if($i+1<10) $lit = '0'; else $lit= ''; echo $year.'-'.$lit.($i+1);?>">
                                        <?php echo $daxie[$i+1];?>月</option>
                                    <?php //echo ( isset($param['mouth']) && $param['mouth'] == ($i+1) || !isset($param['mouth']) && $lastmouth==($i+1)) ? 'selected="selected" ':' '; ?>
                                <?php } ?> 
                            </select>
                        </div>
                        <div class="form-group">
                        <button type="button" id="query2" class="btn btn-primary btn-sm">确定</button>
                    </div>
                </form>
            </div>


        </div>


        <div class="panel-body">
            <div class="row">
                <div class="col-md-10" style="margin-top: 50px">
                    <!--yue统计-->
                    <div class="panel panel-primary-alt noborder" id="c2" style="height:350px">

                    </div>
                    <!-- panel -->
                </div>
            </div>
        </div>
    </div>

</div>
<?php  
$xAxis = '';
$y1='';
$y2=''; 
if(isset($agencylists)) 
    foreach ($agencylists as $key => $value) {
        //echo $key;exit;
        if($xAxis!='') $xAxis .= ","; 
                // $xAxis .= date('d',$key);
        $xAxis .= "'".$key."'";
        if($y1!='') $y1 .= ","; 
        $y1 .= $value['price_total'];
        if($y2!='') $y2 .= ","; 
        $y2 .= $value['num_total'];
    }

    $xAxis2 = '';
        $y21='';  //var_dump($rencilists);exit;
        if(isset($rencilists) && !empty($rencilists)) 
            foreach ($rencilists as $key => $value) {

                if($key=='other'){
                    if($value['num_total']>0){
                       if($xAxis2!='') $xAxis2 .= ","; 
                       if($y21!='') $y21 .= ",";
                       $xAxis2 .= "'其他'";
                       $y21 .= "{value:".$value['num_total'].", name:'其他'}";
                    }
                }elseif($key=='other_all'){
                    if($value['num_total']>0){
                        if($xAxis2!='') $xAxis2 .= ","; 
                        if($y21!='') $y21 .= ",";
                       $xAxis2 .= "'平台分销商'";
                       $y21 .= "{value:".$value['num_total'].", name:'平台分销商'}";
                    }
               }elseif( $key=='top8' ){
                if(!empty($value)) foreach ($value as $k => $val) {
                    //if($val['num_total']>0){
                        if($xAxis2!='') $xAxis2 .= ","; 
                        if($y21!='') $y21 .= ",";
                        $xAxis2 .= "'".$agencys[$val['distributor_id']]."'";
                        $y21 .= "{value:".$val['num_total'].", name:'".$agencys[$val['distributor_id']]."'}";
                   // }
                }    

        } 
    }
      // echo $xAxis2.'<br/>';
     // echo $y21.'<br/>';
    ?>
    <!-- contentpanel -->



    <script src="/js/esl.js"></script>
    <script>
        require.config({
            paths: {
                echarts: '/js/echarts/build/echarts-map',
                'echarts/chart/map': '/js/echarts/src/map'
            }
        })
        <?php if(!empty($y1)||!empty($y2)){?>
            require(
                [
                'echarts',
                'echarts/chart/line'
                ],
                function(ec) {
                    var myChart = ec.init(document.getElementById('c1'))
                    var obj = {
                        title:'销售额与人次统计',
                        subtitle: '<?php echo isset($var)?$var:''; ?>',
                        xx:[<?php echo $xAxis ? $xAxis:"''";?>],
                        yy1:[<?php echo $y1 ? $y1:"0";?>], 
                        yy2:[<?php echo $y2 ? $y2:"0";?>]
                    } 
                    option = option1(obj);  
                    myChart.setOption(option);
                })
            <?php } else{?>
               $("#c1").text("没有可显示的数据！");
               <?php } ?>

               <?php if(!empty($y21)){?>
                require(
                    [
                    'echarts',
                    'echarts/chart/pie'
                    ],
                    function(ec) {
                        var myChart = ec.init(document.getElementById('c2'));
                        var obj = {
                            title:'产品人次图',
                            subtitle: '<?php echo isset($var)?$var:''; ?>',
                            xx:[<?php echo $xAxis2 ? $xAxis2:"{value:0, name:''}";?>],
                            yy:[<?php echo $y21 ? $y21:"{value:0, name:''}";?>]
                        } 
                        option = option2(obj);         
                        myChart.setOption(option); 
                    }
                    )

                <?php } else{?>
                   $("#c2").text("没有可显示的数据！");
                   <?php } ?>
                   function option1(obj){
                    option = {
                        title: {
                            text: obj.title,
                            subtext: obj.subtitle
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            data: ['销售额', '人次']
                        },
                        toolbox: {
                            show: false,
                            feature: {
                                mark: {
                                    show: true
                                },
                                dataView: {
                                    show: true,
                                    readOnly: false
                                },
                                magicType: {
                                    show: true,
                                    type: ['line', 'bar']
                                },
                                restore: {
                                    show: true
                                },
                                saveAsImage: {
                                    show: true
                                }
                            }
                        },
                        calculable: true,
                        xAxis: [{
                            type: 'category',
                            boundaryGap: false,
                data: obj.xx //X:'周一', '周二', '周三', '周四', '周五', '周六', '周日'
            }],
            yAxis: [{
                type: 'value',
                axisLabel: {
                    formatter: '{value} -'
                }
            }],
            series: [{
                name: '销售额',
                type: 'line',
                data: obj.yy1, /*11, 11, 15, 13, 12, 13, 10*/
                markPoint: {
                    data: [{
                        type: 'max',
                        name: '最大值'
                    }, {
                        type: 'min',
                        name: '最小值'
                    }]
                },
                // markLine: {
                //     data: [{
                //         type: 'average',
                //         name: '平均值'
                //     }]
                // }
            }, {
                name: '人次',
                type: 'line',
                data: obj.yy2, /*1, -2, 2, 5, 3, 2, 0*/
                markPoint: {
                    data: [{
                        type: 'max',
                        name: '最大值'
                    }, {
                        type: 'min',
                        name: '最小值'
                    }
                   /* {
                        name: '周最低',
                        value: -2,
                        xAxis: 1,
                        yAxis: -1.5
                    }*/
                    ] 

                },
                // markLine: {
                //     data: [{
                //         type: 'average',
                //         name: '平均值'
                //     }]
                // }
            }]
        };
        return option;  
    }
    function option2(obj){
        option = {
            title : {
                text: obj.title,
                subtext: obj.subtitle,
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient : 'vertical',
                x : 'left',
                data:obj.xx//<?php echo $xAxis2 ? $xAxis2:"{value:335, name:'直接访问'}";?>//'直接访问','邮件营销','联盟广告','视频广告','搜索引擎'
            },
            toolbox: {
                show : false,
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
            calculable : true,
            series : [
            {
                name:'人次',
                type:'pie',
                radius : '55%',
                center: ['50%', '60%'],
                data:obj.yy,
                startAngle: 0
            }
            ]
        }; 
        return option;  
    }
</script>




<script src="/js/select2.min.js"></script>
<script src="/js/jquery-ui-1.10.3.min.js"></script>
<script>
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

   $(function() {
        $("#distributor-select-search").select2(); //景区查询下拉框             
    });
</script>  

<script type="text/javascript">


    $('#query1').click(function(){
            var d1 = new Date($("input[name='start_date']").val()); //alert(d1);
            var d2 = new Date($("input[name='end_date']").val());
            var d3 = d2.getTime()-d1.getTime();  
            if(d3 < 0) {
                alert('订单时间结束时间必须大于开始时间');
                return false;
           }else  var days = d3/(1000 * 60 * 60 * 24)+1; //相差的天数    
          // alert(days);

          if(days >31){
            alert('最多可选一个月的数据');
            return false;
        }else if($('#query1Form').validationEngine('validate')==true ){ 
            var url = "/finance/stat/agencysales"; 
            $.post(url,$("#query1Form").serialize(),function(data) {
                if (data.flag) {
                    console.log(data.msg);         
                } else {

                    var xAxis = '';
                    var y1='';
                    var y2='';
                    $.each(data.agencylists , function( key , elem ){
                                //console.log('index in arr:' + key + ", corresponding value:" + elem);
                                if(xAxis != ''){ xAxis = xAxis +','; }
                                    // var times=parseInt(key);
                                    // var JsonDateValue = new Date(times);
                                    // xAxis = xAxis +JsonDateValue.getDay();
                                    xAxis = xAxis + key;
                                    if(y1 != ''){ y1 = y1 +','; }
                                    y1 = y1 +elem.price_total;
                                    if(y2 != ''){ y2 = y2 +','; }
                                    y2 = y2 +elem.num_total;

                                });
                    if(y1!='' && y2!=''){
                        var xAxisa = xAxis.split(',');
                        var y1a = y1.split(',');
                        var y2a = y2.split(',');
                        require(
                            [
                            'echarts',
                            'echarts/chart/line'
                            ],
                            function(ec) {
                                var myChart = ec.init(document.getElementById('c1'))
                                var obj = {
                                    title:'销售额与人次统计',
                                    subtitle: '<?php echo isset($var)?$var:''; ?>',
                                    xx:xAxisa,
                                    yy1:y1a, 
                                    yy2:y2a
                                } 
                                option = option1(obj);  
                                myChart.setOption(option);
                            })
                    }else{
                      $("#c1").text("没有可显示的数据！");  
                  }

              }
          },'json');
}
});
$('#query2').click(function(){
    if($('#query2Form').validationEngine('validate')==true){ 
        var url = "/finance/stat/productsales"; 
                /*$("#query2Form").attr("action", url);
                $("#query2Form").submit();*/
               var productids = $("select[name='productid']").val();//alert(productids);
               if(productids ==null){alert('请先选择产品');return false;}
               $.post(url,$("#query2Form").serialize(),function(data) {
                if (data.flag) {
                    console.log(data.msg);
                } else {
                    var xAxis2 = '';
                    var y21 = [];
                    var agency = data.agencys;
                    var rencilists = data.rencilists;
                     // console.log(rencilists);
                     // console.log('index in arr:' + rencilists['top8'].length + ", corresponding value:" + rencilists['other'].num_total)
                    if( rencilists['top8'].length== 0  && rencilists['other_all'].num_total == 0 ){
                                  $("#c2").text("没有可显示的数据！");  
                                  return false;
                                }
                            //var agency = data.agencys;
                            $.each(rencilists , function( key , elem ){
                                var tmpa ={};
                                /*console.log('index in arr:' + key + ", corresponding value:" + elem);*/
                                
                                if(key=='other'){
                                    if(elem.num_total>0){
                                        if(xAxis2 != ''){ xAxis2 = xAxis2 +','; }
                                        xAxis2 = xAxis2 + "其他";
                                        tmpa['value'] = elem.num_total;
                                        tmpa['name'] = '其他';
                                    }
                                }else if(key=='other_all'){
                                    if(elem.num_total>0){
                                        if(xAxis2 != ''){ xAxis2 = xAxis2 +','; }
                                        xAxis2 = xAxis2 + "平台分销商";
                                        tmpa['value'] = elem.num_total;
                                        tmpa['name'] = '平台分销商';
                                    }
                                }else if(key=='top8'){
                                        //var fentop = [];
                                      for (var i = 0; i < elem.length ; i++) {
                                    //       Things[i]
                                    //   };
                                    // for (var i = elem.length - 1; i >= 0; i--) {
                                        var tmpfen ={};
                                        if(xAxis2 != ''){ xAxis2 = xAxis2 +','; }
                                        var idss = elem[i].distributor_id;
                                            // console.log(agency );
                                            // console.log(agency[idss] +idss);
                                            if(agency.idss !='undefined') var name = agency[idss];
                                            else var name = idss ;
                                            xAxis2 = xAxis2 +  name;

                                            tmpfen['value'] = elem[i].num_total;
                                            tmpfen['name'] = name;
                                            y21.push(tmpfen);
                                        };

                                    }
                                    y21.push(tmpa); 
                                   // y21.push(fentop);
                                });
                             // console.log(y21);
                            if(y21!=''){
                                var xAxis2a = xAxis2.split(',');
                                require(
                                    [
                                    'echarts',
                                    'echarts/chart/pie'
                                    ],
                                    function(ec) {
                                        var myChart = ec.init(document.getElementById('c2'));
                                        var obj = {
                                            title:'产品人次图',
                                            subtitle: '<?php echo isset($var)?$var:''; ?>',
                                            xx:xAxis2a,
                                            yy: y21
                                        } 
                                        option = option2(obj);         
                                        myChart.setOption(option); 
                                    }
                                    )
                            }else{
                              $("#c2").text("没有可显示的数据！");  
                          }

                      }
                  },'json');
}
});
</script>
