<?php
$this->breadcrumbs = array('订单', '订单管理');

if (!isset($_GET['menu'])) {
	$_GET['menu'] = 'all';
}
?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">

        <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">订单管理</h4>
        </div>
        <!-- panel-body -->
    </div>

    

    <!--导航开始-->
    <ul class="nav nav-tabs">
        <?php
        //公共url参数拼接
       //公共url参数拼接
        $_urlParam = ''; //$_queryName.(empty($get[$_queryName])?'//':'/'.$get[$_queryName].'/');
        /*if(!empty($get['landscape_id'])){
            $_urlParam .= 'landscape_id/'.$get['landscape_id'].'/';
        }
        
        if(!empty($get['distributor_id'])){
            $_urlParam .= 'distributor_id/'.$get['distributor_id'].'/';
        }*/
        
        foreach ($menus as $key => $item) :
            ?>
            <li class="<?php echo $key == $_GET['menu'] ? 'active' : '' ?>">
                <a href="/agency/orders/view/menu/<?php echo $key ?>/<?php echo $_urlParam; ?>"><strong><?php echo $item['title'] ?></strong></a>
            </li>
            <?php
        endforeach;
        ?>
    </ul>
    <!--导航结束-->
    
    <?php 
    /*不同菜单，不同结果
     * _all.php 全部订单
     * _verify.php审核订单
     * _paid.php 支付订单
     * _refund.php 退款订单
     * _bill.php 借款订单
    */
   require(dirname(__FILE__).'/_'.$_GET['menu'].'.php'); 
    ?>
        <div class="panel-footer" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
            <?php if (isset($lists)) { ?>
                <div style="height: 40px;">
                    订单数：
                    <?php
                    echo isset($lists['statics']['order_nums']) ? $lists['statics']['order_nums'] : 0;
                    ?>&nbsp;&nbsp;
                    总人次：
                    <?php
                    $total_nums = intval(isset($lists['statics']['total_nums']) ? $lists['statics']['total_nums'] : "0");
                    $total_refunded_nums = intval(isset($lists['statics']['total_refunded_nums']) ? $lists['statics']['total_refunded_nums'] : "0");
                    echo $total_nums - $total_refunded_nums;
                    ?>&nbsp;&nbsp;
                    使用人次：
                    <?php
                    echo isset($lists['statics']['total_used_nums']) ? $lists['statics']['total_used_nums'] : 0;
                    ?>&nbsp;&nbsp;&nbsp;
                    总金额：
                    <?php
                    $total_amount = intval(isset($lists['statics']['total_amount']) ? $lists['statics']['total_amount'] : "0");
                    $total_refunded = intval(isset($lists['statics']['total_refunded']) ? $lists['statics']['total_refunded'] : "0");
                    echo $total_amount - $total_refunded;
                    ?>
                </div>
            <?php } ?>
            <?php
            if (isset($lists['data'])) {
                $this->widget('common.widgets.pagers.ULinkPager', array(
                    'cssFile' => '',
                    'header' => '',
                    'prevPageLabel' => '上一页',
                    'nextPageLabel' => '下一页',
                    'firstPageLabel' => '',
                    'lastPageLabel' => '',
                    'pages' => $pages,
                    'maxButtonCount' => 5, //分页数量
                ));
            }
            ?>
        </div>

<?php echo '</div>' ; //拆开的DIV class="tab-content mb30"?>

</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function() {
        $('#export').click(function() {
            if ($('#start_date').val() == '')
            {
                $('#start_date').PWShowPrompt('请选择开始日期');
                return false;
            }
            if ($('#end_date').val() == '')
            {
                $('#end_date').PWShowPrompt('请选择结束日期');
                return false;
            }
            $('.is_export').attr('value', '1');
            $('form').submit();
            $('.is_export').attr('value', '0');
        });
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
        jQuery('#datepicker').datepicker();
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
// jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });

        $('[name=landscape_id],[name=distributor_id]').select2();
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
        $('#all-btn').click(function() {
            var obj = $(this).parents('table')
            if ($(this).is(':checked')) {
                obj.find('input').prop('checked', true)
                $(this).text('反选')
            } else {
                obj.find('input').prop('checked', false)
                $(this).text('全选')
            }
        });
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
    });
</script>

