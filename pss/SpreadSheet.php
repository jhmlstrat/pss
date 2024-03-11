<?php

namespace Scoring;
use DateTimeImmutable;
use ZipArchive;

class ODSSheet {
}

class SpreadSheet {

    public function __construct($year, $team) {
        $json = file_get_contents("../data/config.json");
        $confs = json_decode($json, true);
        foreach ($confs['years'] as $conf) {
            if ($conf['year'] == $year) {
                $this->config = $conf;
            }
        }
        $this->year = $year;
        $this->$team = $team;
    }

    public static function buildSpreadSheet($year, $team) {
        $inst = new SpreadSheet($year, $team);
        $za = new ZipArchive();
        $za->open('../data/' . $year . '/JHML' . strtoupper($team) . '.ods', ZipArchive::CREATE|ZipArchive::OVERWRITE); 
        $inst->createEmptyOds($za);
        $inst->addWeatherSheet($za);
        $za->close();
    }

    public function createEmptyOds($za) {
        $now = new DateTimeImmutable('UTC');
        $manifest_rdf = '<?xml version="1.0" encoding="utf-8"?>';
        $manifest_rdf .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">';
        $manifest_rdf .= '  <rdf:Description rdf:about="styles.xml">';
        $manifest_rdf .= '    <rdf:type rdf:resource="http://docs.oasis-open.org/ns/office/1.2/meta/odf#StylesFile"/>';
        $manifest_rdf .= '  </rdf:Description>';
        $manifest_rdf .= '  <rdf:Description rdf:about="">';
        $manifest_rdf .= '    <ns0:hasPart xmlns:ns0="http://docs.oasis-open.org/ns/office/1.2/meta/pkg#" rdf:resource="styles.xml"/>';
        $manifest_rdf .= '  </rdf:Description>';
        $manifest_rdf .= '  <rdf:Description rdf:about="content.xml">';
        $manifest_rdf .= '    <rdf:type rdf:resource="http://docs.oasis-open.org/ns/office/1.2/meta/odf#ContentFile"/>';
        $manifest_rdf .= '  </rdf:Description>';
        $manifest_rdf .= '  <rdf:Description rdf:about="">';
        $manifest_rdf .= '    <ns0:hasPart xmlns:ns0="http://docs.oasis-open.org/ns/office/1.2/meta/pkg#" rdf:resource="content.xml"/>';
        $manifest_rdf .= '  </rdf:Description>';
        $manifest_rdf .= '  <rdf:Description rdf:about="">';
        $manifest_rdf .= '    <rdf:type rdf:resource="http://docs.oasis-open.org/ns/office/1.2/meta/pkg#Document"/>';
        $manifest_rdf .= '  </rdf:Description>';
        $manifest_rdf .= '</rdf:RDF>';

        $manifest_xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $manifest_xml .= '<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.3" xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0">';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="/" manifest:version="1.3" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="Configurations2/" manifest:media-type="application/vnd.sun.xml.ui.configuration"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="manifest.rdf" manifest:media-type="application/rdf+xml"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="Thumbnails/thumbnail.png" manifest:media-type="image/png"/>';
        $manifest_xml .= ' <manifest:file-entry manifest:full-path="settings.xml" manifest:media-type="text/xml"/>';
        $manifest_xml .= '</manifest:manifest>';

        $meta_xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $meta_xml .= '<office:document-meta xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" office:version="1.3">';
        $meta_xml .= '  <office:meta>';
        $meta_xml .= '    <meta:generator>JHML script</meta:generator>';
        $meta_xml .= '    <meta:initial-creator>JHML</meta:initial-creator>';
        $meta_xml .= '    <meta:creation-date>' . $now->format('c') . '</meta:creation-date>';
        $meta_xml .= '    <dc:date>' . $now->format('c') . '</dc:date>';
        $meta_xml .= '    <meta:editing-duration>PT0S</meta:editing-duration>';
        $meta_xml .= '    <meta:editing-cycles>1</meta:editing-cycles>';
        $meta_xml .= '    <meta:document-statistic meta:table-count="0" meta:cell-count="0" meta:object-count="0"/>';
        $meta_xml .= '  </office:meta>';
        $meta_xml .= '</office:document-meta>';

        $mimetype = 'application/vnd.oasis.opendocument.spreadsheet';

        $styles_xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$styles_xml .= '<office:document-styles';
	$styles_xml .= '                        xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"';
	$styles_xml .= '                        xmlns:css3t="http://www.w3.org/TR/css3-text/"';
	$styles_xml .= '                        xmlns:dc="http://purl.org/dc/elements/1.1/"';
	$styles_xml .= '                        xmlns:dom="http://www.w3.org/2001/xml-events"';
	$styles_xml .= '                        xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"';
	$styles_xml .= '                        xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"';
	$styles_xml .= '                        xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"';
	$styles_xml .= '                        xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"';
	$styles_xml .= '                        xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"';
	$styles_xml .= '                        xmlns:grddl="http://www.w3.org/2003/g/data-view#"';
	$styles_xml .= '                        xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"';
	$styles_xml .= '                        xmlns:math="http://www.w3.org/1998/Math/MathML"';
	$styles_xml .= '                        xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"';
	$styles_xml .= '                        xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"';
	$styles_xml .= '                        xmlns:ooo="http://openoffice.org/2004/office"';
	$styles_xml .= '                        xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0"';
	$styles_xml .= '                        xmlns:rpt="http://openoffice.org/2005/report"';
	$styles_xml .= '                        xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"';
	$styles_xml .= '                        xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"';
	$styles_xml .= '                        xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"';
	$styles_xml .= '                        xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"';
	$styles_xml .= '                        xmlns:xhtml="http://www.w3.org/1999/xhtml"';
	$styles_xml .= '                        xmlns:xlink="http://www.w3.org/1999/xlink"';
        $styles_xml .= '                        xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"';
        $styles_xml .= '                        xmlns:drawooo="http://openoffice.org/2010/draw"';
        $styles_xml .= '                        xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"';
        $styles_xml .= '                        xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"';
        $styles_xml .= '                        xmlns:oooc="http://openoffice.org/2004/calc"';
        $styles_xml .= '                        xmlns:ooow="http://openoffice.org/2004/writer"';
        $styles_xml .= '                        xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"';
        $styles_xml .= '                        xmlns:tableooo="http://openoffice.org/2009/table"';
	$styles_xml .+ '                        office:version="1.3">';
        $styles_xml .= '  <office:font-face-decls>';
        $styles_xml .= '    <style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="system" style:font-pitch="variable"/>';
        $styles_xml .= '    <style:font-face style:name="Calibri" svg:font-family="Calibri"/>';
        $styles_xml .= '    <style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/>';
        $styles_xml .= '    <style:font-face style:name="Microsoft YaHei" svg:font-family="&apos;Microsoft YaHei&apos;" style:font-family-generic="system" style:font-pitch="variable"/>';
        $styles_xml .= '  </office:font-face-decls>';
        $styles_xml .= '  <office:styles>';
        $styles_xml .= '  <style:default-style style:family="table-cell">';
        $styles_xml .= '  <style:paragraph-properties style:tab-stop-distance="0.5in"/>';
        $styles_xml .= '  <style:text-properties style:font-name="Liberation Sans" fo:font-size="10pt" fo:language="en" fo:country="US" style:font-name-asian="Microsoft YaHei" style:font-size-asian="10pt" style:language-asian="zh" style:country-asian="CN" style:font-name-complex="Arial" style:font-size-complex="10pt" style:language-complex="hi" style:country-complex="IN"/>';
        $styles_xml .= '  </style:default-style>';
        $styles_xml .= '    <number:number-style style:name="N0">';
        $styles_xml .= '    <number:number number:min-integer-digits="1"/>';
        $styles_xml .= '    </number:number-style>';
//  <style:style style:name="Default" style:family="table-cell" style:data-style-name="N0">
//  <style:table-cell-properties fo:background-color="transparent" style:vertical-align="automatic"/>
//  <style:text-properties fo:color="#000000" style:font-name="Calibri" fo:font-family="Calibri" fo:font-size="11pt" style:font-name-asian="Calibri" style:font-family-asian="Calibri" style:font-size-asian="11pt" style:font-name-complex="Calibri" style:font-family-complex="Calibri" style:font-size-complex="11pt"/>
//  </style:style>
//  <style:style style:name="Heading" style:family="table-cell" style:parent-style-name="Default">
//  <style:text-properties fo:color="#000000" fo:font-size="24pt" fo:font-style="normal" fo:font-weight="bold" style:font-size-asian="24pt" style:font-style-asian="normal" style:font-weight-asian="bold" style:font-size-complex="24pt" style:font-style-complex="normal" style:font-weight-complex="bold"/>
//  </style:style>
//  <style:style style:name="Heading_20_1" style:display-name="Heading 1" style:family="table-cell" style:parent-style-name="Heading">
//  <style:text-properties fo:color="#000000" fo:font-size="18pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="18pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="18pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Heading_20_2" style:display-name="Heading 2" style:family="table-cell" style:parent-style-name="Heading">
//  <style:text-properties fo:color="#000000" fo:font-size="12pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="12pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="12pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Text" style:family="table-cell" style:parent-style-name="Default"/>
//  <style:style style:name="Note" style:family="table-cell" style:parent-style-name="Text">
//  <style:table-cell-properties fo:background-color="#ffffcc" style:diagonal-bl-tr="none" style:diagonal-tl-br="none" fo:border="0.74pt solid #808080"/>
//  <style:text-properties fo:color="#333333" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Footnote" style:family="table-cell" style:parent-style-name="Text">
//  <style:text-properties fo:color="#808080" fo:font-size="10pt" fo:font-style="italic" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="italic" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="italic" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Hyperlink" style:family="table-cell" style:parent-style-name="Text">
//  <style:text-properties fo:color="#0000ee" fo:font-size="10pt" fo:font-style="normal" style:text-underline-style="solid" style:text-underline-width="auto" style:text-underline-color="#0000ee" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Status" style:family="table-cell" style:parent-style-name="Default"/>
//  <style:style style:name="Good" style:family="table-cell" style:parent-style-name="Status">
//  <style:table-cell-properties fo:background-color="#ccffcc"/>
//  <style:text-properties fo:color="#006600" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Neutral" style:family="table-cell" style:parent-style-name="Status">
//  <style:table-cell-properties fo:background-color="#ffffcc"/>
//  <style:text-properties fo:color="#996600" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Bad" style:family="table-cell" style:parent-style-name="Status">
//  <style:table-cell-properties fo:background-color="#ffcccc"/>
//  <style:text-properties fo:color="#cc0000" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Warning" style:family="table-cell" style:parent-style-name="Status">
//  <style:text-properties fo:color="#cc0000" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Error" style:family="table-cell" style:parent-style-name="Status">
//  <style:table-cell-properties fo:background-color="#cc0000"/>
//  <style:text-properties fo:color="#ffffff" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="bold" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="bold" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="bold"/>
//  </style:style>
//  <style:style style:name="Accent" style:family="table-cell" style:parent-style-name="Default">
//  <style:text-properties fo:color="#000000" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="bold" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="bold" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="bold"/>
//  </style:style>
//  <style:style style:name="Accent_20_1" style:display-name="Accent 1" style:family="table-cell" style:parent-style-name="Accent">
//  <style:table-cell-properties fo:background-color="#000000"/>
//  <style:text-properties fo:color="#ffffff" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Accent_20_2" style:display-name="Accent 2" style:family="table-cell" style:parent-style-name="Accent">
//  <style:table-cell-properties fo:background-color="#808080"/>
//  <style:text-properties fo:color="#ffffff" fo:font-size="10pt" fo:font-style="normal" fo:font-weight="normal" style:font-size-asian="10pt" style:font-style-asian="normal" style:font-weight-asian="normal" style:font-size-complex="10pt" style:font-style-complex="normal" style:font-weight-complex="normal"/>
//  </style:style>
//  <style:style style:name="Accent_20_3" style:display-name="Accent 3" style:family="table-cell" style:parent-style-name="Accent">
//  <style:table-cell-properties fo:background-color="#dddddd"/>
//  </style:style>
//  <style:style style:name="Result" style:family="table-cell" style:parent-style-name="Default">
//  <style:text-properties fo:color="#000000" fo:font-size="10pt" fo:font-style="italic" style:text-underline-style="solid" style:text-underline-width="auto" style:text-underline-color="#000000" fo:font-weight="bold" style:font-size-asian="10pt" style:font-style-asian="italic" style:font-weight-asian="bold" style:font-size-complex="10pt" style:font-style-complex="italic" style:font-weight-complex="bold"/>
//  </style:style>
        $styles_xml .= '  </office:styles>';
        $styles_xml .= '  <office:automatic-styles>';
//  <number:number-style style:name="N2">
//  <number:number number:decimal-places="2" number:min-decimal-places="2" number:min-integer-digits="1"/>
//  </number:number-style>
//  <style:page-layout style:name="Mpm1">
//  <style:page-layout-properties style:writing-mode="lr-tb" style:print="charts drawings grid objects zero-values"/>
//  <style:header-style>
//  <style:header-footer-properties fo:min-height="0.2953in" fo:margin-left="0in" fo:margin-right="0in" fo:margin-bottom="0.0984in"/>
//  </style:header-style>
//  <style:footer-style>
//  <style:header-footer-properties fo:min-height="0.2953in" fo:margin-left="0in" fo:margin-right="0in" fo:margin-top="0.0984in"/>
//  </style:footer-style>
//  </style:page-layout>
//  <style:page-layout style:name="Mpm2">
//  <style:page-layout-properties style:writing-mode="lr-tb"/>
//  <style:header-style>
//  <style:header-footer-properties fo:min-height="0.2953in" fo:margin-left="0in" fo:margin-right="0in" fo:margin-bottom="0.0984in" fo:border="2.49pt solid #000000" fo:padding="0.0071in" fo:background-color="#c0c0c0">
//  <style:background-image/>
//  </style:header-footer-properties>
//  </style:header-style>
//  <style:footer-style>
//  <style:header-footer-properties fo:min-height="0.2953in" fo:margin-left="0in" fo:margin-right="0in" fo:margin-top="0.0984in" fo:border="2.49pt solid #000000" fo:padding="0.0071in" fo:background-color="#c0c0c0">
//  <style:background-image/>
//  </style:header-footer-properties>
//  </style:footer-style>
//  </style:page-layout>
//  <style:page-layout style:name="Mpm3">
//  <style:page-layout-properties fo:page-width="8.5in" fo:page-height="11in" style:num-format="1" style:print-orientation="portrait" fo:margin-top="0.3in" fo:margin-bottom="0.3in" fo:margin-left="0.25in" fo:margin-right="0.25in" style:print-page-order="ttb" style:first-page-number="continue" style:scale-to="100%" style:print="charts drawings grid objects"/>
//  <style:header-style>
//  <style:header-footer-properties fo:min-height="0.45in" fo:margin-left="0.25in" fo:margin-right="0.25in" fo:margin-bottom="0in"/>
//  </style:header-style>
//  <style:footer-style>
//  <style:header-footer-properties fo:min-height="0.45in" fo:margin-left="0.25in" fo:margin-right="0.25in" fo:margin-top="0in"/>
//  </style:footer-style>
//  </style:page-layout>
        $styles_xml .= '  </office:automatic-styles>';
//  <office:master-styles>
//  <style:master-page style:name="Default" style:page-layout-name="Mpm1">
//  <style:header>
//  <text:p>
//  <text:sheet-name>
//  ???</text:sheet-name>
//  </text:p>
//  </style:header>
//  <style:header-left style:display="false"/>
//  <style:header-first style:display="false"/>
//  <style:footer>
//  <text:p>
//  Page <text:page-number>
//  1</text:page-number>
//  </text:p>
//  </style:footer>
//  <style:footer-left style:display="false"/>
//  <style:footer-first style:display="false"/>
//  </style:master-page>
//  <style:master-page style:name="Report" style:page-layout-name="Mpm2">
//  <style:header>
//  <style:region-left>
//  <text:p>
//  <text:sheet-name>
//  ???</text:sheet-name>
//  <text:s/>
//  (<text:title>
//  ???</text:title>
//  )</text:p>
//  </style:region-left>
//  <style:region-right>
//  <text:p>
//  <text:date style:data-style-name="N2" text:date-value="2021-12-23">
//  00/00/0000</text:date>
//  , <text:time>
//  00:00:00</text:time>
//  </text:p>
//  </style:region-right>
//  </style:header>
//  <style:header-left style:display="false"/>
//  <style:header-first style:display="false"/>
//  <style:footer>
//  <text:p>
//  Page <text:page-number>
//  1</text:page-number>
//  <text:s/>
//  / <text:page-count>
//  99</text:page-count>
//  </text:p>
//  </style:footer>
//  <style:footer-left style:display="false"/>
//  <style:footer-first style:display="false"/>
//  </style:master-page>
//  <style:master-page style:name="mp1" style:page-layout-name="Mpm3">
//  <style:header/>
//  <style:header-left style:display="false"/>
//  <style:header-first style:display="false"/>
//  <style:footer/>
//  <style:footer-left style:display="false"/>
//  <style:footer-first style:display="false"/>
//  </style:master-page>
//  </office:master-styles>
        $styles_xml .= '</office:document-styles>';

        $za->addFromString('mimetype',$mimetype);
        $za->addEmptyDir('Configurations2');
        $za->addEmptyDir('Configurations2/progressbar');
        $za->addEmptyDir('Configurations2/menubar');
        $za->addEmptyDir('Configurations2/popupmenu');
        $za->addEmptyDir('Configurations2/statusbar');
        $za->addEmptyDir('Configurations2/toolbar');
        $za->addEmptyDir('Configurations2/images');
        $za->addEmptyDir('Configurations2/images/Bitmaps');
        $za->addEmptyDir('Configurations2/floater');
        $za->addEmptyDir('Configurations2/toolpanel');
        $za->addEmptyDir('Configurations2/accelerator');
        $za->addFromString('manifest.rdf',$manifest_rdf);
        $za->addFromString('meta.xml',$meta_xml);
        $za->addEmptyDir('Thumbnails');
        $za->addFile('../data/thumbnail.png','Thumbnails/thumbnail.png');
        $za->addEmptyDir('META-INF');
        $za->addFromString('META-INF/manifest.xml',$manifest_xml);

//      899  2021-12-23 16:37   manifest.rdf
//    14668  2021-12-23 16:37   styles.xml
//      922  2021-12-23 16:37   meta.xml
//    20819  2021-12-23 16:37   content.xml
//     1266  2021-12-23 16:37   Thumbnails/thumbnail.png
//    14576  2021-12-23 16:37   settings.xml
//     1068  2021-12-23 16:37   META-INF/manifest.xml



    }

    public function addWeatherSheet($za) {
    }

}

?>
