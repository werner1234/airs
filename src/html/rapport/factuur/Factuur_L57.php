<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/15 16:11:06 $
File Versie					: $Revision: 1.11 $

$Log: Factuur_L57.php,v $
Revision 1.11  2020/04/15 16:11:06  rvv
*** empty log message ***

Revision 1.10  2020/04/11 16:34:15  rvv
*** empty log message ***

Revision 1.9  2020/02/12 16:41:55  rvv
*** empty log message ***

Revision 1.8  2020/02/09 12:20:07  rvv
*** empty log message ***

Revision 1.7  2020/02/09 08:09:26  rvv
*** empty log message ***

Revision 1.6  2020/01/18 13:29:35  rvv
*** empty log message ***

Revision 1.5  2019/11/06 15:54:59  rvv
*** empty log message ***

Revision 1.4  2016/07/16 15:15:15  rvv
*** empty log message ***

Revision 1.3  2015/10/21 07:27:08  rvv
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
    //$this->pdf->brief_font='Times';
    $fontsize=9;
    $this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
    
		$this->pdf->rapport_type = "FACTUUR";
     
		$this->pdf->AddPage('P');

   
		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);


	  $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $logoYpos=5;
		  $xSize=50;
	    $this->pdf->Image($this->pdf->rapport_logo,20, $logoYpos, $xSize);
 		}

    $font='Arial';
    $this->pdf->SetY(15);
    $this->pdf->SetTextColor(0);
    $this->pdf->SetWidths(array(140,60));
    $this->pdf->SetAligns(array("L","L"));
    $this->pdf->SetFont($font,"",$fontsize);
    $this->pdf->row(array('','Florentes Vermogensbeheer'));
    $this->pdf->row(array('','Maliebaan 89,Utrecht'));
    $this->pdf->row(array('','Zusterplein 22a, Zeist'));
    $this->pdf->row(array('','Parklaan 34, Rotterdam'));
    $this->pdf->ln();
    $this->pdf->row(array('','T: 085-023 0445'));
    $this->pdf->ln();
    $this->pdf->row(array('','florentesvermogensbeheer.nl'));
    $this->pdf->row(array('','btw.nr. NL814125256B01'));
    $this->pdf->row(array('','KVK 28103359'));
    $this->pdf->Ln(15);
    


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
CRM_naw.enOfRekening
FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";

	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $extraMarge=35;
		$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,140));
	  $this->pdf->SetAligns(array('R','L','L','R','R'));
    $this->pdf->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
    $extraVerlaging=10;
	  $this->pdf->SetY(50+$extraVerlaging);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',$fontsize);
	  $this->pdf->row(array('',""));//
	  $this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
		$this->pdf->row(array('Postadres',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));

    /*
    		$this->pdf->SetWidths(array(20-$this->pdf->marge,140));
	  $this->pdf->SetAligns(array('R','L','L','R','R'));
    $this->pdf->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
    $extraVerlaging=0;
	  $this->pdf->SetY(50+$extraVerlaging);
	  $this->pdf->SetFont($this->pdf->brief_font,'B',$fontsize);
	  $this->pdf->row(array('',""));//
	  $this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
		$this->pdf->row(array('',$crmData['naam']));
    if (trim($crmData['naam1']) <> "")  $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats=$crmData['verzendPc'];
    if($crmData['verzendPlaats'] != '') $plaats.=" ".$crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->row(array('',$crmData['verzendLand']));

$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,140));
$this->pdf->SetAligns(array('R','L','L','R','R'));
     */

  //  listarray($this->waarden['CRM_verzendAanhef']);

$beginDatumTxt=$__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))]." ".date("Y",db2jul($this->waarden['datumVan']));
$eindDatumTxt=$__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']));
$this->pdf->SetY(100+$extraVerlaging);
$this->pdf->row(array('Datum',(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));
$this->pdf->ln(10);
$this->pdf->row(array("Factuurperiode",$eindDatumTxt));
$this->pdf->ln(10);

if($this->waarden['BeheerfeeAantalFacturen']==4)
{
  $factuurnr = substr($this->waarden['datumTot'],0,4).'-kw'.ceil(date("n",db2jul($this->waarden['datumTot']))/3).'-'.sprintf("%04d",$this->waarden['factuurNummer']);
}
else
{
  $factuurnr = substr($this->waarden['datumTot'],0,4).'-m'.substr($this->waarden['datumTot'],5,2).'-'.sprintf("%04d",$this->waarden['factuurNummer']);
}
$this->pdf->row(array("Factuurnummer",$factuurnr));
$this->pdf->ln(15);

$this->pdf->SetFont($this->pdf->brief_font,'B',$fontsize);
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,140));
$this->pdf->row(array('',"FACTUUR BEHEERVERGOEDING"));
$this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);
$this->pdf->ln();
//$this->pdf->row(array('',"Onderstaand treft u de berekening van de beheervergoeding over de bovengenoemde factuurperiode aan."));
//$this->pdf->ln();
//$this->pdf->SetFont($this->pdf->brief_font,'',$fontsize);


