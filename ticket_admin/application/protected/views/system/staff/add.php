<div class="contentpanel" id="maincontent">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title">新增用户</h4>
                </div>
                <div id="show_msg"></div>
                <!-- panel-heading -->

                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="repass-form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 用户名</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder=""  tag="用户名" data-validation-engine="validate[required,custom[onlyLetterNumber]]" class="validate[required] form-control" id="account" name="account" autocomplete="off" value=""/>
                                        <input type="text" style="display:none;" id="account"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>密码</label>
                                    <div class="col-sm-6">
                                        <input type="password" placeholder="" tag="密码" data-validation-engine="validate[required,minSize[6],maxSize[16],custom[onlyLetterNumber]]" class="form-control  validate[required,minSize[6],maxSize[16]]" onkeypress="return IsOnlyNumLetter(event)" onblur="this.name='password'" autocomplete="off"/>
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>员工姓名</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" tag="员工姓名" data-validation-engine="form-control validate[required]" class="form-control validate[required]" name="name" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>手机号码</label>
                                    <div class="col-sm-6">
                                        <input type="text" maxlength="11" tag="手机号码" placeholder="" data-validation-engine="form-control validate[required,custom[phone]]"  class="form-control validate[required,custom[phone]]" name="mobile"/>
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>角色</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <select class="select2 col-sm-12 form-control validate[required]" tag="角色" data-prompt-position="topRight:250" id="role_id" name="role_id">
                                                    <option value="">选择</option>
                                                    <?php
                                                    $data = Role::model()->findAllByAttributes(array('status' => 1));
                                                    foreach ($data as $item):
                                                        ?>
                                                        <option tag="角色"  style="height: 37px" value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>状态</label>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <select class="select2 col-sm-12 form-control validate[required]" tag="角色状态"  data-placeholder="Choose One" id="status" name="status">
                                                    <option value="1">启用</option>
                                                    <option value='0'>禁用</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer" style="padding-left:8%">
                            <button class="btn btn-primary mr20" id="buttomsub" style="width:130px;">添加</button>
                            <a class="btn btn-default" href="/system/staff/">取消</a>
                        </div>

                    </form>
                </div>
                <!-- panel-body -->
            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->
</div>
<!-- contentpanel -->
<script>

    jQuery(document).ready(function() {
				
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        $('#role_id').change(function(){
            $('#buttomsub').removeAttr('disabled');
        });

        $('#buttomsub').click(function () {
            $('#buttomsub').attr('disabled', 'disabled');
            $('#repass-form').validationEngine({
                promptPosition: 'topRight',
                addFailureCssClassToField: 'error',
                autoHidePrompt: true,
                autoHideDelay: 3000,
	            maxErrorsPerField: 1
            });


            if ($('#repass-form').validationEngine('validate') === true) {

				if($('#role_id').val() == ''){
					$('#role_id').PWShowPrompt('请选择角色权限！');
					return false;
				} else {
					$.post('/system/staff/saveStaff/', $('#repass-form').serialize(), function (data) {
						if (data.error) {
							var tmp_errors = '';
							if(typeof data.msg === "object") {
								for(k in data.msg) {
                                    tmp_errors += data.msg[k];
								}
							} else {
                                tmp_errors += data.msg;
							}
                            alert(tmp_errors, function() {
                                $('#buttomsub').removeAttr('disabled');
                            });
						} else {
							alert('添加成功！', function() {
                                location.href='/site/switch/#/system/staff/';
                            });
						}
					}, 'json');
				}
            } else {
                $('#buttomsub').removeAttr('disabled');
            }
            return false;
        });

        $('#account').blur(function () {
            if ($('#account').val() === '') {
                return false;
            }
            $.post('/system/staff/accountExist', $(this).serialize(), function (data) {
                if (data.error === 0) {
                    $('#account').validationEngine('showPrompt','用户名已被使用，请输入其他用户名','error');
                    $('#buttomsub').attr('disabled', 'disabled');
                }else{
                    $('#account').validationEngine('hide');
                    $('#buttomsub').removeAttr('disabled');
                }
            }, 'json');
        });

    });
</script>

