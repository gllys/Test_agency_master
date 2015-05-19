        <?PHP 
        if ($num>0):
           
        ?>
        <Row></Row>
        <Row ss:StyleID="s7">
            <Cell ss:StyleID="s18" ss:MergeAcross="255">
                <ss:Data ss:Type="String" xmlns="http://www.w3.org/TR/REC-html40"><Font html:Color="#000000">合计：共<?=$num;?>条订单</Font></ss:Data>
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
    <AutoFilter x:Range="R1C1:R1C10" xmlns="urn:schemas-microsoft-com:office:excel"/>
</Worksheet>
</Workbook>
