<?php
$this->breadcrumbs = array('产品', '优惠规则');
?>
<div class="contentpanel">
    <style>
        .table tr > * {
            text-align: center
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="/ticket/discount/add" class="btn btn-success btn-sm pull-right">增加新规则</a>
            <h4 class="panel-title">优惠规则列表</h4>
        </div>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>名称</th>
                <th>说明</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($lists)): ?>
                <?php foreach($lists as $discount): ?>
                    <tr>
                        <td><?php echo $discount['name']; ?></td>
                        <td><?php echo $discount['note'] ?></td>
                        <td>
                            <a title="编辑" style="margin-left: 10px;" href="/ticket/discount/edit?id=<?php echo $discount['id']; ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <a title="删除" style="margin-left: 10px;" href="javascript:void(0);" onclick="delDiscount('<?php echo $discount['id']; ?>');" class="del">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                            <a href="/ticket/discount/copy?id=<?php echo $discount['id']; ?>" title="复制" style="margin-left: 10px;"><span class="fa fa-copy"
                                                                                                                                                          style="cursor:pointer"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">暂无优惠规则,点<a href="/ticket/discount/add">这里</a>去创建.</td></tr>
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
    function delDiscount(id){
		 PWConfirm('确定要删除该优惠规则?',function(){
			      $.post('/ticket/discount/del',{id:id},function(data){
                if(data.error==0){
                    alert("删除成功",function(){ location.reload();});
                }else{
                    alert("删除失败,"+data.msg);
                }
            },'json');
            });
    }
</script>