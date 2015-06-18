<?php
$this->breadcrumbs = array('验票', '验票');
?>
<div class="contentpanel">
    <form action="/check/used/" method="get" class="form-inline" id="_search">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">打印小票</h4>
                <div class="inline-block" style="float:right; margin-top: -25px;">
                    <a class="btn btn-primary btn-xs clearPart" onclick="setPrinter();return false;">
                        <i class="fa fa-print"></i>
                        打印设置
                    </a>
                </div>
                <div class="inline-block" style="float:right; margin-top: -25px; margin-right: 10px;">
                <a class="btn btn-primary btn-xs clearPart"  href="/check/check/setsimpleticket/" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">
                   小票设置
                </a>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group" style="margin:0">
                    <div class="rdio rdio-default">                    
                        <input type="radio" name="xiaopiao" id="radioDefault" value="1" <?php if (isset($_COOKIE['xiaopiao']) && $_COOKIE['xiaopiao'] == 1): ?>checked="checked"<?php endif ?>/>
                        <label for="radioDefault">是</label>
                    </div>
                    <div class="rdio rdio-default">
                        <input type="radio" name="xiaopiao" value="0" <?php if (!isset($_COOKIE['xiaopiao']) || $_COOKIE['xiaopiao'] == 0): ?>checked="checked"<?php endif ?> id="radioDefault1">
                        <label for="radioDefault1">否</label>
                    </div>
                </div>
            </div>
        </div><!-- panel-body -->

        <div class="panel panel-default" style="display:none;">
            <div class="panel-heading">
                <h4 class="panel-title">选择景区</h4>
            </div>
            <div class="panel-body">
                <div class="form-group" style="margin:0">
                    <select class="select2" name="landscape_id" style="width:200px;padding:0 10px;">
                        <option value="<?php echo Yii::app()->user->lan_id ?>" selected="selected"><?php 
                        $param = array();
                        $param['id'] = Yii::app()->user->lan_id;
                        $param['fields'] = 'name' ; 
                        $rs = Landscape::api()->detail($param);
                        $data = ApiModel::getData($rs);
                        echo $data['name'];
                        ?></option>
                    </select>
                </div>
            </div>
        </div>
        <!-- panel-body -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">检票</h4>
            </div>
            <div class="panel-body">

                <div class="form-group" style="margin:0">
                    <select class="select2" name="type" style="width:150px;padding:0 10px;">
                        <option value="3">订单号</option>
                        <option value="2">手机号</option>
                        <option value="1">身份证</option>
                    </select>
                </div>

                <div class="form-group" style="margin: 0 5px 0 0">
                    <input class="form-control" name='id' value="<?php if (isset($_GET['id'])) echo $_GET['id'] ?>" placeholder="" type="text" style="width:400px;height:30px;line-height: 15px; font-size: 14px;">
                </div>
                <button class="btn btn-primary submit" type="submit"  style="height:30px; line-height: 30px; padding: 0px 15px;">查询</button>
            </div><!-- panel-body -->
        </div>
    </form>



    <style>
        .table tr>*{
            text-align:center;
            font-size:14px;
        }

    </style>
    <?php
    //$lists = array(array('order_id' => '166067866221059', 'nums' => 10), array('order_id' => '166069139206673', 'nums' => 2), array('order_id' => '166069188867089', 'nums' => 5));
    if ($lists):
        ?>
        <form id="used_form">
            <input type="hidden" name="landscape_id" value="<?php echo $_GET['landscape_id'] ?>" />
            <table class="table table-bordered table1">
                <?php
                foreach ($lists as $item):
                    $_rs = Order::api()->detail(array('id' => $item['order_id'],'show_order_items'=>1));
                    $detail = ApiModel::getData($_rs);
                    $tmk1 = '可用门票张(套)数';
                    $tmk2 = '';
                    
                    $ticketSum = 0;
                    foreach((array)$detail['ticket_infos'] as $t){
                        if($_GET['landscape_id']==$t['scenic_id']){
                        $ticketSum = $ticketSum + $t['num'] ;
                        }
                    }
                    if($ticketSum){
                        $item['nums'] = intval($item['nums']/$ticketSum);
                    }
                    ?>
                    <tr>
                    <input type="hidden" name="ticketSum[<?php echo $item['order_id'] ?>]" value="<?php echo $ticketSum ?>" />
                        <th style="width: 175px;">订单号：<?php echo $item['order_id'] ?></th>
                        <th style="width:100px;">取票人：<?php echo $detail['owner_name'] ?></th>
                        <th>手机号：<?php echo $detail['owner_mobile'] ?></th>
                        <th style=" text-align: left;">门票名称：<?php
	                        //todo optimize
                            if (ApiModel::isSucc($_rs)) { #得到景点
                                $detail = ApiModel::getData($_rs);
                                $orderItem = current($detail['order_items']) ;
                                echo $orderItem['name'];
                                $viewPoints = $orderItem['view_point'];
                                $param['ids'] = $viewPoints;
                                $param['items'] = 1000;
                                $_rs = Poi::api()->lists($param);
                                $outs = array();
                                if (ApiModel::isSucc($_rs)) {#得到子景点
                                    $datas = ApiModel::getLists($_rs);
                                    foreach ($datas as $val) {
                                        $outs[] = $val['name'];
                                    }
                                }
                                echo '<br/><font style="font-size:12px;font-weight:400;">可用景点:' . join(',', $outs) . '</font>';
                            }
                            ?></th>
                        <th style="width:40px;">内含:</th>
                        <th style="width:200px;"><?php
                        foreach((array)$detail['ticket_infos'] as $_v){
                            $_model = TicketType::model()->findByPk($_v['type']);
                          echo    ''.(strpos($_v['base_name'],'-')?substr($_v['base_name'],strpos($_v['base_name'],'-')):$_v['base_name']).$_model['name'].'*'.$_v['num'].'张<br/>';
                        }
                        ?></th>
                        <th><?php echo $tmk1; ?>：<span id="can_used"><?php echo $item['nums']; ?></span><?php echo $tmk2; ?></th>
                        <th>已选择： <input type="text"  name="datas[<?php echo $item['order_id'] ?>]" id="min_spinner-<?php echo $item['order_id'] ?>" value="0" onafterpaste="this.value=this.value.replace(/\D/g,'')" onkeyup="this.value=this.value.replace(/\D/g,'')"/> <?php echo $tmk2; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="panel-body">
                <button class="btn btn-primary clearPart" id='used' type="submit">使用门票</button>
            </div>
        </form>
        <?php
    else:
        ?>
        <?php echo $error ?>
    <?php endif ?>
