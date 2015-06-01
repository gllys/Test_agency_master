<link rel="stylesheet" href="/css/jquery-ui-1.11.2.min.css"/>
<style>
    a{
        cursor: pointer;cursor: hand;
    }

    .ui-widget-content {
        background: #fff;
    }

    .ui-widget-content .ui-state-focus {
        background: #F6FAFD;
        border: 1px solid #ddd;
        color: #666;
        font-weight: normal;
    }

</style>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				景区列表
			</h4>
		</div>

		<div class="panel-body">
			<form action="/scenic/scenic/" id="searchForm" class="form-inline">
				<div class="form-group">
					<input class="form-control" placeholder="请输入景区名称" type="text" name="keyword" id="keyword" maxlength="100" style="width:318px;" value="<?php if (isset($_REQUEST['keyword'])) echo $_REQUEST['keyword']; ?>">
				</div>
                <div class="form-group" style="width: 105px; margin-right: 0px;">
					<label>是否绑定供应商:</label>
				</div>
				<div class="form-group">
					<select class="select2" name="has_bind_org" style="width: 150px; padding: 0 10px;">
						<option value="3" >全部</option>
						<option value="1" <?php echo isset($_REQUEST['has_bind_org'])&&$_REQUEST['has_bind_org']==1?'selected':'';?>>是</option>
						<option value="0" <?php echo isset($_REQUEST['has_bind_org'])&&$_REQUEST['has_bind_org']==0?'selected':'';?>>否</option>
					</select>
				</div>
                <div class="form-group" style="width: 92px; margin-right: 0px;">
                    <label>电子票务系统:</label>
                </div>
                <div class="form-group">
                    <select class="select2" name="partner_type" style="width: 150px; padding: 0 10px;">
                        <option value="-1">未使用</option>
                        <option value="0" <?php echo isset($_GET['partner_type'])&&$_GET['partner_type']==0?'selected':'';?>>票台</option>
                        <option value="1" <?php echo isset($_GET['partner_type'])&&$_GET['partner_type']==1?'selected':'';?>>大漠</option>
                    </select>
                </div>
				<div class="form-group">
					<button class="btn btn-primary btn-sm" type="submit">查询</button>
				</div>
				<div style="height: auto; overflow: hidden; position: relative; display: none;" id="more-wrap">
                    <?php
                    $province = Districts::model()->findAllByAttributes(array("parent_id" => 0));
                    $_province_ids = isset($_REQUEST['province_ids']) ? $_REQUEST['province_ids'] : array();
                    foreach ($province as $model) :
                        if ($model->id == 0) {
                            continue;
                        }
                    ?>
                    <div class="form-group" style="width:135px;">
                        <div class="ckbox ckbox-primary">
                            <input type="checkbox" name="province_ids[]" <?php if (in_array($model['id'], $_province_ids)): ?> checked="checked"<?php endif; ?> value="<?php echo $model['id'] ?>" id="checkbox<?php echo $model['id'] ?>">
                            <label for="checkbox<?php echo $model['id'] ?>"><?php echo $model['name'] ?></label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div style="position:absolute;top:0;right:0;display: none;">
                        <a id="more-btn" flag='0' href="javascript:void(0)">更多 ∨</a>
                    </div>
                </div>
			</form>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table table-bordered mb30">
			<thead>
			<tr>
				<th width="80px">景区编号</th>
				<th>景区名称</th>
				<th>景区级别</th>
				<th>所属地区</th>
                <th>是否已绑定供应商</th>
                <th>电子票务系统</th>
                <th>验票账号</th>
                <th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?PHP foreach ($lists as $item): ?>
			<tr>
				<td><?php echo $item['id'] ?></td>
				<td>
<!--                    <a style="" data-toggle="modal" data-target=".bs-example-modal-static" onclick="bind(<?php //echo $item['id'];?>);"><?php //echo $item['name'];?><!--</a>-->
                    <a class="text-primary" href="/scenic/scenic/edit/id/<?php echo $item['id']?>"><?php echo $item['name'];?></a>
                </td>
				<td>
                    <?php echo $level[$item['landscape_level_id']]?>
				</td>
				<td>
				<?php
					if (!empty($item['district_id']) && isset($item['district_id'])) {
						$params['id'] = $item['district_id'];
					} elseif (empty($item['district_id']) || $item['district_id'] == 0 || !isset($item['district_id'])) {
						$params['id'] = $item['city_id'];
					} else {
						$params['id'] = $item['province_id'];
					}
                                        if(isset($item['district'])){
                                            echo count($item['district']) == 0 ? '' : implode(' ', $item['district']);
                                        }
                                ?>
				</td>
                                <td><?php
                                if(isset($item['has_bind_org'])){
                                    if($item['has_bind_org']==1){
                                        echo '是';
                                    }else if($item['has_bind_org']==0){
                                        echo '否';
                                    }
                                }
                                ?></td>
                <td>
                    <?php
                    if((!$item['organization_id']) || $item['partner_type'] == -1) {
                        echo '未使用';
                    } else if($item['partner_type'] == 0) {
                        echo "票台";
                    } else if($item['partner_type'] == 1) {
                        echo '大漠';
                    }
                    ?>
                </td>
                <td>
                    <a style="" data-toggle="modal" data-target=".bs-example-modal-static" onclick="bind(<?php echo $item['id'];?>);">账号管理</a>
                </td>
                <td>
                    <a class="text-primary" href="/scenic/scenic/edit/id/<?php echo $item['id']?>">编辑</a>&nbsp;&nbsp;&nbsp;
                    <a class="text-success" href="/scenic/scenic/supply/id/<?php echo $item['id']?>">查看或绑定供应商</a>
                </td>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>


	<div style="text-align:center" class="panel-footer">
		<div id="basicTable_paginate" class="pagenumQu">
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
<div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static">
	<script type="text/javascript">

        $(function() {

            function focusEnd(obj) {
                var value = obj.val();
                obj.val('').focus().val(value);
            }
            // 默认选中搜索框focus
            var $keyword = $('#keyword');
            focusEnd($keyword);

            // 搜索框模糊查询自动补全
            $keyword.autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url:'/scenic/scenic/index',
                        dataType: 'json',
                        data: {
                            keyword: request.term,
                            items: 8,
                            search: 1
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item.name,
                                    data: 'name'
                                }
                            }));
                        },
                        minLenght: 1
                    });
                },
                select: function() {
                    setTimeout(function() {
                        $('#searchForm').submit();
                    }, 300);
                }
            });
        });


        $(function(){
             jQuery('.select2').select2({
                minimumResultsForSearch: -1
            });
            window.bind = function(id){
                document.getElementById('verify-modal').innerHTML = '';
                $.get('/scenic/scenic/reset/landscape_id/' +id, function(data) {
                    $('#verify-modal').html(data);
                });
            }
        });

	</script>
</div>
