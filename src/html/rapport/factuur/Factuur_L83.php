<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/04 11:55:26 $
File Versie					: $Revision: 1.21 $

$Log: Factuur_L83.php,v $
Revision 1.21  2020/04/04 11:55:26  rvv
*** empty log message ***

Revision 1.20  2020/03/21 12:33:16  rvv
*** empty log message ***

Revision 1.19  2020/03/18 17:56:19  rvv
*** empty log message ***

Revision 1.18  2020/01/14 07:21:17  cvs
call 8345

Revision 1.17  2019/08/21 15:58:05  rvv
*** empty log message ***

Revision 1.16  2019/07/10 07:54:05  rvv
*** empty log message ***

Revision 1.15  2019/07/05 16:39:37  rvv
*** empty log message ***

Revision 1.14  2019/07/03 08:48:10  rvv
*** empty log message ***

Revision 1.13  2019/07/03 08:24:02  rvv
*** empty log message ***

Revision 1.12  2019/07/03 06:34:52  rvv
*** empty log message ***

Revision 1.11  2019/06/29 18:25:27  rvv
*** empty log message ***

Revision 1.10  2019/06/19 15:51:37  rvv
*** empty log message ***

Revision 1.9  2019/05/22 16:02:17  rvv
*** empty log message ***

Revision 1.8  2019/05/22 13:19:07  rvv
*** empty log message ***

Revision 1.7  2019/05/22 12:59:06  rvv
*** empty log message ***

Revision 1.6  2019/05/22 12:48:56  rvv
*** empty log message ***

Revision 1.5  2019/05/22 12:22:30  rvv
*** empty log message ***

Revision 1.4  2019/05/22 07:35:52  rvv
*** empty log message ***

Revision 1.3  2019/05/19 09:50:04  rvv
*** empty log message ***

Revision 1.2  2019/05/18 16:28:09  rvv
*** empty log message ***

Revision 1.1  2019/04/17 11:21:04  rvv
*** empty log message ***

Revision 1.3  2018/01/10 16:27:53  rvv
*** empty log message ***

Revision 1.2  2018/01/06 18:11:54  rvv
*** empty log message ***

Revision 1.1  2017/11/22 17:04:11  rvv
*** empty log message ***



*/
    global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5.64;
    $this->pdf->AddPage('P');
 	  $this->pdf->frontPage = true;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $extraMarge=21;
    $fontsize=$this->pdf->rapport_fontsize+2;
    $txtWidth=210-($this->pdf->marge+$extraMarge)*2;

    if($this->waarden['huisfondsKorting']<>0 && count($this->waarden['huisfondsKortingFondsen'])>0)
    {
      $bijlageToevoegen=true;
    }
    else
    {
      $bijlageToevoegen=false;
    }
    
    $portefeuilleArray=array();
    foreach($this->waarden['portefeuilleVerdeling'] as $verdeling=>$portefeuilles)
    {
      foreach($portefeuilles as $portefeuille=>$waarde)
      {
        if($portefeuille<>'totaal')
          $portefeuilleArray[$portefeuille]=$portefeuille;
      }
    }
    
    if(count($portefeuilleArray)>1)
      $bijlageToevoegen=true;
    //

   $logoOld=str_replace('logo_VLC','logo_VLC_old',$this->pdf->rapport_logo);

   if(is_file($logoOld))
     $logo=$logoOld;
   else
     $logo=$this->pdf->rapport_logo;
  
   
   if(is_file($logo))
	 {
	   $logoWidth=50;
	   $this->pdf->Image($logo, 210/2-$logoWidth/2, 20, $logoWidth);
	 }



    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, 
    Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, 
    Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client,Portefeuilles.afrekenvalutaKosten,
    Vermogensbeheerders.Naam as vermogensbeheerder,
    Accountmanagers.Naam as AccountmanagerNaam,
    Accountmanagers.Handtekening as Handtekening,
    AccountmanagersTwee.Handtekening AS Handtekening2,
    AccountmanagersTwee.Naam as AccountmanagerNaam2
    FROM Portefeuilles 
    JOIN Clienten ON Portefeuilles.Client = Clienten.Client
    LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
    JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    LEFT JOIN Accountmanagers as AccountmanagersTwee ON Portefeuilles.tweedeAanspreekpunt = AccountmanagersTwee.Accountmanager
    WHERE Portefeuille = '".$this->portefeuille."'   ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();


$velden=array();
$query = "desc CRM_naw";
$extraVeld='';
$DB->SQL($query);
$DB->query();
while($data=$DB->nextRecord('num'))
  $velden[]=$data[0];
if(in_array('DienstSoort',$velden))
  $extraVeld.=',DienstSoort';
if(in_array('TariefSoort',$velden))
  $extraVeld.=',TariefSoort';

