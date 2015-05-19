<style>
#attach_box a
{ display: inline-block; margin-bottom: 10px; max-width: 100px; white-space: normal; word-wrap: break-word; }
</style>
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href=""
                   data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href=""
                   data-original-title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <h4 class="panel-title">分销商搜索</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/org/ownagency/">

                <!--分销商编号开始-->
                <div class="form-group">
                    <input class="form-control" name="id" value="<?php if(!empty($_GET['id'])){ echo $_GET['id'] ;}?>" placeholder="分销商编号" type="text" style="width:150px;">
                </div>
                <!--分销商编号结束-->

                <!--分销商名称开始-->
                <div class="form-group">
                    <input class="form-control" name="name" value="<?php if(!empty($_GET['name'])){ echo $_GET['name'] ;}?>" placeholder="分销商名称" type="text" style="width:150px;">
                </div>
                <!--分销商名称结束-->
                
                <!--省开始-->
                <div class="form-group" style="width: 100px;">
                    <select class="select2"  style="width: 100px;" id="province" name="province_id">
                        <option value="">省</option>
                        <?php
                        $province = Districts::model()->findAllByAttributes(array("parent_id" => 0));
                        foreach ($province as $model) {
                            if ($model->id == 0) {
                                continue;
                            } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <!--省结束-->
                
                <!--市开始-->
                <div class="form-group" style="width: 100px;">
                    <select style="width: 100px;" class="select2" data-placeholder="Choose One" id="city" name="city_id">
                        <option value="">市</option>
                        <?php
                        if (!empty($_GET['province_id'])) {
                            $city_value = $_GET['province_id'];
                            $city = Districts::model()->findAllByAttributes(array("parent_id" => $city_value));
                            foreach ($city as $model) {
                                if ($model->id == 0) {
                                    continue;
                                } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <!--市结束-->
                
                <!--县开始-->
                <div class="form-group" style="width: 100px;">
                    <select style="width: 100px;" class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id">
                        <option value="">县</option>
                        <?php
                        if (!empty($_GET['city_id'])) {
                            $area_value = $_GET['city_id'];
                            $area = Districts::model()->findAllByAttributes(array("parent_id" => $area_value));
                            foreach ($area as $model) {
                                if ($model->id == 0) {
                                    continue;
                                } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <!--县结束-->
                
                <div class="form-group">
                    <button class="btn btn-primary btn-sm" type="submit">查询</button>
                </div>
            </form>
        </div>

    </div>
    <!-- panel-body -->

    <!--表格开始-->
    <div class="tab-content mb30">
        <div id="t1" class="tab-pane active">


            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <td>编号</td>
                            <!-- <td>旅行社类别</td> -->
                            <td>名称</td>
                            <td>所在地</td>
                            <td>全平台分销权</td>
                            <td>属于供应商</td>
                            <td style="width: 150px;">操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        
                       <?php if ($lists): ?>
                            <?php
                            foreach ($lists as $value):
                                ?>

                                <tr class="status-pending" height="36px">
                                    <td class="icon"><?php echo $value['id']; ?></td>
                                    <td style="text-align: left">
                                        <div style="float: right">
                                            <?php
                                            if (isset($value['self-support'])) {
                                                echo '自营「' . $value['self-support'] . '」' ;
                                            }
                                            ?>
                                        </div>
                                        <a style="float: left" class="underline" href="/org/agency/view/id/<?php echo $value['id'] ?>/"><?php echo $value['name']; ?></a>
                                    </td>
                                    <td>
                                                <?php
                                                if ($value['province_id']) {
                                                    echo Districts::model()->findByPk($value['province_id'])->name;
                                                }

                                                if ($value['city_id']) {
                                                    echo Districts::model()->findByPk($value['city_id'])->name;
                                                }

                                                if ($value['district_id']) {
                                                    echo Districts::model()->findByPk($value['district_id'])->name;
                                                }
                                                ?>
                                                <?php echo $value['address']; ?>
                                    </td>
                                    <td>
                                                <?php if ($value['is_distribute_person'] == '1'): ?>散客√ <?php endif; ?>
                                                <?php if ($value['is_distribute_group'] == '1'): ?>团体√ <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                               // $result = Credit::api()->listbyxf(array('distributor_id' =>$value['id']));
                                                //print_r($result);
                                                if (isset($result['body']['data']) && !empty($result['body']['data'])) {
                                                    //如果存在所属供应商
                                                    $_attachs = $result['body']['data'];
                                                    foreach ($_attachs as $attach) {
                                                        if($value['id'] == $attach['distributor_id'])
                                                         echo $attach['supplier_name'] . '&nbsp;&nbsp;';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                    <a title="设置所属供应商" href="#set-attach" data-toggle="modal" data-target=".bs-example-modal-static" onclick="modal_jump('<?php echo $value['id'] ?>');">
                                                        设置供应商
                                                    </a>
                                            </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>
                                        
                    </tbody>
                </table>
            </div>

            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    if (!empty($lists)) {
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
        <div id="t2" class="tab-pane "></div>                   
    </div>    
     <!--表格结束-->
     
</div>

<!--设置供应商-->
<div id="set-attach" data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static">
    <div class="modal-dialog" style="width:700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">设置归属供应商</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>
                                <select id="supply-list" class="select2" style="width:100%">
                                    <option value="">请选择</option>
                                </select>
                            </td>
                            </td>
                            <td>
                                <button id="add-attach-btn" style="margin-left: 5px" class="btn btn-success btn-sm">增加</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top:10px">
                    <div id="attach_box" class="box">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                 <input type="hidden" name="attach_agency_id" id="attach_agency_id">
                <button id="set-attach-by-supply" class="btn btn-success" type="button">保存</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function() {    
    jQuery('.datepicker').datepicker();
    jQuery('.select2').select2({
            minimumResultsForSearch: -1
    });
    $('#province,#city,#area').select2();
 });    
</script>
<script>
    jQuery(document).ready(function() {
        /*
         省市县显示问题重置 create by ccq
         */
        //省联动
        $('#province').change(function() {

            var code = $(this).val();

            $('#city').html('<option value="">市</option>');
            $('#area').html('<option value="">县</option>');
            if (code == '') {
                /*
                 需要重置页面上的显示
                 */
                $('#city').select2('val', '');
                $('#area').select2('val', '');
                $('#city').html('<option value="">市</option>');
                $('#area').html('<option value="">县</option>');
            } else {
                $('#city').html('<option value="">市</option>');
                var html = new Array();
                 $.ajaxSetup({  async : false});  
                $.post('/ajaxServer/GetChildern', {id: code}, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }
                    $('#city').append(html.join(''));
                    $('#city,#area').select2();
                }, 'json');
                $.ajaxSetup({  async : true});  
            }
            return false;
        });


//市切换
        $('#city').change(function() {
            var code = $(this).val();
            if (code == '') {
                $('#area').select2('val', '');
                $('#area').html('<option value="">县</option>');
            } else {
                $('#area').html('<option value="">县</option>');
                var html = new Array();
                $.ajaxSetup({  async : false});  
                $.post('/ajaxServer/GetChildern', {id: code}, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }
                    $('#area').append(html.join(''));
                    $('#area').select2();
                }, 'json');
                 $.ajaxSetup({  async : true});  
            }
            return false;
        });
        
        //审核
         $('.btn-verify').click(function() {
                    var id = $(this).attr('data-id');
                    var status = $(this).attr('data-status');
                    $.post('/org/ownagency/verify/', {id: id, status: status}, function(result) {
                        if (result === 1) {
                            alert('操作成功',function(){location.partReload();});
                        }else{
                           alert('操作失败'); 
                        }
                    });
                });
    });
</script>



<script type="text/javascript">
    //设置分销商
    function modal_jump(id){
        $('#attach_agency_id').val(id);
        $.post('/org/ownagency/getAttach/',{id:id},function(data){
            for (i in data.mySupply) {
                var item = data.mySupply[i];
                if($('#attach_'+item.id).length<=0){
                    var html = '<a href="javascript:void(0);" class="label label-primary mr5 attach_label" onclick="delAttach(this);" val=' +
                                item.supplier_id + ' id="attach_' + item.supplier_id + '">' + item.supplier_name + '</a>';
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
            var html = '<a href="javascript:void(0);" class="label label-primary mr5 attach_label" onclick="delAttach(this);" val=' +
                id + ' id="attach_' + id + '">' + name + '</a>';
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

        $.post('/org/ownagency/saveAttach/',{agency_id:agency_id,ids:ids},function(data){
            if(data.error==0){
                alert("保存失败!"+data.message);
            }else{
                $('#set-attach').modal('hide');
                 alert("保存成功",function(){location.partReload();});
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
</script>




<script>
jQuery(document).ready(function() {
<?php
//选中
$selects = array('province_id','city_id','district_id');
foreach ($selects as $val):
    if (!empty($_GET[$val])):
        ?>
             $('[name=<?php echo $val ?>]').select2('val','<?php echo $_GET[$val] ?>');      
        <?php
    endif;
endforeach;
?>
 });

</script>
