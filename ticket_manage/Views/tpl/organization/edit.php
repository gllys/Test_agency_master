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
<div id="show_msg"></div>

<div class="container-fluid padded">
<div class="box">
<div class="box-header">
    <span class="title"><i class="icon-edit"></i> 编辑<?php echo $data[0]['type'] == 'supply' ? '供应商' : '分销商';?></span>
</div>
<div class="box-content">
<form action="index.php?c=organization&a=save" method="post" id="scenic_add_form" class="fill-up">
<input type="hidden" name="id" value="<?php echo $data[0]['id'];?>">
<div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
            <li class="input">
                <label>联系人：<strong class="status-error">*</strong><span class="note"></span>
                    <input type="text" value="<?php echo $data[0]['contact']?>" data-prompt-position="topRight: -80" class="validate[required,minSize[2],maxSize[32]]" name="contact" placeholder="">
                </label>
            </li>
            <!-- 企业简称开始 -->
            <li class="input">
                <label>企业简称（最多四个字符）：<span class="note"></span>
                    <input type="text" value="<?php echo $data[0]['abbreviation']?>" data-prompt-position="topRight: -80" class="validate[maxSize[4]]" name="abbreviation" placeholder="">
                </label>
            </li>
            <!-- 企业简称结束 -->
            <li class="input">
                <label>联系邮箱：
                    <input type="text" value="<?php echo $data[0]['email']?>"  data-prompt-position="topRight: -80" class="validate[custom[email]]" name="email" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>公司电话：
                    <input type="text" value="<?php echo $data[0]['telephone']?>" data-prompt-position="topRight: -80" class="validate[custom[phone]]" name="telephone" placeholder="">
                </label>
            </li>
            <li>
                <label>所在地区：</label>
                <div class="row-fluid">
                    <div class="span4">
                        <select class="uniform" name="province" id="province">
                            <option value="<?php echo $data[0]['districts']['id'] ? $data[0]['districts']['id'] : '';?>"><?php echo $data[0]['districts']['name'] ? $data[0]['districts']['name'] : '省';?></option>
                        </select>
                    </div>
                    <div class="span4">
                        <select class="uniform" name="city" id="city">
                            <option value="<?php echo $data[0]['city']['id'] ? $data[0]['ctty']['id'] : '' ;?>"><?php echo $data[0]['city']['name'] ?  $data[0]['city']['name'] : '市';?></option>
                        </select>
                    </div>
                    <div class="span4">
                        <select class="uniform" name="area" id="area">
                            <option value="<?php echo $data[0]['area']['id'] ?  $data[0]['area']['id'] : '' ;?>"><?php echo $data[0]['area']['name'] ?  $data[0]['area']['name'] : '县';?></option>
                        </select>
                    </div>
                </div>
            </li>
            <li class="input">
                <label>详细地址：
                    <input type="text" value="<?php echo $data[0]['address'];?>" data-prompt-position="topRight: -80" class="validate[minSize[6],maxSize[64]]" name="address" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>企业简介：
                    <textarea rows="4" name="description" placeholder="" data-prompt-position="topRight: -80"  class="validate[maxSize[200]]"><?php echo $data[0]['description'];?></textarea>
                </label>
            </li>
        </ul>
    </div>

    <div class="span6">
        <ul class="padded separate-sections">
            <li class="input">
                <label>企业名称：<strong class="status-error">*</strong><span class="note"></span>
                    <input type="text" value="<?php echo $data[0]['name'];?>" data-prompt-position="topRight: -80" class="validate[required]" name="name" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>手机号码：<strong class="status-error">*</strong><span class="note"></span>
                    <input type="text" value="<?php echo $data[0]['mobile'];?>" data-prompt-position="topRight: -80" class="validate[custom[mobile]]" name="mobile" placeholder="">
                </label>
            </li>
            <li class="input">
                <label>公司传真：
                    <input type="text" value="<?php echo $data[0]['fax'];?>" data-prompt-position="topRight: -80" class="validate[custom[phone]]" name="fax" placeholder="">
                </label>
            </li>
            <li>
                <label><?php echo $data[0]['type'] == 'supply' ? '供应商' : '分销商';?>状态：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="status" value="1" <?php if($data[0]['status'] == '1'){ echo 'checked=checked';}?>>
                            <label>启用</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="status" value="0" <?php if($data[0]['status'] == '0'){ echo 'checked=checked';}?>>
                            <label>禁用</label>
                        </div>
                        <div class="span4"></div>
                    </div>
            </li>
            <?php if($data[0]['type']=="supply"): 
              ?>
            <li>
                <label>信用支付：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_credit" value="1" <?php if($data[0]['is_credit'] == '1'){ echo 'checked=checked';}?>>
                            <label>启用</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_credit" value="0" <?php if($data[0]['is_credit'] == '0'){ echo 'checked=checked';}?>>
                            <label>禁用</label>
                        </div>
                        <div class="span4"></div>
                    </div>
            </li>
            <li>
                <label>储值状态：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_balance" value="1" <?php if($data[0]['is_balance'] == '1'){ echo 'checked=checked';}?>>
                            <label>启用</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="is_balance" value="0" <?php if($data[0]['is_balance'] == '0'){ echo 'checked=checked';}?>>
                            <label>禁用</label>
                        </div>
                        <div class="span4"></div>
                    </div>
            </li>
             <li>
                <label>供应商类别：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <label><?php echo $data[0]['supply_type'] ? '景区' : '批发商'?></label>
                        </div>
                    </div>
            </li>
             <?php endif; ?>
            
            
            
            <?php if($data[0]['type']=="agency"): ?>
                <li>
                    <label>是否旅行社：</label>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="radio" class="icheck" name="agency_type" value="1" <?php if($data[0]['agency_type'] == '1'){ echo 'checked=checked';}?>>
                            <label>是</label>
                        </div>
                        <div class="span4">
                            <input type="radio" class="icheck" name="agency_type" value="0" <?php if($data[0]['agency_type'] == '0'){ echo 'checked=checked';}?>>
                            <label>否</label>
                        </div>
                        <div class="span4"></div>
                    </div>
                </li>
            <li>
                <label>是否开通全平台散客票：</label>
                <div class="row-fluid">
                    <div class="span4">
                        <input type="radio" class="icheck" name="is_distribute_person" value="1" <?php if($data[0]['is_distribute_person'] == 1){ echo 'checked=checked';}?>>
                        <label>是</label>
                    </div>
                    <div class="span4">
                        <input type="radio" class="icheck" name="is_distribute_person" value="0" <?php if($data[0]['is_distribute_person'] == 0){ echo 'checked=checked';}?>>
                        <label>否</label>
                    </div>
                    <div class="span4"></div>
                </div>
            </li>
            <li>
                <label>是否开通全平台团体票：</label>
                <div class="row-fluid">
                    <div class="span4">
                        <input type="radio" class="icheck" name="is_distribute_group" value="1" <?php if($data[0]['is_distribute_group'] == '1'){ echo 'checked=checked';}?>>
                        <label>启用</label>
                    </div>
                    <div class="span4">
                        <input type="radio" class="icheck" name="is_distribute_group" value="0" <?php if($data[0]['is_distribute_group'] == '0'){ echo 'checked=checked';}?>>
                        <label>禁用</label>
                    </div>
                    <div class="span4"></div>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- 营业执照、税务登记证、经营许可证开始 -->
