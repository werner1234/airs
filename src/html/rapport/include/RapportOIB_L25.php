<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/08 05:43:32 $
File Versie					: $Revision: 1.12 $
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L25.php");

class RapportOIB_L25
{
	function RapportOIB_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	
	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();
    $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
	}

	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		// voor data
		$this->pdf->widthB = array(60,35,15,5,25,20,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];

    if($this->pdf->portefeuilledata['PerformanceBerekening']==3 && intval(substr($this->rapportageDatum,0,4))>=2021)
    {
      $old=false;
    }
    else
    {
      $old=true;
    }

    if($old==true)
    {
      $index = new indexHerberekening();
      $indexData = $index->getWaarden($this->rapportageDatumVanaf, $this->rapportageDatum, $this->portefeuille);
    }
    else
    {
      $this->att = new ATTberekening_L25($this);
      $indexData = $this->att->getPerf($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['RapportageValuta'], true);
    }

    $query="SELECT
CategorienPerHoofdcategorie.Vermogensbeheerder,
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
Beleggingscategorien.Omschrijving,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingscategorien.Afdrukvolgorde
FROM
CategorienPerHoofdcategorie
INNER JOIN KeuzePerVermogensbeheerder ON CategorienPerHoofdcategorie.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='$beheerder' AND CategorienPerHoofdcategorie.Vermogensbeheerder='$beheerder'
ORDER BY
Beleggingscategorien.Afdrukvolgorde,KeuzePerVermogensbeheerder.Afdrukvolgorde
 ";

    $DB->SQL($query);
    $DB->Query();
    $conversie=array('LIQ'=>'H-Liq');
    $legeCategorieen=array();
    while($data=$DB->NextRecord())
    {
      $legeCategorieen[$data['Hoofdcategorie']]=0;
      $conversie[$data['Beleggingscategorie']]=$data['Hoofdcategorie'];

      $this->categorieVolgorde[$data['Hoofdcategorie']]=$data['Hoofdcategorie'];
      $this->categorieOmschrijving[$data['Hoofdcategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
    }

    $i=0;
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;

        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
          //echo "$categorie || ".$conversie[$categorie]." <br>\n";
          if(isset($conversie[$categorie])) {
            $categorie=$conversie[$categorie];
          }
          if(!isset($barGraph['Index'][$data['datum']])) {
            $barGraph['Index'][$data['datum']]=$legeCategorieen;
          }

          if($categorie=='LIQ') {
            $categorie='Liquiditeiten';
          }

          if(isset($barGraph['Index'][$data['datum']][$categorie])) {
            $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
          } else {
            $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
          }
          if($waarde <> 0) {
            $categorien[$categorie]=$categorie;
          }
        }
      }
    }

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$query="UPDATE TijdelijkeRapportage SET 	Beleggingscategorie='Rente' WHERE TijdelijkeRapportage.type = 'rente' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
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

		$query = "SELECT 	TijdelijkeRapportage.beleggingscategorieOmschrijving as Omschrijving, ".
			" Valutas.Omschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta,
			if(TijdelijkeRapportage.`type`='rente','Rente',TijdelijkeRapportage.beleggingscategorie ) as beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel,  TijdelijkeRapportage.Hoofdcategorie,
 TijdelijkeRapportage.HoofdcategorieOmschrijving AS HoofdcategorieOmschrijving ".
			" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  
			WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		//	" TijdelijkeRapportage.type = 'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY beleggingscategorie".
			" ORDER BY 	TijdelijkeRapportage.hoofdcategorieVolgorde,
	TijdelijkeRapportage.beleggingscategorieVolgorde ASC,
	TijdelijkeRapportage.valutaVolgorde ASC";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

    //$this->pdf->rapport_row_bg=array(100,100,100);
    $this->pdf->SetFillColor($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]);

    $regel=1;
    // print categorie headers
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('Categorie','','','','Waarde (€)','Weging (%)'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $lastCat='';
    $lastHoofdCategorie='';
    $totaalactueel=0;
    $hoofdTotalen=array();
		while($categorien = $DB->NextRecord())
		{
		  $regel++;
      if($regel%2!=0)
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
      else
        unset($this->pdf->fillCell);
        
		  if($categorien['beleggingscategorie']=='Rente')
		  {
		    $categorien['Omschrijving']='Opgelopen Rente';
		    $categorien['beleggingscategorie']='Opgelopen Rente';
		  }
      
      if($lastHoofdCategorie=='')
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_titel_fontcolor[0],$this->pdf->rapport_titel_fontcolor[1],$this->pdf->rapport_titel_fontcolor[2]);
	      $this->pdf->Cell($this->pdf->widths[0],5,$categorien['HoofdcategorieOmschrijving']);
        $this->pdf->SetTextColor(0);
        $this->pdf->Ln();

      } 



			// print totaal op hele categorie.
			if(!empty($lastCategorie) && $lastCategorie <> $categorien['Omschrijving'])
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
				//$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
        $this->pdf->Row(array(vertaalTekst($lastCategorie,$this->pdf->rapport_taal),'','','',$this->formatGetal($totaalactueel,0),$this->formatGetal($percentageVanTotaal,1)));
        $actueleWaardePortefeuille+=$totaalactueel;
				$totaalbegin = 0;
				$totaalactueel = 0;

        unset($this->pdf->fillCell);
			}


			if($lastHoofdCategorie <> $categorien['HoofdcategorieOmschrijving'] && !empty($lastHoofdCategorie) )
      {
        $hoofdPercentageVanTotaal = $hoofdTotalen[$lastHoofdCategorie] / ($totaalWaarde/100);
	      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      	$this->pdf->Line(130,$this->pdf->getY(),163,$this->pdf->getY());
        $this->pdf->Cell($this->pdf->widths[0],5,'Subtotaal '.$lastHoofdCategorie);//
	      $this->pdf->setX($this->pdf->marge);
        $this->pdf->Row(array('','','','',$this->formatGetal($hoofdTotalen[$lastHoofdCategorie],0),$this->formatGetal($hoofdPercentageVanTotaal,1)));
        $this->pdf->Ln();
        $this->pdf->SetTextColor($this->pdf->rapport_titel_fontcolor[0],$this->pdf->rapport_titel_fontcolor[1],$this->pdf->rapport_titel_fontcolor[2]);
	      $this->pdf->Cell($this->pdf->widths[0],5,$categorien['HoofdcategorieOmschrijving']);
        $this->pdf->SetTextColor(0);
        $this->pdf->Ln();
        $regel=0;
  
  
        $this->pdf->pieData[vertaalTekst($lastHoofdCategorie,$this->pdf->rapport_taal)] = $hoofdPercentageVanTotaal;
        $grafiekCategorien[$lastCat]=array($hoofdPercentageVanTotaal,$lastHoofdCategorie); //toevoeging voor kleuren.
      }


			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			// totaal op categorie tellen
			$totaalinvaluta += $categorien['subtotaalactueelvaluta'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$hoofdTotalen[$categorien['HoofdcategorieOmschrijving']] += $categorien['subtotaalactueel'];

			$lastCategorie = $categorien['Omschrijving'];
			$lastCat       = $categorien['Hoofdcategorie'];
			$lastHoofdCategorie = $categorien['HoofdcategorieOmschrijving'];
		}

		// totaal voor de laatste categorie

		  $regel++;
      if($regel%2!=0)
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
      else
        unset($this->pdf->fillCell);
		$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $totaalactueel;//$this->printTotaal("", $totaalactueel, $percentageVanTotaal);
    $this->pdf->Row(array(vertaalTekst($lastCategorie,$this->pdf->rapport_taal),'','','',$this->formatGetal($totaalactueel,0),$this->formatGetal($percentageVanTotaal,1)));
    unset($this->pdf->fillCell);
		// voor Pie

    $hoofdPercentageVanTotaal = $hoofdTotalen[$lastHoofdCategorie] / ($totaalWaarde/100);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   	$this->pdf->Line(130,$this->pdf->getY(),163,$this->pdf->getY());
    $this->pdf->Cell($this->pdf->widths[0],4,'Subtotaal '.$lastHoofdCategorie);//$lastHoofdCategorie
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->Row(array('','','','',$this->formatGetal($hoofdTotalen[$lastHoofdCategorie],0),$this->formatGetal($hoofdPercentageVanTotaal,1)));
    $this->pdf->Ln();
    $this->pdf->pieData[vertaalTekst($lastHoofdCategorie,$this->pdf->rapport_taal)] = $hoofdPercentageVanTotaal;
    $grafiekCategorien[$lastCat]=array($hoofdPercentageVanTotaal,$lastHoofdCategorie); //toevoeging voor kleuren.

		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3];
		$proc = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$extra =0;
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());
		$this->pdf->setX($this->pdf->marge);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($this->pdf->widthB[0],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
    	$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->Cell($this->pdf->widthB[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "R");
	  $this->pdf->Cell($this->pdf->widthB[3],4,"", 0,0, "L");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		$this->pdf->Cell($this->pdf->widthB[4],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$this->formatGetal(100,1), 0,1, "R");

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[4],$this->pdf->GetY()+1);
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY()+1,$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		if($this->pdf->rapport_OIB_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIB_rendementKort);

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];
		$q = "SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
