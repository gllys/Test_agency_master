<style>
.ui-datepicker { z-index:9999!important }
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
            <h4 class="panel-title">供应商搜索</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/org/supply/">

                <!--供应商编号开始-->
                <div class="form-group">
                    <input class="form-control" name="id" value="<?php if(!empty($_GET['id'])){ echo $_GET['id'] ;}?>" placeholder="供应商编号" type="text" style="width:150px;">
                </div>
                <!--供应商编号结束-->

                <!--供应商名称开始-->
                <div class="form-group">
                    <input class="form-control" name="name" value="<?php if(!empty($_GET['name'])){ echo $_GET['name'] ;}?>" placeholder="供应商名称" type="text" style="width:150px;">
                </div>
                <!--供应商名称结束-->

                <!--注册开始日期-->
                <div class="form-group " style="width: 335px;">
                    <input placeholder="注册日期开始" style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>"> ~
                    <input placeholder="注册日期结束"  style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                </div>
                <!--注册结束日期-->

                <!--可用状态开始-->
                <div class="form-group" style="width: 55px;">
                    可用状态
                </div>
                <div class="form-group" style="width: 120px;">
                    <select style="width: 120px;" class="select2 col-sm-4" data-placeholder="Choose One" name="status">
                        <option value="">所有状态</option>
                        <option value="1">启用</option>
                        <option value="disable" >禁用</option>
                    </select>
                </div>
                <!--可用状态结束-->

                <div></div>
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


                <!--审核状态开始-->
                <div class="form-group">审核状态</div>
                <div class="form-group" style="width: 120px;">
                    <select style="width: 120px;" class="select2 col-sm-4" data-placeholder="Choose One" id="area" name="verify_status">
                        <option value="">所有状态</option>
                        <?php
                        foreach($this->verify as $key=>$value):
                        ?>
                         <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <!--审核状态结束-->

                <!--供应商类型-->
                <div class="form-group" style="width: 65px;">
                   供应商类型
                </div>
                <div class="form-group" style="width: 120px;">
                    <select style="width: 120px;" class="select2 col-sm-4" data-placeholder="Choose One" name="supply_type">
                        <option value="">请选择</option>
                        <option value="0" <?php if(isset($_GET['supply_type']) && $_GET['supply_type'] === '0') echo 'selected="selected"';?>>批发商</option>
                        <option value="1" <?php if(isset($_GET['supply_type']) && $_GET['supply_type'] === '1') echo 'selected="selected"';?>>景区</option>
                    </select>
                </div>
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
                            <td width="120px">所在地</td>
                            <td>供应商类型</td>
                            <td>电子票务系统</td>
                            <td>员工</td>
                            <td>注册日期</td>
                            <td>可用状态</td>
                            <td>审核状态</td>
                            <td>是否允许信用/储值</td>
                            <td style="width: 150px;">操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        
                       <?php if ($lists): ?>
                            <?php
                            $status = array(
                                'apply' => array('color' => 'blue', 'label' => '待审核', 'act' => '通过'),
                                'checked' => array('color' => 'rgb(0, 128, 0)', 'label' => '已审核', 'act' => '驳回'),
                                'reject' => array('color' => 'red', 'label' => '驳回', 'act' => '通过'),
                            );
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
                                        <a class="underline" href="/org/supply/view/id/<?php echo $value['id'] ?>/"><?php echo $value['name']; ?></a>
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
                                    <td><?php echo ($value['supply_type']) ? '景区' : '批发商';?></td>
                                    <td><?php
                                        if(!$value['supply_type']) {
                                            echo '未使用';
                                        }else if($value['partner_type'] == 0) {
                                            echo '票台';
                                        }else if($value['partner_type'] == 1) {
                                            echo '大漠';
                                        }
                                        ?>
                                    </td>
                                    <td class="icon">
                                        <a href="/org/supply/staff/id/<?php echo $value['id'] ?>/"><i class="fa fa-user"></i></a>
                                    </td>
                                    <td><?php echo date('Y-m-d', $value['created_at']); ?></td>
                                    <td><?php echo $value['status'] == 1 ? '启用√' : '禁用'; ?></td>
                                    <td style="color: <?php echo $status[$value['verify_status']]['color']; ?>">
                                        <?php echo $status[$value['verify_status']]['label']; ?>
                                    </td>
                                    <td>
                                      <?php
                                       echo $value['is_credit'] == 1 ? '<font color="green">是</font>' : '<font color="red">否</font>';
                                      ?>/
                                      <?php
                                        echo $value['is_balance'] == 1 ? '<font color="green">是</font>' : '<font color="red">否</font>';
                                      ?>
                                   </td>
                                   <td>
                                       <a class="btn-verify clearPart" title="<?php echo $status[$value['verify_status']]['act']; ?>" style="" href="javascript:void(0)" data-id="<?php echo $value['id']; ?>" data-status="<?php echo $value['verify_status']; ?>">
                                                   <?php echo $status[$value['verify_status']]['act']; ?>
                                       </a>
                                       <a title="编辑" style="margin-left: 10px;" href="/org/supply/edit/id/<?php echo $value['id'] ?>/">编辑</a>
                                       <a title="景区" style="margin-left: 10px;" href="/org/supply/lan/id/<?php echo $value['id'] ?>/">景区列表</a>
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
<script>
jQuery(document).ready(function() {    
    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
        yearRange: "1995:2065",
        beforeShow: function(d){
            setTimeout(function(){
                $('.ui-datepicker-title select').select2({
                    minimumResultsForSearch: -1
                });
            },0)
        },
        onChangeMonthYear: function(){
            setTimeout(function(){
                $('.ui-datepicker-title select').select2({
                    minimumResultsForSearch: -1
                });
            },0)
        },
        onClose: function(dateText, inst) { 
            $('.select2-drop').hide(); 
        }
    });
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
                    $.post('/org/supply/verify/', {id: id, status: status}, function(result) {
                        if (result == 1) {
                            alert('操作成功',function(){location.partReload();});
                        }else{
                           alert('操作失败'); 
                        }
                    });
                });
    });
</script>

<script>
jQuery(document).ready(function() {
<?php
//选中
$selects = array('province_id','city_id','district_id','status', 'verify_status');
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