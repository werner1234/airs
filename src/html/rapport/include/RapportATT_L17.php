<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
File Versie					: $Revision: 1.22 $

$Log: RapportATT_L17.php,v $
Revision 1.22  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.21  2009/11/08 14:11:55  rvv
*** empty log message ***

Revision 1.20  2009/03/14 13:25:06  rvv
*** empty log message ***

Revision 1.19  2008/11/25 08:39:21  rvv
*** empty log message ***

Revision 1.18  2008/07/15 11:21:30  rvv
*** empty log message ***

Revision 1.17  2008/07/15 11:08:40  rvv
*** empty log message ***

Revision 1.16  2008/07/10 15:33:15  rvv
Forcering 1-1 waarden

Revision 1.15  2008/07/08 09:57:57  rvv
Index op 1 decimaal

Revision 1.14  2008/07/07 09:08:05  rvv
Zonder portefeuille gekoppelde index

Revision 1.13  2008/07/03 13:10:15  rvv
Indexwaarden verwijderd

Revision 1.12  2008/07/03 06:27:24  rvv
html_entity_decode toegevoegd

Revision 1.11  2008/06/27 10:27:45  rvv
*** empty log message ***

Revision 1.10  2008/05/16 08:13:26  rvv
*** empty log message ***

Revision 1.9  2008/05/06 10:24:17  rvv
*** empty log message ***

Revision 1.8  2008/03/27 09:49:58  rvv
*** empty log message ***

Revision 1.7  2008/03/27 08:31:58  rvv
*** empty log message ***

Revision 1.6  2008/03/18 09:56:48  rvv
*** empty log message ***

Revision 1.5  2008/01/23 07:39:13  rvv
*** empty log message ***

Revision 1.4  2007/11/16 11:25:30  rvv
*** empty log message ***

Revision 1.2  2007/10/04 12:09:12  rvv
*** empty log message ***

