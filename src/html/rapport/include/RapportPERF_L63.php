<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/14 17:46:24 $
File Versie					: $Revision: 1.9 $

$Log: RapportPERF_L63.php,v $
Revision 1.9  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.8  2019/01/19 18:05:31  rvv
*** empty log message ***

Revision 1.7  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.6  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.5  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.4  2016/01/25 07:43:16  rvv
*** empty log message ***

Revision 1.3  2016/01/23 17:53:31  rvv
*** empty log message ***

Revision 1.2  2015/12/16 17:06:48  rvv
*** empty log message ***

Revision 1.1  2015/09/20 17:32:28  rvv
*** empty log message ***

Revision 1.1  2011/05/08 09:37:33  rvv
*** empty log message ***

Revision 1.1  2011/04/19 16:41:39  rvv
*** empty log message ***

Revision 1.43  2010/07/31 16:07:05  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERF_L63
{

	function RapportPERF_L63($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

   	$this->pdf->AddPage();
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $index=new indexHerberekening();
    $perioden=$index->getKwartalen(db2jul($this->rapportageDatumVanaf),db2jul($this->rapportageDatum));
 
 
		$y=$this->pdf->getY();
		$x=$this->pdf->getX();
		$this->pdf->setXY($x,$y);
    $jaar=substr($this->rapportageDatumVanaf,0,4);
    foreach($perioden as $periode)
    {
      $kwartaal=ceil(date("n",(db2jul($periode['stop'])))/3);
      $data[$kwartaal.'e kwartaal '.$jaar]=$this->getPerfWaarden($periode['start'],$periode['stop']);
    }

    $data['totaal '.$jaar]=$this->getPerfWaarden($this->rapportageDatumVanaf,$this->rapportageDatum);
   
   
   
    $koppen=array('');
    $koppenXls=array('');
    $tmp=array();
    $tmpXls=array();
    foreach($data as $col=>$regelData)
    {
      $koppen[]=$col;
      $koppenXls[]=$col;
      $n=0;
      foreach($regelData as $omschrijving => $waarde)
      {
        if(!isset($tmp[$n][0]))
        {
          $tmp[$n][0]=$omschrijving;
          $tmpXls[$n][0]=$omschrijving;
        }
        $tmp[$n][]=$this->formatGetal($waarde,2);
        $tmpXls[$n][]=round($waarde,2);
        $n++;
      }
      
		}
    
    $this->pdf->excelData[]=$koppenXls;
    
    $this->pdf->SetWidths(array(82,40,40,40,40,40));
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop2_bgcolor['r'],$this->pdf->rapport_kop2_bgcolor['g'],$this->pdf->rapport_kop2_bgcolor['b']);
    $this->pdf->Rect($this->pdf->GetX(),$this->pdf->GetY(),82,8,'FD');
    $this->pdf->Rect($this->pdf->GetX()+82,$this->pdf->GetY()+8,5*40,count($tmp)*4,'FD');
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->Rect($this->pdf->GetX()+82,$this->pdf->GetY(),5*40,8,'FD');
    $this->pdf->Rect($this->pdf->GetX(),$this->pdf->GetY()+8,82,count($tmp)*4,'FD');
    
		$this->pdf->SetAligns(array('L','R','R','R','R','R'));
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($koppen);
    $this->pdf->Ln(4);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$backup=$this->pdf->PageBreakTrigger;
		$this->pdf->PageBreakTrigger=200;
    foreach($tmp as $row)
      $this->pdf->row($row);
    foreach($tmpXls as $row)
      $this->pdf->excelData[]=$row;
		$this->pdf->PageBreakTrigger=$backup;
   
   // $this->addPerf($this->rapportageDatumVanaf,$this->rapportageDatum,0);
   // $this->pdf->setXY($x,$y);
	//	$this->addPerf(substr($this->rapportageDatumVanaf,0,4)."-01-01",$this->rapportageDatum,130);

	}
  
  function getPerfWaarden($vanaf,$tot)
  {
    if(substr($vanaf,5,5)=='01-01')
      $minDag=true;
    else
      $minDag=false;  
    $waardenBegin=berekenPortefeuilleWaarde($this->portefeuille,$vanaf,$minDag,'EUR',$vanaf);
  
    $ValutaKoersBegin=1;
    $ValutaKoersEind=1;
    $koersQuery = "1";
    if($this->pdf->rapportageValuta<>'' && $this->pdf->rapportageValuta<>'EUR')
    {
      $ValutaKoersBegin = getValutaKoers($this->pdf->rapportageValuta, $vanaf);
      $ValutaKoersEind = getValutaKoers($this->pdf->rapportageValuta, $tot);
      $koersQuery =	" (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";

    }
    $verdeling=array();
    $totaal=array();
    foreach($waardenBegin as $idex=>$waarden)
    {
      $waarden['actuelePortefeuilleWaardeEuro']=$waarden['actuelePortefeuilleWaardeEuro']/$ValutaKoersEind;
      $waarden['beginPortefeuilleWaardeEuro']=$waarden['beginPortefeuilleWaardeEuro']/$ValutaKoersBegin;
      
      if($waarden['type']=='rente' && $waarden['fonds']=='')
      {
        $waarden['type']='rekeningRente';
      }
      $verdeling[$vanaf][$waarden['type']]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $verdeling[$vanaf][$waarden['type']]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $verdeling[$vanaf][$waarden['type']]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);
      $totaal[$vanaf]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $totaal[$vanaf]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $totaal[$vanaf]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);

    }
    if(substr($tot,5,5)=='01-01')
      $minDag=true;
    else
      $minDag=false;
    $waardenEind=berekenPortefeuilleWaarde($this->portefeuille,$tot,$minDag,'EUR',$vanaf);

    foreach($waardenEind as $idex=>$waarden)
    {
      $waarden['actuelePortefeuilleWaardeEuro']=$waarden['actuelePortefeuilleWaardeEuro']/$ValutaKoersEind;
      $waarden['beginPortefeuilleWaardeEuro']=$waarden['beginPortefeuilleWaardeEuro']/$ValutaKoersBegin;
      if($waarden['type']=='rente' && $waarden['fonds']=='')
      {
        $waarden['type']='rekeningRente';
      }
      $verdeling[$tot][$waarden['type']]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $verdeling[$tot][$waarden['type']]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $verdeling[$tot][$waarden['type']]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);
      $totaal[$tot]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $totaal[$tot]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $totaal[$tot]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);

    }
    $toonWaarden['Begin vermogen']=$totaal[$vanaf]['actuelePortefeuilleWaardeEuro']*-1;
    $toonWaarden['Eind vermogen']=$totaal[$tot]['actuelePortefeuilleWaardeEuro'];
    $toonWaarden['Begin effecten']=$verdeling[$vanaf]['fondsen']['actuelePortefeuilleWaardeEuro']*-1;
    $toonWaarden['Eind effecten']=$verdeling[$tot]['fondsen']['actuelePortefeuilleWaardeEuro']; 
    $toonWaarden['Begin saldo opgelopen rente']=$verdeling[$vanaf]['rente']['actuelePortefeuilleWaardeEuro']*-1; 
    $toonWaarden['Eind saldo opgelopen rente']=$verdeling[$tot]['rente']['actuelePortefeuilleWaardeEuro']; 
    $toonWaarden['Begin liquiditeiten']=$verdeling[$vanaf]['rekening']['actuelePortefeuilleWaardeEuro']*-1;
    $toonWaarden['Eind liquiditeiten']=$verdeling[$tot]['rekening']['actuelePortefeuilleWaardeEuro']; 
    $toonWaarden['Begin opgelopen rente liquiditeiten']=$verdeling[$vanaf]['rekeningRente']['actuelePortefeuilleWaardeEuro']*-1; 
    $toonWaarden['Eind opgelopen rente liquiditeiten']=$verdeling[$tot]['rekeningRente']['actuelePortefeuilleWaardeEuro']; 

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers - Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers / $koersQuery ) AS totaal
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Transactietype IN('A','A/O','A/S')";
		$DB = new DB();
		$DB->SQL($query);
    $tmp=$DB->lookupRecord();
    $toonWaarden['Totaal aankopen']=$tmp['totaal']; 
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers - Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers / $koersQuery  ) AS totaal
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Transactietype IN('V','V/O','V/S')";
		$DB = new DB();
		$DB->SQL($query);
    $DB->lookupRecord();
    $tmp=$DB->lookupRecord();
    $toonWaarden['Totaal verkopen']=$tmp['totaal'];
  
  
    $perGrootboek=array();
    $grootboekrekeningen=array();
    $query = "SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving
