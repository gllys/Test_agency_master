<div class="pageheader">
    <div class="media">
        <div class="pageicon pull-left">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="media-body">
            <ul class="breadcrumb">
                <li><a href="#"><i class="glyphicon glyphicon-home"></i></a></li>
                <li><a href="#">门票管理</a></li>
                <li><a href="#">发布联票</a></li>
            </ul>
        </div>
    </div><!-- media -->
</div><!-- pageheader -->
<link rel="stylesheet" href="/css/validationEngine.jquery.css">
<div class="contentpanel">
      <?php if(isset($_GET['info']) && $_GET['info']==1){?>
    <div id="show_msg">
         <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button> 
        <i class="fa fa-check fa-lg"></i>保存失败！</div>
    </div>
    <?php  } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">发布联票</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="union" type="hidden" name="type">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 联票名称</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="" class="validate[required] form-control" name="name">
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 包含景区</label>
                            <div class="col-sm-4">
                                <select multiple data-placeholder="请输入景区名称" style="width:300px;padding:0 10px;" id="distributor-select" name="scenic_id[]">
                                            <option value=""  >请输入景区名称</option>                                     
                                        <?php foreach($list as $key=>$model):?>
                                    <option value="<?php echo $model['id'];?>"  ><?php echo $model['name'];?></option>
                                        <?php endforeach;?>
                                </select>
                            </div>
                        </div><!-- form-group -->

                        <?php 
                            $result = Organizations::api()->show(array('id' => Yii::app()->user->org_id));
                            if($result['code'] == 'succ' && !empty($result['body'])){
                                $province_id = $result['body']['province_id'];
                                $city_id = $result['body']['city_id'];
                                $district_id = $result['body']['district_id'];
                            }

                        ?>
                        <input type="hidden" name="province_id" value="<?php echo $province_id?>">
                        <input type="hidden" name="city_id" value="<?php echo $city_id?>">
                        <input type="hidden" name="district_id" value="<?php echo $district_id?>">

                        <div class="form-group" style="border:0">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <div class="panel includes-scenic" style="margin:0">
                                    <style>
                                        .includes-scenic th{
                                            text-align:center
                                        }
                                        .includes-scenic th a{
                                            position:relative;
                                            padding-left:20px;
                                        }
                                        .includes-scenic th a i{
                                            display:none;
                                            position:absolute;
                                            left:0;
                                            top:3px;
                                        }
                                        .includes-scenic th a:hover i{
                                            display:inline-block
                                        }
                                    </style>
                                    <table class="table table-striped" id="appendto"></table>
                                </div>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><input name="is_fit" type="checkbox" value="1" style=" position: relative;top: 2px;" />&nbsp;散客价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="散客价" readonly class=" form-control validate[custom[number]]" name="fat_price"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><input name="is_full" type="checkbox" value="1"  style="position: relative; top: 2px;" />&nbsp;团队价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="团队价" readonly class=" form-control validate[custom[number]]" name="group_price"/></div>
                                <div class="col-sm-3">
                                    最少订票 <input type="text" id="spinner-min" name="mini_buy"> 张
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 销售价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="销售价" class="validate[custom[number]] form-control" name="sale_price"/></div>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 挂牌价</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3"><input type="text" placeholder="挂牌价" class="validate[custom[number]] form-control" name="listed_price"/></div>
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">是否允许退票</label>
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
                                <label class="col-sm-2 control-label"><span class="text-danger">*</span> 门票说明</label>

                                <div class="col-sm-4">
                                    <button class="btn btn-success btn-xs" type="button" data-target=".bs-example-modal-lg"
                                            data-toggle="modal">点击编辑
                                    </button>
                                    <input type="hidden" name="remark"/>
                                </div>
                         </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 预定时间</label>
                            <div class="col-sm-10">
                                需在入园前 <input type="text" class="spinner-day" name="scheduled" readonly="readonly"> 天的
                                <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker2" type="text" class="form-control" style="width:50px" name="scheduledtime"></div>
                                以前购买
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 可用时间段</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[1]" readonly="readonly"> ~
                                <input type="text" placeholder="" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[2]" readonly="readonly">
                               预订游玩日期后 <input type="text" class="spinner-day" name="valid" readonly="readonly"> 天有效
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
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d1" value="1" name="week_time[]">
                                        <label for="d1">周一</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d2" value="2" name="week_time[]">
                                        <label for="d2">周二</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d3" value="3" name="week_time[]">
                                        <label for="d3">周三</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d4" value="4" name="week_time[]">
                                        <label for="d4">周四</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d5" value="5" name="week_time[]">
                                        <label for="d5">周五</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d6" value="6" name="week_time[]">
                                        <label for="d6">周六</label>
                                    </div>
                                    <div class="ckbox ckbox-primary mr10 inline-block">
                                        <input type="checkbox" checked="checked" id="d7" value="0" name="week_time[]">
                                        <label for="d7">周日</label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- form-group -->
                    </div><!-- panel-body --> 

                    <div class="panel-footer">
                        <button class="btn btn-primary mr5" type="buttom" id="form-button">保存</button>
                        <button class="btn btn-default" type="button" id="btnreset">回撤</button>
                    </div>
                </form>

                 <div role="dialog" tabindex="-1" class="modal fade bs-example-modal-lg">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close-remark">×</button>
                                    <h4 class="modal-title">门票说明</h4>
                                </div>
                                <div class="modal-body">
                                    <textarea  id="myremark" placeholder="请输入您的门票说明..." class="form-control validate[required]"
                                               rows="10"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" id="remark-data">保存</button>
                                </div>
                            </div>
                        </div>
                    </div>
        </div> <!---panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->


