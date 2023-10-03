<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/07/07 17:35:19 $
File Versie					: $Revision: 1.17 $

$Log: RapportVOLK_L36.php,v $
Revision 1.17  2018/07/07 17:35:19  rvv
*** empty log message ***

Revision 1.16  2015/01/17 18:32:01  rvv
*** empty log message ***

Revision 1.15  2015/01/14 20:18:36  rvv
*** empty log message ***

Revision 1.14  2014/10/25 14:39:09  rvv
*** empty log message ***

Revision 1.13  2014/08/16 15:31:50  rvv
*** empty log message ***

Revision 1.12  2014/03/19 16:39:09  rvv
*** empty log message ***

Revision 1.11  2014/03/12 15:13:44  rvv
*** empty log message ***

Revision 1.10  2014/03/08 17:02:57  rvv
*** empty log message ***

Revision 1.9  2014/03/02 12:50:18  rvv
*** empty log message ***

Revision 1.8  2014/03/01 14:01:38  rvv
*** empty log message ***

Revision 1.7  2012/07/08 19:29:46  rvv
*** empty log message ***

Revision 1.6  2012/06/30 14:42:50  rvv
*** empty log message ***

Revision 1.5  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.4  2012/04/16 17:56:27  rvv
*** empty log message ***

Revision 1.3  2012/04/08 08:14:05  rvv
*** empty log message ***