Revision 1.1  2007/09/26 15:31:29  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportATT_L17
{
	function RapportATT_L17($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	   if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	   {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      $this->extraVulling = true;
	   }
	  }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}



	function printRisico($risicoTotaal, $actueleWaardePortefeuille)
  {

    $cellw1=48;
    $cellw2=44;
    $extraMarge=0;

		$query = "SELECT  ".
		" Risicoklassen.Risicoklasse, ".
		" Risicoklassen.Minimaal, ".
		" Risicoklassen.Maximaal ".
		" FROM Risicoklassen, Portefeuilles WHERE ".
		" Risicoklassen.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
		" Portefeuilles.Portefeuille = '".$this->portefeuille."' AND ".
		" Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse " ;

		$db = new DB();
		$db->SQL($query);
		$db->Query();
		$risicodata = $db->nextRecord();
		$risicoScore = $risicoTotaal / ($actueleWaardePortefeuille/100);
		$this->pdf->SetX($this->pdf->marge+$extraMarge);
		$this->pdf->Cell($cellw1,4, vertaalTekst("Risico range ",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($cellw2,4, "min. ".$risicodata[Minimaal]." % max. ".$risicodata[Maximaal].' %', 0,1, "R");
		$this->pdf->SetX($this->pdf->marge+$extraMarge);
		$this->pdf->Cell($cellw1,4, vertaalTekst("Risico score ",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($cellw2,4, $this->formatGetal($risicoScore,2).' %', 0,1, "R");
		$this->pdf->ln(1);
    $this->pdf->Line($this->pdf->marge+$extraMarge, $this->pdf->GetY(),$this->pdf->marge+$extraMarge+$cellw1+$cellw2,$this->pdf->GetY());
  }


	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

	// if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	// {
	//  $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
  //  vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
	// }


		$DB = new DB();

		$query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie";
		$DB->SQL($query);
		$DB->Query();

		while($categorie = $DB->nextRecord())
		{
		$categorieKop[$categorie['AttributieCategorie']]=$categorie['Omschrijving'];
		$categorien[]=$categorie['AttributieCategorie'];

		}

		$categorieKop['Liquiditeiten']='Liquiditeiten';
		$categorieKop['Totaal']='Totaal';
		$categorien[]='Liquiditeiten';
    $categorien[] = 'Totaal';

		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');

		$this->pdf->AddPage();

		$this->pdf->ln();

$query ="SELECT
		SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) AS subcredit,
		SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) AS subdebet,
		AttributiePerGrootboekrekening.AttributieCategorie,
 		Rekeningmutaties.Grootboekrekening,
 		Grootboekrekeningen.Kosten ,
 		Grootboekrekeningen.Opbrengst ".
		" FROM (Rekeningen, Portefeuilles , AttributiePerGrootboekrekening)
		Right JOIN Rekeningmutaties  ON  Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening  ,Grootboekrekeningen
		WHERE Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND
		Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 		Rekeningen.Portefeuille = '".$this->portefeuille."' AND
 		Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		Rekeningmutaties.Verwerkt = '1' AND
		Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
 		Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND
 		Rekeningmutaties.GrootboekRekening <> 'FONDS'
		GROUP BY Rekeningmutaties.Grootboekrekening	";

		$DB->SQL($query);
		$DB->Query();

while($attributieGrootboek = $DB->nextRecord())
{
  $attributieCategorie = $attributieGrootboek['AttributieCategorie'];

  if ($attributieCategorie == '')
    $attributieCategorie = 'geen';

  if($attributieCategorie == 'geen' && $attributieGrootboek['Kosten'] == '1' )
   {
   $grootboekKostenTotaal += $attributieGrootboek['subdebet'];
   $grootboekKostenTotaal -= $attributieGrootboek['subcredit'];
   }
  elseif ($attributieCategorie == 'geen')
   {
   $grootboekOverige += $attributieGrootboek['subcredit'];
   $grootboekOverige -= $attributieGrootboek['subdebet'];
   }
  if($attributieGrootboek['Opbrengst'] == '1' )
   {
   $grootboekOpbrengstTotaal += $attributieGrootboek['subcredit'];
   $grootboekOpbrengstTotaal -= $attributieGrootboek['subdebet'];
   }

  if($attributieGrootboek['Opbrengst'] == '1' | $attributieGrootboek['Kosten'] == '1' )
  {
  $attributieCategorieGrootboek[$attributieCategorie] -= $attributieGrootboek['subdebet'];
  $attributieCategorieGrootboek[$attributieCategorie] += $attributieGrootboek['subcredit'];
  }
}

foreach ($categorien as $categorie)
{
  if ($categorie == 'Totaal')
  {
    $attributieQuery = '';
  }
  elseif ($categorie == 'Liquiditeiten')
  {
    $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '' AND ";
  }
  else
  {
    $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";
  }

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " $attributieQuery ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " $attributieQuery ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

		$waardeEind[$categorie]				= $totaalWaarde[totaal];
		$waardeBegin[$categorie] 			 	= $totaalWaardeVanaf[totaal];
		$waardeMutatie[$categorie] 	   	= ($waardeEind[$categorie] / $this->pdf->ValutaKoersEind)  - ($waardeBegin[$categorie] / $this->pdf->ValutaKoersBegin);

		$tmp = getAttributieStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$categorie,$this->pdf->rapportageValuta);
		if($categorie == 'Totaal')
		{
		$stortingen[$categorie]	   += $tmp['subcredit'];
	  $stortingen['Liquiditeiten'] +=  $tmp['subcredit'];

		$onttrekkingen[$categorie] 	 	+= $tmp['subdebet'];
	  $onttrekkingen['Liquiditeiten']	+= $tmp['subdebet'];
		}
		else
		{
		$stortingen[$categorie]	+= 	$tmp['subdebet'];
	  $stortingen['Liquiditeiten']	+= 	  $tmp['subcredit'];

		$onttrekkingen[$categorie]	+= $tmp['subcredit'];
	  $onttrekkingen['Liquiditeiten']	+= $tmp['subdebet'];
		}

		// ***************************** einde ophalen data voor afdruk ************************ //
}


$portefeuilleTotaalWaarde = $waardeEind['Totaal'];
//		$rendementProcent['Liquiditeiten'] = attributiePerformance($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, 1,'Liquiditeiten',($waardeEind['Liquiditeiten']/$waardeEind['Liquiditeiten']),$vastrenteWeging);


		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


		$this->pdf->SetX(108);

		$this->pdf->switchFont('3');
		$this->pdf->MultiCell(170,4, "Beleggingsresultaat");

    $this->pdf->ln(2);

		$this->pdf->widthB = array(100,100,65,15);
		$this->pdf->alignB = array('L','L','R','R','R');

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->switchFont('1');
		$this->pdf->fillCell = array(0,1,1,1);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat per vermogenscategorie",$this->pdf->rapport_taal),"",""));
		$this->pdf->fillCell = array();

		$this->pdf->switchFont('fonds');
		$this->pdf->widthB = array(100,30,20,30,100);
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->CellBorders = array('','U','U','U','U');


foreach ($categorien as $categorie)
{
		if ($categorie != 'Totaal')
		{
		$resultaatVerslagperiode[$categorie] += $directeOpbrengsten[$categorie];
		$resultaatVerslagperiode[$categorie] -= $toegerekendeKosten[$categorie];

		if($categorie != 'Liquiditeiten')
		{
		  $grootboekKosten[$categorie] = $grootboekKostenTotaal / (($waardeEind['Totaal']-$waardeEind['Liquiditeiten'])/ $waardeEind[$categorie]);
		}

  	$onttrekkingen['Liquiditeiten']  -= $attributieCategorieGrootboek[$categorie] ;

		$toegerekendeKosten[$categorie] += $grootboekKosten[$categorie];
		$stortingen['Liquiditeiten']  -= $grootboekKosten[$categorie];
 		$directeOpbrengsten[$categorie]	+= 	$attributieCategorieGrootboek[$categorie] ;

	  }
  	$resultaatVerslagperiode[$categorie] = $waardeMutatie[$categorie] - $stortingen[$categorie] + $onttrekkingen[$categorie] + $directeOpbrengsten[$categorie] - $toegerekendeKosten[$categorie];
//echo 	$resultaatVerslagperiode[$categorie].' = '.$waardeMutatie[$categorie].' - '.$stortingen[$categorie].' + '.$onttrekkingen[$categorie].' + '.$directeOpbrengsten[$categorie].' - '.$toegerekendeKosten[$categorie].'<br>';

    if($categorie == 'Totaal')
    {
	   $rendementProcent[$categorie]  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'], $this->pdf->rapportageValuta);
	   $this->pdf->ln(1);
    }
	  elseif ($categorie != 'Liquiditeiten')
	  {
		  $rendementProcent[$categorie] = attributiePerformance($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, 1,$categorie,$this->pdf->rapportageValuta);
	  }


  if (round($rendementProcent[$categorie] != 0.00))
	  $rendementProcentText=$this->formatGetal($rendementProcent[$categorie],2)." %";
	else
	  $rendementProcentText ='';

	  $aandeel =  $waardeEind[$categorie]/$portefeuilleTotaalWaarde *100;

	  if ($categorie == "Totaal")
	  {
	        $this->pdf->CellBorders = array();
	        $this->pdf->fillCell = array(0,1,1,1,1);
	    		$this->pdf->switchFont('totaal');
	  }
    $this->pdf->row(array('',
                       $categorieKop[$categorie],
                       $this->formatGetal($aandeel,1)." % ",
                       $this->formatGetal($resultaatVerslagperiode[$categorie],2),''));//,$rendementProcentText

  }
  $this->pdf->fillCell= array();
    if($this->pdf->getY() > 150 )
   {
     $rendamentVolgendePagina = true;
   }
   else
   {
     $startRentamentY = $this->pdf->getY();
   }

   $VorigStartJaar = $RapStartJaar -1;

//$this->berekenIndexWaarden($this->rapportageDatumVanaf,$this->rapportageDatum,$this->portefeuille);




 $index=new indexHerberekening();
$indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille,$specifiekeIndex);
//$indexData = $index->getWaardenATT($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);exit;


