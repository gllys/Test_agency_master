        <?PHP 
        $isBg = false;
        if (isset($lists)):
            foreach ($lists["data"] as $item):
                $isBg=!$isBg;
        ?>
        
        <Row ss:Height="20">
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="Number"><?php echo $item['id'];//用户编号?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s12\"":"\"s15\"";?>>
                <Data ss:Type="String"><?php echo date('Y-m-d H:i:s',$item['created_at']);//申请时间?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $item['org_name'];//用户名称?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $item['org_role'] ? '供应商' : '分销商'; //用户角色?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $item['op_account'];//用户帐号?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String" x:Ticked="1"><?php echo $item['trade_type'] == '1' ? ($item['pay_type'] == 0 ? '' : '+') : ($item['trade_type'] == '2' ? '' : ($item['trade_type'] == '3' ? '+' : ($item['trade_type'] == '4' ? '-' : ''))); ?><?php echo $item['money'];//金额?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $item['trade_type'] == '1' ? ($item['pay_type'] == 0 ? '平台支付' : '在线支付') : ($item['trade_type'] == '2' ? '退款' : ($item['trade_type'] == '3' ? '充值' : ($item['trade_type'] == '4' ? '提现' : '应收账款')));//交易类型?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $item['union_money'];//可提现余额?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo $item['frozen_money'];//冻结余额?></Data>
            </Cell>
            <Cell ss:StyleID=<?php echo $isBg?"\"s13\"":"\"s16\"";?>>
                <Data ss:Type="Number"><?php echo ($item['frozen_money'] + $item['union_money']);//帐户总余额?></Data>
            </Cell>
        </Row>
        <?PHP 
        endforeach;
	endif;
        ?>