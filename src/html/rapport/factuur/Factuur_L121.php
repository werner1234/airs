<?php
global $__appvar;

$rowHeightBackup=$this->pdf->rowHeight;
$this->pdf->rowHeight = 5;
$this->pdf->underlinePercentage=0.8;
$this->pdf->brief_font='Times';
$this->pdf->rapport_type = "FACTUUR";
$this->pdf->AddPage('P');
$this->pdf->SetFont($this->pdf->rapport_font,'',10);

$vanaf=db2jul($this->waarden['datumVan']);
$tot=db2jul($this->waarden['datumTot']);

if(is_file($this->pdf->rapport_logo))
{
  $xSize=50;
  $logopos=($this->pdf->w /2 -25 );
  $this->pdf->Image($this->pdf->rapport_logo, $logopos, 5, $xSize);
}
$db=new DB();
$query = "SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.rekening,
Vermogensbeheerders.bank,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE
Vermogensbeheerders.Vermogensbeheerder = '".$this->waarden['Vermogensbeheerder']."'";
$db->SQL($query);
$vermData = $db->lookupRecord();
//listarray($vermData);

$this->pdf->SetY(25);
$this->pdf->SetWidths(array(150,150));
$this->pdf->SetAligns(array('R','L'));
$this->pdf->SetTextColor(164,172,179);
$this->pdf->SetFont($this->pdf->brief_font,'B',10);
$this->pdf->row(array('',$vermData['Naam']));
$this->pdf->SetFont($this->pdf->rapport_font,'',10);
$this->pdf->row(array('',$vermData['Adres']));
$this->pdf->row(array('',$vermData['Woonplaats']));
$this->pdf->ln();
$this->pdf->row(array('',$vermData['Telefoon']));
$this->pdf->row(array('',$vermData['Email']));
$this->pdf->row(array('',$vermData['website']));

$this->pdf->ln(10);

$this->pdf->SetFont($this->pdf->brief_font,'B',10);
$this->pdf->row(array('','Datum'));
$this->pdf->SetFont($this->pdf->rapport_font,'',10);
$dagVertaling=array('', 'maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag');
$this->pdf->row(array('', $dagVertaling[date('N')] . ' ' . date('j') . ' ' . $this->pdf->__appvar["Maanden"][date('n')] . ' ' . date('Y') ) );
$this->pdf->SetTextColor(0,0,0);


$kwartalen = array('null','eerste','tweede','derde','vierde');

$soort = 'adviesfee';
if($this->waarden['SoortOvereenkomst'] === 'Beheer' ) {
  $soort = 'beheerfee';
}

$this->pdf->SetY(50);
$this->pdf->SetWidths(array(22,150));
$this->pdf->SetAligns(array('R','L'));

$this->pdf->row(array('',$this->waarden['clientNaam']));
if($this->waarden['clientNaam1'] <> '')
  $this->pdf->row(array('',$this->waarden['clientNaam1']));
$this->pdf->row(array('',$this->waarden['clientAdres']));
if($this->waarden['clientPostcode'] != '')
  $plaats = $this->waarden['clientPostcode'] . " " .$this->waarden['clientWoonplaats'];
else
  $plaats = $this->waarden['clientWoonplaats'];
$this->pdf->row(array('',$plaats));
$this->pdf->row(array('',$this->waarden['clientLand']));

$this->pdf->SetY(90);
$this->pdf->SetAligns(array('R','L'));

$factuurNr=sprintf("%03d",$this->waarden['factuurNummer']);
$this->pdf->row(array('',"Berekening $soort ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",$tot)));
$this->pdf->row(array('',"Depotnummer ".$this->waarden['portefeuille']));

//		$this->pdf->row(array('',"Factuurnummer: ".date("Y",$tot).".$factuurNr"));
$this->pdf->row(array('',"Factuurnummer: ".date("Y").".$factuurNr"));
$this->pdf->SetAligns(array('R','L'));


$this->pdf->ln();


$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$pdf->rapport_taal)." ".date("Y",$vanaf);
$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$pdf->rapport_taal)." ".date("Y",$tot);
$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;

