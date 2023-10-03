<?php

$pdf->DB = new DB();
$query = "SELECT
CRM_naw.naam,
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.verzendPaAanhef,
CRM_naw.Portefeuille,
CRM_naw.verzendPaAanhef,
Portefeuilles.BetalingsinfoMee,
Portefeuilles.Depotbank,
Portefeuilles.Client
FROM CRM_naw LEFT Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.id = '" . $data['deb_id'] . "'  ";
$pdf->DB->SQL($query);
$crmData = $pdf->DB->lookupRecord();


$convertVals = array(
  'client'=>'Client',
  'clientNaam'=>'naam',
  'clientNaam1'=>'naam1',
  'clientAdres'=>'adres',
  'clientPostcode'=>'pc',
  'clientLand'=>'land',
  'clientWoonplaats'=>'plaats',
  'clientTelefoon'=>'telefoon',
  'clientFax'=>'fax',
  'clientEmail'=>'email',
  'portefeuille' => 'Portefeuille',
  'beheerfeeOpJaarbasis' => 'BeheerfeePerJaar',
  'performancefee' => 'Performancefee',
  'administratieBedrag' => 'BeheerfeeBedrag',
  'BeheerfeeTeruggaveHuisfondsenPercentage' => 'BeheerfeeTeruggaveHuisfondsPercentage',
  'BeheerfeeRemisiervergoedingsPercentage' => 'BeheerfeeRemisiervergoedingsPercentage',
  'totaalTransactie' => 'BetaaldeProvisie',
  'beheerfeeBetalen' => 'TebetalenBeheerfee',
  'btw' => 'BTW',
  'beheerfeeBetalenIncl' => 'TeBetalenBeheerfee+BTW',
  'stortingenOntrekkingen' => 'TotaalStortingen',
  'resultaat' => 'NettoVermogenstoename',
  'performancePeriode' => 'PerformancePeriode',
  'performanceJaar' => 'PerformanceJaar',
  'depotbankOmschrijving' => 'Depotbank',
  'BeheerfeePercentageVermogenDeelVanJaar' => 'BeheerfeePercentage',
  'CRM_naam'    => 'CRM_naam',
  'CRM_naam1'    => 'CRM_naam1',
  'CRM_verzendAanhef'    => 'VerzendAanhef',
  'CRM_verzendAdres'  => 'Adres',
  'CRM_verzendPc'         => 'Postcode',
  'CRM_verzendPlaats'  => 'Plaats',
  'CRM_verzendLand'   => 'Land',
  'rekeningEur' => 'rekening EUR',
  'bestandsvergoeding'=>'bestandsvergoeding',
  'BetalingsinfoMee'=>'BetalingsinfoMee',
  'huisfondsKorting'=>'huisfondsKorting',
  'huisfondsFeeJaar'=>'huisfondsFeeJaar',
  'periodeDeelVanJaar'=>'periodeDeelVanJaar',
  'transactiefee'=>'transactiefee',
  'rekenvermogen' => 'Fee berekend over',
  'IBAN'=>'IBAN',
  'SoortOvereenkomst'=>'SoortOvereenkomst',
  'huisfondsWaarde' => 'huisfondsWaarde',
  'BeheerfeeBedragBuitenBTWPeriode' => 'Bedrag buiten BTW',
  'Accountmanager'=>'Accountmanager',
  'overigeKosten'=>'Overige kosten',
  'afwijkendeOmzetsoort'=>'afwijkendeOmzetsoort',
  'debiteurnr'=>'debiteurnr'
);




$pdf->underlinePercentage=0.8;
$pdf->brief_font='Times';
$pdf->rapport_type = "FACTUUR";
$pdf->AddPage('P');

$rowHeightBackup=$pdf->rowHeight;
$pdf->rowHeight = 5;
$pdf->SetFont($pdf->rapport_font,'',$pdf->rapport_fontsize);


if ( isset ($data['pdfType']) && $data['pdfType'] === 'mail' ) {
  if(is_file($pdf->rapport_logo))
  {
    if($pdf->CurOrientation=='P')
      $pageWidth=210;
    else
      $pageWidth=297;

    $factor=0.045;
    $xSize=1200*$factor;//$x=885*$factor;
    $ySize=350*$factor;//$y=849*$factor;
    $logoX=$pageWidth/2-$xSize/2;
    $pdf->Image($pdf->rapport_logo, $logoX, 2, $xSize, $ySize);
  }
}

$pdf->SetY(60);
$pdf->SetWidths(array(22,150));
$pdf->SetAligns(array('R','L'));

$pdf->row(array('',$crmData[$convertVals['clientNaam']]));
if($crmData[$convertVals['clientNaam1']] <> '')
  $pdf->row(array('',$crmData[$convertVals['clientNaam1']]));
$pdf->row(array('',$crmData[$convertVals['clientAdres']]));
if($crmData[$convertVals['clientPostcode']] != '')
  $plaats = $crmData[$convertVals['clientPostcode']] . "  " .$crmData[$convertVals['clientWoonplaats']];
else
  $plaats = $crmData[$convertVals['clientWoonplaats']];
$pdf->row(array('',$plaats));
$pdf->row(array('',$crmData[$convertVals['clientLand']]));

$pdf->SetY(110);
//$pdf->SetAligns(array('R','C'));




$nu = date('d', strtotime($data['factuurdatum'])) . ' ' . vertaalTekst($__appvar['Maanden'][date('n', strtotime($data['factuurdatum']))], $pdf->rapport_taal) . ' ' . date('Y', strtotime($data['factuurdatum']));


