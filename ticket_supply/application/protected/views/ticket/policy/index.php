<?php
/*
 * 分销策略列表页
 * Date 2015-01-19
 * 
 */

$this->breadcrumbs = array('产品', '分销策略');
?>

<style>
    .rules {
        position: relative;
        display: inline-block;
    }
    .rules+.rules {
        margin-left: 20px;
    }
    .rules > span {
        color: #999;
        font-size: 12px;
        cursor: pointer
    }
    .rules > div >span {
        margin: 0 10px
    }
    .rules > div {
        display: none;
        position: absolute;
        top: 15px;
        left: 50px;
        z-index: 999;
        width: 500px;
        padding: 10px;
        background-color: #fbf8e9;
        border: 1px solid #fed202;
        border-radius: 2px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        word-wrap: break-word;
    }
    .rules > div .table {
        background: none;
    }
    .rules > div .table tr > * {
        border: 1px solid #e0d9b6
    }
    .rules:hover > div {
        display: block;
    }
</style>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <button class="btn btn-primary btn-sm pull-right" data-target=".bs-example-modal-static" data-toggle="modal" onclick="add_rule(0)">新建分销策略</button>
                分销策略
            </h4>
        </div>

    </div>


    <table class="table table-bordered table1">
        <thead>
            <tr>
                <th>分销策略名称</th>
                <th>分销策略说明</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($lists['data'])) : foreach ($lists['data'] as $rule) : ?>
                    <tr>
                        <td><?= $rule['name']; ?></td>
                        <td><?= $rule['note']; ?></td>
                        <td>
                            <a title="编辑" style="margin-left: 5px; border-width: 1px" href="javascript:;" onclick="edit_rule('<?= $rule['id'] ?>')" data-target=".bs-example-modal-static" data-toggle="modal" class="btn btn-success btn-bordered btn-xs" >编辑</a>
                            <a class="btn btn-bordered btn-xs btn-danger del" title="删除" style="margin-left: 5px;border-width: 1px"  onclick="del('<?= $rule['id'] ?>')" data-target="" data-toggle="modal"  >删除</a>
                        </td>
                    </tr>
                <?php endforeach;
            endif; ?>
        </tbody>
    </table>


    <div class="panel-footer">
        <div class="pull-right">
            <div class="pagenumQu">
                <?php
                if (isset($lists['data'])) {
                    $this->widget('common.widgets.pagers.ULinkPager', array(
                        'cssFile' => '',
                        'header' => '',
                        'prevPageLabel' => '上一页',
                        'nextPageLabel' => '下一页',
                        'firstPageLabel' => '',
                        'lastPageLabel' => '',
                        'pages' => $pages,
                        'maxButtonCount' => 5, //分页数量
                    ));
                }
                ?>
            </div>
        </div>
    </div>

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
<!-- mainpanel -->

<div class="modal fade bs-example-modal-static in"  tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="false" >
    <div id="modal1" class="modal-dialog" style="width: 1060px;">
        <div class="modal-content">
            <form class="form-horizontal form-bordered" id="repass-form">
                <div class="modal-header">
                    <button id="close_rule" aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 id="poli_title" class="modal-title">新建分销策略</h4>
                    <input type="hidden" id="distid" name="distid" value="">
                </div>
                <div class="modal-body">
                    <div class="form-group" style="overflow: inherit;">
                        <label class="col-sm-2 control-label">分销策略名称:</label>
                        <div class="col-sm-10">
                            <input type="text" tag="分销策略名称"  class="form-control validate[required]" maxlength="20" value="" id="pname" name="pname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分销策略说明:</label>
                        <div class="col-sm-10">
                            <textarea tag="分销策略说明" class="form-control" rows="5" maxlength="50" id="note" name="note"></textarea>
                        </div>
                    </div>
                    <table class="table table-bordered mb30" style="width: 976px !important;">
                        <tbody>
                            <tr style="background-color:#f7f7f7;">
                                <td style="width:200px;"><label>分销商名称</label></td>
                                <td style="width:100px;"><input id="blacknameAll" type="checkbox" value=""
                                                                name="">&nbsp;<label for="blacknameAll">不允许购买</label></td>
                                <td style=""><label style="margin-right: 4px;">散客结算价</label><input type="text" id="fatAll" name="daystorage" class="spinner form-control"></td>
                                <td style=""><label style="margin-right: 4px;">团购价</label><input type="text" id="groupAll" name="daystorage" class="spinner form-control">
                                </td><td style="width:150px;"><input id="creditAll" type="checkbox" value=""
                                                                    name="">&nbsp;<label for="creditAll">不允许信用支付</label></td>
                                <td style="width:150px;"><input id="advanceAll" type="checkbox" value=""
                                                               name="">&nbsp;<label for="advanceAll">不允许储存支付</label></td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="overflow-y:auto; width:975px;height:200px">
                        <table class="table table-bordered mb30">
                            <tbody id="distributor">
                                <tr>
                                    <td style="width:200px;">eweq</td>
                                    <td style="width:162px;"><input id="p_176" type="checkbox" value="176" name="blackname_arr[176]" class="blackgroup"></td>
                                    <td style="width:167px;"><input type="text" class="spinner-day"></td>
                                    <td><input type="text" class="spinner-day"></td>
                                    <td style="width:148px;"><input id="p_176" type="checkbox" value="176" name="blackname_arr[176]" class="blackgroup"></td>
                                    <td style="width:130px;"><input id="p_176" type="checkbox" value="176" name="blackname_arr[176]" class="blackgroup"></td>
                                </tr>
                        </table>
                    </div>
                    <table class="table table-bordered mb30" style="width:975px;">
                        <tbody id="newdist">
