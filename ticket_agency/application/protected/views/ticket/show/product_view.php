<?php
use common\huilian\models\Widgets;
$this->breadcrumbs = array('门票管理', '门票详情');
?>
<div class="modal-header" style="width:900px">
    <h4 class="modal-title pull-left"><?php echo $ticket['name'] ?></h4>
    <span class="text-danger pull-right" style="font-size: 25px;margin-right: 15px;margin-top: -5px"><del>￥<?php echo $ticket['sale_price'] ?></del>&nbsp￥<?php echo $_GET['price_type'] ? $ticket['group_price'] : $ticket['fat_price'] ?><br/><a class="btn btn-primary pull-right" href=".bs-example-modal-lg" onclick="buy('<?php echo $ticket['id'] ?>',<?php echo $_GET['price_type'] ?>);" data-toggle="modal">购买</a></span>

    <div class="clearfix"></div>
</div>
<div class="panel-body" style="width:900px">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">产品说明</h4>
        </div>
        <div class="panel-body">
            <p>
                <?php echo $ticket['remark']; ?>
            </p>
        </div><!-- panel-body -->
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">

            <h4 class="panel-title">包含内容</h4>
        </div>
        <div class="panel-body">
            <?php
            if (!empty($ticket['items'])):
                //得到所有景区名称
                $supplylanIds = PublicFunHelper::arrayKey($ticket['items'], 'scenic_id');
                $param['ids'] = join(',', $supplylanIds);
                $data = Landscape::api()->lists($param);
                $param['items'] = 100000;
                $lanLists = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');

                $base_scenic_items = PublicFunHelper::ArrayByKeys($ticket['items'], 'scenic_id');
                $_POST['ticketBase'] = array(); //单例
                foreach ($base_scenic_items as $base_scenic_item):
                    ?>
                    <h2><?php
                        if (isset($lanLists[$base_scenic_item[0]['scenic_id']])) {
                            echo $lanLists[$base_scenic_item[0]['scenic_id']]['name'];
                        }
                        ?></h2>
                        
                    <?php
                    foreach($base_scenic_item as $base_item):
                        ?>
            <p class="lead" style="margin-top: 10px;"><?php $_model =TicketType::model()->findByPk($base_item['type']);echo $base_item['base_name'].'('.$_model['name'].')';?>&nbsp;<?php echo $base_item['num']?>张</p>
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <th>包含景点</th>
                                <th>
                                    <?php
                                    if (strlen($base_item['view_point']) > 0) {
                                        $rs = Poi::api()->lists(array('ids' => $base_item['view_point'], 'items' => 100000), true);
                                        $datas = ApiModel::getLists($rs);
                                        $spans = array();
                                        foreach ($datas as $data) {
                                            $spans[]  = $data['name'] ;
                                        }
                                        echo join(' 、 ',$spans );
                                    }
                                    ?>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <?php endforeach; ?>
                <?php
                endforeach;
            endif;
            ?>
            <br/>
           <?= Widgets::httpReferer() ?>
        </div><!-- panel-body -->
    </div>
</div>
<!--购买票开始-->
<div class="modal fade bs-example-modal-lg" id="verify-modal-buy" tabindex="-1" role="dialog"></div>

<script type="text/javascript">
    function buy(id, price_type) {
        $('#verify-modal-buy').html();
        $.get('/ticket/buy/?id=' + id + '&price_type=' + price_type, function(data) {
            $('#verify-modal-buy').html(data);
        });
    }
</script>
<!--购买票结束-->