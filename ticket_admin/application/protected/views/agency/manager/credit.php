<div class="contentpanel">
    <style>
        .table tr > * {
            text-align: center
        }
    </style>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">信用调整</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" id="credit-form">
                <div class="mb10">
                    <div class="form-group">
                        <div class="rdio rdio-default">
                            <input type="radio" id="checkboxPrimary1" value="1" name="credit">
                            <label for="checkboxPrimary1">无限信用</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="rdio rdio-default">
                            <input type="radio" checked="checked" id="checkboxPrimary2" value="0" name="credit" class="credit-btn">
                            <label for="checkboxPrimary2">有限信用</label>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="credit-wrap inline-block">
                        <div class="form-group">
                            <select class="select2" data-prompt-position="bottomLeft:90,80" id="credit-type" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                                <option value="">调整方式</option>
                                <option value="1">增加信用</option>
                                <option value="0">减少信用</option>
                            </select>
                        </div>
                        <!-- form-group -->

                        <div class="form-group">
                            <input class="form-control" id="credit-number" placeholder="输入大于0的数值 " type="text">
                        </div>
                        <!-- form-group -->
                        <div class="form-group">
                            <input class="form-control" id="credit-remark" placeholder="操作原因" type="text" style="width:480px;">
                        </div>
                    </div>
                    
                   <div class="mb10">
                       <div style="padding-top:20px;" class="form-group">
                           <label for="checkboxPrimary1">结算周期配置</label>
                       </div>
                       <div class="form-group">
                           <select class="select2" data-prompt-position="bottomLeft:230,130" data-validation-engine="form-control" id="account_cycle" name="account_cycle" onchange="changeDayShow(this.value)" >
                               <option value="">请选择结算周期</option>
                               <?php if($info['checkout_type'] != ''){?>

                                   <option value="1"  <?php   if($info['checkout_type'] == '1'){ echo "selected";}?>>月结算</option>
                                   <option value="0" <?php   if($info['checkout_type'] == '0'){ echo "selected";}?>>周结算</option>
                               <?php  }  else {?>
                                   <option value="1">月结算</option>
                                   <option value="0">周结算</option>
                               <?php }  ?>
                           </select>
                       </div>
                       <div class="form-group">
                           <select class="select2" data-prompt-position="bottomLeft:310,130" data-validation-engine="form-control" name="account_cycle_day" id="account_cycle_day">
                               <option value="">请选择结算日</option>
                           </select>
                       </div>
                    </div>


                    
                    <input type="hidden" name="id" value="<?php echo $id; ?>" id="credit-id">
                    <button type="button" class="btn btn-primary btn-xs" id="save-credit-btn">保存</button>
                </div>
            </form>
        </div>
        <!-- panel-body -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">信用调整记录</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline">
                <div class="form-group">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input class="form-control" name="remark" value="<?php echo isset($_GET['remark']) ? $_GET['remark'] : ""; ?>" placeholder="操作原因" type="text" style="width:200px;">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm">查询</button>
                </div>
            </form>
        </div>
        <!-- panel-body -->
    </div>


    <div class="panel panel-default">
        <table class="table table-bordered mb30">
            <thead>
                <tr>
                    <th>时间</th>
                    <th>操作员</th>
                    <th>操作信用</th>
                    <th>原因</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lists)): ?>
                    <tr><td colspan="4">暂无相关数据</td></tr>
                <?php else: ?>
                    <?php foreach ($lists as $item): ?>
                        <tr>
                            <td><?php echo date("Y-m-d H:i:s", $item['add_time']) ?></td>
                            <?php $user = Users::model()->findByPk($item['user_id']); ?>
                            <td><?php echo empty($user['name']) ? $user['account'] : $user['name']; ?></td>
                            <td><?php echo $item['credit_moeny'] > 0 ? "+" . $item['credit_moeny'] : $item['credit_moeny']; ?></td>
                            <td><?php echo $item['remark'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="panel-footer">
            <a class="btn btn-default" href="/agency/manager">返回</a>
            <div id="basicTable_paginate" style="float:right;margin:0" class="pagenumQu">
                <?php
                $this->widget('common.widgets.pagers.ULinkPager', array(
                    'cssFile' => '',
                    'header' => '',
                    'prevPageLabel' => '上一页',
                    'nextPageLabel' => '下一页',
                    'firstPageLabel' => '',
                    'lastPageLabel' => '',
                    'pages' => $pages,
                    'maxButtonCount' => 5, //分页数量
                        )
                );
                ?>
            </div>
        </div>

    </div>
</div>
<!-- contentpanel -->
<script>
    
    var weeks = {0:"周日",1:"周一",2:"周二",3:"周三",4:"周四",5:"周五",6:"周六"};
    var d= new Date();
    var dayCount = new Date(d.getFullYear(), d.getMonth()+1,0).getDate();  
    
       //修改结算日的格式
    function changeDayShow(type)
    {
        var obj = $('#account_cycle_day');
        var default_option = '<option value="">请选择结算日</option>';
        if(type == 0){
            for(var i=0;i<7;i++){
                default_option += "<option value='"+i+"'>"+weeks[i]+"</option>";
            }
            obj.html(default_option);
        }else if(type == 1){
            for(var i=1;i<=dayCount;i++){
                default_option += "<option value='"+i+"'>"+i+"号</option>";
            }
            obj.html(default_option);
        }else{
            obj.html('<option value="">请选择结算日</option>');
        }
    }


$(function(){
    var html = '<option value="">请选择结算日</option>';
    var type = '<?php echo $info['checkout_type']; ?>';
    var date = '<?php echo $info['checkout_date']; ?>'
    if(type=='0'){
        for(var i=0;i<7;i++){
            html += "<option "+(date==i?"selected":"")+" value='"+i+"'>"+weeks[i]+"</option>";
        }
    }else if(type=='1'){
        for(var i=1;i<=dayCount;i++){
            html += "<option "+(date==i?"selected":"")+" value='"+i+"'>"+i+"号</option>";
        }
    }
    $("#account_cycle_day").html(html);
})


</script>
<script>
    jQuery(document).ready(function() {
        $('input:radio[name="credit"]').click(function() {
            var obj = $('.credit-wrap')
            if ($(this).val() == 0) {
                obj.show()
            } else {
                obj.hide()
            }
        })

        $('#account_cycle').change(function(){
            $('#account_cycle_day').select2('val','');
        })

        $('#credit-form').validationEngine({
            promptPosition: 'topRight',
            autoHideDelay: 3000
        });

        $('#save-credit-btn').click(function() {
            var credit = $('input:radio[name="credit"]:checked').val();
            if (credit == undefined) {
                alert("请选择要调整的信用额度类型");
                return false;
            }
            
            if($('#account_cycle').val()==""){
                $('#account_cycle').validationEngine('showPrompt','请选择结算周期','error');
                return false;
            }
            if($('#account_cycle_day').val()==""){
                $('#account_cycle_day').validationEngine('showPrompt','请选择结算日','error');
                return false;
            }
            
            var id = $('#credit-id').val();
            if (credit == 1) {
                var data = {id: id, infinite: credit,'checkout_type':$('#account_cycle').val(),'checkout_date':$('#account_cycle_day').val()};
            } else {
                var type = $('#credit-type').find('option:checked').val();
                var number = parseFloat($('#credit-number').val());
                var remark = $('#credit-remark').val();
                if (type == "" || type == undefined) {
                    $('#credit-type').validationEngine('showPrompt','请选择调整方式','error');
                    return false;
                }
                if (number <= 0 || isNaN(number)) {
                    $('#credit-number').validationEngine('showPrompt','请输入正确的金额','error');
                    return false;
                }
                if (remark == "" || remark == undefined) {
                    $('#credit-remark').validationEngine('showPrompt','请输入调整的原因','error');
                    return false;
                }
                var data = {id: id, infinite: credit, type: type, num: number, remark: remark,'checkout_type':$('#account_cycle').val(),'checkout_date':$('#account_cycle_day').val()};
            }
            $.post('/agency/manager/saveCredit/', data, function(data) {
                if (data.error === 0) {
                    alert("保存成功",function(){location.partReload();});
                } else {
                    alert("保存失败," + data.msg);
                }

            }, 'json');
        });




        // Select2
        jQuery("#select-basic, #select-multi").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

    });

</script>