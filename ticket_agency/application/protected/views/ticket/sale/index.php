<?php
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
    .prov_p{width:120px;display:inline-block;height: 20px;text-align: left; cursor: pointer;}
    .ticket_type{cursor: pointer;}
    #proname{color:red;}
    #tablecss th,#tablecss td{text-align: center;}
	.bun {color: #999}
	.fav-done {color: #269abc}
	.sub-done {color: #643534}
	.sub-done:hover {color: #801504}
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
            <form class="form-inline" method="get" action="" id="formsub">
                <table>
                    <tr>
                        <th width="60">地区：</th>
                        <td>
                        <?php
                            $province = Districts::model()->findAllByAttributes(array('level' => 1));
                            $canProvinces = array(310000,110000,320000,330000,340000,350000);
                            foreach ($province as $model) {  
                                if(!in_array($model->id, $canProvinces))
                                   continue;;
                                ?>
                              <a onclick='province("<?php echo $model->id;?>");' class='prov_p'><span<?php 
                              if(isset($param['province_id'])){
                                 if($param['province_id'] == $model->id){
                                     echo  '  id=proname';
                                 }
                              } ?>><?php echo $model->name;?></span></a>
                        <?php    } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>类型：</th>
                        <td>
                            <a onclick="ticketType(0);" class="btn btn-sm ticket_type"><span
                                  <?php 
                              if(isset($param['type'])&&$param['type']!=""){
                                 if($param['type'] == 0){
                                     echo  '  id=proname';
                                 }
                              } ?>
                                    >电子票</span></a>
                            <!--a onclick="ticketType('1');" class="btn btn-sm ticket_type"><span
                                        <?php 
                                        if(isset($param['type'])&&$param['type']!=""){
                                           if($param['type'] == 1){
                                               echo  '  id=proname';
                                           }
                                        } ?>
                                    >任务单</span></a-->
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div id="pro"></div><div id="ttype"></div>
                        </th>
                    
                        <td>
                            <div class="form-group">
        
                                <input class="form-control" type="text" placeholder="请输入门票名称" value="<?php echo isset($_GET['jqname'])?$_GET['jqname']:""; ?>" style="width:300px" name="jqname">
                            </div>
                            <button type="button" class="btn btn-primary mr5 btn-sm" id="ffsub">搜索</button>
                        </td>
                     
                    </tr>
                </table>
             </form>
        </div><!-- panel-body -->
    </div>
<?php if(!empty($param['province_id'])||(isset($param['type'])&&$param['type']!='')): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            所有分类 > 
            <?php
                
                    if(isset($param['province_id'])){
                       $pro = Districts::model()->find('id=:id',array(':id'=>$param['province_id']));  
                         echo "<a  class=btn btn-xs mr5'> $pro[name] <i class='fa fa-times' id='getOver0'></i></a> >";
                    }
                    if(isset($param['type'])&&$param['type']!=""){
                        $str = $param['type']?'任务单':'电子票';
                        echo "<a  class=btn btn-xs mr5'> $str <i class='fa fa-times' id='getOver1'></i></a> ";
                    }
                    
            ?>
        </div><!-- panel-body -->
    </div>
<?php endif; ?>
<?php if(!empty($param['province_id'])): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <form class="form-inline">
                <table>
                    <tr>
                        <th width="60">
                            <?php
                             if(isset($landscapes) && !empty($landscapes)){
                                 $province = $landscapes[0]['province_id'];
                                 $pro = Districts::model()->find('id=:id',array(':id'=>$province));
                                 echo $pro['name'];
                             }
                            ?>
                        </th>
                        <td>
                           <?php 
                         if(isset($landscapes) && !empty($landscapes)){
                            foreach($landscapes as $item):
                           ?> 
                            <a onclick="getLandspace(<?php echo $item['id'];?>);" class="btn btn-xs mr5"><?php echo $item['name'];?></a>
                         <?php endforeach; }?>  
                        </td>
                    </tr>
                </table>
            </form>
        </div><!-- panel-body -->
    </div>
<?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">商品列表</h4>
        </div>
        <style>
		.table-responsive img{
			max-width:100px
		}
		.table-responsive th,.table-responsive td{
			vertical-align:middle!important
		}
		.rules{
			position:relative;
			display:inline-block;
		}
		.rules+.rules{
			margin-left:20px;
			}
		.rules > span{
			color:#999;
			font-size:12px;
			cursor:pointer
		}
		.rules > div >span{
			margin:0 10px
			}
		.rules > div{
			display:none;
			position:absolute;
			top:15px;
			left:50px;
			z-index:999;
			width:500px;
			padding:10px;
			background-color:#fbf8e9;
			border:1px solid #fed202;
			border-radius:2px;
			box-shadow:0 0 10px rgba(0,0,0,.2);
		}
		.rules > div .table{
			background:none;
		}
		.rules > div .table tr > *{
			border:1px solid #e0d9b6
		}
		.rules:hover > div{
			display:block;
		}
		</style>
        <div class="table-responsive">
            <table class="table table-bordered mb30" id="tablecss">
                <thead>
                    <tr>
                        <th style="width:5%; ">票种</th>
                        <th style="text-align:left;width:17%">景区</th>
                        <th style="text-align:left;width:240px">门票名称</th>
                        <th style="text-align:left">供应商</th>
                        <th style="width:15%">游玩日期</th>
                        <th style="text-align:right;width:5%">销售价</th>
                        <th style="text-align:right;width:5%">挂牌价</th>
                        <th style="text-align:right;width:5%">散客价</th>
                        <th style="width:5%">类型</th>
                        <th style="width:5%">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach($lists as $model):
                ?> 
                    <tr>
                    	<td><?php echo $model['is_union'] == 1 ?'联票': '单票'?></td>
                        <td style="text-align:left"><?php
                        $result = Landscape::api()->lists(array("ids" => $model['scenic_id']));
                        $landspaceInfo = ApiModel::getLists($result);
                        foreach ($landspaceInfo as $value) {   
                        	echo "<a href='/ticket/show/?id=" . $value['id'] . "'>" . $value['name'] . "</a><br>";
                        }
                        ?></td>
                        <td style="text-align:left">
	                        <div class="col-md-12">
		                        <div class="pull-left"><strong><?php echo  $model['name'];?></strong></div>
		                        <div class="pull-right" data-id="<?php echo $model['id'] ?>"><?php
			                        echo isset($model['favor']) && $model['favor'] == 1
			                            ? '<a class="bun fav fav-done" href="javascript:;" title="取消收藏">已收藏</a>'
				                        : '<a class="bun fav" href="javascript:;" title="加入收藏">收藏</a>';
		                        ?></div>
	                        </div>
	                        <div class="col-md-12">
		                        <div class="pull-left">
			                        <div class="rules"><span>订票规则</span>
				                        <div class="table-responsive">
					                        <table class="table table-bordered mb30">
						                        <?php echo $model['remark'];?>
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
		                        <div class="pull-right" data-id="<?php echo $model['id'] ?>" data-fat="<?php echo $model['fat_price'] ?>" data-group="<?php echo $model['group_price'] ?>"><?php
			                        echo isset($model['sub']) && $model['sub'] == 1
				                        ? '<a class="bun sub sub-done" href="javascript:;" title="取消订阅">已订阅</a>'
				                        : '<a class="bun sub" href="javascript:;" title="加入订阅">订阅</a>';
			                        ?></div>
	                        </div>
                        </td>
                        <td style="text-align:left">
                            <?php
                              $organ = Organizations::api()->show(array('id'=>$model['organization_id']));
                              echo isset($organ['body']['name'])?$organ['body']['name']:"";
                            ?></td>
                        <td>
                        	<?php 
                        		$time = explode(',',$model['date_available']);
                                        if(!empty($time[0]) && !empty($time[1])){
                                            echo date('m月d日',$time[0]) . '~' .date('m月d日',$time[1]);
                                        }else{
                                            echo '';
                                        }
                        		
                        	?>
                        </td>
                        <td style="text-align:right"><del><?php echo $model['sale_price'];?></del></td>
                        <td style="text-align:right"><del><?php echo $model['listed_price'];?></del></td>
                        <td style="text-align:right" class="text-success"><?php echo number_format($model['fat_price'],2);?></td>
                        <td><?php echo $model['type']?'任务单':'电子票';?></td>
                        <td>
                            <a class="btn btn-success btn-xs" href=".bs-example-modal-lg" onclick="buy('<?php echo $model['id'] ?>','<?php echo $model['organization_id']?>');" data-toggle="modal">购买</a>
                        </td>
                    </tr>
                 <?php endforeach;?>   
                </tbody>
            </table>

        </div>

          <div style="text-align:center" class="panel-footer">
            <div id="basicTable_paginate" class="pagenumQu">
                <?php
                $this->widget('CLinkPager', array(
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

<script type="text/javascript">
function buy(id,supplier_id){
     document.getElementById('verify-modal-buy').innerHTML = '';
     //$('#verify-modal-buy').html('');
        $.get('/ticket/buy/?price_type=0&id='+id+'&supplier_id='+supplier_id, function(data) {
            $('#verify-modal-buy').html(data);
        });
}
// <!--购买票结束-->

$("#ffsub").click(function(){
    jqname = $('input[name="jqname"]').val(); 
    var url = "/ticket/sale?province_id="+pid+"&jqname="+jqname+"&type="+typ;
   window.location.href = url;
    //$("#formsub").submit();
});


function province(provinceid){
   // alert($(this));
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
























