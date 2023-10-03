<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/20 16:19:15 $
File Versie					: $Revision: 1.10 $

$Log: RapportVOLKD_L22.php,v $
Revision 1.10  2019/11/20 16:19:15  rvv
*** empty log message ***

Revision 1.9  2019/10/24 04:24:00  rvv
*** empty log message ***

Revision 1.8  2019/10/23 13:34:01  rvv
*** empty log message ***

Revision 1.7  2019/10/02 15:12:58  rvv
*** empty log message ***

Revision 1.6  2019/09/25 15:31:35  rvv
*** empty log message ***

Revision 1.5  2019/03/27 16:20:18  rvv
*** empty log message ***

Revision 1.4  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.3  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.2  2019/01/19 18:05:31  rvv
*** empty log message ***

Revision 1.1  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.20  2018/08/01 17:56:09  rvv
*** empty log message ***

Revision 1.19  2018/07/05 09:04:18  rvv
*** empty log message ***

Revision 1.18  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.17  2018/05/02 16:13:47  rvv
*** empty log message ***

Revision 1.16  2014/11/06 14:21:35  rvv
*** empty log message ***

Revision 1.15  2014/10/25 14:39:09  rvv
*** empty log message ***

Revision 1.14  2014/08/16 15:31:50  rvv
*** empty log message ***

Revision 1.13  2014/06/04 16:13:28  rvv
*** empty log message ***

Revision 1.12  2014/05/29 12:09:33  rvv
*** empty log message ***

Revision 1.11  2014/05/25 14:38:33  rvv
*** empty log message ***

Revision 1.10  2013/12/04 17:09:05  rvv
*** empty log message ***

Revision 1.9  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.8  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.7  2013/03/09 16:22:24  rvv
*** empty log message ***

Revision 1.6  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.5  2011/06/29 10:33:16  rvv
*** empty log message ***

Revision 1.4  2010/10/11 08:46:37  cvs
*** empty log message ***

Revision 1.3  2010/06/30 16:11:12  rvv
*** empty log message ***

Revision 1.2  2010/06/16 19:15:05  rvv
*** empty log message ***

Revision 1.1  2009/09/27 12:54:02  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");

