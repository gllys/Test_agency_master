<!DOCTYPE html>
<html>
    <?php get_header(); ?>
    <body>
        <?php get_top_nav(); ?>
        <div class="sidebar-background">
            <div class="primary-sidebar-background"></div>
        </div>
        <?php get_menu(); ?>
        <div class="main-content">
            <?php get_crumbs(); ?>	

            <div id="show_msg"></div>


            <style>
                div.selector{
                    margin:0 10px;
                    width:100px;
                }
                .tab-content{
                    overflow:hidden
                }
                .table-normal tbody td a{
                    margin:0 5px;
                    text-decoration:none;
                }
                .table-normal button{
                    min-width:inherit;
                }
                .table-normal tbody td{
                    text-align:center
                }
                .btn-group ul {
                    min-width: 80px;
                }
                .panel-body b{
                    font-size:26px;
                }
                .panel-body>span{
                    margin-right:30px;
                }
                b.red{
                    color:#d9534f;
                }
                b.orange{
                    color:#f0ad4e;
                }
                b.blue{
                    color:#428bca;
                }
            </style>            
            <div class="container-fluid padded">
                <div class="box">
                    <div class="box-header">
                        <ul class="nav nav-tabs nav-tabs-left">
                            <li class=""><a href="/bill_fund.html">资产列表</a></li>
                            <li  class="active"><a href="/bill_fund2.html">提现处理</a></li>
                        </ul>
                    </div>


                    <div class="tab-content">




                        <div id="t1" class="tab-pane">
                            <div class="table-header" style="height:auto;padding-bottom:10px;">
                                <form action="/bill_fund.html" method="get">
                                    <div class="row-fluid" style="margin-bottom:10px;">
                                        时间选择：<input type="text"  name="created_at" style="width:150px;margin:0 10px 0" class="form-time" value="<?php echo isset($data['get']['created_at']) ? $data['get']['created_at'] : ''; ?>">
                                        交易类型：<select class="uniform" name="trade_type">
                                            <option value="null" selected="selected">--请选择--</option>
                                            <option value="1" <?php echo (isset($data['get']['trade_type']) && $data['get']['trade_type'] == '1') ? ' selected="selected"' : ''; ?>>支付</option>
                                            <option value="2" <?php echo (isset($data['get']['trade_type']) && $data['get']['trade_type'] == '2') ? ' selected="selected"' : ''; ?>>退款</option>
                                            <option value="3" <?php echo (isset($data['get']['trade_type']) && $data['get']['trade_type'] == '3') ? ' selected="selected"' : ''; ?>>充值</option>
                                            <option value="4" <?php echo (isset($data['get']['trade_type']) && $data['get']['trade_type'] == '4') ? ' selected="selected"' : ''; ?>>提现</option>
                                            <option value="5" <?php echo (isset($data['get']['trade_type']) && $data['get']['trade_type'] == '5') ? ' selected="selected"' : ''; ?>>应收账款</option>
                                        </select>
                                        <input type="text" name="op_org" style="width:150px;margin:0 10px 0" value="<?php echo isset($data['get']['op_org']) ? $data['get']['op_org'] : ''; ?>">	
                                        <select class="uniform" name="sel_name">
                                            <option  selected="selected" value="0" >用户编号</option>
                                            <option value="1"  <?php echo (isset($data['get']['sel_name']) && $data['get']['sel_name'] == '1') ? ' selected="selected"' : ''; ?>>用户账号</option>
                                            <option value="2"  <?php echo (isset($data['get']['sel_name']) && $data['get']['sel_name'] == '2') ? ' selected="selected"' : ''; ?>>用户名称</option>
                                        </select>
                                        <button class="btn btn-default" style="float:none;">查询</button>
                                    </div>
                                </form>
                            </div>

                            <div class="box-header">
                                <div class="panel-body padded"> <span>截止<?php echo date('Y年m月d日'); ?>，平台可用余额：
                                        <b class="red"><?php echo $data['total']['total_union_money']; ?></b></span> 
                                    <span>冻结金额：<b class="orange"><?php echo $data['total']['total_frozen_money']; ?></b></span>
                                    <span>合计总额：<b class="blue"><?php echo ($data['total']['total_union_money'] + $data['total']['total_frozen_money']); ?></b></span> </div>
                            </div>

                            <div class="content">
                                <table class="table table-normal order-list">
                                    <thead>
                                        <tr>
                                            <td>编号</td>
                                            <td>申请时间</td>
                                            <td>用户名称</td>
                                            <td>用户角色</td>
                                            <td>用户账号</td>
                                            <td>金额</td>
                                            <td>交易类型</td>
                                            <td>可提现余额</td>
                                            <td>冻结余额</td>
                                            <td>账户总余额</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($lists)) {
                                            foreach ($lists as $funds) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $funds['id']; ?></td>
                                                    <td><?php echo date('Y-m-d H:i:s', $funds['created_at']); ?></td>
                                                    <td><?php echo $funds['org_name']; ?></td>
                                                    <td><?php echo $funds['org_role'] ? '供应商' : '分销商'; ?></td>
                                                    <td><?php echo $funds['op_account']; ?></td>
                                                    <td class="status-success"><?php echo $funds['money']; ?></td>
                                                    <td class="status-success"><?php echo $funds['trade_type'] == '1' ? '支付' : ($funds['trade_type'] == '2' ? '退款' : ($funds['trade_type'] == '3' ? '充值' : ($funds['trade_type'] == '4' ? '提现' : '应收账款'))); ?></td>
                                                    <td><?php echo ($funds['frozen_money'] + $funds['union_money']) ?></td>
                                                    <td><?php echo $funds['frozen_money']; ?></td>
                                                    <td><?php echo $funds['union_money']; ?></td>
                                                </tr>
                                            <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="dataTables_paginate paging_full_numbers">
<?php echo isset($pagination) ? $pagination : ''; ?>
                            </div>
                        </div>

                        <div id="t2" class="tab-pane  active">
                            <div class="table-header" style="height:auto;padding-bottom:10px;">
                                <form action="/bill_fund2.html" method="get">
                                    <div class="row-fluid" style="margin-bottom:10px;">
                                        时间选择：<input type="text"  name="created_at" style="width:150px;margin:0 10px 0" class="form-time" value="<?php echo isset($data['get']['created_at']) ? $data['get']['created_at'] : ''; ?>">
                                        提现状态：<select class="uniform" name="status">
                                            <option value="null" selected="selected">--请选择--</option>
                                            <option value="0" <?php echo (isset($data['get']['status']) && $data['get']['status'] == '0') ? ' selected="selected"' : ''; ?>>未打款</option>
                                            <option value="1" <?php echo (isset($data['get']['status']) && $data['get']['status'] == '1') ? ' selected="selected"' : ''; ?>>已打款</option>
                                            <option value="2" <?php echo (isset($data['get']['status']) && $data['get']['status'] == '2') ? ' selected="selected"' : ''; ?>>驳回</option>
                                        </select>
                                        机构角色：<select class="uniform" name="org_role">
                                            <option value="null" selected="selected">--请选择--</option>
                                            <option value="0" <?php echo (isset($data['get']['org_role']) && $data['get']['org_role'] == '0') ? ' selected="selected"' : ''; ?>>分销商</option>
                                            <option value="1" <?php echo (isset($data['get']['org_role']) && $data['get']['org_role'] == '1') ? ' selected="selected"' : ''; ?>>供应商</option>
                                        </select>
                                        <select class="uniform" name="sel_name">
                                            <option  selected="selected" value="0" >机构名称</option>
                                            <option value="1"  <?php echo (isset($data['get']['sel_name']) && $data['get']['sel_name'] == '1') ? ' selected="selected"' : ''; ?>>申请人账号</option>
                                            <option value="2"  <?php echo (isset($data['get']['sel_name']) && $data['get']['sel_name'] == '2') ? ' selected="selected"' : ''; ?>>申请人</option>
                                        </select>
                                        <input type="text" name="op_org" style="width:150px;margin:0 10px 0" value="<?php echo isset($data['get']['op_org']) ? $data['get']['op_org'] : ''; ?>">	
                                        
                                        <button class="btn btn-default" style="float:none;">查询</button>

                                    </div>
                                </form>
                            </div>




                            <div class="box-header">
                                <div class="panel-body padded"> <span>截止<?php echo date('Y年m月d日'); ?>，平台可用余额：
                                        <b class="red"><?php echo $data['total']['total_union_money']; ?></b></span> 
                                    <span>冻结金额：<b class="orange"><?php echo $data['total']['total_frozen_money']; ?></b></span>
                                    <span>合计总额：<b class="blue"><?php echo ($data['total']['total_union_money'] + $data['total']['total_frozen_money']); ?></b></span> </div>
                            </div>

                            <div class="content">
                                <table class="table table-normal order-list">
                                    <thead>
                                        <tr>
                                            <td>编号</td>
                                            <td>申请时间</td>
                                            <td>机构名称</td>
                                            <td>机构角色</td>
                                            <td>申请人账号</td>
                                            <td>申请人</td>
                                            <td>申请金额</td>
                                            <td>收款账户</td>
                                            <td>开户行</td>
                                            <td>开户人</td>
                                            <td>状态</td>
                                            <td>打款日期</td>
                                            <td>操作</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($list)) {
                                            foreach ($list as $fund) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $fund['id']; ?></td>
                                                    <td><?php echo date('Y-m-d H:i:s', $fund['created_at']); ?></td>
                                                    <td><?php echo $fund['org_name']; ?></td>
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
                                                            <a data-toggle="modal" href="#upload-show" onclick="modal_jump('<?php echo $fund['id']; ?>','0')" class="btn btn-green"><i class="icon-share-alt"></i>打款</a> 
                                                        <?php } else{ ?>
                                                            <a data-toggle="modal" href="#show_look" onclick="modal_look('<?php echo $fund['id']; ?>','1')"><button class="btn btn-default"><i class="icon-zoom-in"></i> 查看</button></a>
                                                         <?php } ?>
                                                    </td>
                                                </tr>
    <?php }
} ?> 
                                    </tbody>
                                </table>
                            </div>

                            <div class="dataTables_paginate paging_full_numbers">
<?php echo isset($pagination) ? $pagination : ''; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div id="upload-show" class="modal hide fade"></div>
        <div id="show_look" class="modal hide fade"></div>


        <script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
        <script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
        <script src="Views/js/common/common.js"></script>
        <link href="Views/css/daterangepicker.css" rel="stylesheet">
        <script src="Views/js/vendor/date.js"></script>
        <script src="Views/js/vendor/moment.js"></script>
        <script src="Views/js/vendor/daterangepicker.js?v=2"></script>

        <script>
    $(document).ready(function() {
        $('.form-time').daterangepicker({
            format: 'YYYY-MM-DD'
        });
    })


    //弹框层数值
    function modal_jump(obj,view) {
        $('#upload-show').html();
        $.get('index.php?c=bill&a=uploadShow1&id=' + obj+'&view='+view, function(data) {
            $('#upload-show').html(data);
        });
    }
    
    
    //弹框层数值
    function modal_look(obj,view) {
        $('#show_look').html();
        $.get('index.php?c=bill&a=uploadShow1&id=' + obj+'&view='+view, function(data) {
            $('#show_look').html(data);
        });
    }

        </script>


    </body>
</html>

