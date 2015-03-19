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

<div class="container-fluid padded">
<div class="box">
<div class="box-header">
    <span class="title"><i class="icon-edit"></i> 添加旅行社</span>
</div>
<div class="box-content">
<form action="index.php?c=organization&a=save" method="post" id="scenic_add_form" class="fill-up">
<div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
           <!--  <li class="agency-type">
                <label>旅行社类别：<span class="note"></span></label>
                <div class="row-fluid">
                    <div class="span4">
                        <input type="radio" class="icheck" name="type" value="landscape" checked>
                        <label>景区</label>
                    </div>
                    <div class="span4">
                        <input type="radio" class="icheck" name="type" value="agency">
                        <label>旅行社</label>
                    </div>
                    <div class="span4"></div>
                </div>
            </li> -->
            <input type="hidden" name="type" value="agency">
            <li class="input">
                <label>联系人：<span class="note"></span>
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[minSize[2],maxSize[32]]" name="contact" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>联系邮箱：
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[custom[email]]" name="email" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>公司电话：
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[custom[phone]]" name="telephone" placeholder="">
                </label>
            </li>
            <li>
                <label>所在地区：</label>
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
                <label>详细地址：
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[minSize[6],maxSize[64]]" name="address" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>企业简介：
                    <textarea rows="4" name="description" placeholder="" data-prompt-position="topLeft" class="validate[maxSize[200]]"></textarea>
                </label>
            </li>
        </ul>
    </div>

    <div class="span6">
        <ul class="padded separate-sections">
            <li class="input">
                <label>企业名称：<span class="note"></span>
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[]" name="name" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>手机号码：<!-- <strong class="status-error">*</strong> --><span class="note"></span>
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[custom[mobile]]" name="mobile" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>公司传真：
                    <input type="text" value="" data-prompt-position="topLeft" class="validate[custom[phone]]" name="fax" placeholder="">
                </label>
            </li>
            <li>
                <label>旅行社状态：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="status" value="normal" checked>
                            <label>启用</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="status" value="disable">
                            <label>禁用</label>
                        </div>
                        <div class="span4"></div>
                    </div>
            </li>
            <li>
                <label>旅行社权限：</label>
                <div class="row-fluid">
                    <div class="span4">
                        <input type="checkbox" class="icheck" name="supply" value="yes" checked>
                        <label>供应权</label>
                    </div>
                    <div class="span4">
                        <input type="checkbox" class="icheck agencies-input" name="distribute" value="yes" disabled>
                        <label style="color:#999">分销权</label>
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
    <div class="span4 scenics"></div>
    <div class="span4 scenics"></div>

    <div class="span4 agencies" style="display:none;">
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
    <div class="span4 agencies" style="display:none;">
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
<!-- 营业执照、税务登记证、经营许可证结束 -->

<!--
<div class="row-fluid">
    <ul class="padded separate-sections">
        <li>
            <div class="row-fluid">
                <div class="span1">
                  <a href="javascript:void(0)" id="bank-show-btn"><span class="label label-green"><i class="icon-plus"></i>添加银行</span></a>
                </div>
                <div class="span1">
                    <a href="javascript:void(0)" id="alipay-show-btn"><span class="label label-blue"><i class="icon-plus"></i>添加支付宝</span></a>
                </div>
            </div>
        </li>
        <!--账号
        <li class="input">
            <label>选择默认收款帐号：</label>
            <ul style="tex-align:center">
                <li class="row-fluid" id="banks">

                </li>
            </ul>
        </li>
        <li class="box" id="scenics">
            <div class="box-header">
                <span class="title">拥有景区</span>
                <ul class="box-toolbar">
                    <li><a href="javascript:void(0)" class="add-scenic"><span class="label label-green"><i class="icon-plus"></i>增加</span></a></li>
                </ul>
            </div>
            <div class="content">
                <table class="table table-normal">
                    <thead>
                    <tr>
                        <td>所在地</td>
                        <td>景区名称</td>
                        <td style="width:50px">操作</td>
                    </tr>
                    </thead>
                    <tr>
                        <td>
                            <div class="span4">
                                <select class="uniform" name="provice_poi[]">
                                    <option value="">省</option>
                                </select>
                            </div>
                            <div class="span4">
                                <select class="uniform" name="city_poi[]">
                                    <option value="">市</option>
                                </select>
                            </div>
                            <div class="span4">
                                <select class="uniform" name="area_poi[]">
                                    <option value="">县</option>
                                </select>
                            </div>
                        </td>
                        <td><input type="text" value="" name="senice_name[]" placeholder=""></td>
                        <td class="center"><a title="删除" href="javascript:void(0)" class="del-scenic"><i class="icon-trash"></i></a></td>
                    </tr>
                </table>
            </div>
        </li>
    </ul>
