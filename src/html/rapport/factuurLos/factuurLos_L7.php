<?php

include_once ($__appvar['rapportdir'] . '/include/PDFRapport_headers_L7.php');
$pdf->nawPrinted = true;
$participantData = $pdf;
$rapportLogo = $pdf->rapport_logo;
$pdf->rapport_type = "FACTUUR";

loadLayoutSettings($pdf, $pdf->portefeuille);
$pdf->AddPage();
$pdf->SetAligns(array('L'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);

$pdf->marge = 30;
$pdf->rowHeight=4;
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);

//if(is_file($pdf->rapport_logo))
//{
//  $factor=0.15;
//  $xSize=1000*$factor;//$x=885*$factor;
//  $ySize=620*$factor;//$y=849*$factor;
//  $xStart=(210)/2-($xSize/2);
//  $pdf->Image($pdf->rapport_logo, $xStart, 5, $xSize);
//}
$DB = new DB();
$DB->SQL("SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE Vermogensbeheerders.Vermogensbeheerder='" . $pdfData['Vermogensbeheerders']['Vermogensbeheerder'] . "'");
$vermData = $DB->lookupRecord();
$pdf->rapport_logo = $rapportLogo;

//factuur 1

factuurKop($pdf,$vermData);



$pdf->SetY($pdf->getY() +20);
// start eerste block

$pdf->SetWidths(array(100,80));
$pdf->SetAligns(array("L","L"));



$pdf->SetWidths(array(100,80));
$pdf->SetAligns(array("L","L"));
$pdf->SetFont($pdf->rapport_font,"B",$pdf->rapport_fontsize+1);
$pdf->row(array('Vertrouwelijk'));
$pdf->SetFont($pdf->rapport_font,"",$pdf->rapport_fontsize+1);
$pdf->row(array($crmData['clientNaam']));
if ($crmData['clientNaam1'] !='')
  $pdf->row(array($crmData['clientNaam1']));
$pdf->row(array($crmData['clientAdres']));
$plaats='';
if($crmData['clientPostcode'] != '')
  $plaats .= $crmData['clientPostcode']." ";
$plaats .= $crmData['clientWoonplaats'];
$pdf->row(array($plaats));
if($crmData['clientLand'])
  $pdf->row(array($crmData['clientLand']));
$pdf->SetY($pdf->getY() +20);
$pdf->ln();
$factuurnummer = $data['factuurnr'];

$db=new DB();
$query="SELECT debiteurnr FROM CRM_naw WHERE portefeuille='".$crmData['Portefeuille']."'";
$db->SQL($query);
$crmData=$db->lookupRecord();


$pdf->SetFont($pdf->rapport_font,"",$pdf->rapport_fontsize+1);
$pdf->SetWidths(array(30,100));
$pdf->SetAligns(array("L","L"));
$tussenruimte =1;
$pdf->setY(96);
//rapdate		$pdf->row(array("Datum:", date("j",db2jul($crmData['datumTot']))." ".$pdf->__appvar["Maanden"][date("n",db2jul($crmData['datumTot']))]." ".date("Y",db2jul($crmData['datumTot']))));
$pdf->row(array("Datum:", $data['factuurdatum']));
$pdf->ln($tussenruimte);
$pdf->row(array("Debiteur:",$crmData['debiteurnr']));
$pdf->ln($tussenruimte);
$pdf->row(array("Notanummer", $factuurnummer));
$pdf->ln($tussenruimte);



$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
$transactionDate = '';
$transactionDate = date('d-m-Y', db2jul($transactionData['datum']));

$pdf->Ln(5);

$pdf->multicell(150, 10, $data['tekstblok1']);


$pdf->SetWidths(array(90, 35, 5,20));
$pdf->SetAligns(array('L', 'R', 'R', 'R'));

$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
/** maak de header */

$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
/** maak de regel */

$totEx = 0;
$btwTot[6] = 0;
$btwTot[9] = 0;
$btwTot[21] = 0;
$factuurLinesCount = 0;
foreach ( $data['factuur'] as $row ) {
  if (  empty ($row['ond']) || empty ($row['bedrag'])) {continue;}

  $btw[$row['btw']][] = $row;
  $factuurLinesCount++;
  $pdf->Row((array)array(
    $row['ond'],
    '',
    EURO,
    number_format($row['bedrag'], 2, ',', '.'),
  ));

  $totEx =  $totEx + $row['bedrag'];
}

if ( $factuurLinesCount > 1 ) {
  $pdf->Ln(1);
  $pdf->CellBorders = array(null, null, array('TS'), array('TS'));
  $pdf->Row((array)array('Subtotaal','', EURO , number_format($totEx, 2, ',', '.')));
  $pdf->Ln(5);
}

$pdf->SetWidths(array(90, 35, 5,20));
$pdf->SetAligns(array('L', 'R', 'R', 'R'));
$pdf->CellBorders = array(null, null, 'TS');
//$pdf->Row((array)array(' ','Totaal', EURO . ' ' . number_format($totEx, 2, ',', '.')));

$pdf->CellBorders = null;
ksort ($btw);
foreach ( $btw as $btwNum => $btwdatas ) {
  if ( $btwNum == 6 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
    }
    $pdf->Row((array)array('BTW 6%', '', EURO, number_format($btwTot[6], 2, ',', '.')));
  }

  if ( $btwNum == 9 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
    }
    $pdf->Row((array)array('BTW 9%', '', EURO, number_format($btwTot[9], 2, ',', '.')));
  }

  if ( $btwNum == 21 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
    }
    $pdf->Row((array)array('BTW 21%', '', EURO, number_format($btwTot[21], 2, ',', '.')));
  }
}
$pdf->Ln(1);
$pdf->CellBorders = array(null, null, array('UU', 'TS'), array('UU', 'TS'));
$pdf->Row((array)array('Totaal bedrag', '', EURO, number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));


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