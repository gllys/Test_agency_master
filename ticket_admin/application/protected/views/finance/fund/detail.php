<div class="modal-dialog" style="width: 600px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">提现打款</h4>
        </div>
        <form  method="post" action="#" id="form" class="form-horizontal form-bordered">            
            <div class="panel-body nopadding">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">提现申请机构:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo $billInfo['org_info']['name']; ?></label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">提现时间:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo date('Y-m-d H:i:s', $billInfo['created_at']); ?></label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">提现申请人:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo $billInfo['apply_account']; ?></label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">提现申请账户:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo $billInfo['account']; ?></label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">开户行:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo $billInfo['open_bank']; ?></label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">提现申请金额:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo '￥'.$billInfo['money']; ?></label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">驳回:</div></label>
                    <div class="col-sm-6">
                        <?php if ($billInfo['status'] != 0) { ?>
                        <label class="control-label">
                        <?php     echo empty($billInfo['remark']) ? '':$billInfo['remark']; ?>
                        </label>
                        <?php  } else { ?>
                        <input type="text" name="remark" value="" id="remark" class="form-control">
                        <?php  } ?>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">打款时间:</div></label>
                    <div class="col-sm-6">
                        <label class="control-label">
                        <?php
                        if ($view == 1) {
                            echo $billInfo['paid_at'] ? date('Y-m-d H:i:s', $billInfo['paid_at']) : '---';
                        }
                        ?>
                        </label>
                    </div>
                </div><!-- form-group -->
                <?php  if ($billInfo['status'] == 1) { ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">打印凭证:</div></label>
                    <div class="col-sm-6">
                        <img src="<?php echo $billInfo['paid_img']; ?>" style="width:200px!important;">
                    </div>
                </div><!-- form-group -->                    
                <?php
                        } elseif ($billInfo['status'] == 0) {
                ?>  
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right">上传凭证:</div></label>
                    <div class="col-sm-6">
                        <div  id="proof" style="width:200px; height: 200px;" class="dropzone">
                            <div class="fallback">
                                <img id="proof_img"  src="/img/uploadfile.png" style="bottom: 0;left: 0;margin: auto;max-width: 200px;max-height:200px;position: absolute;right: 0;top: 0;">
                                <input id="proof_input" type="hidden" class="sp_sxming" name="proof" value="<?php echo isset($jingqu_info['images'][0]['url']) ? $jingqu_info['images'][0]['url']: ''; ?>"/>
                            </div>
                        </div>
                    </div>
                </div><!-- form-group -->
                <?php  } ?>                
            </div>   
            <?php if ($view == 0) { ?>
            <input type="hidden" name="id" value="<?php echo $billInfo['id'] ?>" id="ids">            
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="putform">确认打款</button>
                <button type="button" class="btn btn-danger" id="bohui">驳回</button>
            </div>
            <?php } ?>
        </form>
    </div>    
</div>
<!-- 图片上传-->
<script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
<!--<script type="text/javascript" src="/js/jquery.nailthumb.1.1.js"></script>-->
<script type="text/javascript">
    <?php if ($billInfo['status'] == 0){?>
    //上传
    window.imgField = '';
    new AjaxUpload('#proof', {
        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
        name: 'file',
        onSubmit: function(file, ext) {
            //上传文件格式限制
            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                alert('上传格式不正确');
                return false;
            }
            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
            window.imgField = 'proof';
            $('#putform').attr('disabled',"true");            
            $('#bohui').attr('disabled',"true"); 
        },
        onComplete: function(file, data) {
        }
    });
    <?php }?>
    window.upload_callback = function(data) {
        if (data.status != 200) {
            alert('上传失败！');
            return false;
        }
        $('input[name=' + window.imgField + ']').val(data.msg);
        $('#' + window.imgField + '_img').attr('src', data.msg);
        $('#putform').removeAttr("disabled"); 
        $('#bohui').removeAttr("disabled");
    }

    $('#putform').click(function() {
         if ($('[name=proof]').val() == '' || typeof($('[name=proof]').val()) == undefined) {
            $('#proof').PWShowPrompt('请上传凭证！');
            return false;
        }else{
            $('#putform').attr('disabled',"true");            
            $('#bohui').attr('disabled',"true");            
        }
        if ($('#form').validationEngine('validate') == true) {
            $.post('/finance/fund/uploadProve', $('#form').serialize(), function(data) {
                if (data.msg == 'succ') { 
                    alert('保存成功！');
                    window.location.partReload();                     
                } else {
                    alert('保存失败！'+data.msg);
                    $('#putform').removeAttr("disabled"); 
                    $('#bohui').removeAttr("disabled");
                }
            }, "json")
        }

        return false;
    })
    //驳回
    $("#bohui").click(function() {
        var remark = $("#remark").val();
        var id = $('#ids').val();
        if (remark.length == 0) {
            alert('请输入驳回理由！');
            return false;
        } else {
            $('#putform').attr('disabled',"true");            
            $('#bohui').attr('disabled',"true");
            $.post('/finance/fund/uploadProve', {id: id, remark: remark, type: 'bohui'}, function(data) {
                if (data.indexOf('succ') > 0) { 
                    alert('操作成功',function(){window.location.partReload();});
                } else {                    
                    alert('驳回操作失败');
                    $('#putform').removeAttr("disabled"); 
                    $('#bohui').removeAttr("disabled");
                }
            });
        }
    });
</script>