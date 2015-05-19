<style>
    .revise{
        border: 0;
    }
</style>
<div class="modal-dialog" style="width: 400px;margin-top: 150px">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" style="font-size:18px">账号管理</h4>
            <?php if(isset($message)):?><div class="text-danger" style="margin-left: 40px"><?php echo $message?></div><?php endif;?>
            <div id="return_msg"></div>
        </div>
            <div class="modal-body">
                <form class="form-inline">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">用户：</label>
                        <div class="col-sm-6">
                            <input style="display:none">
                            <input style="width:200px;cursor: pointer;cursor: hand;background-color: #ffffff" type="text" autocomplete="off" value="<?php echo isset($userInfo) ? $userInfo['account'] : ''?>" tag="用户名" class="form-control validate[required,custom[account],minSize[6],maxSize[20]]" name="account"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">密码：</label>
                        <div class="col-sm-6">
                            <input style="display:none">
                            <input style="width:200px;cursor: pointer;cursor: hand;background-color: #ffffff" type="text" autocomplete="off" value="<?php echo isset($userInfo) ? $userInfo['password_str'] : ''?>" tag="密码" class="form-control validate[required,custom[onlyLetterNumber],minSize[6],maxSize[16]]" name="password"/>
                        </div>
                    </div>
                    <input type="hidden" value="<?php echo $get['landscape_id']?>" name="landscape_id">
                    <input type="hidden" value="<?php echo isset($userInfo) ? $userInfo['id'] : ''?>" name="user_id">
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success btn-sm" id="reset" style="<?php echo isset($message) ? 'display:none' : ''?>">修改</button>
                <img src="/img/select2-spinner.gif" id="load" style="display: none" >
                <div id="button" style="<?php echo isset($userInfo) ? 'display:none' : ''?>">
                    <button class="btn btn-primary btn-sm" data-dismiss="modal" aria-hidden="true">取消</button>
                    <button class="btn btn-success btn-sm" id="save_user">保存</button>
                </div>
            </div>
    </div>
</div>
<script src="/js/jquery.validationEngine.js"></script>
<script src="/js/jquery.validationEngine-zh-CN.js"></script>
<script>
    jQuery(document).ready(function() {
        <?php if(isset($userInfo)):?>
            $('input[name=account]').addClass('revise').attr('readonly',true);
            $('input[name=password]').addClass('revise').attr('readonly',true);
        <?php endif;?>

        $('#reset').click(function(){
            $('input').removeClass('revise').removeAttr('readonly');
            $('#reset').hide();
            $('#button').show();
        })

        $('form').validationEngine({
            promptPosition: 'topRight',
            autoHidePrompt: true,
            autoHideDelay: 3000
        });

        $('#save_user').click(function() {
            var lan_id = $('input[name=landscape_id]').val();
            var user_id = $('input[name=user_id]').val();
            var account = $('input[name=account]').val();
            var password = $('input[name=password]').val();

            $('#button').hide();
            $('#load').show();
            if ($('form').validationEngine('validate') === true){
                $.post('/scenic/scenic/saveuser', {
                    landscape_id: lan_id,
                    account: account,
                    password: password,
                    user_id: user_id
                }, function (data) {
                    if (data.error) {
                        alert(data.msg, function() {
                            $('#load').hide();
                            $('#button').show();
                        });
                    } else {
                        alert('保存成功！', function() {
                            window.location.partReload();
                        });
                    }
                }, 'json')
        }else{
                $('#load').hide();
                $('#button').show();
            }
            return false;
        })
    })
</script>
