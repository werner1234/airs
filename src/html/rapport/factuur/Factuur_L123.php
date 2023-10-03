<?php
    global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
    $this->pdf->AddPage('P');
 	  $this->pdf->frontPage = true;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $extraMarge=15;
    $fontsize=$this->pdf->rapport_fontsize+2;

   if(is_file($this->pdf->rapport_logo))
		{
		  $logoWidth=50;
			  $this->pdf->Image($this->pdf->rapport_logo, 210/2-$logoWidth/2, 5, $logoWidth);
		}
/*
    if(is_file($this->pdf->rapport_logo))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 140, 20, 60);
		}
*/
    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, 
    Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, 
    Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client,Portefeuilles.afrekenvalutaKosten,
    Vermogensbeheerders.Naam as vermogensbeheerder,Vermogensbeheerders.Adres as vermogensbeheerderAdres,Vermogensbeheerders.Woonplaats as vermogensbeheerderPlaats,
    Accountmanagers.Naam as AccountmanagerNaam,
    Accountmanagers.Handtekening as Handtekening
    FROM Portefeuilles 
    JOIN Clienten ON Portefeuilles.Client = Clienten.Client
    LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
    JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    WHERE Portefeuille = '".$this->portefeuille."'   ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
    
    if($portefeuilledata['afrekenvalutaKosten']=='')
      $portefeuilledata['afrekenvalutaKosten']='EUR';
    $query="SELECT valuta,Valutateken FROM Valutas WHERE valuta='".$portefeuilledata['afrekenvalutaKosten']."'"; 
    $DB->SQL($query);
		$DB->Query();
		$valuta = $DB->nextRecord();
    if($valuta['Valutateken']=='')
      $valuta['Valutateken']=$valuta['valuta'];


    
    $portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];


		$this->pdf->SetY(50);
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetWidths(array($extraMarge,170));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
	  if ($portefeuilledata['Naam1'] != '')
    {
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    }
    $this->pdf->row(array('',$portefeuilledata['Adres']));
  
    $plaats='';
		if($portefeuilledata['pc'] != '')
		  $plaats .= $portefeuilledata['pc']." ";
		$plaats .= $portefeuilledata['Woonplaats'];
		$this->pdf->row(array('',$plaats));


$this->pdf->SetY(45);

$this->pdf->SetWidths(array($extraMarge+110,50));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
$this->pdf->Row(array('',$portefeuilledata['vermogensbeheerder']));
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->Row(array('',$portefeuilledata['vermogensbeheerderAdres']));
$this->pdf->Row(array('',$portefeuilledata['vermogensbeheerderPlaats']));
$this->pdf->ln();
if($portefeuilledata['Vermogensbeheerder']=='FEX')
{
  $this->pdf->Row(array('', 'KvK-nummer: 68829094'));
  $this->pdf->Row(array('', 'BTW-nummer: 8576.10.685.B01'));
}
else
{
  $this->pdf->Row(array('', 'KvK-nummer: '));
  $this->pdf->Row(array('', 'BTW-nummer: '));
}
$this->pdf->setDash(0.1,1);
$this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),$this->pdf->marge+$extraMarge+80,$this->pdf->getY());
$this->pdf->line($this->pdf->marge+$extraMarge+110,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());

    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetY(95);

    
    $vanDatum=date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan']));
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $vervalJul=time()+30*3600*24; //db2jul($this->waarden['datumTot'])
    $vervalDatum=date("j",$vervalJul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vervalJul)],$this->pdf->rapport_taal)." ".date("Y",$vervalJul);
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
 
    
      $this->pdf->ln();
$beginY=$this->pdf->getY();
    if(!isset($this->factuurnummer))
      $this->factuurnummer=1;

    $kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');

$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize+8);
$this->pdf->SetWidths(array($extraMarge,170));
$this->pdf->row(array('','Factuur'));
$this->pdf->ln(2);
$this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
$this->pdf->ln(2);
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->setDash(0);

    $this->pdf->SetWidths(array($extraMarge,30,150));
    $factuurnummer=date("Y",db2jul($this->waarden['datumTot'])).".".$this->factuurnummer;
    $this->pdf->Row(array('',vertaalTekst('Factuurnummer',$this->pdf->rapport_taal),': '.$factuurnummer));//.date("Y",db2jul($this->waarden['datumTot']).'/'.sprintf("%03d",$this->factuurnummer)));
    $this->pdf->Row(array('',vertaalTekst('Factuurdatum',$this->pdf->rapport_taal),': '.$nu));
    $this->pdf->Row(array('',vertaalTekst('Vervaldatum',$this->pdf->rapport_taal),': '.$vervalDatum));
