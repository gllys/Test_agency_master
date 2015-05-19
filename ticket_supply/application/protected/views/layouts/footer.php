<footer>
    <div class="footerwrapper">© 2014-2015 piaotai.com 版权所有 ICP证：  沪ICP备15006375号-2</div>
</footer>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery-migrate-1.2.1.min.js') ?>"></script>

<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery-ui-1.11.2.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/modernizr.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/pace.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/retina.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.cookies.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/marquee.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl(CreateUrl::model()->topbarUrl) ?>"></script>

<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.autogrow-textarea.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.mousewheel.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.tagsinput.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/toggles.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/bootstrap-timepicker.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.maskedinput.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/select2.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/colorpicker.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/dropzone.min.js') ?>"></script>

<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/custom.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.validationEngine.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/jquery.validationEngine-zh-CN.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/bootstrap.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/wysihtml5-0.3.0.min.js') ?>"></script>
<script src="<?php echo Yii::app()->versionUrl->changeUrl('/js/bootstrap-wysihtml5.js') ?>"></script>

<script type="text/javascript">
    if (!window.console) {
        window.console = {};
        console = {};
        console.log = function(e) {
            //alert(e);
        };
        window.console = console;
    }
    // Date Picker
    if(typeof $.datepicker!=='undefined'){
    $.datepicker.regional["zh-CN"] = {closeText: "关闭", prevText: "&#x3c;上月", nextText: "下月&#x3e;", currentText: "今天", monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"], dayNames: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"], dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"], dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], weekHeader: "周", dateFormat: "yy-mm-dd", firstDay: 1, isRTL: !1, showMonthAfterYear: !0, yearSuffix: "年"}
    $.datepicker.setDefaults($.datepicker.regional["zh-CN"]);
    }
</script>








