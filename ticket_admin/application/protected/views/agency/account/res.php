<?php 

$this->childNav = '/agency/account/res';

?>

<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title">查询分销商</h4>
                </div>
                <!-- panel-heading -->
                <form class="form-horizontal form-bordered" action="/agency/account/res">
                <div class="panel-body nopadding">
                    <div class="form-inline">
                        <div class="form-group">
                            <input class="form-control" placeholder="请输入供应商名称" style="width:200px;" type="text" name="name" value="<?php echo isset($agency_name) ? $agency_name : '' ?>">

                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <select class="select2"  data-placeholder="Choose One" id="province" name="province_id">
                                    <option value="__NULL__">选择省</option>
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
                            <div class="col-md-4">

                                <select class="select2" id="city" name="city_id">
                                    <option value="__NULL__">选择市</option>
                                    <?php
                                    if(isset($province_set)){
                                        $city_value = $province_set;
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

                            <div class="col-md-4">
                                <select class="select2" id="area" name="district_id">
                                    <option value="__NULL__">选择县</option>
                                    <?php
                                    if(isset($city_set)){
                                        $area_value = $city_set;
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
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-sm" type="submit">查询</button>
                        </div>
                    </div>
                </div>
                    </form>
                <!-- panel-body -->



            </div>
            <!-- panel -->
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>地区</th>
                        <th>联系人</th>
                        <th>手机号码</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($lists) && !empty($lists)):?>
                        <?php foreach($lists as $agency):?>
                            <tr style="height: 30px">
                                <td><?php echo $agency['name']?></td>
                                <td><?php echo $agency['city_name']?></td>
                                <td><?php echo $agency['contact']?></td>
                                <td><?php echo $agency['mobile']?></td>
                                <td>
                                    <?php if($agency['is_bind'] == 1):?>
                                        已添加
                                    <?php else:?>
                                        <a class="addcredit" href="javascript:;" data-id="<?php echo $agency['id']?>">添加</a>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                        <tr id="empty_tr">
                            <td colspan="5" style="text-align:center">暂无分销商</td>
                        </tr>
                    <?php endif;?>

                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu" style="margin-top:20px">
                    <?php
                    if(isset($lists) && !empty($lists)){
                        $this->widget('common.widgets.pagers.ULinkPager', array(
                            'cssFile' => '',
                            'header' => '',
                            'prevPageLabel' => '上一页',
                            'nextPageLabel' => '下一页',
                            'firstPageLabel' => '首页',
                            'lastPageLabel' => '未页',
                            'pages' => $pages,
                            'maxButtonCount' => 5, //分页数量
                        ));
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->

</div>


<script>
    jQuery(document).ready(function() {
        $('.addcredit').click(function(){
           $(this).attr("disabled","disabled");
           add($(this).attr('data-id'));   
        });

        // Select2

        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        $('#province').select2("val","<?php echo isset($province_set) ? $province_set : '__NULL__'?>");
        $('#city').select2("val","<?php echo isset($city_set) ? $city_set : '__NULL__'?>");
        $('#area').select2("val","<?php echo isset($district_set) ? $district_set : '__NULL__'?>");
//省切换
        $('#province').change(function() {
            var code = $(this).val();

            $('#city').html('<option value="__NULL__">市</option>');
            $('#area').html('<option value="__NULL__">县</option>');

            if (code == '__NULL__') {
                /*
                需要重置页面上的显示
                 */
                $('#city').select2('val','__NULL__');
                $('#area').select2('val','__NULL__');
                $('#city').html('<option value="__NULL__">市</option>');
                $('#area').html('<option value="__NULL__">县</option>');
            } else {
                $('#city').html('<option value="__NULL__">市</option>');
                var html = new Array();
                $.post('/ajaxServer/GetChildern' ,  {id:code}, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }
                    $('#city').append(html.join(''));
                    $('#city,#area').select2();
                }, 'json');
            }
            return false;
        });


//市切换
        $('#city').change(function() {
            var code = $(this).val();
            if (code == '__NULL__') {
                $('#area').select2('val','__NULL__');
                $('#area').html('<option value="__NULL__">县</option>');
            } else {
                $('#area').html('<option value="__NULL__">县</option>');
                var html = new Array();
                $.post('/ajaxServer/GetChildern',  {id:code}, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }
                    $('#area').append(html.join(''));
                    $('#area').select2();
                }, 'json');
            }
            return false;
        });

        function add(id){
        	$.post('/agency/manager/addcredit',{'id':id},function(data){
        		if(data.error==0){
				 	alert('保存成功',function(){
                        setTimeout("location.href='/site/switch/#/agency/manager/'", '1000');
                    });
                }else{
					alert(data.msg);
                }
        	},'json')
        }

    });
</script>
