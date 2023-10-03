<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2009/07/12 09:32:42 $
File Versie					: $Revision: 1.3 $

$Log: RapportVOLK_L16.php,v $
Revision 1.3  2009/07/12 09:32:42  rvv
*** empty log message ***

Revision 1.2  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.1  2007/09/26 15:31:29  rvv
*** empty log message ***

Revision 1.39  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.38  2007/07/05 12:28:39  rvv
*** empty log message ***

Revision 1.37  2007/04/20 12:21:16  rvv
*** empty log message ***

Revision 1.36  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.35  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.34  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.33  2006/11/03 11:24:04  rvv
Na user update

Revision 1.32  2006/10/31 12:11:04  rvv
Voor user update

Revision 1.31  2006/10/27 08:35:30  rvv
Toevoeging Layout 12

Revision 1.30  2006/10/20 14:55:53  rvv
*** empty log message ***

Revision 1.29  2006/05/12 08:23:21  jwellner
*** empty log message ***

Revision 1.28  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.27  2006/04/12 07:54:47  jwellner
*** empty log message ***

Revision 1.26  2006/03/01 07:56:20  jwellner
*** empty log message ***

Revision 1.25  2006/02/03 08:00:27  jwellner
diverse bugfixen

Revision 1.24  2005/12/28 19:28:38  jwellner
diverse aanpassigen jaarafsluiting

Revision 1.23  2005/12/08 13:55:21  jwellner
Modelcontrole rapport

Revision 1.22  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.21  2005/11/25 09:30:08  jwellner
- verdiept overzicht
- layout

Revision 1.20  2005/11/18 15:15:01  jwellner
no message

Revision 1.19  2005/11/11 10:15:31  jwellner
fout in OIV

Revision 1.18  2005/11/07 10:29:17  jwellner
no message

Revision 1.17  2005/11/01 11:20:08  jwellner
diverse aanpassingen

Revision 1.16  2005/10/14 16:17:56  jwellner
no message

Revision 1.15  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.14  2005/09/30 14:05:13  jwellner
- rapport OIH
- rapport MUT2
- Layout 5
- selectieschermen

Revision 1.13  2005/09/30 09:45:45  jwellner
rapporten aangepast.

Revision 1.12  2005/09/29 15:00:18  jwellner
no message

Revision 1.11  2005/09/23 10:12:06  jwellner
auto update

Revision 1.10  2005/09/21 09:41:14  jwellner
no message

Revision 1.9  2005/09/21 07:52:04  jwellner
no message

Revision 1.8  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.7  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.6  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.5  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.4  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.3  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.2  2005/07/29 08:11:57  jwellner
no message

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.2  2005/07/12 07:09:50  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd
 
