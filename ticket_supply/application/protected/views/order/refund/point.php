
<div   style="width: 800px;margin: 0 auto;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"><div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">退款申请单</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th width="120">订单号：</th><td><?php echo $info['order_id']; ?></td></tr>
                        <tr><th>张数：</th><td><?php echo $info['nums']; ?></td></tr>
                        <tr><th>申请时间：</th><td><?php echo date('Y-m-d', $info['created_at']); ?></td></tr>
                        <tr><th>退款时间：</th><td><?php echo ($info['allow_status'] == 0 ? '' : date('Y-m-d', $info['updated_at'])); ?></td></tr>
                        <tr><th>退款金额：</th><td><?php echo $info['money']; ?></td></tr>
                        <tr><th>审核状态：</th><td><?php echo $info['allow_status'] == 0 ? "未审核" : ($info['allow_status'] == 1 ? '已审核' : ($info['allow_status'] == 2 ? '未操作' : '驳回')); ?></td></tr>
                        <tr><th>申请理由：</th><td><?php echo $info['remark'];
if ($info['allow_status'] == 0) { ?>
                                <button id="agree">同意</button><?php } ?></td></tr>
                        <tr><th>驳回理由：</th><td><?php if ($info['allow_status'] == 0) { ?>
                                <input type="text" name="reject_reason" id="reject">
                                <button id="bohui">驳回</button><?php } else {
    echo $info['reject_reason'];
} ?>
                            </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $("#agree").click(function() {
        $.get('/order/refund/point1', {"idnum": 1, "id":"<?php echo $id; ?>"}, function(data) {
            if (data.error == 0) {
                alert('退款成功');
                window.location.reload();
            }else{
                alert(data.msg);
            }
        }, 'json')
        return false;
    });

    $("#bohui").click(function() {
        $.get('/order/refund/point1', {'reject_reason': $("#reject").val(),'id':"<?php echo $id; ?>",'reject': 1}, function(data) {
            if (data.error == 0) {
                alert('驳回成功');
                window.location.reload();
            }else{
                alert(data.msg);
            }
        }, 'json')
        return false;
    });
</script>