<?php 
use common\huilian\utils\Header;
Header::utf8();

// var_dump($supplyLans, $ticket);
// exit;
?>
<style>
.ui-datepicker { z-index:9999!important }
.ui-spinner { border-left-width: 2px;}
</style>
<div class="modal-dialog" style="width: 1150px !important;">
    <div class="modal-content" style="padding-left: 10px;padding-right: 10px;">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">修改产品</h4>
        </div>
        <form  method="post" action="#" id="form" class="form-horizontal form-bordered">
            <input value="<?php   echo $ticket['id'];?>" type="hidden" name="id">
            <table class="table table-bordered" style="width:912px; margin: auto;margin-bottom: 30px !important;">
                <thead>
                    <tr>
                        <th style="width:220px;">景区名称</th>
                        <th style="width:220px;">门票</th>
                        <th>窗口价格</th>
                        <th>数量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="take-ticket">
                    <?php
                    $supplylanIds = PublicFunHelper::arrayKey($supplyLans, 'landscape_id');
                    $lanLists = Landscape::api()->getSimpleByIds($supplylanIds);
                    ?>
                    <?php
                    if (!empty($ticket['items'])):
                        $base_items = $ticket['items'];
                        $_POST['ticketBase'] = array(); //单例
                        foreach ($base_items as $key=>$base_item):
                            ?>
                            <tr>
                                <td>
                                    <div class="form-group" style="margin:0">
                                        <select class="select2 lan" data-placeholder="Choose One" style="width:200px;padding:0 10px;">
                                            <option value="">请选择景区</option>
                                            <?php foreach ($supplyLans as $item): ?>
                                                <option value="<?php echo $item['landscape_id'] ?>" <?php if ($base_item['scenic_id'] == $item['landscape_id']): ?>selected="selected"<?php endif; ?>><?php
                                                    //todo optimize
                                                    if (isset($lanLists[$item['landscape_id']])) {
                                                        echo $lanLists[$item['landscape_id']]['name'];
                                                    }
                                                    ?></option>  
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group" style="margin:0">
                                        <select class="select2 poi" data-placeholder="Choose One" style="width:200px;padding:0 10px;">
                                            <option value="">请选择门票</option>
                                            <?php
                                            //得到景区门票
                                            if (isset($_POST['ticketBase'][$base_item['scenic_id']])) {
                                                $ticketBases = $_POST['ticketBase'][$base_item['scenic_id']];
                                            } else {
                                                $param = array();
                                                $param['scenic_id'] = $base_item['scenic_id'];
                                                $param['state'] = 1;
                                                $param['items'] = 10000;
                                                $param['types'] = '1,2,3,5';
												$param['fields'] = 'id,name,type' ;
												$data = ApiModel::getLists(Tickettemplatebase::api()->lists($param, true));
                                                $ticketBases = $_POST['ticketBase'][$base_item['scenic_id']] = $data;
                                            }
                                            ?>
                                            <?php foreach ($ticketBases as $k => $item): ?>
                                                <option value="<?php echo $item['id'] ?>" <?php if ($base_item['base_id'] == $item['id']): ?>selected="selected"<?php endif; ?>><?php
                                                    //todo optimize
                                                    $_type = TicketType::model()->findByPk($item['type']);
                                                    echo $item['name'] . $_type['name'];
                                                    ?></option> 
                                                <?php
                                                if ($base_item['base_id'] == $item['id']) {
                                                    //删除已选择记录，以后的
                                                    unset($_POST['ticketBase'][$base_item['scenic_id']][$k]);
                                                    ?>
                                                    <script type="text/javascript">
                                                        //多次加载问题
                                                        $(function() {
                                                            $("select.poi:lt(<?php echo $key ?>)").find('option[value=<?php echo $item['id'] ?>]').remove();
                                                        });
                                                    </script>
                                                <?php } ?>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <td class="sale_price">
                                <?php echo $base_item['sale_price']; ?>
                                </td>
                                <td><input type="text" name="base_items[<?php echo $base_item['base_id']; ?>]" value="<?php echo $base_item['num']; ?>" class="spinner_num" style="cursor: pointer;cursor: hand;background-color: #ffffff" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></td>
                                <td>
                                    <?php
                                    if($key === 0):
                                    ?>
                                    <div class="btn btn-primary btn-xs" id="take-ticket-add">增加</div>    
                                    <?php else: ?>
                                    <div class="btn btn-danger btn-xs" id="take-ticket-add">删除</div>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                   <?php endif; ?>
                </tbody>
            </table>

            <div class="panel-body nopadding">
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger">*</span>产品名称:</div></label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="请输入产品名称" maxlength="30" tag="产品名称" class="validate[required] form-control" name="name" value="<?php echo $ticket['name']?>">
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <div class="col-sm-2 control-label">
                        <div class="ckbox ckbox-primary pull-right" style="text-align: left;">
                            <input type="checkbox" name="is_fit" type="checkbox" value="1"  id="checkboxPrimary23" <?php if(!empty($ticket['is_fit'])){ echo "checked=checked";}?> style="vertical-align: middle;">
                            <label for="checkboxPrimary23" style="vertical-align: middle;"><span class="text-danger"></span>散客结算价:</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" placeholder="请输入价格" readonly tag="散客结算价"  class=" form-control onlyMoney" name="fat_price" id="sk_price" value="<?php echo $ticket['fat_price']?>">
                    </div>

                    <?php  if($orgInfo['partner_type'] < 1):?>
                    <label class="col-sm-1 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否一次验票:</div></label>
                    <div class="col-sm-2">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="1"  name="is_fat_once_verificate" id="is_fat_once_verificate1" <?php if($ticket['is_fat_once_verificate'] == 1) {echo 'checked="checked"';}?>>
                            <label for="is_fat_once_verificate1">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="0" name="is_fat_once_verificate" id="is_fat_once_verificate0" <?php if($ticket['is_fat_once_verificate'] == 0) {echo 'checked="checked"';}?>>
                            <label for="is_fat_once_verificate0">否</label>
                        </div>
                    </div>
                    <div class="once">
                    <label class="col-sm-1 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否一次取票:</div></label>
                    <div class="col-sm-2">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="1" name="is_fat_once_taken" id="is_fat_once_taken1" <?php if($ticket['is_fat_once_taken'] == 1) {echo 'checked="checked"';}?>>
                            <label for="is_fat_once_taken1">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="0" name="is_fat_once_taken" id="is_fat_once_taken0" <?php if($ticket['is_fat_once_taken'] == 0) {echo 'checked="checked"';}?>>
                            <label for="is_fat_once_taken0">否</label>
                        </div>
                        <i style="cursor:pointer" animation="true" class="fa fa-question-circle text-muted popovers" title=""  data-original-title="" data-container="body" data-toggle="popover"  data-trigger="hover" data-placement="top" data-html="true" data-content="一次验票：为了防止倒票，您可以开启此功能，开启后一张包含多个门票的订单第一次验票后，所有未使用的门票都会自动退款而不能被使用。
