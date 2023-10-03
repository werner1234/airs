<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2014/03/27 19:30:31 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIV_L36.php,v $
Revision 1.3  2014/03/27 19:30:31  rvv
*** empty log message ***

Revision 1.2  2014/03/26 18:26:15  rvv
*** empty log message ***

Revision 1.1  2014/03/19 16:39:09  rvv
*** empty log message ***

Revision 1.26  2014/02/08 17:42:52  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingValutaLayout.php");

class RapportOIV_L36
{
	function RapportOIV_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIV_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIV_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in valuta";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

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

	function printTotaal($title, $totaalA, $totaalB, $procent)
	{

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIV_decimaal);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_OIV_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,$this->pdf->rapport_OIV_decimaal_proc);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[3],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[5],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalprtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

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

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function renteEnLiquiditeiten()
	{
		global $__appvar;
		// global $totaalWaarde, $categorien, $this->lastValutaCode,$totaalactueel,$totaalactueelvaluta;
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,  ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  ".
		" AND TijdelijkeRapportage.valuta = '".$this->lastValutaCode."' ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$rente = $DB1->NextRecord();

		//$percentageVanTotaal = $rente[subtotaalactueel] / ($totaalWaarde/100);
		if(round($rente['subtotaalactueelvaluta'],2) <> 0)
		{
			$this->pdf->row(array("",
											"",
											vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),
											$this->formatGetal($rente['subtotaalactueelvaluta'],$this->pdf->rapport_OIV_decimaal),
											"",
											"",
											""));
		}

		$this->totaalactueel += $rente['subtotaalactueel'];
		$this->totaalactueelvaluta += $rente['subtotaalactueelvaluta'];

		// selecteer liquid
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta,  ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rekening'  ".
		" AND TijdelijkeRapportage.valuta = '".$this->lastValutaCode."' ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
		$rente = $DB1->NextRecord();

		//$percentageVanTotaal = $rente[subtotaalactueel] / ($totaalWaarde/100);
		if(round($rente['subtotaalactueelvaluta'],2) <> 0)
		{
			$this->pdf->row(array("",
										"",
										vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),
										$this->formatGetal($rente['subtotaalactueelvaluta'],$this->pdf->rapport_OIV_decimaal),
										"",
										"",
										""));
		}

		$this->totaalactueel 				+= $rente['subtotaalactueel'];
		$this->totaalactueelvaluta 	+= $rente['subtotaalactueelvaluta'];
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;

		// voor data
		$this->pdf->widthA = array(25,15,50,25,25,25,15,110);
		$this->pdf->alignA = array('L','R','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(40,50,25,25,25,15,102);
		$this->pdf->alignB = array('L','L','R','R','R','R');

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

		$query = "SELECT ".
		" TijdelijkeRapportage.type, 
    TijdelijkeRapportage.HoofdcategorieOmschrijving,
    TijdelijkeRapportage.Beleggingssector,
    if(TijdelijkeRapportage.Beleggingssector='',
       TijdelijkeRapportage.HoofdcategorieOmschrijving,
       TijdelijkeRapportage.BeleggingscategorieOmschrijving) as Omschrijving, 
    TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
		" TijdelijkeRapportage.valuta, 
    TijdelijkeRapportage.beleggingscategorie, 
    TijdelijkeRapportage.actueleValuta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta,Omschrijving,TijdelijkeRapportage.type ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde asc, TijdelijkeRapportage.beleggingscategorieVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$lastValuta = 'eerste';
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			if ($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $koersPrinted = true;

//listarray($categorien);

			// print totaal op hele categorie.
			if($lastValuta <> $categorien['ValutaOmschrijving'] && !empty($lastValuta) )
			{
				$this->renteEnLiquiditeiten();

				$percentageVanTotaal = $this->totaalactueel / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal("", $this->totaalactueel, $this->totaalactueelvaluta, $percentageVanTotaal);
        
        $key=vertaalTekst($this->lastValutaOmschrijving, $this->pdf->rapport_taal);
        if($key!='')
        {
				$this->pdf->pieData[$key] = round($percentageVanTotaal,1);
        $this->pdf->pieDataWaarde[$key] = $this->totaalactueel;
        }
				$GrafiekValuta[$this->lastValutaOmschrijving]=$percentageVanTotaal;//toevoeging kleuren.
				$this->totaalactueel = 0;
				$this->totaalactueelvaluta = 0;
			}

			if($lastValuta <> $categorien['ValutaOmschrijving'])
			{
				$this->printKop(vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal), "bi");
				$koerstxt = vertaalTekst("Koers",$this->pdf->rapport_taal);
				$koers =  $this->formatGetalKoers($categorien['actueleValuta'],4);
			}
			elseif ($koersPrinted == true )
			{
			  $koerstxt = "";
				$koers =  "";
			}

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			if($categorien[type] == 'fondsen' )
			{
			  if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  {
			    $koerstxt = '';
			    $koers = '';
			  }

				// print categorie footers
				$this->pdf->row(array($koerstxt,
												$koers,
												vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal),
												$this->formatGetal($categorien['subtotaalactueelvaluta'],$this->pdf->rapport_OIV_decimaal),
												"",
												"",
												""));

				// totaal op categorie tellen
				$this->totaalactueel += $categorien['subtotaalactueel'];
				$this->totaalactueelvaluta += $categorien['subtotaalactueelvaluta'];
				$koersPrinted = true;
			}

			$lastValuta = $categorien['ValutaOmschrijving'];
			$this->lastValutaCode = $categorien['valuta'];
			$this->lastValutaOmschrijving = $categorien['ValutaOmschrijving'];
	//		$grafiekCategorien[$lastValuta]=$categorien[ValutaOmschrijving];
		}

		$this->renteEnLiquiditeiten();


		// totaal voor de laatste categorie
		$percentageVanTotaal = $this->totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", $this->totaalactueel, $this->totaalactueelvaluta, $percentageVanTotaal);
		$this->pdf->pieData[vertaalTekst($this->lastValutaOmschrijving, $this->pdf->rapport_taal)] = round($percentageVanTotaal,1);
    $this->pdf->pieDataWaarde[vertaalTekst($this->lastValutaOmschrijving, $this->pdf->rapport_taal)] = $this->totaalactueel;
		$GrafiekValuta[$this->lastValutaOmschrijving]=$percentageVanTotaal; //toevoeging kleuren.


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3]+ $this->pdf->widthA[4];
		$proc = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[5],$this->pdf->GetY());
		$this->pdf->Line($proc+2,$this->pdf->GetY(),$proc + $this->pdf->widthA[6],$this->pdf->GetY());

		$this->pdf->setX($this->pdf->marge);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->Cell($this->pdf->widthA[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[1],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[2],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[3],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[4],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthA[5],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIV_decimaal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$this->formatGetal(100,$this->pdf->rapport_OIV_decimaal_proc), 0,1, "R");

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[5],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthA[5],$this->pdf->GetY()+1);
		$this->pdf->Line($proc+2,$this->pdf->GetY(),$proc + $this->pdf->widthA[6],$this->pdf->GetY());
		$this->pdf->Line($proc+2,$this->pdf->GetY()+1,$proc + $this->pdf->widthA[6],$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		if($this->pdf->rapport_OIV_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIV_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_OIV_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIV_rendementKort );
		}

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIV'];
		$q = "SELECT Valuta, omschrijving FROM Valutas";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbValutacategorien = array();
		while($valta = $DB->NextRecord())
		{
			$dbValutacategorien[$valta['Valuta']] = $valta['omschrijving'];
		}

		while (list($groep, $percentage) = each($GrafiekValuta))//$grafiekCategorien
		{
		  while (list($key, $value) = each($dbValutacategorien))
  		  {
			if ($value == $groep)
			{
			  $groepVertaling=vertaalTekst($groep,$this->pdf->rapport_taal);
  			$kleurdata[$groepVertaling]['kleur'] = $kleuren[$key];
  			$kleurdata[$groepVertaling]['percentage'] = $percentage;
			}
  		  }
		reset($dbValutacategorien);
		}

  foreach($kleurdata as $valuta=>$kleur)
    $kleurdata2[$valuta]=array($kleur['kleur']['R']['value'],$kleur['kleur']['G']['value'],$kleur['kleur']['B']['value']);
	//	$this->pdf->printPie($this->pdf->pieData,$kleurdata);
    $this->pdf->SetXY(215,50);
    
    $barGraph=false;
    foreach($this->pdf->pieDataWaarde as $cat=>$waarde)
    {
      if($waarde<0)
        $barGraph=true;
    }
    
    if($barGraph==false)
    {
     $this->PieChart(50,50,$this->pdf->pieData,$this->pdf->pieDataWaarde,$kleurdata2);
    }
    else
    {
      $this->BarDiagram(60, 140, $this->pdf->pieData, '%l (%p)',$kleurdata2,'');
	  }
      
      
   
    
    
  //  listarray($kleurdata);
