<?php echo "<?php\n";?>
$this->menu=array(
	array('label'=>'管理', 'url'=>array('index')),
	array('label'=>'创建', 'url'=>array('create'),'active'=>true),
);
<?php echo "?>\n";?>
<?php echo "<?php echo \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>
