<?php 

use common\huilian\utils\Format;
use common\huilian\models\District;

$this->breadcrumbs = array('门票管理', '散客预定');
$pid = isset($_GET['province_id'])?$_GET['province_id']:"";
$jqname = isset($_GET['jqname'])?$_GET['jqname']:"";
$typ = isset($_GET['type'])?$_GET['type']:"";
$scenic = isset($_GET['scenic_id'])?$_GET['scenic_id']:"";

?>
<script>
    var pid = '<?php echo $pid; ?>';
    var jqname = '<?php echo $jqname; ?>';
    var typ = '<?php echo $typ; ?>';
    var scenic = '<?php echo $scenic; ?>';
</script>


                <style>
.rules {
    position: relative;
    display: inline-block;
}
.rules+.rules {
    margin-left: 20px;
}
.rules > span {
    color: #999;
    font-size: 12px;
    cursor: pointer
}
.rules > div >span {
    margin: 0 10px
}
.rules > div {
    display: none;
    position: absolute;
    top: 15px;
    left: 50px;
    z-index: 999;
    width: 500px;
    padding: 10px;
    background-color: #f6fafd;
    border: 1px solid #2a84d2;
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .2);
    word-wrap: break-word;
}
.rules > div .table {
    background: none;
}
.rules > div .table tr > * {
    border: 1px solid #e0d9b6
}
.rules:hover > div {
    display: block;
}
.prov_p {
width: 120px;
display: inline-block;
height: 20px;
text-align: left;
cursor: pointer;
}
.table-bordered th:nth-child(1){padding-left:35px;}
.table-bordered td:nth-child(1){padding-left:35px;}

</style>
		
        <div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns" style="display: none;">
                <a title="" data-toggle="tooltip" class="panel-minimize tooltips" href="" data-original-title=""><i class="fa fa-minus"></i></a>
                <a title="" data-toggle="tooltip" class="panel-close tooltips" href="" data-original-title=""><i class="fa fa-times"></i></a>
            </div><!-- panel-btns -->
            <h4 class="panel-title">门票查询</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline" method="get" action="/ticket/sale/index" id="formsub">
            <!--门票查询改动-->
<style>
.form-inline .select2-container{
	width:200px!important
 
 }
.select2-drop{
	width:300px!important;
}
.select2-results li.select2-result-with-children > .select2-result-label{
	background-color:#f6fafd
}


