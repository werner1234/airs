<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/09 10:12:26 $
File Versie					: $Revision: 1.13 $

$Log: Factuur_L52.php,v $
Revision 1.13  2020/02/09 10:12:26  rvv
*** empty log message ***

Revision 1.12  2018/12/12 16:18:12  rvv
*** empty log message ***

Revision 1.11  2018/01/13 18:58:25  rvv
*** empty log message ***

Revision 1.8  2016/03/02 16:59:47  rvv
*** empty log message ***

Revision 1.7  2014/07/12 15:30:13  rvv
*** empty log message ***

Revision 1.6  2014/07/09 16:12:57  rvv
*** empty log message ***

Revision 1.5  2014/06/18 15:47:54  rvv
*** empty log message ***

Revision 1.4  2014/06/14 16:41:06  rvv
*** empty log message ***

Revision 1.3  2014/05/10 13:56:45  rvv
*** empty log message ***

Revision 1.2  2014/05/07 15:40:43  rvv
*** empty log message ***

Revision 1.1  2014/05/07 08:55:29  rvv
*** empty log message ***

Revision 1.1  2014/04/10 06:02:04  rvv
*** empty log message ***

Revision 1.5  2013/12/23 16:43:32  rvv
*** empty log message ***

Revision 1.4  2013/07/19 07:11:17  rvv
*** empty log message ***

Revision 1.3  2013/07/18 17:46:37  rvv
*** empty log message ***

Revision 1.2  2013/07/17 08:13:15  rvv
*** empty log message ***

Revision 1.1  2013/06/15 15:55:44  rvv
*** empty log message ***

Revision 1.3  2013/04/27 16:28:55  rvv
*** empty log message ***

*/


    global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";
    

    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
//
    $this->pdf->AddPage('P');
 	  $this->pdf->frontPage = true;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $extraMarge=15;
/*
   if(is_file($this->pdf->rapport_logo))
		{
		  $logoWidth=80;
			  $this->pdf->Image($this->pdf->rapport_logo, 210/2-$logoWidth/2, 15, $logoWidth);
		}
 */   
    if(is_file($this->pdf->rapport_logo))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 140, 20, 60);
		}
    
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


    
    $portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(50);
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetWidths(array($extraMarge,170));
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


    $this->pdf->SetWidths(array($extraMarge,30,30));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetY(80);
    
    
    $vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']));
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
 
    
      $this->pdf->ln();
    if(!isset($this->factuurnummer))
      $this->factuurnummer=1;

    $kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);

    $this->pdf->Row(array('',vertaalTekst('Factuurnummer',$this->pdf->rapport_taal),': '.substr($this->waarden['datumTot'],0,4).'-'.sprintf("%02d",$kwartaal).'-'.sprintf("%03d",$this->factuurnummer)));
    $this->pdf->Row(array('',vertaalTekst('Factuurdatum',$this->pdf->rapport_taal),': '.$nu));
    $this->pdf->ln();
    $this->pdf->Line($this->pdf->marge+$extraMarge,$this->pdf->GetY(),210-($this->pdf->marge+$extraMarge),$this->pdf->GetY());
    $this->pdf->SetWidths(array($extraMarge,150));

    $this->pdf->SetY(105);
    $this->DB = new DB();

    
$this->pdf->SetWidths(array($extraMarge,150));
$this->pdf->row(array('',$this->waarden['CRM_verzendAanhef'].',

'.vertaalTekst('Hierbij brengen wij de '.strtolower($this->waarden['SoortOvereenkomst']).'fee over het afgelopen kwartaal in rekening.',$this->pdf->rapport_taal).'

'));  

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


$this->pdf->SetAligns(array("L","L",'C','R'));
$this->pdf->SetWidths(array($extraMarge,65,18,22));  
$this->pdf->row(array('',vertaalTekst('Vermogen onder '.strtolower($this->waarden['SoortOvereenkomst']).' per ',$this->pdf->rapport_taal).$totDatum));
$this->pdf->Ln($this->pdf->rowHeight*-1);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$valutaTeken=":           ".$valuta['Valutateken'];
$this->pdf->row(array('','',$valutaTeken,$this->formatGetal($this->waarden['basisRekenvermogen'],2)));


$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->ln();
$this->pdf->row(array('',vertaalTekst($this->waarden['SoortOvereenkomst'].' fee',$this->pdf->rapport_taal).' '.$feePercentage.'% '.vertaalTekst('per kwartaal ',$this->pdf->rapport_taal)));

//listarray($this->waarden);
//,$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2)


$this->pdf->SetAligns(array("L","L",'C','R'));
$this->pdf->SetWidths(array($extraMarge,65,18,22));  



unset($this->pdf->CellBorders);
$this->pdf->ln();
$this->pdf->row(array('',vertaalTekst($this->waarden['SoortOvereenkomst']." fee",$this->pdf->rapport_taal)." ".$kortingsTekst,$valutaTeken,$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2)));
$this->pdf->ln();
if($this->waarden['btwTarief']==0 && $this->waarden['afwijkendeOmzetsoort']=='VRIJ')
{
  $this->pdf->row(array('', vertaalTekst("BTW Vrijgesteld", $this->pdf->rapport_taal), $valutaTeken, $this->formatGetal($this->waarden['btw'], 2)));
}
else
{
  $this->pdf->row(array('', vertaalTekst("BTW", $this->pdf->rapport_taal) . " " . $this->formatGetal($this->waarden['btwTarief'], 0) . "%", $valutaTeken, $this->formatGetal($this->waarden['btw'], 2)));
}
$this->pdf->ln(2);
//$this->pdf->CellBorders = array('','T','','T');
$this->pdf->row(array('',' ',' ',' '));
unset($this->pdf->CellBorders);
$this->pdf->row(array('',vertaalTekst('TOTAAL',$this->pdf->rapport_taal),$valutaTeken,$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->ln(2);
//$this->pdf->row(array('',' ',' ','---------------'));
$this->pdf->ln();
$this->pdf->SetWidths(array($extraMarge,150));
$this->pdf->row(array('',vertaalTekst('Dit bedrag zal een dezer dagen automatisch van uw beleggingsrekening worden afgeschreven.',$this->pdf->rapport_taal)));
$this->pdf->ln();

    $this->pdf->Row(array('',vertaalTekst('Met vriendelijke groet,',$this->pdf->rapport_taal)));
    if($portefeuilledata['Handtekening']<>'')
      $this->pdf->MemImage(base64_decode($portefeuilledata['Handtekening']),$this->pdf->getX()+$extraMarge,$this->pdf->getY(),60);
    $this->pdf->ln(20);
    
    $this->pdf->Row(array('',$portefeuilledata['vermogensbeheerder']));
    $this->pdf->Row(array('',$portefeuilledata['AccountmanagerNaam']));



    $this->pdf->SetAutoPageBreak(false);
    $this->pdf->setY(275);
    $this->pdf->MultiCell(210-$this->pdf->marge*2,4,"KvK: Handelsregister ’s-Gravenhage nr. 27302787 Bank: NL89ABNA0501484108 / BIC ABNANL2A BTW: NL818181709B01
Mercurius Vermogensbeheer B.V. Nassaulaan 19 2514JT Den Haag",0,'C');
$this->pdf->SetAutoPageBreak(true,8);


$this->pdf->SetTextColor(0,0,0);
$this->pdf->rowHeight=$rowHeightBackup;
?>
