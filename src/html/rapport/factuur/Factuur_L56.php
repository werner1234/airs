<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/07 12:24:59 $
File Versie					: $Revision: 1.14 $

$Log: Factuur_L56.php,v $
Revision 1.14  2019/07/07 12:24:59  rvv
*** empty log message ***

Revision 1.13  2019/07/05 16:39:37  rvv
*** empty log message ***

Revision 1.12  2018/10/10 16:11:45  rvv
*** empty log message ***

Revision 1.11  2018/07/18 15:55:26  rvv
*** empty log message ***

Revision 1.10  2017/10/11 16:16:17  rvv
*** empty log message ***

Revision 1.9  2017/06/08 05:30:23  rvv
*** empty log message ***

Revision 1.8  2016/01/18 19:21:05  rvv
*** empty log message ***

Revision 1.7  2016/01/18 19:15:18  rvv
*** empty log message ***

Revision 1.6  2016/01/17 18:17:14  rvv
*** empty log message ***

Revision 1.5  2015/10/14 11:11:38  rvv
*** empty log message ***

Revision 1.4  2015/10/14 09:59:03  rvv
*** empty log message ***

Revision 1.3  2015/07/08 15:40:46  rvv
*** empty log message ***

Revision 1.2  2015/04/04 15:16:43  rvv
*** empty log message ***

Revision 1.1  2015/03/24 16:31:29  rvv
*** empty log message ***

Revision 1.2  2015/01/31 20:03:51  rvv
*** empty log message ***

Revision 1.1  2014/10/19 08:53:58  rvv
*** empty log message ***



*/

global $__appvar;


    //$this->pdf->rowHeight = 5;
   	$this->pdf->underlinePercentage=0.8;
    $this->pdf->brief_font='Arial';
    $fontsize=8;
		$this->pdf->rapport_type = "FACTUUR";
     
		$this->pdf->AddPage('P');
    $rowHeightBackup=$this->pdf->rowHeight;
   
		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);




	  $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $factor=0.06;
      $xSize=669*$factor;
      $ySize=177*$factor;
        $logopos=(210/2)-($xSize/2);
	      $this->pdf->Image($logo, 20, 20, $xSize, $ySize);
		}
    $font='Arial';
    
     $this->pdf->setY(20);
  $this->pdf->SetFont($font,'',8);
$kop="Petram & Co. N.V. 
Maliesingel 27 
3581 BH Utrecht

T +31 (0)85 485 85 70 
E info@petram-co.com 
www.petram-co.com
 ";

$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetWidths(array(140,50));
$this->pdf->SetAligns(array("L","L","L"));
$this->pdf->row(array('',$kop));
$this->pdf->SetWidths(array(140,10,50));
$this->pdf->row(array('','IBAN','NL57 ABNA 0579 1779 98 '));
$this->pdf->row(array('','BIC','ABNANL2A'));
$this->pdf->row(array('','KVK','34140171'));
$this->pdf->row(array('','BTW','NL810449390B01'));

    $this->pdf->SetY(80);
    $this->pdf->SetTextColor(0);
    
    $this->pdf->SetFont($font,"",$fontsize);

$this->DB = new DB();

			  $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
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
CRM_naw.enOfRekening,
CRM_naw.btwnr
FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $extraMarge=60;
		$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,135));
	  $this->pdf->SetAligns(array('R','L','L','L','R'));
    $this->pdf->rowHeight = 5;
	  $this->pdf->SetY(75);
	  $this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
  
    $this->pdf->row(array('',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  
      $this->pdf->row(array('',$crmData['naam1']));

    $naam2=getExtraCrmNaam($this->portefeuille);
    if ($naam2 <> "")
    {
      $this->pdf->row(array('', $naam2));
    }
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));



