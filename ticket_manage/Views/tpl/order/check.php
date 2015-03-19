<!DOCTYPE html>
<html>
    <?php get_header(); ?>
    <body>
        <?php get_top_nav(); ?>
        <div class="sidebar-background">
            <div class="primary-sidebar-background"></div>
        </div>
        <?php get_menu(); ?>
        <div class="main-content">
            <?php get_crumbs(); ?>
            <div id="show_msg"></div>	

            <style>
                .table-normal td{
                    text-align:center
                }
            </style>
            <div class="container-fluid padded">
                <div class="box">
                    <div class="box-header">
                        <span class="title"><i class="icon-list-ol"></i> 验票</span>
                    </div>

                    <div class="table-header" style="height:auto;padding-bottom:10px;">
                        <form action="">
                            <div class="row-fluid" style="margin-bottom:10px;">
                                <input type="text" placeholder="电子编码/票号" name="hash" value="<?php echo $get['hash']; ?>" style="width:400px;margin:0 10px 0 0">
                                <button class="btn btn-default" style="float:none;">查询</button>
                            </div>
                        </form>
                    </div>
                    <?php if ($data['status'] == 0): ?>
                        <div style="padding:20px;font-size:18px;" class="row-fluid">
                            <?php echo $data['msg']; ?>
                        </div>
                        <?php
                    else:
                        $order = $data['msg']['order'];
                        ?>
                        <!--订单信息开始-->
                        <div style="margin-left:0px;" class="box">
                            <div class="box-header">
                                <span class="title">订单信息</span>
                            </div>
                            <div class="box-content">
                                <table class="table table-normal">
                                    <tbody>
                                        <tr>
                                            <td style="background: #F3F4F8;">订单号:</td>
                                            <td style="background: #FFF;"><?php echo $order['order_id'] ?></td>

                                            <td style="background: #F3F4F8;">景区名称：</td>
                                            <td style="background: #FFF;"><?php if ($_model = Load::model('Landscapes')->getID($order['landscape_id'])) echo $_model['name'] ?></td>

                                            <td style="background: #F3F4F8;">创建时间:</td>
                                            <td style="background: #FFF;"><?php echo $order['created_at'] ?></td>

                                            <td style="background: #F3F4F8;">有效期：</td>
                                            <td style="background: #FFF;"><?php echo date('Y-m-d', strtotime($order['expire_start_at'])) . '~' . date('Y-m-d', strtotime($order['expire_end_at'])) ?></td>

                                            <td style="background: #F3F4F8;">使用时间：</td>
                                            <td style="background: #FFF;"><?php echo $order['useday'] ?></td>

                                            <td style="background: #F3F4F8;">有效天数：</td>
                                            <td style="background: #FFF;"><?php echo $order['use_expire'] ?></td>
                                        </tr>

                                        <tr>
                                            <td style="background: #F3F4F8;">使用日期：</td>
                                            <td style="background: #FFF;"><?php echo str_replace('0','7',$order['weekly']); ?></td>

                                            <td style="background: #F3F4F8;">可使用子景点：</td>
                                            <td style="background: #FFF;" rowspan="6"><?php
                                                $_lists = Load::model('TicketRelation')->getList(array('order_id' => $order['order_id']), '', '', 'distinct relate_poi');
                                                foreach ($_lists as $item) {
                                                    if ($_model = Load::model('Poi')->getID($item['relate_poi']))
                                                        echo $_model['name'] . ',';
                                                }
                                                ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--订单信息结束-->
                        <!--票信息开始-->
                        <div style="margin-left:0px;" class="box">
                            <div class="box-header">
                                <span class="title">订单信息</span>
                            </div>
                            <div class="box-content">
                                <table class="table table-normal">
                                    <thead>
                                        <tr>
                                            <td>票号</td>
                                            <td>已使用子景点</td>
                                            <td style="background: #F3F4F8;">票状态</td>
                                        </tr>
                                    </thead>
                                    <tbody>  

                                        <?php
                                        $tickets = $data['msg']['tickets'];
                                        foreach ($tickets as $item):
                                            ?>
                                            <tr>
                                                <td style="background: #F3F4F8;"><?php echo $item['id'] ?></td>
                                                <td style="background: #F3F4F8;"><?php
                                                //得到使用票下的子景点
                                                $_lists = Load::model('TicketUsed')->getList(array('ticket_id' => $item['id']), '', '', 'poi_id,count(poi_id) as num','poi_id');
                                                foreach ($_lists as $item) {
                                                    if ($_model = Load::model('Poi')->getID($item['poi_id'])){//('.$item['num'].')
                                                        echo $_model['name'] . ',';
                                                    }
                                                }
                                                ?></td>
                                                <td style="background: #F3F4F8;"><?php 
                                                    if($item['status']==1){
                                                        echo '可使用' ;
                                                    }else{
                                                        echo '未付款或退款过程中' ;
                                                    }
                                                ?></td>
                                            </tr>
                                            <?php
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--票信息开始-->

                    <?php endif; ?>

                </div>
            </div>
            <link href="Views/css/daterangepicker.css" rel="stylesheet">
            <script src="Views/js/vendor/date.js"></script>
            <script src="Views/js/vendor/moment.js"></script>
            <script src="Views/js/vendor/daterangepicker.js"></script>
            <script type="text/javascript" src="Views/js/order/check.js"></script> 
        </div>
    </body>
</html>