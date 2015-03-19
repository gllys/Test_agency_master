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
                    <h4 class="panel-title"><a class="btn btn-success btn-sm pull-right" href="/system/role/add/" style="color: #ffffff">新增</a>角色权限</h4>
                </div>
                <!-- panel-heading -->




            </div>
            <!-- panel -->
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                    <tr>
                        <th width="20%">角色名称</th>
                        <th width="50%">角色说明</th>
                        <th width="15%">操作</th>
                    </tr>
                    </thead>
                    <tbody id="staff-body">
                    <?php if(isset($list) && !empty($list)):?>
                    <?php
                    foreach ($list as $item):
                    ?>
                    <tr>
                        <td style="text-align: left"><?php echo $item['name'] ?></td>
                        <td style="text-align: left"><?php echo $item['description'] ?></td>
                        <td>
                            <a href="/system/role/edit/?id=<?php echo $item['id'] ?>" class="btn btn-bordered btn-xs btn-success" style="border-width: 1px">
                                修改
                            </a>
                            <a href="/system/role/del/?id=<?php echo $item['id'] ?>" class="btn btn-bordered btn-xs btn-danger del" style="border-width: 1px">删除</a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                        <?php else:?>
                        <tr><td colspan="3">暂无角色权限，请点击新增进行添加</td></tr>
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
    $(function() {
        //选择
        $('a.del').click(function() {
			PWConfirm('确定要删除?',function(){
			    $.post($(this).attr('href'), function() {
                window.location.reload();
            });
        });
           
            return false;
        });
    });

</script>
