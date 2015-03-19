<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h6 id="modal-formLabel"><?php echo $landscape['name']; ?>审核</h6>
</div>
<div id="verify_return"></div>

<!--before审核开始-->
<div class="modal-body select">
    <div class="container-fluid">

        <div class="row-fluid">
            <!-- 已审核信息 -->
            <div class="span6">
                <div class="box">
                    <div class="box-header">
                        <?php if ($landscapeLast && $landscape['status'] == 'unaudited' || $landscape['status'] != 'unaudited'): ?>
                            <span class="title" style="color:green;">已审核信息</span>
                        <?php else: ?>
                            <span class="title" style="color:red;">待审核信息</span>
                        <?php endif; ?>
                    </div>
                    <div class="box-content" style="word-break:break-all;word-wrap:break-word;">
                        景区名称：<?php echo $landscape['name']; ?><br/>
                        景区级别：<?php echo $landscape['level']['name'] ?><br/>
                        联系电话：<?php echo $landscape['phone']; ?><br/>
                        所在城市：
                        <?php if ($landscape['districts']): ?>
                            <?php foreach ($landscape['districts'] as $key => $val): ?>
                                <?php echo $val['name']; ?>
                            <?php endforeach; ?>
                        <?php endif; ?><br/>
                        详细地址：<?php echo $landscape['address']; ?><br/>
                        取票地址：<?php echo $landscape['exaddress']; ?><br/>
                        开放时间：<?php echo $landscape['hours']; ?><br/>
                        景区介绍：<?php echo $landscape['biography']; ?><br/>
                        购票须知：<?php echo $landscape['note']; ?><br/>
                        交通指南：<?php echo $landscape['transit']; ?><br/>
                    </div>
                </div>
            </div>

            <!-- 未审核信息 -->
            <?php if ($landscapeLast && $landscape['status'] == 'unaudited'): ?>
                <div class="span6">
                    <div class="box">
                        <div class="box-header">
                            <span class="title" style="color:red;">待审核信息</span>
                        </div>
                        <div class="box-content">
                            景区名称：<?php echo $landscapeLast['name']; ?><br/>
                            景区级别：<?php echo $landscapeLast['level']['name'] ?><br/>
                            联系电话：<?php echo $landscapeLast['phone']; ?><br/>
                            所在城市：
                            <?php if ($landscapeLast['districts']): ?>
                                <?php foreach ($landscapeLast['districts'] as $key => $val): ?>
                                    <?php echo $val['name']; ?>
                                <?php endforeach; ?>
                            <?php endif; ?><br/>
                            详细地址：<?php echo $landscapeLast['address']; ?><br/>
                            取票地址：<?php echo $landscapeLast['exaddress']; ?><br/>
                            开放时间：<?php echo $landscapeLast['hours']; ?><br/>
                            景区介绍：<?php echo $landscapeLast['biography']; ?><br/>
                            购票须知：<?php echo $landscapeLast['note']; ?><br/>
                            交通指南：<?php echo $landscapeLast['transit']; ?><br/>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


        </div>

        <?php if ($landscape['status'] == 'unaudited'): ?>
            <div class="row-fluid">
                <a href="#" class="btn btn-green" onclick="verifyPoi(<?php echo $landscape['id']; ?>, 'normal')">审核通过</a>
                <a href="#" class="btn btn-red" onclick="verifyPoi(<?php echo $landscape['id']; ?>, 'failed')">驳回</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<!--before审核结束-->
<script>

    function verifyPoi(landscape_id, status)
    {
        $.post('index.php?c=landscape&a=verify', {id: landscape_id, status: status}, function(data) {
            if (data.errors) {
                var tmp_errors = '';
                $.each(data.errors, function(i, n) {
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                $('#verify_return').html(warn_msg);
            } else if (data['data'][0]['id']) {
                var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
                $('#verify_return').html(succss_msg);
                setTimeout("location.href='landscape_lists.html'", '2000');
            }
        }, "json");
        return false;
    }
</script>