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
  <div class="container-fluid padded">
    <div class="row-fluid"> 
      <div id="breadcrumbs">
        <div class="breadcrumb-button blue"> <span class="breadcrumb-label"><i class="icon-home"></i> 首页</span> <span class="breadcrumb-arrow"><span></span></span> </div>
        <div class="breadcrumb-button"> <span class="breadcrumb-label"> <i class="icon-cog"></i> OTA管理</span> <span class="breadcrumb-arrow"><span></span></span> </div>
      </div>
    </div>
  </div>
<style>
div.selector{
    margin:0 10px;
    width:200px;
    }
.btn-default{
    margin-right:10px;
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
</style>
    <div class="container-fluid padded">
        <div class="box">
            <div class="box-header">
                <span class="title"><i class="icon-search"></i> 采集信息检索</span>
            </div>

            <div class="table-header" style="height:auto;padding-bottom:10px;">
                <form action="">
                    <div class="row-fluid" style="margin-bottom:10px;">
                        更新时间：<input type="text" placeholder="" name="updated_at" style="width:180px;margin:0 10px 0" class="form-time" value="<?php echo $get['updated_at'] ?>">
                        发布单位：<input type="text" placeholder="" name="organization_name" value="<?php echo $get['organization_name'] ?>"  style="width:100px;margin:0 10px 0">
                        是否上架：
                        <select class="uniform" name="status">
                            <option  value="0"<?php if($get['status']==0):?> selected="selected"<?php endif;?>>未上架</option>
                            <option  value="1"<?php if($get['status']==1):?> selected="selected"<?php endif;?>>已上架</option>
                        </select>
                        OTA
                        <select class="uniform" name="ota_type">
                            <option  value="lashou">拉手网</option>
                        </select>
                        <button class="btn btn-default" style="float:none;">搜索</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        
    <div class="container-fluid padded gap">
    <div class="box">
            <div class="box-header">
                <span class="title"><i class="icon-list"></i> 列表</span>
            </div>
            <table class="table table-normal">
                <thead>
                  <tr>
                    <td class="icon">序号</td>
                    <td>商品号</td>
                    <td>发布单位</td>
                    <td>地区</td>
                    <td>发布时间</td>
                    <td>门票文档下载</td>
                    <td>是否上架</td>
                  </tr>
                </thead>
                <tbody>
                <?php foreach($ticketOtaList as $key => $value):?>
                  <tr>
                    <td><?php echo $value['id']?></td>
                    <td><?php echo $value['ticket_template_id']?></td>
                    <td><?php echo $value['organization_name']?></td>
                    <td><?php echo $value['district_name']?></td>
                    <td><?php echo $value['created_at']?></td>
                    <td><a href="<?php echo $value['url']?>">下载</a></td>
                    <td><?php if($value['status']==0):?>未上架<?php else:?>已上架<?php endif;?></td>
                  </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <div class="table-footer">
              <div class="dataTables_paginate paging_full_numbers">
                    <?php echo $pagination; ?>
                  </div>
            </div>
        </div>
    </div>
<script src="Views/js/vendor/date.js"></script>
<script src="Views/js/vendor/moment.js"></script>
<script src="Views/js/vendor/daterangepicker.js"></script>
<link href="Views/css/daterangepicker.css" rel="stylesheet">
<script>
$(document).ready(function() {
    $('input.form-time').daterangepicker({format:'YYYY-MM-DD'});
});
</script>
    </body>
</html>