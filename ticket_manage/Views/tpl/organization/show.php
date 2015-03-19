<!DOCTYPE html>
<html>
<?php get_header(); ?>
<body>
<?php get_top_nav(); ?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu(); ?>
<div class="main-content">
    <?php get_crumbs(); ?>
    <div id="show_msg"></div>
    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title"><i
                        class="icon-edit"></i> <?php echo $data[0]['type'] == 'supply' ? '供应商' : '分销商'; ?>信息</span>
            </div>
            <div class="box-content">
                <div class="row-fluid">
                    <div class="span6">
                        <ul class="padded separate-sections">
                            <li>企业名称：<?php echo $data[0]['name']; ?></li>
                            <li class="input">
                                企业简称：<?php echo $data[0]['abbreviation']; ?>
                            </li>
                            <li class="input">
                                联系人：<?php echo $data[0]['contact']; ?>
                            </li>
                            <li class="input">
                                联系邮箱:<?php echo $data[0]['email'] ?>
                            </li>
                            <li class="input">
                                公司电话：<?php echo $data[0]['telephone'] ?>
                            </li>
                            <li>所在地区：
                                <?php echo $data[0]['district']; ?>
                            </li>
                            <li>帐号状态：<?php if ($data[0]['status'] == 1) {
                                    echo '启用';
                                } else {
                                    echo '禁用';
                                } ?></li>
                            <li>营业执照：
                                <?php if ($data[0]['business_license']): ?>
                                    <a href="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                    $data[0]['business_license']; ?>" class="editable-empty thumbs">
                                        <img src="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                        $data[0]['business_license'] ?>" width="100"/>
                                    </a>
                                <?php else: ?>
                                    暂无
                                <?php endif; ?>

                            </li>
                            <?php if ($data[0]['type'] == 'agency') { ?>
                                <li>税务登记证：
                                <?php if ($data[0]['tax_license']): ?>
                                    <a href="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                    $data[0]['tax_license']; ?>" class="editable-empty thumbs">
                                        <img src="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                        $data[0]['tax_license']; ?>" width="100"/>
                                    </a>
                                <?php else: ?>
                                    暂无
                                <?php endif; ?>
                                </li>
                            <?php } ?>


                        </ul>
                    </div>

                    <div class="span6">
                        <ul class="padded separate-sections">
                            <li><?php echo $data[0]['type'] == 'supply' ? '供应商' : '分销商'; ?>
                                编号：<?php echo $data[0]['id']; ?></li>
                            <li class="input">
                                手机号码： <?php echo $data[0]['mobile']; ?>
                            </li>
                            <li class="input">公司传真：<?php echo $data[0]['fax']; ?>
                            </li>
                            <li class="input">
                                详细地址：<?php echo $data[0]['address']; ?>
                            </li>
                            <?php if($data[0]['type'] == 'supply'):?>
                            <li class="input">
                                供应商类别：<?php echo $data[0]['supply_type'] ? '景区' : '批发商'; ?>
                            </li>
                            <?php endif;?>
                            <?php if($data[0]['type']=="agency"): ?>
                                <li class="input">是否旅行社：
                                    <?php echo $data[0]['agency_type']==1?"是":"否"; ?>
                                </li>
                                <li class="input">是否开通全平台散客票：
                                    <?php echo $data[0]['is_distribute_person']==1?"是":"否"; ?>
                                </li>
                                <li class="input">是否开通全平台团体票：
                                    <?php echo $data[0]['is_distribute_group']==1?"是":"否"; ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($data[0]['type'] == 'agency'){ ?>
                                <li style="<?php echo $data[0]['agency_type']==1?"display:none":"1" ?>">经营许可证：
                                    <?php if ($data[0]['certificate_license']): ?>
                                        <a href="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                        $data[0]['certificate_license']; ?>" class="editable-empty thumbs">
                                            <img src="<?php echo /*PI_UPLOADS_URL.'/'.*/
                                            $data[0]['certificate_license']; ?>" width="100"/>
                                        </a>
                                    <?php else: ?>
                                        暂无
                                    <?php endif; ?>
                                </li>
                                <?php } ?>
                        </ul>
                    </div>
                </div>

                <div class="row-fluid">
                    <ul class="padded separate-sections">
                        <?php if ($data[0]['type'] == 'landscape') { ?>
                            <li class="input">
                                拥有景区：
                                <?php if ($data[0]['poi']['data']): ?>
                                    <?php foreach ($data[0]['poi']['data'] as $key => $val): ?>
                                        <?php echo $val['name']; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </li>
                        <?php } ?>
                        <li class="input">
                            <label>企业logo：</label>
                            <?php if ($data[0]['logo']): ?>
                                <a href="<?php echo $data[0]['logo']; ?>" class="editable-empty thumbs">
                                    <img src="<?php echo $data[0]['logo']; ?>" width="100" id="logo">
                                </a>
                            <?php else: ?>
                                暂无
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>

                <div class="form-actions">
                    <button class="btn btn-lg btn-blue" type="button" id="submit-button-organization"
                            onclick="javascript:history.go(-1)">返回
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //点击缩略图查看大图事件
        $('.thumbs').touchTouch();
    })
</script>
</body>
</html>
