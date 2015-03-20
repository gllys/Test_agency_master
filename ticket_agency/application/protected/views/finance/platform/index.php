<?php 
$this->breadcrumbs = array('结算管理','平台资产');
?>
      
      <div class="contentpanel">
        <div class="panel panel-default">
          <div class="panel-heading">
            <div class="panel-btns" style="display: none;"> <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i></a> <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title="Close Panel"><i class="fa fa-times"></i></a> </div>
            <!-- panel-btns -->
            <h4 class="panel-title" style="padding-left:0;">平台资产</h4>
          </div>
            <div class="panel-body">  
                <div class="form-inline">
                <span>可用余额：<b class="red"><?php
                        $union_money = $activity_money = 0.00; 
                        if (isset($total['total_union_money'])){
                            $union_money = $total['total_union_money'];
                        }   
                        echo $union_money ;
                        ?></b></span> 
                         <?php if (isset($total['total_activity_money']) && $total['total_activity_money'] != '0.00') { ?><span>抵用券金额：<b class="text-success"><?php
                        if (isset($total['total_activity_money'])){
                            $activity_money = $total['total_activity_money'];
                        }
                        echo $activity_money;
                        ?></b></span><?php } ?>
                         
                        <span>冻结金额：<b class="orange"><?php
                        if (isset($total['total_frozen_money']))
                            $frozen_money = $total['total_frozen_money'];
                        else
                            $frozen_money = 0; echo $frozen_money;
                        ?></b></span> <span>合计总额：<b class="blue"><?php echo $union_money + $frozen_money + $activity_money; ?></b></span> </div>
            <!-- panel-body --> 
            </div>
        </div>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#t1"><strong>充值</strong></a></li>
          <li class=""><a data-toggle="tab" href="#t2"><strong>提现</strong></a></li>
          <li class=""><a data-toggle="tab" href="#t3"><strong>提现记录</strong></a></li>
        </ul>
        <div class="tab-content mb30">
         <style>
				.panel-body b {
					font-size: 26px;
				}
				.panel-body>span {
					margin-right: 30px;
				}
                                b.red{
                                        color:#d9534f;
                                }
                                b.orange{
                                        color:#f0ad4e;
                                }
                                b.blue{
                                        color:#428bca;
                                }
				.tab-content .table tr>* {
					text-align: center
				}
				.tab-content .ckbox {
					display: inline-block;
					width: 30px;
					text-align: left
				}
				.cur {
					cursor: pointer;
				}
				.form-horizontal{padding:0;}
				.tab-content{padding-top:5px;}
                                .cur{cursor: pointer;}
                            </style>

          <div id="t1" class="tab-pane active">
              <form action="/finance/platform/prePay/" target="_blank"  class="form-horizontal form-bordered" id="bank_pay"  method="post">
                  <div class="form-group">
                      <!--a href=".bs-example-modal-static" data-toggle="modal" class="btn btn-primary btn-xs pull-right" onclick="addlooks();">查看充值记录</a-->
                      <div class="col-sm-12"><a href="/finance/platform/look/" class="btn btn-primary btn-xs pull-right">查看充值优惠记录</a></div>
                  </div>
                  <div class="form-group">
                <label class="col-sm-1 control-label">充值金额：</label>
                <div class="col-sm-2">
                  <input type="text" value="" name="money" tag="充值金额" class="amount form-control  validate[required,min[0.01],max[9999999999],custom[number]]" placeholder="">
                </div>
                <div style="margin-top:5px;">元</div>
              </div>
              <div class="form-group">
                <label class="col-sm-1 control-label">支付方式：</label>
                <div class="col-sm-4">
                  <!--<div class="rdio rdio-default inline-block">
                    <input type="radio" name="pay_type" id="radioDefault5" value="2" />
                    <label for="radioDefault5">支付宝</label>
                  </div>-->
                  <div class="rdio rdio-default inline-block">
                      <input type="radio" name="pay_type" checked="checked" id="radioDefault6" value="1" />
                    <label for="radioDefault6">快钱</label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-1 control-label">优惠方案：</label>
                  <div class="col-sm-11">
                      <div class="rdio rdio-default inline-block">
                          <input type="radio" name="activity_id" checked="checked" id="radioDefault0" value="" />
                          <label for="radioDefault0">不选择</label>
                      </div>
                    <?php if(isset($charge)){
                        foreach($charge as $key=>$val){?>
                      <div class="rdio rdio-default inline-block">
                        <input type="radio" name="activity_id" id="radioDefault<?php echo $key+1?>" value="<?php echo $val['id']?>" />
                        <label for="radioDefault<?php echo $key+1?>"><?php echo isset($val['title'])?$val['title']:'';?></label>
                      </div>
                        <?php     }
                    }?>

                  </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-1 control-label"></label>
                  <div class="col-sm-11 text-danger">
                      <P>优惠方案注意事项：</p>
                      <p>1、优惠活动期间，如选择相应的充值优惠方案，充值金额将转换为抵用券，抵用券只可用来支付线上订单，不可进行提现操作</p>
                      <p>2、充值优惠方案为累计赠送方案，选择同一款充值优惠方案，则充值金额累计赠送抵用券</p>
                  </div>
              </div>

              <div class="panel-footer" style="padding-left:140px;">
                <button type="button" data-toggle="modal"  data-target=".modal-bae_order_idsnk" class="btn btn-primary btn-xs" id="btn_pay">提交充值</button>
              </div>
            </form>
          </div>
          <!-- tab-pane -->
          
          <div id="t2" class="tab-pane">
            <form id="tixian" class="form-horizontal" method="post">
              <input type="hidden" name="type" value="single">
              <div class="panel-body nopadding">
                <!-- div class="form-group">
                  <label class="col-sm-1 control-label">申请金额</label>
                  <<div class="col-sm-4 pull-right">
                    <button data-toggle="modal"  class="btn btn-primary btn-xs" id="cash_record" href=".modal-bank" type="button">提现申请单查询</button>
                  </div> >
                </div-->
                <div class="form-group">
                  <label class="col-sm-1 control-label">提现金额：</label>
                  <div class="col-sm-2">
                    <input type="text" name="amount" tag="提现金额" class="form-control validate[required,min[0.01],max[9999999999],custom[number]]" placeholder="">
                  </div>
                  <span class="form-note">备注：提现申请成功后，我们会在3个工作日内打款。</span>
                </div>
                <!-- form-group -->
                <!--div class="form-group">
                  <label style="text-align:left;" class="col-sm-2 control-label">提现账号</label>
                </div!-->
                <!-- form-group -->
                <div class="form-group tab-radio">
                  <label class="col-sm-1 control-label">提现银行：</label>
                  <div class="col-sm-6">
                    <div class="rdio rdio-default inline-block">
                      <input type="radio" checked="checked" value="0" id="radioDefault9" name="bank_type">
                      <label for="radioDefault9">使用已保存的银行卡</label>
                    </div>
                    <div class="rdio rdio-default inline-block">
                      <input type="radio" value="1" id="radioDefault10" name="bank_type">
                      <label for="radioDefault10">新增银行卡（自动添加到我的银行卡内）</label>
                    </div>
                  </div>
                </div>
                <!-- form-group -->

                        <div class="form-group banks">
                            <label class="col-sm-1 control-label">提现银行：</label>
                            <div class="col-sm-4">
						   <select data-placeholder="Choose One"  data-validation-engine="form-control validate[required]" style="width:300px;padding:0 10px; margin-left:-10px;" id="distributor-select"  class="bank-select"  name="bank_own">
                                    <option value=""  >请选择提现银行</option>                                     
                                    <?php if($bank_own):?>  
                                    <?php foreach ($bank_own as $bank_list):?>
                                    <option value="<?php echo $bank_list['id'].' _ '.$bank_list['bank_name'].' _ '.$bank_list['account'].' _ '.$bank_list['open_bank'].' _ '.$bank_list['account_name'];?>" <?php if($bank_list['status'] == 'normal'): ?> selected <?php endif;?>   ><?php echo $bank_list['bank_name'].'( '.$bank_list['account'].' )'; ?></option>
                                    <?php endforeach;?>
                                    <?php endif;?>
                                </select>
                            </div>
                        </div>
                    <div class="change" style="display:none">
                      <!-- form-group -->
                      <div class="form-group">
                            <label class="col-sm-1 control-label">收款银行：</label>
                            <div class="col-sm-4">
                              <select data-placeholder="Choose One" class="select_bank" tag="收款银行" style="width:300px;padding:0 10px; margin-left:-10px;" id="distributor-select" name="bank_addid">
                                    <option value=""  >请选择银行</option>                                     
                                    <?php if($bank):?>  
                                    <?php foreach ($bank as $bank_item):?>
                                    <option value="<?php echo $bank_item['id'].' _ '.$bank_item['name']?>" ><?php echo $bank_item['name']; ?></option>
                                    <?php endforeach;?>
                                    <?php endif;?>
                                </select>
                            </div>
                        </div>
                      <div class="form-group">
                          <label class="col-sm-1 control-label">开户银行：</label>
                          <div class="col-sm-4">
                            <input type="text"  name="bank_open" tag="开户银行" class="form-control  validate[required,minSize[4],maxSize[80]]" placeholder="">
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-1 control-label">银行卡号：</label>
                          <div class="col-sm-4">
                            <input type="text"  name="bank_account" tag="银行卡号" class="form-control validate[required,custom[number],minSize[15],maxSize[20]]" placeholder="">
                          </div>
                      </div>
                        <div class="form-group">
                          <label class="col-sm-1 control-label">户主姓名：</label>
                          <div class="col-sm-4">
                            <input type="text" name="account_name" tag="户主姓名" class="form-control validate[required,custom[chinese],minSize[1],maxSize[20]]" placeholder="">
                          </div>
                      </div>
                 </div>
                <!--div class="form-group">
                  <label class="col-sm-1 control-label" style="text-align:left;">安全校验</label>
                </div-->
                <div class="form-group">
                  <label class="col-sm-1 control-label">提现申请人：</label>
                  <div class="col-sm-2" style="margin-top:5px;"><span><?php echo $user_name?$user_name:$user_account;?></span></div>
                  <div></div>
                </div>
                <div class="form-group">
                  <label class="col-sm-1 control-label">校验手机号：</label>
                  <div class="col-sm-2" style="margin-top:5px;"><span id="reg_mobile"><?php echo $user_mobile;?></span></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label">手机校验码：</label>
                    
                    <div class="col-sm-2" style="min-width: 300px;">
                        <div class="input-group">
                            <input type="text" name="code" id="code" tag="验证码" placeholder="手机验证码" class="form-control validate[required,custom[number]]">
                            <span class="input-group-btn">
                                <button type="button" id="sendCode" class="checkcode btn btn-success" style="padding:5px;">发送校验码</button>
                            </span>
                        </div>
                    </div>
                    <span class="form-note">如没有收到短信请于<span class="checkcode-num">60</span>秒后点击重新发送</span>
                    
                    <!--div class="col-sm-2">
                    <button type="button" id="sendCode"  class="checkcode btn btn-success" style="padding:5px;">发送校验码</button>
                    </div-->
                    <!--div class="col-sm-4">
                        <div class="input-group mb15 text-danger">
                            如没有收到短信请于<span class="checkcode-num">60</span>秒后点击重新发送 
                        </div>
                    </div-->
                </div>
                 <div class="panel-footer">
                 <input type="hidden" name="trade_type" value="4" />
                <input type="hidden" name="frozen_type" value="1" />
                  <input id="yan" type="hidden" name="yan" value="1" /> 
                	<button type="button" data-toggle="modal" data-target=".modal-bank" class="btn btn-primary mr20" id="fetch_cash">提交提现</button>
                        <button class="btn btn-default" type="button" onclick="window.history.go(-1)">取消返回</button>
                </div>                <!-- form-group -->
              </div>
            </form>
          </div>
          <!-- tab-pane --> 
          <div id="t3" class="tab-pane" style="padding-top:10px;">
            <form class="form-horizontal" id="tixianForm" >
              <div class="form-group">
                <label class="col-sm-1 control-label">选择月份：</label>
                <div class="btn-group" style="margin:0">
                  <button class="btn btn-sm btn-white date-prev" type="button"><i class="fa fa-chevron-left"></i>
                  </button>
                  <input type="text" class="form-control form-date" readonly="readonly" name="time" value="<?php echo isset($get['time'])?$get['time']:date('Y-m',time());?>" style="float:left;width:80px;height:33px;border-radius:0;margin:0 -1px;text-align:center;">
                  <button class="btn btn-sm btn-white date-next" type="button"><i class="fa fa-chevron-right"></i>
                  </button>
                </div>
              
              <select name="status" id="type_link" id="status_link2" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
              <option value="">提现状态</option>
              <?php foreach ($status_labels as $type => $label) :?>
                <option <?php echo isset($get['status']) && $type == $get['status'] ? 'selected="selectd"' : ''?> value="<?php echo $type?>"><?php echo $label?></option>
              <?php endforeach; unset($type, $label)?>
            </select>
                
                <button type="button" id="query" class="btn btn-primary btn-sm" style="margin-right:20px;">查询</button>
               <?php  if($lists['pagination']['count'] > 0) {?>
                <button type="button" id="export" class="btn btn-primary btn-sm">导出记录</button>
                <?php } ?>
              </div></form>
              <table class="table table-bordered mb30">
        <tr>
          <th>序列</th>
          <th>时间</th>
          <th>操作人</th>
          <th>用户账号</th>
          <th>金额</th>
          <th>交易类型</th>
          <th>交易状态</th>
          <th>账户总余额</th>
        </tr>
               <?php if (isset($lists['data']) && !empty($lists['data'])) : foreach ($lists['data'] as $blotter) :?>
        <tr>
          <td><?php echo $blotter['id']?></td>
          <td><?php echo date('y年m月d日',$blotter['created_at'])?></td>
          <td><?php echo $blotter['apply_username']?></td>
          <td><?php echo $blotter['apply_account']?></td>
          <td class="text-<?php echo $status_class[$blotter['status']];?>"><?php if($blotter['status']=='1') echo "-"; ?><?php echo number_format($blotter['money'],2)?></td>
          <td class="text-<?php echo $status_class[$blotter['status']];?>"><?php echo $trade_type['4']?></td>
          <td class="text-success cur"  title="<?php echo $blotter['remark'];?>"><?php echo  $status_labels[$blotter['status']];?></td>
          <td><?php echo  number_format($blotter['union_money'] - $blotter['money'],2);?></td>
        </tr>
        <?php endforeach; ?>
      <?php else:?>
        <tr>
          <td colspan="8">暂无数据</td>
        </tr>
        <?php endif; ?>
      </table>
      <div class="panel-footer pagenumQu" style="padding-top:15px;text-align:right;border:1px solid #ddd;border-top:0">
        <?php
        if (isset($lists['data']) && !empty($lists['data'])) {
          $this->widget('common.widgets.pagers.ULinkPager', array(
            'cssFile' => '',
            'header' => '',
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页',
            'firstPageLabel' => '',
            'lastPageLabel' => '',
            'pages' => $pages,
            'maxButtonCount' => 5, //分页数量
          ));
        }
        ?>
      </div>
            
          </div>
          <!-- tab-pane -->
        </div>
      </div>
      <!-- contentpanel -->   

