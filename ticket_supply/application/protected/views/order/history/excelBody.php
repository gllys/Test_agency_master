        <?PHP
        use common\huilian\utils\Format;
        $isBg = false;
        $mergeDownNum=0;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $order):
                $isBg=!$isBg;
                $verify_items = isset($order['verify_items']) ? $order['verify_items'] : array();
                $mergeDown = count($verify_items)-1;
                $mergeDownNum+=$mergeDown;
                $mergeDownString =  ($mergeDown>0)?" ss:MergeDown=\"$mergeDown\"":"";
                
        //ss:MergeDown="2"
        //<Row ss:AutoFitHeight="0">
        //<Cell ss:Index="4" ss:StyleID="s65"><Data ss:Type="Number">2</Data></Cell>
        //</Row>
        ?>
        <Row ss:Height="20">
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['id']; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['name'] ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['owner_name'] ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['owner_mobile']; ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo Format::date($order['created_at']) ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['use_day'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo count($verify_items)>0?Format::date($verify_items[0]["use_time"]): '' ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo $order['nums'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo $order['nums'] - $order['used_nums'] - $order['refunding_nums'] - $order['refunded_nums'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo $order['used_nums'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo $order['refunding_nums'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo $order['refunded_nums'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo empty($payTypes[$order['pay_type']]) ? '' : $payTypes[$order['pay_type']] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo number_format($order['amount'], 2) ?></Data>
            </Cell>
            
             <!--订单六种状态开始-->
             <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                 <Data ss:Type="String"><?php echo Order::$payStatus[$order['pay_status']]; ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                 <Data ss:Type="String"><?php echo Order::$billStatus[$order['bill_status']]; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo Order::$refundStatus[$order['refund_status']]; ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                 <Data ss:Type="String"><?php echo Order::$useStatus[$order['use_status']]; ?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                 <Data ss:Type="String"><?php echo Order::$auditStatus[$order['audit_status']]; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo Order::$cancelStatus[$order['cancel_status']]; ?></Data>
            </Cell>
             <!--订单六种状态结束-->  
             
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php
                                            $landscapeArr = explode(',', $order['landscape_ids']);
                                            foreach ($landscapeArr as $landscapeId) {
                                                echo (isset($landscape_lists[$landscapeId]) ? $landscape_lists[$landscapeId] : "") . " ";
                                            }
                                            ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['distributor_name'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <?php echo UbbToHtml::toXml(UbbToHtml::Entry('<Data ss:Type="String">'.$order['remark'].'</Data>',time())); ?>
            </Cell>
        </Row>
        <?php
        if ($mergeDown>0):
            for($i=0;$i<$mergeDown ;$i++):
        ?>
        <Row>
            <Cell ss:Index="7" ss:StyleID="s11">
                <Data ss:Type="String"><?=Format::date($verify_items[$i+1]["use_time"]);?></Data>
            </Cell>
        </Row>
        <?PHP 
        endfor;
	endif;
        
        endforeach;
	endif;
        
        
        ?>