foreach ($indexData as $data)
{
 if($data['datum'] == '0000-00-00')
 {

 }
 else
 {
 $rendamentWaarden[] = $data;
 $grafiekData['Datum'][] = $data['datum'];
 $grafiekData['Index'][] = $data['index'];
 $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
 $barGraph['Index'][]= $data['performance'];
 $barGraph['Datum'][] = $data['datum'];
 }
}
//listarray($barGraph);
$barGraph['Datum'][]="$RapStartJaar-12-01";
$grafiekData['Datum'][]="$RapStartJaar-12-01";

		  if (count($grafiekData) > 1)
		  {
		 $this->pdf->switchFont('4');
		 $extraMarge =1;
     $this->pdf->SetXY(8,31)		;
     $this->pdf->Cell(0, 5, 'Beleggingsresultaat op maandbasis', 0, 1);
     $this->pdf->Line($this->pdf->marge+$extraMarge, $this->pdf->GetY(),$this->pdf->marge+$extraMarge+90,$this->pdf->GetY());
  	 $this->pdf->SetXY(14,90)		;  //95
      $this->pdf->VBarDiagram(85,45,$barGraph,'',array(222,203,16),'',10,12); //50

      $this->pdf->SetXY(8,93)		;//98
      $this->pdf->switchFont('4');
      $this->pdf->Line($this->pdf->marge+$extraMarge, $this->pdf->GetY(),$this->pdf->marge+$extraMarge+90,$this->pdf->GetY());

      $this->pdf->SetXY(8,99);//104

		  $this->pdf->Cell(0, 5, 'Vermogens ontwikkeling', 0, 1);
		  $this->pdf->Line($this->pdf->marge+$extraMarge, $this->pdf->GetY(),$this->pdf->marge+$extraMarge+90,$this->pdf->GetY());

		  $this->pdf->SetXY(15,107)		;//112
      $valX = $this->pdf->GetX();
      $valY = $this->pdf->GetY();
      $this->pdf->LineDiagram(84, 45, $grafiekData,array(49,93,33),0,0,10,5,1);//50
      $this->pdf->SetXY($valX, $valY + 80);
		  }

		  $this->pdf->SetXY(8, 155);//165





		$risicoWaarde=  $this->pdf->bepaalRisicoWaarde($this->portefeuille,$this->rapportageDatum);
		$this->pdf->ln();
	//	echo "$risicoWaarde, -> ".$portefeuilleTotaalWaarde."<br>";
	 $this->pdf->switchFont('4');
		  $this->printRisico($risicoWaarde,$portefeuilleTotaalWaarde);

		   $this->printIndexVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);


		  $this->pdf->SetXY($valX, $valY + 80);

		  if($rendamentVolgendePagina)
		   $this->pdf->AddPage();
		  else
		   $this->pdf->SetY($startRentamentY);


		   if(count($rendamentWaarden) > 0)
		  {
		    $this->pdf->widthA = array(100,55,25,25,25,25,25);
		    $this->pdf->alignA = array('L','L','R','R','R','R','R');

		     $this->pdf->SetWidths($this->pdf->widthA);
		     $this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->switchFont('1');
		$this->pdf->fillCell = array(0,1,1,1,1,1,1);


		     $this->pdf->ln(4);

		     $this->pdf->row(array('','Rendement','','','','',''));



		     $this->pdf->ln(1);
		 		$this->pdf->switchFont('2');
				 $this->pdf->row(array('',
		                           "PERIODE\n ",
		                           "VERMOGEN\n ",
		                           "STORTINGEN/\nONTTREKKINGEN",
		                           "BELEGGINGS-\nRESULTAAT",
		                           "IN%\n ",
		                           " \n "));//BENCHMARK
		    $sumWidth = array_sum($this->pdf->widthA);
		    $this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0],
	                    $this->pdf->GetY(),
	                    $this->pdf->marge+$sumWidth,
	                    $this->pdf->GetY());
        $this->pdf->ln(1);
        $n=1;

        $this->pdf->fillCell = array();
        $this->pdf->switchFont('fonds');
        $this->pdf->SetFont($this->pdf->rapport_font,'R',8);

        $this->pdf->CellBorders = array('','U','U','U','U','U','U','U');
        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
		      $this->pdf->row(array('',
		                           date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['performance'],2),
		                           ''));//$this->formatGetal($row['specifiekeIndexPerformance'],2)

		                           $totaalWaarde = $row['waardeHuidige'];
		                           $totaalResultaat += $row['resultaatVerslagperiode'];
		                           $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
		                           $totaalRendament = $row['index'];//($totaalRendament  * (100+$row['performance'])/100); //+= $barGraph['Index'][$n];
		                           $totaalRendamentIndex = $row['specifiekeIndex']; //= $row['specifiekeIndexPerformance'];//($totaalRendamentIndex  * (100+$barGraph['benchmarkIndex'][$n])/100); //+= $barGraph['benchmarkIndex'][$n];

		    $n++;
		    }
		    $this->pdf->switchFont('totaal');
		    $this->pdf->CellBorders = array();
        $this->pdf->ln(1);
        $this->pdf->fillCell = array(0,1,1,1,1,1,1,1,1);
		    $this->pdf->row(array('',
		                          'Totaal over periode',
		                           $this->formatGetal($totaalWaarde,2),
		                           $this->formatGetal($totaalStortingenOntrekkingen,2),
		                           $this->formatGetal($totaalResultaat,2),
		                           $this->formatGetal($totaalRendament-100,2),
		                           " "));//$this->formatGetal($totaalRendamentIndex-100,2)

		  }

