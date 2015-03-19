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
                    margin:0 10px;
                    width:200px;
                }
                .table-normal tbody td a{
                    margin:0 5px;
                    text-decoration:none;
                }
                .table-normal button{
                    min-width:inherit;
                }
                .table-normal tbody td{
                    text-align:center
                }
                .table-1 td:nth-child(2n-1){
                    font-weight:700;
                    width:100px;
                }
                .table-1 td:nth-child(2n){
                    text-align: left;
                }
                .btn-group ul {
                    min-width: 80px;
                }
            </style>


            <div class="container-fluid padded">

                <div class="box">
                    <div class="table-header" style="height:auto;padding-bottom:10px; font-size: 14px;"><?php echo $scenicInfo['name'] ?>--已绑定供应商列表</div>
                    <div class="content">
                        <table class="table table-normal">
                            <thead>
                                <tr>
                                    <td>编号</td>
                                    <td>供应商名称</td>
                                    <td>电子票务账号</td>
                                    <td>操作</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $landscapeOrganizationIds = array(); #已绑定供应商id
                                if ($landscapeOrganization = $scenicInfo['landscape_organization']):
                                    ?>
                                    <?php
                                    foreach ($landscapeOrganization as $item):
                                        $landscapeOrganizationIds[] = $item; #已绑定供应商id
                                        ?> 
                                        <tr>
                                            <td><?php echo $item; ?></td>
                                            <td><?php
                                                $rs = Organizations::api()->show(array('id' => $item));
                                                $org = ApiModel::api()->getData($rs);
                                                if(isset($scenicInfo['organization_id']) && $scenicInfo['organization_id'] > 0){
                                                    if($scenicInfo['organization_id'] == $item){
                                                        echo $org['name']."<span style='color : red'>(管理权限)</span><span style='color : #BDB76B'>(只读权限)</span>";
                                                    }else{
                                                        echo $org['name']."<span style='color : #BDB76B'>(只读权限)</span>";
                                                    }
                                                }else{
                                                    echo $org['name']."<span style='color : #BDB76B'>(只读权限)</span>";
                                                }
                                                ?></td>
                                            <td>
                                            <?php if(isset($scenicInfo['organization_id']) && $scenicInfo['organization_id'] > 0):?>
                                                <?php if($scenicInfo['organization_id'] == $item):?>
                                                    <a href="/landscape_account_<?php echo $scenicInfo['id']; ?>.html?org_id=<?php echo $item?>"><i class="icon-user"></i>维护</a>
                                                <?php else:?>
                                                    <span>暂无该权限</span>
                                                <?php endif;?>
                                            <?php else:?>
                                                <span>暂无该权限</span>
                                            <?php endif;?>
                                            </td>
                                            <td>
                                                <?php if(isset($scenicInfo['organization_id']) && $scenicInfo['organization_id'] > 0):?>
                                                    <?php if($scenicInfo['organization_id'] == $item):?>
                                                        <a href="javascript:;" onclick="bindAdmin('<?php echo $item?>','unbind')"><button class="btn btn-default">解绑管理权</button></a>
                                                    <?php else:?>
                                                        <a href="javascript:;" onclick="unbindSupply('<?php echo $item ?>','yes')">
                                                            <button class="btn btn-green">解除绑定</button>
                                                        </a>
                                                    <?php endif;?>
                                                <?php else:?>
                                                    <a href="javascript:;" onclick="bindAdmin('<?php echo $item?>','bind')"><button class="btn btn-default">管理权绑定</button></a>
                                                    <a href="javascript:;" onclick="unbindSupply('<?php echo $item ?>','yes')">
                                                        <button class="btn btn-green">解除绑定</button>
                                                    </a>
                                                <?php endif;?>


                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">暂无景区绑定供应商记录</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                    <div class="box"  style="height:auto; margin-top: 25px; padding-bottom:10px; font-size: 14px;">
                        <div class="table-header" style="height:auto;padding-bottom:10px;">
                            <form action="">
                                <div class="row-fluid" style="margin-bottom:10px;">
                                    <input type="hidden" name="id" value="<?php echo $scenicInfo['id'] ?>" />
                                    供应商名称：<input type="text" placeholder="" name="supply_name"    value="<?php echo $get['supply_name']; ?>" style="width:300px;margin:0 10px 0">
                                    <button class="btn btn-default" style="float:none;">查询</button>
                                </div>
                            </form>
                        </div>
                        <div class="box">
                            <div class="table-header">所有供应商列表</div>
                            <div id="show_msg1"></div>
                            <div class="content">
                                <table class="table table-normal">
                                    <thead>
                                        <tr>
                                            <td>编号</td>
                                            <td>供应商名称</td>
                                            <td>操作</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($list):
                                            ?>
                                            <?php
                                            foreach ($list as $item):
                                                ?> 
                                                <tr>
                                                    <td><?php echo $item['id']; ?></td>
                                                    <td><?php echo $item['name']; ?></td>
                                                    <td>
                                                        <?php if (in_array($item['id'], $landscapeOrganizationIds)): ?>
                                                            <?php if(isset($scenicInfo['organization_id']) && $scenicInfo['organization_id'] > 0):?>  
                                                                <a  href="javascript:;" onclick="unbindSupply('<?php echo $item['id'] ?>','<?php echo $scenicInfo['organization_id'] == $item['id'] ? 'no' : 'yes' ?>')">
                                                                    <button class="btn btn-green">解除绑定</button>
                                                                </a>
                                                            <?php else:?>
                                                                <a  href="javascript:;" onclick="unbindSupply('<?php echo $item['id'] ?>','yes')">
                                                                    <button class="btn btn-green">解除绑定</button>
                                                                </a>
                                                            <?php endif;?>
                                                        <?php else: ?>
                                                            <a href="javascript:;" onclick="bindSupply('<?php echo $item['id'] ?>','<?php echo $item['supply_type']?>','<?php echo $item['landscape_id']?>')">
                                                                <button class="btn btn-blue">绑定</button>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5">未查寻到供应商记录</td>
                                            </tr>
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
                <script type="text/javascript">
                    //定义绑定landscape的方法
                    function bindSupply(organization_id,supply_type,scenic_id)
                    {
                        var landscape_id = "<?php echo $scenicInfo['id']; ?>";
                        if (window.confirm('确定绑定供应商么？')) {
                            if(supply_type == 1){
                                if(scenic_id > 0){
                                    $.post(
                                        'index.php?c=landscape&a=checkLandscape',
                                        {
                                            id: scenic_id
                                        },
                                        function(data){
                                            if (typeof data.error != 'undefined') {
                                                var tip_msg = '该机构是景区机构，并且已经绑定了景区' + data.error.name;
                                                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>绑定失败:' + tip_msg + '</div>';
                                                $('#show_msg1').html(warn_msg);
                                                return false;
                                            }
                                        },
                                        'json'
                                    )
                                }else{
                                    $.post(
                                        'index.php?c=landscape&a=checklandorg',
                                        {
                                            organization_id : organization_id
                                        },
                                        function(data){
                                            if (data.error) {
                                                var tip_msg = '该机构是景区机构，并且绑定了景区' + data.error.name;
                                                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>绑定失败:' + tip_msg + '</div>';
                                                $('#show_msg1').html(warn_msg);
                                                return false;
                                            }else{
                                                $.post(
                                                    'index.php?c=landscape&a=saveBindSupply',
                                                    {
                                                        organization_id: organization_id,
                                                        landscape_id: landscape_id
                                                    },
                                                function(data) {
                                                    if (typeof data.error != 'undefined') {
                                                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>绑定失败:' + data.error.msg + '</div>';
                                                        $('#show_msg').html(warn_msg);
                                                    } else {
                                                        var succss_msg = '<div class="alert alert-success"><strong>绑定成功!</strong> 2 秒后刷新本页..</div>';
                                                        $('#show_msg').html(succss_msg);
                                                        setTimeout(function() {
                                                            window.location.reload();
                                                        }, 2000);
                                                    }
                                                },
                                                        "json");
                                            }
                                        },
                                        'json'
                                    )

                                }
                            }else{
                                $.post(
                                        'index.php?c=landscape&a=saveBindSupply',
                                        {
                                            organization_id: organization_id,
                                            landscape_id: landscape_id
                                        },
                                function(data) {
                                    if (typeof data.error != 'undefined') {
                                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>绑定失败:' + data.error + '</div>';
                                        $('#show_msg').html(warn_msg);
                                    } else {
                                        var succss_msg = '<div class="alert alert-success"><strong>绑定成功!</strong> 2 秒后刷新本页..</div>';
                                        $('#show_msg').html(succss_msg);
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 2000);
                                    }
                                },
                                        "json");
                            }

                        }
                    }
                        
                    

                    //定义解绑绑定landscape的方法
                    function unbindSupply(id,status)
                    {
                        var landscape_id = "<?php echo $scenicInfo['id']; ?>";
                        if (window.confirm('确定解除绑定供应商么？')) {
                            if(status == 'no'){
                                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>请先解除该机构与景区间的管理权限绑定</div>';
                                $('#show_msg1').html(warn_msg);
                                return false;
                            }else{
                                $.post(
                                    'index.php?c=landscape&a=saveUnbindSupply',
                                    {
                                        organization_id: id,
                                        landscape_id: landscape_id
                                    },
                                function(data) {
                                    if (data.error) {
                                        var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>解除绑定失败!' + data.error + '</div>';
                                        $('#show_msg').html(warn_msg);
                                    } else {
                                        var succss_msg = '<div class="alert alert-success"><strong>解绑成功!</strong> 2 秒后刷新本页..</div>';
                                        $('#show_msg').html(succss_msg);
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 2000);
                                    }
                                },
                                        "json");
  
                            }
                        }
                    }

                    //定义景区绑定供应商管理权限
                    function bindAdmin(organization_id,type)
                    {
                        var id = "<?php echo $scenicInfo['id'];?>"
                        var organization_id = organization_id;
                        $.post(
                            'index.php?c=landscape&a=bindAdmin',
                            {
                                organization_id : organization_id,
                                id : id,
                                type : type
                            },
                            function(data){
                                if(data.error){
                                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>操作失败!' + data.error + '</div>';
                                    $('#show_msg').html(warn_msg);
                                }else{
                                    var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
                                    $('#show_msg').html(succss_msg);
                                    top.location.reload();
                                }
                            },
                            'json')
                    }
                </script>
                </body>
                </html>
