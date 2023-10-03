<?php
$berichten = '';
if (isset ($transactionDatas) && !empty($transactionDatas))
{
  foreach ($transactionDatas as $transactionData)
  {
//debug($transactionData);

    $pdf->nawPrinted = true;
    $participantData = $pdf;
    loadLayoutSettings($participantData, '', '', $transactionData['crm_id']);
    $pdf->AddPage();
    $pdf->SetAligns(array('L'));
    $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);

    $pdf->SetY(30);
    $pdf->x = 150;
//    $pdf->SetWidths(array(110-$pdf->marge));


    $pdf->Row(array($participantData->portefeuilledata['Naam']));
    $pdf->x = 150;
    if ($participantData->portefeuilledata['Naam1'] <> '')
    {
      $pdf->Row(array($participantData->portefeuilledata['Naam1']));
    }
    $pdf->x = 150;
    $pdf->Row(array($participantData->portefeuilledata['Adres']));
    $pdf->x = 150;
    $pdf->Row(array($participantData->portefeuilledata['Woonplaats']));
    $pdf->x = 150;
    $pdf->Row(array($participantData->portefeuilledata['Land']));
    $pdf->x = 150;
    $pdf->multicell(200, 10, 'Relatienummer: ' . $transactionData['CRMGebrNaam']);
    $pdf->SetY(70);

    $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
    //    debug($transactionData);
    $transactionDate = '';
    $transactionDate = date('d-m-Y', db2jul($transactionData['datum']));

    $pdf->Cell(0, 10, 'Breda, ' . date('d-m-Y'));
    $pdf->Ln(15);
    $pdf->Cell(0, 10, $transactionData['verzendAanhef']);
    $pdf->Ln(5);

    switch ($transactionData['transactietype'])
    {
      case '':

      default:
        $pdf->multicell(200, 10, 'Per ' . $transactionDate . ' hebben wij de volgende mutaties in uw beleggingsportefeuille in onze administratie verwerkt:');
        $pdf->Ln(5);
    }

    $pdf->SetWidths(array(70, 40, 30, 30, 20, 40));
    $pdf->SetAligns(array('L', 'L', 'R', 'R', 'R', 'R'));
    $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
    /** maak de header */
    $pdf->Row((array)array(
      'Product',
      'Mutatie',
      'Participaties',
      'Koers',
      'Waarde')
    );
    $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
    /** maak de regel */
    $pdf->Row((array)array(
      $transactionData['Omschrijving'],
      (isset ($AEParticipant->transactionTypes[$transactionData['transactietype']])?$AEParticipant->transactionTypes[$transactionData['transactietype']]:$transactionData['transactietype']),
      $AENumbers->viewFormat2Decimals($transactionData['aantal']),
      $AENumbers->viewFormatMinMaxDecimals($transactionData['koers']),
      $AENumbers->viewFormat2Decimals($transactionData['waarde'])
    ));

    $pdf->Ln(10);
//    $pdf->multicell(180, 5, '');

    $pdf->multicell(200, 10, 'Wij vertrouwen er op u bij deze voldoende te hebben ge' . iconv("UTF-8", "ISO-8859-1", "ï") . 'nformeerd.');

    $pdf->multicell(200, 10, 'Met vriendelijke groet,');

    $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
//    $pdf->multicell(200, 10, '');

    if ($verzenden === true)
    {
      $db = new DB();
      $fields = array(
        'crmId'         => $transactionData['crm_id'],
        'status'        => 'aangemaakt',
        'senderName'    => $_SESSION['usersession']['gebruiker']['Naam'],
        'senderEmail'   => $_SESSION['usersession']['gebruiker']['emailAdres'],
        'ccEmail'       => '',
        'bccEmail'      => '',
        'receiverName'  => $transactionData['naam'],
        'receiverEmail' => $transactionData['email'],
        'subject'       => 'Transactie bevestiging',
        'bodyHtml'      => '
          Hierbij ontvangt u de bevestiging van uw transactie <br/>
          Voor vragen kunt contact met ons opnemen.<br/><br/>
          Met vriendelijke groet,
        '
      );
      $query = "INSERT INTO emailQueue SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
      foreach ($fields as $key => $value)
      {
        $query .= ",$key='" . mysql_escape_string($value) . "'";
      }

      $db->SQL($query);
      $db->Query();
      $lastId = $db->last_id();

      $blobData = bin2hex($pdf->Output('Participatie_Positie_' . $transactionData['crm_id'] . '.pdf', 's'));

      $query = "INSERT INTO emailQueueAttachments
              SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR',
              emailQueueId='$lastId',
              filename = 'Transactie " . date('d-m-Y', db2jul($transactionData['datum'])) . ".pdf',  
              Attachment=unhex('$blobData')";
      $db->SQL($query);
      $db->Query();

      $query = 'UPDATE `participantenFondsVerloop` SET  `participantenFondsVerloop`.`print_date` = NOW() WHERE `participantenFondsVerloop`.`id` = ' . $transactionData['record_id'];
      $db->SQL($query);
      $db->Query();

      $berichten .= '<div class="alert alert-success" role="alert">Transactie overzicht voor (' . $transactionData['email'] . ') in wachtrij geplaatst.</div>';

      //reset pdf
      $pdf = new PDFRapport('P', 'mm');
      $pdf->Rapportagedatum = date('d-m-Y');
      $pdf->rapport_type = 'Participatie';
    }

  }
}

if ($verzenden === false)
{
  $pdf->Output($filename . '.pdf', 'I');
}
else
{
  echo $berichten;
}