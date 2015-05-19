<style>
    a{
        cursor: pointer;cursor: hand;
    }
</style>
<div class="contentpanel">
    <!--景区供应商关系列表-->
    <!--start-->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                景区列表
            </h4>
        </div>
        <div id="show_msg"></div>
        <div class="panel-body">
            <?php echo $scenicInfo['name'] . '--已绑定供应商列表';?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb30">
            <thead>
            <tr>
                <th>编号</th>
                <th>供应商名称</th>
                <th>电子票务账号</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?PHP foreach ($lists as $item):?>
                <tr>
                    <td style="text-align: center"><?php echo $item['organization_id'] ?></td>
                    <td>
                        <?php 
                        echo $orgNames[$item['organization_id']]['name']?>
                        <?php if($item['organization_id'] == $scenicInfo['organization_id']):?>
                            <span class="text-warning">（只读权限）</span><span class="text-danger">（管理权限）</span>
                        <?php else:?>
                            <span class="text-warning">（只读权限）</span>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if($item['organization_id'] == $scenicInfo['organization_id']):?>
                            <a href="/scenic/scenic/account/land_id/<?php echo $scenicInfo['id']?>/org_id/<?php echo $item['organization_id']?>" class="text-primary">
                                <i class="glyphicon glyphicon-user"></i>维护
                            </a>
                        <?php else:?>
                            暂无该权限
                        <?php endif;?>
                    </td>
                    <td>
                        <img src="/img/select2-spinner.gif" class="load" style="display: none" >
                        <?php if(isset($scenicInfo['organization_id']) && $scenicInfo['organization_id'] >0): ?>
                            <?php if($item['organization_id'] == $scenicInfo['organization_id']):?>
                                <a class="text-warning bind_admin clearPart" href="javascript:;"
                                    data-id="<?php echo $item['organization_id']?>"
                                    data-type="unbind"
                                    data-supply="<?php echo $orgNames[$item['organization_id']]['supply_type']?>"
                                    >解除管理权</a>
                                <a  class="clearPart" href="/scenic/scenic/switchadmin/land_id/<?php echo
                                $scenicInfo['id']?>/org_id/<?php echo $item['organization_id']?>" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">切换管理权</a>
                            <?php else:?>
                                <a class="text-success bind_supply clearPart" href="javascript:;"
                                   data-id="<?php echo $item['organization_id']?>"
                                   data-type="unbind" data-place="top">解除绑定</a>
                            <?php endif;?>
                        <?php else:?>
                                <a class="text-primary bind_admin clearPart" href="javascript:;"
                                   data-id="<?php echo $item['organization_id']?>"
                                   data-type="bind"
                                   data-supply="<?php echo $orgNames[$item['organization_id']]['supply_type']?>">绑定管理权</a>
                                <a class="text-success bind_supply clearPart" href="javascript:;"
                                   data-id="<?php echo $item['organization_id']?>"
                                   data-type="unbind" data-place="top">解除绑定</a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!--end-->

    <!--供应商列表-->
    <!--start-->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                供应商列表
            </h4>
        </div>
        <div id="land_rs"></div>
        <div class="panel-body">
            <form class="form-inline" action="/scenic/scenic/supply/">
                <div class="form-group">
                    供应商名称：
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input class="form-control" placeholder="请输入供应商名称" type="text" name="organization_name" style="width:318px;"
                           value="<?php echo isset($org_name) ? $org_name : ''?>">
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-sm">查询</button>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb30">
            <thead>
            <tr>
                <th>编号</th>
                <th>供应商名称</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?PHP foreach ($orgLists as $value): ?>
                <tr>
                    <td style="text-align: center"><?php echo $value['id'] ?></td>
                    <td><?php echo $value['name']?></td>
                    <td>
                        <?php if($value['bind'] == 1):?>
                            <img src="/img/select2-spinner.gif" class="load" style="display: none" >
                            <?php if($item['organization_id'] == $scenicInfo['organization_id']):?>
                                <a class="text-warning bind_admin clearPart" href="javascript:;"
                                    data-id="<?php echo $item['organization_id']?>"
                                    data-type="unbind"
                                    data-supply="<?php echo $value['supply_type']?>">解除管理权</a>
                                <a href="/scenic/scenic/switchadmin/land_id/<?php echo $scenicInfo['id']?>/org_id/<?php echo $item['organization_id']?>" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">切换管理权</a>
                            <?php else:?>
                                <a class="text-success bind_supply clearPart" href="javascript:;"
                                   data-id="<?php echo $item['organization_id']?>"
                                   data-type="unbind" data-place="top">解除绑定</a>
                            <?php endif;?>
                        <?php else:?>
