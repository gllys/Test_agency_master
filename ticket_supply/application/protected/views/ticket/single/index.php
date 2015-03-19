<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php $this->breadcrumbs = array('产品', '货架管理');?>
<div class="contentpanel">
    <div id="verify_return"></div>   
    <ul class="nav nav-tabs">
         <?php
        foreach ($data['type_labels'] as $type => $label) :
                ?>
                <li class="<?php echo isset($param['type']) && $type == $param['type'] ? 'active' : '' ?>">
                        <a href="/ticket/single/index/type/<?php echo $type ?>"><strong><?php echo $label ?></strong></a>
                </li>
        <?php endforeach;
        unset($type, $label); ?>
    </ul>
    
    <div class="tab-content mb30">
        <style>
            .tab-content .table tr>*{
                text-align:center
            }
            .tab-content .ckbox{
                display:inline-block;
                width:30px;
                text-align:left
            }
     
		.rules{
			position:relative;
			display:inline-block;
		}
		.rules+.rules{
			margin-left:20px;
			}
		.rules > span{
			color:#999;
			font-size:12px;
			cursor:pointer
		}
		.rules > div >span{
			margin:0 10px
			}
		.rules > div{
			display:none;
			position:absolute;
			top:15px;
			left:50px;
			z-index:999;
			width:300px;
			padding:10px;
			background-color:#fbf8e9;
			border:1px solid #fed202;
			border-radius:2px;
			box-shadow:0 0 10px rgba(0,0,0,.2);
		}
		.rules > div .table{
			background:none;
		}
		.rules > div .table tr > *{
			border:1px solid #e0d9b6
		}
		.rules:hover > div{
			display:block;
		}
		
        </style>
        <div id="t1" class="tab-pane active">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-inline" action="" method="get">
                         <div class="form-group" style='margin: 0 5px 0 0;<?php if($param["type"] != 2){ echo "display:none;";}?>'>
                            <input class="form-control" placeholder="请输入联票名称" type="text" style="width:200px;" name="name" <?php
                                    if (isset($param['name']) && !empty($param['name'])) {
                                        echo 'value = '.$param["name"];
                                    }
                                    ?>>
                        </div>                       
                        <div class="form-group">
                            <select data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="distributor-select" name="scenic_id">
                                <option value="">请输入景区名称</option>
                                <?php
                                $param['status'] = 1;
                                $param['organization_id'] = YII::app()->user->org_id;
                                $rs = Landscape::api()->lists($param);
                                $data = ApiModel::getLists($rs);
                                foreach ($data as $item) {
                                    ?>
                                    <option value="<?php echo $item['id']; ?>"  <?php
                                    if (isset($param['scenic_id']) && !empty($param['scenic_id'])) {
                                        echo $item['id'] == $param['scenic_id'] ? "selected" : '';
                                    }
                                    ?>><?php echo $item['name']; ?></option>
                                        <?php }
                                        ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mr5 btn-sm">查询</button>
                    </form>
                </div><!-- panel-body -->
            </div>
            <?php
            foreach ($lists as $lanId => $tickets):?>
                <div class="panel panel-default">
                    <div class="panel-heading"><h4 class="panel-title">
                            <?php
                            if($param['type'] != 2){
	                            //todo optimize
                                $rs = Landscape::api()->detail(array('id' => $lanId));
                                $data = ApiModel::api()->getData($rs);
                                echo empty($data['name']) ? '' : $data['name'];
                            }
                            ?>
                        </h4></div>
                    <table class="table table-bordered mb30">
                        <thead>
                            <tr>
                                <th>门票名称</th>
                                <th>团队价格</th>
                                <?php if($param["type"] != 1){ ?>
                                <th>散客价格</th>
                                <th>价格库存规则</th><?php  }  ?>
                                <th>限制分销商</th>
                                <th>优惠规则</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($tickets as $item):
                                ?> 
                                <tr>
                                    <td style="text-align: left">
                                        <?php
                                        echo "<span style='float:left;padding-top:5px;'>" . $item['name'] . "</span>";
                                       
                                        ?>
                                    </td>
                                    <td><?php echo $item['group_price']; ?></td>
                                   <?php if($param["type"] != 1){ ?>
                                    <td><?php echo $item['fat_price']; ?></td>
                                    <td>
                                         <div class="rules"><span><?php 
                                           if(isset($item['rule_id']) && !empty($item['rule_id'])){
                                               $fied['id'] =  $item['rule_id'];
                                               $fied['supplier_id'] = Yii::app()->user->org_id;
	                                           //todo optimize
                                               $lists = Ticketrule::api()->detail($fied);
                                                echo isset($lists['body']['name'])?$lists['body']['name']:'请选择价格库存规则';
                                             }else{
                                                 echo '请选择价格库存规则';
                                             }
                                           ?></span>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mb30">
                                                        <?php echo "请将该产品返仓，才可设置“价格库存规则”！";?>
                                                    </table>
                                                </div>
                                            </div>
                                       </td>
                                           <?php  }  ?>
                                   <?php if($item['state'] == 1){ //已上架?>
                                        <td><a style="color:#ccc">
                                       <?php
                                       if(isset($item['namelist_id']) && !empty($item['namelist_id'])){
	                                       //todo optimize
                                          $lists =  Ticketorgnamelist::api()->detail(array('id'=>$item['namelist_id']));
                                          echo isset($lists['body']['name'])?$lists['body']['name']:'请选择限制分销商';
                                       }else{
                                           echo '请选择限制分销商';
                                       }
                                       ?> 
                                        </a></td>
                                    <td><a style="color:#ccc">
                                         <?php
                                       if(isset($item['discount_id']) && !empty($item['discount_id'])){
	                                       //todo optimize
                                          $lists =  Ticketdiscountrule::api()->detail(array('id'=>$item['discount_id']));
                                          echo isset($lists['body']['name'])?$lists['body']['name']:'请选择活动规则';
                                       }else{
                                           echo '请选择活动规则';
                                       }
                                       ?> 
                                        </a></td>
                                 <?php  }else{ //待上架?>
                                        <td><a href="#limit-modal" data-toggle="modal" onclick="limitrule('<?php echo $item['id']?>');">
                                       <?php
                                       if(isset($item['namelist_id']) && !empty($item['namelist_id'])){
	                                       //todo optimize
                                          $lists =  Ticketorgnamelist::api()->detail(array('id'=>$item['namelist_id']));
                                          echo isset($lists['body']['name'])?$lists['body']['name']:'请选择限制分销商';
                                       }else{
                                           echo '请选择限制分销商';
                                       }
                                       ?> 
                                        </a></td>
                                    <td><a href="#rule-modal" data-toggle="modal"  onclick="addrule('<?php echo $item['id']?>')">
                                         <?php
                                       if(isset($item['discount_id']) && !empty($item['discount_id'])){
	                                       //todo optimize
                                          $lists =  Ticketdiscountrule::api()->detail(array('id'=>$item['discount_id']));
                                          echo isset($lists['body']['name'])?$lists['body']['name']:'请选择活动规则';
                                       }else{
                                           echo '请选择活动规则';
                                       }
                                       ?> 
                                        </a></td>
                                       
                               <?php  }?>
                                   
                                        
                                    <td>
                                    <?php
                                     if ($item['state']==1)
                                            echo "<span style='float:right' class='btn btn-success btn-bordered btn-xs'>已上架</span>";
                                        else
                                            echo "<span style='float:right' class='btn btn-danger btn-bordered btn-xs'>已下架</span>";
                                    ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($item['state'] == 1) {
                                            echo " <a onclick='downUp(" . $item['id'] . ",1);'><i class='glyphicon glyphicon-arrow-down'  style='cursor:pointer'></i></a>";
                                        } else {
                                            echo "<a onclick='downUp(" . $item['id'] . ",2)'><i class='glyphicon glyphicon-arrow-up'  style='cursor:pointer'></i></a>";
                                        }
                                        ?>
                                       <?php if($item['state'] == 2){?>
                                        <a title="返仓" href="javascript:void(0);" style="margin-left: 10px;" onclick='getback("<?php echo $item['id']?>")'>
                                            <span class="glyphicon glyphicon-retweet"></span>
                                        </a>
                                        <?php  }  ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>  
            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    $this->widget('CLinkPager', array(
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
        </div><!-- tab-pane -->

        <div id="t2" class="tab-pane"></div><!-- tab-pane -->
      
    </div>
      <div role="dialog" tabindex="-1" class="modal fade" id='rule-modal'></div>
     <div  role="dialog" tabindex="-1" class="modal fade" id='limit-modal'></div>
  
</div><!-- contentpanel -->



<script>
    function getback(id){
      $.post('/ticket/single/getback/',{'id':id}, function(data) {
            if (data.error) {
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' +data.msg+ '</div>';
                $('#verify_return').html(warn_msg);
                } else{
                    var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>返仓成功！</strong></div>';
                    $('#verify_return').html(succss_msg);

                    setTimeout("location.href='"+window.location.pathname+"'", '500');
                }
        });  
    }
       function addrule(id){
       $('#rule-modal').html("");
        $.get('/ticket/single/rule/id/'+id+'', function(data) {
            $('#rule-modal').html(data);
        });
    }
        
     function limitrule(id){
         $('#limit-modal').html("");
        $.get('/ticket/single/limitrule/id/'+id+'', function(data) {
            $('#limit-modal').html(data);
            
        });
    }   
    
    
    jQuery(document).ready(function() {
        !function() {
            $('#distributor-select').change(function() {
                var obj = $(this),
                        val = obj.val()
                b(obj, val)
            })
        }()
        // Select2
        jQuery("#distributor-select, #select-multi, #through-tickets-select").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        jQuery('select option:first-child').text('');

    });

    $(function() {
        //选择
        $('a.del').click(function() {
            if (!window.confirm("确定要删除?")) {
                return false;
            }
            $.post($(this).attr('href'), function() {
                window.location.reload();
            });
            return false;
        });
    });
//上下架
    function downUp(id, state) {
        var id = id;
        var state = state;
        $.post('/ticket/single/DownUP/', {id: id, state: state}, function(data) {
            if (data.errors) {
                var tmp_errors = '';
                $.each(data.errors, function(i, n) {
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                $('#verify_return').html(warn_msg);
            } else {
                var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功!</strong></div>';
                $('#verify_return').html(succss_msg);
                setTimeout("location.reload();", '2000');
            }
        }, "json");
        return false;
    }
</script>

<script>
    var json = {"pricelists": [{"date": "2014-10-23", "sprice": 111, "lprice": 333, "uid": "17232", "storage": "2", "sale": "0", "remain": 2}, {"date": "2014-10-24", "sprice": 1, "lprice": 2, "uid": "17269", "storage": "3", "sale": "0", "remain": 3}, {"date": "2014-10-31", "sprice": 1, "lprice": 2, "uid": "17270", "storage": "3", "sale": "0", "remain": 3}], "about": {"mintime": 1413993600, "maxtime": 1419955200, "lcode": 0, "totalStorage": -1}}



    jQuery(document).ready(function() {
        (function() {
            //storageCal.init.calDiv = $("#storageCal").get(0);
            //storageCal.init.totalStorage = $("#total_storage").val();
            //storageCal.init.totalStorageBegintime = $("#storage_open").val();
            ////storageCal.init.salesStorage = $("#sales").html();
            var pid = parseInt($("#pid").val());
            //var yearmonth = $("#begintime").val().substr(0, 7);
            //storageCal.show(yearmonth, pid, $("#begintime").val());
        })();

        $(".delete").click(function() {
            $(this).parent().parent().parent().find("input[type='text']").val("");

        });


        $("#configBtn").click(function() {
            var daystorage = $("input#daystorage").val();
            if (daystorage === "")
                daystorage = "-1";
            var sprice = $("#sprice").val();
            var lprice = $("#lprice").val();
            if ($("#sprice").size() > 0 && (isNaN(sprice) || sprice <= 0)) {
                alert("供货价请填写大于0的数字");
                return false;
            }
            if ($("#lprice").size() > 0 && (isNaN(lprice) || lprice <= 0)) {
                alert("零售价请填写大于0的数字");
                return false;
            }
            var hasChecked = 0;
            var data = [];
            var json = {};
            $("#storageCal tbody td input").each(function() {
                if (this.checked == false)
                    return;
                hasChecked = 1;
                var record = {};
                var detail = $(this).parent().parent();
                record['st'] = detail.attr("date");
                record['et'] = detail.attr("date");
                record['rid'] = detail.attr("uid");
                if ($("#sprice").size() > 0) {
                    record['p1'] = sprice;
                    record['p2'] = lprice;
                } else {
                    record['p1'] = -1;
                }
                record['ptype'] = 1;
                record['storage'] = daystorage;
                data.push(record);
            })
            if (hasChecked == 0) {
                alert("请选择配置日期");
                return false;
            }
            json['params'] = JSON.stringify(data)
            json['pid'] = $("#pid").val();
            $.ajax({
                url: "/d/daily_storage.php",
                type: "POST",
                dataType: "json",
                data: json,
                success: function(json) {
                    if (json.errcode == 100) {
                        var yearmonth = $("#storageCalContent input.yearmonth").first().val();
                        delete(storageCal.data[yearmonth])
                        alert("设置成功");
                        storageCal.show(yearmonth, $("#pid").val(), $("#begintime").val());
                    } else {
                        alert(json.msg);
                    }
                }
            })
            return false;
        })



        !function() {
            var orderCount = $('#order-count').val(),
                    spinner = $('#order-count').spinner(),
                    takeTicketObj = $('#take-ticket'),
                    takeTicketFooter = $('#take-ticket-footer'),
                    takeTicketNum,
                    order = 1,
                    tickets,
                    pay = 98.00,
                    tpl = '<tr><th>取票人姓名</th><td><input type="text" class="form-control" placeholder=""></td><th>取票人手机号码</th><td><input type="text" class="form-control" placeholder=""></td><th>取票人身份证号码</th><td><input type="text" class="form-control" placeholder=""></td><td><a class="btn btn-success btn-xs take-ticket-del" href="javascript:void(0)">删除</a></td></tr>'

            spinner.spinner({
                'value': 1,
                'min': 1,
                'spin': function(event, ui) {
                    orderCount = ui.value
                    total()
                }
            })

            $('#take-ticket-add').click(function() {
                takeTicketObj.append(tpl)
                total()
            })

            takeTicketObj.on('click', '.take-ticket-del', function() {
                $(this).parents('tr').remove()
                total()
            })


            function total() {
                order = takeTicketObj.find('tr:not(:first-child)').length
                tickets = orderCount * order
                takeTicketFooter.html('<span>合计取票人:<b class="text-danger">' + order + '</b>位</span><span style="margin-left:30px">合计订单:<b class="text-danger">' + order + '</b>张</span><span style="margin-left:30px">合计票数:<b class="text-danger">' + tickets + '</b>张</span><span style="margin-left:30px">合计支付金额:<b class="text-danger">' + tickets * pay + '</b>元</span>')
            }


        }()

        // Tags Input
        jQuery('#tags').tagsInput({width: 'auto'});

        // Textarea Autogrow
        jQuery('#autoResizeTA').autogrow();



        // Form Toggles
        jQuery('.toggle').toggles({on: true});


        // Date Picker
        jQuery('#datepicker').datepicker();
        jQuery('#datepicker-inline').datepicker();
        jQuery('#datepicker-multiple').datepicker({
            numberOfMonths: 3,
            showButtonPanel: true
        });

        // Input Masks
        jQuery("#date").mask("99/99/9999");
        jQuery("#phone").mask("(999) 999-9999");
        jQuery("#ssn").mask("999-99-9999");

        // Select2
        jQuery("#select-basic, #select-multi").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        function format(item) {
            return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined) ? "" : item.element[0].getAttribute('rel')) + ' mr10"></i>' + item.text;
        }

        // This will empty first option in select to enable placeholder
        jQuery('select option:first-child').text('');

        jQuery("#select-templating").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });

        // Color Picker
        if (jQuery('#colorpicker').length > 0) {
            jQuery('#colorSelector').ColorPicker({
                onShow: function(colpkr) {
                    jQuery(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function(colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function(hsb, hex, rgb) {
                    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
                    jQuery('#colorpicker').val('#' + hex);
                }
            });
        }

        // Color Picker Flat Mode
        jQuery('#colorpickerholder').ColorPicker({
            flat: true,
            onChange: function(hsb, hex, rgb) {
                jQuery('#colorpicker3').val('#' + hex);
            }
        });


    });

</script>
