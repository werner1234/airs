<?php
 foreach ( $this->AEParticipant->getParticipant($filterCrmIds) as $client ) { 
    $rows[$client['crm_id']]['CRM'] = $this->crmNaw->parseById($client['crm_id']);
    $rows[$client['crm_id']]['participate'] = $this->AEParticipant->getParticipant($client['crm_id']);
    $rows[$client['crm_id']]['rows'] = $this->AEParticipant->allFondsOneClient($client['crm_id'], $data['DateStart'], $data['DateEnd']);
  }
  
   $pdfRowHeader = array(
      'Aantal', 
      'Koers', 
      'Waarde', 
    );
    $pdfOrder = array(
      'transactietype', 
      'aantal', 
      'koers', 
      'waarde', 
    );
    $footer = array();
    if( empty ($data['client_id']) ) {$data['client_id'] = 1;}
    
    foreach ( $rows as $client ) {
      /** if we want to send it to the portal check login first**/
      if ( $portal === true ) {
        $portalError = $this->AEPortal->validateCrmDataForPortal($client['CRM']);
        if ($portalError === true) {continue;}
      }
      
//      debug($client);
     if ( $this->AEArray->is_null( array_unique (array_values($client['rows'])) ) === true) {continue;}

      $footer = null;
//      loadLayoutSettings($pdf,'','',$client['CRM']['id']);
//      $pdf->lMargin = 18;
      
      $this->pdf->nawPrinted=true;
    $participantData = $this->pdf;
    loadLayoutSettings($participantData,'','',$client['CRM']['id']);
    $this->pdf->AddPage();
     $this->pdf->SetAligns(array('L'));
     $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
     $this->pdf->Row(array($participantData->portefeuilledata['Naam']));
     if($participantData->portefeuilledata['Naam1'] <> '')
       $this->pdf->Row(array($participantData->portefeuilledata['Naam1']));
     $this->pdf->Row(array($participantData->portefeuilledata['Adres'])); 
     $this->pdf->Row(array($participantData->portefeuilledata['Woonplaats']));
     $this->pdf->Row(array($participantData->portefeuilledata['Land']));
     $this->pdf->SetY(60); 
      
      
      
      $this->pdf->SetWidths(array(55,40,40,40));
      $this->pdf->SetAligns(array('L','R','R','R'));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+5);
      $this->pdf->multicell(200, 10, $client['CRM']['client'] . '' . $client['CRM']['zoekveld']); 

      foreach ( $client['rows'] as $fonds => $values ) {
        if ( empty ($values) ) {continue;}
        if ( ! is_numeric ($fonds) ) {
          /** set the header for each row fonds and registration number **/
          $participateIn = $this->AEArray->extractByKeyValue($client['participate'], array('fonds_fonds' => $fonds));
          $participateIn = $this->AEArray->extractValuesByKey($participateIn[$fonds], 'registration_number');

          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
          $thisFonds  = $this->AEParticipant->getFondskoers($fonds);
          $this->pdf->multicell(200, 10, ''); 
          $this->pdf->multicell(200,10, '(' . implode(', ', $participateIn) . ') ' . $fonds . ' (' . $thisFonds['Valuta'] . ')');  //Fonds
          
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+1);
          $this->pdf->Row( array_values ( array_merge(array('' => ''), $this->pdfRowHeader)) ); //set row headers

          /** loop records **/
          foreach ($values as $type => $overviewData) {
            if ( is_numeric ($type) ){ //numeric are rows
              $requeredItems = array_intersect_key($overviewData, array_flip($pdfOrder));
              $requeredItems = array_merge(array_flip($pdfOrder), $requeredItems);
              $requeredItems = $this->formatVerloopFields($requeredItems);
              $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
              $this->pdf->Row((array)  array_values($requeredItems));
            } else { //non numeric are headers
              $footer[$fonds][$type] = $overviewData;
             
              $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
              if ( in_array ($type, array('head', 'foot')) ) {
                if (strpos($overviewData['datum'], $data['DateStart']) !== false) {
                  $fondsKoers = $this->AEParticipant->getFondskoers($fonds, $data['DateStart']);
                } elseif (strpos($overviewData['datum'], $data['DateEnd']) !== false) {
                  $fondsKoers = $this->AEParticipant->getFondskoers($fonds, $data['DateEnd']);
                }
                
                $overviewData['koers'] = $fondsKoers['Koers'];
                $overviewData['waarde'] = $overviewData['aantal'] * $fondsKoers['Koers'];
                $overviewData = $this->formatVerloopFields($overviewData);
                $this->pdf->Row((array) array(strip_tags ($overviewData['datum']), $overviewData['aantal'], $overviewData['koers'], $overviewData['waarde']) );
              } elseif ( in_array ($type, array('koers', 'value')) ) {
//                $this->pdf->Row((array) array('',  '', strip_tags ($overviewData['transactietype']), $overviewData['aantal']) );
              }
            }
          }
        }
        else
        {
          $overviewData = $this->formatVerloopFields($overviewData);
          $footer[$fonds][$type] = $overviewData;
          if ( in_array ($type, array('head', 'foot')) ) {
            $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
            $this->pdf->Row((array) array(strip_tags ($overviewData['datum']), '', '', '', '', $overviewData['aantal']) );
          } elseif ( in_array ($type, array('koers', 'value')) ) {
            $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
            $this->pdf->Row((array) array('', '', '', '', strip_tags ($overviewData['transactietype']), $overviewData['aantal']) );
          }
        }
      }
      
      
        /** fix footer **/
    $marginLeftOnPage = (297 - 20) - $this->pdf->GetY();
    $totalingHeight = (count($footer) + 3) * 4;
    
    if ( $marginLeftOnPage < $totalingHeight ) {
      $this->pdf->AddPage();
      $this->pdf->SetWidths(array(55,40,40,40));
      $this->pdf->SetAligns(array('L','R','R','R'));
    }
    
      $this->pdf->Row((array) array('', '', '') );
      $this->pdf->Row((array) array('', '', '') );
      $this->pdf->MultiCell(215,10,'Waarde in EUR',0,"C");
