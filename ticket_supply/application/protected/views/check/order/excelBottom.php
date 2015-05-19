<?php
if (isset($num)):
        ?>
        <Row></Row>
        <Row  ss:StyleID="s7">
            <Cell ss:StyleID="s18" ss:MergeAcross="255">
                <ss:Data ss:Type="String">订单数：<?php echo isset($lists['statics']['order_nums']) ? $lists['statics']['order_nums'] : 0;?>  总人次：<?php
                            $total_nums = intval(isset($lists['statics']['total_nums']) ? $lists['statics']['total_nums'] : "0");
                            $total_refunded_nums = intval(isset($lists['statics']['total_refunded_nums']) ? $lists['statics']['total_refunded_nums'] : "0");
                            echo $total_nums - $total_refunded_nums;
                            ?>  使用人次：<?php
                            echo isset($lists['statics']['total_used_nums']) ? $lists['statics']['total_used_nums'] : 0;
                            ?>  总金额：<?php
                            $total_amount = intval(isset($lists['statics']['total_amount']) ? $lists['statics']['total_amount'] : "0");
                            $total_refunded = intval(isset($lists['statics']['total_refunded']) ? $lists['statics']['total_refunded'] : "0");
                            echo $total_amount - $total_refunded;?></ss:Data>
            </Cell>
        </Row>
        <?php
        endif;
        ?>
    </Table>
    <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
        <TabColorIndex>8</TabColorIndex>
        <PageSetup>
            <Header x:Margin="0.3"/>
            <Footer x:Margin="0.3"/>
            <PageMargins x:Left="0.698611111111111" x:Right="0.698611111111111" x:Top="0.75" x:Bottom="0.75"/>
        </PageSetup>
        <Print>
            <ValidPrinterInfo/>
            <PaperSizeIndex>9</PaperSizeIndex>
            <HorizontalResolution>600</HorizontalResolution>
            <VerticalResolution>600</VerticalResolution>
        </Print>
        <Selected/>
        <TopRowVisible>0</TopRowVisible>
        <LeftColumnVisible>0</LeftColumnVisible>
        <FreezePanes/>
        <SplitHorizontal>1</SplitHorizontal>
        <TopRowBottomPane>1</TopRowBottomPane>
        <ActivePane>2</ActivePane>
        <Panes>
            <Pane>
                <Number>3</Number>
            </Pane>
            <Pane>
                <Number>2</Number>
                <ActiveRow>12</ActiveRow>
                <ActiveCol>3</ActiveCol>
                <RangeSelection>R13C4</RangeSelection>
            </Pane>
        </Panes>
        <ProtectObjects>False</ProtectObjects>
        <ProtectScenarios>False</ProtectScenarios>
        <AllowFormatCells/>
        <AllowSizeCols/>
        <AllowSizeRows/>
        <AllowInsertRows/>
        <AllowInsertCols/>
        <AllowInsertHyperlinks/>
        <AllowDeleteCols/>
        <AllowDeleteRows/>
        <AllowSort/>
        <AllowFilter/>
        <AllowUsePivotTables/>
    </WorksheetOptions>
    <AutoFilter x:Range="R1C1:R1C13" xmlns="urn:schemas-microsoft-com:office:excel"/>
</Worksheet>
</Workbook>
