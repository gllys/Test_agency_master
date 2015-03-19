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

            </style>
            <div class="container-fluid padded" style="padding-bottom: 0px;">
                <div class="box">
                    <div class="box-header">
                        <span class="title">拥有景区审核</span>
                    </div>
                    <div class="box-content padded">
                        <form class="fill-up separate-sections" method="get" action="#">
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">景区名称：</div>
                                <div class="span2">
                                    <input type="text" name="keyword" value="<?php if (isset($get['keyword'])) echo $get['keyword']; ?>" placeholder="请输入景区名称">
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
                        <span class="title">景区列表</span>
                    </div>
                    <div class="box-content">
                        <table class="table table-normal">
                            <thead>
                                <tr>
                                    <td>编号</td>
                                    <td style="width:150px;">景区名称</td>
                                    <td>景区级别</td>
                                    <td>所在地区</td>
                                    <td style="width: 200px;">操作</td>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($list): ?>
                                
                                    <?php foreach ($list as $value): ?>
                                        <tr class="status-pending" height="36px">
                                            <td><?php echo $value['id']; ?></td>
                                            <td class="icon">
                                                <a href="/landscape_edit_<?php echo $value['id']; ?>.html" data-toggle="modal"><?php echo $value['name']; ?></a>
                                            </td>
                                            <td><?php echo $value['landscape_level_name']; ?></td>
                                            <td><?php
                                                echo $value['address'];
                                                ?></td>
                                            <td><a title="编辑" href="/landscape_edit_<?php echo $value['id']; ?>.html"><button class="btn btn-blue">编辑</button></a>
                                                <a href="/landscape_bindSupply.html?id=<?php echo $value['id']; ?>" title="绑定供应商"><button class="btn btn-green">绑定供应商</button></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
        <div id="verify-modal-big" class="modal hide fade" style="width:800px;margin-left: -400px;"><!-- 大弹出层 --></div>
        <div id="verify-modal" class="modal hide fade"><!-- 弹出层 --></div>

        <link href="Views/css/daterangepicker.css" rel="stylesheet">
        <script src="Views/js/vendor/date.js"></script>
        <script src="Views/js/vendor/moment.js"></script>
        <script src="Views/js/vendor/daterangepicker.js"></script>
        <script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            $(function() {
                /***表单公用开始**/
                //表单提交数据后自动赋值
<?php
$post = PI::$data['post'];
foreach ($post as $key => $val) {
    ?>
                    $('select[name=<?php echo $key ?>],input[name=<?php echo $key ?>]').val('<?php echo $val ?>');
<?php } ?>
                //分页搜索代码跳转共用代码
//                $('#searchBtn,a.paginate_button').click(function() {
//                    $("form").attr('action', this.href).submit();
//                    return false;
//                });

                //解决select js赋值如果不改变BUG
                $('form select').each(function() {
                    $(this).prev().text($(this).find("option:selected").text());
                });

                $('.form-time').daterangepicker({
                    format: 'YYYY-MM-DD'
                });
                /***表单公用结束**/
                //alert poi info
            });
            //景区审核
            function modal_jump_check(id)
            {
                $('#verify-modal-big').html();
                $.get('index.php?c=landscape&a=getModalJumpCheck&id=' + id, function(data) {
                    $('#verify-modal-big').html(data);
                });
            }

            //查看子景区
            function modal_jump_child(id) {
                $('#verify-modal').html('');
                $.get('index.php?c=landscape&a=childLists&id=' + id, function(data) {
                    $('#verify-modal').html(data);
                });
            }
        </script>
        <script type="text/javascript">

            $(function() {
                //城市更多
                if ($.cookie('city_more')) {
                    $('#more-wrap div.form-group:gt(6)').show();
                } else {
                    $('#more-wrap div.form-group:gt(6)').hide();
                }
                $('#more-btn').click(function() {
                    if ($(this).attr('flag') == 0) {
                        $('#more-wrap div.form-group:gt(6)').show();
                        $(this).attr('flag', 1);
                        $.cookie('city_more', 1);
                    } else {
                        $('#more-wrap div.form-group:gt(6)').hide();
                        $(this).attr('flag', 0);
                        $.cookie('city_more', null);
                    }
                });
            });

        </script>
    </body>
</html>
