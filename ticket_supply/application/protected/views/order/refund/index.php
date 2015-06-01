<?php
$this->breadcrumbs = array('订单', '退款管理');
?>
<div class="contentpanel">
    <style>
        .table tr>*{
            text-align:center
        }
        .ui-datepicker { z-index:9999!important }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <ul class="list-inline">
                <li><h4 class="panel-title">退款管理</h4></li>
                <li><a href="/order/history/help?#5.2" title="帮助文档" class="clearPart" target="_blank">查看帮助文档</a> </li>
            </ul>
        </div>

        <div class="panel-body">
            <form class="form-inline" method="get" action="/order/refund/index" onsubmit="return check();">
                <div class="mb10">
                    <div class="form-group">
                        <input class="form-control" type="text" style="width:150px;" placeholder="门票名称" value="<?php echo isset($_GET['name'])?$_GET['name']:'';?>" name="name">
                    </div>
                    <div class="form-group" style="width:150px;">
                        <select name="distributor_id" class="select2" data-placeholder="分销商"  style="height:34px;">
                            <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分销商&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                            <?php foreach ($distributors_labels as $distributor => $label) : ?>
                                <option <?php echo isset($_GET['distributor_id']) && $distributor == $_GET['distributor_id'] ? 'selected="selectd"' : '' ?> value="<?php echo $distributor ?>"><?php echo $label; ?></option>
                            <?php
                            endforeach;
                            unset($distributor, $label)
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" id="begin_date" name="begin_date" value="<?php if (isset($_GET['begin_date'])) echo $_GET['begin_date'] ?>" placeholder="开始日期" type="text" readonly="readonly">
                        ~
                        <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" id="end_date" name="end_date" value="<?php if (isset($_GET['end_date'])) echo $_GET['end_date'] ?>" placeholder="结束日期" type="text" readonly="readonly">
                    </div>
                    <div></div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <input class="form-control" name="order_id" value="<?php if (isset($_GET['order_id'])) echo $_GET['order_id'] ?>" placeholder="订单编号" type="text" style="width: 324px;">
                    </div>
                    <button class="btn btn-primary btn-xs" type="submit">查询</button>
                </div>
            </form>
        </div>
        

    </div>
    <table class="table table-bordered mb30">
            <thead>
                <tr>
                    <th>退款申请单号</th>
                    <th>订单号</th>
                    <th>申请时间</th>
                    <th>来源</th>
                    <th>门票名称</th>
                    <th>处理进度</th>
                    <th>操作员</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
               <?php                
                foreach ($list as $key=>$item):
               ?> 
                <tr >
                    <td data-target=".bs-example-modal-static"  onclick="point('<?php echo $item['order_id'];?>','<?php echo $item['id'];?>')"data-toggle="modal"><a  class="clearPart" href="javascript:void(0)"><?php echo $key ?></a></td>
                    <td><a href="/order/detail/?id=<?php echo $item['order_id'] ?>"><?php echo $item['order_id'];?></a></td>
                    <td><?php echo date('Y-m-d H:i:s',$item['created_at']);?></td>
                    <td><?php echo isset($org[$item['distributor_id']])?$org[$item['distributor_id']]:''; ?></td>
                    <td><?php echo $item['name'];?></td>                    
                <?php if($item['allow_status'] == 0):?>
                    <td class="text-warning">未审核</td>
                <?php elseif($item['allow_status'] == 1):?>
                	<td class="text-success">已审核</td>
                <?php elseif($item['allow_status'] == 2):?>
                	<td class="text-primary">未操作</td>
                <?php else:?>
                	<td class="text-danger">驳回</td>
                <?php endif;?>
                    <td><?php echo isset($user[$item['op_id']])?$user[$item['op_id']]:''; ?></td>
                <?php if($item['allow_status'] == 0):?>   
                    <td data-target=".bs-example-modal-static"  onclick="point('<?php echo
                    $item['order_id'];?>',
                        '<?php echo $item['id'];?>')"data-toggle="modal"><a href="javascript:void(0)" class="clearPart" >处理</a></td>
                <?php else:?>
                	<td></td>
                <?php endif;?>
                </tr>
               <?php  endforeach;?> 
              
            </tbody>
        </table>

        <div style="text-align:center" class="panel-footer">
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
                    'maxButtonCount' => 5, //分页数量
                        )
                );
                ?>
            </div>
        </div>
</div><!-- contentpanel -->
<div class="modal fade bs-example-modal-static" id="verify-modal-point" tabindex="-1" role="dialog"></div>

<script>
function point(pointid,id){
         $('#verify-modal-point').html('');
        $.get('/order/refund/point/?id='+id+'&order_id='+pointid, function(data) {
            $('#verify-modal-point').html(data);
        });
}
//比较日期大小
function dateCompare(startdate,enddate)   
{   
    var arr=startdate.split("-");    
    var starttime=new Date(arr[0],arr[1],arr[2]);    
    var starttimes=starttime.getTime();   

    var arrs=enddate.split("-");    
    var lktime=new Date(arrs[0],arrs[1],arrs[2]);    
    var lktimes=lktime.getTime();   

    if(starttimes>lktimes){   
        return false;   
    }
    return true; 
}  
//检查搜索条件
function check(){
    var begin_date = $('#begin_date').val();
    var end_date = $('#end_date').val();
    var flag = true;
    if(begin_date != '' && end_date != ''){
        flag = dateCompare(begin_date,end_date);
        if(flag == false){
            $('#end_date').PWShowPrompt('开始日期不能大于结束日期');
        }
    }    
    return flag;
}
$('#begin_date').change(function(){ check();});
$('#end_date').change(function(){ check();});

    jQuery(document).ready(function() {
        // Date Picker
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

        // Select2
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        // This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');

        $('[name=distributor_id]').select2();
    });

</script>
