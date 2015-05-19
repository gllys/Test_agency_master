<?PHP
    use common\huilian\utils\Format;
    $isBg = false;
    $mergeDownNum=0;

   if(isset($lists['data'])):
     foreach ($lists['data'] as  $value):
        $isBg=!$isBg;
        $mergeDownString =  ($mergeDown>0)?" ss:MergeDown=\"$mergeDown\"":"";

?>
        <Row ss:Height="20">
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $value['id']; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $value['bill_type'] == 1||$value['bill_type'] == 4 ? '汇联' : $value['agency_name']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo date('Y年m月d日',$value['created_at'])?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php if($value['bill_type'] == 1){
                        echo "在线支付";
                    }elseif ($value['bill_type'] == 2) {
                        echo "信用支付";
                    }elseif ($value['bill_type'] == 4) {
                        echo "平台支付";
                    }else{
                        echo "储值支付";
                    }?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo $value['bill_amount']?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $value['bill_num'].'张';?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <?php if($value['pay_status'] == 1 && $value['bill_amount'] > 0):?>
                    <Data ss:Type="String">已打款</Data>
                <?php elseif($value['bill_amount'] == 0):?>
                    <Data ss:Type="String">无需打款</Data>
                <?php else:?>
                    <Data ss:Type="String">未打款</Data>
                <?php endif;?>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <?php if($value['receipt_status'] == 1 && $value['bill_amount'] > 0):?>
                    <Data ss:Type="String">已收款</Data>
                <?php elseif($value['bill_amount'] == 0):?>
                    <Data ss:Type="String">无需收款</Data>
                <?php else:?>
                    <Data ss:Type="String">未收款</Data>
                <?php endif;?>

            </Cell>
        </Row>

             <?PHP

            endforeach;
            endif;


        ?>