</style>
            	<div class="form-group" style="width:270px">
                    <label>地区：</label>
					<select data-placeholder="Choose One" id="distributor-select-search" name="scenic_id" class="select2-offscreen">
						<?php foreach(District::initial() as $k => $provinces) { ?>
						<optgroup label="<?= $k ?>">
						<option value="">选择地区</option>
						<?= $k == 'ABCDE' ? '<option value="">全部地区</option>' : '' ?>
						<?php foreach($provinces as $province) { ?>
							<option value="<?= $province->id ?>" <?= $province->id == $pid ? 'selected' : '' ?>><?= $province->name ?></option>			
						<?php } ?>
						</optgroup>
						<?php } ?>
					</select>
                </div>
            	<div class="form-group" style="width:200px">
                	<label class="pull-left">票种：</label>
                    <div class="ckbox ckbox-primary pull-left">
                        <input type="checkbox"  name="ticket_type" value="0" id="checkboxPrimary1" <?= ($param['is_union'] == -1 || $param['is_union'] == 0) ? 'checked' : '' ?>>
                        <label for="checkboxPrimary1">单票</label>
                    </div>
                    <div class="ckbox ckbox-primary pull-left">
                        <input type="checkbox"  name="ticket_type" value="1" id="checkboxPrimary" <?= ($param['is_union'] == -1 || $param['is_union'] == 1) ? 'checked' : '' ?>>
                        <label for="checkboxPrimary">联票</label>
                    </div>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" placeholder="请输入门票名称" value="<?php echo isset($_GET['jqname'])?$_GET['jqname']:""; ?>" style="width:300px" name="jqname">
                </div>
                <div class="form-group">	
                    <button class="btn btn-primary btn-sm" type="button" id="ffsub">搜索</button>
                </div>
             </form>
        </div><!-- panel-body -->
    </div>
   <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">商品列表<div style="float:right;"><a href="/channel/tb/" class="btn btn-xs btn-default">淘宝账号及产品绑定</a></div></h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb30" id="tablecss">
                <thead>
                    <tr>
                        <th>票种</th>
                        <th>景区</th>
                        <th style="width:15%">门票名称</th>
                        <th>供应商</th>
                        <th style="width:130px">游玩有效期</th>
                        <th>门市挂牌价</th>
                        <th>网络销售价</th>
                        <th>散客结算价</th>
                        <th>类型</th>
                        <th style="width:105px;">操作</th>
                    </tr>
                </thead>
                <tbody>
				<?php foreach($lists as $model) { ?>
                    <tr>
                    	<td><?= $model['is_union'] == 1 ? '联票' : '单票' ?></td>
                        <td style="text-align:left;color: #636e7b;">
	                        <?php
                                //单例，性能优化
                                if (!isset($singleLans)) {
                                    //得到所有景点信息
                                    $ids = PublicFunHelper::arrayKey($lists, 'scenic_id');
                                    $param = array();
                                    $param['ids'] = join(',', $ids);
                                    $param['items'] = 100000;
                                    $param['fields'] = 'id,name';
                                    $data = Landscape::api()->lists($param, true, 30);
                                    $singleLans = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
                                    //print_r($singleLans);
                                }
                                $_lans = explode(',', $model['scenic_id']);
                                //print_r($_lans);
                                $html = '';
                                foreach ($_lans as $id) {
                                    if (!empty($singleLans[$id])) {
                                        $html .= "<a href='/ticket/show/?id=" . $singleLans[$id]['id'] . "'>" . $singleLans[$id]['name'] . "</a><br>";
                                    }
                                }
                                ?>
                            <div class="lanpart<?php echo $model['id']?>">
         						<?php echo $html?>
                            </div>
                            <div class="lan<?php echo $model['id']?>" style="display: none"><?php echo $html;?></div>
                        </td>
                        <td style="text-align:left">
	                        <div class="col-md-12">
		                        <div class="pull-left"><a href="/ticket/show/product/?price_type=0&id=<?= $model['id'] ?>"><strong><?= $model['name'] ?></strong></a></div>
	                        </div>
	                        <div class="col-md-12">
		                        <div class="pull-left">
			                        <div class="rules"><span>门票说明</span>
				                        <div class="table-responsive">
					                        <table class="table table-bordered mb30">
					                        	<?= $model['remark'] ?>
						                    </table>
				                        </div>
			                        </div>
			                        <div class="rules"><span>游玩星期</span>

				                        <div class="day"><?php
					                        if (strstr($model['week_time'], '1')) {
						                        echo '周一' . '&nbsp;';
					                        }
					                        if (strstr($model['week_time'], '2')) {
						                        echo '周二' . '&nbsp;';
					                        }
					                        if (strstr($model['week_time'], '3')) {
						                        echo '周三' . '&nbsp;';
					                        }
					                        if (strstr($model['week_time'], '4')) {
						                        echo '周四' . '&nbsp;';
					                        }
					                        if (strstr($model['week_time'], '5')) {
						                        echo '周五' . '&nbsp;';
					                        }
					                        if (strstr($model['week_time'], '6')) {
						                        echo '周六' . '&nbsp;';
					                        }
					                        if (strstr($model['week_time'], '0') === '0') {
						                        echo '周日' . '&nbsp;';
					                        }
					                        ?></div>
			                        </div>
		                        </div>
	                        </div>
                        </td>
                        <td style="text-align:left"><?php
                                    //单例，性能优化
                                if (!isset($singleOrgans)) {
                                    //得到所有景点信息
                                    $ids = PublicFunHelper::arrayKey($lists, 'organization_id');
                                    $param = array();
                                    $param['ids'] = join(',', $ids);
                                    $param['items'] = 100000;
                                    $param['fields'] = 'id,name';
                                    $data = Organizations::api()->list($param,true,30);
                                    $singleOrgans = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
                                   // print_r($singleOrgans);
                                }
                                echo isset($singleOrgans[$model['organization_id']]) ? $singleOrgans[$model['organization_id']]['name'] : "";
                                ?></td>
                        <td>
                        <?php 
                        		$time = explode(',',$model['date_available']);
                                        if(!empty($time[0]) && !empty($time[1])){
                                            echo date('Y年m月d日',$time[0]) . '~<br/>' .date('Y年m月d日',$time[1]);
                                        }else{
                                            echo '';
                                        }
                        		
                        	?>
                          </td>
                        <td style="text-align:right"><del><?= $model['listed_price'];?></del></td>
                        <td style="text-align:right"><del><?= $model['sale_price'];?></del></td>
                        <td style="text-align:right" class="text-success"><?= number_format($model['fat_price'],2) ?></td>
                        <td><?= $model['type'] ? '任务单' : '电子票' ?></td>
                        <td style=" line-height:51px;">
                            <div class="pull-left"><a class="btn btn-success btn-xs" href=".bs-example-modal-lg" onclick="buy('<?= $model['id'] ?>','<?= $model['organization_id']?>');" data-toggle="modal">购买</a><strong style="display:none;"><?= $model['name'] ?></strong></a></div>
                        	<div class="pull-right" style="margin-right:3px;" data-id="<?= $model['id'] ?>" data-fat="<?= $model['fat_price'] ?>" data-group="<?= $model['group_price']?>"><?php
			                        echo isset($model['sub']) && $model['sub'] == 1
			                            ? '<a class="bun fav fav-done" href="javascript:;" title="取消收藏">已收藏</a>'
				                        : '<a class="bun fav" href="javascript:;" title="加入收藏">收藏</a>';
		                        ?>
		                   </div>
                        </td>
                    </tr>
                	<?php } ?>
                </tbody>
            </table>

        </div>        
    </div>
    <div class="panel-footer" style="margin-top:-25px;">
        <div class="row">
                <div class="pagenumQu">
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
</div><!-- contentpanel -->

