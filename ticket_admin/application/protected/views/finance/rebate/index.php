<?php 
$this->breadcrumbs = array('财务管理','充值优惠');
?>
      
      <div class="contentpanel">
        <div class="panel panel-default">
          <div class="panel-heading">
			<h4 class="panel-title">
				<button class="btn btn-primary btn-sm pull-right" onclick="modal_jump_add();" data-target=".modal-bank" data-toggle="modal">新增优惠方案</button>
				设置充值优惠方案
			</h4>
		</div>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="/finance/rebate/index" class="now1"><strong>充值优惠列表</strong></a></li>
            <li class=""><a href="/finance/rebate/index2" class="now2"><strong>充值优惠记录</strong></a></li>
        </ul>
        <div class="tab-content mb30">
            <!-- tab-pane -->      
            <div id="t1" class="tab-pane active">
              <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>方案名称</th>
                            <th>有效期</th>
                            <th>充值金额</th>
                            <th>抵用券金额</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(isset($lists)){
                            foreach ($lists as $item) {
                            
                            ?>                        
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><a data-toggle="modal" data-target=".modal-bank" onclick="modal_jump(this);" href="/finance/rebate/edit/?id=<?php echo $item['id']; ?>" style="cursor: pointer;cursor: hand;"><?php echo $item['title']; ?></a></td>
                                <td><?php echo date('Y-m-d',$item['start_time']).'至'.date('Y-m-d',$item['end_time']) ?></td>
                                <td><?php echo $item['num']; ?></td>
                                <td><?php echo $item['coupon'];?></td>
                                <td> 
                                    <a href="javascript:void(0)" class="changeedit" onclick="changeEdit('<?php echo  $item['id'] ?>','<?php echo $item['status']?>');
                                        return false;" <?php if($item['status'] ==1 ){ echo "style = 'color:red'";}?>><?php echo $item['status'] == 1 ?'开启':"关闭";?></a>
                                </td>
                            </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
            </div>
            <!-- tab-pane -->          
            <div id="t2" class="tab-pane">
                
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
    function modal_jump_add() {
        $('#verify-modal').html('');
        $.get('/finance/rebate/add/', function(data) {
            $('#verify-modal').html(data);

        });
    }
    function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }
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

