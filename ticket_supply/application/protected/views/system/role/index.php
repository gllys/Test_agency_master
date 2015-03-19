<?php
$this->breadcrumbs = array('系统管理', '角色权限');
?>
<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th style="width: 20%">角色名称</th>
                            <th style="width: 50%">角色说明</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody id="staff-body">
                        <?php
                        foreach ($list as $item):
                            ?>
                            <tr>
                                <td style="text-align: left"><?php echo $item['name'] ?></td>
                                <td style="text-align: left"><?php echo $item['description'] ?></td>
                                <td>
                                    <a title="修改" href="/system/role/edit/?id=<?php echo $item['id'] ?>">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a title="删除" href="/system/role/del/?id=<?php echo $item['id'] ?>" class="del">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
	        <div class="table-responsive mt10">
		        <a href="/system/role/add/" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> 新增</a>
	        </div>
        </div><!-- col-md-6 -->
    </div><!-- row -->
</div>
<script type="text/javascript">
    $(function() {
        //选择
        $('a.del').click(function() {
            if (!window.confirm("确定要删除?")) {
                return false;
            }
            $.post($(this).attr('href'), function() {
                window.location.reload();
            });
            return false;
        });
    });

</script>