hoofdCat.Omschrijving as hCatOmschrijving
FROM
Beleggingscategorien
INNER JOIN CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '$beheerder'
INNER JOIN Beleggingscategorien as hoofdCat ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdCat.Beleggingscategorie ";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbBeleggingscategorien = array();
    $dbHoofdcategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
		{
			$dbBeleggingscategorien[$categorie['Hoofdcategorie']] = $categorie['hCatOmschrijving'];
      $dbHoofdcategorien[$categorie['Hoofdcategorie']] = $categorie['hCatOmschrijving'];
		}

		while (list($groep, $data) = each($grafiekCategorien))
		{
		  while (list($key, $value) = each($dbBeleggingscategorien))
  	  {
	  		if ($key == $groep)
	  		{
	  		  $data[1]=	vertaalTekst($data[1],$this->pdf->rapport_taal);
    		  $kleurdata[$data[1]]['kleur'] = $kleuren[$key];
    		  $kleurdata[$data[1]]['percentage'] = $data[0];
          $kleurdata[$data[1]]['hcat'] = $dbHoofdcategorien[$key];
	  		}
      }
		reset($dbBeleggingscategorien);
		}// echo min($this->pdf->pieData);exit;
//listarray($kleuren);
//listarray($this->pdf->pieData);
   // listarray($grafiekCategorien);
	//	listarray($dbBeleggingscategorien);
