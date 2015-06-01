<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close_rule">×</button>
            <h4 class="modal-title">优惠规则配置（分销商名称）</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-2 control-label">模板:</label>
                <div class="col-sm-10">					  					  	
                    <select id="sel_rule" style="width:300px;padding:0 10px;" data-placeholder="Choose One" class="select2" name="rule">
                        <option selected="selected" value="">请选择优惠规则</option>
                        <?php if(isset($list) && !empty($list)):
                        foreach ($list as $item):?>
                        <option value="<?php echo $item['id']?>"><?php echo $item['name']?></option>
                        <?php   endforeach;  endif; ?>
                        <option value="0">不使用优惠规则</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" type="button" id="rule_add">保存</button>
        </div>
    </div>
</div>

<script type="text/javascript">
 
        $('#rule_add').click(function() {
        
            $.post('/ticket/single/ruleadd',{'discount_id':$("#sel_rule").val(),'id':"<?php echo $id;?>"},function(data){
                    if (data.error) {
                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' +data.msg+ '</div>';
                        $('#verify_return').html(warn_msg);
                    } else{
                        var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功</strong></div>';
                        $('#verify_return').html(succss_msg);
                        setTimeout("location.href= '/#'+'"+window.location.pathname+"'", '500');
                    }
            },'json');
            return false;
        });
</script>