$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,95,10,30,10));
$this->pdf->SetAligns(array('R','L','R','R','L'));
if($this->waarden['BeheerfeeBasisberekening']==2)
{
  $this->pdf->row(array('', "Belegd vermogen per ultimo " . $eindDatumTxt, "EUR", $this->formatGetal($this->waarden['rekenvermogenFee'], 2)));
}
else
{
  $this->pdf->row(array('', "Vermogen per " . $beginDatumTxt, "EUR", $this->formatGetal($this->waarden['totaalWaardeVanaf'], 2)));
  $this->pdf->row(array('', "Vermogen per " . $eindDatumTxt, "EUR", $this->formatGetal($this->waarden['totaalWaarde'], 2)));
  $this->pdf->CellBorders = array('', '', array('T', 'U'), array('T', 'U'));
  $this->pdf->row(array('', "Gemiddeld vermogen", "EUR", $this->formatGetal($this->waarden['gemiddeldeVermogen'], 2)));//$this->formatGetal(($this->waarden['drieMaandsWaarde_1']+$this->waarden['drieMaandsWaarde_2']+$this->waarden['drieMaandsWaarde_3'])/3
}
unset($this->pdf->CellBorders);

$this->pdf->ln();
$percentage=$this->waarden['BeheerfeePercentageVermogen'] / $this->waarden['BeheerfeeAantalFacturen'];
if($this->waarden['BeheerfeeAantalFacturen']==4)
  $periode='kwartaal';
else
  $periode='maand';
//listarray($this->waarden);exit;


$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,95,10,30));
$this->pdf->SetAligns(array('R','L','R','R'));
if($this->waarden['BeheerfeeBasisberekening']==2)
{
 // $this->pdf->row(array('', ));
  $this->pdf->row(array('',"Beheerloon " . $this->formatGetal($percentage, 3) . " % per $periode ". "over belegd vermogen", 'EUR', $this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'], 2)));
}
else
{
 // $this->pdf->row(array('', ));
  $this->pdf->row(array('', "Beheerloon " . $this->formatGetal($percentage, 3) . " % per $periode"." over gemiddeld vermogen", 'EUR', $this->formatGetal($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'], 2)));
}

if($this->waarden['administratieBedrag']<>0)
{
  $this->pdf->row(array('', "Toezichthouderskosten ".(date("Y",db2jul($this->waarden['datumTot']))+1), 'EUR', $this->formatGetal($this->waarden['administratieBedrag'], 2)));
//  $this->pdf->row(array('', "Toezichthouderskosten ".(date("Y",db2jul($this->waarden['datumTot']))), 'EUR', $this->formatGetal($this->waarden['administratieBedrag'], 2)));
  $this->pdf->CellBorders=array('','',array('T'),array('T'));
  $this->pdf->row(array('', "Subtotaal", 'EUR', $this->formatGetal($this->waarden['beheerfeePerPeriode'], 2)));
  $this->pdf->ln();
  unset($this->pdf->CellBorders);
}

$this->pdf->row(array('',"B.T.W. ".$this->formatGetal($this->waarden['btwTarief'],0)."%",'EUR',$this->formatGetal($this->waarden['btw'],2)));
$this->pdf->CellBorders=array('','',array('T','U'),array('T','U'));
$this->pdf->row(array('',"Totaal",'EUR',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
unset($this->pdf->CellBorders);
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge,140));
$this->pdf->ln(15);
$this->pdf->row(array('',"Dit bedrag zal binnen enkele werkdagen automatisch worden geïncasseerd van uw rekening ".$this->waarden['rekeningEur'].""));

/*
$this->pdf->ln(8);
$this->pdf->row(array('',"Wij vertrouwen erop u hiermee te hebben geïnformeerd.

Met vriendelijke groet,
Florentes vermogensbeheer BV"));
$this->pdf->ln();
*/
/*
$this->pdf->SetWidths(array($extraMarge-$this->pdf->marge-5,150));
$voet="Florentes vermogensbeheer B.V. is geregistreerd bij de Autoriteit Financiële Markten als vermogensbeheerder en effectenbemiddelaar en valt onder de Wet op het Financieel Toezicht 2007 (Wft), artikel 2.96. als bedoeld in artikel 1:1 Wft onderdeel c van de definitie van het verlenen van een beleggingsdienst. Florentes vermogensbeheer B.V.  is tevens geregistreerd als deelnemende instelling bij het Dutch Securities Institute. (DSI). Medewerkers van Florentes vermogensbeheer B.V. zijn aangesloten bij het DSI en bij de Federatie van Financieel Planners (FFP) of de Vereniging voor beleggingsanalisten (CFAVBA). Florentes vermogensbeheer B.V. is ingeschreven bij de Kamer van Koophandel Midden Nederland onder dossiernummer 28103359.";
 $trigger=$this->pdf->PageBreakTrigger;
 $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;
 $this->pdf->ln(38);
 $this->pdf->setY(270);
 $this->pdf->SetAligns(array('R','C'));
 $this->pdf->SetFont($this->pdf->brief_font,'',6);
 $rowHeightBackup=$this->pdf->rowHeight;
  $this->pdf->rowHeight = 3;
 $this->pdf->row(array('',$voet));
 $this->pdf->PageBreakTrigger=$trigger;
 */
 $this->pdf->rowHeight = $rowHeightBackup;
 $this->pdf->SetTextColor(0,0,0);
 $this->pdf->geenBasisFooter=true;
 $this->pdf->rowHeight= $this->pdf->rowHeightBackup;



    ?>
