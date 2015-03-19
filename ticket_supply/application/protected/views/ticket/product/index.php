<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php $this->breadcrumbs = array('产品', '发布产品');?>
<div class="contentpanel">
    <div id="verify_return"></div>   
    
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
    <div class="panel-heading"><a class="btn btn-success btn-sm pull-left" href="/ticket/product/index1">发布产品</a></div>
                    <div class="panel-heading"><h4 class="panel-title">发布产品</h4></div>
                    <table class="table table-bordered mb30">
                        <thead>
                            <tr>
                                <th>产品名称</th>
                                <th>散客价/团体价/库存</th>
                                <th>价格/库存</th>
                                <th>分销策略</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($list)):
                             foreach ($list as $model):
                                if(is_array($model)):
                                    foreach ($model as $item):?>
                            <tr>
                                    <td><?php echo $item['name'];?> 
                                    <?php
                                     if ($item['state']==1)
                                            echo "<span style='float:right' class='btn btn-success btn-bordered btn-xs'>已上架</span>";
                                        else
                                            echo "<span style='float:right' class='btn btn-danger btn-bordered btn-xs'>已下架</span>";
                                    ?>
                                    
                                    </td>
                                    <td><?php echo "￥".$item['fat_price'].'/￥'.$item['group_price'].'/'.$item['fat_price']; ?></td>
                                    <td><a href="#limit-modal" data-toggle="modal" onclick="limitrule('<?php //echo $item['id']?>');">qqq</a></td>
                                    <td><a href="#rule-modal" data-toggle="modal"  onclick="addrule('<?php //echo $item['id']?>')">111 </a></td>
                                    <td>
                                      <?php
                                        if ($item['state'] == 1) {
                                            echo " <a onclick='downUp(" . $item['id'] . ",1);'><i class='glyphicon glyphicon-arrow-down'  style='cursor:pointer'></i></a>";
                                        } else {
                                            echo "<a onclick='downUp(" . $item['id'] . ",2)'><i class='glyphicon glyphicon-arrow-up'  style='cursor:pointer'></i></a>";
                                        }
                                        ?>
                                    </td>
                                </tr>        
                            
                            
                            
                            
                                <?php    endforeach;
                                       endif;         
                                      endforeach;
                               endif;   
                            ?>
                                
                          
                        </tbody>
                    </table>
                </div>
  
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
