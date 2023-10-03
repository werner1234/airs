<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/01/27 17:30:20 $
File Versie					: $Revision: 1.6 $

$Log: Factuur_L92.php,v $

*/


global $__appvar;
$pdf->rapport_type = "FACTUUR";

/*
if (file_exists(FPDF_FONTPATH . 'Frutiger.php1'))
{
  if (!isset($pdf->fonts['frutiger']))
  {
    $pdf->AddFont('frutiger', '', 'Frutigerl.php');
    $pdf->AddFont('frutiger', 'B', 'Frutigerb.php');
    $pdf->AddFont('frutiger', 'R', 'Frutiger.php');
    $pdf->AddFont('frutiger', 'BI', 'Frutigerbi.php');
  }
  $font = 'frutiger';
}
else
{
  $font = 'Times';
}
*/
$font = $pdf->rapport_font;

$fontsize = 9;

$pdf->AddPage('P');
$pdf->nextFactuur = true;

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
WHERE Vermogensbeheerders.Vermogensbeheerder='" . $pdf->portefeuilledata['Vermogensbeheerder'] . "'");
$vermData = $DB->lookupRecord();
$pdf->SetTextColor($pdf->rapport_fontcolor['r'], $pdf->rapport_fontcolor['g'], $pdf->rapport_fontcolor['b']);
$logo = $__appvar['basedir'] . "/html/rapport/logo/" . $pdf->portefeuilledata['Logo'];
if (is_file($logo))
{
  $logoYpos = 20;
  $xSize = 60;
  $pdf->Image($pdf->rapport_logo, 25, $logoYpos, $xSize);
  $pdf->SetFont($font, "", $fontsize);
}

$nu = date('j', strtotime($data['factuurdatum'])) . ' ' . vertaalTekst($__appvar['Maanden'][date('n', strtotime($data['factuurdatum']))], $pdf->rapport_taal) . ' ' . date('Y', strtotime($data['factuurdatum']));


$pdf->DB = new DB();
$query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.Portefeuille,
CRM_naw.verzendPaAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.id = '" . $data['deb_id'] . "'  ";
$pdf->DB->SQL($query);
$crmData = $pdf->DB->lookupRecord();

$extraMarge = 25 - $pdf->marge;
$pdf->SetY(55);
$pdf->SetWidths(array($extraMarge, 100, 80));
$pdf->SetFont($font, "", 10);
$pdf->SetAligns(array("L", "L", "L", "R"));
$pdf->row(array('', $crmData['naam']));
$pdf->ln(1);
if( $crmData['naam1']<>'')
{
  $pdf->row(array('', $crmData['naam1']));
  $pdf->ln(1);
}
$pdf->row(array('', $crmData['adres']));
$pdf->ln(1);
$plaats = '';
$plaats = $crmData['pc'];
if ($crmData['plaats'] != '')
{
  $plaats .= "    " . $crmData['plaats'];
}
$pdf->row(array('', $plaats));
$pdf->ln(1);
$pdf->row(array('', $crmData['land']));

$pdf->SetY(105);
$pdf->SetFont($font, "B", 12);
$pdf->row(array('', "FACTUUR"));
$pdf->SetFont($font, "", 10);
$pdf->ln(2);

$pdf->SetWidths(array($extraMarge, 30, 80));
$pdf->row(array('', 'Factuurnummer:', $data['factuurnr']));
$pdf->row(array('', 'Factuurdatum:', $nu));
$pdf->row(array('', 'Portefeuille:', $crmData['Portefeuille'] ));
if ( isset($data['factuuronderwerp']) && ! empty ($data['factuuronderwerp']) ) {
  $pdf->Row((array)array('', 'Onderwerp: ', $data['factuuronderwerp']));
}


$pdf->SetWidths(array($extraMarge, 150));

$pdf->ln(4);
$pdf->SetAligns(array('L', 'L'));
$pdf->row(array('', $data['tekstblok1']));
$pdf->ln(3);

