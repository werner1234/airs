<?php
global $__appvar;
$tussenruimte =0;
//debug($crmData['Portefeuille']);
//debug($pdf->waarden);
$pdf->underlinePercentage=0.8;
$pdf->brief_font='Times';
$pdf->rapport_type = "FACTUUR";
$pdf->AddPage('P');

$rowHeightBackup=$pdf->rowHeight;
$pdf->rowHeight = 5;
$pdf->SetFont($pdf->rapport_font,'',$pdf->rapport_fontsize);


if(is_file($pdf->rapport_logo))
{
  $factor=0.02;
  $xSize=1931*$factor;
  $ySize=701*$factor;

  $logoX=$pdf->w-$xSize-$pdf->marge*2;
  $pdf->Image($pdf->rapport_logo, $logoX, $pdf->marge*2, $xSize, $ySize);
}


$pdf->SetY(45);
$pdf->SetWidths(array(22,150));
$pdf->SetAligns(array('R','L'));
//debug($crmData);
$pdf->row(array('',$crmData['naam']));
if($crmData['naam1'] <> '')
  $pdf->row(array('',$crmData['naam1']));
$pdf->row(array('',$crmData['adres']));
if($crmData['pc'] != '')
  $plaats = $crmData['pc'] . "  " .$crmData['plaats'];
else
  $plaats = $crmData['plaats'];
$pdf->row(array('',$plaats));
$pdf->row(array('',$crmData['land']));

$pdf->SetY(80);


