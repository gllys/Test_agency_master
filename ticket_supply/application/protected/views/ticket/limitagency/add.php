<?php
$this->breadcrumbs = array('产品管理', '添加限制清单');
$action = isset($action)?$action:$this->getAction()->getId();
?>
<div class="contentpanel">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $action=="add"?"添加":($action=="edit"?"编辑":""); ?>限制清单</h4>
                </div><!-- panel-heading -->

                <form class="form-horizontal form-bordered" action="/ticket/limitagency/save" id="pwd" method="post">
                    <input type="hidden" name="id" value="<?php echo !empty($info)?$info['id']:""; ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <div class="panel-body nopadding">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 名称</label>
                            <div class="col-sm-4">
                                <input type="text" value="<?php echo !empty($info)?$info['name']:""; ?>" placeholder="请输入清单名称" name="name" class="form-control" data-validation-engine="validate[required]">
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 说明</label>
                            <div class="col-sm-4">
                                <input type="text" name="note" value="<?php echo !empty($info)?$info['note']:""; ?>" placeholder="请输入清单说明" class="form-control" data-validation-engine="validate[required]" />
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="text-danger">*</span> 类型</label>
                            <div class="col-sm-4">
                                <select class="select2" name="type" id="type-select" data-placeholder="请选择清单类型" style="width:200px;padding:0 5px;" data-validation-engine="validate[required]">
                                    <option value="">类型</option>
                                    <option value="0" <?php echo !empty($info)?($info['type']==0?"selected":""):""; ?>>白名单</option>
                                    <option value="1" <?php echo !empty($info)?($info['type']==1?"selected":""):""; ?>>黑名单</option>
                                </select>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label">地区:</label>
                            <div class="col-sm-10">
                                <select class="select2" data-placeholder="" style="width:180px;padding:0 5px;"
                                        id="province-select">
                                    <option value="__NULL__">省</option>
                                    <?php if ($province): ?>
                                        <?php foreach ($province as $value): ?>
                                            <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <select class="select2" data-placeholder="" style="width:180px;padding:0 5px;"
                                        id="area-select">
                                    <option value="__NULL__">市</option>
                                </select>
                                <button type="button" class="btn btn-success btn-xs" id="area-add-btn">添加</button>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label">分销商:</label>
                            <div class="col-sm-10">
                                <input type="hidden" class="bigdrop" id="distributor-select" style="width:350px;padding:0 5px;"/>
                                </select>
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-5">
                                    <select name="" id="select1" style="width:100%;height:300px" class="form-control inline" multiple>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <button id="select-add" style="margin-bottom:20px;" class="btn btn-white btn-block" type="button"><i class="fa fa-angle-right"></i></button>
                                    <button id="select-remove" style="margin-bottom:20px;" class="btn btn-white btn-block" type="button"><i class="fa fa-angle-left"></i></button>
                                    <button id="select-addall" style="margin-bottom:20px;" class="btn btn-white btn-block" type="button"><i class="fa fa-angle-double-right"></i></button>
                                    <button id="select-removeall" class="btn btn-white btn-block" type="button"><i class="fa fa-angle-double-left"></i></button>
                                </div>
                                <div class="col-sm-5">
                                    <select name="agency_ids[]" id="select2" style="width:100%;height:300px" class="form-control inline" multiple="multiple">
                                        <?php if(isset($info['agency_ids'])):$agencyIds = explode(",",$info['agency_ids']); ?>
                                            <?php foreach($agencyIds as $agency_id): ?>
                                                <?php $res = Organizations::api()->show(array('id'=>$agency_id)); //todo optimize?>
                                                <?php if($res['code']!='succ'||empty($res['body']))continue; ?>
                                                <option value="<?php echo $agency_id; ?>"><?php echo $res['body']['name']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                        </div><!-- form-group -->


                    </div><!-- panel-body -->
                    <div class="panel-footer">
                        <input type="button" class="btn btn-primary mr5" id="save-limit-btn" value="保存">
                        <input type="button" class="btn btn-default" id="clear-limit-btn" value="取消">
                    </div>
                </form>

            </div><!-- panel -->

        </div><!-- col-md-6 -->
    </div><!-- row -->
