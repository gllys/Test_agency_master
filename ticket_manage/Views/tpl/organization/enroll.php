<!DOCTYPE html>
<html>
<?php get_header();?>
<body>
<?php get_top_nav();?>
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<?php get_menu();?>

<div class="main-content">
<?php get_crumbs();?>
<div id="show_msg">

</div>
<style>
	.note{color:red;}
</style>
<div class="container-fluid padded">
<div class="box">
<div class="box-header">
    <span class="title"><i class="icon-edit"></i> 添加分销商</span>
</div>
<div class="box-content">
<form action="index.php?c=organization&a=registerOrganzation" method="post" id="register_form" class="fill-up">
<div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
            <input type="hidden" name="type" value="agency">
            <li class="input">
                <label>用户名：<span class="note">*</span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[required,minSize[6],maxSize[32]]" name="account" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>联系人：<span class="note">*</span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[minSize[2],maxSize[32]]" name="contact" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>详细地址：<span class="note">*</span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[minSize[6],maxSize[64]]" name="address" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>公司电话：<span class="note"></span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[custom[phone]]" name="telephone" placeholder="">
                </label>
            </li>
            <li>
                <label>所在地区：</label><span class="note"></span>
                <div class="row-fluid">
                    <div class="span4">
                        <select class="uniform" name="province" id="province">
                            <option value="">省</option>
                        </select>
                    </div>
                    <div class="span4">
                        <select class="uniform" name="city" id="city">
                            <option value="">市</option>
                        </select>
                    </div>
                    <div class="span4">
                        <select class="uniform" name="area" id="area">
                            <option value="">区</option>
                        </select>
                    </div>
                </div>
            </li>
            <li class="input">
                <label>联系邮箱：<span class="note"></span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[custom[email]]" name="email" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>企业简介：<span class="note"></span>
                    <textarea rows="4" name="description" placeholder="" data-prompt-position="topRight: -80" class="validate[maxSize[200]]"></textarea>
                </label>
            </li>
        </ul>
    </div>

    <div class="span6">
        <ul class="padded separate-sections">
			<li class="input">
                <label>密码：<span class="note">*</span>
                    <input type="password" value="" data-prompt-position="topRight: -80" class="validate[required]" name="password" placeholder="">
                </label>
            </li>                        
            <li class="input">
                <label>企业名称：<span class="note">*</span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[]" name="name" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>手机号码：<!-- <strong class="status-error">*</strong> --><span class="note">*</span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[custom[mobile]]" name="mobile" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>公司传真：<span class="note"></span>
                    <input type="text" value="" data-prompt-position="topRight: -80" class="validate[custom[phone]]" name="fax" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>企业简称（最多四个字符）：<span class="note"></span>
                    <input type="text" data-prompt-position="topRight: -80" class="validate[maxSize[4]]" name="abbreviation" placeholder="">
                </label>
            </li>
            <li>
                <label>是否旅行社：</label>
				<div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="agency_type" value="1" checked>
                            <label>是</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="agency_type" value="0">
                            <label>否</label>
                        </div>
                        <div class="span4"></div>
                </div>            
            </li>
            <li>
                <label>全平台散客分销权：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_distribute_person" value="1" checked>
                            <label>是</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_distribute_person" value="0">
                            <label>否</label>
                        </div>
                        <div class="span4"></div>
                    </div>
            </li>
            <li>
                <label>全平台团客分销权：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_distribute_group" value="1" checked>
                            <label>是</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_distribute_group" value="0">
                            <label>否</label>
                        </div>
                        <div class="span4"></div>
                    </div>
            </li>            
        </ul>
    </div>
</div>

<!-- 营业执照、税务登记证、经营许可证开始 -->
<div class="row-fluid">
    <div class="span4">
        <ul class="padded separate-sections">
            <li class="input">
                <label>营业执照：<span class="note"></span>
                <a id='a_licence_id' href="" class="editable-empty thumbs">
                    <img id="img_licence_id" src="" height="100" width="100" />
                </a>
                <input type="hidden" name="licence_id" value="<?php echo $data[0]['licence']['id'];?>">
                <a id="upload-button-licence" href="#upload-show1" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                </label>
            </li>
        </ul>
    </div>

    <div class="span4 agencies" id="tax">
        <ul class="padded separate-sections">
            <li class="input">
                <label>税务登记证：<span class="note"></span>
                <a  id="a_tax_id" href="" class="editable-empty thumbs">
                    <img id="img_tax_id" src="" height="100" width="100" />
                </a>
                <input type="hidden" name="tax_id" value="">
                <a id="upload-button-tax" href="#upload-show4" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                </label>
            </li>
        </ul>
    </div>
    <div class="span4 agencies" id="certificate">
        <ul class="padded separate-sections">
            <li class="input">
                <label>经营许可证：<span class="note"></span>
                <a id="a_certificate_id" href="" class="editable-empty thumbs">
                    <img id="img_certificate_id" src="" height="100" width="100" />
                </a>
                <input type="hidden" name="certificate_id" value="">
                <a id="upload-button-certificate" href="#upload-show3" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                </label>
            </li>
        </ul>
    </div>
