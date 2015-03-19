<div class="navbar navbar-top navbar-inverse">
	<div class="navbar-inner">
		<div class="container-fluid"><a class="brand" href="#"><!--<i class="icon-leaf" style="font-size:18px;"></i>--> 智慧旅游分销-供应管理平台</a>
			<ul class="nav pull-right">
				<li class="toggle-primary-sidebar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-primary"><a><i class="icon-th-list"></i></a></li>
				<li class="collapsed hidden-desktop" data-toggle="collapse" data-target=".nav-collapse-top"><a><i class="icon-align-justify"></i></a></li>
			</ul>
			<div class="nav-collapse nav-collapse-top">
				<ul class="nav full pull-right">
					<li class="dropdown user-avatar"> 
					<!-- the dropdown has a custom user-avatar class, this is the small avatar with the badge --> 
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span> <img class="menu-avatar" src="Views/images/avatars/avatar2.jpg" /> <span><?php echo $_SESSION['backend_userinfo']['name'] ? $_SESSION['backend_userinfo']['name']: $_SESSION['backend_userinfo']['account'];?> <i class="icon-caret-down"></i></span> <span class="badge badge-dark-red">2</span> </span> </a>
					<ul class="dropdown-menu">
						<!-- the first element is the one with the big avatar, add a with-image class to it -->
						<li class="with-image">
						<div class="avatar"> <img src="Views/images/avatars/avatar2.jpg" /> </div>
						<span><?php echo $_SESSION['backend_userinfo']['name'] ? $_SESSION['backend_userinfo']['name']: $_SESSION['backend_userinfo']['account'];?></span> <span style="font-size:12px;"></span> </li>
						<li class="divider"></li>
						<!--<li><a href="index.php?c=system&a=editUser&id=<?php echo $_SESSION['backend_userinfo']['id'];?>"><i class="icon-cog"></i> <span>设置</span></a></li>-->
						<li><a href="index.php?c=login&a=logout"><i class="icon-off"></i> <span>登出</span></a></li>
					</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>