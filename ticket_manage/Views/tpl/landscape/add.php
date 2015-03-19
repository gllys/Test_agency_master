<!DOCTYPE html>
<html>
    <?php get_header(); ?>
    <body>
        <style>
            #allmap{ width:520px; height:350px;}
            .tangram-suggestion-main{ z-index: 9999;}
        </style>

        <?php get_top_nav(); ?>
        <div class="sidebar-background">
            <div class="primary-sidebar-background"></div>
        </div>
        <?php get_menu(); ?>

        <div class="main-content">
            <?php get_crumbs(); ?>
            <div id="show_msg">

            </div>

            <div class="container-fluid padded">
                <div class="box">
                    <div class="box-header">
                        <span class="title"><i class="icon-edit"></i> 修改景区</span>
                    </div>
                    <div class="box-content">
                        <form action="index.php?c=landscape&a=save" method="post" id="scenic_add_form" class="fill-up">
                            <div class="row-fluid">
                                <div class="span6">
                                    <ul class="padded separate-sections">
                                        <input type="hidden" name="pageType" value="<?= $pageType ?>">
                                        <input type="hidden" name="type" value="landscape">
                                        <input type="hidden" name="id" value="<?php echo $scenicInfo['id'] ?>">
                                        <li class="input">
                                            <label>景区名称:<span class="note"></span>
                                                <?php echo $scenicInfo['name'] ?>
                                            </label>
                                        </li>
                                        <li class="input">
                                            <label>景区级别:<span class="note">*</span>
                                                
                                              
                                                <select class="form-control" name="landscape_level_id" >
                                                    <option value="">请选择景区级别</option>
                                                    <?php if ($levelInfo): ?>
                                                        <?php foreach ($levelInfo as $level): ?>
                                                            <option value="<?php echo $level['id']; ?>" <?php if ($level['id'] == $scenicInfo['landscape_level_id']): ?>selected="selected"<?php endif; ?>>
                                                                <?php echo $level['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                              
                                            </label>
                                        </li>
                                        <li>
                                            <label>所在地区：</label>
                                            <div class="row-fluid">
                                                <div class="span4">
                                                    <select class="uniform" name="province_id" id="province">
                                                        <option value="<?php echo $provinceInfo['id'] ? $provinceInfo['id'] : ''; ?>"><?php echo $provinceInfo['name'] ? $provinceInfo['name'] : '省'; ?></option>
                                                    </select>
                                                </div>
                                                <div class="span4">
                                                    <select class="uniform" name="city_id" id="city">
                                                        <option value="<?php echo $cityInfo['id'] ? $cityInfo['id'] : ''; ?>"><?php echo $cityInfo['name'] ? $cityInfo['name'] : '市'; ?></option>
                                                    </select>
                                                </div>
                                                <div class="span4">
                                                    <select class="uniform" name="district_id" id="area">
                                                        <option value="<?php echo $districtInfo['id'] ? $districtInfo['id'] : ''; ?>"><?php echo $districtInfo['name'] ? $districtInfo['name'] : '县'; ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </li>


                                        <li class="input">
                                            <label>详细地址:<span class="note">*</span>
                                                <br/>
                                               
                                                      <input type="text" name="address" value="<?php echo $scenicInfo['address']; ?>" placeholder="" class="form-control validate[required,minSize[1],maxSize[100]]">
                                               
                                                    <?php echo $scenicInfo['address']; ?>
                                             
                                            </label>
                                        </li>
                                    </ul>
                                </div>

                                <div class="span6">
                                    <ul class="padded separate-sections">
                                        <li class="input">
                                            <label>景区介绍:<span class="note">*</span>
                                                
                                                     <textarea  style="text-indent: 2em;" data-prompt-position="topLeft"  placeholder=""  name="biography" rows="5" class="form-control validate[required,minSize[1],maxSize[5000]]" ><?php echo $scenicInfo['biography']; ?></textarea>
                                                
                                                   <?php echo $scenicInfo['biography']; ?>
                                            
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                            <!-- 企业logo开始 -->
                            <div class="row-fluid">
                                <div class="span6">
                                    <ul class="padded separate-sections">
                                        <li class="input">
                                            <label>景区图片<span class="note"></span>
                                                <a id="a_logo_id" href="" class="editable-empty thumbs">
                                                    <img id="img_logo_id" src="<?php
                                                    if ($scenicInfo['images']) {
                                                        echo $scenicInfo['images'][0]['url'];
                                                    }
                                                    ?>" height="100" width="100" />
                                                </a>
                                                <input type="hidden" name="images[id]" value="<?php
                                                    if ($scenicInfo['images']) {
                                                        echo $scenicInfo['images'][0]['id'];
                                                    }
                                               ?>">
                                                <input type="hidden" id="images1" name="images[url]" value="">
                                                <a id="upload-button-logo" href="#upload-show2" data-toggle="modal" class="btn btn-blue"><i class="icon-picture"></i> &nbsp;请选择上传图片..</a>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="span6"></div>
                            </div>
                            <!-- 企业logo结束 -->

                            <div class="form-actions">
                                <button class="btn btn-lg btn-blue" type="button" id="btn-add">保存</button>
                            </div>

                        </form>


                        <style>
                            .agencies{
                                display:none
                            }
                            ul{
                                margin:0;
                                padding:10px 0;
                                list-style:none
                            }
                            .icheckbox_flat-aero{
                                margin-right:5px;
                                vertical-align:middle
                            }
                            #banks .span6{
                                clear:both;
                                margin-left:0
                            }
                            .table-normal tbody td a{
                                text-decoration:none
                            }
                            .modal-body table{
                                width:100%
                            }
                            .modal-body .search{
                                margin:0;
                                position:relative;
                            }
                            .datalist{
                                position:absolute;
                                top:25px;
                                width: 210px;
                                background-color: #fff;
                                box-shadow: 0 1px #ccc, 1px 0 #ccc, -1px 0 #ccc, 0 -1px #ccc;
                                overflow: hidden;
                                visibility: hidden;
                            }
                            .search input:focus + .datalist {
                                visibility:visible;
                            }

                            #scenics tr:first-child .del-scenic{
                                display:none
                            }
                        </style>



                    </div>
                </div>
            </div>
        </div>


        <div id="upload-show1" class="modal hide fade">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h6 id="modal-formLabel">营业执照上传</h6>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    <form action="index.php?c=ajax&a=fileUpload" method="post" id="licence-form1">
                        <span class="span9">
                            <input type="file" name="attachments">
                        </span>
                        <span class="span3">
                            <button class="btn btn-blue" data-dismiss="modal" id="licence_id"><i class="icon-cloud-upload"></i> 上传图片</button>
                        </span>

                    </form>
                </div>
            </div>
        </div>


        <div id="upload-show2" class="modal hide fade">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h6 id="modal-formLabel">企业logo上传</h6>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    <form action="index.php?c=ajax&a=fileUpload" method="post" id="logo-form1">
                        <span class="span9">
                            <input type="file" name="attachments">
                        </span>
                        <span class="span3">
                            <button class="btn btn-blue" data-dismiss="modal" id="logo_id"><i class="icon-cloud-upload"></i> 上传图片</button>
                        </span>
                    </form>
                </div>
            </div>
        </div>



        <!-- Modal -->
        <div id="viewmap" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewmapLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="viewmapLabel">查询经纬度</h3>
            </div>
            <div class="modal-body">

                <div id="allmap"></div>
                <div id="r-result">搜索关键字:<input type="text" id="suggestId" size="20" value="" style="width:150px;" /></div>
            </div>
        </div>

        <script src="http://api.map.baidu.com/api?v=2.0&ak=0A5c95ed872caf1babb6305799e7d4ee"></script>
        <script type="text/javascript">
            $(function() {


                // 百度地图API功能
                var map = new BMap.Map("allmap");
                map.centerAndZoom("上海", 12);

                var top_left_navigation = new BMap.NavigationControl();
                var mapType1 = new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP]});

                map.addControl(mapType1);
                map.addControl(top_left_navigation);

                //单击获取点击的经纬度
                map.addEventListener("click", function(e) {
                    alert('已设置：' + e.point.lat + "," + e.point.lng);
                    $('#lat')[0].value = e.point.lat;
                    $('#lng')[0].value = e.point.lng;
                });

                //建立一个自动完成的对象
                var ac = new BMap.Autocomplete(
                        {"input": "suggestId"
                            , "location": map
                        });
                //鼠标放在下拉列表上的事件
                ac.addEventListener("onhighlight", function(e) {
                });

                //鼠标点击下拉列表后的事件
                ac.addEventListener("onconfirm", function(e) {
                    var _value = e.item.value;
                    var myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
                    setPlace(myValue);
                });

                function setPlace(myValue) {
                    map.clearOverlays();    //清除地图上所有覆盖物
                    function myFun() {
                        var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
                        map.centerAndZoom(pp, 18);
                        map.addOverlay(new BMap.Marker(pp));    //添加标注
                    }
                    var local = new BMap.LocalSearch(map, {//智能搜索
                        onSearchComplete: myFun
                    });
                    local.search(myValue);
                }

            });
        </script>


        <script src="Views/js/jquery.validationEngine-zh-CN.js"></script>
        <script src="Views/js/plugins/jquery.form.js" type="text/javascript" charset="utf-8"></script>
        <script src="Views/js/common/common.js"></script>
        <script src="Views/js/landscape/add.js?1"></script>
        <script>
        $(function(){
            $.get('index.php?c=ajax&a=getAreaChildByCode',{"code":0, "current":<?php echo $provinceInfo['id'] ? $provinceInfo['id'] : 0; ?>}, function(data){
                $('#province').html(data);
            });
        });
        </script>