if(round($totaalResultaat) != round($resultaatVerslagperiode['Totaal']))
{
  // echo "<script  type=\"text/JavaScript\">alert('Beleggingsresultaat totaal (".(round($resultaatVerslagperiode['Totaal'],2)).") komt niet overeen met perioden (".round($totaalResultaat,2).") verschil (".(round($resultaatVerslagperiode['Totaal'],2)-round($totaalResultaat,2)).") voor portefeuille ".$this->portefeuille."'); </script>";
  // ob_flush();
}

		  if($this->pdf->portefeuilledata['txtKoppeling'] !='')
		  {
		    $koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
		    $koppeling = str_replace('/ ','',$koppeling);

		    $query = "SELECT * FROM custom_txt WHERE
		    type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
		    field = '".$this->pdf->rapport_type."_".$koppeling."' AND
		    Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";

		    $DB->SQL($query);
        $txtData = $DB->lookupRecord();
		    $titel = $txtData['title'];
		    $briefData = $txtData['txt'];
		  }
		  else
		  {
		    $cfg=new AE_config();
		    $titel = $cfg->getData('ATTtitel');
		    $briefData = $cfg->getData('ATTopmaak');
		  }

		  $titel = html_entity_decode(strip_tags($titel));
      $briefData = html_entity_decode(strip_tags($briefData));

		$this->pdf->ln(10);
		$this->pdf->SetX(108);

		$this->pdf->switchFont('3');
	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);

	  $this->pdf->switchFont('fonds');
	   $this->pdf->fillCell = array();

	  if(strlen($briefData) > 0)
		{
		 $this->pdf->MultiCell(180,4,$briefData,0,'L');
		}
		// else
		//  $this->pdf->MultiCell(180,4,"\nvolus Mulesin seniam omnontius. clustifec resi con audefacion simus hoctam tius, tereoruripic fictabemer ut fachum tra? quampro iorur. Maetraci int? quid imus, sesceris aur. C. M. Gra atilicitatil vis nostero elum. Fuidemus. C. Marte tuastan icaterric re id di, quostrae, co contemulabem facie atius dertum nonscrum duconve atquam nonfendet; nera dii sere praverit, ut eterfen Itabefecur quam mei se cons con nost vit, pordi patus, sentea pote, quos oc fic tastilis pris bonte derei ca mo vessulici inprendet vest ilis ore cont. Valic venihilissa tam que ilis, sentere teris.",0,'L');
		$this->pdf->ln(10);


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
		}

	}

	function printIndexVergelijking($vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf->AutoPageBreak = false;
	 $this->pdf->rapport_style['printIndex'] = $this->pdf->rapport_style['3'];
	 $this->pdf->rapport_style['printIndex']['line'] = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,139,161));
	 $this->pdf->rapport_style['printIndex']['font']= array('style'=>'','fontSize'=>8);
	 $this->pdf->rapport_style['printIndex']['rowHeight'] = 5;
	 $this->pdf->switchFont('printIndex');
   $this->pdf->fillCell = array();
   $this->pdf->CellBorders = array();

		$DB  = new DB();
		$DB2 = new DB();

		$query = "SELECT Indices.Beursindex as fonds, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();

		while($data = $DB->nextRecord())
		 $indices[] = $data;

