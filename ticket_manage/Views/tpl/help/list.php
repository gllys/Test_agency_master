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
            
    <div class="container-fluid padded" style="padding-bottom: 0px;">
        <div class="box">
            <div class="box-header">
				<span class="title">文档检索</span>
            </div>
            <div class="box-content padded">
                <form class="fill-up separate-sections" method="post" action="help_lists.html">
                    <div class="row-fluid" style="height: 30px;">
                        <div class="span1">提交日期</div>
                        <div class="span2">
                            <input type="text" placeholder="" name="update_time" class="form-time">
                        </div>

                        <div class="span1">标题</div>
                        <div class="span2">
                            <input type="text" name="name" placeholder="请输入文档标题" value="<?php if($post['name']){ echo $post['name'];}?>">
                        </div>
                     </div>
                     <div class="row-fluid" style="height: 30px;">
                        <div class="span1">类别</div>
                        <div class="span2">
	                        <?php if ($allTypes) : ?>
	                        <select class="uniform" name="type_id">
		                        <option value="0" <?php if(!$post['type_id']){ echo "selected='selected'";}?>>所有类别</option>
	                            <?php foreach ($allTypes as $type) : ?>
                                <option value="<?php echo $type['id']?>" <?php if($post['type_id'] == $type['id']){ echo "selected='selected'";}?>><?php echo $type['name']?></option>
		                        <?php endforeach; ?>
                            </select>
	                        <?php endif; ?>
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
                <span class="title">文档列表</span>
                <ul class="box-toolbar">
					<li>
						<a data-toggle="modal" href="/help_add.html">
							<span class="label label-green">添加帮助文档</span>
						</a>
					</li>
				</ul>
            </div>
            <div class="box-content">
                <table class="table table-normal">
                    <thead>
                    <tr>
                        <td>编号</td>
                        <td>类别</td>
                        <td>标题</td>
                        <td>提交时间</td>
                        <td style="width: 30px">操作</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if($data):?>
                        <?php foreach($data as $value):?>
                            <tr class="status-pending" height="36px">
                                <td class="icon"><?php echo $value['id'];?></td>
                                <td class="status-success" style="text-align: center;">
                                    <b><?php echo $value['type_name'];?></b>
                                </td>
                                <td style="text-align: left"><a class="text-left underline" href="help_edit_<?php echo $value['id']?>.html"><?php echo $value['name'];?></a></td>
                                <td><?php echo $value['updated_at'];?></td>
                                <td class="icon">
                                    <div class="span2">
                                    <a href="help_edit_<?php echo $value['id'];?>.html" title="编辑"><button class="btn btn-blue">编辑</button></a>
                                    <a href="javascript:deleteHelp(<?php echo $value['id']?>);" title="删除"><button class="btn btn-blue">删除</button></a>
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

</body>
</html>
