<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/02/22 09:55:14 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportOIB_L18.php,v $
 		Revision 1.5  2015/02/22 09:55:14  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/06/29 16:52:23  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/07/01 07:12:34  rvv
 		*** empty log message ***

 		Revision 1.2  2008/05/16 08:13:26  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L18
{
	function RapportOIB_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Vermogensverdeling";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageJulVanaf = db2jul($rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();

	}

	function formatGetal($waarde, $dec,$procent = false)
	{
	  if($waarde != '')
	  {
		  $waarde = number_format($waarde,$dec,",",".");
		  if($procent == true)
		    $waarde .= " %";
		    return $waarde;
	  }

	}


	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();


		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 " FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaardeBegin = $totaalWaarde[totaal];

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 " FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];


		$actueleWaardePortefeuille = 0;
		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving AS Omschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, ".
			" TijdelijkeRapportage.type ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.valutaVolgorde asc, TijdelijkeRapportage.beleggingscategorieVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  if($categorien['type'] == 'rekening')
		  {
		    $waarden[$categorien['valuta']]['Liquiditeiten'] += $categorien['subtotaalactueel'];

		  }
		  else
		  {
		    $waarden[$categorien['valuta']][$categorien['beleggingscategorie']] += $categorien['subtotaalactueel'];
		  }
		  if($categorien['beleggingscategorie'] == '')
		    $categorien['beleggingscategorie'] = 'Liquiditeiten';
	    $categorieOmschrijvingen[$categorien['beleggingscategorie']] = $categorien['Omschrijving'];//substr($categorien['Omschrijving'],0,13);

	    $valutaTotalen[$categorien['valuta']] += $categorien['subtotaalactueel'];
	    $categorieTotalen[$categorien['beleggingscategorie']] += $categorien['subtotaalactueel'];
	    $allesTotaal += $categorien['subtotaalactueel'];
		}

			$query = "SELECT Beleggingscategorien.Omschrijving, ".
			" Valutas.Omschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, ".
			" TijdelijkeRapportage.type ".
			" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
			" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatumVanaf."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY Valutas.Afdrukvolgorde asc, Beleggingscategorien.Afdrukvolgorde asc ";
		  debugSpecial($query,__FILE__,__LINE__);

				$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  if($categorien['beleggingscategorie'] == '')
		    $categorien['beleggingscategorie'] = 'Liquiditeiten';

	    $beginWaardenTotalen[$categorien['beleggingscategorie']] += $categorien['subtotaalactueel'];
	    $beginAllesTotaal += $categorien['subtotaalactueel'];
		}



		foreach ($categorieOmschrijvingen as $cat=>$omschrijving)
		{
		  $volgordeKol[] = $cat;

		  if($omschrijving == '')
		   $volgordeKolOms[] = 'Liquiditeiten';
		  else
		   $volgordeKolOms[] = $omschrijving;
		}

		$this->pdf->OIBHeaderData = array('volgorde'=>$volgordeKol,'omschrijvingen'=>$categorieOmschrijvingen);


		$this->pdf->AddPage();
		$this->pdf->templateVars['OIBPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;

		$this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;
		$this->pdf->switchFont('fonds');

    
    if(count($waarden)>5)
      $lowRow=6.5;
    else
      $lowRow=0;
    if($lowRow > 0)    
      $this->pdf->rowHeight = $lowRow;
		foreach ($waarden as $valuta=>$categorieData)
		{

		  $this->pdf->Row(array('',$valuta,
		                           $this->formatGetal($categorieData[$volgordeKol[0]],0),'',
		                           $this->formatGetal($categorieData[$volgordeKol[1]],0),'',
		                           $this->formatGetal($categorieData[$volgordeKol[2]],0),'',
		                           $this->formatGetal($valutaTotalen[$valuta],0),$this->formatGetal($valutaTotalen[$valuta]/$totaalWaarde*100,1).' %'
		  ));
		}

		$this->pdf->switchFont('rodelijn');
	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    if($lowRow > 0)    
      $this->pdf->rowHeight = $lowRow;
	  $this->pdf->Row(array('','Totaal',
		                           $this->formatGetal($categorieTotalen[$volgordeKol[0]],2),$this->formatGetal($categorieTotalen[$volgordeKol[0]]/$totaalWaarde*100,1,true),
		                           $this->formatGetal($categorieTotalen[$volgordeKol[1]],2),$this->formatGetal($categorieTotalen[$volgordeKol[1]]/$totaalWaarde*100,1,true),
		                           $this->formatGetal($categorieTotalen[$volgordeKol[2]],2),$this->formatGetal($categorieTotalen[$volgordeKol[2]]/$totaalWaarde*100,1,true),
		                           $this->formatGetal($allesTotaal,0),$this->formatGetal($allesTotaal/$totaalWaarde*100,1,true)
		  ));
		  //vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->rapportageJulVanaf)])." ".
	 $this->pdf->Row(array('',date("j-n-Y",$this->rapportageJulVanaf) ,
		                           $this->formatGetal($beginWaardenTotalen[$volgordeKol[0]],2),$this->formatGetal($beginWaardenTotalen[$volgordeKol[0]]/$totaalWaardeBegin*100,1,true),
		                           $this->formatGetal($beginWaardenTotalen[$volgordeKol[1]],2),$this->formatGetal($beginWaardenTotalen[$volgordeKol[1]]/$totaalWaardeBegin*100,1,true),
		                           $this->formatGetal($beginWaardenTotalen[$volgordeKol[2]],2),$this->formatGetal($beginWaardenTotalen[$volgordeKol[2]]/$totaalWaardeBegin*100,1,true),
		                           $this->formatGetal($beginAllesTotaal,0),$this->formatGetal($beginAllesTotaal/$totaalWaardeBegin*100,1,true)
		  ));
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];

		$kleurdata = array();
    $dbBeleggingscategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		//listarray($kleuren);

$standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));
		$n=0;
		foreach ($categorieOmschrijvingen as $cat=>$omschrijving)
		{
		  if($kleuren[$cat]['R']['value'] != 0 && $kleuren[$cat]['G']['value'] != 0 && $kleuren[$cat]['B']['value'] != 0)
  			$kleurdata[] = array($kleuren[$cat]['R']['value'],$kleuren[$cat]['G']['value'],$kleuren[$cat]['B']['value']);
  		else
  			$kleurdata[] =$standaardKleuren[$n];

  		$waarde = $categorieTotalen[$volgordeKol[$n]]/$totaalWaarde*100;
  		$grafiekdata[] = $waarde;

  		if($omschrijving == '')
		    $omschrijving = 'Liquiditeiten';
  		$grafiekdataOms[] = $omschrijving." ".$this->formatGetal($waarde,1,true);

  		$n++;
	  }



$diameter = 35;
$hoek = 20;
$dikte = 6;
$Xas= 70;
$yas= 155;

$standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));

//foreach ($this->pdf->pieData as $value)
//$grafiekdata[]=$value;

		$this->pdf->set3dLabels($grafiekdataOms,$Xas+110,$yas-40,$kleurdata);
    $this->pdf->Pie3D($grafiekdata,$kleurdata,$Xas,$yas,$diameter,$hoek,$dikte,"",0);



    $q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND ".
		" TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc";

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$aantal = $DB->records();
		$n=1;
		if($aantal > 0)
		{

		  $this->pdf->ln();
		  $this->pdf->setY(130);
      if($lowRow > 0)
        $this->pdf->setY(133);
		  $this->pdf->switchFont('fonds');
      $this->pdf->switchFont('rodelijn');
      $this->pdf->SetWidths(array(195,29,29,29));
		  $this->pdf->SetAligns(array('L','L','C','R'));
		  $this->pdf->CellBorders = array('','U','U','U');
		  $this->pdf->rowHeight = 3;
      $this->pdf->Row(array('','','',''));
      $this->pdf->rowHeight = 12;
	  	$this->pdf->Row(array('','Munt','Valuta','Munt'));
	  	$this->pdf->switchFont('fonds');
	  	while($data = $DB->nextRecord())
	  	{
	  	  if($n==$aantal)
	  	    $this->pdf->switchFont('rodelijn');
        if($lowRow > 0)    
          $this->pdf->rowHeight = $lowRow;  
          
	  	  $this->pdf->Row(array('',$this->pdf->rapportageValuta,'1 = '.$this->formatGetal($data['actueleValuta'],6),$data['val']));
	  	  $n++;

	  	}



		}


    	}
}
?>