<style>
    .red{color:red;}
</style>

<!--购买票开始-->
 <div class="modal fade bs-example-modal-lg" id="verify-modal-buy" tabindex="-1" role="dialog"></div>
 <div class="modal fade bs-example-modal-static" id="verify-modal-alert" tabindex="-2" role="dialog"></div>
 <div class="modal-dialog modal-lg" id="modalalert" style="width:500px;display: none;">
     <div class="modal-content">
<!--         <div class="modal-header">                
            <h4 class="modal-title"></h4>
        </div>-->
        <div class="modal-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span style="font-size: 18px;line-height: 30px;">请等待景区确认！</span><br />
                    <span style="font-size: 18px;line-height: 30px;">景区确认后，您会收到一条消息提醒，请注意查收！</span>
                </div>
            </div>
        </div>
        <!--<div class="modal-footer"></div>-->
     </div>
 </div>



<script type="text/javascript">

jQuery(document).ready(function(){
	$(window).scroll(function() {
		$('#select2-drop-mask').trigger('click');
	});
	$("#distributor-select-search").select2();   
   	$("#distributor-select-search").change(function() {
   		province($(this).val());
   	});             
 });



function buy(id,supplier_id){
     document.getElementById('verify-modal-buy').innerHTML = '';
     //$('#verify-modal-buy').html('');
        $.get('/ticket/buy/?price_type=0&id='+id+'&supplier_id='+supplier_id, function(data) {
            $('#verify-modal-buy').html(data);
        });
}

// <!--购买票结束-->

//全展示
$('.lanview').click(function(){
    var id = $(this).attr('data-id');
    $('.lanpart' + id).hide();
    $('.lan' + id).show();

})

$("#ffsub").click(function(){
    jqname = $('input[name="jqname"]').val();
	var checked = $('input[name="ticket_type"]:checked');
	var is_union = checked.length == 1 ? checked.val() : -1;
// 	alert(is_union);
// 	return 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ+"&is_union="+is_union;

	window.location.href = url;
    //$("#formsub").submit();
});


function province(provinceid){
    pid = provinceid; 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
   window.location.href = url;

}

function ticketType(typeid){
    typ = typeid; 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
    window.location.href = url;

}
function getLandspace(id){
    scenic = id; 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ+"&scenic_id="+scenic;
    window.location.href = url;    
}
   



$("#getOver0").click(function(){
    $("#getOver0").parent().remove();
    pid = ''; 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
     //alert(url);
   window.location.href = url;

})

$("#getOver1").click(function(){
    $("#getOver1").parent().remove();
    typ = ''; 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
   // alert(url);
    window.location.href = url;

})
</script>



<script>
jQuery(document).ready(function(){
                // Tags Input
                jQuery('#tags').tagsInput({width:'auto'});
                 
                // Textarea Autogrow
                jQuery('#autoResizeTA').autogrow();
                

                
                // Form Toggles
                jQuery('.toggle').toggles({on: true});
                

                // Date Picker
                jQuery('#datepicker').datepicker();
                jQuery('#datepicker-inline').datepicker();
                jQuery('#datepicker-multiple').datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
                
                // Input Masks
                jQuery("#date").mask("99/99/9999");
                jQuery("#phone").mask("(999) 999-9999");
                jQuery("#ssn").mask("999-99-9999");
                
                // Select2
                jQuery("#select-basic, #select-multi").select2();
                jQuery('.select2').select2({
                    minimumResultsForSearch: -1
                });
                
                function format(item) {
                    return '<i class="fa ' + ((item.element[0].getAttribute('rel') === undefined)?"":item.element[0].getAttribute('rel') ) + ' mr10"></i>' + item.text;
                }
                
                // This will empty first option in select to enable placeholder
                jQuery('select option:first-child').text('');
                
                jQuery("#select-templating").select2({
                    formatResult: format,
                    formatSelection: format,
                    escapeMarkup: function(m) { return m; }
                });
                
                // Color Picker
                if(jQuery('#colorpicker').length > 0) {
                    jQuery('#colorSelector').ColorPicker({
			onShow: function (colpkr) {
			    jQuery(colpkr).fadeIn(500);
                            return false;
			},
			onHide: function (colpkr) {
                            jQuery(colpkr).fadeOut(500);
                            return false;
			},
			onChange: function (hsb, hex, rgb) {
			    jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
			    jQuery('#colorpicker').val('#'+hex);
			}
                    });
                }
  
                // Color Picker Flat Mode
                jQuery('#colorpickerholder').ColorPicker({
                    flat: true,
                    onChange: function (hsb, hex, rgb) {
			jQuery('#colorpicker3').val('#'+hex);
                    }
                });
                
                
            });

</script>
<script src="/js/fav-sub.js"></script>
