//listarray($this->pdf->pieData);
	}
  
    function SetLegends2($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          if($val <> 0)
          {
            $p=sprintf('%.1f',$val).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          }
          else
            $legend='';
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
      }
  }
   
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends2($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $nbDiv=5;
      $legendWidth=10;
      $YDiag = $YPage;
      $hDiag = floor($h);
      $XDiag = $XPage +  $legendWidth;
      $lDiag = floor($w - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      if($minVal > 0)
        $minVal=0;
      $maxVal=ceil($maxVal/10)*10;  

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100; 
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      
      //echo "$hBar <br>\n";
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;

      $legendaStep=$unit/$nbDiv*$bandBreedte;
      //if($bandBreedte/$legendaStep > $nbDiv)
      //  $legendaStep=$legendaStep*5;
     // if($bandBreedte/$legendaStep > $nbDiv)
      //  $legendaStep=$legendaStep*2;
     // if($bandBreedte/$legendaStep > $nbDiv)
     //   $legendaStep=$legendaStep/2*5;
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
        //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          $i++;
          if($i>100)
            break;
        }

        $i=0;
        //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
     
      $i=0;

      $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
      $this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      
   
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


  }
  
    
function PieChart($w, $h, $data, $dataWaarden, $colors=null,$hcat)
  {

      $this->pdf->sum=array_sum($data);
      $this->pdf->NbVal=count($data);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
     // $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;


      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $key=>$val)
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
              $this->pdf->SetFillColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
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

      $x1 = $XPage -10  ;
      $x2 = $x1 +  $margin;
      $y1 = $YDiag + ($radius) + $margin+5;

$this->pdf->SetXY($this->pdf->GetX(),$y1-5);

      //for($i=0; $i<$this->pdf->NbVal; $i++)
      foreach($data as $key=>$value)
      {
          //$this->pdf->SetXY($x2-30,$y1);
          $this->pdf->SetX($x2-$radius-10);
          $this->pdf->SetFillColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
          $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1+$extraY);
          $this->pdf->Cell(60,$hLegend,$key.' ('.$value.'%)');
          $this->pdf->Cell(20,$hLegend,'€ '.$this->formatGetal($dataWaarden[$key],2),0,0,'R');
          $y1+=$hLegend + 2;
          $lastHcat=$hcat[$i];
      }
      $this->pdf->SetFillColor(0,0,0);

  }

}
?>