FROM Grootboekrekeningen
JOIN KeuzePerVermogensbeheerder ON Grootboekrekeningen.Grootboekrekening=KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Grootboekrekeningen.Opbrengst = '1' OR Kosten = '1' ORDER BY Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten,KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($opbrengst = $DB->nextRecord())
    {
      $perGrootboek[$opbrengst['Omschrijving']] = 0;
      $grootboekrekeningen[$opbrengst['Grootboekrekening']]=$opbrengst['Omschrijving'];
    }
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, 
		  	SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet,
		  	$koersQuery as rapportagevalutaKoers,
		  	Grootboekrekeningen.Grootboekrekening,
        Grootboekrekeningen.Omschrijving 
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
        JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Grootboekrekeningen.Opbrengst = '1' GROUP BY Grootboekrekeningen.Omschrijving ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
   	while($opbrengst = $DB->nextRecord())
		{
			$perGrootboek[$opbrengst['Omschrijving']] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet'])/$opbrengst['rapportagevalutaKoers']*-1;
      $grootboekrekeningen[$opbrengst['Grootboekrekening']]=$opbrengst['Omschrijving'];
		}

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, 
		  	SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet,
		  	$koersQuery as rapportagevalutaKoers,
		  	Grootboekrekeningen.Grootboekrekening,
        Grootboekrekeningen.Omschrijving 
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
        JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Grootboekrekeningen.Kosten = '1' GROUP BY Grootboekrekeningen.Omschrijving ";
		$DB->SQL($query);
		$DB->Query();        
    while($kosten = $DB->nextRecord())
		{
		  $perGrootboek[$kosten['Omschrijving']] +=  ($kosten['totaalcredit']-$kosten['totaaldebet'])/$kosten['rapportagevalutaKoers'];
      $grootboekrekeningen[$kosten['Grootboekrekening']]=$kosten['Omschrijving'];
		}


		foreach($grootboekrekeningen as $grootboekrekening=>$grootboekOmschrijving)
    {
		  if($grootboekrekening=='HUUR' || $grootboekrekening=='OG')
      {
        if(round($perGrootboek[$grootboekOmschrijving],1) <> 0.0)
          $toonWaarden[$grootboekOmschrijving]=$perGrootboek[$grootboekOmschrijving];
      }
      else
		    $toonWaarden[$grootboekOmschrijving]=$perGrootboek[$grootboekOmschrijving];
    }
    
    $waardeMutatie = $toonWaarden['Eind vermogen'] - $toonWaarden['Begin vermogen'];
		$toonWaarden['Stortingen'] 	 = getStortingen($this->portefeuille,$vanaf,$tot,$this->pdf->rapportageValuta)*-1;
		$toonWaarden['Onttrekkingen']= getOnttrekkingen($this->portefeuille,$vanaf,$tot,$this->pdf->rapportageValuta);
		//$toonWaarden['Resultaat verslagperiode'] = $waardeMutatie - $toonWaarden['Stortingen'] + $toonWaarden['Onttrekkingen'];
    
    $ongerealiseerd=$verdeling[$tot]['fondsen']['actuelePortefeuilleWaardeEuro']-$verdeling[$tot]['fondsen']['beginPortefeuilleWaardeEuro'];
    $gerealiseerd=gerealiseerdKoersresultaat($this->portefeuille, $vanaf, $tot,$this->pdf->rapportageValuta,true);
    $toonWaarden['Ongerealiseerd koers resultaat']=$ongerealiseerd;
    $toonWaarden['Gerealiseerd koers resultaat']=$gerealiseerd;
    $toonWaarden['Ongerealiseerd koers resultaat ']=$ongerealiseerd*-1;
    $toonWaarden['Gerealiseerd koers resultaat ']=$gerealiseerd*-1;
        
    return $toonWaarden;
  }

	function addPerf($vanaf,$tot,$offset)
	{
    global $__appvar;
	  $this->pdf->widthA = array(5+$offset,80,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(1+$offset,95,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','R','R');

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$kopStyle = "u";
		// ***************************** ophalen data voor afdruk ************************ //

		$DB = new DB();
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal,TijdelijkeRapportage.type ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$tot."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.type";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
    $waardeEind=0;
    $rapportageWaarden=array();
    while($totaalWaarde = $DB->nextRecord())
    {
      $waardeEind				+= $totaalWaarde['totaal'];
      $rapportageWaarden[$tot][$totaalWaarde['type']]=$totaalWaarde['totaal'];
    }

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal,TijdelijkeRapportage.type ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$vanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.type";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
    $waardeBegin=0;
    while($totaalWaardeVanaf = $DB->nextRecord())
    {
      $waardeBegin 			 	+= $totaalWaardeVanaf['totaal'];
      $rapportageWaarden[$vanaf][$totaalWaardeVanaf['type']]=$totaalWaardeVanaf['totaal'];
    }

		
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$vanaf,$tot,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$vanaf,$tot,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $vanaf, $tot, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

		// ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
 		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro)/ ".$this->pdf->ValutaKoersStart."  AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$tot."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart." ) AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$vanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 . " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];

    $RapJaar = date("Y", db2jul($tot));
    $RapStartJaar = date("Y", db2jul($vanaf));
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;

		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $vanaf, $tot,$this->pdf->rapportageValuta,true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;

		// ophalen van rente totaal A en rentetotaal B
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$tot."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$vanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $this->pdf->ValutaKoersEind;
		$totaalOpbrengst += $opgelopenRente;

		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$vanaf."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
   		while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}

		$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
				$kostenPerGrootboek = array();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$vanaf."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";
			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
      while($kosten = $DB2->nextRecord())
		  {
				$kostenPerGrootboek[$gb['Omschrijving']] =  ($kosten['totaalcredit']-$kosten['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			  $totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		  }
		}

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		// het overgebleven is de koers resultaat op valutas (om de getalletjes te laten kloppen).
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		// ***************************** einde ophalen data voor afdruk ************************ //

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		$extraLengte = $this->pdf->rapport_PERF_lijnenKorter;

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Ontwikkeling van het vermogen",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($vanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($vanaf))],$this->pdf->taal)." ".date("Y",db2jul($vanaf)),$this->formatGetal($waardeBegin,2,true),""));
    $this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($tot))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($tot))],$this->pdf->taal)." ".date("Y",db2jul($tot)),$this->formatGetal($waardeEind,2),""));
  	$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
		$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
		$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($ongerealiseerdeKoersResultaat,2),""));
		$this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($gerealiseerdeKoersResultaat,2),""));
	  $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,2),""));
	  $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($opgelopenRente,2),""));
		while (list($key, $value) = each($opbrengstenPerGrootboek))
      $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value,2),""));
		while (list($key, $value) = each($kostenPerGrootboek))
  	  $this->pdf->row(array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($value*-1,2),""));
  
    $this->pdf->ln();
 		$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));

	}
}
?>