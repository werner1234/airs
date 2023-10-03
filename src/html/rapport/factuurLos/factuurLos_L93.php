<?php

global $__appvar;
$pdf->rapport_type = "FACTUUR";


if (file_exists(FPDF_FONTPATH . 'Frutiger.php'))
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
$fontsize = 9;

$pdf->AddPage('P');
$pdf->nextFactuur = true;

$db=new DB();
$query="SELECT * FROM CRM_naw WHERE  CRM_naw.id = '" . $data['deb_id'] . "' ";

$db->SQL($query);
$crmData=$db->lookupRecord();


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
  $xSize = 50;

  $pdf->Image($pdf->rapport_logo, 210 - $xSize - $logoYpos, $logoYpos, $xSize);
}


$extraMarge = 25 - $pdf->marge;
$pdf->SetY(55 - 8);
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
  $plaats .= "  " . $crmData['plaats'];
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
$pdf->row(array('', 'Factuurdatum:', $data['factuurdatum']));
$pdf->row(array('', 'Portefeuille:', $crmData['portefeuille']));

$pdf->SetY(135);

$pdf->SetAligns(array("L", "L"));

$pdf->SetWidths(array($extraMarge,140));
$pdf->row(array('',$data['tekstblok1']));


$pdf->ln();
$pdf->CellBorders = array();

$pdf->ln(12);
//$pdf->waarden['BeheerfeeBedragBuitenFee']
//$pdf->waarden['rekenvermogen']
$pdf->SetAligns(array("L", "L", "L", "R"));
$pdf->SetWidths(array($extraMarge, 100, 12, 30));







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
  $pdf->ln(2);
  $totEx =  $totEx + $row['bedrag'];
}


if ( $numRowCounter > 1 ) {

  $pdf->CellBorders = null;
  ksort ($btw);
  foreach ( $btw as $btwNum => $btwdatas ) {
    if ( $btwNum == 6 ) {
      foreach ( $btwdatas as $btwdata) {
        $btwTot[6] = $btwTot[6] + ($btwdata['bedrag'] / 100 * 6);
      }
      $pdf->Row((array)array('', 'BTW 6%','EUR', ' ' . number_format($btwTot[6], 2, ',', '.')));
      $pdf->ln(2);
    }

    if ( $btwNum == 9 ) {
      foreach ( $btwdatas as $btwdata) {
        $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
      }
      $pdf->Row((array)array('','BTW 9%' , 'EUR',  ' ' . number_format($btwTot[9], 2, ',', '.')));
      $pdf->ln(2);
    }

    if ( $btwNum == 21 ) {
      foreach ( $btwdatas as $btwdata) {
        $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
      }
      $pdf->Row((array)array('','BTW 21%','EUR',  ' ' . number_format($btwTot[21], 2, ',', '.')));
      $pdf->ln(2);
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
      $pdf->Row((array)array('', 'BTW 6%','EUR', ' ' . number_format($btwTot[6], 2, ',', '.')));
      $pdf->ln(2);
    }

    if ( $btwNum == 9 ) {
      foreach ( $btwdatas as $btwdata) {
        $btwTot[9] = $btwTot[9] + ($btwdata['bedrag'] / 100 * 9);
      }
      $pdf->Row((array)array('','BTW 9%' , 'EUR',  ' ' . number_format($btwTot[9], 2, ',', '.')));
      $pdf->ln(2);
    }

    if ( $btwNum == 21 ) {
      foreach ( $btwdatas as $btwdata) {
        $btwTot[21] = $btwTot[21] + ($btwdata['bedrag'] / 100 * 21);
      }
      $pdf->Row((array)array('','BTW 21%','EUR',  ' ' . number_format($btwTot[21], 2, ',', '.')));
      $pdf->ln(2);
    }
  }
}


$pdf->Line($pdf->marge+$extraMarge+100, $pdf->GetY() + 1, $pdf->marge+$extraMarge+100+12+30, $pdf->GetY() + 1);
$pdf->ln(2);
$pdf->Row((array)array('','TOTAAL','EUR', ' ' . number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));




$pdf->CellBorders=array('','','','');


$pdf->ln(20);
$pdf->SetAligns(array('L', 'L'));
$pdf->SetWidths(array($extraMarge, 150));
$pdf->row(array('', $data['tekstblok1']));
$pdf->SetY(235);
$pdf->ln(3);

$pdf->SetFont($font, "", $fontsize);

$pdf->SetWidths(array($extraMarge, 50,50,50));
$pdf->SetAligns(array("L", "L", "L", "L"));

$pdf->row(array('', $vermData['Naam'], $vermData['Telefoon'],'NL78ABNA0877268223'));
$pdf->row(array('', $vermData['Adres'],'+31 6 36482806','KvK 77808169'));
$pdf->row(array('', $vermData['Woonplaats'],$vermData['Email'],'BTW nr: NL861153960B01'));


$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
$pdf->AutoPageBreak = true;
$pdf->SetTextColor(0, 0, 0);










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

  $pdf->Output(( isset ($data['fileName']) ? $data['fileName'] : 'factuur' ) . '.pdf', 'I');
}
else
{
  echo $berichten;
}