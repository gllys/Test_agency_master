<?php
     $file_name = $typeNames[$type].'销量统计';
     header ( "Content-type:text/xml;charset=utf-8" );
     $str = mb_convert_encoding($file_name, 'gbk', 'utf-8');   
     header('Content-Disposition: attachment;filename="' .$str . '.xml"');      
     header('Cache-Control:must-revalidate,post-check=0,pre-check=0');        
     header('Expires:0');         
     header('Pragma:public');
?>
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882">
                <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
                <Author>票台</Author>
                <LastAuthor>票台</LastAuthor>
                <Created>2015-02-02T13:28:00Z</Created>
                <LastSaved>2015-03-05T03:58:07Z</LastSaved>
            </DocumentProperties>
            <CustomDocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
                <KSOProductBuildVer dt:dt="string">2052-9.1.0.4648</KSOProductBuildVer>
            </CustomDocumentProperties><OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
    <Colors>
        <Color>
            <Index>0</Index>
            <RGB>#000000</RGB>
        </Color>
        <Color>
            <Index>1</Index>
            <RGB>#000000</RGB>
        </Color>
        <Color>
            <Index>2</Index>
            <RGB>#31869B</RGB>
        </Color>
        <Color>
            <Index>3</Index>
            <RGB>#D9D9D9</RGB>
        </Color>
    </Colors>
</OfficeDocumentSettings>
<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
    <WindowWidth>20400</WindowWidth>
    <WindowHeight>8520</WindowHeight>
    <ProtectStructure>False</ProtectStructure>
    <ProtectWindows>False</ProtectWindows>
</ExcelWorkbook>
<Styles>
    <Style ss:ID="s2" ss:Name="货币">
        <NumberFormat ss:Format="_ &quot;￥&quot;* #,##0.00_ ;_ &quot;￥&quot;* \-#,##0.00_ ;_ &quot;￥&quot;* &quot;-&quot;??_ ;_ @_ "/>
    </Style>
    <Style ss:ID="Default" ss:Name="Normal">
        <Alignment ss:Vertical="Bottom"/>
        <Borders/>
        <Font ss:FontName="Calibri" x:CharSet="0" ss:Size="11" ss:Color="#000000"/>
        <NumberFormat/>
    </Style>
    <Style ss:ID="s1" ss:Name="千位分隔">
        <NumberFormat ss:Format="_ * #,##0.00_ ;_ * \-#,##0.00_ ;_ * &quot;-&quot;??_ ;_ @_ "/>
    </Style>
    <Style ss:ID="s5" ss:Name="货币[0]">
        <NumberFormat ss:Format="_ &quot;￥&quot;* #,##0_ ;_ &quot;￥&quot;* \-#,##0_ ;_ &quot;￥&quot;* &quot;-&quot;_ ;_ @_ "/>
    </Style>
    <Style ss:ID="s3" ss:Name="千位分隔[0]">
        <NumberFormat ss:Format="_ * #,##0_ ;_ * \-#,##0_ ;_ * &quot;-&quot;_ ;_ @_ "/>
    </Style>
    <Style ss:ID="s4" ss:Name="百分比">
        <NumberFormat ss:Format="0%"/>
    </Style>
    <Style ss:ID="s7">
        <Alignment ss:Horizontal="Left"/>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior/>
        <Protection/>
    </Style>
    <Style ss:ID="s10">
        <Alignment ss:Horizontal="Center"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
            <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000" ss:Bold="1"/>
        <Interior ss:Color="#31869B" ss:Pattern="Solid"/>
        <Protection/>
    </Style>
    <Style ss:ID="s11">
        <Alignment ss:Horizontal="Center"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior/>
        <Protection/>
    </Style>
    <Style ss:ID="s12">
        <Alignment ss:Horizontal="Center"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior/>
        <NumberFormat ss:Format="yyyy/m/d\ h:mm"/>
        <Protection/>
    </Style>
    <Style ss:ID="s13">
        <Alignment ss:Horizontal="Center" ss:WrapText="1"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior/>
        <NumberFormat ss:Format="0.00_);[Red]\(0.00\)"/>
        <Protection/>
    </Style>
    <Style ss:ID="s14">
        <Alignment ss:Horizontal="Center"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
        <Protection/>
    </Style>
    <Style ss:ID="s15">
        <Alignment ss:Horizontal="Center"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
        <NumberFormat ss:Format="yyyy/m/d\ h:mm"/>
        <Protection/>
    </Style>
    <Style ss:ID="s16">
        <Alignment ss:Horizontal="Center" ss:WrapText="1"/>
        <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
            <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
        </Borders>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
        <NumberFormat ss:Format="0.00_);[Red]\(0.00\)"/>
        <Protection/>
    </Style>
    <Style ss:ID="s17">
        <Alignment ss:Vertical="Bottom" ss:WrapText="1"/>
        <Font ss:FontName="Calibri" x:CharSet="0" ss:Size="11" ss:Color="#000000"/>
        <Interior/>
        <Protection/>
    </Style>
    <Style ss:ID="s18">
        <Alignment ss:Horizontal="Left"/>
        <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
        <Interior/>
        <Protection/>
    </Style>
</Styles>
<Worksheet ss:Name="Worksheet">
    <Table x:FullColumns="1" x:FullRows="1" ss:StyleID="Default" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="15">
        <Column ss:Index="1" ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="105"/>
        <Column ss:Index="2" ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="132"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="151.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="96.75"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="93.75"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63" ss:Span="1"/>
        <Column ss:Index="8" ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="74.25"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="88.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="79.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="52.5" ss:Span="2"/>
        <Column ss:Index="15" ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63" ss:Span="1"/>
        <Column ss:Index="17" ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="89.25"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="52.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="105"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="105"/>
        <Row ss:Height="24">
            <Cell ss:StyleID="s10">
                <Data ss:Type="String"><?= $typeNames[$type] ?></Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">订单数量</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">订购人数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">已使用人数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">未使用人数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">退款人数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">订单金额</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">收入金额</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">退款金额</Data>
            </Cell>
        </Row>