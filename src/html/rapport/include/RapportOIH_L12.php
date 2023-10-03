<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/rapportATTberekening_L12.php");

class RapportOIH_L12
{
	function RapportOIH_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_deel = 'overzicht';
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->RapStartJaar = date("Y", $this->pdf->rapport_datumvanaf);
    
    if($this->RapStartJaar <> date("Y", $this->pdf->rapport_datum))
    {
      echo "Begin en einddatum moeten in hetzelfde jaar liggen.";
      exit;
    }

	  $this->pdf->rapport_titel = "Resultaat per instrument";
    
    
    
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    
    $this->pdf->rapport_header1 = array("\n     \n ","Aantal\n \n ","Koers\n \n ","Waarde\nin ".($this->pdf->rapportageValuta=='EUR'?'euro':$this->pdf->rapportageValuta)."\n".
      date("j",($this->pdf->rapport_datumvanaf))." ".vertaalTekst($maanden[date("n",($this->pdf->rapport_datumvanaf))],$this->pdf->taal)." ".date("Y",($this->pdf->rapport_datumvanaf)),
		"Stortingen/\n onttrekkingen\n ","Resultaat\nverslag-\nperiode","Waarde\nin ".($this->pdf->rapportageValuta=='EUR'?'euro':$this->pdf->rapportageValuta)."\n".
      date("j",($this->pdf->rapport_datum))." ".vertaalTekst($maanden[date("n",($this->pdf->rapport_datum))],$this->pdf->taal)." ".date("Y",($this->pdf->rapport_datum)),
		"Rendement\nverslag-\nperiode","Resultaat\nlopend\njaar","Rendement\nlopend\njaar");

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->perioden['start'] = $this->rapportageDatumVanaf;
		$this->perioden['eind'] = $this->rapportageDatum;

		$this->pdf->underlinePercentage = .7;
		$this->checkValues=false;
    
    $this->pdf->excelData[]=array("Sector",'Categorie','Fonds','Aantal','Koers',"Waarde in ".($this->pdf->rapportageValuta=='EUR'?'euro':$this->pdf->rapportageValuta)." ".date("d-m-y",$this->pdf->rapport_datumvanaf),
      "Stortingen/onttrekkingen","Resultaat verslagperiode","Waarde in ".($this->pdf->rapportageValuta=='EUR'?'euro':$this->pdf->rapportageValuta)." ".date("d-m-y",$this->pdf->rapport_datum),
      "Rendement verslagperiode","Resultaat lopend jaar","Rendement lopend jaar");
    
  }

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
	  {
	    $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
	  }
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}

	function formatGetal($waarde, $dec, $percent = false,$limit = false)
	{
	  if($waarde == '')
	    return '';
	  if(round($waarde,2) != 0.00 || $percent==true)
	  {
	    if($percent == true)
	    {
	      if($limit)
	      {
	        if($waarde >= $limit || $waarde <= $limit * -1)
	          return "p.m.";
	      }
	      return number_format($waarde,$dec,",",".").'%';
	    }

		  else
		    return number_format($waarde,$dec,",",".");
	  }
	}

	function formatGetalKoers($waarde, $dec, $percent = false, $limit = false , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;
	  }
	  return $this->formatGetal($waarde, $dec, $percent = false,$limit = false);
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
	       //  echo $this->portefeuille." $waarde <br>";exit;
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

	function writeRapport()
	{
		global $__appvar;
		$this->tweedeStart();

	  if ($this->rapportageDatumVanaf != $this->tweedePerformanceStart)
	  {
	    if(substr($this->tweedePerformanceStart,4,6)=='-01-01')
	     $startJaar = 1;
      $fondswaarden['c'] =  berekenPortefeuilleWaarde($this->portefeuille, $this->tweedePerformanceStart,$startJaar,$this->pdf->rapportageValuta);
	    vulTijdelijkeTabel($fondswaarden['c'] ,$this->portefeuille,$this->tweedePerformanceStart);

	    $this->perioden['jan'] = $this->tweedePerformanceStart;
	  }
	  else
	   $this->perioden['jan']=$this->rapportageDatumVanaf;

	 	if($this->perioden['jan'])
	    $start = $this->perioden['jan'];
	  else
	    $start = $this->perioden['start'];

	  $vuldata=$this->getMaanden(db2jul($this->perioden['jan']),$this->pdf->rapport_datum);
	  foreach ($vuldata as $periode)
	  {
	    if($periode['stop'] != $this->perioden['jan'] && $periode['stop'] != $this->perioden['start'] && $periode['stop'] != $this->perioden['eind'])
	    {
	      if(substr($periode['stop'],5,5)=='01-01')
          $startJaar=1;
        else
          $startJaar=0;  

	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $periode['stop'],$startJaar,$this->pdf->rapportageValuta,$periode['start']);
	      vulTijdelijkeTabel($fondswaarden,$this->portefeuille, $periode['stop']);
	    }
	  }

	  //$index=new indexHerberekening();
    //$rendamentWaardenPeriode = $index->getWaardenATT($this->perioden['start'] ,$this->perioden['eind'] ,$this->portefeuille,'Totaal','maand',$this->pdf->rapportageValuta);
    //$rendamentWaardenJaar = $index->getWaardenATT($this->perioden['jan'] ,$this->perioden['eind'] ,$this->portefeuille,'Totaal','maand',$this->pdf->rapportageValuta);
    //foreach ($rendamentWaardenPeriode as $rendamentWaarden)
    //  $attributiePerfWaarden['periode']=$rendamentWaarden['totaal']-100;
    //foreach ($rendamentWaardenJaar as $rendamentWaarden)
    //  $attributiePerfWaarden['jaar']=$rendamentWaarden['index']-100;
    
    $this->berekening = new rapportATTberekening_L12($this);//rapportATTberekening_L12($this->pdata);
    $rendamentWaardenPeriode=$this->berekening->bereken($this->perioden['start'],$this->perioden['eind'],'attributie',$this->pdf->rapportageValuta);
    $rendamentWaardenJaar=$this->berekening->bereken($this->perioden['jan'] ,$this->perioden['eind'],'attributie',$this->pdf->rapportageValuta);
    $attributiePerfWaarden['periode']=$rendamentWaardenPeriode['totaal']['procent'];
    $attributiePerfWaarden['jaar']=$rendamentWaardenJaar['totaal']['procent'];

    //listarray($gewogenResultaat);exit;

	 	$DB = new DB();
	 	$query = "SELECT Beleggingscategorie,Omschrijving FROM Beleggingscategorien ";
	 	$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		  $categorieOmschrijving[$data['Beleggingscategorie']] = $data['Omschrijving'];

		$query = "SELECT Beleggingssector,Omschrijving FROM Beleggingssectoren ";
	 	$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		   $categorieOmschrijving[$data['Beleggingssector']] = $data['Omschrijving'];

//		$categorieOmschrijving['LIQUIDITEITEN'] = 'Liquiditeiten';
//		$categorieOmschrijving['Liquiditeiten'] = 'Rekeningen';
		$categorieOmschrijving['geen seq'] = 'Geen sector';
		$categorieOmschrijving['geen cat'] = 'Geen categorie';



