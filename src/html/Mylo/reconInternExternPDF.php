<?php
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Ricardo Monsees
    Filename            : reconInternExternPDF.php
*/
include_once("wwwvars.php");

if ($_SESSION["mokaRecon"] == "" )
{
  echo "foute aanroep";
  exit();
}

require_once($__appvar['basedir'] . '/classes/TCPDF/tcpdf_include.php');

class MyTCPDF extends TCPDF
{
  function Header()
  {
  }

  function Footer()
  {
  }
}

// create new PDF document
$pdf = new MyTCPDF('L', PDF_UNIT, 'A4', false, 'ISO-8859-1', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('');
$pdf->SetTitle('');
$pdf->SetSubject('');
$pdf->SetKeywords('');


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetFont('helvetica', '', 11, '', true);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
//    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(10);
// add a page
$pdf->AddPage();

$rowColors = array(
  'redRow' => 'background-color: #ffaaaa;'
);

$mokaReconDatas = $_SESSION["mokaRecon"];
$reconDate = array_shift($mokaReconDatas);
$reconHeader = array_shift($mokaReconDatas);

$pdfHead = '
  <thead>
    <tr style="background-color: #666;color: white;">
      <td>Recon</td>
      <td></td>
      <td style="text-align: right;">Print datum: ' . date('d-m-Y') . '</td>
      <td style="text-align: right;">' . $reconDate[0] . ': ' . $reconDate[1] . '</td>
      <td></td>
    </tr>
        
    <tr style="background-color: #143c5a;color: white;">
      <td>Fonds</td>
      <td>Rekening</td>
      <td style="text-align: right;">positie Clienten/extern</td>
      <td style="text-align: right;">positie Depotbank/intern</td>
      <td style="text-align: right;">Verschil</td>
    </tr>
  </thead>
';

foreach ( $mokaReconDatas as $mokaReconData ) {
  $pdfBody .= '
    <tr style=" ' . ( isset($rowColors[trim($mokaReconData['trClass'])]) ? $rowColors[trim($mokaReconData['trClass'])] : '' ) . ' ">
      <td>'.$mokaReconData['Fonds'].'</td>
      <td>'.$mokaReconData['Rekening'].'</td>
      <td style="text-align: right;">'.$mokaReconData['externAantal'].'</td>
      <td style="text-align: right;">'.$mokaReconData['internAantal'].'</td>
      <td style="text-align: right;">'.$mokaReconData['Match'].'</td>
    </tr>
  ';
}

$pdf->writeHTML('<table style="width: 100%;background: whitesmoke;">' . $pdfHead . ' <tbody>'.$pdfBody.'</tbody></table>', true, false, false, false, '');

$pdf->lastPage();

//Close and output PDF document
$pdf->Output('ReconExport_'.date('d-m-Y').'.pdf', 'I');

exit();