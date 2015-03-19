<?php
$this->breadcrumbs = array('分销商', '分销商管理');
?>
<div class="contentpanel">
    <style>
        .table tr>*{
            text-align:center
        }
    </style>
    <div class="panel panel-default">
        
        <div id="show_msg"></div>
        <div class="panel-body">
            <div class="row">
            	<div class="col-sm-12" style="padding-bottom:10px">
            		<a href="/agency/manager/res" class="btn btn-warning">查找分销商</a>
            	</div>
            </div>
            <form id="manage-form" method="get" class="form-horizontal">
                <div class="row">
                    <div class="col-lg-2">
                        <input type="text" placeholder="分销商名称" class="form-control" value="<?php echo isset($_GET['name'])?$_GET['name']:""; ?>" name="name">
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" class="btn btn-white btn-sm">查询</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div style="padding-top:10px" class="col-sm-12">
                    <!--a href="partner_apply.html"><button class="btn btn-success">申请审核 <b class="badge">0</b></button></a-->
                    <a href="/agency/manager/history" class="btn btn-info">查看合作记录</a>
                    <!--a href="partner_blacklist.html"><button style="margin-left:10px" class="btn btn-info">查看 黑名单</button></a-->
                </div>
            </div>
        </div>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width:20%">分销商名称</th>
                    <th style="width:20%">结算周期</th>
                    <th style="width:20%">信用余额</th>
                    <th style="width:20%">储值余额</th>
                    <th style="width:20%">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($lists)): ?>
                    <tr><td colspan="4">暂无数据</td></tr>
                <?php else: ?>
                    <?php foreach($lists as $item): ?>
                        <tr>
                            <td><!--a href="javascript:alert('please wait..');"--><?php echo $item['distributor_name'] ?><!--/a--></td>
                            <td>
                                <?php
                                if(isset($orgInfo['is_credit'])&&$orgInfo['is_credit']==1):
                                ?>
                                <a href="#modal2" onclick="settle_setting('<?php echo $item['id']; ?>','<?php echo $item['distributor_name']; ?>','<?php echo $item['checkout_type']; ?>','<?php echo $item['checkout_date']; ?>')"
                                   data-toggle="modal">
                                       <?php if($item['checkout_type'] == '1'){echo '月结 '.$item['checkout_date'].'日';}elseif($item['checkout_type'] == '0'){echo '周结 '.Credit::getWeekDay($item['checkout_date']);}else{echo '未设置';}?>
                                </a>
                                <?php else:?>
                                      <?php if($item['checkout_type'] == '1'){echo '月结 '.$item['checkout_date'].'日';}elseif($item['checkout_type'] == '0'){echo '周结 '.Credit::getWeekDay($item['checkout_date']);}else{echo '--';}?>
                                <?php endif ;?>
                            </td>
                            <td> <?php
                                if(isset($orgInfo['is_credit'])&&$orgInfo['is_credit']==1):
                                ?>
                                <a href="/agency/manager/credit?id=<?php echo $item['id']; ?>"><?php echo $item['credit_infinite']?"无限":$item['credit_money'] ?></a>
                                <?php else:?>
                                <?php echo $item['credit_infinite']?"无限":$item['credit_money'] ?>
                                <?php endif ;?>
                            </td>
                            <td>
                                <?php
                                if(isset($orgInfo['is_balance'])&&$orgInfo['is_balance']==1):
                                ?>
                                <a href="/agency/manager/advance?id=<?php echo $item['id']; ?>"><?php echo $item['balance_money'] ?></a>
                                 <?php else:?>
                                <?php echo $item['balance_money'] ?>
                                <?php endif ;?>
                            </td>
                            <td><a href="/agency/manager/delcredit?id=<?php echo $item['id'];?>" class="del">解除合作</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif;?>
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

<div class="modal fade in" id="modal2">
    <form class="m-b-none" id="settle-form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                    <h4 id="myModalLabel" class="modal-title">结算周期配置（<span id="agency_name2"></span>）</h4>
                </div>
                <div class="modal-body">
                    <div id="show_msg2"></div>
                    <div class="block">
                        <label class="control-label"></label>
                        <div class="col-lg-4">
                            <select class="form-control" id="account_cycle" name="account_cycle"  onchange="changeDayShow(this.value)">
                                <option value="">请选择结算周期</option>
                                <option value="1">月结算</option>
                                <option value="0">周结算</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <select class="form-control" name="account_cycle_day" id="account_cycle_day">
                                <option value="">请选择结算日</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <button class="btn btn-warning btn-xs" id="genbill">立刻结算</button>
                        </div>
                        <div class="col-lg-2" id="settle-right-now">

                        </div>
                    </div>
                </div>
                <input type="hidden" id="agency-credit-id" name="id" />
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-sm btn-default" type="button">取消</button>
                    <button  class="btn btn-sm btn-primary" type="button" onclick="common_post()">保存</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var weeks = {0:"周日",1:"周一",2:"周二",3:"周三",4:"周四",5:"周五",6:"周六"};
    var d= new Date();
    var dayCount = new Date(d.getFullYear(), d.getMonth()+1,0).getDate();
    //修改结算日的格式
    function changeDayShow(type)
    {
        var obj = $('#account_cycle_day');
        var default_option = '<option value="">请选择结算日</option>';
        if(type == 0){
            for(var i=0;i<7;i++){
                default_option += "<option value='"+i+"'>"+weeks[i]+"</option>";
            }
            obj.html(default_option);
        }else if(type == 1){
            for(var i=1;i<=dayCount;i++){
                default_option += "<option value='"+i+"'>"+i+"号</option>";
            }
            obj.html(default_option);
        }else{
            obj.html('<option value="">请选择结算日</option>');
        }
    }

    //结算周期
    function settle_setting(id,name,type,date)
    {
        $('#agency-credit-id').val(id);
        $("#agency_name2").text(name);
        var html = '<option value="">请选择结算日</option>';
        $('#account_cycle').val(type);
        if(type=='0'){
            for(var i=0;i<7;i++){
                html += "<option "+(date==i?"selected":"")+" value='"+i+"'>"+weeks[i]+"</option>";
            }
        }else if(type==1){
            for(var i=1;i<=dayCount;i++){
                html += "<option "+(date==i?"selected":"")+" value='"+i+"'>"+i+"号</option>";
            }
        }
        $("#account_cycle_day").html(html);
    }


    function common_post(){
        if($('#account_cycle_day').val()=="" || $('#account_cycle').val()==""){
            alert("请选择结算信息！");return false;
        }
        $.post('/agency/manager/setCycle',$('#settle-form').serialize(),function(data){
            if(data.error==0){
                alert("保存成功");
                location.href = '/agency/manager';
            }else{
                alert("保存失败,"+data.msg);
            }
        },'json');
    }

</script>
<script>
    jQuery(document).ready(function() {
        $("#genbill").click(function(){
//            if (!window.confirm("确定要立刻结算?")) {
//                return false;
//            }
            $.post('/agency/manager/genbill',{'id':$('#agency-credit-id').val()}, function(data){
	            data = JSON.parse(data);
                if(data.error){
                   alert("结算失败");
                   top.location.reload();
                }else{
                    alert("结算成功");
                   location.href = '/agency/manager';
                }
            });
            return false;
        })


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

        $('a.del').click(function() {
            if (!window.confirm("确定要解除合作?")) {
                return false;
            }
            $.post($(this).attr('href'), function() {
            	alert('解除合作成功');
                window.location.reload();
            });
            return false;
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
