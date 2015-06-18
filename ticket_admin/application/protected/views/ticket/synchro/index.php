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
        <h4 class="panel-title">落地同步查询</h4>
    </div>
    <div class="panel-body">
        <form class="form-inline" method="get" action="/ticket/synchro/">
            <div class="form-group" style="width: 370px">
                <label class="col-sm-3 control-label" style="margin-top: 5px;width: 85px;">同步时间：</label>
                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" placeholder="同步起始时间" name="sent_start" class="form-control datepicker" type="text" readonly="readonly"
                       value="<?php echo isset($get['sent_start']) ? $get['sent_start'] : ''?>"> ~
                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" placeholder="同步结束时间" name="sent_end"  class="form-control datepicker"  type="text" readonly="readonly"
                       value="<?php echo isset($get['sent_end']) ? $get['sent_end'] : ''?>">
            </div>
            <!-- form-group -->

            <div class="form-group">
                <label class="col-sm-4 control-label" style="margin-top: 5px;width: 85px;">景区名称：</label>
                <div class="col-sm-4">
                    <input name="landscape" maxlength="11" id="landscape" type="text" class="form-control" value="<?php echo isset($get['landscape']) ? $get['landscape'] : ''?>">
                </div>
            </div>

            <div class="form-group" style="width: 200px">
                <label class="col-sm-4 control-label" style="margin-top: 5px;width: 85px;">是否在线：</label>
                <div class="col-sm-4" style="width: 100px">
                    <select name="state" id="state_link" class="select2">
                        <option value="" selected="selected">全部</option>
                        <option value="1">是</option>
                        <option value="0">否</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary btn-sm" type="button" id="check">查询</button>
            </div>
        </form>
    </div>

</div>
<!-- panel-body -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href=""
                   data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href=""
                   data-original-title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <h4 class="panel-title">落地同步列表</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                    <tr>
                        <th>景区名称</th>
                        <th>联系人</th>
                        <th>联系电话</th>
                        <th>是否在线</th>
                        <th>最后同步时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data)): foreach($data as $value): $lanid = $value['landscape_id'];?>
                            <tr>
                                <td><?php
                                    foreach($landlist as $v){
                                        if($v['id'] == $value['landscape_id']){
                                            echo $v['name'];break;
                                        }
                                    }
                                  ;?>
                                </td>
                                <td><?php echo $value['user']?></td>
                                <td><?php echo $value['mobile'];?></td>
                                <td><?php echo Yii::app()->redis->get("live:landscape:$lanid") ? '在线' : '不在线';
                                    ?></td>
                                <td><?php echo date('Y-m-d H:i:s',$value['time']);?></td>
                                <td><a href="/ticket/synchro/asyncList/id/<?php echo $lanid;?>">查看</a></td>
                            </tr>
                        <?php endforeach;?>
                        <?php else:?>
                           <tr><td colspan="6" style="text-align: center !important;">无相关数据</td></tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<div class="panel-footer" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
        <div id="basicTable_paginate" class="pagenumQu">
            <?php

         if (!empty($data)) {
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
</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function() {
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });
        $('#state_link').select2("val","<?php echo isset($get['state']) && $get['state']!='' ?$get['state'] : ''?>");
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

      //  $('#state_link').select2('val',<?php echo isset($get['state']) && !empty($get['state']) ? $get['state']
      :''?>);
    });
</script>

