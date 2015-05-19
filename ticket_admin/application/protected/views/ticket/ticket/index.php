<?php
/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 12/17/14
 * Time: 2:19 PM
 * File: index.php
 */
$this->breadcrumbs = array('门票', '门票管理');
?>
<style>
    #ops .btn-default {
        cursor: default;
    }
</style>
<div class="contentpanel">
    <div id="verify_return"></div>
    <div class="mb30">
        <form action="/ticket/ticket" class="form-inline" method="get">
            <div class="form-group" style="width:230px">
                <select name="scenic_id" id="scenic">
                    <option value="">选 择 一 个 景 区</option>
                    <?php if (!empty($landscapes)) {
                        foreach ($landscapes as $landscape) {
                            ?>
                            <option value="<?php echo $landscape['id']?>" <?php if(isset($param['scenic_id']) && $param['scenic_id'] == $landscape['id']){echo 'selected="selected"';}?>><?php echo $landscape['name']?></option>
                        <?php
                        }
                    }?>
                </select>
            </div>
            <div class="form-group">
                <label for="name">景点、门票名称：</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php if(isset($param['name'])){echo $param['name'];}?>"/>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-sm">查询</button>
            </div>
        </form>
        <a href="/ticket/ticket/create" class="btn btn-success btn-xs hide">新增门票</a>
        <div id="t1" class="tab-pane active">
                 <div class="panel panel-default">
                    <table class="table table-default table-hover table-bordered table-striped mb30">
                        <thead>
                        <tr>
                            <th style="line-height: 26px;text-align: left;padding-left: 5px">景区名称</th>
                            <th style="text-align: left;padding-left: 5px">门票名称</th>
                            <th style="width: 95px">类型与价格</th>
                            <th style="width: 25%;text-align: left;padding-left: 5px">景点</th>
                            <th style="width: 19%">销售有效期</th>
                            <!--th style="width: 11%">使用有效期</th>
                            <th style="width: 10%">适用日期</th>
                            <th style="width: 15%">操作</th-->
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($lists as $item): $vp = explode(',', $item['view_point']); $ns = count($vp);
                            ?>
                            <tr>
                                <td style="text-align: left;padding-left: 5px"><?php
                                    printf('<span role="async-name" class="landscape-%d" data-id="landscape_%d" style="display:block"></span>', $item['scenic_id'], $item['scenic_id']);
                                    ?></td>
                                <td style="text-align: left;padding-left: 5px"><a style="color:<?php echo $item['state'] == 1 ? 'blue' : 'gray'?>" href="/ticket/ticket/view/gid/<?php echo $item['gid']?>"><?php echo $item['name']; ?></a></td>
                                <td style="text-align: right">
                                    <?php foreach($item['type_prices'] as $price) { ?>
                                    <div><?php echo $price_type[$price['type']] . '：' . $price['sale_price']; ?></div>
                                    <?php }
                                    $np = count($item['type_prices']);
                                    ?>
                                </td>
                                <td style="text-align: left;padding-left: 5px">
                                    <?php if ($ns > $np && $ns > 3) {//景点数大于类型价格数，隐藏之?>
                                    <span class="btn-poi btn btn-xs btn-primary" data-id="<?php echo $item['gid']?>"><?php echo $ns?>个景点</span>
                                    <?php }?>
                                    <div id="poi_warp_<?php echo $item['gid']?>" style="<?php echo $ns > $np && $ns > 3 ? 'display: none;' : ''?>">
                                        <?php
                                        foreach ($vp as $point) {
                                            printf('<span role="async-name" class="poi-%d" data-id="poi_%d" style="display:block"></span>', $point, $point);
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td><?php $today_available = true;
                                    if (strpos($item['date_available'], ',') !== false && $item['date_available'] != ',') {
                                        list($a, $b) = explode(',', $item['date_available']);
                                        echo date('Y-m-d', $a),' ~ ',date('Y-m-d', $b);
                                        $now = time();
                                        if ($now < $a || $now > $b) {
                                            $today_available = false;
                                        }
                                    }
                                    else {
                                        echo '不限时间';
                                    } ?></td>
                                <!--td>当天<?php //echo $item['valid'] > 0 ? '及之后'.$item['valid'].'天' : '' ?></td>
                                <td><?php
                                /**
                                    switch ($item['week_time']) {
                                        case '1,2,3,4,5,6,0':
                                            echo '全部';
                                            break;
                                        case '1,2,3,4,5':
                                            echo '平日';
                                            break;
                                        case '6,0':
                                            echo '周末';
                                            break;
                                        default:
                                            $week_time = explode(',', $item['week_time']);
                                            $weeks     = array(
                                                '周日',
                                                '一',
                                                '二',
                                                '三',
                                                '四',
                                                '五',
                                                '六'
                                            );
                                            $days = array();
                                            foreach ($week_time as $w) {
                                                $days[] = isset($weeks[$w])?$weeks[$w]:'';
                                            }
                                            echo implode(' ', $days);
                                    }
                                    $toggle = $item['state'] == 1 ? '下架' : '上架';
                                 */
                                    ?></td>
                                <td id="ops">
                                    <a title="<?php //echo $toggle?>" class="btn btn-xs toggle-able <?php //echo $today_available ? $item['state'] == 1 ? 'btn-danger' : 'btn-success' : 'btn-default'?>" href="javascript:;" data-gid="<?php //echo $item['gid']?>"><?php //echo $toggle?></a>
                                    <a title="编辑" class="btn btn-xs btn-primary" <?php //echo $item['state'] == 1 ? 'style="visibility: hidden"' : ''?> href="/ticket/ticket/modify/gid/<?php //echo $item['gid']?>">编辑</a>
                                    <a title="删除" class="btn btn-xs btn-default delete" <?php //echo $item['state'] == 1 ? 'style="visibility: hidden"' : ''?> data-gid="<?php //echo $item['gid']?>" href="javascript:;">删除</a>
                                </td-->
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php

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

                    ?>
                </div>
            </div>
        </div>
        <!-- tab-pane -->

    </div>
</div><!-- contentpanel -->
<script src="/js/async.names.js"></script>
<script>

    jQuery(document).ready(function () {
        $('select').select2();
        $('.delete').click(function() {
            var gid = $(this).attr('data-gid');
            PWConfirm('确定要删除该门票吗？', function () {
                $.get('/ticket/ticket/TicketDelete/gid/' + gid, function(data){
                    if (data.error) {
                        setTimeout(function() {
                            alert(data.msg);
                        }, 500);
                    } else {
                        setTimeout(function() {
                            alert('删除成功!', function() {
                                top.location.partReload();
                            });
                        }, 500);
                    }
                },'json');
            });
        });
        $('.toggle-able').click(function(){
           if (!$(this).hasClass('btn-default')) {
               var gid = $(this).attr('data-gid');
               var state = $(this).text() == '上架' ? 1 : 2;
               $.get('/ticket/ticket/TicketToggle/gid/' + gid + '/state/' + state, function(data){
                   if (data.error) {
                       alert(data.msg);
                   } else {
                       alert('操作成功!', function() {
                           location.partReload();
                       });
                   }
               },'json');
           }
        });
        $('.btn-poi').click(function(){
            var self = $(this);
            var id = self.attr('data-id');
            $('#poi_warp_' + id).toggle();
        });
    });

</script>
