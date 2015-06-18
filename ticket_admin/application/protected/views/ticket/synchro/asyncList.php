<div class="panel panel-default">

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered mb30">
                <thead>
                <tr>
                    <ul class="list-inline">
                        <li style="font-size: 18px;font-weight: 800px;"> 发送数据详细信息</li>
                        <li> <?php echo $landscape;?></li>
                    </ul>
                </tr>
                <tr>
                    <th>同步对象</th>
                    <th>同步接口</th>
                    <th>最后同步时间</th>
                    <th>同步状态</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($menu) && !empty($push)): foreach($menu as $value):
                    if(count(array_intersect($value['items'],ArrayColumn::i_array_column($push,'model'))) == 0){
                        continue;
                    }?>
                    <tr>

                        <td><?php echo $value['label']?></td>
                        <?php
                        $names = $times = '';
                        foreach($value['model'] as $k=>$model) {
                            if ($model['push'] != '') {
                                foreach ($model['push'] as $items) {
                                    $names .= $items['name'] . '<br/>';
                                    $times .= date('Y-m-d H:i:s', $items['time']) . '<br/>';
                                }
                            }
                        }

                        ?>
                        <td><?php echo $names;?></td>
                        <td><?php echo $times;?></td>

                        <td><span class="text-success">成功</span></td>
                    </tr>
                <?php endforeach;?>
                <?php else:?>
                    <tr><td colspan="6" style="text-align: center !important;">无相关数据</td></tr>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered mb30">
                <thead>
                <tr>
                        <ul class="list-inline">
                            <li style="font-size: 18px;font-weight: 800px;"> 接受数据详细信息</li>
                            <li> <?php echo $landscape;?></li>
                        </ul>
                </tr>
                <tr>
                    <th>同步对象</th>
                    <th>同步接口</th>
                    <th>最后同步时间</th>
                    <th>同步状态</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($menu)  && !empty($pull)): foreach($menu as $value):
                    if(count(array_intersect($value['items'],ArrayColumn::i_array_column($pull,'model'))) == 0){
                        continue;
                    } if(count(array_intersect($value['items'],ArrayColumn::i_array_column($pull,'model'))) == 0){
                    continue;
                }
                    ?>
                    <tr>

                        <td><?php echo $value['label']?></td>
                        <?php
                        $pnames = $ptimes = '';
                        foreach($value['model'] as $k=>$model) {
                            if (isset($model['pull']) && $model['pull'] != '') {
                                foreach ($model['pull'] as $items) {
                                    $pnames .= $items['name'] . '<br/>';
                                    $ptimes .= date('Y-m-d H:i:s', $items['time']) . '<br/>';
                                }
                            }
                        }

                        ?>
                        <td><?php echo $pnames;?></td>
                        <td><?php echo $ptimes;?></td>

                        <td><span class="text-success">成功</span></td>
                    </tr>
                <?php endforeach;?>
                <?php else:?>
                    <tr><td colspan="6" style="text-align: center !important;">无相关数据</td></tr>
                <?php endif;?>
                </tbody>

            </table>
        </div>
    </div>
</div>
