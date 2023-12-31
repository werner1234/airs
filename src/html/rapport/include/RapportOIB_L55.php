<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.17 $

$Log: RapportOIB_L55.php,v $
Revision 1.17  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.16  2018/04/11 09:14:19  rvv
*** empty log message ***

Revision 1.15  2018/03/17 18:48:55  rvv
*** empty log message ***

Revision 1.14  2017/01/11 17:12:46  rvv
*** empty log message ***

Revision 1.13  2016/12/17 18:57:35  rvv
*** empty log message ***

Revision 1.12  2016/03/09 17:24:31  rvv
*** empty log message ***

Revision 1.11  2016/03/06 14:37:43  rvv
*** empty log message ***

Revision 1.10  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.9  2014/09/13 14:38:35  rvv
*** empty log message ***

Revision 1.8  2014/09/06 15:24:17  rvv
*** empty log message ***

Revision 1.7  2014/08/21 05:50:52  rvv
*** empty log message ***

Revision 1.6  2014/06/14 16:40:37  rvv
*** empty log message ***

Revision 1.5  2014/06/08 15:27:58  rvv
*** empty log message ***

Revision 1.4  2014/05/21 09:32:51  rvv
*** empty log message ***

Revision 1.3  2014/05/17 16:35:44  rvv
*** empty log message ***

Revision 1.2  2014/05/07 08:40:26  rvv
*** empty log message ***

Revision 1.1  2014/04/12 16:28:12  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIS_L55.php");

