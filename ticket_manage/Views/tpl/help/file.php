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
                .pop-content ul{
                    margin:0;
                    list-style:none
                }
                .pop-content li{
                    padding:0;
                    line-height:2
                }
                .table-normal tbody td{
                    text-align:center
                }
                .table-normal tbody td a{
                    text-decoration:none
                }
                .table-normal tbody td i{
                    margin-left:10px
                }
                .popover-content button i{
                    margin:0!important
                }

                .modal .table-normal tbody td{
                    text-align:left
                }
                #sms textarea{
                    width:100%;
                    height:100px;
                }
            </style>
    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title">资料列表</span>
                <ul class="box-toolbar">
					<li>
						<a data-toggle="modal" href="/help_write.html">
							<span class="label label-green">添加帮助资料</span>
						</a>
					</li>
				</ul>
            </div>
            <div class="box-content">
                <table class="table table-normal">
                    <thead>
                    <tr>
                        <td>编号</td>
                        <td>名称</td>
                        <td>说明</td>
                        <td style="width: 30px">操作</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if($helpFilesList):?>
                        <?php foreach($helpFilesList as $value):?>
                            <tr class="status-pending" height="36px">
                                <td class="icon"><?php echo $value['id'];?></td>
                                <td class="status-success" style="text-align: center;">
                                    <b><?php echo $value['name'];?></b>
                                </td>
                                <td style="text-align: center"><?php echo $value['desc'];?></td>
                                <td class="icon">
                                    <div class="span2">
                                    <a href="help_write_<?php echo $value['id'];?>.html" title="更新"><button class="btn btn-blue">更新</button></a>
                                    <a href="javascript:deleteFile(<?php echo $value['id']?>);" title="删除"><button class="btn btn-blue">删除</button></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                   		<?php else:?>
                   			<tr class="status-pending" height="36px">
                   				<td><?php echo "暂无资料";?></td>
                   			</tr>
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

</body>
</html>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/help/write.js"></script>
