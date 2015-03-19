<div class="span2">
	<select class="uniform" name="province" id="province">
		<option value="__NULL__">省</option>
		<?php if($cityInfo):?>
			<?php foreach($cityInfo as $city):?>
				<option value="<?php echo $city['id'];?>" <?php if($area['province'] == $city['id']):?>selected="selected"<?php endif;?>><?php echo $city['name'];?></option>
			<?php endforeach;?>
		<?php endif;?>
	</select>
</div>
<div class="span2">
	<select class="uniform" name="city" id="city">
		<option value="__NULL__">市</option>
		<?php if($secondArea):?>
			<?php foreach($secondArea as $second):?>
				<option value="<?php echo $second['id'];?>" <?php if($area['city'] == $second['id']):?>selected="selected"<?php endif;?>>
					<?php echo $second['name'];?>
				</option>
			<?php endforeach;?>
		<?php endif;?>
	</select>
</div>
<div class="span2">
	<select class="uniform" name="area" id="area">
		<option value="__NULL__">县</option>
		<?php if($thirdArea):?>
			<?php foreach($thirdArea as $third):?>
				<option value="<?php echo $third['id'];?>" <?php if($area['area'] == $third['id']):?>selected="selected"<?php endif;?>>
					<?php echo $third['name'];?>
				</option>
			<?php endforeach;?>
		<?php endif;?>
	</select>
</div>