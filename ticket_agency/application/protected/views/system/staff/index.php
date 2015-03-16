<?php
$this->breadcrumbs = array('系统管理', '员工管理');
?>
<div class="contentpanel">
    <div id="verify_return"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>姓名</th>
                            <th>账号</th>
                            <th>角色</th>
                            <th>手机号码</th>
                            <th>状态</th>
                            <th width="100">操作</th>
                        </tr>
                    </thead>
                    <tbody id="staff-body">
                        <?php
                        foreach ($lists as $item):
                            ?>
                            <tr>

                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['account']; ?></td>
                                <td><?php
                                    if ($item['is_super']) {
                                        echo '系统管理员';
                                    } else {
                                        $roleid = RoleUser::model()->find('uid=:uid', array(':uid' => $item['id']));
                                        $role_id = $roleid['role_id'];
                                        $rolename = Role::model()->find('id=:id', array(':id' => $role_id));
                                        echo $rolename['name'];
                                    }
                                    ?></td>
                                <td><?php echo $item['mobile']; ?></td>
                                <td><?php echo $item['status'] ? '<font color="green">启用</font>' : '<font color="red">禁用</font>' ?></td>
                                <td>
                                    <a title="修改" href="/system/staff/edit/?id=<?php echo $item['id'] ?>">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <?php if (!$item['is_super']): ?><a title="禁用" href="/system/staff/del/?id=<?php echo $item['id'] ?>" class="del">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a><?php endif ?>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                        <tr>
                            <td colspan="6">

                                <a class="btn btn-success btn-sm" href="/system/staff/add/"><span class="glyphicon glyphicon-plus"></span> 新增</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script type="text/javascript">
    $(function() {
        //选择
        $('a.del').click(function() {
            if (!window.confirm("确定要禁用该用户吗?")) {
                return false;
            }
            $.post($(this).attr('href'), function() {
                window.location.reload();
            });
            return false;
        });
    });

</script>