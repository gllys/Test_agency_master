<div class="contentpanel" id="maincontent">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                        <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">编辑角色</h4>
                </div><!-- panel-heading -->
                <div id="show_msg"></div>
                <div class="panel-body nopadding">

                    <form id="form" class="form-horizontal form-bordered">
                        <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 角色名称</label>
                            <div class="col-sm-4">
                                <input name="name" id="name" type="text" maxlength="15" tag="角色名称" class="form-control validate[required]" placeholder="" value="<?php echo $model->name; ?>">
                                <input name="id" id="id" type="hidden"  value="<?php echo $model->id; ?>">
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 角色说明</label>
                            <div class="col-sm-4">
                                <textarea id="description" name="description" tag="角色说明" class="form-control validate[required]" rows="5"><?php echo $model->description; ?></textarea>
                            </div>
                        </div><!-- form-group -->

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">角色权限设置</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <?php
                                        $titles = CreateUrl::model()->titles;
                                        $lists = CreateUrl::model()->lists;
                                        $permissions = json_decode($model['permissions'], true);
                                        $i = 0;
                                        foreach ($titles as $key => $title):
                                            $i++;
                                            ?>
                                            <tr>
                                                <td style="text-align:left;width:100px">
                                                   <div class="ckbox ckbox-success">
                                                        <input id="checkboxPrimary<?php echo $i ?>" type="checkbox" name="" value="" id="checkboxPrimary2">
                                                        <label for="checkboxPrimary<?php echo $i ?>"><?php echo $title['content'] ?></label>
                                                    </div>
                                                </td>
                                                <td style="text-align:left">
                                                    <?php
                                                    $_lists = is_array($lists[$key]) ? $lists[$key] : array();
                                                    foreach ($_lists as $item):
                                                        $i++;
                                                        ?>
                                                       <div class="ckbox ckbox-success" style=" margin-right: 15px;">
														   <input id="checkboxPrimary<?php echo $i ?>" type="checkbox" name="permissions[]" value="<?php echo $item['params']['href'] ?>" id="checkboxPrimary3" <?php  echo (isset($item['params']['href']) && $item['params']['href'] != null && isset($permissions) && $permissions != null && in_array($item['params']['href'], $permissions)) ? "checked='checked'": ""; ?>>

                                                            <label for="checkboxPrimary<?php echo $i ?>"><?php echo $item['content'] ?></label>
                                                        </div> 
                                                        <?php
                                                    endforeach;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="panel-footer">
                                <button class="btn btn-primary mr5" type="button" id="save_role">保存</button>
	                            <a class="btn btn-default" href="/system/role/">取消</a>
                            </div>
                        </div>
                    </form>          
                </div><!-- panel-body -->      



            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div>
<script type="text/javascript">
    $(document).ready(function() {

//权限设置
        $('td:nth-child(1) input').click(function() {
            if ($(this).is(':checked')) {
                $(this).parents('tr').find('td:nth-child(2) input').prop('checked', true);
            } else {
                $(this).parents('tr').find('td:nth-child(2) input').prop('checked', false);
            }
        });

        setInterval(function() {
            var trObjs = $('.table-bordered').find('tr');
            for (i in trObjs) {
                var _trObjs = trObjs.eq(i);
                var c = _trObjs.find('td:nth-child(2) input:checked').length
                var i = _trObjs.find('td:nth-child(2) input').length
                if (c == i) {
                    _trObjs.find('td:nth-child(1) input').prop('checked', true);
                } else {
                    _trObjs.find('td:nth-child(1) input').prop('checked', false);
                }
            }
        }, 200);
    });
</script>
<script type="text/javascript">
    $(function() {
        //表单赋值
        $('#save_role').click(function(){
            $('#form').validationEngine({
                promptPosition: 'topRight',
                addFailureCssClassToField: 'error',
                autoHidePrompt: true,
                autoHideDelay: 3000
            });

            if ($('#form').validationEngine('validate') === true) {
				var id = $('#id').val();
				var name = $('#name').val();
			    $.post('/system/role/nameExist', {name:name, id:id}, function (data) {
				    if (data.error == 'fail') {
					    $('#name').PWShowPrompt(data.msg);
						return false;
					}  else if($('#description').val().length > 50) {
						$('#description').PWShowPrompt('角色描述最大50字符！');
					}  else {
						submitForm();
						return true
					}
				}, 'json');

            }
            return false;
        });
		
		window.submitForm = function() {
			$.post('/system/role/saverole/',$('#form').serialize(),function(data){
				if (data.error) {
					alert(data.msg);
				} else {
                    alert(data.msg, function() {
                        location.href = '/site/switch/#/system/role/';
                    });
				}
			},'json');
		}

    });
</script>
