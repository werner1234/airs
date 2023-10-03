<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/31 10:04:39 $
File Versie					: $Revision: 1.7 $

$Log: Factuur_L73.php,v $
Revision 1.7  2019/12/31 10:04:39  rvv
*** empty log message ***

Revision 1.6  2019/06/29 18:25:27  rvv
*** empty log message ***

Revision 1.5  2018/07/07 17:34:05  rvv
*** empty log message ***

Revision 1.4  2018/01/06 18:11:54  rvv
*** empty log message ***

Revision 1.3  2018/01/04 05:57:03  rvv
*** empty log message ***

Revision 1.2  2018/01/03 16:23:12  rvv
*** empty log message ***

Revision 1.1  2017/11/22 17:04:11  rvv
*** empty log message ***



*/
    global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
    $this->pdf->AddPage('P');
 	  $this->pdf->frontPage = true;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $extraMarge=15;

   if(is_file($this->pdf->rapport_logo))
		{
		  $logoWidth=40;
			  $this->pdf->Image($this->pdf->rapport_logo, 210/2-$logoWidth/2, 13, $logoWidth);
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
    Vermogensbeheerders.Naam as vermogensbeheerder,
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


$velden=array();
$query = "desc CRM_naw";
$DB->SQL($query);
$DB->query();
while($data=$DB->nextRecord('num'))
  $velden[]=$data[0];
if(in_array('naam2',$velden))
  $extraVeld=',naam2';
if($extraVeld<>'')
{
  $query = "SELECT id $extraVeld FROM CRM_naw WHERE portefeuille = '" . $this->portefeuille . "' ";
  $DB->SQL($query);
  $crmData = $DB->lookupRecord();
  $portefeuilledata['naam2'] = $crmData['naam2'];
}
else
  $portefeuilledata['naam2'] ='';

    
    $portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(50);
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetWidths(array($extraMarge,170));
    $this->pdf->row(array('','Vertrouwelijk'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
	  if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    if ($portefeuilledata['naam2'] != '')
       $this->pdf->row(array('',$portefeuilledata['naam2']));
    $this->pdf->row(array('',$portefeuilledata['Adres']));
  
    $plaats='';
		if($portefeuilledata['pc'] != '')
		  $plaats .= $portefeuilledata['pc']." ";
		$plaats .= $portefeuilledata['Woonplaats'];
		$this->pdf->row(array('',$plaats));



    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetY(80);

    
    $vanDatum=date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan']));
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $incassoJul=time()+5*3600*24; //db2jul($this->waarden['datumTot'])
    $incassoDatum=date("j",$incassoJul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$incassoJul)],$this->pdf->rapport_taal)." ".date("Y",$incassoJul);
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
 
    
      $this->pdf->ln();
$beginY=$this->pdf->getY();
    if(!isset($this->factuurnummer))
      $this->factuurnummer=1;

    $kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

if(strtolower($this->waarden['SoortOvereenkomst'])=='beheer')
  $type="Vermogensbeheer";
else
  $type="Vermogensadvies";

    $this->pdf->row(array('','Factuur voor '.$type.' bij Alpha Capital '.$kwartalen[$this->waarden['kwartaal']]." kwartaal ".date("Y",db2jul($this->waarden['datumTot']))));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->SetWidths(array($extraMarge,30,150));
    $this->pdf->Row(array('',vertaalTekst('Datum:',$this->pdf->rapport_taal),$nu));
    $this->pdf->Row(array('',vertaalTekst('Depot:',$this->pdf->rapport_taal),$this->portefeuille));
    $this->pdf->Row(array('',vertaalTekst('Rekening',$this->pdf->rapport_taal),$this->waarden['rekeningIBAN']));
    $this->pdf->Row(array('',vertaalTekst('Factuurnummer:',$this->pdf->rapport_taal),$this->factuurnummer));//.date("Y",db2jul($this->waarden['datumTot']).'/'.sprintf("%03d",$this->factuurnummer)));

    $this->pdf->ln(15);

    $this->pdf->SetWidths(array($extraMarge,150));
    $this->pdf->Row(array('',vertaalTekst('Periode',$this->pdf->rapport_taal)." $vanDatum ".vertaalTekst('tot en met',$this->pdf->rapport_taal)." $totDatum"));
    $this->pdf->ln();


/*
if($this->waarden['BeheerfeeKortingspercentage'] <> 0)
  $kortingsTekst='inclusief '.$this->formatGetal($this->waarden['BeheerfeeKortingspercentage'],1).'% korting';
else
  $kortingsTekst='';

if(isset($this->waarden['staffelWaarden'][1]['percentage']))
  $feePercentage=$this->waarden['staffelWaarden'][1]['percentage']/$this->waarden['BeheerfeeAantalFacturen'];
else  
  $feePercentage=$this->waarden['BeheerfeePercentageVermogenDeelVanJaar'];
  
$feeParts=explode(".",$feePercentage);
if(strlen($feeParts[1])>3)
  $feePercentage=$this->formatGetal($feePercentage,4);
elseif(strlen($feeParts[1])>2)
  $feePercentage=$this->formatGetal($feePercentage,3);
elseif(strlen($feeParts[1])>1)
  $feePercentage=$this->formatGetal($feePercentage,2);  
*/

$valutaTeken='€';
$this->pdf->SetAligns(array("L","L",'C','R'));
$this->pdf->SetWidths(array($extraMarge,100,15,25));
$this->pdf->SetFont($this->pdf->rapport_font,'I',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Belegde vermogen',$valutaTeken,$this->formatGetal($this->waarden['basisRekenvermogen'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->ln();
$this->pdf->row(array('',vertaalTekst('Door u te betalen (bruto) vaste kosten:',$this->pdf->rapport_taal).' '.$this->formatGetal($this->waarden['BeheerfeePercentageVermogen']/$this->waarden['BeheerfeeAantalFacturen'],4).'% '.vertaalTekst('per kwartaal ',$this->pdf->rapport_taal)));

if($this->waarden['periodeDeelVanJaar']==0.25)
  $periode='3 maanden';
else
  $periode=round($this->waarden['periodeDeelVanJaar']*365)." dagen";

$this->pdf->SetAligns(array("L","L",'L','C','R'));
$this->pdf->SetWidths(array($extraMarge,60,40,15,25));
$this->pdf->row(array('',vertaalTekst('Kosten berekend',$this->pdf->rapport_taal).':',$periode,$valutaTeken,$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2)));
$this->pdf->row(array('',vertaalTekst('BTW',$this->pdf->rapport_taal).':',$this->formatGetal($this->waarden['btwTarief'],0)."%",$valutaTeken,$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array('','','','','T');
$this->pdf->row(array('',vertaalTekst('Per saldo door u te betalen',$this->pdf->rapport_taal).':','',$valutaTeken,$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
unset($this->pdf->CellBorders);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

$this->pdf->ln(30);

$this->pdf->SetWidths(array($extraMarge,150));
$this->pdf->row(array('',vertaalTekst('Het bedrag van',$this->pdf->rapport_taal)." € ".$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)." ". vertaalTekst('wordt binnen 5 werkdagen',$this->pdf->rapport_taal)." ".vertaalTekst('automatisch geïncasseerd van uw
effectenrekening. Heeft u nog vragen over deze factuur, neemt u dan gerust contact met ons op.',$this->pdf->rapport_taal)));
$this->pdf->rect($extraMarge+$this->pdf->marge-1,$beginY,210-(22*2),$this->pdf->getY()-$beginY);
$this->pdf->ln();


$this->pdf->rowHeight=$rowHeightBackup;
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
$this->pdf->row(array('','Don Boscostraat 4','Kvk nr: 60279095'));
$this->pdf->row(array('Beleggingsadvies','5611 KW  Eindhoven','BTW nr: NL853840829B01'));
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


$this->pdf->SetTextColor(0,0,0);

?>