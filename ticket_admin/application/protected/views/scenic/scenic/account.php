<div class="contentpanel">
    <!--景区供应商关系列表-->
    <!--start-->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                景区列表
            </h4>
        </div>
        <div id="show_msg"></div>
        <div class="panel-body">
            <?php echo $scenicInfo['name'] . '--电子票务账号列表';?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb30">
            <thead>
            <tr>
                <th>账号</th>
                <th>密码</th>
                <th>去登录</th>
                <th>启用/禁用</th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($accountLists) && !empty($accountLists)):
                $params = unserialize(PARAMS);
                $params = $params['params'];
                $url = $params['ticket-url']['url'];
                $status_class = array('text-danger','text-success');
                $status_label = array('（已禁用）','（已启用）');
                    foreach($accountLists as $value):
            ?>
                <tr>
                    <td><?php echo $value['account']?>
                        <span class="<?php echo $status_class[$value['status']]?>"><?php echo $status_label[$value['status']]?></span>
                    </td>
                    <td><?php echo $value['password_str']?></td>
                    <td>
                        <a target="new" class="clearPart" href="<?php echo $params['ticket-url']['url']?>/site/login/?u=<?php echo $value['account'];?>">电子票务系统</a>
                        <a target="new"  class="clearPart" href="<?php echo $params['supply-url']['url']?>/site/login/?u=<?php echo $value['account'];?>">供应商系统</a>
                    </td>
                    <td>
                        <img src="/img/select2-spinner.gif" class="load" style="display: none" >
                        <?php if($value['status']):?>
                            <a href="javascript:;" class="text-danger update_status clearPart"
                               data-id="<?php echo $value['id']?>" data-status="0">禁用</a>
                        <?php else:?>
                            <a href="javascript:;" class="text-success update_status clearPart" data-id="<?php echo
                            $value['id']?>" data-status="1">启用</a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; else:?>
            <tr id="empty"><td colspan="4">暂无电子票务账号，请点击生成电子账号进行创建</td></tr>
            <?php endif;?>
            </tbody>
        </table>
        <div class="table-footer">
            <img src="/img/select2-spinner.gif" class="load" style="display: none" >
            <a class="btn btn-primary btn-sm clearPart" href="javascript:;" id="g_btn">生成新账号</a>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        var lan_id = <?php echo $scenicInfo['id']?>;
        var org_id = <?php echo $scenicInfo['organization_id']?>;

        $('#g_btn').click(function(){
            $('#g_btn').hide();
            $('.update_status').hide();
            $('.load').show();
            $.post('/scenic/scenic/saveaccount/',{ landscape_id : lan_id, organization_id : org_id },function(data){
                if(data.error){
                    alert(data.msg, function() {
                        $('#g_btn').show();
                        $('.update_status').show();
                        $('.load').hide();
                    });
                }else{
                    alert('电子票务账号新增成功！', function() {
                        window.location.partReload();
                    });
                }
            },'json')
        })

        //启用与禁用
        $('.update_status').click(function(){
            $('#g_btn').hide();
            $('.update_status').hide();
            $('.load').show();
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            $.post('/scenic/scenic/updatestatus/',{id : id, status : status, landscape_id : lan_id, organization_id : org_id}, function(data){
                if (data.error) {
                    alert(data.msg, function() {
                        $('#g_btn').show();
                        $('.update_status').show();
                        $('.load').hide();
                    });
                } else {
                    alert('更新成功！', function() {
                        window.location.partReload();
                    });
                }
            },'json');
        })

    })
</script>