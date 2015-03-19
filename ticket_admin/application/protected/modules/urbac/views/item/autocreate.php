<?php $this->renderPartial('../_nav'); ?>
<form method="post" action="/urbac/item/createOperation">
	标识:<input type="text" class="form_text" name="operation_name" style="margin-top:0px;">
	描述:<input type="text" class="form_text" name="operation_desc" style="margin-top:0px;">
	<input type="submit" class="xz_but" value="添加">
</form>
<br>
<div class="form">
	<form method="post">
		<?php $this->renderPartial('_operationTable', array('list'=>$list)); ?>
		<div class="row buttons">
			<?php echo CHtml::submitButton('添加', array('class'=>'xz_but')); ?>
		</div>
	</form>
</div>