<?php
$this->breadcrumbs = array('产品管理', '添加优惠规则');
$action = isset($action) ? $action : $this->getAction()->getId();
?>
<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $action == "add" ? "添加" : ($action == "edit" ? "编辑" : ""); ?>优惠规则信息</h4>
                </div>
                <!-- panel-heading -->
                <form class="form-horizontal form-bordered" action="/ticket/discount/save" id="discount-form"
                      method="post">
                    <input type="hidden" name="id" value="<?php echo !empty($info) ? $info['id'] : ""; ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">

                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 名称</label>

                            <div class="col-sm-4">
                                <input type="text" value="<?php echo !empty($info) ? $info['name'] : ""; ?>"
                                       placeholder="请输入规则名称" name="name" class="form-control"
                                       data-validation-engine="validate[required]">
                            </div>
                        </div>
                        <!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 说明</label>

                            <div class="col-sm-4">
                                <input type="text" name="note" value="<?php echo !empty($info) ? $info['note'] : ""; ?>"
                                       placeholder="请输入规则说明" class="form-control"
                                       data-validation-engine="validate[required]"/>
                            </div>
                        </div>
                        <!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>限制清单</label>

                            <div class="col-sm-4">
                                <select data-placeholder="Choose One" style="width:300px;" name="namelist_id"
                                        id="distributor-select" data-validation-engine="validate[required]">
                                    <?php if(!empty($info)){ ?>
                                        <?php $res = Ticketorgnamelist::api()->detail(array('id'=>$info['namelist_id'])); ?>
                                        <?php $limitInfo = $res['code']=="succ"?$res['body']:array(); ?>
                                        <option value="<?php echo !empty($limitInfo)?$limitInfo['id']:""; ?>">
                                            <?php echo !empty($limitInfo)?$limitInfo['name']:"请选择限制分销商清单"; ?>
                                        </option>
                                    <?php }else{ ?>
                                        <option value="">请选择限制分销商清单</option>
                                    <?php } ?>
                                    <?php  ?>
                                    <?php if(!empty($limitList)){ ?>
                                    <?php foreach($limitList as $limit): ?>
                                        <option value="<?php echo $limit['id']; ?>">
                                            <?php echo $limit['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php }else{ ?>
                                        <option value="-1">还没有限制分销商清单,点击去创建</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- form-group -->


                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>优惠日期</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="" class="form-control datepicker" name="start_date" value="<?php echo !empty($info)?(date("Y-m-d",$info['start_date'])):""; ?>"
                                       style="width:150px;display:inline-block" data-validation-engine="validate[required,custom[date]]"> ~
                                <input type="text" placeholder="" class="form-control datepicker" name="end_date" value="<?php echo !empty($info)?(date("Y-m-d",$info['end_date'])):""; ?>"
                                       style="width:150px;display:inline-block" data-validation-engine="validate[required,custom[date]]">
                            </div>
                        </div>
                        <!-- form-group -->

                        <!--div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span>优惠减免</label>
                            <div class="col-sm-2">
                                <select id="g_type" name="g_type" class="select2" data-placeholder=""
                                        style="width:100%">
                                        <option value="1" <?php //echo !empty($info)?($info['fat_discount']<0?"selected='selected'":""):""; ?> >降价(元)</option>
                                    <option value="0" <?php //echo !empty($info)?($info['fat_discount']>=0?"selected='selected'":""):""; ?> >加价(元)</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" value="<?php //echo !empty($info)?(sprintf("%1\$.2f",abs($info['fat_discount']))):"" ?>" name="discount" class="form-control" data-validation-engine="validate[required,custom[onlyNumberPrice]]">
                            </div>
                        </div-->

                        <!-- form-group -->
                        <div class="form-group">
                        <label class="col-sm-2 control-label"><span class="text-danger">*</span> 优惠减免</label>
                        <div class="col-sm-10">
                          
                            <label class="col-sm-1 control-label" style="width:30px">散客</label>
                            <div class="col-sm-1">
                                <input type="text" id="spinner-min" name="fat_discount" <?php if($action=='edit'){ echo "value=".abs($info['fat_discount']);}?>>
                            </div>
                        </div>
                        </div><!-- form-group -->
                        <div class="form-group" style="border:0">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <label class="col-sm-1 control-label" style="width:30px">团队</label>
                            <div class="col-sm-1" >
                                <input type="text" class="spinner-day" name="group_discount" <?php if($action=='edit'){ echo "value=".abs($info['group_discount']);}?>>
                            </div>
                        </div>
                        </div><!-- form-group -->
                        

                    </div>
                    <!-- panel-body -->

                    <div class="panel-footer">
                        <input type="button" class="btn btn-primary mr5" id="save-discount-btn" value="保存">
                        <input type="button" class="btn btn-default" id="clear-discount-btn" value="取消">
                    </div>
                </form>
            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->
</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function () {
        !function () {
            $('#discount-form').validationEngine({
                autoHidePrompt: true,
                scroll: false,
                autoHideDelay: 3000,
                maxErrorsPerField: 1
            })


            //表单提交
            $('#save-discount-btn').click(function () {
                if ($('#discount-form').validationEngine('validate') === true) {
                    $.post('/ticket/discount/save', $('#discount-form').serialize(), function (data) {
                        if (data.error == 0) {
                            alert("保存成功！");
                            location.href = "/ticket/discount";
                        } else {
                            alert("保存失败," + data.msg);
                        }
                    },'json');
                }
            });

            $("#clear-discount-btn").click(function () {
                location.href = "/ticket/discount";
                //$('#s2id_distributor-select').find(".select2-chosen").text("请输入分销商名称");
            });

        }()

        $('#distributor-select').change(function(){
            if($(this).val()=="-1"){
                window.location.href = "/ticket/limitagency/add";
            }
        });

        jQuery('#distributor-select,#g_type').select2({
            minimumResultsForSearch: -1
        });
        
        var spinnerMin = jQuery('#spinner-min').spinner({'min': 0});
        var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
       if($('input[name="action"]').val() == 'add'){
            spinnerMin.spinner('value', 0);
            spinnerDay.spinner('value', 0);
       }
      
       

        // Time Picker
        jQuery('#timepicker').timepicker({defaultTIme: false});
        jQuery('#timepicker2').timepicker({showMeridian: false});
        jQuery('#timepicker3').timepicker({minuteStep: 15});

        // Date Picker
        jQuery('.datepicker').datepicker();
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });
        // Input Masks
        jQuery("#date").mask("99/99/9999");

    });
</script>