if($extraVeld<>'')
{
  $query = "SELECT id $extraVeld FROM CRM_naw WHERE portefeuille = '" . $this->portefeuille . "' ";
  $DB->SQL($query);
  $crmData = $DB->lookupRecord();
  $portefeuilledata['DienstSoort'] = 'Vergoeding '.strtolower($crmData['DienstSoort']);
}
else
  $portefeuilledata['DienstSoort'] ='Vergoeding vermogensbeheer';


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

    if($this->pdf->portefeuilledata['verzendPc']<>'' && $this->pdf->portefeuilledata['verzendPlaats']<>'')
    {
      $portefeuilledata['pc']=$this->pdf->portefeuilledata['verzendPc'];
      $portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['verzendPlaats'];
    }

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
		  $plaats .= $portefeuilledata['pc']."   ";
		$plaats .= $portefeuilledata['Woonplaats'];
		$this->pdf->row(array('',$plaats));
    if($this->pdf->portefeuilledata['Land'] <> 'Nederland')
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Land']));

    //$this->pdf->SetY(45);

    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetY(95);

    
    $vanDatum=date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan']));
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $vervalJul=time()+30*3600*24; //db2jul($this->waarden['datumTot'])
    $vervalDatum=date("j",$vervalJul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vervalJul)],$this->pdf->rapport_taal)." ".date("Y",$vervalJul);
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
    $totKort=date("d-m-Y",db2jul($this->waarden['datumTot']));
    
    $this->pdf->ln();
    $beginY=$this->pdf->getY();
    if(!isset($this->factuurnummer))
      $this->factuurnummer=1;

    $kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');


$this->pdf->Row(array('',vertaalTekst('Den Haag',$this->pdf->rapport_taal).', '.$nu));
$this->pdf->ln();
$this->pdf->SetAligns(array('L','J','L','L'));
    $this->pdf->SetWidths(array($extraMarge,30,164));
    $factuurnummer=date("Y")."-".sprintf("%03d",$this->factuurnummer);


    $this->pdf->Row(array('',vertaalTekst('Factuurnr',$this->pdf->rapport_taal).':',$factuurnummer));//.date("Y",db2jul($this->waarden['datumTot']).'/'.sprintf("%03d",$this->factuurnummer)));
    $this->pdf->Row(array('',vertaalTekst('Betreft',$this->pdf->rapport_taal).':',vertaalTekst($portefeuilledata['DienstSoort'],$this->pdf->rapport_taal)));
    $this->pdf->ln();