$mutaties['periode']=$this->genereerMutatieLijst($this->rapportageDatumVanaf,$this->rapportageDatum);
$mutaties['jaar']=$this->genereerMutatieLijst($start,$this->rapportageDatum);
$this->waarden = array();
// Bepaal juiste afdrukvolgorde.
$datum = "IN('".implode("','",$this->perioden)."')";
$query = "SELECT TijdelijkeRapportage.fondsOmschrijving,
				       TijdelijkeRapportage.fonds,
		           TijdelijkeRapportage.beleggingssector,
		           TijdelijkeRapportage.beleggingscategorie,
				       TijdelijkeRapportage.AttributieCategorie
				       FROM TijdelijkeRapportage
				       WHERE
				       TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
				       TijdelijkeRapportage.type IN('fondsen','rente')  AND TijdelijkeRapportage.Fonds <> '' AND
				       TijdelijkeRapportage.rapportageDatum $datum
				       ".$__appvar['TijdelijkeRapportageMaakUniek']."
				       GROUP BY TijdelijkeRapportage.fonds
				       ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
{
  $this->fondsenPerCategorie[$data['AttributieCategorie']][$data['fonds']]=$data['fonds'];
  $this->fondsenPerCategorie[$data['beleggingscategorie']][$data['fonds']]=$data['fonds'];
  $this->fondsenPerCategorie[$data['beleggingssector']][$data['fonds']]=$data['fonds'];

  $this->fondsenTree[$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$data['fonds']]=$data['fonds'];

  foreach ($this->perioden as $periode)
  {
    $this->waarden[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$data['fonds']]= $data;
    $superCategorieKoppeling[$data['AttributieCategorie']][$data['beleggingscategorie']]=$data['beleggingscategorie'];
		$hoofdcategorieKoppeling[$data['beleggingscategorie']][$data['beleggingssector']]=$data['beleggingssector'];
  }
}

//listarray($this->fondsenPerCategorie);

