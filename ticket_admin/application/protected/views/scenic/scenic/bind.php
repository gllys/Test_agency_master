<div class="modal-dialog" style="width: 1200px">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">景区管理</h4>
            <div id="return_msg"></div>
        </div>
        <div class="modal-body">
            <form id="check_form" class="form-inline">
                <div class="form-group">供应商：
                    <input type="hidden" name="id" value="<?php echo $scenicInfo['id']?>">
                    <input class="form-control" placeholder="请输入供应商名称" type="text" name="organization_name" style="width:318px;">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-sm" type="button" id="check_button">查询</button>
                </div>
            </form>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered mb30" id="supply_lists">
                    <thead>
                    <tr><?php echo $scenicInfo['name'] . '--已绑定供应商列表';?></tr>
                    <tr>
                        <th>编号</th>
                        <th>供应商名称</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($lists) && !empty($lists)): foreach($lists as $value): ?>
                        <tr>
                            <td><?php echo $value['organization_id']?></td>
                            <td>
                                <?php echo $value['organization_name']?>
                            </td>
                            <td>
                                <a>解除绑定</a>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr>
                            <td colspan="3">暂无绑定供应商，请前往<a href="/scenic/scenic/supply/id/<?php echo $scenicInfo['id']?>">绑定供应商页面</a>进行绑定</td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        $('#check_button').click(function(){
            var landscape_id = $('input[name=id]').val();
            var organization_name = $('input[name=organization_name]').val();
            if(organization_name == ''){
                alert('请输入供应商名称');
                return false;
            }
            $.post('/scenic/scenic/supplylists/',{id : landscape_id,organization_name : organization_name},function(data){
                if(data.error){
                    $('#supply_lists tbody').html('<tr><td colspan="3">查询不到该供应商，请查证后再进行查询</td></tr>')
                }else{
                    var lists = data.msg;
                    $.each(lists,function(i,val){
                        var html = '<tr><td>' + val.id + '</td><td>' + val.name + '</td><td>';
                        if(val.bind == 1){
                            html += '<a>解除绑定</a></td></tr>';
                        }else{
                            html += '<a>绑定</a></td></tr>';
                        }
                        $('#supply_lists tbody').html(html);
                    })

                }
            },'json')
        })
    })
</script>