$this->pdf->SetWidths(array($extraMarge,$txtWidth));
$this->pdf->Row(array('',$this->waarden['CRM_verzendAanhef']));
$this->pdf->ln();
$this->pdf->Row(array('',vertaalTekst('Hierbij sturen wij u de declaratie voor onze dienstverlening gedurende de periode van',$this->pdf->rapport_taal).' '.$vanDatum.' '.vertaalTekst("tot en met",$this->pdf->rapport_taal).' '.$totDatum.'.'));
$this->pdf->ln();
$this->pdf->SetWidths(array($extraMarge,$txtWidth-40,10,30));
$this->pdf->SetAligns(array('L','L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

if($this->waarden['BeheerfeeBedragVast']==0)
{
  $feePercentage=round($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],5);
  $feeSplit=explode(".",$feePercentage);
  $decimalen=strlen($feeSplit[1]);
  if($bijlageToevoegen==true)
    $bijlageInfo=" ".vertaalTekst("(zie bijlage)",$this->pdf->rapport_taal);
  else
    $bijlageInfo="";
  
  
  $this->pdf->Row(array('', vertaalTekst('Grondslag per', $this->pdf->rapport_taal) . ' ' . $totDatum.$bijlageInfo, "€", $this->formatGetal($this->waarden['basisRekenvermogen'], 2)));
  if($this->waarden['basisRekenvermogen'] <> $this->waarden['rekenvermogen'])
    $this->pdf->Row(array('', vertaalTekst('Aangepaste grondslag (na aftrek liquiditeiten)', $this->pdf->rapport_taal), "€", $this->formatGetal($this->waarden['rekenvermogen'], 2)));
  $this->pdf->ln(2);
  $this->pdf->Row(array('', vertaalTekst('Kwartaaltarief (zie contract', $this->pdf->rapport_taal) . ' ' . $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'], $decimalen) . '%) ', "€", $this->formatGetal($this->waarden['beheerfeePerPeriodeNor'], 2)));
}
else
{
  if($crmData['TariefSoort']=='Regie + Beheer')
  {
    $this->pdf->Row(array('', vertaalTekst('Grondslag per', $this->pdf->rapport_taal) . ' ' . $totDatum.$bijlageInfo, "€", $this->formatGetal($this->waarden['basisRekenvermogen'], 2)));
    if($this->waarden['basisRekenvermogen'] <> $this->waarden['rekenvermogen'])
      $this->pdf->Row(array('', vertaalTekst('Aangepaste grondslag (na aftrek liquiditeiten)', $this->pdf->rapport_taal), "€", $this->formatGetal($this->waarden['rekenvermogen'], 2)));
    $this->pdf->ln(2);
    
    $vastBedrag=$this->waarden['BeheerfeeBedragVast']/$this->waarden['BeheerfeeAantalFacturen'];
    $this->pdf->Row(array('', vertaalTekst('Kwartaaltarief (zie contract)', $this->pdf->rapport_taal), "€", $this->formatGetal($this->waarden['beheerfeePerPeriodeNor']-$vastBedrag, 2)));
    $this->pdf->Row(array('', vertaalTekst('Vergoeding regie', $this->pdf->rapport_taal), "€", $this->formatGetal($vastBedrag, 2)));
    $this->pdf->CellBorders = array('','','',array('T'));
    $this->pdf->Row(array('', vertaalTekst('Subtotaal', $this->pdf->rapport_taal), "€", $this->formatGetal($this->waarden['beheerfeePerPeriodeNor'], 2)));
    unset($this->pdf->CellBorders );
    $this->pdf->ln();
  }
  else
  {
    $this->pdf->Row(array('', vertaalTekst('Kwartaaltarief (zie contract)', $this->pdf->rapport_taal), "€", $this->formatGetal($this->waarden['beheerfeePerPeriodeNor'], 2)));
  }
}
  if($this->waarden['btwTarief']==0)
    $this->pdf->Row(array('',vertaalTekst('BTW (vrijgesteld)',$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['btw'],2)));
  else
    $this->pdf->Row(array('',vertaalTekst('BTW' ,$this->pdf->rapport_taal).' ('.$this->formatGetal($this->waarden['btwTarief'],0).'%)',"€",$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->ln(2);

$totaalFactuurRegels=0;
foreach($this->waarden['extraFactuurregels']['regels'] as $regel)
{
  $totaalFactuurRegels+=$regel['bedrag'];
}
if($totaalFactuurRegels<>0)
{
  $this->pdf->CellBorders = array('','','',array('U'));
  $this->pdf->Row(array('',vertaalTekst('Subtotaal',$this->pdf->rapport_taal).':',"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl']-$totaalFactuurRegels,2)));
  unset($this->pdf->CellBorders);
}
foreach($this->waarden['extraFactuurregels']['regels'] as $regel)
{
  $this->pdf->Row(array('',vertaalTekst($regel['omschrijving'],$this->pdf->rapport_taal),"€",$this->formatGetal($regel['bedrag'],2)));
}
$this->pdf->CellBorders = array('','','',array('T','UU'));
$this->pdf->Row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal).':',"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
unset($this->pdf->CellBorders);
$this->pdf->ln(10);
$this->pdf->SetWidths(array($extraMarge, $txtWidth));
$this->pdf->SetAligns(array('L','J'));

if($this->waarden['BetalingsinfoMee']==1)
{
  $txt = vertaalTekst("Wij verzoeken u vriendelijk bovenstaand bedrag over te boeken op rekeningnummer NL14ABNA0467225737 ten name van Van Lawick & Co. Vermogensbeheer B.V. o.v.v. het factuurnummer.",$this->pdf->rapport_taal);
  $this->pdf->row(array('', $txt));
}
else
{
  if ($this->pdf->rapport_taal == 1)
  {
    $this->pdf->row(array('', 'The amount above will be debited within 14 days of the invoice-date from your account at ' . $this->waarden['depotbankOmschrijving'] . ' with number  ' . $this->waarden['IBAN'] . ' .'));
  }
  else
  {
    $this->pdf->row(array('', vertaalTekst('Bovenstaand bedrag zal uiterlijk 14 dagen na dagtekening van uw rekening bij', $this->pdf->rapport_taal) . ' ' . vertaalTekst($this->waarden['depotbankOmschrijving'], $this->pdf->rapport_taal) . ' ' . vertaalTekst('met nummer', $this->pdf->rapport_taal) . ' ' . $this->waarden['IBAN'] . ' ' . vertaalTekst('worden afgeschreven.', $this->pdf->rapport_taal)));
  }
}
$this->pdf->ln();
$this->pdf->row(array('', vertaalTekst('Met vriendelijke groeten', $this->pdf->rapport_taal).","));
$this->pdf->ln();


if($portefeuilledata['Handtekening']<>'')
  $this->pdf->MemImage(base64_decode($portefeuilledata['Handtekening']),$this->pdf->getX()+$extraMarge,$this->pdf->getY()+1,60);
if($portefeuilledata['Handtekening2']<>'')
  $this->pdf->MemImage(base64_decode($portefeuilledata['Handtekening2']),$this->pdf->getX()+$extraMarge+90,$this->pdf->getY()+1,60);

$this->pdf->SetWidths(array($extraMarge, 90,90));
$this->pdf->Row(array('',$portefeuilledata['vermogensbeheerder']));
$this->pdf->ln(20);
$this->pdf->Row(array('',$portefeuilledata['AccountmanagerNaam'],$portefeuilledata['AccountmanagerNaam2']));


$this->pdf->setY(245);


$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);


$this->pdf->setY(270);
$this->pdf->rowHeight=3.5;
$this->pdf->SetWidths(array(210-$this->pdf->marge*2));
$this->pdf->SetAligns(array('C'));
$this->pdf->SetDrawColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
$this->pdf->line(49,$this->pdf->getY()-3,210-49,$this->pdf->getY()-3);
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
$this->pdf->row(array('Van Lawick & Co. Vermogensbeheer BV • Wassenaarseweg 20 • 2596 CH  Den Haag'));
$this->pdf->row(array('telefoon: 070-3616131 • email: info@vanlawick.com kvk: 32083194 • BTW 8098.22.362.B.01'));
$this->pdf->row(array('Vergunning Wft'));

if($bijlageToevoegen==true)
{
  $this->pdf->addPage('P');
  
  if(is_file($logo))
  {
    $logoWidth=50;
    $this->pdf->Image($logo, 210/2-$logoWidth/2, 25, $logoWidth);
  }
  
  $this->pdf->setY(75);
  $this->pdf->rowHeight=5.6;
  $this->pdf->SetWidths(array($extraMarge,$txtWidth-60,30,30));
  $this->pdf->SetAligns(array('L','L','L','R','R','R'));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
  $this->pdf->Row(array('',vertaalTekst('Bijlage bij Factuurnr',$this->pdf->rapport_taal).' '.$factuurnummer));
  $this->pdf->ln();
  $this->pdf->SetWidths(array($extraMarge,30,$txtWidth-80,50));
  $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
  $this->pdf->Row(array('',vertaalTekst('Portefeuille',$this->pdf->rapport_taal),vertaalTekst('Tenaamstelling',$this->pdf->rapport_taal),vertaalTekst('Waarde per',$this->pdf->rapport_taal).' '.$totKort));
  $this->pdf->ln();
  $this->pdf->SetWidths(array($extraMarge,30,$txtWidth-66,6,30));
  $verdelingLookup=array(0=>'gemiddeldeWaarde',1=>'beginWaarde',2=>'eindWaarde');
  $verdeling=$verdelingLookup[$this->waarden['BeheerfeeBasisberekening']];
  if(isset($this->waarden['portefeuilleVerdeling'][$verdeling]) && count($this->waarden['portefeuilleVerdeling'][$verdeling])>1)
  {
    foreach($this->waarden['portefeuilleVerdeling'][$verdeling] as $portefeuille=>$waarde)
    {
      if($portefeuille<>'totaal')
      {
        $query="SELECT naam FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
        $DB->SQL($query);
        $DB->Query();
        $crmNaam = $DB->nextRecord();
        
        $this->pdf->Row(array('', $portefeuille,$crmNaam['naam'],'€', $this->formatGetal($waarde, 2)));
      }
    }
    $this->pdf->ln();
    $this->pdf->Row(array('',vertaalTekst("Totaal",$this->pdf->rapport_taal),'','€',$this->formatGetal($this->waarden['basisRekenvermogen'],2)));
  }
  else
  {
    $this->pdf->Row(array('', $this->waarden['portefeuille'], $this->waarden['CRM_naam'], '€',$this->formatGetal($this->waarden['basisRekenvermogen'], 2)));
  }
  $this->pdf->SetWidths(array($extraMarge,$txtWidth-36,6,30));
  $this->pdf->SetAligns(array('L','L','R','R','R','R'));
  $this->pdf->ln();
  foreach($this->waarden['huisfondsKortingFondsen'] as $huisFonds=>$korting)
  {
    $this->pdf->Row(array('',vertaalTekst("Af: waarde",$this->pdf->rapport_taal)." ".$huisFonds." ".vertaalTekst("per",$this->pdf->rapport_taal)." ".$totKort,'€',$this->formatGetal($korting, 2)));
  }
  $this->pdf->ln();
  $this->pdf->Row(array('',vertaalTekst("Grondslag portefeuille",$this->pdf->rapport_taal)." ".$this->waarden['portefeuille'],'€',$this->formatGetal($this->waarden['rekenvermogen'],2)));
  

  $this->pdf->SetDrawColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
  $this->pdf->line(49,267,210-49,267);

}


$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->SetTextColor(0,0,0);
//global $teksten;
//listarray($teksten);

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