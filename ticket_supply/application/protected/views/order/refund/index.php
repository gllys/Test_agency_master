<?php
$this->breadcrumbs = array('订单', '退款管理');
?>
<div class="contentpanel">
    <style>
        .table tr>*{
            text-align:center
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">退款管理</h4>
        </div>
        <table class="table table-bordered mb30">
            <thead>
                <tr>
                    <th>退款申请单号</th>
                    <th>申请时间</th>
                    <th>来源</th>
                    <th>操作员</th>
                    <th>处理进度</th>
                </tr>
            </thead>
            <tbody>
               <?php
                foreach ($list as $key=>$item):
               ?> 
                <tr >
                    <td data-target=".bs-example-modal-static"  onclick="point('<?php echo $item['order_id'];?>','<?php echo $item['id'];?>')"data-toggle="modal"><a href="#"><?php echo $key ?></a></td>
                    <td><?php echo date('Y-m-d',$item['created_at']);?></td>
                    <td>
                        <?php
                        //todo optimize
                        $orginfo = Organizations::api()->show(array('id'=>$item['distributor_id']));
                        if(isset($orginfo['body']['name'])){
                           echo  $orginfo['body']['name'];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        //todo optimize
                        $userinfo = Users::model()->find('id=:id',array(":id"=>$item['op_id']));
                        echo $userinfo['account'];
                        ?>
                    </td>
                <?php if($item['allow_status'] == 0):?>
                    <td class="text-warning">未审核</td>
                <?php elseif($item['allow_status'] == 1):?>
                	<td class="text-success">已审核</td>
                <?php elseif($item['allow_status'] == 2):?>
                	<td class="text-primary">未操作</td>
                <?php else:?>
                	<td class="text-danger">驳回</td>
                <?php endif;?>
                </tr>
               <?php  endforeach;?> 
              
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

    </div>
</div><!-- contentpanel -->
<div class="modal fade bs-example-modal-static" id="verify-modal-point" tabindex="-1" role="dialog"></div>

<script>
function point(pointid,id){
         $('#verify-modal-point').html('');
        $.get('/order/refund/point/?id='+id+'&order_id='+pointid, function(data) {
            $('#verify-modal-point').html(data);
        });
}
</script>
<script>
    jQuery(document).ready(function() {

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
        jQuery('.datepicker').datepicker({showOtherMonths: true, selectOtherMonths: true});
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
        jQuery("#select-basic, #select-multi").select2();
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
