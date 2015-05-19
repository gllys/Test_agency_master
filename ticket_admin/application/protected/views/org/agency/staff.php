<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">分销商用户</h4>
        </div>
        <style>
            .table-responsive th,.table-responsive td{
                vertical-align:middle!important
            }
            .panel-footer b{
                font-size:22px;
                padding:0 5px;
            }
            #t1 th{
                text-align:right
            }
            #t1 td{
                text-align:left
            }
        </style>
    </div>
    
     <!--列表开始-->
    <div class="tab-content mb30">
        <div id="t1" class="tab-pane active">

            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <td>姓名</td>
                            <td>账号</td>
                            <td>手机号码</td>
                            <td>角色</td>
                            <td>状态</td>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody id="staff-body">
                        <?php if ($data): ?>
                            <?php foreach ($data as $value):
                                ?>
                                <tr class="status-pending" height="36px">
                                    <td><?php echo $value['name']; ?></td>
                                    <td><?php echo $value['account']; ?></td>
                                    <td><?php echo $value['mobile']; ?></td>
                                    <td><?php 
                                        echo isset($userroles[$value["id"]]) ? $userroles[$value["id"]]["name"] : "未分配角色";
                                        ?>
									</td>
                                    <td><?php echo $value['status'] ? '<font color="green">已启用</font>' : '<font color="red">已禁用</font>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--列表结束-->
    
</div> 
<div class="panel-footer" style="padding-left:5%">
    <button class="btn btn-default" type="button" onclick="javascript:history.go(-1);" id="export">返回</button>
</div>