<?php

$pdfRowHeader = array(
  'Registratienummer',
  'Zoekveld',
  'Aantal',
  'Koers',
  'Waarde',
);
$pdfOrder = array(
  'registration_number',
  'zoekveld',
  'aantal',
  'koers',
  'waarde',
);

$pdfPositionRowHeader = array(
  'Registratienummer',
  'Fonds',
  'Aantal',
  'Koers',
  'Waarde',
);
$pdfPositionOrder = array(
  'registration_number',
  'fonds_fonds',
  'aantal',
  'koers',
  'waarde',
);

$addHead == 0;
if (empty($data['client_id']))
{
  $data['client_id'] = 1;
}
include_once ('/html/rapport/rapportVertaal.php');
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/PDFRapport.php");

$pdf = new PDFRapport('L', 'mm');
$pdf->rapport_type = 'Course';
loadLayoutSettings($pdf, '', '', $data['client_id']);
$pdf->SetAutoPageBreak(true,25);
$boldIndex = array('head', 'foot');
$skipIndex = array('koers', 'value');


foreach ($rows as $fonds => $values)
{
  if (empty($values))
  {
    continue;
  }
  if (!is_numeric($fonds))
  {
    $pdf->AddPage();
    $pdf->SetWidths(array(45, 75, 45, 25, 40));
    $pdf->SetAligns(array('L', 'L', 'R', 'R', 'R'));
    $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);


    $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize + 5);
    $pdf->multicell(200, 10, $fonds . ' per ' . $data['date']);
    $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
    $pdf->Row((array) array_values($pdfRowHeader));

    foreach ($values as $type => $overviewData)
    {
      if (is_numeric($type))
      {
        $requeredItems = array_intersect_key($overviewData, array_flip($pdfOrder));
        $requeredItems = array_merge(array_flip($pdfOrder), $requeredItems);
        $requeredItems = $this->formatPositionFields($requeredItems);
        $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
        $pdf->Row((array) array_values($requeredItems));
      }
      else
      {
        $overviewData = $this->formatPositionFields($overviewData);
//            debug($type);
//            debug($overviewData);
        $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
        if (in_array($type, array('total', 'koers', 'waarde')))
        {
          $pdf->Row((array) array(strip_tags($overviewData['registration_number']), '', $overviewData['aantal']));
        }
        elseif (in_array($type, array('koers', 'value')))
        {
//              $pdf->Row((array) array('', '', '', '', strip_tags ($overviewData['transactietype']), $overviewData['aantal']) );
        }
      }
    }
  }
  else
  {
    if ($addHead == 0)
    {
      $pdf->AddPage();
      $pdf->SetWidths(array(40, 35, 40, 25, 40));
      $pdf->SetAligns(array('L', 'L', 'R', 'R', 'R'));
      $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);


      $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize + 5);
      $pdf->multicell(200, 10, 'Overzicht  tot ' . $data['date']);
      $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
      $pdf->Row((array) array_values($pdfPositionRowHeader));
      $addHead = 1;
    }
//        debug($values);
    $requeredItems = array_intersect_key($values, array_flip($pdfPositionOrder));
    $requeredItems = array_merge(array_flip($pdfPositionOrder), $requeredItems);
    $requeredItems = $this->formatPositionFields($requeredItems);
//            debug($requeredItems);

    $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
    $pdf->Row((array) array_values($requeredItems));
  }
}
$pdf->Output($filename . '.pdf', 'I');
