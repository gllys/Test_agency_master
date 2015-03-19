<div class="modal-dialog" style="width: 900px !important;">
    <div class="modal-content" style="padding-left: 10px;padding-right: 10px;">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">修改产品</h4>
        </div>
        <form  method="post" action="#" id="form" class="form-horizontal form-bordered">
            <input value="<?php   echo $ticket['id'];?>" type="hidden" name="id">
            <table class="table table-bordered" style="margin-bottom: 30px !important;">
                <thead>
                    <tr>
                        <th style="width:220px;">景区名称</th>
                        <th style="width:220px;">门票</th>
                        <th>窗口价格</th>
                        <th>数量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="take-ticket">
                    <?php
                    $supplylanIds = PublicFunHelper::arrayKey($supplyLans, 'landscape_id');
                    $param['ids'] = join(',', $supplylanIds);
                     $param['items'] = 100000;
                    $data = Landscape::api()->lists($param);
                    $lanLists = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
                    ?>
                    <?php
                    if (!empty($ticket['items'])):
                        $base_items = $ticket['items'];
                        $_POST['ticketBase'] = array(); //单例
                        foreach ($base_items as $key=>$base_item):
                            ?>
                            <tr>
                                <td>
                                    <div class="form-group" style="margin:0">
                                        <select class="select2 lan" data-placeholder="Choose One" style="width:200px;padding:0 10px;">
                                            <option value="">请选择景区</option>
                                            <?php foreach ($supplyLans as $item): ?>
                                                <option value="<?php echo $item['landscape_id'] ?>" <?php if ($base_item['scenic_id'] == $item['landscape_id']): ?>selected="selected"<?php endif; ?>><?php
                                                    //todo optimize
                                                    if (isset($lanLists[$item['landscape_id']])) {
                                                        echo $lanLists[$item['landscape_id']]['name'];
                                                    }
                                                    ?></option>  
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group" style="margin:0">
                                        <select class="select2 poi" data-placeholder="Choose One" style="width:200px;padding:0 10px;">
                                            <option value="">请选择门票</option>
                                            <?php
                                            //得到景区门票
                                            if (isset($_POST['ticketBase'][$base_item['scenic_id']])) {
                                                $ticketBases = $_POST['ticketBase'][$base_item['scenic_id']];
                                            } else {
                                                $param = array();
                                                $param['scenic_id'] = $base_item['scenic_id'];
                                                $param['state'] = 1;
                                                $param['items'] = 10000;
                                                $param['types'] = '1,2,3,5';
                                                $data = ApiModel::getLists(Tickettemplatebase::api()->lists($param, true));
                                                $ticketBases = $_POST['ticketBase'][$base_item['scenic_id']] = $data;
                                            }
                                            ?>
                                            <?php foreach ($ticketBases as $k => $item): ?>
                                                <option value="<?php echo $item['id'] ?>" <?php if ($base_item['base_id'] == $item['id']): ?>selected="selected"<?php endif; ?>><?php
                                                    //todo optimize
                                                    $_type = TicketType::model()->findByPk($item['type']);
                                                    echo $item['name'] . $_type['name'];
                                                    ?></option> 
                                                <?php
                                                if ($base_item['base_id'] == $item['id']) {
                                                    //删除已选择记录，以后的
                                                    unset($_POST['ticketBase'][$base_item['scenic_id']][$k]);
                                                    ?>
                                                    <script type="text/javascript">
                                                        //多次加载问题
                                                        $(function() {
                                                            $("select.poi:lt(<?php echo $key ?>)").find('option[value=<?php echo $item['id'] ?>]').remove();
                                                        });
                                                    </script>
                                                <?php } ?>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <td class="sale_price">
                                <?php echo $base_item['sale_price']; ?>
                                </td>
                                <td><input type="text" name="base_items[<?php echo $base_item['base_id']; ?>]" value="<?php echo $base_item['num']; ?>" class="spinner_num" style="cursor: pointer;cursor: hand;background-color: #ffffff" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></td>
                                <td>
                                    <?php
                                    if($key === 0):
                                    ?>
                                    <div class="btn btn-primary btn-xs" id="take-ticket-add">增加</div>    
                                    <?php else: ?>
                                    <div class="btn btn-danger btn-xs" id="take-ticket-add">删除</div>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                   <?php endif; ?>
                </tbody>
            </table>
            <div class="panel-body nopadding">
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger">*</span>产品名称:</div></label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="请输入产品名称" maxlength="20" tag="产品名称" class="validate[required] form-control" name="name" value="<?php echo $ticket['name']?>">
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <div class="col-sm-2 control-label">
                        <div class="ckbox ckbox-primary pull-right" style="text-align: left;">
                            <input type="checkbox" name="is_fit" type="checkbox" value="1"  id="checkboxPrimary23" <?php if(!empty($ticket['is_fit'])){ echo "checked=checked";}?> style="vertical-align: middle;">
                            <label for="checkboxPrimary23" style="vertical-align: middle;"><span class="text-danger"></span>散客结算价:</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" placeholder="请输入价格" readonly tag="散客结算价"  class=" form-control onlyMoney" name="fat_price" id="sk_price" value="<?php echo $ticket['fat_price']?>">
                    </div>

                </div><!-- form-group -->
                <div class="form-group">
                    <div class="col-sm-2 control-label">
                        <div class="ckbox ckbox-primary pull-right" style="text-align: left;">
                            <input type="checkbox" name="is_full" value="1" id="checkboxPrimary12" style="vertical-align: middle;"  <?php if(!empty($ticket['is_full'])){ echo "checked=checked";}?>>
                            <label for="checkboxPrimary12" style="vertical-align: middle;"><span class="text-danger"></span>团队结算价:</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" placeholder="请输入价格" readonly tag="团队结算价"  class=" form-control onlyMoney" name="group_price" id="tg_price" value="<?php echo $ticket['group_price']?>">
                    </div>
                    <div class="col-sm-5">
                        最少订票 <input type="text" id="spinner-min" tag="最少订票" class="spinner" style="cursor: pointer;cursor: hand;background-color: #ffffff" name="mini_buy" value="<?php echo $ticket['mini_buy'];?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"> 张
                    </div>
                </div><!-- form-group -->


                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否允许退票:</div></label>
                    <div class="col-sm-6">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  <?php if($ticket['refund']==1){echo 'checked="checked"'; }?> value="1"  name="refund" id="radioDefault33">
                            <label for="radioDefault33">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  <?php if($ticket['refund']==0){echo 'checked="checked"'; }?> value="0"   name="refund" id="radioDefault2">
                            <label for="radioDefault2">否</label>
                        </div>
                    </div>
                </div><!-- form-group -->


                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left"></span>提前预定时间:</div></label>
                    <div class="col-sm-10">
                        <?php $day = floor($ticket['scheduled_time'] / 86400);
                            $times = $ticket['scheduled_time'] % 86400;
                            $hour[0] = intval($times / 3600);
                            $minutes = $times % 3600;
                            $hour[1] = $minutes / 60;
                            $time = implode(':', $hour);
                            ?>
                        <label class="pull-left" style="margin-top: 5px;">需在入园前&nbsp;</label> <input type="text" class="spinner-day form-control" name="scheduled" value="<?php echo $day;?>" style="cursor: pointer;cursor: hand;background-color: #ffffff" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"> 天的
                        <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker2" type="text" class="form-control" name="scheduledtime" style="width:50px" value="<?php echo $time;?>"></div>
                        以前购买
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>产品销售有效期:</div></label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="" tag="产品销售开始日期" class="form-control datepicker validate[required]" id='sale_available_1' name="sale_start_time" value='<?php if($ticket['sale_start_time']) echo date('Y-m-d',$ticket['sale_start_time']) ;?>' readonly style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff"> ~
                        <input type="text" placeholder="" tag="产品销售结束日期" class="form-control datepicker validate[required]" id='sale_available_2' name="sale_end_time" value='<?php if($ticket['sale_end_time']) echo date('Y-m-d',$ticket['sale_end_time']) ;?>' readonly style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff">
                    </div>
                    <div class="ckbox ckbox-primary" style="display:inline-block;margin-left:10px;display: none;">
                        <input type="checkbox" name="sale_limit" type="checkbox" value="1" <?php if(empty($ticket['sale_start_time'])&&empty($ticket['sale_end_time'])):?>checked="checked"<?php endif ?>  id="checkboxPrimary30">
                        <label for="checkboxPrimary30"><span class="text-danger"></span>不限期</label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>使用有效期:</div></label>
                    <div class="col-sm-10"><?php list($a,$b)=explode(',',$ticket['date_available']);  ?>
                        <input type="text" placeholder="" tag="使用开始日期" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[1]"  value="<?php if($a) echo date('Y-m-d',$a);?>" readonly> ~
                        <input type="text" placeholder="" tag="使用结束日期" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[2]"  value="<?php if($b) echo date('Y-m-d',$b);?>" readonly>
                        &nbsp;&nbsp;预订游玩日期后 <input type="text" class="spinner-day" name="valid" value="<?php echo $ticket['valid']?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"> 天有效
                    <div class="ckbox ckbox-primary" style="display:inline-block;margin-left:10px">
                        <input type="checkbox" name="valid_flag" type="checkbox" value="1"  <?php if($ticket['valid_flag']):?> checked="checked"<?php endif?> id="checkboxPrimary42">
                        <label for="checkboxPrimary42"><span class="text-danger"></span>不限期</label>
                    </div>
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10 days-checkbox">
                        <div class="checkbox-group">
                            <?php $arr = explode(',', $ticket['week_time']); ?>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox"  id="d1" value="1" name="week_time[]" <?php if (in_array(1, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d1">周一</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d2" value="2" name="week_time[]" <?php if (in_array(2, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d2">周二</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d3" value="3" name="week_time[]" <?php if (in_array(3, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d3">周三</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d4" value="4" name="week_time[]" <?php if (in_array(4, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d4">周四</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d5" value="5" name="week_time[]" <?php if (in_array(5, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d5">周五</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d6" value="6" name="week_time[]" <?php if (in_array(6, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d6">周六</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d7" value="0" name="week_time[]" <?php if (in_array(0, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d7">周日</label>
                            </div>
                        </div>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right">产品说明</div></label>
                    <div class="col-sm-10">
                        <textarea id="myremark"  name='remark'  placeholder="请输入您的门票说明..." class="form-control" rows="10"><?php echo $ticket['remark']?></textarea>
                    </div>
                </div>
            </div><!-- panel-body -->
            <div class="panel-footer"><button type="submit" class="btn btn-primary"  id="form-button">保存</button> </div>
        </form>

    </div>