</div>
<!-- 企业简称开始 -->
<!--div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
            <li class="input">
                <label>企业简称（最多四个字符）：<span class="note"></span>
                    <input type="text" data-prompt-position="topLeft" class="validate[maxSize[4]]" name="abbreviation" placeholder="">
                </label>
            </li>
        </ul>
    </div>
    <div class="span6"></div>
</div-->
<!-- 企业简称结束 -->

<!-- 企业logo开始 -->
<!--div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
            <li class="input">
                <label>企业logo：<span class="note"></span>
                    <a id="a_logo_id" href="" class="editable-empty thumbs">
                        <img id="img_logo_id" src="" height="100" width="100" />
                    </a>
                    <input type="hidden" name="logo_id" value="">
                    <a id="upload-button-logo" href="#upload-show2" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                </label>
            </li>
        </ul>
    </div>
    <div class="span6"></div>
</div-->
<!-- 企业logo结束 -->

<div class="form-actions">
    <button class="btn btn-lg btn-blue" type="button" id="btn-register">保存</button>
</div>

</form>
<style>
    .agencies{
        display:none
    }
    ul{
        margin:0;
        padding:10px 0;
        list-style:none
    }
    .icheckbox_flat-aero{
        margin-right:5px;
        vertical-align:middle
    }
    #banks .span6{
        clear:both;
        margin-left:0
    }
    .table-normal tbody td a{
        text-decoration:none
    }
    .modal-body table{
        width:100%
    }
    .modal-body .search{
        margin:0;
        position:relative;
    }
    .datalist{
        position:absolute;
        top:25px;
        width: 210px;
        background-color: #fff;
        box-shadow: 0 1px #ccc, 1px 0 #ccc, -1px 0 #ccc, 0 -1px #ccc;
        overflow: hidden;
        visibility: hidden;
    }
    .search input:focus + .datalist {
        visibility:visible;
    }

    #scenics tr:first-child .del-scenic{
        display:none
    }
</style>



</div>
</div>
</div>
</div>

<div id="upload-show1" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">营业执照上传</h6>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <form action="index.php?c=ajax&a=fileUpload" method="post" id="licence-form1">
				<span class="span9">
					<input type="file" name="attachments">
				</span>
				<span class="span3">
					<button class="btn btn-blue" data-dismiss="modal" id="licence_id"><i class="icon-cloud-upload"></i> 上传图片</button>
				</span>

            </form>
        </div>
    </div>
</div>

<div id="upload-show3" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">经营许可证上传</h6>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <form action="index.php?c=ajax&a=fileUpload" method="post" id="certificate-form1">
				<span class="span9">
					<input type="file" name="attachments">
				</span>
				<span class="span3">
					<button class="btn btn-blue" data-dismiss="modal" id="certificate_id"><i class="icon-cloud-upload"></i> 上传图片</button>
				</span>
            </form>
        </div>
    </div>
</div>

<div id="upload-show4" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">税务登记证上传</h6>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <form action="index.php?c=ajax&a=fileUpload" method="post" id="tax-form1">
				<span class="span9">
					<input type="file" name="attachments">
				</span>
				<span class="span3">
					<button class="btn btn-blue" data-dismiss="modal" id="tax_id"><i class="icon-cloud-upload"></i> 上传图片</button>
				</span>
            </form>
        </div>
    </div>
</div>

<!--div id="upload-show2" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">企业logo上传</h6>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <form action="index.php?c=ajax&a=fileUpload" method="post" id="logo-form1">
				<span class="span9">
					<input type="file" name="attachments">
				</span>
				<span class="span3">
					<button class="btn btn-blue" data-dismiss="modal" id="logo_id"><i class="icon-cloud-upload"></i> 上传图片</button>
				</span>
            </form>
        </div>
    </div>
</div-->
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/organization/add.js"></script>
<script>
$(document).ready(function(){
    $("input[name=agency_type]").change(function() {
        var vals = $("input[name=agency_type]:checked").val();
        if (vals == 0) {
            $("#tax").hide();
            $("#certificate").hide();
        }
        if (vals == 1) {
            $("#tax").show();
            $("#certificate").show();
        }
    });
})
</script>