<?php
$pdf->nawPrinted = true;
$participantData = $pdf;

$pdf->rapport_type = "FACTUUR";
//$pdf->rapport_voettext='';
$pdf->AddPage();
$pdf->SetAligns(array('L'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);

$pdf->rapport_type = "FACTUUR";

$font='Arial';
$font='dinot';

global $__appvar;


$query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.btwnr,
CRM_naw.verzendAdres,
CRM_naw.verzendPaAanhef,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
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
FROM CRM_naw WHERE  CRM_naw.id = '" . $data['deb_id'] . "' ";

$DB->SQL($query);
$crmData = $DB->lookupRecord();

if ( isset ($data['pdfType']) && $data['pdfType'] === 'mail' ) {
  $pdf->layout->getHeader();
}

$extraMarge=20;
$pdf->SetY(55-8);
$pdf->SetWidths(array($extraMarge,100,80));
$pdf->SetFont($font,"",10);
$pdf->SetAligns(array("L","L","L"));
$pdf->row(array('',$crmData['naam']));
$pdf->ln(1);
if (trim($crmData['naam1']) !='')
{
  $pdf->row(array('',$crmData['naam1']));
  $pdf->ln(1);
}
if (trim($crmData['verzendPaAanhef']) !='')
{
  $pdf->row(array('',$crmData['verzendPaAanhef']));
  $pdf->ln(1);
}
$pdf->row(array('',$crmData['adres']));
$pdf->ln(1);
$plaats='';
$plaats=$crmData['pc'];
if($crmData['plaats'] != '')
  $plaats.="  ".$crmData['plaats'];
$pdf->row(array('',$plaats));
$pdf->ln(1);
$pdf->row(array('',$crmData['land']));

$extraMarge=40-$pdf->marge;
$pdf->SetY(105);
$pdf->SetFont($font,"",8);
$pdf->row(array('    datum'));
$pdf->ln(1);
$pdf->row(array('    betreft'));
$pdf->SetY(105);
$pdf->SetFont($font,"",10);



$pdf->row(array('',date('d', strtotime($data['factuurdatum'])) . ' ' . $__appvar["Maanden"][date('n', strtotime($data['factuurdatum']))] . ' ' . date('Y', strtotime($data['factuurdatum']))));
$pdf->ln(1);
$pdf->row(array('',"factuurnummer ".sprintf("%06d",$data['factuurnr'])));



$pdf->SetY(125);
$pdf->Rect($pdf->marge+$extraMarge,122,210-2*($pdf->marge+$extraMarge),12,'F',null,array(240,240,240));
$pdf->Rect($pdf->marge+$extraMarge,155,210-2*($pdf->marge+$extraMarge),85,'F',null,array(240,240,240));
$extraMarge=42-$pdf->marge;
$pdf->SetWidths(array($extraMarge,210-($pdf->marge+$extraMarge)*2));
$pdf->SetFont($font,"B",11);
$pdf->SetAligns(array('L','C'));
$pdf->row(array('',"Factuur"));


$pdf->SetWidths(array(25-$pdf->marge,160));
$pdf->row(array('',$data['factuuronderwerp']));

$extraH=2;
$pdf->SetFont($font,"",10);
$pdf->SetWidths(array($extraMarge,210-2*($pdf->marge+$extraMarge)));
$pdf->SetAligns(array("L","L",'L','L'));
$pdf->ln(6);
$startY = $pdf->getY();
$pdf->row(array('',$data['tekstblok1']));
$stopY = $pdf->getY();



$pdf->ln(2);
$pdf->SetWidths(array($extraMarge,205-2*($pdf->marge+$extraMarge)-45,10,30));
$pdf->SetAligns(array("L","L",'R','R','R'));


//$pdf->Rect($pdf->marge+$extraMarge, $stopY + 5 ,210-2*($pdf->marge+$extraMarge),65,'F',null,array(240,240,240));

//$pdf->SetY(180);
$pdf->SetY(180);
//$pdf->SetY( $stopY + 10 );

$totEx = 0;
$btwTot[6] = 0;
$btwTot[9] = 0;
$btwTot[21] = 0;

$rowCount = 0;
$thisRowCount = 0;
foreach ( $data['factuur'] as $row )
{
  if ( ! empty ($row['bedrag'])) {$thisRowCount++;}
}


foreach ( $data['factuur'] as $row )
{
  if (  empty ($row['bedrag'])) {continue;}
  $rowCount++;
  if ( $thisRowCount == $rowCount ) {$pdf->CellBorders = array('','','','U');}
  $btwTot[$row['btw']] +=  ($row['bedrag'] * $row['btw']/100);
  
  $pdf->row(array("","        " . $row['ond'],"€",number_format($row['bedrag'], 2, ',', '.')));
  $pdf->ln(1);
//  $pdf->Row(array(' ',$row['ond'],EURO,   ' ' . number_format($row['bedrag'], 2, ',', '.')));
  $totEx =  $totEx + $row['bedrag'];
  
  
}

$pdf->CellBorders = null;
$pdf->SetWidths(array(10,76,30,4,30));

$pdf->SetAligns(array('L', 'L', 'L','L','R', 'R'));
$pdf->Row(array('','','', EURO , number_format($totEx, 2, ',', '.')));

$pdf->CellBorders = null;
$pdf->ln(3);

if ( ! empty ($btwTot[6]) ) {
  if ( empty ($btwTot[9]) && empty ($btwTot[21]) ){$pdf->CellBorders = array('','','','','U');}
  $pdf->Row(array(' ',' ','BTW (6%) ', EURO , number_format($btwTot[6], 2, ',', '.')));
  $pdf->ln(1);
}

if ( ! empty ($btwTot[9]) ) {
  if ( empty ($btwTot[6]) && empty ($btwTot[21])  ){$pdf->CellBorders = array('','','','','U');}
  if ( ! empty ($btwTot[6]) && empty ($btwTot[21])  ){$pdf->CellBorders = array('','','','','U');}
  $pdf->Row(array(' ',' ','BTW (9%) ', EURO , number_format($btwTot[9], 2, ',', '.')));
  $pdf->ln(1);
}

if ( ! empty ($btwTot[21]) ) {
  if ( empty ($btwTot[9]) && ! empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  if ( ! empty ($btwTot[9]) &&  empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  if ( ! empty ($btwTot[9]) &&  !empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  if ( empty ($btwTot[9]) &&  empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  $pdf->Row(array(' ',' ','BTW (21%)', EURO , number_format($btwTot[21], 2, ',', '.')));
}


$pdf->CellBorders = null;
$pdf->ln(5);
$pdf->Row(array(' ',' ','Factuurbedrag', EURO , number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));

$stopY = $pdf->getY();



$pdf->ln($extraH);
$pdf->SetAligns(array("L","L",'R','R','R'));
$pdf->SetWidths(array($extraMarge,210-2*($pdf->marge+$extraMarge)));

$pdf->SetY(250);
//$pdf->SetY( $stopY + 20 );

$pdf->Row(array(' ',$data['tekstblok2']));


if ( isset ($data['pdfType']) && $data['pdfType'] === 'mail' ) {
  $pdf->layout->getFooter();
}



if ( isset($data['sub_email']) )
{
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
  
  $blobData = bin2hex($pdf->Output('Factuur' . $crmData['id'] . '.pdf', 's'));
  
  $query = "INSERT INTO emailQueueAttachments
              SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR',
              emailQueueId='$lastId',
              filename = 'Factuur " . $data['factuurnr'] . ".pdf',
              Attachment=unhex('$blobData')";
  $db->SQL($query);
  $db->Query();
  
  echo '<div class="alert alert-info">In e-mail queue geplaatst</div>';
  
}
elseif ( isset ($data['sub_edos']) )
{
  
  $blobData = ($pdf->Output('Factuur' . $data['factuurnr'] . '.pdf', 's'));
  
  $filesize = strlen($blobData);
  $filetype = mime_content_type($blobData);
  $fileHandle = fopen($pdfFile, "r");
  
  $rec = array (
    'portefeuille' => $crmData['Portefeuille'],
    'filename' => 'factuur_' . $data['factuurnr'] . '.pdf',
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