foreach ($this->perioden as $periode)
{
  $query="SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <='".$periode."' order by Datum desc limit 1";
  $DB->SQL($query);
  $koers=$DB->lookupRecord();
  $this->rapportageKoersen[$periode]=$koers['Koers'];
}
	  $rekeningen = array();
	  $periodeDone=array();


	  $rapportageDatumvelden=" TijdelijkeRapportage.rapportageDatum  IN('".implode("','",$this->perioden)."')";

	  foreach ($this->perioden as $periode)
	  {
	    if(!in_array($periode,$periodeDone))
	    {
	    $periodeDone[]=$periode;
	    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.rapportageDatum,
				       TijdelijkeRapportage.fonds,
				       TijdelijkeRapportage.actueleValuta,
				       TijdelijkeRapportage.totaalAantal,
				       TijdelijkeRapportage.Valuta,
		           TijdelijkeRapportage.beleggingssector,
		           TijdelijkeRapportage.beleggingscategorie,
				       TijdelijkeRapportage.actueleFonds,
               Fondsen.forward,
				       SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as actuelePortefeuilleWaardeInValuta,
				       SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->rapportageKoersen[$periode]."  as actuelePortefeuilleWaardeEuro ,
				       TijdelijkeRapportage.portefeuille,
				       TijdelijkeRapportage.AttributieCategorie,
				        TijdelijkeRapportage.type
				       FROM TijdelijkeRapportage
               LEFT JOIN Fondsen ON (TijdelijkeRapportage.fonds = Fondsen.Fonds)
				       WHERE
				       TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
				       TijdelijkeRapportage.type IN('fondsen','rente')  AND TijdelijkeRapportage.Fonds <> '' AND
				       TijdelijkeRapportage.rapportageDatum = '$periode'
				       ".$__appvar['TijdelijkeRapportageMaakUniek']."
				       GROUP BY TijdelijkeRapportage.fonds
				       ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
      	       $DB->SQL($query);
		           $DB->Query();
		           while($data = $DB->NextRecord())
		           {
		            if($data['beleggingssector'] == '')
		               $data['beleggingssector']='geen seq';
		            if($data['beleggingscategorie'] == '')
		              $data['beleggingscategorie'] = 'geen cat';
		            if($data['AttributieCategorie'] =='')
		              $data['AttributieCategorie']='geen att';

                $superCategorieKoppeling[$data['AttributieCategorie']][$data['beleggingscategorie']]=$data['beleggingscategorie'];
		            $hoofdcategorieKoppeling[$data['beleggingscategorie']][$data['beleggingssector']]=$data['beleggingssector'];

		            $this->waarden[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$data['fonds']]= $data;
		            $this->fondsOmschrijvingen[$data['fonds']]=$data['fondsOmschrijving'];
                $this->fondsForward[$data['fonds']]=$data['forward'];
		            $sectorTotalen[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
		            $sectorTotalen[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];

		            $superTotalen[$periode][$data['AttributieCategorie']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
		            $superTotalen[$periode][$data['AttributieCategorie']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];

		            $periodeTotalen[$periode] += $data['actuelePortefeuilleWaardeEuro'];
		           }

	  }
	  }

    $periodeDone=array();
	  foreach ($this->perioden as $periode)
	  {
	   	if(!in_array($periode,$periodeDone))
	    {
	    $periodeDone[]=$periode;
		// Liquiditeiten
		$query = "SELECT (TijdelijkeRapportage.fondsOmschrijving) as fondsOmschrijving,  TijdelijkeRapportage.rapportageDatum, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening ,
	  		TijdelijkeRapportage.beleggingscategorie,
	  		 TijdelijkeRapportage.beleggingssector,".
			" sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as actuelePortefeuilleWaardeInValuta , ".
			" sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro ) / ".$this->rapportageKoersen[$periode]." as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, Rekeningen.Beleggingscategorie as beleggingscategorie, TijdelijkeRapportage.AttributieCategorie".
			" FROM TijdelijkeRapportage
			  LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening = Rekeningen.Rekening  AND Rekeningen.portefeuille = '".$this->portefeuille."'
			  WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND   TijdelijkeRapportage.Fonds = '' AND ".
			" TijdelijkeRapportage.type IN('rekening','rente') AND ".
			" TijdelijkeRapportage.rapportageDatum = '$periode' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.rapportageDatum,TijdelijkeRapportage.rekening
			ORDER BY SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3), TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.fondsOmschrijving";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		if($DB1->records() > 0)
		{
		  while($data = $DB1->NextRecord())
			{
			  $periode=$data['rapportageDatum'];
         $rekeningen[$data['rekening']] = $data['rekening'];
         if($data['beleggingscategorie'] == '')
  			    $data['beleggingscategorie'] ='Liquiditeiten';
  			 if($data['AttributieCategorie'] =='')
  			   $data['AttributieCategorie']='Liquiditeiten';
  			 if($data['beleggingssector'] =='')
  			   $data['beleggingssector']='Liquiditeiten';
        
         $data['fonds']=$data['rekening'];

         $this->waarden[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$data['rekening']]= $data;
	       $this->fondsenPerCategorie[$data['beleggingscategorie']][$data['rekening']]=$data['rekening'];
         $this->fondsenPerCategorie[$data['AttributieCategorie']][$data['rekening']]=$data['rekening'];
	       $this->fondsenTree[$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']][$data['rekening']]=$data['rekening'];
	       
	       $this->fondsOmschrijvingen[$data['rekening']]=$data['fondsOmschrijving']." ".$data['rekening'];
	       $categorieTotalen[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
	       $categorieTotalen[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
	       $superTotalen[$periode][$data['AttributieCategorie']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
		     $superTotalen[$periode][$data['AttributieCategorie']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];

		     $sectorTotalen[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
		     $sectorTotalen[$periode][$data['AttributieCategorie']][$data['beleggingscategorie']][$data['beleggingssector']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];

	       $periodeTotalen[$periode] += $data['actuelePortefeuilleWaardeEuro'];
	       $this->toon['hoofdcategorie']['Liquiditeiten']=1;
         $this->toon['categorie'][$data['beleggingscategorie']]=1;

        // if(!in_array($data['beleggingssector'],$hoofdcategorieKoppeling[$data['beleggingscategorie']]))
		     $hoofdcategorieKoppeling[$data['beleggingscategorie']][$data['beleggingssector']]=$data['beleggingssector'];
	       $superCategorieKoppeling[$data['AttributieCategorie']][$data['beleggingscategorie']]=$data['beleggingscategorie'];
			}
      }
		}
    }

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

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

		$actueleWaardePortefeuille = 0;
    $fondsMutaties=$mutaties['jaar'];


foreach ($rekeningen as $rekening)
{
  $mutaties['periode'][$rekening][0]=$this->getRekeningMutaties($rekening,$this->rapportageDatumVanaf,$this->rapportageDatum);
  $mutaties['jaar'][$rekening][0]=$this->getRekeningMutaties($rekening,$start,$this->rapportageDatum);
}


$periodeDone=array();
foreach ($this->waarden as $periode=>$data)
{
  foreach ($fondsMutaties as $fonds=>$waarden)
  {
     $this->toon['fonds'][$waarden[7]][$waarden[6]][$waarden[5]][$fonds]=1;
     if(!isset($this->waarden[$periode][$waarden[7]][$waarden[6]][$waarden[5]][$fonds]))
     {
       $this->waarden[$periode][$waarden[7]][$waarden[6]][$waarden[5]][$fonds]['fonds']=$fonds;
       $this->fondsenPerCategorie[$waarden[5]][$fonds]=$fonds;
       $this->fondsenPerCategorie[$waarden[6]][$fonds]=$fonds;
       $this->fondsenPerCategorie[$waarden[7]][$fonds]=$fonds;
       $hoofdcategorieKoppeling[$waarden[6]][$waarden[5]]=$waarden[5];
	     $superCategorieKoppeling[$waarden[7]][$waarden[6]]=$waarden[6];

       $this->fondsenTree[$waarden[7]][$waarden[6]][$waarden[5]][$fonds]=$fonds;
       $query="SELECT Fonds,Omschrijving,forward FROM Fondsen WHERE Fonds='$fonds'";
       $DB->SQL($query);
       $fondsOmschrijving=$DB->lookupRecord();
       $this->fondsOmschrijvingen[$fonds]=$fondsOmschrijving['Omschrijving'];
       $this->fondsForward[$fonds]=$fondsOmschrijving['forward'];
     }
		$hoofdcategorieKoppeling[$waarden[6]][$waarden[5]]=$waarden[5];
		$fondsmutatiesPerCategorie[$waarden[5]][$fonds]=$fonds;
  }
}

		$hoofdcategorieTotalen=array();
		$supercategorieTotalen=array();
		$totalen=array();
foreach ($this->waarden as $periode=>$superCategorien)
{
  foreach ($superCategorien as $supercategorie=>$hoofdcategorien)
  {
    foreach ($hoofdcategorien as $hoofdcategorie=>$categorien)
    {
      foreach ($categorien as $categorie=>$fondsData)
      {
	      foreach ($fondsData as $fonds=>$waarden)
        {
          if(!in_array($periode,$periodeDone))
          {
            $hoofdcategorieTotalen[$periode][$hoofdcategorie]['actuelePortefeuilleWaardeEuro']     += $waarden['actuelePortefeuilleWaardeEuro'];
            $supercategorieTotalen[$periode][$supercategorie]['actuelePortefeuilleWaardeEuro']     += $waarden['actuelePortefeuilleWaardeEuro'];
            $totalen[$periode]['actuelePortefeuilleWaardeEuro']     += $waarden['actuelePortefeuilleWaardeEuro'];
         }
         $portefeuilleFondsen[$fonds] = $fonds;
         if(is_array($fondsMutaties[$fonds]))
         {
            $this->toon['hoofdcategorie'][$hoofdcategorie]=1;
            $this->toon['categorie'][$categorie]=1;
            $this->toon['fonds'][$supercategorie][$hoofdcategorie][$categorie][$fonds]=1;
         }
       }
     }
   }
 }
  $periodeDone[]=$periode;
}

	if(isset($this->fondsenTree['Liquiditeiten']['Liquiditeiten']['Liquiditeiten']))
	{
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum IN('".implode("','",$this->perioden)."') "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3), TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.fondsOmschrijving";
		$tmp=array();
		$DB1->SQL($query);
		$DB1->Query();
		while($data = $DB1->NextRecord())
		{
			$tmp[$data['rekening']]=$data['rekening'];
		}
// uitzet ivp dubbele rekingen wanneer niet aan Liq gekoppeld.
//		$this->fondsenTree['Liquiditeiten']['Liquiditeiten']['Liquiditeiten']=$tmp;
	}

if ($this->rapportageDatumVanaf == $this->tweedePerformanceStart)
{
  $this->perioden['jan'] = $this->rapportageDatumVanaf;
}

foreach ($superCategorieKoppeling as $supercategorie=>$hoofdcategorien)
{
  foreach ($hoofdcategorien as $hoofdcategorie)
  {
  //echo "$supercategorie $hoofdcategorie <br>\n ";ob_flush();listarray($hoofdcategorieKoppeling[$hoofdcategorie]);
       if($hoofdcategorieTotalen[$this->perioden['start']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] <> 0 || $hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] <>0 || $this->toon['hoofdcategorie'][$hoofdcategorie]==1)//|| $hoofdcategorieTotalen[$this->perioden['jan']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] <>0
       {
         $regels['overzicht'][] = array('kopHoofd',array('omschrijving'=>$categorieOmschrijving[$hoofdcategorie]));
         foreach ($hoofdcategorieKoppeling[$hoofdcategorie] as $categorie)
         {
             if($sectorTotalen[$this->perioden['start']][$supercategorie][$hoofdcategorie][$categorie]['actuelePortefeuilleWaardeEuro'] <>0 || $sectorTotalen[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie]['actuelePortefeuilleWaardeEuro'] <>0 || $this->toon['categorie'][$categorie]==1)//|| $categorieTotalen[$this->perioden['jan']][$categorie]['actuelePortefeuilleWaardeEuro'] <>0
             {
             	 if($categorie<>'Liquiditeiten')
                 $regels['overzicht'][] = array('kopCategorie',array('omschrijving'=>($categorieOmschrijving[$categorie]<>''?$categorieOmschrijving[$categorie]:$categorie)));
               foreach ($this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie] as $fonds)//($this->fondsenPerCategorie[$categorie] as $fonds)
		           {
		             $totaalMutaties  = $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] - $this->waarden[$this->perioden['start']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'];
		             $totaalMutatiesJaar  = $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] - $this->waarden[$this->perioden['jan']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'];
                 //echo "$supercategorie $hoofdcategorie $categorie $fonds $totaalMutaties<br>\n";ob_flush();
  		           $directeKostenOpbrengsten = $this->fondsKostenOpbrengsten($fonds,$this->perioden['start'],$this->perioden['eind']);
		             $stortingenontrekkingen = $mutaties['periode'][$fonds][0]-$directeKostenOpbrengsten;
		             $beleggingsresultaat = $totaalMutaties - $stortingenontrekkingen ;
		             //echo "$fonds $beleggingsresultaat = $totaalMutaties - $stortingenontrekkingen ;<br>\n";

  	             $bijdrageResultaat = $beleggingsresultaat / ($supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'])*100;
  	             $directeKostenOpbrengstenJaar = $this->fondsKostenOpbrengsten($fonds,$this->perioden['jan'],$this->perioden['eind']);
		             $stortingenontrekkingenJaar = $mutaties['jaar'][$fonds][0]-$directeKostenOpbrengstenJaar;

		             $beleggingsresultaatJaar = $totaalMutatiesJaar -$stortingenontrekkingenJaar;
		             
		             if($categorie == 'Liquiditeiten' || $categorie=='Spaarrekeningen')
		             {
		               $gewogenResultaatJaar='';
		               $gewogenResultaat='';
		             }
		             else
		             {
                   //$gewogenResultaatJaar = $this->fondsPerformance2($fonds,$this->perioden['jan'],$this->perioden['eind']);
                   //$gewogenResultaat = $this->fondsPerformance2($fonds,$this->perioden['start'],$this->perioden['eind']);
                   
                   $gewogenResultaatJaar = $this->berekening->fondsPerformance(array('fondsen'=>array($fonds)),$this->perioden['jan'],$this->perioden['eind'],true,$fonds,$this->pdf->rapportageValuta);
                   $gewogenResultaat = $this->berekening->fondsPerformance(array('fondsen'=>array($fonds)),$this->perioden['start'],$this->perioden['eind'],true,$fonds,$this->pdf->rapportageValuta);
                   $gewogenResultaatJaar=$gewogenResultaatJaar['procent'];
                   $gewogenResultaat=$gewogenResultaat['procent'];
		             }
                 
                 if($this->fondsForward[$fonds]==1)
                 {
             		   $gewogenResultaatJaar='';
		               $gewogenResultaat='';     
                 }
                 

		             $bijdrageResultaatJaar = $beleggingsresultaat / ($supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['jan']][$supercategorie]['actuelePortefeuilleWaardeEuro'])*100;

               $rekeningRente = 0;
               $rekeningRenteJaar = 0;

               	 $categorienBijdrageResultaat[$hoofdcategorie][$categorie] += $bijdrageResultaat;
		             $categorienStortingenontrekkingen[$hoofdcategorie][$categorie] += $stortingenontrekkingen;
		             $categorienBeleggingsresultaat[$hoofdcategorie][$categorie] += $beleggingsresultaat;
		             $categorienStortingenontrekkingenJaar[$hoofdcategorie][$categorie] += $stortingenontrekkingenJaar;
		             $categorienBeleggingsresultaatJaar[$hoofdcategorie][$categorie] += $beleggingsresultaatJaar;
//hoofdcategorie
		             $hoofdcategorienBijdrageResultaat[$hoofdcategorie] += $bijdrageResultaat;
		             $hoofdcategorienStortingenontrekkingen[$hoofdcategorie] += $stortingenontrekkingen;
		             $hoofdcategorienBeleggingsresultaat[$hoofdcategorie] += $beleggingsresultaat;
		             $hoofdcategorienStortingenontrekkingenJaar[$hoofdcategorie] += $stortingenontrekkingenJaar;
		             $hoofdcategorienBeleggingsresultaatJaar[$hoofdcategorie] += $beleggingsresultaatJaar;
//supercategorie
		             $supercategorienBijdrageResultaat += $bijdrageResultaat;
		             $supercategorienStortingenontrekkingen += $stortingenontrekkingen;
		             $supercategorienBeleggingsresultaat += $beleggingsresultaat;
		             $supercategorienStortingenontrekkingenJaar += $stortingenontrekkingenJaar;
		             $supercategorienBeleggingsresultaatJaar += $beleggingsresultaatJaar;


		           if($this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] <> 0 ||
                  $this->waarden[$this->perioden['start']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] <> 0 ||
                  $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['totaalAantal'] <> 0 ||
                  $this->toon['fonds'][$supercategorie][$hoofdcategorie][$categorie][$fonds])
               {
                 $regels['overzicht'][] = array('fonds',
                   array('omschrijving'                      => $this->fondsOmschrijvingen[$fonds],
                         'totaalAantal'                      => $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['totaalAantal'],
                         'actueleFonds'                      => $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actueleFonds'],
                         'startWaardeVal'                    => $this->waarden[$this->perioden['start']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeInValuta'],
                         'startWaardeEur'                    => $this->waarden[$this->perioden['start']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'],
                         'stort'                             => $stortingenontrekkingen,
                         'actuelePortefeuilleWaardeInValuta' => $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeInValuta'],
                         'actuelePortefeuilleWaardeEuro'     => $this->waarden[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'],
                         'supercategorie'                    => $supercategorie,
                         'hoofdcategorie'                    => $hoofdcategorie,
                         'categorie'                         => $categorie,
                         'resultaat'                         => $gewogenResultaat,
                         'resultaatJaar'                     => $gewogenResultaatJaar,
                         'resultaatAbsoluut'                 => $beleggingsresultaat,
                         'resultaatAbsoluutJaar'             => $beleggingsresultaatJaar
                   ));
               }
		           else
							 {
							 	//listarray($fonds);
								 
							 }


		           }


		           	 if($categorie == 'Liquiditeiten'||$categorie == 'Spaarrekeningen')
		             {
		               $gewogenResultaatJaar='';
		               $gewogenResultaat='';
		             }
		             else
		             {


                   //$gewogenResultaatJaar = $this->fondsPerformance2($this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie],$this->perioden['jan'],$this->perioden['eind']);
                   //$gewogenResultaat = $this->fondsPerformance2($this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie],$this->perioden['start'],$this->perioden['eind']);
      
                   $gewogenResultaatJaar = $this->berekening->fondsPerformance(array('fondsen'=>$this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie],
																																										 'rekeningen'=>$this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie]),
																																							 $this->perioden['jan'],$this->perioden['eind'],
																																							 true,
                                                                               $supercategorie.'_'.$hoofdcategorie.'_'.$categorie,
																																							 $this->pdf->rapportageValuta);
                   $gewogenResultaat = $this->berekening->fondsPerformance(array('fondsen'=>$this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie],
																																								 'rekeningen'=>$this->fondsenTree[$supercategorie][$hoofdcategorie][$categorie]),
																																					 $this->perioden['start'],$this->perioden['eind'],
																																					 true,
                                                                           $supercategorie.'_'.$hoofdcategorie.'_'.$categorie,
																																					 $this->pdf->rapportageValuta);
                   $gewogenResultaatJaar=$gewogenResultaatJaar['procent'];
                   $gewogenResultaat=$gewogenResultaat['procent'];
                   
		             }
		             //echo $categorie." ".$sectorTotalen[$this->perioden['start']][$hoofdcategorie][$categorie]['actuelePortefeuilleWaardeEuro']."<br>\n";
		           //$regels['overzicht'][] = array('categorieTotaal',array(''));
		              $regels['overzicht'][] = array('categorieTotaal',
		                              array('actuelePortefeuilleWaardeInValuta'=>'',//$categorieOmschrijving[$categorie]
		                                    'actuelePortefeuilleWaardeEuro'=>$sectorTotalen[$this->perioden['eind']][$supercategorie][$hoofdcategorie][$categorie]['actuelePortefeuilleWaardeEuro'],//$categorieTotalen[$this->perioden['eind']][$categorie]['actuelePortefeuilleWaardeEuro'],
		                                    'startWaardeEur'=>$sectorTotalen[$this->perioden['start']][$supercategorie][$hoofdcategorie][$categorie]['actuelePortefeuilleWaardeEuro'],
		                                    'resultaat'=>$gewogenResultaat,
		                                    'resultaatJaar'=>$gewogenResultaatJaar,
		                                    'resultaatAbsoluut'=>$categorienBeleggingsresultaat[$hoofdcategorie][$categorie],
		                                    'resultaatAbsoluutJaar'=>$categorienBeleggingsresultaatJaar[$hoofdcategorie][$categorie]));

		                                    //

	           }
           }

           $performance='';
           $performanceJaar='';
/*
           if($hoofdcategorie != 'LIQUIDITEITEN')
           {
              $performance = $this->fondsPerformance2($this->fondsenPerCategorie[$hoofdcategorie],$this->perioden['start'],$this->perioden['eind']);
              $performanceJaar = $this->fondsPerformance2($this->fondsenPerCategorie[$hoofdcategorie],$this->perioden['jan'],$this->perioden['eind'],true);
           }


   $regels['overzicht'][] = array('hoofdcategorieTotaal',
		                              array('actuelePortefeuilleWaardeInValuta'=>'Subtotaal '.$categorieOmschrijving[$hoofdcategorie],
		                                    'actuelePortefeuilleWaardeEuro'=>$hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'startWaardeEur'=>$hoofdcategorieTotalen[$this->perioden['start']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'resultaat'=>$performance,
		                                    'resultaatJaar'=>$performanceJaar));
*/
       }
   }

      $regels['overzicht'][] = array('',array());

      $performance='';$performanceJaar='';
      if($supercategorie != 'Liquiditeiten')
           {
           /*
$performance = $this->fondsPerformance2($this->fondsenPerCategorie[$supercategorie],$this->perioden['start'],$this->perioden['eind'],false);
$performanceJaar = $this->fondsPerformance2($this->fondsenPerCategorie[$supercategorie],$this->perioden['jan'],$this->perioden['eind'],false);
*/
             $gewogenResultaatJaar = $this->berekening->fondsPerformance(array('fondsen'=>$this->fondsenPerCategorie[$supercategorie],
																																							 'rekeningen'=>$this->fondsenPerCategorie[$supercategorie]),
																																				 $this->perioden['jan'],$this->perioden['eind'],
																																				 true,
                                                                         $supercategorie,
																																				 $this->pdf->rapportageValuta);
             $gewogenResultaat = $this->berekening->fondsPerformance(array('fondsen'=>$this->fondsenPerCategorie[$supercategorie],
																																					 'rekeningen'=>$this->fondsenPerCategorie[$supercategorie]),
																																		 $this->perioden['start'],$this->perioden['eind'],
																																		 true,
                                                                     $supercategorie,
																																		 $this->pdf->rapportageValuta);
             $performanceJaar=$gewogenResultaatJaar['procent'];
             $performance=$gewogenResultaat['procent'];

//echo $this->perioden['jan']." -> ".$this->perioden['eind']." | $supercategorie | $performanceJaar <br>\n"; listarray($this->fondsenPerCategorie[$supercategorie]);
           }
//echo $supercategorie."<br>\n";listarray($this->fondsenPerCategorie[$supercategorie]);ob_flush();

      $regels['overzicht'][] = array('hoofdcategorieTotaal',
		                              array('actuelePortefeuilleWaardeInValuta'=>'Subtotaal',//$supercategorie
		                                    'startWaardeEur'=>$supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'actuelePortefeuilleWaardeEuro'=>$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'resultaat'=>$performance,
		                                    'resultaatJaar'=>$performanceJaar,
		                                    '',
		                                    'janWaardeEur' =>$supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'mutatieWaarde'=>$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'] ));

		  $regelCount[] = count($regels['overzicht']);
}

/*
	    $attributiePerfWaarden['periode']=$rendamentWaarden['index']-100;
	  foreach ($rendamentWaardenJaar as $rendamentWaarden)
	    $attributiePerfWaarden['jaar']=$rendamentWaarden['index']-100;
	    */

    $regels['overzicht'][] = array('supercategorieTotaal',
		                              array('omschrijving'=>'Totaal',
		                                    'startWaardeEur'=>$totalen[$this->perioden['start']]['actuelePortefeuilleWaardeEuro'],
		                                    'actuelePortefeuilleWaardeEuro'=>$totalen[$this->perioden['eind']]['actuelePortefeuilleWaardeEuro'],
		                                    'resultaat'=>$attributiePerfWaarden['periode'],
		                                    'resultaatJaar'=>$attributiePerfWaarden['jaar'],
		                                    '',
		                                    'janWaardeEur' =>$totalen[$this->perioden['start']]['actuelePortefeuilleWaardeEuro'],
		                                    'mutatieWaarde'=>''));




//listarray($regels);
   $this->pdf->rapport_deel = 'overzicht';
   $this->pdf->rapport_titel = "Resultaat per instrument";
   unset($this->pdf->widthsDefault);
   $this->pdf->addPage();
    $this->pdf->SetLineWidth(0.2);
   $this->pdf->widthsDefault=$this->pdf->widths;
   foreach ($regels as $categorie=>$waarden)
   {
     $kopSuper = 0;
     foreach ($waarden as $data)
     {
       $this->pdf->widths=$this->pdf->widthsDefault;
       switch ($data[0])
       {
         case "kopHoofd":
					 $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
					 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           $this->pdf->line($this->pdf->marge,$this->pdf->getY()+$this->pdf->rowHeight,$this->pdf->marge+$this->pdf->widths[0],$this->pdf->getY()+$this->pdf->rowHeight,array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
         break;
         case "kopCategorie":
					 $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
					 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $data[1][0] = "  ".$data[1][0];
         break;
         case "fonds":
					 $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $data[1][0] = "  ".$data[1][0];
         break;
         case 'categorieTotaal':
           $this->pdf->widths[2]+=60;
           $this->pdf->widths[1]-=20;
           $this->pdf->widths[0]-=40;
           $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
					 $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
					 $this->pdf->CellBorders = array('','','',array('T','U'),'',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'));
					 $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
         break;
         case 'hoofdcategorieTotaal':
           $this->pdf->widths[2]+=60;
           $this->pdf->widths[1]-=20;
           $this->pdf->widths[0]-=40;
           $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
					 $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
         break;
         case 'supercategorieTotaal':
           $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
					 $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
         break;
       }

       switch ($categorie)
       {
         case "overzicht":
           if(substr($data[1]['omschrijving'],0,6) == 'Totaal')
           {
						 $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
           //  $this->pdf->CellBorders = array('','','','UU','','',"UU",'UU','','UU');
             $limit=0;
           }
           elseif($data[1]['regio'] == 'Subtotaal')
           {
             $limit=0;
           }
           else
           {

             $limit=1000;
           }
        //   echo "$categorie <br>\n";
           
           if($data[0]=='kopHoofd')
           {
             if($data[1]['omschrijving']=='Valuta-termijn-affaires')
             {
               $this->toonPM=true;
             }
             else
             {
              $this->toonPM=false;
             }
           } 
         //  listarray($data);
         
         if($this->toonPM==false)
         {
           $resultaat=$this->formatGetal($data[1]['resultaat'],2,true,$limit);
           $resultaatJaar=$this->formatGetal($data[1]['resultaatJaar'],2,true,$limit);
         }
         else
         {
           if(substr($data[1]['actuelePortefeuilleWaardeInValuta'],0,9)=='Subtotaal')
           {
             $resultaat='p.m.';
             $resultaatJaar='p.m.';
           }
           else
           {
            $resultaat='';
            $resultaatJaar='';
           }
         }
         

           if(substr($data[1]['actuelePortefeuilleWaardeInValuta'],0,9)=='Subtotaal')
           {
		         $this->pdf->row(array($data[1]['omschrijving'],
                                $this->formatGetal($data[1]['totaalAantal'],0),
                           $data[1]['actuelePortefeuilleWaardeInValuta'],
                           $this->formatGetal($data[1]['startWaardeEur'],0), //$this->formatGetalKoers($data[1]['startWaardeEur'],0,false,false,true),
                           $this->formatGetal($data[1]['stort'],0),
                           $this->formatGetal($data[1]['resultaatAbsoluut'],0),
                           $this->formatGetal($data[1]['actuelePortefeuilleWaardeEuro'],0),
                           $resultaat,
                           $this->formatGetal($data[1]['resultaatAbsoluutJaar'],0),
                           $resultaatJaar
                           ));
             $this->pdf->ln();
						 if(isset($this->pdf->CellFontStyle))
							 unset($this->pdf->CellFontStyle);
           }
           else
           {
  
             //'supercategorie'=>$supercategorie,
						 if($data[0]=='fonds')
               $this->pdf->excelData[]=array($data[1]['hoofdcategorie'],$data[1]['categorie'],$data[1]['omschrijving'],
							   round($data[1]['totaalAantal'],0),
                 round($data[1]['actueleFonds'],2),
                 round($data[1]['startWaardeEur'],0),
                 round($data[1]['stort'],0),
                 round($data[1]['resultaatAbsoluut'],0),
                 round($data[1]['actuelePortefeuilleWaardeEuro'],0),
                 round($data[1]['resultaat'],2),
                 round($data[1]['resultaatAbsoluutJaar'],0),
                 round($data[1]['resultaatJaar'],2));
             
             $this->pdf->row(array($data[1]['omschrijving'],
                           $this->formatGetal($data[1]['totaalAantal'],0),
                           $this->formatGetal($data[1]['actueleFonds'],2),
                           $this->formatGetal($data[1]['startWaardeEur'],0),
                           $this->formatGetal($data[1]['stort'],0),
                           $this->formatGetal($data[1]['resultaatAbsoluut'],0),
                           $this->formatGetal($data[1]['actuelePortefeuilleWaardeEuro'],0),
                           $resultaat,
                           $this->formatGetal($data[1]['resultaatAbsoluutJaar'],0),
                           $resultaatJaar
                           ));
           }
					 $this->pdf->CellBorders = array();
           if(isset($this->pdf->CellFontStyle))
             unset($this->pdf->CellFontStyle);
           if(substr($data[1]['omschrijving'],0,6) == 'Totaal') //Witregel na Totaal
             $this->pdf->ln(8);
         break;
       }
     }
   }

		$DB = new DB();
		foreach ($periodeTotalen as $datum=>$waarde)
		{
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->rapportageKoersen[$datum]." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$datum' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];

		  if(round($totaalWaarde,2) != round($waarde,2))
		  {
		  	echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal op $datum (".round($waarde,2).") in rapport ".$this->pdf->rapport_type."');
			  </script>";
			  ob_flush();
		  }

		}

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];
		  if(round($totaalWaarde,2) != round($periodeTotalen[$this->rapportageDatum],2))
		  {
		  	echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal ".round($periodeTotalen[$this->rapportageDatum],2).") in rapport ".$this->pdf->rapport_type."');
			  </script>";
			  ob_flush();
		  }

//			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);
/*
    foreach ($vuldata as $periode)
	  {
	    if($periode['stop'] != $this->perioden['jan'] && $periode['stop'] != $this->perioden['start'] && $periode['stop'] != $this->perioden['eind'])
	      verwijderTijdelijkeTabel($this->portefeuille, $periode['stop']);
	  }
*/
	  $this->pdf->CellBorders = array();
    unset($this->pdf->widthsDefault);
    unset($this->pdf->CellFontStyle);

	}


	function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum)
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery   as Rapportagekoers
		  ".
		"FROM (Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen)
				".
		" WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
		"Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		// haal koersresultaat op om % te berekenen

/*
 * ,BeleggingssectorPerFonds.Beleggingssector,BeleggingssectorPerFonds.AttributieCategorie
 , BeleggingscategoriePerFonds.Beleggingscategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
		LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ".
 */
		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
	  foreach ($buffer as $mutaties)
		{
      
      $koppelingen = getFondsKoppelingen($this->pdf->portefeuilledata['Vermogensbeheerder'],$rapportageDatum,$mutaties['Fonds'],false);
      foreach($koppelingen as $key=>$value)
        $mutaties[$key]=$value;
      

      
			$mutaties['Aantal'] = abs($mutaties['Aantal']);
			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0 ;


			switch($mutaties['Transactietype'])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;

					break;
					case "B" :
						// Beginstorting
					break;
					case "D" :
					case "S" :
							// Deponering
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= ($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = ($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						//if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						//if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						//if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if(	$mutaties['Transactietype'] == "L" ||
					$mutaties['Transactietype'] == "V" ||
					$mutaties['Transactietype'] == "V/S" ||
					$mutaties['Transactietype'] == "A/S")
			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
					$beginditjaar          = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
				}
				else
				{
					$historischekostprijs = $mutaties['Aantal']        * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
				  $beginditjaar         = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
				}
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
		    {
		    $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		    $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
		    }

				if($historie['voorgaandejarenActief'] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
					}
				}
				else
				{
					$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
					$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}
				$result_historischkostprijs = $historischekostprijs;
				$result_voorgaandejaren = $resultaatvoorgaande;
				$result_lopendejaar = $resultaatlopende;

				$totaal_resultaat_waarde += $resultaatlopende;

			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
			}

	//	listarray($mutaties);
				$data[$mutaties['Fonds']][0]+=$aankoop_waarde-$verkoop_waarde;
				$data[$mutaties['Fonds']][1].=' '.$mutaties['Transactietype'];
				if($mutaties['Credit'])
				  $data[$mutaties['Fonds']][2]+=$mutaties['Aantal'];
				else
			  	$data[$mutaties['Fonds']][2]+=$mutaties['Aantal'];
				$data[$mutaties['Fonds']][3]+=$aankoop_waarde;
				$data[$mutaties['Fonds']][4]+=$verkoop_waarde;
				$data[$mutaties['Fonds']][5]=$mutaties['beleggingssector'];
				$data[$mutaties['Fonds']][6]=$mutaties['beleggingscategorie'];
				$data[$mutaties['Fonds']][7]=$mutaties['AttributieCategorie'];
        

				/*
				$data[]=array(date("d-m",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
											$mutaties['Fonds'],
											$this->formatGetal($mutaties['Aantal'],0),
											"",
											$aankoop_koers,
											$aankoop_waardeinValuta,
											$aankoop_waarde,
											$verkoop_koers,
											$verkoop_waardeinValuta,
											$verkoop_waarde,
											$result_historischkostprijs,
											$result_voorgaandejaren,
											$result_lopendejaar,
											$percentageTotaalTekst);
				*/

		}
   
		//listarray($data);
		return $data;
	}

	function getRekeningMutaties($rekening,$van,$tot)
	{
	  $db= new DB();
    
    	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	    $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQueryBoekdatum = "";
      
	  $query = "
	  SELECT
  SUM(((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers))$koersQueryBoekdatum)  as totaal
 	FROM
	Rekeningmutaties
  WHERE 
	Rekeningmutaties.Rekening =  '$rekening'  AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '$van' AND
	Rekeningmutaties.Boekdatum <= '$tot'";

	  $db->SQL($query);
	  $db->Query();
	  $data = $db->nextRecord();
return $data['totaal'];
	}



		function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
		{
		  
      if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	      $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	    else
	      $koersQueryBoekdatum = "";
      
      
		  $DB=new DB();
		  $query = "SELECT
      Sum(((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))$koersQueryBoekdatum) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
      $DB->SQL($query);
      //if($fonds=='Citigroup')
      //  echo "$fonds $query  <br>\n";
      $DB->Query();
      $totaalWaarde = $DB->NextRecord();

		  return $totaalWaarde['totaalWaarde'];
		}


  function fondsPerformance2($fonds,$datumBegin,$datumEind,$debug=false)
  {
    global $__appvar;
	  $DB=new DB();
	  $perioden=$this->getmaanden(db2jul($datumBegin),db2jul($datumEind));
    $totaalPerf = 100;

    if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	    $koersQueryBoekdatum =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQueryBoekdatum = "";
    foreach ($perioden as $periode)
    {
	    $datumBegin = $periode['start'];
	    $datumEind = $periode['stop'];

	    $RapStartJaar = date("Y", db2jul($datumBegin));
	    if((db2jul($this->pdf->PortefeuilleStartdatum) >= db2jul($datumBegin)) && substr($datumBegin,5,6) <> '01-01')
	      $datumBeginATT=date("Y-m-d",db2jul($datumBegin));
	    else
	      $datumBeginATT=$datumBegin;

      if(substr($datumBegin,5,6) == '01-01')
	      $datumBeginWeging=date("Y-m-d",db2jul($datumBegin)-86400);
	    else
	      $datumBeginWeging=$datumBeginATT;


      if(is_array($fonds))
	      $fondsenWhere = " IN('".implode('\',\'',$fonds)."') ";
      else
        $fondsenWhere = " IN('$fonds') ";

      if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      {
	     $koersQueryDatumBegin =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= '$datumBegin' ORDER BY Datum DESC LIMIT 1 ) ";
	     $koersQueryDatumEind =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= '$datumEind' ORDER BY Datum DESC LIMIT 1 ) ";
      }
      else
      {
        $koersQueryDatum = "";
        $koersQueryDatumEind='';
      }

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) $koersQueryDatumBegin as actuelePortefeuilleWaardeEuro
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];
    


       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) $koersQueryDatumEind as actuelePortefeuilleWaardeEuro
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumEind' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];


	    if($beginwaarde == 0)
	    {
	      $query = "SELECT Rekeningmutaties.Boekdatum as Boekdatum,
	      (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) $koersQueryBoekdatum as waarde FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE (Rekeningmutaties.Fonds $fondsenWhere OR Rekeningmutaties.Rekening $fondsenWhere) AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $start = $DB->NextRecord();
	      if($start['Boekdatum'] != '')
	      {
	        $datumBegin = $start['Boekdatum'];
	        $beginTransactieWaarde = $start['waarde'];
	      }
	      $beginCorrectie=true;
	    }
	    else
	     $beginCorrectie=false;

 	    if($eindwaarde == 0)
 	    {
 	      $query = "SELECT Rekeningmutaties.Boekdatum + INTERVAL 1 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE (Rekeningmutaties.Fonds $fondsenWhere OR Rekeningmutaties.Rekening $fondsenWhere) AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $eind = $DB->NextRecord();
	      if($eind['Boekdatum'] != '')
	        $datumEind = $eind['Boekdatum'];
	      $eindCorrectie=true;
 	    }
 	    else
 	      $eindCorrectie=false;


	     $queryAttributieStortingenOntrekkingenRekening = "SELECT
	               (((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBeginWeging."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQueryBoekdatum )))*-1  AS gewogen, ".
	              "((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQueryBoekdatum)*-1   AS totaal ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               Rekeningmutaties.Rekening $fondsenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
       $AttributieStortingenOntrekkingenRekening=array();
       while($tmp=$DB->nextRecord())
       {
         $AttributieStortingenOntrekkingenRekening['gewogen']+=$tmp['gewogen'];
         $AttributieStortingenOntrekkingenRekening['totaal']+=$tmp['totaal'];
       }
      //if($debug==true){echo "$queryAttributieStortingenOntrekkingenRekening <br>\n";listarray($tmp);listarray($periode);}
	     //$AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
      
       $queryRekeningDirecteKostenOpbrengsten = "SELECT
                 (((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum )))  AS gewogen,
                 ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum)  AS totaal
                 FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                 JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                 WHERE
                 (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND   Rekeningmutaties.Fonds = '' AND
                 Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                 Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBeginATT' AND
                 Rekeningmutaties.Boekdatum <= '$datumEind' AND
                 Rekeningmutaties.Rekening $fondsenWhere  ";
       $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
       $DB->Query();
       $RekeningDirecteKostenOpbrengsten=array();
       while($tmp=$DB->nextRecord())
       {
         $RekeningDirecteKostenOpbrengsten['gewogen']+=$tmp['gewogen'];
         $RekeningDirecteKostenOpbrengsten['totaal']+=$tmp['totaal'];
       }
       //$RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT (((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$datumEind') - TO_DAYS('$datumBeginWeging')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum )))  AS gewogen,
                       ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) $koersQueryBoekdatum AS totaal
                FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBeginATT' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                Rekeningmutaties.Fonds $fondsenWhere";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten); 
       $DB->Query();
       $FondsDirecteKostenOpbrengsten=array();
       while($tmp=$DB->nextRecord())
       {
         $FondsDirecteKostenOpbrengsten['gewogen']+=$tmp['gewogen'];
         $FondsDirecteKostenOpbrengsten['totaal']+=$tmp['totaal'];
       }
 
	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBeginWeging."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum )))  AS gewogen, ".
	              " ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQueryBoekdatum) AS totaal".
	              " FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Transactietype <> 'B' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBeginATT."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Fonds $fondsenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "stort $queryAttributieStortingenOntrekkingen <br>\n";
	     $DB->Query();
       $AttributieStortingenOntrekkingen=array();
        while($tmp=$DB->nextRecord())
       {
         $AttributieStortingenOntrekkingen['gewogen']+=$tmp['gewogen'];
         $AttributieStortingenOntrekkingen['totaal']+=$tmp['totaal'];
       }
 	     //$AttributieStortingenOntrekkingen = $DB->NextRecord(); // echo "$queryAttributieStortingenOntrekkingen <br>\n<br>\n";

       $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
   //    $AttributieStortingenOntrekkingen['totaal'] +=$AttributieStortingenOntrekkingenRekening['totaal'];
   	  $query = "SELECT (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) $koersQueryBoekdatum as totaal
 	              FROM Rekeningmutaties,Rekeningen
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Rekeningen.Rekening $fondsenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBeginATT' AND
	              Rekeningmutaties.Boekdatum <= '$datumEind'";

	     $DB->SQL($query);
	     $DB->Query();
	     while($data = $DB->nextRecord());
      {
        $AttributieStortingenOntrekkingen['totaal'] -= $data['totaal'];
      }
       $directeKostenOpbrengsten['totaal'] = $RekeningDirecteKostenOpbrengsten['totaal'] + $FondsDirecteKostenOpbrengsten['totaal'];
       $directeKostenOpbrengsten['gewogen'] = $RekeningDirecteKostenOpbrengsten['gewogen'] + $FondsDirecteKostenOpbrengsten['gewogen'];

      if($beginCorrectie)
      {
         $AttributieStortingenOntrekkingen['gewogen']=$AttributieStortingenOntrekkingen['totaal'];
         $directeKostenOpbrengsten['gewogen']=$directeKostenOpbrengsten['totaal'];
      }
      if($eindCorrectie)
      {
        $AttributieStortingenOntrekkingen['gewogen']=0;
        $directeKostenOpbrengsten['gewogen']=0;
      }

      if($beginCorrectie && $eindCorrectie)
      {
        $performance=$AttributieStortingenOntrekkingen['totaal']/ $beginTransactieWaarde * -100;
        //echo "perf $performance=".$AttributieStortingenOntrekkingen['totaal']."/ $beginTransactieWaarde * -100; <br>\n";
      }
      else
      {
 	      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'] - $directeKostenOpbrengsten['gewogen'] ;
        if($beginwaarde > 0 && $gemiddelde <0)
        {
          //echo "$fondsenWhere $gemiddelde <br>\n";
          $gemiddelde=$gemiddelde*-1;
        }
        $performance = ((($eindwaarde - $beginwaarde) + $AttributieStortingenOntrekkingen['totaal'] + $directeKostenOpbrengsten['totaal'] ) / $gemiddelde) * 100;
      }

		$debug=true;
	  $debug=false;
      if($debug )//
      {
        echo "    <br>\n" ;
        echo "$datumBegin $datumEind ($beginCorrectie) ($eindCorrectie) $datumBeginWeging<br>\n";

    //    echo "$queryAttributieStortingenOntrekkingen <br>\n";
     //  echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n " ;
      //echo "$queryFondsDirecteKostenOpbrengsten <br>\n";
       echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryAttributieStortingenOntrekkingen <br>\n";
     //  listarray($directeKostenOpbrengsten);
     //  listarray($AttributieStortingenOntrekkingen);

       echo "$fondsenWhere $datumBegin -> $datumEind <br>\n";
       echo "gemiddelde= 	 $gemiddelde = begin $beginwaarde -  gewogenSo ".$AttributieStortingenOntrekkingen['gewogen']." - gewogenDko ".$directeKostenOpbrengsten['gewogen']."<br>\n " ;
       echo "   $performance = ((($eindwaarde - $beginwaarde) + ".$AttributieStortingenOntrekkingen['totaal']." + ".$directeKostenOpbrengsten['totaal']." ) / $gemiddelde) * 100;	<br>\n";
       ob_flush();


      echo ($totaalPerf  * (100+$performance)/100)." = ($totaalPerf  * (100+$performance)/100) <br>\n";
      }
      $totaalPerf = ($totaalPerf  * (100+$performance)/100) ;

    }
    if($debug)// && $fonds=='Citigroup'
     echo " perftotaal ".($totaalPerf-100) ."<br>\n ";

  return ($totaalPerf-100);
	}


function getKwartalen($julBegin, $julEind)
{
	  $eindjaar = date("Y",$julEind);
	  $eindmaand = floor(date("m",$julEind)/3)*3;
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = floor(date("m",$julBegin)/3)*3;

   $i=0;
   $stop=mktime (0,0,0,$eindmaand-3,0,$eindjaar);
    while ($counterStart <= $stop)
	  {

	    $counterStart = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+4,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i=$i+3;
	  }

	  if($julEind > db2jul($datum[$i-3]['stop']))
	  {
	    $datum[$i]['start'] = $datum[$i-3]['stop'];
	    $datum[$i]['stop'] = jul2sql($julEind);
	  }
	  return $datum;
}

function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}


}
?>
