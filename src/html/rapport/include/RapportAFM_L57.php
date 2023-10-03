<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/08 15:42:42 $
File Versie					: $Revision: 1.4 $

$Log: RapportAFM_L57.php,v $
Revision 1.4  2020/04/08 15:42:42  rvv
*** empty log message ***

Revision 1.3  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.2  2019/10/30 16:47:58  rvv
*** empty log message ***

Revision 1.1  2014/12/21 13:23:18  rvv
*** empty log message ***

Revision 1.8  2014/05/25 14:37:00  rvv
*** empty log message ***

Revision 1.7  2014/04/12 16:28:56  rvv
*** empty log message ***

Revision 1.6  2014/03/02 10:24:34  rvv
*** empty log message ***

Revision 1.5  2014/02/08 17:42:52  rvv
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

class RapportAFM_L57
{
	function RapportAFM_L57($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
    $this->pdf->rapport_type = "AFM";
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
					$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor[r],$this->pdf->rapport_totaal_omschr_fontcolor[g],$this->pdf->rapport_totaal_omschr_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor[r],$this->pdf->rapport_totaal_fontcolor[g],$this->pdf->rapport_totaal_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");
	
		if($grandtotaal)
		{


			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[3],$this->pdf->GetY()+1);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	//	$this->pdf->ln(2);

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
		//$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetTextColor(0);
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
		$this->pdf->widthB = array(70,5,5,5,25,15,175);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = 	$this->pdf->alignB;


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
   
    $ybegin=$this->pdf->GetY();
    $this->pdf->Ln(7);

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

		$query = "SELECT TijdelijkeRapportage.afmCategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.afmCategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.afmCategorie".
			" ORDER BY TijdelijkeRapportage.afmCategorie asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
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

				$categorieTekst = $categorien[Omschrijving];
			  $this->printKop(vertaalTekst($categorieTekst,$this->pdf->rapport_taal), '');
			}
			$lastCategorie = $categorien[Omschrijving];

			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);


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



		
		$this->pdf->Cell($this->pdf->widthB[0],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "R");
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
      if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
	      $groep = strtoupper($groep);
      $groep=	vertaalTekst($groep,$this->pdf->rapport_taal);
      $kleurdata[$groep]['kleur'] = $kleuren[$cat];
      $kleurdata[$groep]['percentage'] = $percentage;
    }

    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);

    $this->pdf->ln(8);
    $this->pdf->SetWidths(array(70,5,5,5,25,15));
    $this->pdf->SetAligns(array('R','L','L','L','R'));
    $this->pdf->Row(array(vertaalTekst('AFM-standaarddeviatie',$this->pdf->rapport_taal),'','','',$this->formatGetal($afm['std'],2)." %"));
    
    $this->pdf->ln();
    $txt="De AFM standaarddeviatie betreft een risicoberekening van 1x de standaarddeviatie. Bij de AFM standaarddeviatie wordt geen berekening gemaakt van het werkelijke risico, maar wordt gebruik gemaakt van lange termijn standaarden. Florentes vermogensbeheer gebruikt daarnaast, in samenhang, nog andere risicomaatstaven zoals de werkelijke standaarddeviatie en de \"Value at Risk\".";

    $this->pdf->MultiCell(135,$this->pdf->rowHeight,$txt,$border=0,$align='J',$fill=0);
    
    
    if($this->pdf->debug)
      listarray($afm);

	//	$this->pdf->printPie($this->pdf->pieData,$kleurdata);
    
    $this->pdf->SetXY(160,$ybegin+5);
    $this->BarDiagram(100, 60, $this->pdf->pieData, '',$kleurdata,0,10);//%l (%p)

	}
  
  
  
    function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YDiag = $YPage;
      $hDiag = floor($h);
      $XDiag = $XPage;
      $lDiag = floor($w);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      $minVal=0;
      $offset=$minVal;
      $valIndRepere = ceil(($maxVal-$minVal) / $nbDiv);
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hDiag=$this->pdf->NbVal*5;
      $hBar = floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*5;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*2;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth(0.2);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

      $nullijn=$XDiag - ($offset * $unit);

      $i=0;
      $nbDiv=10;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        }

        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $i=0;

      //$this->pdf->SetXY(0, $YDiag);
      //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
      //$this->pdf->SetXY($nullijn, $YDiag);
      //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, 'AFM verdeling',0,0,'C');
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key]['kleur']['R']['value'],$colorArray[$key]['kleur']['G']['value'],$colorArray[$key]['kleur']['B']['value']);
          //Bar
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          //Legend
          $this->pdf->SetXY(0, $yval);
          $this->pdf->Cell(75 , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";

      for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
      {
          $xpos = $XDiag +  $i;
          $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
          $val = $i * $valIndRepere;
          $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
          $ypos = $YDiag + $hDiag - $margin;
          $this->pdf->Text($xpos, $ypos, $val);
      }
  }
}
?>