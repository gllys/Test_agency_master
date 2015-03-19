$(document).ready(function() {
	var start_time = phpvars.expire_start_at;
	var end_time   = phpvars.expire_end_at;
	var usableDay  = phpvars.weekly.split(',');
	var sale_price = phpvars.sale_price;
	var reserve    = phpvars.reserve;
	var now_time   = phpvars.now_time;
	var setdate = $('.FX-date').datepicker({
		language: "zh-CN",
		autoclose: true,
		format: 'yyyy-mm-dd',

		//不在适用期和可用天的不让点
		onRender: function(date) {
			if(reserve > 0){
				if((date.valueOf()-86400000*reserve) < now_time || date.valueOf() < now_time || date.valueOf() < start_time || date.valueOf() > end_time || !in_array(date.getDay(), usableDay)){
					return ['disabled',''];
				}else{
					return ['enabled','<div>'+sale_price+'元</div>'];
				}
			}else{
				if (date.valueOf() <= now_time || date.valueOf() < start_time || date.valueOf() > end_time || !in_array(date.getDay(), usableDay) ) {
					return ['disabled',''];
				} else {
					return ['enabled','<div>'+sale_price+'元</div>'];
				}
			}
		}
	}).on('changeDate', function(ev) {$('.datepicker').hide();});
});

//define close change use day function
var closeChangeUseDay = function(){
	$('#postpone .close').trigger('click');
}

$('#change-form-button').click(function(){
	var obj = $('#change-form');
	if(obj.validationEngine('validate')== true){
		if($('input[name=changeTo]').val() != phpvars.useday){
			$.post('index.php?c=order&a=doChangeUseDay', obj.serialize(),function(data){
				if(data.errors){
					var tmp_errors = '';
					$.each(data.errors, function(i, n){
						tmp_errors += n;
					});
					var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
					$('#change_show_msg').html(warn_msg);
				}else if(data['data'][0]['order_id']){
					var succss_msg = '<div class="alert alert-success"><strong>改期成功!</strong></div>';
					$('#change_show_msg').html(succss_msg);
					setTimeout("closeChangeUseDay()", 1000);
					$('#change_show_msg').html('');
				}
			}, "json");
			return false;
		}
	}
	return false;
});