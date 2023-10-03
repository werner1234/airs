<?php
  $crmId = $data['client_id'];
  $selectedFonds = $data['Fonds'];
//  debug($data);
  $partitipateFonds = $this->Participanten->parseBySearch(
    array(
      'crm_id' => $crmId,
      'fonds_fonds' => $selectedFonds
    ),
    array('fonds_fonds'),
    'GROUP BY `fonds_fonds`',
    -1
  );
  $partitipateFonds = $this->AEArray->extractValuesByKey($partitipateFonds, 'fonds_fonds');
  $Vermogensbeheerders = new Vermogensbeheerder();
  
  $queryWhere = $BeleggingscategoriePerFonds->__makeConditions(array(
    'Vermogensbeheerder'  => $huidigeVermogensbeheerder,
    'Fonds'               => $partitipateFonds,
  ));
  
  $db = new DB();
  $query = 'SELECT * FROM `BeleggingscategoriePerFonds` 
    LEFT JOIN `Beleggingscategorien` ON `BeleggingscategoriePerFonds`.`Beleggingscategorie` = `Beleggingscategorien`.`Beleggingscategorie` ' . $queryWhere . ' ORDER BY `BeleggingscategoriePerFonds`.`Beleggingscategorie`';
  $db->executeQuery($query);

  $categoryFonds = array();

  while ($category = $db->nextRecord()) {
    $categoryFonds[$category['Fonds']] = $category['Omschrijving'];
    $rows[$crmId]['rows'][$category['Beleggingscategorie']] = array();
  }

  //lets buils some rows
  $rows[$crmId]['Layout'] = $this->pdf->portefeuilledata['Layout'];
  $rows[$crmId]['CRM'] = $this->crmNaw->parseById($crmId);
  $rows[$crmId]['category'] = $category;
  $rows[$crmId]['participate'] = $this->AEParticipant->getParticipant($crmId);

  $fondsen = new fonds();
  

  foreach ( $partitipateFonds as $fonds ) {
    $transactions = $this->AEParticipant->positionOneFondsOneClient($crmId, $fonds, $data['DateEnd'], $data);
    if ( $transactions ) {
      $rows[$crmId]['fonds'][$fonds] = $fondsen->parseBySearch(
        array('fonds' => $fonds)
      );
      
      if ( isset ($categoryFonds[$fonds])) {$thisCategory = $categoryFonds[$fonds];} else {$thisCategory = 'Overig';}
      $rows[$crmId]['rows'][$thisCategory][$fonds] = $transactions;
    }
   
  }

$this->pdf->underlinePercentage = 0.8; 

$pdfOrder = array(
  'aantal', 
  'koers', 
  'waarde', 
);
//debug($rows);
foreach ( $rows as $client ) {
  $this->pdf->SetWidths(array(200,50));
  /** if we want to send it to the portal check login first**/
  if ( $portal === true ) {
    $portalError = $this->AEPortal->validateCrmDataForPortal($client['CRM']);
    if ($portalError === true) {continue;}
  }
  
  $clientTotal = 0; // re/set the total per client

  if ( count($client['rows']) !== count($client['rows'], 1)   ) {
    
    /** set pdf naw **/
    
    $this->pdf->nawPrinted=true;
    $participantData = $this->pdf;
    loadLayoutSettings($participantData,'','',$client['CRM']['id']);
    $this->pdf->AddPage();
     $this->pdf->SetAligns(array('L'));
     $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
     $this->pdf->setY(50);

     $this->pdf->SetWidths(array(110-$this->pdf->marge));
     $this->pdf->Row(array('',$participantData->portefeuilledata['Naam']));
     if($participantData->portefeuilledata['Naam1'] <> '')
       $this->pdf->Row(array('',$participantData->portefeuilledata['Naam1']));
     $this->pdf->Row(array('',$participantData->portefeuilledata['Adres'])); 
     $this->pdf->Row(array('',$participantData->portefeuilledata['Woonplaats']));
     $this->pdf->Row(array('',$participantData->portefeuilledata['Land']));
     $this->pdf->SetY(75);
    
    /** start printing one relation **/
    $this->pdf->SetWidths(array(55,40,40,40));
    $this->pdf->SetAligns(array('L','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+5);
    $this->pdf->multicell(200, 10, 'Uw relatienummer: '.$client['CRM']['CRMGebrNaam']); 
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->multicell(200, 10, 'De samenstelling en waarde van uw vermogen per ' . $data['DateEnd'] . ' bedraagt: ');
    
    /** loop rows **/
    foreach ( $client['rows'] as $category => $fonds ) {
      if (empty($fonds)) {continue;}
      $categoryTotal = 0;
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->multicell(200,10, $category);  //Fonds
      
      $this->pdf->SetWidths(array(60,40,20,25,20,25));
      $this->pdf->SetAligns(array('L','R','R','R','R','R'));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->Row((array) array('Fondsnaam', 'Aantal', '', 'Koers', '', 'Waarde'));
      foreach ( $fonds as $fonds => $values ) {
        if ( $this->AEArray->hasNumericKeys($values) == true) {
          if ( empty ($values) ) {continue;}
          if ( ! is_numeric ($fonds) ) {
            /** loop records **/
            $this->pdf->SetWidths(array(60,40,20,25,20,25));
            $this->pdf->SetAligns(array('L','R','R','R','R','R'));
            foreach ($values as $type => $overviewData) {
              if ( is_numeric ($type) ){ //numeric are rows
                $overviewData['waarde'] = $overviewData['waarde'] * $overviewData['currentValutaCourse'];
                
                //format fields hier
                $requeredItems = array(
                  'aantal' => $this->AENumbers->viewFormat2Decimals($overviewData['aantal']),
                  EURO,
                  'koers' => $this->AENumbers->viewFormatMinMaxDecimals($overviewData['koers']),
                  EURO,
                  'waarde' => $this->AENumbers->viewFormat2Decimals($overviewData['waarde']),
                );
                
                $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
                $this->pdf->Row((array)  array_values(array_merge(array(( ! empty($client['fonds'][$fonds]['Omschrijving']) ? $client['fonds'][$fonds]['Omschrijving'] : $fonds)), $requeredItems)));
                $categoryTotal += $overviewData['waarde'];
              }
            }
          }
        }
      }

      /** category totals **/
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array(null, null, null, null, null, 'TS');
      $this->pdf->ln(1);
      $this->pdf->Row((array) array('', '', '', '', EURO,  $this->AENumbers->viewFormat2Decimals($categoryTotal)) );
      $this->pdf->CellBorders = null;
      /** add category total to client total **/
      $clientTotal += $categoryTotal;
    }

    /** grand totals **/
    $this->pdf->SetWidths(array(145,20,25));
            $this->pdf->SetAligns(array('R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->CellBorders = array(null, null, 'TS');
    $this->pdf->Row((array) array('Totale waarde van uw beleggingen', EURO, $this->AENumbers->viewFormat2Decimals($clientTotal)) );
    $this->pdf->CellBorders = null;
  }
}

$this->pdf->Output('Participatie_positie.pdf', 'I');