//          $this->pdf->Row((array) array('', 'Waarde in EUR') );
      $this->pdf->Row((array) array('Fonds', $data['DateStart'], $data['DateEnd']) );
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      foreach ( $footer as $fonds => $footerItems ) {
        $startposition = $footerItems['head']['aantal'];
        $endPosition = $footerItems['foot']['aantal'];
        
        $startKoers = $this->AEParticipant->getFondskoers($fonds, $this->AEDate->formToDb($data['DateStart']));
        $endKoers = $this->AEParticipant->getFondskoers($fonds, $this->AEDate->formToDb($data['DateEnd']));
        
        $startValutaKoers = $this->AEParticipant->getExchangeRate($startKoers['Valuta'], $this->AEDate->formToDb($data['DateStart']));
        $endValutaKoers = $this->AEParticipant->getExchangeRate($endKoers['Valuta'], $this->AEDate->formToDb($data['DateEnd']));

        $start = $startposition * $startKoers['Koers'] * $startValutaKoers['Koers'];
        $end = $endPosition * $endKoers['Koers'] * $endValutaKoers['Koers'];

        $startValue = $this->formatVerloopFields(array('waarde' => $start));
        $endValue = $this->formatVerloopFields(array('waarde' => $end));

        $this->pdf->Row((array) array($fonds, $startValue['waarde'], $endValue['waarde'] ));
      }
      
      if ( $portal === true ) {
        $this->AEPortal->addToPortalqueue(
          $client['CRM'],
          array(
            'periode' => 'M',
            'portefeuille'  => 'P'.str_pad($client['CRM']['CRMGebrNaam'], 6, '0', STR_PAD_LEFT),
            'raportageDatum' => $data['DateEnd']
          ),
          $this->pdf->Output('Participatie_verloop_'. $client['CRM']['id'] . '.pdf', 's'),
          str_pad($client['CRM']['id'], 6, '0', STR_PAD_LEFT)

        );
        
        //reset pdf
        $this->pdf = new PDFRapport('P','mm');
        $this->pdf->Rapportagedatum = date('d-m-Y');
        $this->pdf->rapport_type='Participatie';
      }
      
      
    }
    
if ( $portal === false ) {
  $this->pdf->Output('Participatie_verloop.pdf', 'I');
}