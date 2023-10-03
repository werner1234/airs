<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/27 16:23:17 $
File Versie					: $Revision: 1.26 $

$Log: RapportKERNV_L12.php,v $
Revision 1.26  2020/06/27 16:23:17  rvv
*** empty log message ***

Revision 1.25  2020/04/18 17:06:34  rvv
*** empty log message ***

Revision 1.24  2020/04/15 16:13:11  rvv
*** empty log message ***

Revision 1.23  2020/03/11 15:18:12  rvv
*** empty log message ***

Revision 1.22  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.21  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.20  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.19  2020/02/15 18:29:05  rvv
*** empty log message ***

Revision 1.18  2020/02/08 10:33:21  rvv
*** empty log message ***

Revision 1.17  2019/12/21 14:08:32  rvv
*** empty log message ***

Revision 1.16  2019/12/07 17:48:23  rvv
*** empty log message ***

Revision 1.15  2019/11/24 14:27:15  rvv
*** empty log message ***

Revision 1.14  2019/11/23 18:36:42  rvv
*** empty log message ***

Revision 1.13  2019/11/09 16:39:21  rvv
*** empty log message ***

Revision 1.12  2019/04/13 17:42:49  rvv
*** empty log message ***

Revision 1.11  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.10  2019/02/20 16:51:10  rvv
*** empty log message ***

Revision 1.7  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.6  2018/07/12 06:37:55  rvv
*** empty log message ***

Revision 1.5  2018/07/11 16:16:40  rvv
*** empty log message ***

Revision 1.4  2018/06/28 05:39:31  rvv
*** empty log message ***

Revision 1.3  2018/06/27 16:13:50  rvv
*** empty log message ***

Revision 1.2  2018/06/24 12:47:04  rvv
*** empty log message ***

Revision 1.1  2018/05/26 17:24:24  rvv
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

class RapportKERNV_L12
{
	function RapportKERNV_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "";//Onderverdeling in beleggingscategorie";

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
		if($this->pdf->checkRappNaam==true)
		{
			$extraVeld=',RappNaam';
		}
		$query="SELECT naam $extraVeld FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naamParts=explode('-',$crmData['naam'],2);
		$naam=trim($naamParts[1]);

