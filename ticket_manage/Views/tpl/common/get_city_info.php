<?php if($cityInfo):?>
	<option value="__NULL__"><?php if($type == 'city'):?>市<?php else: ?>县<?php endif;?></option>
	<?php foreach($cityInfo as $value): ?>
	<option value="<?php echo $value['id'];?>" <?php if($current==$value['id']):?>selected<?php endif;?>><?php echo $value['name'];?></option>
	<?php endforeach;?>
<?php endif;?>