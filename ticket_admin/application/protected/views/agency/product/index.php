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

.ui-spinner-button {
	display: none;
}
</style>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">供应商产品查看</h4>
		</div>
		<div class="panel-body">
			<form class="form-inline" method="get" action="/agency/product/">
				<div class="form-group">
					<select name="state" class="select2" data-placeholder="Choose One" style="width: 130px; padding-left:6px;height: 32px;">
						<option value="0" <?= isset($_GET['state']) && ($_GET['state'] == 0) ? ' selected' : '' ?>>产品状态</option>
						<option value="1" <?= isset($_GET['state']) && ($_GET['state'] == 1) ? ' selected' : '' ?>>已上架</option>
						<option value="2" <?= isset($_GET['state']) && ($_GET['state'] == 2) ? ' selected' : '' ?>>未上架</option>
						<option value="3" <?= isset($_GET['state']) && ($_GET['state'] == 3) ? ' selected' : '' ?>>强制下架</option>
					</select>
				</div>
				<div class="form-group">
					<div class="input-group input-group-sm">
						<div class="input-group-btn">
							<button id="search_label" type="button" class="btn btn-default" tabindex="-1">
                                <?php
								if(isset($_GET['scenic_id']))
									echo '景区';
								elseif(isset($_GET['organization_name']))
									echo '供应商';
								elseif(isset($_GET['name']))
									echo '产品名称';
								else echo '景区';
								?>
                            </button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a class="sec-btn" href="javascript:;" data-id="scenic_name" id="">景区</a></li>
								<li><a class="sec-btn" href="javascript:;" data-id="organization_name" id="" aria-labelledby="search_label">供应商</a></li>
								<li><a class="sec-btn" href="javascript:;" data-id="name" id="">产品名称</a></li>
							</ul>
							<script>
                                $('.sec-btn').click(function() {
                                    $('#search_label').text($(this).text());
                                    $('#search_field').attr('name', $(this).attr('data-id'));
                                });
                            </script>
						</div>
						<!-- input-group-btn -->
						<input id="search_field" name="<?php
						if(isset($_GET['scenic_name']))
							echo 'scenic_name';
						elseif(isset($_GET['name']))
							echo 'name';
						elseif(isset($_GET['organization_name']))
							echo 'organization_name';
						else echo 'scenic_name';
						?>"
							value="<?php
							if(isset($_GET['scenic_name']))
								echo $_GET['scenic_name'];
							elseif(isset($_GET['name']))
								echo $_GET['name'];
							elseif(isset($_GET['organization_name']))
								echo $_GET['organization_name'];
							else echo '';
							?>" type="text" class="form-control" style="z-index: 0" />
					</div>
				</div>
				<div class="form-group">
					<button class="btn btn-primary btn-sm" type="submit">查询</button>
				</div>
			</form>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered mb30" style="min-width: 1006px;">
			<thead>
				<tr>
					<th style="text-indent: 30px; background: rgb(246, 250, 253);">
						<h4>产品列表</h4>
					</th>
				</tr>
			</thead>
		</table>
		<table class="table table-bordered mb30" style="">
			<thead>
				<tr>
					<th>供应商</th>
					<th>景区</th>
					<th>产品名称</th>
					<th>状态</th>
					<th>散客价</th>
					<th>团队价</th>
					<th>销售起始</th>
					<th>销售结束</th>
					<th>门票起始</th>
					<th>门票结束</th>
					<th>购买后</th>
					<th>日库存设置</th>
					<th>分销策略</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody id="staff-body">
				<?php foreach( $lists as $key => $item ) { ?>
				<tr>
					<td><?= empty($item['organization'][0]['name']) ? '' :  $item['organization'][0]['name'] ?></td>
					<td style="width: 11%;">
					<?php
					
					foreach( $item['landscapes'] as $v ) {
						echo $v['name'] . '<br/>';
					}
					?>
                    </td>
					<td>
						<div class="rules" style="word-break:break-all;">
							<a href="/agency/product/view?id=<?= $item['id'] ?>" target="_blank"> <span class="pull-left" style="margin-top: 5px"><?= mb_strlen($item['name'],'utf8')>15?mb_substr($item['name'], 0, 15,'utf8').'...':$item['name'] ?></span>
							</a>
						</div>
					</td>
					<td><?php
						if($item['force_out']) {
							echo '<span style="color:blue;">强制下架</span>'; 
						} else if($item['state'] == '1') { 
							echo '<span style="color:green;">已上架</span>';
						} else {
							echo '<span style="color:red;">未上架</span>';
						}
						 ?></td>
					<td><?= $item['fat_price'] ?></td>
					<td><?= $item['group_price'] ?></td>
					<td><?= $item['sale_start_time'] ? Format::date($item['sale_start_time']) : '不限制' ?></td>
					<td><?= $item['sale_end_time'] ? Format::date($item['sale_end_time']) : '不限制' ?></td>
					<td><?= Format::date($item['expire_start']) ?></td>
					<td><?= Format::date($item['expire_end']) ?></td>
					<td><?= $item['valid_flag'] ? '不限制' : $item['valid'] ?></td>
					<td>
						<?php if(empty($item['rule_id'])) { ?>
						未设置
						<?php } else { ?>
						<a style="cursor: pointer; cursor: hand;" data-target=".modal-bank" data-toggle="modal" href="/agency/product/inventory/?id=<?= $item['id']?>&rid=<?= $item['rule_id']?>&name=<?= $item['name']; ?>&org_id=<?= $item['organization_id'] ?>" onclick="modal_jump(this);">已设置</a>
						<?php } ?>
					</td>
					<td>
						<?php if(empty($item['policy_id'])) { ?>
						未设置
						<?php } else { ?>
						<a style="cursor: pointer; cursor: hand;" data-target=".modal-bank" data-toggle="modal" href="javascript:;" onclick="viewPolicy(<?= $item['policy_id'] ?>,<?= $item['organization_id'] ?>);">已设置</a>
						<?php }?>
					</td>
					<td>
						<?php 
						/*
						 * 上架status为1，下架为2，待上架为0
						 * 判断非上架状态，用表达式： `state != 1`
						 * 注意：
						 * - 产品同时满足`非强制下架`和`上架`时，才能强制下架
						 * - 产品同时满足`强制下架`和`下架`时，才能解除下架
						 * - 不同时满足时，都会提示错误，
						 * - 在其它不同的状态时，如`非强制下架`和`下架`时，显示灰色的强制下架，即不可触发强制下架行为
						 */
						?>
						<?php if($item['force_out'] == 0 && $item['state'] == 1) { ?>
						<a class="clearPart"  style="cursor: pointer; cursor: hand;" data-target=".modal-bank" data-toggle="modal"
							href="/agency/product/forceOut?id=<?= $item['id'] ?>" onclick="modal_jump(this);">强制下架</a>
						<?php } else if($item['force_out'] == 0 && $item['state'] != 1) { ?>
						强制下架									
						<?php } else if($item['force_out'] == 1 && $item['state'] != 1) { // ?>
						<a class="clearPart" href="javascript:;" onclick="clearForceOut(<?= $item['id'] ?>);">解除下架</a>
						<?php } else {?>
						解除下架
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="panel-footer">
		<div class="pagenumQu">
				<?php
				if(! empty($lists)) {
					$this->widget('common.widgets.pagers.ULinkPager', array(
						'cssFile' => '',
						'header' => '',
						'prevPageLabel' => '上一页',
						'nextPageLabel' => '下一页',
						'firstPageLabel' => '',
						'lastPageLabel' => '',
						'pages' => $pages,
						'maxButtonCount' => 5
					)); // 分页数量
				}
				?>
			</div>
	</div>
	<script type="text/javascript">


                    $(function() {
						$('.select2').select2({ minimumResultsForSearch: -1});
						
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
<div class="modal fade bs-example-modal-static in" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="false">
	<div id="modal1" class="modal-dialog" style="width: 1060px;">
		<div class="modal-content">
			<form class="form-horizontal form-bordered" id="repass-form">
				<div class="modal-body">
					<div class="form-group" style="position:relative;top:-20px;">
						<button id="close_rule" class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
					</div>
					<div class="form-group" style="overflow: inherit;">
						<label class="col-sm-2 control-label">分销策略名称:</label>
						<div class="col-sm-10">
							<input disabled type="text" tag="分销策略名称" class="form-control validate[required]" maxlength="20" value="" id="pname" name="pname" disabled>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">分销策略说明:</label>
						<div class="col-sm-10">
							<textarea tag="分销策略说明" class="form-control" rows="5" maxlength="50" id="note" name="note" disabled></textarea>
						</div>
					</div>
					<table class="table table-bordered mb30" style="width: 976px !important;">
						<tbody>
							<tr style="background-color: #f7f7f7;">
								<td style=""><label>分销商名称</label></td>
								<td style=""><input id="blacknameAll" type="checkbox" disabled value="" name="">&nbsp;<label for="blacknameAll">不允许购买</label></td>
								<td style=""><label style="margin-right: 4px;">散客结算价</label><input type="text" id="fatAll" name="daystorage" class="spinner form-control"></td>
								<td style=""><label style="margin-right: 4px;">团购价</label><input type="text" id="groupAll" name="daystorage" class="spinner form-control"></td>
								<td style=""><input id="creditAll" type="checkbox" disabled value="" name="">&nbsp;<label for="creditAll">不允许信用支付</label></td>
								<td style=""><input id="advanceAll" type="checkbox" disabled value="" name="">&nbsp;<label for="advanceAll">不允许储存支付</label></td>
							</tr>
						</tbody>
					</table>
					<div style="overflow-y: auto; width: 975px; height: 200px">
						<table class="table table-bordered mb30">
							<tbody id="distributor">
								<tr>
									<td style="width: 200px;">eweq</td>
									<td style="width: 116px;"><input id="p_176" type="checkbox" disabled value="176" name="blackname_arr[176]" class="blackgroup"></td>
									<td style="width: 200px;"><input type="text" class="spinner-day"></td>
									<td><input type="text" class="spinner-day"></td>
									<td style="width: 116px;"><input id="p_176" type="checkbox" disabled value="176" name="blackname_arr[176]" class="blackgroup"></td>
									<td style="width: 116px;"><input id="p_176" type="checkbox" disabled value="176" name="blackname_arr[176]" class="blackgroup"></td>
								</tr>
						
						</table>
					</div>
					<table class="table table-bordered mb30">
						<tbody id="otherdist">
							<tr style="background-color: #f7f7f7;">
								<td style="width: 200px;">未合作分销商</td>
								<td style="width: 116px;"><input id="p_0" type="checkbox" disabled value="0" name="blackname_arr[0]"></td>
								<td style="width: 200px;"><input type="text" class="spinner-day"></td>
								<td><input type="text" class="spinner-day"></td>
							</tr>
					
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /.modal -->
<script type="text/javascript">

	function modal_jump(obj) {
	    $('#verify-modal').html('');
	    $.get($(obj).attr('href'), function(data) {
	        $('#verify-modal').html(data);
	    });
	}

	
    function modal_jump_add() {
        $('#verify-modal').html('');
        $.get('/ticket/single/add/', function(data) {
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
                    alert('产品删除成功',function(){window.location.partReload();});
                },500);
            }
        }, 'json');
            });
    }

    function down(id) {
        var ids = id ;
        if (!ids) {
            alert('请选择下载项');
            return false;
        }
        
        $.post('/ticket/single/state/', {id: ids, state: 2}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert('产品下架成功',function(){window.location.partReload();});
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
                alert('产品上架成功',function(){window.location.partReload();});
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
                	alert(data.msg,function(){window.location.partReload();});
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
            alert('请选择下载项');
            return false;
        }
        
        $.post('/ticket/single/state/', {id: ids, state: 2}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert(data.msg,function(){window.location.partReload();});
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
                alert(data.msg,function(){window.location.partReload();});
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
                alert(data.message,function(){window.location.partReload();});
            }
        }, 'json');
    }
    //取消分销商策略设置
    function clearPolicy(id, org_id){
        $.post('/agency/product/savePolicy/', {ptid: id, selpol: 0, org_id:org_id}, function(data) {
            if (data.error) {
                alert(data.msg);
            } else {
                alert(data.msg,function(){window.location.partReload();});
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
            json1['org_id'] = $("#org_id").val();
            json1['ptid'] = $("#ptid").val();
            json1['rid'] = $("#pid").val();
            json1['name'] = $('#name').val();
            json1['desc'] = $('#desc').val();
            var wrap = $('#configWrap').html();
            $.ajax({
                url: "/agency/product/saveRule",
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
	function clearInventory(id, org_id) {
		 $.post('/agency/product/clearInventory', {id:id, org_id:org_id}, function(data) {
			   if (data.error) {
	               alert(data.msg);
	           } else {
	               alert('已清空日库存',function(){window.location.partReload();});
	           }
         }, 'json');
	}
	
    $('body').on('click','.confirmation-modal .close',function(){
		$('#saveBtn').css('display', 'none');
    })
    
    
    $('form').submit(function() {
		var state = $('form').find('select[name="state"]').val();
        var search = $('#search_field').val();
        var url = '/site/switch/#/agency/product?';
		if(parseInt(state)) {
			url += '&state=' + state;
		}
		if(search) {
			url += '&' + $('#search_field').attr('name') + '=' + search;
		}
		location.href = url;
		return false;
    });

    //编辑分销策略
    function viewPolicy(distid, org_id) {
        
		$('#repass-form').validationEngine({

		}); 
        $('#verify-modal').html('');
        $('#distributor').empty();
        $('#otherdist').empty();
        $('#fatAll').val('');
        $('#groupAll').val('');
        $('.formError').remove();
        $('#poli_title').html('编辑分销策略');
        $.post('/agency/product/viewPolicy/?id=' + distid + '&org_id='+org_id+'&time=' + parseInt(1000 * Math.random()), function(result) {
            result = JSON.parse(result);
            if (result.error == 0) {
                $('#verify-modal').html($("#modal1").parent().html()).modal('show');
                $('#distributor').append(result.data);
                $('#otherdist').append(result.otherdata);
                $('#modal1').show();
                $('#rule_add').show();
                $('#distid').val(result.dist_id);
                $('#pname').val(result.name);
                $('#note').val(result.note);
                $("#blacknameAll").prop('checked', false);
                $("#fatAll").prop('checked', false);
                $("#groupAll").prop('checked', false);
                jQuery('.spinner').spinner({
                    create: function(event, ui) {
                        if (this.value > 0) {
                            this.value = '+' + this.value;
                        } else if (this.value == 0) {
                            this.value = '';
                        }
                    },
                    spin: function(event, ui) {
                        val = ui.value;
                        if (val > 0) {
                            this.value = '+' + val;
                            return false
                        }
                    }
                });
                //spin事件统一控制散客价
                jQuery('#fatAll').spinner({
                    spin: function(event, ui) {
                        var fnum = ui.value;
                        var bnum = $("#s_price_0").val();
                        fnum = parseInt(fnum, 10);
                        if (fnum > 0) {
                            $("input[name^='s_price']").val('+' + fnum);
                            this.value = '+' + fnum;
                            $("#s_price_0").val(bnum);
                            return false
                        } else if (fnum <= 0) {
                            $("input[name^='s_price']").val(fnum);
                            $("#s_price_0").val(bnum);
                        }
                    }
                });
                //spin事件统一控制团客价
                jQuery('#groupAll').spinner({
                    spin: function(event, ui) {
                        var fnum = ui.value;
                        var bnum = $("#g_price_0").val();
                        fnum = parseInt(fnum, 10);
                        if (fnum > 0) {
                            $("input[name^='g_price']").val('+' + fnum);
                            this.value = '+' + fnum;
                            $("#g_price_0").val(bnum);
                            return false
                        } else if (fnum <= 0) {
                            $("input[name^='g_price']").val(fnum);
                            $("#g_price_0").val(bnum);
                        }
                    }
                });
                $("input[name^='s_price']").parent().css("width", "83px");
                $("input[name^='g_price']").parent().css("width", "83px");
            } else {
                alert("获取分销商失败");
            }
        });
    }

    // 解除下架
	function clearForceOut(id) {
		PWConfirm('确定解除强制下架吗？',function(){
			$.post('/agency/product/forceOut', {id:id,force_out:0, force_out_remark:''}, function(data) {
				if (data.error) {
					setTimeout(function(){
						alert('解除强制下架成功', function(){window.location.partReload();});
						},
					1000);
				} else {
					setTimeout(function(){
						alert(data.msg, function(){window.location.partReload();});
						},
					1000);
				}
			}, 'json');
		});
	}  
  
</script>