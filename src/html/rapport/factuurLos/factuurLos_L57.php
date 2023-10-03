<?php



  //$pdf->rowHeight = 5;
  $pdf->underlinePercentage=0.8;
  $pdf->brief_font='Arial';
  //$pdf->brief_font='Times';
  $fontsize=9;
  $pdf->SetFont($pdf->brief_font,'',$fontsize);

  $pdf->rapport_type = "FACTUUR";

  $pdf->AddPage('P');


  $vanaf=db2jul($pdfData['datumVan']);
  $tot=db2jul($pdfData['datumTot']);


  $logo=$__appvar['basedir']."/html/rapport/logo/".$pdf->portefeuilledata['Logo'];
  if(is_file($logo))
  {
    $logoYpos=5;
    $xSize=50;
    $pdf->Image($pdf->rapport_logo,20, $logoYpos, $xSize);
  }

  $font='Arial';
  $pdf->SetY(15);
  $pdf->SetTextColor(0);
  $pdf->SetWidths(array(140,60));
  $pdf->SetAligns(array("L","L"));
  $pdf->SetFont($font,"",$fontsize);
  $pdf->row(array('','Florentes Vermogensbeheer'));
  $pdf->row(array('','Maliebaan 89,Utrecht'));
  $pdf->row(array('','Zusterplein 22a, Zeist'));
  $pdf->row(array('','Parklaan 34, Rotterdam'));
  $pdf->ln();
  $pdf->row(array('','T: 085-023 0445'));
  $pdf->ln();
  $pdf->row(array('','florentesvermogensbeheer.nl'));
  $pdf->row(array('','btw.nr. NL814125256B01'));
  $pdf->row(array('','KVK 28103359'));
  $pdf->Ln(15);



  $DB = new DB();

  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.verzendAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.voorletters,
