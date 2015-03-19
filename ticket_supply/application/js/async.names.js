/**
 * Created by yves on 11/28/14.
 */
$(function(){
    var wrap = $('[role="async-name"]');
    var data = {poi: {}, landscape: {}};
    if (wrap.length > 0) {
        $.each(wrap, function(i, v){
            var param = $(v).attr('class').split('-');
            if (param.length == 2 && Math.floor(param[1]) > 0) {
                //data[param[0]].push(param[1]);
                data[param[0]][param[1]] = 1;
            }
        });
        $.each(data, function(i, v){
            var a = [];
            $.each(v, function(idx, _){
                a.push(idx);
            });
            trans(i, a.join(','));
        });
    }
    function trans(item, id) {
        $.ajax({
            url: '/AjaxServer/' + item + 'Names',
            type: 'POST',
            dataType: 'json',
            data: {ids: id},
            beforeSend: function(){

            },
            success: function(result){
                if (result.code == 1) {
                    $.each(result.data, function(i, v){
                        $('.'+ item +'-' + v.id).text(v.name);
                    });
                }
            }
        });
    }
});

