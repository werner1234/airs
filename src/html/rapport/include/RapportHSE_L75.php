<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/25 16:22:07 $
File Versie					: $Revision: 1.8 $

$Log: RapportHSE_L75.php,v $
Revision 1.8  2019/05/25 16:22:07  rvv
*** empty log message ***

Revision 1.7  2018/07/18 15:46:07  rvv
*** empty log message ***

Revision 1.6  2018/07/14 14:05:14  rvv
*** empty log message ***

Revision 1.5  2018/07/04 08:25:01  rvv
*** empty log message ***

Revision 1.4  2018/06/23 14:21:39  rvv
*** empty log message ***

Revision 1.3  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.2  2018/05/30 16:10:58  rvv
*** empty log message ***

Revision 1.1  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.1  2018/03/31 18:06:01  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L75
{
	function RapportHSE_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vermogen lopend jaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vermogen rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->whereFilter="";//AND TijdelijkeRapportage.hoofdcategorie='Liquide'";

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
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
			$start += $this->pdf->widthB[$tel];
		}

		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		if ($type == 'tekst' && $this->pdf->rapport_layout == 8)
		{
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
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
	}


	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF, $totaalG = 0, $totaalH = 0)
	{
		$hoogte = 16;

		/*
		echo $this->pdf->pagebreak;
		echo "<br>";
		echo $this->pdf->GetY();
		echo "<br>";
		*/
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

	//title "Subtotaal:",$this->pdf->rapport_taal), 
  //A $categorien[subtotaalbegin],
  //B $categorien[subtotaalactueel],
  //C $subtotaal[percentageVanTotaal], 
  //D $subtotaal[fondsResultaat], 
  //E $subtotaal[valutaResultaat], 
  //F $procentResultaat);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalC <>0)
				$this->printCol(11,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
	//		if($totaalD <>0) //fondsResultaat
	//			$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
//			if(($totaalD+$totaalE+$totaalG) <>0 ) //valutaResultaat
//				$this->printCol(12,$this->formatGetal($totaalD+$totaalE+$totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
	//		if($totaalG <>0) //divident
	//			$this->printCol(13,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
//			if($totaalF <>0) //$procentResultaat
//				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		$hoogte = 10;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

    if($grandtotaal)
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    else
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		
		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";

    	$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(11,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
	//		if($totaalD <>0)
	//			$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
//			if(($totaalD+$totaalE+$totaalG) <>0)
//				$this->printCol(12,$this->formatGetal($totaalD+$totaalE+$totaalG,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
	//		if($totaalG <>0) //divident
	//			$this->printCol(13,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
//			if($totaalF <>0)
//				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}
  
  
  function getDividend($fonds)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
       elseif($data['type']=='fondsen')  
         $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalCorrected=$totaal;

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving 
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND 
     Grootboekrekeningen.Opbrengst=1";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    { 
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      } 
     // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }

    
    return array('totaal'=>$totaal/$this->pdf->ValutaKoersEind,'corrected'=>$totaalCorrected/$this->pdf->ValutaKoersEind);
  }


	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$this->pdf->widthB = array(5,65,5,20,25,25,5,5,15,25,25,18,28,8,10);
		$this->pdf->widthA = array(  70,5,20,25,25,5,5,15,25,25,18,28,8,10);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes

		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
	

		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$portBackup=$this->portefeuille;
		if ($this->pdf->lastPOST['doorkijk'] == 1)
		{
			$gegevens = bepaaldFondsWaardenVerdiept_L75($this->portefeuille, $this->rapportageDatum, $this->pdf);
			vulTijdelijkeTabel($gegevens,'d'.$this->portefeuille,$this->rapportageDatum);
			$this->portefeuille='d'.$this->portefeuille;
			$formatAantal=false;

		}


		$this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' ".$this->whereFilter." AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

//  IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as subtotaalbegin,
			$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
	    "
     
       SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS subtotaalbegin, 
      ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$this->whereFilter." "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde asc";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query); //echo $query;exit;
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

           $procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false,$totaaldividend);

				$totaalbegin = 0;
				$totaalactueel = 0;
                $totaaldividend = 0;
                $totaaldividendCorrected = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
					$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
	

			// subkop (valuta)
			if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $beginQuery = 'beginwaardeValutaLopendeJaar';
			else
			  $beginQuery = $this->pdf->ValutaKoersBegin;

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro, 
				(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, 
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalEUR,".
				//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' ".$this->whereFilter." AND ".
				" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";//exit;
			
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
      $n=0;
      $kopPrinted=false;
			while($subdata = $DB2->NextRecord())
			{
		 //   if($n>$regels-2 && $this->pdf->GetY()>160)//
     //   {
     //      $this->pdf->AddPage();
     //   }
        if($kopPrinted==false)
        {
          $this->printKop(vertaalTekst("Fondsen in",$this->pdf->rapport_taal)." ".$categorien['valuta'], "");
          $kopPrinted=true;
        }   	 
			  $dividend=$this->getDividend($subdata['fonds']);
       
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
        $fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;

				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro'] / $totaalWaarde) * 100;


					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

					if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
					else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
        $dividendtxt='';

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($dividend['totaal'] <> 0)
					$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);

				$samengesteldResultaat=$fondsResultaat+$valutaResultaat+$dividend['totaal'];
				$resultaatTxt=$this->formatGetal($samengesteldResultaat,$this->pdf->rapport_VOLK_decimaal);
          
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$stringWidth=$this->pdf->GetStringWidth($subdata['fondsOmschrijving']);
				$newString='';
				if($stringWidth >= $this->pdf->widths[1]-2)
				{
					$omschrijvingRuimte = $this->pdf->widths[1] - $this->pdf->GetStringWidth('...') - 2;
					for ($i = 0; $i < strlen($subdata['fondsOmschrijving']); $i++)
					{
						$char = $subdata['fondsOmschrijving'][$i];
						$omschrijvingRuimte -= $this->pdf->GetStringWidth($char);
						if ($omschrijvingRuimte < 0)
						{
							$newString = substr($subdata['fondsOmschrijving'], 0, $i);
							break;
						}
					}
					$subdata['fondsOmschrijving'] = $newString . '...';
				}

				if($fill==true)
				{
					$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
					$fill=false;
				}
				else
				{
					$this->pdf->fillCell=array();
					$fill=true;
				}


				$this->pdf->row(array("",
													$subdata['fondsOmschrijving'],
													'',//		$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													'',//		$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
											    '',//		$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata['historischeWaardeTotaalEUR'],$this->pdf->rapport_VOLK_decimaal),
													"",
													'',//$this->formatGetal($subdata[actueleFonds],2),
											    '',//		$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),'',
													$percentageVanTotaaltxt,

													'',//$resultaatTxt,//$valutaResultaattxt,
													'',//$dividendtxt,
                          ''));//$procentResultaattxt
					
				

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
                $subtotaal['totaalDividend'] += $dividend['totaal'];
                $subtotaal['totaalDividendCorrected'] += $dividend['corrected'];
        $n++;
			}


			// print categorie footers
				$procentResultaat = (($categorien[subtotaalactueel]  - $categorien[subtotaalbegin] + $subtotaal['totaalDividendCorrected'] ) / ($categorien[subtotaalbegin]  /100));
				if($categorien[subtotaalbegin] < 0)
					$procentResultaat = -1 * $procentResultaat;


					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalbegin],$categorien[subtotaalactueel],$subtotaal[percentageVanTotaal], $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat,$subtotaal['totaalDividend']);

