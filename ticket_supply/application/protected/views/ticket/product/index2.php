<section>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        发布任务单2
                    </h4>
                </div><!-- panel-heading -->
                    <div class="panel-body nopadding">
                        <form class="form-horizontal form-bordered" id="repass-form">
                        <h2 style="margin-left: 30px;">步骤2，完善产品信息，必填
                        </h2>
                        <h5 style="margin-left: 30px;">已选择的票种</h5>
                        <table class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th>景区名称</th>
                                    <th>票种名称</th>
                                    <th>包含景点</th>
                                    <th>销售指导价/挂牌价</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php if(isset($info)):
                                  foreach ($info as $scenic_id=>$item):
                                  foreach($item as $model):?>
                                         
                                <tr>
                                    <td>
                                        <?php
                                        $field['id'] = $scenic_id;
                                        $detail = Landscape::api()->detail($field);
                                        $laninfo = ApiModel::getData($detail);
                                        echo isset($laninfo['name'])?$laninfo['name']:'';
                                        ?>
                                    <td><?php echo $model['name']?>
                                    <input type="hidden" name="ticket_id[<?php echo $scenic_id;?>][]" value="<?php echo $model['id'];?>">
                                    </td>
                                    <td>
                                   <?php
                                    $result = Poi::api()->lists(array("ids" => $model['view_point']));
                                    $poiInfo = ApiModel::getLists($result);
                                    foreach ($poiInfo as $value) {  
                                            echo $value['name'];
                                            if ( next($poiInfo) !== false){ echo '、';}
                                    }
                                    ?>
                                    </td>
                                    <td><?php echo $model['sale_price'].'/'.$model['sale_price'];?></td>
                                </tr>
                                <?php     endforeach; endforeach; endif;?>
                            </tbody>
                        </table>
                         <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 门票名称</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="name">
                            </div>
                        </div><!-- form-group -->
                        
                        <div class="form-group">
                           <label class="col-sm-1 control-label"><input name="is_fit" type="checkbox" value="1"  style="position: relative; top: 2px;" />&nbsp; 散客价</label>
                             <div class="col-sm-10">
                                <div class="col-sm-3" style="margin-left: 20px"><input type="text" placeholder="散客价" class="form-control validate[custom[number]]" name="fat_price" /></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                        	<label class="col-sm-1 control-label"><input name="is_full" type="checkbox" value="1"  style="position: relative; top: 2px;" />&nbsp; 团购价</label>
                        	<div class="col-sm-10">
                        	<div class="col-sm-3" style="margin-left: 20px"><input type="text" placeholder="团购价" class="form-control validate[custom[number]]" name="group_price" /></div>
                                <div class="col-sm-3">
                                    最少订票 <input type="text" id="spinner-min" name="mini_buy"> 张
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 是否允许退票</label>
                            <div class="col-sm-4">
                                <div class="rdio rdio-default inline-block">
                                    <input type="radio" checked="checked" value="1" id="radioDefault5" name="refund">
                                    <label for="radioDefault5">是</label>
                                </div>
                                <div class="rdio rdio-default inline-block">
                                    <input type="radio" checked="checked" value="0" id="radioDefault6" name="refund">
                                    <label for="radioDefault6">否</label>
                                </div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-1 control-label">
                                <span class="text-danger">*</span>预定时间</label>
                            <div class="col-sm-10">
                                需在入园前 <input type="text" class="spinner-day" name="scheduled"> 天的
                                <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker2" type="text" class="form-control" style="width:150px" name="scheduledtime"></div>
                                以前购买
                            </div>
                        </div><!-- form-group -->
                         <div class="form-group">
                            <label class="col-sm-1 control-label"><span class="text-danger">*</span> 可售时间段</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[1]" readonly> ~
                                <input type="text" placeholder="" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[2]" readonly>
                      
                            </div>
                        </div><!-- form-group -->
                        <div class="panel-footer">
                            <button class="btn btn-primary mr5" id="form-button">保存,下一步</button>
                            <button class="btn btn-default" type="reset">返回</button>
                        </div>
                </form>
            </div><!-- panel -->
        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->

</section>


