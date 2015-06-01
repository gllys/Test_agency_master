<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 11/3/14
 * Time: 2:46 PM
 */
$this->breadcrumbs = array('产品', '价格、库存规则定制');
?>
<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">定制价格库存规则</h4>
                </div>
                <!-- panel-heading -->

                <form class="form-horizontal form-bordered" id="pwd">
                    <div class="panel-body nopadding">
                        <div class="form-group" style="padding-left: 0px">
                            <label class="col-sm-1 control-label" style="width: 5%"><span class="text-danger">*</span> 名称</label>

                            <div class="col-sm-3">
                                <input id="name" type="text" value="<?php echo isset($name) ? $name : '' ?>" class="form-control validate[required]">
                            </div>
                        </div>
                        <!-- form-group -->

                        <div class="form-group" style="padding-left: 0px">
                            <label class="col-sm-1 control-label" style="width: 5%"><span class="text-danger"></span> 说明</label>

                            <div class="col-sm-6">
                                <input id="desc" type="text" value="<?php echo isset($desc) ? $desc : '' ?>" class="form-control"/>
                            </div>
                        </div>
                        <!-- form-group -->

                        <div class="form-group">
                            <input type="hidden" id="pid" value="<?php echo $id ?>"/>
                            <input type="hidden" id="begintime" value="<?php echo date('Y-m-d') ?>"/>

                            <div class="row">
                                <div class="col-sm-8">
                                    <div id="storageCal"></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="panel panel-default">
                                        <div class="panel-footer">
                                            选择一些日期，设置以下价格、库存
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">散客结算价</label>

                                                <div class="col-sm-6">
                                                    <select id="s_type" class="select2" data-placeholder=""
                                                            style="width:100%;padding:0 10px;">
                                                        <option value="0">加价(元)</option>
                                                        <option value="1">降价(元)</option>
                                                        <!--option value="2">加价(%)</option>
                                                        <option value="3">降价(%)</option-->
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" id="s_price" name="s_price" class="spinner">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">团体价</label>

                                                <div class="col-sm-6">
                                                    <select id="g_type" class="select2" data-placeholder=""
                                                            style="width:100%;padding:0 10px;">
                                                        <option value="0">加价(元)</option>
                                                        <option value="1">降价(元)</option>
                                                        <!--option value="2">加价(%)</option>
                                                        <option value="3">降价(%)</option-->
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" id="g_price" name="g_price" class="spinner">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">日库存</label>

                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="day_storage"
                                                           name="day_storage">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer" id="configWrap">
                                            <button class="btn btn-primary mr5" id="configBtn">设置</button>
                                            日库存留空，表示库存不限
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- form-group -->


                    </div>
                    <!-- panel-body -->
                    <div class="panel-footer" id="saveWrap">
                        <button class="btn btn-primary mr5" type="button" id="saveBtn">保存</button>
                        <a class="btn btn-default" href="/ticket/strategy/">返回</a>
                    </div>
                </form>

            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->