$berekeningVanaf=$vanaf;
$pstartJul=db2jul($this->waarden['portefeuilledata']['Startdatum']);
if($pstartJul>$vanaf)
  $berekeningVanaf=$pstartJul;


//listarray($this->waarden);


$this->pdf->SetAligns(array('R','L'));
$this->pdf->row(array('','Belegd vermogen exclusief liquiditeiten.'));
$this->pdf->ln();
$this->pdf->SetWidths(array(22,70,25,25,25));
$this->pdf->SetAligns(array('R','L','R','R','R'));
$this->pdf->row(array('','','','Aantal dagen'));




$staffelTotaal=$this->waarden['beheerfeePerPeriodeNor'];
$ultimoVanaf=$berekeningVanaf;
$ultimoTot=$this->waarden['maandsData_1'];
if($berekeningVanaf>$ultimoTot)
  $ultimoTot=$berekeningVanaf;
$fondsWaarde=$this->waarden['maandsFondsUitsluitingen'][date('Y-m-d',$this->waarden['maandsData_1'])];
$dagen=round(($ultimoTot-$ultimoVanaf)/86400);
if(date('d-m',$ultimoVanaf) =='01-01')
  $dagen++;
//echo "<br>\n".date('d-m-Y',$ultimoVanaf)." -> ".date('d-m-Y',$ultimoTot)."<br>\n";
$this->pdf->row(array('','Belegd vermogen ultimo '.vertaalTekst($__appvar["Maanden"][date("n",$this->waarden['maandsData_1'])],$pdf->rapport_taal),$this->formatGetal($this->waarden['maandsWaarde_1']-$fondsWaarde,0),$dagen));
//echo "<br>".$this->formatGetal($this->waarden['maandsWaarde_1']-$fondsWaarde,0)."=".$this->waarden['maandsWaarde_1']."-$fondsWaarde <br>\n";

if($berekeningVanaf>$this->waarden['maandsData_1'])
  $ultimoVanaf=$berekeningVanaf;
else
  $ultimoVanaf=$this->waarden['maandsData_1'];

if($pstartJul>$ultimoVanaf)
  $ultimoVanaf=$pstartJul;

if($berekeningVanaf>$ultimoTot)
  $ultimoTot=$berekeningVanaf;
else
  $ultimoTot=$this->waarden['maandsData_2'];

if($pstartJul>$ultimoTot)
  $ultimoTot=$pstartJul;
if($pstartJul>$ultimoVanaf)
  $ultimoVanaf=$pstartJul;
$dagen=round(($ultimoTot-$ultimoVanaf)/86400);
if($dagen<0)$dagen=0;

$fondsWaarde=$this->waarden['maandsFondsUitsluitingen'][date('Y-m-d',$this->waarden['maandsData_2'])];
$this->pdf->row(array('','Belegd vermogen ultimo '.vertaalTekst($__appvar["Maanden"][date("n",$this->waarden['maandsData_2'])],$pdf->rapport_taal),$this->formatGetal($this->waarden['maandsWaarde_2']-$fondsWaarde,0),$dagen));

//echo "<br>".$this->formatGetal($this->waarden['maandsWaarde_2']-$fondsWaarde,0)."=".$this->waarden['maandsWaarde_2']."-$fondsWaarde <br>\n";

if($berekeningVanaf>$this->waarden['maandsData_2'])
  $ultimoVanaf=$berekeningVanaf;
else
  $ultimoVanaf=$this->waarden['maandsData_2'];

if($berekeningVanaf>$ultimoTot)
  $ultimoTot=$berekeningVanaf;
else
  $ultimoTot=$this->waarden['maandsData_3'];

if($pstartJul>$ultimoTot)
  $ultimoTot=$pstartJul;
if($pstartJul>$ultimoVanaf)
  $ultimoVanaf=$pstartJul;
$dagen=round(($ultimoTot-$ultimoVanaf)/86400);
if($dagen<0)$dagen=0;

$fondsWaarde=$this->waarden['maandsFondsUitsluitingen'][date('Y-m-d',$this->waarden['maandsData_3'])];
$this->pdf->row(array('','Belegd vermogen ultimo '.vertaalTekst($__appvar["Maanden"][date("n",$this->waarden['maandsData_3'])],$pdf->rapport_taal),$this->formatGetal($this->waarden['maandsWaarde_3']-$fondsWaarde,0),$dagen));

