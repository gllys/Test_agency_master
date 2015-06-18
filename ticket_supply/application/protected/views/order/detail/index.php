<?php
$this->breadcrumbs = array('订单', '订单详情');
?>

<?php if(empty($detail)) { ?>
<div class="contentpanel">不存在该订单</div>
<?php } else { ?>
<div class="contentpanel">
<!--<?php if(isset($landscape)):?>
	<h4 class="lg-title"><?php echo $landscape['name']?></h4>
<?php endif;?> -->
    
	<?php if(isset($detail)):
		$order_item = current($detail['order_items']);
	?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">订单信息</h4>
		</div>
		<style>
		.table-responsive th,.table-responsive td{
			vertical-align:middle!important
		}
		.panel-footer b{
			font-size:22px;
			padding:0 5px;
		}
		#t1 th{
			text-align:right
			}
		#t1 td{
			text-align:left
			}
		</style>
		<div class="panel-body">
			<div class="table-responsive mb10">
                            <table class="table table-bordered" id="t1">
				<tbody>
				  <tr>
                      <?php if ($detail['partner_type'] == 1) : ?>
                          <th width="150">订单号：</th>
                          <td colspan="5"><?php echo $detail['id']; ?></td>
                          <th width="150">大漠订单号：</th>
                          <td colspan="10"><?php echo $detail['partner_order_id']; ?></td>
                      <?php else : ?>
                          <th width="150">订单号：</th>
                          <td colspan="15"><?php echo $detail['id']; ?></td>
                      <?php endif;?>
                                 </tr>
                                  <tr>
					  <th width="150">订单状态：</th>
					  <td colspan="15"><?php echo $status_labels[$detail['status']] ?></td>
				  </tr>
				  <tr  <?php echo in_array($detail['status'],array('paid','finish','billed'),true)? '' : 'hidden'; ?>>
					  <th>支付时间：</th>
					  <td colspan="15"><?php echo date("Y-m-d H:i:s",$detail['pay_at']);?></td>
                                    </tr>
                                  <tr <?php echo in_array($detail['status'],array('paid','finish','billed'),true)? '' : 'hidden'; ?>>
					  <th>支付方式：</th>
                      <td colspan="15"><?php if(!empty($detail['payment'])){echo $paid_type[$detail['payment']];}?></td>
				  </tr>
				  <tr>
					  <th>预定时间：</th>
					  <td colspan="15"><?php echo date('Y-m-d H:i:s',$detail['created_at'])?></td>
                                  </tr>
                                     <tr>
					  <th>游玩时间：</th>
					  <td colspan="15"><?php echo date('Y-m-d',strtotime($detail['use_day']))?></td>
				  </tr>
                  <tr>
                      <th>入园时间：</th>
                      <?php
                      $dateNums = array();
                      if(is_array($detail['order_items'])) {
                          foreach($detail['order_items'] as $val) {
                              // 表示已使用的状态
                              if ($val['status'] == 2) {
                                  $usedDate = date('Y-m-d H:m:s', $val['use_time']);
                                  if(!isset($dateNums[$usedDate])) {
                                      $dateNums[$usedDate] = 1;
                                  } else {
                                      $dateNums[$usedDate] += 1;
                                  }
                              }
                          }
                      }
                      ?>
                      <td colspan="15">
                      <?php foreach($dateNums as $key => $val):?>
                          <?php echo $key." &nbsp;&nbsp;".$val."张<br/>";?>
                      <?php endforeach;?>
                      </td>
                  </tr>
				  <tr>
					  <th>订单类型：</th>
					  <td colspan="15">电子票</td>
				  </tr>
				<tr <?php echo ($detail['nums'] - $detail['used_nums'] - $detail['refunding_nums'] - $detail['refunded_nums'])== 0?'hidden':'';?>>
					  <th>未使用张数：</th>
					  <td colspan="15"><?php echo $detail['nums'] - $detail['used_nums'] - $detail['refunding_nums'] - $detail['refunded_nums']?>张</td>
					  </tr>
                                     <tr <?php echo $detail['used_nums'] == 0? 'hidden' : '';?>>
                                          <th>已使用张数：</th>
					  <td colspan="15"><?php echo $detail['used_nums'] == 0? '0' : $detail['used_nums']?>张</td>
				  </tr>
				  <tr <?php echo $detail['refunding_nums'] == 0? 'hidden' : '';?>>
					  <th>退款中张数：</th>
					  <td colspan="15"><?php echo $detail['refunding_nums'] == 0? '0' : $detail['refunding_nums']?>张</td>
					  </tr>
                                     <tr <?php echo $detail['refunded_nums'] == 0? 'hidden' : '';?>>
                                          <th>已退款张数：</th>
					  <td colspan="15"><?php echo $detail['refunded_nums'] == 0? '0' : $detail['refunded_nums']?>张</td>
				  </tr>
				</tbody>
			  </table>
			</div>
			<div class="table-responsive">
			  <table class="table table-bordered">
				<thead>
				  <tr>
					<th>门票名称</th>
					<th>门票张数</th>
					<th><?php echo $detail['price_type'] == 0 ? '散客结算价' : '团队结算价'?></th>
					<th>当日价格</th>
					<th>总计</th>
				  </tr>
				</thead>
				<tbody>
	                <tr>
                        <td><?php 
                         echo $detail['name'] ?></td>
                        <td><?php echo $detail['nums']?></td>
                        <td><del><?php echo $detail['price_type'] == 0 ? $detail['fat_price'] : $detail['group_price'] ?></del></td>
                        <td><?php echo $detail['price']?></td>
                        <td><?php echo number_format($detail['amount'],2);?></td>
                    </tr>
				</tbody>
			  </table>
			</div>
		</div>
		<?php endif;?>
		<div class="panel-footer" style="text-align:right" id="take-ticket-footer">
                    <span style="margin-right:30px">合计票数:<b class="text-danger" id="totalnum"><?php echo $detail['nums']?></b>张</span>
			<span style="margin-right:30px">合计支付金额:<b class="text-danger"><?php echo $detail['amount']?></b>元</span>
			
			<?php if ($detail['status'] == 'unaudited' ) : ?>
            <!--/**
             *hefeng
             *2015-01-29
             *新增 确定 驳回 按钮 unaudited
             **/-->
            
                <a id="isConfirm" href="javascript:;" class="btn btn-primary">确定</a>
                <button class="btn btn-danger" id="una" type="button" data-toggle="modal" data-target="#myModal" >驳回</button>
            <?php endif; ?>
		</div>
	</div>
	
	<!-- <div class="panel panel-default">
		<div class="panel-body form-inline">
                    <form action="/order/detail/apply" method="post" id="applyform">
                        <input type="hidden" name="order_id" value="<?php echo $detail['id']?>">
			<div class="form-group">
                            申请退票张数 <input type="text" name="nums" class="form-control" style="width:50px" id="ticketmun"> 当前未使用可退票张数：<?php echo $detail['nums']?>张
			</div>
			<div class="form-group">
                            退票理由: <input type="text" name="remark" class="form-control" style="width:400px" id="applyremark"> <button class="btn btn-primary btn-xs" type="button" id="applysub">申请退款</button>
			</div>
                    </form>
		</div>
	</div>-->
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">取票人信息</h4>
		</div>
	  <table class="table table-bordered">
		<thead>
		<tbody>
		  <tr>
			<td>取票人姓名：<?php echo $detail['owner_name']?></td>
			<td>取票人手机号码：<?php echo $detail['owner_mobile'];?>
				<?php if($detail['status'] == 'paid'){ ?>
				<button id="complexConfirm" class="btn btn-primary btn-xs ml10" <?php if($detail['message_open']===0): ?>style="display:none;"<?php endif; ?> type="button">重发短信</button></td>
			  <?php  }  ?>
			</td>
                        <td style=" font-weight: bold;<?php if($detail['message_open']===0): ?>display:none;<?php endif; ?>">是否发送成功：<?php echo $detail['send_sms_nums']>0?'<font color="green">成功</font>':'<font color="red">失败</font>'?></td>
			<td>取票人身份证号码：<?php echo $detail['owner_card']?></td>
		  </tr>
		</tbody>
	  </table>
	</div>
	<div class="row"> 
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">分销商信息</h4>
				</div>
			  <table class="table table-bordered">
				<thead>
				<tbody>
				  <?php $agencyInfo = Organizations::api()->show(array('id' => intval($ticket[key($ticket)]['distributor_id'])));?>  
				  <tr>
					<th width="150">用户名称：</th><td><?php echo $agencyInfo['body']['name']?></td>
				  </tr>
				  <tr>
					<th>操作人：</th><td><?php echo $detail['user_name']?$detail['user_name']:$agencyInfo['body']['contact']?></td>
				  </tr>
				</tbody>
			  </table>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">供应商信息</h4>
				</div>
			  <table class="table table-bordered">
				<thead>
				<tbody>
				  <?php $supplyInfo = Organizations::api()->show(array('id' => $ticket[key($ticket)]['supplier_id']));?> 
				  <tr>
					<th width="150">供应商名称：</th><td><?php echo $supplyInfo['body']['name']?></td>
				  </tr>
				  <tr>
					<th>操作人：</th><td><?php echo  !empty($order_item['user_name'])?$order_item['user_name']:$supplyInfo['body']['contact']?></td>
				  </tr>
				</tbody>
			  </table>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">订单备注</h4>
			</div>
			<div class="panel-body">
                <p><?php echo $detail['remark'];?></p>	
			</div><!-- panel-body -->
	</div>
	
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">可游玩景点</h4>
        </div>
        <div class="panel-body">
        <?php 
        if(isset($infos['ticket_infos'])){ foreach ($infos['ticket_infos'] as $item){?>
        <div class="table-responsive mb10">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2"><?php echo $item['sceinc_name'];?></th>
                    <th>退票张数： <?php echo $infos['refunded_nums'] * $item['num'];?></th></tr>
                <tr>
                    <th>景点</th><th>未使用张数</th><th>已使用张数</th>
                </tr>
                <?php 
                if(isset($infos['poi_counts'])) {
                foreach ((array)$infos['poi_counts'][$item['scenic_id']] as $poi){?>
                <tr>
                    <td><?php echo $infos['poi_names'][$poi['poi_id']];?></td>
                    <td><?php echo $poi['unuse_num'];?></td>
                    <td><?php echo $poi['used_num'];?></td>
                </tr>
                <?php }?>
                <?php } ?>
            </table>
        </div>
        <?php } }else{ echo '该景区已被删除';}?>
        </div>
    </div>
    
	<div class="panel panel-default">
		<table class="table table-bordered mb30">
		<tbody>
		<tr>
		<th width="120">产品说明</th>
		<td><?php echo $detail['ticket_template_remark']?></td>
		</tr>
		</tbody>
		</table>
	</div>	