<script>
$("#repass-form").keypress(function(e) {
  if (e.which == 13) {
    return false;
  }
});

    
jQuery(document).ready(function() {
        $('#btnreset').click(function(){
            location.href = '/ticket/goods/';
        });
        
        $('#distributor-select').change(function(){
             var names =  $("#distributor-select").val();
             if(names == null){
                 $('#appendto').html('');
             }else{
                 if(names.length >= $('.landscape-box').length){//如果增加景区
                     for(var i=0;i<names.length;i++){
                        var id = names[i];
                        if($('.landscape-box-'+id).length<=0){
                            $.post('/ticket/goods/union',{ids:id},function(data){
                               $('#appendto').append(data);
                            },'json');
                        }
                    }
                 }else{//减少景区
                     var arr = [];
                     for(var i=0;i<names.length;i++){//讲对象转化为数组
                        arr.push(names[i]);
                     }
                     $('.landscape-box').each(function(){
                         var obj = $(this);
                         if($.inArray(obj.attr('box-by-id'),arr)==-1){
                             obj.remove();
                         }
                     });
                 }
             }
        });
        

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
                //$('#show_msg').empty();
                var obj = $('#repass-form');
                if (!$('[name=is_fit]').prop('checked')&& !$('[name=is_full]').prop('checked')) {
                    alert('团队价和散客价至少选一个');
                    return false;
                }
                //联票必须选择一个子景点开始
                var $pidObjs = $('.landscape-box') ;
                for(i=0;i<$pidObjs.length;i++){
                    var $viewPoints = $pidObjs.eq(i).find('input:checked');
                   // console.log($viewPoints);
                    if($viewPoints.length<1){
                        alert($pidObjs.eq(i).find('a').html()+'景区至少选择一个景点');
                        return false;
                    }
                }
                
                if (obj.validationEngine('validate') == true) {
                    $('#form-button').attr('disabled', true);
                    $.post('/ticket/goods/add', obj.serialize(), function(data) {
                            if (data.error) {
                                alert(data.msg);
                                 $('#form-button').attr('disabled', false);
                            } else {
                              location.href = '/ticket/depot/index/type/2';
                            }
                        }, 'json');
                };

                return false;
            });

   
    
        $('#remark-data').click(function() {
            $('input[name=remark]').val($('#myremark').val());
            $('#close-remark').trigger('click');

        })  

        




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

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1,'max':99});
        spinnerMin.spinner('value', 1);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
        spinnerDay.spinner('value', 0);

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
