<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/10/16 05:36:15 $
File Versie					: $Revision: 1.22 $

$Log: RapportOIB_L68.php,v $
Revision 1.22  2019/10/16 05:36:15  rvv
*** empty log message ***

Revision 1.21  2019/07/13 17:49:46  rvv
*** empty log message ***

Revision 1.20  2019/05/25 16:22:07  rvv
*** empty log message ***

Revision 1.19  2019/05/01 15:53:25  rvv
*** empty log message ***

Revision 1.18  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.17  2018/09/02 12:02:03  rvv
*** empty log message ***

Revision 1.16  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.15  2018/07/18 15:45:00  rvv
*** empty log message ***

Revision 1.14  2018/06/27 16:13:50  rvv
*** empty log message ***

Revision 1.13  2017/12/09 17:54:25  rvv
*** empty log message ***

Revision 1.12  2017/10/01 14:29:55  rvv
*** empty log message ***

Revision 1.11  2017/04/12 15:38:14  rvv
*** empty log message ***

Revision 1.10  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.9  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.8  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.7  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.6  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.5  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.2  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportOIB_L68
{
	function RapportOIB_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	function getCRMnaam($portefeuille)
	{
		$db = new DB();
		$query="SELECT naam FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naamParts=explode('-',$crmData['naam'],2);
		$naam=trim($naamParts[1]);
		if($naam<>'')
			return $naam;
		else
			return $portefeuille;
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
    
    if(!is_array($this->pdf->grafiekKleuren))
	  {
	      $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	    	$DB->SQL($q);
  	  	$DB->Query();
    		$kleuren = $DB->LookupRecord();
    		$kleuren = unserialize($kleuren['grafiek_kleur']);
    		$this->pdf->grafiekKleuren=$kleuren;
	  }

		if(is_array($this->pdf->portefeuilles))
			$consolidatie=true;
		else
			$consolidatie=false;
    
    $portefeuilleWaarden=array();
		$aantalPortefeuilles=0;
    $totaalWaarde=0;
    if($consolidatie)
    {
			$aantalPortefeuilles=count($this->pdf->portefeuilles);
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $portefeuilleWaarden[$portefeuille]['belCatWaarde']=array();
        if($this->pdf->lastPOST['doorkijk']==1)
				{
					vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum), $portefeuille, $this->rapportageDatum);
					//$gegevens = $this->bepaaldFondsWaardenVerdiept($portefeuille, $this->rapportageDatum);
					$gegevens = bepaalHuidfondsenVerdeling($portefeuille,  $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->portefeuilledata['RapportageValuta']);
				}
        else
          $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum);
        foreach($gegevens as $waarde)
        {
          $portefeuilleWaarden[$portefeuille]['belCatWaarde'][$waarde['beleggingscategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
          $portefeuilleWaarden[$portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
          $categorieVolgorde[$waarde['beleggingscategorieVolgorde']]=$waarde['beleggingscategorie'];
          $categorieOmschrijving[$waarde['beleggingscategorie']]=$waarde['beleggingscategorieOmschrijving'];
          $totaalWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
        }
      }
      foreach($portefeuilleWaarden as $portefeuille=>$waarden)
      {
        foreach($waarden['belCatWaarde'] as $categorie=>$waardeEur)
        {
          $percentage=($waardeEur/$waarden['totaleWaarde']);
          $portefeuilleWaarden[$portefeuille]['belCatPercentage'][$categorie]=$percentage;
          $portefeuilleWaarden[$portefeuille]['totalePercentage']+=$percentage;
        }
      }
    }
    else
    {
      if($this->pdf->lastPOST['doorkijk']==1)
      {
        //$gegevens = $this->bepaaldFondsWaardenVerdiept($this->portefeuille, $this->rapportageDatum);
				$gegevens = bepaalHuidfondsenVerdeling($this->portefeuille,  $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->portefeuilledata['RapportageValuta']);
        foreach($gegevens as $waarde)
        {
          $portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$waarde['beleggingscategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
          $portefeuilleWaarden[$this->portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
          $categorieVolgorde[$waarde['beleggingscategorieVolgorde']]=$waarde['beleggingscategorie'];
          $categorieOmschrijving[$waarde['beleggingscategorie']]=$waarde['beleggingscategorieOmschrijving'];
          $totaalWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
        }
        foreach($portefeuilleWaarden[$this->portefeuille]['belCatWaarde'] as $categorie=>$waardeEur)
        {
          $percentage=($waardeEur/$portefeuilleWaarden[$this->portefeuille]['totaleWaarde']);
          $portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorie]=$percentage;
          $portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage;
        }
  
      }
      else
      {
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
      $portefeuilleWaarden[$this->portefeuille]['totaleWaarde']=$totaalWaarde;
      

		  $query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.beleggingscategorieVolgorde, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";
		  debugSpecial($query,__FILE__,__LINE__);

		  $DB->SQL($query);
		  $DB->Query();

  		while($categorien = $DB->NextRecord())
	  	{
	  	  if($categorien['beleggingscategorie']=='')
        {
          $categorien['beleggingscategorie']='GeenCategorie';
          $categorien['Omschrijving']='Geen categorie';
        }
         $categorieOmschrijving[$categorien['beleggingscategorie']]=$categorien['Omschrijving'];
         $categorieVolgorde[$categorien['beleggingscategorieVolgorde']]=$categorien['beleggingscategorie'];
         $portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$categorien['beleggingscategorie']]+=$categorien['actuelePortefeuilleWaardeEuro'];
         $percentage=($categorien['actuelePortefeuilleWaardeEuro']/$totaalWaarde);
         $portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorien['beleggingscategorie']]=$percentage;
         $portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage;
      }
      }
    }
	
    $this->pdf->gebruiktePortefeuilles=array();
    foreach($portefeuilleWaarden as $portefeuille=>$pdata)
    {
      if(round($portefeuilleWaarden[$portefeuille]['totaleWaarde'],2)==0.00)
      {
        unset($portefeuilleWaarden[$portefeuille]);
      }
      else
      {
        $this->pdf->gebruiktePortefeuilles[]=$portefeuille;
      }
    }

		// voor kopjes
		$pw=14;
		$portw=23;
		$tw=$pw+$portw;
		$this->pdf->widthA = array(60,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor data
		$this->pdf->widthB = array(65,$tw,$tw,$tw,$tw,$tw,$tw,$tw);
		$this->pdf->alignB = array('L','C','C','C','C','C','C','C','C');
		if(is_array($this->pdf->portefeuilles))
		{
			$query="SELECT Portefeuille,ClientVermogensbeheerder FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')";
			$DB->SQL($query);
			$DB->Query();
			while($portefeuille = $DB->NextRecord())
			{
				$this->pdf->clientVermogensbeheerder[$portefeuille['Portefeuille']]=$this->getCRMnaam($portefeuille['Portefeuille']);
			}
		}

	
		$this->pdf->AddPage();
		$this->pdf->templateVars['OIBPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['OIBPaginas']=$this->pdf->rapport_titel;
		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
 		$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);

    ksort($categorieVolgorde);
		$regelData=array();
		$regelDataTotaal=array();
		$totaalPercentage=0;
    $catBar=false;
    foreach($categorieVolgorde as $categorie)
    {
      $regelTotaal=0;
      foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
      {
				$regelData[$portefeuille][$categorieOmschrijving[$categorie]]=array('waarde'=>$this->formatGetal($belCatData['belCatWaarde'][$categorie],0),'percentage'=>$this->formatGetal($belCatData['belCatPercentage'][$categorie]*100,1));
        $regelTotaal+=$belCatData['belCatWaarde'][$categorie];
      }  
      if($consolidatie)
      {
				$percentage=$regelTotaal/$totaalWaarde;
				$regelData['Totaal'][$categorieOmschrijving[$categorie]]=array('waarde'=>$this->formatGetal($regelTotaal,0),'percentage'=>$this->formatGetal($percentage*100,1));

				//echo "$portefeuille $percentage=$regelTotaal/$totaalWaarde; ->$totaalPercentage <br>\n";
        $totaalPercentage+=$percentage;
      }
			$percentage=$regelTotaal/$totaalWaarde*100;
      if($percentage<0)
        $catBar=true;
      $categorieVerdeling['percentage'][$categorieOmschrijving[$categorie]]=$percentage;
      $categorieVerdeling['kleur'][]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
			$categorieVerdeling['kleurBar'][$categorieOmschrijving[$categorie]]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
    }

    $regel=array('Totalen');
		foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
  		$regelDataTotaal[$portefeuille]=array('waarde'=>$this->formatGetal($belCatData['totaleWaarde'],0),'percentage'=>$this->formatGetal($belCatData['totalePercentage']*100,1));
    if($consolidatie)
  			$regelDataTotaal['Totaal']=array('waarde'=>$this->formatGetal($totaalWaarde,0),'percentage'=>$this->formatGetal($totaalPercentage*100,1));

    $portefeuilleAantal=count($portefeuilleWaarden);
		$blokken=ceil($portefeuilleAantal/5);
    $aantalCetegorieen=count($categorieVolgorde);
   
		for($i=0;$i<$blokken;$i++)
		{
      if($this->pdf->getY()+($aantalCetegorieen+1)*$this->pdf->rowHeight > $this->pdf->PageBreakTrigger)
        $this->pdf->addPage();
			//Kop regel
			$regel = array();
			array_push($regel, 'Beleggingscategorie');
			if($i==0 && $consolidatie==true)
		  	array_push($regel, 'Totaal');
			else
				array_push($regel, '');
			//array_push($regel, '');
			$min=$i*5;
			$max=($i+1)*5;
			$n=0;
			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				if($n>=$min && $n<$max)
				{
					$kop=$this->getCRMnaam($portefeuille);
					array_push($regel, $kop);
					//array_push($regel,'');
				}
				$n++;
			}

			if($aantalPortefeuilles>5)
			{
				if($i==0)
					$this->pdf->ln(-5);
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->Row($regel);
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			}
			//categorieen
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
		  foreach($categorieVolgorde as $categorie)
			{
				$regel = array();
				if($i==0  && $consolidatie==true)
				{
					array_push($regel, $categorieOmschrijving[$categorie]);
					array_push($regel, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['waarde']);
					array_push($regel, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['percentage']);
				}
				else
				{
					array_push($regel, $categorieOmschrijving[$categorie]);
					if($consolidatie==true)
						$cols=2;
					else
						$cols=0;
					for($a=0;$a<$cols;$a++)
					  array_push($regel,'');
				}
				$min=$i*5;
				$max=($i+1)*5;
				$n=0;
				foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
				{
					if($n>=$min && $n<$max)
					{
						array_push($regel, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['waarde']);
						array_push($regel, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['percentage']);
					}
					$n++;
				}
				$this->pdf->Row($regel);
			}

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			//Totaal regel
			$regel = array();
			if($consolidatie==true)
			{
				array_push($regel, 'Totalen');
				if($i==0)
				{
					array_push($regel, $regelDataTotaal['Totaal']['waarde']);
					array_push($regel, $regelDataTotaal['Totaal']['percentage']);
				}
				else
				{
					array_push($regel,'');
					array_push($regel,'');
				}
			}
			else
			{
				if($consolidatie==true)
					$cols=3;
				else
					$cols=1;
				for($a=0;$a<$cols;$a++)
					array_push($regel,'');
			}
			$max=($i+1)*5;
			$n=0;
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				if($n>=$min && $n<$max)
				{
					array_push($regel, $regelDataTotaal[$portefeuille]['waarde']);
			    array_push($regel, $regelDataTotaal[$portefeuille]['percentage']);
				}
				$n++;
			}
			$this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
			$this->pdf->Row($regel);
			$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}