//rvv
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
    " TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
    		" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' ".$this->whereFilter." AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query,__FILE__,__LINE__);

		$DB2 = new DB();
		$DB2->SQL($query);
		$DB2->Query();

		if($DB2->records() > 0)
		{
			
			$totaalRenteInValuta = 0 ;
			while($rente = $DB2->NextRecord())
			{
				$totaalRenteInValuta += $rente['subtotaalactueel'];
			}
      if($totaalRenteInValuta <> 0)
      {
        $this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
      
		  	$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
        $actueleWaardePortefeuille 		+= $this->printSubTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		    $categorien['subtotaalactueel']+=$totaalRenteInValuta;
        $subtotaal['percentageVanTotaal']+=$subtotaalPercentageVanTotaal;
       }
    }
//rvv

	

			// totaal op categorie tellen
			$totaalbegin   += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];
      
			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];
      $totaaldividend        += $subtotaal['totaalDividend'];
      $totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];


			$lastCategorie = $categorien[Omschrijving];

			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds  += $subtotaal[fondsResultaat];
      $grandtotaaldividend  += $subtotaal['totaalDividend'];
      $grandtotaaldividendCorrected  += $subtotaal['totaalDividendCorrected'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat,false,$totaaldividend);
		//$actueleWaardePortefeuille += $this->printTotaal(                                                                                                        $title,     $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat,false,$totaaldividend);



		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage 
			LEFT JOIN Rekeningen on TijdelijkeRapportage.rekening=Rekeningen.rekening AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ".$this->whereFilter." AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() > 0)
		{
		  if($DB1->records()==1 && ($DB1->records()+3)*$this->pdf->rowHeight+$this->pdf->GetY() > $this->pdf->PageBreakTrigger )
        $this->pdf->AddPage();
        
			$totaalLiquiditeitenInValuta = 0;
				$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");

			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}


			foreach($liqiteitenBuffer as $data)
			{
				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{PortefeuilleVoorzet}",  $this->pdf->rapport_portefeuilleVoorzet, $omschrijving);

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];
				$subtotaalPercentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				if($fill==true)
				{
					$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
					$fill=false;
				}
				else
				{
					$this->pdf->fillCell=array();
					$fill=true;
				}

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
													$omschrijving,
												"",
												"",
												"",
												"",
												"",'',
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),'',
												$subtotaalPercentageVanTotaaltxt,'','',''));
			}

			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
			$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$subtotaalPercentageVanTotaal,"","");
		} // einde liquide

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{

			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."  Verschil (".round($actueleWaardePortefeuille-$totaalWaarde,2).") ');
			</script>";
			ob_flush();
		}
   
		$this->printTotaal(vertaalTekst("Totaal vermogen",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaaldividend);
	
/*
		$this->pdf->ln();

		$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
	

		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
	*/
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
		unset($this->pdf->fillCell);

		if ($this->pdf->lastPOST['doorkijk'] == 1)
		{
			$this->portefeuille=$portBackup;
		}
		
	}
}
?>
