<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/03/19 16:39:09 $
File Versie					: $Revision: 1.5 $

$Log: RapportAFM_L40.php,v $
Revision 1.5  2014/03/19 16:39:09  rvv
*** empty log message ***

Revision 1.4  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.3  2013/08/04 10:49:02  rvv
*** empty log message ***

Revision 1.2  2013/07/15 17:06:38  rvv
*** empty log message ***

Revision 1.1  2013/07/10 16:01:24  rvv
*** empty log message ***

Revision 1.4  2012/05/17 06:59:15  rvv
*** empty log message ***

Revision 1.3  2012/05/06 12:00:14  rvv
*** empty log message ***

Revision 1.2  2012/04/11 17:15:21  rvv
*** empty log message ***

Revision 1.1  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.35  2011/06/25 16:51:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L40.php");

class RapportAFM_L40
{
	function RapportAFM_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "AFM";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_AFM_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in AFM categorieën";

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

	function printTotaal($title, $totaalA, $procent, $grandtotaal)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2];

		if(!empty($totaalA))
		{
			if($this->pdf->rapport_OIB_specificatie == 1)
				$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_layout == 14)
		{
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1];
		$this->pdf->SetX($actueel);
		$this->pdf->Cell($this->pdf->widthB[2],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[3],4,$totaalprtxt, 0,1, "R");
		}
		else
		{
		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");
		}

		if($grandtotaal)
		{
		  if($this->pdf->rapport_layout == 14)
		  {
      $actueel  = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1];
		  }

			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[3],$this->pdf->GetY()+1);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);

		return $totaalA;
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();


		if($this->pdf->rapport_layout == 14)
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0]+$this->pdf->widthB[1],4, $title, 0, "L");
		}
		else
		{
		  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");
		}

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
		$this->pdf->widthB = array(60,35,25,25,25,15,96);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

		$this->pdf->AddPage();

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

		$query = "SELECT TijdelijkeRapportage.afmCategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.afmCategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.afmCategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.afmCategorie asc,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  if($categorien['afmCategorie']=='')
        $categorien['afmCategorie']='Geen afmCategorie';
		  if($categorien['Omschrijving']=='')
        $categorien['Omschrijving']='Geen afmCategorie';        
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);


			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
				// voor Pie
				$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.
			}

			if($lastCategorie != $categorien['Omschrijving'])
			{
				$categorieTekst = $categorien['Omschrijving'];
			  $this->printKop(vertaalTekst($categorieTekst,$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
			}
			$lastCategorie = $categorien['Omschrijving'];

			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);

			   $this->pdf->Cell($this->pdf->widthB[0],4,"");
			   $this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal));
		
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// print categorie footers

			if($this->pdf->rapport_OIB_specificatie == 1)
			{

					$this->pdf->row(array("",
											"",
											$this->formatGetal($categorien['subtotaalactueelvaluta'],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIB_decimaal),
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
			
			}
			else
			{

				$this->pdf->row(array("",
											"",
											"",
											"",
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
	
			}


			// totaal op categorie tellen
			$totaalinvaluta += $categorien['subtotaalactueelvaluta'];
			$totaalactueel += $categorien['subtotaalactueel'];
      $lastCat       = $categorien['afmCategorie'];
			$lastCategorie = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie


		$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
		// voor Pie
		$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
		$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.

		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3];
		$proc = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];

		$extra =0;


		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());

		$this->pdf->setX($this->pdf->marge);

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "R");
	  $this->pdf->Cell($this->pdf->widthB[3],4,"", 0,0, "L");


		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
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

		if($this->pdf->rapport_OIB_valutaoverzicht == 1)
		{
			$this->pdf->ln(2);
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIB_valutaoverzicht == 2)
		{
			$this->pdf->ln(2);
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_OIB_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIB_rendementKort);
		}

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['AFM'];
		//listarray($kleuren);
		$q = "SELECT afmCategorie, omschrijving FROM afmCategorien";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbBeleggingscategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
			$dbBeleggingscategorien[$categorie['afmCategorie']] = $categorie['omschrijving'];


    foreach ($grafiekCategorien as $cat=>$percentage)
    {
      $groep=$dbBeleggingscategorien[$cat];
      $groep=	vertaalTekst($groep,$this->pdf->rapport_taal);
      $kleurdata[$groep]['kleur'] = $kleuren[$cat];
      $kleurdata[$groep]['percentage'] = $percentage;
    }

    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);

    $this->pdf->ln(8);
    $this->pdf->SetWidths(array(60,35+25+0+25+25,15,95));
    $this->pdf->Row(array('','AFM-standaarddeviatie',$this->formatGetal($afm['std'],2)." %"));

    if($this->pdf->debug)
      listarray($afm);

		//		if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
		//		  $this->pdf->pieData[strtoupper(vertaalTekst($lastCategorie,$this->pdf->rapport_taal))] = $percentageVanTotaal;
		//		else z
  
  $this->printSTDDEV();
  
		$this->printPie($this->pdf->pieData,$kleurdata);
    
    

	}
  
  
  function printPie($pieData,$kleurdata)
	{



		// default colors
		// custom maken zet de kleuren in config/rapportage.php , en laad deze hier als ze bestaand, anders deze als default .
		if (is_array($this->pdf->customPieColors))
		{
		  $col1=$this->pdf->customPieColors["col1"];
		  $col2=$this->pdf->customPieColors["col2"];
		  $col3=$this->pdf->customPieColors["col3"];
		  $col4=$this->pdf->customPieColors["col4"];
		  $col5=$this->pdf->customPieColors["col5"];
		  $col6=$this->pdf->customPieColors["col6"];
		  $col7=$this->pdf->customPieColors["col7"];
		  $col8=$this->pdf->customPieColors["col8"];
		  $col9=$this->pdf->customPieColors["col9"];
		  $col0=$this->pdf->customPieColors["col0"];
		  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		}
		else
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
		}