//    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
//    $this->pdf->Row($regel);
    if($this->pdf->getY() > 110)
		{
			$this->pdf->addPage();
		  $grafiekY=$this->pdf->getY()+15;
    }
		else
			$grafiekY=120;





    if(isset($this->pdf->__appvar['consolidatie']))
		{
	  		    $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
		    $DB->SQL($query);
	    	$DB->Query();
	      while($tmp = $DB->nextRecord())
	        $portefeuilledata[$tmp['Portefeuille']]=$tmp;

      $portefeuilleKleur=array();
			$portefeuilleKleurBar=array();
			$barGraph=false;


			$randomKleuren=array();
			foreach($this->pdf->grafiekKleuren['OIB'] as $categorie=>$kleur)
				$randomKleuren[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
			$i=0;
      foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
      {
        $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
				//$kleur=array();
	      if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
					$kleur = $randomKleuren[$i];

        if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
					$kleur = array(rand(0, 255), rand(0, 255), rand(0, 255));

				$kop=($i+1).'.||'.$this->getCRMnaam($portefeuille);
			//	echo $kop."<br>\n";ob_flush();
        $portefeuilleAandeel[$kop]=$waarde['totaleWaarde']/$totaalWaarde*100;
        $portefeuilleKleur[]=$kleur;
				$portefeuilleKleurBar[$kop]=$kleur;
	      if($waarde['totaleWaarde'] < 0)
				  $barGraph=true;

				$i++;
      }
    
      if(count($categorieVerdeling['percentage']) > 18 || count($portefeuilleAandeel) > 18 && $this->pdf->getY() > 70)
      {
        $this->pdf->addPage();
        $grafiekY=55;
      }
    
      $this->pdf->setY($grafiekY-10);
      $this->pdf->SetAligns(array('C','C'));
      $this->pdf->SetWidths(array(140,140));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
      $this->pdf->row(array(vertaalTekst("Verdeling over categorieën",$this->pdf->rapport_taal),vertaalTekst("Verdeling over portefeuilles",$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      if($catBar==false)
      {
        $this->pdf->setXY(20,$grafiekY);
        PieChart($this->pdf,65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);
      }
      else
      {
        $this->pdf->setXY(50,$grafiekY);
        $this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
      }

			if($barGraph==false)
			{
				$this->pdf->setXY(160,$grafiekY);
				PieChart($this->pdf,65, 65, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleur);
			}
			else
			{
				$this->pdf->setXY(190,$grafiekY);
				$this->BarDiagram(80, 100, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleurBar);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
			}
    

		}
		else
		{
      
      if($catBar==false)
      {
        $this->pdf->setXY(20,$grafiekY);
        PieChart($this->pdf,65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);
      }
      else
      {
        $this->pdf->setXY(50,$grafiekY);
        $this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
      }
    }

	}



	function SetLegends2($data, $format)
	{
		$this->pdf->legends=array();
		$this->pdf->wLegend=0;

		$this->pdf->sum=array_sum($data);

		$this->pdf->NbVal=count($data);
		foreach($data as $l=>$val)
		{
      if(strpos($l,'||')>0)
      {
        $parts=explode("||",$l);
        $l=$parts[1];
      }
      
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';
			$p=sprintf('%.1f',$val).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}
	}

	function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
	{
		$pdfObject = &$object;
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
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
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
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');

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
//listarray($colorArray);listarray($data);
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


}
?>