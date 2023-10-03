<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/23 05:56:31 $
File Versie					: $Revision: 1.10 $

$Log: RapportVOLKD_L68.php,v $
Revision 1.10  2020/04/23 05:56:31  rvv
*** empty log message ***

Revision 1.9  2020/04/22 15:40:47  rvv
*** empty log message ***

Revision 1.8  2020/03/21 16:32:57  rvv
*** empty log message ***

Revision 1.7  2020/03/08 09:28:38  rvv
*** empty log message ***

Revision 1.6  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.5  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.4  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.3  2017/03/25 18:04:40  rvv
*** empty log message ***

Revision 1.2  2017/01/21 17:48:04  rvv
*** empty log message ***

Revision 1.1  2017/01/08 10:46:31  rvv
*** empty log message ***

Revision 1.7  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.6  2016/09/18 08:49:02  rvv
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
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportVOLKD_L68
{
	function RapportVOLKD_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLKD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"Overzicht portefeuille - samengevoegd";
    $this->rapport_HSE_aantal_decimaal=2;
    $this->rapport_HSE_decimaal=2;
    $this->rapport_HSE_decimaal_proc=2;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->db = new DB();
if($this->pdf->lastPOST['doorkijk']==1)

		$this->verdiept = new portefeuilleVerdiept($this->pdf,$this->portefeuille,$this->rapportageDatum);

	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
      return '';
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
    if($waarde==0)
      return '';
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $tweeDecimalenZonderNullen=false)
	{
    if($waarde==0)
      return '';
	  if ($tweeDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '00' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     if($newDec>2)
         $newDec=2;
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}

	// type = totaal / subtotaal / tekst
	function printCol($row, $data, $type = "tekst")
	{
		$y = $this->pdf->getY();
		// draw lines
		// calculate positions
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthA[$tel];
		}

		$writerow = $this->pdf->widthA[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		if ($type == 'tekst')
		{
	    $y = $this->pdf->getY();
      $this->pdf->setY($y);
  	  $this->pdf->Cell($writerow,4,$data, 0,0, "L");
  	}
		else
		{
		  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		}
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "grandtotaal")
			{
		//		$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//		$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
		//		$this->pdf->setDash(1,1);
		//		$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
		//		$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
	}



	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $durationTotaal = 0, $ytdTotaal = 0 ,$geenLijn=false)
	{
		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		//$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";
		
		if($geenLijn==true)
      $grandtotaal='geenLijn';


			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(0,$title,"tekst");


		  if($durationTotaal <>0)
				$this->printCol(5,$this->formatGetal($durationTotaal,2),$grandtotaal);
		  if($ytdTotaal <>0)
			  $this->printCol(6,$this->formatGetal($ytdTotaal,2),$grandtotaal);

      if($totaalB <>0)
				$this->printCol(8,$this->formatGetal($totaalB,$this->rapport_HSE_decimaal),$grandtotaal);
//			if($totaalA <>0)
//				$this->printCol(5,$this->formatGetal($totaalA,$this->rapport_HSE_decimaal,true),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(9,$this->formatGetal($totaalC,$this->rapport_HSE_decimaal_proc)." %",$grandtotaal);
//			if($totaalD <>0)
//				$this->printCol(10,$this->formatGetal($totaalD,$this->rapport_HSE_decimaal),$grandtotaal);
//			if($totaalE <>0)
//				$this->printCol(11,$this->formatGetal($totaalE,$this->rapport_HSE_decimaal),$grandtotaal);
//			if($totaalF <>0)
//				$this->printCol(12,$this->formatGetal($totaalF,$this->rapport_HSE_decimaal_proc),$grandtotaal);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

		$this->pdf->ln();
		return $totaalB;
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
				$fontsize = $this->pdf->rapport_fontsize+1;
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
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
		global $__appvar;

    $this->pdf->templateVars['VOLKDPaginas']=$this->pdf->page+1;
    $this->pdf->templateVarsOmschrijving['VOLKDPaginas'] = $this->pdf->rapport_titel;

		$echtePortefeuille=$this->portefeuille;
if($this->pdf->lastPOST['doorkijk']==1)
{


		$verdiepteFondsen = $this->verdiept->getFondsen();

		foreach ($verdiepteFondsen as $fonds)
			$this->verdiept->bepaalVerdeling($fonds,$this->verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$this->rapportageDatum);

//		listarray($verdiepteFondsen);exit;
//    listarray($this->verdiept->FondsPortefeuilleData);
//		listarray($this->pdf->fondsPortefeuille);

		if(substr($this->rapportageDatum,5,5)=='01-01')
			$startjaar=true;
		else
			$startjaar=false;

		$fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $this->rapportageDatum,$startjaar,'EUR',substr($this->rapportageDatum,0,4).'-01-01');
		$correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
		foreach($fondswaarden as $i=>$fondsData)
		{
			//
			if(isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
			{

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
		$fondswaarden  = array_values($fondswaarden);//listarray($fondswaarden);
		$tmp=array();
		foreach($fondswaarden as $mixedInstrument)
		{
			$instrument=array();
			foreach($mixedInstrument as $index=>$value)
				$instrument[strtolower($index)]=$value;
			unset($instrument['voorgaandejarenactief']);

			$key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
			if(isset($tmp[$key]))
			{
				foreach($correctieVelden as $veld)
				{
					$veld=strtolower($veld);
					$tmp[$key][$veld] += $instrument[$veld];
				}
			}
			else
		  	$tmp[$key]=$instrument;
		//	listarray($instrument);
		}
		$fondswaarden  = array_values($tmp);


//		listarray($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] );
		$this->portefeuille='v'.$this->portefeuille;
		vulTijdelijkeTabel($fondswaarden ,$this->portefeuille, $this->rapportageDatum);

}
		$query = "SELECT Vermogensbeheerders.VerouderdeKoersDagen, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client 
FROM Portefeuilles
JOIN Clienten ON Portefeuilles.Client = Clienten.Client 
JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille = '".$echtePortefeuille."'  ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		$maxDagenOud=$this->portefeuilledata['VerouderdeKoersDagen'];

		$this->extraVoet="Koersen met een * zijn meer dan $maxDagenOud dagen oud.";
		$this->extraVoetPages=array();

    $this->pdf->hoofdcategorie='';
    $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
    $this->cashfow->genereerTransacties();
    $this->cashfow->genereerRows();
		$totalen=array();

			$fondsresultwidth = 5;
			$omschrijvingExtra = 9;

		// voor kopjes
			$this->pdf->widthA = array(0,70,20,15,40,40,18,22,22,22,12);//verhuist naar header
			$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');
      $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
      $fill=true;

		$this->pdf->AddPage();
    $this->pdf->templateVars['VOLKDPaginas']=$this->pdf->page;

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
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

		$query = "SELECT TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieOmschrijving AS Omschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.regioOmschrijving,
Fondsen.rating as fondsRating,
Fondsen.Lossingsdatum,
Fondsen.Rentedatum,
Fondsen.Renteperiode,
Fondsen.variabeleCoupon,
Fondsen.OptieBovenliggendFonds,
if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.fondsOmschrijving ,optie.Omschrijving) as onderliggendFonds, 
TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.type,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				   TijdelijkeRapportage.portefeuille,TijdelijkeRapportage.rekening,
				    round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd
FROM ".
			" TijdelijkeRapportage 
Left Join Fondsen ON TijdelijkeRapportage.Fonds = Fondsen.Fonds 
Left Join Fondsen as optie ON Fondsen.OptieBovenliggendFonds = optie.Fonds   
      ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND 
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND 
      TijdelijkeRapportage.type <> 'rente'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, 
      TijdelijkeRapportage.beleggingscategorieVolgorde asc, onderliggendFonds,
      TijdelijkeRapportage.fondsOmschrijving asc, 
      TijdelijkeRapportage.type asc,
      TijdelijkeRapportage.valuta asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query); 
		$DB->Query();
		
		$groupCategorien=array('AAND','ZAKBEL','OBL','REN-VARBEL');
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->extraVoet();
    $lastCategorieShort='';
		while($fonds = $DB->NextRecord())
		{
		  $categorieShort=$fonds['beleggingscategorie'];
		  
      $q="SELECT actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage WHERE type = 'rente' AND 
      fonds='".$fonds['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
      TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"  
			.$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB2->SQL($q);
      $rente=$DB2->lookupRecord();
      $fonds['rente']=$rente['actuelePortefeuilleWaardeEuro'];


		  if( $fonds['hoofdcategorieOmschrijving'] == '')
		    $fonds['hoofdcategorieOmschrijving'] ='Geen hoofdcategorie';
		  if($fonds['Omschrijving']=='')
		    $fonds['Omschrijving']='Geen categorie';


     if($fonds['rekening'] <>'')
     {
       preg_match("/[0-9]{1,}/", $fonds['rekening'], $matches);
       if($matches[0])
         $fonds['fondsOmschrijving']="Liquiditeiten ".$matches[0];
     }
 
    $ytm='';        
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);


			// print totaal op hele categorie.
			if($lastCategorie <> $fonds['Omschrijving'] && !empty($lastCategorie) )
			{
			 
        if($totaalactueelRente <> 0)
        {

					if($fill==true)
					{
						$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,0,1);
						//listarray($this->pdf->widths);
						$fill=false;
					}
					else
					{
						$this->pdf->fillCell=array();
						$fill=true;
					}
          $aandeel=$totaalactueelRente/$totaalWaarde*100;
		
			            if(!in_array($lastCategorieShort,$groupCategorien))
                  {
	        $this->pdf->row(array("",'Opgelopen rente','','','',"",'','',$this->formatGetal($totaalactueelRente,2),
          $this->formatGetal($aandeel,$this->rapport_HSE_decimaal_proc).' %'));
                  }
        }
        $totaalactueel+=$totaalactueelRente;
        $totaalpercentage+=$aandeel;
       // $totaalbegin=$fonds['beginPortefeuilleWaardeEuro'];
       
				$title = 'Totaal '.$lastCategorie;
        $procentResultaat=$totaalBijdrage/$totaalpercentage*100;

				$durationTotaal=$totalen['durationWaardeSum']/$totalen['WaardeSum'];
				$ytdTotaal=$totalen['ytmWaardeSum']/$totalen['WaardeSum'];
        
        $this->extraVoet();
        if(in_array($lastCategorieShort,$groupCategorien))
				{
				  $geenLijn=true;
				}
				else
				{
					$geenLijn=false;
				}
        $actueleWaardePortefeuille += $this->printTotaal($title, '', $totaalactueel, $totaalpercentage , '', '', $procentResultaat,'',$durationTotaal,$ytdTotaal,$geenLijn);
        $this->extraVoet();
				$totalen['durationWaardeSum']=0;
				$totalen['ytmWaardeSum']=0;
				$totalen['WaardeSum']=0;

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalactueelRente =0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}


			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{// echo $this->pdf->GetY()." ".$fonds['hoofdcategorieOmschrijving']."<br>\n";
        $this->pdf->hoofdcategorie=$fonds['hoofdcategorie'];
			  if($this->pdf->GetY() > 156 || isset($lastHCategorie))
				{
          $this->pdf->AddPage();
					$this->extraVoet();
				}
				$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}
			else
			{
        if($this->pdf->GetY() > 190)
        {
          $this->pdf->AddPage();
          $this->extraVoet();
        }
			}

			if($lastCategorie <> $fonds['Omschrijving'])
			{
			            if(!in_array($fonds['beleggingscategorie'],$groupCategorien))
                  {
                    $this->printKop(vertaalTekst($fonds['Omschrijving'], $this->pdf->rapport_taal), "b");
                  }
			}


      $resultaat=$fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'];
  		$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro']) / ($fonds['beginPortefeuilleWaardeEuro'] /100));
			if($fonds['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

			$percentageVanTotaal = ($fonds['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->rapport_HSE_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
      {
				$procentResultaattxt = "p.m.";
        $procentResultaat=0;
      }
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->rapport_HSE_decimaal_proc);
         
         $bijdrage=$procentResultaat*$percentageVanTotaal/100;
          
if($fonds['type']=='rekening')
{
  $resultaat=0;
  $fondsResultaat=0;
  $fondsResultaatprocent=0;
  $valutaResultaat=0;
  $procentResultaat=0;
  $procentResultaattxt='';
  $fonds['totaalAantal']=$fonds['actuelePortefeuilleWaardeInValuta'];
  $fonds['actueleFonds']=0;
  $fonds['beginwaardeLopendeJaar']=0;
}




				$resultaattxt = "";
        $resultaattxt=$this->formatGetal($resultaat); 
        
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

					 if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,0,1);
            //listarray($this->pdf->widths);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }

			$omschrijvingWidth=$this->pdf->GetStringWidth($fonds['fondsOmschrijving']);
			$cellWidth=$this->pdf->widths[1]-2;
			if($omschrijvingWidth > $cellWidth)
			{
				$dotWidth=$this->pdf->GetStringWidth('...');
				$chars=strlen($fonds['fondsOmschrijving']);
				$newOmschrijving=$fonds['fondsOmschrijving'];
				for($i=3;$i<$chars;$i++)
				{
					$omschrijvingWidth=$this->pdf->GetStringWidth(substr($newOmschrijving,0,$chars-$i));
					if($cellWidth>($omschrijvingWidth+$dotWidth))
					{
						$omschrijving=substr($newOmschrijving,0,$chars-$i).'...';
						break;
					}
				}
			}
			else
				$omschrijving=$fonds['fondsOmschrijving'];

			if($fonds['type']!='rekening' && $fonds['koersLeeftijd'] > $maxDagenOud)
				$markering="*";
			else
				$markering="";

        if($fonds['hoofdcategorie']=='ZAK')// || $fonds['hoofdcategorie']=='VAR'
        {
        	
          if(!in_array($fonds['beleggingscategorie'],$groupCategorien))
          {
            $this->pdf->row(array('',
                              $omschrijving,
                              $this->formatGetal($fonds['totaalAantal'], $this->rapport_HSE_aantal_decimaal),
                              $fonds['valuta'],
                              $fonds['regioOmschrijving'],
                              $fonds['beleggingssectorOmschrijving'],
                              '',
                              $this->formatGetal($fonds['actueleFonds'], 2) . $markering,
                              $this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'], $this->rapport_HSE_decimaal),
                              $percentageVanTotaaltxt,
                              '',
                              '',
                              '',
                              ''));
          }
        }
        elseif($fonds['hoofdcategorie']=='VAR')
        {

					$rente=getRenteParameters($fonds['fonds'], $this->rapportageDatum);
					foreach($rente as $key=>$value)
						$fonds[$key]=$value;

          if($fonds['Lossingsdatum'] <> '')
            $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
          else
            $lossingsJul=0;
          $rentedatumJul = adodb_db2jul($fonds['Rentedatum']);
          $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));
         // $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";

					$koers=getRentePercentage($fonds['fonds'],$this->rapportageDatum);


          $renteDag=0;
          if($fonds['variabeleCoupon'] == 1)
          {
            $rapportJul=adodb_db2jul($this->rapportageDatum);
            $renteJul=adodb_db2jul($fonds['Rentedatum']);
            $renteStap=($fonds['Renteperiode']/12)*31556925.96;
            $renteDag=$renteJul;
            if($renteStap > 100000)
              while($renteDag<$rapportJul)
              {
                $renteDag+=$renteStap;
              }
          }
          $ytm=0;
          $duration=0;
          $modifiedDuration=0;

          if($lossingsJul > 0)
          {
            //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
            $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

            $p = $fonds['actueleFonds'];
            $r = $koers['Rentepercentage']/100;
            $b = $this->cashfow->fondsDataKeyed[$fonds['fonds']]['lossingskoers'];
            $y = $jaar;

            $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;

						if($ytm>100)
						{
							$ytm=0;
							$ytmTxt = 'p.m.';
						}
						else
							$ytmTxt=$this->formatGetal($ytm, 2);

            $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

            $duration=$this->cashfow->waardePerFonds[$fonds['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$fonds['fonds']]['ActueelWaarde'];
            if($fonds['variabeleCoupon'] == 1 && $renteDag <> 0)
              $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
            else
              $modifiedDuration=$duration/(1+$ytm/100);
            $aandeel=$fonds['actuelePortefeuilleWaardeEuro']/$this->actueleWaardePortefeuille;

            $totalen['yield']+=$koers['Rentepercentage']*$fonds['totaalAantal']/$fonds['actuelePortefeuilleWaardeEuro']*$fonds['actueleValuta']*$aandeel;
            $totalen['ytm']+=$ytm*$aandeel;
						$totalen['ytmWaardeSum']+=$ytm*$fonds['actuelePortefeuilleWaardeEuro'];
            $totalen['duration']+=$duration*$aandeel;
						$totalen['durationWaardeSum']+=$duration*$fonds['actuelePortefeuilleWaardeEuro'];
						$totalen['WaardeSum']+=$fonds['actuelePortefeuilleWaardeEuro'];
            $totalen['modifiedDuration']+=$modifiedDuration*$aandeel;
            $totalen['restLooptijd']+=$restLooptijd*$aandeel;

          }
          else
          {
            $ytm=0;
             $duration=0;
						$ytmTxt=$this->formatGetal($ytm, 2);
          }
          if(!in_array($fonds['beleggingscategorie'],$groupCategorien))
          {
          $this->pdf->row(array('',
														$omschrijving,
                            $this->formatGetal($fonds['totaalAantal'], $this->rapport_HSE_aantal_decimaal),
                            $fonds['valuta'],
                            $fonds['fondsRating'],
                            $this->formatGetal($duration, 2),
														$ytmTxt,
                            $this->formatGetal($fonds['actueleFonds'], 2).$markering,
                            $this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'], $this->rapport_HSE_decimaal),
                            $percentageVanTotaaltxt,
                            '',
                            '',
                            '',
                            ''));
          }
        }
        else
				  $this->pdf->row(array('',
													$fonds['fondsOmschrijving'],
													$this->formatGetal($fonds['totaalAantal'],$this->rapport_HSE_aantal_decimaal),
													$fonds['valuta'],
                          $this->formatGetal($fonds['beginwaardeLopendeJaar'],2),
													$this->formatGetal($fonds['beginPortefeuilleWaardeEuro'],$this->rapport_HSE_decimaal),
													$this->formatGetal($fonds['actueleFonds'],2).$markering,
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->rapport_HSE_decimaal),
													$percentageVanTotaaltxt,
                          $this->formatGetal($fonds['rente']),
													$resultaattxt,
													'',
													$procentResultaattxt,''));
                   
				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['totaalResultaat'] +=$resultaat;
				$subtotaal['totaalBijdrage'] += $bijdrage;
        $subtotaal['rente'] += $fonds['rente'];
        $hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hcatTotaal['totaalactueel'] += $fonds['actuelePortefeuilleWaardeEuro'];
        $hcatTotaal['totaalbegin'] += $fonds['beginPortefeuilleWaardeEuro'];

	  	$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];
      $totaalbegin +=$fonds['beginPortefeuilleWaardeEuro'];
      $totaalactueelRente+=$fonds['rente'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];


			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $bijdrage;

			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;
			
      
