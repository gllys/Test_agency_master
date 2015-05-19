
;(function($){
    $.fn.extend({
        loading:function(){
            $(this).css({"position":"relative"});
            var html = '' +
                '<div class="contentpanel-load" style="position:absolute;height:100%;width:100%;top:0;left:0;background:url(/img/white.png);background-repeat: repeat;">' +
                    '<div class="contentpanel-inner" style="position:absolute;height:93px;width:105px;top:50%;left:50%;margin:0 0 0 -52px;text-align:center;background:url(/img/load-sprite.png);background-repeat: repeat;"></div>' +
                '</div>';
            $(this).append(html);
            $(".contentpanel-inner").css("top",(($(window).height()-60)/2)-130);
            $(".contentpanel-load").width($(window).width()-190);
            $(window).resize(function(){
                $(".contentpanel-inner").css("top",(($(window).height()-60)/2)-130);
                $(".contentpanel-load").width($(window).width()-190);
            });
            var positionY = 93,i = 1,timer = null,_img = $(".contentpanel-inner");
            clearInterval(timer);
            timer = setInterval(function(){
                i >= 25?i = 1:i++;
                _img.css({"background-position":"0 "+ -i*positionY +"px"});
            },30);

        },
        removeLoad:function(){
            $(this).find(".contentpanel-load").remove();
        }
    });
})(jQuery);