</div><!-- contentpanel -->
<script>
    jQuery(document).ready(function() {
        !function(){
            $('#province-select').change(function () {
                var code = $(this).val();
                $('#s2id_area-select').find(".select2-chosen").text("市");
                $('#area-select').html('<option value="__NULL__">市</option>');
                if (code != '__NULL__') {
                    var html = new Array();
                    $.get('/ajaxServer/getChildern/id/' + code, function (data) {
                        for (i in data) {
                            html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                        }
                        $('#area-select').append(html.join(''));
                    }, 'json');
                }
                return false;
            });

            $('#area-add-btn').click(function(){
                var province_id = $('#province-select').val();
                if (province_id == "__NULL__") {
                    alert("请选择地区!");
                    return false;
                }
                var cid = $('#area-select').val();

                $.post('/ajaxServer/getAgency/', {pid: province_id, cid: cid}, function (data) {
                    if(data.error){
                        alert(data.error);return false;
                    }
                    if (data.results.length <= 0) {
                        alert("选择的地区下暂无分销商!");
                        return false;
                    }
                    for (i in data.results) {
                        var item = data.results[i];
                        var len1 = $('#select1').find('option[value="'+item.id+'"]').length;
                        var len2 = $('#select2').find('option[value="'+item.id+'"]').length;
                        if(len1 <= 0 && len2 <= 0){
                            $('#select1').append('<option value="'+item.id+'">'+item.text+'</option>')
                        }
                    }
                },'json');
            })

            $("#distributor-select").select2({
                placeholder: "请输入例外的分销商",
                minimumInputLength: 1,
                ajax: {
                    url: "/ajaxServer/agency",
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            term: term,
                            page_limit: 20
                        };
                    },
                    results: function (data, page) {
                        return {results: data.results};
                    },
                    type: 'post'
                },
                formatSelection: function (item) {
                    var id = item.id;
                    var name = '请输入例外的分销商';
                    if (id != 0 && id != undefined) {
                        name = item.text;
                        //将该地区下未在列表的分销商添加到列表
                        var len1 = $('#select1').find('option[value="'+id+'"]').length;
                        var len2 = $('#select2').find('option[value="'+id+'"]').length;
                        if(len1 <= 0 && len2 <= 0){
                            $('#select1').append('<option value="'+id+'">'+name+'</option>')
                        }
                    }
                    return name;
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });


            $('#select-add').click(function(){
                var options = $('#select1 option:selected')
                var remove = options.remove()
                remove.appendTo('#select2')
            })

            $('#select-remove').click(function(){
                var removeOptions = $('#select2 option:selected')
                removeOptions.appendTo('#select1')
            })

            $('#select-addall').click(function(){
                var options = $('#select1 option')
                options.appendTo('#select2')
            })

            $('#select-removeall').click(function(){
                var options = $('#select2 option')
                options.appendTo('#select1')
            })

            $('#select1').dblclick(function(){
                var options = $('option:selected', this)
                options.appendTo('#select2')
            })

            $('#select2').dblclick(function(){
                $('#select2 option:selected').appendTo('#select1')
            })

            $('#pwd').validationEngine({
                autoHidePrompt: true,
                scroll: false,
                autoHideDelay: 3000,
                maxErrorsPerField: 1
            })

            $('#save-limit-btn').click(function(){
                $('#select2 option').attr("selected",true);
                if($('#pwd').validationEngine('validate') === true){
                    $.post('/ticket/limitagency/save',$('#pwd').serialize(),function(data){
                        if(data.error==0){
                            alert("保存成功！",function(){location.href = '/#'+ "/ticket/limitagency";});
                        }else{
                            alert("保存失败,"+data.msg);
                        }
                    },'json');
                }
            });

            $("#clear-limit-btn").click(function(){
//                $('#s2id_type-select').find(".select2-chosen").text("类型");
//                $('#s2id_province-select').find(".select2-chosen").text("省");
//                $('#s2id_area-select').find(".select2-chosen").text("市");
//                $('#s2id_distributor-select').find(".select2-chosen").text("请输入分销商名称");
//                $('#select1 option,#select2 option').remove();
//                $('input[name="note"],input[name="name"]').val("");
                location.href = '/#'+ "/ticket/limitagency";
            });

        }()

        // Select2
        jQuery(".select").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

    });
</script>
