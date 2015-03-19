<?php
$this->breadcrumbs = array('景区管理', '景点管理');
?>
<div class="contentpanel">
    <div id="verify_return"></div>
    <ul style="margin-bottom:-1px;position:relative;z-index:1;" class="nav nav-tabs">
        <li><a href="/scenic/scenic/view/?id=<?php echo $_GET['id'] ?>"><strong>图文全景</strong></a></li>
        <li class="active"><a href="/scenic/Spot/?id=<?php echo $_GET['id'] ?>"><strong>景点管理</strong></a></li>
    </ul>

  <input type="hidden" name="landscape_id" value="<?php echo $_GET['id'];?>" id="landscape_id">
    <div class="tab-content">
        <div id="t1" class="panel panel-default tab-pane active">
            <div style="background-color:#fff" class="panel-heading">
                <h4 class="panel-title"><button data-toggle="modal" data-target=".bs-example-modal-static" onclick="modal_jump_add();" class="btn btn-success">新增景点</button></h4>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb30" style="word-break:break-all;">
                    <thead>
                        <tr>
                            <th style="width:30%">景点名称</th>
                            <th style="width:60%">景点介绍</th>
                            <th style="width:10%">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        foreach ($lists as $item):
                            ?>
                            <tr>
                                <td><?php
                                    echo $item['name'];
                                    //if ($item['status'])
                                    //    echo "<span class='btn btn-success btn-bordered btn-xs'>已上架</span>";
                                    //else
                                    //    echo "<span class='btn btn-danger btn-bordered btn-xs'>已下架</span>";
                                    ?></td>
                                <td  style="word-break: break-all; word-wrap:break-word;"><?php echo $item['description']; ?></td>
                                <td> 
                                    <?php
                                    if ($item['status'] == 1) {
                                        echo " <a onclick='downUp(".$item['id'].",1);'><i class='glyphicon glyphicon-arrow-down'  style='cursor:pointer'></i></a>";
                                    } else {
                                        echo "<a onclick='downUp(".$item['id'].",0)'><i class='glyphicon glyphicon-arrow-up'  style='cursor:pointer'></i></a>";
                                    }
                                    ?>
                                   
                                <a data-toggle="modal" data-target=".bs-example-modal-static" onclick="modal_jump_edit(<?php echo $item['id'];?>);" ><i class="glyphicon glyphicon-edit"></i></a>
                                </td>
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
                    $this->widget('common.widgets.pagers.ULinkPager', array(
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
</div>


<div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static">

</div>

<script type="text/javascript">
    function modal_jump_add(){
        $('#verify-modal').html();
        $.get('/scenic/Spot/add/', function(data) {
            $('#verify-modal').html(data);
            
        });
    }
 
     function modal_jump_edit(id){
        $('#verify-modal').html();
        $.get('/scenic/Spot/edit/?id='+id+'&landscape_id='+$('#landscape_id').val()+'', function(data) {
            $('#verify-modal').html(data);
            
        });
    }

    function downUp(id,status){
       var id = id;
       var status = status;
        $.post('/scenic/Spot/DownUP/', {id: id, status: status,'landscape_id':$('#landscape_id').val()}, function(data) {         
            if (data.errors) {
                var tmp_errors = '';
                $.each(data.errors, function(i, n) {
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + tmp_errors + '</div>';
                $('#verify_return').html(warn_msg);
            } else{
                //alert(data.msg);
                var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>'+data.msg+'</strong></div>';
                $('#verify_return').html(succss_msg);
                setTimeout("location.href='/scenic/Spot/?id="+$('#landscape_id').val()+"'", '2000');
            }
        }, "json");
        return false;  
    }
</script>    
