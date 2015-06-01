<?php
use common\huilian\utils\Format;

?>
<div class="contentpanel">
    <ul class="nav nav-tabs">
        <li class="<?= $status == 0 ? 'active' : '' ?>"><a href="/message/notice/" ><strong>全部</strong></a></li>
        <li class="<?= $status == 1 ? 'active' : '' ?>"><a href="/message/notice/?status=1"><strong>未读</strong><?= $unreadNum ? '<span class="badge" style="background:red;" id="unread">' .$unreadNum. '</span>' : '' ?></a></li>
        <li class="<?= $status == 2 ? 'active' : '' ?>"><a href="/message/notice/?status=2"><strong>已读</strong></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="home">
            <div id="t1" class="tab-pane active">
                <div class="panel panel-default">
                    <table class="table  table-striped">
                        <thead>
                         <tr>
				            <th width="100">
				                <div class="ckbox ckbox-primary" style="margin-left:17px;">
				                    <input type="checkbox" class="ids" id="checkbox-allcheck">
				                    <label for="checkbox-allcheck" class="allcheck">全选</label>
				                </div>
				            </th>
				            <th width="200">
                                <a class="btn btn-primary btn-sm" href="javascript:void(0)" id="delete-all"> 删除</a>
                                <?php if(!isset($status) || $status != 2) {?>
                                <a class="btn btn-primary btn-sm clearPart" href="javascript:void(0)" id="update-all" style="margin-left: 20px"> 设为已读</a>
                                <?php }?>
				            </th>
				            <th width="450"></th>
				            <th width="100"></th>
				            <th width="80"></th>
				        </tr>
                        </thead>
                        <tbody>
                        	<?php foreach($messages as $message) { ?>
             				<tr>
             					<td>
				                    <div class="ckbox ckbox-primary" style="margin-left: 17px;">
				                        <input type="checkbox" class="ids" id="checkbox<?php echo $message['id']?>" value="<?php echo $message['id']?>">
				                        <label for="checkbox<?php echo $message['id']?>"></label>
				                    </div>
				                </td>
             					<?= $status == 0 && !$message['is_read'] ?  '<td><b>用户提醒</b></td>' : '<td>用户提醒</td>' ?>
             					<?= $status == 0 && !$message['is_read'] ?  '<td><b>'.$message['content'].'</b></td>' : '<td>'.$message['content'].'</td>' ?>
             					<td><a href="/message/notice/view?id=<?= $message['id'] ?>">查看</a></td>
             					<td><a href="javascript:void(0);" data-id="<?php echo $message['id']?>" class="text-danger setDeleted clearPart">删除</a></td>
             				</tr>
             				<?php } ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align:center" class="panel-footer">
                    <?php
                    if(isset($pages)) {
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
                    }
                    ?>
                </div>
            </div>
        </div><!-- tab-pane -->
    </div>
</div>
<script>
$(function() {
	
	// 消息删除
    $('.setDeleted').click(function() {
        var id = Math.floor($(this).attr('data-id'));
		PWConfirm('确定要删除消息?',function(){
			$.post('/message/notice/delete', {
            'id': id
        }, function(data) {
            if (data.error == 0) {
                $('#message' + id).remove();
                window.location.partReload();
            } else {
                setTimeout(function() {
                    alert(data.msg);
                }, 500);
            }
        }, 'json');
        });
    });

	// 全选
	$('#checkbox-allcheck').click(function(){
		var obj = $(this).parents('table');
		if($(this).attr('checked')){
			obj.find('input').prop('checked', true)
			obj.find('tbody tr[class!="empty"]').addClass('selected')
			$('.allcheck').text('全不选')
		}else{
			obj.find('input').prop('checked', false)
			obj.find('tbody tr').removeClass('selected')
			$('.allcheck').text('全选')
		}
	})
    $('.ids').click(function(){
        $(this).parents('tr').toggleClass('selected');
    });
    

    //批量删除
    $('#delete-all').click(function(){
        var ids = "";

        $(".selected").each(function(i){
            if(ids != "") {
                ids += ',';
            }
            var a = $(this).find('input[type="checkbox"]').val();
            if(a!="" || a!="undefined" || a!="on"){
                ids += $(this).find('input[type="checkbox"]').val();
            }
        })

        if(ids==""){
            alert("请先选中要删除的消息");return false;
            //var delMsg = "请先选中要删除的消息";
        }else{
            var delMsg = "确定要删除选中消息" 
        }
		PWConfirm(delMsg,function(){
            //console.log(ids);return false;
    		$.post('/message/notice/updateall/',{ids : ids, type : 'del'},function(data){
                if (data.error) {
                    setTimeout(function() {
                        alert(data.msg);
                    }, 500);
                } else {
                    setTimeout(function() {
                        alert(data.msg, function() {
                            window.location.partReload();
                        });
                    }, 500);
                }
            },'json');
        });
		return false;
    });

    //批量设置已读
    $('#update-all').click(function(){
        var ids = "";
        $(".selected").each(function(i){
            if(ids != "") {
                ids += ',';
            }
            var a = $(this).find('input[type="checkbox"]').val();
            if(a!="" || a!="undefined" || a!="on"){
                ids += $(this).find('input[type="checkbox"]').val();
            }
        })

        if(ids==""){
            alert("请先选中要设置已读的消息");return false;
        }else{
            var delMsg = "确定要将选中消息设置已读"
        }
        PWConfirm(delMsg,function(){
            $.post('/message/notice/updateBatch/',{ids : ids},function(data){
                if (data.error) {
                    setTimeout(function() {
                        alert(data.msg);
                    }, 500);
                } else {
                    setTimeout(function() {
                        alert(data.msg, function() {
                            window.location.partReload();
                        });
                    }, 500);
                }
            },'json');
        });
    });

});
</script>