class RapportVOLKD_L22
{
	function RapportVOLKD_L22($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLKD";
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
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
    $this->aandeel=1;
    $this->verdiept = new portefeuilleVerdiept($this->pdf,$this->portefeuille,$this->rapportageDatum);
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
	  if($this->aandeel <> 1)
	    $waarde=round($waarde,0);
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
	{  //Subtotaal:, Asubtotaalbegin,Bsubtotaalactueel,CpercentageVanTotaal,DfondsResultaat,EvalutaResultaat,FprocentResultaat,Gfalse,H'dividend'
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
			if($totaalH <>0)
				$this->printCol(9,$this->formatGetal($totaalH,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalD <>0)
				$this->printCol(10,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(11,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(12,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();

	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0 )
	{//$title, A$totaalbegin, B$totaalactueel, C$totaalpercentage , D$totaalfondsresultaat, E$totaalvalutaresultaat, F$procentResultaat,Gfalse,H$totaalDividend
		$hoogte = 20;
		if(($this->pdf->GetY() + $hoogte) >= $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel

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
			if($totaalH <>0)
				$this->printCol(9,$this->formatGetal($totaalH,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
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

function getDividend($fonds,$portefeuille='')
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
    
    if($portefeuille=='')
      $portefeuille=$this->portefeuille;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
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
     WHERE Rekeningen.Portefeuille='".$portefeuille."' AND
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
      //        echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    //echo $totaal." $totaalCorrected<br>\n";
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
		$fondsresultwidth = 15;
		$omschrijvingExtra = 10;

		$this->pdf->widthB = array(22,66+$omschrijvingExtra,18,21,21,4,21,21,4,22,$fondsresultwidth,15,15,15);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
		$this->pdf->widthA = array(66+$omschrijvingExtra,18,15,21,21,15,21,21,15,22,22,$fondsresultwidth,15,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->AddPage();
    $this->pdf->rapport_fondsVerdiept_fontcolor=array('r'=>100,'g'=>100,'b'=>100);
    
    $verdiepteFondsen = $this->verdiept->getFondsen();
    $query="SELECT Fonds FROM FondsenBuitenBeheerfee WHERE Fonds IN ('".implode("','",$verdiepteFondsen)."') AND LayoutNr=1";
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();
    $verdiepteFondsen=array();
		while($fonds = $DB->NextRecord())
    {
      $verdiepteFondsen[]=$fonds['Fonds'];
    }
		//listarray($this->verdiept->FondsPortefeuilleData);exit;
    foreach ($verdiepteFondsen as $fonds)
      $this->verdiept->bepaalVerdeling($fonds,$this->verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$this->rapportageDatum,'',$this->rapportageDatumVanaf,false);
    
    
    
    // haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS totaal ".
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


			$query = "SELECT ".
			" TijdelijkeRapportage.beleggingscategorieOmschrijving as Omschrijving,
			 TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdOmschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, 
				TijdelijkeRapportage.hoofdcategorie,
       IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar * ".$this->aandeel."),
       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel.") as subtotaalbegin,
      ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." AS subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' 
			AND TijdelijkeRapportage.beleggingscategorie <> 'Liquiditeiten' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.hoofdcategorie,TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde, beleggingscategorieVolgorde asc, valutaVolgorde asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);// echo $query;exit;
		$DB->Query();
		$lastHoofdCategorie='';
		$hoofdtotaal=array();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);

        $procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividendCorrected) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;


//echo "$lastCategorie $procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividend) / ($totaalbegin /100)); <br>\n";

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false,false,$totaalDividend);

				$totaalbegin = 0;
				$totaalactueel = 0;
        $totaalDividend = 0;
        $totaalDividendCorrected =0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;

				$totaalResultaat = 0;
				$totaalBijdrage = 0;
			}
			if(count($hoofdtotaal)>0 && $lastHoofdCategorie <> $categorien['hoofdOmschrijving'])
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
				$procentResultaat = (($hoofdtotaal['actuelePortefeuilleWaardeEuro'] - $hoofdtotaal['beginPortefeuilleWaardeEuro'] + $hoofdtotaal['dividendCorrected']) / ($hoofdtotaal['beginPortefeuilleWaardeEuro'] /100));
				$this->printTotaal('Totaal '.$lastHoofdCategorie, $hoofdtotaal['beginPortefeuilleWaardeEuro'], $hoofdtotaal['actuelePortefeuilleWaardeEuro'], $hoofdtotaal['percentageVanTotaal'] ,
													 $hoofdtotaal['fondsResultaat'] , $hoofdtotaal['valutaResultaat'] , $procentResultaat,true,false,$hoofdtotaal['dividend'] );
				$hoofdtotaal=array();
			}


			if($lastHoofdCategorie <> $categorien['hoofdOmschrijving'])
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
				$this->pdf->Cell(100,4,vertaalTekst($categorien['hoofdOmschrijving'],$this->pdf->rapport_taal), 0,1, "L");

			}

			if($lastCategorie <> $categorien['Omschrijving'])
			{
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

		
				$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'], "");
				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta * ".$this->aandeel." as beginPortefeuilleWaardeInValuta,".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery  * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
				" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie, 
				 if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.Fonds ,Fondsen.OptieBovenliggendFonds) as onderliggendFonds,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
				" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND TijdelijkeRapportage.beleggingscategorie <> 'Liquiditeiten' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, onderliggendFonds,TijdelijkeRapportage.fondspaar, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
		
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
			while($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
        $dividend=$this->getDividend($subdata['fonds']);
   
        $dividend['totaal']=$dividend['totaal']*$this->aandeel;
        $dividend['corrected']=$dividend['corrected']*$this->aandeel;
      //  listarray($divided);
    //echo $dividend['totaal']. ' '.$subdata['fonds'].'<br>';
				$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
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
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");

				//$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving],null,null,null,null,"http://url?code=");
				if($this->pdf->rapport_VOLK_link == 1)
				{
					// getStroevecode.
					$DBx = new DB();
					$DBx->SQL("SELECT stroeveCode FROM Fondsen Where Fonds = '".$subdata['fonds']."'");
					$DBx->Query();
					$fdata = $DBx->nextRecord();

					$url = str_replace("[stroevecode]",$fdata['stroeveCode'],$this->pdf->rapport_VOLK_url);
					$url = str_replace("[fonds]",$subdata['fonds'],$url);

					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,$url);
				}
				else
				{
					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'],null,null,null,null,null);
				}

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
        if($dividend['totaal'] <> 0)  
          $dividendTxt=$this->formatGetal($dividend['totaal'],0);
	      else
          $dividendTxt='';

					$this->pdf->row(array($this->formatAantal($subdata['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												"",
												$this->formatGetal($subdata['actueleFonds'],2),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
												"",
												$this->formatGetal($subdata['beginwaardeLopendeJaar'],2),
												$this->formatGetal($subdata['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												"",
                        $dividendTxt,
												$fondsResultaattxt,
												$valutaResultaattxt,
												$procentResultaattxt	)	);

        
        

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

				$subtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
        $subtotaal['dividend'] +=$dividend['totaal'];
        $subtotaal['dividendCorrected'] +=$dividend['corrected'];
				$subtotaal['fondsResultaat'] +=$fondsResultaat;
				$subtotaal['valutaResultaat'] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;

				$hoofdtotaal['actuelePortefeuilleWaardeEuro'] +=$subdata['actuelePortefeuilleWaardeEuro'];
				$hoofdtotaal['beginPortefeuilleWaardeEuro'] +=$subdata['beginPortefeuilleWaardeEuro'];
				$hoofdtotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hoofdtotaal['dividend'] +=$dividend['totaal'];
				$hoofdtotaal['dividendCorrected'] +=$dividend['corrected'];
				$hoofdtotaal['fondsResultaat'] +=$fondsResultaat;
				$hoofdtotaal['valutaResultaat'] +=$valutaResultaat;
        
        
        if(in_array($subdata['fonds'],$verdiepteFondsen))
        {
          $subFondsData = $this->verdiept->getFondsVerdeling($subdata['fonds'],1000);
					//listarray($subFondsData);
          $this->pdf->SetTextColor($this->pdf->rapport_fondsVerdiept_fontcolor['r'],$this->pdf->rapport_fondsVerdiept_fontcolor['g'],$this->pdf->rapport_fondsVerdiept_fontcolor['b']);
          //   listarray($subFondsData);
          $sortedSubFonds=array();
          foreach($subFondsData as $fonds=>$fondsData)
          {
            $sortedSubFonds[$fondsData['Omschrijving']]=$fondsData;
          }
          
          //ksort($sortedSubFonds,SORT_STRING | SORT_FLAG_CASE);
          uksort($sortedSubFonds, 'strcasecmp');
          //listarray($sortedSubFonds);
          
          $huisfondsPortefeuille=$this->verdiept->FondsPortefeuilleData[$subdata['fonds']];

					$DBx = new DB();
					$query="SELECT boekdatum FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
WHERE Rekeningmutaties.Fonds = '".$subdata['fonds']."' 
AND Rekeningen.Portefeuille='".$this->portefeuille."' 
AND Rekeningmutaties.boekDatum>='".substr($this->rapportageDatum,0,4)."-01-01' 
AND Rekeningmutaties.boekDatum<='".substr($this->rapportageDatum,0,10)."'  ORDER BY boekdatum asc limit 1";
					$DBx->SQL($query);
					$DBx->Query();
					$eersteDag = $DBx->nextRecord();

          foreach ($sortedSubFonds as $fonds)
          {
            
            $fonds['portefeuilleDeel']  = $fonds['percentage'] * ($percentageVanTotaal/100);
            $fonds['waardeRegelEur']    = $fonds['portefeuilleDeel'] * $totaalWaarde;
            $fonds['valutaWaardeRegel'] = $fonds['waardeRegelEur'] / $fonds['valutakoers'];
            $fonds['aantal']=$fonds['valutaWaardeRegel']/$fonds['fondskoers'] / $fonds['Fondseenheid'];
            $fonds['aandeelInHuis']=$fonds['aantal']/$fonds['overige']['totaalAantal'];
            $this->pdf->Cell($this->pdf->widthB[0],4," ",null,null,null,null,null);
            $this->pdf->Cell($this->pdf->widthB[1],4,"     ".$fonds['Omschrijving'],null,null,null,null,null);
            $this->pdf->setX($this->pdf->marge);

						if(substr($eersteDag['boekdatum'],5,5) <> '01-01')
						{
              $query="SELECT boekdatum FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningmutaties.Fonds = '".mysql_real_escape_string($fonds['overige']['Fonds'])."'
AND Rekeningen.Portefeuille='".mysql_real_escape_string($huisfondsPortefeuille)."'
AND Rekeningmutaties.boekDatum>='".substr($this->rapportageDatum,0,4)."-01-01'
AND Rekeningmutaties.boekDatum<='".substr($this->rapportageDatum,0,10)."'  ORDER BY boekdatum asc limit 1";
              $DBx->SQL($query);
              $DBx->Query();
              $eersteHuisDag = $DBx->nextRecord();
              if(db2jul($eersteHuisDag['boekdatum'])>db2jul($eersteDag['boekdatum']))
							{
								$transactieBoekdatum=$eersteHuisDag['boekdatum'];
							}
							else
							{
                $transactieBoekdatum=$eersteDag['boekdatum'];
							}
       
							$fonds['overige']['beginwaardeLopendeJaar']=globalGetFondsKoers($fonds['overige']['Fonds'],$transactieBoekdatum);
							$valutakoers=getValutaKoers($fonds['overige']['valuta'],$transactieBoekdatum);

						//	listarray($fonds);
							//echo $fonds['overige']['valuta']."|".$fonds['overige']['Fonds']."|".$fonds['overige']['beginPortefeuilleWaardeInValuta']."|".$fonds['overige']['beginPortefeuilleWaardeEuro']."<br>\n";
							$fonds['overige']['beginPortefeuilleWaardeInValuta']=$fonds['overige']['totaalAantal']*$fonds['overige']['fondsEenheid']*$fonds['overige']['beginwaardeLopendeJaar'];
							$fonds['overige']['beginPortefeuilleWaardeEuro']=$fonds['overige']['beginPortefeuilleWaardeInValuta']*$valutakoers;
							//echo $fonds['overige']['valuta']."|".$fonds['overige']['Fonds']."|".$fonds['overige']['beginPortefeuilleWaardeInValuta']."|".$fonds['overige']['beginPortefeuilleWaardeEuro']."<br>\n";
						}
            
            $fondsResultaat = ($fonds['overige']['actuelePortefeuilleWaardeInValuta'] - $fonds['overige']['beginPortefeuilleWaardeInValuta']) * $fonds['overige']['actueleValuta'];
            $valutaResultaat = $fonds['overige']['ActuelePortefeuilleWaardeEuro'] - $fonds['overige']['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
            $fonds['fondsResultaat']=$fondsResultaat;
            $fonds['valutaResultaat']=$valutaResultaat;
            
            $dividend=$this->getDividend($fonds['Fonds'],$huisfondsPortefeuille);
            
            $procentResultaat = (( $fonds['overige']['ActuelePortefeuilleWaardeEuro'] -  $fonds['overige']['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ( $fonds['overige']['beginPortefeuilleWaardeEuro'] /100));
            
            //    $dividend['corrected']
            
            //listarray($dividend);
            //  listarray($verdiepteFondsen);
            //       listarray($fonds); echo $aandeelInHuis;
            //         exit;
            
            if($dividend['totaal']<>0)
              $dividendTxt=$this->formatGetal($fonds['aandeelInHuis']  *$dividend['totaal'],0);
            else
              $dividendTxt='';
            if($fonds['fondsResultaat']<>0)
              $fondsResultaattxt= $this->formatGetal($fonds['aandeelInHuis']  *$fonds['fondsResultaat'],0);
            else
              $fondsResultaattxt='';
            if($fonds['valutaResultaat']<>0)
              $valutaResultaattxt= $this->formatGetal($fonds['aandeelInHuis']  *$fonds['valutaResultaat'],0);
            else
              $valutaResultaattxt='';
            $procentResultaattxt= $this->formatGetal($procentResultaat,1);
            
            $this->pdf->row(array($this->formatAantal($fonds['aantal'],1,0),
                              "",
                              $this->formatGetal($fonds['fondskoers'],2),
                              $this->formatGetal($fonds['waardeRegelEur'],$this->pdf->rapport_VOLK_decimaal),
                              $this->formatGetal($fonds['portefeuilleDeel']*100,$this->pdf->rapport_VOLK_decimaal_proc),
                              '',
                              $this->formatGetal($fonds['overige']['beginwaardeLopendeJaar'],2),
                              $this->formatGetal($fonds['aandeelInHuis']  *$fonds['overige']['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
                              '',
                              $dividendTxt,
                              $fondsResultaattxt,
                              $valutaResultaattxt,
                              $procentResultaattxt	));
            
          }
          $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
        }

			}

			// print categorie footers
			if($this->pdf->rapport_VOLK_geensubtotaal == 1)
			{
			}
			else
			{
				$procentResultaat = (($categorien['subtotaalactueel']  - $categorien['subtotaalbegin'] + $subtotaal['dividendCorrected']) / ($categorien['subtotaalbegin']  /100));
				if($categorien['subtotaalbegin'] < 0)
					$procentResultaat = -1 * $procentResultaat;
//echo $categorien['beleggingscategorie']."		$procentResultaat = ((".$categorien['subtotaalactueel']."  - ".$categorien['subtotaalbegin']." + ".$subtotaal['dividend'].") / (".$categorien['subtotaalbegin']."  /100)); <br>\n";

					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalbegin'],$categorien['subtotaalactueel'],$subtotaal['percentageVanTotaal'], $subtotaal['fondsResultaat'], $subtotaal['valutaResultaat'], $procentResultaat,false,$subtotaal['dividend']);


			}

			// totaal op categorie tellen
			$totaalbegin   += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];

      $totaalDividend  += $subtotaal['dividend'];
      $totaalDividendCorrected  += $subtotaal['dividendCorrected'];
			$totaalfondsresultaat  += $subtotaal['fondsResultaat'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];
			$totaalpercentage      += $subtotaal['percentageVanTotaal'];

			$lastCategorie = $categorien['Omschrijving'];
			$lastHoofdCategorie = $categorien['hoofdOmschrijving'];

      $grandtotaalDividend  += $subtotaal['dividend'];
      $grandtotaalDividendCorrected  += $subtotaal['dividendCorrected'];
			$grandtotaalvaluta += $subtotaal['valutaResultaat'];
			$grandtotaalfonds  += $subtotaal['fondsResultaat'];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividendCorrected) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

//echo "$lastCategorie $procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividend) / ($totaalbegin /100)); <br>\n";
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat,false,$totaalDividend);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) * ".$this->aandeel." as subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " * ".$this->aandeel." as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
				$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)));


			//$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_taal));
			//$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;


						$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);

					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{

						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['rentedatum']));
							if($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
								$rentePeriodetxt .= " / ".$subdata['renteperiode'];
						}

						$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
						$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
	

						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'].$rentePeriodetxt);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



							$this->pdf->row(array("","","","",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
														"","","", "",
														$percentageVanTotaaltxt));
				

					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					$subtotaalPercentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde/100);


					if($this->pdf->rapport_VOLK_geensubtotaal == 1)
					{
					}
					else
					{
						$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, $subtotaalPercentageVanTotaal, "", "");
					}

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
				}
			}

			// totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}


		if(count($hoofdtotaal)>0 && $lastHoofdCategorie <> $categorien['hoofdOmschrijving'])
		{
			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
			$procentResultaat = (($hoofdtotaal['actuelePortefeuilleWaardeEuro'] - $hoofdtotaal['beginPortefeuilleWaardeEuro'] + $hoofdtotaal['dividendCorrected']) / ($hoofdtotaal['beginPortefeuilleWaardeEuro'] /100));
			$this->printTotaal('Totaal '.$lastHoofdCategorie, $hoofdtotaal['beginPortefeuilleWaardeEuro'], $hoofdtotaal['actuelePortefeuilleWaardeEuro'], $hoofdtotaal['percentageVanTotaal'] ,
												 $hoofdtotaal['fondsResultaat'] , $hoofdtotaal['valutaResultaat'] , $procentResultaat,true,false,$hoofdtotaal['dividend'] );
			$hoofdtotaal=array();
		}


		$beginQuery = $this->pdf->ValutaKoersBegin;

		// Liquiditeiten
		$query="SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.rekening , TijdelijkeRapportage.type, ".
			" TijdelijkeRapportage.totaalAantal * ".$this->aandeel." as totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta * ".$this->aandeel." as beginPortefeuilleWaardeInValuta,".
			" TijdelijkeRapportage.Valuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery  * ".$this->aandeel." as beginPortefeuilleWaardeEuro, ".
			//" TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta * ".$this->aandeel." as actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " * ".$this->aandeel." as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
			" FROM TijdelijkeRapportage 
			LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening = Rekeningen.rekening   AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" (TijdelijkeRapportage.type = 'rekening' OR (TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.beleggingscategorie = 'Liquiditeiten' )) AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			//$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
				$this->pdf->row(array("",vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal)));


			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}



			foreach($liqiteitenBuffer as $data)
			{
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

				
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->setX($this->pdf->marge);



			  if($data['type']=='fondsen')
				{
					$subdata=$data;
					$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['beginPortefeuilleWaardeInValuta']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
					$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] - $fondsResultaat;
					$dividend = $this->getDividend($subdata['fonds']);

					$dividend['totaal'] = $dividend['totaal'] * $this->aandeel;
					$dividend['corrected'] = $dividend['corrected'] * $this->aandeel;
					//  listarray($divided);
					//echo $dividend['totaal']. ' '.$subdata['fonds'].'<br>';
					$fondsResultaatprocent = ($fondsResultaat / $subdata['beginPortefeuilleWaardeEuro']) * 100;
					$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] / 100));
					if ($subdata['beginPortefeuilleWaardeEuro'] < 0)
					{
						$procentResultaat = -1 * $procentResultaat;
					}

					$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde / 100);


					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal, $this->pdf->rapport_VOLK_decimaal_proc) . " %";

					if ($procentResultaat > 1000 || $procentResultaat < -1000)
					{
						$procentResultaattxt = "p.m.";
					}
					else
					{
						$procentResultaattxt = $this->formatGetal($procentResultaat, $this->pdf->rapport_VOLK_decimaal_proc);
					}


					$fondsResultaattxt = "";
					$valutaResultaattxt = "";

					if ($fondsResultaat <> 0)
					{
						$fondsResultaattxt = $this->formatGetal($fondsResultaat, $this->pdf->rapport_VOLK_decimaal);
					}

					if ($valutaResultaat <> 0)
					{
						$valutaResultaattxt = $this->formatGetal($valutaResultaat, $this->pdf->rapport_VOLK_decimaal);
					}

					if ($dividend['totaal'] <> 0)
					{
						$dividendTxt = $this->formatGetal($dividend['totaal'], 0);
					}
					else
					{
						$dividendTxt = '';
					}

					$this->pdf->row(array($this->formatAantal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal, $this->pdf->rapport_VOLK_aantalVierDecimaal),
														"",
														$this->formatGetal($subdata['actueleFonds'], 2),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($percentageVanTotaal, $this->pdf->rapport_VOLK_decimaal_proc),
														"",
														$this->formatGetal($subdata['beginwaardeLopendeJaar'], 2),
														$this->formatGetal($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
														"",
														$dividendTxt,
														$fondsResultaattxt,
														$valutaResultaattxt,
														$procentResultaattxt));


					$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

					$subtotaal['percentageVanTotaal'] += $percentageVanTotaal;
					$subtotaal['dividend'] += $dividend['totaal'];
					$subtotaal['dividendCorrected'] += $dividend['corrected'];
					$subtotaal['fondsResultaat'] += $fondsResultaat;
					$subtotaal['valutaResultaat'] += $valutaResultaat;
					$subtotaal['totaalResultaat'] += $subTotaalResultaat;
					$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;

					$grandtotaalDividend  += $subtotaal['dividend'];
					$grandtotaalDividendCorrected  += $subtotaal['dividendCorrected'];
					$grandtotaalvaluta += $subtotaal['valutaResultaat'];
					$grandtotaalfonds  += $subtotaal['fondsResultaat'];

					$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
					$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
					$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
					$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;
					//rvv
				}
        else
				  $this->pdf->row(array("",
												"",
												"",
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt,
												"",
												"",
												"",
												"",
												""));
				

	

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


			if(($this->pdf->GetY() + 4) > $this->pdf->pagebreak)
			{
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$y = $this->pdf->getY();
			$this->pdf->setY(($y+4));
			$this->printCol(7,vertaalTekst("Totaal ongerealiseerd resultaat",$this->pdf->rapport_taal),"tekst");
			$this->pdf->setY($y);
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"",$grandtotaalfonds,$grandtotaalvaluta,"",true,false,$grandtotaalDividend);

	}

	function getFondsKoers($fonds,$datum)
	{
	    $DB2=new DB();
	  	$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$datum."' AND Fonds = '".$fonds."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers = $DB2->LookupRecord();
			return $koers['Koers'];
	}
}
?>