class RapportOIB_L55
{
	function RapportOIB_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->ois= new RapportOIS_L55($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Verdeling over categorieŽn";

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

		$this->totaalWaarde=$totaalWaarde;
		if(round($totaalWaarde,0)==0)
			return 0;

		// voor data
		$this->pdf->widthB = array(40,45,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;

		$this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

   $this->ois->writeRapport(true);
    unset($this->pdf->fillCell);
  	$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->underlinePercentage=0.8;

    //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
//echo count($this->ois->hseTotalen);
//listarray($this->ois->hseTotalen);
//		exit;
    $regels=0;
    foreach($this->ois->hseTotalen as $hoofdcategorie=>$categorieData)
    {
      $regels++;
      foreach($categorieData as $categorie=>$waarden)
      {
        $regels++;
      }
      $regels++;
    }
    $regels+=4;
    //echo "$regels <br>\n";
    
    $grafiekY=$this->pdf->GetY();
    if($regels*$this->pdf->rowHeight+$grafiekY > 190)
    {
      $this->pdf->AddPage();
      $grafiekY=$this->pdf->GetY();
    }

//listarray($this->ois->hseTotalen);
    $n=0;
    foreach($this->ois->hseTotalen as $hoofdcategorie=>$categorieData)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      if(isset($lastHcat) && $lastHcat <> $hoofdcategorie)
      {
        
        unset($this->pdf->fillCell);
        $this->pdf->row(array('','',
                              $this->formatGetal($totalen[$lastHcat]['waardeEUR'],2),
                              $this->formatGetal($totalen[$lastHcat]['aandeel']*100,2)));
      }
      
      $this->pdf->row(array(vertaalTekst($hoofdcategorie,$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $regel=0;
      foreach($categorieData as $categorie=>$waarden)
      {
        $regel=fillLine($this->pdf,$regel,array(1,1,1,1));
        $this->pdf->row(array('',vertaalTekst($categorie,$this->pdf->rapport_taal),
                              $this->formatGetal($waarden['waardeEUR'],2),
                              $this->formatGetal($waarden['aandeel']*100,2)));
        $totalen[$hoofdcategorie]['aandeel']+=$waarden['aandeel'];
        $totalen[$hoofdcategorie]['waardeEUR']+=$waarden['waardeEUR'];
        
        if($n<10)
          $grafiekCategorie=$categorie;
        else
          $grafiekCategorie='Overig';
          
        $grafiekData[$grafiekCategorie]+=$waarden['aandeel']*100;
  
        $grafiekKleuren[]=array($kleurenOIB[$categoriePerOmgeschijving[$grafiekCategorie]]['R']['value'],
                                $kleurenOIB[$categoriePerOmgeschijving[$grafiekCategorie]]['G']['value'],
                                $kleurenOIB[$categoriePerOmgeschijving[$grafiekCategorie]]['B']['value']);
        $totaal['aandeel']+=$waarden['aandeel'];
        $totaal['waardeEUR']+=$waarden['waardeEUR'];

       // $totaalWaarde+=$waarden['waardeEUR'];
       // echo "$hoofdcategorie $categorie | ".$waarden['aandeel']." | ".$waarden['waardeEUR']." |$totaalPercentage|$totaalWaarde <br>\n";
       $n++;
      }
      $lastHcat=$hoofdcategorie;
    }
    unset($this->pdf->fillCell);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   // $this->pdf->
    $this->pdf->row(array('','',
                         $this->formatGetal($totalen[$lastHcat]['waardeEUR'],2),
                         $this->formatGetal($totalen[$lastHcat]['aandeel']*100,2)));
                         
                         $this->pdf->CellBorders = array('','',array('TS',"UU"),array('TS',"UU"));
     $this->pdf->row(array('','',
                         $this->formatGetal($totaal['waardeEUR'],2),
                         $this->formatGetal($totaal['aandeel']*100,2)));  
    unset($this->pdf->CellBorders);


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
    $this->pdf->ln(8);
    $this->toonZorgplicht();

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

		$dbBeleggingscategorie = array();
		$dbBeleggingscategorie['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
		{
			$dbBeleggingscategorie[$categorie['Beleggingscategorie']] = $categorie['omschrijving'];
		}


		//$this->pdf->printPie($this->pdf->pieData,$kleurdata);
    
    getTypeGrafiekData($this,'Valuta');//,"AND hoofdcategorie='EFI_AAND'"
    foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
    {
      $grafiekData['OIV']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
      $grafiekData['OIV']['Percentage'][]=$waarde;
    }

    getTypeGrafiekData($this,'Hoofdcategorie');
    foreach ($this->pdf->grafiekData['Hoofdcategorie']['grafiek'] as $omschrijving=>$waarde)
    {
      $grafiekData['OIH']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
      $grafiekData['OIH']['Percentage'][]=$waarde;
    }

		if(count($this->ois->hseTotalen['Obligaties']) > 0 )
		{
			$obligatieTotaalWaarde=0;
			foreach($this->ois->hseTotalen['Obligaties'] as $categorie=>$details)
				$obligatieTotaalWaarde+=$details['waardeEUR'];

			foreach($this->ois->hseTotalen['Obligaties'] as $categorie=>$details)
			{
				$percentage=$details['waardeEUR']/$obligatieTotaalWaarde*100;
				$grafiekData['OIO']['Omschrijving'][]=$categorie." (".$this->formatGetal($percentage,1)."%)";
		  	$grafiekData['OIO']['Percentage'][]=$percentage;
				$kleur=$kleuren[$this->ois->hseCategorieReverseLookup[$categorie]];

				$this->pdf->grafiekData['obligatie']['grafiekKleur'][]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);


			}

			$diameter = 34;
			$hoek = 30;
			$dikte = 10;
			$Xas = 75;
		//	$onderY = 80;
			$xRechts = 160;
			$xLabelCorrectie=-20;
			$yLabelCorrectie=-30;

			//listarray($kleuren);
			$yas = 60;
			$this->pdf->set3dLabels($grafiekData['OIH']['Omschrijving'], $Xas + $xRechts + $xLabelCorrectie, $yas+$yLabelCorrectie, $this->pdf->grafiekData['Hoofdcategorie']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIH']['Percentage'], $this->pdf->grafiekData['Hoofdcategorie']['grafiekKleur'], $Xas + $xRechts, $yas, $diameter, $hoek, $dikte, "Verdeling over categorieŽn", 'titel');

			$yas = 110;
			$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'], $Xas + $xRechts + $xLabelCorrectie, $yas+$yLabelCorrectie, $this->pdf->grafiekData['Valuta']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIV']['Percentage'], $this->pdf->grafiekData['Valuta']['grafiekKleur'], $Xas + $xRechts, $yas, $diameter, $hoek, $dikte, "Valutaverdeling", 'titel');

			$yas = 160;
			$this->pdf->set3dLabels($grafiekData['OIO']['Omschrijving'], $Xas + $xRechts + $xLabelCorrectie, $yas+$yLabelCorrectie, $this->pdf->grafiekData['obligatie']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIO']['Percentage'], $this->pdf->grafiekData['obligatie']['grafiekKleur'], $Xas + $xRechts, $yas, $diameter, $hoek, $dikte, "Verdeling obligaties", 'titel');

		}
		else
		{

			$diameter = 34;
			$hoek = 30;
			$dikte = 10;
			$Xas = 65;
			$onderY = 80;
			$xRechts = 160;

			$yas = 58;
			$this->pdf->set3dLabels($grafiekData['OIH']['Omschrijving'], $Xas + $xRechts + 10, $yas, $this->pdf->grafiekData['Hoofdcategorie']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIH']['Percentage'], $this->pdf->grafiekData['Hoofdcategorie']['grafiekKleur'], $Xas + $xRechts, $yas, $diameter, $hoek, $dikte, "Hoofdcategorie", 'titel');

			$yas = 135;
			$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'], $Xas + $xRechts + 10, $yas, $this->pdf->grafiekData['Valuta']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIV']['Percentage'], $this->pdf->grafiekData['Valuta']['grafiekKleur'], $Xas + $xRechts, $yas, $diameter, $hoek, $dikte, "Valutaverdeling", 'titel');
		}
 
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize); 
	}
  
  function toonZorgplicht()
  {
    global $__appvar;
    $DB=new DB();


$query="SELECT
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->totaalWaarde." as percentage,
ZorgplichtPerBeleggingscategorie.Zorgplicht,
Zorgplichtcategorien.Omschrijving
FROM
Zorgplichtcategorien
INNER JOIN ZorgplichtPerBeleggingscategorie ON Zorgplichtcategorien.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN TijdelijkeRapportage ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
INNER JOIN CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie 
 
WHERE Zorgplichtcategorien.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY Beleggingscategorien.Afdrukvolgorde
";

    $DB->SQL($query); //echo $query;exit;
    $DB->Query();
		while($data= $DB->nextRecord())
		{
		  $categorieWaarden[$data['Zorgplicht']]=$data['percentage']*100;
      $categorieOmschrijving[$data['Zorgplicht']]=$data['Omschrijving'];
		}
    
    $tmp=$this->pdf->portefeuilledata;
    $tmp['Portefeuille']=$this->portefeuille;
    $zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($tmp,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

//listarray($zpwaarde['conclusie']);
    //listarray($tmp);exit;

    $this->pdf->SetAligns(array('L','R','R','R','R','R'));
    
   	
     	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize); 
      $this->pdf->SetWidths(array(120));
    $this->pdf->row(array('Bandbreedtes per Categorie'));
  	$this->pdf->SetWidths(array(40,16,18,16,20,20));
    $beginY=$this->pdf->getY();
    $this->pdf->row(array('','Minimaal','Strategisch','Maximaal',"Werkelijk","Conclusie"));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)
    
    
  //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen=''; 
      //if($zpwaarde['categorien'][$cat]['Minimum'])   
        $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      //else
     //   $min='';   
      //if($zpwaarde['categorien'][$cat]['Maximum'])  
        $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
     // else
     //   $max='';  
     $norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
      
  	  $this->pdf->row(array($categorieOmschrijving[$cat],$min,$norm,$max,$this->formatGetal($categorieWaarden[$cat],1)."%",$tmp[$cat][5]));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge,$beginY,130,count($categorieWaarden)*4+4);
    
  }
}
?>