//echo "<br>".$this->formatGetal($this->waarden['maandsWaarde_3']-$fondsWaarde,0)."=".$this->waarden['maandsWaarde_3']."-$fondsWaarde <br>\n";
//$this->pdf->CellBorders = array('','','T');
$this->pdf->ln();
$this->pdf->row(array('','Gemiddeld vermogen:',$this->formatGetal($this->waarden['rekenvermogen'],0),$this->waarden['periodeDagen']['dagen']));
//$this->pdf->CellBorders = array();
$this->pdf->ln();
$this->pdf->SetWidths(array(22,200));
$this->pdf->row(array('','Berekening '.$soort.' op basis van uw belegd vermogen en aantal dagen:'));
$this->pdf->ln();
$this->pdf->SetWidths(array(22,70,25,25,25));
$this->pdf->row(array('','','Percentage','Grondslag','Vergoeding'));
$this->pdf->ln();
$staffelTotaal=$this->waarden['beheerfeePerPeriodeNor'];
$waardeTotaal=0;
if($this->waarden['BeheerfeePercentageVermogen'] <> 0)
{
  $restWaarde=$this->waarden['rekenvermogen'];
  $vorigeStaffel=0;
  $staffelTotaal=0;
//listarray($this->waarden['portefeuilledata']);

  for($i=1;$i<6;$i++)
  {
    $staffelWaarde = $this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i]-$vorigeStaffel;
    $vorigeStaffel = $this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i];
    $staffelPercentage = $this->waarden['portefeuilledata']['BeheerfeeStaffelPercentage' . $i];
    if ($staffelWaarde >= $restWaarde)
    {
      $waarde=$restWaarde;
      $restWaarde=0;
    }
    elseif ($restWaarde > $staffelWaarde)
    {
      $restWaarde = $restWaarde - $staffelWaarde;
      $waarde=$staffelWaarde;
    }
    $fee=$waarde*$staffelPercentage/100;

    $feeDeel = $fee * $this->waarden['periodeDeelVanJaar'];
    //if($feeDeel<>0)
    // {
    $this->waarden['staffelWaarden'][$i] = array('staffelEind' =>$this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i], 'percentage' => $staffelPercentage, 'waarde' => $waarde, 'fee' => $fee, 'feeDeel' => $feeDeel);
    $staffelTotaal += $feeDeel;
    $waardeTotaal += $waarde;
    // }
  }
}
else
{
  $vorigeStaffel=0;
  for($i=1;$i<6;$i++)
  {
    $staffelWaarde= $this->waarden['portefeuilledata']['BeheerfeeStaffel' . $i];
    $staffelPercentage = $this->waarden['portefeuilledata']['BeheerfeeStaffelPercentage' . $i];

    $this->waarden['staffelWaarden'][$i]['staffelEind']=$staffelWaarde;// = array('staffelEind' => $staffelWaarde, 'percentage' => $staffelPercentage, 'waarde' => $waarde, 'fee' => $fee, 'feeDeel' => $feeDeel);
    $this->waarden['staffelWaarden'][$i]['percentage']=$staffelPercentage;
    $waardeTotaal +=  $this->waarden['staffelWaarden'][$i]['waarde'];
  }
}
//listarray($this->waarden);
$totaalFee = 0;
foreach($this->waarden['staffelWaarden'] as $i=>$staffelData)
{
  $totaalFee += $staffelData['feeDeel'];
  if($i==1)
    $this->pdf->row(array('','Tot €'.$this->formatGetal($staffelData['staffelEind'],0),$this->formatGetal($staffelData['percentage'],2)."%",$this->formatGetal($staffelData['waarde'],2),$this->formatGetal($staffelData['feeDeel'],2)));
  elseif($i==5)
    $this->pdf->row(array('','Van €'.$this->formatGetal($vorige,0),$this->formatGetal($staffelData['percentage'],2)."%",$this->formatGetal($staffelData['waarde'],2),$this->formatGetal($staffelData['feeDeel'],2)));
  else
    $this->pdf->row(array('','Van €'.$this->formatGetal($vorige,0)." tot €".$this->formatGetal($staffelData['staffelEind'],0),$this->formatGetal($staffelData['percentage'],2)."%",$this->formatGetal($staffelData['waarde'],2),$this->formatGetal($staffelData['feeDeel'],2)));
  $vorige=$staffelData['staffelEind'];
}

