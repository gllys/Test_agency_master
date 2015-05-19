<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 1/16/15
 * Time: 3:18 PM
 * File: view.php
 */
$this->breadcrumbs = array('门票', '门票管理', '查看门票');
?>
<style>
    #ops .btn-default {
        cursor: default;
    }
</style>
<div class="contentpanel">
    <div id="verify_return"></div>
        <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">查看门票</h4>
                </div><!-- panel-heading -->
                <form class="form-horizontal form-bordered" id="repass-form">
                    <input value="single" type="hidden" name="type">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">门票名称：</label>
                            <div class="col-sm-10" style="margin-top:5px"><?php echo $ticket['name']?></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">包含景点：</label>
                            <div class="col-sm-10" style="margin-top: 5px; word-wrap: break-word; max-width: 1000px;"><?php
                                $vp = explode(',', $ticket['view_point']);
                                foreach ($vp as $point) {
                                    printf('<span role="async-name" class="poi-%d" data-id="poi_%d" style="margin-right:10px"></span>', $point, $point);
                                }
                                ?></div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">
								<table class="table table-bordered" id="ticket-type">
								<thead>
								<tr>
									<th style="width:100px">类型</th>
									<th style="width:100px">价格</th>
								</tr>
								</thead>
                                    <tbody>
                                    <?php foreach ($price_type as $idx => $type) {
                                        if (isset($ticket['type_price'][$idx])) {
                                            ?>
                                            <tr>
                                                <td><?php echo $type ?>票</td>
                                                <td><?php echo $ticket['type_price'][$idx] ?></td>
                                            </tr>
                                        <?php }
                                    } ?>
                                    </tbody>
								</table>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">销售有效期：</label>
                            <div class="col-sm-10" style="margin-top:5px"><?php $today_available = true;
                                if (strpos($ticket['date_available'], ',') !== false ) {
                                    list($a, $b) = explode(',', $ticket['date_available']);
                                    echo date('Y-m-d', $a),' ~ ',date('Y-m-d', $b);
                                    $now = time();
                                    if ($now < $a || $now > $b) {
                                        $today_available = false;
                                    }
                                }
                                else {
                                    echo '不限时间';
                                } ?></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">适用日期：</label>
                            <div class="col-sm-10" style="margin-top:5px"><?php
                                switch ($ticket['week_time']) {
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
                                        $week_time = explode(',', $ticket['week_time']);
                                        $weeks     = array(
                                            '周日',
                                            '周一',
                                            '周二',
                                            '周三',
                                            '周四',
                                            '周五',
                                            '周六'
                                        );
                                        $days = array();
                                        foreach ($week_time as $w) {
                                            if (!isset($weeks[$w])) {
                                                continue;
                                            }
                                            $days[] = $weeks[$w];
                                        }
                                        echo implode(' ', $days);
                                }
                                ?></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">使用有效期：</label>
                            <div class="col-sm-10" style="margin-top:5px">当天<?php echo $ticket['valid'] > 0 ? '及之后'.$ticket['valid'].'天' : '' ?></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10" id="ops">
								<div class="btn-group" data-toggle="buttons">
								  <label id="option1" class="btn <?php echo $today_available ? $ticket['state'] == 1 ? 'btn-danger' : 'btn-success' : 'btn-default'?> btn-xs">
									<input type="radio" name="options" autocomplete="off"><?php echo $ticket['state'] == 1 ? '下架' : '上架'?>
								  </label>
                                    <?php if ($ticket['state'] != 1) {?>
								  <label id="option2" class="btn btn-primary btn-xs">
									<input type="radio" name="options" autocomplete="off">修改
								  </label>
								  <label id="option3" class="btn btn-danger btn-xs">
									<input type="radio" name="options" autocomplete="off">删除
								  </label>
                                    <?php }?>
								</div>
							</div>
                        </div>

					</div>

					<div class="panel-footer">
                        <a href="/ticket/ticket/" class="btn btn-default">返回</a>
					</div>
                </form>
            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div>
<script src="/js/async.names.js"></script>
<script>
    $(function () {
        $('#option1').click(function () {
            if (!$(this).hasClass('btn-default')) {
                var state = $(this).text().trim() == '上架' ? 1 : 2;
                var c = function () {
                    $.get('/ticket/ticket/TicketToggle/gid/<?php echo $ticket['gid']?>/state/' + state, function(data){
                        if (data.error) {
                            setTimeout(function() {
                                alert(data.msg);
                            }, 500);
                        } else {
                            setTimeout(function() {
                                alert('操作成功', function() {
                                    location.partReload();
                                });
                            }, 500);
                        }
                    },'json');

                };
                if (state == 2) {
                    c();
                } else {
                    PWConfirm('确定要上架该门票吗？', c);
                }
            }
        });
        $('#option2').click(function () {
            location.href = '/site/switch/#/ticket/ticket/modify/gid/<?php echo $ticket['gid']?>';
        });
        $('#option3').click(function () {
            PWConfirm('确定要删除该门票吗？', function () {
                $.get('/ticket/ticket/TicketDelete/gid/<?php echo $ticket['gid']?>', function(data){
                    if (data.error) {
                        setTimeout(function() {
                            alert(data.msg);
                        }, 500);
                    } else {
                        setTimeout(function() {
                            alert('删除成功!', function() {
                                location.href = '/site/switch/#/ticket/ticket/';
                            });
                        }, 500);
                    }
                },'json');

            });
        });
    });
</script>