</div>
<link href="/css/storageCal.css" rel="stylesheet">
<script src="/js/storageCal.js"></script>
<script>
    var dateSelected = [];
    var json = {
        "rules":<?php echo isset($rules) ? json_encode($rules) : '[]' ?>,
        "about": {"mintime": 1415027194, "maxtime": 1415027194, "lcode": 0, "totalStorage": -1}
    };

    $(document).ready(function() {

        (function() {
            storageCal.init.calDiv = $("#storageCal").get(0);
            storageCal.init.totalStorage = $("#total_storage").val();
            storageCal.init.totalStorageBegintime = $("#storage_open").val();
            storageCal.init.salesStorage = $("#sales").html();
            var pid = parseInt($("#pid").val());
            var year_month = $("#begintime").val().substr(0, 7);
            storageCal.show(year_month, pid, $("#begintime").val());
        })();

        $(".delete").click(function() {
            $(this).parent().parent().parent().find("input[type='text']").val("");

        });


        $("#configBtn").click(function() {
            var s_price = $("#s_price").val();
            var g_price = $("#g_price").val();
            var day_storage = $("#day_storage").val();
            if ((isNaN(s_price) || s_price <= 0)
                    && (isNaN(g_price) || g_price <= 0)
                    && (isNaN(day_storage) || day_storage <= 0)) {
                $("#day_storage").val('');
                alert("请设置一个有效的加减价或库存限制");
                return false;
            }

            var json = {};
            var hasChecked = 0;
            $("#storageCal tbody td input").each(function() {
                if (this.checked == false)
                    return;
                hasChecked = 1;
                var detail = $(this).parent().parent();
                dateSelected.push(detail.attr("date"));
            });
            if (hasChecked == 0) {
                alert("请选择日期");
                return false;
            }
            var json = {};
            json['params'] = dateSelected;
            if (!isNaN(s_price) && s_price > 0) {
                json['s_type'] = $('#s_type').val();
                json['s_price'] = s_price;
            }
            if (!isNaN(g_price) && g_price > 0) {
                json['g_type'] = $('#g_type').val();
                json['g_price'] = g_price;
            }
            if (!isNaN(day_storage) && day_storage > 0) {
                json['storage'] = day_storage;
            }
            json['pid'] = $("#pid").val();
            json['name'] = $('#name').val();
            json['desc'] = $('#desc').val();
            var wrap = $('#configWrap').html();
            $.ajax({
                url: "/ticket/strategy/commit",
                type: "POST",
                dataType: "json",
                data: json,
                beforeSend: function() {
                    $('#configWrap').html('<img alt="" src="/img/loaders/loader1.gif">');
                },
                success: function(result) {
                    if (result.code == 200) {
                        //var year_month = $("#storageCalContent input.year_month").first().val();
                        //delete(storageCal.data[year_month]);
                        alert("设置成功");
                        location.href = '/#'+ '/ticket/strategy/amend/id/' + result.id;
                        //storageCal.show(year_month, $("#pid").val(), $("#begintime").val());
                    } else {
                        alert(result.message);
                    }
                    $('#configWrap').html(wrap);
                }
            });
            return false;
        });

        $('#saveBtn').click(function() {
            if ($('#name').val() == '') {
                alert('名称不能为空');
                return false;
            }
            var json = {};
            json['pid'] = $("#pid").val();
            json['name'] = $('#name').val();
            json['desc'] = $('#desc').val();
            var wrap = $('#saveWrap').html();
            $.ajax({
                url: "/ticket/strategy/commit",
                type: "POST",
                dataType: "json",
                data: json,
                beforeSend: function() {
                    $('#saveWrap').html('<img alt="" src="/img/loaders/loader1.gif">');
                },
                success: function(result) {
                    if (result.code == 200) {
                        //var year_month = $("#storageCalContent input.year_month").first().val();
                        //delete(storageCal.data[year_month]);
                        alert("保存成功",function(){location.href = '/#'+ '/ticket/strategy/amend/id/' + result.id;});
                        //storageCal.show(year_month, $("#pid").val(), $("#begintime").val());
                    } else {
                        alert(result.message);
                    }
                    $('#saveWrap').html(wrap);
                }
            });

            return false;
        });


        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });


        var spinner = jQuery('.spinner').spinner({'min': 1});
        //spinner.spinner('value', 1);
        //因url写死，改放在调用页面处理
        $('.rule-remove-btn').click(function(){
            var date = $(this).parent().parent().attr('date');
		    PWConfirm('确认要删除"+date+"的规则设定吗？',function(){
			       var pid = $("#pid").val();
                $.get('/ticket/strategy/delete', {id: pid, date: date}, function(result){
                    if (result == 1) {
                        location.href = '/#'+ '/ticket/strategy/amend/id/'+pid;
                    }
                });
            });
        });
    });
</script>
