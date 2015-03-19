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

                .union_btn{
                    clear: both;
                    position: relative;
                    top: -10px;
                }

                form .row-fluid .span1{ line-height: 22px; width:60px;}
                form .row-fluid .span2{ margin:0px 20px 0px 10px;}
            </style>
            <div class="container-fluid padded" style="padding-bottom: 0px;">
                <!--景区相关关联信息开始-->
                <div class="box" style="margin-left:0px;">
                    <div class="box-header">
                        <span class="title">景区关联</span>
                    </div>
                    <div class="box-content">
                        <table class="table table-normal">
                            <?php
                            $value = $data['landscape'];
                            $isUion = $value['location_hash']; //是否关联
                            ?>
                            <tbody>
                                <tr>
                                    <td style="background: #F3F4F8;">机构名称:</td>
                                    <td style="background: #FFF;"><?php echo $value['organization']['name'] ?></td>

                                    <td style="background: #F3F4F8;">景区名称：</td>
                                    <td style="background: #FFF;"><?php echo $value['name'] ?></td>

                                    <td style="background: #F3F4F8;">状态:</td>
                                    <td style="background: #FFF;"><?php echo PoiCommon::$status[$value['status']]; ?></td>
                                </tr>

                                <tr>
                                    <td style="background: #F3F4F8;">POI名称：</td>
                                    <td style="background: #FFF;"><?php echo $value['location_name'] ?></td>

                                    <td style="background: #F3F4F8;">Hash：</td>
                                    <td style="background: #FFF;"><?php echo $value['location_hash'] ?></td>

                                    <td style="background: #F3F4F8;">关联时间：</td>
                                    <td style="background: #FFF;"><?php if ($isUion) {
                                echo $value['location_at'];
                            } ?></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

<?php if ($isUion): ?><div class="union_btn"><a href="javacript:void(0)"  data-info-id="<?php echo $value['id'] ?>" data-original-title="取消关联" class="btn btn-default cancelPoi">取消关联</a></div><?php endif; ?>
                <!--景区相关关联信息结束-->

                <!--POI查询开始-->
                <div class="box">
                    <div class="box-header">
                        <span class="title">POI查询</span>
                    </div>
                    <div class="box-content padded">
                        <form class="fill-up separate-sections" method="post" action="#">
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">POI名称：</div>
                                <div class="span2">
                                    <input type="text" name="name" placeholder="POI名称" value="<?php if(isset(PI::$data['post']['name'])) echo PI::$data['post']['name']; ?>">
                                </div>

                                <div class="span1">Hash：</div>
                                <div class="span2">
                                    <input type="text" name="hash" placeholder="" value="<?php if(isset(PI::$data['post']['hash'])) echo PI::$data['post']['hash']; ?>">
                                </div>

                                <div class="span3">
                                    <button class="btn btn-default" type="submit" id="searchBtn">搜索</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--POI列表开始-->
                    <div class="box-header">
                        <span class="title"></span>
                    </div>
                    <div class="box-content">
                        <table class="table table-normal">
                            <thead>
                                <tr>
                                    <td style="width:150px;">Hash</td>
                                    <td style="width:150px;">景区名称</td>
                                    <td>关联机构</td>
                                    <td  style="width: 100px">操作</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($poi as $item):
                                    ?>
                                    <tr>
                                        <td><?php echo $item['hash'] ?></td>
                                        <td><?php echo $item['name'] ?></td>
                                        <td><?php
                                            $hashPoi = isset($hashPois[$item['hash']]) ? $hashPois[$item['hash']] : array();
                                            foreach ($hashPoi as $item) {
                                                echo $item['organization']['name'];
                                            }
                                            ?></td>
                                        <td><?php if ($hashPoi): ?>已关联
    <?php else: ?>
                                                <a title="关联" data-toggle="modal" onclick="add_poi(<?php echo $value['id'] ?>, '<?php echo $item['hash'] ?>', '<?php echo $item['name'] ?>')">
                                                    <button class="btn btn-blue">关联</button>
                                                </a>    
                                    <?php endif; ?></td>
                                    </tr>
