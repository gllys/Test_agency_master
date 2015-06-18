<style>
    .title{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 36em;}
    .badge{background-color:#fc3232;}
    .no-read td{font-weight:bold;}
    .table-bordered td span{margin-left:5px;}
    .table-bordered td a{font-weight:normal;}
</style>
<?php
    function cutstr_html($string, $sublen)
    {
        $string = strip_tags($string);
        $string = preg_replace ('/\n/is', '', $string);
        $string = preg_replace ('/ |　/is', '', $string);
        $string = preg_replace ('/&nbsp;/is', '', $string);

        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string);
        if(count($t_string[0]) - 0 > $sublen) $string = join('', array_slice($t_string[0], 0, $sublen))."…";
        else $string = join('', array_slice($t_string[0], 0, $sublen));

        return $string;
    }
?>
<div class="contentpanel">
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="<?php echo !isset($read_time) || $read_time == '' && empty($is_allow) ? 'active' : ''?>"><a href="/system/message/view/type/<?php echo $type ?>"><strong>全部</strong></a></li>
            <li class="<?php echo $read_time == '0' ? 'active' : ''?>">
                <a href="/system/message/view/type/<?php echo $type ?>/read_time/0" style="float:none;" class="btn btn-sm"> 未读
                    <?php if($unread_num > 0): ?><span class="badge" id="unread"><?php echo $unread_num; ?></span><?php endif;?>
                </a>
            </li>
            <li class="<?php echo $read_time == '1' ? 'active' : ''?>">
                <a href="/system/message/view/type/<?php echo $type ?>/read_time/1" style="float: none;" class="btn btn-sm">已读</a>
            </li>
        <?php if($type == 'advice'):?>
            <li class="<?php echo $is_allow == 1 ? 'active' : ''?>">
                <a href="/system/message/view/type/<?php echo $type ?>/is_allow/1" style="float:none;" class="btn btn-sm"> 已发送</a>
            </li>
            <li class="<?php echo $is_allow == 2 ? 'active' : ''?>">
                <a href="/system/message/view/type/<?php echo $type ?>/is_allow/2" style="float: none;" class="btn btn-sm">已驳回</a>
            </li>
            <li style="float: right;margin-top: 5px">
                <button data-toggle="modal" data-target=".bs-example-modal-lg" type="button" class="btn btn-sm btn-primary">发布公告</button>
            </li>
        <?php endif;?>
        </ul>
        <div id="show_msg"></div>
