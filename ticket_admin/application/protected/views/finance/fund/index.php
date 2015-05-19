<?php 
$this->breadcrumbs = array('财务管理','资产管理');
?>
<style>
    .box-header {
        background: #f7fafc;
        border-bottom: 1px solid #ebeef0;
        padding: 0 30px;
    }
    .padded {
        padding: 15px;
    }
    .panel-body b {
        font-size: 26px;
    }
    b.red {
        color: #d9534f;
    }
    b.orange {
        color: #f0ad4e;
    }
    b.blue {
        color: #428bca;
    }
    .ui-datepicker { z-index:9999!important }
</style>     
      <div class="contentpanel">        
        <ul class="nav nav-tabs">
            <li class="active"><a href="/finance/fund/index" class="now1"><strong>资产列表</strong></a></li>
            <?php if (!isset($get['open']) || empty($get['open'])){?>
            <li class=""><a href="/finance/fund/index2" class="now2"><strong>提现处理</strong></a></li>
            <?php }?>
        </ul>
        <div class="tab-content mb30">
            <!-- tab-pane -->      
            <div id="t1" class="tab-pane active">
                <div class="table-responsive">
                    <form class="form-inline" method="get" action="/finance/fund/index">
                        <div class="form-group" style="margin: 0 0 10px 0;">
                            <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="start_time" id="start_date" value="<?php echo isset($_GET['start_time'])?$_GET['start_time']:''; ?>" placeholder="开始日期" type="text" readonly="readonly">~
                        </div>
                        <!-- form-group -->
                        <div class="form-group" style="margin: 0 0 10px 0;">
                            <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="end_time" id="end_date" value="<?php echo isset($_GET['end_time'])?$_GET['end_time']:''; ?>" placeholder="结束日期" type="text" readonly="readonly">
                        </div>
                        <div class="form-group" style="margin-left: 10px;">                           
                            <select name="trade_type" class="select2" data-placeholder="交易类型" style="width:150px;padding:0 10px;">
                                <option value="">交易类型</option>
                                <option value="1" <?php echo (isset($get['trade_type']) && $get['trade_type'] == '1') ? ' selected="selected"' : ''; ?>>支付</option>
                                <option value="2" <?php echo (isset($get['trade_type']) && $get['trade_type'] == '2') ? ' selected="selected"' : ''; ?>>退款</option>
                                <option value="3" <?php echo (isset($get['trade_type']) && $get['trade_type'] == '3') ? ' selected="selected"' : ''; ?>>充值</option>
                                <option value="4" <?php echo (isset($get['trade_type']) && $get['trade_type'] == '4') ? ' selected="selected"' : ''; ?>>提现</option>
                                <option value="5" <?php echo (isset($get['trade_type']) && $get['trade_type'] == '5') ? ' selected="selected"' : ''; ?>>应收账款</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 6px;">
                            <div class="input-group input-group-sm" style=" position: relative; top: -2px;">
                                <div class="input-group-btn">
                                    <button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                                        <?php
                                        if (isset($get['org_name']))
                                            echo '机构名称';
                                        else
                                            echo '操作者帐号';
                                        ?>
                                    </button>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            tabindex="-1">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a class="sec-btn" href="javascript:;" data-id="op_account" id="">操作者帐号</a></li>
                                        <li><a class="sec-btn" href="javascript:;" data-id="org_name" id="">机构名称</a></li>
                                    </ul>
                                    <script>
                                        $('.sec-btn').click(function() {
                                            $('#search_label').text($(this).text());
                                            $('#search_field').attr('name', $(this).attr('data-id'));
                                        });
                                    </script>


                                </div>
                                <!-- input-group-btn -->
                                <input id="search_field" name="<?php
                                if (isset($get['op_account']))
                                    echo 'op_account';
                                elseif (isset($get['org_name']))
                                    echo 'org_name';
                                else
                                    echo 'op_account';
                                ?>" value="<?php
                                       if (isset($get['op_account']))
                                           echo $get['op_account'];
                                       elseif (isset($get['org_name']))
                                           echo $get['org_name'];
                                       else
                                           echo '';
                                       ?>" type="text" class="form-control" style="z-index: 0"/>
                            </div>
                        </div>


                        <div class="form-group" style="margin-bottom: 10px;">
                            <input type="hidden" name="is_export" class="is_export" value="0">
                            <input type="hidden" name="open" value="<?php echo isset($get['open'])?$get['open']:''?>">
                            <button  type="submit" class="btn btn-primary btn-sm">查询</button>
                            <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
                        </div>
                </form>
                <div class="box-header">
                    <div class="panel-body padded"> <span>截止<?php echo date('Y年m月d日'); ?>，平台可用余额：
                            <b class="red"><?php echo $total['total_union_money']; ?></b></span> 
                        <span>冻结金额：<b class="orange"><?php echo $total['total_frozen_money']; ?></b></span>
                        <span>合计总额：<b class="blue"><?php echo ($total['total_union_money'] + $total['total_frozen_money']); ?></b></span> </div>
                </div>
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>时间</th>
                            <th>机构名称</th>
                            <th>机构角色</th>
                            <th>操作者账号</th>
                            <th>金额</th>
                            <th>交易类型</th>
                            <th>可提现余额</th>
                            <th>冻结余额</th>
                            <th>账户总余额</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(isset($lists["data"])){
                            foreach ($lists["data"] as $item) {
                           // print_r($lists);
                           // exit;
                            ?>                        
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s',$item['created_at']); ?></td>
                                <td><?php echo $item['org_name']; ?></td>
                                <td><?php echo $item['org_role'] ? '供应商' : '分销商'; ?></td>
                                <td><?php echo $item['op_account']; ?></td>
                                <td <?php echo $item['trade_type'] == '1' ?  'style="color:#cc3399"' : ($item['trade_type'] == '2' ? 'style="color:#cc3399"' : ($item['trade_type'] == '3' ? 'style="color:green"' : ($item['trade_type'] == '4' ? 'style="color:red"' : 'style="color:#cc3399"'))); ?>>
                                    <?php echo $item['trade_type'] == '1' ?  '' : ($item['trade_type'] == '2' ? '' : ($item['trade_type'] == '3' ? '+' : ($item['trade_type'] == '4' ? '-' : ''))); ?><?php echo $item['money']; ?></td>
                                <td  <?php echo $item['trade_type'] == '1' ? 'style="color:#cc3399"' : ($item['trade_type'] == '2' ? 'style="color:#cc3399"' : ($item['trade_type'] == '3' ? 'style="color:green"' : ($item['trade_type'] == '4' ? 'style="color:red"' : 'style="color:#cc3399"'))); ?>>
                                    <?php echo $item['trade_type'] == '1' ? '支付' : ($item['trade_type'] == '2' ? '退款' : ($item['trade_type'] == '3' ? '充值' : ($item['trade_type'] == '4' ? '提现' : '应收账款'))); ?></td>
                                <td><?php echo $item['union_money']; ?></td>
                                <td><?php echo $item['frozen_money']; ?></td>
                                <td><?php echo ($item['frozen_money'] + $item['union_money']) ?></td>                                
                            </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
                <div style="text-align:center" class="panel-footer">
                    <div id="basicTable_paginate" class="pagenumQu">
                        <?php
                        if (!empty($lists)) {
                            $this->widget('common.widgets.pagers.ULinkPager', array(
                                    'cssFile' => '',
                                    'header' => '',
                                    'prevPageLabel' => '上一页',
                                    'nextPageLabel' => '下一页',
                                    'firstPageLabel' => '',
                                    'lastPageLabel' => '',
                                    'pages' => $pages,
                                    'maxButtonCount' => 5, 
                                )
                            );
                        }
                        ?>
                    </div>
                </div>
            </div>
            <!-- tab-pane -->          
            <div id="t2" class="tab-pane">
                
            </div>
               
        </div>
      </div>
      <!-- contentpanel -->   

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>

<?php if(isset($tab)): ?>
<script>
    $(document).ready(function() {
        $('.nav-tabs a[href="#t<?php echo $tab; ?>"]').tab('show');
    });
</script>
<?php endif; ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.select2').select2({
             minimumResultsForSearch: -1
         });
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
			$('form').addClass('clearPart');
			$('form').submit();
			$('form').removeClass('clearPart');
            $('.is_export').attr('value', '0');
        });
    });
</script>

