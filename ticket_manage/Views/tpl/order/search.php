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
                div.selector{
                    margin:0 10px;
                    width:200px;
                }
                .table-normal tbody td a{
                    margin:0 5px;
                    text-decoration:none;
                }
                .table-normal button{
                    min-width:inherit;
                }
                .table-normal tbody td{
                    text-align:center
                }
                .modal .table-normal tbody td{
                    text-align:left
                }
                .table-normal .dropdown-menu{
                    left:auto;
                    right:0;
                }
                .order-list{
                    width:3000px;
                    max-width:inherit
                }	
                #return-ticket,#postpone{
                    width:800px;
                    margin-left:-400px;
                }
                #return-ticket .table-normal:nth-child(2) td,#postpone .table-normal:nth-child(2) td{
                    width:25%;
                }

                #sms textarea{
                    width:100%;
                    height:100px;
                }
            </style>

            <div class="container-fluid padded">
                <div class="box">
                    <div class="box-header">
                        <span class="title"><i class="icon-list"></i> 订单列表</span>
                    </div>
                    <div class="table-header" style="height:auto;padding-bottom:10px;">
                        <form action="">
                            <div class="row-fluid" style="margin-bottom:10px;">
                                下单时间：<input type="text" placeholder="" value="<?php echo $get['created_at'] ?>" name="created_at" style="width:170px;margin:0 10px 0" class="form-time">
                                游玩时间：<input type="text" placeholder="" value="<?php echo $get['useday'] ?>" name="useday" style="width:170px;margin:0 10px 0" class="form-time">
                                取票人电话：<input type="text" placeholder="" value="<?php echo $get['owner_mobile'] ?>" name="owner_mobile" style="width:120px;margin:0 10px 0">
                                一级票务名称：<input type="text" placeholder="" value="<?php echo $get['landscape_name'] ?>" name="landscape_name" style="width:150px;margin:0 10px 0">
                            </div>
                            <div class="row-fluid" style="margin-bottom:10px;">
                                支付方式：<select class="uniform" name="payment">
                                    <option  selected="selected" value="">全部</option>
                                    <?php foreach ($payments = OrderCommon::getPayments() as $type => $name): ?>
                                        <option value="<?php echo $type; ?>" <?php if ($get['payment'] == $type): ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                订单状态：<select class="uniform" name="status">
                                    <option  selected="selected" value="">全部</option>
                                    <?php foreach ($allStatus as $allStatusKey => $allStatusVal): ?>
                                        <option value="<?php echo $allStatusKey; ?>" <?php if ($get['status'] == $allStatusKey): ?>selected="selected"<?php endif; ?>><?php echo $allStatusVal; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                订单编号：<input type="text" placeholder="" name="hash" value="<?php echo $get['hash'] ?>" style="width:150px;margin:0 10px 0">
                                <button class="btn btn-default" style="float:none;">搜索</button>
                            </div>
                        </form>
                    </div>


                    <div class="content">
                        <table class="table table-normal order-list">
                            <thead>
                                <tr>
                                    <td>操作</td>
                                    <td>订单编号</td>
                                    <td>门票名称</td>
                                    <td>门票类型</td>
                                    <td>取票人</td>
                                    <td>联系电话</td>
                                    <td>分销商</td>
                                    <td>供应商</td>
                                    <td>是否合作关系</td>
                                    <td>支付方式</td>
                                    <td>订单状态</td>
                                    <td>订购数量</td>
                                    <td>结算价</td>
                                    <td>合作机构价</td>
                                    <td>总金额</td>
                                    <td>游玩时间</td>
                                    <td>结束时间</td>
                                    <td>下单时间</td>
                                    <td>支付时间</td>
                                    <td>短信发送次数</td>
                                    <td>订单备注</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($orderList)): ?>
                                    <?php foreach ($orderList as $key => $order): ?>
                                        <tr>
                                            <td>
                                                <a title="退票" href="#return-ticket" onclick="modal_jump_refund('<?php echo $order['id']; ?>')" data-toggle="modal"><i class="icon-reply"></i></a>
                                                <a title="改期" href="#postpone"  onclick="modal_jump_useday('<?php echo $order['id']; ?>')" data-toggle="modal"><i class="icon-time"></i></a>
                                                <a title="检票记录" href="#wicket-record" onclick="modal_jump_record('<?php echo $order['id']; ?>')" data-toggle="modal"><i class="icon-list-ol"></i></a>
                                                <a title="短信" href="#sms" onclick="modal_jump_sms('<?php echo $order['owner_mobile'] ?>')" data-toggle="modal"><i class="icon-tablet"></i></a>
                                            </td>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo $order['landscape']['name']; ?></td>
                                            <td><?php echo $order['order_item'][0]['name']; ?></td>
                                            <td><?php echo $order['owner_name']; ?></td>
                                            <td><?php echo $order['owner_mobile'] ?></td>
                                            <td><?php echo $order['buyer_organization']['name']; ?></td>
                                            <td><?php echo $order['seller_organization']['name']; ?></td>
                                            <td><?php echo $order['is_partner'] ? '是' : '否'; ?></td>
                                            <td><?php echo OrderCommon::getPayments($order['payment']); ?></td>
                                            <td><?php echo OrderCommon::getOrderRealShowStatus($order['status'], $order['pay_status']); ?></td>
                                            <td><?php echo $order['nums']; ?></td>
                                            <td><?php echo $order['order_item'][0]['sale_price']; ?></td>
                                            <td><?php echo $order['order_item'][0]['price']; ?></td>
                                            <td><?php echo $order['amount']; ?></td>
                                            <td><?php echo !empty($order['order_item'][0]['useday'])?$order['order_item'][0]['useday']:''; ?></td>
                                            <td><?php echo $order['order_item'][0]['expire_end_at']; ?></td>
                                            <td><?php echo $order['created_at']; ?></td>
                                            <td><?php echo $order['pay_at']; ?></td>
                                            <td>0</td>
                                            <td>
                                                <button class="popover-btn btn btn-default" data-content="<?php echo $order['remarks']; ?>" data-placement="bottom" data-toggle="popover" data-container="body" type="button" data-original-title="" title=""><i class="icon-list-alt"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="23">暂无订单信息</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination; ?>
                </div>

            </div>
        </div>


        <div id="return-ticket" class="modal hide fade"><!-- 退票 --></div>

        <div id="postpone" class="modal hide fade"><!-- 改期--></div>

        <div id="wicket-record" class="modal hide fade" style="width:900px; margin-left: -450px;"><!-- 检票记录 --></div>

        <!-- 短信 -->
        <div id="sms" class="modal hide fade">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h6 id="modal-formLabel">短信</h6>
            </div>
            <div class="modal-body select">
                <div class="container-fluid">
                    <div class="box">
                        <table class="table table-normal">
                            <tbody>
                                <tr>
                                    <td>发送短信：<span id='sms_mobile'>13838383838</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>输入短信内容：</label>
                                        <textarea id="sms_content"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-green" type="button" id="send_sms">发送</button>
            </div>
        </div>

        <link href="Views/css/daterangepicker.css" rel="stylesheet">
        <script src="Views/js/vendor/date.js"></script>
        <script src="Views/js/vendor/moment.js"></script>
        <script src="Views/js/vendor/daterangepicker.js"></script>
        <script src="Views/js/jquery.slimscroll.min.js"></script>
        <script src="Views/js/order/search.js"></script>
    </body>
</html>