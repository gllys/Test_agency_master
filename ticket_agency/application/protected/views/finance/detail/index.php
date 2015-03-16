<div class="contentpanel">
    <div class="panel panel-default"><div class="panel-heading">
            <h4 class="panel-title">应付账款</h4>
        </div>
        <div class="panel-body">
            <?php if ($detail): ?>
                <input type="hidden" value="<?php echo $detail['id'] ?>" name="id"/>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><span class="mr20">账单日期：<?php echo $detail['created_at'] ?></span>
                            <span id="pay_status">应付账款单支付状态：
                            <?php if($detail['pay_status'] == 0 && $detail['bill_amount'] > 0):?>
                                <b class="text-danger">未打款</b>
                            <?php elseif($detail['bill_amount'] == 0):?>
                                <b class="text-warning">无需打款</b>
                            <?php else:?>
                                <b class="text-success">已打款</b>
                            <?php endif;?>
                            </span>                    
                        </h4>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>单号</th>
                                <th>门票名称</th>
                                <th>预订日期</th>
                                <th>游玩日期</th>
                                <th>取票人</th>
                                <th>取票人手机</th>
                                <th>支付金额</th>
                                <th>退款金额</th>
                                <th>结款金额</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($detail['order_list']): ?>
                                <?php foreach ($detail['order_list'] as $value): ?>
                                    <tr>
                                        <td><?php echo $value['order_id'] ?></td>
                                        <td><?php echo $value['ticket_name'] ?></td>
                                        <td><?php echo $value['ordered_at'] ?></td>
                                        <td><?php echo $value['use_day'] ?></td>
                                        <td><?php echo $value['owner_name'] ?></td>
                                        <td><?php echo $value['owner_mobile'] ?></td>
                                        <td><?php echo $value['payed'] ?></td>
                                        <td><?php echo $value['refunded'] ? $value['refunded'] : '0.00' ?></td>
                                        <td><?php echo $value['bill_amount'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel panel-default">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width:100px">应付账款总额:</th>
                                <td><?php echo $detail['bill_amount'] ?></td>
                            </tr>
                            <tr <?php if($detail['bill_amount'] == 0){echo "style='display:none'";}?>>
                                <th>打款日期:</th>
                                <td><?php echo $detail['payed_at'] == 0 ? '未打款' : $detail['payed_at']?></td>
                            </tr>
                            <tr <?php if($detail['bill_amount'] == 0){echo "style='display:none'";}?>>
                                <th>打款凭证:</th>
                                <td>


                                    <div class="dropzone" id="payed" >
                                        <div class="fallback">
                                            <img id="payed_img"  src="<?php
                                            if (!empty($detail['payed_img'])) {
                                                echo $detail['payed_img'];
                                            } else {
                                                echo '/img/uploadfile.png';
                                            }
                                            ?>" style="max-width:150px;height:150px">
                                            <input type="hidden" class="sp_sxming" name="payed"/>
                                        </div>
                                    </div>




                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="panel-footer">
                        <a class="btn btn-primary" id="bill_finish" <?php if($detail['pay_status'] == 1 || empty($detail['payed_img'])){echo "style='display:none'";} ?>>确认打款</a>
                        <a class="btn btn-default" href="/finance/payment" >返回</a>
                    </div>


                </div>
            <?php endif; ?>		
        </div>
    </div>
</div>    
    <script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
    <script type="text/javascript">
                                    //上传

    </script>
    <script>
        jQuery(document).ready(function() {
            	var button = document.getElementById('payed');
                window.imgField = '';
                new AjaxUpload(button, {
                    action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
                    name: 'file',
                    onSubmit: function(file, ext) {
                        //上传文件格式限制
                        if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                            alert('上传格式不正确');
                            return false;
                        }
                        this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                        window.imgField = 'payed';
                    },
                    onComplete: function(file, data) {
                    }
                });

                window.upload_callback = function(data) {
                    if (data.status != 200) {
                        alert('上传失败！');
                        return false;
                    }
                    $('input[name=' + window.imgField + ']').val(data.msg);
                    $('#' + window.imgField + '_img').attr('src', data.msg);
                    $.ajax({
                        type: "POST",
                        url: "/finance/detail/upimg",
                        data: {
                            payed_img: $('input[name=payed]').val(),
                            id: $('input[name=id]').val()
                        },
                        success: function(data) {
                            if (data.error === 0) {
                                alert('上传凭证成功');
                                if($('#pay_status b').text() == '未打款'){
                                    $('#bill_finish').show();
                                }else{
                                    $('#bill_finish').hide();
                                }
                                
                            } else {
                                alert(data.msg);
                            }
                        },
                        dataType: 'json'
                    })

                }
            $('#bill_finish').click(function() {
                $('#bill_finish').attr('disabled',true);
                $.post('/finance/detail/finish', {id: $('input[name=id]').val(), payed_img: $('input[name=payed_img]').val()}, function(data) {
                    if (data.error === 0) {
                        alert('打款成功');
                        window.location.href = '/finance/payment';
                    } else {
                        alert(data.msg);
                    }
                }, 'json')
                return false;
            })

        });
    </script>
