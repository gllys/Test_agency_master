<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h6 id="modal-formLabel">检票记录</h6>
</div>
<div class="modal-body select">
    <div class="container-fluid">
        <div class="box">
            <table class="table table-normal">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>检票终端</td>
                        <td>检票数量</td>
                        <td>硬件编号</td>
                        <td>操作员</td>
                        <td>验票时间</td>
                        <td>成功状态</td>
                        <td>日志</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($list as $item):
                    ?>
                    <tr>
                        <td><?php echo $item['id']?></td>
                        <td><?php echo $item['uid']?'pos终端':'闸机' ?></td>
                        <td><?php echo $item['num']?></td>
                        <td><?php echo $item['equipment_code']?></td>
                        <td><?php if ($_model = Load::model('Users')->getID($item['uid']))echo $_model['name']?></td>
                        <td><?php echo $item['created_at']?></td>
                        <td><?php echo $item['status']?'<font color="green">成功</font>':'<font color="red">失败</font>'?></td>
                        <td><?php echo $item['note']?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>