

            <div class="mainpanel">
                <div class="contentpanel">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">打印模版</h4>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" method="get" action="/ticket/template/">
                            <!--查询-->
                              
        
                                <div class="form-group">
                                    <div class="input-group input-group-sm">
                                        <input id="search_field" name="name" value="<?php if (isset($get["name"])) echo $get["name"]; ?>" placeholder="请输入模板名称" type="text" class="form-control" style="z-index: 0;width:300px;" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm" type="submit">查询</button>
                                    <button onclick="document.location.href='/site/switch/#/ticket/template/add'" style="background-color:green;" class="btn btn-primary btn-sm" type="button">新增</button>
                                </div>
                            </form>
                        </div>
                        <!-- panel-body -->
                    </div>


                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="javascript:;"><strong>模版列表</strong></a>
                        </li>
                    </ul>
                    <div class="tab-content mb30">
                        <div id="t1" class="tab-pane active">
                            <table class="table table-bordered mb30">
                            <thead>
                                <tr>             
                                    <th>编号</th>
                                    <th>模板名称</th>
                                    <th>材质</th>
                                    <th>尺寸</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody id="staff-body">
                                <?php if (isset($list)): ?>

                                    <?php foreach ($list as $value): ?>
                                        <tr class="status-pending" height="36px">
                                            <td><?php echo $value['id']; ?></td>
                                            <td class="icon">
                                                <?php echo $value['name']; ?>
                                            </td>
                                            <td><?php echo $value['spec']; ?></td>
                                            <td><?php
                                                echo (isset($value['height']) ? $value['height'] : "") .'*'.(isset($value['width']) ? $value['width'] : "");
                                                ?></td>
                                            <td><a title="编辑" href="/ticket/template/edit/id/<?php echo $value['id']; ?>">   
                                                    编辑
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>   
                            </tbody>
                        </table>
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
                                    ));
                                ?> 
                            </div>
                        </div>
                            <!--<div class="panel-footer">
                                    <div class="row">
                                         <div class="col-xs-6"></div>
                                        <div class="col-xs-6">
                                            <div class="pagenumQu">
                                                <ul class="yiiPager" id="yw0">
                                                    <li class="first">
                                                        <a href="/order/history/index"></a>
                                                    </li>
                                                    <li class="previous"><a href="/order/history/index">上一页</a>
                                                    </li>
                                                    <li class="page"><a href="/order/history/index">1</a>
                                                    </li>
                                                    <li class="page selected"><a href="/order/history/index/page/2">2</a>
                                                    </li>
                                                    <li class="page"><a href="/order/history/index/page/3">3</a>
                                                    </li>
                                                    <li class="page"><a href="/order/history/index/page/4">4</a>
                                                    </li>
                                                    <li class="page"><a href="/order/history/index/page/5">5</a>
                                                    </li>
                                                    <li class="break">. . .</li>
                                                    <li class="page"><a href="/order/history/index/page/528">528</a>
                                                    </li>
                                                    <li class="next"><a href="/order/history/index/page/3">下一页</a>
                                                    </li>
                                                    <li class="last">
                                                        <a href="/order/history/index/page/528"></a>
                                                    </li>
                                                </ul>
                                                
                                                跳转到 <input id="" value="" type="text" class="form-control"><button class="btn btn-primary btn-sm" type="submit">GO</button>
                                            </div>  
                                    
                                        </div>
                                    </div>
                            </div>-->
                        </div>
                    </div>
                </div>
                <!-- contentpanel -->
            </div>
            <!-- mainpanel -->
        </div>
        <!-- mainwrapper -->
    </section>

    

<div class="modal fade" id="msg">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
                <div id="advice_title" class="modal-title"></div>
                <div id="advice_name" style="float:left;color:#999;font-size:12px;"></div>
                <div id="advice_time" style="float:left;margin-left:20px;color:#999;font-size:12px;"></div>
            </div>
            <div id="advice_content" class="modal-body" style="word-break:break-all;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="close_advice">关闭</button>
            </div>
        </div>
    </div>
</div>
        