if ( $totaalFee-$staffelTotaal <> 0 && $this->waarden['BeheerfeeKortingspercentage'] <> 0 ) {
  $this->pdf->row(array('','SubTotaal','','',$this->formatGetal($totaalFee,2)));
  $this->pdf->row(array('','Korting (' . $this->waarden['BeheerfeeKortingspercentage'] . '%)','','',$this->formatGetal($totaalFee-$staffelTotaal,2)));
}

$this->pdf->ln();
$this->pdf->row(array('','Totaal','',$this->formatGetal($waardeTotaal,2),$this->formatGetal($staffelTotaal,2)));
$this->pdf->ln();

if($this->waarden['BeheerfeePercentageVermogen'] <> 0 || $this->waarden['MinJaarbedragGebruikt']==1)
{
  $standaardInc=$staffelTotaal*(100+$this->waarden['btwTarief'])/100;
  $this->pdf->row(array('', 'Standaard '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', '', '', $this->formatGetal($standaardInc, 2) . ""));
  $this->pdf->ln();

  $this->pdf->row(array('','Uw tarief op basis van onze afspraak ','','',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
  $this->pdf->row(array('', 'BTW ( ' . $this->waarden['btwTarief'] . '%)', '', '', $this->formatGetal($this->waarden['btw'], 2) . ""));
  $this->pdf->ln();
  if($this->waarden['BeheerfeePercentageVermogen'] <> 0 )
  {

    $this->pdf->row(array('', 'Uw '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', $this->formatGetal($this->waarden['BeheerfeePercentageVermogen'], 2) . '%', '', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . ""));
    $this->pdf->ln();

    $this->pdf->row(array('', 'Uw voordeel t.o.v. standaardtarief', '', '', $this->formatGetal($standaardInc-$this->waarden['beheerfeeBetalenIncl'], 2) . ""));
  }
  else
    $this->pdf->row(array('', 'Uw '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', '', '', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . ""));
}
else
{
  $this->pdf->row(array('', 'BTW ( ' . $this->waarden['btwTarief'] . '%)', '', '', $this->formatGetal($this->waarden['btw'], 2) . ""));
  $this->pdf->ln();
  $this->pdf->row(array('', 'Uw '.$soort.' inclusief ' . $this->waarden['btwTarief'] . '% BTW', '', '', $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . ""));
}

$this->footerLogoSmall=  base64_decode('iVBORw0KGgoAAAANSUhEUgAAACcAAAAlCAIAAABOCWdpAAAFJUlEQVR42sWX21PbRhSHtau7JUs2YEwA1yYQSpomk9J22maaTJqQTts/tzNN2ykPuTSZJs1DcyVDIGDiCzgGYyRLWl12u7IxsWw3MMRNzoNH1kr76fzOnnN2ASGEee8GmlQSeA5D8JvbpM9Vn5dZDnLSManIeF0vPaZUAEATQ5os0kFt/ja/D7T/cizEUExkv4G8fByqvZMnjYLleIATwqlJD691QRgQ+QjMiYo6MQ9Z4ThUazvPuZsu0JTUDEPdPVqk6VdAyFKVj6mwtb3Oe1sIJtTRj48xxbtQNxFMvneqW0ZwSE1/AGpSTc99eCrBfoCMVkK3b0Vebi0oyIqAl5tZ198Iwdip02cOkrtFXWsrHKE6O2tebYWmC/zPPMau62GG46SElMyK2jgDYC/VNcrmxi1Bz6mTX3VRSwgOd1GtyhJjlRyPgZzY+uYO3r4AJPCxZ8ky63s+I6bU8fOQj3VRfXun/nIRQqhNLbBS4hBqo7Ik0owSxpXUKSpS/wgBEHg22iui2ooiAtvn1cmvWUHt0tjY+FPCO35sRh4900ktNqmno9RnoldxxYnYyKlDF4iPDLNwX+GRTeLxzAUAI9XD2V7BOw9dGE9Mf39Afdn2tQ8VCRPU10Op1ALXMNZvCdCHw+ek5FRkCBm7L64Dltenr7FCvE1FRcSO9KNShSePSKVmV5eZ+pILVG3qSmRVE1xbvg6xKWcuC+pYm+oUEddD3WpSRerrbOd97NmBa/Kx4d4VGyCzvvoHxWknr7Ki1jm0t34zBnYC/byYmGpSq6GvLjespD/poW4icbKLulf42zcKyviXoj7Z6y71KUB72lToU+d9s3BPDkqeMieNnN6nCqhAfe2hPm1TI5XSqj5nzTXL5/Tcpf2k6rDdF78Rr65kLgnxE5HZSg8k75Ubm5VTZ1oKrwphXKmvZ45CxYG7l78tQSeI5ZTRyCu0nNWWfyEB0k9e5eThiK/Fe7JfcpU5ueUr7eqSu2HBlDI610MtIzHTRaWG6gW7fJ8ALp69zElv4kfLkJG/AfhYcuYnumIjccnfUphtXzsnJqdDKvYRTXNBTbOCckRqM/HvymTXhiPxzH6dozOFU4O6K2RiJ+YjTxNcX/1dIAY3dpEqD96yR2xsPWkq3JfKeNZ2Y+N24Hvx7Ld0olCzyiO8+yIgrJK7wkl6JCi+XXv+MwBEn/6BFsVDqWVXysZGZvs+YG0+gtYaYuLK+LxdfU7MVwQTcWxeGprpiUjeKd4hnKbP/EjL1lupm49ZmsdAE7Sxg31rmPlk/wq7llVdovLRiRSJs5Evpc7KoTDdXc8s/iV5JSRkaL4x7f1wf7MrT3maUa53UGRAtN9hagQHQRCOC8lY+iwfzdGWBW6jvvorwEjNLfBK+hCqZ9dohaMLobOydbTY8IJ6idEubTaBnKM69w/E1kO2seIQVTu5AJp7SvDuJw7XKNFlhQnQpxc4eajbUbRn5hdpS5InLgh6dl+zgZxzzFd3paBig6F49mIkqASbhTt0qEGS+tR3B0ODofrOrrm2iAMU/+gSr2XeaFt5gmtPPMzGc1c7ZRgMNQRs/cNZq5Yv0+C1ziDO9rJVfsCyQEx/IQ1FeuXAqLT9meuLAnCYxKe0q9ivn7nVRxwLgDYbG/us6+GBUamh2iravM+wMs0ixi7TILL6rDJ2vrcND5JKU9fM32D9Kt0OOi4RU2el4bm+++RBUql5jS1j/SYQFPXE530rxv9CZcJKZNLVBN56qB089Sj2L8uTNuRL7Kt9AAAAAElFTkSuQmCC');
$this->footerLogolg=  base64_decode('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');

$this->pdf->setXY(0,297-20);
$this->pdf->MemImage($this->footerLogoSmall, 28 , 297-20, 3.9, 3.9);
$this->pdf->MemImage($this->footerLogoSmall, 78 , 297-20, 3.9, 3.9);
$this->pdf->MemImage($this->footerLogoSmall, 105 , 297-20, 3.9, 3.9);
$this->pdf->MemImage($this->footerLogoSmall, 133 , 297-20, 3.9, 3.9);
$this->pdf->MemImage($this->footerLogolg, 190 , 297-22, 10, 10);
$this->pdf->AutoPageBreak=false;

//$this->pdf->SetFont($font,"",10);
$this->pdf->SetFont($this->pdf->rapport_font,'',8);
$this->pdf->SetTextColor(164,172,179);
$this->pdf->MultiCell(210,$this->pdf->rowHeight-0.5,"ING-bank: NL 31 INGB 0006 6483 27               KvK: 82097291               AFM: 14006146               BTW-nummer: 8623.35.231.B.01",0,'C',0);
$this->pdf->AutoPageBreak=true;

$this->pdf->SetTextColor(0,0,0);
$this->pdf->rowHeight=$rowHeightBackup;
?>
