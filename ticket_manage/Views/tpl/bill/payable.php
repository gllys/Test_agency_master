<!DOCTYPE html>
<html>
<?php get_header();?>
<body>
<?php get_top_nav();?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu();?>
<div class="main-content">
<?php get_crumbs();?>
<div id="show_msg"></div>
	
<style>

div.selector{
	margin:0 10px;
	width:200px;
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
.modal .table-normal tbody td{
	text-align:left
	}
</style>
<div class="container-fluid padded">
    <div class="box">
        <div class="box-header">
            <span class="title"> 应付账款</span>
        </div>

        <div class="table-header" style="height:auto;padding-bottom:10px;">
            <form action="">
                <div class="row-fluid" style="margin-bottom:10px;">
                    结算日期:<input type="text" placeholder="" name="settlement_time" style="width:180px;margin:0 10px 0"  value="<?php echo $get['settlement_time'] ?>" class="form-time">
                    支付状态:
                    <select class="uniform" name="pay_status">
                        <option value="">所有</option>
                        <?php foreach ($allBillPayStatus as $allBillPayStatusKey => $allBillPayStatusVal): ?>
                            <option value="<?php echo $allBillPayStatusKey; ?>" <?php if ($allBillPayStatusKey == $get['pay_status']): ?>selected="selected"<?php endif; ?>><?php echo $allBillPayStatusVal; ?></option>
                        <?php endforeach; ?>
                    </select>

                    供应商名称：<input type="text" placeholder="" name="organization_name" value="<?php echo $get['organization_name']; ?>" style="width:180px;margin:0 10px 0">
                    <button class="btn btn-default" style="float:none;">查询</button>
                </div>
            </form>
        </div>

        <div class="content" style="border-bottom:1px solid #ccc">
            <table class="table table-normal">
                <thead>
                    <tr>
                        <td>结算单号</td>
                        <td>供应商</td>
                        <td>账单日期</td>
                        <td>账单类型</td>
                        <td>应付金额</td>
                        <td>订单个数</td>
                        <td>打款状态</td>
                        <!--td>收款状态</td-->
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$billsList): ?>
                        <tr>
                            <td colspan="7">无记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($billsList as $bill): ?>
                            <tr>
                                <td><?php echo $bill['id']; ?></td>
                                <td><?php echo $bill['supply_name']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', $bill['created_at']); ?></td>
                                <td><?php echo $bill['bill_type'] == 3 ? "储值支付" : $bill['bill_type'] == 2 ? "信用支付" : "在线支付"  ?></td>
                                <td><?php echo $bill['bill_amount']; ?></td>
                                <td><?php echo $bill['bill_num'] ?></td>
                                <td><?php if ($bill['pay_status'] == '1' && $bill['bill_amount'] > 0) {
                        echo '<font color="green">已打款</font>';
                    } elseif ($bill['bill_amount'] == 0) {
                        echo '<font color="#BDB76B">无需打款</font>';
                    } else {
                        echo '<font color="red">未打款</font>';
                    } ?></td>
                                <td style="display:none"><?php if ($bill['receipt_status'] == '1' && $bill['bill_amount'] > 0) {
                        echo '<font color="green">已收款</font>';
                    } elseif ($bill['bill_amount'] == 0) {
                        echo '<font color="#BDB76B">无需打款</font>';
                    } else {
                        echo '<font color="red">未收款</font>';
                    } ?></td>
                                <td class="center">
                                    <a href="bill_detail_<?php echo $bill['id']; ?>.html?s=1"><button class="btn btn-default"><i class="icon-zoom-in"></i> 查看</button></a>
        <?php if ($bill['pay_status'] == '0'): ?><a data-toggle="modal" href="#upload-show1" onclick="modal_jump('<?php echo $bill['id']; ?>')" class="btn btn-green" style="<?php if($bill['bill_amount'] == 0){echo "display:none;";}?>"><i class="icon-share-alt"></i>打款</a><?php endif; ?>
                                </td>
                            </tr>
    <?php endforeach; ?>
<?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="dataTables_paginate paging_full_numbers">
<?php echo $pagination; ?>
        </div>
    </div>

    <div id="upload-show1" class="modal hide fade">

    </div>
</div>
		</div>
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/bill/payable.js?v=1" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<script src="Views/js/vendor/daterangepicker.js?v=2"></script>
<script>
$(document).ready(function() {
	$('.form-time').daterangepicker({
        format:'YYYY-MM-DD'
    })
})
</script>
</body>
</html>