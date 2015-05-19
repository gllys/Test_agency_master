<?php
$this->breadcrumbs = array('景区管理', '景区列表');
?>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                景区查询
            </h4>
        </div>
        <div id="land_rs"></div>
        
        <div class="panel-body">
            <form action="/org/supply/lan/id/<?php echo $_GET['id'] ?>/" class="form-inline">
                <div class="form-group">
                    <input class="form-control" placeholder="请输入景区名称" type="text" name="keyword" style="width:318px;" value="<?php if (isset($_REQUEST['keyword'])) echo $_REQUEST['keyword']; ?>">
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
                    <th>所属地区</th>
                    <th>操作</th>
                </tr>   
            </thead>
            <tbody>
                <?PHP foreach ($lists as $item): ?>
                    <tr>
                        <td style="text-align: center"><?php echo $item['id'] ?></td>
                        <td><a href="/scenic/scenic/edit/id/<?php echo $item['id'] ?>" target="_blank"><?php echo $item['name']; ?></a>
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
                            echo count($item['district']) == 0 ? '' : implode(' ', $item['district']);
                            // $rs = Districts::model()->findByPk($params['id']);
                            // echo isset($rs['name']) ? $rs['name'] : '';
                            ?>
                        </td>
                        <td>
                            <img src="/img/select2-spinner.gif" class="load" style="display: none" >
                            <a  class="text-success bind_supply clearPart" href="javascript:;"
                                 data-id="<?php echo $_GET['id'] ?>" data-type="unbind"
                                 data-lan="<?php echo $item['id'] ?>" data-place="down">解除绑定</a></td>
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
                }
                ;

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

        $('.bind_supply').click(function() {
            $('.bind_admin').hide();
            $('.bind_supply').hide();
            $('.load').show();
            var org_id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            var supply = $(this).attr('data-supply');
            var org_lan = $(this).attr('data-lan');
            var place = $(this).attr('data-place');
            if (supply == 1) {
                checkLandscape(org_id, org_lan, type, place);
            } else {
                bindSupply(org_lan, org_id, type, place);
            }
        })

        function checkLandscape(org_id, lan_id, type, place) {
            $.post('/scenic/scenic/checklandscape/', {organization_id: org_id}, function(data) {
                if (data.error) {
                    alert('该机构是景区用户，且已与景区' + data.msg + '绑定', function() {
                        $('.bind_admin').show();
                        $('.bind_supply').show();
                        $('.load').hide();
                    });
                } else {
                    bindSupply(lan_id, org_id, type, place);
                }
            }, 'json')
        }

        function bindSupply(lan_id, org_id, type, place) {
            $.post('/scenic/scenic/savebind/', {landscape_id: lan_id, organization_id: org_id, type: type},
            function(data) {
                if (data.error) {
                    alert(data.msg, function() {
                        $('.bind_admin').show();
                        $('.bind_supply').show();
                        $('.load').hide();
                    });
                } else {
                    alert('保存成功！', function() {
                        window.location.partReload();
                    });
                }
            }, 'json')
        }


        $('#more-wrap input:checkbox').click(function() {
            if ($('#form').submit()) {
                children.location.partReload();
            }
            ;
        });
    });
</script>
