<?php
$berichten = '';
//debug($transactionDatas);

$signature = base64_decode('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');
global $__appvar;
$extraMarge = 10;
if (isset ($transactionDatas) && !empty($transactionDatas))
{
  foreach ($transactionDatas as $crmKey => $transactionDates)
  {
    foreach ($transactionDates as $transActionDate => $transactionDatas)
    {
//      $crmDatas[$crmKey]

      foreach ($transactionDatas as $transaction)
      {

        $pdf->nawPrinted = true;
        $participantData = $pdf;
        loadLayoutSettings($participantData, '', '', $crmKey);
        $pdf->AddPage();
        $pdf->SetAligns(array('L'));
        $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
        $pdf->setY(50);
        $pdf->SetWidths(array(110 - $pdf->marge));
        $pdf->Row(array('', $participantData->portefeuilledata['Naam']));
        if ($participantData->portefeuilledata['Naam1'] <> '')
        {
          $pdf->Row(array('', $participantData->portefeuilledata['Naam1']));
        }
        $pdf->Row(array('', $participantData->portefeuilledata['Adres']));
        $pdf->Row(array('', $participantData->portefeuilledata['Woonplaats']));

        //land niet tonen wanneer dit Nederland is
        $land = $participantData->portefeuilledata['Land'];
        if (strtolower($land) != 'nederland')
        {
          $pdf->Row(array('', $participantData->portefeuilledata['Land']));
        }


        $pdf->SetY(80);

        $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);

        $pdf->SetWidths(array($extraMarge, 150));
        $pdf->Row(array('', 'Breda, ' . date('j') . " " . $__appvar["Maanden"][date("n")] . " " . date('Y')));
        $pdf->ln();
        $pdf->Row(array('', 'Relatienummer: ' . $crmDatas[$crmKey]['CRMGebrNaam']));

        //$pdf->Cell(0, 10, );
        $pdf->Ln(15);
        $pdf->Row(array('', $crmDatas[$crmKey]['verzendAanhef']));
        $pdf->Ln(3);

        $transTime = strtotime($transActionDate);
        $transactieDatum = date('j', $transTime) . " " . $__appvar["Maanden"][date("n", $transTime)] . " " . date('Y', $transTime);

        $reinvestQuarterNumer = 0;
        $reinvestYear = date('Y', $transTime);

        $quarterNumber = 0;
        $thisYear = date('Y', $transTime);
        $quarter = date('n', $transTime);

        if ($quarter < 4)
        {
          $reinvestQuarterNumer = 4;
          $reinvestYear = date('Y', $transTime) - 1;
          $quarterNumber = 1;
        }
        elseif ($quarter > 3 && $quarter < 7)
        {
          $reinvestQuarterNumer = 1;
          $quarterNumber = 2;
        }
        elseif ($quarter > 6 && $quarter < 10)
        {
          $reinvestQuarterNumer = 2;
          $quarterNumber = 3;
        }
        elseif ($quarter > 9)
        {
          $reinvestQuarterNumer = 3;
          $quarterNumber = 4;
        }

        if (strtolower($transaction['transactietype']) === 'a')
        {
          $intro = "U heeft besloten om deel te nemen in " . $transaction['Omschrijving'] . ". \n\n";
          $intro .= "Door middel van deze brief bevestigen wij uw deelname en de verwerking hiervan in onze administratie. \n\n";
          $intro .= "Per $transactieDatum hebben wij de volgende deelname in onze administratie verwerkt:\n ";
        }
        elseif (strtolower($transaction['transactietype']) === 'v')
        {
          $intro = "U heeft besloten om uw belegging in " . $transaction['Omschrijving'] . " geheel te verkopen. \n\n";
          $intro .= "Door middel van deze brief bevestigen wij uw verkoop en de verwerking hiervan in onze administratie. \n\n";
          $intro .= "Per $transactieDatum hebben wij de volgende verkoop in onze administratie verwerkt:\n ";
        }
        elseif (strtolower($transaction['transactietype']) === 'bk')
        {
          $intro = "U heeft bijgestort in " . $transaction['Omschrijving'] . ". \n\n";
          $intro .= "Door middel van deze brief bevestigen wij de bijstorting en de verwerking hiervan in onze administratie.\n\n";
          $intro .= "Per $transactieDatum hebben wij de volgende bijstorting in onze administratie verwerkt:\n ";
        }
        elseif (strtolower($transaction['transactietype']) === 'dv')
        {
          $intro = "U heeft besloten om een gedeelte van uw belegging in " . $transaction['Omschrijving'] . " te verkopen.\n\n";
          $intro .= "Door middel van deze brief bevestigen wij uw verkoop en de verwerking hiervan in onze administratie. \n\n";
          $intro .= "Per $transactieDatum hebben wij de volgende verkoop in onze administratie verwerkt:\n ";
        }
        elseif (strtolower($transaction['transactietype']) === 'h')
        {
          $intro = "U belegt in het " . $transaction['Omschrijving'] . ". Zoals u weet keert dit fonds periodiek rendement uit. U heeft er voor gekozen om dit rendement te herbeleggen. \n\n";
          $intro .= "Door middel van deze brief bevestigen wij u dat het rendement van het " . $reinvestQuarterNumer . "e kwartaal " . $reinvestYear . " is herbelegd.\n\n";
          $intro .= "Per $transactieDatum hebben wij de volgende herbelegging in onze administratie verwerkt:\n ";
        }
        elseif (strtolower($transaction['transactietype']) === 'u')
        {
          $intro = "U belegt in het " . $transaction['Omschrijving'] . ". Zoals u weet keert dit fonds periodiek rendement uit. U heeft er voor gekozen om dit rendement uit te laten keren. \n\n";
          $intro .= "Door middel van deze brief bevestigen wij u dat het rendement van het " . $quarterNumber . "e kwartaal " . $thisYear . " wordt uitgekeerd.\n\n";
          $intro .= "Wij hebben de volgende uitkering in onze administratie verwerkt:\n ";
        }

        else
        {
          $intro = '';
        }

        $pdf->Row(array('', $intro));


        //stel de valuta koers in 1 bij eur anders ophalens
        $transaction['currentValutaCourse'] = 1;
        if ($transaction['Valuta'] !== 'EUR')
        {
          $currentValutaCourse = $AEParticipant->getExchangeRate($transaction['Valuta'], $transaction['datum']);
          $transaction['currentValutaCourse'] = $currentValutaCourse['Koers'];
        }
        //bereken huidige transactie waarde

        if (strtolower($transaction['transactietype']) === 'u')
        {
          $pdf->SetWidths(array($extraMarge, 70, 50));
          $pdf->SetAligns(array('L', 'L', 'L'));
          $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
          $pdf->Row(array('', 'Rendement ' . $quarterNumber . 'e kwartaal ' . $thisYear, chr(128) . ' ' . $AENumbers->viewFormat2Decimals($transaction['waarde']) ));
          $pdf->Ln();
          $pdf->SetWidths(array($extraMarge, 150));
          $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
          $uitkeringText = "Het bedrag wordt een dezer dagen naar uw tegenrekening overgemaakt. \n\n";
          $pdf->Row(array('', $uitkeringText));
        }
        else
        {
          $transaction['waarde'] = $transaction['aantal'] * $transaction['koers'] * $transaction['currentValutaCourse'];
          $pdf->SetWidths(array($extraMarge, 40, 70));
          $pdf->SetAligns(array('L', 'L', 'R'));
          $pdf->Row(array('', 'Subfonds', $transaction['Omschrijving']));
          $pdf->Ln();
          $pdf->Row(array('', 'Aantal Participaties', $AENumbers->viewFormatMinMaxDecimals($transaction['aantal'], 2, 5)));
          $pdf->Row(array('', 'Koers', chr(128) . ' ' . $AENumbers->viewFormat2Decimals($transaction['koers'])));
          $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
          $pdf->Row(array('', 'Waarde', chr(128) . ' ' . $AENumbers->viewFormat2Decimals($transaction['waarde'])));
          $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
          $pdf->Ln();
          $pdf->SetWidths(array($extraMarge, 150));
        }
        $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);

        if (substr($transaction['transactietype'], 0, 1) == 'V')
        {
          //$pdf->Row(array('', "Het bedrag ad. " . chr(128) . ' ' . $AENumbers->viewFormat2Decimals($transaction['waarde']) . " zal een dezer dagen worden overgemaakt naar uw bankrekening."));
          $pdf->Ln();
        }
        $pdf->Row(array('', "Indien u nog vragen heeft of als we u ergens anders mee van dienst kunnen zijn dan vernemen we dat graag."));
//          (isset ($AEParticipant->transactionTypes[$transaction['transactietype']])?$AEParticipant->transactionTypes[$transaction['transactietype']]:$transaction['transactietype']),

        $pdf->Ln();
        $pdf->Row(array('', "Met vriendelijke groet,"));
        $pdf->ln();
        $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
        $pdf->Row(array('', "De Veste"));
        $pdf->Ln(7);
        $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
        $pdf->Row(array('', "Jack van Oosterbosch"));
        $pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
        $pdf->Row(array('', "CEO"));

        $pdf->Ln(5);
        $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
        $pdf->Row(array('', "Deze brief is automatisch opgesteld en derhalve niet ondertekend."));

        if (strtolower($transaction['transactietype']) === 'u')
        {
          $pdf->Ln();
          $pdf->SetWidths(array($extraMarge, 150));
          $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
          $uitkeringText = "P.S. U kunt het rendement ook herbeleggen. Hierdoor creëert u een rendement-op-rendement effect en wordt uw jaarrendement hoger! We vertellen u graag over de mogelijkheden. \n\n";
          $pdf->Row(array('', $uitkeringText));

        }

        //Deze brief is automatisch opgesteld en derhalve niet ondertekend.
        if ($verzenden === true)
        {
          $handtekening = '';
          if ( isset ($_SESSION['usersession']['gebruiker']['emailHandtekening']) && ! empty ($_SESSION['usersession']['gebruiker']['emailHandtekening']) ) {
            $handtekening = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
          }


          $aeconfig = new AE_config();
          $participantenTransactieMailBody = $aeconfig->getData('participantenTransactieMailBody');
          $participantenTransactieMailSubject = $aeconfig->getData('participantenTransactieMailSubject');


          $participantsObj = new AE_Participants();

          $db = new DB();
          $query = "
            SELECT 
              *
              FROM `participantenFondsVerloop`
              LEFT JOIN participanten on participanten.id = `participantenFondsVerloop`.`participanten_id`
              WHERE `participantenFondsVerloop`.`id` = '".$transaction['record_id']."'
          ";

          $db->QRecords($query);
          $transactionData = $db->nextRecord();

          $transaction['datum'] = date('d-m-Y', db2jul($transaction['datum']));
          foreach ($transactionData as $key => $value) {
            $participantenTransactieMailBodyData[$key] = $value;
          }
          $participantenTransactieMailBodyData['currentValutaCourse'] = $transaction['currentValutaCourse'];


          if (strtolower($transaction['transactietype']) === 'u')
          {
            $participantenTransactieMailBodyData['waarde'] = $participantenTransactieMailBodyData['waarde'];
          }
          else
          {
            $participantenTransactieMailBodyData['waarde'] = $participantenTransactieMailBodyData['aantal'] * $participantenTransactieMailBodyData['koers'] * $participantenTransactieMailBodyData['currentValutaCourse'];
          }

          $crmObj = new Naw();

          //CRM gegevens ophalen
          $crmNawData = $crmObj->parseBySearch(array ('id' => $transactionData['crm_id']));
          foreach ($crmNawData as $key => $value) {
            $participantenTransactieMailBodyData[$key] = $value;
          }

          //Fonds gegevens ophalen
          $fondsen = new Fonds();
          $fondsData = $fondsen->parseBySearch(array ('Fonds' => $participantenTransactieMailBodyData['fonds_fonds']));
          foreach ($fondsData as $key => $value) {
            $participantenTransactieMailBodyData[$key] = $value;
          }

          $participantenTransactieMailBodyData['emailHandtekening'] = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
          $participantenTransactieMailBodyData['transactietype'] = $participantsObj->transactionTypes[$participantenTransactieMailBodyData['transactietype']];
          $participantenTransactieMailBodyData['huidigeDatum'] = date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
          $participantenTransactieMailBodyData['huidigeGebruiker'] = $USR;

          $query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
          $db = new DB();
          $db->SQL($query);
          $dataGebr=$db->lookupRecord();
          $participantenTransactieMailBodyData['GebruikerNaam']=$dataGebr['Naam'];
          $participantenTransactieMailBodyData['GebruikerTitel']=$dataGebr['titel'];

          foreach ( $participantenTransactieMailBodyData as $key => $val ) {
            $participantenTransactieMailBody  = str_replace("[" . $key . "]", $val, $participantenTransactieMailBody );
            $participantenTransactieMailSubject = str_replace("[" . $key . "]", $val, $participantenTransactieMailSubject);
          }

          $db = new DB();
          $fields = array(
            'crmId'         => $crmKey,
            'status'        => 'aangemaakt',
            'senderName'    => $_SESSION['usersession']['gebruiker']['Naam'],
            'senderEmail'   => $_SESSION['usersession']['gebruiker']['emailAdres'],
            'ccEmail'       => '',
            'bccEmail'      => '',
            'receiverName'  => $crmDatas[$crmKey]['naam'],
            'receiverEmail' => $crmDatas[$crmKey]['email'],
            'subject'       => $participantenTransactieMailSubject,
            'bodyHtml'      => $participantenTransactieMailBody
          );
          $query = "INSERT INTO emailQueue SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
          foreach ($fields as $key => $value)
          {
            $query .= ",$key='" . mysql_escape_string($value) . "'";
          }

          $db->SQL($query);
          $db->Query();
          $lastId = $db->last_id();

          $blobData = bin2hex($pdf->Output('Participatie_Positie_' . $crmKey . '.pdf', 's'));

          $query = "INSERT INTO emailQueueAttachments
              SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR',
              emailQueueId='$lastId',
              filename = 'Transactie " . date('d-m-Y', db2jul($transActionDate)) . ".pdf', 
              Attachment=unhex('$blobData')";
          $db->SQL($query);
          $db->Query();

          foreach ($transactionDatas as $transaction)
          {
            $query = 'UPDATE `participantenFondsVerloop` SET  `participantenFondsVerloop`.`print_date` = NOW() WHERE `participantenFondsVerloop`.`id` = ' . $transaction['record_id'];
            $db->SQL($query);
            $db->Query();
          }

          $berichten .= '<div class="alert alert-success" role="alert">Transactie overzicht voor (' . $crmDatas[$crmKey]['email'] . ') in wachtrij geplaatst.</div>';

          //reset pdf
          $pdf = new PDFRapport('P', 'mm');
          $pdf->Rapportagedatum = date('d-m-Y');
          $pdf->rapport_type = 'Participatie';
        }
      }
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