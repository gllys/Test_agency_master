<?php
$this->breadcrumbs = array('单票管理', '设置单票模板');
?>
<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">分销商价格设置</h4>
                </div><!-- panel-heading -->
                <div class="panel-body nopadding">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">地区:</label>
                        <div class="col-sm-10">
                            <select class="select2" data-placeholder="" style="width:150px;padding:0 10px;" id="area-select">
                                <?php
                                //得到省级市
                                $provinces = Districts::provinceCity();
                                foreach ($provinces as $key => $val) :
                                    ?>
                                    <option value="<?php echo $key ?>"><?php echo $val ?></option>
                                <?php endforeach; ?>

                                <?php
                                //得到地级市
                                $provinces = Districts::model()->findAllByAttributes(array("level" => 2));
                                foreach ($provinces as $model) :
                                    if (in_array($model['name'], array('市辖区', '县'))) {
                                        continue;
                                    }
                                    ?>
                                    <option value="<?php echo $model['id'] ?>"><?php echo $model['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-success btn-xs" id="area-add-btn">添加</button>
                        </div>
                    </div><!-- form-group -->

                    <div class="form-group">

                        <label class="col-sm-2 control-label">分销商:</label>
                        <div class="col-sm-10">
                            <select data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="distributor-select">
                                <?php
                                $cityLists = $this->getAgencysByCityList();
                                foreach ($cityLists as $cityId => $agentylists):
                                    ?>
                                    <option disabled="disabled" id="city_option_<?php echo $cityId ?>" value="<?php echo $cityId ?>"><?php
                                        $_rs = Districts::model()->findByPk($cityId);
                                        echo $_rs['name'] == '中国' ? '其它' : $_rs['name']
                                        ?></option>
                                    <?php
                                    foreach ($agentylists as $agentylist) {
                                        ?>
                                        <option pcity ="<?php echo $cityId ?>" value="<?php echo $agentylist['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <?php echo $agentylist['name']; ?></option>
                                    <?php } ?>
                                    <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div><!-- form-group -->
                    <form id="form" class="form-horizontal form-bordered" action="#" method="POST">
                        <div class="panel panel-warning" style="margin:10px;width:600px">
                            <div class="panel-heading">
                                <!--<button type="button" class="btn btn-success btn-xs" id="area-all-btn">全选</button>-->&nbsp;
                            </div>

                            <div class="panel-body" id="selected-distributor">
                                <style>
                                    #selected-distributor td i{
                                        display:none
                                    }
                                    #selected-distributor td a:hover i{
                                        display:inline-block
                                    }
                                </style>
                                <?php
                                foreach ($lists as $city => $_lists):
                                    ?>

                                    <div class="table-responsive mb<?php echo $city ?>" id="city_<?php echo $city ?>">
                                        <table class="table table-bordered">
                                            <thead>
                                            <td>
                                                <div class="ckbox ckbox-primary">
                                                    <input type="checkbox" class="all-btn" id="all-btn-<?php echo $city ?>" value="1">
                                                    <label for="all-btn-<?php echo $city ?>"><?php echo Districts::model()->findByPk($city)->name ?></label>
                                                </div>
                                            </td>
                                            <td><input type="text" class="form-control i-price" placeholder="散客结算价"></td>
                                            <td><input type="text" class="form-control g-price" placeholder="团队结算价"></td>
                                            <td><button class="btn btn-success btn-xs area-set-btn" type="button">批量设置</button></td>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th></th>
                                                    <th>名称</th>
                                                    <th>散客结算价</th>
                                                    <th>团队结算价</th>
                                                </tr>
                                                <tr>
                                                    <?Php
                                                    foreach ($_lists as $item) {
                                                        ?>
                                                    <tr id="agency_<?php echo $item['agency_id'] ?>">
                                                        <td>
                                                            <div class="ckbox ckbox-primary">
                                                                <input type="checkbox" id="checkbox-<?php echo $city ?>"  value="1">
                                                                <label for="checkbox-<?php echo $city ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td><?php 
                                                        // Organizations::api()->debug = true;
	                                                        //todo optimize
                                                        $_rs = Organizations::api()->show(array('id'=>$item['agency_id']));
                                                        $_data = ApiModel::api()->getData($_rs);
                                                        echo isset($_data['name'])?$_data['name']:'';
                                                        ?></td>
                                                        <td><input type="text" name="price[<?php echo $city ?>][<?php echo $item['agency_id'] ?>][]" value="<?php echo $item['fit_price'] ?>" class="form-control i-price"></td>
                                                        <td><input type="text" name="price[<?php echo $city ?>][<?php echo $item['agency_id'] ?>][]" value="<?php echo $item['full_price'] ?>" class="form-control g-price"></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            </div><!-- panel-body -->
                        </div>
                </div><!-- panel-body --> 

                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary mr5">保存</button>
                    <a class="btn btn-default" href="/ticket/single/" >取消</a>
                </div>
                </form>

            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script src="/js/application.js" type="text/javascript"></script>
<script src="/js/jquery.validationEngine-zh-CN.js"></script>
<script>
    jQuery(document).ready(function() {


        !function() {
            var sd = $('#selected-distributor'),
                    aab = $('#area-add-btn'),
                    allBtn = $('#area-all-btn'),
                    o = 0

            function b(obj, val) {
                var i
                sd.find('thead label').each(function() {
                    if ($(this).text() == val) {
                        i = $(this).parents('.table-responsive').index()
                    }
                })
                $('.none').hide()
                o++
                if (i) {
                    sd.find('.table-responsive').eq(i - 1).find('tbody').append('<tr><td><div class="ckbox ckbox-primary"><input type="checkbox" value="1" id="checkbox-' + o + '"><label for="checkbox-' + o + '"></label></div></td><td>' + obj.find('option:selected').text() + '</td><td><input type="text" class="form-control i-price"></td><td><input type="text" class="form-control g-price"></td></tr>')
                } else {
                    sd.append('<div class="table-responsive mb10"><table class="table table-bordered"><thead><td><div class="ckbox ckbox-primary"><input type="checkbox" class="all-btn" id="all-btn-' + o + '" value="1"><label for="all-btn-' + o + '">' + obj.find('option:selected').text() + '</label></div></td><td><input type="text" class="form-control i-price" placeholder="散客结算价"></td><td><input type="text" class="form-control g-price" placeholder="团队结算价"></td><td><button class="btn btn-success btn-xs area-set-btn" type="button">批量设置</button></td></thead><tbody><tr><th></th><th>名称</th><th>散客结算价</th><th>团队结算价</th></tr><tr></tr><tr><td><div class="ckbox ckbox-primary"><input type="checkbox" id="checkbox-' + o + '" value="1"><label for="checkbox-' + o + '"></label></div></td><td>土楼</td><td><input type="text" class="form-control i-price"></td><td><input type="text" class="form-control g-price"></td></tr></tbody></table></div>')
                }
            }


            //全选
            allBtn.click(function() {
                if ($(this).text() == '全选') {
                    sd.find('input').prop('checked', true)
                    $(this).text('反选')
                } else {
                    sd.find('input').prop('checked', false)
                    $(this).text('全选')
                }
            })

            //地区全选
            $('body').on('click', '.all-btn', function() {
                var obj = $(this).parents('table')
                if ($(this).is(':checked')) {
                    obj.find('input').prop('checked', true)
                } else {
                    obj.find('input').prop('checked', false)
                }
            })

            //批量设置
            $('body').on('click', '.area-set-btn', function() {
                var obj = $(this).parents('table')
                var iPrice = obj.find('thead .i-price').val()
                var gPrice = obj.find('thead .g-price').val()

                obj.find('tbody').find(':checked').parents('tr').find('.i-price').val(iPrice)
                obj.find('tbody').find(':checked').parents('tr').find('.g-price').val(gPrice)

            })



            //城市分销商设置
            aab.click(function() {
                var obj = $('#area-select'), val = obj.val();
                if ($('#city_' + val).length > 0) {
                    $('#city_' + val).css('border', '1px solid #ffff00');
                    $("html,body").delay(500).animate({scrollTop: $('#city_' + val).offset().top}, 300);
                } else {
                    $.get('/ticket/singleTemplate/CityAgencys/id/' + val, function(data) {
                        if (data.error == 0) {
                            var lists = data['msg'];
                            var _html = '<div class="table-responsive  mb' + val + '" id="city_' + val + '"><table class="table table-bordered"><thead><td><div class="ckbox ckbox-primary"><input type="checkbox" class="all-btn" id="all-btn-' + val + '" value="1"><label for="all-btn-' + val + '">' + obj.find('option:selected').text() + '</label></div></td><td><input type="text" class="form-control i-price" placeholder="散客结算价"></td><td><input type="text" class="form-control g-price" placeholder="团队结算价"></td><td><button class="btn btn-success btn-xs area-set-btn" type="button">批量设置</button></td></thead><tbody>';
                            _html += '<tr><th></th><th>名称</th><th>散客结算价</th><th>团队结算价</th></tr><tr></tr>';
                            for (i in lists) {
                                var _list = lists[i];
                                _html += '<tr id="agency_' + _list['id'] + '"><td><div class="ckbox ckbox-primary"><input type="checkbox" id="checkbox-' + val + '" value="1"><label for="checkbox-' + val + '"></label></div></td><td>' + _list['name'] + '</td><td><input type="text" name="price[' + val + '][' + _list['id'] + '][]" class="form-control i-price"></td><td><input type="text" name="price[' + val + '][' + _list['id'] + '][]" class="form-control g-price"></td></tr>';
                            }
                            _html += '</tbody></table></div>';
                            sd.append(_html);
                            $('#city_' + val).css('border', '1px solid #ffff00');
                            $("html,body").delay(500).animate({scrollTop: $('#city_' + val).offset().top}, 300);
                        } else {
                            alert('该城市下还没有分销商');
                        }
                    }, 'json');
                }
            })

            //单个分销商设置
            $('#distributor-select').change(function() {
                var obj = $(this), val = obj.val();
                if ($('#agency_' + val).length > 0) { //如果存在直接跳到该位置
                    $('#agency_' + val).css('border', '2px solid #ff0000');
                    $("html,body").delay(500).animate({scrollTop: $('#agency_' + val).offset().top}, 300);
                } else {
                    var pcityId = $(this).find("option:selected").attr('pcity');
                    var $pcityObj = $('#city_option_' + pcityId);

                    if ($('#city_' + pcityId).length > 0) {
                        $('#city_' + pcityId).find('tbody').append('<tr id="agency_' + val + '"><td><div class="ckbox ckbox-primary"><input type="checkbox" id="checkbox-' + pcityId + '" value="1"><label for="checkbox-' + pcityId + '"></label></div></td><td>' + obj.find("option:selected").text() + '</td><td><input type="text" name="price[' + pcityId + '][' + val + '][]" class="form-control i-price"></td><td><input type="text"  name="price[' + pcityId + '][' + val + '][]" class="form-control g-price"></td></tr>');
                    } else {
                        var _html = '<div class="table-responsive  mb' + pcityId + '" id="city_' + pcityId + '"><table class="table table-bordered"><thead><td><div class="ckbox ckbox-primary"><input type="checkbox" class="all-btn" id="all-btn-' + pcityId + '" value="1"><label for="all-btn-' + pcityId + '">' + $pcityObj.text() + '</label></div></td><td><input type="text"  class="form-control i-price" placeholder="散客结算价"></td><td><input type="text"   class="form-control g-price" placeholder="团队结算价"></td><td><button class="btn btn-success btn-xs area-set-btn" type="button">批量设置</button></td></thead><tbody>';
                        _html += '<tr><th></th><th>名称</th><th>散客结算价</th><th>团队结算价</th></tr><tr></tr>';
                        _html += '<tr id="agency_' + val + '"><td><div class="ckbox ckbox-primary"><input type="checkbox" id="checkbox-' + pcityId + '" value="1"><label for="checkbox-' + pcityId + '"></label></div></td><td>' + obj.find("option:selected").text() + '</td><td><input type="text"  name="price[' + pcityId + '][' + val + '][]" class="form-control i-price"></td><td><input type="text" name="price[' + pcityId + '][' + val + '][]" class="form-control g-price"></td></tr>';
                        _html += '</tbody></table></div>';
                        sd.append(_html);
                        $('#city_' + pcityId).css('border', '1px solid #ffff00');
                        $("html,body").delay(500).animate({scrollTop: $('#city_' + pcityId).offset().top}, 300);
                    }
                    $('#agency_' + val).css('border', '2px solid #ff0000');
                    $("html,body").delay(500).animate({scrollTop: $('#agency_' + val).offset().top}, 300);
                }
            })
        }()

        //保存订单

        $('#form').submit(function() {
            $(this).validationEngine({
                promptPosition: 'topRight',
                addFailureCssClassToField: 'error',
                autoHidePrompt: true,
                autoHideDelay: 3000
            });
            if ($(this).validationEngine('validate') === true) {
                $.post('#', $(this).serialize(), function(data) {
                    if(data.error==0){
                         alert('保存成功',function(){ window.location.reload();});
                    }else{
                    alert('保存失败');
                    }
                },'json');
            }
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
        var spinner = jQuery('#spinner').spinner({'min': 1});
        spinner.spinner('value', 1);

        var spinnerMin = jQuery('#spinner-min').spinner({'min': 1});
        spinnerMin.spinner('value', 1);

        var spinnerDay = jQuery('.spinner-day').spinner({'min': 1});
        spinnerDay.spinner('value', 1);





        // Form Toggles
        jQuery('.toggle').toggles({on: true});

        // Input Masks
        jQuery("#date").mask("99/99/9999");
        jQuery("#phone").mask("(999) 999-9999");
        jQuery("#ssn").mask("999-99-9999");

        // Select2
        jQuery("#area-select,#distributor-select, #select-multi, #through-tickets-select").select2();
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