</div>
-->

<!-- 企业简称开始 -->
<div class="row-fluid">
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
</div>
<!-- 企业简称结束 -->

<!-- 企业logo开始 -->
<div class="row-fluid">
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
</div>
<!-- 企业logo结束 -->

<div class="form-actions">
    <button class="btn btn-lg btn-blue" type="button" id="btn-add">保存</button>
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

<div id="bank-show" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">添加银行</h6>
    </div>
    <form action="index.php?c=ajax&a=addBankAccount" id="add-bank-form" method="post">
    <input type="hidden" name="type" value="bank">
    <input type="hidden" name="organization_id" value="1">
        <div class="modal-body">
            <table>
                <tr>
                    <th><label>收款银行：</label></th>
                    <td>
                        <div class="search">
                            <input type="search" id="bank" placeholder="输入银行名称" name="bank_name"/>
                            <label class="datalist" for="bank">
                                <?php foreach($data as $k => $v){?>
                                    <div class="list" data-index="<?php echo $v['name'];?>"><?php echo $v['name'];?></div>
                                <?php }?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label>卡号：</label></th><td><input type="text" value="" class="" name="account" placeholder="" id="bank-name"></td>
                </tr>
                <tr>
                    <th><label>户名：</label></th><td><input type="text" value="" class="" name="account_name" placeholder="" id="bank-num"></td>
                </tr>
            </table>
            <script>
                if (document.addEventListener) {
                    var eleStyle = document.createElement("style"),
                        eleInput = document.querySelector("#bank"),
                        eleDatalist = document.querySelector(".datalist");

                    // 用来CSS控制的style插入
                    document.querySelector("head").appendChild(eleStyle);

                    // 文本框输入
                    eleInput.addEventListener("input", function() {
                        var val = this.value.trim().toLowerCase();
                        if (val !== '') {
                            eleStyle.innerHTML = '.list:not([data-index*="'+ this.value +'"]) { display: none; }';
                        } else {
                            eleStyle.innerHTML = '';
                        }
                    });

                    // 点击确定
                    eleDatalist.addEventListener("mousedown", function(event) {
                        eleInput.value = event.target.innerHTML;
                        eleInput.blur();
                    });
                } else {
                    var timeClick = 0;
                    document.querySelector && (document.querySelector(".datalist").style.visibility = "hidden");
                    document.getElementById("bank").onclick = function() {
                        timeClick++;
                        if (timeClick == 3) {
                            this.insertAdjacentHTML("beforebegin", '<p style="color:#cd0000;"></p>');
                        }
                    };
                }
            </script>
        </div>
        <div class="modal-footer">
            <button class="btn btn-green" type="button" id="add-bank">保存</button>
        </div>
    </form>
</div>

<form action="" method="post">
<div id="alipay-show" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h6 id="modal-formLabel">添加支付宝</h6>
    </div>
    <div class="modal-body">
        <table>
            <tr>
                <th><label>支付宝帐号：</label></th><td><input type="text" value="" class="" name="" placeholder="" id="alipay-name"></td>
            </tr>
            <tr>
                <th><label>账户名：</label></th><td><input type="text" value="" class="" name="" placeholder="" id="alipay-num"></td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button class="btn btn-green" type="submit" id="add-bank">保存</button>
    </div>
</div>
</form>

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

<div id="upload-show2" class="modal hide fade">
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
</div>
<script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
<script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="Views/js/common/common.js"></script>
<script src="Views/js/organization/add.js"></script>