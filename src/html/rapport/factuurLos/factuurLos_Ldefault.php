<?php
$pdf->nawPrinted = true;
$participantData = $pdf;

$pdf->rapport_type = "FACTUUR";

loadLayoutSettings($pdf, $pdf->portefeuille);
$pdf->AddPage();
$pdf->SetAligns(array('L'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
if(is_file($pdf->rapport_logo))
{
  $factor=0.15;
  $xSize=1000*$factor;//$x=885*$factor;
  $ySize=620*$factor;//$y=849*$factor;
  $xStart=(210)/2-($xSize/2);
  $pdf->Image($pdf->rapport_logo, $xStart, 5, $xSize);
}

$pdf->SetWidths(array(210-2*$pdf->marge));
$pdf->SetY(30);


$pdf->SetFont($font,"B",11);
$pdf->row(array('FACTUUR'));
$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);

$pdf->SetWidths(array(140, 60));
$pdf->SetAligns(array('L', 'L'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);

$pdf->Row((array)array(' ', $pdfData['Vermogensbeheerders']['Naam']));
$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);

$pdf->Row((array)array(' ', $pdfData['Vermogensbeheerders']['Adres']));
$pdf->Row((array)array(' ', $pdfData['Vermogensbeheerders']['Woonplaats']));
$pdf->Ln(5);

$pdf->SetWidths(array(140, 20, 40));
$pdf->SetAligns(array('L', 'L', 'L'));
$naam = $crmData['naam'] . '' . ( ! empty ($crmData['naam1']) ? ' '.$crmData['naam1']:'' );
$pdf->Row((array)array('Aan: ' . $naam, 'KvK nr:', '12345678'));
$pdf->Row((array)array($crmData['adres'] . ' ' . $crmData['plaats'], 'BTW nr:', 'NL123456789B01'));
$pdf->Ln(2);
$pdf->Row((array)array(' ', 'Bank:', $pdfData['Vermogensbeheerders']['bank']));
$pdf->Row((array)array(' ', 'IBAN:', $pdfData['Vermogensbeheerders']['rekening']));
$pdf->Ln(2);

$pdf->SetWidths(array(30, 110, 20, 40));
$pdf->SetAligns(array('L', 'L', 'L', 'L'));
$pdf->Row((array)array('Factuurnummer: ',$data['factuurnr'], 'Tel:', $pdfData['Vermogensbeheerders']['Telefoon']));
$pdf->Row((array)array('Factuurdatum: ',$data['factuurdatum'], 'E-mail:', $pdfData['Vermogensbeheerders']['Email']));
$pdf->Row((array)array('Onderwerp: ',$data['factuuronderwerp'],  'Website:',$pdfData['Vermogensbeheerders']['website']));



$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
$transactionDate = '';
$transactionDate = date('d-m-Y', db2jul($transactionData['datum']));

$pdf->Ln(15);
$pdf->Ln(5);

$pdf->multicell(200, 10, $data['tekstblok1']);


$pdf->SetWidths(array(120, 20, 40));
$pdf->SetAligns(array('L', 'R', 'R'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
/** maak de header */
$pdf->Row((array)array(
  'Omschrijving',
  'Btw',
  'Bedrag',
  )
);
$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
/** maak de regel */

$totEx = 0;
$btwTot[6] = 0;
$btwTot[9] = 0;
$btwTot[21] = 0;
foreach ( $data['factuur'] as $row ) {
  if (  empty ($row['ond']) || empty ($row['bedrag'])) {continue;}

  $btw[$row['btw']][] = $row;

  $pdf->Row((array)array(
    $row['ond'],
    $row['btw'] .'%',
    EURO . ' ' . number_format($row['bedrag'], 2, ',', '.'),
  ));

  $totEx =  $totEx + $row['bedrag'];
}


$pdf->SetWidths(array(120, 20, 40));
$pdf->SetAligns(array('L', 'R', 'R'));
$pdf->CellBorders = array(null, null, 'TS');
$pdf->Row((array)array(' ','Totaal', EURO . ' ' . number_format($totEx, 2, ',', '.')));
$pdf->Ln(15);

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

$pdf->CellBorders = array(null, null, 'TS');
$pdf->Row((array)array(' ','Totaal', EURO . ' ' . number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));

$pdf->Ln(25);
$pdf->multicell(200, 10, $data['tekstblok2']);
$pdf->Ln(5);
$pdf->multicell(200, 10, 'Wij vertrouwen er op u bij deze voldoende te hebben ge' . iconv("UTF-8", "ISO-8859-1", "ï") . 'nformeerd.');


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