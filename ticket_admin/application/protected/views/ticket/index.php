<?php
$this->breadcrumbs = array('门票管理','发布门票');
?>
<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <div style="display: none;" class="panel-btns">
                <a data-original-title="" href="" class="panel-minimize tooltips" data-toggle="tooltip"
                   title=""><i class="fa fa-minus"></i></a>
                <a data-original-title="" href="" class="panel-close tooltips" data-toggle="tooltip"
                   title=""><i class="fa fa-times"></i></a>
            </div>
            <!-- panel-btns -->
            <h4 class="panel-title">景区查询</h4>
        </div>
        <div class="panel-body">
            <form class="form-inline">
                <div class="">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">景区名称:</label>

                        <div class="col-sm-9">
                            <select data-placeholder="Choose One" style="width:600px;padding:0 10px;" id="distributor-select" name="jq">
                                <option value="">请输入景区名称</option>
                                <?php
                                $param['status'] = 1;
                                $param['organization_id'] = YII::app()->user->org_id;
                                $rs = Landscape::api()->lists($param);
                                $data = ApiModel::getLists($rs);
                                foreach ($data as $item) {
                                    ?>
                                    <option value="<?php echo $item['id']; ?>"  <?php
                                    if(isset($param['scenic_id']) &&  !empty($param['scenic_id'])){
                                        echo    $item['id'] == $param['scenic_id']?"selected":'';
                                    }?>><?php echo $item['name']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- form-group -->
                </div>

            </form>
        </div>
        <!-- panel-body -->
    </div>




    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">您要发布门票的景区为</h4>
        </div>

        <div class="panel-body">
            <h4 style="text-align:center;margin-bottom:40px" id="selected_landscape">请选择一个景区</h4>

            <div class="row">
                <div class="col-md-6">
                    <a id="add-electronic" style="width:100%;height:300px;line-height:300px;font-size:24px"
                                         class="btn btn-success btn-bordered" href="javascript:alert('请先选择要发布门票的景区');">发布电子票</a></div>
                <!--<div id="add-task" class="col-md-6"><a style="width:100%;height:300px;line-height:300px;font-size:24px" class="btn btn-info btn-bordered" href="">发布任务单</a></div>-->
            </div>
        </div>
    </div>


</div><!-- contentpanel -->

<script>
    jQuery(document).ready(function() {
        !function() {
            $('#distributor-select').change(function() {
                var id = $(this).val();
                var name = "请输入要发布门票的景区";
                $('#add-electronic,#add-task').attr('href',"javascript:void(0);")
                    .bind('click',function(){
                        alert('请先选择要发布门票的景区');return false;
                    });
                if(id!=0&&typeof id!=undefined){
                    name = $(this).find('option:selected').text();
                    $('#selected_landscape').text(name);
                    $('#add-electronic,#add-task').unbind('click');
                    $('#add-electronic').attr('href','/ticket/electronic/index?id='+id);
                    $('#add-task').attr('href','/ticket/task/index?id='+id);
                }
            })
        }()

        // Select2
        jQuery("#distributor-select, #select-multi, #through-tickets-select").select2();
        jQuery('.select2').select2({
            minimumResultsForSearch: -1
        });

        jQuery('select option:first-child').text('');

    });

//    $(document).ready(function() {
//        $("#distributor-select").select2({
//            placeholder: "请输入要发布门票的景区",
//            minimumInputLength: 1,
//            ajax: {
//                url: "/ajaxServer/landscapes",
//                dataType: 'json',
//                data: function (term, page) {
//                    return {
//                        term: term,
//                        page_limit: 20
//                    };
//                },
//                results: function (data, page) {
//                    return {results: data.results};
//                },
//                type:'post'
//            },
//            formatSelection: function(result){
//                var id = result.id;
//                var name = "请输入要发布门票的景区";
//                $('#add-electronic,#add-task').attr('href',"javascript:void(0);")
//                    .bind('click',function(){
//                        alert('请先选择要发布门票的景区');return false;
//                    });
//                if(id!=0&&id!=undefined){
//                    name = result.text;
//                    $('#selected_landscape').text(name);
//                    $('#add-electronic,#add-task').unbind('click');
//                    $('#add-electronic').attr('href','/ticket/electronic/index?id='+id);
//                    $('#add-task').attr('href','/ticket/task/index?id='+id);
//                }
//                return name;
//            },
//            dropdownCssClass: "bigdrop"
//        });
//    });
</script>
