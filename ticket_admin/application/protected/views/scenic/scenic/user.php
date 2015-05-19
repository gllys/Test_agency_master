<div class="modal-dialog" style="width: 400px">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">供应商绑定，设置账号</h4>
            <div id="return_msg"></div>
        </div>
        <?php if(!isset($scenicInfo)):?>
        <div class="modal-body">
            <form class="form-inline">
                <div class="form-group" style="width: 250px">
                    <label class="col-sm-5 control-label">用户：</label>
                    <div class="col-sm-6">
                        <input style="display:none">
                        <input type="text" autocomplete="off" placeholder="" class="form-control" name="account"/>
                    </div>
                </div>
                <div class="form-group" style="width: 250px">
                    <label class="col-sm-5 control-label">密码：</label>
                    <div class="col-sm-6">
                        <input style="display:none">
                        <input type="password" autocomplete="off" placeholder="" class="form-control" name="password"/>
                    </div>
                </div>
                <div class="form-group" style="width: 250px">
                    <label class="col-sm-5 control-label">重复密码：</label>
                    <div class="col-sm-6">
                        <input style="display:none">
                        <input type="password" autocomplete="off" placeholder="" class="form-control" name="repassword"/>
                    </div>
                </div>
                <div class="from-group" id="chk_pwd" style="color: #FF0000;font-size: 15px;margin-left:10px;display: none">
                    两次密码输入不一致
                </div>
                <input type="hidden" name="landscape_id" value="<?php echo $get['landscape_id']?>">
                <input type="hidden" name="organization_id" value="<?php echo $get['organization_id']?>">
            </form>
        </div>
        <div class="modal-footer">
            <img src="/img/select2-spinner.gif" id="load" style="display: none" >
            <div id="button">
                <button class="btn btn-primary btn-sm" data-dismiss="modal" aria-hidden="true" >取消</button>
                <button class="btn btn-success btn-sm" id="save_bind">保存</button>
            </div>
        </div>
        <?php else:?>
        <div class="modal-body">
            <span class="text-danger">该机构是景区机构，且已与景区<?php echo $scenicInfo['name']?>绑定</span>
        </div>
        <?php endif;?>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        $('#save_bind').click(function(){
            var lan_id = $('input[name=landscape_id]').val();
            var org_id = $('input[name=organization_id]').val();
            var account = $('input[name=account]').val();
            var password = $('input[name=password]').val();
            var repassword = $('input[name=repassword]').val();


            if(password != repassword){
                $('#chk_pwd').show();
                return false;
            }
            $('#button').hide();
            $('#load').show();
            $.post('/scenic/scenic/savebind',{
                landscape_id : lan_id,
                organization_id : org_id,
                account : account,
                password : password
            },function(data){

            },'json')
        })
    })
</script>
