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
                .table-1 tbody td{
                    text-align:center
                }
                .table-1 tbody td a{
                    text-decoration:none
                }
                .table-1 tbody td i{
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

                .modal .table-1 tbody td{
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
                        <span class="title"><?php echo $label ?>搜索</span>
                    </div>
                    <div class="box-content padded">
                        <form class="fill-up separate-sections" method="get" action="organization_<?php echo $type ?>.html">
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">编号</div>
                                <div class="span3">
                                    <input type="text" name="id" placeholder="<?php echo $label ?>编号" value="<?php
                                    if ($post['id']) {
                                        echo $post['id'];
                                    }
                                    ?>">
                                </div>
                                <div class="span1">名称</div>
                                <div class="span3">
                                    <input type="text" name="name" placeholder="请输入<?php echo $label ?>名称" value="<?php
                                           if ($post['name']) {
                                               echo $post['name'];
                                           }
                                    ?>">
                                </div>
                            </div>
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">注册日期</div>
                                <div class="span3">
                                    <input type="text" placeholder="" name="created_at" class="form-time" value="<?php
                                           if ($post['created_at']) {
                                               echo $post['created_at'];
                                           }
                                    ?>">
                                </div>

                                <div class="span1">所在地</div>
                                        <?php get_city(); ?>
                            </div>
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">可用状态</div>
                                <div class="span3">
                                    <select class="uniform" name="status">
                                        <option value="0" <?php
                                        if (!$post['status']) {
                                            echo "selected='selected'";
                                        }
                                        ?>>所有状态</option>
                                        <option value="1" <?php
                                        if ($post['status'] == '1') {
                                            echo "selected='selected'";
                                        }
                                        ?>>启用</option>
                                        <option value="disable" <?php
                                                if ($post['status'] == 'disable') {
                                                    echo "selected='selected'";
                                                }
                                                ?>>停用</option>
                                    </select>
                                </div>
                                <div class="span1">审核状态</div>
                                <div class="span3">
                                    <select class="uniform" name="verify_status">
                                        <option value="0" <?php
                                        if (!$post['verify_status']) {
                                            echo "selected='selected'";
                                        }
                                                ?>>所有状态</option>
<?php foreach ($verifyStatus as $key => $value) : ?>
                                            <option value="<?php echo $key; ?>" <?php
    if ($post['verify_status'] == $key) {
        echo "selected='selected'";
    }
    ?>><?php echo $value; ?></option>
<?php endforeach; ?>
                                    </select>
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
                        <span class="title"><?php echo $label ?>列表</span>
                    </div>
                    <div class="box-content">
                        <table class="table table-1">
                            <thead>
                                <tr>
                                    <td>编号</td>
                                    <!-- <td>旅行社类别</td> -->
                                    <td>名称</td>
                                    <td>所在地</td>
                                    <td>员工</td>
                                    <td>注册日期</td>
                                    <td>可用状态</td>
                                    <!--<td>用户</td>-->
<?php if ($type == 'agency') { ?><!--<td style="width: 80px">平台分销权</td>--><?php } ?>
                                    <td>审核状态</td>
<?php if ($type != 'agency') { ?> <td>是否允许信用/储值</td><?php } ?>
                                    <td style="width: 30px; text-align: center;">操作</td>
                                </tr>
                            </thead>

                            <tbody>
                                            <?php if ($data): ?>
                                                <?php
                                                $status = array(
                                                    'apply' => array('color' => 'blue', 'label' => '待审核', 'act' => '通过'),
                                                    'checked' => array('color' => 'rgb(0, 128, 0)', 'label' => '已审核', 'act' => '驳回'),
                                                    'reject' => array('color' => 'red', 'label' => '驳回', 'act' => '通过'),
                                                );
                                                foreach ($data as $value):
                                                    ?>

                                        <tr class="status-pending" height="36px">
                                            <td class="icon"><?php echo $value['id']; ?></td>
                                            <td style="text-align: left">
                                                <div style="float: right">
        <?php
        if (isset($value['self-support'])) {
            echo $type == 'supply' ? '自营「' . $value['self-support'] . '」' : '由「' . $value['self-support'] . '」直营';
        }
        ?>
                                                </div>
                                                <a style="float: left" class="underline" href="organization_view_<?php echo $value['id'] ?>.html"><?php echo $value['name']; ?></a>
                                            </td>
                                            <td>
                                                <?php echo $value['address']; ?>
                                            </td>
                                            <td class="icon">
                                                <a href="/organization_staff.html?organization_id=<?php echo $value['id'] ?>&type=<?php echo $type ?>" target="_blank"><i class="icon-user"></i></a>
                                            </td>
                                            <td><?php echo date('Y-m-d', $value['created_at']); ?></td>
                                            <td><?php echo $value['status'] == 1 ? '启用√' : '禁用'; ?></td>
                                       <!--<td class="icon">
                                           <a href="organization_staff_<?php echo $value['id']; ?>.html" title="账号列表"><i class="icon-user"></i></a>
                                       </td>-->
        <?php if ($type == 'agency') { ?>
                                               <!--<td class="icon">
                                            <?php if ($value['is_distribute_person'] == '1'): ?>散客√ <?php endif; ?>
                                            <?php if ($value['is_distribute_group'] == '1'): ?>团体√ <?php endif; ?>
                                               </td>-->
        <?php } ?>
                                            <td style="color: <?php echo $status[$value['verify_status']]['color']; ?>">
        <?php echo $status[$value['verify_status']]['label']; ?>
                                            </td>
        <?php if ($type != 'agency') { ?>  <td>
                                            <?php
                                            echo $value['is_credit']==1?'<font color="green">是</font>':'<font color="red">否</font>';
                                            ?>/
                                            <?php
                                            echo $value['is_balance']==1?'<font color="green">是</font>':'<font color="red">否</font>';
                                            ?>
                                            </td>
        <?php  }   ?>
                                            
                                            <td class="icon">
                                                <div class="span2" style="width: 170px">
                                                    <button class="btn btn-green btn-verify" data-id="<?php echo $value['id']; ?>" data-status="<?php echo $value['verify_status']; ?>" class="btn btn-red"><?php echo $status[$value['verify_status']]['act']; ?></button>
                                                    <a href="organization_edit_<?php echo $value['id']; ?>.html" title="编辑"><button class="btn btn-blue">编辑</button></a>

                                                    <!--a title="发消息" href="#sms" data-toggle="modal" onclick="modal_jump(<?php echo $value['id'] ?>, '<?php echo $value['name']; ?>');"><button class="btn btn-blue">发消息</button></a-->
                                                </div>
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
        <!--员工信息start-->
        <div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static"></div>
        <!--员工信息end--> 

        <div id="sms" class="modal hide fade">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h6 id="modal-formLabel">发送消息</h6>-->
            </div>
            <div id="model_show_msg"></div>
            <div class="modal-body select">
                <div class="container-fluid">
                    <div class="box">
                        <table class="table table-1">
                            <tbody>
                                <tr>
                                    <td>发送给：<span id='organization_name'>13838383838</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>输入消息内容：</label>
                                        <textarea id="msg_content" name="content"></textarea>
                                        <input type="hidden" name='organization_id' id='organization_id'/> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-green" type="button" id="send_msg">发送</button>
            </div>
        </div>

        <link href="Views/css/daterangepicker.css" rel="stylesheet">
        <script src="Views/js/vendor/date.js"></script>
        <script src="Views/js/vendor/moment.js"></script>
        <script src="Views/js/vendor/daterangepicker.js"></script>
        <script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('input.form-time').daterangepicker({format:'YYYY-MM-DD'});
                $('.btn-verify').click(function() {
                    var id = $(this).attr('data-id');
                    var status = $(this).attr('data-status');
                    $.get('index.php?c=organization&a=verify', {id: id, status: status}, function(result) {
                        if (result == 1) {
                            location.reload();
                        }
                    });
                });
            });

            // 供应商用户名列表 
            function Supplyuser(id) {
                $('#verify-modal').html();
                $.get('/ticket/depot/rule/?id=' + id, function(data) {
                    $('#verify-modal').html(data);
                });
            }
            // 供应商用户名列表 
            function Agencyuser(id) {
                $('#verify-modal').html();
                $.get('/ticket/depot/rule/?id=' + id, function(data) {
                    $('#verify-modal').html(data);
                });
            }


            //发送消息点击事件
            function modal_jump(organization_id, organization_name)
            {
                $('#organization_name').html(organization_name);
                $('#organization_id').val(organization_id);
            }

            $('#send_msg').click(function() {
                var oid = $('#organization_id').val();
                var con = $('#msg_content').val();
                if (!con) {
                    alert('消息内容不能为空');
                    return false;
                }
                $.post('index.php?c=organization&a=sendMessage', {type: '<?php echo $type ?>', oid: oid, con: con}, function(data) {
                    if (data['code'] == 'fail') {
                        var tmp_errors = data['message'];
                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                        $('#model_show_msg').html(warn_msg);
                        location.href = '#model_show_msg';
                    } else {
                        var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
                        $('#model_show_msg').html(succss_msg);
                        location.href = '#model_show_msg';
                        setTimeout("location.href='organization_<?php echo $type ?>.html'", 2000);
                    }
                }, "json");
                return false;
            });
        </script>
    </body>
</html>
