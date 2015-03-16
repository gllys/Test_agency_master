/**
 * Created by yves on 11/20/14.
 */
$(function(){
    function h_hover(self){
        if ($(self).find('img').length > 0) {
            return false;
        }
        var text = $(self).text();
        var title= $(self).attr('title');
        $(self).text(title);
        $(self).attr('title', text);
    }
    $('.bun').hover(function(e){
        h_hover($(this));
        e.stopPropagation();
    });
    function h_click(self){
        var url = 'subscribes';
        var obj = 'sub';
        var txt = '订阅';
        if ($(self).hasClass('fav')) {
            url = 'favorites';
            obj = 'fav';
            txt = '收藏';
        }
        var params = {
            id: $(self).parent().attr('data-id'),
            done: $(self).hasClass(obj+'-done') ? 1 : 0,
            single: $(self).hasClass('group') ? 0 : 1
        };
        if (params.done == 0) {
            if (obj == 'fav') {
                params.name = $(self).parent().parent().find('strong').text();
            } else {
                params.name = $(self).parent().parent().parent().find('strong').text();
                params.fat_price = $(self).parent().attr('data-fat');
                params.group_price = $(self).parent().attr('data-group');
            }
        }
        $.ajax({
            'type': 'POST',
            'url': '/ticket/' + url + '/toggle/',
            'data': params,
            'dataType': 'json',
            beforeSend: function(){
                $('.bun').hover(function(){return false});
                $(self).html('<img src="/img/loaders/loader1.gif"/>');
            },
            success: function(result){
                if (result['code'] == 1) {
                    var g = $(self).hasClass('group') ? 'group' : '';
                    if (result['done'] == 1) {
                        $(self).parent().html('<a class="bun '+obj+' '+g+' '+obj+'-done" href="javascript:;" title="取消'+txt+'">已'+txt+'</a>');
                    } else {
                        $(self).parent().html('<a class="bun '+obj+' '+g+'" href="javascript:;" title="加入'+txt+'">'+txt+'</a>');
                    }
                    $('.bun').click(function(){
                        h_click($(this));
                    });
                }
                $('.bun').hover(function(e){
                    h_hover($(this));
                    e.stopPropagation();
                });
            }
        });
        return false;
    }
    $('.bun').click(function(){
        h_click($(this));
    });
});
