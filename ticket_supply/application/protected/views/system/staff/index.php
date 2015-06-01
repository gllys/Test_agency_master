<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <ul class="list-inline">
                        <li class="pull-right"><a class="btn btn-success btn-sm" href="/system/staff/add/" style="color: #ffffff">新增</a></li>
                        <li><h4 class="panel-title">员工管理</h4></li>
                        <li><a href="/order/history/help?#9.2" title="帮助文档" class="clearPart"
                               target="_blank">查看帮助文档</a> </li>
                    </ul>
                </div>
                <!-- panel-heading -->




            </div>
            <div id="show_msg"></div>
            <!-- panel -->
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                    <tr>
                        <th style="width: 20%">姓名</th>
                        <th style="width: 20%">账号</th>
                        <th style="width: 20%">角色</th>
                        <th>手机号码</th>
                        <th>状态</th>
                        <th style="width: 100px">操作</th>
                    </tr>
                    </thead>
                    <tbody id="staff-body">
                    <?php if(isset($lists) && !empty($lists)):?>
                    <?php
                    foreach ($lists as $item):
                    ?>
                    <tr>

                        <td style="text-align: left"><?php echo $item['name']; ?></td>
                        <td style="text-align: left"><?php echo $item['account']; ?></td>
                        <td><?php
                            if ($item['is_super']) {
                                echo '系统管理员';
                            }
                            else {
                                $roleid   = RoleUser::model()->find('uid=:uid', array(':uid' => $item['id']));
                                $role_id  = $roleid['role_id'];
                                $rolename = Role::model()->find('id=:id', array(':id' => $role_id));
                                echo $rolename['name'];
                            }
                            ?>
                        </td>
                        <td><?php echo $item['mobile']; ?></td>
                        <td><?php echo $item['status'] ? '<font color="green">启用</font>' : '<font color="red">禁用</font>' ?></td>
                        <td>
                            <a  title="修改" href="/system/staff/edit/?id=<?php echo $item['id'] ?>">
                                修改
                            </a>
                            <?php if (!$item['is_super']): ?>
                                <a title="删除" style="color:#FF0000" data-name="<?php echo $item['name']?>" data-id="<?php echo $item['id'] ?>" href="javascript:;" class="del  clearPart">
                                    删除
                                </a>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                        <?php else:?>
                        <tr><td style="text-align: center" colspan="6">暂无员工，请点击新增增加</td></tr>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->

</div>
<script type="text/javascript">
	$(function () {
		//选择
		$('.del').click(function () {
            var id = $(this).attr('data-id');
            var name = '确定删除姓名为' + $(this).attr('data-name') + '的用户吗？';
			PWConfirm(name,function(){

			    $.post('/system/staff/del/id/',{id : id}, function (data) {
                if (data.error) {
                    var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+data.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                    location.href= '/#'+'#show_msg';
                } else {
                    var succss_msg = '<div class="alert alert-success"><strong>删除成功</strong></div>';
                    $('#show_msg').html(succss_msg);
                    window.location.partReload();
                }
			},'json');
        });
			
			return false;
		});
	});

</script>
