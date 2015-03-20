        <?PHP
        use common\huilian\utils\Format;
        $isBg = false;
        $mergeDownNum=0;
        if (isset($lists['data'])):
            foreach ($lists['data'] as $blotter):
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
                <Data ss:Type="String"><?php echo $blotter['id']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo date('y年m月d日',$blotter['created_at'])?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $blotter['apply_username']?></Data>
            </Cell>
            
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $blotter['apply_account']?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php if($blotter['status']=='1') echo "-"; ?><?php echo number_format($blotter['money'],2)?></Data>
            </Cell>
            
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String">提现</Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo  $status_labels[$blotter['status']];?></Data>
            </Cell>
             <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo  number_format($blotter['union_money'] - $blotter['money'],2);?></Data>
             </Cell>
        </Row>
        <?php
        endforeach;
	endif;
        ?>