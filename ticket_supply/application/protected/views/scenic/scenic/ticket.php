<link rel="stylesheet" href="/css/validationEngine.jquery.css">
<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">发布单票</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="<?php echo $id;?>" type="hidden" name="scenic_id">
                    <?php if(isset($info['id'])){?>
                         <input value="edit" type="hidden" name="type">
                          <input value="<?php echo $info['id']?>" type="hidden" name="id">
                    <?php   }?>
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">景区名称</label>
                            <div class="col-sm-4" style="padding-top: 5px;">
                                
                            <?php
                                $field['id'] = $id;
                                $field['organization_id'] = Yii::app()->user->org_id;
                                $detail = Landscape::api()->detail($field);
                                $val = ApiModel::getData($detail);
                                echo isset($val['name'])?$val['name']:"";
                            ?>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 票种名称</label>
                            <div class="col-sm-4">
                                <input type="text" class="validate[required,] form-control" name="name" value="<?php echo isset($info['name'])?$info['name']:'';?>">
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">        
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景点</label>
                            <div class="col-sm-4">
                                <div class="panel" style="margin:0">
                                    <?php 
                                    if(isset($list) && !empty($list)):
                                            foreach ($list as $k=>$item):?>
                                               <div class="ckbox ckbox-default mr20 inline-block">
                                                   <input type="checkbox" value="<?php echo $item['id'];?>" id="remember<?php echo $k+1;?>" <?php
                                                   if(isset( $info['view_point'])){
                                                        $view = explode(',', $info['view_point']);
                                                        if(in_array($item['id'], $view,true)){
                                                            echo ' checked ="checked" ';
                                                        }
                                                   }
                                                   ?> name="view_point[]">
                                                    <label for="remember<?php echo $k+1;?>"><?php echo $item['name'];?></label>
                                            </div> 
                                    <?php      endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div><!-- form-group -->
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 销售指导价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="销售指导价" class="validate[custom[number]] form-control" name="sale_price" value="<?php echo isset($info['sale_price'])?$info['sale_price']:'';?>"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 挂牌价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="挂牌价" class="validate[custom[number]] form-control" name="listed_price" value="<?php echo isset($info['listed_price'])?$info['listed_price']:'';?>"/></div>
                            </div>
                        </div>
                        
                       
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 可用时间段</label>
                            
                            <div class="col-sm-4" style="width: 320px">
                                <?php if(isset($info['date_available'])){
                                    list($a,$b) =  explode(',',$info['date_available']);
                                    if($a === '0'){
                                        unset($a);unset($b);
                                        unset($info['date_available']);
                                        $all_available = 0;
                                    }else{
                                        $all_available = 1;
                                    }
                                }?>
                                <div class="input-group" style="width: 140px;float: left">
                                    <input type="text" class="form-control datepicker picker" name="date_available[]" readonly value="<?php echo isset($info['date_available'])?date('Y-m-d',$a):'';?>">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                                <div class="input-group" style="width: 15px;float: left;text-align: center;padding-top: 7px">
                                    ~
                                </div>
                                <div class="input-group" style="width: 140px;float: left">
                                    <input type="text" class="form-control datepicker picker" name="date_available[]" readonly value="<?php echo isset($info['date_available'])?date('Y-m-d',$b):'';?>">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3 inline-block" style="padding-top: 7px">
                                <input name="all_available" id="all_available" type="checkbox" value="1" <?php echo isset($info['date_available'])?'':" checked='checked'";?>/>
                                <label for="all_available">不限</label>
                            </div>
                        </div><!-- form-group -->

                          <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>预定有效期</label>
                            <div class="col-sm-10">
                                预订游玩日期后 <input type="text" class="spinner-day" name="valid" readonly="readonly" value="<?php echo isset($info['valid'])?$info['valid']:'';?>"> 天内有效
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 适用日期</label>
                            <div class="col-sm-10 days-checkbox">
                                <div class="btn-group days-check">
                                    <button class="btn btn-primary btn-xs" type="button">反选</button>
                                    <button class="btn btn-primary btn-xs" type="button">周末</button>
                                    <button class="btn btn-primary btn-xs" type="button">平日</button>
                                </div>
                                <div class="checkbox-group">
                                    <?php if(isset($info['week_time'])){ $week = explode(',',$info['week_time']);}?>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('1', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d1" value="1" name="week_time[]">
                                        <label for="d1">周一</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('2', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d2" value="2" name="week_time[]">
                                        <label for="d2">周二</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('3', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d3" value="3" name="week_time[]">
                                        <label for="d3">周三</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('4', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d4" value="4" name="week_time[]">
                                        <label for="d4">周四</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('5', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d5" value="5" name="week_time[]">
                                        <label for="d5">周五</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('6', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d6" value="6" name="week_time[]">
                                        <label for="d6">周六</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" <?php echo isset($week)?(in_array('0', $week,true)?'checked="checked"':''):'checked="checked"';?> id="d7" value="0" name="week_time[]">
                                        <label for="d7">周日</label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- panel-body --> 
                        
                         <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票说明</label>
                            <div class="col-sm-4">
                                <div class="modal-body">
                                    <textarea  id="myremark" placeholder="请输入您的门票说明..." class="form-control validate[required]"
                                               rows="10" cols="500" name="remark" style="width: 592px; height: 205px;">
                                                   <?php echo isset($info['remark'])?$info['remark']:'';?>
                                    </textarea>
                                </div>
                            </div> 
                         </div>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary mr5" type="button" id="form-button">保存</button>
                            <input type="button" class="btn btn-default"  value="回撤" onclick="javascript:history.go(-1);"/>
                        </div>
                </form>

                
            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script src="/js/jquery.validationEngine-zh-CN.js"></script> 
<script src="/js/jquery.validationEngine.js"></script> 
<script>
    $("#repass-form").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });

    jQuery(document).ready(function() {
        
        $('#all_available').click(function () {
            if ($('#all_available').prop('checked') == false) {
                $('.datepicker').addClass("picker").addClass('validate[required]');
            } else {
                $('.datepicker').val("");
                $('.datepicker').removeClass("picker").removeClass('validate[required]');
            }
        });


        $('#repass-form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });

        $('#form-button').click(function() {
            var obj = $('#repass-form');
            if (obj.validationEngine('validate') == true) {
                $('#form-button').attr('disabled', true);
                $.post('/scenic/scenic/addticket', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-button').attr('disabled', false);
                    } else {
                        location.href = '/scenic/scenic/view/?id=<?php echo $id;?>';
                    }
                }, 'json');
            };
            return false;
        });





        //适用日期
        $('.days-check button').click(function() {
            var i = $(this).index()
            var obj = $(this).parents('.days-checkbox').find('.checkbox-group input')
            if (i == 0) {
                if ($(this).text() == '全部') {
                    obj.prop('checked', true)
                    $(this).text('反选')
                } else {
                    $(this).text('全部')
                    obj.prop('checked', false)
                }
            }

            if (i == 1) {
                obj.prop('checked', false)
                obj.eq(5).prop('checked', true)
                obj.eq(6).prop('checked', true)
            }

            if (i == 2) {
                obj.prop('checked', true)
                obj.eq(5).prop('checked', false)
                obj.eq(6).prop('checked', false)
            }
            return false
        })









        // Tags Input
        jQuery('#tags').tagsInput({width: 'auto'});

        // Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();

        // Spinner
        var spinner = jQuery('#spinner').spinner({'min': 0});
        spinner.spinner('value', 1);

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1, 'max': 99});
        spinnerMin.spinner('value', 1);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        spinnerDay.spinner('value', <?php echo isset($info['valid'])?$info['valid']:1;?>);





        // Form Toggles
        jQuery('.toggle').toggles({on: true});
        
        // Time Picker
        jQuery('#timepicker2').timepicker({showMeridian: false});

        // Date Picker
        jQuery('.datepicker').datepicker({minDate: 0});



        // Input Masks
        jQuery("#date").mask("99/99/9999");
        jQuery("#phone").mask("(999) 999-9999");
        jQuery("#ssn").mask("999-99-9999");

        // Select2
        jQuery("#distributor-select, #select-multi").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        function format(item) {
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel')) + ' mr10"></i>' + item.text;
        }

        // This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });
    });



</script>
