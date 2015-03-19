<?php
$this->breadcrumbs = array('分销商', '查找分销商');
?>

<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">查找分销商</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-1 control-label" style="width:45px">分销商:</label>
                            <div class="col-sm-10">
                                <select data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="distributor-select">
                                    <option value="" select='selected'>请输入分销商名称</option>
                                    <?php  if(isset($lists) && !empty($lists)): foreach($lists as $key=>$model):?>
                                    	<option value="<?php echo $model['id'];?>"  ><?php echo $model['name'];?></option>
                                    <?php endforeach; endif;?>
                                </select>
                            </div>
                        </div><!-- form-group -->


                        <style>
                            #selected-distributor td i{
                                display:none
                            }
                            #selected-distributor td a:hover i{
                                display:inline-block;
                                text-align:center;
                            } 
                            #selected-distributor th{
                                text-align:center;
                            }                          
                        </style>

                        <div class="table-responsive" style="margin:10px;width:600px;text-align:center">
                            <table class="table table-bordered" id="selected-distributor">
                                <tbody>
                                    <tr>
                                        <th>名称</th>
                                        <th>地区</th>
                                        <th>操作</th>
                                    </tr>
                                    <tr id="empty_tr">
                                        <td colspan="3" style="text-align:center">未选择分销商</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                    </div><!-- panel-body --> 

                    <div class="panel-footer" style="text-align:center">
                    </div>
                </form>

            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->


<script>
    jQuery(document).ready(function() {
        $('#distributor-select').change(function() {
            $('#empty_tr').hide();
            var name = $("option:selected", this).text();
            var id =  $("option:selected", this).val();
            $.post('/agency/manager/checkcredit',{'id' : id,'name' : name},function(data){
            	var city_name = data.msg.city.name;
            	if(data.error==0){
            		$('#selected-distributor tbody').append('<tr><td>' + name + '</td><td>'+ city_name +'</td><td><p>已添加</p></td></tr>')            		
            	}else{
            		$('#selected-distributor tbody').append('<tr><td>' + name + '</td><td>'+ city_name +'</td><td><a href="javascript:;" data-id="'+id+'"  class="addcredit btn btn-primary btn-xs" >添加</a></td></tr>');
            		    $('.addcredit').click(function(){
        					add($(this).attr('data-id'));	
        			});
            	}
            },'json');
            $("option:selected", this).remove();
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

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 1});
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
        jQuery("#distributor-select, #select-multi, #through-tickets-select").select2();
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

        function add(id){
        	$.post('/agency/manager/addcredit',{'id':id},function(data){
        		if(data.error==0){
                    alert("添加成功");
                    setTimeout("location.href='/agency/manager/history'", '1000');
                }else{
                    alert("添加失败,"+data.msg);
                    location.reload();
                }
        	},'json')
        }

    });
</script>