$pdf->row(array('',"Amsterdam, ".date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y") ));
$pdf->ln(12);
$pdf->row(array('',"Factuurnummer: ".$data['factuurnr']."" ));
$pdf->row(array('',$data['factuuronderwerp'] ));
$pdf->ln(12);
$pdf->SetAligns(array('R','L'));
$pdf->row(array('',$crmData['verzendAanhef'].','));

$pdf->ln(12);




$pdf->SetAligns(array('R','L'));
$pdf->row(array('',$data['tekstblok1']));
$pdf->ln();
//$pdf->ln();
$pdf->SetWidths(array(22,100,10,30,50));
$pdf->SetAligns(array('R','L','L','R'));
//	$pdf->row(array('','Aanvangsvermogen per '.$vanafTxt.':',"EUR".$this->formatGetal($this->waarden['portefeuilleVerdeling']['beginWaarde']['totaal'],0).""));
//	$pdf->row(array('','Eindvermogen per '.$totTxt.':',"EUR".$this->formatGetal($this->waarden['portefeuilleVerdeling']['eindWaarde']['totaal'],0).""));
//  $pdf->CellBorders = array('','',array('TS','UU'));
//  $pdf->row(array('','Gemiddeld belegd vermogen '.$totTxt,"EUR",number_format($crmData['gemiddeldeVermogen'],2, ',', '.').""));



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

  $pdf->row(array('', $row['ond'],"EUR",number_format($row['bedrag'], 2, ',', '.')));
  $pdf->ln($tussenruimte);
//  $pdf->Row(array(' ',$row['ond'],'EUR',   ' ' . number_format($row['bedrag'], 2, ',', '.')));
  $totEx =  $totEx + $row['bedrag'];


}


$pdf->CellBorders = null;
$pdf->ln(3);

if ( ! empty ($btwTot[6]) ) {
  if ( empty ($btwTot[9]) && empty ($btwTot[21]) ){$pdf->CellBorders = array('','','','','U');}
  $pdf->Row(array('','BTW (6%) ', 'EUR' , number_format($btwTot[6], 2, ',', '.')));
  $pdf->ln($tussenruimte);
}

if ( ! empty ($btwTot[9]) ) {
  if ( empty ($btwTot[6]) && empty ($btwTot[21])  ){$pdf->CellBorders = array('','','','','U');}
  if ( ! empty ($btwTot[6]) && empty ($btwTot[21])  ){$pdf->CellBorders = array('','','','','U');}
  $pdf->Row(array('','BTW (9%) ', 'EUR' , number_format($btwTot[9], 2, ',', '.')));
  $pdf->ln($tussenruimte);
}

if ( ! empty ($btwTot[21]) ) {
  if ( empty ($btwTot[9]) && ! empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  if ( ! empty ($btwTot[9]) &&  empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  if ( ! empty ($btwTot[9]) &&  !empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  if ( empty ($btwTot[9]) &&  empty ($btwTot[6]) ){$pdf->CellBorders = array('','','','','U');}
  $pdf->Row(array('','BTW (21%)', 'EUR' , number_format($btwTot[21], 2, ',', '.')));
}



$pdf->ln($tussenruimte);
//	$pdf->row(array("Subtotaal","EUR",$this->formatGetal($this->waarden['beheerfeePerPeriode']+$this->waarden['btw'],2)));
//	$pdf->ln($tussenruimte);
$pdf->Line($pdf->marge+132,$pdf->GetY(),$pdf->marge +162 ,$pdf->GetY());
//$pdf->row(array("Totaal","EUR",number_format($totEx, 2, ',', '.')));
$pdf->Row(array('','Totaal', 'EUR' , number_format($totEx + $btwTot[21] + $btwTot[9] + $btwTot[6], 2, ',', '.')));






//	$pdf->CellBorders = array();
//	$pdf->ln();
////$pdf->ln();
//	$pdf->SetWidths(array(22,100,10,30,50));
//$pdf->SetAligns(array('R','L','L','R'));
////  $pdf->row(array('','Vermogensbeheervergoeding '.$this->waarden['kwartaal'].'e kwartaal '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],2).'%',"EUR",$this->formatGetal($this->waarden['beheerfeeBetalen'],2).""));//-$this->waarden['administratieBedrag']
// // $pdf->row(array('','Account Kosten',"EUR ".$this->formatGetal($this->waarden['administratieBedrag'],2).""));
//$pdf->CellBorders = array('','','T','T');
////$pdf->SetFont($pdf->rapport_font,'b',$pdf->rapport_fontsize);
////  $pdf->row(array('','Subtotaal',"EUR",$this->formatGetal($this->waarden['beheerfeeBetalen'],2).""));
//$pdf->CellBorders = array();
//$pdf->SetFont($pdf->rapport_font,'',$pdf->rapport_fontsize);
//  $pdf->ln();
// // listarray($this->waarden);
////  $pdf->row(array('','BTW ('.$this->formatGetal($this->waarden['btwTarief'],2).'%)',"EUR",$this->formatGetal($this->waarden['btw'],2).""));
//$pdf->CellBorders = array('','','T','T');
//$pdf->SetFont($pdf->rapport_font,'b',$pdf->rapport_fontsize);
////  $pdf->row(array('','Totaal',"EUR",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));//-$this->waarden['BeheerfeeBedragBuitenBTWPeriode']
//$pdf->SetFont($pdf->rapport_font,'',$pdf->rapport_fontsize);
//  $pdf->ln();
//  $pdf->CellBorders = array();
//

//  $pdf->ln();
//$pdf->CellBorders = array('','',array('TS','UU'));
// $pdf->row(array('','Totaalbedrag',"EUR ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2).""));
$pdf->CellBorders = array();
$pdf->ln(12);
$pdf->SetWidths(array(22,150));
$pdf->row(array('',$data['tekstblok2']));

//	$pdf->row(array('',"De beheervergoeding wordt berekend over het gewogen gemiddelde van het beheerdvermogen.
//Conform de vermogensbeheerovereenkomst is Mpartners gemachtigd ".$this->waarden['depotbankOmschrijving']."
//opdracht te geven het bovenstaande bedrag ten laste van uw rekening af te schrijven."));
$pdf->ln();

//$pdf->SetWidths(array(22,150));
//$pdf->SetAligns(array('R','L','R','L'));

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

//$pdf->SetAligns(array('L','L','L','L'));
//$pdf->SetWidths(array(22,150));
//$pdf->row(array('',"Bovengenoemde vergoeding zullen wij ten laste brengen van rekeningnummer ".$rekeningnr));

$pdf->ln();
$pdf->row(array('',"Met vriendelijke groet,


Mpartners"));


$autoPageBreakBackup=$pdf->AutoPageBreak;
$pdf->AutoPageBreak=false;
$pdf->setY(275);
$pdf->rowHeight=$rowHeightBackup;
$pdf->SetFont($pdf->rapport_font,'',7);
$pdf->SetWidths(array(22,40,40,40,40));
$pdf->SetAligns(array('L','C','C','C','C'));
$pdf->SetTextColor(100,100,100);
$pdf->row(array('','IBAN: NL60 GILL 0211622907','BIC: GILLNLQA ','BTW Nummer: NL8223.23.655.B01','KVK: 34389387'));
$pdf->SetWidths(array(22,40,40,40,40));
$pdf->row(array('','','Vergunninghouder AFM / FSMA','Ingeschreven in het register DSI'));
$pdf->SetFont($pdf->rapport_font,'',$pdf->rapport_fontsize);
$pdf->SetTextColor(0,0,0);
$pdf->AutoPageBreak=$autoPageBreakBackup;




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