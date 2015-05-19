<?php
$item = current($items);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">查看门票</h4>
        </div>
        <form method="post" action="#" id="form">
            <div class="modal-body">

                <div class="form-group">
                    <label class="col-sm-2 control-label" style="margin: 0px;">门票名称:</label>
                    <div class="col-sm-10">
                        <?php echo  $item['name']?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" style="margin: 0px;">景区名称:</label>
                    <div class="col-sm-4">
                        <?php
                        //todo optimize
                        $rs = Landscape::api()->detail(array('id' => $item['scenic_id']), true);
                        $data = ApiModel::getData($rs);
                        echo isset($data['name']) ? $data['name'] : ''
                        ?>
                    </div>
                </div><!-- form-group -->

                <div class="form-group">
                    <label class="col-sm-2 control-label">包含景点:</label>
                    <div class="col-sm-10">
                        <div class="form-group" style="margin:0;word-break:break-all;" id="appendto">
                            <?php
                            if (strlen($item['view_point']) > 0) {
                                $rs = Poi::api()->lists(array('ids' => $item['view_point'],'items'=>100000), true);
                                $datas = ApiModel::getLists($rs);
                                $spans = array();
                                foreach ($datas as $data) {
                                   echo '<div class="ckbox ckbox-primary pull-left" style="margin-right: 5px; min-width:100px;" >'.$data['name'].'</div>';
                                }
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
                        foreach($items as $_item):
                        ?>
                        <tr>
                            <td><?php $_model = TicketType::model()->findByPk($_item['type']);echo $_model['name'] ?></td>
                            <td><?php echo $_item['sale_price'] ?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button  class="btn btn-default" aria-hidden="true" data-dismiss="modal" type="button">关闭</button>
            </div>
        </form>
    </div>
</div>