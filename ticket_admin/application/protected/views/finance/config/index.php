<?php
$this->breadcrumbs = array('结算管理', '结算配置');
?>

<div class="contentpanel">
    <div id="show_msg"></div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"> 系统配置 </h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" class="clearPart" id="setting-form">
            <div class="form-group" style="width: 160px;">
                <label>平台统一结算周期配置:</label>
                
            </div>
            <div class="form-group" style="width:120px;">
                <select class="select2" data-placeholder="Choose One" style="width:250px;padding:0 10px;" name="account_cycle" id="account_cycle"  onchange="changeDayShow(this.value)">
                    <option value="undefined" <?php if ($config['conf_bill_type'] == 'undefined'): ?>selected="selected"<?php endif ?>>请选择结算周期</option>
                    <option value="month" <?php if ($config['conf_bill_type'] == '0'): ?>selected="selected"<?php endif ?>>月结算</option>
                    <option value="week" <?php if ($config['conf_bill_type'] == '1'): ?>selected="selected"<?php endif ?>>周结算</option>
                </select>
            </div>
            <div class="form-group"  style="width:120px;">
                <select class="select2" data-placeholder="Choose One" style="width:250px;padding:0 10px;" name="account_cycle_day" id="account_cycle_day">
                    <option value="__NULL__">请选择结算日</option>
                    <?php if ($config['conf_bill_type'] == '0'): ?>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if ($config['conf_bill_value'] == $i): ?>selected="selected"<?php endif ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    <?php endif; ?>

                    <?php if ($config['conf_bill_type'] == '1'): ?>
                        <?php foreach ($weekArray as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php if ($config['conf_bill_value'] == $key): ?>selected="selected"<?php endif ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-sm" type="submit" id="setting-form-button">确定</button>
            </div>
        </form>

        <form class="form-inline" id="other-form">
            <div class="form-group"  style="width: 160px;">
                <label>特殊供应商结算周期设置:</label>
            </div>
            <div class="form-group" style="width:120px;">
                <select class="select2" data-placeholder="Choose One" style="width:250px;padding:0 10px;" name="account_cycle" id="account_cycle_0"  onchange="changeDayShowMore(this.value, 0)">
                    <option value="" <?php if ($config['conf_bill_type'] == 'undefined'): ?>selected="selected"<?php endif ?>>请选择结算周期</option>
                    <option value="month" <?php if ($config['conf_bill_type'] == '0'): ?>selected="selected"<?php endif ?>>月结算</option>
                    <option value="week" <?php if ($config['conf_bill_type'] == '1'): ?>selected="selected"<?php endif ?>>周结算</option>
                </select>
            </div>
            <div class="form-group" style="width:120px;">
                <select class="select2" data-placeholder="Choose One" style="width:250px;padding:0 10px;" name="account_cycle_day" id="account_cycle_day_0">
                    <option value="">请选择结算日</option>
                    <?php if ($config['conf_bill_type'] == '0'): ?>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if ($config['conf_bill_value'] == $i): ?>selected="selected"<?php endif ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    <?php endif; ?>

                    <?php if ($config['conf_bill_type'] == '1'): ?>
                        <?php foreach ($weekArray as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php if ($config['conf_bill_value'] == $key): ?>selected="selected"<?php endif ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group" style="width:250px;">
                <select data-placeholder="Choose One" style="width:450px;padding:0 10px;" id="supply" name="supply_id">
                    <option value=''>请输入供应商名称</option>
                    <?php
                    if ($orgList):
                        ?>
                        <?php
                        foreach ($orgList as $item):
                            ?> 
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-sm" id="other-form-button">确定</button>
            </div>
        </form>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"> 特殊供应商结算周期设置列表 </h4>
        </div>
        <div class="panel-body">
            <table class="table table-normal ">
                <thead>   

                    <tr>
                        <td>供应商编号</td>
                        <td>供应商名称</td>
                        <td>结算周/月</td>
                        <td>结算日</td>
                        <td>改动日期</td>
                        <td>操作人</td>
                        <td>操作</td>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($list):
                        ?>
                        <?php
                        foreach ($list as $item):
                            ?>     
                            <tr id="supply_<?php echo $item['org_id']; ?>">
                                <td><?php echo $item['org_id']; ?></td>
                                <td><?php echo $item['org_name'];?></td>
                                <td id="account_box_<?php echo $item['org_id'] ?>"><?php echo $item['balance_type'] == 1 ? '周结算' : '月结算' ?></td> 
                                <td id="account_box_day_<?php echo $item['org_id'] ?>"><?php
                                    if ($item['balance_type'] == 1) {
                                        echo Bill::$weekArray[$item['balance_cycle']];
                                    } else {
                                        echo $item['balance_cycle'];
                                    }
                                    ?></td>
                                <td><?php echo date('Y-m-d H:i:s', $item['updated_at']) ?></td>
                                <td><?php echo $item['admin_name'] ?></td>
                                <td style="width:60px;">
                                    <a href="#" class="edit btn btn-primary btn-bordered btn-xs" onclick="singleEdit(<?php echo $item['org_id'] ?>,this);return false;">编辑</a> 
                                    <a href="#" class="save btn btn-success btn-bordered btn-xs" style="display:none;"  onclick="singleSave(<?php echo $item['org_id'] ?>);return false;">保存</a> 
                                    <a href="#" class="btn btn-danger btn-bordered btn-xs clearPart" onclick="singleDel(<?php echo $item['org_id'] ?>);return false;">删除</a>
                                </td> 
                        <input type="hidden" name="supply_id" value="<?php echo $item['org_id'] ?>" />
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
var phpvars = {};
phpvars.week_tpl = '';
phpvars.month_tpl = '';
<?php foreach ($weekArray as $weekkey => $weekvalue): ?>
phpvars.week_tpl += '<option value="<?php echo $weekkey; ?>"><?php echo $weekvalue; ?></option>';
<?php endforeach; ?>

<?php for ($j = 1; $j <= 31; $j++): ?>
phpvars.month_tpl += '<option value="<?php echo $j; ?>"><?php echo $j; ?></option>';
<?php endfor; ?>
$(function() {
    $('#other-form-button').click(function() {
        if ($.trim($('#other-form [name=account_cycle]').val()) === '') {
            alert('请选择结算周期');
            return false;
        }

        if ($.trim($('#other-form [name=account_cycle_day]').val()) === '') {
            alert('请选择结算日');
            return false;
        }

        var _supply_id = $.trim($('#other-form [name=supply_id]').val());
        if (_supply_id === '') {
            alert('请选择供应商');
            return false;
        }

        if ($('#supply_' + _supply_id).length > 0) {
            alert('此供应商已存在特殊供应商结算周期设置列表中');
            $("html,body").delay(500).animate({scrollTop: $('#supply_' + _supply_id).offset().top}, 300);
            return false;
        }

        $.post('/finance/config/savesupplyconfig', $('#other-form').serialize(), function(data) {
            if (data.errors) {
                alert(data.errors[0]);
                return false;
            }
            //alert('添加成功');
            window.location.partReload();
        }, "json");
        return false;
    });

    // Select2
    jQuery('.select2').select2({
        minimumResultsForSearch: -1
    });
    jQuery('#supply').select2();
    
});
//修改结算日的格式
function changeDayShowMore(type, id)
{
    var obj = $('#account_cycle_day_' + id);
    var default_option = '<option value="__NULL__">请选择结算日</option>';
    if (type == 'month') {
        obj.html(default_option + phpvars.month_tpl);
    } else if (type == 'week') {
        obj.html(default_option + phpvars.week_tpl);
    } else {
        obj.html('<option value="__NULL__">请选择结算日</option>');
    }
    $.uniform.update('#account_cycle_day_' + id);
}

function singleEdit($id,eventObj) {
    var _html = '';
    _html += '<select class="uniform select2" name="account_cycle" id="account_cycle_' + $id + '"  onchange="changeDayShowMore(this.value,' + $id + ')">';
    _html += '<option value="month">月结算</option>'
    _html += '<option value="week">周结算</option>'
    _html += '</select>';
    $('#account_box_' + $id).html(_html);

    var obj = $('#account_box_day_' + $id);
    var default_option = '<select class="uniform select2" name="account_cycle_day" id="account_cycle_day_' + $id + '"><option value="">请选择结算日</option>';
    obj.html(default_option + phpvars.month_tpl + '</select>');
    $.uniform.update('#account_cycle_day_' + $id);
    $(eventObj).hide().siblings('.save').show();
    $('#account_cycle_' + $id).select2({minimumResultsForSearch: -1});
    $('#account_cycle_day_' + $id).select2({minimumResultsForSearch: -1});
}

function  singleSave($id){
    var account_cycle = $.trim($('#supply_'+$id+' [name=account_cycle]').val()) ;
    if (account_cycle=== '') {
        alert('请选择结算周期');
        return false;
    }
    var account_cycle_day = $.trim($('#supply_'+$id+' [name=account_cycle_day]').val()) ;
    if ( account_cycle_day === '') {
        alert('请选择结算日');
        return false;
    }

    $.post('/finance/config/savesupplyconfig', {account_cycle:account_cycle,account_cycle_day:account_cycle_day,supply_id:$id}, function(data) {
        if (data.errors) {
            alert(data.errors[0]);
            return false;
        }
        alert('保存成功');
        window.location.partReload();
    }, "json");
    return false;
}

function singleDel($id){
    PWConfirm('确定要删除么？', function () {
        $.post('/finance/config/savesupplyconfig', {account_cycle:0,account_cycle_day:0,supply_id:$id}, function(data) {
            if(data.errors){
                setTimeout(function() {
                    alert('删除结算配置失败!'+data.errors[0]);
                }, 500);
            }else{
                setTimeout(function() {
                    alert('删除成功!', function() {
                        window.location.partReload();
                    });
                }, 500)
            }
        },"json");
    });
}
</script>
<script src="/js/jquery.uniform.min.js"></script>
<script src="/js/finance/config.js"></script>