<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">新增子景点</h4>
        </div>
        
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">景点名称:</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control" maxlength="15" id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">介绍:</label>
                    <div class="col-sm-10">
                        <textarea rows="5" placeholder="" class="form-control" style="word-break: break-all; word-wrap:break-word;" name="description" id="description"></textarea>
                    </div>
                </div>
            </div>
      
            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="buttonsub">保存</button>
            </div>
       
    </div>
</div>

<script type="text/javascript">
    $("#buttonsub").click(function(){
        var name =$('#name').val();
        var description =$('#description').val();
        if(name !='' && description !=''){
           $.post('/scenic/Spot/add/', {'name': $('#name').val(), "description": $('#description').val(),'landscape_id':$('#landscape_id').val()}, function(data) {
                 
                if (data.errors) {
                    var tmp_errors = '';
                    $.each(data.errors, function(i, n) {
                        tmp_errors += n;
                    });
                    alert(tmp_errors);
                } else{
                    alert('操作成功!', function() {
                        location.href='/site/switch/#/scenic/Spot/?id='+$('#landscape_id').val();
                    });
                }
            }, "json"); 
        }else{
            alert('相关数据不可以为空！');
        }
        return false; 
      });

</script>