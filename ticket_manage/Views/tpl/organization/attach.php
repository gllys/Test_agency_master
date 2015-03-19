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
    .popover-content .btn-default{
        min-width:inherit;
        margin-left:10px;
    }
    .popover-content button i{
        margin:0!important
    }
    .attach_label {
        margin: 5px;
    }
</style>

<div class="container-fluid padded" style="padding-bottom: 0px;">
    <div class="box">
        <div class="box-header">
            <span class="title"><?php echo $label?>搜索</span>
        </div>
        <div class="box-content padded">
            <form class="fill-up separate-sections" method="get" action="organization_attach.html">
                <div class="row-fluid" style="height: 30px;">
                    <div class="span1">分销商名称</div>
                    <div class="span3">
                        <input type="text" name="name" placeholder="请输入分销商名称" value="<?php if($get['name']){ echo $get['name'];}?>">
                    </div>
                    <div class="span1">所在地</div>
                    <?php get_city();?>
                </div>
                <div class="row-fluid" style="height: 30px;">
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
            <span class="title"><?php echo $label?>列表</span>
        </div>
        <div class="box-content">
            <table class="table table-normal">
                <thead>
                <tr>
                    <td>编号</td>
                    <td>分销商名称</td>
                    <td>所在地</td>
                    <td>全平台分销权</td>
                    <td style="min-width: 150px">属于供应商</td>
                    <td style="width: 30px">操作</td>
                </tr>
                </thead>

                <tbody>
                <?php if($data):?>
                    <?php foreach($data as $value):?>
                        <tr class="status-pending" height="36px">
                            <td class="icon"><?php echo $value['id'];?></td>
                            <td style="text-align: left">
                                <?php echo $value['name'];?>
                            </td>
                            <td>
                                <?php echo $value['address'];?>
                            </td>
                            <td>
                                <?php if($value['is_distribute_person'] == '1'):?>散客√ <?php endif;?>
                                <?php if($value['is_distribute_group'] == '1'):?>团体√ <?php endif;?>
                            </td>
                            <td>
                                <?php foreach($value['attachList'] as $attach):?>
                                    <?php echo $attach['supplier_name']; ?>&nbsp;&nbsp;
                                <?php endforeach; ?>
                            </td>
                            <td class="icon">
                                <div class="span2" style="width: 90px">
                                    <a title="设置所属供应商" href="#set-attach" data-toggle="modal" onclick="modal_jump('<?php echo $value['id']?>');">
                                        <button class="btn btn-blue">编辑</button>
                                    </a>
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

<div id="set-attach" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">设置归属供应商</h6>
    </div>
    <div id="model_show_msg"></div>
    <div class="modal-body select">
        <table>
            <tr>
                <td width="320px">
                    <select id="supply-list">
                        <option value="">请选择</option>
                    </select>
                </td>
                <td>
                    <button class="btn btn-green" style="margin-left: 5px" id="add-attach-btn">增加</button>
                </td>
            </tr>
        </table>

        <div class="container-fluid" style="margin-top: 10px">
            <div class="box" id="attach_box">

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="attach_agency_id" id="attach_agency_id">
        <button class="btn btn-green" type="button" id="set-attach-by-supply">保存</button>
    </div>
</div>

<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<script src="Views/js/common/common.js"></script>
<script type="text/javascript">
    function modal_jump(id){
        $('#attach_agency_id').val(id);
        $.post('organization_getAttach.html',{id:id},function(data){
            for (i in data.mySupply) {
                var item = data.mySupply[i];
                if($('#attach_'+item.id).length<=0){
                    var html = '<a href="javascript:void(0);" class="btn btn-blue mr5 attach_label" onclick="delAttach(this);" val=' +
                                item.supplier_id + ' id="attach_' + item.supplier_id + '">' + item.supplier_name + ' <i class="fa fa-times"></i></a>';
                    $('#attach_box').append(html);
                }
            }
            var optionStr = '<option value="">请选择</option>';
            for(i in data.supplyList){
                var supply = data.supplyList[i];
                optionStr += '<option value="'+supply.id+'">'+supply.name+'</option>';
            }
            $('#supply-list').html(optionStr);
        },'json');
    }
    $('#add-attach-btn').click(function(){
        var id = $('#supply-list').val();
        var name = $('#supply-list').find('option:selected').text();
        if(id==""){
            return false;
        }
        if($('#attach_'+id).length<=0){
            var html = '<a href="javascript:void(0);" class="btn btn-blue mr5 attach_label" onclick="delAttach(this);" val=' +
                id + ' id="attach_' + id + '">' + name + ' <i class="fa fa-times"></i></a>';
            $('#attach_box').append(html);
        }
    });
    $('#set-attach').on('hidden.bs.modal', function (e) {
        $('#supply-list').val('');
        $('#attach_box').html('');
    })
    $('#set-attach-by-supply').click(function(){
        var agency_id = $('#attach_agency_id').val();
        var supplyList = [];
        $('.attach_label').each(function () {
            supplyList.push($(this).attr('val'));
        });
        var ids = supplyList.join(',');

        $.post('organization_saveAttach.html',{agency_id:agency_id,ids:ids},function(data){
            if(data.error==0){
                alert("保存失败!"+data.message);
            }else{
                alert("保存成功");
                $('#set-attach').modal('hide');
                location.reload();
            }
        },'json');
    });
    function delAttach(obj){return false;
        var obj = $(obj);
        if (obj.siblings().length == 0) {
            $('#attach_box').html('<div style="text-align:center;padding:10px">暂无</div>');
        }
        obj.remove();
    }
    $(document).ready(function(){
        $('.form-time').daterangepicker({
            format:'YYYY-MM-DD'
        });
    });
</script>
</body>
</html>
