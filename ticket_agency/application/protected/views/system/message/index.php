<?php
$this->breadcrumbs = array('系统管理','消息');
?>
<div class="contentpanel">
	<div class="row">
		<div class="col-sm-3 col-md-3 col-lg-2">
			<!--a href="/system/message/write" class="btn btn-success btn-block btn-create-msg">新消息</a-->
			<br />
			<ul class="nav nav-pills nav-stacked nav-msg">
				<li class="<?php echo $type == null ? 'active' : '' ?>">
					<a href="/system/message/all">
						<!--span class="badge pull-right"></span-->
						<i class="glyphicon glyphicon-inbox"></i> 全部
					</a>
				</li>
				<li class="<?php echo $type == 1 ? 'active' : '' ?>"><a href="/system/message/sub/"><i
							class="glyphicon glyphicon-star"></i> 订阅</a></li>
				<li class="<?php echo $type == 2 ? 'active' : '' ?>"><a href="/system/message/org/"><i
							class="glyphicon glyphicon-tower"></i> 机构</a></li>
				<li class="<?php echo $type == 3 ? 'active' : '' ?>"><a href="/system/message/rem/"><i
							class="glyphicon glyphicon-bullhorn"></i> 提醒</a></li>
				<li class="<?php echo $type == 4 ? 'active' : '' ?>"><a href="/system/message/col/"><i
							class="glyphicon glyphicon-heart"></i> 收藏</a></li>
				<!--li class="<?php //echo $type == 'sent' ? 'active' : '' ?>"><a href="/system/message/sent/"><i
							class="glyphicon glyphicon-send"></i> 已发送</a></li-->
			</ul>
		</div>
		<style>
			#title{
				overflow: hidden; /*自动隐藏文字*/
            	text-overflow: ellipsis;/*文字隐藏后添加省略号*/
            	white-space: nowrap;/*强制不换行*/
            	width: 10em;/*不允许出现半汉字截断*/
			}
		</style>

		<div class="col-sm-9 col-md-9 col-lg-10">

			<ul class="media-list msg-list">
				<?php if ($messages) : foreach ($messages as $message) : ?>
					<li id="message<?php echo $message{'id'} ?>" class="media <?php echo $message['read_time'] == 0 ? 'unread' : '' ?>">
						<!--div class="ckbox ckbox-primary pull-left">
							<input type="checkbox" id="checkbox<?php //echo $message{'id'} ?>">
							<label for="checkbox<?php //echo $message{'id'} ?>"></label>
						</div-->
						<!--a class="pull-left" href="#">
							<img class="media-object img-circle img-online" src="/img/user1.png" alt="...">
						</a-->

						<div class="media-body">
							<div class="pull-right media-option">
								<i class="fa fa-paperclip mr5" style="display: none"></i>
								<small><?php echo date('Y年m月d日',$message['created_at']) ?></small>
								<!--i class="fa fa-star"></i-->

								<div class="btn-group">
									<a class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-cog"></i>
									</a>
									<ul class="dropdown-menu pull-right" role="menu">
										<li><a href="javascript:;" class="setDeleted" data-id="<?php echo $message{'id'} ?>">删除</a></li>
										<li style="display:<?php echo $message['read_time'] == 0 ? '' : 'none'?>"><a href="javascript:;" class="setRead" data-id="<?php echo $message{'id'} ?>">标记为已读</a></li>
									</ul>
								</div>
							</div>
							<h4 class="sender"><div style="float:left" <?php echo $message['read_time'] == 0 ? '' : 'hidden'?> ><i style="padding-right:5px" id="read<?php echo $message['id']?>" class="glyphicon glyphicon-envelope" ></i></div>
							<?php $result = Organizations::api()->show(array('id' => $message['send_organization']));
											if(!empty($result['body']['name'])){
												echo $result['body']['name'];
											}else{
												echo '系统公告';
											}
											?><span class="text-<?php echo $sms_class[$message['sms_type']]?>">（<?php echo $sms_label[$message['sms_type']]?>）</span></h4>

							<p id="title"><a href="javascript:;"  class="setRead" data-id="<?php echo $message{'id'} ?>" data-food="<?php echo $message['read_time']?>"><strong
										class="subject"></strong> <?php echo htmlspecialchars($message['content']) ?>
								</a></p>
							<p style="display:none" id="content<?php echo $message['id']?>"><?php echo htmlspecialchars($message['content'])?></p>	
						</div>
					</li>
				<?php endforeach; ?>
			<?php else:?>
				<li> 暂无消息</li>
			<?php endif;?>
			</ul>
			<div class="msg-footer">
				<div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0" <?php if(empty($messages)){ echo 'hidden';}?>>
					<?php
					if (isset($messages)) {
						$this->widget('CLinkPager', array(
						'cssFile' => '',
						'header' => '',
						'prevPageLabel' => '上一页',
						'nextPageLabel' => '下一页',
						'firstPageLabel' => '',
						'lastPageLabel' => '',
						'pages' => $pages,
						'maxButtonCount' => 5, //分页数量
					));
				}
				?>
				</div>
			</div>
			<!-- msg-footer -->
		</div>
	</div>
</div><!-- contentpanel -->
<script>
	jQuery(document).ready(function (){
		$('.setRead').click(function() {
			var sels = $(this);
			var id = Math.floor($(this).attr('data-id'));
			var rt = $(this).attr('data-food');
			$('#content' + id).toggle('normal');
			if(rt == 0){
				$.post('/system/message/read',{'id' : id},function(data) {
				if (data.error == 0) {
					$('#read' + id).remove();
					var num = $('.badge').text();
					if(Number(num) > 0){
						num = num - 1;
					}
					if(Number(num) == 0){
						$('.badge').remove();
					}else{
						$('.badge').text(num);		
					}
					sels.attr('data-food','1');
				}else{
					alert(data.msg);
				}
			},'json');
		}else{
				return false;
	}
			
		});
		$('.setDeleted').click(function() {
			if (!window.confirm("确定要删除消息?")) {
                return false;
            }
			var id = Math.floor($(this).attr('data-id'));
			$.post('/system/message/delete', {'id' : id},function(data) {
				if (data.error == 0) {
					$('#message' + id).remove();
					top.location.reload();
				}else{
					alert(data.msg);
				}
			},'json');
		});
	});
</script>
