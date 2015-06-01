<?php
$content = isset($content)?$content:'' ;
$url = isset($url)?$url:'javascript:history.go(-1);' ;
$infotitle = isset($infotitle)?$infotitle:'infotitle2' ;
?> 
<style type="text/css">
.self_content

</style>
 <!--页面内容开始-->
      <!--二级目录 区Start-->
      <h3>提示!</h3>
      <!--二级目录 区End-->
<div class="infobox"><h4 class="<?php echo $infotitle ?>"><?php echo $content ?></h4><p class="marginbot"><a class="lightlink" href="<?php echo $url ?>">点击这里返回上一页</a></p></div>
<script type="text/javascript">
window.setTimeout(function(){
   var $url = '<?php echo $url ?>' ;
   if($url=='javascript:history.go(-1);')javascript:history.go(-1);
   else window.location.href = '/#'+ $url ;
},2000);
</script>