<br/>一次取票：对于线下有实体票的联票，为了防止在A景区换实体票后再去B
景区扫二维码入园，您可以开启此功能，开启后，在A景区验证换实体票后，其他景区不可以再扫二维码。"></i>
                    </div>
                    </div>
                    <?php endif;?>

                </div><!-- form-group -->

                <div class="form-group" id="shio">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left"></span>散客提前预定时间:</div></label>
                    <div class="col-sm-10">
                        <?php $day = floor($ticket['fat_scheduled_time'] / 86400);
                        $times = $ticket['fat_scheduled_time'] % 86400;
                        $hour[0] = intval($times / 3600);
                        $minutes = $times % 3600;
                        $hour[1] = $minutes / 60;
                        $time = implode(':', $hour);
                        ?>
                        <label class="pull-left" style="margin-top: 5px;">需在入园前&nbsp;</label> <input type="text"  tag="散客提前预定时间" class="spinner-day form-control validate[custom[number]]"  value="<?php echo $day;?>" name="fat_scheduled" style="cursor: pointer;cursor: hand;background-color: #ffffff"  onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/> 天的
                        <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker2" tag="散客提前预定时间" type="text" value="<?php echo $time;?>" class="form-control" name="fat_scheduledtime" style="width:50px"></div>
                        以前购买
                    </div>
                </div><!-- form-group -->
                <div class="form-group" id="shide">
                    <label class="col-sm-2 control-label"><div class="pull-right">散客产品说明</div></label>
                    <div class="col-sm-10">
                        <textarea id="fat_des" name='fat_description' placeholder="请输入您的门票说明..." class="form-control" rows="10"><?php echo $ticket['fat_description'];?></textarea>
                    </div>
                </div>






                <div class="form-group">
                    <div class="col-sm-2 control-label">
                        <div class="ckbox ckbox-primary pull-right" style="text-align: left;">
                            <input type="checkbox" name="is_full" value="1" id="checkboxPrimary12" style="vertical-align: middle;"  <?php if(!empty($ticket['is_full'])){ echo "checked=checked";}?>>
                            <label for="checkboxPrimary12" style="vertical-align: middle;"><span class="text-danger"></span>团队结算价:</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" placeholder="请输入价格" readonly tag="团队结算价"  class=" form-control onlyMoney" name="group_price" id="tg_price" value="<?php echo $ticket['group_price']?>">
                    </div>

                    <?php  if($orgInfo['partner_type'] < 1):?> 
                    <label class="col-sm-1 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否一次验票:</div></label>
                    <div class="col-sm-2">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="1"  name="is_group_once_verificate" id="is_group_once_verificate1" <?php if($ticket['is_group_once_verificate'] == 1) {echo 'checked="checked"';}?>>
                            <label for="is_group_once_verificate1">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="0" name="is_group_once_verificate" id="is_group_once_verificate0" <?php if($ticket['is_group_once_verificate'] == 0) {echo 'checked="checked"';}?>>
                            <label for="is_group_once_verificate0">否</label>
                        </div>
                    </div>
                     <div class="once">
                    <label class="col-sm-1 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否一次取票:</div></label>
                    <div class="col-sm-2">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="1" name="is_group_once_taken" id="is_group_once_taken1" <?php if($ticket['is_group_once_taken'] == 1) {echo 'checked="checked"';}?>>
                            <label for="is_group_once_taken1">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" value="0" name="is_group_once_taken" id="is_group_once_taken0" <?php if($ticket['is_group_once_taken'] == 0) {echo 'checked="checked"';}?>>
                            <label for="is_group_once_taken0">否</label>
                        </div>
                    </div>
                    </div>
                    <?php endif ;?>

                    <div class="col-sm-2">
                        最少订票 <input type="text" id="spinner-min" tag="最少订票" class="spinner" style="cursor: pointer;cursor: hand;background-color: #ffffff" name="mini_buy" value="<?php echo $ticket['mini_buy'];?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"> 张
                    </div>
                </div><!-- form-group -->

                <div class="form-group" id="groupShide">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left"></span>团队提前预定时间:</div></label>
                    <div class="col-sm-10">
                        <?php $day = floor($ticket['group_scheduled_time'] / 86400);
                        $times = $ticket['group_scheduled_time'] % 86400;
                        $hour[0] = intval($times / 3600);
                        $minutes = $times % 3600;
                        $hour[1] = $minutes / 60;
                        if($hour[1]==0) $hour[1]= '00';
                        $time = implode(':', $hour);
                        ?>
                        <label class="pull-left" style="margin-top: 5px;">需在入园前&nbsp;</label><input  tag="团队提前预定时间" type="text" class="spinner-day form-control validate[custom[number]]" value="<?php echo $day;?>" name="group_scheduled" style="cursor: pointer;cursor: hand;background-color: #ffffff"  onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/> 天的
                        <div class="bootstrap-timepicker inline-block input-group" style="vertical-align:middle"><input id="timepicker4" type="text" tag="团队提前预定时间" value="<?php echo $time;?>" class="form-control" name="group_scheduledtime" style="width:50px"></div>
                        以前购买
                    </div>
                </div><!-- form-group -->
                <div class="form-group" id="groupShowd">
                    <label class="col-sm-2 control-label"><div class="pull-right">团队产品说明</div></label>
                    <div class="col-sm-10">
                        <textarea id="group_des" name='group_description' placeholder="请输入您的门票说明..." class="form-control" rows="10"><?php echo $ticket['group_description']?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>门市挂牌价:</div></label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="请输入价格" tag="门市挂牌价"  class="form-control onlyMoney validate[required]" name="listed_price"  value="<?php echo $ticket['listed_price']?>" id="sk_price" />
                    </div>
                </div><!-- form-group -->
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>网络销售价:</div></label>
                    <div class="col-sm-4">
                        <input type="text" placeholder="请输入价格" tag="网络销售价"  class="form-control onlyMoney validate[required]" name="sale_price"  value="<?php echo $ticket['sale_price']?>" id="sk_price" />
                    </div>
                </div><!-- form-group -->
                
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否允许退票:</div></label>
                    <div class="col-sm-6">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  <?php if($ticket['refund']==1){echo 'checked="checked"'; }?> value="1"  name="refund" id="radioDefault33">
                            <label for="radioDefault33">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  <?php if($ticket['refund']==0){echo 'checked="checked"'; }?> value="0"   name="refund" id="radioDefault2">
                            <label for="radioDefault2">否</label>
                        </div>
                    </div>
                </div><!-- form-group -->
                
                 <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否发短信:</div></label>
                    <div class="col-sm-6">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" <?php if($ticket['message_open']==1){echo 'checked="checked"'; }?> value="1"  name="message_open" id="radioDefault3">
                            <label for="radioDefault3">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  <?php if($ticket['message_open']==0){echo 'checked="checked"'; }?> value="0"   name="message_open" id="radioDefault4">
                            <label for="radioDefault4">否</label>
                        </div>
                    </div>
                </div><!-- form-group -->
                
                 <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>是否门票验证:</div></label>
                    <div class="col-sm-6">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" <?php if($ticket['checked_open']==1){echo 'checked="checked"'; }?> value="1"  name="checked_open" id="radioDefault5">
                            <label for="radioDefault5">是</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" <?php if($ticket['checked_open']==0){echo 'checked="checked"'; }?> value="0"   name="checked_open" id="radioDefault6">
                            <label for="radioDefault6">否</label>
                        </div>
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>产品销售有效期:</div></label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="" tag="产品销售开始日期" class="form-control datepicker validate[required]" id='sale_available_1' name="sale_start_time" value='<?php if($ticket['sale_start_time']) echo date('Y-m-d',$ticket['sale_start_time']) ;?>' readonly style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff"> ~
                        <input type="text" placeholder="" tag="产品销售结束日期" class="form-control datepicker validate[required]" id='sale_available_2' name="sale_end_time" value='<?php if($ticket['sale_end_time']) echo date('Y-m-d',$ticket['sale_end_time']) ;?>' readonly style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff">
                    </div>
                    <div class="ckbox ckbox-primary" style="display:inline-block;margin-left:10px;display: none;">
                        <input type="checkbox" name="sale_limit" type="checkbox" value="1" <?php if(empty($ticket['sale_start_time'])&&empty($ticket['sale_end_time'])):?>checked="checked"<?php endif ?>  id="checkboxPrimary30">
                        <label for="checkboxPrimary30"><span class="text-danger"></span>不限期</label>
                    </div>
                </div><!-- form-group -->
                <div class="form-group">
                    <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger pull-left">*</span>使用有效期:</div></label>
                    <div class="col-sm-10"><?php list($a,$b)=explode(',',$ticket['date_available']);  ?>
                        <input type="text" placeholder="" tag="使用开始日期" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[1]"  value="<?php if($a) echo date('Y-m-d',$a);?>" readonly> ~
                        <input type="text" placeholder="" tag="使用结束日期" class="validate[required] form-control datepicker" style="width:120px;display:inline-block;cursor: pointer;cursor: hand;background-color: #ffffff" name="date_available[2]"  value="<?php if($b) echo date('Y-m-d',$b);?>" readonly>
                        &nbsp;&nbsp;预订游玩日期后 <input type="text" class="spinner-day" name="valid" value="<?php echo $ticket['valid']?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"> 天有效
                    <div class="ckbox ckbox-primary" style="display:inline-block;margin-left:10px">
                        <input type="checkbox" name="valid_flag" type="checkbox" value="1"  <?php if($ticket['valid_flag']):?> checked="checked"<?php endif?> id="checkboxPrimary42">
                        <label for="checkboxPrimary42"><span class="text-danger"></span>不限期</label>
                    </div>
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10 days-checkbox">
                        <div class="checkbox-group">
                            <?php $arr = explode(',', $ticket['week_time']); ?>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox"  id="d1" value="1" name="week_time[]" <?php if (in_array(1, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d1">周一</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d2" value="2" name="week_time[]" <?php if (in_array(2, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d2">周二</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d3" value="3" name="week_time[]" <?php if (in_array(3, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d3">周三</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d4" value="4" name="week_time[]" <?php if (in_array(4, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d4">周四</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d5" value="5" name="week_time[]" <?php if (in_array(5, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d5">周五</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d6" value="6" name="week_time[]" <?php if (in_array(6, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d6">周六</label>
                            </div>
                            <div class="ckbox ckbox-primary mr10 inline-block">
                                <input type="checkbox" id="d7" value="0" name="week_time[]" <?php if (in_array(0, $arr)) {
                                echo 'checked="checked"';
                            } ?>>
                                <label for="d7">周日</label>
                            </div>
                        </div>
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <div class="col-sm-2 control-label">
                        <div class="ckbox ckbox-primary pull-right">
                            <input type="checkbox" name="sms_tem" type="checkbox" value="1"  id="sms-tpl-checkbox" style="vertical-align: middle;" <?= $ticket['sms_template'] ? 'checked' : '' ?>>
                            <label for="sms-tpl-checkbox" style="vertical-align: middle;"><span class="text-danger"></span>使用短信模版</label>
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <style>
                            .sms-template{
                                display:none;
                            }
                            .sms-template .form-control{
                                margin:0;
                                width:130px;
                                display:inline-block;
                            }
                            .sms-template .type-default{
                                background:none;
                                border:1px solid #FBFBFB;
                                cursor:pointer;
                            }
                            .template-del-btn{
                                background:#549FC4;
                                border: 0;
                                border-radius: 2px;
                                display: inline-block;
                                height:32px;
                                line-height:32px;
                                margin: 0 6px;
                                text-align: center;
                                text-shadow: none;
                                width:30px;
                            }
                            .template-del-btn i{
                                color:#FFF;
                            }
                            #sms-template-content label{
                                display:inline-block;
                                margin:0 5px;
                            }

                            #sms-template-content{
                                border:1px solid #CCC;
                                min-height:100px;
                                padding:5px;
                                border-radius:5px;
                                background-color:#eee
                            }
                            #sms-template-content[contenteditable=true]{
                                box-shadow:0 2px 5px rgba(0, 0, 0, 0.2) inset;
                            }
                            #sms-template-content.default i{
                                display:none
                            }
                            #sms-template-content.default label{
                                background:none;
                                padding:0;
                                margin:0;
                                box-shadow:none;
                                text-shadow:none;
                                color:#707070;
                                font-weight:100;
                            }
                            .sms-template .var{
                                display:none;
                                padding:15px 0
                            }
                            .sms-template .var button{
                                margin-bottom:5px;
                            }
                            .sms-template .edit{
                                padding:15px 0
                            }
                            .sms-template .var label{
                                margin-right:10px
                            }
                            .box-header .btn-group{
                                float:right;
                            }
                            .box-header .btn-group button{
                                min-width:inherit;
                                line-height:29px;
                                border:0;
                            }
                            .box-header .btn-group .dropdown-toggle{
                                border-left:1px solid #D4D4D4;
                            }
                            .box-header .btn-group .dropdown-toggle .caret{
                                vertical-align: baseline;
                            }
                            #sms-template-table .selector{
                                width:200px;
                            }
                            .sms-template .dropdown-menu{
                                min-width:inherit;
                            }
                            .sms-template button{
                                min-width:inherit;
                            }
                            .save{
                                display:none
                            }
                            #sms-template-table td a{
                                margin-left:10px;
                            }
                            .sms-templates div label{
                                margin-left:10px;
                                vertical-align:middle
                            }
                            .sms-templates > div{
                                margin-bottom:10px;
                            }
                        </style>
                        <div class="sms-template"<?= $ticket['sms_template'] ? ' style="display:block;"' : '' ?>>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="padded">
                                        <textarea name="sms_con" style="display: none;" id="sms_con" cols="30" rows="10"></textarea>
                                        <div id="sms-template-content" maxlength="200" class="default"><?php
                                            if ($ticket['sms_template']) {
                                                echo str_replace(array('{{{', '}}}'), array('<label contenteditable="false" class="label label-default">', '<i class="fa fa-times"></i></label>'), $ticket['sms_template']);
                                            }
                                            else {
                                                ?>您已成功预订<label contenteditable="false" class="label label-default">产品名称<i class="fa fa-times"></i></label>门票<label contenteditable="false" class="label label-default">产品数量<i class="fa fa-times"></i></label>，<label contenteditable="false" class="label label-default">订单号<i class="fa fa-times"></i></label>，点击以下链接，至售票处展示二维码，工作人员扫描后即可入园。<label contenteditable="false" class="label label-default">二维码链接<i class="fa fa-times"></i></label>可于：<label contenteditable="false" class="label label-default">游玩日期<i class="fa fa-times"></i></label>游玩。<?php
                                            }?></div>
                                        <div class="var">
                                            <button class="btn btn-success btn-xs" type="button" data-toggle="tooltip" data-placement="top" title="该参数已重复">产品名称<i class="fa fa-plus"></i></button>
                                            <button class="btn btn-success btn-xs" type="button" data-toggle="tooltip" data-placement="top" title="该参数已重复">产品数量<i class="fa fa-plus"></i></button>
                                            <button class="btn btn-success btn-xs" type="button" data-toggle="tooltip" data-placement="top" title="该参数已重复">订单号<i class="fa fa-plus"></i></button>
                                            <button class="btn btn-success btn-xs" type="button" data-toggle="tooltip" data-placement="top" title="该参数已重复">二维码链接<i class="fa fa-plus"></i></button>
                                            <button class="btn btn-success btn-xs" type="button" data-toggle="tooltip" data-placement="top" title="该参数已重复">游玩日期<i class="fa fa-plus"></i></button>
                                            <label class="text-black">&nbsp;&nbsp;<span id="word" data="70">0/70</span></label>
                                        </div>
                                        <div class="edit">
                                            <button class="btn btn-success btn-sm" id="edit-template">编辑</button>
                                        </div>
                                        <div class="save">
                                            <button class="btn btn-primary btn-sm" id="save-template">保存</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .var button.disabled{
                            pointer-events:auto;
                            }
                        </style>
                        <script>
                            $(document).ready(function() {

                                // 初始化鼠标悬浮框
                                $('[data-toggle="popover"]').popover();

                                //是否使用短信模版
                                $('#sms-tpl-checkbox').click(function(){
                                    if($(this).is(':checked')){
                                        $('.sms-template').show()
                                    }else{
                                        $('.sms-template').hide()
                                    }
                                })


                                //编辑
                                $(document).on('click','.sms-templates input[type="radio"]',function(){
                                    $('#sms-template-content').html($(this).val())
                                    $('.sms-template .var').hide()
                                    $('.sms-template .edit').show()
                                    $('.sms-template .save').hide()
                                })

                                // 检测文本输入框是否有对应的文字的label
                                // return 如果有 返回对应的label
                                function contentHasLabel(text) {
                                    var labels = $("#sms-template-content>label");
                                    var ret = null;    // 是否有该文字的标签
                                    labels.each(function(k, lvalue) {
                                        labelText = $(lvalue).text();
                                        if(text == labelText) {
                                            ret = $(lvalue);
                                            return false;
                                        }
                                    });
                                    return ret;
                                }
                                
                                // 检测文本输class=var是否有对应的文字的button
                                // return 如果有 返回对应的button
                                function varHasButton(text) {
                                    var labels = $(".var>button");
                                    var ret = null;    // 是否有该文字的标签
                                    labels.each(function(k, bvalue) {
                                        buttonText = $(bvalue).text();
                                        if(text == buttonText) {
                                            ret = $(bvalue);
                                            return false;
                                        }
                                    });
                                    return ret;
                                }
                                // 初始化预定义文字按钮与输入框之中的一对一关系
                                // 预定义文字按钮中的文字只能存在一个在编辑文本中
                                function initLabelButton() {
                                    
                                    var buttons = $(".var>button");
                                    buttons.each(function(k, bvalue) {
                                        if(contentHasLabel($(bvalue).text())) {
                                            $(bvalue).addClass("disabled").tooltip();
                                            
                                        } else{
                                            $(bvalue).removeClass("disabled").tooltip('destroy');
                                        }
                                    });
                                }

                                // 获取字符统计处理函数
                                function summaryText() {
                                    // 获取最大字符数
                                    var maxLength = parseInt($("#word").attr("data"));
                                    var oldHtml = $("#sms-template-content").html();
                                    return function() {
                                        var text = $("#sms-template-content").html().replace('<br>','').replace(/<label.*?<\/label>/ig,'');
                                        if( text.length > maxLength){
                                            $("#sms-template-content").html(oldHtml);
                                            $("#sms-template-content").focus();
                                        } else {
                                            initLabelButton();
                                            $("#word").text(text.length+"/"+maxLength);
                                        }
                                        oldHtml = $("#sms-template-content").html();
                                    };
                                }

                                // 获取字符统计函数
                                var summaryWords = summaryText();

                                initLabelButton();
                                summaryWords();

                                // 短信模板的字符统计
                                $('#sms-template-content').on("keyup click", summaryWords);
                                $("#sms-template-content").on("cut copy paste",function(e){
                                    return false;
                                })
                                

                                $('#edit-template').click(function(){
                                    $('#sms-template-content').attr('contenteditable','true').removeClass('default');
                                    $('.sms-template .var').show();
                                    $('.sms-template .edit').hide();
                                    $('.sms-template .save').show();
                                    return false
                                })

                                //保存
                                $('#save-template').click(function(){
                                    var obj = $('#sms-template-content')
                                    $('.sms-template .var').hide()
                                    $('.sms-template .edit').show()
                                    $('.sms-template .save').hide()
                                    obj.addClass('default')
                                    obj.removeAttr('contenteditable')
                                    obj.html()
                                    return false
                                })

                                function pasteHtmlAtCaret(html) {
                                    var sel, range;
                                    if (window.getSelection) {
                                        sel = window.getSelection();
                                        if (sel.getRangeAt && sel.rangeCount) {
                                            range = sel.getRangeAt(0);
                                            range.deleteContents();
                                            var el = document.createElement("div");
                                            el.innerHTML = html;
                                            var frag = document.createDocumentFragment(), node, lastNode;
                                            while ( (node = el.firstChild) ) {
                                                lastNode = frag.appendChild(node);
                                            }
                                            range.insertNode(frag);

                                            if (lastNode) {
                                                range = range.cloneRange();
                                                range.setStartAfter(lastNode);
                                                range.collapse(true);
                                                sel.removeAllRanges();
                                                sel.addRange(range);
                                            }
                                        }
                                    } else if (document.selection && document.selection.type != "Control") {
                                        document.selection.createRange().pasteHTML(html);
                                    }
                                }
                                
                                $('.var button').click(function(){
                                    if($(this).is('.disabled')){
                                            return false;
                                    }
                                    var text = $(this).text();
                                    $('#sms-template-content').focus();
                                    pasteHtmlAtCaret('<label contenteditable="false" class="label label-default">'+text+'<i class="fa fa-times"></i></label>');
                                    summaryWords(); // 重新统计字数
                                    if(contentHasLabel($(this).text())) {       // 是否添加label文字成功
                                        $(this).addClass("disabled");   // 一次使用 添加成功，不可再使用
                                    }
                                    return false;
                                })


                                $('#sms-template-content').on('click','label i',function(e){
                                    // e.stopPropagation();
                                    $(this).parents('label').remove();
                                })

                                $('#timepicker2').timepicker({showMeridian: false}).on('show.timepicker', function(e) {
                                        $(this).val(e.time.value)
                                    });
                                $('#timepicker4').timepicker({showMeridian: false}).on('show.timepicker', function(e) {
                                        $(this).val(e.time.value)
                                    });
                            });
                        </script>
                    </div>
                </div>
                <?php if($orgInfo['partner_type'] >= 1):?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><div class="pull-right"><span class="text-danger">*</span>对接系统产品ID:</div></label>
                        <div class="col-sm-3">
                            <input type="hidden" name="partner_type" id="partner_type" value="1"/>
                            <input class="form-control" type="text" name="partner_product_code" id="partner_product_code" value="<?php echo isset($ticket['partner_product_code']) ? $ticket['partner_product_code'] : 0; ?>"/>
                        </div>
                    </div>
                <?php endif;?>

            </div><!-- panel-body -->
            <div class="panel-footer"><button type="submit" class="btn btn-primary"  id="form-button">保存</button> </div>
        </form>

    </div>
