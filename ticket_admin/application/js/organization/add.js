/**
 * Created by yuanwei on 13-12-24.
 */
$(document).ready(function(){

//点击缩略图查看大图事件
    $('.thumbs').touchTouch();

// 表单验证
    $('#scenic_add_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })
    
    $('#btn-add').click(function(){

        var obj = $('#scenic_add_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=organization&a=save', obj.serialize(),function(data){

                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存用户失败!'+data.errors.msg+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>保存成功!</strong> 2 秒后跳转到机构列表页..</div>';
                    $('#show_msg').html(succss_msg);
                    setTimeout("location.href='organization_lists.html'", 3000);
                }
            },"json");

        };
        return false;
    })


//注册供应商
    $('#register_form').validationEngine({
        addFailureCssClassToField: 'error',
        showPrompts: true
    })
    
    $('#btn-register').click(function(){
    	var tmp = $('input[name=type]').val();
        var obj = $('#register_form');
        if(obj.validationEngine('validate')== true){
            $.post('index.php?c=organization&a=registerOrganzation', obj.serialize(),function(data){
                if(typeof data.errors != 'undefined'){
                    var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>保存机构失败!'+data.errors+'</div>';
                    $('#show_msg').html(warn_msg);
                }else{
                    var succss_msg = '<div class="alert alert-success"><strong>保存成功!</strong> 2 秒后跳转到机构列表页..</div>';
                    $('#show_msg').html(succss_msg);
                    if(tmp == 'agency'){
                    	setTimeout("location.href='organization_agency.html'", 2000);
                    }else{
                    	setTimeout("location.href='organization_supply.html'", 2000);
                    }
                    
                }
            },"json");

        }
        return false;
    })    


//机构类别的点击事件
    $('.agency-type input').click(function(){
        var i = $('.agency-type input').index(this);
        if(i==0){
            $('.agencies').hide();
            $('#scenics').show();
            $('.scenics').show();
            $('.agencies-input').iCheck('uncheck');
            $('.agencies-input').iCheck('disable');
        }else{
            $('.agencies').show();
            $('#scenics').hide();
            $('.scenics').hide();
            $('.agencies-input').iCheck('enable');
            $('.agencies-input').iCheck('check');
        }
    })









    var bankObj = {}

//添加银行
    $('#bank-show-btn').click(function(){
        bankObj.id = ''
        bankObj.type = 'bank'
        $('#bank-show').modal('show')
        $('#bank').val('')
        $('#bank-name').val('')
        $('#bank-num').val('')
        return false
    })

//编辑银行帐号
    $(document).on('click','.banks .icon-edit',function(){
        bankObj.id = $(this).parents('.banks')
        bankObj.type = 'bank'
        var bank = {
            type: '',
            bank: bankObj.id.find('.bank').text(),
            name: bankObj.id.find('.name').text(),
            num: bankObj.id.find('.num').text()
        }
        $('#bank-show').modal('show')
        $('#bank').val(bank.bank)
        $('#bank-name').val(bank.name)
        $('#bank-num').val(bank.num)
        return false
    })

//删除银行帐号
    $(document).on('click','.banks .icon-trash',function(){
        if(confirm('确认删除吗?')){
            //........
            location.reload()
        }
        return false
    })
var count = 1;
//确认添加银行
    $('.modal-footer button').click(function(){
        if(bankObj.type=='bank'){
            if(bankObj.id==''){
                var html = '<div class="span6 alert banks" type="0">' +
                    '<span class="span4"><input type="radio" name="bankrow" class="icheck" value="'+count+'">' +
                    ' <span class="bank">'+$('#bank').val()+'</span>' +
                    '<input class="bankname" type="hidden" name="row['+count+'][bank_name]" value="'+$('#bank').val()+'">' +
                    '</span>' +

                    '<span class="span3">户名：<span class="name">'+$('#bank-name').val()+'</span>' +
                    '<input class="account_name" type="hidden" name="row['+count+'][account_name]" value="'+$('#bank-name').val()+'">'+
                    '</span>' +

                    '<span class="span4">账号：<span class="num">'+$('#bank-num').val()+'</span></span>' +
                    '<input type="hidden" class="account" name="row['+count+'][account]" value="'+$('#bank-num').val()+'">'+
                    '<input type="hidden" name="row['+count+'][type]" value="bank">'+
                    '<span class="span1"><a title="编辑" href="javascript:void(0)"><i class="icon-edit"></i></a>' +
                    '<a title="删除" href="javascript:void(0)"><i class="icon-trash"></i></a></span></div>'
                $('#banks').append(html)
            }else{
                bankObj.id.find('.bank').text($('#bank').val())
                bankObj.id.find('.bankname').val($('#bank').val());

                bankObj.id.find('.name').text($('#bank-name').val())
                bankObj.id.find('.account_name').val($('#bank-name').val());

                bankObj.id.find('.num').text($('#bank-num').val())
                bankObj.id.find('.account').val($('#bank-num').val())
                bankObj.id=''
            }
        }else{
            if(bankObj.id==''){
                var html = '<div class="span6 alert alipay" type="0">' +
                    '<span class="span4"><input type="radio" class="icheck" name="bankrow" value="'+count+'">' +
                    ' <span class="bank">支付宝</span></span>' +
                    '<input class="bankname" type="hidden" name="row['+count+'][bank_name]" value="支付宝">' +
                    '<span class="span3">户名：<span class="name">'+$('#alipay-name').val()+'</span></span>' +
                    '<input class="account_name" type="hidden" name="row['+count+'][account_name]" value="'+$('#bank-name').val()+'">'+
                    '<span class="span4">账号：<span class="num">'+$('#alipay-num').val()+'</span></span>' +
                    '<input type="hidden" class="account" name="row['+count+'][account]" value="'+$('#bank-num').val()+'">'+
                    '<input type="hidden" name="row['+count+'][type]" value="alipay">'+
                    '<span class="span1"><a title="编辑" href="javascript:void(0)"><i class="icon-edit"></i></a>' +
                    '<a title="删除" href="javascript:void(0)"><i class="icon-trash"></i></a></span></div>'
                $('#banks').append(html)
            }else{
                bankObj.id.find('.name').text($('#alipay-name').val())
                bankObj.id.find('.num').text($('#alipay-num').val())
                bankObj.id=''
            }
        }
        $('.modal').modal('hide')
        $('#banks input').iCheck({radioClass:'iradio_flat-aero'})
        count++
        return false
    })






//添加支付宝
    $('#alipay-show-btn').click(function(){
        bankObj.type = 'alipay'
        bankObj.id = ''
        $('#alipay-show').modal('show')
        $('#alipay-name').val('')
        $('#alipay-num').val('')
        return false
    })

//编辑支付宝帐号
    $(document).on('click','.alipay .icon-edit',function(){
        bankObj.type = 'alipay'
        bankObj.id = $(this).parents('.alipay')
        var bank = {
            type: '',
            name: bankObj.id.find('.name').text(),
            num: bankObj.id.find('.num').text()
        }
        $('#alipay-show').modal('show')
        $('#alipay-name').val(bank.name)
        $('#alipay-num').val(bank.num)
        return false
    })

//删除支付宝帐号
    $(document).on('click','.alipay .icon-trash',function(){
        if(confirm('确认删除吗?')){
            //........
            location.reload()
        }
        return false
    })






//增加景区
    $('.add-scenic').click(function(){
        var obj = $('#scenics tbody tr:first-child').clone()
        $('#scenics tbody').append(obj)

        return false
    })

//删除景区
    $(document).on('click','.del-scenic',function(){

        if(confirm('确认删除吗?')){
            //........
            $(this).parents('tr').remove()
        }
        return false
    })
    $.get('index.php?c=ajax&a=getAreaChildByCode',{"code":0}, function(data){
        $('#province').html(data);
    });
    $.get('index.php?c=ajax&a=getAreaChildByCode',{'code':0},function(data){
        $("select[name='provice_poi[]']").html(data);
        $("#province").html(data);
    })

    //省切换
    $("select[name^='provice_poi']").live('change',function(){
        var code = $(this).val();
        $(this).parents('td').find("select[name^='city_poi']").html('<option value="__NULL__">县</option>');
        if(code == '__NULL__'){
            $(this).parents('td').find("select[name^='city_poi']").html('<option value="__NULL__">县</option>');

        }else{
            var seleter = $(this);
            $.get('index.php?c=ajax&a=getAreaChildByCode',{code:code, type:"city"}, function(data){
                seleter.prev().text(seleter.find("option:selected").text());
                seleter.parents('td').find("select[name^='city_poi']").html(data);

            });
        }
    })


//市切换
    $("select[name^='city_poi']").live('change',function(){
        var code = $(this).val();
        var seleter = $(this);
        $("select[name^='area_poi']").html('<option value="__NULL__">县</option>');
        if(code == '__NULL__'){
            $("select[name^='area_poi']").html('<option value="__NULL__">县</option>');
        }else{
            $("select[name^='area_poi']").html('<option value="__NULL__">县</option>');

            $.get('index.php?c=ajax&a=getAreaChildByCode',{code:code, type:"area"}, function(data){
                seleter.prev().text(seleter.find("option:selected").text());
                seleter.parents('td').find("select[name^='area_poi']").html(data);
            });
        }
    })
//区切换
    $("select[name^='area_poi']").live('change',function(){
        var seleter = $(this);
        seleter.prev().text(seleter.find("option:selected").text());
    })
//上传经营许可证
    $('#certificate_id').click(function() {
        $('#certificate-form1').ajaxSubmit({dataType: 'json',success: function(data){

            if(data.errors){
                var tmp_errors = '';
                $.each(data.errors, function(i, n){
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                $('#show_msg').html(warn_msg);
                location.href='#show_msg';
            }else if(data['data'][0]['id'] && data['data'][0]['url']){
                $('#a_certificate_id').attr('href', data['data'][0]['url']);
                $('#img_certificate_id').attr('src', data['data'][0]['url']);
                $("input[name='certificate_id']").val(data['data'][0]['id']);
            }
        }});
    });
    //上传营业执照
    $('#licence_id').click(function() {
        $('#licence-form1').ajaxSubmit({dataType: 'json',success: function(data){
            if(data.errors){
                var tmp_errors = '';
                $.each(data.errors, function(i, n){
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                $('#show_msg').html(warn_msg);
                location.href='#show_msg';
            }else if(data['data'][0]['id'] && data['data'][0]['url']){
                $('#a_licence_id').attr('href', data['data'][0]['url']);
                $('#img_licence_id').attr('src', data['data'][0]['url']);
                $("input[name='licence_id']").val(data['data'][0]['url']);
            }
        }});
    });

    //税务执照
    $('#tax_id').click(function() {
        $('#tax-form1').ajaxSubmit({dataType: 'json',success: function(data){

            if(data.errors){
                var tmp_errors = '';
                $.each(data.errors, function(i, n){
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                $('#show_msg').html(warn_msg);
                location.href='#show_msg';
            }else if(data['data'][0]['id'] && data['data'][0]['url']){
                $('#a_tax_id').attr('href', data['data'][0]['url']);
                $('#img_tax_id').attr('src', data['data'][0]['url']);
                $("input[name='tax_id']").val(data['data'][0]['id']);
            }
        }});
    });
    //企业logo
    $('#logo_id').click(function() {
        $('#logo-form1').ajaxSubmit({dataType: 'json',success: function(data){

            if(data.errors){
                var tmp_errors = '';
                $.each(data.errors, function(i, n){
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                $('#show_msg').html(warn_msg);
                location.href='#show_msg';
            }else if(data['data'][0]['id'] && data['data'][0]['url']){
                $('#a_logo_id').attr('href', data['data'][0]['url']);
                $('#img_logo_id').attr('src', data['data'][0]['url']);
                $("input[name='logo_id']").val(data['data'][0]['id']);
        }}});
    });

})
