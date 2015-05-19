        <?PHP
        $isBg = false;
        $mergeDownNum=0;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $item):
                $isBg=!$isBg;
                $verify_items = isset($order['verify_items']) ? $order['verify_items'] : array();
                $mergeDown = count($verify_items)-1;
                $mergeDownNum+=$mergeDown;
                $mergeDownString =  ($mergeDown>0)?" ss:MergeDown=\"$mergeDown\"":"";
        ?>

        <Row ss:Height="20">
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";//景区?>>
                <Data ss:Type="String"><?php echo $item['landscape_name'];?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;//销售数量?>>
                <Data ss:Type="Number"><?php echo $item['tickets_total']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;//销售额?>>
                <Data ss:Type="Number"><?php echo $item['sale_money']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;//入园数?>>
                <Data ss:Type="Number"><?php echo $item['used_total']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;//退票数?>>
                <Data ss:Type="Number"><?php echo $item['refunded_total']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;//退票总额?>>
                <Data ss:Type="Number"><?php echo $item['refund_money']?></Data>
            </Cell>
        </Row>
        <?php
        if ($mergeDown>0):
            for($i=0;$i<$mergeDown ;$i++):
        ?>
        <Row>
            <Cell ss:Index="9" ss:StyleID="s11">
                <Data ss:Type="String"><?=Format::date($verify_items[$i+1]["use_time"]);?></Data>
            </Cell>
        </Row>
        <?PHP 
        endfor;
	endif;
        
        endforeach;
	endif;
        ?>