<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!--POI列表结束-->
                </div>
                <!--POI查询结束-->

                <!--分页开始-->
                <div class="dataTables_paginate paging_full_numbers">
<?php echo $pagination; ?>
                </div>
                <!--分页结束-->
            </div>
        </div>
        <div style="margin-top: 80px;"></div>
        <script src="Views/js/plugins/artDialog/jquery.artDialog.js?skin=black"></script>
        <script type="text/javascript">
                //取消关联按钮
                $('.cancelPoi').popover({'placement': 'bottom', 'html': true}).click(function() {
                    var info_id = $(this).attr('data-info-id');
                    var html = '<div class="editable-buttons info"><button class="btn btn-primary btn-sm" data-info-id="' + info_id + '"><i class="icon-ok"></i></button><button class="btn btn-default btn-sm" data-info-id="' + info_id + '"><i class="icon-remove"></i></button></div>';
                    $('.popover-content').html(html);
                    return false;
                });

                //取消关联按钮 - 同意
                $(document).on('click', '.info .btn-primary', function() {
                    var info_id = $(this).attr('data-info-id');
                    cancelPoi(info_id);
                    $('.cancelPoi').popover('hide');
                });

                //取消关联按钮 - 不同意
                $(document).on('click', '.info .btn-default', function() {
                    var info_id = $(this).attr('data-info-id');
                    $('.cancelPoi').popover('hide');
                });

                function cancelPoi(id)
                {
                    $.post('index.php?c=landscape&a=cancelPoi', {id: id}, function(data) {
                        if (data.errors) {
                            var tmp_errors = '';
                            $.each(data.errors, function(i, n) {
                                tmp_errors += n;
                            });
                            var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                            $('#show_msg').html(warn_msg);
                            location.href = '#show_msg';
                        } else if (data['data'][0]['updated_at']) {
                            var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
                            $('#show_msg').html(succss_msg);
                            location.href = '#show_msg';
                            setTimeout("location.href='landscape_unionPoi.html?id=<?php echo $value['id'] ?>'", '2000');
                        }
                    }, "json");
                    return false;
                }

                //通用警告框 
                function seft_warn($content, okCallFunc) {
                    var okCallFunc = okCallFunc || true;
                    var list = art.dialog.list;
                    for (var i in list) {
                        list[i].close();
                    }
                    ;
                    art.dialog({
                        title: '警告',
                        content: $content,
                        'ok': okCallFunc,
                        'fixed': true
                    });
                }

                //系统消息 
                function seft_system($content, okCallFunc) {
                    var okCallFunc = okCallFunc || true;
                    var list = art.dialog.list;
                    for (var i in list) {
                        list[i].close();
                    }
                    ;
                    art.dialog({
                        title: '系统消息',
                        content: $content,
                        'ok': okCallFunc,
                        'fixed': true
                    });
                };

                //添加关联POI
                function add_poi(id, hash, name) {
                <?php if ($isUion): ?>seft_warn('您已经关联POI，请取消关联后，再关联其它POI');
                        return false;<?php endif; ?>
                    $.post('index.php?c=landscape&a=addPoi', {id: id, hash: hash, name: name}, function(data) {
                        if (data.errors) {
                            var tmp_errors = '';
                            $.each(data.errors, function(i, n) {
                                tmp_errors += n;
                            });
                            var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                            $('#show_msg').html(warn_msg);
                            location.href = '#show_msg';
                        } else if (data['data'][0]['updated_at']) {
                            seft_system('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;操作成功!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                    function() {
                                        location.href = 'landscape_unionPoi.html?id=<?php echo $value['id'] ?>';
                                    }
                            );
                        }
                    }, "json");
                    return false;
                }
                //分页搜索代码跳转共用代码
                $('#searchBtn,a.paginate_button').click(function() {
                        $("form").attr('action', this.href).submit();
                        return false;
                });
        </script>
    </body>
</html>