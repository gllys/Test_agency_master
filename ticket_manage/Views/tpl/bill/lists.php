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

            <style type="text/css">
                .label-green{
                    cursor:pointer
                }
                .pop{
                    position:relative;
                    display:inline-block;
                }
                .pop-content{
                    display:none;
                    width:300px;
                    position:absolute;
                    top:20px;
                    left:0;
                    z-index:1;
                    background-color:#fff;
                    padding:10px;
                    border-radius:5px;
                    border:1px solid #ccc;
                    box-shadow:0 5px 10px rgba(0,0,0,.1)
                }
                .pop:hover .pop-content{
                    display:block;
                    text-align:left;
                }
                .pop-content ul{
                    margin:0;
                    list-style:none
                }
                .pop-content li{
                    padding:0;
                    line-height:2
                }
                div.selector{
                    margin-right:10px;
                    width:100px;
                }
                .btn-green{
                    min-width:inherit
                }
                .table-normal tbody td{
                    text-align:center
                }
                .table-normal tbody td a{
                    text-decoration:none
                }
                .table-normal tbody td i{
                    margin-left:10px
                }

                .dropdown-menu{
                    min-width:90px
                }
                .popover{
                    width:125px
                }
                .popover-content .btn-default{
                    min-width:inherit;
                    margin-left:10px;
                }
                .popover-content button i{
                    margin:0!important
                }
                .btn-primary{
                    background:#428BCA;
                    border-color:#357EBD;
                    color: #FFF;
                }
                .btn-group:hover .dropdown-menu{
                    display:block;
                    top:27px;
                }

            </style>
            <div class="container-fluid padded" style="padding-bottom: 0px;">
                <div class="box">
                    <div class="box-header">
                        <span class="title">取现管理</span>
                    </div>
                    <div class="box-content padded">
                        <form class="fill-up separate-sections" method="post" action="#">
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">提交日期：</div>
                                <div class="span2">
                                    <input type="text" placeholder="" name="updated_at" class="form-time" />
                                </div>

                                <div class="span1">审核状态：</div>
                                <div class="span2">
                                    <select class="uniform" name="state">
                                        <option value="">全部</option>
                                        <option value="1">已取现</option>
                                        <option value="0">未取现</option>
										<option value="2">未取消</option>
										<option value="3">已拒绝</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-fluid" style="height: 30px;">
                                <div class="span1">机构编号：</div>
                                <div class="span2">
                                    <input type="text" name="organization_id" placeholder="机构编号" value="">
                                </div>
                                
                                <div class="span1">机构名称：</div>
                                <div class="span2">
                                    <input type="text" name="organization_name" placeholder="请输入机构名称">
                                </div>
                                
                         

                                <div class="span3">
                                    <button class="btn btn-default" id="searchBtn" type="submit">搜索</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="container-fluid padded">
                <div class="box">
                    <div class="box-header">
                        <span class="title">取现列表</span>
                    </div>
                    <div class="box-content">
                        <table class="table table-normal">
                            <thead>
                                <tr>
                                    <td>编号</td>
                                    <td>机构名称</td>
                                    <td>机构编号</td>
                                    <td>取现金额</td>
                                    <td>提交时间</td>
                                    <td>取现类型</td>
                                    <td>取现银行</td>
                                    <td>取现账号</td>
								    <td>账号名</td>
									<td>取现状态</td>
                                    <td style="width: 30px">操作</td>
                                </tr>
                            </thead>
                              <?php if ($list): ?>
							   <?php foreach ($list as $value): ?>
                            <tbody>
                              
                                   <tr>
                                       <td><?php echo $value["id"];?></td>
									   <td><?php echo $value["name"];?></td>
									   <td><?php echo $value["organization_id"];?></td>
									   <td width="100px;"><?php echo $value["num"];?></td>
									   <td width="170px;"><?php echo $value["created_at"];?></td>
									   <td width="100px;"><?php if($value["cash_type"] == 1){ echo "24小时取现";} else{ echo "5个工作日";};?></td>
									    <td><?php echo $value["bank_name"];?></td>
										  <td><?php echo $value["account"];?></td>
										  <td><?php echo $value["account_name"];?></td>
										  <td><?php if($value["state"]==1){ echo "已取现";}
                                                    if($value["state"]==2){ echo "已取消";}
													if($value["state"]==0){ echo "未取现";}
                                                    if($value["state"]==3){ echo "已拒绝";}

                                                     ?></td>
										   <td><?php if($value["state"] ==0) {?>
										         <a class="verifyOrg info" data-info-id="<?php echo $value["id"];?>" data-original-title="取现" href="javascript:" title="">
																	<button class="btn btn-blue btn-primary"  data-info-id="<?php echo $value["id"];?>">取现</button>
												 </a>
										   <?php }?>
										   </td>
                                       
                                  
                             </tr>
                            </tbody>
  <?php endforeach; ?>
							   <?php endif; ?>
                        </table>
                    </div>
                </div>

                <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
        <div id="verify-modal-big" class="modal hide fade" style="width:800px;margin-left: -400px;"><!-- 大弹出层 --></div>
        <div id="verify-modal" class="modal hide fade"><!-- 弹出层 --></div>
        
        <link href="Views/css/daterangepicker.css" rel="stylesheet">
        <script src="Views/js/vendor/date.js"></script>
        <script src="Views/js/vendor/moment.js"></script>
        <script src="Views/js/vendor/daterangepicker.js"></script>
        <script src="Views/js/common/common.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
        $(function(){
            /***表单公用开始**/
            //表单提交数据后自动赋值
            <?php
            $post = PI::$data['post'] ;
            foreach($post as $key=>$val){
            ?>
                $('select[name=<?php echo $key ?>],input[name=<?php echo $key ?>]').val('<?php echo $val ?>');
            <?php } ?>
            //分页搜索代码跳转共用代码
            $('#searchBtn,a.paginate_button').click(function() {
                    $("form").attr('action', this.href).submit();
                    return false;
             });
             
             //解决select js赋值如果不改变BUG
              $('form select').each(function(){
                  $(this).prev().text($(this).find("option:selected").text());
              });
              
             $('.form-time').daterangepicker({
                    format:'YYYY-MM-DD'
              });
              /***表单公用结束**/
             //alert poi info
        });     
            //景区审核
             function modal_jump_check(id)
            {
                $('#verify-modal-big').html();
                $.get('index.php?c=landscape&a=getModalJumpCheck&id='+id,function(data){
                $('#verify-modal-big').html(data);
                });
            }
            
            //查看子景区
            function modal_jump_child(id){
                $('#verify-modal').html('');
                $.get('index.php?c=landscape&a=childLists&id='+id,function(data){
                $('#verify-modal').html(data);
                });
            }

   $('.verifyOrg').popover({'placement':'bottom','html':true}).click(function(){
        var info_id = $(this).attr('data-info-id');
        var html='<div class="editable-buttons info"><button class="btn btn-primary btn-sm" data-info-id="'+info_id+'"><i class="icon-ok"></i></button><button class="btn btn-default btn-sm" data-info-id="'+info_id+'"><i class="icon-remove"></i></button></div>'
        $('.popover-content').html(html);
        return false;
    })


	 //审核按钮 - 不同意
    $(document).on('click','.info .btn-default',function(){
        var info_id = $(this).attr('data-info-id');
        if(confirm('确定要驳回吗？')) {
            verifyOrganization(info_id, 'reject');
        }
        $('.verifyOrg').popover('hide');
    });


	   //审核按钮 - 同意
    $(document).on('click','.info .btn-primary',function(){
        var info_id = $(this).attr('data-info-id');
        if(confirm('确定取现？')) {
			
           verifyOrganization(info_id, 'checked');
        }
        $('.verifyOrg').popover('hide');
    });




    function verifyOrganization (organization_id, status) 
    {
        $.post('index.php?c=bill&a=dosucc', {id:organization_id,status:status},function(data){
            if(data.errors){
                var tmp_errors = '';
                $.each(data.errors, function(i, n){
                    tmp_errors += n;
                });
                var warn_msg = '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>'+tmp_errors+'</div>';
                $('#show_msg').html(warn_msg);
                 location.href='#model_show_msg';
            }else if(data['data']){
				
                var succss_msg = '<div class="alert alert-success"><strong>操作成功!</strong></div>';
                $('#show_msg').html(succss_msg);
                 location.href='#model_show_msg';
				 setTimeout("location.href='bill_lists.html'", '2000');
            }
        }, "json");
        return false;
    }
        </script>
    </body>
</html>