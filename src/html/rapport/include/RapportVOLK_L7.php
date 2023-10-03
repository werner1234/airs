<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/05/30 16:10:58 $
File Versie					: $Revision: 1.12 $

$Log: RapportVOLK_L7.php,v $
Revision 1.12  2018/05/30 16:10:58  rvv
*** empty log message ***

Revision 1.11  2018/05/26 17:24:24  rvv
*** empty log message ***

Revision 1.10  2016/07/16 15:16:49  rvv
*** empty log message ***

Revision 1.9  2016/05/16 07:35:34  rvv
*** empty log message ***

Revision 1.8  2016/03/27 17:35:07  rvv
*** empty log message ***

Revision 1.7  2015/04/19 08:35:38  rvv
*** empty log message ***

Revision 1.6  2015/04/11 17:08:24  rvv
*** empty log message ***

Revision 1.5  2015/01/24 19:54:52  rvv
*** empty log message ***

Revision 1.4  2014/10/29 16:47:20  rvv
*** empty log message ***

Revision 1.3  2014/10/19 08:52:15  rvv
*** empty log message ***

Revision 1.2  2014/09/27 16:07:13  rvv
*** empty log message ***

Revision 1.1  2013/10/23 15:45:31  rvv
*** empty log message ***

Revision 1.56  2011/06/25 16:51:45  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L7
{
	function RapportVOLK_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData[]=array('Portefeuille','Client','Depotbank','Accountmanager','ISINCode','beleggingscategorie','fonds','fondsvaluta','Aantal','fondskoers','koersdatum','Waarde in EUR (ex opg rente)','Opgelopen rente in EUR','Totale waarde in EUR','Rapportagedatum');

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


	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF, $TotaalG = 0, $totaalH = 0)
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

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			if($totaalB <>0)
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				$this->printCol(3,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(7,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalC <>0)
				$this->printCol(4,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			if($totaalD <>0)
				$this->printCol(9,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(11,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(12,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
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

		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->printCol(1,$title,"tekst");
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(3,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(7,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(4,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($totaalD <>0)
				$this->printCol(9,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
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

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank,Portefeuilles.Accountmanager, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

			$fondsresultwidth = 3;
			$omschrijvingExtra = 10;
			$this->pdf->widthB = array(18+8,50+$omschrijvingExtra,18+8,21,21,10,25,21,10,22,$fondsresultwidth,21,18);
			$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
			$this->pdf->widthA = array(66+8+$omschrijvingExtra,18+8,15,21,21,10,25,21,10,22,22,$fondsresultwidth,18);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->AddPage();
    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
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

			$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
	    "
       IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as subtotaalbegin,
      ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();
    $xlsData=array();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
			 
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

        $procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);

				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
			{
			  if($this->pdf->GetY()>=165)
          $this->pdf->AddPage();
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
				$this->pdf->row(array("",vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal)));
			}
			// subkop (valuta)
			if($categorien['valuta'] == $this->pdf->rapportageValuta)
			  $beginQuery = 'beginwaardeValutaLopendeJaar';
			else
			  $beginQuery = $this->pdf->ValutaKoersBegin;

   //     echo $this->formatAantal($subdata['totaalAantal'],0).' '.$subdata['fondsOmschrijving'].' '.$this->pdf->getY()."<br>\n";
        if($this->pdf->GetY()>=170)
          $this->pdf->AddPage();
				$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'], "");
				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta,TijdelijkeRapportage.koersDatum, TijdelijkeRapportage.rapportageDatum, 
          TijdelijkeRapportage.type, Fondsen.ISINCode,".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro, ".
				//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.beleggingscategorieOmschrijving,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage LEFT JOIN Fondsen On TijdelijkeRapportage.fonds=Fondsen.Fonds WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
				" TijdelijkeRapportage.type IN('fondsen','rente')  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;

			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query(); 
//echo $subquery."<br><br>";exit;
      $totaalRegels=$DB2->records();
      $n=0;
      $tmpData=array();
      $renteRegel=array();
			while($subdata = $DB2->NextRecord())
			{
				$xlsData[$subdata['fonds']][$subdata['type']]=$subdata;
			  if($subdata['type']=='rente')
        {
          $renteRegel['type']='rente';
          $renteRegel['fondsOmschrijving']='Opgelopen rente';
          $renteRegel['actuelePortefeuilleWaardeEuro']+=$subdata['actuelePortefeuilleWaardeEuro'];
          $renteRegel['beginPortefeuilleWaardeEuro']+=$subdata['actuelePortefeuilleWaardeEuro'];
          $categorien['subtotaalactueel']+=$subdata['actuelePortefeuilleWaardeEuro'];
        }
        else
				{
					$tmpData[] = $subdata;
				}
      }
      if(count($renteRegel)>0)
        $tmpData[]=$renteRegel;
      
      foreach($tmpData as $subdata)
			{
			  $n++;
       // echo "$totaalRegels $n ".$this->pdf->GetY()." ".$subdata['fondsOmschrijving']."<br>\n"; 
        if($n>=$totaalRegels && $this->pdf->GetY()>175)
          $this->pdf->AddPage();
        
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;

				$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
        
    //    if($subdata['type']=='rente')
    //      $valutaResultaat=0;
    //    else
				  $valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;

				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
				if($subdata['beginPortefeuilleWaardeEuro'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);

   //     echo $this->formatAantal($subdata['totaalAantal'],0).' '.$subdata['fondsOmschrijving'].' '.$this->pdf->getY()."<br>\n";
      //  if($this->pdf->GetY()>175)
      //    $this->pdf->AddPage();

				$this->pdf->setX($this->pdf->marge);
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,null);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				
        if($subdata['type']=='rente')
          $this->pdf->row(array('','','',$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)));
        else
          $this->pdf->row(array($this->formatAantal($subdata['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												"",
												$this->formatGetal($subdata['actueleFonds'],2),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$this->formatGetal($subdata['beginwaardeLopendeJaar'],2),
												$this->formatGetal($subdata['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												"",
												$fondsResultaattxt,
												$fondsResultaatprocenttxt,
												$valutaResultaattxt,
												$procentResultaattxt	)	);


				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;

			}
			$procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] ) / ($categorien['subtotaalbegin']  /100));
			if($categorien['subtotaalbegin'] < 0)
				$procentResultaat = -1 * $procentResultaat;

			$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalbegin'],$categorien['subtotaalactueel'],$subtotaal['percentageVanTotaal'], $subtotaal['fondsResultaat'], $subtotaal['valutaResultaat'], $procentResultaat);

			// totaal op categorie tellen
			$totaalbegin   += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];

			$totaalfondsresultaat  += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $categorien['Omschrijving'];

			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		// totaal voor de laatste categorie
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);
/*
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();


    $totaalRegels=1;
    $n=0;
		if($DB->records() > 0)
		{
	  	  $n++;

        if($n>=$totaalRegels && $this->pdf->GetY()>=165)
          $this->pdf->AddPage();
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
				$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)));
			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
			// totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}
*/
		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , TijdelijkeRapportage.type, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.beleggingscategorieOmschrijving,  TijdelijkeRapportage.rapportageDatum,
			  ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal, Rekeningen.Depotbank".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
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
		  if($this->pdf->GetY()>=160)
        $this->pdf->AddPage();
		  	$totaalLiquiditeitenInValuta = 0;
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
				$this->pdf->row(array("",vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal)));
	      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
				$xlsData[$data['rekening']][$data['type']]=$data;
			}
      
      
      $totaalRegels=count($liqiteitenBuffer);
      $n=0;
      foreach($liqiteitenBuffer as $data)
			{
			 	$n++;
        if($n>=$totaalRegels && $this->pdf->GetY()>=165)
        { 
          $this->pdf->AddPage();
        } 
				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];
				$subtotaalPercentageVanTotaal  = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc);
		
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("","",$data['Depotbank'],$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt,"","","","",""));
			}
			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
			$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$subtotaalPercentageVanTotaal,"","");
		} // einde liquide

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();

		}
			if(($this->pdf->GetY() + 20) > $this->pdf->pagebreak)
			{
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$y = $this->pdf->getY();
			$this->pdf->setY(($y+4));
			$this->printCol(7,vertaalTekst("Totaal ongerealiseerd resultaat",$this->pdf->rapport_taal),"tekst");
			$this->pdf->setY($y);
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"",$grandtotaalfonds,$grandtotaalvaluta,"",true);
		
		$this->pdf->ln();

		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
	//		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		foreach($xlsData as $key=>$fondsData)
		{
      if(isset($fondsData['rekening']))
				$this->pdf->excelData[]=array($this->portefeuille,$this->portefeuilledata['Client'],$this->portefeuilledata['Depotbank'],$this->portefeuilledata['Client'],
					$fondsData['rekening']['ISINCode'],$fondsData['rekening']['beleggingscategorie'],$fondsData['rekening']['rekening'],$fondsData['rekening']['valuta'],$fondsData['rekening']['totaalAantal'],$fondsData['rekening']['actueleFonds'],
					$fondsData['rekening']['koersDatum'],$fondsData['rekening']['actuelePortefeuilleWaardeEuro'],$fondsData['rente']['actuelePortefeuilleWaardeEuro'],
					$fondsData['rekening']['actuelePortefeuilleWaardeEuro']+$fondsData['rente']['actuelePortefeuilleWaardeEuro'],$fondsData['rekening']['rapportageDatum']);
			else
			$this->pdf->excelData[]=array($this->portefeuille,$this->portefeuilledata['Client'],$this->portefeuilledata['Depotbank'],$this->portefeuilledata['Client'],
				$fondsData['fondsen']['ISINCode'],$fondsData['fondsen']['beleggingscategorie'],$fondsData['fondsen']['fonds'],$fondsData['fondsen']['Valuta'],$fondsData['fondsen']['totaalAantal'],$fondsData['fondsen']['actueleFonds'],
				$fondsData['fondsen']['koersDatum'],$fondsData['fondsen']['actuelePortefeuilleWaardeEuro'],$fondsData['rente']['actuelePortefeuilleWaardeEuro'],
				$fondsData['fondsen']['actuelePortefeuilleWaardeEuro']+$fondsData['rente']['actuelePortefeuilleWaardeEuro'],$fondsData['fondsen']['rapportageDatum']);

		}

		if($this->pdf->rapport_VOLK_rendement == 1)
			printRendement_L7($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		elseif ($this->pdf->rapport_VOLK_rendement == 2)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;

//		if($this->pdf->rapport_layout == 8)
//		  include_once('indexGrafiek.php');
		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata['AEXVergelijking'] > 0 ) //|| $this->pdf->rapport_layout == 8 voor L8 de index er weer uitgehaald.
		{
		  if(!$this->pdf->rapport_VOLK_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
	}
}
?>
