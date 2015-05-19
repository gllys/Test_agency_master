<?PHP
    use common\huilian\utils\Format;
    $isBg = false;
    $mergeDownNum=0;

if (isset($lists['data']) && !empty($lists['data'])):
    foreach ($lists['data'] as $blotter):
        $isBg=!$isBg;
        $mergeDownString =  ($mergeDown>0)?" ss:MergeDown=\"$mergeDown\"":"";

?>
        <Row ss:Height="20">
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo date('Y年m月d日 H:i:s', $blotter['created_at']) ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php
                    $rs = Users::model()->find('id=:id', array(':id' => $blotter['op_id']));
                    if (!empty($rs)) {
                        if (!empty($rs->name)) {
                            echo $rs->name;
                        } else {
                            echo $rs->account;
                        }
                    }?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $mode_type[$blotter['mode']];?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo $status_labels[$blotter['type']];?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s13\"":"\"s16\""; echo$mergeDownString;?>>
                <Data ss:Type="Number"><?php echo number_format($blotter['amount'], 2);?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\"";?>>
                <Data ss:Type="String"><?php echo $blotter['id']; ?></Data>
            </Cell>
            <Cell ss:StyleID=<?= $isBg?"\"s11\"":"\"s14\""; echo$mergeDownString;?>>
                <Data ss:Type="String"><?php echo empty($blotter['bill_id']) ? '' : $blotter['bill_id']; ?></Data>
            </Cell>

        </Row>

             <?PHP

            endforeach;
            endif;


        ?>