$beginDatumTxt=date("j",db2jul($this->waarden['datumVan']))." ".$__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))]." ".date("Y",db2jul($this->waarden['datumVan']));
$eindDatumTxt=date("j",db2jul($this->waarden['datumTot']))." ".$__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']));
$this->pdf->SetY(110);
$this->pdf->row(array('',"Utrecht, ".(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
$this->pdf->SetY(130);
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,40,50));
$this->pdf->row(array('',"BTW-nummer",'NL810449390B01'));
$this->pdf->row(array('',"Factuurnummer",date("Y").'\\'.$this->waarden['factuurNummer']));
$this->pdf->SetY(150);
$this->pdf->SetFont($this->pdf->brief_font,'',$fontsize+4);
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,160));
$this->pdf->SetAligns(array('R','C','L','L','R'));
$this->pdf->row(array('',"NOTA"));
$this->pdf->SetAligns(array('R','L','L','L','R'));
$this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
$this->pdf->ln();

$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,135));
$this->pdf->SetAligns(array('R','L','R','R','L'));
$this->pdf->row(array('',"Vergoeding beheerwerkzaamheden volgens onderstaande waardeberekening van het vermogen per $eindDatumTxt"));
$this->pdf->ln();
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,85,15,35));
$this->pdf->SetAligns(array('R','L','R','R'));
$this->pdf->row(array('',"Waarde portefeuille per ".$eindDatumTxt,"EUR",$this->formatGetal($this->waarden['totaalWaarde'],2)));
$this->pdf->ln(2);


if($this->waarden['BeheerfeeBedragBuitenFee'] <> 0.0)
{
  if($this->waarden['BeheerfeeBedragBuitenFeePortefeuille']<>0)
    $uitgesloten=$this->waarden['BeheerfeeBedragBuitenFeePortefeuille'];
  else
    $uitgesloten=$this->waarden['BeheerfeeBedragBuitenFee'];
  $this->pdf->CellBorders = array('','','','US');
  $this->pdf->row(array('',"Van beheerfee vrijgesteld","EUR",$this->formatGetal($uitgesloten,2)));
  $this->pdf->ln(2);
  $this->pdf->CellBorders = array('','','','UU');
  $this->pdf->row(array('',"","EUR",$this->formatGetal($this->waarden['rekenvermogenFee'],2)));
  //$this->pdf->row(array('',"","EUR",$this->formatGetal($this->waarden['rekenvermogen'],2)));
  $this->pdf->ln(2);
  unset($this->pdf->CellBorders);
}
$this->pdf->ln(2);

$percentage=$this->waarden['BeheerfeePercentageVermogen'] / $this->waarden['BeheerfeeAantalFacturen'];


$this->pdf->row(array('',$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],4)." % over ".$this->formatGetal($this->waarden['rekenvermogen']),'EUR',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
$this->pdf->ln();
$this->pdf->row(array('',$this->formatGetal($this->waarden['btwTarief'],1)."% BTW",'EUR',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->ln(6);
$this->pdf->Rect($extraMarge-1,$this->pdf->GetY()-1,135+2,7,'DF','',array(225,225,225));
$this->pdf->row(array('',"",'EUR',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->ln(15);
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,135));

 //$this->pdf->setY(220);
$this->pdf->row(array('',"Bovenvermelde vergoeding zal ten laste worden gebracht van uw geldrekening nummer ".$this->waarden['rekeningEur']." bij ".$this->waarden['depotbankOmschrijving']."."));



$voet="Petram & Co. N.V. is geregistreerd bij de Autoriteit Financiële Markten (AFM) en staat onder toezicht van de AFM en 
De Nederlandse Bank (DNB). Petram & Co. N.V. staat ingeschreven in de registers van de Stichting DSI.";
 $trigger=$this->pdf->PageBreakTrigger;
 $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;

 $this->pdf->setY(280);
 $this->pdf->SetAligns(array('R','L'));
 $this->pdf->SetFont($this->pdf->brief_font,'i',6);

  $this->pdf->rowHeight = 3;
  $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
 $this->pdf->row(array('',$voet));
 $this->pdf->PageBreakTrigger=$trigger;
 
 $this->pdf->rowHeight = $rowHeightBackup;
 $this->pdf->SetTextColor(0,0,0);
 $this->pdf->geenBasisFooter=true;



?>