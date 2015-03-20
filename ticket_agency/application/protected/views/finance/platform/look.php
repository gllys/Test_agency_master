<?php
$this->breadcrumbs = array('结算','平台资产');
?>
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
            </div><!-- panel-btns -->
            <h4 class="panel-title">充值优惠记录</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/finance/platform/look">
                <div class="panel-body">
                        <div class="form-group" style="margin:0;">
                            <label>充值日期：</label>
                                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="start_date" class="form-control datepicker" value="<?php echo isset($get['start_date']) ? $get['start_date'] : date('Y-m-01',time())?>" placeholder="<?php echo isset($get['start_date']) ? $get['start_date'] :date('Y-m-01',time());?>" type="text" readonly="readonly"> ~
                                <input style="cursor: pointer;cursor: hand;background-color: #ffffff" name="end_date" class="form-control datepicker" value="<?php echo isset($get['end_date']) ? $get['end_date'] : date('Y-m-d',time())?>" placeholder="<?php echo isset($get['end_date']) ? $get['end_date'] :date('Y-m-01',time());?>" type="text" readonly="readonly">
                    </div><!-- form-group -->
                    <div class="form-group" style="margin:0">
                        <button class="btn btn-primary btn-xs" type="submit">查询</button>
                    </div>

                </div><!-- panel-body -->
            </form>
        </div><!-- panel-body -->
    </div>
    <style>
        .table1 tr>*{
            text-align:center
        }

    </style>
    <table class="table table-bordered table1">
        <thead>
        <tr>
            <th>编号</th>
            <th>日期</th>
            <th>操作人</th>
            <th>支付方式</th>
            <th>优惠方案</th>
            <th>充值金额</th>
            <th>抵用券数量</th>
            <th>账户抵用券余额</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($list)):
            foreach( $list as $key => $val): ?>
                <tr>
                    <td><?php echo $val['id']?></td>
                    <td><?php echo date("Y-m-d",$val['created_at'])?></td>
                    <td><?php echo $val['created_name']?></td>
                    <td><?php  echo $val['pay_type']== 1?"快钱支付":"支付宝";?></td>
                    <td><?php echo $val['activity_title']?></td>
                    <td><?php echo $val['money']?></td>
                    <td><?php echo $val['coupon'];?></td>
                    <td><?php echo $val['coupon_total']?></td>
                </tr>
        <?php
       endforeach;
        else:?>
        <tr><td colspan="18" style="text-align:center">暂无数据</td></tr>
        <?php endif;?>
        </tbody>
    </table>

<div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
    <?php
    if (isset($list)) {
        $this->widget('common.widgets.pagers.ULinkPager', array(
            'cssFile' => '',
            'header' => '',
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页',
            'firstPageLabel' => '',
            'lastPageLabel' => '',
            'pages' => $pages,
            'maxButtonCount' => 3, //分页数量
        ));
    }
    ?>
</div>


</div><!-- contentpanel -->


<script>
    jQuery(document).ready(function () {
        jQuery('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    })
</script>
