<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/06 15:48:23 $
 		File Versie					: $Revision: 1.14 $

 		$Log: RapportPERFG_L13.php,v $
 		Revision 1.14  2020/06/06 15:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/11/16 10:18:07  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/10/03 13:20:24  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/09/23 15:05:33  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/09/13 11:32:29  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/04/29 15:28:24  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/12/03 17:30:11  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/07/29 10:24:33  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/04/18 16:03:03  rvv
 		*** empty log message ***

 		Revision 1.5  2012/03/25 13:27:46  rvv
 		*** empty log message ***

 		Revision 1.4  2011/11/23 18:56:17  rvv
 		*** empty log message ***

 		Revision 1.3  2011/11/09 18:56:32  rvv
 		*** empty log message ***

 		Revision 1.2  2011/11/05 16:05:17  rvv
 		*** empty log message ***

 		Revision 1.1  2011/08/31 14:40:25  rvv
 		*** empty log message ***

 		Revision 1.7  2011/04/13 14:17:39  rvv
 		*** empty log message ***

 		Revision 1.6  2010/12/08 18:29:07  rvv
 		*** empty log message ***

 		Revision 1.5  2010/06/09 18:48:09  rvv
 		*** empty log message ***

 		Revision 1.4  2010/03/10 10:55:21  rvv
 		*** empty log message ***

 		Revision 1.3  2009/01/31 16:42:38  rvv
 		*** empty log message ***

 		Revision 1.2  2009/01/20 17:44:09  rvv
 		*** empty log message ***

 		Revision 1.1  2008/10/01 10:22:35  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPERFG_L13
{

	function RapportPERFG_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";

    $this->pdf->rapport_titel='';
		//$this->pdf->rapport_PERF_displayType

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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

	function printTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$extra = $this->pdf->rapport_PERF_lijnenKorter;

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

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
		/*
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
    */

		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		if($this->pdf->rapport_layout == 1)
		{
			$kopStyle = "";
		}
		else
		{
			$kopStyle = "u";
		}
    if($this->pdf->rapportToonRente == false)
      $renteFilter=" AND Type <> 'rente' ";
    else
      $renteFilter='';

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(12,25,35,35,30,30,17,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(12,50,10,30,20,3);
		$this->pdf->alignB = array('L','L','R','R','R','R');

		$this->pdf->AddPage("P");
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

		// ***************************** ophalen data voor afdruk ************************ //

		$jaren=array();
		$beginJul=db2jul($this->rapportageDatumVanaf);
		$eindJul=db2jul($this->rapportageDatum);
		$beginJaar=date("Y",$beginJul);
		$eindJaar=date("Y",$eindJul);
		for($jaar=$beginJaar;$jaar<=$eindJaar;$jaar++)
		{
		  $jaren[]=$jaar;
		}
		foreach ($jaren as $jaar)
		{
		  if($jaar==$beginJaar)
		    $begin=date('Y-m-d',$beginJul);
		  else
		    $begin=$jaar."-01-01";
		  if($jaar==$eindJaar)
		    $eind=date('Y-m-d',$eindJul);
		  else
		    $eind=($jaar+1)."-01-01";

		  $perioden[]=array($begin,$eind);
		}
    
    $portefeuileWaarde=array();
		foreach ($perioden as $periode)
		{
		  foreach ($periode as $datum)
		  {
		    if(!isset($portefeuileWaarde[$datum]))
		    {
          //if(substr($datum,5,5)=='01-01')
          //  $minDag=true;
          //else
          $minDag=false;
		      $fondsen=berekenPortefeuilleWaarde($this->portefeuille,$datum,$minDag,$this->pdf->rapportageValuta);
		      foreach ($fondsen as $id=>$waarden)
          {
		        $portefeuileWaarde[$datum][$waarden['type']]+=$waarden['actuelePortefeuilleWaardeEuro'];
          }
		    }
		  }
		}


		if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";



    $RapJaar = date("Y", db2jul($this->rapportageDatum));
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
		// ***************************** einde ophalen data voor afdruk ************************ //
    $stippelStart =$this->pdf->marge+$this->pdf->widthA[0] ;
    $stippelEind = $this->pdf->marge+$this->pdf->widthA[0]+$this->pdf->widthA[1];

    $stippelStart2 =$this->pdf->marge+$this->pdf->widthA[0]+$this->pdf->widthA[1]+$this->pdf->widthA[2] ;
    $stippelEind2 = $stippelStart2+$this->pdf->widthA[3];

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		//$this->pdf->setDash(1,1);

		$n=1;
		$rendementTotaal=1;
    $totaalDagen=0;
    $kopGetoond=0;
    $totalen=array();
		foreach ($perioden as $periode)
		{
       $waardeBegin=$portefeuileWaarde[$periode[0]]['fondsen']+$portefeuileWaarde[$periode[0]]['rekening'];
       $waardeEind=$portefeuileWaarde[$periode[1]]['fondsen']+$portefeuileWaarde[$periode[1]]['rekening'];
       if($this->pdf->rapportToonRente == true)
       {
        $waardeBegin+=$portefeuileWaarde[$periode[0]]['rente'];
        $waardeEind+=$portefeuileWaarde[$periode[1]]['rente'];
       }
       
       $waardeMutatie 	   	= $waardeEind - $waardeBegin;
       $stortingen 			 	  = getStortingen($this->portefeuille,$periode[0],$periode[1],$this->pdf->rapportageValuta);
		   $onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$periode[0],$periode[1],$this->pdf->rapportageValuta);
		   $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		   $query = "SELECT SUM(((TO_DAYS('".$periode[1]."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$periode[1]."') - TO_DAYS('".$periode[0]."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
   	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
    "FROM  (Rekeningen, Portefeuilles)
	  Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
    "WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningmutaties.Verwerkt = '1' AND ".
	  "Rekeningmutaties.Boekdatum > '".$periode[0]."' AND  Rekeningmutaties.Boekdatum <= '".$periode[1]."' AND ".
	  "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	  $DB->SQL($query);
	  $DB->Query();
	  $weging = $DB->NextRecord();
  	$gemiddelde = $waardeBegin + $weging['totaal1'];
  	if($gemiddelde <> 0)
  		$rendementProcent = ((($waardeEind - $waardeBegin) - $weging['totaal2']) / $gemiddelde) * 100;

  		$rendementTotaal=(1+$rendementProcent/100)*$rendementTotaal;
  		$totaalDagen+=(db2jul($periode[1])-db2jul($periode[0]))/86400;

  	if($this->pdf->lastPOST['PERFG_perc'] == 1)
    {
     	$rendement = $this->formatGetal($rendementProcent,2);
  	  $percentage = "%";
    }

    $backup=$this->pdf->widths;
    if(1 == 1) //$this->pdf->lastPOST['vvgl']
    {
      $this->pdf->widths=array(12,25+35,35,30,30,17,5);
       if($n==count($perioden) && substr($periode[1],5,5) <> '12-31')
          $jaar=vertaalTekst('Jaar' ,$this->pdf->rapport_taal).' '.date("Y",db2jul($periode[1])). " ".vertaalTekst('t/m',$this->pdf->rapport_taal)." ".date("d/m",db2jul($periode[1]));
       else
         $jaar=vertaalTekst('Jaar' ,$this->pdf->rapport_taal).' '.date("Y",db2jul($periode[0]));

      if($kopGetoond==0)
      {
        $this->pdf->setY($this->pdf->getY()-4);
        $this->pdf->row(array('','',vertaalTekst('Opnamen/Stortingen',$this->pdf->rapport_taal),'',vertaalTekst("Toename/afname",$this->pdf->rapport_taal),''));
        $this->pdf->row(array('',$jaar,'','','',''));
        $this->pdf->ln();
        $kopGetoond=1;
      }
      else
      {
        $this->pdf->row(array('',$jaar,'','','',''));//date("d/m/Y",db2jul($periode[1]))
        $this->pdf->ln();
      }

    $this->pdf->widths=$backup;
		$this->pdf->row(array('',vertaalTekst('effecten',$this->pdf->rapport_taal),$this->formatGetal($portefeuileWaarde[$periode[0]]['fondsen'],2,true),"",$this->formatGetal($portefeuileWaarde[$periode[1]]['fondsen'],2,true)));
	  if($this->pdf->rapportToonRente == true)
      $this->pdf->row(array('',vertaalTekst('liquiditeiten',$this->pdf->rapport_taal),$this->formatGetal($portefeuileWaarde[$periode[0]]['rekening']+$portefeuileWaarde[$periode[0]]['rente'],2,true),"",$this->formatGetal($portefeuileWaarde[$periode[1]]['rekening']+$portefeuileWaarde[$periode[1]]['rente'],2,true)));
	  else
      $this->pdf->row(array('',vertaalTekst('liquiditeiten',$this->pdf->rapport_taal),$this->formatGetal($portefeuileWaarde[$periode[0]]['rekening'],2,true),"",$this->formatGetal($portefeuileWaarde[$periode[1]]['rekening'],2,true)));
	  $this->pdf->row(array('','','----------------','','----------------'));
    //$this->pdf->ln(2);
	  //$this->pdf->Line($stippelStart ,$this->pdf->GetY() ,$stippelEind,$this->pdf->GetY());
	  //$this->pdf->Line($stippelStart2 ,$this->pdf->GetY() ,$stippelEind2,$this->pdf->GetY());
		//$this->pdf->ln(2);
		//$this->pdf->SetAligns($this->pdf->alignA);
	  $this->pdf->row(array('','EUR',$this->formatGetal($waardeBegin,2,true),$this->formatGetal($stortingen-$onttrekkingen,2,true),$this->formatGetal($waardeEind,2,true),$this->formatGetal($resultaatVerslagperiode,2,true),$rendement,$percentage));
	  //$this->pdf->SetAligns($this->pdf->alignA);
	  $this->pdf->ln(10);
	  $totalen['stortingen'] +=($stortingen-$onttrekkingen);
	  $totalen['resultaat'] +=($resultaatVerslagperiode);
    }
    $n++;
}

if($this->pdf->lastPOST['PERFG_perc'] == 1)
{
  	$gemiddeldeRendement=(($rendementTotaal-1)*100)/($totaalDagen/365.25);
  	$gemiddeldeRendement=$this->formatGetal($gemiddeldeRendement,2);
  	$percentage = "%";
}
else
{
$gemiddeldeRendement='';
$percentage = "";
}

    if($this->pdf->lastPOST['PERFG_totaal'] == 1 || $this->pdf->lastPOST['PERG_totaal'] == 1)
    {
       $this->pdf->ln(2);
       $this->pdf->SetAligns(array("L",'R','R','R','R','R','R'));
       //$this->pdf->Line($stippelStart+$this->pdf->widthA[1] ,$this->pdf->GetY() ,$stippelEind+$this->pdf->widthA[2],$this->pdf->GetY());
	     //$this->pdf->Line($stippelStart2 +$this->pdf->widthA[3] ,$this->pdf->GetY() ,$stippelEind2+$this->pdf->widthA[4] ,$this->pdf->GetY());
	     //$this->pdf->ln(2);
       $this->pdf->row(array('','','','----------------','','----------------'));
       $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),'',$this->formatGetal($totalen['stortingen'],2,true),'',$this->formatGetal($totalen['resultaat'],2,true),$gemiddeldeRendement,$percentage));
       //$this->pdf->row(array('Totaal','opname/storingen',$this->formatGetal($totalen['stortingen'],2,true),'toename/afname',$this->formatGetal($totalen['resultaat'],2,true)));

    }
//listarray($portefeuileWaarde);






  $this->pdf->SetWidths($this->pdf->widthB);
	}
}
?>
