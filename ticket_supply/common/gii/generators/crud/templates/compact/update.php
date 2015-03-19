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
	array('label'=>'编辑', 'url'=>array('update','id'=>$model->id),'active'=>true),
	array('label'=>'查看', 'url'=>array('view','id'=>$model->id)),
);
<?php echo "?>";?>
<?php echo "<?php echo \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>