<?php

/**
 * Created by PhpStorm.
 * vim: set ai ts=4 sw=4 ff=unix:
 * Date: 3/13/15
 * Time: 2:45 PM
 * File: index.php
 */
$this->breadcrumbs = array('渠道对接', '淘宝绑定');
?>
<style>
    .pay span {display: none;}
    .pay2 #pay2, .pay3 #pay3, .pay4 #pay4 {display: inline !important;}
</style>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">淘宝账号绑定及支付方式设置</h4>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <form action="/channel/tb/seller/" method="post" class="form-inline">
                    <?php if (isset($tb)) {
                        ?>
                        <input type="hidden" name="tid" value="<?php echo $tb['id']?>"/>
                    <?php
                    }?>
                    <label for="tb_seller">您的淘宝卖家账号：</label>
                    <input id="tb_seller" name="account" type="text" class="form-control" <?php if (isset($tb)){echo 'value="'.$tb['account'].'"';echo $tb['status'] == 1 ? 'disabled="disabled"' : '';}?>/>
                    <button class="btn btn-default btn-sm" id="tb_btn">保存</button>
                </form>
                <p style="margin: 20px;">需经本平台工作人员审核，通过后方可向淘宝发布产品。且审核通过的账号不可修改。<a title="帮助文档" href="/channel/tb/help/" target="_blank">查看帮助文档</a></p>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title" style="color: red;">注意：退款政策以供应商为准！<div style="float:right;<?php echo isset($tb) && $tb['status'] == 1 ? '' : 'display:none;'?>"><a href="/channel/tb/ticket/" class="btn btn-xs btn-warning">新增产品绑定</a></div></h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb30" id="tablecss">
                <thead>
                <tr>
                    <th>产品名称</th>
                    <th style="width: 280px;">对接码</th>
                    <th>可用支付方式</th>
                    <th style="width: 12%">已绑定支付方式</th>
                    <th style="width: 10%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($lists)) {
                    foreach ($lists as $item) {
                        ?>
                        <tr>
                            <td><?php echo $item['product_name']?></td>
                            <td><?php echo empty($item['code'])||$item['code']=='null'?'无':$item['code'];?></td>
                            <td class="pay<?php echo str_replace(',', ' pay', ','.$item['payment_list'])?>"><span id="pay1">在线支付 </span><span id="pay2">信用支付 </span><span id="pay3">储值支付 </span><span id="pay4">平台支付</span></td>
                            <td class="pay<?php echo str_replace(',', ' pay', ','.$item['payment'])?>"><span id="pay1">在线支付 </span><span id="pay2">信用支付 </span><span id="pay3">储值支付 </span><span id="pay4">平台支付</span></td>
                            <td>
                                <a style="color: #0000FF" href="javascript:;" data-id="<?php echo $item['product_id']?>" data-pid="<?php echo $item['id']?>" data-org="<?php echo $item['organization_id']?>" data-policy="<?php echo $item['policy_id']?>" data-name="<?php echo $item['product_name']?>" data-code="<?php echo empty($item['code'])||$item['code']=='null'?'无':$item['code'];?>" data-price="<?php echo $item['price']?>" data-payment="<?php echo $item['payment']?>" data-payments="<?php echo $item['payment_list']?>" class="binding">修改</a>
                                <a style="color: #FF0000" href="javascript:;" data-id="<?php echo $item['id']?>" class="del">删除</a>
                            </td>
                        </tr>
                    <?php
                    }
                }?>
                </tbody>
            </table>
            <div class="panel-footer" style="padding-left:10px;">
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

<script>
    $(function () {
        $('#tb_btn').click(function(){
            var t = $('#tb_seller').val().trim().toString();
            if (t == '' || t.indexOf(' ') > 0) {
                alert('请输入淘宝卖家账号');
                return false;
            }
        });
        var payment_str = {1: '在线支付', 2: '信用支付', 3: '储值支付', 4: '平台支付'};
        $('.binding').click(function () {
            $('#btn').text('更新');
            var self = $(this);
            var id = self.attr('data-id');
            var pid = self.attr('data-pid');
            var name = self.attr('data-name');
            var code = self.attr('data-code');
            var price = self.attr('data-price');
            var org = self.attr('data-org');
            var policy = self.attr('data-policy');
            var payment = self.attr('data-payment');
            var payments = self.attr('data-payments').replace('1,', '');
            var modal = $('.modal-te');

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

            $('#m_name').text(name);
            $('#m_code').text(code);
            $('#pid').val(pid);
            var option = '';
            $.each(payments.split(','), function(val, id){
                option += '<option value="'+id+'" '+(id == payment ? 'selected' : '')+'>'+payment_str[id]+'</option>';
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
                        is_update: 1,
                        pid: pid
                    },
                    success: function(result){
                        if (result['code'] == 1) {
                            modal.modal('hide');
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
        $('.del').click(function () {
            var self = $(this);
            var id = self.attr('data-id');

            PWConfirm("请确认淘宝对应的产品已经下架，否则票台将无法接收来自淘宝的订单。<br/>是否解除与淘宝对应的产品绑定？", function(){
                $.ajax({
                    url: '/channel/tb/unbind/',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        product_id: id
                    },
                    success: function(result){
                        if (result['code'] == 1) {
                            location.reload();
                        }
                        else {
                            alert("出错");
                        }
                    }
                });
            });
        });
    });

</script>
