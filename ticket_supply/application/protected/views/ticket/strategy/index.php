<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/3/14
 * Time: 2:45 PM
 */
$this->breadcrumbs = array('产品', '价格、库存规则库');
?>
<style>
	.table tr>*{
		text-align:center;
		padding: 8px;
		vertical-align: middle;
	}
</style>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<a href="/ticket/strategy/amend" class="btn btn-success btn-sm pull-right">增加新规则</a>
			<h4 class="panel-title">价格、库存规则</h4>
		</div>

		<table class="table table-bordered">
			<thead>
			<tr>
				<th style="text-align: left">名称</th>
				<th style="text-align: left">说明</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?php if (isset($lists['data'])) : foreach ($lists['data'] as $rule) :?>
			<tr>
				<td style="text-align: left"><?php echo $rule['name']?></td>
				<td style="text-align: left"><?php echo $rule['desc']?></td>
				<td>
					<a title="编辑" style="margin-left: 10px;" href="/ticket/strategy/amend/id/<?php echo $rule['id']?>">
						<span class="glyphicon glyphicon-edit"></span>
					</a>
					<a title="删除" style="margin-left: 10px;" href="javascript:void(0);" 
						onclick="del('<?php echo $rule['id']?>')" class="del-link del clearPart">
						<span class="glyphicon glyphicon-trash"></span>
					</a>
					<!--a onclick="" title="复制" style="margin-left: 10px;"><span class="fa fa-copy" style="cursor:pointer"></span></a-->
				</td>
			</tr>
			<?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
			<?php
			if (isset($lists['data'])) {
				$this->widget('common.widgets.pagers.ULinkPager', array(
					'cssFile' => '',
					'header' => '',
					'prevPageLabel' => '上一页',
					'nextPageLabel' => '下一页',
					'firstPageLabel' => '',
					'lastPageLabel' => '',
					'pages' => $pages,
					'maxButtonCount' => 5, //分页数量
				));
			}
			?>
		</div>
	</div>
</div>
<script>
	function del(id){
		if(id){
			 PWConfirm('确认删除此条规则？',function(){
			      $.post('/ticket/strategy/del',{id:id},function(data){
					if(data.error==0){
						location.href = '/#'+ "/ticket/strategy";
					}else{
						alert("删除失败,"+data.message);
					}
				},'json');
            });
			return false;
		}
	}
</script>