</div>
<script type="text/javascript">
    
	$('#sms-template-content').keydown(function(e) {
		if( parseInt($('#sms-template-content').text().length) > 200   && e.keyCode != 8) {
			return false;
		}
	});

                        
    var spinner = jQuery('.spinner').spinner({'min': 1});
    //spinner.spinner('value', 1);

    $('select').eq(-1).select2({
        // minimumResultsForSearch: -1
    });
    $('select').eq(-2).select2({
        //minimumResultsForSearch: -1
    });
    $('select').eq(-3).select2({
        //minimumResultsForSearch: -1
    });

    // Time Picker
//    jQuery('#timepicker').timepicker({defaultTIme: false});
//    jQuery('#timepicker2').timepicker({showMeridian: false});
//    jQuery('#timepicker4').timepicker({showMeridian: false});
//    jQuery('#timepicker3').timepicker({minuteStep: 15});

    // Date Picker
    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
        yearRange: "1995:2065",
		beforeShow: function(d){
			setTimeout(function(){
				$('.ui-datepicker-title select').select2({
					minimumResultsForSearch: -1
				});
			},0)
		},
		onChangeMonthYear: function(){
			setTimeout(function(){
				$('.ui-datepicker-title select').select2({
					minimumResultsForSearch: -1
				});
			},0)
		},
        onClose: function(dateText, inst) { 
            $('.select2-drop').hide(); 
        }
    });
    jQuery('#datepicker-inline').datepicker();
    jQuery('#datepicker-multiple').datepicker({
        numberOfMonths: 3,
        showButtonPanel: true
    });

    var spinnerDay = jQuery('.spinner-day').spinner({'min': 0});
    //spinnerDay.spinner('value', 1);


    var tpl = '<tr>' +
            '<td>' +
            '<div class="form-group" style="margin:0">' +
            '<select class="select2 lan" data-placeholder="Choose One" style="width:200px;padding:0 10px;">' +
            '<option value="">请选择景区</option>' +<?php foreach ($supplyLans as $item): ?>
        '<option value = "<?php echo $item['landscape_id'] ?>"><?php
    if (isset($lanLists[$item['landscape_id']])) {
        echo $lanLists[$item['landscape_id']]['name'];
    }
    ?></option>' +