<script>
    jQuery(document).ready(function() {
        
     $('#repass-form').validationEngine({
        autoHidePrompt: false,
        scroll: false,
        autoHideDelay: 3000,
        maxErrorsPerField: 1
    });
        
         //如果选择了团散客
        window.setInterval(function(){
            if($('[name=is_fit]').prop('checked')){
               $('input[name="fat_price"]').removeAttr("readonly").addClass('validate[required]'); 
            }else{
               $('input[name="fat_price"]').val("");
               $('input[name="fat_price"]').attr("readonly","readonly").removeClass('validate[required]'); 
            }
            
            if($('[name=is_full]').prop('checked')){
               $('input[name="group_price"]').removeAttr("readonly").addClass('validate[required]'); 
            }else{
               $('input[name="group_price"]').val("");
               $('input[name="group_price"]').attr("readonly","readonly").removeClass('validate[required]'); 
            }
        },200);
        
        
        $('#form-button').click(function() {
	    $('#form-button').attr('disabled', 'disabled');
        var obj = $('#repass-form');
         if (!$('[name=is_fit]').prop('checked')&& !$('[name=is_full]').prop('checked')) {
                alert('团队价和散客价至少选一个');
	        $('#form-button').removeAttr('disabled');
	         return false;
            }
        if (obj.validationEngine('validate') == true) {
            $.post('/ticket/product/Newticket', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
	                    $('#form-button').removeAttr('disabled');
                        return false;
                    } else {
                      location.href = '/ticket/product/index3?ticket_template_id='+data.params;
                    }
                }, 'json');
        } else {
	        $('#form-button').removeAttr('disabled');
        }
        return false;
    });
     
        
        
        
        
        
        
        
        
        

        // HTML5 WYSIWYG Editor
        $('#wysiwyg').wysihtml5({color: true, html: true})

        !function() {
            var sd = $('#selected-distributor'),
                    aab = $('#area-add-btn')

            function b(obj, p, val) {
                var i, a, s
                sd.find('th').each(function() {
                    if ($(this).text() == val) {
                        i = $(this).parent().index()
                    }
                })

                sd.find('.panel-heading').each(function() {
                    if ($(this).text() == p) {
                        a = $(this).parent('.panel')
                    }
                })


                if (i != undefined) {
                    return false
                }

                if (a) {
                    a.find('tbody').append('<tr><th width="100">' + val + '</th><td><a href="" class="btn btn-xs mr5">武义三峰 <i class="fa fa-times"></i></a></td></tr>')
                } else {
                    sd.find('> .panel-body').append('<div class="panel panel-info"><div class="panel-heading">' + p + '</div><div class="panel-body"><div class="table-responsive mb10"><table class="table table-bordered"><tbody><tr><th width="100">' + val + '</th><td><a href="" class="btn btn-xs mr5">武义三峰 <i class="fa fa-times"></i></a></td></tr></tbody></table></div></div></div>')
                }
            }


            aab.click(function() {
                var obj = $('#area-select'),
                        val = obj.val(),
                        p = obj.find(':selected').attr('data-p')
                b(obj, p, val)
            })

            $('#distributor-select').change(function() {
                var obj = $(this),
                        val = obj.val(),
                        p = obj.find(':selected').attr('data-p'),
                        t = obj.find(':selected').text(),
                        i,
                        d
                sd.find('td a').each(function() {
                    if ($(this).text() == t) {
                        d = true
                    }
                })

                b(obj, p, val)

                sd.find('th').each(function() {
                    if ($(this).text() == val) {
                        i = $(this)
                    }
                })
                if (d == undefined) {
                    i.next('td').append('<a href="" class="btn btn-xs mr5">' + t + ' <i class="fa fa-times"></i></a>')
                }
            })
        }()




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
        var spinner = jQuery('#spinner').spinner({'min': 1});
        spinner.spinner('value', 1);

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1});
        spinnerMin.spinner('value', 1);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        spinnerDay.spinner('value', 1);





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

        // Color Picker
        if (jQuery('#colorpicker').length > 0) {
            jQuery('#colorSelector').ColorPicker({
                onShow: function(colpkr) {
                    jQuery(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function(colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function(hsb, hex, rgb) {
                    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
                    jQuery('#colorpicker').val('#' + hex);
                }
            });
        }

        // Color Picker Flat Mode
        jQuery('#colorpickerholder').ColorPicker({
            flat: true,
            onChange: function(hsb, hex, rgb) {
                jQuery('#colorpicker3').val('#' + hex);
            }
        });


    });
</script>