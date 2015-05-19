<style>
    #ui-datepicker-div .ui-state-disabled {
    color: #eeeeee;
}
.labeltop {
      margin-top: 5px;
}
.ui-datepicker { z-index:9999!important }
</style>
<div class="modal-dialog" style="width: 600px !important;">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">编辑优惠方案</h4>
        </div>
        <form  method="post" action="#" id="form" class="form-horizontal form-bordered">            
            <div class="panel-body nopadding">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right"><span class="text-danger">*</span>方案名称:</div></label>
                    <div class="col-sm-6">
                        <?php if($detail['status'] == 1) {?>
                        <label class="labeltop"><?php echo $detail['title']; ?></label>
                        <?php }else{?>
                        <input type="text" placeholder="请输入方案名称" maxlength="20" tag="方案名称" class="validate[required] form-control" name="title" value="<?php echo $detail['title']; ?>">
                        <?php }?>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>有效期:</div></label>
                    <div class="col-sm-9">
                        <?php if($detail['status'] == 1) {?>
                        <label class="labeltop"><?php echo date('Y-m-d',$detail['start_time']).'~'.date('Y-m-d',$detail['end_time']); ?></label>
                        <?php }else{?>
                        <input type="text" placeholder="" tag="开始日期" class="form-control datepicker validate[required]" id='start_time' name="start_time" readonly style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" value="<?php echo date('Y-m-d',$detail['start_time']); ?>"> ~
                        <input type="text" placeholder="" tag="结束日期" class="form-control datepicker validate[required]" id='end_time' name="end_time" readonly style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" value="<?php echo date('Y-m-d',$detail['end_time']); ?>">
                        <?php }?>
                    </div>
                </div><!-- form-group -->
               <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>充值金额:</div></label>
                    <div class="col-sm-6">
                        <?php if($detail['status'] == 1) {?>
                        <label class="labeltop"><?php echo $detail['num']; ?></label>
                        <?php }else{?>
                        <input type="text" placeholder="请输入充值金额" tag="充值金额"  class="form-control onlyMoney validate[required]" name="num" id="num" value="<?php echo $detail['num']; ?>" />
                        <?php }?>
                    </div>
                </div><!-- form-group -->
                
                
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>赠送抵用券金额:</div></label>
                    <div class="col-sm-6">
                        <?php if($detail['status'] == 1) {?>
                        <label class="labeltop"><?php echo $detail['coupon']; ?></label>
                        <?php }else{?>
                        <input type="text" placeholder="请输入赠送抵用券金额" tag="赠送抵用券金额"  class="form-control onlyMoney validate[required]" name="coupon" id="coupon" value="<?php echo $detail['coupon']; ?>" />
                        <?php }?>
                    </div>
                </div><!-- form-group -->
                
                <div class="form-group">
                    <label class="col-sm-3 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否开启:</div></label>
                    <div class="col-sm-6">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" <?php if($detail['status'] == 1)echo 'checked="checked"'; ?> value="1"  name="status" id="rds1">
                            <label for="rds1">开启</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  <?php if($detail['status'] == 0)echo 'checked="checked"'; ?> value="0"   name="status" id="rds2">
                            <label for="rds2">关闭</label>
                        </div>
                    </div>
                </div><!-- form-group -->
            </div>            
            <div class="modal-footer">
                <input type="hidden" name="id" id="id" value="<?php echo $detail['id']; ?>" />
                <button type="submit" type="button" class="btn btn-success" id="form-submit">保存</button>
                <button type="button"  class="btn btn-default" id="form-cancel"  data-dismiss="modal" aria-hidden="true">取消</button>
            </div>
</form>
        </div>
    
</div>
<script type="text/javascript">   
    $(function() {
        // Date Picker
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
            yearRange: "1995:2065",
            minDate: 0,
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
        $('.datepicker').change(function(){            
           $(this).blur()
        })
        //提示设置
        $('#form').validationEngine({
            autoHidePrompt: true,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true
        });
        //提交表单
        $('#form-submit').click(function() { 
            var obj = $('#form');
            if (obj.validationEngine('validate') === true) {
                $('#form-submit').attr('disabled', true);
                $.post('/finance/rebate/edit', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-submit').attr('disabled', false);
                    } else {
                        alert('编辑成功',function(){window.location.partReload();});
                    }
                }, 'json');
            }
            return false;
        });
    });
</script>