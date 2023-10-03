<?php
loadLayoutSettings($this->pdf,'','',$participants[0]['crm_id']);
foreach ( $participants as $client ) {
  $partitipateFonds = $this->Participanten->parseBySearch(
    array(
      'crm_id' => $client['crm_id']
    ),
    array('fonds_fonds'),
    'GROUP BY `fonds_fonds`',
    -1
  );
  $partitipateFonds = $this->AEArray->extractValuesByKey($partitipateFonds, 'fonds_fonds');
  $Vermogensbeheerders = new Vermogensbeheerder();

  $categorieen = $BeleggingscategoriePerFonds->parseBySearch(
    array(
      'Vermogensbeheerder'  => $this->pdf->portefeuilledata['Vermogensbeheerder'],
      'Fonds'               => $partitipateFonds
    ),
    'all', 
    'ORDER BY `Beleggingscategorie`', 
    -1
  );
  $categoryFonds = array();
  foreach ( $categorieen as $category ) {
    $categoryFonds[$category['Fonds']] = $category['Beleggingscategorie'];
    $rows[$client['crm_id']]['rows'][$category['Beleggingscategorie']] = array();
  }

  //lets buils some rows
  $rows[$client['crm_id']]['Layout'] = $this->pdf->portefeuilledata['Layout'];
  $rows[$client['crm_id']]['CRM'] = $this->crmNaw->parseById($client['crm_id'], array('id', 'naam', 'zoekveld'));
  $rows[$client['crm_id']]['category'] = $category;
  $rows[$client['crm_id']]['participate'] = $this->AEParticipant->getParticipant($client['crm_id']);

  foreach ( $partitipateFonds as $fonds ) {
    $transactions = $this->AEParticipant->positionOneFondsOneClient($client['crm_id'], $fonds, $data['DateEnd'], $data);
    if ( $transactions ) {
      if ( isset ($categoryFonds[$fonds])) {$thisCategory = $categoryFonds[$fonds];} else {$thisCategory = 'Overige';}
      $rows[$client['crm_id']]['rows'][$thisCategory][$fonds] = $transactions;
    }
  }
}

$this->pdf->underlinePercentage = 0.8; 

$pdfOrder = array(
  'aantal', 
  'koers', 
  'waarde', 
);

foreach ( $rows as $client ) {
  $clientTotal = 0; // re/set the total per client

  if ( count($client['rows']) !== count($client['rows'], 1)   ) {
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
     $this->pdf->SetY(70);  
    
    
    $this->pdf->SetWidths(array(55,40,40,40));
    $this->pdf->SetAligns(array('L','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+5);
    $this->pdf->multicell(200, 10, 'Uw relatienummer: '.$client['CRM']['id']); 

    foreach ( $client['rows'] as $category => $fonds ) {
      $categoryTotal = 0;
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->multicell(200,10, $category);  //Fonds

      foreach ( $fonds as $fonds => $values ) {
        if ( $this->AEArray->hasNumericKeys($values) == true) {
          if ( empty ($values) ) {break;}
          if ( ! is_numeric ($fonds) ) {
            /** loop records **/
            foreach ($values as $type => $overviewData) {
              if ( is_numeric ($type) ){ //numeric are rows
                $overviewData['waarde'] = $overviewData['waarde'] * $overviewData['currentValutaCourse'];
                $requeredItems = array_intersect_key($overviewData, array_flip($pdfOrder));
                $requeredItems = array_merge(array_flip($pdfOrder), $requeredItems);
                $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
                $this->pdf->Row((array)  array_values(array_merge(array($fonds), $this->formatDataForView($requeredItems))));
                $categoryTotal += $overviewData['waarde'];
              }
            }
          }
        }
      }

      /** category totals **/
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array(null, null, null, 'TS');
      $this->pdf->ln(1);
      $this->pdf->Row((array) array('', '', '', $this->AENumbers->viewFormat2Decimals($categoryTotal)) );
      $this->pdf->CellBorders = null;
      /** add category total to client total **/
      $clientTotal += $categoryTotal;
    }

    /** category totals **/
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->CellBorders = array(null, null, null, 'TS');
    $this->pdf->Row((array) array('', '', '', $this->AENumbers->viewFormat2Decimals($clientTotal)) );
    $this->pdf->CellBorders = null;
  }
  
  if ( $portal === true ) {
    $this->AEPortal->addToPortalqueue(
      $rows[$client['crm_id']]['CRM'],
      array(
        'periode' => 'M',
        'portefeuille'  => 'P'.str_pad($client['CRMGebrNaam'], 6, '0', STR_PAD_LEFT),
        'raportageDatum' => $data['DateEnd']
      ),
      $this->pdf->Output('Participatie_Positie_'. $client['crm_id'] . '.pdf', 's'),
      str_pad($client['crm_id'], 6, '0', STR_PAD_LEFT)
            
    );
    
    //reset pdf
    $this->pdf = new PDFRapport('P','mm');
    $this->pdf->Rapportagedatum = date('d-m-Y');
    $this->pdf->rapport_type='Participatie';
  }
  
}

if ( $portal === false ) {
  $this->pdf->Output($filename.'.pdf', 'I');
}