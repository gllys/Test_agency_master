<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">编辑子景点</h4>
            <div id="return_msg"></div>
        </div>
        <form action="#" id="form">
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">景点名称:</label>
                    <div class="col-sm-10">
                        <input maxlength="100" type="text" name="name" tag="景点名称" class="validate[required] form-control" id="name" value="<?php echo $data['name']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">介绍:</label>
                    <div class="col-sm-10">
                        <textarea maxlength="255" rows="5" placeholder="" tag="介绍" class="validate[required] form-control" name="description" id="description"><?php echo $data['description'];?></textarea>
                    </div>
                </div>
            </div>
        <input type="hidden" name="id" value="<?php echo $data['id'];?>" id="idval">
            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="editbutton">保存</button>
            </div>
       </form>
    </div>
</div>

<script type="text/javascript">
    //提示设置
         $('#form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true
        });
    $("#editbutton").click(function(){
        if ($('#form').validationEngine('validate') !== true) {
                return false;
        }
        var name =$('#name').val();
        var description =$('#description').val();
        if(name !='' && description !='') {
            $.post('/scenic/scenic/edit/', {
                'id': $('#idval').val(),
                'name': $('#name').val(),
                "description": $('#description').val(),
                'landscape_id': $('#landscape_id').val()
            }, function (data) {

                if (data.errors) {
                    var tmp_errors = '';
                    $.each(data.errors, function (i, n) {
                        tmp_errors += n;
                    });
                    var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                    $('#return_msg').html(warn_msg);
                } else {
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
                    $('#return_msg').html(succss_msg);
                    setTimeout("location.href='/scenic/scenic/view?id=" + $('#landscape_id').val() + "'", '2000');
                }
            }, "json");
        }else{
            $('#return_msg').html('<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>相关数据不可为空！</div>');
        }
            return false; 
      });

</script>