</div>
<script type="text/javascript">
    var spinner = jQuery('.spinner').spinner({'min': 1});
    //spinner.spinner('value', 1);

    $('select').eq(-1).select2({
        // minimumResultsForSearch: -1
    });
    $('select').eq(-2).select2({
        //minimumResultsForSearch: -1
    });
    $('select').eq(-3).select2({
        //minimumResultsForSearch: -1
    });

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

    var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
    //spinnerDay.spinner('value', 1);


    var tpl = '<tr>' +
            '<td>' +
            '<div class="form-group" style="margin:0">' +
            '<select class="select2 lan" data-placeholder="Choose One" style="width:200px;padding:0 10px;">' +
            '<option value="">请选择景区</option>' +<?php foreach ($supplyLans as $item): ?>
        '<option value = "<?php echo $item['landscape_id'] ?>"><?php
    if (isset($lanLists[$item['landscape_id']])) {
        echo $lanLists[$item['landscape_id']]['name'];
    }
    ?></option>' +
<?php endforeach; ?>
    '</select>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group" style="margin:0">' +
            '<select class="select2 poi" data-placeholder="Choose One" style="width:200px;padding:0 10px;">' +
            '<option value="">请选择门票</option>' +
            '</select>' +
            '</div>' +
            '</td>' +
            '<td class="sale_price">0.00</td><td><input type="text" name="" class="spinner_num" style="cursor: pointer;cursor: hand;background-color: #ffffff" onkeyup="this.value=this.value.replace(/\D/g,\'\')" onafterpaste="this.value=this.value.replace(/\D/g,\'\')" /></td>' +
            '<td><div class="btn btn-danger btn-xs" id="take-ticket-add">删除</div></td></tr>';

    $(function() {
        var spinner = jQuery('.spinner_num').spinner({'min': 1});
        //spinner.spinner('value', 1);
        $('select.lan,select.poi').select2({});
        $('#take-ticket-add').click(function() {
            $("#take-ticket").append(tpl);
            /*jQuery('.select2').select2({
             minimumResultsForSearch: -1
             });*/
            $('select').eq(-1).select2({
            });
            $('select').eq(-2).select2({
            });
            $('select').eq(-3).select2({
            });

            var spinner = jQuery('.spinner_num').last().spinner({'min': 1});
            spinner.spinner('value', 1);

        });

        //不限期
        $('[name=sale_limit]').click(function() {
            if ($(this).prop('checked')) {
                $('#sale_available_1').val('').attr("disabled", true);
                $('#sale_available_2').val('').attr("disabled", true);
            } else {
                $('#sale_available_1').attr("disabled", false);
                $('#sale_available_2').attr("disabled", false);
            }
        });
        //如果选择了团散客
        window.setInterval(function() {
            if ($('[name=is_fit]').prop('checked')) {
                $('input[name="fat_price"]').removeAttr("readonly").addClass('validate[required]');
            } else {
                $('input[name="fat_price"]').val("");
                $('input[name="fat_price"]').attr("readonly", "readonly").removeClass('validate[required]');
            }

            if ($('[name=is_full]').prop('checked')) {
                $('input[name="group_price"]').removeAttr("readonly").addClass('validate[required]');
                $('#spinner-min').removeAttr("readonly").css('background-color','#ffffff');
            } else {
                $('input[name="group_price"]').val("");
                $('input[name="group_price"]').attr("readonly", "readonly").removeClass('validate[required]');
                 $('#spinner-min').attr("readonly", "readonly").val(1).css('background-color','#eeeeee');
            }
            
           if ($('[name=valid_flag]').prop('checked')) {
                $('input[name="valid"]').attr("readonly", "readonly").val(0).css('background-color','#eeeeee');
            }else{
                $('input[name="valid"]').removeAttr("readonly").css('background-color','#ffffff');
            }
            
            if ($('[name=sale_limit]').prop('checked')) {
                $('input[name="sale_start_time"]').attr("readonly", "readonly").removeClass('validate[required]').css('background-color','#eeeeee');
                $('input[name="sale_end_time"]').attr("readonly", "readonly").removeClass('validate[required]').css('background-color','#eeeeee');
            }else{
               $('input[name="sale_start_time"]').removeAttr("readonly").addClass('validate[required]').css('background-color','#ffffff');
               $('input[name="sale_end_time"]').removeAttr("readonly").addClass('validate[required]').css('background-color','#ffffff');
            }
        }, 200);


        //提示设置
        $('#form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true
        });
        //提交表单
        $('#form-button').click(function() {
            if (!$('[name=is_fit]').prop('checked') && !$('[name=is_full]').prop('checked')) {
                alert('团队价和散客价至少选一个');
                return false;
            }

            var _flag = false;
            $('select.lan').each(function() {
                if ($(this).val() === '') {
                    alert('请选择景区');
                    $(this).select2("open");
                    _flag = true;
                    return false;
                }
            });
            if (_flag) {
                return false;
            }

            var _flag = false;
            $('select.poi').each(function() {
                if ($(this).val() === '') {
                    alert('请选择门票');
                    $(this).select2("open");
                    _flag = true;
                    return false;
                }
            });
            if (_flag) {
                return false;
            }
            var obj = $('#form');
            if (obj.validationEngine('validate') === true) {
                $('#form-button').attr('disabled', true);
                $.post('/ticket/single/edit', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-button').attr('disabled', false);
                    } else {
                        alert('修改产品成功',function(){window.location.reload();});
                    }
                }, 'json');
            }
            return false;
        });
    });

    //多次加载问题
    $(function() {
        if (window['edit.js'])
            return;
        window['edit.js'] = true;
        //景区改变
        var ticketTypes = <?php echo json_encode(TicketType::model()->findAll()) ?>;
        $(document).on('change', 'select.lan', function() {
            var lan_id = $(this).val();
            if (lan_id === '') {
                //保存之前的值
                var $poi = $(this).parents('tr').find('select.poi');
                select_old[$poi.val()] = $poi.find("option:selected").text();
                $poi.children('option').eq(0).attr("selected", true).nextAll().remove();
                $poi.trigger('change');
                return false;
            }
            var that = this;
            $.post('/ticket/single/getbase/', {id: lan_id}, function(data) {
                var _data = data['msg'];
                var _html = '';
                for (i in _data) {
                    _html += '<option sale_price="' + _data[i]['sale_price'] + '" value="' + _data[i]['id'] + '">' + _data[i]['name'] + '(' + ticketTypes[_data[i]['type']]['name'] + ')' + '</option>';
                }
                var _select = $(that).parents('tr').find('select.poi');
                  _select.children('option').eq(0).attr("selected", true).nextAll().remove().end().after(_html);
                $("select.poi").each(function() {
                    if ($(this).val() != '') {
                        _select.find('option[value=' + $(this).val() + ']').remove();
                    }
                });
            }, 'json');
            return false;
        });


        $("#take-ticket").on('click', '.btn-danger', function() {
            var $poi = $(this).parents('tr').find('select.poi');
            $poi.trigger('select2-open');
            $poi.val('');
            $poi.trigger('change');
            $(this).parents('tr').remove()
        });
        //下拉改变值后不能再选
        window.select_old;
        window.select_old_lan;
        $(document).on('select2-open', 'select.poi', function() {
            //存储select修改之前的值
            select_old = {};
            select_old[$(this).val()] = $(this).find("option:selected").text();
            //console.log(select_old);
            select_old_lan = {};
            var $lan = $(this).parents('tr').find('select.lan');
            select_old_lan = $lan.val();
        });

        $(document).on('select2-open', 'select.lan', function() {
            //存储select修改之前的值
            select_old_lan = $(this).val();
        });

        //子景点改变
        $(document).on('change', 'select.poi', function() {
            var op_val = $(this).val();
            if (op_val) { //禁止其它选项再选当前选项
                $("select.poi").not(this).find('option[value=' + op_val + ']').remove();
                //给数值name重新赋值
                $(this).parents('tr').find('.spinner_num').attr('name', 'base_items[' + op_val + ']');
                $(this).parents('tr').find('.sale_price').html($(this).find("option:selected").attr('sale_price'));
            } else {
                $(this).parents('tr').find('.sale_price').html('0.00');
            }

            if (typeof select_old != 'undefind') {
                for (i in select_old) {
                    if (typeof i !== 'undefind' && i !== '' && op_val !== i) {
                        $("select.poi").not(this).each(function() {
                            if ($(this).parents('tr').find('select.lan').val() != select_old_lan) {
                                return true;
                            }
                            var len = $(this).find('option').length - 1;
                            $(this).find('option').each(function(index) {
                                if ($(this).attr('value') != '' && $(this).attr('value') > i) {
                                    $(this).before('<option value="' + i + '">' + select_old[i] + '</option>');
                                    return false;
                                }
                                if (len === index) {
                                    $(this).parents("select.poi").append('<option value="' + i + '">' + select_old[i] + '</option>');
                                }
                            });
                        });
                    }
                }
            }
            return false;
        });
    });
</script>