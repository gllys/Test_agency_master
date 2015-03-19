<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h6 id="modal-formLabel"><?Php echo $landscape['name'] ?>所有子景区列表</h6>
</div>
<div id="verify_return"></div>

<!--before审核开始-->
<div class="modal-body select">
    <div class="container-fluid">

        <div class="box-content">
            <table class="table table-normal">
                <thead>
                    <tr>
                        <td>编号</td>
                        <td>子景区名称</td>
                        <td>景区说明</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($list as $item):
                        ?>
                        <tr>
                            <td><?php echo $item['id'] ?></td>
                            <td><?php echo $item['name'] ?></td>
                            <td><?php echo $item['description'] ?></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
