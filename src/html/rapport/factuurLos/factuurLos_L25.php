<?php
$pdf->nawPrinted = true;
$participantData = $pdf;

$pdf->rapport_type = "FACTUUR";

//loadLayoutSettings($pdf, $pdf->portefeuille,11);

$pdf->SetWidths(array(22,150));
$pdf->SetAligns(array('R','L'));
$pdf->rowHeightBackup=$pdf->rowHeight;
$pdf->rowHeight = 5;

$pdf->brief_font='Arial';
if(file_exists(FPDF_FONTPATH.'calibril.php'))
{
  if(!isset($pdf->fonts['calibri']))
  {
    $pdf->AddFont('calibri','','calibri.php');
    $pdf->AddFont('calibri','B','calibriB.php');
    $pdf->AddFont('calibri','I','calibrii.php');
    $pdf->AddFont('calibri','BI','calibribi.php');
  }
  // $pdf->rapport_font = 'calibri';
  $pdf->brief_font='calibri';
}



$pdf->AddPage('P');

if(is_file($pdf->rapport_logo))
{
  $w=48;
  $pdf->Image($pdf->rapport_logo, $pdf->w/2-$w/2, 10, 48);
  //   $pdfObject->Image($pdfObject->rapport_logo,3,180, 48);
}
$pdf->SetY(50);
//$pdf->SetFont($pdf->brief_font,'B',11);
//$pdf->row(array('','Vertrouwelijk'));
$pdf->SetFont($pdf->brief_font,'',11);
$pdf->row(array('',$crmData['naam']));
if(trim($crmData['naam1']) <> '')
  $pdf->row(array('',$crmData['naam1']));
$pdf->row(array('',$crmData['adres']));
if($crmData['pc'] != '')
  $plaats = $crmData['pc'] . " " .$crmData['plaats'];
else
  $plaats = $crmData['plaats'];
$pdf->row(array('',$plaats));
$pdf->row(array('',$crmData['land']));
//debug($crmData);

$portefeuilleObj = new Portefeuilles();
$selectieveld1= $portefeuilleObj->parseBySearch(array('Portefeuille' => $crmData['Portefeuille']), 'selectieveld1');

$pdf->SetY(90);
$pdf->SetAligns(array('R','R'));
$selectieVeld=substr((isset($selectieveld1)? $selectieveld1:''),0,3);
$plaatsKoppelingen=array('MAA'=>'Maastricht',
  'LAN'=>'Lanaken',
  'AMS'=>'Amsterdam',
  'ROT'=>'Rotterdam',
  'VEN'=>'Venlo',
  'WAA'=>'Waalre',
  'ZEE'=>'Goes');

if(isset($plaatsKoppelingen[$selectieVeld])) {
  $plaats=$plaatsKoppelingen[$selectieVeld];
} else {
  $plaats='';
}

$pdf->row(array('',(! empty ($plaats) ? $plaats . ', ':'' ) . date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
$pdf->ln();

$pdf->SetAligns(array('R','L'));

$pdf->row(array('',"Geachte ".$crmData['verzendAanhef'].","));
$pdf->ln();



$pdf->row(array('',$data['tekstblok1']));
$pdf->ln();
$pdf->SetFont($pdf->brief_font,'B',11);
$pdf->row(array('',"Factuurnummer: ".$data['factuurnr']));


$pdf->SetFont($pdf->brief_font,'',11);
$pdf->ln();
$pdf->SetWidths(array(22,100,15,30));
$pdf->SetAligns(array("L","L","R","R"));

/** maak de header */
//$pdf->Row((array)array(
//  '',
//  'Omschrijving',
//  'Btw',
//  'Bedrag',
//  )
//);
//$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
/** maak de regel */

$totEx = 0;
$btwTot[6] = 0;
$btwTot[9] = 0;
$btwTot[21] = 0;
foreach ( $data['factuur'] as $row ) {
  if (  empty ($row['ond']) || empty ($row['bedrag'])) {continue;}

  $btw[$row['btw']][] = $row;

  $pdf->Row((array)array(
    '',
    $row['ond'],
    €,
    EURO . ' ' . number_format($row['bedrag'], 2, ',', '.'),
  ));

  $totEx =  $totEx + $row['bedrag'];
}




//De berekende fee
$pdf->ln();
$pdf->SetFont($pdf->brief_font,'B',11);
$pdf->row(array('',"Factuurbedrag"));
$pdf->SetFont($pdf->brief_font,'',11);
$pdf->ln();
$pdf->row(array('',"Subtotaal",'€',number_format($totEx, 2, ',', '.')));
//$pdf->row(array('',"BTW ".$crmData['btwTarief']."%",'€',number_format($crmData['btw'],2)));



$pdf->CellBorders = null;
ksort ($btw);
foreach ( $btw as $btwNum => $btwdatas ) {
  if ( $btwNum == 6 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
    }
    $pdf->Row((array)array(' ','BTW 6%', EURO , number_format($btwTot[6], 2, ',', '.')));
  }

  if ( $btwNum == 9 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
    }
    $pdf->Row((array)array(' ','BTW 9%', EURO , number_format($btwTot[9], 2, ',', '.')));
  }

  if ( $btwNum == 21 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
    }
    $pdf->Row((array)array(' ', 'BTW 21%', EURO , number_format($btwTot[21], 2, ',', '.')));
  }
}


$pdf->ln();
$pdf->row(array('',"========= ",' ','========='));
$pdf->ln();
$pdf->row(array('',"Totaal",'€',number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));
$pdf->ln();
$pdf->SetWidths(array(22,145));


$pdf->row(array('', $data['tekstblok2']));



$pdf->ln();
$pdf->row(array('','Met vriendelijke groet,'));
$pdf->ln();
$pdf->row(array('','Auréus'));


$pdf->AutoPageBreak=false;
$pdf->SetY(277);
$pdf->SetWidths(array(10,200));
$pdf->SetAligns(array('L','L','L','L','L'));
$pdf->SetFont($pdf->brief_font,'B',8);
$pdf->SetTextColor(151,151,151);
$pdf->SetWidths(array(15,60,55,55));
$pdf->rowHeight=4;
$pdf->row(array('','Auréus Group BV','Website',''));
$pdf->SetFont($pdf->brief_font,'',8);
$pdf->ln(-4);
if($selectieVeld=='LAN')
{
  $pdf->row(array('', '', '', 'IBAN: BE20735028350256'));
  $pdf->row(array('', 'Europaplein 13', 'www.aureus.eu', 'BTW: BE 0842.091.840'));
  $pdf->row(array('', 'BE-3620  Lanaken', 'info@aureus.eu', 'Vergunninghouder FSMA/AFM'));
}
else
{
  $pdf->row(array('', '', '', 'IBAN: NL61ABNA0421423528'));
  $pdf->row(array('', 'Piet Heinkade 55', 'www.aureus.eu', 'BTW: NL811109343B01'));
  $pdf->row(array('', '1019 GM Amsterdam', 'info@aureus.eu', 'KvK: 14073764'));
}
$pdf->row(array('','','','',));
$pdf->AutoPageBreak=true;
$pdf->SetFillColor(82,83,90);
$pdf->rect(0,$pdf->h-5,$pdf->w/2,5,'F');
$pdf->SetFillColor(132,149,164);
$pdf->rect($pdf->w/2,$pdf->h-5,$pdf->w/2,5,'F');
$pdf->SetTextColor(0,0,0);
$pdf->rowHeight=$pdf->rowHeightBackup;








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
  $pdf->Output($data['fileName'] . '.pdf', 'I');
}
else
{
  echo $berichten;
}