<!--                        <a class="text-primary" data-toggle="modal" data-target=".bs-example-modal-static" onclick="bindSupply('<?php //echo $value['id']?>//','<?php //echo $value['supply_type']?>//')">绑定</a>
             -->
                            <img src="/img/select2-spinner.gif" class="load" style="display: none" >
                            <a  class="text-primary bind_supply clearPart" href="javascript:;"
                                data-id="<?php echo $value['id']?>" data-type="bind"
                                data-supply="<?php echo $value['supply_type']?>" data-place="down">绑定</a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="text-align:center" class="panel-footer">
        <div id="basicTable_paginate" class="pagenumQu">
            <?php
            if (!empty($orgLists)) {
                $this->widget('common.widgets.pagers.ULinkPager', array(
                        'cssFile' => '',
                        'header' => '',
                        'prevPageLabel' => '上一页',
                        'nextPageLabel' => '下一页',
                        'firstPageLabel' => '',
                        'lastPageLabel' => '',
                        'pages' => $pages,
                        'maxButtonCount' => 5, //分页数量
                    )
                );
            }
            ?>
        </div>
    </div>
</div>

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>
<script>
     function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }
</script>    
<!--end-->
<!--<div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static">-->
<!--    <script type="text/javascript">-->
<!--        function bindSupply(id,org_type){-->
<!--            document.getElementById('verify-modal').innerHTML = '';-->
<!--            var lan_id = $('input[name=id]').val();-->
<!--            $.get('/scenic/scenic/user/organization_id/' +id+ '/org_type/' +org_type+ '/landscape_id/' +lan_id, function(data) {-->
<!--                $('#verify-modal').html(data);-->
<!--            });-->
<!--        }-->
<!--    </script>-->
<!--</div>-->

    <script>
        jQuery(document).ready(function() {
            var lan_id = $('input[name=id]').val();


            $('.bind_admin').click(function(){
                var org_id = $(this).attr('data-id');
                var type = $(this).attr('data-type');
                var supply = $(this).attr('data-supply');
                if(type == 'unbind'){
                    var type_name = '解除该机构与本景区的管理权绑定吗？';
                }else{
                    type_name = '绑定该机构与本景区的管理权吗？';
                }

                PWConfirm('确定要'+ type_name,function(){
                    $('.bind_admin').hide();
                    $('.bind_supply').hide();
                    $('.load').show();
                     if(type == 'bind'&&supply == 1){
                        checkLandscape(org_id,lan_id,type);
                    }else{
                        bindAdmin(lan_id,org_id,type);
                    }
                   
                });
            })

            $('.bind_supply').click(function(){
                var org_id = $(this).attr('data-id');
                var type = $(this).attr('data-type');
                var supply = $(this).attr('data-supply');
                var org_lan = $(this).attr('data-lan');
                var place = $(this).attr('data-place');

                if(type == 'unbind'){
                    var type_name = '解除该机构与本景区的绑定吗？';
                }else{
                    type_name = '绑定该机构与本景区吗？';
                }

                PWConfirm('确定要'+ type_name,function(){
                    $('.bind_admin').hide();
                    $('.bind_supply').hide();
                    $('.load').show();
                    if(lan_id == org_lan){
                        alert('请先解除与景区的管理权限的绑定');
                        return false;
                    }                    
                    bindSupply(lan_id,org_id,type,place);
                   
                });
            })

            function checkLandscape(org_id,lan_id,type){
                $.post('/scenic/scenic/checklandscape/',{organization_id : org_id},function(data){
                    if(data.error){
                        setTimeout(function(){
                        //$('#land_rs').html('<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>该机构是景区用户，且已与景区'+data.msg+'绑定</div>');
                        alert('该机构是景区用户，且已与景区'+data.msg+'绑定', function() {
                            $('.bind_admin').show();
                            $('.bind_supply').show();
                            $('.load').hide();
                        });
                        }, 500); 
                    }else{
                        bindAdmin(lan_id,org_id,type);
                    }
                },'json')
            }

            function bindSupply(lan_id,org_id,type,place){
                $.post('/scenic/scenic/savebind/',{landscape_id : lan_id, organization_id : org_id,type : type},
                function(data){
                    if (data.error) {
                        setTimeout(function(){
                            alert(data.msg, function() {
                                $('.bind_admin').show();
                                $('.bind_supply').show();
                                $('.load').hide();
                            });
                        }, 500);
                    } else {
                        setTimeout(function(){
                            alert('保存成功！', function() {
                                window.location.partReload();
                            });
                        }, 500);
                    }
                },'json')
            }

            function bindAdmin(lan_id,org_id,type){
                $.post('/scenic/scenic/bindadmin/',{landscape_id : lan_id, organization_id : org_id,type : type},
                    function(data){
                        if (data.error) {
                            setTimeout(function(){
                                alert(data.msg, function() {
                                    $('.bind_admin').show();
                                    $('.bind_supply').show();
                                    $('.load').hide();
                                });
                            }, 500);
                        } else {
                            setTimeout(function(){
                                alert(data.msg, function() {
                                    window.location.partReload();
                                });
                            }, 500);
                        }
                    },'json')
            }
        });
    </script>
