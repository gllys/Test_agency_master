<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-btns" style="display: none;">
            <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href=""
               data-original-title=""><i class="fa fa-minus"></i></a>
            <a title="" data-toggle="tooltip" class="panel-close tooltips" href=""
               data-original-title=""><i class="fa fa-times"></i></a>
        </div>
        <!-- panel-btns -->
        <h4 class="panel-title">短信日志查询</h4>
    </div>
    <div class="panel-body">
        <form class="form-inline" method="get" action="/agency/sms/">
            <div class="form-group" style="width: 400px">
                <label class="col-sm-3 control-label" style="margin-top: 5px">发送时间：</label>
                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="sent_start" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['sent_start']) ? $get['sent_start'] : ''?>"> ~
                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="sent_end"  class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['sent_end']) ? $get['sent_end'] : ''?>">
            </div>
            <!-- form-group -->

            <div class="form-group" style="width: 180px">
                <label class="col-sm-4 control-label" style="margin-top: 5px">状态：</label>
                <div class="col-sm-4" style="width: 100px">
                    <select name="state" id="state_link" class="select2" data-placeholder="状态">
                        <option value="0">全部</option>
                        <option value="1">成功</option>
                        <option value="2">失败</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" style="margin-top: 5px">手机号码：</label>
                <div class="col-sm-6">
                    <input name="mobile" maxlength="11" id="mobile" type="text" class="form-control validate[custom[mobile]]" value="<?php echo isset($get['mobile']) ? $get['mobile'] : ''?>">
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-sm" type="button" id="check">查询</button>
            </div>
        </form>
    </div>

</div>
<!-- panel-body -->
    <div class="panel-body" style="font-size: 20px;margin-left: 7px;" >


        <div class="col-sm-4"> <span>当前余额：<b class="red"><span class="text-danger"><?php echo !empty($balance) ?
                            $balance : 0 ?></span></b>元</span></div>

        <div class="col-sm-6"><span>可发送短信剩余条数：<b class="red"><span class="text-danger"><?php echo !empty($remainder)
                            ? $remainder : 0
                        ?></span></b>条</span></div>
        <div class="col-sm-2"><button class="btn btn-primary btn-sm" data-target=".bs-example-modal-static"
                                      data-toggle="modal" onclick="add_rule()">预警通知设置</button>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href=""
                   data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href=""
                   data-original-title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <h4 class="panel-title">短信列表</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                    <tr>
                        <th width="120px">发送时间</th>
                        <th width="120px">手机号码</th>
                        <th width="60px">状态</th>
                        <th>内容</th>
                        <th width="120px">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($smsInfo)): foreach($smsInfo as $value):?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s',$value['sent_at'])?></td>
                                <td><?php echo $value['mobile']?></td>
                                <td>
                                    <span class="text-<?php echo $value['status'] == 1 ? 'success' : 'danger' ?>">
                                        <?php
                                            echo $value['status'] == 1 ? '成功' : '失败：'.$value['fail_reason']
                                        ?>
                                    </span>
                                </td>
                                <td><?php
                                        $content = urldecode($value['content']);
                                        switch($value['type']){
                                            case 0:
                                            case 2:
                                            case 3:
                                            case 4:
                                                $content = substr_replace($content,'****',-5,4);
                                                break;
                                            default:
                                                $content = $content;
                                                break;
                                        }
                                        echo $content;
                                    ?></td>
                                <td><?php if($value['type'] == 1):?><a href="/agency/orders/detail/id/<?php echo
                                    $value['order_id']?>">查看</a><?php endif;?></td>
                            </tr>
                        <?php endforeach;?>
                        <?php else:?>
                           <tr><td colspan="5" style="text-align: center !important;">无相关数据</td></tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="panel-footer" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
        <div id="basicTable_paginate" class="pagenumQu">
            <?php
            if (!empty($smsInfo)) {
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
            }
            ?>
        </div>
    </div>

    <div class="modal fade bs-example-modal-static in"  tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="false" >
        <div id="modal1" class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal form-bordered" id="repass-form">
                    <div class="modal-body">
                        <div class="form-group" style="overflow: inherit;">
                            <label class="col-sm-3 control-label">短信当前余额低于</label>
                            <div class="col-sm-3">
                                <input type="text" tag="短信提醒时余额"  class="form-control validate[required,number]" value=""
                                       id="money" name="money">
                            </div>
                            <label class="control-label">元</label>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">提醒邮箱地址:</label>
                            <div class="col-sm-8">
                                <textarea tag="提醒邮箱地址" class="form-control validate[required,email]" rows="5" id="emails" name="emails"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="text-danger">
                                <span>提醒：发送多用户邮件,邮箱地址可用“;”符号进行区分。</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="sendSms" type="button" class="btn btn-success">保存</button>
                        <button class="cancel btn btn-default" data-dismiss="modal" type="button">取消</button>
                    </div>
                </form>
            </div>
        </div></div>
</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function() {
// Date Picker
        jQuery('#datepicker').datepicker();
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });
// Select2
        jQuery("#select-basic, #select-multi").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        function format(item) {
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel')) + ' mr10"></i>' + item.text;
        }

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
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

        $('form').validationEngine({
            autoHidePrompt : true,
            autoHideDelay: 2000
        })

        $('#check').click(function(){
            if($('form').validationEngine('validate') == true){
                $('form').submit();
            }
            return false;
        })

        $('#state_link').select2('val',<?php echo isset($get['state']) && !empty($get['state']) ? $get['state'] : 0?>);
    });

    //新增分销策略
    function add_rule() {
        $('#verify-modal').html();
    }

    //预警短信设置
    $("#sendSms").click(function(){
        var obj = $('#repass-form');
        if (obj.validationEngine('validate') == true) {
            $.post('/agency/sms/smsWarn', obj.serialize(), function (data) {
                if(data.error == 0){
                    alert(data.msg); return false;
                }else{
                    alert(data.msg);
                }
            }, 'json');
        }
    });
</script>