</div><!-- contentpanel -->

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>

<div id="print" style="display:none;">
    <div style="width:48mm; padding-bottom:20px;font-size:12px; position:relative;">
        <div style="font-size: 14px;top:-5mm;">{print_type}</div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">景区名称:</span><span style="display:inline-block;*display:inline;zoom:1; width:30mm;vertical-align:middle;">{lan_name}</span>
        </div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">产品名称:</span><span style="display:inline-block;*display:inline;zoom:1; width:30mm; vertical-align:middle;">{ticket_name}</span>
        </div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">订单号:</span><span>{order_id}</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">验证套数:</span><span>{num}套</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">验证时间:</span><span>{date}</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">取票人:</span><span>{owner_name}</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">电话:</span><span>{owner_mobile}</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">验证结果:</span><span>验证成功</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">操作员:</span><span>{op_name}</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">备注:</span><span></span></div>
    </div>
</div>
<script language="javascript" src="/js/jquery.cookies.js"></script>
<script language="javascript" src="/js/lodop/LodopFuncs.js?v=1"></script>
<object classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" codebase="/js/lodop/lodop.cab#version=6,1,8,7" width=0 height=0></object>
<script>
    function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }
    
    jQuery(document).ready(function() {
        //选中
        $('[name=landscape_id]').val(<?php echo isset($_GET['landscape_id']) ? $_GET['landscape_id'] : '' ?>);
        $('[name=type]').val(<?php echo isset($_GET['type']) ? $_GET['type'] : 3 ?>);

        //查询
        $('.submit').click(function() {
            if ($('[name=landscape_id]').val() === '') {
                alert('请选择景区');
                return false;
            }

            if ($('[name=id]').val() === '') {
                alert('查询号码不能为空');
                $('[name=id]').focus();
                return false;
            }
            $('#_search').submit();
        });

        //得到子景点
//        $('[name=landscape_id]').change(function() {
//            $('#_search').submit();
//        });
        //使用门票
        $('#used').click(function() {
        	var can_used = $('#can_used').text();
        	var used_num = $('table tr th').eq(3).find('input').val();
        	if(Number(used_num) > Number(can_used)){
        		alert('验票票数不得大于可用票数');
        		return false;
        	}else{
        		$.post('/check/used/used', $('#used_form').serialize(), function(data) {
	                if (data.error === 0) {
	                    if ($.cookie('xiaopiao') && $.cookie('xiaopiao') != 0) {
                                if(<?php echo Users::model()->findByPk(Yii::app()->user->uid)->print_type ?>==1){
                                    printLodop(data.params,'正联');
                                    setTimeout(function() {
                                        printLodop(data.params,'副联');
                                        alert('验证成功',function(){
                                            location.partReload();
                                        });
                                    }, 1000);
                                }else{
                                    printLodop(data.params,'正联');
                                    alert('验证成功',function(){
                                        location.partReload();
                                    });
                                }
	                    } else {
	                        alert('验证成功',function(){
                                    location.partReload();
                                });
	                    }
	                } else {
	                    alert(data.msg);
	                }
	            }, 'json');
        	}   	
            return false;
        });

        function printLodop(params,print_type) {
            var $content = '<div><img style="display:block; margin:10mm auto; margin-bottom:5mm;" src="/img/xiaopiao_logo.png" /></div>';
            var $templet = $('#print').html();
            for (i in params) {
                var _content = $templet;
                var _param = params[i];
                _param['print_type'] = print_type ;
                _param['lan_name'] = $('[name=landscape_id] option:selected').text();
                for (j in _param) {
                    _content = _content.replace('{' + j + '}', _param[j]);
                }
                $content += _content;
            }
            LODOP = getLodop();
            LODOP.PRINT_INIT("打印任务名");               //首先一个初始化语句
            LODOP.SET_PRINTER_INDEXA(getPrinter());
            LODOP.SET_PRINT_PAGESIZE(3, '48mm', '5mm', 'sd');
            LODOP.ADD_PRINT_HTM(0, 0, '48mm', '100%', $content);
            LODOP.PRINT();
        }

        function getPrinter() {

            var username = 'print_' + "<?php echo Yii::app()->user->id; ?>";
            var cookValue = $.cookie(username);
            if (cookValue == null || cookValue == "") {
                LODOP = getLodop();
                var cookValue = LODOP.SELECT_PRINTER();
                $.cookie(username, cookValue, {expires: 365});
            }
            return cookValue;
        }

        function setPrinter(){
            var username = 'print_' + "<?php echo Yii::app()->user->id; ?>";
            LODOP = getLodop();
            var cookValue = LODOP.SELECT_PRINTER();
            $.cookie(username, cookValue, {expires: 365});
        }
        
        window.setPrinter = setPrinter ;
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
<?php
foreach ($lists as $item):
					$ticketSum = 0;
                    foreach((array)$detail['ticket_infos'] as $t){
                        if($_GET['landscape_id']==$t['scenic_id']){
                        $ticketSum = $ticketSum + $t['num'] ;
                        }
                    }
                    if($ticketSum){
                        $item['nums'] = intval($item['nums']/$ticketSum);
                    }
    ?>
            jQuery('#min_spinner-<?php echo $item['order_id'] ?>').spinner({'min': 0, 'max':<?php echo $item['nums'] ?>});
<?php endforeach; ?>

        //实时更新增加减少验票表单数据
        $('.num').blur(function(){
<?php
foreach ($lists as $item):
					$ticketSum = 0;
                    foreach((array)$detail['ticket_infos'] as $t){
                        if($_GET['landscape_id']==$t['scenic_id']){
                        $ticketSum = $ticketSum + $t['num'] ;
                        }
                    }
                    if($ticketSum){
                        $item['nums'] = intval($item['nums']/$ticketSum);
                    }
    ?>
                if (parseInt($('#min_spinner-<?php echo $item['order_id'] ?>').val()) > <?php echo $item['nums'] ?>) {
                    $('#min_spinner-<?php echo $item['order_id'] ?>').val(<?php echo $item['nums'] ?>);
                }

                if (parseInt($('#min_spinner-<?php echo $item['order_id'] ?>').val()) < 0) {
                    $('#min_spinner-<?php echo $item['order_id'] ?>').val(0);
                }
<?php endforeach; ?>
        });

        $('.ui-spinner-button').addClass('clearPart');
//小票设置
        $('[name=xiaopiao]').click(function() {
            $.cookie('xiaopiao', $(this).val(), {expires: 365});
        });

        // Form Toggles
        jQuery('.toggle').toggles({on: true});

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
