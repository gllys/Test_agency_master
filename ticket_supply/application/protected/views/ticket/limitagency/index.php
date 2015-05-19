<?php
$this->breadcrumbs = array('产品', '限制分销商');
?>
<div class="contentpanel">
    <style>
        .table tr > * {
            text-align: center
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="/ticket/limitagency/add" class="btn btn-success btn-sm pull-right">增加</a>
            <h4 class="panel-title">限制清单列表</h4>
        </div>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>名称</th>
                <th>类型</th>
                <th>说明</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($lists)): ?>
                <?php foreach($lists as $limit): ?>
                    <tr>
                        <td><?php echo $limit['name']; ?></td>
                        <td><?php echo $limit['type']==1?"黑名单":"白名单"; ?></td>
                        <td><?php echo $limit['note'] ?></td>
                        <td>
                            <a title="编辑" style="margin-left: 10px;" href="/ticket/limitagency/edit?id=<?php echo $limit['id']; ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <a title="删除" style="margin-left: 10px;" href="javascript:void(0);" onclick="delLimit('<?php echo $limit['id']; ?>');" class="del">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                            <a href="/ticket/limitagency/copy?id=<?php echo $limit['id']; ?>" title="复制" style="margin-left: 10px;"><span class="fa fa-copy"
                                                                                      style="cursor:pointer"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">暂无限制清单,点<a href="/ticket/limitagency/add">这里</a>去创建.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div style="text-align:center" class="panel-footer">
            <div id="basicTable_paginate" class="pagenumQu">
                <?php
                $this->widget('common.widgets.pagers.ULinkPager', array(
                        'cssFile' => '',
                        'header' => '',
                        'prevPageLabel' => '上一页',
                        'nextPageLabel' => '下一页',
                        'firstPageLabel' => '',
                        'lastPageLabel' => '',
                        'pages' => $pages,
                        'maxButtonCount' => 5, //分页数量
                    )
                );
                ?>
            </div>
        </div>

    </div>
</div><!-- contentpanel -->
<script>
    function delLimit(id){
		PWConfirm('确定要删除清单?',function(){
			      $.post('/ticket/limitagency/del',{id:id},function(data){
                if(data.error==0){
                    alert("删除成功",function(){location.reload();});
                }else{
                    alert("删除失败,"+data.msg);
                }
            },'json');
            });
    }
</script>