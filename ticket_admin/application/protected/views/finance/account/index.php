<?php
$this->breadcrumbs = array('结算管理', '收款账号');
?>
<style>
                .table-bordered th {
                    line-height: 2em !important;
                }
                .table-bordered th,
                .table-bordered td {
                    vertical-align: middle !important;
                }
                .table-bordered a:hover {
                    text-decoration: none;
                }
                </style>
<div class="contentpanel">
    <style>
        .table tr>*{
            text-align:center
        }
    </style>
    <link rel="stylesheet" href="/css/validationEngine.jquery.css">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                  <button id="add_card" class="btn btn-primary btn-xs pull-right" data-target=".modal-bank" data-toggle="modal">添加银行卡</button>我的银行卡</h4>
        </div>
            <table class="table table-bordered table1">
                <thead>
                    <tr>
                        <th style="padding-left:20px;">银行名称</th>
                        <th>开户行</th>
                        <th>账号/卡号</th>
                        <th>账户名</th>
                        <th>是否默认收款账户</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($list): ?>	
                        <?php foreach ($list as $bank_list): ?>
                            <tr>
                                <td style="padding-left:20px;"><?php echo $bank_list['bank_name'] ?></td>
                                <td><?php echo $bank_list['open_bank'] ?></td>
                                <td><?php echo $bank_list['account'] ?></td>
                                <td><?php echo $bank_list['account_name'] ?></td>
                                <td>
                                    <?php if ($bank_list['status'] == 'normal'): ?>默认账户
                                    <?php else: ?>
                                        <a class="update_status" title="" href="/finance/account/updateBank/?id=<?php echo $bank_list['id'] ?>"><button class='btn  btn-success btn-xs'>设为默认</button></a>
                                    <?php endif; ?>	
                                </td>
                                <td>
                                    <a href=".bs-example-modal-lg"  onclick="edit('<?php echo $bank_list['id'] ?>')" data-toggle="modal" class="btn btn-success btn-bordered btn-xs" style="border-width: 1px">修改</a>
                                    <a title="删除" href="/finance/account/delBank/?id=<?php echo $bank_list['id'] ?>&account=<?php echo $bank_list['account'] ?>&account_name=<?php echo $bank_list['account_name'] ?>" class="del btn btn-xs btn-danger btn-bordered" style="border-width: 1px">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
       


    </div>
    <div style="text-align:center" class="panel-footer">
        <div id="basicTable_paginate" class="pagenumQu">
            <?php
            $this->widget('common.widgets.pagers.ULinkPager', array(
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

    <div class="modal fade modal-alipay" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                    <h4 class="modal-title">添加支付宝</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="position:static">支付宝账号:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="position:static">账户名:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">添加</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-bank" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="bank_card" action="#">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                        <h4 class="modal-title">添加银行卡</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"  style="overflow: inherit;">
                            <label class="col-sm-2 control-label">收款银行:</label>
                            <?php if ($bank): ?>
                                <div class="col-sm-7" style="position: static;">
                                    <select  data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="select-basic" name="bank_id">
                                        <option  value="" selected='selected'>请选择银行</option>
                                        <?php foreach ($bank as $value): ?>
                                            <option value="<?php echo $value['id'] ?>" ><?php echo $value['name'] ?></option>
                                        <?php endforeach; ?>		
                                    </select>					
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">开户行:</label>
                            <div class="col-sm-10" style="position:static">
                                <input type="text" data-prompt-position="topLeft" class="form-control validate[required]" tag="开户行" name="open_bank">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">卡号:</label>
                            <div class="col-sm-10" style="position:static">
                                <input type="text" class="form-control validate[required,custom[onlyNumberSp],minSize[15],maxSize[20]]" tag="卡号" name="account">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">账户名:</label>
                            <div class="col-sm-10" style="position:static">
                                <input type="text" class="form-control validate[required]" tag="账户名" name="account_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="add_bank_card">添加</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- contentpanel -->

<div class="modal fade bs-example-modal-lg" id="bank_edit" tabindex="-1" role="dialog"></div>

<script type="text/javascript">
    function edit(id) {
        $('#bank_edit').html();
        $.get('/finance/account/edit/?id=' + id, function(data) {
            $('#bank_edit').html(data);
        });
    }
</script>


<script>
    jQuery(document).ready(function() {

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

        $('#add_card').click(function() {
            $('#s2id_select-basic').find('.select2-chosen').text('请选择银行');
            $('#bank_card')[0].reset();
        });

        $('#bank_card').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });

        $('#bank_card').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });
        
        $('#add_bank_card').click(function() {
           // return false;
            if ($('#bank_card').validationEngine('validate') == true) {
               // alert(123);
				if($('#bank_card [name=bank_id]').val()==''){
					$('#bank_card [name=bank_id]').parent().PWShowPrompt('请选择收款银行');
					return false;
                }
				
                $.post('/finance/account/saveBank', $('#bank_card').serialize(), function(data) {
                    if (data.error === 0) {

                        setTimeout(function(){
                            alert('添加银行卡成功',function(){window.location.partReload();});
                        },500)
                    } else {

                        setTimeout(function(){
                            alert(data.msg);
                        },500)
                    }
                }, "json");
            }
            return false;
        });

        $('a.del').click(function() {
            var _this = $(this)
			PWConfirm('确认要删除此条银行卡记录吗？',function(){
			  $.post(_this.attr('href'), function(data) {
                if (data.error === 0) {

                    setTimeout(function(){
                        alert('删除成功',function(){window.location.partReload();});
                    },500)
                } else {

                    setTimeout(function(){
                        alert(data.msg);
                    },500)
                }
              }, "json");
        	});
            return false;
        });

        $('a.update_status').click(function() {
            var _this = $(this)
			 PWConfirm('确认要设置此银行卡为默认账户吗？',function(){
			    $.post(_this.attr('href'), function(data) {
                if (data.error == 0) {
                    //console.log(123);
                    //alert('设置成功',function(){window.location.partReload();});

                    setTimeout(function(){
                        alert('设置成功',function(){window.location.partReload();});
                    },500)

                    //console.log(456);
                    //window.location.partReload();
                } else {

                    setTimeout(function(){
                        //alert('设置成功',function(){window.location.partReload();});
                        alert(data.msg);
                    },500)

                }
            	}, " json ");
            });
            return false;
        });

    });

</script>