<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">分销商信息</h4>
        </div>
        <style>
            .table-responsive th,.table-responsive td{
                vertical-align:middle!important
            }
            .panel-footer b{
                font-size:22px;
                padding:0 5px;
            }
            #t1 th{
                text-align:right
            }
            #t1 td{
                text-align:left
            }
        </style>
    </div>
    <div class="panel-body">
        <div class="table-responsive mb10">
            <table class="table table-bordered" id="t1">
                <tbody>
                    <tr>
                        <th width="150">企业名称：</th>
                        <td colspan="15"><?php echo $data[0]['name']; ?></td>
                        <th width="150">分销商编号：</th>
                        <td colspan="15"><?php echo $data[0]['id']; ?></td>
                    </tr>
                    <tr>
                        <th width="150">企业简称：</th>
                        <td colspan="15"><?php echo $data[0]['abbreviation']; ?></td>
                        <th width="150">手机号码：</th>
                        <td colspan="15"> <?php echo $data[0]['mobile']; ?></td>
                    </tr>
                    <tr>
                        <th width="150">联系人：</th>
                        <td colspan="15"><?php echo $data[0]['contact']; ?></td>
                        <th width="150">公司传真：</th>
                        <td colspan="15"><?php echo $data[0]['fax']; ?></td>
                    </tr>
                    <tr>
                        <th width="150">所在地区：</th>
                        <td colspan="15"><?php 
                                    if($data[0]['province_id']){
                                        echo Districts::model()->findByPk($data[0]['province_id'])->name;
                                    }
                                    
                                    if($data[0]['city_id']){
                                        echo Districts::model()->findByPk($data[0]['city_id'])->name;
                                    }
                                    
                                    if($data[0]['district_id']){
                                        echo Districts::model()->findByPk($data[0]['district_id'])->name;
                                    }
                                ?></td>
                        <th width="150">详细地址：</th>
                        <td colspan="15"><?php echo $data[0]['address']; ?></td>
                    </tr>
                    <tr>
                        <th width="150">联系邮箱：</th>
                        <td colspan="15"><?php echo $data[0]['email'] ?></td>
                        <th width="150">是否旅行社：</th>
                        <td colspan="15"><?php echo $data[0]['agency_type']==1?"是":"否"; ?></td>
                    </tr>
                    <tr>
                        <th width="150">公司电话：</th>
                        <td colspan="15"><?php echo $data[0]['telephone'] ?></td>
                        <th width="150">是否开通全平台散客票：</th>
                        <td colspan="15"><?php echo $data[0]['is_distribute_person']==1?"是":"否"; ?></td>
                    </tr>
                    <tr>
                        <th width="150">帐号状态：</th>
                        <td colspan="15"><?php if ($data[0]['status'] == 1) {
                                    echo '启用';
                                } else {
                                    echo '禁用';
                                } ?></td>
                        <th width="150">是否开通全平台团体票：</th>
                        <td colspan="15"><?php echo $data[0]['is_distribute_group']==1?"是":"否"; ?></td>
                    </tr>
                    <tr>
                        <th width="150">营业执照：</th>
                        <td colspan="15"><?php if ($data[0]['business_license']): ?>
                                    <a href="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                    $data[0]['business_license']; ?>" class="editable-empty thumbs">
                                        <img src="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                        $data[0]['business_license'] ?>" width="100"/>
                                    </a>
                                <?php else: ?>
                                    暂无
                                <?php endif; ?></td>
                    </tr>
                     <tr>
                        <th width="150">税务登记证：</th>
                        <td colspan="15"><?php if ($data[0]['tax_license']): ?>
                                <a href="<?php echo /* PI_UPLOADS_URL.'/'. */
                                    $data[0]['tax_license'];
                                    ?>" class="editable-empty thumbs">
                                    <img src="<?php echo /* PI_UPLOADS_URL.'/'. */
                               $data[0]['tax_license'];
                               ?>" width="100"/>
                                </a>
                            <?php else: ?>
                                暂无<?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th width="150">企业logo：</th>
                        <td colspan="15"><?php if ($data[0]['logo']): ?>
                                <a href="<?php echo $data[0]['logo']; ?>" class="editable-empty thumbs">
                                    <img src="<?php echo $data[0]['logo']; ?>" width="100" id="logo">
                                </a>
                            <?php else: ?>
                                暂无
                            <?php endif; ?></td>
                    </tr>
                    <?php if ($data[0]['type'] == 'landscape') { ?>
                        <tr>
                            <th width="150"> 拥有景区：</th>
                            <td colspan="15">
                            <?php if ($data[0]['poi']['data']): ?>
                                <?php foreach ($data[0]['poi']['data'] as $key => $val): ?>
                                    <?php echo $val['name']; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </td>    
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
 <div class="panel-footer" style="padding-left:5%">
    <button class="btn btn-default" type="button" onclick="javascript:history.go(-1);" id="export">返回</button>
</div>

<link href="/js/touchTouch/touchTouch.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/touchTouch/touchTouch.jquery.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        //点击缩略图查看大图事件
        $('.thumbs').touchTouch();
    })
</script>
