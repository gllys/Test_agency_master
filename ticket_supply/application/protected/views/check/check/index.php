    <?php
$this->breadcrumbs = array('核销', '核销管理');
?>

<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
            </div><!-- panel-btns -->
            <h4 class="panel-title">核销管理</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get">
                <div class="mb10">
                    <div class="form-group" style="margin:0">
                        <input style="cursor: pointer;cursor: hand;background-color: #ffffff" class="form-control datepicker" name="begin_date" value="<?php if (isset($_GET['begin_date'])) echo $_GET['begin_date'] ?>" placeholder="游玩日期" type="text" readonly="readonly">
                    </div><!-- form-group -->
                    <div class="form-group" style="margin:0">
                        <input style="cursor: pointer;cursor: hand;background-color: #ffffff" class="form-control datepicker" name="end_date" value="<?php if (isset($_GET['end_date'])) echo $_GET['end_date'] ?>" placeholder="游玩日期" type="text" readonly="readonly">
                    </div><!-- form-group -->

                    <div class="form-group" style="margin:0">
                        <select class="select2" name="landscape_id" id="check-landscape" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                            <option value="">景区</option>
                            <?php
                            $lid = Yii::app()->user->lan_id;
                            foreach ($landscapes as $landscape) {
                                $selected = "";
                                if (isset($_GET['landscape_id']) && $_GET['landscape_id'] == $landscape['id']) {
                                    $selected = "selected='selected'";
                                }
                                echo '<option ' . $selected . ' value="' . $landscape['id'] .
                                '">' . $landscape['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0">
                        <select class="select2" id="check-point" name="view_point" style="width:150px;padding:0 10px;">
                            <option value="">景点</option>
                            <?php
                            foreach ($pois as $poi) {
                                $selected = "";
                                if (isset($_GET['view_point']) && $_GET['view_point'] == $poi['id']) {
                                    $selected = "selected='selected'";
                                }
                                echo '<option ' . $selected . ' value="' . $poi['id'] . '">' . $poi['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0 5px 0 0">
                        <input class="form-control" name="order_id" value="<?php if (isset($_GET['order_id'])) echo $_GET['order_id'] ?>" placeholder="订单编号" type="text" style="width:318px;">
                    </div>
                    <button class="btn btn-primary btn-xs" type="submit">查询</button>
                </div>

            </form>
        </div><!-- panel-body -->
    </div>

    <style>
        .tab-content .table tr>*{
            text-align:center
        }
        .tab-content .ckbox{
            display:inline-block;
            width:30px;
            text-align:left

        }

    </style>


    <table class="table table-bordered mb30">
        <thead>
            <tr>
                <th sttyle="width:10%">订单编号</th>
                <th sttyle="width:10%">验证时间</th>
                <th sttyle="width:10%">验证数量</th>
                <th sttyle="width:10%">验证景区</th>
                <th sttyle="width:10%">验证景点</th>
                <th sttyle="width:5%">验证结果</th>
                <th sttyle="width:10%">操作员</th>
                <th sttyle="width:10%">设备类型</th>
                <th sttyle="width:10%">设备编号</th>
                <th sttyle="width:10%">设备名称</th>
                <th sttyle="width:5%">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($lists as $item):
                ?>
                <tr>
                    <td sttyle="width:10%"><?php echo $item['record_code'] ?></td>
                    <td sttyle="width:10%"><?php echo date('Y-m-d H:i:s', $item['created_at']) ?></td>
                    <td sttyle="width:10%"><?php echo $item['num'] ?></td>
                    <td style="text-align: left;width:10%"><?php
	                    //todo optimize
                        $rs = Landscape::api()->detail(array('id' => $item['landscape_id']));
                        $data = ApiModel::getData($rs);
                        echo isset($data['name']) ? $data['name'] : ''
                        ?>
                    <td style="text-align: left;width:10%"><?php
                        if (strlen($item['poi_id']) > 0) {
                            $p_ids = explode(',', $item['poi_id']);
                            $spans = array();
                            foreach($p_ids as $pid) {
                                $spans[] = sprintf('<span role="async-name" class="poi-%d" data-id="poi_%d"></span>', $pid, $pid);
                            }
                            echo implode(',', $spans);
                        }
                        ?>
                    </td>
                    <td sttyle="width:10%"><span class="text <?php echo $item['status'] ? 'text-success' : 'text-danger' ?>"><?php echo $item['status'] ? '成功' : '失败' ?></span></td>
                    <td sttyle="width:10%"><?php
                        echo $item['user_name'];
                        ?>
                    </td>
                    <td sttyle="width:5%"><?php
                       if (isset($item['equipment_code']) && !empty($item['equipment_code'])) {
	                       //todo optimize
                            $lists = Equipments::api()->detail(array('code' => $item['equipment_code']));
                            $list = ApiModel::getData($lists);
                            if($list){
                                echo $list['type'] == 1?'闸机':'手持机';
                            }
                        }
                        ?></td>
                    <td sttyle="width:10%"><?php
                        echo $item['equipment_code'] == 0 ? '' : $item['equipment_code'];
                        ?></td>
                    <td sttyle="width:10%"><?php
                        if (isset($item['equipment_code']) && !empty($item['equipment_code'])) {
	                        //todo optimize
                            $lists = Equipments::api()->detail(array('code' => $item['equipment_code']));
                            $list = ApiModel::getData($lists);
                            if (isset($list['name'])) {
                                echo $list['name'];
                            }
                        }
                        ?></td>
                    <td sttyle="width:5%"><?php
                        if ($item['cancel_status'] == 1) {
                            ?>
                            已撤销
                            <?php
                        } else {
                            if ($item['status'] == 1 && ( time() - $item['created_at'] < 300)) { #五分钟内票可以撤销
                            ?>
                            <a href="#" onclick="cancel('<?php echo $item['id'] ?>')" class="btn btn-primary btn-xs" id="dell">撤销</a>
                            <?php
                            }
                        }
                        ?></td>
                </tr>
                <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <div style="text-align:center" class="panel-footer">
        <div id="basicTable_paginate" class="pagenumQu">
            <?php
            $this->widget('CLinkPager', array(
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
            ?>
        </div>
    </div>


</div><!-- contentpanel -->
<script src="/js/async.names.js"></script>
<script>
    window.cancel = function(id) {
        if (confirm("您是否需要撤销该操作")) {
            $.post('/check/check/cancel/',{id:id}, function(data){
                if(data.error){
                    alert(data.msg);
                }else{
                  window.location.reload();
                }
            },"json");
        }
        return false;
    }


    jQuery(document).ready(function() {
        //撤销
        function cancel(id) {

        }




        $('#all-btn').click(function() {
            var obj = $(this).parents('table')
            if ($(this).is(':checked')) {
                obj.find('input').prop('checked', true)
                $(this).text('反选')
            } else {
                obj.find('input').prop('checked', false)
                $(this).text('全选')
            }
        })


// Tags Input
        jQuery('#tags').tagsInput({width: 'auto'});

// Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();

// Spinner
        var spinner = jQuery('#spinner').spinner();
        spinner.spinner('value', 0);

// Form Toggles
        jQuery('.toggle').toggles({on: true});

// Time Picker
        jQuery('#timepicker').timepicker({defaultTIme: false});
        jQuery('#timepicker2').timepicker({showMeridian: false});
        jQuery('#timepicker3').timepicker({minuteStep: 15});

// Date Picker
        jQuery('.datepicker').datepicker();
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });

// Input Masks
        jQuery("#date").mask("99/99/9999");
        jQuery("#phone").mask("(999) 999-9999");
        jQuery("#ssn").mask("999-99-9999");

// Select2
        jQuery("#check-landscape, #check-point").select2();


        $('#check-landscape').change(function(event, $first) {
            var id = $(this).val();
            var optionStr = "<option value='' selected='selected'>景点</option>";
            $('#check-point').html(optionStr).select2();
            if (!id) {
                return false;
            }
            var view_point =<?php echo isset($_GET['view_point']) && !empty($_GET['view_point']) ? $_GET['view_point'] : 0 ?>;
            if ($first == null) {
                view_point = 0;
            }
            $.post('/check/check/getPoi', {id: id}, function(data) {
                var optionStr = "<option value='' selected='selected'>景点</option>";
                for (var i in data.result) {
                    var item = data.result[i];
                    if (view_point == item.id) {
                        optionStr += '<option value="' + item.id + '" selected="selected">' + item.name + '</option>';
                    } else {
                        optionStr += '<option value="' + item.id + '">' + item.name + '</option>';
                    }
                }
                $('#check-point').html(optionStr).select2();
            }, 'json');
        });
    });

</script>

