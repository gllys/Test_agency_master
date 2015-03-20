<header>
    <!--[if IE 8]>  
    <script type='text/javascript' src='/js/excanvas.js'></script>  
    <link rel="stylesheet" href="/css/iefix.css" type="text/css" media="screen" />  
    <![endif]-->   
    <style>
       
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
                $info = Organizations::api()->show(array('id' => Yii::app()->user->org_id,'fields'=>'name'));
                if ($info['code'] == 'succ') {
                    $org_name = $info['body']['name'];
                }
                echo $org_name;
                ?>
            </div>


            <div class="pull-right" id="pull-right">
                <!--消息开始-->
                <?php
                if (CreateUrl::model()->checkAccess('/system/message/')):
                    ?>
                    <div class="btn-group btn-group-option">
                        <a href="/system/message" title="消息" class="btn btn-default dropdown-toggle"><i class="fa fa-envelope-o"></i>
                            <?php
                            $result = Message::api()->count(array('receiver_organization' => Yii::app()->user->org_id,
                                'read_time' => 0
                            ),true,3);
                            if ($result['code'] == 'succ') {
                                $num = $result['body']['pagination']['count'];
                            }
                            ?>
                            <?php if (isset($num) && $num > 0): ?><span id="unread_num" class="badge"><?php echo $num ?></span><?php endif; ?>消息</a>
                    </div>
                <?php endif ?>
                <!--消息结束-->

                <!--购物车开始-->
                <?php
                if (CreateUrl::model()->checkAccess('/ticket/cart/')):
                    ?>
                    <div class="btn-group btn-group-option">
                        <a href="/ticket/cart/" title="购物车" class="btn btn-default dropdown-toggle"><i class="fa fa-shopping-cart"></i>
                            <?php
                            //购物车
                            $rs = Cart::api()->count(array('user_id' => Yii::app()->user->uid),true,3);
                            $num = $rs['body']['pagination']['count'];
                            ?>
                            <?php if (isset($num) && $num > 0): ?><span class="badge"><?php echo $num ?></span><?php endif; ?>购物车</a>
                    </div>
                <?php endif ?>
                <!--购物车结束-->

                <!--收藏开始-->
                <?php
                if (CreateUrl::model()->checkAccess('/ticket/favorites/')):
                    ?>
                    <div class="btn-group btn-group-option">
                        <a href="/ticket/favorites/" title="收藏" class="btn btn-default dropdown-toggle"><i class="fa fa-star-o"></i>
                            <?php
                            $all = Subscribes::api()->count(array('organization_id' => Yii::app()->user->org_id), true,3);
                            $num = $all['body']['pagination']['count'];
                            ?>
                            <?php if (isset($num) && $num > 0): ?><span id="favorite" class="badge"><?php echo $num; ?></span><?php endif; ?>收藏</a>
                    </div>
                <?php endif ?>
                <!--收藏结束-->

                <!--工作台开始-->
                <div class="btn-group btn-group-option">
                    <a href="/dashboard" class="btn btn-default dropdown-toggle" title="工作台"><i class="fa fa-desktop"></i>工作台</a>
                </div>
                <!--工作台结束-->

                <!--下载开始-->
                <div class="btn-group btn-group-option">
                    <a href="http://piaowu-manual.b0.upaiyun.com/%E7%A5%A8%E5%8F%B0%E5%88%86%E9%94%80%E5%95%86%E7%B3%BB%E7%BB%9F%E6%93%8D%E4%BD%9C%E6%89%8B%E5%86%8C.pdf" target="_blank" class="btn btn-default" title="票台分销商系统操作手册"><i class="fa fa-download"></i>下载</a>
                </div><!-- btn-group -->
                <!--下载结束-->

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
            if (Yii::app()->user->is_super):
                ?>
                <?php
                if (isset(Yii::app()->user->org_id) && !empty(Yii::app()->user->org_id)) {
                    $org_id = Yii::app()->user->org_id;
                    $orgRs = Organizations::api()->show(array('id' => $org_id));
                    if ($orgRs['code'] == 'succ') {
                        $status = $orgRs['body']['verify_status'];
                    }
                }
                ?>
                <?php if (isset($status) && $status == 'checked'): ?>
                    <?php
                    $advice_field = array(
                        'receiver_organization' => Yii::app()->user->org_id,
                        'sys_type' => 0,
                        'read_time' => 0,
                        'items' => 5
                    );
                    $advice_result = Message::api()->list($advice_field);
                    if ($advice_result['code'] == 'succ') {
                        $advice_list = !empty($advice_result['body']['data']) ? $advice_result['body']['data'] : '';
                    }
                    ?>
                    <div id="PT-marquee" class="pull-right">
                        <ul>
                            <?php if (isset($advice_list) && !empty($advice_list)): foreach ($advice_list as $value): ?>
                                    <li><a href="javascript:;" class="readAdvice" id="already<?php echo $value['id'] ?>"
                                           data-id="<?php echo $value['id'] ?>"
                                           data-name="<?php echo $value['organization_name'] ?>"
                                           data-food="<?php echo $value['read_time'] == 0 ? 0 : date('Y年m月d日', $value['read_time']) ?>"
                                           data-title="<?php echo $value['title'] ?>"
                                           data-time="<?php echo date('Y年m月d日', $value['created_at']) ?>"
                                            data-content='<?php echo strip_tags($value['content'],'<a><p><br/><br>'); ?>'>
                                            【公告】 <?php echo $value['title'] ?>
                                        </a></li>
                                <?php endforeach;
                            endif; ?>
                        </ul>
                    </div>
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
