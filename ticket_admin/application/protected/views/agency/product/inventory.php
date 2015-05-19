<style type="text/css">
#modal5 .rule-remove-btn {display:none;}
#modal5 .ckbox label:before {border:none;}
#modal5 #checkAll {display:none;}

</style>

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
                        	<input type="hidden" id="org_id" value="<?= $_GET['org_id'] ?>" />
                            <input type="hidden" id="ptid" value="<?php echo $ptid; ?>"/>
                            <input type="hidden" id="pid" value="<?php echo $rid; ?>"/>
                            <input type="hidden" id="begintime" value="<?php echo date('Y-m-d') ?>"/>
                            <input id="name" type="hidden" value="<?php echo isset($name) ? $name : '' ?>" >
                            <input id="desc" type="hidden" value="" >

                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="storageCal"></div>
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
            json['org_id'] = $("#org_id").val();
            json['ptid'] = $("#ptid").val();
            json['rid'] = $("#pid").val();
            json['name'] = $('#name').val();
            json['desc'] = $('#desc').val();
            var wrap = $('#saveWrap').html();
            $.ajax({
                url: "/agency/product/saveInventory",
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
                        		window.location.partReload();
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
            return;
            var date = $(this).parent().parent().attr('date');
			PWConfirm("确认要删除"+date+"的规则设定吗？",function(){
				var pid = $("#pid").val();
				var org_id = $("#org_id").val();
	            $.get('/agency/product/clearSomedayInventory', {id: pid, date: date, org_id:org_id}, function(result){
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
    $(function() {
		$('label[for="checkAll"]').css('display', 'none');
    })
    
</script>