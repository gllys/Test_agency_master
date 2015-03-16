<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n";?>
$this->menu=array(
	array('label'=>'管理', 'url'=>array('index')),
	array('label'=>'创建', 'url'=>array('create')),
	array('label'=>'编辑', 'url'=>array('update','id'=>$model->id)),
	array('label'=>'查看', 'url'=>array('view','id'=>$model->id),'active'=>true),
);
<?php echo "?>";?>
<?php echo "<?php"; ?> $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
<?php
foreach($this->tableSchema->columns as $column)
	echo "\t\t'".$column->name."',\n";
?>
	),
)); ?>
