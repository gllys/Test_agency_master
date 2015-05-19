<?php
$this->breadcrumbs = array('分销商', '分销商管理');
?>
<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title">分销商管理</h4>
                </div>
                <!-- panel-heading -->
                <form id="manage-form" method="get" action="/agency/manager/index2" class="form-horizontal">
                    <div class="panel-body nopadding">
                        <div class="form-inline">
                            <div class="form-group " style="width: 350px;">
                                <label>合作日期：</label>
                                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['start_date']) ? $get['start_date'] :""; ?>" placeholder="开始日期"> ~
                                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['end_date']) ? $get['end_date'] : date('Y-m-d', time()); ?>" placeholder="结束日期">
                            </div>

                            <!--订单查询开始-->
                            <div class="form-group">
                                <div class="input-group input-group-sm" style=" position: relative; top: -2px;">
                                    <div class="input-group-btn">
                                        <button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                                            <?php
                                            //左边显示的名称
                                            $_querys = array('name' => '分销商名称', 'agency_name' => '分销商账号');
                                            //当前选择的name
                                            $_queryName = 'name';
                                            foreach ($_querys as $key => $val) {
                                                if (isset($get[$key])) {
                                                    $_queryName =  $key;
                                                    break;
                                                }
                                            }
                                            echo $_querys[$_queryName] ;
                                            ?>
                                        </button>
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                                tabindex="-1">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php
                                            //下拉列表
                                            foreach ($_querys as $key => $val) :
                                                ?>
                                                <li><a class="sec-btn" href="javascript:;" data-id="<?php echo $key ?>" id="" aria-labelledby="search_label"><?php echo $val; ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <script>
                                            $('.sec-btn').click(function() {
                                                $('#search_label').text($(this).text());
                                                $('#search_field').attr('name', $(this).attr('data-id'));
                                            });
                                        </script>
                                    </div>
                                    <input id="search_field" name="<?php echo $_queryName ?>" value="<?php echo empty($get[$_queryName])?'':$get[$_queryName] ?>" type="text" class="form-control" style="z-index: 0"/>
                                </div>
                            </div>
                            <!--订单查询结束-->


                            <div class="form-group">
                                <button class="btn btn-primary btn-sm" type="submit">查询</button>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-success btn-sm" href="/agency/manager/history">查看合作记录</a>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- panel-body -->



            </div>

            <ul class="nav nav-tabs">
                <li class=""><a href="/agency/manager/index" class="now1"><strong>我的合作分销商</strong></a></li>
                <li class="active"><a href="/agency/manager/index2" class="now2"><strong>平台合作分销商</strong></a></li>
            </ul>
            <!-- panel -->
            <div class="tab-content mb30">
                <div id="t1" class="tab-pane"></div>
                <div id="t2" class="tab-pane active">
                    <div class="table-responsive">
                        <table class="table table-bordered mb30">
                            <thead>
                            <tr>
                                <th>分销商名称</th>
                                <th>联系人</th>
                                <th>手机</th>
                                <th>电话</th>
                                <th>结算周期</th>
                                <th>信用余额</th>
                                <th>储值余额</th>
                                <th>合作日期</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($lists)): ?>
                                <tr><td colspan="9">暂无相关数据</td></tr>
                            <?php else: ?>
                                <?php
                                foreach($lists as $item):
                                    ?>
                                    <tr>
                                        <td><a href="/agency/manager/look?id=<?php echo $item['distributor_id'];?>&source=2"><?php echo $item['distributor_name'] ?></a>
                                        </td>
                                        <?php if(isset($list_ids)){
                                            ?>
                                            <td><?php  echo isset($list_ids[$item['distributor_id']]['contact'])?$list_ids[$item['distributor_id']]['contact']:"";?></td>
                                            <td><?php  echo  isset($list_ids[$item['distributor_id']]['mobile'])?$list_ids[$item['distributor_id']]['mobile']:"";?></td>
                                            <td><?php  echo  isset($list_ids[$item['distributor_id']]['telephone'])?$list_ids[$item['distributor_id']]['telephone']:"";?></td>
                                        <?php
                                        }else{ ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        <?php    }   ?>
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
                                        <td> <?php echo date('Y-m-d',$item['add_time']); ?></td>
                                        <!--data-id="<?php echo $item['id'];?>" href="/agency/manager/delcredit?id=<?php echo $item['id'];?>"-->
                                        <td data-names="<?php echo $item['distributor_name'] ?>" ><a  data-id="<?php echo $item['id'];?>" href="javascript:;" class="del">解除合作</a></td>
                                    </tr>
                                <?php endforeach;?>
                            <?php endif;?>
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
                </div>
            </div>
        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->

</div>

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
                            <select class="form-control select2" id="account_cycle" name="account_cycle"  onchange="changeDayShow(this.value)">
                                <option value="">请选择结算周期</option>
                                <option value="1">月结算</option>
                                <option value="0">周结算</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <select class="form-control select2" name="account_cycle_day" id="account_cycle_day">
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
        $('#settle-form').validationEngine({
            promptPosition: 'topRight',
            autoHideDelay: 3000
        });

        //非空判断
        if($('#account_cycle').val()==""){
            $('#account_cycle').validationEngine('showPrompt','请选择结算周期','error');
            return false;
        }

        if($('#account_cycle_day').val()==""){
            $('#account_cycle_day').validationEngine('showPrompt','请选择结算日','error');
            return false;
        }

        $.post('/agency/manager/setCycle',$('#settle-form').serialize(),function(data){
            if(data.error==0){
                alert("保存成功",function(){window.location.reload();});
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
                    alert("结算失败",function(){top.location.reload();});
                }else{
                    alert("结算成功",function(){window.location.reload();});
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
            var oagencyName = $(this).parent().data("names");
            var userid=$(this).data("id");
            //console.log(userid);
            PWConfirm('确认要解除与'+oagencyName+'分销商的合作么？',function(){
                $.get("/agency/manager/delcredit?id="+userid+"&tt="+Math.random(),function(datamsg) {
                    // alert(datamsg);
                    if(datamsg.error==0){
                        //setTimeout(alert("解除合作成功"),100);
                        //setTimeout(window.location.reload(),110);
                        setTimeout(function(){
                            alert('解除合作成功',function(){window.location.reload();});
                        },500)
                    }else{
                        setTimeout(function(){
                            alert('解除合作失败',function(){window.location.reload();});
                        },500)
                        //setTimeout(alert("解除合作失败"),100);
                        //setTimeout(window.location.reload(),110);
                    }

                }, " json ");
            });
            // return 123;
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