*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L16
{
	function RapportVOLK_L16($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;
	    return number_format($this->pdf->ValutaKoersStart,2,",",".") ." - ".number_format($waarde,$dec,",",".");
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
		
		if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2)
		{
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
		else if ($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
		{
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
		else 
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(5,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalA <>0)
				$this->printCol(9,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %","subtotaal");
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),"subtotaal");
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");
			if($totaalG <>0)
				$this->printCol(15,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");		
			if($totaalH <>0)
			  $this->printCol(16,$this->formatGetal($totaalH,$this->pdf->rapport_VOLK_decimaal_proc),"subtotaal");				

				
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}
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
	
		// lege regel
		if($this->pdf->rapport_layout != 8)
			$this->pdf->ln();
			
		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";
			
		if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2)
		{
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
		}
		else if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
		{
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
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}
		elseif($this->pdf->rapport_layout == 8)
		{ 
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(11,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(6,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(8,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(12,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(14,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(15,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			if($totaalG <>0)
				$this->printCol(16,$this->formatGetal($totaalG,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);		
			if($totaalH <>0)
			  $this->printCol(17,$this->formatGetal($totaalH,2),$grandtotaal);		
				
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}		
		else 
		{
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(1,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(5,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(9,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(10,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(11,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalF <>0)
				$this->printCol(14,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc),$grandtotaal);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->ln();
		}
	
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
		
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		
		if($this->pdf->rapport_layout == 8 || $this->pdf->rapport_layout == 5)				
		{
			$fondsresultwidth = 15;
			$omschrijvingExtra = 0;
		}
		else 
		{
			$fondsresultwidth = 5;
			$omschrijvingExtra = 10;
		}
		
		// voor data
		if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2)
		{
			
			                                                             //   
			$this->pdf->widthB = array(18,50+$omschrijvingExtra,18,21,21,20,20,20,14,21,$fondsresultwidth,21,21,15);
			$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
			$this->pdf->widthA = array(66+$omschrijvingExtra,18,15,21,21,15,21,21,15,22,22,$fondsresultwidth,22,15);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		}
		else 
		{
      if($this->pdf->rapport_layout == 8) //rvv
			{
			$this->pdf->widthB = array(2,40+$omschrijvingExtra,18,8,15,21,21,1,15,21,21,17,15,11,15,11,14,14);
			$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		
		// voor kopjes
			$this->pdf->widthA = array(60+$omschrijvingExtra,18,8,15,21,21,1,15,21,21,15,15,$fondsresultwidth,15,15);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
			}
			else 
			{
			$this->pdf->widthB = array(10,50+$omschrijvingExtra,18,15,21,21,1,15,21,21,15,22,$fondsresultwidth,22,15);
			$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		
		// voor kopjes
			$this->pdf->widthA = array(60+$omschrijvingExtra,18,15,21,21,1,15,21,21,15,22,$fondsresultwidth,22,15);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
			}
		}
		
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
		
		if($this->pdf->rapport_VOLK_geenvaluta == 1)
		{
			$query = "SELECT ".
			" Beleggingscategorien.Omschrijving, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersStart. " AS subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
			" ORDER BY Beleggingscategorien.Afdrukvolgorde asc";			
		}
		else 
		{
			$query = "SELECT ".
			" Beleggingscategorien.Omschrijving, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersStart. " AS subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS subtotaalactueel FROM ".
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		} 
		debugSpecial($query,__FILE__,__LINE__);	
		$DB = new DB();
		$DB->SQL($query); 
		$DB->Query();
		
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

				if($this->pdf->rapport_layout == 1)
					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, "" , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);
				else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, "" , $totaalfondsresultaat, $totaalvalutaresultaat, $procentResultaat);
				else if($this->pdf->rapport_layout == 8 )
				{
				  $actueleWaardePortefeuille += $totaalactueel;
					 $this->printTotaal($title,$totaalactueel , $totaalbegin, $totaalpercentage , $totaalfondsresultaat, $totaalvalutaresultaat, 0,false,0,$totaalBijdrage);
				}
				else
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
			
			if($lastCategorie <> $categorien[Omschrijving])
			{
				if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2 )
				{
					$this->pdf->SetWidths($this->pdf->widthB);
					$this->pdf->SetAligns($this->pdf->alignB);
					$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
					$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
					$this->pdf->row(array("",vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal)));
				}
				else 
				{
					$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal), "bi");
				}
			}
			// subkop (valuta)
			if($this->pdf->rapport_VOLK_geenvaluta == 1)
			{
				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart. " as beginPortefeuilleWaardeEuro, 
				TijdelijkeRapportage.actueleFonds, 
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie, 
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			}
			else 
			{
				$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien[valuta], "");
				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersStart. " as beginPortefeuilleWaardeEuro,
				 TijdelijkeRapportage.actueleFonds, 
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, 
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro , 
				 TijdelijkeRapportage.beleggingscategorie, 
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
				" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			}
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);	
			$DB2 = new DB();
			$DB2->SQL($subquery); 
			$DB2->Query();
//echo $subquery."<br><br>";exit;
			while($subdata = $DB2->NextRecord())
			{
			  $opgelopenRente=array();

			    $subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.fonds = '".$subdata['fonds']."' AND ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$subdata[valuta]."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			     
					 $DB3 = new DB();
		    	 $DB3->SQL($subquery); 
			     $DB3->Query();
			     $opgelopenRente=$DB3->NextRecord();

			   

			  
			  
				$fondsResultaat = ($subdata[actuelePortefeuilleWaardeInValuta] - $subdata[beginPortefeuilleWaardeInValuta]) * $subdata[actueleValuta] / $this->pdf->ValutaKoersEind;
				
				$fondsResultaatprocent = ($fondsResultaat / $subdata[beginPortefeuilleWaardeEuro]) * 100;
				$valutaResultaat = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro] - $fondsResultaat;
				
				$procentResultaat = (($subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro]) / ($subdata[beginPortefeuilleWaardeEuro] /100));
				if($subdata[beginPortefeuilleWaardeEuro] < 0)
					$procentResultaat = -1 * $procentResultaat;
				
				$percentageVanTotaal = ($subdata[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);

				if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)				
				{
					$percentageVanTotaaltxt = "";
				}
				else 
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
				
				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc);

				if($this->pdf->rapport_layout == 8 || $this->pdf->rapport_layout == 5)				
				{
					if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
					else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);
				}

				if($this->pdf->rapport_layout == 8)
				{
				  		$subTotaalBijdrage = $procentResultaat * $percentageVanTotaal / 100;
				  		
				  		$valutaResultaatprocent = ($valutaResultaat / $subdata[beginPortefeuilleWaardeEuro]) * 100;
				  		
				  		if($valutaResultaatprocent > 1000 || $valutaResultaatprocent < -1000)
				  		{
					     $valutaResultaatprocentTxt = "p.m.";
				  		}
				  		else 
                $valutaResultaatprocentTxt = $this->formatGetal($valutaResultaatprocent,$this->pdf->rapport_VOLK_decimaal_proc);
				  		
					     
				  		if($subTotaalBijdrage > 1000 || $subTotaalBijdrage < -1000)
				  		{
					     $totaalBijdragetTxt = "p.m.";
					     $subTotaalBijdrage =0;
				  		}
					    else 
					     $totaalBijdrageTxt = $this->formatGetal($subTotaalBijdrage,2);					     
				}
				
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
				
				$this->pdf->setX($this->pdf->marge);
				
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				
				//$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving],null,null,null,null,"http://url?code=");
				if($this->pdf->rapport_VOLK_link == 1)
				{
					// getStroevecode.
					$DBx = new DB();
					$DBx->SQL("SELECT stroeveCode FROM Fondsen Where Fonds = '".$subdata[fonds]."'");
					$DBx->Query();
					$fdata = $DBx->nextRecord();
					
					$url = str_replace("[stroevecode]",$fdata[stroeveCode],$this->pdf->rapport_VOLK_url);
					$url = str_replace("[fonds]",$subdata[fonds],$url);
					
					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving],null,null,null,null,$url);	
				}
				else 
				{
					$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving],null,null,null,null,null);
				}
				
				$this->pdf->setX($this->pdf->marge);
				
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);										
				
				if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2)
				{
						if(round($opgelopenRente[actuelePortefeuilleWaardeEuro],1) != 0.0)			
							$renteTxt = $this->formatGetal($opgelopenRente[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal);
						else 
						  $renteTxt = '';	
					$this->pdf->row(array($this->formatGetal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												"",
												$this->formatGetal($subdata[actueleFonds],2),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc),
									$renteTxt,
												$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
												$this->formatGetal($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
												$fondsResultaattxt,
												$fondsResultaatprocenttxt,
												$valutaResultaattxt,
												$procentResultaattxt	)	);
				}
				else if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
				{
					// bij layout 1  (HAR) Geen % van totaal
					if($this->pdf->rapport_layout == 1)
					{
						
						$this->pdf->row(array("",
													"", 
													$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
													$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
													"",
													$this->formatGetal($subdata[actueleFonds],2),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
													"",
													$fondsResultaattxt,
													$fondsResultaatprocenttxt,
													$valutaResultaattxt,
													$procentResultaattxt	)
													);
					}
					else 
					{//echo $subdata[actuelePortefeuilleWaardeInValuta]. " " .$subdata[actuelePortefeuilleWaardeEuro] . "<br>";
						$this->pdf->row(array("",
													"",
													$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
													$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
													"",
													$this->formatGetal($subdata[actueleFonds],2),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
													$percentageVanTotaaltxt,
													$fondsResultaattxt,
													$fondsResultaatprocenttxt,
													$valutaResultaattxt,
													$procentResultaattxt	)
													);
					}
				}
				else 
				{
				  if($this->pdf->rapport_layout == 8) //rvv
					{
					$this->pdf->row(array("",
												"",
												$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$subdata['Valuta'],
												$this->formatGetal($subdata[actueleFonds],2),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
												$percentageVanTotaaltxt,
												$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
												$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$fondsResultaattxt,
												$fondsResultaatprocenttxt,
												$valutaResultaattxt,
												$valutaResultaatprocentTxt,
												$procentResultaattxt,
												$totaalBijdrageTxt)
												);
					}
				  else 
				  {
					$this->pdf->row(array("",
												"",
												$this->formatAantal($subdata[totaalAantal],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
												$this->formatGetal($subdata[actueleFonds],2),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
												$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
												$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$percentageVanTotaaltxt,
												$fondsResultaattxt,
												$fondsResultaatprocenttxt,
												$valutaResultaattxt,
												$procentResultaattxt	)
												);
				  }
				}
												
				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];
				
				$subtotaal[percentageVanTotaal] +=$percentageVanTotaal;
				$subtotaal[fondsResultaat] +=$fondsResultaat;
				$subtotaal[valutaResultaat] +=$valutaResultaat;
				$subtotaal['totaalResultaat'] +=$subTotaalResultaat;
				$subtotaal['totaalBijdrage'] += $subTotaalBijdrage;
			
			}
		
			// print categorie footers
			if($this->pdf->rapport_VOLK_geensubtotaal == 1)
			{
			}
			else  
			{
				$procentResultaat = (($categorien[subtotaalactueel]  - $categorien[subtotaalbegin] ) / ($categorien[subtotaalbegin]  /100));
				if($categorien[subtotaalbegin] < 0)
					$procentResultaat = -1 * $procentResultaat;
				
				
				if($this->pdf->rapport_layout == 1)
					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalbegin], $categorien[subtotaalactueel],"", $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat);
				else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalbegin], $categorien[subtotaalactueel],"", $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat);
				else
					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalbegin],$categorien[subtotaalactueel],$subtotaal[percentageVanTotaal], $subtotaal[fondsResultaat], $subtotaal[valutaResultaat], $procentResultaat);
			}
		
			// totaal op categorie tellen
			$totaalbegin   += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];
			
			$totaalfondsresultaat  += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];
			$totaalpercentage      += $subtotaal[percentageVanTotaal];
			
			$lastCategorie = $categorien[Omschrijving];
			
			$grandtotaalvaluta += $subtotaal[valutaResultaat];
			$grandtotaalfonds  += $subtotaal[fondsResultaat];
			
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
		if($this->pdf->rapport_layout == 1)
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,"",$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);
		else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,"",$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);
		else if($this->pdf->rapport_layout == 8 )
		{
			$actueleWaardePortefeuille +=	$totaalactueel;
			$this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal),$totaalactueel , $totaalbegin, $totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,0,false,0,$totaalBijdrage);
		}
		else
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersStart. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
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
			if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2 )
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
				$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)));
			}
			else 
			{
				$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
			}
			
			//$this->pdf->row(array("",vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_taal));
			//$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");
			
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
						
						if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
						{
							$percentageVanTotaaltxt = "";
						}
						else 
						{
							$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
						}
						
						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];
						
						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);
						
						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);			
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);
						
						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving].$rentePeriodetxt);
						
						$this->pdf->setX($this->pdf->marge);
							
						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);										
	
						if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
						{
							if($this->pdf->rapport_layout == 1)
							{
								$this->pdf->row(array("","","","","","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
														""));
							}
							else 
							{
								$this->pdf->row(array("","","","","","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
														$percentageVanTotaaltxt));
							}
						}
						else 
						{
													
							$this->pdf->row(array("","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
														"","","", "",	
														$percentageVanTotaaltxt));
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
						if($this->pdf->rapport_layout == 1)
							$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, "", "", "");
						else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
							$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, "", "", "");
						else
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
			if($this->pdf->rapport_layout == 1)
				$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,"","","");
			else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
				$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,"","","");
			else
				$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}
				
		// Liquiditeiten
		
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
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
			if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 2 )
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->SetFont($this->pdf->rapport_font,'bi',$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
				$this->pdf->row(array("",vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal)));
			}
			else 
			{
				$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal), "bi");
			}
			
			while($data = $DB1->NextRecord())
			{
				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);
				
				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];
				$subtotaalPercentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
				
				
				if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
				{
					$subtotaalPercentageVanTotaaltxt = "";
				}
				else if($this->pdf->rapport_layout == 7 )
				{
					$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc);
				}
				else 
				{
					$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
				}
				
				
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				
				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);			
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
				$this->pdf->setX($this->pdf->marge);
				
				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);
				
				$this->pdf->setX($this->pdf->marge);
					
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);		
					
				if($this->pdf->rapport_VOLK_volgorde_beginwaarde == 1)
				{
					if($this->pdf->rapport_layout == 1)
					{
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
												""));
					}
					else 
					{
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
					
				}
				else 
				{
					if($this->pdf->rapport_layout == 7 ||$this->pdf->rapport_layout == 14 ||$this->pdf->rapport_layout == 16)
					{
						$this->pdf->row(array("",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												$subtotaalPercentageVanTotaaltxt,
												"",
												"",
												"",
												"",
												""));
					}
					elseif($this->pdf->rapport_layout == 8) //rvv
					{
					   $this->pdf->row(array("","","","","",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
												$subtotaalPercentageVanTotaaltxt)
												);
					}
					else 
					{
						$this->pdf->row(array("",
												"",
												"",
												"",
												$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_VOLK_decimaal),
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VOLK_decimaal),
												"",
												"",
												"",
												"",
												$subtotaalPercentageVanTotaaltxt));
					}
		
				}
		
			}
			
			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
			
			// totaal liquiditeiten
			if($this->pdf->rapport_layout == 1)
				$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,"","","");
			else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12)
				$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,"","","");
			else if($this->pdf->rapport_layout == 8)	
			{
			  $actueleWaardePortefeuille += $totaalLiquiditeitenEuro;
			  $this->printTotaal("", $totaalLiquiditeitenEuro, '',$subtotaalPercentageVanTotaal,"","");	
			}		
			else
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
		
		// print grandtotaal
		if($this->pdf->rapport_layout == 1) // layout 1 / HAR heeft geen totaal fonds en valuta
		{
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"","","","",true);
		}
		else if($this->pdf->rapport_layout == 2 || $this->pdf->rapport_layout == 12) // layout 2 / BVS heeft geen % op totaal
		{
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"",$grandtotaalfonds,$grandtotaalvaluta,"",true);
		}
		else if($this->pdf->rapport_layout == 7) // layout 7 heeft extra totaal tekst
		{
			if(($this->pdf->GetY() + 4) > $this->pdf->pagebreak)
			{
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$y = $this->pdf->getY();
			$this->pdf->setY(($y+4));
			$this->printCol(7,vertaalTekst("Totaal ongerealiseerd resultaat",$this->pdf->rapport_taal),"tekst");		
			$this->pdf->setY($y);
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,"",$grandtotaalfonds,$grandtotaalvaluta,"",true);
		}
		elseif($this->pdf->rapport_layout == 8)
		{
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), $actueleWaardePortefeuille, '',100,$grandtotaalfonds,$grandtotaalvaluta,"",true,0,$grandtotaalBijdrage);
		}
		else
		{
			$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true);
		}

		$this->pdf->ln();
		
		if($this->pdf->rapport_VOLK_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VOLK_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_layout == 8)
		  $this->pdf->printSamenstellingResultaat($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf) ;
		
		if($this->pdf->rapport_VOLK_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
		
		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0) 
		{
			$this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}
	}
}
?>