<div class="table-responsive" style="margin-bottom:30px;">
    <table class="table table-bordered clearPart_body">
        <thead>
        <tr>
            <th width="100">
                <div class="ckbox ckbox-primary" style="margin-left:17px;">
                    <input type="checkbox" class="ids" id="checkbox-allcheck">
                    <label for="checkbox-allcheck" class="allcheck">全选</label>
                </div>
            </th>
            <th width="200">
                <a class="btn btn-primary btn-sm  clearPart" id="delete-all"> 删除</a>
                <?php if($read_time != 1 && $is_allow != 1) {?>
                <a class="btn btn-primary btn-sm  clearPart" id="update-all" style="margin-left: 20px"> 设为已读</a>
                <?php }?>
            </th>
            <th width="450"></th>
            <th width="100"></th>
            <th width="80"><input type="hidden" id="idlog" value="m" /></th>
        </tr>
        </thead>
        <tbody id="staff-body">
        <?php if (isset($lists) && !empty($lists)) : foreach ($lists as $message) : ?>
            <tr id="tr<?php echo $message['id']?>" class="<?php echo $message['read_time'] == 0 ? 'no-read' : ''?>">
                <td>
                    <div class="ckbox ckbox-primary" style="margin-left: 17px;">
                        <input type="checkbox" class="ids" id="checkbox<?php echo $message['id']?>" value="<?php echo $message['id']?>">
                        <label for="checkbox<?php echo $message['id']?>"></label>
                    </div>
                </td>
                <td>
                    <?php echo $message['title']?><span class="text-<?php echo $sys_class[$message['sys_type']]?>"><?php echo $sys_name[$message['sys_type']]?></span>
                </td>
                <td>
                    <?php if($message['sys_type'] != 0):?>
                        <p id="setRead<?php echo $message['id']?>" style="cursor: pointer;cursor: hand;margin: 5px 0px;" class="Newclass setRead <?php echo $message['read_time'] == 0 ? 'font-bold' : ''?>"
                           data-id="<?php echo $message['id']?>"
                           data-food="<?php echo $message['read_time']?>">
                            <?php echo $message['content']?>
                        </p>
                    <?php else:?>
                        <p id="readAdvice<?php echo $message['id']?>" style="cursor: pointer;cursor: hand;color: #2B84D1;margin: 5px 0px;" class="readAdvice <?php echo $message['read_time'] == 0 ? 'font-bold' : ''?> title"
                           data-id="<?php echo $message['id']?>"
                           data-time="<?php echo $message['read_time']?>"
                           data-type="body">
                            <?php
                                $content = trim($message['content']);
                                $content = cutstr_html($content,40);
                                echo !empty($content) ? $content : '无';
                            ?>
                        </p>

                        <div class="modal fade clearPart_body msg msg_body<?php echo $message['id']?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <div class="modal-title"><?php echo $message['title']?></div>
                                        <div style="float:left;color:#999;font-size:12px;margin-left:30px">
                                            <?php echo $message['organization_name']?>
                                        </div>
                                        <div style="float:left;margin-left:20px;color:#999;font-size:12px;">
                                            <?php echo $message['read_time'] == 0 ? date('Y年m月d日',$message['created_at']) : date('Y年m月d日',$message['read_time'])?>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <div style="word-break:break-all;">
                                            <p>公告内容：</p>
                                            <?php echo $message['content']?>
                                        </div>
                                        <?php if(!empty($message['remark'])):?>
                                        <div style="word-break:break-all;margin-top: 5px">
                                            <p style="color: #FF0000">驳回理由：</p>
                                            <?php echo $message['remark']?>
                                        </div>
                                        <?php endif;?>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div>

                    <?php endif;?>
                </td>
                <td><?php echo date('Y-m-d',$message['created_at'])?></td>
                <td><a href="javascript:;" data-id="<?php echo $message['id']?>" class="text-danger setDeleted  clearPart">删除</a></td>
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
</div>
<!-- contentpanel -->
<!-- 发送公告开始 -->
<div role="dialog" tabindex="-1" class="modal fade bs-example-modal-lg">
    <div class="modal-dialog modal-lg">


        <div class="modal-content">
            <form method="post" class="clearPart" action="/system/message/preview" target="_blank">
            <div class="modal-header">
                <div id="report"></div>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">公告</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label class="col-sm-2 control-label">主题：</label>
                    <div class="col-sm-4">
                        <input maxlength="20" name="title" type="text" class="form-control" placeholder="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">发给：</label>
                    <div class="col-sm-4">
                        <select id="receiver_organization" name="receiver_organization" class="select2"  data-placeholder="Choose One" style="width:100%;padding:0 10px;">
                            <option value="">请选择</option>
                            <option value="0">所有分销商</option>
                            <option value="1 ">已合作分销商</option>
                        </select>
                    </div>
                    <div id="tip" class="col-sm-5 text-danger"></div>
                </div>
                <div class="form-group">
                    <textarea id="remark" name="content"  style="width: 850px; height: 250px; visibility: hidden;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <img id="loader" src="/img/select2-spinner.gif" style="display: none" alt="" />
                <input id="preview" type="submit" class="btn btn-primary" value="预览" />
                <button type="button" class="btn btn-success" id="send_advice">发送</button>
            </div>
        </form>
        </div>
    </div>
<!-- 发送公告结束 -->
</div>
<!--退款申请处理开始-->
<div class="modal fade bs-example-modal-static" id="verify-modal-point" tabindex="-1" role="dialog"></div>

<script>
    function point(pointid,id){
        document.getElementById('verify-modal-point').innerHTML = '';
        $.get('/order/refund/point/id/'+id+'/order_id/'+pointid, function(data) {
            $('#verify-modal-point').html(data);
        });
    }
</script>
<!--退款处理申请结束-->
<!--到期提醒处理开始-->
<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>