$pdf->SetY(145);
$pdf->SetWidths(array($extraMarge, 100, 12, 30));
$lijnx2 = 210 - 38;



//listarray($pdf->waarden['basisRekenvermogen']);
$pdf->SetWidths(array($extraMarge, 65, 35));
$pdf->SetAligns(array("L", "L", "L", "R"));
$lineYstart=$pdf->getY()-1;
$lineXtussen=$pdf->marge+$extraMarge+100;
$pdf->line($pdf->marge+$extraMarge,$lineYstart,$lijnx2,$lineYstart);
$pdf->row(array(''));
$pdf->SetWidths(array($extraMarge, 100, 12, 30));
$pdf->line($pdf->marge+$extraMarge,$pdf->getY()+1,$lijnx2,$pdf->getY()+1);

$pdf->line($pdf->marge+$extraMarge+65,$lineYstart,$pdf->marge+$extraMarge+65,$pdf->getY()+1);
$pdf->SetAligns(array("L", "L", "L", "R"));
$pdf->ln(8);
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
    EURO,
    number_format($row['bedrag'], 2, ',', '.'),
  ));



  $totEx =  $totEx + $row['bedrag'];
}


$pdf->ln(8);
$pdf->SetAligns(array("L", "R", "L", "R"));

$pdf->CellBorders = null;
ksort ($btw);
foreach ( $btw as $btwNum => $btwdatas ) {
  if ( $btwNum == 6 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
    }
    $pdf->Row((array)array(' ','btw 6%', EURO , number_format($btwTot[6], 2, ',', '.')));
  }

  if ( $btwNum == 9 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
    }
    $pdf->Row((array)array(' ','btw 9%', EURO , number_format($btwTot[9], 2, ',', '.')));
  }

  if ( $btwNum == 21 ) {
    foreach ( $btwdatas as $btwdata) {
      $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
    }
    $pdf->Row((array)array(' ', 'btw 21%', EURO , number_format($btwTot[21], 2, ',', '.')));
  }
}


$pdf->Line($lineXtussen, $pdf->GetY() + 1, $lijnx2, $pdf->GetY() + 1);
$pdf->ln(12);
$pdf->Row((array)array(' ','TOTAAL ', EURO , number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));
$lineYstop=$pdf->getY()+1;
$pdf->line($pdf->marge+$extraMarge,$lineYstop,$lijnx2,$lineYstop);
$pdf->line($pdf->marge+$extraMarge,$lineYstart,$pdf->marge+$extraMarge,$lineYstop);
$pdf->line($lijnx2,$lineYstart,$lijnx2,$lineYstop);
$pdf->line($lineXtussen,$lineYstart,$lineXtussen,$lineYstop);

$pdf->ln(2);
$pdf->SetAligns(array('L', 'L'));
$pdf->SetWidths(array($extraMarge, 150));
$pdf->ln(20);
$pdf->row(array('', $data['tekstblok2']));
$pdf->ln(3);

//""
$pdf->AutoPageBreak = false;
$pdf->SetY(297 - 19);
$pdf->SetFont($pdf->rapport_font, '', 9);
$pdf->Cell(210, 5, '' . $vermData['Naam'] . ' · ' . $vermData['Adres'] . ' · ' . $vermData['Woonplaats'] . ' · ' . $vermData['Telefoon'] . ' · ' . $vermData['Email'] . ' · ' . $vermData['website'] . '', 0, 1, 'C');
//$pdf->Cell(210, 5, 'Tel: ' . $vermData['Telefoon'] . ' – ' . $vermData['Email'] . ' – ' . $vermData['website'] . '', 0, 1, 'C');
$pdf->Cell(210, 5, 'KVK nr. 34364453 · BTW nr: 821476531B01', 0, 1, 'C');
$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
$pdf->AutoPageBreak = true;
$pdf->SetTextColor(0, 0, 0);












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