<?php
use common\huilian\utils\GET;

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
                    <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
                </div>
            </form>
        </div>
        <!-- panel-body -->
    </div>


    <ul class="nav nav-tabs">
    	<?php foreach($typeNames as $k => $v) { ?>
    	<li<?= $type == $k ? ' class="active"' : '' ?>>
            <a href="/agency/sales/index/type/<?= $k ?>"><strong><?= $v ?>销量统计</strong></a>
        </li>
    	<?php } ?>
    </ul>

    <div class="tab-content mb30">
        <div id="t1" class="tab-pane active">
            <form action="/order/payments/method/" method="post">

            <table class="table table-bordered mb30">
            <thead>
                <tr>   
                    <th><?= $typeNames[$type] ?></th>
                    <th>订单数量</th>
                    <th>订购人数</th>
                    <th>已使用人数</th>
                    <th>未使用人数</th>
                    <th>退款人数</th>
                    <th>订单金额</th>
                    <th><?= $type == 'whole' ? '收入' : '支出' ?>金额</th>
                    <th>退款金额</th>
                    <th>操作</th>                
                </tr>
            </thead>
            <tbody id="staff-body">
            <?php foreach($lists as $list) { ?>    
                <tr> 
                    <td><?= empty($list['owner'][0]['name']) ? '' : $list['owner'][0]['name'] ?></td>
                    <td><?= $list['order_num'] ?></td>
                    <td ><?= $list['person_num'] ?></td>
                    <td><?= $list['used_person_num'] ?></td>
                    <td><?= $list['unused_person_num'] ?></td>
                    <td><?= $list['refunded_person_num'] ?></td>
                    <td><?= $list['amount'] ?></td>
                    <td ><?= $list['receive_amount'] ?></td>
                    <td ><?= $list['refunded'] ?></td>
                    <td>
                        <a href="/agency/sales/view?type=<?= $type; ?>&id=<?= current($list) ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&name=<?= empty($list['owner'][0]['name']) ? '' : $list['owner'][0]['name'] ?>">查看详情</a>
                    </td>
                 </tr>                
                 
             <?php } ?>
            </tbody>
        </table>
    
            </form>

            <div style="margin-top: 20px;">
                <span style="width: 150px;padding-right: 20px;">订单数量：<?php echo $amount['order_num']?></span>
                <span style="width: 150px;padding-right: 20px;">订购人数：<?php echo $amount['person_num']?></span>
                <span style="width: 150px;padding-right: 20px;">已使用人数：<?php echo $amount['used_person_num']?></span>
                <span style="width: 150px;padding-right: 20px;">未使用人数：<?php echo $amount['unused_person_num']?></span>
                <span style="width: 150px;padding-right: 20px;">退款人数：<?php echo $amount['refunded_person_num']?></span>
                <span style="width: 150px;padding-right: 20px;">订单金额：<?php echo $amount['amount']?></span>
                <span style="width: 150px;padding-right: 20px;">收入金额：<?php echo $amount['receive_amount']?></span>
                <span style="width: 150px;">退款金额：<?php echo $amount['refunded']?></span></div>
            <div class="panel-footer">
                <?php
                    if(!empty($lists)) {
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
        </div>
        <!-- tab-pane -->

        <!-- tab-pane -->

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
    
    
    });
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