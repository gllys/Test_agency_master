<div   style="width: 800px;margin: 0 auto;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"><div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">退款申请单</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th width="120">订单号：</th><td><?php echo $info['order_id'];?></td></tr>
                        <tr><th>张数：</th><td><?php echo $info['nums'];?></td></tr>
                        <tr><th>申请时间：</th><td><?php echo date('Y-m-d',$info['created_at']);?></td></tr>
                        <tr><th>退款时间：</th><td><?php echo ($info['allow_status']==0?'':date('Y-m-d',$info['updated_at']));?></td></tr>
                        <tr><th>退款金额：</th><td><?php echo $info['money'];?></td></tr>
                        <tr><th>退款状态：</th><td><?php echo $info['status']==0 ? "退款中" :($info['status']==1 ?'退款成功':'退款失败'); ?></td></tr>
                        <tr><th>申请理由：</th><td><?php echo $info['remark'];?></td></tr>
                        <tr><th>驳回理由：</th><td><?php echo $info['reject_reason'];?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>