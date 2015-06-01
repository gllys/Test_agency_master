<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">新增门票</h4>
        </div>
        <form class="form-horizontal form-bordered" method="post" action="#" id="form">
            <div class="modal-body">

                <div class="form-group" style="overflow: inherit;">
                    <label style="margin: 0px;" class="col-sm-2 control-label">门票名称:</label>
                    <div class="col-sm-10">
                        <input type="text" class="validate[required] form-control" tag="门票名称" name="name" maxlength="20" style="" placeholder="请输入门票名称" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="overflow: inherit;">
                    <label style="margin: 0px;" class="col-sm-2 control-label">景区名称</label>
                    <div class="col-sm-4">
                        <select data-placeholder="Choose One" style="width:300px;padding:0 10px; margin-left: -10px;"  id="distributor-select" name="scenic_id">
                            <option value=""  >请输入景区名称</option>                                     
                            <?php if (isset($lanLists) && !empty($lanLists)): foreach ($lanLists as $key => $item): ?>
                                    <option value="<?php echo $item['id']; ?>"  ><?php echo $item['name']; ?></option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                </div><!-- form-group -->

                <div class="form-group" style="overflow: inherit;">
                    <label class="col-sm-2 control-label">包含景点:</label>
                    <div class="col-sm-10" id="jingdianTag" data-prompt-position="topLeft">
                        <div class="form-group" style="margin:0;word-break: break-all;" id="appendto"></div>
                    </div>
                </div>
                <table class="table table-bordered mb30" >
                    <thead>
                        <tr>
                            <th>类型</th>
                            <th>价格</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody id="take-ticket">
                        <tr>
                            <td>
                                <div class="form-group" style="margin:0">
                                    <select class="select2 ticket_type"  data-prompt-position="bottomLeft" name="items[0][type]" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                                        <option value="">请选择门票类型</option>
                                        <?php
                                        $_lists = TicketType::model()->findAll();
                                        foreach ($_lists as $item):
                                            ?>
                                            <option value="<?php echo $item['id'] ?>" name="items[0][sale_price]"><?php echo $item['name'] ?></option>
                                            <?php
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td><input type="text"  name="items[0][sale_price]" value="0.00" class="onlyMoney"/></td>
                            <td><div class="btn btn-primary btn-xs" id="take-ticket-add">增加</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" id="form-button" class="btn btn-success">保存</button>
                <button  class="btn btn-default" aria-hidden="true" data-dismiss="modal" type="button">取消</button>
            </div>
        </form>
    </div>
</div>
<script>

    jQuery(document).ready(function() {
        var keyIndex = 1;
        $('#take-ticket-add').click(function() {
            if ($('.ticket_type').length > 4) {
                $(this).PWShowPrompt('门票类型最多创建五种'); 
                return false;
            }

            //添加新类型
            var tpl = '<tr><td><div class="form-group" style="margin:0"><select class="select2 ticket_type" name="items['+keyIndex+'][type]"  data-placeholder="Choose One" style="width:150px;padding:0 10px;">' +
                  '<option value="">请选择门票类型</option>' +
            <?php
            $_lists = TicketType::model()->findAll();
            foreach ($_lists as $item):
            ?>
                   '<option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>' +
            <?php endforeach;?>
            '</select></div></td><td><input type="text" value="0.00" name="items['+keyIndex+'][sale_price]" class="onlyMoney"/></td><td><div class="btn btn-danger btn-xs" >删除</div></td></tr>';
            var _select = $(tpl);
            keyIndex++;
            
            
            $("#take-ticket").append(_select);
            $("#take-ticket").find('select').each(function() {
                if ($(this).val() != '') {
                    _select.find('option[value=' + $(this).val() + ']').remove();
                }
            });

            $('.select2').last().select2({
                minimumResultsForSearch: -1
            });

        });

        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        $("#take-ticket").on('click', '.btn-danger', function() {
            var _this = this;
        	PWConfirm('确定要删除此门票吗？',function(){
	            $(_this).parents('tr').remove()
          	});
        })

        jQuery('#distributor-select').select2({
        });

        //得到子景点
        $('#distributor-select').change(function() {
            var names = $("#distributor-select").val();
            if (names == null || names == '') {
                $('#appendto').html('');
            } else {
                $.post('/ticket/goods/getPois', {'ids': names}, function(data) {
                    $('#appendto').html(data);
                }, 'json');
            }

        });

        //下拉改变值后不能再选
        var select_old;
        $(document).on('select2-open', '.ticket_type', function() {
            //存储select修改之前的值
            select_old = {};
            select_old[$(this).val()] = $(this).find("option:selected").text();
            //console.log(select_old);
        });

        $(document).on('change', '.ticket_type', function() {
            var op_val = $(this).val();
            if (op_val) { //禁止其它选项再选当前选项
                $(".ticket_type").not(this).find('option[value=' + op_val + ']').remove();
            }

            if (typeof select_old != 'undefind') {
                for (i in select_old) {
                    if (typeof i !== 'undefind' &&i!==''&& op_val !== i) {
                        $(".ticket_type").not(this).each(function() {
                            var len = $(this).find('option').length - 1;
                            $(this).find('option').each(function(index) {
                                if ($(this).attr('value') != '' && $(this).attr('value') > i) {
                                    $(this).before('<option value="' + i + '">' + select_old[i] + '</option>');
                                    return false;
                                }
                                if (len === index) {
                                    $(this).parents(".ticket_type").append('<option value="' + i + '">' + select_old[i] + '</option>');
                                }
                            });
                        });
                    }
                }
            }
        });
        
        //提示设置
         $('#form').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1,
            showOneMessage: true
        });
        //提交表单
        $('#form-button').click(function() { 
            var obj = $('#form');
            if (obj.validationEngine('validate') == true) {
                
                if($('#form [name=scenic_id]').val()==''){
                 $('#form [name=scenic_id]').PWShowPrompt('请选择景区'); 
                 return false;
                }
            
                if($('#form .view_point:checked').length<1){
                     $('#jingdianTag').PWShowPrompt('请至少选择一个景点'); 
                     return false;
                }
            
                var _flag = false;
                $('select.ticket_type').each(function(){
                     if($(this).val()===''){
                          $(this).parent().PWShowPrompt('请选择门票类型'); 
                          $(this).select2("open");
                          _flag = true;
                          return false;
                      }
                });
                if(_flag){
                    return false;
                }
                
                $('#form-button').attr('disabled', true);
                $.post('/ticket/goods/add', obj.serialize(), function(data) {
                        if (data.error) {
                            alert(data.msg);
                            $('#form-button').attr('disabled', false);
                        } else {
                            alert('新增门票成功',function(){window.location.partReload();});
                        }
                    }, 'json');
                }
            return false;
        });
    });
</script>

