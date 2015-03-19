<?php echo "<?php\n";?>
$this->menu=array(
	array('label'=>'管理', 'url'=>array('index'),'active'=>true),
	array('label'=>'创建', 'url'=>array('create')),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="main_tab">
  <tbody><tr>
	<td class="search" colspan="19">
	<div class="search-form">
	<?php echo "<?php \$this->renderPartial('_search',array(
	'model'=>\$model,\n
)); ?>\n";?>
	</div><!-- search-form -->
	</td>
  </tr>
</tbody></table>
<div class="clear"></div>

<?php echo "<?php"; ?> $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
	'dataProvider'=>$model->search(),
	'template'=>'{items}{pager}{summary}',
	'columns'=>array(
<?php
$count=0;
foreach($this->tableSchema->columns as $column)
{
	if(++$count==7)
		echo "\t\t/*\n";
	echo "\t\t'".$column->name."',\n";
}
if($count>=7)
	echo "\t\t*/\n";
?>
		array(
			'class'=>'CButtonColumn',
		),
	),
	'cssFile'=>Yii::app()->request->baseUrl."/css/gridview/styles.css",
)); ?>
<div class="main_tab_footer">&nbsp;</div>