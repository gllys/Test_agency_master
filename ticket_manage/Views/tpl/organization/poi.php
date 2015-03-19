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

            <style type="text/css">
                .label-green{
                    cursor:pointer
                }
                .pop{
                    position:relative;
                    display:inline-block;
                }
                .pop-content{
                    display:none;
                    width:300px;
                    position:absolute;
                    top:20px;
                    left:0;
                    z-index:1;
                    background-color:#fff;
                    padding:10px;
                    border-radius:5px;
                    border:1px solid #ccc;
                    box-shadow:0 5px 10px rgba(0,0,0,.1)
                }
                .pop:hover .pop-content{
                    display:block;
                    text-align:left;
                }
                .pop-content ul{
                    margin:0;
                    list-style:none
                }
                .pop-content li{
                    padding:0;
                    line-height:2
                }
                div.selector{
                    margin-right:10px;
                    width:100px;
                }
                .btn-green{
                    min-width:inherit
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
                    
                .dropdown-menu{
                    min-width:90px
                }
                .popover{
                    width:125px
                }
                .popover-content .btn-default{
                    min-width:inherit;
                    margin-left:10px;
                }
                .popover-content button i{
                    margin:0!important
                }
                .btn-primary{
                    background:#428BCA;
                    border-color:#357EBD;
                    color: #FFF;
                }
                .btn-group:hover .dropdown-menu{
                    display:block;
                    top:27px;
                }
               
            </style>
    <div class="container-fluid padded" style="padding-bottom: 0px;">
        <div class="box">
            <div class="box-header">
				<span class="title">拥有景区审核</span>
            </div>
            <div class="box-content padded">
                <form class="fill-up separate-sections" method="post" action="organization_reorder.html">
                    <div class="row-fluid" style="height: 30px;">
                        <div class="span1">提交日期：</div>
                        <div class="span2">
                            <input type="text" placeholder="" name="updated_at" class="form-time" />
                        </div>

                       <div class="span1">审核状态：</div>
                        <div class="span2">
                            <select class="uniform" name="status">
                                <option value="0">全部</option>
                                <option value="unaudited">未审核</option>
                                <option value="normal">审核通过</option>
                                <option value="failed">驳回</option>
                            </select>
                        </div>

                       <div class="span1">机构名称：</div>
                        <div class="span2">
                            <input type="text" name="organization_name" placeholder="请输入机构名称">
                        </div>
                    </div>
                    <div class="row-fluid" style="height: 30px;">
                        <div class="span1">机构编号：</div>
                        <div class="span2">
                            <input type="text" name="organization_id" placeholder="机构编号" value="">
                        </div>
                        
                        <div class="span1">是否启用：</div>
                        <div class="span2">
                            <select class="uniform" name="organization_status">
                                <option value="0">所有状态</option>
                                <option value="normal">启用</option>
                                <option value="disable">停用</option>
                            </select>
                        </div>
    
                        <div class="span3">
                            <button class="btn btn-default" type="submit">搜索</button>
                        </div>
                     </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title">拥有景区列表</span>
            </div>
            <div class="box-content">
                <table class="table table-normal">
                    <thead>
                    <tr>
                        <td style="width:150px;">拥有景区名称</td>
                        <td>景区级别</td>
                        <td>所在地区</td>
                        <td>提交时间</td>
                        <td>机构名称</td>
                        <td>机构编号</td>
                        <td>是否启用</td>
                        <td>审核状态</td>
                        <td style="width: 30px">操作</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if($poiList):?>
                        <?php foreach($poiList as $value):?>
                            <tr class="status-pending" height="36px">
                                <td class="icon">
                                    <a href="#verify-modal-poi" data-toggle="modal" onclick="modal_jump_poi(<?php echo $value['id'];?>)"><?php echo $value['name'];?></a>
                                </td>
                                <td><?php echo $value['level']['name'];?></td>
                                <td>
                                    <?php if($value['districts']):?>
                                        <?php foreach($value['districts'] as $key => $val):?>
                                            <?php echo $val['name'];?>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                </td>
                                <td><?php echo $value['updated_at'];?></td>
                                <td>
                                    <div class="pop">
                                        <span class="label label-green" data-placement="bottom" data-toggle="popover" data-container="body">
                                            <i class="icon-zoom-in"></i> <?php echo $value['organization']['name'];?>
                                        </span>
                                        <div class="pop-content">
                                            <ul>
                                            <li>机构名称：<?php echo $value['organization']['name'];?></li>
                                            <li>联系人：<?php echo $value['organization']['contact'];?></li>
                                            <li>手机：<?php echo $value['organization']['mobile'];?></li>
                                            <li>公司电话：<?php echo $value['organization']['telephone'];?></li>
                                            <li>公司传真：<?php echo $value['organization']['fax'];?></li>
                                            <li>邮箱：<?php echo $value['organization']['email'];?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $value['organization']['id'];?></td>
                                <td><?php echo OrganizationCommon::$status[$value['organization']['status']];?></td>
                                <td><?php echo PoiCommon::$status[$value['status']];?></td>
                                <td class="icon">
                                    <?php if($value['status'] == 'unaudited'):?>
                                        <a title="审核" href="#verify-modal-poi" data-toggle="modal" onclick="modal_jump_poi(<?php echo $value['id'];?>)">
                                            <button class="btn btn-blue">审核</button>
                                        </a>
                                    <?php endif;?>
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

<div id="verify-modal-poi" class="modal hide fade"><!-- 弹出层 --></div>

<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/organization/poi.js" type="text/javascript"></script>
</body>
</html>