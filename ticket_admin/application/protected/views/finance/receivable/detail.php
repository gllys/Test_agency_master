<div class="modal-dialog modal-lg" style="width: 950px;">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">应收账款单明细</h4>
    </div>
    <div class="modal-body" style="padding:0;">
        <table class="table table-bordered">
        <thead>
            <tr>
                <td colspan="8">
                    <span class="mr20">账单日期：<?php echo $detail['created_at']?></span>  
                    <span>应收账款单支付状态：
                    <?php if($detail['pay_status'] == 0 && $detail['bill_amount'] != 0):?>
                        <b class="text-danger">未打款</b>
                    <?php elseif($detail['bill_amount'] == 0):?>
                        <b class="text-warning">无需打款</b>
                    <?php else:?>
                        <b class="text-success">已打款</b>
                    <?php endif;?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>订单号</th>
                <th>分销商名称</th>
                <th>门票名称</th>
                <th>预订日期</th>
                <th>游玩日期</th>
                <th>支付信用金额</th>
                <th>退款信用金额</th>
                <th>结款金额</th>
            </tr>
        </thead>
        <tbody>
        <?php if($detail['order_list']):?>
            <?php foreach ($detail['order_list'] as $value):?>
          <tr>
            <td><b class="text-warning"><?php echo $value['order_id'];?></b></td>
            <td><?php echo $value['agency_name']?></td>
            <td><?php echo $value['ticket_name']?></td>
            <td><?php echo $value['ordered_at']?></td>
            <td><?php echo $value['use_day']?></td>
            <td><?php echo $value['payed']?></td>
            <td><?php echo $value['refunded']?></td>
            <td><?php echo $value['bill_amount']?></td>
          </tr>
            <?php endforeach;?>
        <?php endif;?>
          <tr>
            <th>应付账款总额:</th>
            <td colspan="7"><?php echo $detail['bill_amount']?></td>
          </tr>
          <tr <?php if($detail['bill_amount'] == 0){echo "style='display:none'";}?>>
            <th>打款日期:</th>
            <td colspan="7"><?php echo $detail['payed_at'] == 0 ? '未打款' : $detail['payed_at']?></td>
          </tr>
        </tbody>
        </table>
        
    </div>
</div>
</div>