<section>
<div class="contentpanel">
<style>
    .table tr>*{
        text-align:center
    }
    .box{
        border: 1px solid #ccc;
        width: 100%;
        min-height: 300px;
        padding-left: 0px;
    }
    .box li{
        padding-left: 10px;
        list-style: none;
        height: 2em;
        line-height: 2em;
    }
    .box li:hover{
        cursor: pointer;
    }
    .add{
        background: #ccc;
    }

</style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                发布产品3
            </h4>
        </div>
        <div class="panel-body" style="padding-left: 50px;">
            <h2>步骤3，产品价格库存制定，选填
            </h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <form class="form-horizontal form-bordered" id="pwd">
                            <div class="panel-body nopadding">
                                <div class="form-group">
                                    <input type="hidden" id="pid" value="<?php echo $id ?>"/>
                                    <input type="hidden" id="begintime" value="<?php echo date('Y-m-d') ?>"/>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div id="storageCal"></div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    选择日期，设置价格和库存
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 control-label" for="sprice">散客价：</label>
                                                        <div class="col-md-9">
                                                            <input type="text" id="s_price" class="col-md-8" name="s_price"><label class="col-sm-1">元</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">

                                                        <label class="col-sm-3 control-label" for="lprice">团体价：</label>
                                                        <div class="col-md-9">
                                                            <input type="text" id="g_price" class="col-md-8" name="g_price"><label class="col-sm-1">元</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 control-label">日库存：<br/></label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" id="day_storage" name="day_storage">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-footer">
                                                    <button class="btn btn-primary mr5" id="configBtn">设置</button>
                                                    日库存留空，表示库存不限
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- form-group -->
                            </div><!-- panel-body -->
                            <div class="panel-footer">
                                <button class="btn btn-primary mr5">保存</button>
                                <button class="btn btn-default" type="reset">取消</button>
                            </div>
                        </form>

                    </div><!-- panel -->

                </div><!-- col-md-6 -->
            </div><!-- row -->

        </div><!-- panel-body -->
    </div>
</div><!-- contentpanel -->

</section>

<script src="/js/jquery-ui-1.10.3.min.js"></script>
<link href="/css/storageCal.css" rel="stylesheet">
<script src="/js/Calendar.js"></script>
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
                json['s_price'] = s_price;
            }
            if (!isNaN(g_price) && g_price > 0) {
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
                url: "/ticket/product/commit",
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
                        location.href = '/ticket/strategy/amend/id/' + result.id;
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
                        alert("保存成功");
                       // location.href = '/ticket/strategy/amend/id/' + result.id;
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
        spinner.spinner('value', 1);


    });
</script>

