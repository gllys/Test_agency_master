<div id="modal12" style="width:1000px;" class="modal-dialog">
    <div class="modal-content" >
        <div class="modal-header">
            <button id="close_irule" class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
            <h4 class="modal-title">设置分销商策略</h4>
        </div>
        <div class="modal-body" >
            <div class="panel panel-default">
                <form class="form-horizontal form-bordered" id="polform">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <a class="btn btn-primary btn-lg pull-right" href="/#/ticket/policy/">新建分销商策略</a>
                        </div>
                        <input type="hidden" id="ptid" name="ptid" value="<?php echo $id; ?>">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-5" style="margin-left: 7%;">                                    
                                    <select multiple="" class="form-control inline" style="width:100%;height:300px" id="selpol" name="selpol">
                                        <?php foreach($policy_name_arr as $pid => $pname){?>
                                        <option value="<?php echo $pid; ?>" <?php echo $pid==$policy_id?'selected="selected"':''?>><?php echo $pname; ?></option>
                                        <?php }?>
                                    </select>
                                </div>                                
                                <div class="col-sm-5" style="margin-left: 4%;">
                                    <textarea style="width:100%;height:300px;text-align: left;" id="selnote"><?php if($policy_id>0 && key_exists($policy_id, $policy_note_arr)){
                                            echo trim($policy_note_arr[$policy_id]);
                                        }?></textarea>
                                </div>
                            </div>

                        </div><!-- form-group -->
                        <!-- form-group -->


                    </div>
                    <!-- panel-body -->
                    
                </form>

            </div>
            <!-- panel -->           
            <?php foreach ($policy_note_arr as $pid => $pnote){?>
            <input type="hidden" id="note_<?php echo $pid;?>" value="<?php echo $pnote; ?>">
            <?php }?>
        </div>
        <!-- col-md-6 -->
        <div class="modal-footer" style="text-align: left;">
            <button class="btn btn-success btn-lg" type="button" id="savePolicyBtn">保存此分销商策略</button>
        </div>
    </div>
    <!-- row -->
</div>
<script type="text/javascript">
$(document).ready(function() {
    
});
$("#selpol").change(function () {
    $("#selnote").text($("#note_"+$("#selpol").val()).val());
});
//提交表单
$('#savePolicyBtn').click(function() {
    if (!$("#selpol").val()) {
        alert('请选择策略');
        return false;
    }else{
        var obj = $('#polform');
        $.post('/ticket/single/savePolicy', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#savePolicyBtn').attr('disabled', false);
                    } else {
                        alert('设置策略成功',function(){window.location.partReload();});
                    }
                }, 'json');
    }
});
</script>