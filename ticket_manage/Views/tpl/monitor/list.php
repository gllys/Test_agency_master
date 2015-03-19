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

    <div id="show_msg"></div>

	<style>

		svg {
			margin-left: 20px;
			border-bottom: 1px solid #ccc;
		}
		.node circle {
			fill: #fff;
			stroke: steelblue;
			stroke-width: 1.5px;
		}
		.selected text{
			fill: #f00;
		}
		.scenic text {
			fill: #07a;
		}
		.node, .scenic {
			font: 10px sans-serif;
		}

		.link {
			fill: none;
			stroke: #ccc;
			stroke-width: 1.5px;
		}

		.pop-content ul {
			margin: 0;
			list-style: none
		}

		.pop-content li {
			padding: 0;
			line-height: 2
		}

		.table-normal tbody td {
			text-align: center
		}

		.table-normal tbody strong {
			color: red;
		}

		.table-normal tbody td a {
			text-decoration: none
		}

		.table-normal tbody td i {
			margin-left: 10px
		}

		.popover-content button i {
			margin: 0 !important
		}

		.modal .table-normal tbody td {
			text-align: left
		}

		#sms textarea {
			width: 100%;
			height: 100px;
		}
		.switch-btn {
			width:64px;
			display:block;
			padding:1px;
			background:#33be40;
			overflow:hidden;
			margin-bottom:5px;
			border:1px solid #33be40;
			border-radius:18px;
			cursor: pointer;
		}
		.switch-btn span{
			width:32px;
			font-size:14px;
			height:18px;
			display:block;
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#f6f6f6,endColorstr=#eeeeee,grandientType=1);
			background:-webkit-gradient(linear, 0 0, 0 100%, from(#f6f6f6), to(#eeeeee));
			background:-moz-linear-gradient(top, #f6f6f6, #eeeeee);
			border-radius:18px;
			float:left;
			color:#33be40;
			text-align:center;
		}
		.switch-btn:hover span{
			background:#fff;
		}
		.switch-btn span.off{float:right;color:#DD1144}
		button.off{background: #DD1144}
	</style>

	<div class="container-fluid padded" style="padding-bottom: 0px;">
        <div class="box">
            <div class="box-header">
				<span class="title">功能设置</span>
            </div>
            <div class="box-content padded">
                <div class="row-fluid" style="height: 50px;">
                    <div class="span1">
	                    <label for="">手动上报</label>
                        <button class="switch-btn <?php $off = boolval($setting['gb_upward']) ? '' : 'off';echo $off?>">
	                        <span change="开" class="<?php echo $off?>">关</span>
	                        <input type="hidden" name="upward" value="0" />
                        </button>
                    </div>
                    <div class="span2" style="margin-left: 50px">
	                    <label for="">上报前修正</label>
	                    <button class="switch-btn <?php $off = boolval($setting['gb_editable']) ? '' : 'off';echo $off?>">
		                    <span change="开" class="<?php echo $off?>">关</span>
		                    <input type="hidden" name="editable" value="0" />
	                    </button>
                    </div>

                    <div class="span3">
                    </div>
                 </div>
            </div>
        </div>
    </div>

	<div class="container-fluid padded" style="padding-bottom: 0px;">
		<div class="box">
			<div class="box-header">
				<span class="title">搜索</span>
			</div>
			<div class="box-content padded">
				<form class="fill-up separate-sections" method="post" action="<?php echo $_SERVER['REQUEST_URL']?>">
					<div class="row-fluid" style="height: 30px;">
						<div class="span1">机构名称</div>
						<div class="span2">
							<input type="text" name="name" placeholder="请输入机构名称" value="<?php if($post['name']){ echo $post['name'];}?>">
						</div>

						<div class="span3">
							<button class="btn btn-default" id="searchBtn" type="submit">搜索</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title">监管机构</span>
            </div>
            <div class="box-content">
                <table class="table table-normal">
                    <thead>
                    <tr>
                        <td>编号</td>
                        <td>机构名称</td>
	                    <td>用户</td>
	                    <td>状态</td>
	                    <td style="width: 75px">上报前修正</td>
                        <td style="width: 30px">操作</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if($data):?>
                        <?php foreach($data as $value):?>
                            <tr class="status-pending" height="36px">
                                <td class="icon"><?php echo $value['id'];?></td>
                                <td style="text-align: left"><a class="text-left underline" href="monitor_lists_<?php echo $value['id']?>.html"><?php echo $value['name'];?></a></td>
	                            <td class="icon">
		                            <a href="/monitor_accountLists_<?php echo $value['id']?>.html?type=1" title="用户列表"><i class="icon-user" style="margin-right:10px;color:#666"></i></a>
	                            </td>
	                            <td><?php echo $value['status'] ? '启用' : '停用'?></td>
	                            <td>
		                            <button class="switch-btn <?php $off = boolval($value['editable']) ? '' : 'off';echo $off?>">
			                            <span change="开" class="<?php echo $off?>">关</span>
			                            <input type="hidden" name="edit[<?php echo $value['id']?>]" value="0" />
		                            </button>
	                            </td>
                                <td class="icon">
                                    <div class="span2">
                                    <a href="monitor_lists_<?php echo $value['id'];?>.html" title="筛选"><button class="btn btn-blue">筛选</button></a>
                                    <a href="monitor_edit_<?php echo $value['id'];?>.html" title="编辑"><button class="btn btn-blue">编辑</button></a>
                                    <a href="javascript:deleteMonitor(<?php echo $value['id']?>);" title="删除"><button class="btn btn-blue">删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dataTables_paginate paging_full_numbers">
            <?php echo $pagination;?>
        </div>
    </div>
</div>
<script src="Views/js/d3.v3.min.js"></script>
<script>
	function deleteMonitor(id) {
		if (window.confirm('确定要删除么？')) {
			$.post('index.php?c=monitor&a=delete', {id: id}, function (data) {
				if (typeof data.errors != 'undefined') {
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>删除失败!' + data.errors.msg + '</div>';
					$('#show_msg').html(warn_msg);
				} else {
					var succss_msg = '<div class="alert alert-success"><strong>删除成功!</strong> 2 秒后跳转到监管机构列表页面..</div>';
					$('#show_msg').html(succss_msg);
					setTimeout("location.href='monitor_lists.html'", 2000);
				}
			}, "json");
		}
		return false;
	}

	var tree = '<?php echo json_encode($tree, JSON_UNESCAPED_UNICODE)?>';
	var root = JSON.parse(tree);

	$.each(root, function(i, data){
		var str = JSON.stringify(data);
		var nNode = str.split('name').length-1;
		var hasC = str.split('children').length-1;
		var width = 960,
			height = 35 * (nNode - hasC);

		var cluster = d3.layout.cluster()
			.size([height, width - 180]);

		var diagonal = d3.svg.diagonal()
			.projection(function(d) { return [d.y, d.x]; });

		var svg = d3.select("body div.main-content").append("svg")
			.attr("width", width)
			.attr("height", height)
			.append("g")
			.attr("transform", "translate(80,0)");


		//d3.data(j_tree, function(error, root) {
		var nodes = cluster.nodes(data),
			links = cluster.links(nodes);

		var link = svg.selectAll(".link")
			.data(links)
			.enter().append("path")
			.attr("class", "link")
			.attr("d", diagonal);

		var node = svg.selectAll(".node")
			.data(nodes)
			.enter().append("g")
			.attr("class", function(d) {
				if (d.name == null) {
					d.name = '';
				}
				return d.name.indexOf("strong") > 0
					? "selected"
					: (d.is_scenic != undefined && d.is_scenic == true ? 'scenic' : "node"); })
			.attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })

		node.append("circle")
			.attr("r", 4.5)
			.on('click', function(d){
				var url = d.is_scenic != undefined && d.is_scenic == true
					? '/landscape_edit_'+ (2147483647 - d.id) + '.html'
					: '/monitor_edit_'+ d.id +'.html';
				window.location = url;
			});


		node.append("text")
			.attr("dx", function(d) { return d.children ? -8 : 8; })
			.attr("dy", 3)
			.style("text-anchor", function(d) { return d.children ? "end" : "start"; })
			.text(function(d) { return d.name.replace("<strong>","").replace("</strong>",""); })
			.on('click', function(d){
				var url = d.is_scenic != undefined && d.is_scenic == true
					? '/landscape_edit_'+ (2147483647 - d.id) + '.html'
					: '/monitor_lists_'+ d.id +'.html';
				window.location = url;
			});
		//});

		d3.select(self.frameElement).style("height", height + "px");
	});

	$(function(){
		$('.switch-btn').each(function() {
			$(this).bind("click", function() {
				var btn = $(this).find("span");
				var change = btn.attr("change");
				btn.toggleClass('off');
				$(this).toggleClass('off');

				if(btn.attr("class") == 'off') {
					$(this).find("input").val("0");
					btn.attr("change", btn.html());
					btn.html(change);
				} else {
					$(this).find("input").val("1");
					btn.attr("change", btn.html());
					btn.html(change);
				}

				var v = $(this).find("input").val();
				$.post('/monitor_gbSetting.html', {
					gb:$(this).find("input").attr('name'),
					val:v}, function(ret){
						if (ret + v != 1) {
							//console.log(ret);
						}
				});

				return false;
			});
		});
	})

</script>
</body>
</html>