<!--到期提醒处理结束-->
<script  charset="utf-8" src="/js/kindeditor-4.1.10/kindeditor.js"></script>
<script  charset="utf-8" src="/js/kindeditor-4.1.10/lang/zh_CN.js"></script>
<script  charset="utf-8" src="/js/kindeditor.create.js"></script>
<script  charset="utf-8" src="/js/message.js"></script>
<script>
    function modal_jump(obj) {
        
        var url = $(obj).attr('href'); 
        var idlog = $('#idlog').val();        
        //到期提醒未读数量减一
        if($(obj).parent().parent().parent().hasClass('no-read') && url.indexOf("ticket_id=") != -1){            
            var msg_id = parseInt($(obj).parent().attr('data-id'));
            //已减一的不再减       
            $('#tr' + msg_id).removeClass('no-read');
            if(idlog.indexOf(msg_id+'m') != -1){
                $('#idlog').val(idlog+msg_id+'m');                
                $.post('/system/message/read', {
                    'id': msg_id
                }, function (data) {
                    if (data.error == 0) {
                        var num;
                        var read_num;
                        var unread;
                        num = $('#unread_num').text();
                        unread = $('#unread').text();
                        read_num = $('#read_num').text();
                        //未读消息的累减
                        if (Number(num) > 0) {
                            num = num - 1;
                        }
                        if (Number(num) == 0) {
                            $('#unread_num').remove();
                        } else {
                            $('#unread_num').text(num);
                        }
                        //标签内未读消息累减
                        if (Number(unread) > 0) {
                            unread = unread - 1;
                        }
                        if (Number(unread) == 0) {
                            $('#unread').remove();
                        } else {
                            $('#unread').text(unread);
                        }
                        $('#setRead' + msg_id).removeClass('font-bold');
                        $('#readAdvice' + msg_id).removeClass('font-bold');
                        $('#sender' + msg_id).removeAttr('style');
                    } 
                }, 'json');
            }
        }
        document.getElementById('verify-modal').innerHTML = '';
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }
jQuery(document).ready(function() {
	
	//　预览，本处对公告的完整性进行检测
	$('#preview').click(function() {
		
		if($('input[name="title"]').val() == '') {
			$('input[name="title"]').PWShowPrompt('公告主题不能为空');
			return false;
		}

		if($('select[name="receiver_organization"]').val() == '') {
			$('select[name="receiver_organization"]').PWShowPrompt('请选择公告发送的对象');
			return false;
		}
		
		if(editor.html() == '') {
			$('#remark').PWShowPrompt('公告内容不能为空！');
			return false;
		}
		
	});
	
    jQuery('.select2').select2({
        minimumResultsForSearch: -1
    });

    /*
    消息删除
     */
    $('.setDeleted').click(function() {
        var id = Math.floor($(this).attr('data-id'));
		PWConfirm('确定要删除消息?',function(){
			   $.post('/system/message/delete', {
            'id': id
        }, function(data) {
            if (data.error == 0) {
                $('#message' + id).remove();
                location.partReload();
            } else {
                alert(data.msg);
            }
        }, 'json');
        	});
    });

    /*
    下拉框内容判断
     */
    $('#receiver_organization').change(function(){
        var recevier = $(this).find('option:selected').val();
        if(recevier == 0){
            $('#tip').text('注意：当您选择的是发给“所有分销商”时，该条公告需要经过系统审核，审核时间最多为24小时！');
        }else{
            $('#tip').text('');
        }
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

        $(".selected").each(function(i){
            if(ids != "") {
                ids += ',';
            }
            var a = $(this).find('input[type="checkbox"]').val();
            if(a!="" || a!="undefined" || a!="on"){
                ids += $(this).find('input[type="checkbox"]').val();
            }
        })
        /*console.log(ids);return false;
        $('tr[class="selected"]').each(function(){

            
            if(ids != "") {
                ids += ',';
            }
            ids += $(this).find('input[type="checkbox"]').val();


        });

        return false;
       */
        //console.log(ids);return false;
        //alert(ids);return false;

        if(ids==""){
            alert("请先选中要删除的消息");return false;
            //var delMsg = "请先选中要删除的消息";
        }else{
            var delMsg = "确定要删除选中消息" 
        }
		PWConfirm(delMsg,function(){
    		$.post('/system/message/updateall/',{ids : ids, type : 'del'},function(data){
                if (data.error) {
                    alert(data.msg);
                } else {
                    alert(data.msg);
                    setTimeout("window.location.partReload();",2000);
                }
            },'json');
        });
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
            $.post('/system/message/updateBatch/',{ids : ids},function(data){
                if (data.error) {
                    alert(data.msg);
                } else {
                    alert(data.msg);
                    setTimeout("window.location.partReload();",2000);
                }
            },'json');
        });
    });





    /*
    发布公告
     */
    $('#send_advice').click(function(){
        editor.sync();
        $(this).hide();
        $('#loader').show();
        var title = $('input[name=title]').val();
        var receiver_organization = $('#receiver_organization').find('option:selected').val();
        var content = $('#remark').val();
        $.post('/system/message/saveadvice',{
                title : title,
                receiver_organization : receiver_organization,
                content : content
            },
            function(data){
                if (data.error) {
                    var warn_msg = '<div class="alert alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + data.msg + '</div>';
                    $('#report').html(warn_msg);
                    location.href = '/#'+ '#report';
                    $('#send_advice').show();
                    $('#loader').hide();
                } else {
                    var succss_msg = '<div class="alert alert-success"><strong>发送成功!</strong></div>';
                    $('#report').html(succss_msg);
                    location.partReload();
                }
        },'json');
    })
});
</script>
<script type="text/javascript">
    if (!window.console) {
        window.console = {};
        console = {};
        console.log = function(e) {
        };
        window.console = console;
    }

</script>

<script type="text/javascript">
<?php if($this->action->getId() == 'index') { ?>
$('#child_nav a[href="/system/message/view/type/due/"]').parent().addClass('active').parent().show();
<?php } ?>
</script>