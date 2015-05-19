<div id="modal5" style="width:1000px;" class="modal-dialog">
    <div class="modal-content" >
        <div class="modal-header">
            <button id="close_rule" class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
            <h4 class="modal-title">&nbsp;</h4>
        </div>
        <div class="modal-body" >
            <div class="panel panel-default">
                <form class="form-horizontal form-bordered" id="pwd">
                    <div class="panel-body nopadding">                        
                        <!-- form-group -->
                        <div class="form-group">
                            <input type="hidden" id="ptid" value="<?php echo $ptid; ?>"/>
                            <input type="hidden" id="pid" value="<?php echo $rid; ?>"/>
                            <input type="hidden" id="begintime" value="<?php echo date('Y-m-d') ?>"/>
                            <input id="name" type="hidden" value="<?php echo isset($name) ? $name : '' ?>" >
                            <input id="desc" type="hidden" value="" >

                            <div class="row">
                                <div class="col-sm-8">
                                    <div id="storageCal"></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="panel panel-default" style="font-size: 13px;">
                                        <div class="panel-footer show_prompt">
                                            选择一些日期，设置以下价格、库存
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">散客价</label>

                                                <div class="col-sm-6">
                                                    <select id="s_type" class="select2" data-placeholder=""
                                                            style="width:100%;height: 30px;padding:0 10px;">
                                                        <option value="0">加价(元)</option>
                                                        <option value="1">降价(元)</option>
                                                        <!--option value="2">加价(%)</option>
                                                        <option value="3">降价(%)</option-->
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" id="s_price" name="s_price" class="spinner" value="0">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">团体价</label>

                                                <div class="col-sm-6">
                                                    <select id="g_type" class="select2" data-placeholder=""
                                                            style="width:100%;height: 30px;padding:0 10px;">
                                                        <option value="0">加价(元)</option>
                                                        <option value="1">降价(元)</option>
                                                        <!--option value="2">加价(%)</option>
                                                        <option value="3">降价(%)</option-->
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" id="g_price" name="g_price" class="spinner" value="0">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">日库存</label>

                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="day_storage"
                                                           name="day_storage">
                                                    <span style="line-height: 30px;">日库存留空，表示库存不限</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer" id="configWrap">
                                            <button class="btn btn-primary mr5" id="setinvBtn">设置</button><br />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- form-group -->
                    </div>
                    <!-- panel-body -->
                </form>
            </div>
            <!-- panel -->
        </div>
        <!-- col-md-6 -->
        <div class="modal-footer" style="text-align: left;">
            <button class="btn btn-primary mr5" type="button" id="saveBtn">保存</button>
        </div>
    </div>
    <!-- row -->
</div>

<link href="/css/storageCal.css" rel="stylesheet">
<script src="/js/storageCal.js"></script>
<script type="text/javascript">
       
    var dateSelected = [];
    var json = {
        "rules":<?php echo isset($data['rules']) ? json_encode($data['rules']) : '[]' ?>,
        "about": {"mintime": 1415027194, "maxtime": 1415027194, "lcode": 0, "totalStorage": -1}
    };

    $(document).ready(function() {        

    (function() {
            storageCal.init.calDiv = $("#storageCal").get(0);
            storageCal.init.totalStorage = $("#total_storage").val();
            storageCal.init.totalStorageBegintime = $("#storage_open").val();
            storageCal.init.salesStorage = $("#sales").html();
            var ptid = parseInt($("#ptid").val());
            var year_month = $("#begintime").val().substr(0, 7);
            storageCal.show(year_month, ptid, $("#begintime").val());
        })();
        $(".delete").click(function() {
            $(this).parent().parent().parent().find("input[type='text']").val("");

        });        

        $('#saveBtn').click(function() {
            if ($('#name').val() == '') {
                alert('名称不能为空');
                return false;
            }
            if (parseInt($("#pid").val()) == 0) {
                alert('请先设置价格、库存数据');
                return false;
            }
            var json = {};
            json['ptid'] = $("#ptid").val();
            json['rid'] = $("#pid").val();
            json['name'] = $('#name').val();
            json['desc'] = $('#desc').val();
            var wrap = $('#saveWrap').html();
            $.ajax({
                url: "/ticket/single/saveInvetory",
                type: "POST",
                dataType: "json",
                data: json,
                beforeSend: function() {
                    $('#saveWrap').html('<img alt="" src="/img/loaders/loader1.gif">');
                },
                success: function(result) {
                    if (result.code == 200) {
                    	alert('保存成功', function() {
							$('#modal5').css('display', 'none');
                    		setInterval(function() {
                        		window.location.reload();
                            }, 2000);
                        });
                    	
                    } else {
                    	$('#saveBtn').PWShowPrompt(result.message);
                    }
                    $('#saveWrap').html(wrap);
                }
            });

            return false;
        });

        var spinner = jQuery('.spinner').spinner({'min': 0});
        $(document).on('keyup','#day_storage',function(){
            var dstor = parseInt($(this).val());
            if(isNaN(dstor)){
                $(this).val('');
            }else{
                $(this).val(dstor)
            }
        });
        $(document).on('blur','#day_storage',function(){
            var dstor = parseInt($(this).val());
            if(isNaN(dstor)){
                $(this).val('');
            }else{
                $(this).val(dstor)
            }
        });

        $(document).on('click','.rule-remove-btn',function(){
            var date = $(this).parent().parent().attr('date');
			
		PWConfirm("确认要删除"+date+"的规则设定吗？",function(){
			  var pid = $("#pid").val();
            $.get('/ticket/single/delete', {id: pid, date: date}, function(result){
                result = JSON.parse(result);
                if (result.code == 200) {
                    json.rules = result.dateSelected;
                    storageCal.init.calDiv = $("#storageCal").get(0);
                    storageCal.init.totalStorage = $("#total_storage").val();
                    storageCal.init.totalStorageBegintime = $("#storage_open").val();
                    storageCal.init.salesStorage = $("#sales").html();
                    var ptid = parseInt($("#ptid").val());
                    var year_month = $(".year_month").val().substr(0, 7);
                    storageCal.show(year_month, ptid, $("#begintime").val());
                }
            });
        });
            

        });

    });

    $('.close').on('click', function () {

    });
    
</script>