<div class="row-fluid">
    <div class="span4">
        <ul class="padded separate-sections">
            <li class="input">
                <label>营业执照：<span class="note"></span>
                <a id='a_licence_id' href="<?php echo $data[0]['business_license']?>" class="editable-empty thumbs">
                    <img id="img_licence_id" src="<?php echo $data[0]['business_license']?>" height="100" width="100" />
                </a>
                <input type="hidden" name="licence_id" value="<?php echo $data[0]['business_license']?>">
                <a id="upload-button-licence" href="#upload-show1" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                </label>
            </li>
        </ul>
    </div>
    <?php if($data[0]['type'] == 'landscape'):?>
        <div class="span4"></div>
        <div class="span4"></div>
    <?php endif;?>
    <?php if($data[0]['type'] == 'agency'){?>
        <div class="span4 agency_type" style="<?php echo $data[0]['agency_type']==0?"display:none":"" ?>">
            <ul class="padded separate-sections">
                <li class="input">
                    <label>税务登记证：<span class="note"></span>
                    <a  id="a_tax_id" href="<?php echo $data[0]['tax_license']?>" class="editable-empty thumbs">
                        <img id="img_tax_id" src="<?php echo $data[0]['tax_license'];?>" height="100" width="100" />
                    </a>
                    <input type="hidden" name="tax_id" value="<?php echo $data[0]['tax_license'];?>">
                    <a id="upload-button-tax" href="#upload-show4" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                    </label>
                </li>
            </ul>
        </div>
        <div class="span4 agency_type" style="<?php echo $data[0]['agency_type']==0?"display:none":"1" ?>">
            <ul class="padded separate-sections">
                <li class="input">
                    <label>经营许可证：<span class="note"></span>
                    <a id="a_certificate_id" href="<?php echo $data[0]['certificate_license']?>" class="editable-empty thumbs">
                        <img id="img_certificate_id" src="<?php echo $data[0]['certificate_license']?>" height="100" width="100" />
                    </a>
                    <input type="hidden" name="certificate_id" value="<?php echo $data[0]['certificate_license']?>">
                    <a id="upload-button-certificate" href="#upload-show3" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                    </label>
                </li>
            </ul>
        </div>
    <?php }?>
</div>
<!-- 营业执照、税务登记证、经营许可证结束 -->

<!-- 企业logo开始 -->
<div class="row-fluid">
    <div class="span6">
        <ul class="padded separate-sections">
            <li class="input">
                <label>企业logo：<strong class="status-error">*</strong><span class="note"></span>
                    <a id="a_logo_id" href="<?php echo $data[0]['logo'];?>" class="editable-empty thumbs">
                        <img id="img_logo_id" src="<?php echo $data[0]['logo'];?>" height="100" width="100" />
                    </a>
                    <input type="hidden" name="logo_id" value="<?php echo $data[0]['logo'];?>">
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
    <form>
        <div class="modal-body">
            <table>
                <tr>
                    <th><label>收款银行：</label></th>
                    <td>
                        <div class="search">
                            <input type="search" id="bank" placeholder="输入银行名称" />
                            <label class="datalist" for="bank">
                                <div class="list" data-index="工商银行gongshang">工商银行</div>
                                <div class="list" data-index="建设银行jianshe">建设银行</div>
                                <div class="list" data-index="农业银行nongye">农业银行</div>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label>卡号：</label></th><td><input type="text" value="" class="" name="" placeholder="" id="bank-name"></td>
                </tr>
                <tr>
                    <th><label>户名：</label></th><td><input type="text" value="" class="" name="" placeholder="" id="bank-num"></td>
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
            <button class="btn btn-green" type="submit">保存</button>
        </div>
    </form>
</div>


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
<script src="Views/js/organization/edit.js"></script>
<script>
    $('.icheck[name="agency_type"]').click(function(){
        if($(this).val()==1){
            $('.agency_type').show();
        }else{
            $('.agency_type').hide();
        }
    });
</script>