<!--                        <tr>-->
<!--                            <td style="width:200px;">新合作分销商</td>-->
<!--                            <td style="width:162px;"><input id="p_n" type="checkbox" value="1" name="new_blackname_flag" class="new_blackname_flag"></td>-->
<!--                            <td style="width:167px;"><input type="text" class="spinner" id="s_price_n" name="new_fat_price"></td>-->
<!--                            <td style="width:148px;"><input type="text" class="spinner" id="g_price_n" name="new_group_price"></td>-->
<!--                            <td style="width:150px;"><input id="credit_n" type="checkbox" value="1" name="new_credit_flag" class="new_credit_flag"></td>-->
<!--                            <td><input id="advance_n" type="checkbox" value="1" name="new_advance_flag" class="new_advance_flag"></td>-->
<!--                        </tr>-->
                        </tbody>
                        <tbody id="otherdist">
                            <tr style="background-color:#f7f7f7;">
                                <td style="width:200px;">未合作分销商</td><td style="width:116px;">
                                    <input id="p_0" type="checkbox" value="0" name="blackname_arr[0]"></td>
                                <td style="width:200px;"><input type="text" class="spinner-day"></td>
                                <td><input type="text" class="spinner-day"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="rule_add" type="button" class="btn btn-success">保存</button>
                    <a class="btn btn-default" href="/ticket/policy">取消</a>
                </div>
            </form>
        </div>
    </div></div>
<!-- /.modal -->


