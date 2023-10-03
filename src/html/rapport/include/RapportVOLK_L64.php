<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/08 16:06:01 $
File Versie					: $Revision: 1.16 $

$Log: RapportVOLK_L64.php,v $
Revision 1.16  2019/06/08 16:06:01  rvv
*** empty log message ***

Revision 1.15  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.14  2018/02/10 18:09:44  rvv
*** empty log message ***

Revision 1.13  2018/02/03 18:54:04  rvv
*** empty log message ***

Revision 1.12  2017/06/28 15:28:21  rvv
*** empty log message ***

Revision 1.11  2017/06/21 16:17:42  rvv
*** empty log message ***

Revision 1.10  2017/06/14 16:11:15  rvv
*** empty log message ***

Revision 1.9  2017/05/31 16:09:43  rvv
*** empty log message ***

Revision 1.8  2017/05/24 15:56:56  rvv
*** empty log message ***

Revision 1.7  2017/05/15 06:07:59  rvv
*** empty log message ***

Revision 1.6  2017/04/29 17:26:01  rvv
*** empty log message ***

Revision 1.5  2017/04/27 06:12:33  rvv
*** empty log message ***

Revision 1.4  2017/04/26 15:19:25  rvv
*** empty log message ***

Revision 1.3  2016/08/17 16:01:13  rvv
*** empty log message ***

Revision 1.2  2016/07/31 10:40:44  rvv
*** empty log message ***

