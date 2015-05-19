        <?PHP 
        $isBg = false;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $order):
                $isBg=!$isBg;
        ?>
        <Row ss:Height="20">
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php if(isset($order['supplier_id'])){echo isset($supply_labels[$order['supplier_id']])?$supply_labels[$order['supplier_id']]:"";}elseif(isset($order["landscape_ids"])){echo isset($landscape_labels[$order["landscape_ids"]])?$landscape_labels[$order["landscape_ids"]]:"";} //供应商?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['order_num'];//订单数量?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['person_num'];//订购人数?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['used_person_num'];//已使用人数?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['unused_person_num'];//未使用人数?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['refunded_person_num'];//退款人数?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['amount'];//订单金额?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['receive_amount'];//收入金额?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['refunded'];//退款金额?></Data>
            </Cell>
        </Row>
        <?PHP 
        endforeach;
	endif;
        ?>