<div class="contentpanel" id="maincontent">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title"><a class="btn btn-success btn-sm pull-right" href="/system/homerec/add/" style="color: #ffffff">新增</a>首页推荐</h4>
                </div>
                <!-- panel-heading -->
            </div>
            <div id="show_msg"></div>
            <!-- panel -->
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
						<tr>
							<th>推荐编号</th>
							<th>发布时间</th>
							<th>活动时间</th>
							<th>主题</th>
							<th>展示区域</th>
							<th>状态</th>
							<th>操作人</th>
							<th width="200px">操作</th>
						</tr>
                    </thead>
                    <tbody id="rec-body">
						<?php 
							if($datas) {
								foreach ($datas as $v): ?>
									<tr>
										<td><?php echo $v['id'];?></td>
										<td><?php echo date('Y-m-d',$v['created_at']);?></td>
										<td><?php echo date('Y-m-d',$v['start_time']);?>至<?php echo date('Y-m-d',$v['end_time']);?></td>
										<td><a href="/system/homerec/edit/id/<?php echo $v['id'];?>"><?php echo mb_substr($v['title'], 0,20,'UTF-8');?></a></td>
										<td>
											<?php 
											$posids = explode(',', $v['pos_id']);
											foreach ($posids as $posid) {
												echo $posinfo[$posid].' ';
											}
											?>
										</td>
										<td>
											<?php echo $v['status'] == 0 ? "未发布" : "已发布";?>
										</td>
										<td>
											<?php
												$user = Users::model()->findByPk($v['created_by']);
												echo $user['name'];
											?>
										</td>
										<td>
											<button onclick="pubRec(<?php echo $v['id'];?>, 1)" class="btn btn-success btn-xs" <?php echo $v['status']?'disabled="disabled"':''?>>发布</button>
											<button onclick="pubRec(<?php echo $v['id'];?>, 0)" class="btn btn-warning btn-xs" <?php echo !$v['status']?'disabled="disabled"':''?>>撤销</button>
											<a href='/system/homerec/edit/id/<?php echo $v['id'];?>' class="btn btn-default btn-xs" <?php echo $v['status']?'disabled="disabled"':''?>>编辑</a>
											<button onclick="delRec(<?php echo $v['id'];?>)" class="btn btn-danger btn-xs">删除</button>
										</td>
									</tr>
						<?php 
								endforeach;
							} else {
						?>
	                        <tr><td style="text-align: center" colspan="6">暂无首页推荐，请点击新增增加</td></tr>
						<?php 
							};
						?>
                    </tbody>
                </table>
                <div class="panel-footer" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
				<?php 		
					if($datas) {
						$this->widget('common.widgets.pagers.ULinkPager', array(
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
        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->

</div>
<script type="text/javascript">

	
	$(function() {
		
		/**
		 * 发布或撤销首页推荐 
		 */
		window.pubRec = function(id, status) {
			var opera = status ? "发布" : "撤销";
            var name = '确定'+opera+'这条推荐吗？';
			PWConfirm(name,function(){
				$.post('/system/homerec/pub/id/',{id : id, status: status}, function (data) {
					if (data.error) {
                        setTimeout(function() {
                            alert(data.msg);
                        }, 500);
					} else {
                        setTimeout(function() {
                            alert(opera+'成功', function() {
                                window.location.partReload();
                            });
                        }, 500);
					}
				},'json');
			});
		}
		
		/* 删除推荐 */
        window.delRec = function(id) {
            var name = '确定删除这条推荐吗？';
			PWConfirm(name,function(){
				$.post('/system/homerec/del/id/',{id : id}, function (data) {
					if (data.error) {
                        alert(data.msg);
					} else {
                        setTimeout(function() {
                            alert('删除成功', function() {
                                window.location.partReload();
                            });
                        }, 500);
					}
				},'json');
			});
		}
	});

</script>
