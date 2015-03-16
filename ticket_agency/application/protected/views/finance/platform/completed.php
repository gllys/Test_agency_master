<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 10/25/14
 * Time: 9:02 PM
 */
$this->breadcrumbs = array('充值', '充值完成');
?>
<div class="contentpanel contentpanel-wizard">
    <div class="row">
        <div class="col-md-12">
            <form id="valWizard" action="">
                <ul class="nav nav-justified nav-wizard nav-disabled-click nav-pills">
                    <li class="active"><a href="#tab3-4" data-toggle="tab"> 支付完成</a></li>
                </ul>
            </form>

            <div class="tab-content" style="padding: 10px; min-height: 110px;">
                <h3 style="text-align: center;color: #006600">支付<?php echo $status_labels[$status] ?>!</h3>
                <ul class="list-unstyled wizard">
                    <li class="pull-right next hide">
                        <a href="/finance/platform/" class="btn btn-primary">再次重值</a>
                    </li>
                    <li class="pull-right finish ">
                        <a href="/finance/platform/" class="btn btn-primary">完成</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="/js/bootstrap-wizard.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('#valWizard').bootstrapWizard({
            onTabClick: function(tab, navigation, index) {
                return false;
            },
            onNext: function(tab, navigation, index) {
                return false;
            }
        });
    });
</script>
