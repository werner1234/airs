<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/11/10 15:42:19 $
File Versie					: $Revision: 1.1 $

$Log: RapportAFM_L19.php,v $
Revision 1.1  2012/11/10 15:42:19  rvv
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

class RapportAFM_L19
{
	function RapportAFM_L19($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "AFM";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_AFM_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in AFM categorien";

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

  	$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");


		if($grandtotaal)
		{
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


    
    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);

		// voor data
		$this->pdf->widthB = array(20,55,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(60,15,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');

		$this->pdf->AddPage();
    $afmCategorieverdeling=getAFMWaarden($this->portefeuille,$this->rapportageDatum);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("AFM Categorien"));
		$this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		foreach($afmCategorieverdeling['verdeling'] as $categorie=>$categorieData)
		{
   		$this->pdf->row(array("",
											$categorieData['omschrijving'],
											$this->formatGetal($categorieData['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetalKoers($categorieData['procent']*100,$this->pdf->rapport_OIB_decimaal)));
                      
      if($categorieData['procent'] >0)
      {
       $kleurdata[$categorieData['omschrijving']]['kleur'] = $kleuren[$categorie];
       $kleurdata[$categorieData['omschrijving']]['percentage'] = $categorieData['procent']*100;
		  }
		}
    $this->pdf->CellBorders=array('','',array('UU','T'),array('UU','T'));
    $this->pdf->Ln();
    $this->pdf->row(array("",'Totale actuele waarde portefeuille',
											$this->formatGetal($afmCategorieverdeling['totaalWaarde'],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetalKoers(100,$this->pdf->rapport_OIB_decimaal)));
    $this->pdf->CellBorders=array();  
        
    $this->pdf->ln(8);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("AFM Risico"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->Row(array('','AFM-standaarddeviatie','',$this->formatGetal($afm['std'],2)." %"));

    if($this->pdf->debug)
      listarray($afm);     
        
    $this->pdf->ln(2);
    if($this->pdf->rapport_OIB_valutaoverzicht == 1)
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		elseif($this->pdf->rapport_OIB_valutaoverzicht == 2)
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		if($this->pdf->rapport_OIB_rendement == 1)
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIB_rendementKort);
    
    $this->pdf->headerStart=45;
		$this->pdf->printPie($this->pdf->pieData,$kleurdata);
    
    $this->pdf->afmPage2=true;
    $this->pdf->AddPage();
    $this->pdf->Ln();
    
             

		foreach($afmCategorieverdeling['verdeling'] as $categorie=>$categorieData)
		{
		  $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      if($categorieData['actuelePortefeuilleWaardeEuro']==0)
        $this->pdf->row(array($categorieData['omschrijving'],'',
	             $this->formatGetal($categorieData['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIB_decimaal),
               $this->formatGetal(0,$this->pdf->rapport_OIB_decimaal),
               $this->formatGetal($categorieData['procent']*100,$this->pdf->rapport_OIB_decimaal)));
      else
        $this->pdf->row(array($categorieData['omschrijving']));
                 
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->SetWidths($this->pdf->widthB);
      foreach($categorieData['fondsen'] as $fonds=>$fondsWaarden)
      { 
        $this->pdf->row(array("",
               $fonds,
			         $this->formatGetal($fondsWaarden['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIB_decimaal),
		           $this->formatGetal($fondsWaarden['actuelePortefeuilleWaardeEuro']/$categorieData['actuelePortefeuilleWaardeEuro']*100,$this->pdf->rapport_OIB_decimaal),
			         $this->formatGetal($fondsWaarden['procent']*100,$this->pdf->rapport_OIB_decimaal)));
      }
   
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      if($categorieData['actuelePortefeuilleWaardeEuro'] > 0)
        $this->pdf->row(array('','',$this->formatGetal($categorieData['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIB_decimaal),
               $this->formatGetal(100,$this->pdf->rapport_OIB_decimaal),
               $this->formatGetal($categorieData['procent']*100,$this->pdf->rapport_OIB_decimaal)));
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		}
		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
$this->pdf->afmPage2=false;
	}
}
?>