// standaardkleuren vervangen voor eigen kleuren.

		if($kleurdata)
		{
		  if(!$this->rapport_dontsortpie)
		  {
   			 $sorted 		= array();
   			 $percentages 	= array();
   			 $kleur			= array();
   			 $valuta 		= array();

  			while (list($key, $data) = each($kleurdata))
   			{
   			  $percentages[] 	= $data[percentage];
   			  $kleur[] 			= $data[kleur];
   			  $valuta[] 		= $key;
   			}
   			arsort($percentages);

   			while (list($key, $percentage) = each($percentages))
   			{
   			  $sorted[$valuta[$key]]['kleur']=$kleur[$key];
   			  $sorted[$valuta[$key]]['percentage']=$percentage;
   			}
			$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
		  }

		  $pieData=array();
		  $grafiekKleuren = array();

		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
			if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
			  {
			  $grafiekKleuren[]=$standaardKleuren[$a];
			  }
			else
			  {
			  $grafiekKleuren[] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			  }
			$pieData[$key] = $value['percentage'];
      $grafiekData3d[]=$value['percentage'];
      $grafiekOmschrijving3d[]=$key." (".round($value['percentage'],1).")";
      $grafiekKleurData[]=array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

		$this->pdf->rapport_printpie = true;

		while (list($key, $value) = each($pieData))
		{
			if ($value < 0)
			{
				$this->pdf->rapport_printpie = false;
			}
		}

		if($this->pdf->rapport_printpie)
		{
		  
      $xDiff=-1;
			
			$y = $this->pdf->headerStart;
      $this->pdf->SetXY(220+$xDiff, $this->pdf->headerStart+5);
			$this->pdf->SetFont($this->pdf->rapport_font,'b',11);
			$this->pdf->Cell(50,4,vertaalTekst($this->pdf->rapport_titel, $this->pdf->rapport_taal),0,1,"C");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->SetX(220+$xDiff);
		//	$this->pdf->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);

    
    
    
    $grafiekX=244;
    $grafiekY=65;
    $radius=30;
    $this->pdf->Pie3D($grafiekData3d,$grafiekKleurData,$grafiekX,$grafiekY,$radius,30,5,'',0);
    $stringWidth=0;
    $hLegend=3;
    $this->pdf->SetFont($this->rapport_font, '', $this->pdf->rapport_fontsize-2);
    foreach($grafiekOmschrijving3d as $cat)
      $stringWidth=max($stringWidth,$this->pdf->GetStringWidth($cat));
    $stringWidth+=5;  
    $this->pdf->SetXY($grafiekX,$grafiekY);
    foreach($grafiekOmschrijving3d as $i=>$cat)
    {
      $this->pdf->SetFillColor($grafiekKleurData[$i][0],$grafiekKleurData[$i][1],$grafiekKleurData[$i][2]);
      $this->pdf->Rect($grafiekX-($stringWidth/2), $grafiekY+$radius+$i*5, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($grafiekX-($stringWidth/2)+5,$grafiekY+$radius+$i*5);
      $this->pdf->Cell(0,$hLegend,$cat);
    }
    
    			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);

		
				$this->pdf->Rect(200+$xDiff,$this->pdf->getY(),90,$hoogte);
			
		}
    
    
	}
  
  function printSTDDEV()
  {

    $att=new ATTberekening_L40($this);
    $data=$att->bereken($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,'Hoofdcategorie');
    $stddevInput=array();
    $startX=$this->pdf->GetX();
    $startY=$this->pdf->GetY()+3;

    $head=array_keys($data);

    $this->pdf->excelData[0]=array('Periode');
    foreach($head as $cat)
      $this->pdf->excelData[0][]=$cat;
  
    $ncat=0;
    foreach($data as $categorie=>$categorieData)
    {
      $ncat++;
      $row=1;
      foreach($categorieData['perfWaarden'] as $datum=>$perfData)
      {
        $stddevInput[$categorie][]=$perfData['procent']*100;
        $this->pdf->excelData[$row][0]=$datum;
        $this->pdf->excelData[$row][$ncat]=$perfData['procent']*100;
        $row++;
      }
    }
    if($this->pdf->debug)
    {
      listarray($stddevInput);
    }
   
    $stddev=array();
    foreach($stddevInput as $categorie=>$input)
    {
      if(!in_array($categorie,array('Geen H-cat','Geen cat')))
        $stddev[$categorie]=$this->standard_deviation($input)*sqrt(12); //*12^0.5 maanden/jaar
    }
    
    $row=count($this->pdf->excelData);
    $this->pdf->excelData[$row]=array("stdev*sqrt(12)");
    foreach($head as $cat)
      $this->pdf->excelData[$row][]=$stddev[$cat];

    $extraY=15;
   //	$this->pdf->Rect(157,$startY,106,10);
   // $this->pdf->setXY($this->pdf->marge,$startY);
   // $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   // $this->pdf->SetWidths(array(155,50,20));
   // $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->ln(2);
    $this->pdf->Row(array('','Standaarddeviatie portefeuille ',$this->formatGetal($stddev['totaal'],2)." %"));
    unset($stddev['totaal']);
    unset($stddev['Liquiditeiten']);
  //  $this->pdf->setXY($startX,$startY);
  //  $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),120,((count($stddev)+1)*4));
	//	$this->pdf->SetWidths(array(75,25));
  //	$this->pdf->SetAligns(array('L','R'));
  //  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  //  $this->pdf->Row(array('Standaarddeviatie portefeuille'));
  //  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  	foreach ($stddev as $cat=>$stddevWaarde)
    {
      if($att->categorien[$cat] <> '')
        $categorieOmschrijving=$att->categorien[$cat];
      else
       $categorieOmschrijving=$cat;
  	  $this->pdf->row(array('','Standaarddeviatie '.$categorieOmschrijving,$this->formatGetal($stddevWaarde,2)." %"));
    }
   // $this->pdf->ln(2);

    
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
}
?>