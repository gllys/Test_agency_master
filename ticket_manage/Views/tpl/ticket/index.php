<!DOCTYPE html>
<html>
    <?php get_header(); ?>
    <body>
        <?php get_top_nav(); ?>
        <div class="sidebar-background">
            <div class="primary-sidebar-background"></div>
        </div>
        <?php get_menu(); ?>
        <div class="main-content">
            <?php get_crumbs(); ?>

            <div id="show_msg">
            </div>
            <style>
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
                #verify-modal{
                    margin:auto;
                    left:0;
                    right:0;
                    width:80%;
                }

                #verify-modal .modal-body{
                    max-height:none;
                    overflow:visible;
                }
            </style>
            <script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
            <div class="container-fluid padded">
                <div class="box">
                    <div class="table-header" style="height:auto;padding-bottom:10px;">
                        <form action="">
                            <div class="row-fluid" style="margin-bottom:10px;">
                                更新时间：<input type="text" placeholder="" name="updated_at" style="width:180px;margin:0 10px 0" class="form-time" value="<?php echo $get['created_at']; ?>">
                                审核状态：
                                <select class="uniform" name="status">
                                    <option value="">全部</option>
                                    <?php foreach ($allStatus as $allStatusKey => $allStatusVal): ?>
                                        <option value="<?php echo $allStatusKey; ?>" <?php if ($get['status'] == $allStatusKey): ?>selected="selected"<?php endif; ?>><?php echo $allStatusVal; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                名称：<input type="text" placeholder="" name="landscape_name" value="<?php echo $get['landscape_name']; ?>" style="width:100px;margin:0 10px 0">
                                发布机构：<input type="text" placeholder="" name="organization_name" value="<?php echo $get['organization_name']; ?>"  style="width:100px;margin:0 10px 0">

                                接入状态：
                                <select class="uniform" name="location_hash">
                                    <option <?php if ($get['location_hash'] == '') {
                                        echo "selected";
                                    } ?> value="">全部</option>
                                    <option <?php if ($get['location_hash'] == 'yes') {
                                        echo "selected";
                                    } ?> value="yes">已接入</option>
                                    <option <?php if ($get['location_hash'] == 'no') {
                                        echo "selected";
                                    } ?> value="no">未接入</option>
                                </select>
                                <button class="btn btn-default" style="float:none;">搜索</button>
                            </div>
                        </form>
                    </div>

                    <div class="content">
                        <table class="table table-normal">
                            <thead>
                                <tr>
                                    <td>一级票务编号</td>
                                    <td>名称</td>
                                    <td>地区</td>
                                    <td>所属机构</td>
                                    <td>更新时间</td>
                                    <td>二级票务</td>
                                    <td>接入POI</td>
                                    <td>一级票务状态</td>
                                </tr>
                            </thead>
                            <tbody>
<?php if ($landscapesList): ?>
    <?php foreach ($landscapesList as $landscape): ?>
                                        <tr>
                                            <td><?php echo $landscape['id']; ?></td>
                                            <td><a href="ticket_preview_<?php echo $landscape['id']; ?>.html"><?php echo $landscape['name']; ?></a></td>
                                            <td><?php echo $landscape['districts'][0]['name'] . $landscape['districts'][1]['name'] . $landscape['districts'][2]['name'] ?></td>
                                            <td>
                                                <div class="pop">
                                                    <span class="label label-green" data-placement="bottom" data-toggle="popover" data-container="body">
                                                        <a href="organization_view_<?php echo $landscape['organization']['id']; ?>.html">
                                                            <i class="icon-zoom-in"></i> <?php echo $landscape['organization']['name']; ?>
                                                        </a>
                                                    </span>
                                                    <div class="pop-content">
                                                        <ul>
                                                            <li>机构名称：<?php echo $landscape['organization']['name']; ?></li>
                                                            <li>联系人：<?php echo $landscape['organization']['contact']; ?></li>
                                                            <li>手机：<?php echo $landscape['organization']['mobile']; ?></li>
                                                            <li>公司电话：<?php echo $landscape['organization']['telephone']; ?></li>
                                                            <li>公司传真：<?php echo $landscape['organization']['fax']; ?></li>
                                                            <li>邮箱：<?php echo $landscape['organization']['email']; ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        <!--<td class="center"><?php echo OrganizationCommon::getOrganizationType($landscape['organization']['type']); ?></td>-->
                                            <td class="center"><?php echo $landscape['updated_at']; ?></td>
                                            
                                            <td class="center">
                                                <div class="btn-group">
                                                <?php if (empty($landscape['second'])): ?>
                                                        <span class="label">暂无</span>
                                                        <?php else: ?>
                                                        <button class="btn <?php if ($landscape['second'][0]['status'] == 'unaudited'): ?>btn-red<?php else: ?>btn-green<?php endif; ?>">
                                                        <?php echo LandscapeCommon::getLandscapeStatus($landscape['second'][0]['status']); ?>
                                                        </button>
                                                        <button class="btn <?php if ($landscape['second'][0]['status'] == 'unaudited'): ?>btn-red<?php else: ?>btn-green<?php endif; ?> dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                                        <ul class="dropdown-menu">
                                                        <?php foreach ($landscape['second'] as $second): ?>
                                                                <li>
                                                                    <a href="#verify-modal" onclick="modal_jump(<?php echo $landscape['id']; ?>, '<?php echo $second['status']; ?>')" data-toggle="modal">
                                                        <?php echo LandscapeCommon::getLandscapeStatus($second['status']); ?>
                                                                    </a>
                                                                </li>
                                                                <li class="divider"></li>
                                                    <?php endforeach; ?>
                                                        </ul>
                                                <?php endif; ?>
                                                </div>

                                            </td>
                                            <td class="center"><?php if (empty($landscape['location_hash'])): ?><a title="接入POI" href="landscape_unionPoi.html?id=<?php echo $landscape['id'] ?>.html"><i class="icon-sitemap"></i></a>
                                            <?php else: ?>
                                                    <a title="" href="landscape_unionPoi.html?id=<?php echo $landscape['id'] ?>.html"><?php echo $itour_ism_landscapes_list[$landscape['location_hash']]['name']; ?><br><?php echo $landscape['location_name']; ?></a>
                                            <?php endif; ?>         
                                            </td>
                                            
                                            <td class="center">
                                                        <?php if ($landscape['status'] == 'unaudited'): ?><a title="审核" href="" data-original-title="审核" class="verify" data-info-id="<?php echo $landscape['id']; ?>"><button class="btn btn-blue">未审核</button></a>
                                                        <?php else: ?>    
                                                        <a title="审核" href="" data-original-title="审核" class="verify" data-info-id="<?php echo $landscape['id']; ?>">
                                                                <span class="label label-green">
                                                                <?php echo LandscapeCommon::getLandscapeStatus($landscape['status']); ?>
                                                            </span>
                                                        </a>
                                                        <?php endif; ?>    
                                           </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="dataTables_paginate paging_full_numbers">
<?php echo $pagination; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="verify-modal" class="modal hide fade" style="position: absolute;">
        </div>
        <script src="Views/js/vendor/date.js"></script>
        <script src="Views/js/vendor/moment.js"></script>
        <script src="Views/js/vendor/daterangepicker.js"></script>
        <link href="Views/css/daterangepicker.css" rel="stylesheet">
        <script src="Views/js/ticket/index.js"></script>
    </body>
</html>