<?php endforeach; ?>
    '</select>' +
            '</div>' +
            '</td>' +
            '<td>' +
            '<div class="form-group" style="margin:0">' +
            '<select class="select2 poi" data-placeholder="Choose One" style="width:200px;padding:0 10px;">' +
            '<option value="">请选择门票</option>' +
            '</select>' +
            '</div>' +
            '</td>' +
            '<td class="sale_price">0.00</td><td><input type="text" name="" class="spinner_num" style="cursor: pointer;cursor: hand;background-color: #ffffff" onkeyup="this.value=this.value.replace(/\D/g,\'\')" onafterpaste="this.value=this.value.replace(/\D/g,\'\')" /></td>' +
            '<td><div class="btn btn-danger btn-xs" id="take-ticket-add">删除</div></td></tr>';

    $(function() {
        jQuery('.spinner_num').spinner({'min': 1});
        //spinner.spinner('value', 1);
        $('select.lan,select.poi').select2({});
        $('#take-ticket-add').click(function() {
            var ticketBody = $("#take-ticket").append(tpl);
            /*jQuery('.select2').select2({
             minimumResultsForSearch: -1
             });*/
            ticketBody.children().last().find('select').select2({});

            var spinner = jQuery('.spinner_num').last().spinner({'min': 1});
            spinner.spinner('value', 1);

        });

        //不限期
        $('[name=sale_limit]').click(function() {
            if ($(this).prop('checked')) {
                $('#sale_available_1').val('').attr("disabled", true);
                $('#sale_available_2').val('').attr("disabled", true);
            } else {
                $('#sale_available_1').attr("disabled", false);
                $('#sale_available_2').attr("disabled", false);
            }
        });
        //如果选择了团散客
        window.setInterval(function() {
            if ($('[name=is_fit]').prop('checked')) {
                $('input[name="fat_price"]').removeAttr("readonly").addClass('validate[required]');
                $('input[name="fat_scheduled"]').addClass('validate[required]');
                $('input[name="fat_scheduledtime"]').addClass('validate[required]');
                $("#shide").show();
                $("#shio").show();
                $('[name=is_fit]').parents('.form-group').find('[type=radio]').removeAttr('disabled');
            } else {
                $("#shide").hide();
                $("#shio").hide();
                $('input[name="fat_price"]').val("");
                $('input[name="fat_scheduled"]').val("");
                $('input[name="fat_scheduledtime"]').val("");
                $('#fat_des').val("");
                $('input[name="fat_price"]').attr("readonly", "readonly").removeClass('validate[required]');
                $('[name=is_fit]').parents('.form-group').find('[type=radio]').attr('disabled', 'disabled');
            }

            if ($('[name=is_full]').prop('checked')) {
                $('input[name="group_price"]').removeAttr("readonly").addClass('validate[required]');
                $('#spinner-min').removeAttr("readonly").css('background-color','#ffffff');
                $('input[name="group_scheduled"]').addClass('validate[required]');
                $('input[name="group_scheduledtime"]').addClass('validate[required]');
                $("#groupShide").show();
                $("#groupShowd").show();
                $('[name=is_full]').parents('.form-group').find('[type=radio]').removeAttr('disabled');
            } else {
                $("#groupShide").hide();
                $("#groupShowd").hide();
                $('input[name="group_scheduled"]').val("");
                $('input[name="group_scheduledtime"]').val("");
                $('#group_des').val("");
                $('input[name="group_price"]').val("");
                $('input[name="group_price"]').attr("readonly", "readonly").removeClass('validate[required]');
                 $('#spinner-min').attr("readonly", "readonly").val(1).css('background-color','#eeeeee');
                $('[name=is_full]').parents('.form-group').find('[type=radio]').attr('disabled', 'disabled');
            }
            
           if ($('[name=valid_flag]').prop('checked')) {
                $('input[name="valid"]').attr("readonly", "readonly").val(0).css('background-color','#eeeeee');
            }else{
                $('input[name="valid"]').removeAttr("readonly").css('background-color','#ffffff');
            }
            
            if ($('[name=sale_limit]').prop('checked')) {
                $('input[name="sale_start_time"]').attr("readonly", "readonly").removeClass('validate[required]').css('background-color','#eeeeee');
                $('input[name="sale_end_time"]').attr("readonly", "readonly").removeClass('validate[required]').css('background-color','#eeeeee');
            }else{
               $('input[name="sale_start_time"]').removeAttr("readonly").addClass('validate[required]').css('background-color','#ffffff');
               $('input[name="sale_end_time"]').removeAttr("readonly").addClass('validate[required]').css('background-color','#ffffff');
            }
            
            //一次性取票是否显示
            var _lanCount = [];
            $('select.lan').each(function(){
                if($(this).val()){
                    _lanCount.push($(this).val());
                }
            });
           _lanCount = unique(_lanCount);
           if(_lanCount.length>1){
               $('.once').show();
           }else{
               $('.once').hide();
           }
        }, 200);

        //散团客点击后默认
        $('[name=is_fit]').click(function(){
           if ($('[name=is_fit]').prop('checked')) {
               if($('input[name="fat_scheduled"]').val()=='')
                  $('input[name="fat_scheduled"]').val(0);
               if($('input[name="fat_scheduledtime"]').val()=='')
                  $('input[name="fat_scheduledtime"]').val('<?php echo date('H:i') ?>');
            }
        });
        
        $('[name=is_full]').click(function(){
           if ($('[name=is_full]').prop('checked')) {
               if($('input[name="group_scheduled"]').val()=='')
                  $('input[name="group_scheduled"]').val(0);
               if($('input[name="group_scheduledtime"]').val()=='')
                  $('input[name="group_scheduledtime"]').val('<?php echo date('H:i') ?>');
            }
        });
        
        
        //提示设置
        $('#form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true
        });
        //提交表单
        $('#form-button').click(function() {        
            if (!$('[name=is_fit]').prop('checked') && !$('[name=is_full]').prop('checked')) {
                alert('团队价和散客价至少选一个!');
                return false;
            }
            if($('#save-template').is(":hidden") == false){
                alert('请先保存短信模板!');
                return false;
            }
            var _flag = false;
            $('select.lan').each(function() {
                if ($(this).val() === '') {
                    alert('请选择景区!');
                    $(this).select2("open");
                    _flag = true;
                    return false;
                }
            });
            if(typeof($("#partner_type").val())!='undefined' && $('#partner_product_code').val()==''){
                alert('请先输入对接产品ID!');
                return false;
            }
            if (_flag) {
                return false;
            }

            var _flag = false;
            $('select.poi').each(function() {
                if ($(this).val() === '') {
                    alert('请选择门票');
                    $(this).select2("open");
                    _flag = true;
                    return false;
                }
            });
            if (_flag) {
                return false;
            }
			
            var cont = $('#sms-template-content');
            var data = cont.html();
            $('#sms_con').val(data);
            if ($('[name=sms_tem]').prop('checked')) {
                $('#sms-template-content label').html('');
                if ($('#sms-template-content').text().length > 200) {
                    $('#sms-tpl-checkbox').PWShowPrompt('短信字符超出长度限制');
                    return false;
                }
                cont.html(data);
            }


            
            var obj = $('#form');
            if (obj.validationEngine('validate') === true) {
                $('#form-button').attr('disabled', true);
                $.post('/ticket/single/edit', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-button').attr('disabled', false);
                    } else {
                        alert('修改产品成功',function(){
                           setTimeout(function(){location.partReload();},1000);
                        });
                    }
                }, 'json');
            }
            return false;
        });
    });

    //多次加载问题
    $(function() {        
        
        // 添加区分样式，去除第一个选项以外的所有选项颜色显示
        function selectClass(select2, className) {
            var selectOption = select2.prev("div").find(".select2-chosen");
            if(selectOption.text() != select2.children().first().text()) {
                selectOption.addClass(className);
            } else {
                selectOption.removeClass(className);
            }
        }
        function initSelectClass(select2s){
            selectClass($('select.lan'), "text-black");
            selectClass($('select.poi'), "text-black");
        }
        initSelectClass();  // 为select2中的非第一个元素添加text-black类型

        if (window['edit.js'])
            return;
        window['edit.js'] = true;

        //景区改变
        var ticketTypes = <?php echo json_encode(TicketType::model()->findAll()) ?>;
        $(document).on('change', 'select.lan', function() {

            selectClass($(this), "text-black");
            
            var lan_id = $(this).val();
            if (lan_id === '') {
                //保存之前的值
                var $poi = $(this).parents('tr').find('select.poi');
                select_old[$poi.val()] = $poi.find("option:selected").text();
                $poi.children('option').eq(0).attr("selected", true).nextAll().remove();
                $poi.trigger('change');
                return false;
            }
            var that = this;
            $.post('/ticket/single/getbase/', {id: lan_id}, function(data) {
                var _data = data['msg'];
                var _html = '';
                for (i in _data) {
                    _html += '<option sale_price="' + _data[i]['sale_price'] + '" value="' + _data[i]['id'] + '">' + _data[i]['name'] + '(' + ticketTypes[_data[i]['type']]['name'] + ')' + '</option>';
                }
                var _select = $(that).parents('tr').find('select.poi');
                  _select.children('option').eq(0).attr("selected", true).nextAll().remove().end().after(_html);
                $("select.poi").each(function() {
                    if ($(this).val() != '') {
                        _select.find('option[value=' + $(this).val() + ']').remove();
                    }
                });
            }, 'json');
            return false;
        });


        $("#take-ticket").on('click', '.btn-danger', function() {
            var $poi = $(this).parents('tr').find('select.poi');
            $poi.trigger('select2-open');
            $poi.val('');
            $poi.trigger('change');
            $(this).parents('tr').remove()
        });
        //下拉改变值后不能再选
        window.select_old;
        window.select_old_lan;
        $(document).on('select2-open', 'select.poi', function() {
            //存储select修改之前的值
            select_old = {};
            select_old[$(this).val()] = $(this).find("option:selected").text();            
            select_old_lan = {};
            var $lan = $(this).parents('tr').find('select.lan');
            select_old_lan = $lan.val();
        });

        $(document).on('select2-open', 'select.lan', function() {
            //存储select修改之前的值
            select_old_lan = $(this).val();
        });

        //子景点改变
        $(document).on('change', 'select.poi', function() {
            
            selectClass($(this), "text-black");
            
            var op_val = $(this).val();
            if (op_val) { //禁止其它选项再选当前选项
                $("select.poi").not(this).find('option[value=' + op_val + ']').remove();
                //给数值name重新赋值
                $(this).parents('tr').find('.spinner_num').attr('name', 'base_items[' + op_val + ']');
                $(this).parents('tr').find('.sale_price').html($(this).find("option:selected").attr('sale_price'));
            } else {
                $(this).parents('tr').find('.sale_price').html('0.00');
            }

            if (typeof select_old != 'undefind') {
                for (i in select_old) {
                    if (typeof i !== 'undefind' && i !== '' && op_val !== i) {
                        $("select.poi").not(this).each(function() {
                            if ($(this).parents('tr').find('select.lan').val() != select_old_lan) {
                                return true;
                            }
                            var len = $(this).find('option').length - 1;
                            $(this).find('option').each(function(index) {
                                if ($(this).attr('value') != '' && $(this).attr('value') > i) {
                                    $(this).before('<option value="' + i + '">' + select_old[i] + '</option>');
                                    return false;
                                }
                                if (len === index) {
                                    $(this).parents("select.poi").append('<option value="' + i + '">' + select_old[i] + '</option>');
                                }
                            });
                        });
                    }
                }
            }
            return false;
        });
    });
</script>
