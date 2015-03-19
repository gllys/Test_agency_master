<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">编辑子景点</h4>
        </div>
        
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">景点名称:</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo $data['name']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">介绍:</label>
                    <div class="col-sm-10">
                        <textarea rows="5" placeholder="" class="form-control" name="description" id="description"><?php echo $data['description'];?></textarea>
                    </div>
                </div>
            </div>
        <input type="hidden" name="id" value="<?php echo $data['id'];?>" id="idval">
            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="editbutton">保存</button>
            </div>
       
    </div>
</div>

<script type="text/javascript">
    $("#editbutton").click(function(){
        $.post('/scenic/Spot/edit/', {'id':$('#idval').val(),'name': $('#name').val(), "description": $('#description').val(),'landscape_id':$('#landscape_id').val()}, function(data) {
                 
                if (data.errors) {
                    var tmp_errors = '';
                    $.each(data.errors, function(i, n) {
                        tmp_errors += n;
                    });
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                    $('#verify_return').html(warn_msg);
                } else{
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
                    $('#verify_return').html(succss_msg);
                    setTimeout("location.href='/scenic/Spot/?id="+$('#landscape_id').val()+"'", '2000');
                }
            }, "json");
            return false; 
      });

</script>