$pdf->Row((array)array('','Factuurdatum: ' . $nu));
$pdf->Row((array)array('','Onderwerp: ' . $data['factuuronderwerp']));


$pdf->SetAligns(array('R','L'));


$pdf->ln(20);


//listarray($crmData);

$pdf->SetAligns(array('R','L'));

$pdf->row(array('',$data['tekstblok1']));

$pdf->ln(12);
$pdf->SetWidths(array(22,75,30,25));
$pdf->SetAligns(array('R','L','R','R'));

$totEx = 0;
$btwTot[6] = 0;
$btwTot[9] = 0;
$btwTot[21] = 0;

$rowCount = 0;
$thisRowCount = 0;
foreach ( $data['factuur'] as $row ) {
  if ( ! empty ($row['bedrag'])) {$thisRowCount++;}
}


foreach ( $data['factuur'] as $row )
{
  if (  empty ($row['bedrag'])) {continue;}
  $rowCount++;
  if ( $thisRowCount == $rowCount ) {$pdf->CellBorders = array('','','','U');}
  $btw[$row['btw']][] = $row;

  $pdf->Row((array)array(
    '',
    $row['ond'],
    $row['btw'] .'%',
    EURO . ' ' . number_format($row['bedrag'], 2, ',', '.'),
  ));

//  $pdf->row(array("", $row['ond'],"?".number_format($row['bedrag'], 2, ',', '.')));
  $pdf->ln(1);

  $totEx =  $totEx + $row['bedrag'];
}

$pdf->SetWidths(array(22,100,30,50));
$pdf->SetAligns(array('R','L','R'));

$pdf->ln(20);


$pdf->CellBorders = array();
$pdf->SetWidths(array(22,100,30,50));




$pdf->CellBorders = array(null, null, 'TS');

$pdf->CellBorders = null;
ksort ($btw);

foreach ( $btw as $btwNum => $btwdatas ) {
  if ( $btwNum == 6 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
    }
    $pdf->Row((array)array(' ','BTW 6%', EURO . ' ' . number_format($btwTot[6], 2, ',', '.')));
  }

  if ( $btwNum == 9 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
    }
    $pdf->Row((array)array(' ','BTW 9%', EURO . ' ' . number_format($btwTot[9], 2, ',', '.')));
  }

  if ( $btwNum == 21 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
    }
    $pdf->Row((array)array(' ', 'BTW 21%', EURO . ' ' . number_format($btwTot[21], 2, ',', '.')));
  }
}
$pdf->CellBorders = array('','',array('TS','UU'));

//$pdf->CellBorders = array(null, null, 'TS');
$pdf->Row((array)array(' ','Totaal bedrag', EURO . ' ' . number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));

$pdf->ln();
$pdf->CellBorders = array();


//  $pdf->ln();
//$pdf->CellBorders = array('','',array('TS','UU'));
// $pdf->row(array('','Totaalbedrag',"? ".$pdf->formatGetal($crmData['beheerfeeBetalenIncl'],2).""));
$pdf->CellBorders = array();
$pdf->ln(12);
$pdf->SetWidths(array(22,150));
$pdf->row(array('',$data['tekstblok2']));
$pdf->ln(12);
$pdf->SetWidths(array(22,60,30,50));
$pdf->SetAligns(array('R','L','R','L'));

$query="SELECT Rekeningen.IBANnr FROM Rekeningen 
WHERE Rekeningen.Portefeuille='".$crmData['Portefeuille']."' AND 
Rekeningen.Depotbank='".$crmData['Depotbank']."' AND 
Rekeningen.IBANnr<>''  ORDER BY Rekeningen.Valuta limit 1";


$db=new DB();
$db->SQL($query);
$rekening=$db->lookupRecord();
if($rekening['IBANnr']<>'')
  $rekeningnr=$rekening['IBANnr'];
else
  $rekeningnr=$crmData['portefeuille'];

$pdf->row(array('',"Rekeningnummer:",'',$rekeningnr));
$pdf->row(array('',"Factuurnummer:",'', $data['factuurnr']));
$pdf->row(array('',"Rekeningnummer VEC:",'',"223605050"));



if ( isset ($data['pdfType']) && $data['pdfType'] === 'mail' ) {


  $autoPageBreakBackup = $pdf->AutoPageBreak;
  $pdf->AutoPageBreak = false;
  $pdf->setY(275);
  $pdf->rowHeight = $rowHeightBackup;
  $pdf->SetFont($pdf->rapport_font, '', 7);
  $pdf->SetWidths(array(50, 50, 50, 50));
  $pdf->SetAligns(array('L', 'L', 'L', 'L'));
  $pdf->SetTextColor(100, 100, 100);
  $pdf->row(array('N.V. De Vereenigde Effecten Compagnie', 'Oosteinde 30', 'Berg en Dalseweg 127', 'KvK Alkmaar nr. 34.12.74.03'));
  $pdf->row(array('Postbus 23, 1483 ZG De Rijp', '1483 AE De Rijp', '6522 BE Nijmegen', 'Vergunning AFM nr. BFW789'));
  $pdf->row(array('E: info@effectencompagnie.nl', 'T: +31 (0)299 - 315 778', 'T: +31 (0)85 - 744 00 56', 'Onder toezicht van DNB'));
  $pdf->row(array('I: www.effectencompagnie.nl', 'F: +31 (0)299 - 315 895', 'T: +31 (0)85 - 744 00 57', 'DSI registratie'));
  $pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
  $pdf->SetTextColor(0, 0, 0);
  $pdf->AutoPageBreak = $autoPageBreakBackup;


}



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