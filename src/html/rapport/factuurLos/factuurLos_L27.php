<?php
$pdf->nawPrinted = true;
$participantData = $pdf;

$pdf->rapport_type = "FACTUUR";
$pdf->rapport_voettext='';
$pdf->AddPage();
$pdf->SetAligns(array('L'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);

$brief_font='Arial';

global $__appvar;
$logo=$__appvar['basedir']."/html/rapport/logo/logo_fintessa.jpg";
if(is_file($logo))
{
  $pdf->Image($logo,$pdf->marge*2 , 10, 54, 15);
}


$query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.btwnr,
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
FROM CRM_naw WHERE  CRM_naw.id = '" . $data['deb_id'] . "' ";

$DB->SQL($query);
$crmData = $DB->lookupRecord();
$pdf->SetWidths(array(25-$pdf->marge,140));
$pdf->SetAligns(array('R','L','L','R','R'));
$pdf->rowHeight = 5;
$pdf->SetY(42);
$pdf->SetFont($brief_font,'',10);
$pdf->row(array('',$crmData['verzendAanhef']));
$pdf->row(array('',$crmData['verzendAdres']));
$plaats=$crmData['verzendPc'];
if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
$pdf->row(array('',$plaats));
$pdf->row(array('',$crmData['verzendLand']));

$pdf->SetY(80);
$pdf->SetWidths(array(25-$pdf->marge,100,60));
$pdf->row(array('','Factuurdatum: '.$data['factuurdatum'],"Factuurnummer: ".$data['factuurnr']));
$pdf->ln();

$pdf->SetFont($brief_font,'B',10);
$pdf->SetWidths(array(25-$pdf->marge,160));
$pdf->row(array('',$data['factuuronderwerp']));
$pdf->SetFont($brief_font,'',10);
$pdf->Ln(5);
$pdf->setX(25);
$pdf->multicell(200, 10, $data['tekstblok1']);


$pdf->SetWidths(array(25-$pdf->marge,120, 20, 40));
$pdf->SetAligns(array('','L', 'R', 'R'));
$pdf->SetFont($pdf->rapport_font, 'B', $pdf->rapport_fontsize);
/** maak de header */
$pdf->Row(array('','Omschrijving','Bedrag'));
$pdf->SetFont($pdf->rapport_font, '', $pdf->rapport_fontsize);
/** maak de regel */

$totEx = 0;
$btwTot[6] = 0;
$btwTot[9] = 0;
$btwTot[21] = 0;
foreach ( $data['factuur'] as $row )
{
  if (  empty ($row['ond']) || empty ($row['bedrag'])) {continue;}
  $btwTot[$row['btw']] +=  ($row['bedrag'] * $row['btw']/100);
  $pdf->Row(array(' ',$row['ond'],  EURO . ' ' . number_format($row['bedrag'], 2, ',', '.')));
  $totEx =  $totEx + $row['bedrag'];
}


$pdf->SetWidths(array(25-$pdf->marge,120, 20, 40));
$pdf->SetAligns(array('L', 'L', 'R', 'R'));
$pdf->CellBorders = array(null, null, 'TS');
$pdf->SetAligns(array('L', 'R', 'R', 'R'));
$pdf->Row(array('','Subtotaal', EURO . ' ' . number_format($totEx, 2, ',', '.')));
$pdf->Ln(15);

$pdf->CellBorders = null;


$pdf->CellBorders = array(null, null, 'TS');
$pdf->Row(array(' ','BTW', EURO . ' ' . number_format($btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));
$pdf->ln(2);
$pdf->CellBorders = array(null, null, 'TS');
$pdf->Row(array(' ','Totaal', EURO . ' ' . number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));
$pdf->SetAligns(array('L', 'L'));
$pdf->Ln(25);
$pdf->SetWidths(array(25-$pdf->marge,180));
$pdf->Row(array(' ',$data['tekstblok2']));
$pdf->Ln(5);
$pdf->ln();

$pdf->row(array('',"Met vriendelijke groet,




Ing. Mark W. Sombekke CCO
Directeur Operationele Zaken
Fintessa vermogensbeheer B.V.

"));


$pdf->SetWidths(array(25-$pdf->marge,160));
$pdf->SetTextColor($pdf->rapport_grafiek_color['r'],$pdf->rapport_grafiek_color['g'],$pdf->rapport_grafiek_color['b']);
$pdf->rowHeight = 5;
$trigger=$pdf->PageBreakTrigger;
$pdf->PageBreakTrigger=$pdf->PageBreakTrigger+30;
$pdf->setY(-22);
$pdf->SetAligns(array('R','C'));
$pdf->SetFont($brief_font,'',8);
$pdf->row(array('',"Fintessa BV Amserdamsestraatweg 37, 3744 MA Baarn   Postbus 418,37040 AK Baarn
Telefoon 035 5431450  Fax 035 5426006  info@fintessa.nl  www.fintessa.nl
K.v.K. 32123885   btw NL818017338B01  IBAN: NL72ABNA0578964813
Fintessa BV staat geregistreerd bij de Autoriteit Financiële Markten"));
$pdf->PageBreakTrigger=$trigger;



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