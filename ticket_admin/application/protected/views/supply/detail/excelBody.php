        <?PHP 
        $isBg = false;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $order):
                $isBg=!$isBg;
        //ss:MergeDown="2"
        //<Row ss:AutoFitHeight="0">
        //<Cell ss:Index="4" ss:StyleID="s65"><Data ss:Type="Number">2</Data></Cell>
        //</Row>
        ?>
        <Row ss:Height="20">
            <Cell ss:StyleID=<?php echo $isBg?"\"s12\"":"\"s15\"";?>>
                <Data ss:Type="String"><?php echo $order['day'];//日期?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['order_num'];//订单数量?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['person_num'];//订购人数?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['used_person_num'];//已使用人数?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['unused_person_num'];//未使用人数?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['refunded_person_num'];//退款人数?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['amount'];//订单金额?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['receive_amount'];//收入金额?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['refunded'];//退款金额?></Data>
            </Cell>
        </Row>
        <?PHP 
        endforeach;
	endif;
        ?>