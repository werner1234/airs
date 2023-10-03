<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/01/13 19:10:29 $
 		File Versie					: $Revision: 1.20 $

 		$Log: RapportTRANSFEE_L99.php,v $
 
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//ini_set('max_execution_time',60);
class RapportTRANSFEE_L99
{
	function RapportTRANSFEE_L99($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
//		$this->pdf->rapport_titel = "Transactiekosten";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    if(!isset($this->pdf->excelData) || count($this->pdf->excelData)<1)
      $this->pdf->excelData[]=array('Portefeuille','Naam','Naam1','Adres','pc','Woonplaats','Factuurnummer','Totaal','BTW','TotaalInclusiefBtw');
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde <> 0)
		  return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
  
        
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, 
    Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, 
    Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client,Portefeuilles.afrekenvalutaKosten,Portefeuilles.BeheerfeeBTW,
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

    if($portefeuilledata['afrekenvalutaKosten']=='')
      $portefeuilledata['afrekenvalutaKosten']='EUR';
    $query="SELECT valuta,Valutateken FROM Valutas WHERE valuta='".$portefeuilledata['afrekenvalutaKosten']."'"; 
    $DB->SQL($query);
		$DB->Query();
		$valuta = $DB->nextRecord();
    if($valuta['Valutateken']=='')
      $valuta['Valutateken']=$valuta['valuta'];
      
    $query="SELECT
Rekeningen.Rekening,
Rekeningen.Portefeuille,
Rekeningen.Valuta,
Rekeningen.Depotbank,
Depotbanken.Omschrijving
FROM
Rekeningen
LEFT JOIN Depotbanken ON Rekeningen.Depotbank=Depotbanken.Depotbank
WHERE 
Portefeuille= '".$this->portefeuille."' AND Rekeningen.Valuta='".$portefeuilledata['afrekenvalutaKosten']."' AND 
Rekeningen.Inactief=0 AND Rekeningen.Deposito=0 AND Rekeningen.Memoriaal=0 AND Rekeningen.Depotbank='".$portefeuilledata['Depotbank']."'";
 		$DB->SQL($query);
		$DB->Query();
		$rekening = $DB->nextRecord();
    
    
    		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, 
     Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit,
     Rekeningmutaties.Bedrag as Bedrag,  
     (Rekeningmutaties.Credit-Rekeningmutaties.Debet)*Rekeningmutaties.Valutakoers as WaardeEur,
		Rekeningmutaties.Valutakoers,
    Rekeningmutaties.Rekening,
		 1  as Rapportagekoers, ".
		"Transactietypes.transactievorm
FROM
Rekeningmutaties
JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
INNER JOIN Transactietypes ON Rekeningmutaties.Transactietype = Transactietypes.Transactietype ".
		" WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id"; 
		$DB = new DB();
		$DB->SQL($query); 
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$transactietypen = array();
		$buffer = array();
		$sortBuffer = array();
    $kostenTotaal=0;

		while($mutaties = $DB->nextRecord())
		{
		  $buffer[]=$mutaties;
    }
    
  //  listarray($buffer);
    
    foreach($buffer as $index=>$mutatie)
    {//,Rekeningmutaties.Boekdatum,Rekeningmutaties.Afschriftnummer,Rekeningmutaties.Fonds
      $query="SELECT Rekeningmutaties.Omschrijving,Rekeningmutaties.Grootboekrekening
      FROM Rekeningmutaties 
      INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND Grootboekrekeningen.Kosten=1
      WHERE 
      Rekeningmutaties.Fonds='".$mutatie['Fonds']."' AND 
      Rekeningmutaties.Rekening='".$mutatie['Rekening']."' AND 
      Rekeningmutaties.Boekdatum='".$mutatie['Boekdatum']."' AND 
      Rekeningmutaties.Afschriftnummer='".$mutatie['Afschriftnummer']."' ";
      $DB->SQL($query);
      $DB->Query();
      $kostenFound=false;
      while($data=$DB->nextRecord())
      {
        //listarray($data);
         $kostenFound=true;
      }
      if($kostenFound==false)
      {
        unset($buffer[$index]);
      }
    }

