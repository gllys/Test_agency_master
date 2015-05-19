        <?PHP 
        use common\huilian\utils\Format;
        $isBg = false;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $order):
                $isBg=!$isBg;
                if(Format::date($order['created_at'])==Format::date(time())) continue;
        //ss:MergeDown="2"
        //<Row ss:AutoFitHeight="0">
        //<Cell ss:Index="4" ss:StyleID="s65"><Data ss:Type="Number">2</Data></Cell>
        //</Row>
        ?>
        <Row ss:Height="20">
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order['id']; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order['distributor_name'];//分销商名称 ?></Data>
            </Cell>
             <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order['name'];//门票名称 ?></Data>
            </Cell>
             <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php
                                            $landscapeArr = explode(',', $order['landscape_ids']);
                                            foreach ($landscapeArr as $landscapeId) {
                                                echo (isset($landscape_labels[$landscapeId]) ? $landscape_labels[$landscapeId] : "") . " ";
                                            }//景区
                                            ?></Data>
            </Cell>
             <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order['supplier_name'];//供应商名称 ?></Data>
            </Cell>
             <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo Format::date($order['created_at']);//生成时间 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="String"><?php echo Format::date($order['pay_at']);//支付时间 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="String"><?php echo $order['use_day'];//游玩时间 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $order['nums'];//张数 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order['price'];//单价 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['amount'];//结算金额 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order_types[$order['type']];//订单类型 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $order_kind_types[$order['kind']];//订单类别 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo empty($payTypes[$order['pay_type']]) ? '' : $payTypes[$order['pay_type']];//支付方式 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['payed'];//支付金额 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo Format::money($order['pay_rate']*$order['payed']); //手续费?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $order['refunded'];//退款金额 ?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $status_labels[$order['status']]; //订单状态?></Data>
            </Cell>
        </Row>
        <?PHP 
        endforeach;
	endif;
       
        ?>