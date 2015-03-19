<style>
    .box{
        border: 1px solid #ccc;
        height: 400px;
        overflow: auto;
        padding-left: 0px;
    }
    li{
        list-style: none;
    }
    .box li{
        line-height: 2em;
        height: 2em;
        padding-left: 10px;
    }
    .box li:hover{
        cursor: pointer;
    }
    #search_list{
        height: 360px;
    }
    .right{
        display:inline-block;
        width: 100%;
        font-size: 40px;
        text-align: center;
        margin-top: 225px;
    }
    .active{
        background: #f5f5f5;
    }
    body{
        -moz-user-select: none; /*火狐*/
        -webkit-user-select: none; /*webkit浏览器*/
        -ms-user-select: none; /*IE10*/
        -khtml-user-select: none; /*早期浏览器*/
        user-select: none;
    }
</style>
<section>
    <div class="contentpanel">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h4 class="panel-title">
                            发布产品
                        </h4>
                    </div><!-- panel-heading -->

                    <h2 style="margin-left: 100px;">步骤1，完善产品信息，必填
                    </h2>
                    <div class="row">
                        <div class="col-lg-4" style="margin-left: 20px;margin-top: 10px;">
                            说明:您可以任意组合票种来发布成产品,比如A景区的成人票+B景区的儿童票。
                            如没有票种请先到景区模块内新建票种，如无法新建则表明您没有权限。
                        </div>
                    </div>
                    <div class="row" style="margin-top: 40px;margin-left: 10px;" >
                        <div class="col-lg-3">
                            <p>
                                选择你要发布门票的景区
                            </p>
                            <div class="form-group">
                                <div class="col-sm-4" style="width:100%;margin-top:10px;">
                                    <select data-placeholder="Choose One" style="width:100%;padding:0 10px;" id="distributor-select" name="scenic_id">
                                        <option value="">请输入景区名称</option>
                                        <?php
                                        if (isset($lanList)):
                                            foreach ($lanList as $item):
                                                ?>
                                                <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                                                <?php
                                            endforeach;
                                        endif;
                                        ?> 
                                    </select>
                                </div>
                            </div><!-- form-group -->
                        </div>
                        <div class="col-sm-1"><span class="glyphicon  glyphicon-chevron-right right"></span></div>
                        <div class="col-lg-3">
                            <p>
                                双击选择要发布的票种
                            </p>
                            <ul class="box" id="choose"></ul>
                        </div>
                        <form class="form-horizontal form-bordered" id="scenic_land">
                            <div class="col-sm-1"><span class="glyphicon  glyphicon-chevron-right right"></span></div>
                            <div class="col-lg-3" style="margin-right: 10px;">
                                <p>双击删除选择的票种</p>
                                <ul class="box" id="delete"></ul>
                            </div>
                            <button type="button" class="btn btn-success" style="margin:20px;" id="next_but2">下一步</button>
                        </form>
                    </div>

                </div><!-- panel -->

            </div><!-- col-md-6 -->
        </div><!-- row -->
    </div><!-- contentpanel -->
</section>
<script>
    jQuery(document).ready(function() {
        $('#next_but2').click(function() {
            var obj = $('#scenic_land');
            if(obj.serialize().length == 0){
                alert('请选择票');
            }else{
                location.href = "/ticket/product/index2?"+obj.serialize();
                //$.post('/ticket/product/next2', obj.serialize(), function() {},"json");
            }
        });
    });
</script>
<script>
    jQuery(document).ready(function() {
        $('#search_list').delegate('li', 'mouseover', function() {
            $(this).addClass('active')
        }).delegate('li', 'mouseout', function() {
            $(this).removeClass('active')
        });
        $("#delete").delegate('li', 'dblclick', function() {
            $(this).clone().prependTo("#choose");
            $(this).remove();
        }).delegate('li', 'mouseover', function() {
            $(this).addClass('active')
        }).delegate('li', 'mouseout', function() {
            $(this).removeClass('active')
        })

        $("#choose").delegate("li", 'dblclick', function() {
            $(this).clone().prependTo("#delete");
            $(this).remove();
        }).delegate('li', 'mouseover', function() {
            $(this).addClass('active')
        }).delegate('li', 'mouseout', function() {
            $(this).removeClass('active')
        })

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


                if (typeof i != undefined) {
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
                var scenic_id = $(this).val();
                $.ajaxSetup({async: false});
                $.post('/ticket/product/list', {scenic_id: scenic_id}, function(data) {
                    var code = '';
                    if(data.error == '1'){
                        alert(data.msg);
                    }else{
                        var objdata = eval(data);
                        $(objdata).each(function(index) {
                            var val = objdata[index];
                            code = code + '<li><input type="hidden" name="scenic_id[]" value="' + val.id + '"/> ' + val.name + '</li>';
                        }); 
                    }
                    $('#choose').html(code);
                },'json');

                $.ajaxSetup({async: true});

                var obj = $(this),
                        val = obj.val(), //选中的景区id
                        p = obj.find(':selected').attr('data-p'),
                        t = obj.find(':selected').text(), // 选中的景区的名称
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
                if (typeof d == undefined) {
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