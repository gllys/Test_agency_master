<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                        <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
                    </div><!-- panel-btns -->
                    <h4 class="panel-title">添加角色</h4>
                </div><!-- panel-heading -->
                <div id="show_msg"></div>
                <div class="panel-body nopadding">

                    <form id="form" class="form-horizontal form-bordered">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>角色名称:</label>
                            <div class="col-sm-4">					  					  	
                                <input name="name" id="name" tag="角色名称" type="text" maxlength="15"  class="form-control validate[required]" placeholder="">		
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>角色说明:</label>
                            <div class="col-sm-4">					  					  	
                                <textarea name="description" tag="角色说明" class="form-control validate[required,maxSize[50]]" rows="5"></textarea>		
                            </div>
                        </div>

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
                                        $i = 0;
                                        foreach ($titles as $key => $title):
                                            $i++;
                                            ?>
                                            <tr>
                                                <td style="text-align:left;width:100px">
                                                    <div class="ckbox ckbox-success">
                                                        <input id="checkboxPrimary<?php echo $i ?>" type="checkbox" name="" value="">
                                                        <label for="checkboxPrimary<?php echo $i ?>"><?php echo $title['content'] ?></label>
                                                    </div>
                                                </td>
                                                <td style="text-align:left">
                                                    <?php
                                                    $_lists = $lists[$key];
                                                    foreach ($_lists as $item):
                                                        $i++;
                                                        ?>
                                                        <div class="ckbox ckbox-success" style=" margin-right: 15px;">
                                                            <input id="checkboxPrimary<?php echo $i ?>" type="checkbox" name="permissions[]" value="<?php echo $item['params']['href'] ?>">
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
        $('#save_role').click(function(){
            $('#form').validationEngine({
                promptPosition: 'topRight',
                addFailureCssClassToField: 'error',
                autoHidePrompt: true,
                autoHideDelay: 3000
            });
			
			/**
			 * 检查是否重复权限名
			 */
            if ($('#form').validationEngine('validate') === true) {
				var name = $('#name').val();
				var org_id = <?php echo Yii::app()->user->org_id; ?>;
			    $.post('/system/role/nameExist', {name:name, org_id:org_id, id:0}, function (data) {
				    if (data.error == 'fail') {
					    $('#name').PWShowPrompt(data.msg);
						return false;
					} else {
						submitForm();
						return true
					}
				}, 'json');

            }
            return false;
        });
		
		/**
		 * 提交表单
		 */
		window.submitForm = function() {
			$.post('/system/role/saverole/',$('#form').serialize(),function(data){
				if (data.error) {
					var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+data.msg+'</div>';
					$('#show_msg').html(warn_msg);
					location.href='#show_msg';
				} else {
					var succss_msg = '<div class="alert alert-success"><strong>'+ data.msg +'</strong></div>';
					$('#show_msg').html(succss_msg);
					location.href = '/system/role/';
				}
			},'json');
		}
		
    });
</script>
