<?php
use common\huilian\utils\Format;
    $file_name = "订单列表_".Format::date(time())."_".rand(100000, 999999);
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
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63" ss:Span="1"/>
        <!--退款-->
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63" ss:Span="1"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63" ss:Span="1"/>
        
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="89.25"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="52.5"/>
        <!--订单六种状态开始-->
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="115.5"/>
        <!--订单六种状态结束-->  
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="105"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="63"/>
        <Column ss:StyleID="Default" ss:AutoFitWidth="0" ss:Width="105"/>
        <Row ss:Height="24">
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">订单号</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">门票名称</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">取票人</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">取票人手机号</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">预定日期</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">游玩日期</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">入园日期</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">预定票数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">未使用票数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">已使用票数</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">退款中</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">已退款</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">支付类型</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">支付金额</Data>
            </Cell>
            <!--订单六种状态开始-->
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">支付状态</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">结算状态</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">退款状态</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">使用状态</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">审核状态</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">取消状态</Data>
            </Cell>
             <!--订单六种状态结束-->  
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">景区</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">分销商</Data>
            </Cell>
            <Cell ss:StyleID="s10">
                <Data ss:Type="String">备注</Data>
            </Cell>
        </Row>