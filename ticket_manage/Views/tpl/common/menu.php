<div class="primary-sidebar">
	<!-- Main nav -->
	<ul class="nav nav-collapse collapse nav-collapse-primary">
		<?php if($menu):?>
		<?php foreach($menu as $key => $value):?>
			<?php if(!$value['toggle']):?>
			<li class="dark-nav<?php if($key == $c):?> active<?php endif;?>">
				<span class="glow"></span>
				<a href="<?php echo $value['url'];?>">
					<i class="<?php echo $value['class'];?> icon-2x"></i>
					<span style="font-size:14px"><?php echo $value['title'];?></span>
				</a>
			</li>
			<?php else:?>
			<li class="dark-nav<?php if($key == $c):?> active<?php endif;?>">
				<span class="glow"></span>
				<a class="accordion-toggle collapsed " data-toggle="collapse" href="#<?php echo $value['toggle'];?>">
				<i class="<?php echo $value['class'];?> icon-2x"></i>
					<span style="font-size:14px">
						<?php echo $value['title'];?> <i class="icon-caret-down"></i>
					</span>
				</a>

				<ul id="<?php echo $value['toggle'];?>" class="collapse <?php if($key == $c):?>in<?php endif;?>">
					<?php foreach($value['menu'] as $second => $secondValue):?>
						<?php if(!$secondValue['hidden']):?>
							<li class="<?php if($key == $c && $second == $a):?>active<?php endif;?>"><a href="<?php echo $secondValue['url'];?>"><i class="<?php echo $secondValue['class'];?>"></i> <?php echo $secondValue['title'];?></a></li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			</li>
			<?php endif;?>
		<?php endforeach;?>
		<?php endif;?>
	</ul>
</div>
<!--sidebar end-->