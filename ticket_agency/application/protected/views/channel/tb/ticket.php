<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/13/15
 * Time: 6:33 PM
 * File: ticket.php
 */
$this->breadcrumbs = array('渠道对接', '选择产品绑定');
?>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title" style="color: red;">选择一款产品</h4>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <form action="" method="get" class="form-inline">
                    <label for="tb_seller">产品名称：</label>
                    <input id="tb_seller" name="name" type="text" class="form-control" <?php if (isset($param['name'])){echo 'value="'.$param['name'].'"';}?>/>
                    <label for="tb_seller">包含景区：</label>
                    <input id="tb_seller" name="scenic_name" type="text" class="form-control" <?php if (isset($param['scenic_name'])){echo 'value="'.$param['scenic_name'].'"';}?>/>
                    <label for="tb_seller">供应商：</label>
                    <input id="tb_seller" name="org_name" type="text" class="form-control" <?php if (isset($param['org_name'])){echo 'value="'.$param['org_name'].'"';}?>/>
                    <button class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb30" id="tablecss">
                <thead>
                <tr>
                    <th>产品名称</th>
                    <th>包含景区</th>
                    <th>供应商</th>
                    <th>游玩有效期</th>
                    <th>挂牌价</th>
                    <th>网络销售价</th>
                    <th>散客结算价</th>
                    <th>选择</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($lists)) {
                    foreach ($lists as $item) {
                        ?>
                        <tr>
                            <td><?php echo $item['name']?></td>
                            <td><?php printf('<span role="async-name" class="landscape-%d" data-id="landscape_%d"></span>', $item['scenic_id'], $item['scenic_id'])?></td>
                            <td><?php printf('<span role="async-name" class="organizations-%d" data-id="organizations_%d"></span>', $item['organization_id'], $item['organization_id'])?></td>
                            <td><?php
                                $time = explode(',',$item['date_available']);
                                if(!empty($time[0]) && !empty($time[1])){
                                    echo date('Y年m月d日',$time[0]) . '~<br/>' .date('Y年m月d日',$time[1]);
                                }else{
                                    echo '';
                                }
                                ?></td>
                            <td><del><?php echo $item['listed_price'];?></del></td>
                            <td><del><?php echo $item['sale_price'];?></del></td>
                            <td class="text-success"><?php echo number_format($item['fat_price'],2) ?></td>
                            <td><a href="javascript:;" data-org="<?php echo $item['organization_id']?>" data-id="<?php echo $item['id']?>" data-policy="<?php echo $item['policy_id']?>" data-name="<?php echo $item['name']?>" data-price="<?php echo $item['fat_price']?>" data-payments="<?php echo $item['payment']?>" class="binding text-<?php echo isset($item['is_bind']) && !empty($item['is_bind']) ? 'primary' : 'success'?>"><?php echo isset($item['is_bind']) && !empty($item['is_bind']) ? '更新' : '绑定'?></a></td>
                        </tr>
                    <?php
                    }
                }?>
                </tbody>
            </table>
            <div class="panel-footer" style="padding-left: 10px">
                <div class="row">
                    <div class="pagenumQu">
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
            </div>
        </div>
    </div>
</div>

<div class="modal modal-te" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                <h4 class="modal-title">　</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">产品名称:</label>
                    <div class="col-sm-10" id="m_name"></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">支付方式:</label>
                    <div class="col-sm-5">
                        <select name="m_payment" id="m_payment">

                        </select>
                    </div>
                </div>
                <div class="form-group" id="w_code">
                    <label class="col-sm-2 control-label">对接码:</label>
                    <div class="col-sm-10" id="m_code"></div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="pid" id="pid"/>
                <button type="button" class="btn btn-success" id="btn">保存</button>
                <button data-dismiss="modal" class="hide btn btn-default" type="button">取消</button>
            </div>
        </div>
    </div>
</div>

<script async="async" src="/js/async.names.js"></script>
<script>
    $(function () {
        var payment_str = {1: '在线支付', 2: '信用支付', 3: '储值支付', 4: '平台支付'};
        //特价
        $('.binding').click(function () {
            $('#m_code').text('');
            $('#w_code').hide();
            $('#btn').text('保存');
            $('#pid').val(0);
            var self = $(this);
            var id = self.attr('data-id');
            var name = self.attr('data-name');
            var price = self.attr('data-price');
            var org = self.attr('data-org');
            var policy = self.attr('data-policy');
            var payments = self.attr('data-payments').replace('1,', '');

            $.ajax({
                url: '/channel/tb/supply/',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    supplier_id : org,
                    policy_id: policy
                },
                async: false,
                success: function(result){
                    if(result['code'] != 1){
                        payments = '4';
                    } else if(result['data'] != undefined) {
                        payments = result['data'];
                    }
                }
            });

            var modal = $('.modal-te');

            $('#m_name').text(name);

            var selected = 0;
            $.ajax({
                url: '/channel/tb/code/',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    product_id: id,
                    source: 1
                },
                async: false,
                success: function(result){
                    if (result['code'] == 1) {
                        selected = result['data']['payment'];
                        $('#w_code').show();
                        $('#m_code').text(result['data']['code']);
                        $('#pid').val(result['data']['id']);
                        $('#btn').text('更新');
                    }
                }
            });

            var option = '';
            $.each(payments.split(','), function(val, id){
                option += '<option value="'+id+'" '+(id == selected ? 'selected' : '')+'>'+payment_str[id]+'</option>';
            });
            $('#m_payment').html(option);

            modal.modal('show');
            $('.modal-te .btn-success').off('click');
            $('.modal-te .btn-success').click(function () {
                //保存所选的支付方式
                $.ajax({
                    url: '/channel/tb/binding/',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        product_id: id,
                        product_name: name,
                        product_price: price,
                        source: 1,
                        payment: $('#m_payment').val(),
                        payment_list: payments,
                        is_update: $('#btn').text() == '更新' ? 1 : 0,
                        pid: $('#pid').val()
                    },
                    success: function(result){
                        if (result['code'] == 1) {
                            modal.modal('hide');
                            alert('绑定成功！');
                            location.reload();
                        }
                        else {
                            alert("出错");
                        }
                        $('#special_user').val('');
                        $('#special_password').val('');
                    }
                });
            });
        });
    });

</script>