$ongerealiseerdResultaat += $subtotaal['totaalResultaat'] ;
$inProcent += $subtotaal['totaalBijdrage'] ;   

$subtotaal = array();
$lastCategorieShort=$categorieShort;
		}
    
//    listarray($totaalBijdrage);
    
    if(in_array($lastCategorieShort,$groupCategorien))
    {
      $geenLijn=true;
    }
    else
    {
      $geenLijn=false;
    }

      //vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)
		$title = 'Totaal '.$lastCategorie;
    //$title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $durationTotaal = 0, $ytdTotaal = 0 ,$geenLijn=fals
		$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel,$totaalpercentage,$totaalResultaat,'',$totaalBijdrage,'','','',$geenLijn);
    $aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100;
    //echo "$aandeelOpTotaal=$actueleWaardePortefeuille/$totaalWaarde*100; <br>\n ".($actueleWaardePortefeuille-$totaalWaarde);exit;
    $this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,$aandeelOpTotaal,$ongerealiseerdResultaat,'',$inProcent,true);
    $this->pdf->Ln();
	//		   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren);
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // printRendement($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,$this->pdf->rapportageValuta);
   // printAEXVergelijking($this->pdf,$this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
    $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum,true);

$this->pdf->SetFillColor(0);
unset($this->pdf->fillCell);

	}

	function extraVoet($force=false)
	{
		if(trim($this->extraVoet.$this->extraVoet2)<>'')
		{

			//if($this->pdf->GetY()+$this->pdf->rowHeight*3>$this->pdf->PageBreakTrigger || $force)
			//{
				if(!in_array($this->pdf->page,$this->extraVoetPages))
				{
					$x=$this->pdf->getX();
					$y=$this->pdf->GetY();
					$this->pdf->AutoPageBreak=false;
					$this->pdf->SetXY(0,202);
					//$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
					$this->pdf->MultiCell(297,$this->pdf->rowHeight,trim($this->extraVoet.$this->extraVoet2),0,'C');
					$this->pdf->SetXY($x,$y);
					$this->pdf->AutoPageBreak=true;
					$this->extraVoetPages[]=$this->pdf->page;
				}
		//	}
		}
	}
}
?>
