<?php 
$this->breadcrumbs = array('财务管理','充值优惠');
?>
      
      <div class="contentpanel">
        <div class="panel panel-default">
          <div class="panel-heading">
			<h4 class="panel-title">
				设置充值优惠方案
			</h4>
		</div>
        </div>
        <ul class="nav nav-tabs">
            <li class=""><a href="/finance/rebate/index" class="now1"><strong>充值优惠列表</strong></a></li>
            <li class="active"><a href="/finance/rebate/index2" class="now2"><strong>充值优惠记录</strong></a></li>
        </ul>
        <div class="tab-content mb30">
            <!-- tab-pane -->      
            <div id="t1" class="tab-pane active">              
            </div>
            <!-- tab-pane -->          
            <div id="t2" class="tab-pane active">
                <div class="table-responsive">
                    <form class="form-inline" method="get" action="/finance/rebate/index2">
                        <div class="form-group" style="margin: 0 0 10px 0;">
						<input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="start_time" value="<?php echo isset($_GET['start_time'])?$_GET['start_time']:''; ?>" placeholder="开始日期" type="text" readonly="readonly">~
					</div>
					<!-- form-group -->
					<div class="form-group" style="margin: 0 0 10px 0;">
						<input style="cursor: pointer; cursor: hand; background-color: #ffffff" class="form-control datepicker" name="end_time" value="<?php echo isset($_GET['end_time'])?$_GET['end_time']:''; ?>" placeholder="结束日期" type="text" readonly="readonly">
					</div>
                    <div class="form-group" style="margin: 0 5px 10px 0">
						<input class="form-control" name="organization_name" value="<?php if (isset($_GET['organization_name'])) echo $_GET['organization_name']; ?>" placeholder="机构名称" type="text" style="width: 318px;">
					</div>


                    <div class="form-group" style="margin-bottom: 10px;">
                    <button  type="submit" class="btn btn-primary btn-sm pull-left">查询</button>
                </div>
            </form>
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>充值日期</th>
                            <th>机构</th>
                            <th>操作人</th>
                            <th>优惠方案</th>
                            <th>充值金额</th>
                            <th>抵用券金额</th>
                            <th>账户抵用券余额</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(isset($lists)){
                            foreach ($lists as $item) {
                            
                            ?>                        
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo date('Y-m-d',$item['paid_at']); ?></td>
                                <td><?php echo $item['organization_name']; ?></td>
                                <td><?php echo $item['created_name']; ?></td>
                                <td><?php echo $item['activity_title']; ?></td>
                                <td><?php echo $item['money']; ?></td>
                                <td><?php echo $item['coupon'];?></td>
                                <td><?php echo $item['coupon_total'];?></td>
                            </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
                <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    if (!empty($lists)) {
                        $this->widget('common.widgets.pagers.ULinkPager', array(
                                'cssFile' => '',
                                'header' => '',
                                'prevPageLabel' => '上一页',
                                'nextPageLabel' => '下一页',
                                'firstPageLabel' => '',
                                'lastPageLabel' => '',
                                'pages' => $pages,
                                'maxButtonCount' => 5, 
                            )
                        );
                    }
                    ?>
                </div>
            </div>
            </div>
               
        </div>
      </div>
      <!-- contentpanel -->   

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>
<script src="/js/bootstrap-wizard.min.js"></script>
<script src="/js/fenxiao.js"></script>
<?php if(isset($tab)): ?>
<script>
    $(document).ready(function() {
        $('.nav-tabs a[href="#t<?php echo $tab; ?>"]').tab('show');
    });
</script>
<?php endif; ?>
<script type="text/javascript">
   
    function changeEdit(id,status){
     $.post('/finance/rebate/change/',{id:id,status:status},function(data){
         if(typeof  data.errors != 'undefined'){
             alert(data.errors.msg);
         }else {
             if( status == 1){
                alert('关闭成功',function(){window.location.partReload();});
             }else{
                alert('开启成功',function(){window.location.partReload();});
             }
         }
    },'json');
}

</script>