CRM_naw.tussenvoegsel,
CRM_naw.achternaam,
CRM_naw.achtervoegsel,
CRM_naw.part_naam,
CRM_naw.part_voorvoegsel,
CRM_naw.part_titel,
CRM_naw.part_voorletters,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_achtervoegsel,
CRM_naw.enOfRekening
FROM CRM_naw WHERE Portefeuille = '".$portefeuille."'  ";

  $DB->SQL($query);
  $crmData = $DB->lookupRecord();


  $extraMarge=35;
  $pdf->SetWidths(array($extraMarge-$pdf->marge,140));
  $pdf->SetAligns(array('R','L','L','R','R'));
  $pdf->rowHeightBackup=$pdf->rowHeight;
  $pdf->rowHeight = 5;
  $extraVerlaging=10;
  $pdf->SetY(50+$extraVerlaging);
  $pdf->SetFont($pdf->brief_font,'B',$fontsize);
  $pdf->row(array('',""));//
  $pdf->SetFont($pdf->brief_font,'',$fontsize);
  $pdf->row(array('Postadres',$crmData['naam']));
  if (trim($crmData['naam1']) <> "")  $pdf->row(array('',$crmData['naam1']));
  $pdf->row(array('',$crmData['verzendAdres']));
  $plaats=$crmData['verzendPc'];
  if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
  $pdf->row(array('',$plaats));
  $pdf->row(array('',$crmData['verzendLand']));



  $pdf->SetY(100+$extraVerlaging);
  $pdf->row(array('Datum',(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
  $pdf->ln(10);
  $pdf->row(array("Onderwerp",$data['factuuronderwerp']));
  $pdf->ln(10);




  $pdf->row(array("Factuurnummer",$data['factuurnr']));
  $pdf->ln(15);

  $pdf->SetFont($pdf->brief_font,'',$fontsize);
  $pdf->SetWidths(array($extraMarge-$pdf->marge,140));
  $pdf->row(array('',$data['tekstblok1']));
  $pdf->SetFont($pdf->brief_font,'',$fontsize);
  $pdf->ln();

//$pdf->row(array('',"Onderstaand treft u de berekening van de beheervergoeding over de bovengenoemde factuurperiode aan."));
//$pdf->ln();
//$pdf->SetFont($pdf->brief_font,'',$fontsize);

//
//
//  $pdf->SetWidths(array($extraMarge-$pdf->marge,95,10,30,10));
//  $pdf->SetAligns(array('R','L','R','R','L'));
//  if($waarden['BeheerfeeBasisberekening']==2)
//  {
//    $pdf->row(array('', "Belegd vermogen per " . $eindDatumTxt, "EUR", number_format($waarden['rekenvermogenFee'], 2)));
//  }
//  else
//  {
//    $pdf->row(array('', "Vermogen per " . $beginDatumTxt, "EUR", number_format($waarden['totaalWaardeVanaf'], 2)));
//    $pdf->row(array('', "Vermogen per " . $eindDatumTxt, "EUR", number_format($waarden['totaalWaarde'], 2)));
//    $pdf->CellBorders = array('', '', array('T', 'U'), array('T', 'U'));
//    $pdf->row(array('', "Gemiddeld vermogen", "EUR", number_format($waarden['gemiddeldeVermogen'], 2)));//number_format(($waarden['drieMaandsWaarde_1']+$waarden['drieMaandsWaarde_2']+$waarden['drieMaandsWaarde_3'])/3
//  }
//  unset($pdf->CellBorders);
//
//  $pdf->ln();
//  $percentage=$waarden['BeheerfeePercentageVermogen'] / $waarden['BeheerfeeAantalFacturen'];
//  if($waarden['BeheerfeeAantalFacturen']==4)
//    $periode='kwartaal';
//  else
//    $periode='maand';
////listarray($waarden);exit;
//
//
//  $pdf->SetWidths(array($extraMarge-$pdf->marge,95,10,30));
//  $pdf->SetAligns(array('R','L','R','R'));
//  if($waarden['BeheerfeeBasisberekening']==2)
//  {
//    // $pdf->row(array('', ));
//    $pdf->row(array('',"Beheerloon " . number_format($percentage, 3) . " % per $periode ". "over belegd vermogen", 'EUR', number_format($waarden['beheerfeePerPeriode']-$waarden['administratieBedrag'], 2)));
//  }
//  else
//  {
//    // $pdf->row(array('', ));
//    $pdf->row(array('', "Beheerloon " . number_format($percentage, 3) . " % per $periode"." over gemiddeld vermogen", 'EUR', number_format($waarden['beheerfeePerPeriode']-$waarden['administratieBedrag'], 2)));
//  }
//
//  if($waarden['administratieBedrag']<>0)
//  {
//    $pdf->row(array('', "Kosten toezichthouders", 'EUR', number_format($waarden['administratieBedrag'], 2)));
//    $pdf->CellBorders=array('','',array('T'),array('T'));
//    $pdf->row(array('', "Subtotaal", 'EUR', number_format($waarden['beheerfeePerPeriode'], 2)));
//    $pdf->ln();
//    unset($pdf->CellBorders);
//  }
//
//  $pdf->row(array('',"B.T.W. ".number_format($waarden['btwTarief'],0)."%",'EUR',number_format($waarden['btw'],2)));
//  $pdf->CellBorders=array('','',array('T','U'),array('T','U'));
//  $pdf->row(array('',"Totaal",'EUR',number_format($waarden['beheerfeeBetalenIncl'],2)));
//  unset($pdf->CellBorders);
//
//
//
//
//
//  $pdf->ln(15);


  $pdf->SetWidths(array($extraMarge-$pdf->marge,95,10,30,10));
  $pdf->SetAligns(array('R','L','R','R','L'));


  $numRowCounter = 0;
  $totEx = 0;
  $btwTot[6] = 0;
  $btwTot[9] = 0;
  $btwTot[21] = 0;
  foreach ( $data['factuur'] as $row ) {
    if (  empty ($row['ond']) || empty ($row['bedrag'])) {continue;}

    $btw[$row['btw']][] = $row;
    $numRowCounter++;


    $pdf->Row((array)array(
      '',
      $row['ond'],
      'EUR',
       ' ' . number_format($row['bedrag'], 2, ',', '.'),
    ));

    $totEx =  $totEx + $row['bedrag'];
  }


  if ( $numRowCounter > 1 ) {

    $pdf->CellBorders = array('', '', array('T'), array('T'));
    $pdf->Row((array)array('','','EUR',  ' ' . number_format($totEx, 2, ',', '.')));
    $pdf->Ln(10);

    $pdf->CellBorders = null;
    ksort ($btw);
    foreach ( $btw as $btwNum => $btwdatas ) {
      if ( $btwNum == 6 ) {
        foreach ( $btwdatas as $btwdata) {
          $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
        }
        $pdf->Row((array)array('', 'B.T.W. 6%','EUR', ' ' . number_format($btwTot[6], 2, ',', '.')));
      }

      if ( $btwNum == 9 ) {
        foreach ( $btwdatas as $btwdata) {
          $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
        }
        $pdf->Row((array)array('','B.T.W. 9%' , 'EUR',  ' ' . number_format($btwTot[9], 2, ',', '.')));
      }

      if ( $btwNum == 21 ) {
        foreach ( $btwdatas as $btwdata) {
          $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
        }
        $pdf->Row((array)array('','B.T.W. 21%','EUR',  ' ' . number_format($btwTot[21], 2, ',', '.')));
      }
    }

  } else {

    $pdf->CellBorders = array('', '', array('T', 'U'), array('T', 'U'));

    $pdf->CellBorders = null;
    ksort ($btw);
    foreach ( $btw as $btwNum => $btwdatas ) {
      if ( $btwNum == 6 ) {
        foreach ( $btwdatas as $btwdata) {
          $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
        }
        $pdf->Row((array)array('', 'B.T.W. 6%','EUR', ' ' . number_format($btwTot[6], 2, ',', '.')));
      }

      if ( $btwNum == 9 ) {
        foreach ( $btwdatas as $btwdata) {
          $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
        }
        $pdf->Row((array)array('','B.T.W. 9%' , 'EUR',  ' ' . number_format($btwTot[9], 2, ',', '.')));
      }

      if ( $btwNum == 21 ) {
        foreach ( $btwdatas as $btwdata) {
          $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
        }
        $pdf->Row((array)array('','B.T.W. 21%','EUR',  ' ' . number_format($btwTot[21], 2, ',', '.')));
      }
    }


  }


  $pdf->SetFont($pdf->brief_font,'B',$fontsize);
  $pdf->CellBorders=array('','',array('T'),array('T'));
  $pdf->Row((array)array('','Totaal','EUR', ' ' . number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));
  $pdf->SetFont($pdf->brief_font,'',$fontsize);



//
//
//
//  if($waarden['BeheerfeeBasisberekening']==2)
//  {
//    $pdf->row(array('', "Belegd vermogen per " . $eindDatumTxt, "EUR", number_format($waarden['rekenvermogenFee'], 2)));
//  }
//  else
//  {
//    $pdf->row(array('', "Vermogen per " . $beginDatumTxt, "EUR", number_format($waarden['totaalWaardeVanaf'], 2)));
//    $pdf->row(array('', "Vermogen per " . $eindDatumTxt, "EUR", number_format($waarden['totaalWaarde'], 2)));
//    $pdf->CellBorders = array('', '', array('T', 'U'), array('T', 'U'));
//    $pdf->row(array('', "Gemiddeld vermogen", "EUR", number_format($waarden['gemiddeldeVermogen'], 2)));//number_format(($waarden['drieMaandsWaarde_1']+$waarden['drieMaandsWaarde_2']+$waarden['drieMaandsWaarde_3'])/3
//  }
//  unset($pdf->CellBorders);
//
//  $pdf->ln();
//  $percentage=$waarden['BeheerfeePercentageVermogen'] / $waarden['BeheerfeeAantalFacturen'];
//  if($waarden['BeheerfeeAantalFacturen']==4)
//    $periode='kwartaal';
//  else
//    $periode='maand';
////listarray($waarden);exit;
//
//
//  $pdf->SetWidths(array($extraMarge-$pdf->marge,95,10,30));
//  $pdf->SetAligns(array('R','L','R','R'));
//  if($waarden['BeheerfeeBasisberekening']==2)
//  {
//    // $pdf->row(array('', ));
//    $pdf->row(array('',"Beheerloon " . number_format($percentage, 3) . " % per $periode ". "over belegd vermogen", 'EUR', number_format($waarden['beheerfeePerPeriode']-$waarden['administratieBedrag'], 2)));
//  }
//  else
//  {
//    // $pdf->row(array('', ));
//    $pdf->row(array('', "Beheerloon " . number_format($percentage, 3) . " % per $periode"." over gemiddeld vermogen", 'EUR', number_format($waarden['beheerfeePerPeriode']-$waarden['administratieBedrag'], 2)));
//  }
//
//  if($waarden['administratieBedrag']<>0)
//  {
//    $pdf->row(array('', "Kosten toezichthouders", 'EUR', number_format($waarden['administratieBedrag'], 2)));
//    $pdf->CellBorders=array('','',array('T'),array('T'));
//    $pdf->row(array('', "Subtotaal", 'EUR', number_format($waarden['beheerfeePerPeriode'], 2)));
//    $pdf->ln();
//    unset($pdf->CellBorders);
//  }

//  $pdf->row(array('',"B.T.W. ".number_format($waarden['btwTarief'],0)."%",'EUR',number_format($waarden['btw'],2)));
//  $pdf->CellBorders=array('','',array('T','U'),array('T','U'));
//  $pdf->row(array('',"Totaal",'EUR',number_format($waarden['beheerfeeBetalenIncl'],2)));
  unset($pdf->CellBorders);
  $pdf->SetWidths(array($extraMarge-$pdf->marge,140));
  $pdf->ln(15);
  $pdf->row(array('',$data['tekstblok2']));

  
  
  
  
  
  






  
  



//
//
//$pdf->Ln(25);
//$pdf->multicell(200, 10, $data['tekstblok2']);
//$pdf->Ln(5);
//$pdf->multicell(200, 10, 'Wij vertrouwen er op u bij deze voldoende te hebben ge' . iconv("UTF-8", "ISO-8859-1", "?") . 'nformeerd.');
//










if ( isset($data['sub_email']) ) {
  $db = new DB();
  $fields = array(
    'crmId'         => $crmData['id'],
    'status'        => 'aangemaakt',
    'senderName'    => $_SESSION['usersession']['gebruiker']['Naam'],
    'senderEmail'   => $_SESSION['usersession']['gebruiker']['emailAdres'],
    'ccEmail'       => '',
    'bccEmail'      => '',
    'receiverName'  => $crmData['naam'],
    'receiverEmail' => $crmData['email'],
    'subject'       => 'Factuur ' . $data['factuurnr'],
    'bodyHtml'      => 'Factuur'
  );
  $query = "INSERT INTO emailQueue SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
  foreach ($fields as $key => $value)
  {
    $query .= ",$key='" . mysql_escape_string($value) . "'";
  }

  $db->SQL($query);
  $db->Query();
  $lastId = $db->last_id();

  $blobData = bin2hex($pdf->Output( ( isset($data['fileName']) && ! empty ($data['fileName']) ? $data['fileName']: 'Factuur' . $crmData['id'] ) . '.pdf', 's'));

  $query = "INSERT INTO emailQueueAttachments
              SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR',
              emailQueueId='$lastId',
              filename = '" . ( isset($data['fileName']) && ! empty ($data['fileName']) ? $data['fileName']: 'Factuur' . $data['factuurnr'] ) . ".pdf',
              Attachment=unhex('$blobData')";
  $db->SQL($query);
  $db->Query();

  echo '<div class="alert alert-info">In e-mail queue geplaatst</div>';

} elseif ( isset ($data['sub_edos']) ) {
  
  $blobData = ($pdf->Output(( isset($data['fileName']) && ! empty ($data['fileName']) ? $data['fileName']: 'Factuur' . $data['factuurnr'] ) . '.pdf', 's'));

  $filesize = strlen($blobData);
  $filetype = mime_content_type($blobData);
  $fileHandle = fopen($pdfFile, "r");

  $rec = array (
    'portefeuille' => $crmData['Portefeuille'],
    'filename' => ( isset($data['fileName']) && ! empty ($data['fileName']) ? $data['fileName']: 'factuur_' . $data['factuurnr'] ) . '.pdf',
    'keywords' => 'factuur_' . $data['factuurnr'] . '.pdf',
    'filesize' => $filesize,
    'filetype' => 'application/pdf',
    'categorie' => $data['categorie'],
    'description' => $data['description'],
    'keywords' => 'factuur_' . $data['factuurnr'] . '.pdf',
    'module' => 'CRM_naw',
    'module_id' => $crmData['id'],
    'blobdata' => $blobData
  );

  $dd = new digidoc();
  $extraVelden = array();
  $dd->useZlib = false;

  if ($dd->addDocumentToStore($rec, $extraVelden) == false)
  {
    echo '<div class="alert alert-warning">Niet in e-dossier  geplaatst</div>';
  }
  else
  {
    echo '<div class="alert alert-info">In e-dossier geplaatst</div>';
  }




} elseif ($verzenden === false)
{
  $pdf->Output($filename . '.pdf', 'I');
}
else
{
  echo $berichten;
}