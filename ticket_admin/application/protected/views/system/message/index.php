<style>
    .title{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 10em;}
    .badge{background-color:#fc3232;}
    .no-read td{font-weight:bold;}
    .table-bordered td span{margin-left:5px;}
    .table-bordered td a{font-weight:normal;}
</style>
<div class="contentpanel">
    <div class="row">
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
        <?php if($type == 'advice'):?>
            <li class="<?php echo $is_allow == 1 ? 'active' : ''?>">
                <a href="/system/message/view/type/<?php echo $type ?>/is_allow/1" style="float:none;" class="btn btn-sm"> 已发送</a>
            </li>
            <li class="<?php echo $is_allow == 2 ? 'active' : ''?>">
                <a href="/system/message/view/type/<?php echo $type ?>/is_allow/2" style="float: none;" class="btn btn-sm">已驳回</a>
            </li>
            <li style="float: right;margin-top: 5px">
                <button data-toggle="modal" data-target=".bs-example-modal-lg" type="button" class="btn btn-sm btn-default btn-bordered">发布公告</button>
            </li>
        <?php endif;?>
        </ul>
        <div id="show_msg"></div>
<div class="table-responsive" style="margin-bottom:30px;">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="100">
                <div class="ckbox ckbox-primary" style="margin-left:17px;">
                    <input type="checkbox" class="ids" id="checkbox-allcheck">
                    <label for="checkbox-allcheck" class="allcheck">全选</label>
                </div>
            </th>
            <th width="200">
                <a class="btn btn-xm btn-default btn-bordered" id="delete-all"> 删除</a>
            </th>
            <th width="450"></th>
            <th width="100"></th>
            <th width="80"></th>
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
                           data-name="<?php echo $message['organization_name']?>"
                           data-food="<?php echo $message['read_time'] == 0 ? 0 : date('Y年m月d日',$message['created_at'])?>"
                           data-time="<?php echo date('Y年m月d日',$message['created_at'])?>"
                           data-title="<?php echo $message['title']?>"
                           data-content='<?php echo json_encode($message['content'])?>'
                           data-remark='<?php echo !empty($message['remark']) ? $message['remark'] : ''?>'>
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
</div>
<!-- contentpanel -->
<!-- 发送公告开始 -->
<div role="dialog" tabindex="-1" class="modal fade bs-example-modal-lg">
    <div class="modal-dialog modal-lg">


        <div class="modal-content">
            <from>
            <div class="modal-header">
                <div id="report"></div>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">公告</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label class="col-sm-2 control-label">主题：</label>
                    <div class="col-sm-4">
                        <input name="title" type="text" class="form-control" placeholder="">
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
                <img id="loader" src="/img/select2-spinner.gif" style="display: none" alt=""/>
                <button type="button" class="btn btn-success" id="send_advice">发送</button>
            </div>
        </div>
        </form>
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

<script>
    function modal_jump(obj) {
        document.getElementById('verify-modal').innerHTML = '';
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }
</script>
<!--到期提醒处理结束-->
<script  charset="utf-8" src="/js/kindeditor-4.1.10/kindeditor.js"></script>
<script  charset="utf-8" src="/js/kindeditor-4.1.10/lang/zh_CN.js"></script>
<script  charset="utf-8" src="/js/kindeditor.create.js"></script>
<script>
jQuery(document).ready(function() {
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
                top.location.reload();
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
        //  console.log(ids);return false;

		PWConfirm('确定要删除选中消息?',function(){

            //console.log(ids);return false;
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
                    location.href = '#report';
                    $('#send_advice').show();
                    $('#loader').hide();
                } else {
                    var succss_msg = '<div class="alert alert-success"><strong>发送成功!</strong></div>';
                    $('#report').html(succss_msg);
                    top.location.reload();
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

