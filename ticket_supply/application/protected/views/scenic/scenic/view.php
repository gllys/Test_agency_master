<?php
$this->breadcrumbs = array('景区管理', '景区详情');
?>
<div class="contentpanel">
    <ul style="margin-bottom:-1px;position:relative;z-index:1;" class="nav nav-tabs">
        <li class="active"><a href="/scenic/scenic/view/?id=<?php echo $_GET['id'] ?>"><strong>景区详情</strong></a></li>
        <!--li><a href="/scenic/Spot/?id=<?php echo $_GET['id'] ?>"><strong>景点管理</strong></a></li-->
        <li style="float:right;"><a href="/ticket/single/?jq=<?php echo $_GET['id']; ?>">门票管理</a></li>
       
    </ul>

    <div class="tab-content">
        <div id="t1" class="row tab-pane active">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <img style="max-width:100%; border: 0px; display: block; margin: 0px auto; height: 208px;" src="<?php echo !empty($data['images'])?$data['images'][0]['url']:'/img/default.jpg'; ?>">
                </div>
            </div>

            <div class="col-md-8">
                <div class="panel panel-default change1">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <?php echo isset($data['name'])?$data['name']:''; ?>
                           <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
			   <a href="#" style="color:#06C; margin-left:10px;" class="wirte">编辑</a>
			   <?php } ?>
                        </h4>
                        <p></p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb30">
                            <tbody>
                        <tr>
                            <th>景区级别</th>
                            <td id="td_landscape_level_name"><?php echo $data['landscape_level_name'] ?></td>
                        </tr>
                        <tr>
                            <th>所在地</th>
                            <td id="td_district_name"><?php echo isset($data['district_name']) ? $data['district_name'] : '';?></td>
                        </tr>
                        <tr>
                            <th>详细地址</th>
                            <td id="td_address"><?php echo $data['address']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:left; padding:15px 0 15px 10px;">
                                <div>景点说明</div>
                                <div style="text-indent:2em; margin-top:10px;" id="td_biography">
                                    <?php echo nl2br($data['biography']); ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="panel panel-default change2" style="display:none">
                <div class="panel-heading">
                    <h4 class="panel-title"><?php echo $data['name']; ?><a href="#" style="color:#06C; margin-left:10px;" class="save">保存</a></h4>
                    <p></p>
                </div>

                <div class="table-responsive th-width">
                    <table class="table table-bordered mb30">
                        <tbody>
                        <tr>
                            <th>景区级别</th>
                            <td><?php echo $data['landscape_level_name'] ?></td>
                            <td style="display: none;">
                                <select name="landscape_level" id="landscape_level_name" class="select2" data-placeholder="Choose One" style="width:150px;padding:0 10px;">
                                    <option value="">请选择景区基本</option>
                                    <?php
                                    $levels = array(
                                        0 => '非A景区',
                                        1 => 'A景区',
                                        2 => 'AA景区',
                                        3 => 'AAA景区',
                                        4 => 'AAAA景区',
                                        5 => 'AAAAA景区',
                                    );
                                    ?>
                                    <?php foreach($levels as $l => $n):?>
                                        <option <?php if($l== $data['landscape_level_id']):?> selected="selected" <?php endif;?> value="<?=$l ?>"  ><?=$n ?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>所在地</th>
                            <td><?php echo isset($data['district_name']) ? $data['district_name'] : '';?></td>
                            <td style="display: none">
                                <select class="select2 col-sm-4" data-placeholder="Choose One" id="province" name="province_id">
                                    <option value="__NULL__" selected="selected" >省</option>
                                    <?php
                                    $province = Districts::model()->findAllByAttributes(array("parent_id" => 0));
                                    foreach ($province as $model) {
                                        if ($model->id == 0) {
                                            continue;
                                        } echo " <option value='" . $model->id . "'>" . $model->name . "</option>";
                                    }
                                    ?>
                                </select>
                                <select class=" select2 col-sm-4" data-placeholder="Choose One" id="city" name="city_id">
                                    <option value="__NULL__" selected="selected" >市</option>
                                </select>
                                <select class=" select2 col-sm-4" data-placeholder="Choose One" id="area" name="district_id">
                                    <option value="__NULL__" selected="selected" >县</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>详细地址</th>
                            <td><?php echo $data['address']; ?></td>
                            <td style="display: none">
                                <input id="address" type="text" class="form-control" placeholder="" value="<?php echo $data['address']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:left; padding:15px 15px 15px 10px;">
                                <textarea id="biography" placeholder="" rows="5" class="form-control"><?php echo strip_tags(nl2br($data['biography'])); ?></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            </div><!-- col-md-6 -->
        </div>
    </div>
  
    
    
    <br/>
    <div><h5><b>票种管理</b></h5></div>  
   <div id="verify_return"></div>
   <input type="hidden" name="landscape_id" value="<?php echo $_GET['id'];?>" id="landscape_id">    
    <div class="tab-content">
        <div id="t1" class="panel panel-default tab-pane active">
            <div style="background-color:#fff" class="panel-heading">
                <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
                <h4 class="panel-title">
                    <a href="/scenic/scenic/ticket?scenic_id=<?php echo $_GET['id'];?>" class="btn btn-success">新增票种</a>
                </h4>
                <?php } ?>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb30" style="word-break:break-all;">
                    <thead>
                        <tr>
                            <th style="width:30%">门票名称</th>
                            <th style="width:60%">包含景点</th>
                            <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
                            <th style="width:10%">操作</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        foreach ($data['ticket'] as $item):
                            ?>
                            <tr>
                                <td><?php echo $item['name'];?></td>
                                <td>
                                    <?php
                                    $result = Poi::api()->lists(array("ids" => $item['view_point']));
                                    $poiInfo = ApiModel::getLists($result);
                                    foreach ($poiInfo as $value) {  
                                            echo $value['name'];
                                            if ( next($poiInfo) !== false){ echo '、';}
                                    }
                                    ?>
                                </td>
                                <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
                                <td> 
                                    <a href="/scenic/scenic/ticketedit?id=<?php echo $item['id'];?>" title="编辑"><i class="glyphicon glyphicon-edit"></i></a>
                                </td>
                                <?php }?>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    $this->widget('CLinkPager', array(
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
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
    
    
    
    
    
    
    

    <br/>
    <div><h5><b>景点管理</b></h5></div>  
   <div id="verify_return"></div>
  <input type="hidden" name="landscape_id" value="<?php echo $_GET['id'];?>" id="landscape_id">
    <div class="tab-content">
        <div id="t1" class="panel panel-default tab-pane active">
            <div style="background-color:#fff" class="panel-heading">
                <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
                <h4 class="panel-title"><button data-toggle="modal" data-target=".bs-example-modal-static" onclick="modal_jump_add();" class="btn btn-success">新增景点</button></h4>
                <?php } ?>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb30" style="word-break:break-all;">
                    <thead>
                        <tr>
                            <th style="width:30%">景点名称</th>
                            <th style="width:60%">景点介绍</th>
                            <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
                            <th style="width:10%">操作</th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        foreach ($lists as $item):
                            ?>
                            <tr>
                                <td><?php echo $item['name'];?></td>
                                <td  style="word-break: break-all; word-wrap:break-word;"><?php echo $item['description']; ?></td>
                                <?php if($data['organization_id'] == Yii::app()->user->org_id) {?>
                                <td> 
                                    <?php
                                    if ($item['status'] == 1) {
                                        echo " <a onclick='downUp(".$item['id'].",1);' title='下架'><i class='glyphicon glyphicon-arrow-down'  style='cursor:pointer'></i></a>";
                                    } else {
                                        echo "<a onclick='downUp(".$item['id'].",0)' title='上架'><i class='glyphicon glyphicon-arrow-up'  style='cursor:pointer'></i></a>";
                                    }
                                    ?>
                                <a data-toggle="modal" data-target=".bs-example-modal-static" title="编辑" onclick="modal_jump_edit(<?php echo $item['id'];?>);" ><i class="glyphicon glyphicon-edit"></i></a>
                                </td>
                                <?php }?>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    $this->widget('CLinkPager', array(
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
                    ?>
                </div>
            </div>
        </div>
    </div>
<div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static"></div>
</div>
</div>

<script type="text/javascript">
    function modal_jump_add(){
        $('#verify-modal').html();
        $.get('/scenic/scenic/add/', function(data) {
            $('#verify-modal').html(data);
            
        });
    }
 
     function modal_jump_edit(id){
        $('#verify-modal').html();
        $.get('/scenic/scenic/edit/?id='+id+'&landscape_id='+$('#landscape_id').val()+'', function(data) {
            $('#verify-modal').html(data);
            
        });
    }

    function downUp(id,status){
       var id = id;
       var status = status;
        $.post('/scenic/scenic/DownUP/', {id: id, status: status,'landscape_id':$('#landscape_id').val()}, function(data) {         
            if (data.errors) {
                var tmp_errors = '';
                $.each(data.errors, function(i, n) {
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                $('#verify_return').html(warn_msg);
            } else{
                //alert(data.msg);
                var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功</strong></div>';
                $('#verify_return').html(succss_msg);
                setTimeout("location.href='/scenic/scenic/view?id="+$('#landscape_id').val()+"'", '2000');
            }
        }, "json");
        return false;  
    }
    
     jQuery(document).ready(function() {

        $('#all-btn').click(function(){
            var obj = $(this).parents('table')
            if($(this).is(':checked')){
                obj.find('input').prop('checked', true)
                $(this).text('反选')
            }else{
                obj.find('input').prop('checked', false)
                $(this).text('全选')
            }
        })


        $('.wirte').click(function(){
            $('.change1').hide();
            $('.change2').show();
            $('.show-pic').hide();
            $('.pics').show();
        })

        $('.save').click(function(){
            $('.change1').show();
            $('.change2').hide();
            $('.show-pic').show();
            $('.pics').hide();
            saveData();
        })


        function saveData()
        {
           // var landscape_level_id =$('#landscape_level_name').val();
           // var province =$('#province').val();
          //  var city =$('#city').val();
          //  var area =$('#area').val();
          //  var address =$('#address').val();
            var biography = $('#biography').val();
            var business_license_input = $('#business_license_input').val();
            $.post('/scenic/scenic/editinfo',{
               // landscape_level_id:landscape_level_id,
               // province:province,
              //  city:city,
              //  area:area,
              //  address:address,
                id:<?php echo $_GET['id'];?>,
                biography:biography,
              //  thumbnail_id:0,
              //  business_license_input:business_license_input

            }, function(result){
                 location.reload();
            }, 'json');

         //   $('#td_landscape_level_name').html($('#landscape_level_name option:selected').text());
          //  $('#td_district_name').html($('#province option:selected').text() + $('#city option:selected').text() + $('#area option:selected').text());
            //详细地址
          //  $('#td_address').html($('#address').val());
           // $('#td_biography').html($('#biography').val());
        }

//省联动
        $('#province').change(function() {
            var code = $(this).val();
            $('#city').html('<option value="__NULL__" selected>市</option>');
            $('#area').html('<option value="__NULL__" selected>县</option>');
            if (code == '__NULL__') {
                $('#city').html('<option value="__NULL__" selected>市</option>');
            } else {
                $('#city').html('<option value="__NULL__" selected>市</option>');
                var html = new Array();
                $.get('/ajaxServer/GetChildern/id/' + code, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }

                    $('#city').append(html.join(''));
                    $('#city,#area').select2();
                }, 'json');
            }
            return false;
        });


//市切换
        $('#city').change(function() {
            var code = $(this).val();
            if (code == '__NULL__') {
                $('#area').html('<option value="__NULL__" selected>县</option>');
            } else {
                $('#area').html('<option value="__NULL__" selected>县</option>');
                var html = new Array();
                $.get('/ajaxServer/GetChildern/id/' + code, function(data) {
                    for (i in data) {
                        html.push("<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>");
                    }
                    $('#area').append(html.join(''));
                    $('#area').select2();
                }, 'json');
            }
            return false;
        });

$('#a_send_msg').click(function(argument) {
    var poi_id = $(this).attr('poi_id');
    var name = $('#td_name_' + poi_id).html();

    $.post('/settings/jingqu/sendmsg',
    {
        id:poi_id,
        name:name
    },function(result){
        if (result.status=='error') {
            alert('出错！请刷新页面重试！');
            return false;
        }
        alert('发送成功！');
        $('#delpoi_div').modal('hide');
         location.href= location.href;
    }, 'json');
});
        $('#newedit').on('show.bs.modal',function(e){
            var action = $(e.relatedTarget).attr('action');
            $('#btn_new_edit').attr('action', action);
            if(action=='new')
            {
                return;
            }
            //
            var id = $(e.relatedTarget).attr('id');
            $('#btn_new_edit').attr('edit_id', id);
            var name = $('#td_name_' + id).html();
            var desc = $('#td_desc_' + id).html();
            $('#jindian_name').val(name);
            $('#jindian_description').val(desc);
        });


        $('#btn_new_edit').click(function(){
            var action = $(this).attr('action');
            var id=$(this).attr('edit_id');;
            savenewjingqu(action, id);

        });

       
        function savenewjingqu(action, id)
        {
            var name = $.trim($('#jindian_name').val());
            var description  = $.trim($('#jindian_description').val());
            if(name == '')
            {
                alert('景点名称不能为空！')
                return;
            }

            if(description == '')
            {
                alert('景点描述不能为空！')
                return;
            }

            $.post('/settings/jingqu/jingdianedit',
                {
                    action:action,
                    id:id,
                    name:name,
                    description:description
                },function(result){
                    if (result.status=='error') {
                        alert('出错！请刷新页面重试！');
                        return false;
                    }
                    $('#newedit').modal('hide')
                    location.href= location.href;
                }, 'json');
        }

    });
</script>    

<!-- 图片上传-->
<script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
<script type="text/javascript" src="/js/jquery.nailthumb.1.1.js"></script>
<script type="text/javascript">
    //上传
    window.imgField = '';
    new AjaxUpload('#business_license', {
        action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
        name: 'file',
        onSubmit: function(file, ext) {
            //上传文件格式限制
            if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                alert('上传格式不正确');
                return false;
            }
            this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
            window.imgField = 'business_license';
        },
        onComplete: function(file, data) {
        }
    });

    window.upload_callback = function(data) {
        if (data.status != 200) {
            alert('上传失败！');
            return false;
        }
        $('input[name=' + window.imgField + ']').val(data.msg);
        $('#' + window.imgField + '_img').attr('src', data.msg);
    }

    $('#putform').click(function() {
        if ($('#form-data-supply').validationEngine('validate') == true) {
            $.post('/system/organization/saveSupply', $('#form-data-supply').serialize(), function(data) {
                if (data.errors) {
                    var tmp_errors = data.errors;
                    alert(tmp_errors);
                } else if (data.succ) {
                    alert('保存成功');
                    window.location.reload();
                }
            }, "json")
        }

        return false;
    })


</script>