<style>
    .title{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 36em;}
    .badge{background-color:#fc3232;}
    .no-read td{font-weight:bold;}
    .table-bordered td span{margin-left:5px;}
    .table-bordered td a{font-weight:normal;}
</style>
<div class="contentpanel">
    <ul class="nav nav-tabs">
        <li class="<?php echo !isset($read_time) || $read_time == '' ? 'active' : ''?>"><a href="/system/message/view/type/<?php echo $type ?>"><strong>全部</strong></a></li>
        <li class="<?php echo $read_time == '0' ? 'active' : ''?>">
            <a href="/system/message/view/type/<?php echo $type ?>/read_time/0" style="float:none;" class="btn btn-sm"> 未读
                <?php if($unread_num > 0): ?><span class="badge" id="unread"><?php echo $unread_num; ?></span><?php endif;?>
            </a>
        </li>
        <li class="<?php echo $read_time == '1' ? 'active' : ''?>">
            <a href="/system/message/view/type/<?php echo $type ?>/read_time/1" style="float: none;" class="btn btn-sm">已读</a>
        </li>
    </ul>
    <div id="show_msg"></div>
    <div class="table-responsive" style="margin-bottom:30px;">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="80">
                    <div class="ckbox ckbox-primary" style="margin-left:17px;">
                        <input type="checkbox" class="ids" id="checkbox-allcheck">
                        <label for="checkbox-allcheck" class="allcheck">全选</label>
                    </div>
                </th>
                <th width="150">
                    <a class="btn btn-xm btn-default btn-bordered" id="delete-all"> 删除</a>
                </th>
                <th width="400" />
                <th width="100" />
                <th width="80" />
            </tr>
            </thead>
            <tbody id="staff-body">
            <?php if (isset($lists) && !empty($lists)) : foreach ($lists as $message) : ?>
            <tr class="<?php echo $message['read_time'] == 0 ? 'no-read' : ''?>">
                <td>
                    <div class="ckbox ckbox-primary" style="margin-left: 17px;">
                        <input type="checkbox" class="ids" id="checkbox<?php echo $message['id']?>" value="<?php echo $message['id']?>">
                        <label for="checkbox<?php echo $message['id']?>"></label>
                    </div>
                </td>
                <td>
                    <?php echo $message['title']?><span class="text-<?php echo $sys_class[$message['sys_type']]?>">(<?php echo $sys_name[$message['sys_type']]?>)</span>
                </td>
                <td>
                    <?php if($message['sys_type'] != 0):?>
                        <p id="setRead<?php echo $message['id']?>" style="cursor: pointer;cursor: hand;margin: 5px 0px;" class="Newclass setRead <?php echo $message['read_time'] == 0 ? 'font-bold' : ''?>"
                           data-id="<?php echo $message['id']?>"
                           data-food="<?php echo $message['read_time']?>">
                            <?php echo $message['content']?>
                        </p>
                    <?php else:?>
                        <p id="readAdvice<?php echo $message['id']?>" style="cursor: pointer;cursor: hand;margin: 5px 0px;" class="readAdvice <?php echo $message['read_time'] == 0 ? 'font-bold' : ''?> title"
                           data-id="<?php echo $message['id']?>"
                           data-name="<?php echo $message['organization_name']?>"
                           data-food="<?php echo $message['read_time'] == 0 ? 0 : date('Y年m月d日',$message['created_at'])?>"
                           data-time="<?php echo date('Y年m月d日',$message['created_at'])?>"
                           data-title="<?php echo $message['title']?>"
                            data-content='<?php echo strip_tags($message['content'],'<a><p><br/><br>'); ?>'>
                            <?php   $content = trim($message['content']);
                                    $content = htmlspecialchars_decode($content);
                                    $content = preg_replace("/<(.*?)>/","",$content);
                                    echo $content;
                            ?>
                        </p>

                    <?php endif;?>
                </td>
                <td><?php echo date('Y-m-d',$message['created_at'])?></td>
                <td><a href="javascript:;" data-id="<?php echo $message['id']?>" class="text-danger setDeleted">删除</a></td>
            </tr>
            <?php endforeach;?>
            <?php else:?>
                <tr><td colspan="5">暂无数据</td></tr>
            <?php endif;?>
            </tbody>
        </table>

        <div class="msg-footer">
            <div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0" <?php if(empty($lists)){ echo 'hidden';}?>>
                <?php
                if (isset($lists)) {
                    $this->widget('common.widgets.pagers.ULinkPager', array(
                        'cssFile' => '',
                        'header' => '',
                        'prevPageLabel' => '上一页',
                        'nextPageLabel' => '下一页',
                        'firstPageLabel' => '',
                        'lastPageLabel' => '',
                        'pages' => $pages,
                        'maxButtonCount' => 5, //分页数量
                    ));
                }
                ?>
            </div>
        </div>

    </div>
</div>
<!-- contentpanel -->
<script>
	jQuery(document).ready(function (){
        /*
        删除消息
         */
		$('.setDeleted').on("click",function() {
            var id = $(this).data('id');
			PWConfirm('确定要删除消息？',function(){
				$.post('/system/message/delete', {id : id},function(data) {
				if (data.error == 0) {
					$('#message' + id).remove();
					top.location.reload();
				}else{
					alert(data.msg);
				}
			  },'json');
            });
		});

        /*
        全选/全不选
         */
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
            /*$('tr[class="selected"]').each(function(){
                if(ids != "") {
                    ids += ',';
                }
                ids += $(this).find('input[type="checkbox"]').val();
            });*/

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
			    $.post('/system/message/updateall/',{ids : ids, type : 'del'},function(data){
                if (data.error) {
                    var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+data.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                    location.href='#show_msg';
                } else {
                    var succss_msg = '<div class="alert alert-success"><strong>'+ data.msg +'</strong></div>';
                    $('#show_msg').html(succss_msg);
                    window.location.reload();
                }
            },'json');
            });
        })
	});
</script>
