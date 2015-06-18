 <div class="modal-dialog" style="width:700px !important;">
    <div class="modal-content" style="padding-left: 10px;padding-right: 10px;">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">查看产品</h4>
        </div>
        <!--            --><?php //file_put_contents('D:/log/log.txt', print_r($ticket, true), FILE_APPEND)?>
        <form method="post" action="#" id="form" class="form-horizontal form-bordered">
            <table class="table table-bordered" style="margin-bottom:10px !important;width:600px;margin:0 auto;">
                <thead>
                <tr>
                    <th style="width:180px;">景区名称</th>
                    <th style="width:180px;">景区门票</th>
                    <th>窗口价格</th>
                    <th>数量</th>
                </tr>
                </thead>
                <tbody id="take-ticket">
                <?php foreach ($ticket['items'] as $item): ?>
                    <tr>
                        <td><?php echo $item['sceinc_name']; ?></td>
                        <td><?php echo $item['base_name']; ?></td>
                        <td class="text-danger"><?php echo $item['sale_price']; ?></td>
                        <td class="text-danger"><?php echo $item['num']; ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <div class="panel-body nopadding">
                <div class="form-group">
                    <label class="col-sm-2 control-label">产品名称:</label>

                    <div class="col-sm-4" style="margin-top:5px"><?php echo $ticket['name']; ?></div>
                </div>
                
                <?php
                //是否是单票
                $productIds = array_filter(PublicFunHelper::arrayKey($ticket['items'],'scenic_id'));
                ?>
                <?php if (!empty($ticket['is_fit'])): ?>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">散客结算价:</div>
                        <div class="col-sm-2 text-danger"
                             style="margin-top:5px"><?php echo $ticket['fat_price']; ?>
                        </div>
                        <?php if($orgInfo['partner_type'] < 1): ?>
                        <div class="col-sm-2 control-label">是否一次验票:</div>
                        <div class="col-sm-1"
                             style="margin-top:5px"><?php echo empty($ticket['is_fat_once_verificate']) ? '否' : '是'; ?></div>
                        <?php
                                if (count($productIds) > 1) {
                                    ?>
                                    <div class="col-sm-2 control-label">是否一次取票:</div>
                                    <div class="col-sm-1"
                                         style="margin-top:5px"><?php echo empty($ticket['is_fat_once_taken']) ? '否' : '是'; ?>
                                    </div>
                                <?php } ?>
                        <?php endif;?>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">提前预定时间:</div>
                        <?php
                        $fat_scheduled_day = floor($ticket['fat_scheduled_time'] / (3600 * 24));
                        $fat_scheduled_time = sprintf('%02d:%02d', floor($ticket['fat_scheduled_time'] % (3600 * 24) / 3600), $ticket['fat_scheduled_time'] % 3600 / 60);
                        ?>
                        <div class="col-sm-10" style="margin-top:5px">需要在入园前<span
                                class="text-danger"><?php echo $fat_scheduled_day; ?></span>天的<span
                                class="text-danger"><?php echo $fat_scheduled_time; ?></span>以前购买
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">散客产品说明:</div>
                        <div class="col-sm-10" style="margin-top:5px"><?php echo $ticket['fat_description']; ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($ticket['is_full'])): ?>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">团队结算价:</div>
                        <div class="col-sm-2 text-danger"
                             style="margin-top:5px"><?php echo $ticket['group_price']; ?>
                        </div>
                        <?php if($orgInfo['partner_type'] < 1): ?>
                        <div class="col-sm-2 control-label">是否一次验票:</div>
                        <div class="col-sm-1"
                             style="margin-top:5px"><?php echo empty($ticket['is_group_once_verificate']) ? '否' : '是'; ?></div>
                        <?php
                        if (count($productIds) > 1) {
                        ?>
                        <div class="col-sm-2 control-label">是否一次取票:</div>
                        <div class="col-sm-1"
                             style="margin-top:5px"><?php echo empty($ticket['is_group_once_taken']) ? '否' : '是'; ?>
                        </div>
                        <?php }?>
                        <?php endif;?>
                        <div class="col-sm-2" style="margin-top:5px">最少订票<span
                                class="text-danger"><?php echo $ticket['mini_buy']; ?></span>张
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">提前预定时间:</div>
                        <?php
                        $group_scheduled_day = floor($ticket['group_scheduled_time'] / (3600 * 24));
                        $group_scheduled_time = sprintf('%02d:%02d', floor($ticket['group_scheduled_time'] % (3600 * 24) / 3600), $ticket['group_scheduled_time'] % 3600 / 60);
                        ?>
                        <div class="col-sm-10" style="margin-top:5px">需要在入园前<span
                                class="text-danger"><?php echo $group_scheduled_day; ?></span>天的<span
                                class="text-danger"><?php echo $group_scheduled_time; ?></span>以前购买
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">团队产品说明:</div>
                        <div class="col-sm-10" style="margin-top:5px"><?php echo $ticket['group_description']; ?></div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-sm-2 control-label">门市挂牌价:</div>
                    <div class="col-sm-10 text-danger"
                         style="margin-top:5px"><?php echo $ticket['listed_price']; ?></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 control-label">网络销售价:</div>
                    <div class="col-sm-10 text-danger" style="margin-top:5px"><?php echo $ticket['sale_price']; ?></div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">是否允许退票:</div>
                    <div class="col-sm-1" style="margin-top:5px"><?php echo $ticket['refund'] == 1 ? '是' : '否'; ?></div>
                    <div class="col-sm-2 control-label">是否允许短信:</div>
                    <div class="col-sm-1"
                         style="margin-top:5px"><?php echo $ticket['message_open'] == 1 ? '是' : '否'; ?></div>
                    <div class="col-sm-2 control-label">是否门票验证:</div>
                    <div class="col-sm-1"
                         style="margin-top:5px"><?php echo $ticket['checked_open'] == 1 ? '是' : '否'; ?></div>
                </div>


                <div class="form-group">
                    <div class="col-sm-2 control-label">产品销售有效期:</div>
                    <div class="col-sm-10 text-danger" style="margin-top:5px">
                        <?= $ticket['sale_start_time'] ? date('Y-m-d', $ticket['sale_start_time']) . '~' . date('Y-m-d', $ticket['sale_end_time']) : '不限' ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">使用有效期:</div>
                    <?php list($val_start_time, $val_end_time) = explode(',', $ticket['date_available']); ?>
                    <div class="col-sm-10 text-danger" style="margin-top:5px">
                    	<?= $ticket['sale_start_time'] ? date('Y-m-d', $val_start_time) . '~' . date('Y-m-d', $val_end_time) : '不限' ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">预定游玩日期后:</div>
                    <div class="col-sm-10" style="margin-top:5px">
                        <?php if ($ticket['valid_flag'] == 1): ?>
                            不限期
                        <?php else: ?>
                            <span class="text-danger"><?php echo $ticket['valid']; ?></span>天
                        <?php endif; ?>
                        有效
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2 control-label">可用时间:</div>
                    <?php $arr = explode(',', $ticket['week_time']); ?>
                    <div class="col-sm-10" style="margin-top:5px">
                        <?php
                            echo in_array(1, $arr) ? '周一　　' : '';
                            echo in_array(2, $arr) ? '周二　　' : '';
                            echo in_array(3, $arr) ? '周三　　' : '';
                            echo in_array(4, $arr) ? '周四　　' : '';
                            echo in_array(5, $arr) ? '周五　　' : '';
                            echo in_array(6, $arr) ? '周六　　' : '';
                            echo in_array(0, $arr) ? '周日　　' : '';
                        ?>
                    </div>
                </div>

                <?php if($ticket['sms_template']):?>
                <div class="form-group">
                    <div class="col-sm-2 control-label">短信模版:</div>
                    <div class="col-sm-10" style="margin-top:5px">
                        <?php echo str_replace(array('{{{', '}}}'), array('', ''), $ticket['sms_template']);?>
                    </div>
                </div>
                <?php endif;?>

            </div>
            <!-- panel-body -->
        </form>
    </div>
</div>
