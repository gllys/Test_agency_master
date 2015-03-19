<!DOCTYPE html>
<html>
<?php get_header(); ?>
<body>
<style type="text/css">
	.lel {margin-left: 0;line-height: 30px;text-align: right}
</style>
<?php get_top_nav(); ?>
<div class="sidebar-background">
	<div class="primary-sidebar-background"></div>
</div>
<?php get_menu(); ?>
<div class="main-content">
	<?php get_crumbs(); ?>

	<div id="show_msg"></div>
<style>
div.selector{
    margin:0 10px;
    width:200px;
    }
.tab-content{
	overflow:hidden
	}
.table-normal tbody td a{
    margin:0 5px;
    text-decoration:none;
    }
.table-normal button{
    min-width:inherit;
    }
.table-normal tbody td{
    text-align:center
    }
.btn-group ul {
    min-width: 80px;
}
.panel-body b{
	font-size:26px;
}
.panel-body>span{
	margin-right:30px;
}
b.red{
	color:#d9534f;
}
b.orange{
	color:#f0ad4e;
}
b.blue{
	color:#428bca;
}
</style>  
	<div class="container-fluid padded">
		<div class="box">
			<div class="box-header">
				<span class="title">应收账款</span>
			</div>
                    <div class="panel-body box-header"  style="height:50px;padding-bottom:10px;padding-top:10px;">
          <form class="form-inline" method="get" action="/bill_receivable.html">
                <div class="form-group" style="margin:0;float:left;">
                    <span class="title">账单日期：</span>
                   <input type="text" placeholder="" name="update_time" style="width:150px;margin:0 10px 0" class="form-time" value="<?php echo $get['update_time'] ?>">
                </div><!-- form-group -->
                <div class="form-group" style="margin:0;float:left;">
                    <span class="title">支付状态：</span>
                        <select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;" name="pay_state">
                                <option value="" >支付状态</option>
                                <option value="1" <?php echo $get['pay_state'] =='1'?'selected=selected':'';?>>已打款</option>
                                <option value="0" <?php echo $get['pay_state'] =='0'?'selected=selected':'';?>>未打款</option>
                        </select>
                </div>
                <div class="form-group" style="margin: 0 5px 0 10px;float:left;" >
                    <input class="form-control" placeholder="请输入供应商名称"type="text" style="width:200px;" name="supply_name" value="<?php echo $get['supply_name'] ?>">
                    <input class="form-control" placeholder="请输入分销商名称"type="text" style="width:200px;" name="agency_name" value="<?php echo $get['agency_name'] ?>">
                </div>
                <button class="btn btn-primary btn-xs" type="submit">查询</button>
            </form>
        </div><!-- panel-body -->
			<div class="content" style="overflow-x: auto">
				<table class="table table-normal">
					<thead>
					<tr>
                                                <td>结算单号</td>
                                                <td>供应商</td>
                                                <td>分销商</td>
                                                <td>账单生成日期</td>
                                                <td>账单类型</td>
                                                <td>应付金额</td>
                                                <td>订单张数</td>
                                                <td>支付状态</td>
                                                <td>操作</td>
                                          </tr>
					</thead>
					<tbody>
                                                <?php if(isset($bill)):?>
                                                        <?php foreach ($bill as $value):?>
                                                  <tr>
                                                        <td><?php echo $value['id']?></td>
                                                        <td><?php echo $value['supply_name'];?></td>
                                                        <td><?php echo $value['bill_type'] == 1 ? '汇联' :($value['agency_name'] == '-'?'汇联':$value['agency_name']) ?></td>
                                                        <td><?php echo date('20y年m月d日',$value['created_at'])?></td>
                                                        <td>
                                                                <?php if($value['bill_type'] == 1){
                                                                        echo "在线支付";
                                                                        }elseif ($value['bill_type'] == 2) {
                                                                                echo "信用支付";
                                                                        }elseif ($value['bill_type'] == 3) {
                                                                                echo "储值支付";
                                                                         }else{
                                                                             echo "平台支付";
                                                                         }?>
                                                        </td>
                                                        <td><?php echo $value['bill_amount']?></td>
                                                        <td ><?php echo $value['bill_num']?>张</td>
                                                        <?php if($value['pay_status'] == 1 &&  $value['bill_amount'] > 0):?><td class="text-success">已打款</td><?php elseif( $value['bill_amount'] == 0):?><td class="text-warning">无需打款</td><?php else:?><td class="text-error">未打款</td><?php endif;?>
                                                        <?php ///if($value['receipt_status'] == 1):?><!--td class="text-success">已收款</td--><?php //else:?><!--td class="text-danger">未收款</td---><?php //endif;?>
                                                        <td>
                                                            <a  href="bill_detail_<?php echo $value['id']?>.html" ><button class="btn btn-default"><i class="icon-zoom-in"></i> 查看</button></a>
                             
                                                        </td>
                                                </tr>
                                                        <?php endforeach;?>
                                                        <?php else:?>
                                                                        <tr><td colspan="8" style="text-align:center">暂无数据</td></tr>
                                                <?php endif;?>
					</tbody>
				</table>
			</div>
			<div class="dataTables_paginate paging_full_numbers">
				<?php echo isset($pagination) ? $pagination : '';?>
			</div>
		</div>
	</div>
</div>


<script>
	$(function(){
		$('button.month-prev').bind('click', function() {
			var origin = $('#ym1').val();
			origin = origin.split('-');
			var year = Math.floor(origin[0]);
			var month = Math.floor(origin[1]);
			year = month > 1 ? year : year - 1;
			month = month > 1 ? month - 1 : 12;
			var days = new Date(year,month,0).getDate();//当月天数
			month = month < 10 ? '0' + month : month;
			days = days < 10 ? '0' + days : days;
			$('#ym1').val(year + '-' + month + '-01');
			$('#ym2').val(year + '-' + month + '-' + days);
		});
		$('button.month-next').bind('click', function() {
			var origin = $('#ym1').val();
			origin = origin.split('-');
			var year = Math.floor(origin[0]);
			var month = Math.floor(origin[1]);
			year = month < 12 ? year : year + 1;
			month = month < 12 ? month + 1 : 1;
			var days = new Date(year,month,0).getDate();
			month = month < 10 ? '0' + month : month;
			days = days < 10 ? '0' + days : days;
			$('#ym1').val(year + '-' + month + '-01');
			$('#ym2').val(year + '-' + month + '-' + days);
		});
		$('#btn_gen').click(function(){
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $('#filter_form').serialize(),
				url: '/bill_file.html',
				beforeSend: function() {
					$('#btn_gen').attr('disabled', 'disabled');
				},
				success: function(result) {
					$('#btn_gen').removeAttr('disabled');
					$('#btn_gen').addClass('btn-link');
					$('#btn_gen').text('下载报表（Excel）');
					$('#btn_gen').attr('data-link', result.link);
					$('#btn_gen').click(function(){
						location.href = result.link;
					});


				}
			});
		});
	});
</script>


<script src="Views/js/equipment/add.js?v=1"></script>
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js?v=2"></script>
<script>
$(document).ready(function() {
    $('.form-time').daterangepicker({
        format:'YYYY-MM-DD'
    })
})
</script>
</body>
</html>

