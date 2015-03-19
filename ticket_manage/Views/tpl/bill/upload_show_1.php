<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h6 id="modal-formLabel">提现打款</h6>
</div>
<div id="show_msg">
</div>
<div class="modal-body">
    <form action="index.php?c=bill&a=uploadProve&id=<?php echo $billInfo['id'] ?>" method="post" id="file-upload-form1" style="background:#fff">
        <table class="table table-normal">
            <tbody>
                <tr><th width="100">提现申请机构：</th><td><?php echo $billInfo['org_info']['name']; ?></td></tr>
                <tr><th>提现时间：</th><td><?php echo date('Y-m-d H:i:s', $billInfo['created_at']); ?></td></tr>
                <tr><th>提现申请人：</th><td><?php echo $billInfo['apply_account']; ?></td></tr>
                <tr><th>提现申请账户：</th><td><?php echo $billInfo['account']; ?></td></tr>
                <tr><th>开户行：</th><td><?php echo $billInfo['open_bank']; ?></td></tr>
                <tr><th>提现申请金额：</th><td>￥<?php echo $billInfo['money']; ?></td></tr>
                <tr><th>驳回：</th><td><?php if ($billInfo['status'] != 0) {
    echo!empty($billInfo['remark']) ? $billInfo['remark'] : '';
} else { ?><input type="text" name="remark" value="" id="remark"><?php } ?></td></tr>
                <tr><th>打款时间：</th><td>
                        <?php
                        if ($view == 1) {
                            echo $billInfo['paid_at'] ? date('Y-m-d H:i:s', $billInfo['paid_at']) : '---';
                        }
                        ?>       
                    </td></tr>
                <?php if ($billInfo['status'] == 1) { ?>
                    <tr><th>打印凭证：</th><td> <img src="<?php echo $billInfo['paid_img']; ?>" style="width:100px!important;"></td></tr>
                    <?php
                } elseif ($billInfo['status'] == 0) {
                    ?>   
                    <tr><th>上传凭证：</th><td><input type="file" name="attachments"></td></tr>
<?php } ?>
            </tbody>
        </table>
        <?php if ($view == 0) { ?>
            <input type="hidden" name="id" value="<?php echo $billInfo['id'] ?>" id="ids">
            <div style="padding:20px 50px; float: left;"><button class="btn btn-green" type="button" id="file-upload-button1">确认打款</button></div>
            <div style="padding:20px 50px;float: right;"><button class="btn btn-green" type="button" id="bohui">驳回</button></div>
        <?php } ?>  
    </form>
</div>
<script>
    $('#file-upload-button1').click(function() {
        $('#file-upload-form1').ajaxSubmit({dataType: 'json', success: function(data) {
                if ($('[name=attachments]').val() === '') {
                    alert('上传凭证不可为空！');
                    return false;
                }
                if (data) {
                    alert('打款成功');
                    location.href = 'bill_fund2.html';
                } else {
                    alert('打款失败');
                    location.href = 'bill_fund2.html';
                    //var succss_msg = '<div class="alert alert-success"><strong>账款单'+data['data'][0]['id']+'上传凭证成功</strong></div>';
                    //$('#show_msg').html(succss_msg);
                    //location.href='#show_msg';
                }
            }});
        return false;
    });


    $("#bohui").click(function() {
        var remark = $("#remark").val();
        var id = $('#ids').val();
        if (remark.length == 0) {
            alert('驳回理由不可为空！');
            return false;
        } else {
            $.post('index.php?c=bill&a=uploadProve', {id: id, remark: remark, type: 'bohui'}, function(data) {
                if (data.error) {
                    //  var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+驳回操作失败！+'</div>';
                    //  $('#show_msg').html(warn_msg);
                    //  location.href='#show_msg';
                    alert('驳回操作失败');
                } else {
                    //var succss_msg = '<div class="alert alert-success"><strong>账款单'+data['data'][0]['id']+'被驳回</strong></div>';
                    //   $('#show_msg').html(succss_msg);
                    //   location.href='#show_msg';
                    setTimeout("location.href='bill_fund2.html'", 2000);
                }
            });
        }
    })
</script>