		if($crmData['RappNaam'] <> '')
			return $crmData['RappNaam'] ;
		elseif($naam<>'')
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
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();


		  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");


	  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
	}
	
	function bepaaldFondsWaardenVerdiept($portefeuille,$einddatum)
  {
    $startjaar=true;
    /*
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
    listarray($gegevens);
    return $gegevens;
    */
    $this->verdiept = new portefeuilleVerdiept($this->pdf,$portefeuille,$einddatum);
    $verdiepteFondsen = $this->verdiept->getFondsen();
  //listarray($portefeuille);listarray($verdiepteFondsen);ob_flush();
    foreach ($verdiepteFondsen as $fonds)
      $this->verdiept->bepaalVerdeling($fonds,$this->verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum);
  
   
    $fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
    $correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
    foreach($fondswaarden as $i=>$fondsData)
    {
      //
      if(isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
      {
      //echo $fondsData['fonds'];ob_flush();exit;
        $fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
        $fondsWaardeHuis=$this->pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
        $aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
        //echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis ";exit;
        unset($fondswaarden[$i]);
        foreach($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
        {
          foreach ($details as $element => $emementDetail)
          {
          
            if(isset($emementDetail['overige']))
            {
              foreach($correctieVelden as $veld)
                $emementDetail['overige'][$veld]=$emementDetail['overige'][$veld]*$aandeel;
              unset($emementDetail['overige']['WaardeEuro']);
              unset($emementDetail['overige']['koersLeeftijd']);
              unset($emementDetail['overige']['FondsOmschrijving']);
              unset($emementDetail['overige']['Fonds']);
              $fondswaarden[] = $emementDetail['overige'];
            }
          }
        }
      }
    }
    $fondswaarden  = array_values($fondswaarden);
    $tmp=array();
    $conversies=array('ActuelePortefeuilleWaardeEuro'=>'actuelePortefeuilleWaardeEuro');
    foreach($fondswaarden as $mixedInstrument)
    {
      $instrument=array();
      foreach($mixedInstrument as $index=>$value)
      {
        if(isset($conversies[$index]))
          $instrument[$conversies[$index]] = $value;
        else
          $instrument[$index] = $value;
      }
      unset($instrument['voorgaandejarenactief']);
    
      $key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
      if(isset($tmp[$key]))
      {
        foreach($correctieVelden as $veld)
        {
          $veld=($veld);
          $tmp[$key][$veld] += $instrument[$veld];
        }
      }
      else
        $tmp[$key]=$instrument;
      //	listarray($instrument);
    }
    $fondswaarden  = array_values($tmp);
    //echo $portefeuille,$einddatum;listarray($fondswaarden);
    return $fondswaarden;
  }
  
	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
    
    $this->pdf->AddPage();
    $this->pdf->templateVars['KERNVPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['KERNVPaginas']=$this->pdf->rapport_titel;

	      $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	    	$DB->SQL($q);
  	  	$DB->Query();
    		$kleuren = $DB->LookupRecord();
    		$kleuren = unserialize($kleuren['grafiek_kleur']);
    		$this->pdf->grafiekKleuren=$kleuren;
      $this->categorieKleuren=$kleuren['OIB'];

		if(is_array($this->pdf->portefeuilles))
			$consolidatie=true;
		else
			$consolidatie=false;
    
    $portefeuilleWaarden=array();
		$aantalPortefeuilles=0;
    $totaalWaarde=0;
    if($consolidatie)
    {
      $actievePortefeuilles=array();
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $query="SELECT einddatum FROM Portefeuilles WHERE portefeuille='$portefeuille'";
        $DB->SQL($query);
        $pdata = $DB->lookupRecord();
        if(db2jul($pdata['einddatum']) > db2jul($this->rapportageDatum))
          $actievePortefeuilles[]=$portefeuille;
    
      }
      
			$aantalPortefeuilles=count($actievePortefeuilles);
      foreach($actievePortefeuilles as $portefeuille)
      {
        $portefeuilleWaarden[$portefeuille]['belCatWaarde']=array();
        if($this->pdf->lastPOST['doorkijk']==1)
				{
					vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum), $portefeuille, $this->rapportageDatum);
					$gegevens = $this->bepaaldFondsWaardenVerdiept($portefeuille, $this->rapportageDatum);
				}
        else
          $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum);
        foreach($gegevens as $waarde)
        {
					if($waarde['hoofdcategorie']=='')
					{
						$waarde['hoofdcategorie']='GeenCategorie';
						$waarde['hoofdcategorieOmschrijving']='Geen categorie';
					}
          $portefeuilleWaarden[$portefeuille]['belCatWaarde'][$waarde['hoofdcategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
          $portefeuilleWaarden[$portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
          $categorieVolgorde[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieVolgorde'];
          $categorieOmschrijving[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieOmschrijving'];
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
        $gegevens = $this->bepaaldFondsWaardenVerdiept($this->portefeuille, $this->rapportageDatum);
        foreach($gegevens as $waarde)
        {
					if($waarde['hoofdcategorie']=='')
					{
						$waarde['hoofdcategorie']='GeenCategorie';
						$waarde['hoofdcategorieOmschrijving']='Geen categorie';
					}
          $portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$waarde['hoofdcategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
          $portefeuilleWaarden[$this->portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
					$categorieVolgorde[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieVolgorde'];
          $categorieOmschrijving[$waarde['hoofdcategorie']]=$waarde['hoofdcategorieOmschrijving'];
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
      

		  $query = "SELECT TijdelijkeRapportage.hoofdcategorieOmschrijving as Omschrijving, TijdelijkeRapportage.hoofdcategorieVolgorde, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.hoofdcategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.hoofdcategorie".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc";
		  debugSpecial($query,__FILE__,__LINE__);

		  $DB->SQL($query);
		  $DB->Query();

  		while($categorien = $DB->NextRecord())
	  	{
	  	  if($categorien['hoofdcategorie']=='')
        {
          $categorien['hoofdcategorie']='GeenCategorie';
          $categorien['Omschrijving']='Geen categorie';
        }
         $categorieOmschrijving[$categorien['hoofdcategorie']]=$categorien['Omschrijving'];
         $categorieVolgorde[$categorien['hoofdcategorie']]=$categorien['hoofdcategorieVolgorde'];
         $portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$categorien['hoofdcategorie']]+=$categorien['actuelePortefeuilleWaardeEuro'];
         $percentage=($categorien['actuelePortefeuilleWaardeEuro']/$totaalWaarde);
         $portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorien['hoofdcategorie']]=$percentage;
         $portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage;
      }
      }
    }
  //2+35+extraw
    
    $maxPortefeuilles=5;
    $extraW=5;
		// voor kopjes
		$pw=12;
    $eurw=5;
		$portw=20;
		
		$headerWidth=$eurw+$portw+$pw-2;
		//echo $headerWidth;exit;

		$this->pdf->widthA = array(50+3,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor data
    
    
    $this->pdf->SetFont($this->pdf->rapport_fontEur,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(4,5,'',0,0,'L',0);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),
      array($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize));
    
    $this->clientVermogensbeheerder=array();
		if(is_array($this->pdf->portefeuilles))
		{
			$query="SELECT Portefeuille,ClientVermogensbeheerder FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')";
			$DB->SQL($query);
			$DB->Query();
			while($portefeuille = $DB->NextRecord())
			{
				$this->clientVermogensbeheerder[$portefeuille['Portefeuille']]=$this->getCRMnaam($portefeuille['Portefeuille']);
			}
		}
    
 //   $this->clientVermogensbeheerder['471568767']='test p1 lange naam extri nopg meer';
//listarray($this->clientVermogensbeheerder);exit;
  //  if(is_array($this->pdf->portefeuilles))
  //  {


		//  }
		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
 		$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);

		asort ($categorieVolgorde);

		$regelData=array();
		$regelDataTotaal=array();
    $portefeuilleGrafiekDataDetail=array();
    $portefeuilleGrafiekKleurDetail=array();
		$totaalPercentage=0;
		$barGraph=false;
    $pieOke=true;

    foreach($categorieVolgorde as $categorie=>$volgorde)
    {

      
      $regelTotaal=0;
      foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
      {
        $regelData[$portefeuille][$categorieOmschrijving[$categorie]]=array('waarde'=>$belCatData['belCatWaarde'][$categorie],'percentage'=>$belCatData['belCatPercentage'][$categorie]*100);
        $regelTotaal+=$belCatData['belCatWaarde'][$categorie];
      }  
      if($consolidatie)
      {
				$percentage=$regelTotaal/$totaalWaarde;
        $regelData['Totaal'][$categorieOmschrijving[$categorie]]=array('waarde'=>$regelTotaal,'percentage'=>$percentage*100);
        if($percentage<0)
          $pieOke=false;
        $portefeuilleGrafiekDataDetail[$categorie] = round($percentage * 100, 1);
        $portefeuilleGrafiekKleurDetail[] = array($this->categorieKleuren[$categorie]['R']['value'], $this->categorieKleuren[$categorie]['G']['value'], $this->categorieKleuren[$categorie]['B']['value']);
				//echo "$portefeuille $percentage=$regelTotaal/$totaalWaarde; ->$totaalPercentage <br>\n";
        $totaalPercentage+=$percentage;
      }
			if($regelTotaal<0)
			  $barGraph=true;
      $categorieVerdeling['percentage'][$categorieOmschrijving[$categorie]]=$regelTotaal/$totaalWaarde*100;
      $categorieVerdeling['kleur'][]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
			$categorieVerdeling['kleurBar'][$categorieOmschrijving[$categorie]]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
    }


    $x=65+$this->pdf->marge+3+$extraW;
    $y=$this->pdf->getY();
    $this->pdf->setXY($x, $y+(count($categorieVolgorde)+9)*$this->pdf->rowHeight+5);
    
    $totaalGrafiek=array($portefeuilleGrafiekDataDetail,$portefeuilleGrafiekKleurDetail);
		
    foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
    {
      $portefeuilleGrafiekDataDetail=array();
      $portefeuilleGrafiekKleurDetail=array();
      $pieOke=true;
			foreach($categorieVolgorde as $categorie=>$volgorde)
      {
        if($belCatData['belCatPercentage'][$categorie] <> 0)
        {
          $percentage=$belCatData['belCatPercentage'][$categorie];
          //foreach($belCatData['belCatPercentage'] as $categorie=>$percentage)
          //{
          if ($percentage < 0)
          {
            $pieOke = false;
          }
          $portefeuilleGrafiekDataDetail[] = round($percentage * 100, 1);
          $portefeuilleGrafiekKleurDetail[] = array($this->categorieKleuren[$categorie]['R']['value'], $this->categorieKleuren[$categorie]['G']['value'], $this->categorieKleuren[$categorie]['B']['value']);
        }
      }
			$portefeuilleWaardenGrafiek[$portefeuille]['waarde']=$portefeuilleGrafiekDataDetail;
			$portefeuilleWaardenGrafiek[$portefeuille]['kleur']=$portefeuilleGrafiekKleurDetail;
      $this->pdf->setY(80);
    }
    $this->pdf->setXY($this->pdf->marge, $y);

    $regel=array('Totaal');
		foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
      $regelDataTotaal[$portefeuille]=array('waarde'=>$belCatData['totaleWaarde'],'percentage'=>$belCatData['totalePercentage']*100);
    if($consolidatie)
      $regelDataTotaal['Totaal']=array('waarde'=>$totaalWaarde,'percentage'=>$totaalPercentage*100);

    $portefeuilleAantal=count($portefeuilleWaarden);
   // echo $portefeuilleAantal."<br>\n";listarray($portefeuilleWaarden);exit;
    
    $portrefeuilleDataPerBlok=array();
    $i=0;
		$blokken=ceil($portefeuilleAantal/$maxPortefeuilles);
		$n=1;
    foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
    {
      $portrefeuilleDataPerBlok[$i][$portefeuille]=$belCatData;
      if($n%$maxPortefeuilles==0)
        $i++;
      $n++;
    }


		for($b=0;$b<$blokken;$b++)
		{
      $portefeuilleWaarden= $portrefeuilleDataPerBlok[$b];
   //echo "$i | <br>\n"; ob_flush();
		  if($b>0)
		    $this->pdf->addPage();
			//Kop regel
			$regel = array();
      $regelXls = array();
			array_push($regel, 'Beleggingscategorie');
	  	array_push($regel, 'Totaal');

			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
   
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
					$kop=$this->getCRMnaam($portefeuille);
					array_push($regel, $kop);
          array_push($regelXls, $kop);
			}

			/*
			if( $i>0)//$aantalPortefeuilles>$maxPortefeuilles &&
			{
        $this->pdf->SetFillColor($this->pdf->rapport_kop_kleur[0],$this->pdf->rapport_kop_kleur[1],$this->pdf->rapport_kop_kleur[2]);
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor(255,255,255);
        
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->Row($regel);
        $this->pdf->excelData[]=$regelXls;
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
        
      }
			*/
      
      
      if(count($this->pdf->portefeuilles) < 60)
      {
        $this->pdf->SetFillColor($this->pdf->rapport_kop_kleur[0],$this->pdf->rapport_kop_kleur[1],$this->pdf->rapport_kop_kleur[2]);
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor(255,255,255);
  
        $tweeRegels=false;
        foreach($this->clientVermogensbeheerder as $portefeuilleNaam)
        {
          $width = $this->pdf->getStringWidth($portefeuilleNaam);
         //   echo "$portefeuilleNaam $width < ".($headerWidth-2) ." <br>\n";
          if ($width >= $headerWidth-2)
          {
            $tweeRegels = true;
          }
        }
     //   echo "$headerWidth $tweeRegels ";exit;
        $lastX=$this->pdf->marge;

        // listarray($this->pdf->widthA);
        foreach($this->pdf->widthA  as $i=>$width)
        {
          if($width==$eurw)
            $extraX=2;
          else
            $extraX=0;
          $this->pdf->Rect($lastX+$extraX, $this->pdf->getY()-.5, $width, 5+$tweeRegels*4, 'F');

          $lastX += $width;
          if($i==count($portefeuilleWaarden)*3+3)
            break;
        }
        
//S        logScherm('aantal:'.count($this->clientVermogensbeheerder));
        
        $this->pdf->SetX($this->pdf->marge);
        $this->pdf->Cell(53, 4, vertaalTekst("Beleggingscategorie", $this->pdf->rapport_taal), 0, 0, "L",0);
        $this->pdf->Cell(2, 4, '', 0, 0, "C",0);
        $this->pdf->Cell($headerWidth, 4, vertaalTekst('Totaal',$this->pdf->rapport_taal), 0, 0, "C",0);
        
        $this->pdf->SetX($this->pdf->marge);
        $this->pdf->Cell(50+$headerWidth+$extraW, 6, '', 0, 0, "C");
  

  
        
        //echo $tweeRegels;exit;
        
        if( $consolidatie==true)
        {
       //   $this->clientVermogensbeheerder['157139921']='Lange naam voor portefeuille';
  
  

          
          $startY=$this->pdf->getY();
          $maxY=0;
          foreach ($portefeuilleWaarden as $portefeuille => $pdata)
          {
            $this->pdf->Cell(2, 4, '', 0, 0, "C", 0);
            $x=$this->pdf->getX();
            $y=$this->pdf->getY();
            $this->pdf->MultiCell($headerWidth, 4, $this->clientVermogensbeheerder[$portefeuille], 0,  "C", 0);
            $maxY=max(array($y,$maxY,$this->pdf->getY()));
            //echo $maxY."<br>\n";ob_flush();
            $this->pdf->setXY($x+$headerWidth,$y);
            
            //MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
          }
          //echo ($maxY-$startY) ."($maxY-$startY) <br>\n";exit;

          if(($maxY-$startY) < 8 && $tweeRegels == true)
          {
            $maxY+=4;
          }
  
       //   echo "set: ".$maxY."<br>\n";ob_flush();
          $this->pdf->setY($maxY);
        }
        else
        {
          $this->pdf->ln(6);
        }
        //$this->pdf->SetX($this->pdf->marge + 65);
        // $this->pdf->Cell(20, 6,  vertaalTekst("Waarde",$this->pdf->rapport_taal), 0, 0, "C");
        // $this->pdf->Cell(17, 6, "%", 0, 0, "C");
        $tmp=array( vertaalTekst('Beleggingscategorie', $this->pdf->rapport_taal), vertaalTekst('Totaal waarde',$this->pdf->rapport_taal),vertaalTekst('Totaal',$this->pdf->rapport_taal)." %");
        foreach ($portefeuilleWaarden as $portefeuille=>$pdata)
        {
          $tmp[]=$portefeuille." ".vertaalTekst("waarde",$this->pdf->rapport_taal);
          $tmp[]="$portefeuille %";
          //  $this->pdf->Cell(23, 4,  vertaalTekst("Waarde",$this->pdf->rapport_taal), 0, 0, "C");
          //  $this->pdf->Cell(14, 4, "%", 0, 0, "C");
        }
        $this->pdf->excelData[]=$tmp;
        
        $this->pdf->Ln(3);
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			
			
			
			//categorieen
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			foreach($categorieVolgorde as $categorie=>$volgorde)
			{
				$regel = array();
        $regelXls =array();
				if( $consolidatie==true)
				{
          array_push($regel,"      ".  vertaalTekst(($categorieOmschrijving[$categorie]==''?$categorie:$categorieOmschrijving[$categorie]),$this->pdf->rapport_taal) );
          array_push($regel, '€');
          array_push($regel,$this->formatGetalKoers($regelData['Totaal'][$categorieOmschrijving[$categorie]]['waarde'],0));
          array_push($regel, $this->formatGetal($regelData['Totaal'][$categorieOmschrijving[$categorie]]['percentage'],1).'%');

          array_push($regelXls, $categorieOmschrijving[$categorie]);
          array_push($regelXls, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['waarde']);
          array_push($regelXls, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['percentage']);
					$beginX=55+$this->pdf->marge+3;
				}
				else
				{
          array_push($regel,  vertaalTekst("      ".  $categorieOmschrijving[$categorie],$this->pdf->rapport_taal));
          array_push($regelXls,  vertaalTekst($categorieOmschrijving[$categorie],$this->pdf->rapport_taal));
					if($consolidatie==true)
						$cols=2;
					else
						$cols=0;
					for($a=0;$a<$cols;$a++)
          {
					  array_push($regel,'');
            array_push($regelXls, '');
          }
					$beginX=20+$this->pdf->marge+3;
				}
				$min=$i*$maxPortefeuilles;
				$max=($i+1)*$maxPortefeuilles;
				$n=0;


				foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
				{
            array_push($regel, '€');
            array_push($regel, $this->formatGetalKoers($regelData[$portefeuille][$categorieOmschrijving[$categorie]]['waarde'],0));
            array_push($regel, $this->formatGetal($regelData[$portefeuille][$categorieOmschrijving[$categorie]]['percentage'],1).'%');
            array_push($regelXls, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['waarde']);
            array_push($regelXls, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['percentage']);

				}
				//echo $categorie."<br>\n"; ob_flush();

				$this->pdf->SetDrawColor($this->categorieKleuren[$categorie]['R']['value'], $this->categorieKleuren[$categorie]['G']['value'], $this->categorieKleuren[$categorie]['B']['value']);
        $this->pdf->SetFillColor($this->categorieKleuren[$categorie]['R']['value'], $this->categorieKleuren[$categorie]['G']['value'], $this->categorieKleuren[$categorie]['B']['value']);
        $this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),4,4,'DF');
				$this->pdf->Row($regel);
				$this->pdf->Ln(8);
        $this->pdf->excelData[]=$regelXls;
			}

			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			//Totaal regel
			$regel = array();
      $regelXls=array();
			if($consolidatie==true)
			{
				array_push($regel, 'Totaal');
        array_push($regel, '€');
        array_push($regel, $this->formatGetalKoers($regelDataTotaal['Totaal']['waarde'],0));
        array_push($regel, $this->formatGetal($regelDataTotaal['Totaal']['percentage'],1).'%');
        array_push($regelXls, 'Totaal');
        array_push($regelXls, $regelDataTotaal['Totaal']['waarde']);
        array_push($regelXls, $regelDataTotaal['Totaal']['percentage']);
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
		//	$max=($i+1)*$maxPortefeuilles;
			$n=0;
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
          array_push($regel, '€');
          array_push($regel, $this->formatGetalKoers($regelDataTotaal[$portefeuille]['waarde'],0));
          array_push($regel, $this->formatGetal($regelDataTotaal[$portefeuille]['percentage'],1).'%');
          array_push($regelXls, $regelDataTotaal[$portefeuille]['waarde']);
          array_push($regelXls, $regelDataTotaal[$portefeuille]['percentage']);
        
        $n++;
			}
			$this->pdf->setDrawColor($this->pdf->rapport_grijs_kleur[0],$this->pdf->rapport_grijs_kleur[1],$this->pdf->rapport_grijs_kleur[2]);
			$this->pdf->line($this->pdf->marge,$this->pdf->getY()-3,$this->pdf->marge+53,$this->pdf->getY()-3);
      $this->pdf->line($this->pdf->marge+55,$this->pdf->getY()-3,$this->pdf->marge+55+($n+1)*($headerWidth+2),$this->pdf->getY()-3);
      $this->pdf->excelData[]=$regelXls;
			$this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
			$this->pdf->Row($regel);
			$this->pdf->Ln(3);

			$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
      
      $beginY=$this->pdf->getY();
      
      if(true)
      {
        $x=$beginX;
  
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
        $this->pdf->setXY($this->pdf->marge, $beginY+32);
        $this->pdf->Cell($headerWidth,4,'Percentage van het vermogen',0,0,'L',0);
  
        $pieOke=true;
        if($consolidatie==true)
        {
          foreach($totaalGrafiek[0] as $waarde)
          {
            if($waarde<0)
              $pieOke=false;
          }
  
          if ($pieOke == true)
          {
            $this->pdf->setXY($x, $beginY + 2);
            $this->PieChart(35, 35, $totaalGrafiek[0], $totaalGrafiek[1]);
          }
          else
          {

           // $this->pdf->setXY($x - 5, $y + (count($categorieVolgorde) + 1) * $this->pdf->rowHeight + 8);
            $this->pdf->setXY($x, $beginY +18 - count(array_values($totaalGrafiek[0]))*4 ); //listarray(array_values($totaalGrafiek[0]));
            $this->BarDiagram(30, 30, array_values($totaalGrafiek[0]), '', $totaalGrafiek[1],'');
          }
  
  
          $aandeelOpTotaal = 1;
  
          $this->pdf->setXY($x, $beginY + 32);
          $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
          $this->pdf->Cell($headerWidth, 4, $this->formatGetal($aandeelOpTotaal * 100, 1) . ' %', 0, 0, 'C', 0);
        }
      }
      

			
			$x=$beginX+$headerWidth+2;
			$n=0;
			
			
			
			
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
         $pieOke=true;
         foreach($portefeuilleWaardenGrafiek[$portefeuille]['waarde'] as $waarde)
         {
           if($waarde<0)
             $pieOke=false;
         }

					if ($pieOke == true)
					{
						$this->pdf->setXY($x, $beginY+2);
						$this->PieChart(35, 35, $portefeuilleWaardenGrafiek[$portefeuille]['waarde'], $portefeuilleWaardenGrafiek[$portefeuille]['kleur']);
					}
					else
					{
						$this->pdf->setXY($x, $beginY+2);// listarray($portefeuilleWaardenGrafiek[$portefeuille]);
						$this->BarDiagram(30, 30, $portefeuilleWaardenGrafiek[$portefeuille]['waarde'],'', $portefeuilleWaardenGrafiek[$portefeuille]['kleur'],'');
					}
					$aandeelOpTotaal=$belCatData['totaleWaarde']/$totaalWaarde;
					$this->pdf->setXY($x, $beginY+32);
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
					$this->pdf->Cell(35-$extraW,4,$this->formatGetal($aandeelOpTotaal*100,1).' %',0,0,'C',0);
		//			listarray($belCatData['totaleWaarde']);
		//		listarray($belCatData['totaleWaarde']/$totaalWaarde);
					$x += $headerWidth+2;
	
			}


			$this->pdf->ln(50);
		}
    unset($this->pdf->CellFontStyle);

//    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
//    $this->pdf->Row($regel);


/*
		if(count($this->pdf->portefeuilles) >5)
		{
			$this->pdf->addPage();
		  $grafiekY=$this->pdf->getY()+40;
    }
		else
			$grafiekY=130;
*/

/*
		if($barGraph==false)
		{
			$this->pdf->setXY(20,$grafiekY);
			PieChart($this->pdf,65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);
		}
		else
		{
			$this->pdf->setXY(50,$grafiekY);
			$this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde ? ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
		}
*/
    /*
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
            //listarray($waarde);
            $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
            //$kleur=array();
            if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
              $kleur = $randomKleuren[$i];
    
            if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
              $kleur = array(rand(0, 255), rand(0, 255), rand(0, 255));
    
            $kop=$this->getCRMnaam($portefeuille);
            $portefeuilleAandeel[$kop]+=$waarde['totaleWaarde']/$totaalWaarde*100;
            $portefeuilleKleur[]=$kleur;
            $portefeuilleKleurBar[$kop]=$kleur;
            if($waarde['totaleWaarde'] < 0)
              $barGraph=true;
            $i++;
          }
    
          $this->pdf->setY($grafiekY-10);
          $this->pdf->SetAligns(array('C','C'));
          $this->pdf->SetWidths(array(140,140));
          $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
          $this->pdf->row(array(vertaalTekst("Verdeling over categorieën",$this->pdf->rapport_taal),vertaalTekst("Verdeling over portefeuilles",$this->pdf->rapport_taal)));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
          if($barGraph==false)
          {
            $this->pdf->setXY(160,$grafiekY);
            PieChart($this->pdf,65, 65, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleur);
          }
          else
          {
            $this->pdf->setXY(190,$grafiekY);
            $this->BarDiagram(80, 100, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleurBar);//"Portefeuillewaarde ? ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
          }
       
    
        }
     */

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
			$p=sprintf('%.1f',$val).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);

			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}
	}

  function  PieChart($w, $h, $data, $colors=null)
  {
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
        for($i = 0;$i < count($data); $i++) {
          $gray = $i * intval(255 / count($data));
          $colors[$i] = array($gray,$gray,$gray);
        }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $sum=array_sum($data);
      foreach($data as $val) {
        $angle = floor(($val * 360) / doubleval($sum));
        if ($angle != 0) {
          $angleEnd = $angleStart + $angle;
					$this->pdf->SetDrawColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
          $angleStart += $angle;
        }
        $i++;
      }
      if ($angleEnd != 360) {
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
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
		$legendWidth=0;
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
		elseif($minVal>-25)
    {
      $minVal=-25;
      $maxVal=100;
    }
		
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
		//$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
		$nullijn=$XDiag - ($offset * $unit);

		$i=0;
		$nbDiv=10;

		$this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    
    $this->pdf->setXY($nullijn,$YDiag + $hDiag);
    $this->pdf->Cell(0.1, 5, "0",0,0,'C');
    $this->pdf->Line($nullijn, $YDiag, $nullijn, $YDiag + $hDiag);
    $this->pdf->Line($XPage, $YDiag+$hDiag, $XPage+$w, $YDiag + $hDiag);
		if(round($legendaStep,5) <> 0.0)
		{
			//for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
			for($x=$nullijn;$x>=$XDiag; $x=$x-$legendaStep)
			{
				$this->pdf->Line($x, $YDiag+$hDiag-1, $x, $YDiag + $hDiag);
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
				$i++;
				if($i>100)
					break;
			}

			$i=0;
			//for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
			for($x=$nullijn;$x<=($XDiag+$lDiag); $x=$x+$legendaStep)
			{
				$this->pdf->Line($x, $YDiag+$hDiag-1, $x, $YDiag + $hDiag);
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');

				$i++;
				if($i>100)
					break;
			}
		}

		$i=0;

		$this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
	//	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
	//	$this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
//listarray($colorArray);listarray($data);
		foreach($data as $key=>$val)
		{
			$this->pdf->SetDrawColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
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