<!DOCTYPE html>
<html>
	<?php get_header();?>
	<body>
		<?php get_top_nav();?>
		<div class="sidebar-background">
			<div class="primary-sidebar-background"></div>
		</div>
		<?php get_menu();?>
		<div class="main-content">
			<?php get_crumbs();?>
			<div id="show_msg">

			</div>
			<style>
				.msg-nav{
					float:left;
					width:10%;
					}
				.msg-nav .nav-list li.active > a:after,.msg-nav .nav-list > li.active:after{
					display:none
					}
				.msg-nav .nav-list > li > a{
					padding:0 5px 0 15px;
					}
				.msg-nav .nav-list > li.active > a i{
					vertical-align:inherit;
					}
				.msg-box{
					float:left;
					width:90%;
					min-height:300px;
					background-color:#fff;
					}
				.msg-box .icon-trash{
					float:right;
					}
				.msg-box .chat-box{
					font-size:12px;
					}
				.box-header{
					overflow:hidden
					}
				.box-header .box-toolbar{
					float:right;
					list-style:none;
					margin:0;
					padding:0;
					}
				.box-header .box-toolbar > li{
					color: #636363;
					float: left;
					font-size: 12px;
					line-height: 37px;
					margin-left: 15px;
					padding-right: 10px;
					position: relative;
					}
				.time .icheckbox_flat-aero{
					margin-left:10px;
					vertical-align:middle;
					}
				#labels2 .arrow-box-left:after,#labels2 .arrow-box-left:before{
					display:none
					}
				#labels2 .chat-box .arrow-box-left{
					margin-left:0;
					}
				.chat-box a{
					border-bottom:1px dotted;
					font-weight:700;
					}
				.chat-box .indent{
					margin:0
					}
				.chat-box dd{
					padding:5px
					}
				#labels2 .chat-box.timeline .content{
					border-top:0
					}
				#sms-template-table tr:nth-child(2n-1) td{
					cursor:pointer;
				}
				#sms-template-table tr:nth-child(2n){
					display:none;
					color:#000
				}
				.unread{
					background-color:#ccc;
				}
			</style>

			<div class="container-fluid padded">
				<div class="row-fluid">
					<div class="msg-nav">
						<div class="wizard-nav-container" style="height: 360px;">
							<ul style="padding-bottom:30px;" class="nav nav-list">
								<li class="wizard-nav-item <?php if($viewType == 2):?>active<?php endif;?>">
									<a class="wizard-nav-link"  href="message_publish.html?viewType=2"><i class="icon-chevron-right"></i>系统提醒</a>
								</li>

								<li class="wizard-nav-item <?php if($viewType == 3):?>active<?php endif;?>">
									<a class="wizard-nav-link"  href="message_publish.html?viewType=3"><i class="icon-chevron-right"></i>已发送</a>
								</li>

								<li class="wizard-nav-item <?php if($viewType == 4):?>active<?php endif;?>">
									<a class="wizard-nav-link" href="message_publish.html?viewType=4"><i class="icon-chevron-right"></i>发送消息</a>
								</li>
							</ul>
						</div>
					</div>

					<div class="msg-box tab-content">
						<?php if($viewType == 1 || $viewType == 2) :?>
						<div class="container-fluid padded tab-pane <?php if($viewType == 1 || $viewType == 2 ):?>active<?php endif;?>" >
							<div class="box-header">
								<ul class="box-toolbar">
									<li class="toolbar-link">
									<a href="javascript:;" class="allcheck">
										<span class="label label-green"><i class="icon-plus"></i> <ins>全选</ins></span>
									</a>

									<a href="javascript:;" class="alldel">
										<span class="label label-red"><i class="icon-remove"></i> 删除选中</span>
									</a>
									</li>
								</ul>
							</div>
							
							<ul class="chat-box timeline">
								<?php if($messageList):?>
									<?php foreach($messageList as $message):?>
										<li class="arrow-box-left gray" mid="<?php echo $message['id'];?>">
											<div class="info">
												<span class="name">
												<strong class="indent"><?php echo $message['organization_name'];?></strong>
												</span>
												<span class="time"><i class="icon-time"></i> <?php echo $message['created_at'];?> <input type="checkbox" class="icheck" name="" value="<?php echo $message['id'];?>"></span>
											</div>
											<div class="content">
												<blockquote style="cursor:pointer;" class="sms-content" data-id="<?php echo $message['id'];?>" data-read="<?php echo $message['ums_status'];?>">
													<?php echo msubstr($message['content'], 18);?>
												</blockquote>
												<div style="display:none;">
													<?php echo $message['content'];?>
												</div>
												<div>
													<a class="del-msg" href="" title="删除" data-id="<?php echo $message['id'];?>"><i class="icon-trash"></i></a>
												</div>
											</div>
										</li>
									<?php endforeach;?>
								<?php else:?>
									暂无消息
								<?php endif;?>
							</ul>
						</div>
						<?php endif;?>
						<?php if($viewType == 3) :?>
						<div class="container-fluid padded tab-pane <?php if($viewType == 3):?>active<?php endif;?>" >
							<div class="box-header">
								<ul class="box-toolbar">
									<li class="toolbar-link">
									<a href="javascript:;" class="allcheck">
										<span class="label label-green"><i class="icon-plus"></i> <ins>全选</ins></span>
									</a>

									<a href="javascript:;" class="alldel">
										<span class="label label-red"><i class="icon-remove"></i> 删除选中</span>
									</a>
									</li>
								</ul>
							</div>
							
							<ul class="chat-box timeline">
								<?php if($messageList):?>
									<?php foreach($messageList as $message):?>
										<li class="arrow-box-left gray" mid="<?php echo $message['id'];?>">
											<div class="info">
												<span class="name">
												<strong class="indent"><?php if($message['msg_type'] == 'system_all'):?>您对所有人说：<?php else:?>您对 [<?php echo $message['organization_names'];?>] 说：<?php endif;?></strong>
												</span>
												<span class="time"><i class="icon-time"></i> <?php echo $message['created_at'];?> <input type="checkbox" class="icheck" name="" value="<?php echo $message['id'];?>"></span>
											</div>
											<div class="content">
												<blockquote style="cursor:pointer;" class="sms-content" data-id="<?php echo $message['id'];?>" data-read="<?php echo $message['ums_status'];?>">
													<?php echo msubstr($message['content'], 18);?>
												</blockquote>
												<div style="display:none;">
													<?php echo $message['content'];?>
												</div>
												<div>
													<a class="del-msg" href="" title="删除" data-id="<?php echo $message['id'];?>"><i class="icon-trash"></i></a>
												</div>
											</div>
										</li>
									<?php endforeach;?>
								<?php else:?>
									暂无消息
								<?php endif;?>
							</ul>
						</div>
						<?php endif;?>
						<?php if($viewType == 4) :?>
							<form action="message_addmsg.html" method="post" id="msg-form">
								<div class="container-fluid padded tab-pane <?php if($viewType == 4):?>active<?php endif;?>" >
									<ul class="landscape-add">
									<li id="noticebox">
										<label>发送给:</label><?php if(!$partnerList):?>暂无合作机构<?php endif;?>
										<div class="row-fluid">
											<div class="span8">
												<select multiple="multiple" name="receive[]" class="chzn-select">
													<?php if($partnerList):?>
														<?php foreach($partnerList as $partner):?>
															<option value="<?php echo $partner['id'];?>">
																<?php echo $partner['name'];?>
															</option>
														<?php endforeach;?>
													<?php endif;?>
												</select>
											</div>
										</div>
									</li>
									<li>
										<input type="checkbox" class="icheck" name="user_type" value="system" id="notice"> <label>发公告</label>
									</li>
									<li>
										<label>消息内容：<strong class="status-error"></strong> <span class="note"></span>
											<textarea name="content" class="summary validate[minSize[1],maxSize[500]]" placeholder=""></textarea>
										</label>
									</li>
									</ul>
									<div align="right">
										<button id="msg-form-button" type="button" class="btn btn-green">发送</button>
									</div>
								</div>
							</form>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>

		<script src="Views/js/jquery.validationEngine-zh-CN.js" type="text/javascript" charset="utf-8"></script>
		<script src="Views/js/message/message.js" type="text/javascript" charset="utf-8"></script>
	</body>
</html>