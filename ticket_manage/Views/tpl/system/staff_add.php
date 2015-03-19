<!DOCTYPE html>
<html>
<?php get_header(); ?>

<body>
<?php get_top_nav(); ?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu(); ?>


<div class="main-content">
    <?php get_crumbs(); ?>
    <div id="show_msg">

    </div>

    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title"><i class="icon-edit"></i>新增员工</span>
            </div>
            <div class="box-content">
                <form class="fill-up" action="index.php?c=system&a=saveStaff" method="post" id="staff-form">
                    <div class="row-fluid">
                        <ul class="padded separate-sections">
                            <li class="input">
                                <label>组员：</label>

                                <div class="row-fluid">
                                    <?php if ($redmineUsers): ?>
                                        <?php if (count($redmineUsers) <= 6): ?>
                                            <?php foreach ($redmineUsers as $redmineUser): ?>
                                                <div class="span2">
                                                    <div>
                                                        <input type="checkbox" name="group_members[]" class="icheck"
                                                               id="redmineUser_<?php echo $redmineUser['id']; ?>"
                                                               value="<?php echo $redmineUser['id']; ?>"
                                                               <?php if ($addedList && in_array($redmineUser['id'], $addedList)): ?>checked="checked"<?php endif; ?>>
                                                        <label
                                                            for="icheck2"><?php echo $redmineUser['lastname'] . $redmineUser['firstname']; ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php for ($i = 0; $i < 6; $i++): ?>
                                                <div class="span2">
                                                    <?php for ($j = 0; $j < ceil(count($redmineUsers) / 6); $j++): ?>
                                                        <?php if ($redmineUsers[$i + 6 * $j]): ?>
                                                            <div>
                                                                <input type="checkbox" name="group_members[]"
                                                                       class="icheck"
                                                                       id="audience_<?php echo $redmineUsers[$i + 6 * $j]['id']; ?>"
                                                                       value="<?php echo $redmineUsers[$i + 6 * $j]['id']; ?>"
                                                                       <?php if ($addedList && in_array($redmineUsers[$i + 6 * $j]['id'], $addedList)): ?>checked="checked"<?php endif; ?>>
                                                                <label for="icheck2">
                                                                    <?php echo $redmineUsers[$i + 6 * $j]['lastname'] . $redmineUsers[$i + 6 * $j]['firstname']; ?>
                                                                </label>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <style>
                        .btn-green {
                            min-width: inherit
                        }

                        .table-normal tbody td {
                            text-align: center
                        }

                        .table-normal tbody td a {
                            text-decoration: none
                        }

                        .table-normal tbody td i {
                            margin-left: 10px
                        }

                        .popover-content .btn-default {
                            min-width: inherit;
                            margin-left: 10px;
                        }

                        .popover-content button i {
                            margin: 0 !important
                        }

                        .modal .table-normal tbody td {
                            text-align: left
                        }

                        #sms textarea {
                            width: 100%;
                            height: 100px;
                        }
                    </style>
                    <div class="form-actions">
                            <table class="table table-normal">
                                <thead>
                                <tr>
                                    <td>帐号</td>
                                    <td>姓名</td>
                                    <td>角色</td>
                                    <td>手机号码</td>
                                    <td rowspan="2">
                                        <button type="button" id="add-user-button" class="btn btn-blue">保存</button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td width="22%"><input type="text" id="user_account" name="account"></td>
                                    <td width="22%"><input type="text" id="user_name" name="name"></td>
                                    <td>
                                        <select class="uniform" id="user_role" name="role_id">
                                            <option value=''>请选择角色权限</option>
                                            <?php if ($roles): ?>
                                                <?php foreach ($roles as $key => $value): ?>
                                                    <option value="<?php echo $value['id'] ?>"
                                                            <?php if ($value['id'] == $info['role_id']): ?>selected="selected"<?php endif; ?>><?php echo $value['name']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td width="21%">
                                    <td width="25%"><input type="text" id="user_mobile" name="mobile"></td>
                                </tr>
                                </tbody>
                            </table>
                    </div>
                    <div class="form-actions" style="text-align:right">
                        <input type="hidden" value="<?php echo $type; ?>" name="type">
                        <button type="button" id="staff-add-button" class="btn btn-blue">保存</button>
                        <a class="btn btn-default" href="system_staff.html">取消</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/system/staff_add.js"></script>
<script>
    $('#add-user-button').click(function(){
        var account = $.trim($("#user_account").val());
        var name = $.trim($('#user_name').val());
        var role_id = $('#user_role').val();
        var mobile = $('#user_mobile').val();
        if(account==""||account==undefined){alert("帐号不能为空");return false;}
        if(name==""||name==undefined){alert("姓名不能为空");return false;}
        if(role_id==""||role_id==undefined){alert("角色不能为空");return false;}
        $.post('index.php?c=system&a=addUser',
            {account:account,name:name,role_id:role_id,mobile:mobile},function(data){
            if(data.error==1){
                alert(data.message);
            }else{
                alert("保存成功");
                location.reload();
            }
        },"json");
        return false;
    });
</script>
</body>
</html>