<div id="lock_layer" class="unlocked" style="display: none">
  <div class="lockedpanel">
    <div class="loginuser">
      <img src="/img/logo.png" width="200" class="img-circleimg-online" alt=""/>
    </div>
    <div class="logged">
      <h3 style="color:darkred">金额:<?php echo isset($amount) ? number_format($amount, 2) : 0 ?>元</h3>
      <strong class="text-muted">支付中，请勿关闭页面</strong>
    </div>
    <form id="unlock" method="post" class="form-inline" action="">
      <div class="form-group">
        <button class="btn btn-success">支付成功</button>
      </div>
      <div class="form-group" style="margin-right: 0">
        <button class="btn btn-fail">支付失败</button>
      </div>
      <!-- input-group -->
    </form>
  </div>
  <!-- lockedpanel -->
</div>
<!-- locked -->

<div role="dialog" tabindex="-1" class="modal fade bs-example-modal-static"  id="addlook"></div>


<script src="/js/bootstrap-wizard.min.js"></script>
<script src="/js/fenxiao.js"></script>
<?php if(isset($tab)): ?>
<script>
    $(document).ready(function() {
        $('.nav-tabs a[href="#t<?php echo $tab; ?>"]').tab('show');
    });


</script>
<?php endif; ?>
<script type="text/javascript">

    function addlooks(){
        $('#addlook').html();
        $.get('/finance/platform/look', function(data) {
            $('#addlook').html(data);
        });
    }
  $(function() {
        $('#sendCode').click(function() {
          $.get('/site/smsCode/mobile/' + $('#reg_mobile').text()+'/type/1', function(result) {
            if (result == 1) {
              $('#sendCode').attr('disabled', 'disabled');
              var time_limit = 60;
              var handle = setInterval(function() {
                $('#sendCode').text('获取验证码('+(time_limit)+')');
                if (time_limit == 0) {
                  clearInterval(handle);
                  $('#sendCode').removeAttr('disabled');
                  $('#sendCode').text('获取验证码');
                }
                time_limit -= 1;
              }, 1000);
            }else if (result == 0) {
              alert('短信发送失败');
            } else if (result > 1) {
              alert(result);
            }
          });
        });


// $('#code').blur(function(){
//           var codes = $('#code').val();
//           if(codes){
//               $.post('/finance/platform/pre',{chk:"code",val:codes},function(data){
//                   if(data.error===0){
//                     // $('.text-danger').text('如没有收到短信请于<span class="checkcode-num">60</span>秒后点击重新发送');
//                   }else{
//                     //$('.text-danger').text(data.msg);
//                     $('#code').validationEngine('showPrompt',data.msg,'load');
//                   }
//               },"json");
//             }
//                  return false;
//         });



    jQuery('#btn_pay').validationEngine({
      autoHidePrompt: false,
      scroll: false,
      autoHideDelay: 3000,
      maxErrorsPerField: 1
    });
    jQuery('#btn_pay').click(function () {
      
      if($('#bank_pay').validationEngine('validate')==true){
          var money = $('.amount').val();
          $('.logged h3').text('金额:'+money+'元');
          $('#bank_pay').submit();
          jQuery('#lock_layer').removeClass('unlocked');
          jQuery('#lock_layer').addClass('locked');
          jQuery('#lock_layer').show();
        }
    });

    jQuery('#unlock').submit(function () {
      $('.locked').fadeOut(function () {
        $(this).remove();
        location.reload();
      });
      return false;
    });

    setTimeout(function () {
      var pay_state = setInterval(function () {
        if (!$('#lock_layer').hasClass('locked')) {
          return;
        }
        $.get('/finance/platform/state/',function (result) {
          if (result > 0) {
            clearInterval(pay_state);
            location.href = '/finance/platform/completed/id/' + result;
          }
        });
      }, 5000);
    }, 0);

    /*提现*/
        function fetch(){
                  if($('#tixian').validationEngine('validate')==true){
                        $.post('/finance/platform/fetchapply',$('#tixian').serialize(),function(data){
                           if(data.error==0) alert('error',function(){location.href = '/finance/platform/index/tab/3' ;});
                           else alert(data.msg,function(){location.href = '/finance/platform/index/tab/3' ;});
                            //location.reload();
                      },"json");
                    }
        }
    
        $('#fetch_cash').click(function(){
            var codes = $('#code').val();

            var bank_name = $('.select_bank option:selected').val();
            if(bank_name == ''&& $('input[name=bank_type]:radio:checked').val()==1){
                $('.select_bank').validationEngine('showPrompt','请选择提现银行','load');
                return false;
            }
             if(codes){
                $.post('/finance/platform/pre',{chk:"code",code:codes},function(data){
                    if(data.error===0){
                      //$('#yan').val('1');
                      fetch();
                      // $('.text-danger').text('如没有收到短信请于<span class="checkcode-num">60</span>秒后点击重新发送');
                    }else{
                      //$('.text-danger').text(data.msg);
                     // $('#yan').val('0');
                      $('#code').validationEngine('showPrompt',data.msg,'load');
                    }
                },"json");
              }else{
                  fetch();
              }
              return false;
            
         }); 
        $('#query').click(function(){
            if($('#tixianForm').validationEngine('validate')==true){
                var url = "/finance/platform/index/tab/3";
                $("#tixianForm").attr("action", url);
                $("#tixianForm").submit();
            }
         });   
        $('#export').click(function(){
            if($('#tixianForm').validationEngine('validate')==true){
                var url = "/finance/platform/fetchCashExport/tab/3";
                $("#tixianForm").attr("action", url);
                $("#tixianForm").submit();
              }
        });

   });

</script>