Revision 1.1  2015/11/29 13:13:22  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L64
{
	function RapportVOLK_L64($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
		$this->pdf->excelData[]=array('Categorie','Effect','ISIN','Aantal','Valuta','Koers',"Portefeuille in EUR","in % van Vermogen",'Koers',"Portefeuille in EUR",'Koers Resultaat','Rente / Dividend',"Totaal in %","Historische kostprijs","YtD-rendement Portefeuille",'Portefeuille','Portefeuillewaarde','perfVanafStart');

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}



	function printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde,$resultaat=true)
	{
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          if(!isset($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro']))
          {
            $this->pdf->CellBorders = array('','','','','SUB','SUB');
            $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
            $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc)));
          }
          else
          {
          $this->pdf->CellBorders = array('','','','','SUB','SUB','','','SUB','','SUB','SUB','SUB');
          if($resultaat)
          {
            $resultaatProcent=$this->formatGetal((($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] - $categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] + $categorieTotaal[$lastCategorieOmschrijving]['dividendCorrected']) / ($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'] /100)),$this->pdf->rapport_VOLK_decimaal_proc);
            $resultaatWaarde=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']-$categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal);
            $resultaatDividend=$this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['dividend'],$this->pdf->rapport_VOLK_decimaal);
          }
          else
          {
            $this->pdf->CellBorders = array('','','','','SUB','SUB','','','SUB','','','');
            $resultaatProcent='';
            $resultaatWaarde='';
            $resultaatDividend='';
          }
          

          $this->pdf->row(array("Subtotaal ".$lastCategorieOmschrijving,'','','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['actuelePortefeuilleWaardeEuro']/ ($totaalWaarde/100),$this->pdf->rapport_VOLK_decimaal_proc),'','',
          $this->formatGetal($categorieTotaal[$lastCategorieOmschrijving]['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),'',
          $resultaatWaarde,
          $resultaatDividend,
          $resultaatProcent));
          }
          $this->pdf->CellBorders = array();
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		return $totaalB;
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->row(array($title));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
  
  function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
       elseif($data['type']=='fondsen')  
         $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalCorrected=$totaal;

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    { 
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      } 
     // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
  //  listarray($aantal);
//    if($fonds=='Delta Lloyd VAR 12-42')
//    {
//     echo $fonds." ".$rente[$this->rapportageDatum].' - '.$rente[$this->rapportageDatumVanaf]."<br>\n"; 
//      echo "$query <br>\n";// exit;
//    }  
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }

	function writeRapport()
	{
		global $__appvar;
   // $this->pdf->rapport_fontsize-=1;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex,
    Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client ,Portefeuilles.Startdatum
    FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$perf=0;
		$perfStart=0;
		if($this->pdf->extra=='xls')
		{
			$perf=round(performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta),2);
			$perfStart=round(performanceMeting($this->portefeuille, substr($this->portefeuilledata['Startdatum'],0,10) , $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta),2);
		}

		$this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
//hoofdscategorieVolgorde.Afdrukvolgorde as hoofdcategorieAfdrukVolgorde,
		$query = "SELECT
TijdelijkeRapportage.type,TijdelijkeRapportage.rekening,
if(TijdelijkeRapportage.type='rente', ifnull(300,hoofdscategorieVolgorde.Afdrukvolgorde+100),
if(TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten',400,
hoofdscategorieVolgorde.Afdrukvolgorde))  as hoofdcategorieAfdrukVolgorde,
hoofdscategorieVolgorde.Omschrijving as hoofdcategorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
categorieVolgorde.Afdrukvolgorde as categorieAfdrukVolgorde,categorieVolgorde.Omschrijving as categorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
						" TijdelijkeRapportage.rentedatum, ".
		" TijdelijkeRapportage.renteperiode, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
				   Valutas.Valutateken,
				    Fondsen.ISINCode".
				" FROM TijdelijkeRapportage
				LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as hoofdscategorieVolgorde ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdscategorieVolgorde.Beleggingscategorie
LEFT Join Beleggingscategorien as categorieVolgorde ON TijdelijkeRapportage.beleggingscategorie = categorieVolgorde.Beleggingscategorie
LEFT Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta
LEFT JOIN Fondsen on TijdelijkeRapportage.Fonds=Fondsen.Fonds
WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY hoofdcategorieAfdrukVolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
categorieAfdrukVolgorde,
TijdelijkeRapportage.beleggingscategorie ,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
//" TijdelijkeRapportage.type =  'fondsen' AND ".
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($data = $DB->NextRecord())
		{
		  //categorietotalen
		  if($data['type']=='fondsen' && $data['beleggingscategorie'] <> 'Liquiditeiten')
		    $fondsData[]=$data;
		  elseif($data['type']=='rente')
		    $renteData[]=$data;
		  else
		    $liquiditeitenData[]=$data;
		}

		foreach ($fondsData as $data)
		{
		  $dividend=$this->getDividend($data['fonds']);
	    if($data['categorieOmschrijving'] != $lastCategorieOmschrijving && $lastCategorieOmschrijving !='' && is_array($categorieTotaal[$lastCategorieOmschrijving]))
          $this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);
      if($data['hoofdcategorieOmschrijving'] != $lastHoofdcategorieOmschrijving)
        $this->printKop($data['hoofdcategorieOmschrijving'],'BI',true);
      if($data['categorieOmschrijving'] != $lastCategorieOmschrijving)
        $this->printKop($data['categorieOmschrijving'],'B',false);

			$resultaat = $data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'];
//			$procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro']) / ($data['beginPortefeuilleWaardeEuro'] /100));
      $procentResultaat = (($data['actuelePortefeuilleWaardeEuro'] - $data['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($data['beginPortefeuilleWaardeEuro'] /100));		
    	if($data['beginPortefeuilleWaardeEuro'] < 0)
				$procentResultaat = -1 * $procentResultaat;
			$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
				$procentResultaattxt = "p.m.";
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

			$resultaattxt = "";
 			if($resultaat <> 0)
				$resultaattxt = $this->formatGetal($resultaat,$this->pdf->rapport_VOLK_decimaal);

			$this->pdf->row(array("  ".$data['fondsOmschrijving'],
				                $this->formatAantal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$data['Valutateken'],
												$this->formatGetal($data['actueleFonds'],2),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$this->formatGetal($data['beginwaardeLopendeJaar'],2),
												$this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												"",
												$resultaattxt,
                        $this->formatGetal($dividend['totaal'],0),
												$procentResultaattxt,
												$this->formatGetal($data['historischeWaarde'],2))	);

			$this->pdf->excelData[]=array($data['categorieOmschrijving'],$data['fondsOmschrijving'],$data['ISINCode'],$data['totaalAantal'],$data['valuta'],$data['actueleFonds'],
				$data['actuelePortefeuilleWaardeEuro'],$percentageVanTotaal,$data['beginwaardeLopendeJaar'],$data['beginPortefeuilleWaardeEuro'],
				$resultaat,$dividend['totaal'],$procentResultaat,$data['historischeWaarde'],$perf,$this->portefeuille,$totaalWaarde,$perfStart);

			$categorieTotaal[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$categorieTotaal[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $categorieTotaal[$data['categorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
		$hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividend'] +=$dividend['totaal'];
    $hoofdcategorieTotaal[$data['hoofdcategorieOmschrijving']]['dividendCorrected'] +=$dividend['corrected'];

    $lastHoofdcategorieOmschrijving=$data['hoofdcategorieOmschrijving'];
    $lastCategorieOmschrijving=$data['categorieOmschrijving'];
		}
		$this->printSubTotaal($lastCategorieOmschrijving,$categorieTotaal,$totaalWaarde);

/*
		// selecteer rente
		$query = "SELECT Valutas.Valutateken,TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.fondsOmschrijving, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
		" TijdelijkeRapportage.rentedatum, ".
		" TijdelijkeRapportage.renteperiode, ".
		" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
		" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		"  ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
*/

		//if($DB->records() > 0)
		if(count($renteData) > 0)
		{
		  $this->printKop(vertaalTekst("Opgelopen rente en coupondatum",$this->pdf->rapport_taal), "B");
			$totaalRenteInValuta = 0 ;
			//while($data = $DB->NextRecord())
			foreach ($renteData as $data)
			{
			  	$rentePeriodetxt = "  ".date("d-m",db2jul($data['rentedatum']));
					if($subdata['renteperiode'] <> 12 && $data['renteperiode'] <> 0)
						$rentePeriodetxt .= " / ".$data['renteperiode'];

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->Cell($this->pdf->widths[0],4,"  ".$data['fondsOmschrijving'].' '.$rentePeriodetxt, 0,0, "L");
					$this->pdf->setX($this->pdf->marge);
					$this->pdf->row(array('','',
					              $data['Valutateken'],'',
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)));

				$this->pdf->excelData[]=array('Opgelopen rente','Opgelopen rente','',$data['totaalAantal'],$data['valuta'],$data['actueleFonds'],
					$data['actuelePortefeuilleWaardeEuro'],$percentageVanTotaal,$data['beginwaardeLopendeJaar'],$data['beginPortefeuilleWaardeEuro'],
					$resultaat,$dividend['totaal'],$procentResultaat,$data['historischeWaarde'],$perf,$this->portefeuille,$totaalWaarde,$perfStart);
					$categorieTotaal["Opgelopen Rente"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Opgelopen Rente",$categorieTotaal,$totaalWaarde);
		}


/*
		// Liquiditeiten
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening as zoekRekening, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" (SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = zoekRekening AND type='rekening'  LIMIT 1)  / ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);


		$DB1->SQL($query);
		$DB1->Query();
*/
		//if($DB1->records() > 0)
		if(count($liquiditeitenData) > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"B");
			/*
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}
			foreach($liqiteitenBuffer as $data)
      */
					$DB1 = new DB();
			foreach($liquiditeitenData as $data)
			{

			  if($data['beginPortefeuilleWaardeEuro'] == 0)
			  {
			    $query="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE portefeuille = '".$this->portefeuille."' AND rapportageDatum = '".$this->rapportageDatumVanaf."' AND rekening = '".$data['rekening']."' AND type='rekening' LIMIT 1";
    		  $DB1->SQL($query);
		      $DB1->Query();
		      $begin = $DB1->NextRecord();
		      $data['beginPortefeuilleWaardeEuro']=$begin['actuelePortefeuilleWaardeEuro'];
		    }

					$percentageVanTotaal = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
					$this->pdf->row(array("  ".$data['fondsOmschrijving'].' '.$data['rekening'],
                            ($data['totaalAantal']<>0?$this->formatAantal($data['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal):''),
                            $data['Valutateken'],'',
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),'','',
												$this->formatGetal($data['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal)

												));
				$this->pdf->excelData[]=array('Liquiditeiten',$data['fondsOmschrijving'],'',$data['totaalAantal'],$data['valuta'],'',
					$data['actuelePortefeuilleWaardeEuro'],$percentageVanTotaal,'',
					$data['beginPortefeuilleWaardeEuro'],'','','','',$perf,$this->portefeuille,$totaalWaarde,$perfStart);
					$categorieTotaal["Liquiditeiten"]['actuelePortefeuilleWaardeEuro'] +=$data['actuelePortefeuilleWaardeEuro'];
					$categorieTotaal["Liquiditeiten"]['beginPortefeuilleWaardeEuro'] +=$data['beginPortefeuilleWaardeEuro'];
			}
			$this->printSubTotaal("Liquiditeiten",$categorieTotaal,$totaalWaarde,false);
		} // einde liquide

		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}

		$this->pdf->CellBorders = array('','','','','SUB','SUB');
		$this->pdf->row(array("Totale actuele waarde portefeuille",'','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_VOLK_decimaal),
		$this->formatGetal(($actueleWaardePortefeuille/$totaalWaarde*100),$this->pdf->rapport_VOLK_decimaal_proc)));
    $this->pdf->CellBorders = array();
		$this->pdf->ln();



		if($this->pdf->rapport_VOLK_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;


		//if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
	//	{
	//		$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
	//	}
		//elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
	//	{
		if(function_exists('printValutaPerformanceOverzicht'))
			printValutaPerformanceOverzicht($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,true);
	//	}
    
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
    //$this->pdf->rapport_fontsize+=1;
	}

	function autorun($selectData,$settings)
	{
		global $__appvar;
		include_once('../../../classes/excel/Writer.php');
		$selectie= new portefeuilleSelectie($selectData);
		$portefeuilles=$selectie->getSelectie();
		$this->pdf->ValutaKoersBegin=1;
		$this->pdf->ValutaKoersEind=1;
		$this->pdf->extra='xls';
		$startBackup=$this->rapportageDatumVanaf;
		$eindBackup=$this->rapportageDatum;
		$headerBackup=$this->pdf->excelData;

    foreach($portefeuilles as $portefeuille=>$pdata)
		{
			$this->pdf->excelData=$headerBackup;
			if($pdata['RapportageValuta']=='')
        $pdata['RapportageValuta']='EUR';
			$this->pdf->rapportageValuta=$pdata['RapportageValuta'];
			$this->rapportageDatumVanaf=$startBackup;
			$this->rapportageDatum=$eindBackup;
			if(db2jul($this->rapportageDatumVanaf) < db2jul($pdata['Startdatum']))
				$this->rapportageDatumVanaf = substr($pdata['Startdatum'],0,10);

			loadLayoutSettings($this->pdf, $portefeuille);

			logIt('autorun VOLK_L64: '.$portefeuille." over ".$this->rapportageDatumVanaf."->".$this->rapportageDatum." met ".$this->pdf->portefeuilledata['PerformanceBerekening']." in ".$this->pdf->rapportageValuta);

			$this->portefeuille = $portefeuille;
			if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
				$startjaar=true;
			else
				$startjaar=false;

  		$fondswaarden = berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatumVanaf,$startjaar,'EUR',$this->rapportageDatumVanaf);
	  	vulTijdelijkeTabel($fondswaarden ,$portefeuille,$this->rapportageDatumVanaf);
			$fondswaarden = berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,false,'EUR',$this->rapportageDatumVanaf);
			vulTijdelijkeTabel($fondswaarden ,$portefeuille,$this->rapportageDatum);
			$this->writeRapport();

			if(is_dir($settings['Export_pad']))
				$path = $settings['Export_pad'];
			else
				$path = $__appvar['tempdir'];

	 	  $this->pdf->OutputXls($path.'/'.$portefeuille.'.xls');
			logIt('autorun VOLK_L64: '.$path.'/'.$portefeuille.'.xls');
      //echo $path.'/'.$portefeuille.'.xls';
		}
	}
}
?>