//		listarray($kleurdata);
//		exit;
		//$this->printPie($this->pdf->pieData,$kleurdata);
    $tableY = $this->pdf->getY();
    $this->pdf->setXY(200,33);
//$this->pdf->setXY(175,40);
    if(min($this->pdf->pieData)>0)
    {
      $this->printPie($this->pdf->pieData, $kleurdata, 'Verdeling naar vermogenscategorie', 60, 50);
    }
    else
    {
      include_once('');
      include_once($__appvar["basedir"]."/html/rapport/include/RapportDOORKIJK_L25.php");
      $doorkijk=new RapportDOORKIJK_L25( $this->pdf,$this->portefeuille,  $this->rapportageDatumVanaf,  $this->rapportageDatum);
      //listarray($kleurdata);
      $kleur=array();
      foreach($kleurdata as $categorie=>$catdata)
        $kleur[$categorie]=array($catdata['kleur']['R']['value'],$catdata['kleur']['G']['value'],$catdata['kleur']['B']['value']);
      $doorkijk->BarDiagram(50,50,$this->pdf->pieData,'%l',$kleur);
      
    }
    $this->pdf->wLegend=0;


    $originalPageBreakTrigger = $this->pdf->PageBreakTrigger;
    $this->pdf->PageBreakTrigger=$this->pdf->PageBreakTrigger+10;

    $barGraphTextY = '130';
    if( $tableY > 125 ) {
      $this->pdf->addPage();
      $barGraphTextY = $this->pdf->getY();
    }

    if (count($barGraph['Index']) > 0)
    {
      $this->pdf->SetXY($this->pdf->marge,$barGraphTextY);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
      $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
      $barGraphY = $this->pdf->getY() + 52;
      $this->pdf->SetXY(15,$barGraphY)		;//112
      $this->VBarDiagram(220, 50, $barGraph['Index']);
    }

    $this->pdf->PageBreakTrigger = $originalPageBreakTrigger;
	}
  
  function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
	{

	  $col1=array(255,0,0); // rood
	  $col2=array(0,255,0); // groen
	  $col3=array(255,128,0); // oranje
	  $col4=array(0,0,255); // blauw
	  $col5=array(255,255,0); // geel
	  $col6=array(255,0,255); // paars
	  $col7=array(128,128,128); // grijs
	  $col8=array(128,64,64); // bruin
	  $col9=array(255,255,255); // wit
	  $col0=array(0,0,0); //zwart
	  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
//listarray($kleurdata);
		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
	    foreach($kleurdata as $key=>$value)
			{
  			if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
		  	$pieData[$key] = $value['percentage'];
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',10);
			$this->pdf->setXY($startX,$y-4);
			$this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $this->pdf->setX($startX);
      
      
      //
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius=min($width,$height);
    $radius = floor($radius / 2);
    $YDiag = $YPage + $margin + $radius;
    $x1 = $XPage ;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YDiag + ($radius) + $margin;
  
    $lastHcat='';
    foreach($kleurdata as $cat=>$catData)
    {
      /*
      if($catData['hcat']<>$lastHcat)
      {
        $this->pdf->SetXY($x2,$y1);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_titel_fontcolor[0],$this->pdf->rapport_titel_fontcolor[1],$this->pdf->rapport_titel_fontcolor[2]);
        $this->pdf->Cell(0,$hLegend,$catData['hcat']);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $y1+=$hLegend + 2;
      }
      */
      $this->pdf->SetFillColor($catData['kleur']['R']['value'],$catData['kleur']['G']['value'],$catData['kleur']['B']['value']);
      $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      $this->pdf->Cell(0,$hLegend,$cat);
      $y1+=$hLegend + 2;
  
      $lastHcat=$catData['hcat'];
    }
    $this->pdf->setXY($XPage,$YPage);
    
    //
    
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}
  
	function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      //$radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              //$this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }



  }

  function VBarDiagram($w, $h, $data)
  {
    global $__appvar;
    $legendaWidth = 00;
    $grafiekPunt = array();
    $verwijder=array();

    foreach ($data as $datum=>$waarden)
    {
      $legenda[$datum] = jul2form(db2jul($datum));
      $n=0;
      $minVal=0;
      $maxVal=100;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        //foreach ($waarden as $categorie=>$waarde)
        //{
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';

        $grafiek[$datum][$categorie]=$waarden[$categorie];
        $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
        $categorien[$categorie] = $n;
        $categorieId[$n]=$categorie ;

        $maxVal=max(array($maxVal,$waarden[$categorie]));
        $minVal=min(array($minVal,$waarden[$categorie]));

        if($waarden[$categorie] < 0)
        {
          unset($grafiek[$datum][$categorie]);
          $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
        }
        else
          $grafiekNegatief[$datum][$categorie]=0;


        if(!isset($colors[$categorie]))
          $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        $n++;
      }
    }



    $numBars = count($legenda);
    $numBars=10;

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
    $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda

    $n=0;
    foreach (($this->categorieVolgorde) as $categorie)//array_reverse
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
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
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
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
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
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
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
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