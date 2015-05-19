<?php
$this->breadcrumbs = array('景区管理', '景区列表');
?>
<link rel="stylesheet" href="/css/jquery-ui-1.11.2.min.css"/>
<style type="text/css">

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
				景区查询
			</h4>
		</div>

		<div class="panel-body">
			<form action="/scenic/scenic/" id="searchForm" class="form-inline">
					<div class="form-group">
						<input class="form-control" placeholder="请输入景区名称" type="text" name="keyword" id="keyword" maxlength="100" style="width:318px;" value="<?php if (isset($_REQUEST['keyword'])) echo $_REQUEST['keyword']; ?>">
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
				<th>景区编号</th>
				<th>景区名称</th>
<!--				<th>密码/账号</th>-->
				<th>所属地区</th>
			</tr>
			</thead>
			<tbody>
			<?PHP foreach ($lists as $item): ?>
			<tr>
				<td style="text-align: center"><?php echo $item['id'] ?></td>
				<td><a href="/scenic/scenic/view/id/<?php echo $item['id'] ?>"><?php echo $item['name'];?></a>
				</td>
<!--				<td>-->
<!--				--><?php
//					$_rs =Users::model()->findByAttributes(array('organization_id'=>Yii::app()->user->org_id,'landscape_id'=>$item['id']));
//					if($_rs){
//						echo $_rs['account'].'/'.$_rs['password_str'] ;
//					}
//				?>
<!--				</td>-->
				<td>
				<?php
					if (!empty($item['district_id']) && isset($item['district_id'])) {
						$params['id'] = $item['district_id'];
					} elseif (empty($item['district_id']) || $item['district_id'] == 0 || !isset($item['district_id'])) {
						$params['id'] = $item['city_id'];
					} else {
						$params['id'] = $item['province_id'];
					}
					echo count($item['district']) == 0 ? '' : implode(' ', $item['district']);
					// $rs = Districts::model()->findByPk($params['id']);
					// echo isset($rs['name']) ? $rs['name'] : '';
				?>
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
	<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>
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
<script type="text/javascript">
jQuery(document).ready(function() {
        //城市更多
//        if ($.cookie('city_more')) {
//            $('#more-wrap div.form-group:gt(5)').show();
//        } else {
//            $('#more-wrap div.form-group:gt(5)').hide();
//        }
//        $('#more-btn').click(function() {
//            if ($(this).attr('flag') == 0) {
//                $('#more-wrap div.form-group:gt(5)').show();
//                $(this).attr('flag', 1);
//                $.cookie('city_more', 1);
//            } else {
//                $('#more-wrap div.form-group:gt(5)').hide();
//                $(this).attr('flag', 0);
//                $.cookie('city_more', null);
//            }
//        });

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
                    minLenght: 1,
                });
            },
            select: function() {
                setTimeout(function() {
                    $('#searchForm').submit();
                }, 300);
            }
        });

        $('#more-wrap input:checkbox').click(function(){
            if($('#form').submit()){
                children.location.reload();
            };
        });
    });
</script>
