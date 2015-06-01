<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>

<!--h2>Error <?php echo $code; ?></h2-->
<div class="error" style="text-align:center">
<br>
<?php echo CHtml::encode($message); ?>

<br>
<br>
	<div class="aui_buttons" id="window" style="display:none">
		<button type="button" class="aui_state_highlight">关闭</button>
	</div>
	
	<div class="aui_buttons" id="operations" style="text-align:center; display:none">
		<button type="button" name="home">首页</button>
		<button type="button" name="back">返回</button>
	</div>
</div>

<script type="text/javascript">
try{
	$(function(){
		if(window.parent.length) {
			if(window.parent.opendia) {
				$("#window").show();
				$("#window .aui_state_highlight").click(function() {
					window.parent.opendia.close();
				});
			}
		} else {
			
			$("#operations").show()
			.find("button[name=home]").click(function() {
				window.location.href = '/#'+ window.location.protocol+'//'+window.location.hostname;
			}).end()
			.find("button[name=back]").click(function() {
				history.go(-1);
			});
		}
	});
}catch(e){}
</script>