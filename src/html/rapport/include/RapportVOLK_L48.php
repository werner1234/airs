<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/11 11:17:44 $
File Versie					: $Revision: 1.9 $

$Log: RapportVOLK_L48.php,v $
Revision 1.9  2019/12/11 11:17:44  rvv
*** empty log message ***

Revision 1.8  2019/12/07 17:48:23  rvv
*** empty log message ***

Revision 1.7  2019/07/20 16:28:44  rvv
*** empty log message ***

Revision 1.6  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.5  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.4  2013/07/17 08:11:22  rvv
*** empty log message ***

Revision 1.3  2013/06/15 15:55:18  rvv
*** empty log message ***

Revision 1.2  2013/06/12 18:46:36  rvv
*** empty log message ***

Revision 1.1  2013/05/26 13:54:49  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L48
{
	function RapportVOLK_L48($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		$this->pdf->rapport_titel =	"Overzicht portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->db = new DB();
    $this->rapport_aantal_decimaal=4;
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

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
    if($waarde==0)
      return '';
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
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



	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
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


			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->printCol(0,$title,"tekst");
      if($totaalB <>0)
				$this->printCol(7,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(8,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(10,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(11,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(12,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
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

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


			$fondsresultwidth = 5;
			$omschrijvingExtra = 9;

		// voor kopjes
			$this->pdf->widthA = array(0,80,30,15,22,22,18,22,22,2,28,0,18);
			$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');
      $this->pdf->SetFillColor(230,230,230);
      $fill=true;

		$this->pdf->AddPage();
    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;

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
				   TijdelijkeRapportage.portefeuille FROM ".
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
      
     // echo $query;exit;
      

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query); 
		$DB->Query();
 //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

		while($fonds = $DB->NextRecord())
		{
		  
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

        
 
    $ytm='';        
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $fonds['Omschrijving'] && !empty($lastCategorie) )
			{
			 
        if($totaalactueelRente <> 0)
        {
          $aandeel=$totaalactueelRente/$totaalWaarde*100;
	        $this->pdf->row(array("Opgelopen rente",'','','','',"",'',$this->formatGetal($totaalactueelRente,$this->pdf->rapport_VOLK_decimaal),
          $this->formatGetal($aandeel,1).'%'));
        }
        $totaalactueel+=$totaalactueelRente;
        $totaalpercentage+=$aandeel;
       // $totaalbegin=$fonds['beginPortefeuilleWaardeEuro'];
       
				$title = '';
        $procentResultaat=$totaalBijdrage/$totaalpercentage*100;

        $actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalResultaat, $totaalvalutaresultaat, $procentResultaat);

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalactueelRente =0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}


			if($lastCategorie <> $fonds['Omschrijving'])
			{
					$this->printKop(vertaalTekst($fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}


      $resultaat=$fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro'];
  		$procentResultaat = (($fonds['actuelePortefeuilleWaardeEuro'] - $fonds['beginPortefeuilleWaardeEuro']) / ($fonds['beginPortefeuilleWaardeEuro'] /100));
			if($fonds['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

			$percentageVanTotaal = ($fonds['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

			if($procentResultaat > 1000 || $procentResultaat < -1000)
      {
				$procentResultaattxt = "p.m.";
        $procentResultaat=0;
      }
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);
         
         $bijdrage=$procentResultaat*$percentageVanTotaal/100;
          
if($fonds['type']=='rekening')
{
  $resultaat=0;
  $fondsResultaat=0;
  $fondsResultaatprocent=0;
  $valutaResultaat=0;
  $procentResultaat=0;
  $procentResultaattxt='';
  $fonds['totaalAantal']=0;
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
    //  echo $fonds['regioOmschrijving'];ob_flush();
				$this->pdf->row(array('',//$fonds['regioOmschrijving'],
													$fonds['fondsOmschrijving'],
													$this->formatAantal($fonds['totaalAantal'],$this->rapport_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$fonds['valuta'],
                          $this->formatGetal($fonds['beginwaardeLopendeJaar'],2),
													$this->formatGetal($fonds['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($fonds['actueleFonds'],2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt,
                          '',//$this->formatGetal($fonds['rente']),
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
		}
    
//    listarray($totaalBijdrage);


      //vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal)
		$actueleWaardePortefeuille += $this->printTotaal('', $totaalbegin, $totaalactueel,$totaalpercentage,$totaalResultaat,'',$totaalBijdrage);
		
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
}
?>