$this->pdf->ln();

$this->pdf->SetWidths(array($extraMarge,90,15,22,15,22));
$this->pdf->SetAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
$this->pdf->Row(array('',vertaalTekst('Omschrijving',$this->pdf->rapport_taal),vertaalTekst('Aantal',$this->pdf->rapport_taal),vertaalTekst('Bedrag',$this->pdf->rapport_taal),vertaalTekst('BTW',$this->pdf->rapport_taal),vertaalTekst('Totaal',$this->pdf->rapport_taal)));
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->ln(2);
$this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
$this->pdf->ln(2);
$this->pdf->Row(array('','Beleggingsadvies over periode '.date("d-m-Y",db2jul($this->waarden['datumVan'])).' tot '.date("d-m-Y",db2jul($this->waarden['datumTot'])),
                  '1',
                  '€'.$this->formatGetal($this->waarden['beheerfeeBetalen'],2),
                  $this->formatGetal($this->waarden['btwTarief'],0).'%',
                  '€'.$this->formatGetal($this->waarden['beheerfeeBetalen'],2) ));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
$this->pdf->SetWidths(array($extraMarge+90+15,22+15,22));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
$this->pdf->ln(2);
$this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
$this->pdf->ln(2);
$this->pdf->Row(array('','Totaal exclusief BTW:',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->Row(array('','BTW ('.$this->formatGetal($this->waarden['btwTarief'],0).'%)',"€ ".$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
$this->pdf->ln(2);
$this->pdf->line($this->pdf->marge+$extraMarge+90+15,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
$this->pdf->ln(2);
$this->pdf->Row(array('','Totaal:',"€ ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

$this->pdf->setY(250);
$this->pdf->SetWidths(array($extraMarge+15,50,50,50));
$this->pdf->SetAligns(array('L','L','L','L'));
$this->pdf->Row(array('','IBAN-nummer','Factuurnummer','Factuurbedrag'));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
$this->pdf->Row(array('','NL73KNAB0256371822',$factuurnummer,'€ '.$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
for($i=0;$i<3;$i++)
  $this->pdf->rect($this->pdf->marge+$extraMarge+10+($i*50),$this->pdf->getY()-15,45,20);


  $this->pdf->SetWidths(array($extraMarge, 180));
  $this->pdf->ln(10);
if($this->waarden['BetalingsinfoMee'])
{
  $this->pdf->row(array('', vertaalTekst('Het openstaande bedrag van ', $this->pdf->rapport_taal) . "€ " . $this->formatGetal($this->waarden['beheerfeeBetalenIncl'], 2) . " " .
    vertaalTekst('dient binnen 30 dagen overgemaakt te zijn op rekeningnummer NL73KNAB0256371822 onder vermelding van het factuurnummer', $this->pdf->rapport_taal) . " $factuurnummer."));
}
else
{
  $this->pdf->row(array('', vertaalTekst('De advies fee wordt binnen 7 dagen geïncasseerd van uw cash fund bij uw broker.', $this->pdf->rapport_taal)));
}
//$this->pdf->rect($extraMarge+$this->pdf->marge-1,$beginY,210-(22*2),$this->pdf->getY()-$beginY);
  $this->pdf->ln();


$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->SetTextColor(0,0,0);
/*
    $this->pdf->SetAutoPageBreak(false);
    $this->pdf->setY(275);

$this->pdf->SetAligns(array("L","L",'L'));
$this->pdf->SetWidths(array(100,40,60));
$yPage=$this->pdf->getY();
$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->row(array('Alpha Capital Asset Management B.V.'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->setY($yPage);
$this->pdf->SetTextColor(102,91,84);
$this->pdf->row(array('','Willemstraat 1M','Kvk nr: 60279095'));
$this->pdf->row(array('Beleggingsadvies','5611 HA Eindhoven','BTW nr: NL853840829B01'));
$this->pdf->row(array('Vermogensbeheer','t +31 40 288 17 40','IBAN: NL65 RABO 0158 8696 56'));
$yPage=$this->pdf->getY();
$this->pdf->row(array('Vermogensbegeleiding','info@alphacapital.nl',''));
$this->pdf->getY($yPage);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->setY($yPage);
$this->pdf->row(array('','','www.alphacapital.nl'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

$this->pdf->SetTextColor(0);
$this->pdf->SetAutoPageBreak(true,8);

*/


?>