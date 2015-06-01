<?php
$item = current($items);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">修改门票</h4>
        </div>
        <form method="post" action="#" id="form" class="form-horizontal form-bordered">
            <input type="hidden" name="gid" value="<?php echo $_GET['gid'] ?>" />
            <div class="modal-body">

                <div class="form-group" style="overflow: inherit;">
                    <label class="col-sm-2 control-label" style="margin: 0px;">门票名称:</label>
                    <div class="col-sm-10">
                        <input type="text" class="validate[required] form-control" value="<?php echo $item['name'] ?>" tag="门票名称" name="name" maxlength="33" style="" placeholder="请输入门票名称" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="overflow: inherit;">
                    <label class="col-sm-2 control-label" style="margin: 0px;">景区名称</label>
                    <div class="col-sm-4">
                        <?php
                        //todo optimize
                        $rs = Landscape::api()->detail(array('id' => $item['scenic_id']), true);
                        $data = ApiModel::getData($rs);
                        echo isset($data['name']) ? $data['name'] : ''
                        ?>
                    </div>
                </div><!-- form-group -->

                <div class="form-group" style="overflow: inherit;">
                    <label class="col-sm-2 control-label">包含景点:</label>
                    <div class="col-sm-10" id="jingdianTag" data-prompt-position="topLeft">
                        <div class="form-group" style="margin:0" id="appendto">
                            <?php
                            $rs = Poi::api()->lists(array('landscape_ids' => $item['scenic_id'], 'items' => 100000,'status'=>1), true);
                            $datas = ApiModel::getLists($rs);
                            $view_point = explode(',', $item['view_point']);
                            foreach ($datas as $key => $_item) {
                                echo '<div class="ckbox ckbox-primary pull-left" style="margin-right: 5px; min-width:100px;" >'
                                . '<input type="checkbox" value="' . $_item['id'] . '" id="remember' . $_item['id'] . '" ' . (in_array($_item['id'], $view_point) ? 'checked="checked"' : '') . ' class="view_point" name="view_point[]">'
                                . '<label for="remember' . $_item['id'] . '">' . $_item['name'] . '</label></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered mb30" >
                    <thead>
                        <tr>
                            <th>类型</th>
                            <th>价格</th>
                        </tr>
                    </thead>
                    <tbody id="take-ticket">
                        <?php
                        foreach ($items as $_item):
                            ?>
                            <tr>
                                <td><?php
                                    $_model = TicketType::model()->findByPk($_item['type']);
                                    echo $_model['name']
                                    ?></td>
                                <td>
                                    <input type="hidden"  name="items[<?php echo $_item['id'] ?>][id]" value="<?php echo $_item['id'] ?>" class="validate[custom[number]]"/>
                                    <input type="hidden"  name="items[<?php echo $_item['id'] ?>][type]" value="<?php echo $_item['type'] ?>" class="validate[custom[number]]"/>
                                    <input type="text"  name="items[<?php echo $_item['id'] ?>][sale_price]" value="<?php echo $_item['sale_price'] ?>" class="onlyMoney"/>
                                </td>
                            <tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" id="form-button" class="btn btn-success">保存</button>
                <button  class="btn btn-default" aria-hidden="true" data-dismiss="modal" type="button">取消</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        //提交表单
        $('#form-button').click(function() {
            var obj = $('#form');
            if ($('#form .view_point:checked').length < 1) {
                 $('#jingdianTag').PWShowPrompt('至少选择一个景点'); 
                return false;
            }
            if (obj.validationEngine('validate') == true) {
                $('#form-button').attr('disabled', true);
                $.post('/ticket/goods/edit/', obj.serialize(), function(data) {
                    if (data.error) {
                        alert(data.msg);
                        $('#form-button').attr('disabled', false);
                    } else {
                        alert('修改门票成功',function(){ window.location.partReload();}); 
                    }
                }, 'json');
            }
            return false;
        });
    });
</script>