<script>
    jQuery(document).ready(function() {
        var spinnerDay = jQuery('.spinner-day').spinner({'min': 1});
        spinnerDay.spinner('value', 1);
    });


    //删除分销策略
    function del(id) {
        if (id) {
            PWConfirm('确认删除此条策略?', function() {
                $.post('/ticket/policy/del', {id: id}, function(data) {
                    if (data.error == 0) {
                        location.href = "/ticket/policy";
                    } else {
                        setTimeout(function() {
                            alert(data.message);
                        }, 500)
                    }
                }, 'json');
            });
            return false;
        }
    }
    //新增分销策略
    function add_rule(distid) {
        $('#verify-modal').html();
        $('#distributor').empty();
        $('#otherdist').empty();
        $('#newdist').empty();
        $('#fatAll').val('');
        $('#groupAll').val('');
        $('.formError').remove();
        $('#pname').val('');
        $('#note').val('');
        $('#poli_title').html('新建分销策略');
        $.post('/ticket/policy/getDistributor/?id=' + distid + '&time=' + parseInt(1000 * Math.random()), function(result) {
            result = JSON.parse(result);
            if (result.error == 0) {
                $('#verify-modal').html($("#modal1")).modal('show');
                $('#distributor').append(result.data);
                $('#otherdist').append(result.otherdata);
                $('#newdist').append(result.newdata);
                $('#modal1').show();
                $('#rule_add').show();
                $('#distid').val('');
                $("#blacknameAll").prop('checked', false);
                $("#fatAll").prop('checked', false);
                $("#groupAll").prop('checked', false);
                jQuery('.spinner').spinner({
                    spin: function(event, ui) {
                        val = ui.value;
                        if (val > 0) {
                            this.value = '+' + val;
                            return false
                        }
                    }
                });
                //spin事件统一控制散客价
                jQuery('#fatAll').spinner({
                    spin: function(event, ui) {
                        var fnum = ui.value;
                        var bnum = $("#s_price_0").val();
                        fnum = parseInt(fnum, 10);
                        if (fnum > 0) {
                            $("input[name^='s_price']").val('+' + fnum);
                            this.value = '+' + fnum;
                            $("#s_price_0").val(bnum);
                            return false
                        } else if (fnum <= 0) {
                            $("input[name^='s_price']").val(fnum);
                            $("#s_price_0").val(bnum);
                        }
                    }
                });
                //spin事件统一控制团客价
                jQuery('#groupAll').spinner({
                    spin: function(event, ui) {
                        var fnum = ui.value;
                        var bnum = $("#g_price_0").val();
                        fnum = parseInt(fnum, 10);
                        if (fnum > 0) {
                            $("input[name^='g_price']").val('+' + fnum);
                            this.value = '+' + fnum;
                            $("#g_price_0").val(bnum);
                            return false
                        } else if (fnum <= 0) {
                            $("input[name^='g_price']").val(fnum);
                            $("#g_price_0").val(bnum);
                        }
                    }
                });
                $("input[name^='s_price']").parent().css("width", "83px");
                $("input[name^='g_price']").parent().css("width", "83px");
//                $("#s_price_0").parent().css("width","84px"); 
                //禁止滚轮
                jQuery('.spinner').off('mousewheel');

            } else {
                alert("获取分销商失败");
            }
        });
    }
    //编辑分销策略
    function edit_rule(distid) {
		$('#repass-form').validationEngine({

		}); 
        $('#verify-modal').html();
        $('#distributor').empty();
        $('#otherdist').empty();
        $('#newdist').empty();
        $('#fatAll').val('');
        $('#groupAll').val('');
        $('.formError').remove();
        $('#poli_title').html('编辑分销策略');
        $.post('/ticket/policy/detail/?id=' + distid + '&time=' + parseInt(1000 * Math.random()), function(result) {
            result = JSON.parse(result);
            if (result.error == 0) {
                $('#verify-modal').html($("#modal1")).modal('show');
                $('#distributor').append(result.data);
                $('#otherdist').append(result.otherdata);
                $('#newdist').append(result.newdata);
                $('#modal1').show();
                $('#rule_add').show();
                $('#distid').val(result.dist_id);
                $('#pname').val(result.name);
                $('#note').val(result.note);
                $("#blacknameAll").prop('checked', false);
                $("#fatAll").prop('checked', false);
                $("#groupAll").prop('checked', false);
                jQuery('.spinner').spinner({
                    create: function(event, ui) {
                        if (this.value > 0) {
                            this.value = '+' + this.value;
                        } else if (this.value == 0) {
                            this.value = '';
                        }
                    },
                    spin: function(event, ui) {
                        val = ui.value;
                        if (val > 0) {
                            this.value = '+' + val;
                            return false
                        }
                    }
                });
                //spin事件统一控制散客价
                jQuery('#fatAll').spinner({
                    spin: function(event, ui) {
                        var fnum = ui.value;
                        var bnum = $("#s_price_0").val();
                        fnum = parseInt(fnum, 10);
                        if (fnum > 0) {
                            $("input[name^='s_price']").val('+' + fnum);
                            this.value = '+' + fnum;
                            $("#s_price_0").val(bnum);
                            return false
                        } else if (fnum <= 0) {
                            $("input[name^='s_price']").val(fnum);
                            $("#s_price_0").val(bnum);
                        }
                    }
                });
                //spin事件统一控制团客价
                jQuery('#groupAll').spinner({
                    spin: function(event, ui) {
                        var fnum = ui.value;
                        var bnum = $("#g_price_0").val();
                        fnum = parseInt(fnum, 10);
                        if (fnum > 0) {
                            $("input[name^='g_price']").val('+' + fnum);
                            this.value = '+' + fnum;
                            $("#g_price_0").val(bnum);
                            return false
                        } else if (fnum <= 0) {
                            $("input[name^='g_price']").val(fnum);
                            $("#g_price_0").val(bnum);
                        }
                    }
                });
                $("input[name^='s_price']").parent().css("width", "83px");
                $("input[name^='g_price']").parent().css("width", "83px");
            } else {
                alert("获取分销商失败");
            }
        });
    }
    //保存分销策略
    $(document).on('click', '#rule_add', function() {
        var obj = $('#repass-form');

        $('#rule_add').hide();
        $('#loader').show();

        //console.log(obj.serialize());return false;

        if (obj.validationEngine('validate') == true) {
            $.post('/ticket/policy/save', obj.serialize(), function(data) {
                if (data.error) {
                    alert(data.msg);
                    $('#rule_add').show();
                    $('#loader').hide();
                } else {
                    location.href = '/ticket/policy/';
                }
            }, 'json');
        } else {
            var browser = navigator.appName;
            var b_version = navigator.appVersion;
            var version = b_version.split(";");
            if (version[1] != undefined) {
                var trim_Version = version[1].replace(/[ ]/g, "");
                if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE8.0")
                {
                    var pnamestr = $("#pname").val();
                    if (pnamestr == undefined || pnamestr == '') {
                        alert('分销策略名称不能为空！');
                    } else if (pnamestr.length > 60) {
                        alert('分销策略名称不能超过20个字符！');
                    } else {
                        var notestr = $("#note").val();
                        if (notestr.length > 150) {
                            alert('分销策略说明不能超过50个字符！');
                        }
                    }
                }
            }
//                alert("请检查填写的内容");
            $('#rule_add').show();
            $('#loader').hide();
        }
        return false;
    });
    //判断中英文字符串长度
    function getLength(str) {
        var len = 0;
        for (var i = 0; i < str.length; i++) {
            var c = str.charCodeAt(i);
            //单字节加1 
            if ((c >= 0x0001 && c <= 0x007e) || (0xff60 <= c && c <= 0xff9f)) {
                len++;
            }
            else {
                len += 2;
            }
        }
        return len;
    }
    ;
    //黑名单全选
    $(document).on('click', '#blacknameAll', function() {
        if ($("#blacknameAll").prop('checked') == true) {
            $(".blackgroup").prop('checked', true);
        } else {
            $(".blackgroup").prop('checked', false);
        }
        ;
    });
    //信用支付全选
    $(document).on('click', '#creditAll', function() {
        if ($("#creditAll").prop('checked') == true) {
            $(".creditgroup").prop('checked', true);
        } else {
            $(".creditgroup").prop('checked', false);
        }
        ;
    });
    //储值支付全选
    $(document).on('click', '#advanceAll', function() {
        if ($("#advanceAll").prop('checked') == true) {
            $(".advancegroup").prop('checked', true);
        } else {
            $(".advancegroup").prop('checked', false);
        }
        ;
    });
    //统一控制散客价
    $(document).on('keyup', '#fatAll', function() {
        var fnum = $("#fatAll").val();
        var bnum = $("#s_price_0").val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $("input[name^='s_price']").val('+' + fnum);
                $("#fatAll").val('+' + fnum);
            } else if (fnum <= 0) {
                $("input[name^='s_price']").val(fnum);
            } else {
                $(this).val('');
            }
        }
        $("#s_price_0").val(bnum);
    });
    //散客价编辑后正数显示加号
    $(document).on('keyup', "input[name^='s_price']", function() {
        var fnum = $(this).val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $(this).val('+' + fnum);
            } else if (fnum <= 0) {
                $(this).val(fnum);
            } else {
                $(this).val('');
            }
        }
    });
    //统一控制团客价
    $(document).on('keyup', '#groupAll', function() {
        var fnum = $("#groupAll").val();
        var bnum = $("#g_price_0").val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $("input[name^='g_price']").val('+' + fnum);
                $("#groupAll").val('+' + fnum);
            } else if (fnum <= 0) {
                $("input[name^='g_price']").val(fnum);
            } else {
                $(this).val('');
            }
        }
        $("#g_price_0").val(bnum);
    });
    //团客价编辑后正数显示加号
    $(document).on('keyup', "input[name^='g_price']", function() {
        var fnum = $(this).val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $(this).val('+' + fnum);
            } else if (fnum <= 0) {
                $(this).val(fnum);
            } else {
                $(this).val('');
            }
        }
    });
    //统一控制散客价 chrome中文输入法
    $(document).on('blur', '#fatAll', function() {
        var fnum = $("#fatAll").val();
        var bnum = $("#s_price_0").val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $("input[name^='s_price']").val('+' + fnum);
                $("#fatAll").val('+' + fnum);
            } else if (fnum <= 0) {
                $("input[name^='s_price']").val(fnum);
            } else {
                $(this).val('');
            }
        }
        $("#s_price_0").val(bnum);
    });
    //散客价编辑后正数显示加号 chrome中文输入法
    $(document).on('blur', "input[name^='s_price']", function() {
        var fnum = $(this).val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $(this).val('+' + fnum);
            } else if (fnum <= 0) {
                $(this).val(fnum);
            } else {
                $(this).val('');
            }
        }
    });
    //统一控制团客价 chrome中文输入法
    $(document).on('blur', '#groupAll', function() {
        var fnum = $("#groupAll").val();
        var bnum = $("#g_price_0").val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $("input[name^='g_price']").val('+' + fnum);
                $("#groupAll").val('+' + fnum);
            } else if (fnum <= 0) {
                $("input[name^='g_price']").val(fnum);
            } else {
                $(this).val('');
            }
        }
        $("#g_price_0").val(bnum);
    });
    //团客价编辑后正数显示加号 chrome中文输入法
    $(document).on('blur', "input[name^='g_price']", function() {
        var fnum = $(this).val();
        if (fnum == '') {

        } else if (fnum == '-') {
            $(this).val('-');
        } else if (fnum == '+') {
            $(this).val('+');
        } else {
            fnum = parseInt(fnum, 10);
            if (fnum > 0) {
                $(this).val('+' + fnum);
            } else if (fnum <= 0) {
                $(this).val(fnum);
            } else {
                $(this).val('');
            }
        }
    });
</script>
