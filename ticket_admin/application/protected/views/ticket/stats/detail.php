<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/18/15
 * Time: 4:18 PM
 * File: index.php
 */
?>
<div class="contentpanel">
    <ul class="nav nav-tabs">
        <li class="<?php echo $param['type'] == 'detail' ? 'active' : ''?>"><a href="/ticket/stats/detail/range/<?php echo "{$param['range']}/landscape_id/{$param['landscape_id']}/first_date/{$param['first_date']}/last_date/{$param['last_date']}/"?>" ><strong>详情</strong></a></li>
        <li class="<?php echo $param['type'] != 'detail' ? 'active' : ''?>"><a href="/ticket/stats/graph/range/<?php echo "{$param['range']}/landscape_id/{$param['landscape_id']}/first_date/{$param['first_date']}/last_date/{$param['last_date']}/"?>"><strong>图表</strong></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="home">
            <div id="t1" class="tab-pane active">
                <p>
                    景区：<?php echo isset($landscape_name) ? $landscape_name : ''?>
                    日期：<?php echo $param['first_date'] . ' ~ ' .$param['last_date']?>
                </p>
                <div class="panel panel-default">
                    <table class="table table-default table-hover table-bordered table-striped mb30">
                        <thead>
                        <tr>
                            <th style="line-height: 26px;">日期</th>
                            <th>销售数量</th>
                            <th>销售额</th>
                            <th>入园数</th>
                            <th>退票数</th>
                            <th>退票总额</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($lists)):
                            foreach ($lists as $item):
                                ?>
                                <tr>
                                    <td><?php echo $item['created_day']?></td>
                                    <td><?php echo $item['tickets_total']?></td>
                                    <td><?php echo $item['sale_money']?></td>
                                    <td><?php echo $item['used_total']?></td>
                                    <td><?php echo $item['refunded_total']?></td>
                                    <td><?php echo $item['refund_money']?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align:center" class="panel-footer">
                    <?php
                    if (isset($pages)) {
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
                    }
                    ?>
                </div>
            </div>
        </div><!-- tab-pane -->
    </div>
</div>
