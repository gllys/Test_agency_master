
    <div class="modal-dialog" style="width:700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">限制分销商规则配置</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-sm-12">					  					  	
                        <div class="rdio rdio-default">
                            <input type="radio" value="0" id="radioDefault1" name="radio" onclick="limitrule0('0')">
                            <label for="radioDefault1">白名单</label>
                        </div>
                        <span>选择白名单后，只允许白名单中的分销商分销，不允许白名单外的任何分销商分销</span>
                    </div>

                    <div class="col-sm-12">	
                        <div class="rdio rdio-default">
                            <input type="radio" value="1" id="radioDefault2" name="radio" onclick="limitrule1('1')">
                            <label for="radioDefault2">黑名单</label>
                        </div>
                        <span>选择黑名单后，不允许黑名单中的分销商分销，允许黑名单外的所有分销商分销</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">					  					  	
                        <select name="" id="sel_r" style="width:300px;padding:0 10px;" data-placeholder="Choose One" class="select2">
                            <option selected="selected" value="">请选择限制分销商清单</option>
                        </select>					
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="r_button" class="btn btn-success" type="button">保存</button>
            </div>
        </div>
    </div>
<script>

function limitrule0(type){
    $.post('/ticket/single/limit',{'type':type},function(data){
        $("#sel_r").html(data);
        $("#sel_r").append('<option value="0">不使用限制清单</option>');
    },'json');
}

function limitrule1(type){
    $.post('/ticket/single/limit',{'type':type},function(data){
         $("#sel_r").html(data);
         $("#sel_r").append('<option value="0">不使用限制清单</option>');
    },'json');
}

$("#r_button").click(function(){
    if($('#sel_r').val() == ''){ alert('请选择规则');}else{
         $.post('/ticket/single/namelist',{'namelist_id':$('#sel_r').val(),'id':"<?php echo $id;?>"},function(data){
                if (data.error) {
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' +data.msg+ '</div>';
                    $('#verify_return').html(warn_msg);
                } else{
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功</strong></div>';
                    $('#verify_return').html(succss_msg);
                    setTimeout("location.href='"+window.location.pathname+"'", '500');
                }
        },'json');
    }
         
});

</script>