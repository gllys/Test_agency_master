<?php
use common\huilian\utils\Format;
?>
<!--子查询开始-->
    <div class="panel-body"  style=" padding-bottom: 0px; border-left: 1px solid #ebeef0;border-right: 1px solid #ebeef0;">
    <form class="form-inline" method="get" action="/order/history/view/menu/<?php echo $_GET['menu'] ?>/<?php echo $_urlParam ?>">
        
        <!--预定日期开始-->
        <div class="form-group">
            <select name="time_type" class="select2" data-placeholder="Choose One" style="width:103px;height:34px;">
                <?php
                $_types = $timeTypes ;
                ?>
                <?php foreach ($_types as $k => $v) { ?>
                    <option value="<?= $k ?>"<?= $k == $time_type ? ' selected' : '' ?>><?= $v ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group " style="width: 270px;">
            <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" id="start_date" class="form-control datepicker" type="text" readonly="readonly" value="<?php echo isset($get['start_date']) ? $get['start_date'] : ''; ?>" placeholder="开始日期"> ~
            <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" id="end_date" class="form-control datepicker"  type="text" readonly="readonly" value="<?php echo isset($get['end_date']) ? $get['end_date'] : '' ?>" placeholder="结束日期">
        </div>
        <!--预定日期结束-->
        <!-- form-group -->        
      
        
        <!--退款状态开始-->
        <div class="form-group"  style="width: 120px;">
            <select name="allow_status" id="status_link" class="select2" data-placeholder="退款状态"  style="width:120px;height:34px;">
                <option value="">退款状态</option>
                <?php
                $refundTypes = $_types =array(
                    '0'=>'退款中',
                    '1'=>'已退款',
                ) ;
                ?>
                <?php foreach ($_types as $status => $label) : ?>
                    <option <?php echo isset($get['allow_status']) && $status == $get['allow_status'] ? 'selected="selectd"' : '' ?> value="<?php echo $status ?>"><?php echo $label ?></option>
                    <?php
                endforeach;
                ?>
            </select>
        </div>
         <!--退款状态结束-->
        
        <!--订单状态开始-->
        <div class="form-group"  style="width: 120px;">
            <select name="use_status" id="status_link" class="select2" data-placeholder="使用状态"  style="width:120px;height:34px;">
                <option value="">使用状态</option>
                <?php
                $_types = Order::$useStatus;
                ?>
                <?php foreach ($_types as $status => $label) : ?>
                    <option <?php echo isset($get['use_status']) && $status == $get['use_status'] ? 'selected="selectd"' : '' ?> value="<?php echo $status ?>"><?php echo $label ?></option>
                    <?php
                endforeach;
                ?>
            </select>
        </div>
        <!--订单状态结束-->

        <!--订单查询开始-->
        <div class="form-group">
            <div class="input-group input-group-sm" style=" position: relative; top: -2px;">
                <div class="input-group-btn">
                    <button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                        <?php
                        //左边显示的名称
                        $_querys = array('id' => '订单号', 'product_name' => '门票名称','scenic_name'=>'景区名称', 'owner_name' => '取票人', 'owner_mobile' => '手机号', 'owner_card' => '身份证');

                        //当前选择的name
                        $_queryName = 'id';
                        foreach ($_querys as $key => $val) {
                            if (isset($get[$key])) {
                                $_queryName =  $key;
                                break;
                            }
                        }

                        echo $_querys[$_queryName] ;
                        ?>
                    </button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            tabindex="-1">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <?php
                        //下拉列表
                        foreach ($_querys as $key => $val) :
                            ?>
                            <li><a class="sec-btn clearPart" href="javascript:;" data-id="<?php echo $key ?>" id="" aria-labelledby="search_label"><?php echo $val; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <script>
                        $('.sec-btn').click(function() {
                            $('#search_label').text($(this).text());
                            $('#search_field').attr('name', $(this).attr('data-id'));
                        });
                    </script>
                </div>
                <!-- input-group-btn -->
                <input id="search_field" name="<?php echo $_queryName ?>" value="<?php echo empty($get[$_queryName])?'':$get[$_queryName] ?>" type="text" class="form-control" style="z-index: 0"/>
            </div>
        </div>
        <!--订单查询结束-->


        <!--分销商查询开始-->
        <div class="form-group">
            <select name="distributor_id" class="select2" data-placeholder="分销商"  style="width:170px;height:34px;">
                <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分销商&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                <?php foreach ($distributors_labels as $distributor => $label) : ?>
                    <option <?php echo isset($get['distributor_id']) && $distributor == $get['distributor_id'] ? 'selected="selectd"' : '' ?> value="<?php echo $distributor ?>"><?php echo $label ?></option>
                <?php
                endforeach;
                unset($distributor, $label)
                ?>
            </select>
        </div>
        <!--分销商查询结束-->




        <div class="form-group">
            <input type="hidden" name="is_export" class="is_export" value="0">
            <button class="btn btn-primary btn-sm" type="submit">查询</button>
            <button class="btn btn-primary btn-sm" type="button" id="export">导出</button>
        </div>
    </form>
    </div>
    <!--子查询结束-->
    
<?php echo ' <div class="tab-content mb30">' ; //拆开的DIV 在两个文件中"?>   
        <style>
            .tab-content .table tr > * {
                text-align: center
            }
            .tab-content .ckbox {
                display: inline-block;
                width: 30px;
                text-align: left
            }
        </style>
        <div id="t1" class="tab-pane active">
            <div class="table-scrollable">
                <table class="table table-bordered" style="min-width: 1060px;border-bottom:0">
                    <thead>
                        <tr>
                            <th style="width:13%">订单号</th>
                            <th style="width:5%">景区</th>
                            <th style="width:6%">门票名称</th>
                            <th style="width:5%">取票人</th>
                            <th style="width:9%">手机号码</th>
                            <th style="width:8%">预订日期</th>
                            <th style="width:8%">游玩日期</th>
                            <th style="width:8%">入园日期</th>
                            <th style="width:6%">票数</th>
                            <th style="width:6%">未使用票数</th>
                            <th style="width:7%">已使用票数</th>
                            <th style="width:7%">退票中</th>
                            <th style="width:7%">已退票</th>
                            <th style="width:5%">支付方式</th>
                            <th style="width:70px;">支付金额</th>
                            <th style="width:70px;">退款状态</th>
                            <th style="width:70px;">使用状态</th>
                            <th style="width:70px;">分销商</th>
                        </tr>
                    </thead>
                </table>
                <div style="overflow-y:scroll;max-height:400px;margin-right: -15px;">
                    <table class="table table-bordered mb30" style="min-width: 1060px;">
                        
                        <tbody>
<?php if (isset($lists['data'])) : foreach ($lists['data'] as $order) : ?>
                                    <tr>
                                        <td style="width:13%"><a href="/order/detail/?id=<?php echo $order['id'] ?>"><?php echo $order['id']; ?></td>
                                         <td style="width:5%"> <?php
                                            $landscapeArr = explode(',', $order['landscape_ids']);
                                            $landscapeName = '';
                                            foreach ($landscapeArr as $landscapeId) {
                                                 $landscapeName .= isset($landscape_lists[$landscapeId]) ? $landscape_lists[$landscapeId] : "";
                                            }
                                            ?>
                                            <a style="color: #636e7b;cursor: default;" class="clearPart" href="javascipt:void(0)" title="<?php echo $landscapeName?>" readonly>
                                                <?php echo mb_strlen($landscapeName,'UTF8') > 15 ? mb_substr($landscapeName,0,14,'UTF8') . '...' : $landscapeName ?>
                                            </a> </td>
                                        <td style="width:6%"><?php echo $order['name'] ?></td>
                                        <td style="width:5%"><?php echo $order['owner_name'] ?></td>
                                        <td style="width:9%"><?php echo $order['owner_mobile']; ?></td>
                                        <td style="width:8%"><?php echo Format::date($order['created_at']) ?></td>
                                        <td style="width:8%"><?php echo $order['use_day'] ?></td>
                                        <td style="width:8%"><?php 
                                         echo $order['use_time']?Format::date($order['use_time']):'';
                                    ?></td>
                                        <td style="width:6%"><?php echo $order['nums'] ?></td>
                                        <td style="width:6%"><?php echo $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'] ?></td>
                                        <td style="width:7%"><?php echo $order['used_nums'] ?></td>
                                        <td style="width:7%"><?php echo $order['refunding_nums'] ?></td>
                                        <td style="width:7%"><?php echo $order['refunded_nums'] ?></td>
                                        <td style="width:5%"><?php echo empty($payTypes[$order['payment']]) ? '' : $payTypes[$order['payment']] ?></td>
                                        <td style="width:70px;"><?php echo number_format($order['amount'], 2) ?></td>
                                        <td style="width:70px;" class="text-<?php echo Order::$refundStatusStyle[$order['allow_status']] ?>"><span><?php echo $refundTypes[$order['allow_status']]; ?></span></td>
                                        <td style="width:70px;" class="text-<?php echo Order::$useStatusStyle[$order['use_status']] ?>"><span><?php echo Order::$useStatus[$order['use_status']]; ?></span></td>
                                        <td style="width:70px;"><?php echo $order['distributor_name'] ?></td>
                                    </tr>
        <?php
    endforeach;
endif;
?>
                        </tbody>
                    </table> 

                </div>
            </div>
            <!-- tab-pane -->
        </div>