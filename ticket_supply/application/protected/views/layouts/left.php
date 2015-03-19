<div class="leftpanel" style="position:fixed;top:60px">
    <!--<div class="media profile-left">
        <a href="#" class="pull-left profile-thumb">
            <img alt="" src="/img/profile.png" class="img-circle">
        </a>
        <div class="media-body">
         
            <small class="text-muted">超级管理员</small>
        </div>
    </div><!-- media -->

    <ul class="nav nav-pills nav-stacked" id="child_nav"  style="margin-top:10px">
       <li></li>
        <?Php
        echo CreateUrl::model()->createBody($this->nav);
        ?>
    </ul>

</div><!-- leftpanel -->

<script type="text/javascript">
$('#child_nav a[href="<?php echo $this->childNav ?>"]').parent().addClass('active');
</script>
