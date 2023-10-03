<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/04/05 15:39:45 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIH_L25.php,v $
Revision 1.2  2017/04/05 15:39:45  rvv
*** empty log message ***

Revision 1.1  2016/11/13 16:27:53  rvv
*** empty log message ***

Revision 1.11  2016/02/03 14:55:12  rvv
*** empty log message ***

Revision 1.10  2016/02/03 09:36:21  rvv
*** empty log message ***

Revision 1.6  2016/01/03 09:16:56  rvv
*** empty log message ***

Revision 1.5  2010/06/09 16:39:37  rvv
*** empty log message ***

Revision 1.4  2010/05/30 12:46:25  rvv
*** empty log message ***

Revision 1.3  2010/05/19 16:49:42  rvv
*** empty log message ***

Revision 1.2  2010/05/19 16:33:07  rvv
*** empty log message ***

Revision 1.1  2010/05/19 16:24:10  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L25
{
	function RapportOIH_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
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
		$this->pdf->widthB = array(60,35,15,5,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;



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
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT Beleggingscategorien.Omschrijving, ".
			" Valutas.Omschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta,
			if(TijdelijkeRapportage.`type`='rente','Rente',TijdelijkeRapportage.beleggingscategorie ) as beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, 
			if(CategorienPerHoofdcategorie.Hoofdcategorie='WW-RISD','Beleggingen',                if(CategorienPerHoofdcategorie.Hoofdcategorie='WW-RISP','Beleggingen'                ,'Bezittingen')) as Hoofdcategorie,
			if(CategorienPerHoofdcategorie.Hoofdcategorie='WW-RISD','Beursgenoteerde beleggingen',if(CategorienPerHoofdcategorie.Hoofdcategorie='WW-RISP','Beursgenoteerde beleggingen','Niet-beursgenoteerde beleggingen')) as HoofdcategorieOmschrijving ".
			" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
			" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
		  	LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'".
			" LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie  = CategorienPerHoofdcategorie.Hoofdcategorie
			WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		//	" TijdelijkeRapportage.type = 'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY beleggingscategorie".
			" ORDER BY Hoofdcategorie, Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

    //$this->pdf->rapport_row_bg=array(100,100,100);
    $this->pdf->SetFillColor($this->pdf->rapport_row_bg[0],$this->pdf->rapport_row_bg[1],$this->pdf->rapport_row_bg[2]);

    $regel=1;
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

			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
				//$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
        $this->pdf->Row(array(vertaalTekst($lastCategorie,$this->pdf->rapport_taal),'','','',$this->formatGetal($totaalactueel,0),$this->formatGetal($percentageVanTotaal,1)));
        $actueleWaardePortefeuille+=$totaalactueel;
				$totaalbegin = 0;
				$totaalactueel = 0;
				$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien[$lastCat]=array($percentageVanTotaal,$lastCategorie); //toevoeging voor kleuren.
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
      }


			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			// totaal op categorie tellen
			$totaalinvaluta += $categorien['subtotaalactueelvaluta'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$hoofdTotalen[$categorien['HoofdcategorieOmschrijving']] += $categorien['subtotaalactueel'];

			$lastCategorie = $categorien['Omschrijving'];
			$lastCat       = $categorien['beleggingscategorie'];
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
		$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
		$grafiekCategorien[$lastCat]=array($percentageVanTotaal,$lastCategorie);; //toevoeging voor kleuren.


    $hoofdPercentageVanTotaal = $hoofdTotalen[$lastHoofdCategorie] / ($totaalWaarde/100);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   	$this->pdf->Line(130,$this->pdf->getY(),163,$this->pdf->getY());
    $this->pdf->Cell($this->pdf->widths[0],4,'Subtotaal '.$lastHoofdCategorie);//$lastHoofdCategorie
    $this->pdf->setX($this->pdf->marge);
    $this->pdf->Row(array('','','','',$this->formatGetal($hoofdTotalen[$lastHoofdCategorie],0),$this->formatGetal($hoofdPercentageVanTotaal,1)));
    $this->pdf->Ln();

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

			$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($this->pdf->widthB[0],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
    	$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->Cell($this->pdf->widthB[1],4,"", 0,0, "L");
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
		$q = "SELECT Beleggingscategorie, omschrijving FROM Beleggingscategorien";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbBeleggingscategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
		{
			$dbBeleggingscategorien[$categorie['Beleggingscategorie']] = $categorie['omschrijving'];
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
	  		}
      }
		reset($dbBeleggingscategorien);
		} //listarray($this->pdf->pieData);exit;
//listarray($kleuren);
//listarray($this->pdf->pieData);
//		listarray($kleurdata);
		//$this->printPie($this->pdf->pieData,$kleurdata);
    
    $this->pdf->setXY(200,33);
//$this->pdf->setXY(175,40);
$this->printPie($this->pdf->pieData,$kleurdata,'Verdeling naar vermogenscategorie',60,50);
$this->pdf->wLegend=0;
/*    
    $grafiekData['OIB']['Omschrijving']=array();
    $grafiekData['OIB']['Kleur']=array();
    $grafiekData['OIB']['Percentage']=array();
    
    foreach($this->pdf->pieData as $categorie=>$percentage)
    {
      $grafiekData['OIB']['Omschrijving'][]=$categorie.' ('.round($percentage,1).' %)';
      $grafiekData['OIB']['Percentage'][]=$percentage;
      $grafiekData['OIB']['Kleur'][]=array($kleurdata[$categorie]['kleur']['R']['value'],$kleurdata[$categorie]['kleur']['G']['value'],$kleurdata[$categorie]['kleur']['B']['value']);
    }
    
    $diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 235;
$yas= 55;
    $this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas+10,$yas,$grafiekData['OIB']['Kleur']);
    $this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$grafiekData['OIB']['Kleur'],$Xas,$yas,$diameter,$hoek,$dikte,"Verdeling naar vermogenscategorie",0);
*/

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

		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
		  while (list($key, $value) = each($kleurdata))
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
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
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
              $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag + ($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + 2;
      }

  }
}
?>