</div><!-- contentpanel -->

 <div class="panel-footer" style="padding-left:5%">
    <button class="btn btn-default" type="button" onclick="javascript:history.go(-1);" id="export">返回</button>
</div>


<script>
$("#applysub").click(function(){
    var mun = $("#ticketmun").val();
    var total = $("#totalnum").html();  
    var leng = $("#applyremark").val().length; 
    if(isNaN(mun)){
        alert('申请退票数不可为非数字！');
    }else if (Number(mun) < 0 || Number(mun) > Number(total)) {
        alert('申请退票数填写出错了！');
    }else{
        if(leng>0){
            $("#applyform").submit();
        }else{
            alert("请填写退票理由！");
        }
    }
    
});
</script>

<script>
jQuery(document).ready(function(){


	$("#complexConfirm").confirm({
		title:"重发短信",
		text:'<label>请输入需要发送短信的手机号码</label><input type="text" placeholder="" class="form-control" value="<?php echo $detail['owner_mobile'] ?>" id="sms-input">',
		confirm: function(button) {
			var id = <?php echo $detail['id'] ?>;
			var mobile = $('#sms-input').val();
			if (mobile) {
				$.post('/order/detail/againSms', {id: id, mobile: mobile}, function(data) {
					alert(data.errors);
				}, "json");
			}

		},
		cancel: function(button) {

		},
		confirmButton: "确定",
		cancelButton: "取消",
		confirmButtonClass: 'btn-success'
	});


!function(){
var takeTicketFooter = $('#take-ticket-footer'),
allBtn = $('#all-btn'),
pay = 98.00

//全选
allBtn.click(function(){
	var obj = $(this).parents('table')
	if($(this).text() == '全选'){
		obj.find('input').prop('checked', true)
		obj.find('tbody tr').addClass('selected')
		$(this).text('反选')
	}else{
		obj.find('input').prop('checked', false)
		obj.find('tbody tr').removeClass('selected')
		$(this).text('全选')
	}
	total()
})
	
$('.del-btn').click(function(){
	$(this).parents('tr').remove()
	total()
	return false
})

$('.all-btn').click(function(){
	$(this).parents('tr').toggleClass('selected')
	total()
})


$('.num').keyup(function(){
	total()
})


function total(){
	var tickets,
	takeTicketNum=0
	tickets = $('#take-ticket tbody').find('.selected').length
	$('.selected .num').each(function(){
		takeTicketNum+=parseInt($(this).val())
	})
	takeTicketFooter.html('<span style="margin-right:30px">共计门票:<b class="text-danger">'+tickets+'</b>张</span><span style="margin-right:30px">	共计票数:<b class="text-danger">'+takeTicketNum+'</b>张</span><span style="margin-right:30px">总金额:<b class="text-danger">'+takeTicketNum * pay+'</b>元</span><button type="submit" class="btn btn-primary">生成订单</button>')
}


}()
              // Tags Input
        jQuery('#tags').tagsInput({width:'auto'});
         
        // Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();
        

        
        // Form Toggles
        jQuery('.toggle').toggles({on: true});
        

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
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined)?"":item.element[0].getAttribute('rel') ) + ' mr10"></i>' + item.text;
        }
        
        // This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');
        
        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) { return m; }
        });
        
        // Color Picker
        if(jQuery('#colorpicker').length > 0) {
            jQuery('#colorSelector').ColorPicker({
	onShow: function (colpkr) {
	    jQuery(colpkr).fadeIn(500);
                    return false;
	},
	onHide: function (colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
	},
	onChange: function (hsb, hex, rgb) {
	    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
	    jQuery('#colorpicker').val('#'+hex);
	}
            });
        }

        // Color Picker Flat Mode
        jQuery('#colorpickerholder').ColorPicker({
            flat: true,
            onChange: function (hsb, hex, rgb) {
	jQuery('#colorpicker3').val('#'+hex);
            }
        });
        
        
    });

