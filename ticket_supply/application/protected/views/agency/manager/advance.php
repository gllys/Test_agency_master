<div class="contentpanel">
    <style>
        .table tr>*{
            text-align:center
        }
    </style>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">储值管理</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline">
                <!--<div class="mb10">
                    <div class="inline-block">
                        <div class="form-group" style="width:160px">
                            当前透支额度： <?php echo $balance_over; ?> 元
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="over-number" placeholder="输入大于0的数值 "type="text">
                        </div>

                        <button type="button" id="save-over-credit" class="btn btn-primary btn-xs">保存透支额度</button>
                    </div>
                </div>-->
                <div>
                    <div class="inline-block">
                        <div class="form-group" style="width:160px">
                            增加储值：
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="advance_number" placeholder="输入大于0的数值 "type="text">
                        </div><!-- form-group -->

                        <div class="form-group">
                            <input class="form-control" id="advance-remark" placeholder="操作原因" type="text" style="width:480px;">
                        </div>
                        <button type="button" id="advance-btn"  class="btn btn-primary btn-xs">保存</button>
                    </div>
                </div>
            </form>
        </div><!-- panel-body -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">储值调整记录</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline">
                <div class="form-group">
                    <input type="hidden" id="credit-over-id" name="id" value="<?php echo $id; ?>">
                    <input class="form-control" name="remark" value="<?php echo isset($_GET['remark'])?$_GET['remark']:"";?>" placeholder="操作原因" type="text" style="width:480px;">
                </div>
                <button type="submit" class="btn btn-primary btn-xs">查询</button>
            </form>
        </div><!-- panel-body -->
    </div>


    <div class="panel panel-default">
        <table class="table table-bordered mb30">
            <thead>
            <tr>
                <th>时间</th>
                <th>操作员</th>
                <th>操作储值</th>
                <th>原因</th>
            </tr>
            </thead>
            <tbody>
            <?php if(empty($lists)): ?>
                <tr><td colspan="4">暂无相关数据</td></tr>
            <?php else: ?>
                <?php foreach($lists as $item):?>
                    <tr>
                        <td><?php echo date("Y-m-d H:i:s",$item['add_time']) ?></td>
                        <?php $user = Users::model()->findByPk($item['user_id']); ?>
                        <td><?php echo empty($user['name'])?$user['account']:$user['name']; ?></td>
                        <td><?php echo $item['balance_money']>0?"+".$item['balance_money']:$item['balance_money']; ?></td>
                        <td><?php echo $item['remark'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <div style="text-align:center" class="panel-footer">
            <a class="btn btn-primary btn-xs" href="/agency/manager">返回</a>
            <div id="basicTable_paginate" style="float:right;margin:0" class="pagenumQu">
                <?php
                $this->widget('CLinkPager', array(
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
</div><!-- contentpanel -->

<script>
    $('#over-number').keyup(function(){
        var number = parseFloat($(this).val());
        if(number<=0){
            $(this).val("");
            alert("透支额度只能是大于0的数值");
            return false;
        }
    });
    $('#save-over-credit').click(function(){
        var id = $('#credit-over-id').val();
        var number = parseFloat($('#over-number').val());
        $.post('/agency/manager/over',{id:id,money:number},function(data){
            if(data.error==0){
                alert("保存成功");
                location.reload();
            }else{
                alert("保存失败,"+data.msg);
            }
        },'json');
    });
    $('#advance-btn').click(function(){
        var id = $('#credit-over-id').val();
        var number = parseFloat($('#advance_number').val());
        var remark = $('#advance-remark').val();
        if(number<=0 || isNaN(number)){alert("储值只能是不小于0的数值");return false;}
        if(remark==""){alert("原因不能为空");}
        $.post('/agency/manager/saveAdvance',{id:id,num:number,remark:remark},function(data){
            if(data.error===0){
                alert("保存成功");
                location.reload();
            }else{
                alert("保存失败,"+data.msg);
            }
        },'json');
    });
</script>