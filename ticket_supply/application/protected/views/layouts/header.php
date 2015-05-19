<header>
    <!--[if IE 8]>  
      <script type='text/javascript' src='/js/excanvas.js'></script>  
      <link rel="stylesheet" href="/css/iefix.css" type="text/css" media="screen" />  
      <![endif]-->
    <style>
        .modal-backdrop{
            z-index:10;
        }
    </style>
    <div class="headerwrapper">
        <div class="header-left">
            <a class="logo" href="/ticket/sale" id="title" title="首页"></a>
            <div class="pull-right">
                <a class="menu-collapse" href="#">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>

        <div class="header-right"  id="header_nav">
            <div class="header-profile">
                <div><?php echo Yii::app()->user->display_name ?>，欢迎登录!</div>
                <?php
                $org_name = '汇联分销系统';
                $status = 0;
                $info = $this->getOrgInfo();
                if ($info) {
                    $org_name = $info['name'];
                    $status = $info['status'];
                }
                if (empty(Yii::app()->user->lan_id)) {
                    echo $org_name;
                }
                ?>
            </div>


            <div class="pull-right" id="pull-right">
                <!--消息开始-->
                <?php
                if ($status) {
                    if (CreateUrl::model()->checkAccess('/system/message/')):
                        ?>
                        <div class="btn-group btn-group-option">
                        <a href="/system/message" title="消息" class="btn btn-default dropdown-toggle">
                            <i class="fa fa-envelope-o"></i>
                            <span id="unread_num" class="badge hide unread_message">0</span>消息</a>
                    </div>
                    <?php endif ?>
                    <!--消息结束-->

                    <?php if (empty(Yii::app()->user->lan_id)): ?>
                        <!--工作台开始-->
                        <div class="btn-group btn-group-option">
                            <a href="/dashboard" class="btn btn-default dropdown-toggle" title="工作台"><i class="fa fa-desktop"></i>工作台</a>
                        </div>
                        <!--工作台结束-->

                        <!--下载开始-->
                        <!--div class="btn-group btn-group-option">
                            <a href="http://piaowu-manual.b0.upaiyun.com/%E7%A5%A8%E5%8F%B0%E4%BE%9B%E5%BA%94%E5%95%86%E7%B3%BB%E7%BB%9F%E6%93%8D%E4%BD%9C%E6%89%8B%E5%86%8C.pdf" target="_blank" class="btn btn-default" title="票台供应商系统操作手册"><i class="fa fa-download"></i>下载</a>
                        </div--><!-- btn-group -->
                        <!--下载结束-->
                        <!--在线帮助开始  author：徐娟-->
                        <div class="btn-group btn-group-option">
                            <a href="/dashboard/help" class="btn btn-default dropdown-toggle" title="在线帮助"><i class="fa
                                                                                                              fa-question-circle"></i>在线帮助</a>
                        </div>
                        <!--在线帮助结束-->

                    <?php endif;
                } ?>
                <!--退出开始-->
                <div class="btn-group btn-group-option">
                    <!--button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="/site/logout"><i class="glyphicon glyphicon-log-out"></i>退出</a></li>
                    </ul-->

                    <a href="/site/logout" title="退出" class="btn btn-default dropdown-toggle"><i class="fa fa-sign-out"></i>退出
                    </a>
                </div><!-- btn-group -->
                <!--退出结束-->


            </div><!-- pull-right -->

            <!--公告开始-->
            <?php
            // 显示消息轮播条的两种情况，一 供应商超级管理员，二 验票账号
            if (Yii::app()->user->is_super || (!Yii::app()->user->isGuest && Yii::app()->user->lan_id)):
                ?>
                <?php
                    if (isset(Yii::app()->user->org_id) && !empty(Yii::app()->user->org_id)) {
                        $info = $this->getOrgInfo();
                        if ($info) {
                            $status = $info['verify_status'];
                         }
                    }
                ?>
                <?php if (isset($status) && $status == 'checked'): ?>
                   
                    <div id="PT-marquee" class="pull-right"></div>
                    <div id="PT-model"></div>
                <?php endif; ?>
            <?php endif; ?>
            <!--公告结束-->

        </div><!-- header-right -->

    </div><!-- headerwrapper -->
</header>
<script type="text/javascript">
//头部选中
    $('#nav_<?php echo CreateUrl::model()->getIndex($this->nav) ?>').addClass('on');
</script>
