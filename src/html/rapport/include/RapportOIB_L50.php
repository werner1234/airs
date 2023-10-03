<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/08/28 16:02:50 $
 		File Versie					: $Revision: 1.3 $

 		$Log: RapportOIB_L50.php,v $
 		Revision 1.3  2013/08/28 16:02:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/08/24 15:48:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/06/30 15:07:33  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/11/10 15:42:19  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/10/31 16:59:18  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/05/12 15:11:00  rvv
 		*** empty log message ***
 		
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportOIB_L50
{
	function RapportOIB_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Portefeuille in relatie tot de strategische weging";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function getOIBdata()
	{
	  global $__appvar;
    
 		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    

    
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$this->rapportageDatum."' AND ".
								 " portefeuille = '".$this->portefeuille."'"
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$portefwaarde = $DB->nextRecord();
		$portTotaal = $portefwaarde['totaal'];
		$this->portTotaal=$portTotaal;

		$query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Omschrijving,
hoofdCat.Omschrijving as HcatOmschrijving,
hoofdCat.Afdrukvolgorde as HcatVolgorde
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Inner Join Beleggingscategorien as hoofdCat ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdCat.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY HcatVolgorde,Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
      $this->categorien[$categorien['Beleggingscategorie']]=$categorien['Omschrijving'];
      $this->tabelData[$categorien['Hoofdcategorie']][$categorien['Beleggingscategorie']]=array();
      $this->tabelData[$categorien['Hoofdcategorie']][$categorien['Beleggingscategorie']]=array();
		}

		$query = "SELECT TijdelijkeRapportage.portefeuille,
		    TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving,
			TijdelijkeRapportage.beleggingscategorie as beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, TijdelijkeRapportage.Hoofdcategorie,
		   	TijdelijkeRapportage.HoofdcategorieOmschrijving ".
			" FROM TijdelijkeRapportage
			WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY beleggingscategorie ".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  $this->hoofdcategorien[$categorien['Hoofdcategorie']]=$categorien['HoofdcategorieOmschrijving'];
		  $this->categorien[$categorien['beleggingscategorie']]=$categorien['Omschrijving'];
		  if ($categorien['beleggingscategorie']=='')
		    $categorien['beleggingscategorie']='geenCat';
		  if ($categorien['Hoofdcategorie']=='')
		    $categorien['Hoofdcategorie']='geenHCat';

		  if($categorien['beleggingscategorie']=='Rente')
		  {
		    $categorien['Omschrijving']='Opgelopen Rente';
		    $categorien['beleggingscategorie']='Opgelopen Rente';
		  }
    $catogorieData[$categorien['beleggingscategorie']]['port']['waarde']+=$categorien['subtotaalactueel'];
    $hoofdCatogorieData[$categorien['Hoofdcategorie']]['port']['waarde']+=$categorien['subtotaalactueel'];

    $this->tabelData[$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['waarde']+=$categorien['subtotaalactueel'];
    $this->tabelData[$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['precentage']=$this->tabelData[$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['waarde']/$portTotaal;



		}

		foreach ($catogorieData as $categorie=>$data)
		{
		  if(isset($data['port']['waarde']))
		    $catogorieData[$categorie]['port']['procent']=$data['port']['waarde']/$portTotaal;

		}
		foreach ($hoofdCatogorieData as $categorie=>$data)
		{
		  if(isset($data['port']['waarde']))
		    $hoofdCatogorieData[$categorie]['port']['procent']=$data['port']['waarde']/$portTotaal;

		}


   $this->catogorieData=$catogorieData;
   $this->hoofdCatogorieData=$hoofdCatogorieData;
	}


	function standard_deviation($aValues)
  {
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= count($aValues)-1;
    return (float) sqrt($fVariance);
  }

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->widthA = array(40,80,40,40,20,20,20);
		$this->pdf->alignA = array('L','L','L','R','R','R','R');
		$this->pdf->AddPage();
		$this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

		// print categorie headers
	  $this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$DB = new DB();
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];

		$DB = new DB();
		$q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,ZorgplichtPerRisicoklasse.norm,ZorgplichtPerRisicoklasse.Zorgplicht,CategorienPerHoofdcategorie.Hoofdcategorie
    FROM
    ZorgplichtPerRisicoklasse
    Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    ORDER by CategorienPerHoofdcategorie.Hoofdcategorie";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		  $normData[$data['Hoofdcategorie']]=$data['norm'];

$q="SELECT
ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
CategorienPerHoofdcategorie.Hoofdcategorie,
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.norm
FROM ZorgplichtPerPortefeuille
JOIN ZorgplichtPerBeleggingscategorie  ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER by CategorienPerHoofdcategorie.Hoofdcategorie
";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		  $normData[$data['Hoofdcategorie']]=$data['norm'];



		$query = "SELECT Beleggingscategorie,Omschrijving as value FROM Beleggingscategorien";
    $DB->SQL($query);
    $DB->Query();
	  while($data=$DB->nextRecord())
      $omschrijving[$data['Beleggingscategorie']]=$data['value'];

	//	$portefeuilleData = berekenPortefeuilleWaarde($this->pdf->portefeuilledata['ModelPortefeuille'], $this->rapportageDatum);
	 // vulTijdelijkeTabel($portefeuilleData,"m".$this->pdf->portefeuilledata['ModelPortefeuille'],$this->rapportageDatum);
	  $this->getOIBdata();

		foreach ($this->catogorieData as $categorie=>$data)
	  {
	    if($data['port']['procent'] > 0)
	    {
	      $portefeuilleGrafiekDataDetail[$omschrijving[$categorie]]=round($data['port']['procent']*100,1);
	      $portefeuilleGrafiekKleurDetail[]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
	    }
	  }



    $this->pdf->setXY(230,50);
	  $this->pdf->PieChart(50, 50, $portefeuilleGrafiekDataDetail, '%l (%p)',$portefeuilleGrafiekKleurDetail);
//    $this->pdf->setXY(100,50);
//	  $this->pdf->PieChart(50, 50, $modelGrafiekData, '%l (%p)',$modelGrafiekKleur);
$this->pdf->underlinePercentage=0.8;
	  $this->pdf->setXY(8,60);
	  $this->pdf->SetWidths(array(20,50,30,20,20,20));
		$this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->SetFont($this->rapport_font, 'B', $this->rapport_fontsize);
    $this->pdf->row(array('','Belegd vermogen','in Euro','in %',"strategische\nverdeling",'verschil'));
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
		foreach ($this->tabelData as $hoofdCategorie=>$categorieData)
		{
		  foreach ($categorieData as $categorie=>$waarde)
      {
        if(round($waarde['waarde'],2) <> 0)
		      $this->pdf->row(array('',$this->categorien[$categorie],$this->formatGetal($waarde['waarde'],0),$this->formatGetal($waarde['precentage']*100,1)));
      }
		  $percentage=$this->hoofdCatogorieData[$hoofdCategorie]['port']['waarde']/$this->portTotaal*100;
		  $verschil[$hoofdCategorie]=$percentage-$normData[$hoofdCategorie];
		  $this->pdf->CellBorders = array('','','TS','TS','','');
		  $this->pdf->row(array('','Totaal '.$this->hoofdcategorien[$hoofdCategorie],$this->formatGetal($this->hoofdCatogorieData[$hoofdCategorie]['port']['waarde'],0),$this->formatGetal($percentage,1),
		                         $this->formatGetal($normData[$hoofdCategorie],0),$this->formatGetal($verschil[$hoofdCategorie],1)));
		  $this->pdf->CellBorders = array();
		  $this->pdf->ln(2);
		}
		$this->pdf->CellBorders = array('','','UU','UU','','');
		$this->pdf->row(array('','Belegd vermogen',$this->formatGetal($this->portTotaal,0),$this->formatGetal(100,1)));
    $this->pdf->CellBorders = array();


    $this->pdf->SetWidths(array(60,20,20));
	  foreach ($this->hoofdCatogorieData as $categorie=>$data)
	  {
	    $grafiekData[$omschrijving[$categorie]]=round($verschil[$categorie],1);
	    $grafiekKleurData[$omschrijving[$categorie]]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
	  }
	  $this->pdf->setXY(20,125);
	 // $this->BarDiagram(140, 60, $grafiekData, '%l (%p)',$grafiekKleurData,0,10);

    verwijderTijdelijkeTabel("m".$this->pdf->portefeuilledata['ModelPortefeuille'],$this->rapportageDatum);


  //$index=new indexHerberekening();
  //$indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

  $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);



foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
    foreach (array_reverse($this->categorien) as $categorie=>$omschrijving)
    {
      $waarde=$data['extra']['cat'][$categorie];
      
      if($categorie=='LIQ')
        $categorie='Liquiditeiten';

      $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
      if($waarde <> 0)
        $categorien[$categorie]=$categorie;
    }
  }
}

        $q="SELECT Beleggingscategorien.Beleggingscategorie, Beleggingscategorien.Omschrijving, Beleggingscategorien.Afdrukvolgorde
FROM Beleggingscategorien 
WHERE  Beleggingscategorien.Beleggingscategorie IN('".implode("','",$categorien)."') ORDER BY Beleggingscategorien.Afdrukvolgorde desc";
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=$data['Omschrijving'];
		}
    
		  if (count($barGraph) > 0)
		  {
		    $this->pdf->AddPage();
		    $this->pdf->SetXY($this->pdf->marge,45)		;//112
		    $this->pdf->Cell(0, 5, 'Vermogensverdeling', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		    $this->pdf->SetXY(15,180)		;//112
		    $this->VBarDiagram(270, 125, $barGraph['Index']);
		  }



	}


  
	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='')
	{
  $julBegin = db2jul($datumBegin);
  $julEind = db2jul($datumEind);

 	$eindjaar = date("Y",$julEind);
	$eindmaand = date("m",$julEind);
	$beginjaar = date("Y",$julBegin);
	$startjaar = date("Y",$julBegin);
	$beginmaand = date("m",$julBegin);

	$ready = false;
	$i=0;
	$vorigeIndex = 100;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$datum == array();

	while ($ready == false)
	{
	  if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
	  {
	    $ready = true;
		}
		else
		{
		  if($i==0)
        $datum[$i]['start']=$datumBegin;
	    else
	    {
		    $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	    }
	    $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
	    $i++;
		}
	}
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;

	$i=1;
	$indexData['index']=100;
	$db=new DB();
	foreach ($datum as $periode)
	{
	 	$indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille));
	 	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
 	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	  $data[$i] = $indexData;
    $i++;
	}
	return $data;
	}



	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();

    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  { 
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;


	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']=array('cat'=>$categorieVerdeling);
    return $data;

	}
  

  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = count($legenda);

      if($color == null)
      {
        $color=array(155,155,155);
      }


      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;

      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }

      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>