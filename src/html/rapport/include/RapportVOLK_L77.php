<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/30 06:15:01 $
File Versie					: $Revision: 1.14 $

$Log: RapportVOLK_L77.php,v $
Revision 1.14  2020/07/30 06:15:01  rvv
*** empty log message ***

Revision 1.13  2020/07/29 13:56:12  rvv
*** empty log message ***

Revision 1.12  2020/06/24 13:02:42  rvv
*** empty log message ***

Revision 1.11  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.10  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.9  2019/01/02 16:18:56  rvv
*** empty log message ***

Revision 1.8  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.7  2018/10/13 17:18:13  rvv
*** empty log message ***

Revision 1.6  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.5  2018/10/07 10:19:56  rvv
*** empty log message ***

Revision 1.4  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.3  2018/09/29 16:19:30  rvv
*** empty log message ***

Revision 1.2  2018/09/19 17:35:08  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L77
{
	function RapportVOLK_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->xls=array();
			$this->pdf->rapport_titel = "Portefeuille";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->verdeling='beleggingscategorie';
		$this->OIHindeling=false;
    //$this->verdeling='hoofdcategorie';
    $this->tijdelijkeRapportageFilter='';
    $this->xls[]=array('Aantal','Fondsomschrijving','Valuta','ISIN');
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
     $newDec='';
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && $newDec<>'')
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
				$this->printCol(10,$this->formatGetal($totaalC,1)." %","subtotaal");
			if($totaalD <>0) //fondsResultaat
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0) //valutaResultaat
				$this->printCol(12,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalG <>0) //divident
				$this->printCol(13,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0) //$procentResultaat
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{
		$hoogte = 20;
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
			     if($this->pdf->modelRapport==true)
           {
             if ($totaalB <> 0)
             {
               $this->printCol(9, $this->formatGetal($totaalB, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
             }

             if ($totaalC <> 0)
             {
               $this->printCol(10, $this->formatGetal($totaalC, 1) . " %", $grandtotaal);
             }


           }
           else
					 {
			       if ($totaalB <> 0)
             {
               $this->printCol(9, $this->formatGetal($totaalB, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
             }
             if ($totaalA <> 0)
             {
               $this->printCol(5, $this->formatGetal($totaalA, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
             }
             if ($totaalC <> 0)
             {
               $this->printCol(10, $this->formatGetal($totaalC, 1) . " %", $grandtotaal);
             }
             if ($totaalD <> 0)
             {
               $this->printCol(11, $this->formatGetal($totaalD, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
             }
             if ($totaalE <> 0)
             {
               $this->printCol(12, $this->formatGetal($totaalE, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
             }
             if ($totaalG <> 0) //divident
             {
               $this->printCol(13, $this->formatGetal($totaalG, $this->pdf->rapport_VOLK_decimaal), $grandtotaal);
             }
             if ($totaalF <> 0)
             {
               $this->printCol(14, $this->formatGetal($totaalF, 1), $grandtotaal);
             }
					 }
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

    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }


	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    
    $this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    $fillArray=array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
     if($this->pdf->modelRapport==true)
     {
       $this->pdf->alignB = array('R','L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');
       $this->pdf->widthB = array(12, 87, 38, 18, 1, 21, 1, 17, 1, 21, 16, 18, 18, 8, 4);
       $this->pdf->widthA = array(89, 18, 38, 1, 21, 1, 17, 1, 21, 16, 18, 18, 8, 4);
     }
     else
     {
         $this->pdf->widthB = array(12, 87, 18, 18, 1, 21, 1, 17, 1, 21, 16, 18, 18, 18, 14);
         $this->pdf->widthA = array(89, 18, 18, 1, 21, 1, 17, 1, 21, 16, 18, 18, 18, 14);
  
       if($this->OIHindeling==true)
       {
         $this->pdf->widthB[1] -=15;
         $this->pdf->widthB[2] +=5;
         $this->pdf->widthB[3] +=5;
         $this->pdf->widthB[7] +=5;
       }
     }

	


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' ".$this->tijdelijkeRapportageFilter
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
		
		if(round($totaalWaarde,0)==0.00)
		  return '';
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetDrawColor(0,0,0);

		$actueleWaardePortefeuille = 0;
		$totaalbegin = array();
    $totaalactueel = 0;
    $totaaldividend = 0;
    $totaaldividendCorrected = 0;
    $totaalpercentage= 0;
    $totaalfondsresultaat= 0;
    $totaalvalutaresultaat= 0;
    $this->pdf->SetFillColor(230);

			$query = "SELECT TijdelijkeRapportage.".$this->verdeling."Omschrijving as Omschrijving, ".
			" TijdelijkeRapportage.".$this->verdeling." as beleggingscategorie,
              SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin,
			  SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) as subtotaalbeginHist,".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type IN('fondsen','rekening') AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter.
			" GROUP BY TijdelijkeRapportage.".$this->verdeling." ".
			" ORDER BY TijdelijkeRapportage.".$this->verdeling."Volgorde asc";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query); //echo $query;exit;
		$DB->Query();
    $i=0;
    $subtotaal = array();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if(!empty($lastCategorie) && $lastCategorie <> $categorien['Omschrijving'])
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

           $procentResultaat = (($totaalactueel - $totaalbegin['subtotaalbegin']  + $totaaldividendCorrected) / ($totaalbegin['subtotaalbegin']  /100));
		    if($totaalbegin['subtotaalbegin']  < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin['subtotaalbeginHist'] , $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false,$totaaldividend);

				$totaalbegin = array();
				$totaalactueel = 0;
        $totaaldividend = 0;
        $totaaldividendCorrected = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
        $i=0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
					$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
	

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving,
			if(Fondsen.OptieBovenliggendFonds <> '',Fondsen.OptieBovenliggendFonds,TijdelijkeRapportage.Fonds) as fondsVolgorde,".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.type,
				 Fondsen.isinCode as isinCode,
				 TijdelijkeRapportage.historischeWaarde,
				 (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
				 TijdelijkeRapportage.".$this->verdeling." as beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.rekening ".
				" FROM TijdelijkeRapportage
				  LEFT JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.".$this->verdeling." =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.type IN('fondsen','rekening') AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter.
				" ORDER BY TijdelijkeRapportage.Lossingsdatum,  fondsVolgorde,OptieBovenliggendFonds, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
			
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

      
      $kopPrinted=false;
			while($subdata = $DB2->NextRecord())
			{
		    if($n>$regels-2 && $this->pdf->GetY()>185)//
        {
           $this->pdf->AddPage();
        }
        if($kopPrinted==false)
        {
          //$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'], "");
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


					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,1);

					if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
					else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,1);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
        $dividendtxt='';

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($dividend['totaal'] <> 0)
					$dividendtxt = $this->formatGetal($dividend['totaal'],$this->pdf->rapport_VOLK_decimaal);
          
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
     
        if($i%2==0)
          $this->pdf->fillCell=$fillArray;
        else
          $this->pdf->fillCell=array();
        $i++;
        if($subdata['type']=='fondsen')
        {
        			if($this->pdf->modelRapport==true)
              {
                $this->xls[]=array($subdata['totaalAantal'],$subdata['fondsOmschrijving'],$subdata['Valuta'],$subdata['isinCode']);
                $this->pdf->row(array($subdata['Valuta'],
                                  $subdata['fondsOmschrijving'],
                                  $subdata['isinCode'],
                                  $this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                                
                                  '',
                                  '',
                                  "",
                                  $this->formatGetal($subdata['actueleFonds'], 2),
                                  '',
                                  $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                                  $percentageVanTotaaltxt,
                                  '',
                                  '',
                                  '',
                                  ''));
              }
              else
							{
                $this->pdf->row(array($subdata['Valuta'],
                                  $subdata['fondsOmschrijving'],
                                  $this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
                                  $this->formatGetal($subdata['historischeWaarde'], 2),
                                  '',
                                  $this->formatGetal($subdata['historischeWaardeTotaalValuta'], $this->pdf->rapport_VOLK_decimaal),
                                  "",
                                  $this->formatGetal($subdata['actueleFonds'], 2),
                                  '',
                                  $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                                  $percentageVanTotaaltxt,
                                  $fondsResultaattxt,
                                  $valutaResultaattxt,
                                  $dividendtxt,
                                  $procentResultaattxt));
							}
        }
        else
				{
          $fondsResultaat=0;
          if(trim($subdata['fondsOmschrijving'])<>'Effectenrekening' || $this->pdf->lastPOST['anoniem'] === '1')
            $rapport_liquiditeiten_omschr='{Tenaamstelling}';
          else
            $rapport_liquiditeiten_omschr='{Tenaamstelling} {Rekening}';
          
          $omschrijving = $rapport_liquiditeiten_omschr;//$this->pdf->rapport_liquiditeiten_omschr;
          $omschrijving = vertaalTekst(str_replace("{Rekening}",$subdata['rekening'],$omschrijving),$this->pdf->rapport_taal);
          $omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($subdata['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
          $omschrijving = vertaalTekst(str_replace("{Valuta}",$subdata['valuta'],$omschrijving),$this->pdf->rapport_taal);
          

          $this->pdf->SetWidths($this->pdf->widthB);
          $this->pdf->SetAligns($this->pdf->alignB);
          
          
          if($i%2==0)
            $this->pdf->fillCell=$fillArray;
          else
            $this->pdf->fillCell=array();
          $i++;
          
          $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
         	if($this->pdf->modelRapport==true)
          {
              $this->pdf->row(array($subdata['Valuta'],
                                            $omschrijving,
                                            '',
                                            "",
                                            "",
                                            "",
                                            "",
                                            "",
                                            '',
                                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                                            $percentageVanTotaaltxt, '', '', '', ''));
           }
           else
					 {
              $this->pdf->row(array($subdata['Valuta'],
                                            $omschrijving,
                                            $this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_VOLK_decimaal),
                                            "",
                                            "",
                                            "",
                                            "",
                                            "",
                                            '',
                                            $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                                            $percentageVanTotaaltxt, '', '', '', ''));
					 }
				}


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
				$procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] + $subtotaal['totaalDividendCorrected'] ) / ($categorien['subtotaalbegin']  /100));
				if($categorien[subtotaalbegin] < 0)
					$procentResultaat = -1 * $procentResultaat;


			//		$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalbegin],$categorien[subtotaalactueel],$subtotaal[percentageVanTotaal], $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat,$subtotaal['totaalDividend']);

//rvv
		// selecteer rente
		$query = "SELECT ".
		" TijdelijkeRapportage.".$this->verdeling." as beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
    " TijdelijkeRapportage.".$this->verdeling." =  '".$categorien['beleggingscategorie']."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].$this->tijdelijkeRapportageFilter.
		" GROUP BY TijdelijkeRapportage.".$this->verdeling." ".
		" ORDER BY TijdelijkeRapportage.".$this->verdeling."Volgorde ";
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
			$totaalbegin['subtotaalbegin']   += $categorien['subtotaalbegin'];
      $totaalbegin['subtotaalbeginHist']   += $categorien['subtotaalbeginHist'];
			$totaalactueel += $categorien['subtotaalactueel'];
      
			$totaalfondsresultaat  += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];
      $totaaldividend        += $subtotaal['totaalDividend'];
      $totaaldividendCorrected        += $subtotaal['totaalDividendCorrected'];


			$lastCategorie = $categorien['Omschrijving'];

			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];
      $grandtotaaldividend  += $subtotaal['totaalDividend'];
      $grandtotaaldividendCorrected  += $subtotaal['totaalDividendCorrected'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin['subtotaalbegin'] + $totaaldividendCorrected) / ($totaalbegin['subtotaalbegin']  /100));
		if($totaalbegin['subtotaalbegin'] < 0)
			$procentResultaat = -1 * $procentResultaat;

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin['subtotaalbeginHist'], $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat,false,$totaaldividend);


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."  Verschil (".round($actueleWaardePortefeuille-$totaalWaarde,2).") ');
			</script>";
			ob_flush();

		}
    $this->pdf->fillCell=array();
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaaldividend);
	
		RestrictiesKader_l77($this->pdf);

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
	}
}
?>