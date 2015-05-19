<?php 

use common\huilian\utils\Format;

?>

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php $this->breadcrumbs = array('产品', '产品列表'); ?>
<style>
.rules {
	position: relative;
	display: inline-block;
}

.rules+.rules {
	margin-left: 20px;
}

.rules>span {
	color: #999;
	font-size: 12px;
	cursor: pointer
}

.rules>div>span {
	margin: 0 10px
}

.rules>div {
	display: none;
	position: absolute;
	top: 15px;
	left: 50px;
	z-index: 999;
	width: 500px;
	padding: 10px;
	background-color: #fbf8e9;
	border: 1px solid #fed202;
	border-radius: 2px;
	box-shadow: 0 0 10px rgba(0, 0, 0, .2);
	word-wrap: break-word;
}

.rules>div .table {
	background: none;
}

.rules>div .table tr>* {
	border: 1px solid #e0d9b6
}

.rules:hover>div {
	display: block;
}
</style>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<button class="btn btn-primary btn-sm pull-right" onclick="modal_jump_add();" data-target=".modal-bank" data-toggle="modal">新建产品</button>
				产品管理
			</h4>
		</div>
		<div class="panel-body">
			<form action="/ticket/single/" class="form-inline">
				<div class="form-group"  style="width: 200px;">
					<select data-placeholder="Choose One"  style="width:200px;height:32px;" id="distributor-select-search" name="scenic_id">
						<option value="">请输入景区名称</option>
						<?php
                                                $supplylanIds = PublicFunHelper::arrayKey($supplyLans, 'landscape_id');
                                                $lanLists = Landscape::api()->getSimpleByIds($supplylanIds);
                                                ?>
                                                <?php foreach ($supplyLans as $item): ?>
                                                    <option value="<?php echo $item['landscape_id'] ?>" <?php if (!empty($_GET['scenic_id']) && $_GET['scenic_id'] == $item['landscape_id']): ?>selected="selected"<?php endif; ?>><?php
                                                        //todo optimize
                                                        if (isset($lanLists[$item['landscape_id']])) {
                                                            echo $lanLists[$item['landscape_id']]['name'];
                                                        }
                                                        ?></option>  
                                                <?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<div class="ckbox ckbox-primary pull-left mr20">
						<input type="checkbox" name="down" value="1" id="checkboxPrimary1" <?php if (!isset($_GET['scenic_id']) || !empty($_GET['down'])): ?>checked="checked"<?php endif ?>> <label for="checkboxPrimary1">下架产品</label>
					</div>
					<div class="ckbox ckbox-primary pull-left">
						<input type="checkbox" name="up" value="1" id="checkboxPrimary" <?php if (!isset($_GET['scenic_id']) || !empty($_GET['up'])): ?>checked="checked"<?php endif ?>> <label for="checkboxPrimary">上架产品</label>
					</div>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-sm pull-left">搜索景区产品</button>
				</div>
			</form>
		</div>
	</div>
	<div class="table-responsive">
        <table class="table table-bordered mb30" style="min-width: 1006px;">
			<thead>
				<tr>
					<th style="widht:7%">
						<div class="ckbox ckbox-primary" style="margin-left: 17px;">
							<input type="checkbox" class="ids" id="checkbox-allcheck" value="">
							<label for="checkbox-allcheck" class="allcheck">全选</label>
						</div>
					</th>
					<th style="width:11%;">景区</th>
                    <th style="width:15%;">产品名称</th>
                    <th style="width:5%;">状态</th>
					<th style="width:5%;">散客价</th>
					<th style="width:5%;">团队价</th>
					<th style="width:9%;">销售起始</th>
					<th style="width:9%;">销售结束</th>
					<th style="width:11%;">日库存设置</th>
					<th style="width:15%;">分销策略</th>
					<th style="width:8%;">操作</th>
				</tr>
			</thead>
			<tbody id="staff-body">
				<?php 
					foreach($lists as $key => $item) {
					
				?>
				<tr>
					<td style="width:7%;">
						<div class="ckbox ckbox-primary" style="margin-left: 17px;">
							<input type="checkbox" class="ids" id="checkbox<?= $key ?>" value="<?= $item['id'] ?>">
							<label for="checkbox<?= $key ?>"></label>
						</div>
					</td>
					<td style="width:11%;">
						<?php if(mb_strlen($item['lan_name'],'UTF8') > 6):?>
                            <a style="color: #636e7b;cursor: pointer;cursor: hand;" title="<?php echo $item['lan_name']?>"><?php echo mb_substr($item['lan_name'],0,6,'UTF8') . '...'?></a>
                        <?php else:?>
                            <a style="color: #636e7b;cursor: pointer;cursor: hand;" title="<?php echo $item['lan_name']?>"><?php echo $item['lan_name']?></a>
                        <?php endif;?>
                    </td>
					<td style="width:15%;">
						<div class="rules">
							<span class="pull-left" style="margin-top: 5px"><?= mb_strlen($item['name'],'utf8')>15?mb_substr($item['name'], 0, 15,'utf8').'...':$item['name'] ?></span>
						</div>
					</td>
					<td style="width:5%;">
						<?php if($item['force_out'] == 1) { ?>
						<span style="cursor:auto;color:red;">强制下架</span>
						<?php } else if ($item['state'] == 1) { ?>
						<span style="cursor:auto;color:green;">已上架</span>
						<?php } else { ?>
						<span style="cursor:auto;color:red;">已下架</span>
						<?php } ?>
					</td>
					<td style="width:5%;"> <?= $item['fat_price'] ?></td>
					<td style="width:5%;"><?= $item['group_price'] ?></td>
					<td style="width:9%;"><?= $item['sale_start_time'] ? Format::date($item['sale_start_time']) : '不限制' ?></td>
					<td style="width:9%;"><?= $item['sale_end_time'] ? Format::date($item['sale_end_time']) : '不限制' ?></td>
					<td style="width:11%;">
						<a style="cursor: pointer; cursor: hand;" data-target=".modal-bank" data-toggle="modal" href_delay="/ticket/single/inventory/?id=<?= $item['id']?>&rid=<?= $item['rule_id']?>&name=<?= $item['name']; ?>" onclick="modal_jump_delay(this);"><?= empty($item['rule_id']) ? '' : '已' ?>设置</a>
						<?php if(!empty($item['rule_id'])) { ?>
						<a style="cursor: pointer;cursor: hand;color:black;" class="pull-center" href="#" onclick="clearDailyStock(<?= $item['id'] ?>)">[清空]</a>
						<?php } ?>
					</td>
					<td style="width:15%;">
						<a style="cursor: pointer;cursor: hand;"  data-target=".modal-bank" data-toggle="modal" href_delay="/ticket/single/policy/?id=<?= $item['id'] ?>&policy_id=<?= $item['policy_id'] ?>" onclick="modal_jump_delay(this);"><?= empty($item['policy_name']) ? '设置分销商策略' : $item['policy_name'] ?></a>
						<?php if(!empty($item['policy_name'])) {?>
						<a style="cursor: pointer;cursor: hand;color:black;" class="pull-center" href="#" onclick="clearPoli(<?php echo $item['id']?>);">[清空]</a>
						<?php }?>
					</td>
					<td style="white-space:nowrap;">						
						<?php if ($item['state'] == 1): ?>
							<a  onclick="down(<?php echo $item['id'] ?>);
								return false;" style="cursor: pointer;cursor: hand;">下架</a>
						<?php else: ?>
							<?php if($item['force_out'] != 1) { ?>
							<a onclick="up(<?php echo $item['id'] ?>);return false;" style="cursor: pointer;cursor: hand;">上架</a>
							<?php } ?>
							<a style="cursor: pointer;cursor: hand;" style="margin-left: 10px;" href="/ticket/single/edit/?ticket_id=<?php echo $item['id'] ?>" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">修改
							</a>
							<a style="cursor: pointer;cursor: hand;" style="margin-left: 10px;" href="#" onclick="del(<?php echo $item['id'] ?>); return false;" class="del">删除</a>
						<?php endif; ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="panel-footer">
		<div class="pull-left">
<!--			<div class="ckbox ckbox-primary" style="display: inline-block; margin: 0 17px; vertical-align: middle;">-->
<!--				<input type="checkbox" class="ids" id="checkbox-allcheck1" value=""> <label for="checkbox-allcheck1" class="allcheck">全选</label>-->
<!--			</div>-->
			<a href="#" onclick="delAll();return false;" class="btn btn-default btn-bordered btn-sm">全部删除</a> 
			<a href="#" onclick="upAll();return false;" class="btn btn-default btn-bordered btn-sm">全部上架</a> 
			<a href="#" onclick="downAll();return false;" class="btn btn-default btn-bordered btn-sm">全部下架</a>
		</div>
		<div class="pull-right">
			
		</div>
	</div>
	<div class="panel-footer">
		<div class="pagenumQu">
				<?php
                if (!empty($lists)) {
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
	<script type="text/javascript">


                    $(function() {
                        $("#distributor-select-search").select2(); //景区查询下拉框

                        $('.allcheck').click(function() {
                            if ($(this).text() == '全选') {
                                $('#staff-body').find('input').prop('checked', true)
                                $(this).text('反选')
                            } else {
                                $('#staff-body').find('input').prop('checked', false)
                                $(this).text('全选')
                            };

                        });
                    });


 	</script>
</div>
<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>
<script type="text/javascript">
    function modal_jump_add() {
        $('#verify-modal').html('');
        $.get('/ticket/single/add/', function(data) {
            $('#verify-modal').html(data);

        });
    }

    function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }

    function modal_jump_delay(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href_delay'), function(data) {
            $('#verify-modal').html(data);
        });
    }

    function del(id) {
        

        var ids = id ;
        if (!ids) {
            alert('请选择删除项');
            return false;
        }

		PWConfirm('确定要删除此门票吗？',function(){
			     $.post('/ticket/single/del/', {id: ids, state: 0}, function(data) {
            if (data.error) {
                
                setTimeout(function(){
                    alert(data.msg);
                },500);
            } else {
                
                setTimeout(function(){
                    alert('产品删除成功',function(){window.location.reload();});
                },500);
            }
        }, 'json');
            });
    }

    function down(id) {
        var ids = id ;
        if (!ids) {
            alert('请选择下架项');
            return false;
        }
        
        $.post('/ticket/single/state/', {id: ids, state: 2}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert('产品下架成功',function(){window.location.reload();});
            }
        }, 'json');
    }

    function up(id) {

        var ids = id ;
        if (!ids) {
            alert('请选择上架项');
            return false;
        }
        
        $.post('/ticket/single/state/', {id: ids, state: 1}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert('产品上架成功',function(){window.location.reload();});
            }
        }, 'json');
    }

    function delAll() {
        
        var $checkbox = [];
        $('.ids:checked').each(function() {
            if ($(this).val() != '') {
                $checkbox.push($(this).val());
            }
        });

        var ids = $checkbox.join(',');
        if (!ids) {
            alert('请选择删除项');
            return false;
        }
		PWConfirm('确定要全部删除?',function(){
			    $.post('/ticket/single/del/', {id: ids, state: 0}, function(data) {

            if (data.error) {
            	setTimeout(function() {
               	 alert(data.msg);
            	}, 2000);
            } else {
            	setTimeout(function() {
                	alert(data.msg,function(){window.location.reload();});
            	}, 2000);
            }
        }, 'json');
        });
       
    }

    function downAll() {
        var $checkbox = [];
        $('.ids:checked').each(function() {
            if ($(this).val() != '') {
                $checkbox.push($(this).val());
            }
        });
        var ids = $checkbox.join(',');
        if (!ids) {
            alert('请选择下架项');
            return false;
        }
        
        $.post('/ticket/single/state/', {id: ids, state: 2}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert(data.msg,function(){window.location.reload();});
            }
        }, 'json');
    }

    function upAll() {
        var $checkbox = [];
        $('.ids:checked').each(function() {
             if ($(this).val() != '') {
                $checkbox.push($(this).val());
            }
        });
        var ids = $checkbox.join(',');
        if (!ids) {
            alert('请选择上架项');
            return false;
        }
        
        $.post('/ticket/single/state/', {id: ids, state: 1}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert(data.msg,function(){window.location.reload();});
            }
        }, 'json');
    }
    
    $(function() {
        $("#distributor-select-search").select2(); //景区查询下拉框     

        $('#allcheck').click(function() {
            if ($(this).text() == '全选') {
                $('#staff-body').find('input').prop('checked', true)
                $(this).text('反选')
            } else {
                $('#staff-body').find('input').prop('checked', false)
                $(this).text('全选')
            }
            ;
            return false;
        });
    }); 
    
    //取消库存设置
    function clearInve(id,rid){
        $.post('/ticket/single/delInvetory/', {id: id, rid: rid}, function(data) {
            if (data.error) {
                alert(data.message);
            } else {
                alert(data.message,function(){window.location.reload();});
            }
        }, 'json');
    }
    //取消分销商策略设置
    function clearPoli(id){
        $.post('/ticket/single/savePolicy/', {ptid: id, selpol: 0}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert(data.msg,function(){window.location.reload();});
            }
        }, 'json');
    }
    $(document).on('click','#setinvBtn',function() {
            var s_price = $("#s_price").val();
            var g_price = $("#g_price").val();
            var day_storage = $("#day_storage").val();
            if ((isNaN(s_price) || s_price <= 0)
                    && (isNaN(g_price) || g_price <= 0)
                    && (isNaN(day_storage) || day_storage <= 0)) {
                $("#day_storage").val('');
                $('.show_prompt').PWShowPrompt('请设置一个有效的加减价或库存限制'); 
                return false;
            }

            var json1 = {};
            var hasChecked = 0;
            $("#storageCal tbody td input").each(function() {
                if (this.checked == false)
                    return;
                hasChecked = 1;
                var detail = $(this).parent().parent();
                dateSelected.push(detail.attr("date"));
            });
            if (hasChecked == 0) {
                $('#checkAll').PWShowPrompt('请选择日期');
                return false;
            }
            var json1 = {};
            json1['params'] = dateSelected;
            if (!isNaN(s_price) && s_price > 0) {
                json1['s_type'] = $('#s_type').val();
                json1['s_price'] = s_price;
            }
            if (!isNaN(g_price) && g_price > 0) {
                json1['g_type'] = $('#g_type').val();
                json1['g_price'] = g_price;
            }
            if (!isNaN(day_storage) && day_storage > 0) {
                json1['storage'] = day_storage;
            }
            json1['ptid'] = $("#ptid").val();
            json1['rid'] = $("#pid").val();
            json1['name'] = $('#name').val();
            json1['desc'] = $('#desc').val();
            var wrap = $('#configWrap').html();
            $.ajax({
                url: "/ticket/single/commit",
                type: "POST",
                dataType: "json",
                data: json1,
                beforeSend: function() {
                    $('#configWrap').html('<img alt="" src="/img/loaders/loader1.gif">');
                },
                success: function(result) {
                    if (result.code == 200) {
                        alert("设置成功");
                        $("#ptid").val(result.id);
                        $("#pid").val(result.rid);
                        $("#name").val(result.name);
                        $("#s_price").val(result.s_price);
                        $("#g_price").val(result.g_price);
                        json.rules = result.dateSelected;
                        storageCal.init.calDiv = $("#storageCal").get(0);
                        storageCal.init.totalStorage = $("#total_storage").val();
                        storageCal.init.totalStorageBegintime = $("#storage_open").val();
                        storageCal.init.salesStorage = $("#sales").html();
                        var ptid = parseInt($("#ptid").val());
                        var year_month = $(".year_month").val().substr(0, 7);
                        storageCal.show(year_month, ptid, $("#begintime").val());
                    } else {
                        alert(result.message);
                    }
                    $('#configWrap').html(wrap);
                }
            });
            return false;
        });

    // 清空日库存
	function clearDailyStock(id) {
		 $.post('/ticket/single/clearDailyStock', {id:id}, function(data) {
			   if (data.error) {
	               alert(data.msg);
	           } else {
	               alert('已清空日库存',function(){window.location.reload();});
	           }
         }, 'json');
	}
	
    $('body').on('click','.confirmation-modal .close',function(){
		$('#saveBtn').css('display', 'none');
    })
</script>
