<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/04/25 16:45:28 $
 		File Versie					: $Revision: 1.7 $

 		$Log: RapportHSE_L20.php,v $
 		Revision 1.7  2018/04/25 16:45:28  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/03/21 17:04:24  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/03/17 18:48:55  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/05/31 18:55:19  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/15 14:52:51  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2012/07/29 10:24:33  rvv
 		*** empty log message ***
 		

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L20
{
	function RapportHSE_L20($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_deel = 'overzicht';
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->RapStartJaar = date("Y", $this->pdf->rapport_datumvanaf);

	  $this->pdf->rapport_titel = "Overzicht beleggingen.";

		$this->pdf->rapport_header1 = array('Beheerder',"Regio","Waarde\nEUR\n".date("d-m-y",$this->pdf->rapport_datum),
		"Procentuele\nverdeling","Waarde\nEUR\n".date("d-m-y",$this->pdf->rapport_datumvanaf),"stortingen en\nonttr.dit jaar\nEUR",
    "Resultaat\ndit jaar\nEUR","Resultaat\ndit jaar\n%");

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->perioden['start'] = $this->rapportageDatumVanaf;
		$this->perioden['eind'] = $this->rapportageDatum;

		$this->pdf->underlinePercentage = .7;
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}

	function formatGetal($waarde, $dec, $percent = false,$limit = false)
	{
	  if(round($waarde,2) != 0.00)
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

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;
	    return number_format($this->pdf->ValutaKoersStart,2,",",".") ." - ".number_format($waarde,$dec,",",".");
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
    global $USR;


		$this->tweedeStart();

	  if ($this->rapportageDatumVanaf != $this->tweedePerformanceStart)
	  {
	    if(substr($this->tweedePerformanceStart,4,6)=='-01-01')
	     $startJaar = 1;
      $fondswaarden[c] =  berekenPortefeuilleWaarde($this->portefeuille, $this->tweedePerformanceStart,$startJaar,$this->pdf->rapportageValuta);
	    vulTijdelijkeTabel($fondswaarden[c] ,$this->portefeuille,$this->tweedePerformanceStart);

	    $this->perioden['jan'] = $this->tweedePerformanceStart;
	  }
	  else
	   $this->perioden['jan']=$this->rapportageDatumVanaf;

	 	if($this->perioden['jan'])
	    $start = $this->perioden['jan'];
	  else
	    $start = $this->perioden['start'];

	  $vuldata=$this->getKwartalen(db2jul($this->perioden['jan']),$this->pdf->rapport_datum);
	  foreach ($vuldata as $periode)
	  {
	    if($periode['stop'] != $this->perioden['jan'] && $periode['stop'] != $this->perioden['start'] && $periode['stop'] != $this->perioden['eind'])
	    {
	      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $periode['stop'],0,$this->pdf->rapportageValuta,$periode['start']);
	      vulTijdelijkeTabel($fondswaarden,$this->portefeuille, $periode['stop']);
	    }
	  }


	  $index=new indexHerberekening();
	  $categorien=array('RISM','RISD');
    $rendamentWaardenPeriode = $index->getWaardenATT($this->perioden['start'] ,$this->perioden['eind'] ,$this->portefeuille,$categorien,'kwartaal');
    $rendamentWaardenJaar = $index->getWaardenATT($this->perioden['jan'] ,$this->perioden['eind'] ,$this->portefeuille,$categorien,'kwartaal');

	  foreach ($categorien as $categorie)
	  {
	    foreach ($rendamentWaardenPeriode[$categorie] as $rendamentWaarden)
	      $attributieWaarden['periode'][$categorie]=$rendamentWaarden['index']-100;
	    foreach ($rendamentWaardenJaar[$categorie] as $rendamentWaarden)
	      $attributieWaarden['jaar'][$categorie]=$rendamentWaarden['index']-100;
	  }

	 	$DB = new DB();
	 	$query = "SELECT Beleggingscategorie,Omschrijving FROM Beleggingscategorien ";
	 	$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		  $categorieOmschrijving[$data['Beleggingscategorie']] = $data['Omschrijving'];

		$categorieOmschrijving['LIQUIDITEITEN'] = 'Liquiditeiten';
		$categorieOmschrijving['Liquiditeiten'] = 'Rekeningen';



$mutaties['periode']=$this->genereerMutatieLijst($this->rapportageDatumVanaf,$this->rapportageDatum);
$mutaties['jaar']=$this->genereerMutatieLijst($start,$this->rapportageDatum);

	  $this->waarden = array();
	  $rekeningen = array();
		$categorieTotalen = array();
		$periodeTotalen = array();


	    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving,
				       TijdelijkeRapportage.fonds,
				       TijdelijkeRapportage.rapportageDatum,
				       TijdelijkeRapportage.actueleValuta,
				       TijdelijkeRapportage.totaalAantal,
				       TijdelijkeRapportage.Valuta,
				       TijdelijkeRapportage.Bewaarder,
				       Regios.Omschrijving as Regio,
				       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) as beginPortefeuilleWaardeEuro,
				       TijdelijkeRapportage.actueleFonds,
				       SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as actuelePortefeuilleWaardeInValuta,
				       SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)  as actuelePortefeuilleWaardeEuro ,
				       TijdelijkeRapportage.beleggingscategorie,
				       TijdelijkeRapportage.portefeuille
				       FROM TijdelijkeRapportage
				       LEFT JOIN Regios ON  TijdelijkeRapportage.Regio = Regios.Regio
				       WHERE
				       TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
				       (TijdelijkeRapportage.type = 'fondsen' OR TijdelijkeRapportage.type = 'rente' )  AND
				       TijdelijkeRapportage.rapportageDatum IN('".implode("','",$this->perioden)."')
				       ".$__appvar['TijdelijkeRapportageMaakUniek']."
				       GROUP BY TijdelijkeRapportage.fonds,TijdelijkeRapportage.rapportageDatum
				       ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";//TijdelijkeRapportage.Lossingsdatum,
      	       $DB->SQL($query);
		           $DB->Query();
		           while($data = $DB->NextRecord())
		           {
		            $this->waarden[$data['rapportageDatum']][$data['beleggingscategorie']][$data['fonds']]= $data;
		            $this->fondsenPerCategorie[$data['beleggingscategorie']][$data['fonds']]=$data['fonds'];
		            $this->fondsOmschrijvingen[$data['fonds']]=$data['fondsOmschrijving'];
		            $categorieTotalen[$data['rapportageDatum']][$data['beleggingscategorie']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
		            $categorieTotalen[$data['rapportageDatum']][$data['beleggingscategorie']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
		            $periodeTotalen[$data['rapportageDatum']] += $data['actuelePortefeuilleWaardeEuro'];
		           }

		// Liquiditeiten
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta ,TijdelijkeRapportage.rapportageDatum, ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, Rekeningen.Beleggingscategorie as beleggingscategorie".
			" FROM TijdelijkeRapportage
			  LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening = Rekeningen.Rekening AND Rekeningen.portefeuille <> 'C_$USR'
			  WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum IN('".implode("','",$this->perioden)."') "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		if($DB1->records() > 0)
		{

		  while($data = $DB1->NextRecord())
			{
         $rekeningCategorien[$data['beleggingscategorie']]=$data['beleggingscategorie'];
			          $rekeningen[$data['rekening']] = $data['rekening'];
			          if($data['beleggingscategorie'] == '')
			  			    $data['beleggingscategorie'] ='Liquiditeiten';
		            $this->waarden[$data['rapportageDatum']][$data['beleggingscategorie']][$data['rekening']]= $data;
		            $this->fondsenPerCategorie[$data['beleggingscategorie']][$data['rekening']]=$data['rekening'];
		            $this->fondsOmschrijvingen[$data['rekening']]=$data['fondsOmschrijving']." ".$data['rekening'];
		            $categorieTotalen[$data['rapportageDatum']][$data['beleggingscategorie']]['actuelePortefeuilleWaardeInValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
		            $categorieTotalen[$data['rapportageDatum']][$data['beleggingscategorie']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
		            $periodeTotalen[$data['rapportageDatum']] += $data['actuelePortefeuilleWaardeEuro'];
			}
		}


	 // listarray($categorieTotalen);flush();


		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";

		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


	//

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

	  foreach ($this->perioden as $key=>$datum)
	  {
	    $this->waarden[$datum][$data['beleggingscategorie']][$data['fonds']]= $data;
	    foreach ($fondsMutaties as $fonds=>$mutatieData)
	    {

	      if(!key_exists($fonds,$this->fondsOmschrijvingen))
	      {
	        $query="SELECT BeleggingscategoriePerFonds.Beleggingscategorie,Fondsen.Omschrijving,Regios.Omschrijving as RegioOmschrijving
	                FROM Fondsen
	                JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds  AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
	                JOIN BeleggingssectorPerFonds ON Fondsen.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
	                LEFT JOIN Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
	                WHERE BeleggingscategoriePerFonds.Fonds='$fonds' AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'";
	     		$DB->SQL($query);
		      $DB->Query();
		      $categorie = $DB->nextRecord();
		      if(!is_array($this->waarden[$datum][$categorie['Beleggingscategorie']][$fonds]))
		      {
  		      $this->waarden[$datum][$categorie['Beleggingscategorie']][$fonds]= array('fonds'=>$fonds,'Regio'=>$categorie['RegioOmschrijving']);
  		      $this->fondsOmschrijvingen[$fonds]=$categorie['Omschrijving'];
		      }
	      }
	    }
	  }


foreach ($rekeningen as $rekening)
{
  $mutaties['periode'][$rekening][0]=$this->getRekeningMutaties($rekening,$this->rapportageDatumVanaf,$this->rapportageDatum);
  $mutaties['jaar'][$rekening][0]=$this->getRekeningMutaties($rekening,$start,$this->rapportageDatum);
}

	//

		$query = "SELECT
    CategorienPerHoofdcategorie.beleggingscategorie,CategorienPerHoofdcategorie.Hoofdcategorie
    FROM CategorienPerHoofdcategorie
    LEFT JOIN  Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
    WHERE CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
    ORDER BY Beleggingscategorien.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
		while($categorien = $DB->NextRecord())
		{
     $beleggingscategorien[$categorien['beleggingscategorie']] =$categorien['Hoofdcategorie'];
     $categorieKoppeling[$categorien['Hoofdcategorie']][]=$categorien['beleggingscategorie'];
  	}
  	foreach ($beleggingscategorien as $beleggingscategorie=>$hoofdcategorie)
  	{
  	  if(in_array($beleggingscategorie,$beleggingscategorien))
  	    $supercategorien[$hoofdcategorie][]=$beleggingscategorie;
  	}


		$hoofdcategorienBijdrageResultaat=array();
		$hoofdcategorienStortingenontrekkingen=array();
		$hoofdcategorienBeleggingsresultaat=array();

		$hoofdcategorienStortingenontrekkingenJaar=array();
		$hoofdcategorienBeleggingsresultaatJaar=array();

		$supercategorienBijdrageResultaat=array();
		$supercategorienStortingenontrekkingen=array();
		$supercategorienBeleggingsresultaat=array();

		$supercategorienStortingenontrekkingenJaar=array();
		$supercategorienBeleggingsresultaatJaar=array();


$categorieKoppeling['LIQUIDITEITEN'][] = 'Rente';
$categorieKoppeling['LIQUIDITEITEN'][] = 'Liquiditeiten';
$supercategorien['WW-RISM'][] = 'LIQUIDITEITEN';



$indexPerioden=$this->perioden;
$huidigeMaand = substr($indexPerioden['eind'],5,2);
$huidigeDag = substr($indexPerioden['eind'],8,2);
if($huidigeMaand < 4)
 $indexPerioden['kwartaal']= date('Y-m-d',mktime(0,0,0,1,1,$this->RapStartJaar));
elseif($huidigeMaand < 7)
 $indexPerioden['kwartaal'] = date('Y-m-d',mktime(0,0,0,4,0,$this->RapStartJaar));
elseif ($huidigeMaand < 10)
 $indexPerioden['kwartaal'] = date('Y-m-d',mktime(0,0,0,7,0,$this->RapStartJaar));
else
 $indexPerioden['kwartaal'] = date('Y-m-d',mktime(0,0,0,10,0,$this->RapStartJaar));

//$indexPerioden['driejaar']=date('Y-m-d',mktime(0,0,0,1,1,$this->RapStartJaar-2));
    $indexPerioden['driejaar']=date('Y-m-d',mktime(0,0,0,$huidigeMaand,$huidigeDag,$this->RapStartJaar-3));
		$indexPerioden['vijfjaar']=date('Y-m-d',mktime(0,0,0,$huidigeMaand,$huidigeDag,$this->RapStartJaar-5));
		$indexPerioden['tienjaar']=date('Y-m-d',mktime(0,0,0,$huidigeMaand,$huidigeDag,$this->RapStartJaar-10));


//listarray($indexPerioden);exit;




  $periodeDone=array();
   foreach ($supercategorien as $supercategorie=>$hoofdcategorien)
   {
     foreach ($hoofdcategorien as $hoofdcategorie)
     {
       if(isset($categorieKoppeling[$hoofdcategorie]))
       {
         foreach ($categorieKoppeling[$hoofdcategorie] as $categorie)
         {
           $periodeDone=array();
           foreach ($this->perioden as $periode)
	         {
             foreach ($this->waarden[$periode][$categorie] as $fonds=>$waarden)
             {
               if(!in_array($periode,$periodeDone))
               {
               $supercategorieTotalen[$periode][$supercategorie]['actuelePortefeuilleWaardeInValuta'] += $waarden['actuelePortefeuilleWaardeInValuta'];
               $supercategorieTotalen[$periode][$supercategorie]['actuelePortefeuilleWaardeEuro']     += $waarden['actuelePortefeuilleWaardeEuro'];

               $hoofdcategorieTotalen[$periode][$hoofdcategorie]['actuelePortefeuilleWaardeInValuta'] += $waarden['actuelePortefeuilleWaardeInValuta'];
               $hoofdcategorieTotalen[$periode][$hoofdcategorie]['actuelePortefeuilleWaardeEuro']     += $waarden['actuelePortefeuilleWaardeEuro'];
               }
               $portefeuilleFondsen[$fonds] = $fonds;

               $this->fondsenPerCategorie[$hoofdcategorie][$fonds]=$fonds; //Toegevoegd voor performance berekening in categorie
               $this->fondsenPerCategorie[$supercategorie][$fonds]=$fonds;
               $this->fondsenPerCategorie[$categorie][$fonds]=$fonds;
               if(is_array($fondsMutaties[$fonds]))
               {
                 $this->toon['hoofdcategorie'][$hoofdcategorie]=1;
                 $this->toon['supercategorie'][$supercategorie]=1;
                 $this->toon['categorie'][$categorie]=1;
               }

	           }

	           $periodeDone[]=$periode;
	         }
         }
       }
     }
   }


//listarray($portefeuilleFondsen);
//listarray($supercategorien);
//listarray($hoofdcategorien);
//listarray($this->fondsenPerCategorie);ob_flush();
//exit;
	$query="desc FondsExtraInformatie";
	$extraVeld='Benchmark';
	$DB->SQL($query);
	$DB->Query();
	$extraJoin='';
	$extraSelect='';
	while($data = $DB->NextRecord())
	{
			if($data['Field']==$extraVeld)
			{
				$extraJoin="LEFT JOIN FondsExtraInformatie ON Fondsen.Fonds=FondsExtraInformatie.fonds";
				$extraSelect=", FondsExtraInformatie.$extraVeld as extraInfo";
			}
	}
  $query = "SELECT Indices.Beursindex, Indices.specialeIndex, Fondsen.Omschrijving,BeleggingscategoriePerFonds.Beleggingscategorie $extraSelect
            FROM Indices Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
            LEFT Join BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds  AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            $extraJoin
            WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
            ORDER BY Indices.Afdrukvolgorde ";
  $DB->SQL($query);
	$DB->Query();
	while($data = $DB->NextRecord())
	{
	  if($data['specialeIndex'] == 1)
	  {
	    if(in_array($data['Beursindex'],$portefeuilleFondsen))
	    {
	      $indexen['speciale'][$data['Beursindex']]['Omschrijving']=$data['Omschrijving'];
	      $indexen['speciale'][$data['Beursindex']]['Categorie']=$data['Beleggingscategorie'];
				$indexen['speciale'][$data['Beursindex']]['extraInfo']=$data['extraInfo'];
	    }
	  }
	  else
	  {
      $indexen['normale'][$data['Beursindex']]['Omschrijving']=$data['Omschrijving'];
      $indexen['normale'][$data['Beursindex']]['Categorie']=$data['Beleggingscategorie'];
			$indexen['normale'][$data['Beursindex']]['extraInfo']=$data['extraInfo'];
	  }
	}

	foreach ($indexen as $type=>$fondsen)
	{
	  foreach ($fondsen as $fonds=>$fondsData)
	  {
	    $this->fondsOmschrijvingen[$fonds] = $fondsData['Omschrijving'];
			$this->fondsExtraInfo[$fonds] = $fondsData['extraInfo'];
	    foreach ($indexPerioden as $key=>$value)
	    {
	      $normalekoers = false;
	      $query = "SELECT Koers FROM Schaduwkoersen WHERE Fonds = '$fonds' AND datum <=  '".$value ."'  ORDER BY datum DESC limit 1 ";
        $DB->SQL($query);
		    $DB->Query();
		    $koers = $DB->NextRecord();
		    if($koers['Koers'] != '')
		      $this->fondsIndex[$type][$fonds][$key]=$koers['Koers'];
		    else
		      $normalekoers = true;

		    if($normalekoers == true)
	      {
	        $query = "SELECT Koers FROM Fondskoersen WHERE Fonds = '$fonds' AND datum <=  '".$value ."'  ORDER BY datum DESC limit 1 ";
          $DB->SQL($query);
		      $DB->Query();
		      $koers = $DB->NextRecord();
	        $this->fondsIndex[$type][$fonds][$key]=$koers['Koers'];
	      }
	    }
	     $this->fondsIndex[$type][$fonds]['categorie']=$fondsData['Categorie'];
	  }
	}
//listarray(  $this->fondsIndex);
	 $tmp=array();
	 foreach ($supercategorien as $supercategorie=>$hoofdcategorien)
   {
     foreach ($hoofdcategorien as $hoofdcategorie)
     {
       foreach ($categorieKoppeling[$hoofdcategorie] as $categorie)
       {
         foreach ($this->fondsIndex as $type=>$fondsWaarden)
         {
           foreach ($fondsWaarden as $fonds=>$waarden)
           {
             if($categorie == $waarden['categorie'])
             {
	             $tmp['kwartaal'] =($waarden['eind']/$waarden['kwartaal']*100)-100;
	             $tmp['jaar']     = ($waarden['eind']/$waarden['jan']*100)-100;
	             $tmp['driejaar'] = (pow($waarden['eind']/$waarden['driejaar'],1/3)* 100)-100;
							 $tmp['vijfjaar'] = (pow($waarden['eind']/$waarden['vijfjaar'],1/5)* 100)-100;
							 $tmp['tienjaar'] = (pow($waarden['eind']/$waarden['tienjaar'],1/10)* 100)-100;
							 if($type=='speciale')
                 $this->fondsIndex3[$type][$supercategorie][$hoofdcategorie][$categorie][$fonds]=$tmp;
							 else
								 $this->fondsIndex2[$type][$supercategorie][$hoofdcategorie][$categorie][$fonds]=$tmp;

               $indexMetCategorie[]=$fonds;
             }
           }
         }
       }
     }
   }
   $tmp=array();
   foreach ($this->fondsIndex as $type=>$fondsWaarden)
   {
     foreach ($fondsWaarden as $fonds=>$waarden)
     {
       if(!in_array($fonds,$indexMetCategorie))
       {
	        $tmp['kwartaal'] =($waarden['eind']/$waarden['kwartaal']*100)-100;
	        $tmp['jaar']     = ($waarden['eind']/$waarden['jan']*100)-100;
	        $tmp['driejaar'] = (pow($waarden['eind']/$waarden['driejaar'],1/3)* 100)-100;
			    $tmp['vijfjaar'] = (pow($waarden['eind']/$waarden['vijfjaar'],1/5)* 100)-100;
				  $tmp['tienjaar'] = (pow($waarden['eind']/$waarden['tienjaar'],1/10)* 100)-100;
				 if($type=='speciale')
					 $this->fondsIndex3[$type]['geenSuperCategorie']['geenHoofdCategorie']['geenCategorie'][$fonds]=$tmp;
				 else
          $this->fondsIndex2[$type]['geenSuperCategorie']['geenHoofdCategorie']['geenCategorie'][$fonds]=$tmp;
       }
     }
   }

   	if ($this->rapportageDatumVanaf == $this->tweedePerformanceStart)
	  {
	   $this->perioden['jan'] = $this->rapportageDatumVanaf;
	  }

   foreach ($supercategorien as $supercategorie=>$hoofdcategorien)
   {
     if($supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'] <>0 || $supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] <>0 || $this->toon['supercategorie'][$supercategorie]==1) //|| $supercategorieTotalen[$this->perioden['jan']][$supercategorie]['actuelePortefeuilleWaardeEuro'] <>0
     {
       $regels['overzicht'][] = array('kopSuper',array('omschrijving'=>$categorieOmschrijving[$supercategorie]));
       $regels['mutaties'][] = array('kopSuper',array('0'=>$categorieOmschrijving[$supercategorie]));

     foreach ($hoofdcategorien as $hoofdcategorie)
     {
       if($hoofdcategorieTotalen[$this->perioden['start']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] <>0 || $hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] <>0 || $this->toon['hoofdcategorie'][$hoofdcategorie]==1 )//|| $hoofdcategorieTotalen[$this->perioden['jan']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] <>0
       {
         $regels['overzicht'][] = array('kopHoofd',array('omschrijving'=>$categorieOmschrijving[$hoofdcategorie]));
         $regels['mutaties'][]  = array('kopHoofd',array('0'=>$categorieOmschrijving[$hoofdcategorie]));
         if(isset($categorieKoppeling[$hoofdcategorie]))
         {
           foreach ($categorieKoppeling[$hoofdcategorie] as $categorie)
           {

             if($categorieTotalen[$this->perioden['start']][$categorie]['actuelePortefeuilleWaardeEuro'] <>0 || $categorieTotalen[$this->perioden['eind']][$categorie]['actuelePortefeuilleWaardeEuro'] <>0 || $this->toon['categorie'][$categorie]==1)//|| $categorieTotalen[$this->perioden['jan']][$categorie]['actuelePortefeuilleWaardeEuro'] <>0
             {
               $regels['overzicht'][] = array('kopCategorie',array('omschrijving'=>$categorieOmschrijving[$categorie]));
               $regels['mutaties'][]  = array('kopCategorie',array('0'=>$categorieOmschrijving[$categorie]));
  
  
               if(isset($this->fondsenPerCategorie[$categorie]))
                 ksort($this->fondsenPerCategorie[$categorie]);
  
               //listarray($this->fondsenPerCategorie[$categorie]);
               
               foreach ($this->fondsenPerCategorie[$categorie] as $fonds)
		           {
		             $totaalMutaties  = $this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] - $this->waarden[$this->perioden['start']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'];
		             $totaalMutatiesJaar  = $this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] - $this->waarden[$this->perioden['jan']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'];

  		           $directeKostenOpbrengsten = $this->fondsKostenOpbrengsten($fonds,$this->perioden['start'],$this->perioden['eind']);


		             $stortingenontrekkingen = $mutaties['periode'][$fonds][0]-$directeKostenOpbrengsten;

		             $beleggingsresultaat = $totaalMutaties - $stortingenontrekkingen ;

		             $gewogenResultaat = $this->fondsPerformance2($fonds,$this->perioden['start'],$this->perioden['eind']);
  	             $bijdrageResultaat = $beleggingsresultaat / ($supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'])*100;

  	             $directeKostenOpbrengstenJaar = $this->fondsKostenOpbrengsten($fonds,$this->perioden['jan'],$this->perioden['eind']);
		             $stortingenontrekkingenJaar = $mutaties['jaar'][$fonds][0]-$directeKostenOpbrengstenJaar;

		             $beleggingsresultaatJaar = $totaalMutatiesJaar -$stortingenontrekkingenJaar;

                 $gewogenResultaatJaar = $this->fondsPerformance2($fonds,$this->perioden['jan'],$this->perioden['eind'],true);

		             $bijdrageResultaatJaar = $beleggingsresultaat / ($supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['jan']][$supercategorie]['actuelePortefeuilleWaardeEuro'])*100;

// rente op geldrekeningen ophalen
// toegevoegd 12 feb 2009 door cvs
               $rekeningRente = 0;
               $rekeningRenteJaar = 0;


               if ($this->waarden[$this->perioden['eind']][$categorie][$fonds]["beleggingscategorie"] == "Liquiditeiten" ||
                   in_array($this->waarden[$this->perioden['eind']][$categorie][$fonds]["beleggingscategorie"],$rekeningCategorien)||
                   in_array($fonds,$rekeningen))
               {

                 $item = $this->waarden[$this->perioden['eind']][$categorie][$fonds];
             // rente berekenen dezelfde methode als PERF
    			       $query = "
    			   SELECT
    			     SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit,
    			     SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet
		    	   FROM
		    	     Rekeningmutaties, Rekeningen, Portefeuilles
		    	   WHERE
		    	     Rekeningmutaties.Rekening = Rekeningen.Rekening AND
		  	       Rekeningen.Portefeuille = '".$this->portefeuille."' AND
		  	       Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		  	       Rekeningmutaties.Verwerkt = '1' AND
		  	       Rekeningmutaties.Rekening = '".$fonds."' AND
		  	       Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
		  	       Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
			         Rekeningmutaties.Grootboekrekening = 'rente' ";

			           $DB2 = new DB();
			           $DB2->SQL($query);
			           $DB2->Query();
			           $opbrengst = $DB2->nextRecord();
			           $rekeningRente =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
                 $query = "
    			   SELECT
    			     SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit,
    			     SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet
		    	   FROM
		    	     Rekeningmutaties, Rekeningen, Portefeuilles
		    	   WHERE
		    	     Rekeningmutaties.Rekening = Rekeningen.Rekening AND
		  	       Rekeningen.Portefeuille = '".$this->portefeuille."' AND
		  	       Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		  	       Rekeningmutaties.Verwerkt = '1' AND
		  	       Rekeningmutaties.Rekening = '".$fonds."' AND
		  	       Rekeningmutaties.Boekdatum > '".substr($this->rapportageDatum,0,4)."-01-01' AND
		  	       Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
			         Rekeningmutaties.Grootboekrekening = 'rente' ";
                 $DB2->SQL($query);
			           $DB2->Query();
			           $opbrengst = $DB2->nextRecord();
			           $rekeningRenteJaar =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);


                 $stortingenontrekkingen     -= $rekeningRente;
                 $stortingenontrekkingenJaar -= $rekeningRenteJaar;
                 $beleggingsresultaat        += $rekeningRente;
                 $beleggingsresultaatJaar    += $rekeningRenteJaar;
			  //////////////////

               }
		        //     echo "$fonds $beleggingsresultaat = $totaalMutaties - $stortingenontrekkingen |  $rekeningRente $rekeningRenteJaar <br>\n";

//hoofdcategorie

		             $hoofdcategorienBijdrageResultaat[$hoofdcategorie] += $bijdrageResultaat;
		             $hoofdcategorienStortingenontrekkingen[$hoofdcategorie] += $stortingenontrekkingen;
		             $hoofdcategorienBeleggingsresultaat[$hoofdcategorie] += $beleggingsresultaat;

		             $hoofdcategorienStortingenontrekkingenJaar[$hoofdcategorie] += $stortingenontrekkingenJaar;
		             $hoofdcategorienBeleggingsresultaatJaar[$hoofdcategorie] += $beleggingsresultaatJaar;
//supercategorie
		             $supercategorienBijdrageResultaat[$supercategorie] += $bijdrageResultaat;
		             $supercategorienStortingenontrekkingen[$supercategorie] += $stortingenontrekkingen;
		             $supercategorienBeleggingsresultaat[$supercategorie] += $beleggingsresultaat;

		             $supercategorienStortingenontrekkingenJaar[$supercategorie] += $stortingenontrekkingenJaar;
		             $supercategorienBeleggingsresultaatJaar[$supercategorie] += $beleggingsresultaatJaar;


		             if($this->waarden[$this->perioden['start']][$categorie][$fonds]['Bewaarder'] !='')
		               $bewaarder = $this->waarden[$this->perioden['start']][$categorie][$fonds]['Bewaarder'];
		             elseif($this->waarden[$this->perioden['eind']][$categorie][$fonds]['Bewaarder'] !='')
		               $bewaarder = $this->waarden[$this->perioden['eind']][$categorie][$fonds]['Bewaarder'];
		             else
		              $bewaarder = '';

		             if($this->waarden[$this->perioden['start']][$categorie][$fonds]['Regio'] !='')
		               $regio =$this->waarden[$this->perioden['start']][$categorie][$fonds]['Regio'];
		             elseif($this->waarden[$this->perioden['eind']][$categorie][$fonds]['Regio'] !='')
		               $regio =$this->waarden[$this->perioden['eind']][$categorie][$fonds]['Regio'];
		             else
		              $regio ='';

                if($this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] <> 0 ||
                   $this->waarden[$this->perioden['start']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] <> 0 || $beleggingsresultaatJaar <> 0)
		            $regels['overzicht'][] = array('fonds',
		                                     array('omschrijving'=>$this->fondsOmschrijvingen[$fonds],
		                                         'bewaarder'=>$bewaarder,
		                                         'regio'=>$regio,
		                                         'totaalAantal'=>$this->waarden[$this->perioden['eind']][$categorie][$fonds]['totaalAantal'],
		                                         'actueleFonds'=> $this->waarden[$this->perioden['eind']][$categorie][$fonds]['actueleFonds'],
		                                         'actuelePortefeuilleWaardeInValuta'=>$this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeInValuta'],
		                                         'actuelePortefeuilleWaardeEuro'=>$this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'],
		                                         'perc'=> $this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro']/$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro']*100,
		                                         'janWaardeVal'=>$this->waarden[$this->perioden['start']][$categorie][$fonds]['actuelePortefeuilleWaardeInValuta'],
		                                         'janWaardeEur'=>$this->waarden[$this->perioden['start']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'],
		                                         'mutatieWaarde'=>$this->waarden[$this->perioden['eind']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'] - $this->waarden[$this->perioden['start']][$categorie][$fonds]['actuelePortefeuilleWaardeEuro'],
                                             'storting'=>$stortingenontrekkingenJaar,
		                                         'resultaat'=>$beleggingsresultaatJaar,
		                                         'resultaatgewogen'=>$gewogenResultaatJaar));




		            $regels['mutaties'][] = array('fonds',
		                                     array($this->fondsOmschrijvingen[$fonds],
		                                         $totaalMutaties,
		                                         $stortingenontrekkingen,
		                                         $beleggingsresultaat,
		                                         $gewogenResultaat ,
		                                         '' ,//$bijdrageResultaat
		                                         '',
		                                         $stortingenontrekkingenJaar,
		                                         $beleggingsresultaatJaar,
		                                         $gewogenResultaatJaar));

		           }
		           $regels['overzicht'][] = array('categorieTotaal',array(''));
		           $regels['mutaties'][] = array('categorieTotaal',array(''));
	           }
           }
         }
$performance = $this->fondsPerformance2($this->fondsenPerCategorie[$hoofdcategorie],$this->perioden['start'],$this->perioden['eind']);
$performanceJaar = $this->fondsPerformance2($this->fondsenPerCategorie[$hoofdcategorie],$this->perioden['jan'],$this->perioden['eind'],true);

         $regels['overzicht'][] = array('hoofdcategorieTotaal',
		                              array('regio'=>'Subtotaal',
		                                    'actuelePortefeuilleWaardeEuro'=>$hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'perc'=>$hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro']/$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro']*100,
		                                    'janWaardeEur'=>$hoofdcategorieTotalen[$this->perioden['start']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'mutatieWaarde'=>$hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] - $hoofdcategorieTotalen[$this->perioden['start']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'],
                                     
                                        'storting'=>$hoofdcategorienStortingenontrekkingenJaar[$hoofdcategorie],
		                                    'resultaat'=>$hoofdcategorienBeleggingsresultaatJaar[$hoofdcategorie],
		                                    'resultaatgewogen'=>$performanceJaar   
                                        
                                         ));



        $regels['mutaties'][] = array('hoofdcategorieTotaal',
		                              array('Subtotaal',
		                                    $hoofdcategorieTotalen[$this->perioden['eind']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'] - $hoofdcategorieTotalen[$this->perioden['start']][$hoofdcategorie]['actuelePortefeuilleWaardeEuro'],
		                                    $hoofdcategorienStortingenontrekkingen[$hoofdcategorie],
		                                    $hoofdcategorienBeleggingsresultaat[$hoofdcategorie],
		                                    $performance,
		                                    '',//$hoofdcategorienBijdrageResultaat[$hoofdcategorie]
		                                    '',
		                                    $hoofdcategorienStortingenontrekkingenJaar[$hoofdcategorie],
		                                    $hoofdcategorienBeleggingsresultaatJaar[$hoofdcategorie],
		                                    $performanceJaar
		                                    ));
		     $regels['overzicht'][] = array('categorieTotaal',array(''));
		     $regels['mutaties'][] = array('categorieTotaal',array(''));

       }
     }


      $regels['mutaties'][] = array('',array());
      $regels['overzicht'][] = array('',array());

$performance = $this->fondsPerformance2($this->fondsenPerCategorie[$supercategorie],$this->perioden['start'],$this->perioden['eind'],false);
$performanceJaar = $this->fondsPerformance2($this->fondsenPerCategorie[$supercategorie],$this->perioden['jan'],$this->perioden['eind'],true);

if($this->checkValues == true)
{
  if(round($performance,1) != round($attributieWaarden['periode'][substr($supercategorie,3,4)],1))
  {
    echo "<script>alert('Fout :Voor Portefeuille ".$this->portefeuille.", ".substr($supercategorie,3,4)." periode ".round($performance,1)." <> ".round($attributieWaarden['periode'][substr($supercategorie,3,4)],1)."');</script>";ob_flush();
  }
  if(round($performanceJaar,1) != round($attributieWaarden['jaar'][substr($supercategorie,3,4)],1))
  {
    echo "<script>alert('Fout :Voor Portefeuille ".$this->portefeuille.", ".substr($supercategorie,3,4)." jaar ".round($performanceJaar,1)." <> ".round($attributieWaarden['jaar'][substr($supercategorie,3,4)],1)."');</script>";ob_flush();
  }
}
      $regels['overzicht'][] = array('supercategorieTotaal',
		                              array('omschrijving'=>'Totaal '.$categorieOmschrijving[$supercategorie] ,
		                                    'actuelePortefeuilleWaardeEuro'=>$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'perc'=>$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro']/$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro']*100,
		                                    '',
		                                    'janWaardeEur' =>$supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'],
		                                    'mutatieWaarde'=>$supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'],
                                         
                                        'storting'=>$supercategorienStortingenontrekkingenJaar[$supercategorie],
		                                    'resultaat'=>$supercategorienBeleggingsresultaatJaar[$supercategorie],
		                                    'resultaatgewogen'=>$performanceJaar   
                                        ));

		 $regels['mutaties'][] = array('supercategorieTotaal',
		                              array('Totaal '.$categorieOmschrijving[$supercategorie],
		                                    $supercategorieTotalen[$this->perioden['eind']][$supercategorie]['actuelePortefeuilleWaardeEuro'] - $supercategorieTotalen[$this->perioden['start']][$supercategorie]['actuelePortefeuilleWaardeEuro'] ,
		                                    $supercategorienStortingenontrekkingen[$supercategorie],
		                                    $supercategorienBeleggingsresultaat[$supercategorie],
		                                    $performance ,
		                                    '',
		                                    '',
		                                    $supercategorienStortingenontrekkingenJaar[$supercategorie],
		                                    $supercategorienBeleggingsresultaatJaar[$supercategorie],
		                                    $performanceJaar ));
		  $regelCount[] = count($regels['overzicht']);
     }
   }

  $this->pdf->SetFillColor(234,230,223);

   if(count($this->fondsIndex3)>0)
     $regels['index'][0]=array();
	 if(count($this->fondsIndex2)>0)
	   $regels['index2'][0]=array();
   unset($regels['mutaties']);
   foreach ($regels as $categorie=>$waarden)
   {

    $kopSuper = 0;
     switch ($categorie)
     {
       case "overzicht":
         $this->pdf->rapport_deel = 'overzicht';
         $this->pdf->rapport_titel = "Overzicht beleggingen";
         $this->pdf->addPage();
       break;
       case "mutaties":
         $this->pdf->rapport_deel = 'mutaties';
         $this->pdf->rapport_titel = "Mutaties gedurende verslagperiode";
         $this->pdf->addPage();
       break;
       case "index":
         $this->pdf->rapport_deel = 'index';
         $this->pdf->rapport_titel = "";
         $this->pdf->addPage();
       break;
			 case "index2":
				 $this->pdf->rapport_deel = 'index';
				 $this->pdf->rapport_titel = "";
				 $this->pdf->addPage();
			 break;
     }

     foreach ($waarden as $data)
     {
       switch ($data[0])
       {
        case "kopSuper":
          if($this->pdf->getY() >65)
            if($this->pdf->getY() + ($regelCount[$kopSuper] - $regelCount[$kopSuper-1]) * 4 >180)
              $this->pdf->addPage();
           $this->pdf->SetFont($this->pdf->rapport_font,'BI',($this->pdf->rapport_fontsize+2));
          $kopSuper++;
         break;
         case "kopHoofd":
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
         break;
         case "kopCategorie":
          $this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
          $data[1][0] = "  ".$data[1][0];
         break;
         case "fonds":
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $data[1][0] = "  ".$data[1][0];
         break;
         case 'categorieTotaal':
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
         break;
         case 'hoofdcategorieTotaal':
          $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
         break;
         case 'supercategorieTotaal':
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
         break;
       }

       switch ($categorie)
       {
         case "overzicht":
           if(substr($data[1]['omschrijving'],0,6) == 'Totaal')
           {
             $this->pdf->CellBorders = array('','','UU','','UU','UU',"UU",'','','');
             $limit=0;
           }
           elseif($data[1]['regio'] == 'Subtotaal')
           {
             $limit=0;
           }
           else
           {
             $this->pdf->CellBorders = array();
             $limit=100;
           }



           $this->pdf->row(array($data[1]['omschrijving'],
                           $data[1]['regio'],
                           $this->formatGetal($data[1]['actuelePortefeuilleWaardeEuro'],0),
                           $this->formatGetal($data[1]['perc'],1,true,$limit),
                           $this->formatGetal($data[1]['janWaardeEur'],0),
                           
                           $this->formatGetal($data[1]['storting'],0),
                           $this->formatGetal($data[1]['resultaat'],0),
                           $this->formatGetal($data[1]['resultaatgewogen'],1,true)
                           ));
           if(substr($data[1]['omschrijving'],0,6) == 'Totaal') //Witregel na Totaal
             $this->pdf->ln(8);
         break;
         case "mutaties":
           if(substr($data[1][0],0,6) == 'Totaal')
             $this->pdf->CellBorders = array('','','','UU','','','','','UU','');
           else
             $this->pdf->CellBorders = array();
           $this->pdf->row(array($data[1][0],$this->formatGetal($data[1][1],0),$this->formatGetal($data[1][2],0),$this->formatGetal($data[1][3],0),$this->formatGetal($data[1][4],1,true),$this->formatGetal($data[1][5],1,true),$this->formatGetal($data[1][6],0),$this->formatGetal($data[1][7],0),$this->formatGetal($data[1][8],0),$this->formatGetal($data[1][9],1,true)));

           if(substr($data[1][0],0,6) == 'Totaal')//Witregel na Totaal
             $this->pdf->ln(8);
         break;
         case "index":
          foreach ($this->fondsIndex3 as $type=>$indexData)
          {
            $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+2);
            $this->pdf->setY(40);
            if($type=='speciale')
            {
              $this->pdf->SetWidths(array(150));
              $this->pdf->SetAligns(array('L','L','C','C','C','C','C'));
              $this->pdf->row(array("Beleggingsresultaten (in EUR) gerapporteerd door vermogensbeheerders"));
              $this->pdf->ln(2);
              $this->pdf->SetWidths(array(72,70,21,21,21,21,21));
              $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
              $this->pdf->fillCell = array(1,1,1,1,1,1,1);
              $this->pdf->CellBorders=array(array('L','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'));
              $this->pdf->row(array("\n ","\n ","Laatste\nkwartaal","Vanaf begin\ndit jaar","Afgelopen\n3 jaar (p/jr)","Afgelopen\n5 jaar (p/jr)","Afgelopen\n10 jaar (p/jr)"));
              $this->pdf->CellBorders=array(array('L'),array('R'),array('R'),array('R'),array('R'),array('R'),array('R'));
            }
            $this->pdf->fillCell=array();
            foreach ($indexData as $supercategorie=>$supercategorieData)
            {
               $this->pdf->row(array('','','','','','','',''));
              if($categorieOmschrijving[$supercategorie] != '')
              {
                 $this->pdf->SetFont($this->pdf->rapport_font,'BI',($this->pdf->rapport_fontsize+2));
                if($type=='speciale')
                  $this->pdf->row(array($categorieOmschrijving[$supercategorie],'','','','','',''));
              }
              foreach ($supercategorieData as $hoofdcategorie=>$hoofdcategorieData)
              {
                if($categorieOmschrijving[$hoofdcategorie] != '')
                {
                   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
                  if($type=='speciale')
                    $this->pdf->row(array($categorieOmschrijving[$hoofdcategorie],'','','','','',''));
                }
                foreach ($hoofdcategorieData as $categorie=>$fondsData)
                {
                  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
                  foreach ($fondsData as $fonds=>$waarden)
                  {
                    foreach ($waarden as $key=>$value)
                      if($value==-100)
                        $waarden[$key]='';

                    if($type=='speciale')
                      $this->pdf->row(array("  ".$this->fondsOmschrijvingen[$fonds],$this->fondsExtraInfo[$fonds],$this->formatGetal($waarden['kwartaal'],1,true),$this->formatGetal($waarden['jaar'],1,true),$this->formatGetal($waarden['driejaar'],1,true),$this->formatGetal($waarden['vijfjaar'],1,true),$this->formatGetal($waarden['tienjaar'],1,true)));

                  }
                }
                $this->pdf->row(array('','','','','','','',''));
              }
            }
            if($type=='speciale')
            {
              $this->pdf->CellBorders=array('T','T','T','T','T','T','T');
              $this->pdf->row(array('','','','','','',''));
            }
            $this->pdf->CellBorders=array();
          }
       break;
				 case "index2":
					 foreach ($this->fondsIndex2 as $type=>$indexData)
					 {
						 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize+2);
						 $this->pdf->setY(40);
							 $this->pdf->SetWidths(array(135));
							 $this->pdf->SetAligns(array('L','L','C','C','C','C','C'));
							 $this->pdf->row(array("Netto resultaten indices (in EUR)"));
							 $this->pdf->ln(2);
							 $this->pdf->SetWidths(array(72,70,21,21,21,21,21));
							 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
							 $this->pdf->fillCell = array(1,1,1,1,1,1,1);
							 $this->pdf->CellBorders=array(array('L','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'));
							 $this->pdf->row(array("Benchmark\n "," \n ","Laatste\nkwartaal","Vanaf begin\ndit jaar","Afgelopen\n3 jaar (p/jr)","Afgelopen\n5 jaar (p/jr)","Afgelopen\n10 jaar (p/jr)"));
							 $this->pdf->CellBorders=array(array('L'),array('R'),array('R'),array('R'),array('R'),array('R'),array('R'));
						 $this->pdf->fillCell=array();
						 foreach ($indexData as $supercategorie=>$supercategorieData)
						 {
							 $this->pdf->row(array('','','','','','',''));
							 if($categorieOmschrijving[$supercategorie] != '')
							 {
								 $this->pdf->SetFont($this->pdf->rapport_font,'BI',($this->pdf->rapport_fontsize+2));
								 $this->pdf->row(array($categorieOmschrijving[$supercategorie],'','','','','',''));
							 }
							 foreach ($supercategorieData as $hoofdcategorie=>$hoofdcategorieData)
							 {
								 if($categorieOmschrijving[$hoofdcategorie] != '')
								 {
									 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
									 $this->pdf->row(array($categorieOmschrijving[$hoofdcategorie],'','','','','',''));
								 }
								 foreach ($hoofdcategorieData as $categorie=>$fondsData)
								 {
									 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
									 foreach ($fondsData as $fonds=>$waarden)
									 {
										 foreach ($waarden as $key=>$value)
											 if($value==-100)
												 $waarden[$key]='';

											 $this->pdf->row(array("  ".$this->fondsOmschrijvingen[$fonds],$this->fondsExtraInfo[$fonds],$this->formatGetal($waarden['kwartaal'],1,true),$this->formatGetal($waarden['jaar'],1,true),$this->formatGetal($waarden['driejaar'],1,true),$this->formatGetal($waarden['vijfjaar'],1,true),$this->formatGetal($waarden['tienjaar'],1,true)));
									 }
								 }
								 $this->pdf->row(array('','','','','','',''));
							 }
						 }
						 $this->pdf->CellBorders=array('T','T','T','T','T','T','T');
						 $this->pdf->row(array('','','','','','',''));

						 $this->pdf->CellBorders=array();
					 }
					 break;
       }
     }
   }

		$DB = new DB();
		foreach ($periodeTotalen as $datum=>$waarde)
		{
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
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

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
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
	}


	function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum)
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
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
		 1 $koersQuery as Rapportagekoers ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
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


		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

	  foreach ($buffer as $mutaties)
		{
			$mutaties[Aantal] = abs($mutaties[Aantal]);
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


			switch($mutaties[Transactietype])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

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

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties[Aantal] * -1) * $historie[historischeWaarde]      * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar          = ($mutaties[Aantal] * -1) * $historie[beginwaardeLopendeJaar] * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				else
				{
					$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
				  $beginditjaar         = $mutaties[Aantal]        * $historie[beginwaardeLopendeJaar]  * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
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

				if($historie[voorgaandejarenActief] == 0)
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
		return $data;
	}

	function getRekeningMutaties($rekening,$van,$tot)
	{
	  $db= new DB();
	  $query = "
	  SELECT
  SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	FROM
	Rekeningmutaties ,  Rekeningen

	WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Rekening =  '$rekening'  AND
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
		  $DB=new DB();
		  $query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
      $DB->SQL($query); //echo "$fonds $query  <br>\n";
      $DB->Query();
      $totaalWaarde = $DB->NextRecord();

		  return $totaalWaarde['totaalWaarde'];
		}


  function fondsPerformance2($fonds,$datumBegin,$datumEind,$debug=false)
  {
    global $__appvar;
	  $DB=new DB();
	  $datum=$this->getKwartalen(db2jul($datumBegin),db2jul($datumEind));
    $totaalPerf = 100;
    foreach ($datum as $periode)
    {
	    $datumBegin = $periode['start'];
	    $datumEind = $periode['stop'];


  //  if(substr($this->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
  //    $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
  //  else
      $weegBegindatum=$datumBegin;
      $weegEinddatum=$datumEind;
  //  $datumEind=$this->rapportageDatum;


      $fondsQuery = 'Fonds';

      if(is_array($fonds))
	      $fondsenWhere = " IN('".implode('\',\'',$fonds)."') ";
      else
        $fondsenWhere = " IN('$fonds') ";

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];


       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumEind' AND
               (TijdelijkeRapportage.rekening $fondsenWhere OR TijdelijkeRapportage.fonds $fondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];

	    if($beginwaarde == 0 && $eindwaarde <> 0)
	    {
	      $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 1 DAY as Boekdatum , Rekeningmutaties.Boekdatum as weegBegindatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE (Rekeningmutaties.Fonds $fondsenWhere OR Rekeningmutaties.Rekening $fondsenWhere ) AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $start = $DB->NextRecord();

	      if($start['Boekdatum'] != '')
	        $datumBegin = $start['Boekdatum'];

	      $weegBegindatum=$start['weegBegindatum'];

	      //  echo " $datumBegin   $fondsenWhere <br>\n";

	    }

 	    if($eindwaarde == 0 && $beginwaarde <> 0)
 	    { // + INTERVAL 1 DAY
 	      $query = "SELECT Rekeningmutaties.Boekdatum as Boekdatum, Rekeningmutaties.Boekdatum as weegEinddatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE (Rekeningmutaties.Fonds $fondsenWhere OR Rekeningmutaties.Rekening $fondsenWhere ) AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $eind = $DB->NextRecord();
	      if($eind['Boekdatum'] != '')
	        $datumEind = $eind['Boekdatum'];

	      $weegEinddatum=$eind['weegEinddatum'];
 	    }
      
	     $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$weegEinddatum."') - TO_DAYS('".$weegBegindatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))*-1  AS totaal ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Grootboekrekeningen.Opbrengst=0 AND Grootboekrekeningen.Kosten =0) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               Rekeningmutaties.Rekening $fondsenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();


       $queryRekeningDirecteKostenOpbrengsten = "SELECT
                 SUM(((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$weegEinddatum') - TO_DAYS('$weegBegindatum')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen,
                 SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal
                 FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                 JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                 WHERE
                 (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND   Rekeningmutaties.Fonds = '' AND
                 Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                 Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                 Rekeningmutaties.Boekdatum <= '$datumEind' AND
                 Rekeningmutaties.Rekening $fondsenWhere  ";
       $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
       $DB->Query();
       $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT SUM(((TO_DAYS('$datumEind') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('$weegEinddatum') - TO_DAYS('$weegBegindatum')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen,
                       SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal
                FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                Rekeningmutaties.Fonds $fondsenWhere";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten); //echo "$fonds $query  <br>\n";
       $DB->Query();
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$weegEinddatum."') - TO_DAYS('".$weegBegindatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Fonds $fondsenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$query <br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();

       $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
   //    $AttributieStortingenOntrekkingen['totaal'] +=$AttributieStortingenOntrekkingenRekening['totaal'];
   	  $query2 = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	              FROM Rekeningmutaties,Rekeningen
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Rekeningen.Rekening $fondsenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind'";

	     $DB->SQL($query2);
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] -=$data['totaal'];




       $directeKostenOpbrengsten['totaal'] = $RekeningDirecteKostenOpbrengsten['totaal'] + $FondsDirecteKostenOpbrengsten['totaal'];
       $directeKostenOpbrengsten['gewogen'] = $RekeningDirecteKostenOpbrengsten['gewogen'] + $FondsDirecteKostenOpbrengsten['gewogen'];
       
       if($beginwaarde == 0 && $eindwaarde == 0)
       {
         $query="SELECT (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers)-(Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers) as waarde
          FROM Rekeningen
          INNER JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
          INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
          WHERE Grootboekrekeningen.FondsAanVerkoop=1 AND Rekeningmutaties.Fonds $fondsenWhere AND Rekeningen.Portefeuille='".$this->portefeuille."'
          order by boekdatum";
	      $DB->SQL($query);
	      $DB->Query();
	      $start = $DB->NextRecord();
        $beginstorting=$start['waarde'];
        $gemiddelde = $beginstorting; 
       }
       else
         $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'] - $directeKostenOpbrengsten['gewogen'] ;

 	     if($weegEinddatum != $datumEind)
       {
         //echo "$fondsenWhere datumAanpasing: $weegEinddatum != $datumEind <br>\n";
         $gemiddelde = $beginwaarde;
       }
      $performance = ((($eindwaarde - $beginwaarde) + $AttributieStortingenOntrekkingen['totaal'] + $directeKostenOpbrengsten['totaal'] ) / $gemiddelde) * 100;


	  $debug=false;
 //$debug=true;
      if($debug)// || trim($fondsenWhere)=="IN('OTUS MAGA SC EUR')")
      {
        
        echo $queryAttributieStortingenOntrekkingen."<br>\n  <br>\n";
   //    echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryRekeningDirecteKostenOpbrengsten <br>\n " ;
    //   echo "$queryRekeningDirecteKostenOpbrengsten <br>\n $queryFondsDirecteKostenOpbrengsten <br>\n";
     //  echo "$queryAttributieStortingenOntrekkingenRekening <br>\n $queryAttributieStortingenOntrekkingen <br>\n";
     //  listarray($directeKostenOpbrengsten);
       listarray($AttributieStortingenOntrekkingen);
       
       echo "$fondsenWhere $datumBegin -> $datumEind <br>\n";
       echo "gemiddelde= 	 $gemiddelde = begin $beginwaarde -  gewogenSo ".$AttributieStortingenOntrekkingen['gewogen']." - gewogenDko ".$directeKostenOpbrengsten['gewogen']."<br>\n " ;
       echo "   $performance = ((($eindwaarde - $beginwaarde) + ".$AttributieStortingenOntrekkingen['totaal']." + ".$directeKostenOpbrengsten['totaal']." ) / $gemiddelde) * 100;	<br>\n";
        echo "    <br>\n" ;
       ob_flush();


      echo ($totaalPerf  * (100+$performance)/100)." = ($totaalPerf  * (100+$performance)/100) <br>\n";
      }
      $totaalPerf = ($totaalPerf  * (100+$performance)/100) ;

    }

   if($debug)
     echo "rvv $datumBegin,$datumEind perftotaal ".($totaalPerf-100) ."<br>\n ";

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


}
?>