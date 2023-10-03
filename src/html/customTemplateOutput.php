<?php
/**
 * Created by PhpStorm.
 * User: rm
 * Date: 10-6-2020
 * Time: 14:35
 */
include_once("wwwvars.php");
$customTemplate = new AE_CustomTemplate();
$templateParser = new AE_cls_TemplateParser();
$AETemplate = new AE_template();
$aeconfig = new AE_config();
$AEMessage = new AE_Message();
$baseDir = realpath(dirname(__FILE__)."/..");

include_once($baseDir . "/config/JSON.php");

$postData = array_merge($_POST, $_GET);

if ( isset ($postData['portefeuille']) ) {
  $templateParser->setData($customTemplate->getVariableByPportefeuille($postData['portefeuille']));
}

if ( isset ($postData['deb_id']) ) {
  $templateParser->setData($customTemplate->getVariableByDebId($postData['deb_id']));
}


if ( isset ($postData['templateVars']) && ! empty ($postData['templateVars']) ) {
  foreach ( $postData['templateVars'] as $key => $value ) {
    $postData['templateVars'][$key] = $templateParser->ParseData($value);
  }
} else {
  foreach ( $postData as $key => $value ) {
    if ( ! is_array($value) ) {
      $postData[$key] = $templateParser->ParseData($value);
    }
  }
}

$useTemplate = array();

$useTemplate = $customTemplate->fillPdfVars($postData);

//$useTemplate['headerp1'] = str_replace('width: 100%', 'width: 370px', $useTemplate['headerp1']);
//$useTemplate['headerp2'] = str_replace('width: 100%', 'width: 370px', $useTemplate['headerp2']);

//$useTemplate['footerp1'] = str_replace('width: 100%', 'width: 870px', $useTemplate['footerp1']);
//$useTemplate['footerp2'] = str_replace('width: 100%', 'width: 870px', $useTemplate['footerp2']);

//foreach ( $useTemplate as $value ) {
//  debug(htmlspecialchars($value));
//}

  // Include the main TCPDF library (search for installation path).
  require_once( $__appvar['basedir'] . '/classes/TCPDF/tcpdf_include.php');
  
  class MyTCPDF extends TCPDF {
    
    var $htmlHeader;
    
    function setHtmlHeader($htmlHeader1, $htmlHeader2) {
      $this->htmlHeader1 = $htmlHeader1;
      $this->htmlHeader2 = $htmlHeader2;
    }
    
    function setHtmlFooter($htmlFooter1, $htmlFooter2) {
      $this->htmlFooter1 = $htmlFooter1;
      $this->htmlFooter2 = $htmlFooter2;
    }
    
    function Header() {
//      debug(htmlspecialchars($this->htmlHeader2));
//      exit();
      $header1 = '
  <style>
  </style>
' . $this->htmlHeader1;


      if($this->page == 1) {
        //print header 1 and whatever the header 2 is
        $this->writeHTMLCell(
          $w = 0, $h = 0, $x = '', $y = '',
          $header1, $border = 0, $ln = 1, $fill = 0,
          $reseth = true, $align = 'top', $autopadding = true);
        
      } else {
        $this->writeHTMLCell(
          $w = 0, $h = 0, $x = '', $y = '',
          $this->htmlHeader2, $border = 0, $ln = 1, $fill = 0,
          $reseth = true, $align = 'top', $autopadding = true);
      }
    }
    
    function Footer()
    {
      
      if ($this->page == 1) {
        
        $this->writeHTML($this->htmlFooter1, false, true, false, true);
      } else {
        $this->writeHTML($this->htmlFooter2, false, true, false, true);
      }
    }
    
  }
  
  
  

// create new PDF document
  $pdf = new MyTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', false, 'ISO-8859-1', false);

// set document information
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor('');
  $pdf->SetTitle('');
  $pdf->SetSubject('');
  $pdf->SetKeywords('');
  
  
  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetFont('helvetica', '', 11, '', true);

// set margins
//    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  // set default header data
  
  $pdf->setHtmlHeader($useTemplate['headerp1'], $useTemplate['headerp2']);
  $pdf->setHtmlFooter($useTemplate['footerp1'], $useTemplate['footerp2']);
  
  // set margins
  $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
//    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(30);
  // add a page
  $pdf->AddPage();
$pdf->SetFont('helvetica', '', 11, '', true);
//Y:\airs2020\html\style\fontAwesome\fontawesome-webfont.ttf
//  debug(DOCUMENT_ROOT);
//  TCPDF_FONTS::addTTFfont(DOCUMENT_ROOT . '/librairies/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96);


$outData =  $useTemplate['tekstblok'];
$pdf->writeHTML(($outData), true, false, true, false, '');

//$pdf->writeHTML(($outData), true, false, true, false, '');
  $pdf->lastPage();
  
  //Close and output PDF document
  $pdf->Output('test.pdf', 'I');
  
  
  
  exit();