Revision 1.2  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.1  2012/03/25 12:29:01  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L36
{
	function RapportVOLK_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_HSE_geenrentespec=true;
		/*
		if($this->pdf->rapport_VOLK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VOLK_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend overzicht lopend kalenderjaar";

		if(substr(jul2form($this->pdf->rapport_datumvanaf),0,5) != '01-01')
			$this->pdf->rapport_titel = "Vergelijkend overzicht rapportage periode";
			*/
		//$this->takje=base64_decode("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");
		$this->takje=base64_decode("iVBORw0KGgoAAAANSUhEUgAAACMAAAAxCAIAAADfpYeeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RjIyNDY3QjFBMTNEMTFFMzk4ODVEMDg1OUQ4MjE3OUYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RjIyNDY3QjBBMTNEMTFFMzk4ODVEMDg1OUQ4MjE3OUYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBXaW5kb3dzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9IkI4NjEyQUVENzUwMDJEQjA0MjNCQ0M4RUFDODU4QURBIiBzdFJlZjpkb2N1bWVudElEPSJCODYxMkFFRDc1MDAyREIwNDIzQkNDOEVBQzg1OEFEQSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PoxDKtEAAAg/SURBVHja3Jh5jF1VHcfPfpd379vfvG22dmZaOkBp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");
    $this->pdf->rapport_titel =	"Overzicht portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
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
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal,true),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF = 0, $grandtotaal=false, $totaalG = 0, $totaalH = 0, $totaalI = 0 )
	{
		$hoogte = 20;
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
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(9,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(5,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(12,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($totaalG <>0)
				$this->printCol(11,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
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
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);

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
    $legenda=false;


			$fondsresultwidth = 20;
			$omschrijvingExtra = 1;


			$this->pdf->widthB = array(10,50+$omschrijvingExtra,18,15,23,21,1,15,23,21,16,22,$fondsresultwidth,16,13);
			$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
			$this->pdf->widthA = array(60+$omschrijvingExtra,18,15,23,21,1,15,23,21,15,22,$fondsresultwidth,16,13);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->AddPage();

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
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

			$query = "SELECT TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieOmschrijving AS Omschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
BeleggingscategoriePerFonds.duurzaamheid,

TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
				" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				   TijdelijkeRapportage.portefeuille

FROM ".
			" TijdelijkeRapportage
			LEFT Join BeleggingscategoriePerFonds ON TijdelijkeRapportage.fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc,TijdelijkeRapportage.fondsOmschrijving";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($fonds = $DB->NextRecord())
		{
		  if( $fonds['hoofdcategorieOmschrijving'] == '')
		    $fonds['hoofdcategorieOmschrijving'] ='Geen hoofdcategorie';
		  if($fonds['Omschrijving']=='')
		    $fonds['Omschrijving']='Geen categorie';
		  if($fonds['beleggingssectorOmschrijving']=='')
		    $fonds['beleggingssectorOmschrijving']='Geen sector';



			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $fonds[Omschrijving] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
        $procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
        
    //    echo "$lastCategorie $procentResultaat = (($totaalactueel - $totaalbegin + $totaalDividend) / ($totaalbegin /100)); <br>\n";
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat,false,$totaaldividend);

				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$totaalpercentage = 0;
				$procentResultaat = 0;
				$totaalResultaat = 0;
				$totaalBijdrage = 0;
        $totaaldividend = 0;
        $totaaldividendCorrected=0;
			}

			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{
					$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}

			if($lastCategorie <> $fonds['Omschrijving'] && $fonds['hoofdcategorieOmschrijving'] <> $fonds['Omschrijving'])
			{
					$this->printKop(vertaalTekst('    '.$fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			if($lastSector <> $fonds['beleggingssectorOmschrijving'] && $fonds['beleggingssectorOmschrijving'] <> 'Geen sector')
			{
					$this->printKop(vertaalTekst('       '.$fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
			}

        $dividend=$this->getDividend($fonds['fonds']);

				$fondsResultaat = ($fonds[actuelePortefeuilleWaardeInValuta] - $fonds[beginPortefeuilleWaardeInValuta]) * $fonds[actueleValuta] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $fonds[beginPortefeuilleWaardeEuro]) * 100;
				$valutaResultaat = $fonds[actuelePortefeuilleWaardeEuro] - $fonds[beginPortefeuilleWaardeEuro] - $fondsResultaat;

				$procentResultaat = (($fonds[actuelePortefeuilleWaardeEuro] - $fonds[beginPortefeuilleWaardeEuro] + $dividend['corrected']) / ($fonds[beginPortefeuilleWaardeEuro] /100));
				if($fonds[beginPortefeuilleWaardeEuro] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($fonds[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
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
			//	$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			//	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			  $fondsOmschrijving=$fonds['fondsOmschrijving'];
        $stringWidth=$this->pdf->GetStringWidth($fondsOmschrijving);
        if($stringWidth>$this->pdf->widthB[1])
        {
          $widthCount=$this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000*3;
          $newFondsOmschrijving='';
          for($i=0; $i<strlen($fondsOmschrijving); $i++) 
          { 
            $char=$fondsOmschrijving[$i];
            $charWidth=$this->pdf->CurrentFont['cw'][$char]*$this->pdf->FontSize/1000;
            $widthCount+=$charWidth;
            if($widthCount < $this->pdf->widthB[1])
              $newFondsOmschrijving.=$char;
          }  
          $fondsOmschrijving=$newFondsOmschrijving."...";
        }
				$this->pdf->Cell($this->pdf->widthB[1],4,$fondsOmschrijving,null,null,null,null,null);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
        if($dividend['totaal'] <> 0)  
          $dividendTxt=$this->formatGetal($dividend['totaal'],2);
	      else
          $dividendTxt='';
          
				$this->pdf->row(array("",
													"",
													$this->formatAantal($fonds[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$this->formatGetal($fonds[beginwaardeLopendeJaar],2),
													$this->formatGetal($fonds[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($fonds[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
													"",
													$this->formatGetal($fonds[actueleFonds],2),
													$this->formatGetal($fonds[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($fonds[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt,
                          $dividendTxt,
													$fondsResultaattxt,
													$valutaResultaattxt,
													$procentResultaattxt	)
													);
        if($fonds['duurzaamheid'] > 0)
        {
          $legenda=true;
				  for($n=$fonds['duurzaamheid']; $n > 0; $n--)
				  {
			  	  $this->pdf->MemImage($this->takje, (18 - $n*3), $this->pdf->getY()-4, 2.5);
				  }
        }


				$valutaWaarden[$categorien[valuta]] = $fonds[actueleValuta];

				$subtotaal[percentageVanTotaal] +=$percentageVanTotaal;
				$subtotaal[fondsResultaat] +=$fondsResultaat;
				$subtotaal[valutaResultaat] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
        $subtotaal['dividend'] +=$dividend['totaal'];
        $subtotaal['dividendCorrected'] +=$dividend['corrected'];


			// totaal op categorie tellen
			$totaalbegin   += $fonds['beginPortefeuilleWaardeEuro'];
			$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];

      $totaaldividend += $subtotaal['dividend'];
      $totaaldividendCorrected += $subtotaal['dividendCorrected'];
			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = $fonds['beleggingssectorOmschrijving'];

      $grandtotaaldividend  += $subtotaal['dividend'];
      $grandtotaaldividendCorrected  += $subtotaal['dividendCorrected'];
			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds  += $subtotaal[fondsResultaat];

			$totaalResultaat +=	$subtotaal['totaalResultaat'] ;
			$totaalBijdrage  += $subtotaal['totaalBijdrage'] ;
			$grandtotaalResultaat  +=	$subtotaal['totaalResultaat'] ;
			$grandtotaalBijdrage   += $subtotaal['totaalBijdrage'] ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin + $totaaldividendCorrected) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		// totaal voor de laatste categorie
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), 
      $totaalbegin, 
      $totaalactueel,
      $totaalpercentage,
      $totaalfondsresultaat,
      $totaalvalutaresultaat,
      $procentResultaat,false,$totaaldividend);

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

		if($DB->records() > 0)
		{

			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					if($this->pdf->rapport_VOLK_geenvaluta == 1) {
					}
					else
						$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien[valuta],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
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
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata[rentedatum]));
							if($subdata[renteperiode] <> 12 && $subdata[renteperiode] <> 0)
								$rentePeriodetxt .= " / ".$subdata[renteperiode];
						}

						$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
						$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";


						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						//$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						//$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving].$rentePeriodetxt);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
						{
								$this->pdf->row(array("","","","","","","","",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
														$percentageVanTotaaltxt));

						}
						else
						{
              if($this->pdf->rapport_layout == 24)
							  $this->pdf->row(array("","","",$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),$percentageVanTotaaltxt));
              else
						  	$this->pdf->row(array("","","","",$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
														          $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),"","","","",$percentageVanTotaaltxt));
						}

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
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal".
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
			$totaalLiquiditeitenInValuta = 0;
			//$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");

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

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];
				$subtotaalPercentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);


				$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";



				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
			//	$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			//	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);
			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

	

						$this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt));




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
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true,$grandtotaaldividend);
		$this->pdf->ln();
   
    if($legenda==true)
    {
    if($this->pdf->getY() > 150)
      $this->pdf->addPage();
		$legenda=array(5=>"10%",4=>"10% tot 20%",3=>"20% tot 30%",2=>"30% tot 40%",1=>"40% tot 50%");
		$this->pdf->SetDrawColor(116,140,28);
		$this->pdf->Rect(12, $this->pdf->getY(), 105, 30, 'D');
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->SetWidths(array(25,150));
		$this->pdf->row(array("","Toelichting duurzaamheidsscore"));
		$this->pdf->ln();
		foreach ($legenda as $takjes=>$tekst)
		{
		  if($takjes > 0)
      {
        $this->pdf->row(array("","Behoren tot de ".$tekst." beste bedrijven in de sector."));
			  for($n=$takjes; $n > 0; $n--)
				{
			    $this->pdf->MemImage($this->takje, (18+13 - $n*3), $this->pdf->getY()-4, 2.5);
				}
      }
		}
    }







	}
}
?>
