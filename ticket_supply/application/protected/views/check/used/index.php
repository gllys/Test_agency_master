<?php
$this->breadcrumbs = array('核销', '核销');
?>
<div class="contentpanel">
    <form class="form-inline" id="_search">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">打印小票</h4>
            </div>
            <div class="panel-body">
                <div class="form-group" style="margin:0">
                	<div class="rdio rdio-default">                    
                		<input type="radio" name="xiaopiao" id="radioDefault" value="1" <?php if (isset($_COOKIE['xiaopiao']) && $_COOKIE['xiaopiao'] == 1): ?>checked="checked"<?php endif ?>/>
                		<label for="radioDefault">是</label>
                	</div>
                    <div class="rdio rdio-default">
                    	<input type="radio" name="xiaopiao" value="0" id="radioDefault1">
                    	<label for="radioDefault1">否</label>
                    </div>
                </div>
            </div>
        </div><!-- panel-body -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">选择景区</h4>
            </div>
            <div class="panel-body">
                <div class="form-group" style="margin:0">
                    <select class="select2" name="landscape_id" style="width:200px;padding:0 10px;">
                        <option value="">请选择</option>
                        <?php
                        foreach ($landscapes as $item): #如果是供应该商景区核销，则显示一个景区
                            if ($_lanId = Yii::app()->user->lan_id):
                                if ($_lanId != $item['id']) {
                                    continue;
                                }
                                ?>
                                <option selected="selected" value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                            <?php else: ?>
                                <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                            <?php endif ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div><!-- panel-body -->

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
                <button class="btn btn-primary submit"  style="height:30px; line-height: 30px; padding: 0px 15px;">查询</button>
            </div><!-- panel-body -->
        </div>
    </form>



    <style>
        .table tr>*{
            text-align:center;
            font-size:16px;
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
                    ?>
                    <tr>
                        <th>订单号：<?php echo $item['order_id'] ?></th>
                        <th style=" text-align: left;">票名称：<?php
	                        //todo optimize
                            $_rs = Order::api()->detail(array('id' => $item['order_id']));
                            if (ApiModel::isSucc($_rs)) { #得到景点
                                $detail = ApiModel::getData($_rs);
                                echo $detail['order_items'][0]['name'];
                                $viewPoints = $detail['order_items'][0]['view_point'];
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
                        <th>可用门票张数：<span id="can_used"><?php echo $item['nums']; ?></span>张</th>
                        <th>已选择： <input type="text" style="padding:0px;" name="datas[<?php echo $item['order_id'] ?>]" id="min_spinner-<?php echo $item['order_id'] ?>" value="0" /> 张</th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="panel-body">
                <button class="btn btn-primary" id='used' type="submit">使用门票</button>
            </div>
        </form>
        <?php
    else:
        ?>
        <?php echo $error ?>
    <?php endif ?>
</div><!-- contentpanel -->

<div id="print" style="display:none;">
    <div style="width:48mm; padding-bottom:20px;font-size:12px; position:relative;">
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">景区名称:</span><span style="display:inline-block;*display:inline;zoom:1; width:30mm;vertical-align:middle;">{lan_name}</span>
        </div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">产品名称:</span><span style="display:inline-block;*display:inline;zoom:1; width:30mm; vertical-align:middle;">{ticket_name}</span>
        </div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">订单号:</span><span>{order_id}</span></div>
        <div><span style="display:inline-block;*display:inline;zoom:1; width:15mm;">验证张数:</span><span>{num}张</span></div>
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
        		alert('核销票数不得大于可用票数');
        		return false;
        	}else{
        		$.post('/check/used/used', $('#used_form').serialize(), function(data) {
	                if (data.error === 0) {
	                    if ($.cookie('xiaopiao') && $.cookie('xiaopiao') != 0) {
	                        printLodop(data.params);
	                        setTimeout(function() {
	                            printLodop(data.params);
	                            alert('验证成功');
	                            top.location.reload();
	                        }, 1000);
	                    } else {
	                        alert('验证成功');
	                        top.location.reload();
	                    }
	                } else {
	                    alert(data.msg);
	                }
	            }, 'json');
        	}   	
            return false;
        });

        function printLodop(params) {
            var $content = '<div><img style="display:block; margin:10mm auto; margin-bottom:5mm;" src="/img/xiaopiao_logo.png" /></div>';
            var $templet = $('#print').html();
            for (i in params) {
                var _content = $templet;
                var _param = params[i];
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
    ?>
            jQuery('#min_spinner-<?php echo $item['order_id'] ?>').spinner({'min': 0, 'max':<?php echo $item['nums'] ?>});
<?php endforeach; ?>

        //实时更新增加减少验票表单数据
        $('.num').blur(function(){
<?php
foreach ($lists as $item):
    ?>
                if (parseInt($('#min_spinner-<?php echo $item['order_id'] ?>').val()) > <?php echo $item['nums'] ?>) {
                    $('#min_spinner-<?php echo $item['order_id'] ?>').val(<?php echo $item['nums'] ?>);
                }

                if (parseInt($('#min_spinner-<?php echo $item['order_id'] ?>').val()) < 0) {
                    $('#min_spinner-<?php echo $item['order_id'] ?>').val(0);
                }
<?php endforeach; ?>
        });

//小票设置
        $('[name=xiaopiao]').click(function() {
            $.cookie('xiaopiao', $(this).val(), {expires: 365});
        });

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
