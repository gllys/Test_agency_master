<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading" style="padding-left:10px;">
            <h4 class="panel-title pull-left">员工管理</h4>
        </div>
        <div id="show_msg"></div>
        <div class="table-responsive">
            <table class="table table-bordered mb30">
                <thead>
                <tr>
                    <th>
                        <div class="ckbox ckbox-primary" style="margin-left:17px;">
                            <input type="checkbox" id="checkbox-allcheck">
                            <label for="checkbox-allcheck" class="allcheck">全选</label>
                        </div>
                    </th>
                    <th>姓名</th>
                    <th>账号</th>
                    <th>角色</th>
                    <th>手机号码</th>
                    <th>状态</th>
                    <th width="100">操作</th>
                </tr>
                </thead>
                <tbody id="staff-body">
                <?php if(isset($lists) && !empty($lists)):?>
                    <?php foreach($lists as $item):?>
                <tr>
                    <td style="text-align: center">
                        <?php if (!$item['is_super']): ?>
                        <div class="ckbox ckbox-primary" style="margin-left: 17px;">
                            <input type="checkbox" class="ids" id="checkbox<?php echo $item['id']?>" value="<?php echo $item['id']?>">
                            <label for="checkbox<?php echo $item['id']?>"></label>
                        </div>
                        <?php endif;?>
                    </td>
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
                        ?>
                    </td>
                    <td><?php echo $item['mobile']; ?></td>
                    <td><font color="<?php echo $item['status'] == 1 ? 'green' : 'red' ?>"><?php echo $item['status'] == 1 ? '启用' : '禁用' ?></font></td>
                    <td>
                        <a title="修改" href="/system/staff/edit/id/<?php echo $item['id']; ?>">
                            修改
                        </a>
                        <?php if (!$item['is_super']): ?>
                        <a title="删除" style="color:#FF0000" data-name="<?php echo $item['name']?>" data-id="<?php echo $item['id'] ?>" href="javascript:;" class="del">
                            删除
                        </a>
                        <?php endif;?>
                    </td>
                </tr>
                    <?php endforeach;?>
                    <?php else:?>
                    <tr><td colspan="7">暂无数据</td></tr>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="height: 60px; margin-top: 10px;">
        <a class="btn btn-sm btn-default btn-bordered" id="open-status"><span class="glyphicon glyphicon"></span>启用</a>
        <a class="btn btn-sm btn-default btn-bordered" id="close-status"><span class="glyphicon glyphicon"></span>禁用</a>
        <a class="btn btn-sm btn-default btn-bordered" href="/system/staff/add/"><span class="glyphicon glyphicon-plus"></span> 新增</a>
    </div>
</div><!-- contentpanel -->
<script type="text/javascript">
    
    jQuery(document).ready(
        function() {
            //选择
            $('.del').click(function() {
                var id = $(this).attr('data-id');
                var name = '确定删除姓名为' + $(this).attr('data-name') + '的用户吗？';
				PWConfirm(name,function(){
                    $.post('/system/staff/del/id/',{id : id}, function (data) {
                        if (data.error) {
                            var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+data.msg+'</div>';
                            $('#show_msg').html(warn_msg);
                            location.href='#show_msg';
                        } else {
                            var succss_msg = '<div class="alert alert-success"><strong>删除成功</strong></div>';
                            $('#show_msg').html(succss_msg);
                            window.location.reload();
                        }
                    },'json');
            });
                return false;
            });
            
            $('#checkbox-allcheck').click(function(){
                var obj = $(this).parents('table');
                if($(this).attr('checked')){
                    obj.find('input').prop('checked', true)
                    obj.find('tbody tr[class!="empty"]').addClass('selected')
                    $('.allcheck').text('全不选')
                }else{
                    obj.find('input').prop('checked', false)
                    obj.find('tbody tr').removeClass('selected')
                    $('.allcheck').text('全选')
                }
            })

            
            $('.ids').click(function(){
                $(this).parents('tr').toggleClass('selected');
            });
            
            $('#close-status, #open-status').click(function() {
                
                //获取ids字符串
                var ids = "";
                $('tr[class="selected"]').each(function(){
                    if(ids != "") {
                        ids += ',';
                    }
                    ids += $(this).find('input[type="checkbox"]').val();
                });
                var status = 0;
                if($(this).attr('id') == 'open-status') {
                    status = 1;
                } else if($(this).attr('id') == 'close-status') {
                    status = 0;
                }
                //window.location.href = "/system/staff/status/ids/"+ids+"/status/"+status;
                $.post('/system/staff/status/',{ids : ids, status: status},function(data){
                    if (data.error) {
                        var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+data.msg+'</div>';
                        $('#show_msg').html(warn_msg);
                        location.href='#show_msg';
                    } else {
                        var succss_msg = '<div class="alert alert-success"><strong>更新成功！</strong></div>';
                        $('#show_msg').html(succss_msg);
                        window.location.reload();
                    }
                },'json')
            });
            
        }
    );

</script>