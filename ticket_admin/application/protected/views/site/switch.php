<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/pt.loading.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $(window).bind('hashchange', function() {
            loadPart();
        });

        function loadPart() {
            //页面加载进度条
            var $loading = setTimeout(function(){
                $('#mainpanel').loading();
            },1000);
            
            //滚动条到最上
            $('body,html').animate({scrollTop: 0}, 500);

            //ajax页面加载
            var _url = location.hash.substring(1);
            if (_url===''||_url.indexOf('/') === -1) {
                location.href = '/site/switch/#/dashboard/';
            }
            
            //ajax页面加载
            if (_url.indexOf('?') === -1) {
                _url += '?mod=part';
            } else {
                _url += '&mod=part';
            }
            $.get(_url, function(data,status) {
                if (data.error === 0) {
                    $('#mainpanel').html(data.msg);
                } else if(data.error === 1) { 
                    $('#mainpanel').html(data.msg);
                }else if(data.error === 200) { 
                    location.href = data.msg;
                }else { 
                    location.href = '/site/login/'
                }

                //所有mainpanel下part标签连接全部改变
                $('#mainpanel').find('a:not(.clearPart)').not('[data-toggle=modal]').each(function() {
                    $(this).attr('href', '/site/switch/#' + $(this).attr('href'));
                });

                //改变选中菜单
                $('#child_nav').find('li').removeClass('active');
                 $('#child_nav a[href="' + data.params + '"]').parent().addClass('active').parent().show().parent().addClass('parent-focus');
                
                
                 clearTimeout($loading);
                $('#mainpanel').removeLoad();
				
                //刷新后滚动
                $('body').removeClass('modal-open');
                
                 //删除黑瓶
                $('.modal-backdrop').remove();
                
                //共供初始化
                 $('input, textarea').placeholder();
            }, 'json');
        }
        loadPart();

        //form查询监听
        $(document).on("submit", "form:not(.clearPart)", function() {
            var _url = $(this).attr('action');
            if (_url.indexOf('?') === -1) {
                _url += '?'+$(this).serialize()+'&mod=part';
            } else {
                _url += $(this).serialize()+'&mod=part';
            }
            location.href = '/site/switch/#' + _url;
            return false;
        });
		
		//重写window.location.partReload
		window.location.partReload = location.partReload = function(){
			$('.modal-backdrop').remove();
			loadPart() ;
		}
		
		//F5 reload
		$(document).bind("keydown",function(e){
			if(e.keyCode==116){
			   loadPart() ;
			}
		});
        
        //get完成后初始化
        $(document).ajaxComplete(function(event,request, settings) {
             //共供初始化
             if(typeof(settings) !== undefined && settings.type.toUpperCase() == 'GET'){
                $('input, textarea').placeholder();
            }
         });
    });
</script>   