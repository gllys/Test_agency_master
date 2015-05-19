<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/18/15
 * Time: 4:18 PM
 * File: index.php
 */
?>
<style>
    .btn-group-date .form-date {
        width: 90px;
        background-color: #ffffff;
        cursor: default;
    }
    .btn-group-date .btn-sm {
        width: 50px;
    }
    .btn-group-date .form-group {
        margin-right: 5px;
    }
    <?php if ($param['date_range'] == 'month') {?>
     .ui-datepicker-calendar{
         display: none;
     }
    .ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year{
        width:100px;
    }
    <?php }?>
    .ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">
    <ul class="nav nav-tabs">
        <li class="<?php echo $param['date_range'] == 'day' ? 'active' : ''?>"><a href="/ticket/stats/index/range/day/" ><strong>景区统计（天）</strong></a></li>
        <li class="<?php echo $param['date_range'] != 'day' ? 'active' : ''?>"><a href="/ticket/stats/index/range/month/"><strong>景区统计（月）</strong></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="home">
            <form action="/ticket/stats/" method="get" id="date_form">
                <div class="btn-group form-inline" style="height:43px;width:230px">
                    <select name="landscape_id">
                        <option>选择一个景区</option><?php
                        if (isset($landscapes)) {
                            foreach ($landscapes as $item) {
                                ?><option value="<?php echo $item['id']?>" <?php echo isset($param['landscape_id']) && $param['landscape_id'] == $item['id'] ? 'selected="selected"' : ''?>><?php echo $item['name']?></option><?php
                            }
                        }
                        ?></select>
                </div>
                <div id="date-0" class="btn-group form-inline btn-group-date"
                     data-max="<?php echo date('Ymd') ?>" data="<?php echo $param['first_date'] ?>"
                     style="<?php echo $param['date_range'] != 'day' ? 'display:none' : '' ?>">
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary day-prev" type="button"><i class="fa fa-chevron-left"></i></button>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control form-date datepicker first_date" readonly="readonly" value="<?php echo $param['first_date'] ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control form-date datepicker last_date" readonly="readonly" value="<?php echo $param['last_date'] ?>">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary day-next" type="button"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
                <div id="date-1" class="btn-group form-inline btn-group-date"
                     data-max="<?php echo date('Ym') ?>" data="<?php echo $param['first_date'] ?>"
                     style="<?php echo $param['date_range'] != 'month' ? 'display:none' : '' ?>">
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary month-prev" type="button"><i class="fa fa-chevron-left"></i></button>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control datepicker first_date" readonly="readonly" value="<?php echo $param['first_date'] ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control datepicker last_date" readonly="readonly" value="<?php echo $param['last_date'] ?>">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary month-next" type="button"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="btn-group form-inline">
                    <div class="form-group">
                        <input type="hidden" name="range" value="<?php echo $param['date_range'] ?>">
                        <input type="hidden" name="first_date" id="first_date" value="<?php echo $param['first_date'] ?>">
                        <input type="hidden" name="last_date" id="last_date" value="<?php echo $param['last_date'] ?>">
                        <input type="hidden" name="is_export" class="is_export" value="0">
                        <button class="btn btn-sm btn-primary" type="button" id="search">查询</button>
                        <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
                    </div>
                </div>
            </form>
            <div id="t1" class="tab-pane active">
                <div class="panel panel-default">
                    <table class="table table-default table-hover table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="line-height: 26px;">景区</th>
                            <th>销售数量</th>
                            <th>销售额</th>
                            <th>入园数</th>
                            <th>退票数</th>
                            <th>退票总额</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($lists)):
                            foreach ($lists as $item):
                                ?>
                                <tr>
                                    <td><?php echo $item['landscape_name']?></td>
                                    <td><?php echo $item['tickets_total']?></td>
                                    <td><?php echo $item['sale_money']?></td>
                                    <td><?php echo $item['used_total']?></td>
                                    <td><?php echo $item['refunded_total']?></td>
                                    <td><?php echo $item['refund_money']?></td>
                                    <td>
                                        <a href="/ticket/stats/detail/range/<?php echo "{$param['date_range']}/landscape_id/{$item['landscape_id']}/first_date/{$param['first_date']}/last_date/{$param['last_date']}/"?>">查看详情</a>
                                        <a  target="_Blank" class="clearPart" href="/ticket/stats/detail/range/<?php echo "{$param['date_range']}/landscape_id/{$item['landscape_id']}/first_date/{$param['first_date']}/last_date/{$param['last_date']}/is_export/1/"?>">导出详情</a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align:center" class="panel-footer">
                    <?php
                    if (isset($pages)) {
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
        </div><!-- tab-pane -->
    </div>
</div>
<script>
    $(function ()
    {
        $('#export').click(function() {
            $('.is_export').attr('value', '1');
            $('#date_form').addClass('clearPart');
            $('#date_form').submit();
			$('#date_form').removeClass('clearPart');
            $('.is_export').attr('value', '0');
        });

        $('#search').click(function () {
            var x = <?php echo $param['date_range'] == 'day' ? 0 : 1?>;
            $('[name=first_date]').val($('#date-' + x + ' .first_date').val());
            $('[name=last_date]').val($('#date-' + x + ' .last_date').val());
            $('#date_form').submit();
        });
        
        
        $('select').select2();
        $('.day-prev').bind('click', function ()
        {
            var origin = $('#date-0 .first_date').val().split('-');
            var date = new Date(origin[0], origin[1] - 1, origin[2] == undefined ? 1 : origin[2]);
            date.setMonth(date.getMonth() - 1);
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var date = date.getDate();
            $('#date-0 .first_date').val(year + '-' + lpad(month) + '-' + lpad(date));
            var days = new Date(year, month, 0).getDate();
            $('#date-0 .last_date').val(year + '-' + lpad(month) + '-' + days);
            sync_date($('#date-0 .first_date').val(), $('#date-0 .last_date').val());
        });
        $('.day-next').bind('click', function ()
        {
            var origin = $('#date-0 .first_date').val().split('-');
            var date = new Date(origin[0], origin[1] - 1, origin[2] == undefined ? 1 : origin[2]);
            date.setMonth(date.getMonth() + 1);
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var date = date.getDate();
            $('#date-0 .first_date').val(year + '-' + lpad(month) + '-' + lpad(date));
            var days = new Date(year, month, 0).getDate();
            $('#date-0 .last_date').val(year + '-' + lpad(month) + '-' + days);
            sync_date($('#date-0 .first_date').val(), $('#date-0 .last_date').val());
        });
        $('.month-prev').bind('click', function ()
        {
            var origin = $('#date-1 .first_date').val().split('-');
            var date = new Date(origin[0], origin[1] - 1, origin[2] == undefined ? 1 : origin[2]);
            var year = date.getFullYear() - 1;
            date.setYear(year);
            var month = date.getMonth() + 1;
            $('#date-1 .first_date').val(year + '-' + lpad(month));
            $('#date-1 .last_date').val(year + '-' + 12);
            sync_date($('#date-1 .first_date').val(), $('#date-1 .last_date').val());
        });
        $('.month-next').bind('click', function ()
        {
            var origin = $('#date-1 .first_date').val().split('-');
            var date = new Date(origin[0], origin[1] - 1, origin[2] == undefined ? 1 : origin[2]);
            var year = date.getFullYear() + 1;
            date.setYear(year);
            var month = date.getMonth() + 1;
            $('#date-1 .first_date').val(year + '-' + lpad(month));
            $('#date-1 .last_date').val(year + '-' + 12);
            sync_date($('#date-1 .first_date').val(), $('#date-1 .last_date').val());
        });
        function sync_date(a, b){
            $('#first_date').val(a);
            $('#last_date').val(b);
        }
        function lpad(val) {
            return val < 10 ? '0' + val : val;
        }
        <?php if ($param['date_range'] == 'day') {?>
        jQuery('.form-date').datepicker({
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
        <?php } else {?>
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'yy-mm',
            closeText: '确定',
            monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
            yearRange: "1995:2065",
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            },
            onClose: function(dateText, inst) { 
                $('.select2-drop').hide(); 
            }
        });
        <?php }?>
    });
</script>