    $regels=array();
    foreach($buffer as $mutatie)
    {
      /*
      if(strpos($mutatie['Fonds'],'%'))
        $query="UPDATE Fondsen SET fondssoort='OBL' WHERE Fonds='".$mutatie['Fonds']."'";
      else
        $query="UPDATE Fondsen SET fondssoort='AAND' WHERE Fonds='".$mutatie['Fonds']."'";
      $DB->SQL($query);
      $DB->Query();
      $portefeuilledata['Vermogensbeheerder']='FDX';
      */

		  $query="SELECT fondssoort,Beurzen.beursregio FROM Fondsen LEFT JOIN Beurzen ON Fondsen.beurs=Beurzen.beurs WHERE Fonds='".$mutatie['Fonds']."'";
      $DB->SQL($query);
      $fondsData=$DB->lookupRecord();
  
      if(trim($mutatie['transactievorm'])=='')
        $orderBy=",orderkosten.transactievorm asc ";
      else
        $orderBy=",orderkosten.transactievorm desc ";
          
      if(trim($fondsData['beursregio'])=='')
        $orderBy.=",orderkosten.beursregio asc ";
      else
        $orderBy.=",orderkosten.beursregio desc ";
	

          
      $query="SELECT
orderkosten.kostenpercentage,
orderkosten.kostenminimumbedrag,
orderkosten.brokerkostenpercentage,
orderkosten.brokerkostenminimumbedrag,
orderkosten.prijsPerStuk,
orderkosten.transactievorm,
orderkosten.beursregio
FROM
orderkosten
WHERE 
(
 (orderkosten.vermogensbeheerder='".$portefeuilledata['Vermogensbeheerder']."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='".$fondsData['beursregio']."') OR 
 (orderkosten.vermogensbeheerder='".$portefeuilledata['Vermogensbeheerder']."' AND orderkosten.Portefeuille='' AND orderkosten.beursregio='') OR 
  orderkosten.Portefeuille='".$this->portefeuille."' 
)
AND orderkosten.fondssoort='".$fondsData['fondssoort']."'
AND (orderkosten.transactievorm='".$mutatie['transactievorm']."' OR orderkosten.transactievorm='')
ORDER BY orderkosten.Portefeuille desc $orderBy LIMIT 1";
       $DB->SQL($query);
	     $kostenData=$DB->lookupRecord();

      if($portefeuilledata['afrekenvalutaKosten']=='EUR')
        $omrekenKoers=1;
      elseif($portefeuilledata['afrekenvalutaKosten'] == $mutatie['Valuta']) 
        $omrekenKoers=$mutatie['Valutakoers'];
      else
        $omrekenKoers=getValutaKoers($portefeuilledata['afrekenvalutaKosten'],$mutatie["Boekdatum"]);  
         
      //listarray($fondsData);
      //listarray($kostenData);
      if($kostenData['prijsPerStuk'] <> 0)
      {
        $kosten=abs($mutatie['Aantal'])*($kostenData['prijsPerStuk']);
        $vast=$this->formatGetal($kostenData['prijsPerStuk'],2);
        $percentage='';
      }
      else
      {
        $kosten=abs($mutatie['WaardeEur'])*($kostenData['kostenpercentage']/100);
        $vast='';
        $percentage=$this->formatGetal($kostenData['kostenpercentage'],2);
      }
      if($kosten < $kostenData['kostenminimumbedrag'])
        $kosten=$kostenData['kostenminimumbedrag'];
        
      $kosten=$kosten/$omrekenKoers;
      $mutatie["WaardeEur"]=$mutatie["WaardeEur"]/$omrekenKoers;
    
      $regel=array('',date('d-m',db2jul($mutatie["Boekdatum"])),
          $mutatie["Transactietype"],
          $this->formatGetal($mutatie["Aantal"]),
          $mutatie["Omschrijving"],
          $this->formatGetal($mutatie["Fondskoers"],2),
          $this->formatGetal($mutatie["WaardeEur"],2), 
          $vast,
          $percentage,
          $this->formatGetal($kosten,2));
       $regels[]=$regel;
          
      $kostenTotaal+=round($kosten,2);    

		}
    
    
if($kostenTotaal <> 0.00)
{ 
    
    $this->pdf->AddPage('P');
 	  $this->pdf->frontPage = true;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $extraMarge=11;

 
    //$this->pdf->SetFont($this->pdf->rapport_font,'',10);
		//$this->pdf->SetWidths(array($extraMarge,120));
    //$this->pdf->Row(array('',$portefeuilledata['vermogensbeheerder']));
    
    if(is_file($this->pdf->rapport_logo))
		{
			  $this->pdf->Image($this->pdf->rapport_logo, 140, 20, 60);
		}

    
    $portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
		$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
		$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
		$portefeuilledata['pc']=$this->pdf->portefeuilledata['pc'];
		$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetY(50);
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

		$this->pdf->SetY(60);
    $this->pdf->SetAligns(array('L','R'));
    $this->pdf->Row(array('','Den Haag, '.date('d-m-Y')));
    $this->pdf->SetWidths(array($extraMarge,150));
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetY(80);

$txt=$this->pdf->portefeuilledata['verzendAanhef'].",

Hierbij ontvangt u de factuur voor de voor uw rekening bij ".$this->pdf->portefeuilledata['DepotbankOmschrijving']." uitgevoerde transacties in de periode ".date('d-m-Y',db2jul($this->rapportageDatumVanaf))." tot en met ".date('d-m-Y',db2jul($this->rapportageDatum)).". De transacties en de daarbij behorende bedragen zijn in de onderstaande tabel gespecificeerd. 

Het totaalbedrag zal van uw rekening, ".substr($rekening['Rekening'],0,-3).", bij ".$this->pdf->portefeuilledata['DepotbankOmschrijving']." worden afgeschreven.";

    $this->pdf->Row(array('',$txt));

/*    
    $startWeek=intval(date('W',db2jul($this->rapportageDatumVanaf)));
    $eindWeek=intval(date('W',db2jul($this->rapportageDatum)));
    $txt="Overzicht transacties week nr. $startWeek";
    if($startWeek < $eindWeek)
    {
      for($i=$startWeek;$i<=$eindWeek;$i++)
      {
        if($i<>$startWeek)
        {
          if($i<>$eindWeek)
            $txt.=", $i";
          else
            $txt.=" en $i";
        }
      }
    }
    $this->pdf->SetY(140);
    $this->pdf->Row(array('',$txt.":"));
*/
    $this->pdf->ln();
    if(!isset($this->pdf->factuurNummer))
      $this->pdf->factuurNummer=1;
    $this->pdf->Row(array('',vertaalTekst('Factuurnummer',$this->pdf->rapport_taal).' '.date('ymd').$this->portefeuille.sprintf("%03d",$this->pdf->factuurNummer)));
    $this->pdf->ln();
    
    

    if($portefeuilledata['afrekenvalutaKosten']!='EUR')
      $headerTotaalBedrag=vertaalTekst('Totaalbedrag',$this->pdf->rapport_taal).' '.vertaalTekst('in',$this->pdf->rapport_taal).' '.$portefeuilledata['afrekenvalutaKosten'];
    else
      $headerTotaalBedrag=vertaalTekst('Totaalbedrag',$this->pdf->rapport_taal);  
      
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->widthA = array($extraMarge,12,17,15,57,15,21,12,17,16);
    $this->pdf->widthB = array($extraMarge,12,17,12,57,15,21,12,12,19);
		$this->pdf->alignA = array('L','L','L','R','L','R','R','R','R','R','R');
		// print categorie headers
	  $this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('',array('L','T'),'T','T','T','T','T','T','T',array('T','R'));
    $this->pdf->Row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal),
                             vertaalTekst('Transactie soort',$this->pdf->rapport_taal),
                             vertaalTekst('Aantal',$this->pdf->rapport_taal),
                             vertaalTekst('Omschrijving',$this->pdf->rapport_taal),
                             vertaalTekst('Koers',$this->pdf->rapport_taal),
                             $headerTotaalBedrag,
                             vertaalTekst("Tarief\nvast *)",$this->pdf->rapport_taal),
                             vertaalTekst("\n% **)",$this->pdf->rapport_taal),
                             vertaalTekst('Provisie bedrag in '.$valuta['Valutateken'],$this->pdf->rapport_taal)));
                             
    $this->pdf->CellBorders = array('','L','','','','','','','','R');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);  
    foreach($regels as $regel)   
       $this->pdf->Row($regel);

    $this->pdf->CellBorders = array('','L','R');
   	$this->pdf->SetAligns(array('L','R','R'));
    $this->pdf->widthA = array($extraMarge,12+17+15+57+15+21+12+12,16);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->CellBorders = array('','L',array('R','UU'));
    $this->pdf->Row(array('','',''));
    $this->pdf->CellBorders = array('','L','R');
    $this->pdf->Row(array('','',''));
    $this->pdf->Row(array('',vertaalTekst('Totaal transactiekosten',$this->pdf->rapport_taal),$this->formatGetal($kostenTotaal,2)));

    $btw=round($kostenTotaal*$portefeuilledata['BeheerfeeBTW']/100,2);
    $this->pdf->Row(array('',vertaalTekst('BTW',$this->pdf->rapport_taal).' '.round($portefeuilledata['BeheerfeeBTW']).'%',$this->formatGetal($btw,2)));
    $this->pdf->Row(array('',vertaalTekst('Totaal bedrag',$this->pdf->rapport_taal),$this->formatGetal($kostenTotaal+$btw,2)));
    
    $this->pdf->CellBorders = array('',array('L','U'),array('R','U'));
    $this->pdf->Row(array('','',''));
    $this->pdf->CellBorders = array();
    $this->pdf->SetAligns(array('L','L','R'));

    $this->pdf->Row(array('',vertaalTekst('*) 	dit geldt voor opties',$this->pdf->rapport_taal)));
    $this->pdf->Row(array('',vertaalTekst('**)	dit geldt voor alle andere effectentransacties',$this->pdf->rapport_taal)));
    $this->pdf->ln();
    if($this->pdf->GetY() > 220)
    {
      $this->pdf->SetAutoPageBreak(false);
      $this->pdf->setY(275);
      $this->pdf->MultiCell(210-$this->pdf->marge*2,4,"KvK: Handelsregister ’s-Gravenhage nr. 27302787 Bank: NL89ABNA0501484108 / BIC ABNANL2A BTW: NL818181709B01
Mercurius Vermogensbeheer B.V. Nassaulaan 19 2514JT Den Haag",0,'C');
      $this->pdf->SetAutoPageBreak(true,8);
      $this->pdf->AddPage('P');
 	    $this->pdf->frontPage = true;  
      $this->pdf->ln(10);
    }
    $this->pdf->Row(array('',vertaalTekst('Mocht u naar aanleiding van deze factuur nog vragen hebben, dan verzoeken wij u om binnen 14 dagen te reageren.',$this->pdf->rapport_taal)));
    $this->pdf->ln(10);
    $this->pdf->Row(array('',vertaalTekst('Met vriendelijke groet,',$this->pdf->rapport_taal)));
    if($portefeuilledata['Handtekening']<>'')
      $this->pdf->MemImage(base64_decode($portefeuilledata['Handtekening']),$this->pdf->getX()+$extraMarge,$this->pdf->getY(),60);
    $this->pdf->ln(20);
    
    $this->pdf->Row(array('',$portefeuilledata['vermogensbeheerder']));
    $this->pdf->Row(array('',$portefeuilledata['AccountmanagerNaam']));
  
    $this->pdf->excelData[]=array($this->portefeuille,
                                $this->pdf->portefeuilledata['Naam'],
                                $this->pdf->portefeuilledata['Naam1'],
                                $this->pdf->portefeuilledata['Adres'],
                                $this->pdf->portefeuilledata['pc'],
                                $this->pdf->portefeuilledata['Woonplaats'],
                                date('ymd').$this->portefeuille.sprintf("%03d",$this->pdf->factuurNummer),
                                $kostenTotaal,
                                $btw,
                                round($kostenTotaal+$btw,2));
    $this->pdf->factuurNummer++;                              
    $this->pdf->SetAutoPageBreak(false);
    $this->pdf->setY(275);
    $this->pdf->MultiCell(210-$this->pdf->marge*2,4,"KvK: Handelsregister ’s-Gravenhage nr. 27302787 Bank: NL89ABNA0501484108 / BIC ABNANL2A BTW: NL818181709B01
Mercurius Vermogensbeheer B.V. Nassaulaan 19 2514JT Den Haag",0,'C');
}
else
{
  if($this->pdf->selectData['type'] != 'pdf')
    $this->pdf->stopOutput=true;
  logScherm("Geen TRANSFEE uivoer voor portefeuille ".$this->portefeuille);
}  
$this->pdf->SetAutoPageBreak(true,8);
  
  
  
  
  }
}
?>