</script>
<script type="text/javascript">
	/**
	 *打开弹窗，关闭弹窗，提交
	 *hefeng
	 *2015-1-30
	 **/
    $(function(){
        $("#s").on("click",function(){

            // body...
            if($.trim($("#rejectedContent").val())==""){
                alert("驳回理由不能为空");
                $("#rejectedContent").focus();
                return false;
            }
            var rejectedContent = $.trim($("#rejectedContent").val());
            var orderId = $.trim($("#olderId").val());
            var receiver_organization = <?php echo $ticket[key($ticket)]['distributor_id']?>;

            if(rejectedContent!="" && orderId!=""){
                $.post("/order/detail/rejected/",{orderId:orderId,receiver_organization:receiver_organization,rejectedContent:rejectedContent,t:Math.random()},function(data){
                    if(data.code=="succ"){
                        //alert("驳回成功");
                        //window.location.href= '/#'+data.url;

                        setTimeout(function(){
			                alert('驳回成功',function(){window.location.partReload();});
			            },500)
                    }else{
                        //alert("驳回失败");
                        //window.location.href= '/#'+data.url;
                        setTimeout(function(){
			                alert('驳回失败',function(){window.location.partReload();});
			            },500)
                    }
                } ,' json ');
            }
        })
    });
    $(function(){
        $("#isConfirm").on("click",function(){
            $.get("/order/detail/checkStatus/id/<?php echo $detail["id"] ?>"+"/t/"+Math.random(),function(data){
                if(data=="cancel"){
                   // alert("订单已经取消");
                   //location.partReload();
                    alert('订单已经取消',function(){window.location.partReload();});
                    return false;
                }else{
                    
                	PWConfirm("是否确定审核通过",function(){
                		$.get("/order/detail/confirm/id/<?php echo $detail["id"] ?>"+"/receiver_organization/<?php echo $ticket[key($ticket)]['distributor_id']?>"+"/t/"+Math.random(),function(data){
                            if(data.code=="succ"){
                         
                                setTimeout(function(){
			                        alert('审核成功',function(){window.location.partReload();});
			                    },500)
                            }else{
                                setTimeout(function(){
			                        alert('审核失败',function(){window.location.partReload();});
			                    },500)
                            }
                        },' json ');
                	});

                   /*if(confirm('是否确定审核通过')){
                        $.get("/order/detail/confirm/id/<?php echo $detail["id"] ?>"+"/receiver_organization/<?php echo $ticket[key($ticket)]['distributor_id']?>"+"/t/"+Math.random(),function(data){
                            if(data.code=="succ"){
                                alert("审核成功");
                                window.location.href= '/#'+data.url;
                            }else{
                                alert("审核失败");
                                window.location.href= '/#'+data.url;
                            }
                        },' json ');
                    }else{
                        return false;
                    }*/
                }
            })
			return false;
        });
        $("#una").on("click",function(){
            $.get("/order/detail/checkStatus/id/<?php echo $detail["id"] ?>"+"/t/"+Math.random(),function(data){
                if(data=="cancel"){
                    $("#myModal").hide();
                    //alert("订单已经取消");
                    //location.partReload();
                    alert('订单已经取消',function(){window.location.partReload();});
                    return false;
                }
            });
        	$("#rejectedContent").val("");
        });
    })

    function checkStatus(){
        $.get("/order/detail/checkStatus/id/<?php echo $detail["id"] ?>"+"/t/"+Math.random(),function(data){
                return data;
        })
    }
</script>
<!--
	**
	*驳回订单弹窗
	*hefeng
	*2015-1-30
	**-->
<style>
    .close {
        line-height: 0px;
    }
</style>
<form action="#" method="post" name="form1">
    <input type="hidden" value="<?php echo $detail['id']?>" id="olderId" name="olderId">
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">请输入驳回理由</h4>
            </div>
            <div class="modal-body">
                <textarea name="rejectedContent" id="rejectedContent" cols="51" rows="5" ></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancel" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button"  id="s" class="btn btn-primary">确定驳回</button>
            </div>
        </div>
    </div>
</div>
</form>
<?php } ?>
