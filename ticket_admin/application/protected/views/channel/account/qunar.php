<?php
$this->breadcrumbs = array('渠道对接', '去哪儿账号管理');
?>
<style>
.table-bordered th {
    line-height: 2em !important;
}
.table-bordered th,
.table-bordered td {
    vertical-align: middle !important;
}
.table-bordered a:hover {
    text-decoration: none;
}
</style>
<div class="contentpanel">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="/channel/account/">淘宝</a></li>
        <li role="presentation" class="active"><a href="/channel/account/qunar">去哪儿</a></li>
    </ul>
	<div class="panel panel-default">
        <div class="panel-body">
            
            <form class="form-inline" method="post" action="/channel/account/Qunar">
                <div class="form-group">
					<select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;" name="organization_id">
				       		<option value="">分销商名称</option>
                                       <?php if(isset($organizations)&&!empty($organizations)){ 
                                                foreach($organizations as $k => $v){
                                        ?>
                                                <option value="<?php echo $k;?>" <?= $k == $organization_id ? ' selected' : '' ?>><?php echo $v;?></option>
                                       <?php } }?>         
					</select>                   
				</div>
                <div class="form-group">
					<input type="text" style="width:150px;" placeholder="去哪儿账号" value="<?= isset($account)?$account:''; ?>" name="account" class="form-control">
				</div>
				<div class="form-group">
					<select class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;" name="status">
						<option value="">审核状态</option>
                        <option value="1" <?= $status === 1 ? ' selected' : '' ?>>已审核</option>
						<option value="0" <?= $status === 0 ? ' selected' : '' ?>>未审核</option>
					</select>
				</div>
                <div class="form-group">
				      <button class="btn btn-primary btn-sm" type="submit">查询</button>
                </div>
            </form>
            
        </div><!-- panel-body -->
    </div>
    <style>
    .table1 tr>*{
		text-align:center
	}
	
	</style>
	<table class="table table-bordered table1 mb30">
		<thead>
		  <tr>
            <th>分销商名称</th>
            <th>去哪儿账号</th>
            <th>去哪儿供应商ID</th>
            <th>接口用户名</th>
            <th>接口密码</th>
            <th>状态</th>
			<th>操作</th>
		  </tr>
		</thead>
		<tbody>
            <?php if(isset($qunar)&&!empty($qunar)){ 
			 foreach ($qunar as $value){?>
            <tr>
                <td><?php echo empty($value['name'])?'暂无':$value['name']; ?></td>
                <td><?php echo empty($value['account'])?'暂无':$value['account']; ?></td>
                <td><?php echo empty($value['ext']['supplier_id'])?'暂无':$value['ext']['supplier_id']; ?></td>
                <td><?php echo empty($value['ext']['api_account'])?'暂无':$value['ext']['api_account']; ?></td>
                <td><?php echo empty($value['ext']['api_password'])?'暂无':$value['ext']['api_password']; ?></td>
                <td><?php echo 0 == $value['status'] ? '未审核' : '已审核';?></td>
                <td>
                    <?php if (0 == $value['status']){ ?>
                        <a onclick="qunar_status('<?php echo $value['id']; ?>', 1);" href="javascript:void(0);" class="btn btn-success btn-bordered btn-xs clearPart" style="border-width: 1px">审核通过</a>
                    <?php } ?>
                </td>
              </tr>
                   <?php } }else{?>
					<tr><td colspan="4" style="text-align:center">暂无数据</td></tr>
            <?php }?>
        </tbody>
	</table>
    
    <div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
        <?php
            $this->widget('common.widgets.pagers.ULinkPager', array(
                'cssFile' => '',
                'header' => '',
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'firstPageLabel' => '',
                'lastPageLabel' => '',
                'pages' => $pages,
                'maxButtonCount' => 3, //分页数量
            ));
        ?>
    </div>
    
</div><!-- contentpanel -->
<script type="text/javascript">
$(function() {
    jQuery('.select2').select2({
        minimumResultsForSearch: -1
    });
});
function qunar_status($id, $status)
{ 
    PWConfirm('确定要通过审核么？', function () {
        $.post('/channel/account/Accountaudit', {id:$id,status:$status},function(data){
            setTimeout(function() {
                alert(data.msg, function() {
                    if(0 === data.error){
                        location.partReload();
                    }
                });
            }, 500);
        },"json");
    });
}
</script>