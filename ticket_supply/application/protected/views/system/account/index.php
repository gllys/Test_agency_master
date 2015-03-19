<?php
$this->breadcrumbs = array('系统管理','修改密码');
?>
<script src="/js/application.js" type="text/javascript"></script>
    
        <style>
            .form-control{width:300px;}
            .col-lg-10{float: left;width: 400px;display: block;}
             .col-lg-10 input{float: left;display: block;}
             .status-error{ color:red;}
        </style>           
    
<div class="main-content">
    <div id="show_msg" ></div>
  <div class="container-fluid padded">
    <div class="box">
      <div class="box-content" style="width:550px">
        <form id="repass-form" action="" method="post">
            <div class="padded">
                        <div class="form-group" style="display:block;padding-bottom: 15px;padding-top: 20px;">
                            <label class="control-label col-lg-2" ><strong class="status-error">*</strong>输入原密码 <span class="note"></span></label>
                                <div class="col-lg-10">
                                    <input type="password" class="validate[required,minSize[6],maxSize[16]] error form-control" name="user[oldpass]" autocomplete="off" id="old_pass">
                                </div>
                        </div>

                    <div class="form-group" style="display:block;padding-bottom: 15px;">
                                <label class="control-label col-lg-2"><strong class="status-error">*</strong>输入新密码 <span class="note"></span></label>
                                <div class="col-lg-10">
                                <input type="password" class="validate[required,minSize[6],maxSize[16]] form-control error" name="user[password]" id="password" autocomplete="off">
                                </div>
                        </div>

                        <div class="form-group" style="display:block;padding-bottom: 15px;">
                                <label class="control-label col-lg-2"><strong class="status-error">*</strong>确认新密码 <span class="note"></span></label>
                                <div class="col-lg-10">
                                <input  type="password" class="validate[required,equals[password],minSize[6],maxSize[16]] form-control error" name="user[confirm_password]" autocomplete="off">
                                </div>
                        </div>

                </div>

                <div class="form-actions">
                        <button class="btn btn-success" type="submit" id="repass-form-button">保存修改</button>
                </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
            $(function() {
                $(document).keydown(function(e) {
                    //回车键
                    if (e.keyCode == 13) {
                        $('#repass-form').submit();
                    }
                });
            });
  </script>  
<script src="/js/repass.js"></script>             

