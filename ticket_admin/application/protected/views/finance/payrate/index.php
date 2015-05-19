<?php 
$this->breadcrumbs = array('结算管理','费率设置');
?>

<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">费率设置</h4>
        </div>
        <table class="table table-bordered table1">
            <thead>
                <tr>
                    <th>名称</th>
                    <th>费率</th>
                    <th colspan="3">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(!empty($payrate)){
                foreach ($payrate as $value){?>
                <tr payname="<?php echo $value['name'];?>" payment="<?php echo $value['payment'];?>">
                    <td style="padding-left:20px;" class="payname">
                        <?php echo $value['name'];?>
                        当前费率：
                    </td>
                    <td><?php echo $value['rate']*100;?><span>%</span></td>
                    <td>
                        <span>修改费率：</span>
                    </td>
                    <td>
                        <input class="input-small rate" type="text" size="5">
                        <span>%</span>
                    </td>
                    <td><button onclick="saveRate(this);" class="btn btn-primary btn-xs">保存费率</button></td>
                </tr>
                <?php }}else{?>
                <tr><td colspan="5" style="text-align:center">暂无数据</td></tr>
                <?php }?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
function saveRate(obj)
{
    var $payment = $(obj).parents('tr').attr('payment');
    var $name = $(obj).parents('tr').attr('payname');
    var $rate = $(obj).parents('tr').find('.rate').val();
    if(isNaN($rate)){
        alert('费率必须为数字类型');
    }else{
        $.post('/finance/payrate/setting',{payment:$payment,rate:$rate,name:$name},function(data){
            if(data.error===0){
                alert(data.msg,function(){location.partReload();});
            }else{
                alert(data.msg);
            }
        }, 'json'); 
    }
    
    
}
</script>