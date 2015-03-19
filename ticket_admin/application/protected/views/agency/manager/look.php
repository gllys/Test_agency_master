<?php
$this->breadcrumbs = array('分销商', '分销商管理');
?>

<div class="contentpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">查看分销商信息</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="single" type="hidden" name="type">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">公司名称：</label>
                            <div class="col-sm-4" style="margin-top:5px">
                                <?php echo isset($lookd['name'])?$lookd['name']:"";?></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系电话：</label>
                            <div class="col-sm-4" style="margin-top:5px"><?php echo isset($lookd['telephone'])?$lookd['telephone']:"";?></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">传真：</label>
                            <div class="col-sm-4" style="margin-top:5px"><?php echo isset($lookd['fax'])?$lookd['fax']:"";?></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系人：</label>
                            <div class="col-sm-4" style="margin-top:5px"><?php echo isset($lookd['contact'])?$lookd['contact']:"";?></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">手机号码：</label>
                            <div class="col-sm-4" style="margin-top:5px"><?php echo isset($lookd['mobile'])?$lookd['mobile']:"";?></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">地址：</label>
                            <div class="col-sm-4" style="margin-top:5px">
                                <?php
                               // print_r($lookd);
                                //[province_id] => 140000 [city_id] => 140200 [district_id] => 140202
                               $post1 = Districts::model()->findByPk($lookd['province_id']);
                                echo   isset($post1->name)?$post1->name:'';
                                if(empty($lookd['city_id']) || $lookd['city_id'] ==1){
                                    echo "";
                                }else{
                                    $post2 = Districts::model()->findByPk($lookd['city_id']);
                                    echo   isset($post2->name)?$post2->name:'';
                                }
                                if(empty($lookd['district_id']) || $lookd['district_id'] == 1){
                                    echo "";
                                }else{
                                    $post3 = Districts::model()->findByPk($lookd['district_id']);
                                    echo   isset($post3->name)?$post3->name:'';
                                }
                                echo isset($lookd['address'])?$lookd['address']:"";
                                ?>
                            </div>
                        </div><!-- form-group -->


                    </div>

                    <div class="panel-footer">
                        <input id="btnreset"  type="button"  class="btn btn-default"  value="返回"   onclick="window.history.go(-1)">
                    </div>
                </form>
            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->

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
    $.datepicker.regional["zh-CN"] = {closeText: "关闭", prevText: "&#x3c;上月", nextText: "下月&#x3e;", currentText: "今天", monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"], monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"], dayNames: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"], dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"], dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], weekHeader: "周", dateFormat: "yy-mm-dd", firstDay: 1, isRTL: !1, showMonthAfterYear: !0, yearSuffix: "年"}
    $.datepicker.setDefaults($.datepicker.regional["zh-CN"]);
</script>
