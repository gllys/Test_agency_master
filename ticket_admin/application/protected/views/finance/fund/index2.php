<?php 
$this->breadcrumbs = array('财务管理','资产管理');
?>
<style>    
    .hlb {color: #428BCA;}
    .hand {cursor:pointer;cursor: hand;}
    .ui-datepicker { z-index:9999!important }
</style>     
      <div class="contentpanel">        
        <ul class="nav nav-tabs">
            <li class=""><a href="/finance/fund/index" class="now1"><strong>资产列表</strong></a></li>
            <li class="active"><a href="/finance/fund/index2" class="now2"><strong>提现处理</strong></a></li>
        </ul>
        <div class="tab-content mb30">
            <!-- tab-pane -->          
            <div id="t1" class="tab-pane">
                
            </div>
            <!-- tab-pane -->      
            <div id="t2" class="tab-pane active">
              <div class="table-responsive">
                  <form class="form-inline" method="get" action="/finance/fund/index2">
                            <div class="form-group" style="margin: 0 0 10px 0;">
                            <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="start_time" value="<?php echo isset($_GET['start_time'])?$_GET['start_time']:''; ?>" placeholder="开始日期" type="text" readonly="readonly">~
                        </div>
                        <!-- form-group -->
                        <div class="form-group" style="margin: 0 0 10px 0;">
                            <input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="end_time" value="<?php echo isset($_GET['end_time'])?$_GET['end_time']:''; ?>" placeholder="结束日期" type="text" readonly="readonly">
                        </div>
                        <div class="form-group" style="margin-left: 20px;">                           
                            <select name="status" class="select2" data-placeholder="提现状态" style="width:150px;padding:0 10px;">
                                <option value="">提现状态</option>
                                <option value="0" <?php echo (isset($data['get']['status']) && $data['get']['status'] == '0') ? ' selected="selected"' : ''; ?>>未打款</option>
                                <option value="1" <?php echo (isset($data['get']['status']) && $data['get']['status'] == '1') ? ' selected="selected"' : ''; ?>>已打款</option>
                                <option value="2" <?php echo (isset($data['get']['status']) && $data['get']['status'] == '2') ? ' selected="selected"' : ''; ?>>驳回</option>
                            </select>
                        </div>
                        <div class="form-group">                           
                            <select name="org_role" class="select2" data-placeholder="机构角色" style="width:150px;padding:0 10px;">
                                <option value="">机构角色</option>
                                <option value="0" <?php echo (isset($data['get']['org_role']) && $data['get']['org_role'] == '0') ? ' selected="selected"' : ''; ?>>分销商</option>
                                <option value="1" <?php echo (isset($data['get']['org_role']) && $data['get']['org_role'] == '1') ? ' selected="selected"' : ''; ?>>供应商</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 6px;">
                            <div class="input-group input-group-sm" style=" position: relative; top: -2px;">
                                <div class="input-group-btn">
                                    <button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                                        <?php
                                        if (isset($data['get']['apply_account']))
                                            echo '申请人帐号';
                                        else if (isset($data['get']['apply_username']))
                                            echo '申请人';
                                        else
                                            echo '机构名称';
                                        ?>
                                    </button>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            tabindex="-1">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a class="sec-btn" href="javascript:;" data-id="org_name" id="">机构名称</a></li>
                                        <li><a class="sec-btn" href="javascript:;" data-id="apply_account" id="">申请人帐号</a></li>
                                        <li><a class="sec-btn" href="javascript:;" data-id="apply_username" id="">申请人</a></li>
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
                                if (isset($data['get']['org_name']))
                                    echo 'org_name';
                                elseif (isset($data['get']['apply_account']))
                                    echo 'apply_account';
                                elseif (isset($data['get']['apply_username']))
                                    echo 'apply_username';
                                else
                                    echo 'org_name';
                                ?>" value="<?php
                                       if (isset($data['get']['org_name']))
                                           echo $data['get']['org_name'];
                                       elseif (isset($data['get']['apply_account']))
                                           echo $data['get']['apply_account'];
                                       elseif (isset($data['get']['apply_username']))
                                           echo $data['get']['apply_username'];
                                       else
                                           echo '';
                                       ?>" type="text" class="form-control" style="z-index: 0"/>
                            </div>
                        </div>


                        <div class="form-group" style="margin-bottom: 10px;">
                        <button  type="submit" class="btn btn-primary btn-sm pull-left">查询</button>
                    </div>
                </form>
                <div class="box-header">
                    <div class="panel-body padded"> <span>截止<?php echo date('Y年m月d日'); ?>，平台可用余额：
                            <b class="red"><?php echo $data['total']['total_union_money']; ?></b></span> 
                        <span>冻结金额：<b class="orange"><?php echo $data['total']['total_frozen_money']; ?></b></span>
                        <span>合计总额：<b class="blue"><?php echo ($data['total']['total_union_money'] + $data['total']['total_frozen_money']); ?></b></span> </div>
                </div>
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <td>编号</td>
                            <td>申请时间</td>
                            <td>机构名称</td>
                            <td>机构角色</td>
                            <td>申请人账号</td>
                            <td>申请人</td>
                            <td>申请金额</td>
                            <td width="130px">收款账户</td>
                            <td>开户行</td>
                            <td>开户人</td>
                            <td>状态</td>
                            <td>打款日期</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(isset($lists)){
                            foreach ($lists as $fund) {
                            ?>
                            <tr>
                                <td><?php echo $fund['id']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', $fund['created_at']); ?></td>
                                <td>
                                    <a href="/finance/fund/index?org_name=<?php echo $fund['org_name']; ?>&open=new" target="_blank"><?php echo $fund['org_name']; ?></a>
                                </td>
                                <td><?php echo $fund['org_role'] ? '供应商' : '分销商'; ?></td>
                                <td><?php echo $fund['apply_account']; ?></td>
                                <td><?php echo $fund['apply_username']; ?></td>
                                <td><?php echo $fund['money']; ?></td>
                                <td><?php echo $fund['account']; ?></td>
                                <td><?php echo $fund['open_bank']; ?></td>
                                <td><?php echo $fund['account_name']; ?></td>
                                <td class="status-success"><?php echo $fund['status']==1 ? '已打款' :($fund['status']==2 ?'驳回':'未打款'); ?></td>
                                <td><?php echo   $fund['paid_at']?date('Y-m-d H:i:s',$fund['paid_at']):'---'; ?></td>
                                <td>
                                    <?php if ($fund['status']=='0') { ?>
                                        <span data-target=".modal-bank" data-toggle="modal" onclick="modal_jump('<?php echo $fund['id']; ?>','0')" class="hlb hand"></i>打款</span> 
                                    <?php } else{ ?>
                                        <span data-target=".modal-bank" data-toggle="modal" onclick="modal_jump('<?php echo $fund['id']; ?>','1')" class="hlb hand"></i> 查看</span>
                                    <?php } ?>
                                </td>
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
    });
    function modal_jump(bid,btype) {
        $('#verify-modal').html('');
        $.get('/finance/fund/detail/?bid='+bid+'&btype='+btype, function(data) {
            $('#verify-modal').html(data);
        });
    }

</script>

