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
            <form class="form-inline" id="advance-form">
                <div>
                    <div class="form-inline">
                        <div class="form-group">
                            <label>增加储值：</label>
                            <input class="form-control" id="advance_number" placeholder="大于0的值"  type="text">
                        </div>
                        <div class="form-group" style="border:none !important;">
                            <input class="form-control" id="advance-remark" placeholder="操作原因" style="width:200px;" type="text">
                        </div>
                        <div class="form-group"  style="border:none !important;">
                            <button type="button" id="advance-btn" class="btn btn-primary btn-sm">保存</button>
                        </div>
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
                    <input class="form-control" name="remark" value="<?php echo isset($_GET['remark'])?$_GET['remark']:"";?>" placeholder="操作原因" type="text" style="width:200px;">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm">查询</button>
                </div>
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
</div><!-- contentpanel -->

<script>
    jQuery(document).ready(function() {
        $('#advance-form').validationEngine({
            promptPosition: 'topRight',
            autoHideDelay: 3000
        });

        $('#over-number').keyup(function () {
            var number = parseFloat($(this).val());
            if (number <= 0) {
                $(this).val("");
                alert("透支额度只能是大于0的数值");
                return false;
            }
        });
        $('#save-over-credit').click(function () {
            var id = $('#credit-over-id').val();
            var number = parseFloat($('#over-number').val());
            $.post('/agency/manager/over', {id: id, money: number}, function (data) {
                if (data.error == 0) {
                    alert("保存成功", function () {
                        location.partReload();
                    });
                } else {
                    alert("保存失败," + data.msg);
                }
            }, 'json');
        });



        $('#advance-btn').click(function () {
            var id = $('#credit-over-id').val();
            var number = parseFloat($('#advance_number').val());
            var remark = $('#advance-remark').val();
            if (number <= 0 || isNaN(number)) {
                $('#advance_number').validationEngine('showPrompt', '请输入正确的金额', 'error');
                return false;
            }
            if (remark == "") {
                $('#advance-remark').validationEngine('showPrompt', '请输入相应的原因', 'error');
                return false;
            }
            $.post('/agency/manager/saveAdvance', {id: id, num: number, remark: remark}, function (data) {
                if (data.error === 0) {
                    alert("保存成功", function () {
                        location.partReload();
                    });
                } else {
                    alert("保存失败," + data.msg);
                }
            }, 'json');
        });
    });
</script>