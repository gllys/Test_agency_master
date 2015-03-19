//弹框层数值
function modal_jump(obj){
	$('#upload-show1').html();
	$.get('index.php?c=bill&a=uploadShow&id='+obj,function(data){
		$('#upload-show1').html(data);
	});
}
