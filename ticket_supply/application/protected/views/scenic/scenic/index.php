<?php
$this->breadcrumbs = array('景区管理', '景区列表');
?>
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div style="display: none;" class="panel-btns">
                <a data-original-title="" href="" class="panel-minimize tooltips" data-toggle="tooltip" title=""><i class="fa fa-minus"></i></a>
                <a data-original-title="" href="" class="panel-close tooltips" data-toggle="tooltip" title=""><i class="fa fa-times"></i></a>
            </div><!-- panel-btns -->
            <h4 class="panel-title">景区查询</h4>
        </div>
        <div class="panel-body">
            <form id="form" class="form-inline" action="/scenic/scenic/desk/" method="post">
                <div class="mb10">
                    <div class="input-group mb10">
                        <input type="text" name="keyword" value="<?php if (isset($_REQUEST['keyword'])) echo $_REQUEST['keyword']; ?>" placeholder="请输入景区名" style="width:300px" class="form-control">
                        <button type="submit" class="btn btn-primary mr5 btn-sm" style="margin-left:10px"> 搜索</button>
                        <!--span class="input-group-btn" style="padding-left:10px">
                            
                        </span-->
                    </div>
                </div>
                <div style="height: auto; overflow: hidden; position: relative;display: none;" id="more-wrap">
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
        </div><!-- panel-body -->
    </div>



    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">景区列表</h4>
        </div>



        <div class="table-responsive">
            <table class="table table-bordered mb30">
                <thead>
                    <tr>
                        <!--th>序号</th-->
                        <th>景区名称</th>
                        <th>账号/密码</th>
                        <th>所属地区</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    foreach ($lists as $item):
                        ?>
                        <tr>
                            <td style="display:none"><?php echo $item['id'] ?></td>
                            <td><a href="/scenic/scenic/view/?id=<?php echo $item['id'] ?>"><?php echo $item['name'] ?></a></td>
                            <td>
                                <?php
                                    $_rs =Users::model()->findByAttributes(array('organization_id'=>Yii::app()->user->org_id,'landscape_id'=>$item['id']));
                                    if($_rs){
                                        echo $_rs['account'].'/'.$_rs['password_str'] ;
                                    }
                                ?>
                            </td>
                            <td><?php
                                if(!empty($item['district_id']) && isset($item['district_id'])){
                                    $params['id'] = $item['district_id'];
                                }elseif(empty($item['district_id']) || $item['district_id'] == 0 || !isset($item['district_id'])){
                                    $params['id'] = $item['city_id'];
                                }else{
                                    $params['id'] = $item['province_id'];
                                }
                                
                                $rs = Districts::model()->findByPk($params['id']);
                                echo isset($rs['name']) ? $rs['name'] : '';
                                ?></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>

        <div style="text-align:center" class="panel-footer">
            <div id="basicTable_paginate" class="pagenumQu">
                <?php
					if (!empty($lists)) {
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
					}
                ?>
            </div>
        </div>

    </div>
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
        
        $('#more-wrap input:checkbox').click(function(){
            if($('#form').submit()){
                children.location.reload();
            };
        });
    });
</script>