//  	$query = "SELECT Portefeuilles.SpecifiekeIndex as fonds, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $this->pdf->rapport_portefeuille."' ";
//		$DB->SQL($query);
//		$DB->Query();
//		while($data = $DB->nextRecord())
//		  $indices[] = $data;

		$this->pdf->ln();
		$this->pdf->SetX($this->pdf->marge);

		$width = array(20,8,8,8,4);

		$this->pdf->Cell($width[0]+$width[1]+$width[2],4, vertaalTekst("Index",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($width[3],4, vertaalTekst("Performance",$this->pdf->rapport_taal), 0,0, "R");
		$this->pdf->Cell($width[4],4,'  ', 0 ,0, "R");
		$this->pdf->Cell($width[0]+$width[1]+$width[2],4, vertaalTekst("Index",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($width[3],4, vertaalTekst("Performance",$this->pdf->rapport_taal), 0,1, "R");

		$n=0;
		foreach ($indices as $index)
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$index['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB->SQL($q);
			  $DB->Query();
			  $valutaKoersStart = $DB->LookupRecord();
		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$index['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB->SQL($q);
			  $DB->Query();
			  $valutaKoersStop = $DB->LookupRecord();
		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$index['fonds']."'  ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($q);
			$DB->Query();
			$koers1 = $DB->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$index['fonds']."'  ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($q);
			$DB->Query();
			$koers2 = $DB->LookupRecord();

			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$this->pdf->Cell($width[0]+$width[1]+$width[2],4, substr($index[Omschrijving],0,25), 0,0, "L");
		//	$this->pdf->Cell($width[1],4, $this->formatGetalLength($koers1[Koers],2,3), 0,0, "R");
		//	$this->pdf->Cell($width[2],4, $this->formatGetalLength($koers2[Koers],2,3), 0,0, "R");

	//	  $this->pdf->Cell($width[3],4, $this->formatGetalLength($performance,2,3), 0 , $n , "R");
		$this->pdf->Cell($width[3],4, $this->formatGetal($performance,1), 0 , $n , "R");


		  if ($n == 0)
		    $this->pdf->Cell($width[4],4,'  ', 0 ,0, "R");

	    $n++;
		  if($n > 1)
		    $n = 0;
		}

		$this->pdf->AutoPageBreak = true;
}

function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}

}
?>