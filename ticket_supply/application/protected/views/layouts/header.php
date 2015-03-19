<header>
    <style>
        #title{
            overflow: hidden; /*自动隐藏文字*/
            text-overflow: ellipsis;/*文字隐藏后添加省略号*/
            white-space: nowrap;/*强制不换行*/
            width: 8em;/*不允许出现半汉字截断*/}
        /*#pull-right a {position:relative;}
                #pull-right a:hover:before {position:absolute;top:40px;right:0;content:attr(title);color:#000000;border:1px solid #242424;background-color:#E5E5E5;}
        */
    </style>
    <div class="headerwrapper">
        <div class="header-left">
            <?php
            $org_name = '汇联供应系统';
            $info = Organizations::api()->show(array('id' => Yii::app()->user->org_id));
            if ($info['code'] == 'succ') {
                $org_name = $info['body']['name'];
            }
            ?>
            <?php
            if (!Yii::app()->user->isGuest && Yii::app()->user->lan_id) {
                ?>
            <a class="logo" href="#" style="cursor:default" id="title" title="<?php echo $org_name; ?>">
                    <?php
                    echo $org_name;
                    ?>
                </a>
                <?php
            } else {
                ?>
                <a class="logo" href="/scenic/scenic" id="title" title="<?php echo $org_name; ?>">
                    <?php
                    echo $org_name;
                    ?>
                </a>
                <?php
            }
            ?>
            <div class="pull-right">
                <a class="menu-collapse" href="#">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>

        <div class="header-right"  id="header_nav">
            <ul class="nav navbar-nav hidden-xs">
                <?Php
                echo CreateUrl::model()->createHeader();
                ?>
            </ul>


            <div class="pull-right" id="pull-right">
                <?php /*
                  <div class="btn-group btn-group-list btn-group-notification">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i>
                  <span class="badge"><?php echo $unread_msg['sys'] ?></span>
                  </button>
                  <div class="dropdown-menu pull-right">
                  <a href="#" class="link-right"><i class="fa fa-times"></i></a>
                  <h5>系统通知</h5>
                  <ul class="media-list dropdown-list">
                  <?php if ($unread_msg['sys'] > 0) : foreach ($unread_msg['sysUnread'] as $sysMsg) : ?>
                  <li class="media">
                  <img class="img-circle pull-left noti-thumb" src="/img/user1.png" alt="">
                  <div class="media-body">
                  <?php echo mb_substr(strip_tags($sysMsg['content']), 0, 30, 'UTF-8') ?>…
                  <small class="date"><i class="fa fa-thumbs-up"></i> <?php echo $sysMsg['created_at'] ?></small>
                  </div>
                  </li>
                  <?php
                  endforeach;
                  endif;
                  ?>
                  </ul>
                  <div class="dropdown-footer text-center">
                  <a href="#" class="link">See All Notifications</a>
                  </div>
                  </div><!-- dropdown-menu -->
                  </div><!-- btn-group -->

                  <div class="btn-group btn-group-list btn-group-messages">

                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-envelope-o"></i>
                  <span class="badge"><?php echo $unread_msg['org'] ?></span>
                  </button>
                  <div class="dropdown-menu pull-right">
                  <a href="#" class="link-right"><i class="fa fa-times"></i></a>
                  <h5>消息</h5>
                  <ul class="media-list dropdown-list">
                  <?php if ($unread_msg['org'] > 0) : foreach ($unread_msg['orgUnread'] as $orgMsg) : ?>
                  <li class="media">
                  <span class="badge badge-success">New</span>
                  <img class="img-circle pull-left noti-thumb" src="/img/user1.png" alt="">
                  <div class="media-body">
                  <strong>Nusja Nawancali</strong>
                  <p><?php echo mb_substr(strip_tags($orgMsg['content']), 0, 30, 'UTF-8') ?>…</p>
                  <small class="date"><i class="fa fa-clock-o"></i> <?php echo $orgMsg['created_at'] ?></small>
                  </div>
                  </li>
                  <?php
                  endforeach;
                  endif;
                  ?>
                  </ul>
                  <div class="dropdown-footer text-center">
                  <a href="#" class="link">See All Messages</a>
                  </div>
                  </div><!-- dropdown-menu -->
                  </div><!-- btn-group -->
                 */ ?>
                <?php
                if (CreateUrl::model()->checkAccess('/system/message/')):
                    ?>
                    <div class="btn-group btn-group-option">
                        <a href="/system/message" title="消息" class="btn btn-default dropdown-toggle"><i class="fa fa-bell"></i>
                            <?php
                            $result = Message::api()->list(array('receiver_organization' => Yii::app()->user->org_id,
                                'read_time' => 0
                            ));
                            if ($result['code'] == 'succ') {
                                $num = $result['body']['pagination']['count'];
                            }
                            ?>
                            <?php if (isset($num) && $num > 0): ?><span class="badge"><?php echo $num ?></span><?php endif; ?></a>
                    </div>
                <?php endif ?>

                <?php
                if (Yii::app()->user->is_super):
                    ?>
                    <div class="btn-group btn-group-option">
                        <a href="/dashboard" class="btn btn-default dropdown-toggle" title="工作台"><i class="fa fa-desktop"></i></a>
                    </div>
                <?php endif ?>

                <div class="btn-group btn-group-option">
                    <a href="/site/logout" class="btn btn-default dropdown-toggle" title="退出"><i class="fa fa-sign-out"></i></a>
                </div><!-- btn-group -->

            </div><!-- pull-right -->

        </div><!-- header-right -->

    </div><!-- headerwrapper -->
</header>
<script type="text/javascript">
//头部选中
    $('#nav_<?php echo CreateUrl::model()->getIndex($this->nav) ?>').addClass('on');
</script>
