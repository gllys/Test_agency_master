/**
 * Created by poloxue on 2015/5/6.
 */
jQuery(document).ready(function() {

    initBind();

    function initBind(){
        /*
         阅读公告，公告弹窗
         */
        $('.readAdvice').bind('click', function () {
            var id = Math.floor($(this).attr('data-id'));
            var time = $(this).attr('data-time');
            var type = $(this).attr('data-type');

            if(time == 0){
                setRead(id);
            }

            if(type == 'header'){
                $('.msg_header' + id).modal('show');
            }else{
                $('.msg_body' + id).modal('show');
            }
            $('.msg_' + type + id).on('hide.bs.modal', function () {
                window.location.reload();
            });
        })

        /*
         阅读各种提醒
         */
        $('.setRead').bind('click', function () {
            var sels = $(this);
            var id = Math.floor($(this).attr('data-id'));
            var rt = $(this).attr('data-food');
            if (rt == 0) {
                setRead(id);
                sels.attr('data-food', 1);
            }
        });
    }

    /*
     阅读后的更新
     */
    function setRead(id) {
        $.post('/message/notice/read', {
            'id': id
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

                $('#setRead' + id).removeClass('font-bold');
                $('#readAdvice' + id).removeClass('font-bold');
                $('#sender' + id).removeAttr('style');
                return data.msg;
            } else {
                alert(data.msg);
            }
        }, 'json');
    }

    // 头部数目获取，加载完延迟1s获取
    setTimeout(function (){getTopBar(); }, 2000);
    // 头部数目获取，30秒定时执行
    setInterval(function(){getTopBar(); }, 60000);

    function getTopBar()
    {
        $.getJSON('/message/notice/topbar', function(result) {
            if(0 === result.error){
                var marquee = '';
                var model = '';
               if(typeof(result.params.notice_list.data) === "object"){
                    marquee += '<ul>';
                    var mdata = [];
                    $.each(result.params.notice_list.data, function(){
                        mdata.push(this);
                    });
                    mdata.sort(function(a,b){
                        return parseInt(b.created_at)-parseInt(a.created_at);
                    });
                    $.each(mdata, function(){
                        marquee += '<li><a href=\"javascript:;\" class=\"readAdvice\" id=\"already'+this.id+'\"';
                        marquee += 'data-id=\"'+this.id+'\" data-type=\"header\" data-time=\"'+this.read_time+'\">';
                        marquee += '【公告】 '+this.title+'<\/a><\/li>';


                        model +='<div class="modal fade msg_header'+this.id+'">';
                        model +='<div class="modal-dialog"><div class="modal-content">';
                        model +='<div class="modal-header">';
                        model +='<button type="button" class="close" data-dismiss="modal">';
                        model +='<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
                        model +='<div class="modal-title">'+this.title+'</div>';
                        model +='<div style="float:left;color:#999;font-size:12px;margin-left:30px">';
                        model +=this.organization_name+'</div><div style="float:left;margin-left:20px;color:#999;font-size:12px;">';
                        if(parseInt(this.read_time) === 0){
                            model += new Date(parseInt(this.created_at) * 1000).toLocaleString();
                        }else{
                            model += new Date(parseInt(this.read_time) * 1000).toLocaleString();
                        }
                        model +='</div></div><div class="modal-body">';
                        model +='<div style="word-break:break-all;"><p>公告内容：</p>'+this.content+'</div>';
                        if(this.remark.length){
                            model +='<div style="word-break:break-all;margin-top: 5px">';
                            model +='<p style="color: #FF0000">驳回理由：</p>'+this.remark+'</div>';
                        }
                        model +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal" id="close_advice">关闭</button></div>';
                        model +='</div></div></div>';

                    });
                    marquee += '</ul>';
                }
                $('#PT-marquee').html(marquee);
                $('#PT-model').html(model);

                if(0 < parseInt(result.params.message_count)){
                    $('.unread_message').text(result.params.message_count).removeClass('hide');
                }
//                if(0 < parseInt(result.params.cart_count)){
//                    $('.shopping_cart').text(result.params.cart_count).removeClass('hide');
//                }
//                if(0 < parseInt(result.params.subscribe_count)){
//                    $('.favorite_num').text(result.params.subscribe_count).removeClass('hide');
//                }

                initBind();

                $('#PT-marquee ul').newsTicker({
                    row_height: 19,
                    max_rows: 1,
                    duration: 3000,
                    pauseOnHover: true
                });
            }
        });
    }
});
