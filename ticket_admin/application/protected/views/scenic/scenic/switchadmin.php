<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">切换管理权</h4>
        </div>
        <form class="form-horizontal form-bordered" method="post" action="#" id="form">
            <input type="hidden" name="id" value="<?php echo $_GET['land_id'] ?>" />
            <div class="modal-body">
                
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="form-group" style="margin:0; margin-left: 30px;">
                            <select class="select2 lan" name="organization_id" data-placeholder="Choose One" style="width:300px;padding:0 10px;">
                                <option value="">请选供应商</option>
                               <?php
                                $_data = Organizations::api()->getAll();
                                foreach ($_data as $item) {
                                    $selected = "";
                                    if (isset($_GET['org_id']) && $_GET['org_id'] == $item['id']) {
                                        continue;
                                    }
                                    echo '<option  value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div><!-- form-group -->

            </div>
            <div class="modal-footer">
                <button type="submit" id="form-button" class="btn btn-success">保存</button>
                <button  class="btn btn-default" aria-hidden="true" data-dismiss="modal" type="button">取消</button>
            </div>
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        $('select').select2({});
        //提交表单
        $('#form-button').click(function() {
            $.post('/scenic/scenic/switchadmin/',$('#form').serialize(), function(data) {
                if (data.error) {
                    alert(data.msg);
                } else {
                    alert('切换供应商成功', function() {
                    window.setTimeout(function(){
                           window.location.partReload(); 
                    },500);
                      //  location=location ;
                    });
                }
            }, 'json'); 
            return false;
        });
    });
</script>
