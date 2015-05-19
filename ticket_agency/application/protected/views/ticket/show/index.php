<?php
$this->breadcrumbs = array('门票管理', '门票预订');
?>

<div class="contentpanel">
    <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
                    <img style="max-width:100%; display: block; margin: 0px auto; border: 0px; height: 208px;" src="<?php echo !empty($landspace['images'])?$landspace['images'][0]['url']:'/img/default.jpg'; ?>">
                </div>
        </div><!-- col-md-6 -->

        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $landspace['name']; ?></h4>
                    <p><?php
                        $pro = Districts::model()->find('id=:province_id', array(':province_id' => $landspace['province_id']));
                        $city = Districts::model()->find('id=:city_id', array(':city_id' => $landspace['city_id']));
                        $district = Districts::model()->find('id=:district_id', array(':district_id' => $landspace['district_id']));
                        echo !empty($pro)?$pro->name:"";
                        echo !empty($city)?$city->name:"";
                        echo !empty($district)?$district->name:"";
                        ?></p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered mb30">
                        <tr>
                            <th>景区级别</th>
                            <td><?php echo $landspace['landscape_level_name']; ?></td>
                        </tr>
                        <tr>
                            <th>开放时间</th>
                            <td><?php echo $landspace['hours']; ?></td>
                        </tr>
                        <tr>
                            <th>窗口电话</th>
                            <td><?php echo $landspace['phone']; ?></td>
                        </tr>
                        <tr>
                            <th>详细地址</th>
                            <td><?php echo $landspace['address']; ?></td>
                        </tr>
                        <tr>
                            <th>供应商</th>
                            <td>
                                <?php
                                $name = Organizations::api()->show(array('id' => $landspace['organization_id']));
                                echo empty($name['body']['name']) ? '' : $name['body']['name'];
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div><!-- col-md-6 -->
    </div>



    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">散客预订</h4>
        </div><!-- panel-heading -->
        <div class="panel-body">

            <style>
                .table-responsive img{
                    max-width:100px
                }
                .table-responsive th,.table-responsive td{
                    vertical-align:middle!important
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
	                width:500px;
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
                .bun {color: #999}
                .fav-done {color: #269abc}
                .sub-done {color: #643534}
                .sub-done:hover {color: #801504}
            </style>
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>景区</th>
                            <th style="width: 26%">门票名称</th>
                            <th>供应商</th>
                            <th style="text-align: center">游玩日期</th>
                            <!--th>游玩星期</th-->
                            <th style="text-align: right">销售价</th>
                            <th style="text-align: right">挂牌价</th>
                            <th style="text-align: right">散客价</th>

                            <th style="text-align: center">类型</th>
                            <th style="text-align: center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($fitlist as $model):
                            ?> 
                            <tr>
                                <td><?php echo $landspace['name']; ?></td>
	                            <td style="text-align:left">
		                            <div class="col-md-12">
			                            <div class="pull-left"><strong><?php echo  $model['name'];?></strong></div>
			                            <div class="pull-right" data-id="<?php echo $model['id'] ?>"><?php
				                            echo isset($model['favor']) && $model['favor'] == 1
					                            ? '<a class="bun fav fav-done" href="javascript:;" title="取消收藏">已收藏</a>'
					                            : '<a class="bun fav" href="javascript:;" title="加入收藏">收藏</a>';
				                            ?></div>
		                            </div>
		                            <div class="col-md-12">
			                            <div class="pull-left">
				                            <div class="rules"><span>订票规则</span>
					                            <div class="table-responsive">
						                            <table class="table table-bordered mb30">
							                            <?php echo $model['remark'];?>
						                            </table>
					                            </div>
				                            </div>
				                            <div class="rules"><span>游玩星期</span>

					                            <div class="day"><?php
						                            if (strstr($model['week_time'], '1')) {
							                            echo '周一' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '2')) {
							                            echo '周二' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '3')) {
							                            echo '周三' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '4')) {
							                            echo '周四' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '5')) {
							                            echo '周五' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '6')) {
							                            echo '周六' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '0') === '0') {
							                            echo '周日' . '&nbsp;';
						                            }
						                            ?></div>
				                            </div>
			                            </div>
			                            <div class="pull-right" data-id="<?php echo $model['id'] ?>" data-fat="<?php echo $model['fat_price'] ?>" data-group="<?php echo $model['group_price'] ?>"><?php
				                            echo isset($model['sub']) && $model['sub'] == 1
					                            ? '<a class="bun sub sub-done" href="javascript;" title="取消订阅">已订阅</a>'
					                            : '<a class="bun sub" href="javascript:;" title="加入订阅">订阅</a>';
				                            ?></div>
		                            </div>
	                            </td>
                                <td>
                                    <?php
                                    $organ = Organizations::api()->show(array('id' => $model['organization_id']));
                                    echo isset($organ['body']['name']) ? $organ['body']['name'] : "";
                                    ?>
                                </td>
                                <td style="text-align: center">
                                    <?php
                                    $time = explode(',', $model['date_available']);
                                    if (!empty($time[0]) && !empty($time[1])) {
                                        echo date('m月d日', $time[0]) . '-' . date('m月d日', $time[1]);
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <!--td>
                                    <?php
                                    $week = explode(',', $model['week_time']);
                                    if (count($week) == 7) {
                                        echo '每天';
                                    } elseif (strstr($model['week_time'], '1,2,3,4,5')) {
                                        echo '工作日';
                                    } else {
                                        echo '周末';
                                    }
                                    ?>
                                </td-->
                                <td style="text-align: right"><del><?php echo $model['sale_price']; ?></del></td>
                                <td style="text-align: right"><del><?php echo $model['listed_price']; ?></del></td>
                                <td style="text-align: right" class="text-success"><?php echo $model['fat_price']; ?></td>

                                <td style="text-align: center"><?php echo $model['type'] ? '任务单' : '电子票'; ?></td>
                                <td style="text-align: center">
                                    <!--a class="btn btn-success btn-xs" href=".bs-example-modal-lg" data-toggle="modal">购买</a-->
                                    <a class="btn btn-success btn-xs" href=".bs-example-modal-lg" onclick="buy('<?php echo $model['id'] ?>',0);" data-toggle="modal">购买</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>




    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">团队预订</h4>
        </div><!-- panel-heading -->
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>景区</th>
                            <th style="width: 24%">门票名称</th>
                            <th>供应商</th>
                            <th style="text-align: center">游玩日期</th>
                            <!--th>游玩星期</th-->
                            <th style="text-align: right">销售价</th>
                            <th style="text-align: right">挂牌价</th>
                            <th style="text-align: right">团队价</th>
                            <th style="text-align: center">最低订购数</th>

                            <th style="text-align: center">类型</th>
                            <th style="text-align: center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($fulllist as $model):
                            ?> 
                            <tr>
                                <td><?php echo $landspace['name']; ?></td>
	                            <td style="text-align:left">
		                            <div class="col-md-12">
			                            <div class="pull-left"><strong><?php echo  $model['name'];?></strong></div>
			                            <div class="pull-right" data-id="<?php echo $model['id'] ?>"><?php
				                            echo isset($model['favor']) && $model['favor'] == 1
					                            ? '<a class="bun fav group fav-done" href="javascript:;" title="取消收藏">已收藏</a>'
					                            : '<a class="bun fav group" href="javascript:;" title="加入收藏">收藏</a>';
				                            ?></div>
		                            </div>
		                            <div class="col-md-12">
			                            <div class="pull-left">
				                            <div class="rules"><span>订票规则</span>
					                            <div class="table-responsive">
						                            <table class="table table-bordered mb30">
							                            <?php echo $model['remark'];?>
						                            </table>
					                            </div>
				                            </div>
				                            <div class="rules"><span>游玩星期</span>

					                            <div class="day"><?php
						                            if (strstr($model['week_time'], '1')) {
							                            echo '周一' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '2')) {
							                            echo '周二' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '3')) {
							                            echo '周三' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '4')) {
							                            echo '周四' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '5')) {
							                            echo '周五' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '6')) {
							                            echo '周六' . '&nbsp;';
						                            }
						                            if (strstr($model['week_time'], '0') === '0') {
							                            echo '周日' . '&nbsp;';
						                            }
						                            ?></div>
				                            </div>
			                            </div>
			                            <div class="pull-right" data-id="<?php echo $model['id'] ?>" data-fat="<?php echo $model['fat_price'] ?>" data-group="<?php echo $model['group_price'] ?>"><?php
				                            echo isset($model['sub']) && $model['sub'] == 1
					                            ? '<a class="bun sub group sub-done" href="javascript:;" title="取消订阅">已订阅</a>'
					                            : '<a class="bun sub group" href="javascript:;" title="加入订阅">订阅</a>';
				                            ?></div>
		                            </div>
	                            </td>
                                <td>
                                    <?php
                                    $organ = Organizations::api()->show(array('id' => $model['organization_id']));
                                    echo isset($organ['body']['name']) ? $organ['body']['name'] : "";
                                    ?>
                                </td>
                                <td style="text-align: center">
                                    <?php
                                    $time = explode(',', $model['date_available']);
                                    if (!empty($time[0]) && !empty($time[1])) {
                                        echo date('m月d日', $time[0]) . '-' . date('m月d日', $time[1]);
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <!--td>
    <?php
    $week = explode(',', $model['week_time']);
    if (count($week) == 7) {
        echo '每天';
    } elseif (strstr($model['week_time'], '1,2,3,4,5')) {
        echo '工作日';
    } else {
        echo '周末';
    }
    ?>
                                </td-->
                                <td style="text-align: right"><del><?php echo $model['sale_price']; ?></del></td>
                                <td style="text-align: right"><del><?php echo $model['listed_price']; ?></del></td>
                                <td style="text-align: right" class="text-success"><?php echo $model['group_price']; ?></td>
                                <td style="text-align: center"><?php echo $model['mini_buy']; ?></td>

                                <td style="text-align: center"><?php echo $model['type'] ? '任务单' : '电子票'; ?></td>
                                <td style="text-align: center">
                                    <!--a class="btn btn-success btn-xs" href=".bs-example-modal-lg" data-toggle="modal">购买</a-->
                                    <a class="btn btn-success btn-xs" href=".bs-example-modal-lg" onclick="buy('<?php echo $model['id'] ?>',1);" data-toggle="modal">购买</a>
                                </td>
                            </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">景区介绍</h4>
        </div><!-- panel-heading -->
        <div class="panel-body">
<?php echo $landspace['biography']; ?>
        </div>
    </div>

</div><!-- contentpanel -->

<!--购买票开始-->
<div class="modal fade bs-example-modal-lg" id="verify-modal-buy" tabindex="-1" role="dialog"></div>

<script type="text/javascript">
    function buy(id,price_type) {
        $('#verify-modal-buy').html();
        $.get('/ticket/buy/?id=' + id + '&price_type=' + price_type, function(data) {
            $('#verify-modal-buy').html(data);
        });
    }
</script>
<!--购买票结束-->
<script src="/js/fav-sub.js"></script>
