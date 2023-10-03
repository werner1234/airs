<?php
 $pdfRowHeader = array(
      'Datum', 
      'Registratienummer', 
      'Fonds', 
      'Zoekveld', 
      'Transactietype', 
      'Aantal', 
      'Koers', 
      'Waarde', 
    );
    $pdfOrder = array(
      'datum', 
      'registration_number', 
      'fonds_fonds', 
      'zoekveld', 
      'transactietype', 
      'aantal', 
      'koers', 
      'waarde', 
    );

    if( empty ($data['client_id']) ) {$data['client_id'] = 1;}
    include_once ('/html/rapport/rapportVertaal.php');
    include_once("../classes/AE_cls_fpdf.php");
    include_once("rapport/PDFRapport.php");
  
    $pdf = new PDFRapport('L','mm');
    $pdf->rapport_type='Course';
    
    loadLayoutSettings($pdf,'','',$data['client_id']);
//   debug($pdf);
    $boldIndex=array('head','foot');
    $skipIndex=array('koers','value');
  
   
    foreach ($rows as $fonds => $values)
    {
      
      if ( $fonds == 'csv' || empty ($values)) {continue;}
      if ( ! is_numeric ($fonds) ) {
        $pdf->AddPage();
        $pdf->SetWidths(array(35,32,40,35,25,25,25,25,35));
        $pdf->SetAligns(array('L','L','L','L','L','R','R','R','L'));
        $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);


        $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize + 5);
        $pdf->multicell(200,10,$fonds);
        $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
        $pdf->Row( (array) array_values ($pdfRowHeader) );
        
        $pdf->SetFillColor(230,230,230);
        $fill=true;
        
        foreach ($values as $type => $overviewData) {
          
          
          if ( is_numeric ($type) ) {
             if($fill==true)
              {
                $pdf->fillCell = array(1,1,1,1,1,1,1,1,1);
                $fill=false;
              }
              else
              {
                $pdf->fillCell=array();
                $fill=true;
              }
            
            
            $requeredItems = array_intersect_key($overviewData, array_flip($pdfOrder));
            $requeredItems = array_merge(array_flip($pdfOrder), $requeredItems);
            
            $koersWaarde = $this->AENumbers->viewFormat2Decimals($requeredItems['koers']);
            $thisWaarde = $this->AENumbers->viewFormat2Decimals($requeredItems['waarde'] * -1);
            
            $requeredItems = $this->formatVerloopFields($requeredItems);
            $requeredItems['koers'] = $koersWaarde;
            $requeredItems['waarde'] = $thisWaarde;
            
            
            $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
            $pdf->Row((array)  array_values($requeredItems));
            $pdf->fillCell=array();
          } else {
            $overviewData = $this->formatVerloopFields($overviewData);
            $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
            if ( in_array ($type, array('head', 'foot')) ) {
              $pdf->Row((array) array(strip_tags ($overviewData['datum']), '', '', '', '', $overviewData['aantal']) );
            } elseif ( in_array ($type, array('koers', 'value')) ) {
              $pdf->Row((array) array('', '', '', '', strip_tags ($overviewData['transactietype']), $overviewData['aantal']) );
            }
          }
        }
      }
      else
      {
        $overviewData = $this->formatVerloopFields($overviewData);
            if ( in_array ($type, array('head', 'foot')) ) {
              $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
              $pdf->Row((array) array(strip_tags ($overviewData['datum']), '', '', '', '', $overviewData['aantal']) );
            } elseif ( in_array ($type, array('koers', 'value')) ) {
              $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
              $pdf->Row((array) array('', '', '', '', strip_tags ($overviewData['transactietype']), $overviewData['aantal']) );
            }
      }
    }
    $pdf->Output($filename.'.pdf', 'I');