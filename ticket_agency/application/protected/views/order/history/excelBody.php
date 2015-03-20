        <?PHP
        use common\huilian\utils\Format;
        $isBg = false;
        $mergeDownNum=0;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $order):
                $isBg=!$isBg;
                $verify_items = isset($order['verify_items']) ? $order['verify_items'] : array();
                $mergeDown = count($verify_items);
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
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['supplier_name']; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['name'] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo is_numeric($order['source']) ? $source_labels[$order['source']] : ''; ?></Data>
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
                <Data ss:Type="String"><?php echo $order['used_nums'] ? Format::date($order['updated_at']) : '' ?></Data>
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
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo empty($payTypes[$order['pay_type']]) ? '' : $payTypes[$order['pay_type']] ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo number_format($order['amount'], 2) ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $status_labels[$order['status']]; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php
                                            $landscapeArr = explode(',', $order['landscape_ids']);
                                            foreach ($landscapeArr as $landscapeId) {
                                                echo (isset($landscape_labels[$landscapeId]) ? $landscape_labels[$landscapeId] : "") . " ";
                                            }
                                            ?></Data>
            </Cell>
            
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $order['remark'] ?></Data>
            </Cell>
        </Row>
        <?php
        if ($mergeDown>0):
            foreach ($verify_items as $item):
        ?>
        <Row>
            <Cell ss:Index="9" ss:StyleID="s11">
                <Data ss:Type="String"><?=Format::date($item["use_time"]);?></Data>
            </Cell>
        </Row>
        <?PHP 
        endforeach;
	endif;
        
        endforeach;
	endif;
        
        
        ?>