<?php
    $this->breadcrumbs = array('平台销量统计详情', '详情列表');
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
            <form class="form-inline" id="form1" method="get" action="/supply/detail/view">
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
        <!-- panel-body -->
    </div>


    <ul class="nav nav-tabs">
        <li class="active" >
            <a href="/supply/detail/index<?php echo $url; ?>"><strong>列表数据</strong></a>
        </li>
        <li >
            <a href="/supply/detail/index2<?php echo $url; ?>"><strong>图表数据</strong></a>
        </li>
    </ul>

    <div class="tab-content mb30">
        <div id="t1" class="tab-pane active">
            <form action="/order/payments/method/" method="post">
            
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
            <?php if (isset($lists['data'])) : foreach ($lists['data'] as $order) : ?>    
                <tr> 
                    <td><?php echo $order['day'] ?></td>
                    <td><?php echo $order['order_num'] ?></td>
                    <td ><?php echo $order['person_num'] ?></td>
                    <td><?php echo $order['used_person_num'] ?></td>
                    <td><?php echo $order['unused_person_num'] ?></td>
                    <td><?php echo $order['refunded_person_num'] ?></td>
                    <td><?php echo $order['amount'] ?></td>
                    <td ><?php echo $order['receive_amount'] ?></td>
                    <td ><?php echo $order['refunded'] ?></td>
                 </tr>                
                 
             <?php
                endforeach;
                endif;
            ?>
            </tbody>
        </table>
    
            </form>
            <div class="panel-footer">
                <?php
                    if (isset($lists['data'])) {
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

