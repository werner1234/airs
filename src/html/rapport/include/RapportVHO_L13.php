<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/02/20 16:50:39 $
 		File Versie					: $Revision: 1.25 $

 		$Log: RapportVHO_L13.php,v $
 		Revision 1.25  2019/02/20 16:50:39  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2019/02/13 14:50:15  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2015/12/19 09:11:09  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2013/05/04 15:59:49  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2012/12/05 16:45:29  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2012/05/20 06:44:07  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2010/04/07 14:21:12  rvv
 		*** empty log message ***

 		Revision 1.17  2010/04/07 14:02:19  rvv
 		*** empty log message ***

 		Revision 1.16  2010/04/07 13:13:47  rvv
 		*** empty log message ***

 		Revision 1.15  2010/03/24 16:24:44  rvv
 		*** empty log message ***

 		Revision 1.14  2009/10/21 16:13:19  rvv
 		*** empty log message ***

 		Revision 1.13  2009/10/14 13:18:39  rvv
 		*** empty log message ***

 		Revision 1.12  2009/09/23 14:52:07  rvv
 		*** empty log message ***

 		Revision 1.11  2009/09/23 13:30:28  rvv
 		*** empty log message ***

 		Revision 1.10  2009/09/23 13:09:59  rvv
 		*** empty log message ***

 		Revision 1.9  2009/09/23 11:14:26  rvv
 		*** empty log message ***

 		Revision 1.8  2009/09/23 10:39:24  rvv
 		*** empty log message ***

 		Revision 1.7  2009/09/23 09:36:50  rvv
 		*** empty log message ***

 		Revision 1.6  2009/07/12 09:32:42  rvv
 		*** empty log message ***

 		Revision 1.5  2009/07/06 07:15:25  cvs
 		*** empty log message ***

 		Revision 1.4  2009/05/06 14:39:13  rvv
 		*** empty log message ***

 		Revision 1.3  2009/04/29 15:51:52  rvv
 		*** empty log message ***

 		Revision 1.2  2009/01/20 17:44:09  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/10 15:26:50  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L13
{
	function RapportVHO_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_VHO_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_VHO_titel;
		else
			$this->pdf->rapport_titel = "Vergelijkend historisch overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';

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
	     return $this->formatGetal($waarde,$newDec);
	   }
	  else
	   return $this->formatGetal($waarde,$dec);
	  }
	  else
	   return $this->formatGetal($waarde,$dec);
	}

	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		//$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10]+ $this->pdf->widthB[11];
		//$totaal5 = $totaal4 + $this->pdf->widthB[12];

		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] + $this->pdf->widthB[12];



		  $extra = $this->pdf->rapport_VHO_lijnenKorter;


		$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		if(!empty($totaalA))
		{
			$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
		}

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);

		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
		}

		if(!empty($totaalD2))
		{
			$totaalD2txt = $this->formatGetal($totaalD2,$this->pdf->rapport_VHO_decimaal_proc)."%";

		}

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}



		$this->pdf->SetX(0);
		$this->pdf->Cell($begin,4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[12],4,$totaalD2txt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF=0, $grandtotaal=false)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel
		if($this->pdf->rapport_layout != 8)
			$this->pdf->ln();

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] + $this->pdf->widthB[12];

		  $extra = $this->pdf->rapport_VHO_lijnenKorter;

		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
			if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
			{
				$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			}
			else
			{
				$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			}
		}
		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);

			if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
				$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			else
				$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		}


		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
		}

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);


			$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[12],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,1, "R");


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



		if($grandtotaal)
		{

				if(!empty($totaalB))
				{
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
				}
				if(!empty($totaalA))
				{
					$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
					$this->pdf->Line($begin+$extra,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
				}
				if(!empty($totaalE))
				{
					$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
					$this->pdf->Line($totaal5+$extra,$this->pdf->GetY()+1,$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY()+1);
				}
				if(!empty($totaalD))
				{
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY()+1,$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY()+1);
				}


		}
		else
		{

				if(!empty($totaalB))
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				if(!empty($totaalA))
					$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
				if(!empty($totaalE))
					$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
				if(!empty($totaalD))
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());

			$this->pdf->setDash();
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
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();


		$this->pdf->widthB = array(10,80,20,28,1,30,5,28,1,30,1,2,1,28,1);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes



		$this->pdf->AddPage();

		$optieData=array();
		$query="SELECT TijdelijkeRapportage.fonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,Fondsen.OptieBovenliggendFonds
            FROM TijdelijkeRapportage
                 Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
            WHERE
                 TijdelijkeRapportage.`type` = 'fondsen' AND
						     TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."' AND
						     TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
    $aanwezigeFondsen=array();
		while($data = $DB->nextRecord())
		{
			if($data['OptieBovenliggendFonds']<>'')
      {
        $optieData[$data['OptieBovenliggendFonds']]['waardeEUR'] += $data['actuelePortefeuilleWaardeEuro'];
        $optieData[$data['OptieBovenliggendFonds']]['waardeValuta'] += $data['actuelePortefeuilleWaardeInValuta'];
        $optieData[$data['OptieBovenliggendFonds']]['fondsen'][] = $data['fonds'];
      }
      else
			{
				$aanwezigeFondsen[$data['fonds']]=$data['fonds'];
			}
		}
    
    $optieZonderFonds=array();
		foreach($optieData as $fondsBijOptie=>$optieWaarden)
		{
			if(!in_array($fondsBijOptie,$aanwezigeFondsen))
			{
				foreach($optieWaarden['fondsen'] as $optie)
         $optieZonderFonds[]=$optie;
			}
		}
	//	listarray($optieZonderFonds);exit;
		

		if($this->pdf->debug == true)
		{
		  //echo "optieData:<br>\n$query<br>\n";
		  //listarray($optieData);
		}

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
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
    
    if(count($optieZonderFonds) > 0)
    {
      $fondsenIn="OR TijdelijkeRapportage.Fonds IN('".implode("','",$optieZonderFonds)."')";
    }
    else
    {
      $fondsenIn='';
    }

		$query = "SELECT Beleggingscategorien.Omschrijving, TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
//		" SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers) AS subtotaalhistorisch, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." AS subtotaalactueel ".
		" FROM TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
		LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
		Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
		AND (OptieBovenliggendFonds =  '' $fondsenIn) " .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		 if($this->pdf->debug == true)
		{
		 // echo "Beleggingscategorien:<br>\n$query<br>\n";
		}

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers

      $subtotaalactueel=0;

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien[Omschrijving] && !empty($lastCategorie) )
			{
				if($this->pdf->rapport_VHO_percentageTotaal == 1)
				{
					$percentageVanTotaal = ($totaalactueel) / ($totaalWaarde/100);
				}
				else
				{
					$percentageVanTotaal = "";
				}

				$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				if($totaalhistorisch < 0)
					$procentResultaat = -1 * $procentResultaat;
				// attica ?
				//$procentResultaat = ($totaalvalutaresultaat / $totaalhistorisch) *100;

				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
				//function $this->printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)

				if($this->pdf->rapport_layout == 14)
				  $actueleWaardePortefeuille += $this->printTotaal($title, $totaalhistorisch, $totaalactueel,$percentageVanTotaal, 0, $totaalGecombeneerdResultaat, 0);
				else
				  $actueleWaardePortefeuille += $this->printTotaal($title, $totaalhistorisch, $totaalactueel,0, 0 , $totaalGecombeneerdResultaat, 0);

				$totaalhistorisch = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$procentResultaat = 0 ;
				$totaalGecombeneerdResultaat =0;
			}

			if($lastCategorie <> $categorien[Omschrijving])
			{
			  if($this->pdf->rapport_layout == 14)
			  	$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "b");
			  else
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
			}
			// subkop (valuta)
			if($this->pdf->rapport_VHO_geenvaluta == 1)
			{
			}
			else
			{
				$tekst = vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'];
				$this->printKop($tekst, "");
			}
      

   
			// print detail (select from tijdelijkeRapportage)
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers,
			  TijdelijkeRapportage.fondsEenheid, ".
		//	" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage
			  LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			  WHERE (Fondsen.OptieBovenliggendFonds = '' $fondsenIn ) AND ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

		if($this->pdf->debug == true)
		{
		  //echo "Fondsen:<br>\n$subquery<br>\n";
		}

			while($subdata = $DB2->NextRecord())
			{
				if(in_array($subdata['fonds'],$optieZonderFonds))
        {
          $nieuweKoers = array('koers'                 => $subdata['historischeWaarde'], 'waarde' => $subdata['historischeWaardeTotaalValuta'], 'historischeValutaKoers' => $subdata['historischeValutakoers'],
                               'verkregenWaardeValuta' => $subdata['actuelePortefeuilleWaardeEuro'], 'verkregenAantal' => $subdata['totaalAantal']);
        }
				else
        {
          $nieuweKoers = $this->bepaalKostprijs($subdata['fonds']);
        }
//listarray($nieuweKoers);
			  if($this->pdf->debug == true)
		    {
		      echo "Fonds:<br>\n";
		      listarray($subdata);
		      echo "NieuweKoers:<br>\n";
		      listarray($nieuweKoers);
		    }


			  if($optieData[$subdata['fonds']])
			  {
			    if($this->pdf->debug == true)
			      echo $subdata['fonds']." is gecorrigeerd met NieuweKoers.<br>\n";
			    $subdata['actuelePortefeuilleWaardeInValuta'] += $optieData[$subdata['fonds']]['waardeValuta'];
			    $subdata['actuelePortefeuilleWaardeEuro'] += $optieData[$subdata['fonds']]['waardeEUR'];
			    $subdata['actueleFonds']='';
			  }

			  $subtotaalactueel += $subdata['actuelePortefeuilleWaardeEuro'];

			  if(round($nieuweKoers['verkregenAantal'],1) != round($subdata['totaalAantal'],1))
			  {
			    if($this->pdf->debug == true)
			      listarray($this->kostPrijsDebug[$subdata['fonds']]);
			    $subdata['totaalAantal']='';
			    $subdata['historischeWaarde']='';
			    $subdata['historischeWaardeTotaal']='';
			    $subdata['historischeWaardeTotaalValuta']='';
			  }
			  else
			  {
			  $subdata['historischeWaarde']       = $nieuweKoers['koers'];
			  $subdata['historischeWaardeTotaal'] = $nieuweKoers['koers'] * $subdata['totaalAantal'] * $subdata['fondsEenheid'];
			  $subdata['historischeWaardeTotaalValuta'] = $nieuweKoers['waarde'];//$nieuweKoers['koers'] * $subdata['totaalAantal'] * $subdata['fondsEenheid'] * $nieuweKoers['historischeValutaKoers'];
			  }
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['historischeWaardeTotaal']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
			  $gecombeneerdResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'];


				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
				{

				  $this->pdf->row(array("",
												"",
												$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_VHO_aantalVierDecimaal),
												$this->formatGetal($subdata['historischeWaarde'],2),
												'',
												$this->formatGetal($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal),
												"",
												$this->formatGetal($subdata['actueleFonds'],2),
												'',
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
												'',
												'',
												'',
												$this->formatGetal($gecombeneerdResultaat,0),
												'')
												);

				}


				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$categorien['subtotaalhistorisch'] += $subdata['historischeWaardeTotaalValuta'];
				$subtotaal['gecombeneerdResultaat'] += $gecombeneerdResultaat;
			}

			if($this->pdf->rapport_VHO_percentageTotaal == 1)
			{
				$percentageVanTotaal = ($categorien[subtotaalactueel]) / ($totaalWaarde/100);
			}
			else {
				$percentageVanTotaal = "";
			}

			$procentResultaat = (($categorien[subtotaalactueel] - $categorien[subtotaalhistorisch]) / ($categorien[subtotaalhistorisch] /100));
			if($categorien[subtotaalhistorisch] < 0)
				$procentResultaat = -1 * $procentResultaat;

			// attica?
			//$procentResultaat = ($subtotaal[valutaResultaat] / $categorien[subtotaalhistorisch]) *100;

			// print categorie footers
			if($this->pdf->rapport_VHO_geensubtotaal == 1)
			{
			}
			else
			{
			     $this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalhistorisch], $subtotaalactueel,$percentageVanTotaal, 0, $subtotaal['gecombeneerdResultaat'], 0);
			}

			// totaal op categorie tellen
			$totaalhistorisch += $categorien[subtotaalhistorisch];
			$totaalactueel += $subtotaalactueel;
			//echo $categorien[subtotaalactueel]." $subtotaalactueel <br>\n"; ob_flush();

			$totaalfondsresultaat += $subtotaal[fondsResultaat];
			$totaalvalutaresultaat += $subtotaal[valutaResultaat];

		  $totaalGecombeneerdResultaat += $subtotaal['gecombeneerdResultaat'];


			$lastCategorie = $categorien[Omschrijving];
			$subtotaal = array();
		}

		if($this->pdf->rapport_VHO_percentageTotaal == 1)
		{
			$percentageVanTotaal = ($totaalactueel) / ($totaalWaarde/100);
		}
		else {
			$percentageVanTotaal = "";
		}

		// totaal voor de laatste categorie
		$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
		if($totaalhistorisch < 0)
			$procentResultaat = -1 * $procentResultaat;

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalhistorisch, $totaalactueel,0 ,0,$totaalGecombeneerdResultaat, 0);



		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
//		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersStart." subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  ".
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

			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					if($this->pdf->rapport_VHO_geenvaluta == 1) {
					}
					else
						$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien[valuta],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
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

						if($this->pdf->rapport_VHO_percentageTotaal == 1)
							$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
						else
							$percentageTotaalTekst = "";



						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving].$rentePeriodetxt );

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						if($this->pdf->rapport_VHO_volgorde_beginwaarde == 1)
						{
								$this->pdf->row(array("","","","","","","","",
														'',
														$this->formatGetal($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
														$percentageTotaalTekst));
						}

					}

					// print subtotaal
					//$this->printSubTotaal("Subtotaal:", "", $subtotaalRenteInValuta);
					if($this->pdf->rapport_VHO_percentageTotaal ==1)
					{
						$percentageVanTotaal = ($subtotaalRenteInValuta) / ($totaalWaarde/100);
					}
					else
						$percentageVanTotaal = 0;

					if($this->pdf->rapport_VHO_geensubtotaal == 1)
					{
					}
					else
						$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, $percentageVanTotaal, "", "");

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}
			}

			// totaal op rente
			if($this->pdf->rapport_VHO_percentageTotaal ==1)
			{
				$percentageVanTotaal = ($totaalRenteInValuta) / ($totaalWaarde/100);
			}
			else
				$percentageVanTotaal = 0;

			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal Opgelopen rente:",$this->pdf->rapport_taal),"", $totaalRenteInValuta, 0,"");
		}

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro , ".
			" TijdelijkeRapportage.rekening, ".
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

		if($DB1->records() >0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");
			$totaalLiquiditeitenInValuta = 0;

			while($data = $DB1->NextRecord())
			{

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{PortefeuilleVoorzet}",$this->pdf->rapport_portefeuilleVoorzet,$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = vertaalTekst(str_replace("{Tenaamstelling}",$data[Tenaamstelling],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

				if($this->pdf->rapport_VHO_percentageTotaal ==1)
				{
					$percentageVanTotaal  = ($data[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
					$percentageVanTotaalTekst = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VHO_decimaal_proc)."%";
				}
				else
					$percentageVanTotaalTekst = "";

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

		  $this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												'',
												$this->formatGetal($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_VHO_decimaal),
												$percentageVanTotaalTekst));


			}
		}


		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
		}
		else
			$percentageVanTotaal = 0;

		// totaal liquiditeiten
		$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$percentageVanTotaal,"","");


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = 100;
		}
		else
			$percentageVanTotaal = 0;


		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille, $percentageVanTotaal,"","","",true);


		$this->pdf->ln();

		if($this->pdf->rapport_VHO_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VHO_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_VHO_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata[AEXVergelijking] > 0 && $this->pdf->rapport_VHO_indexUit == 0)
		{
		  if(!$this->pdf->rapport_VHO_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

		if($this->pdf->debug == true)
		{
		  exit;
		}

	}
  
  function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

	function bepaalKostprijs($fonds)
	{
		global $__appvar;
		$einddatum = $this->rapportageDatum;
	  $begindatum = '2009-01-01';
    $DB2 = new DB();

		$fondsenSelectie = array();
		$fondsenSelectie[] = $fonds;

	  $query = "SELECT Fondsen.Fonds FROM Fondsen WHERE OptieBovenliggendFonds = '$fonds'";
	  $DB2->SQL($query);
	  $DB2->Query();
 	  while($optie = $DB2->NextRecord())
	  {
	    $fondsenSelectie[]=$optie['Fonds'];
 		}

		$fondsenSelectie = implode('\',\'',$fondsenSelectie);
	  $fondsenQuery .= " Rekeningmutaties.Fonds IN('$fondsenSelectie')  AND ";

    $query = "SELECT
	              Rekeningmutaties.transactietype,
	              Rekeningmutaties.Afschriftnummer,
	              Rekeningmutaties.Boekdatum,
	              Rekeningmutaties.Aantal,
	              Rekeningmutaties.Valuta,
	              Rekeningmutaties.Fondskoers,
	              Rekeningmutaties.Rekening,
	              Rekeningmutaties.Omschrijving,
	              Rekeningmutaties.Bedrag,
                Rekeningmutaties.Valutakoers,
                Fondsen.OptieExpDatum,
                Fondsen.OptieBovenliggendFonds,
	              Fondsen.Renteperiode,
	              Fondsen.EersteRentedatum,
	              Fondsen.Rentedatum,
	              Fondsen.Fondseenheid,
	              Fondsen.Valuta,
	              Fondsen.Fonds,
	              Fondsen.Omschrijving AS FondsOmschrijving
	              FROM Rekeningmutaties,
	              Rekeningen, Fondsen, Portefeuilles
                WHERE
                Portefeuilles.Portefeuille = '".$this->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              Fondsen.Fonds = Rekeningmutaties.Fonds AND
	              $fondsenQuery
	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	              Rekeningmutaties.Boekdatum >='".$begindatum."' AND
	              Rekeningmutaties.Boekdatum <='".$einddatum."'
	              ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Omschrijving, Rekeningmutaties.id ";

	  $DB2->SQL($query);
	  $DB2->Query();

	  $line = 0;
    $skipJaarafsluiting = false;
	  $eersteFondsBoeking=true;
		$eersteOptieBoeking=true;
	  $verkregenAantal = 0;
	  $verkregenWaarde = 0;
		$fondsKostenTotaal = 0;
	  $DB3 = new DB();
	  while($mutaties = $DB2->NextRecord())
		{
	    if($mutaties['OptieBovenliggendFonds'] == '' || $mutaties['Fonds'] == $fonds )
        $isfonds=true;
      else
        $isfonds=false;

		  if($mutaties['transactietype'] == 'B')
		  {
		    if($mutaties['Boekdatum']=='2009-01-01 00:00:00')
          $mutaties['Fondskoers']=$this->getFondsKoers($mutaties['Fonds'],'2009-01-01');

		   	if( ($isfonds && $eersteFondsBoeking) || ( !$isfonds && $eersteOptieBoeking))
		      $skipJaarafsluiting = false;
		    else
		      $skipJaarafsluiting = true;
		  }
		  else
		    $skipJaarafsluiting = false;

	    if($isfonds)
        $eersteFondsBoeking = false;
	    else
        $eersteOptieBoeking = false;


		  if ($skipJaarafsluiting == false)
		  {
/*
		    if($mutaties['Valuta'] <> 'EUR')
		    {
		      $query = "SELECT Valutakoersen.Koers FROM Valutakoersen WHERE ".
									 " Valutakoersen.Valuta = '".$mutaties['Valuta']."' AND ".
				  	       " Valutakoersen.Datum <= '".$mutaties['Boekdatum']."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";
				 	$DB3->SQL($query);
		      $valutaKoersData = $DB3->lookupRecord();
		      $valutaKoers = $valutaKoersData['Koers'];
		    }
		    else
*/        
        $valutaKoers = $mutaties['Valutakoers'];

//$koersWaarde
		    $fondskoers  = $mutaties['Fondskoers'];
		    if ($mutaties['Fondseenheid'] == '')
		      $mutaties['Fondseenheid'] = 1;

		    $fondsWaardeValuta = $mutaties['Aantal'] * $mutaties['Fondseenheid'] * $mutaties['Fondskoers'];
        $fondsWaarde = $fondsWaardeValuta * $valutaKoers; //in EUR

        $metKosten = true;
        if ($metKosten)
        {//boekdatum toevoegen
          $kostenTransactie=0;
		      $kostenTransactieValuta=0;
		  	  $query = "SELECT round(Debet,2) as Debet, Valuta,Valutakoers FROM Rekeningmutaties
		                WHERE
		                Rekeningmutaties.Afschriftnummer = '".$mutaties['Afschriftnummer']."' AND
		                Rekeningmutaties.Rekening = '".$mutaties['Rekening']."' AND
		                Rekeningmutaties.Boekdatum = '".$mutaties['Boekdatum']."' AND
		                Rekeningmutaties.Omschrijving = '".$mutaties['Omschrijving']."' AND
		                Rekeningmutaties.GrootboekRekening IN ('KOST','KOBU') ";
		      $DB3->SQL($query);
		      $DB3->Query();

		      while($kosten = $DB3->NextRecord())
		      {
		        $kostenValuta = $kosten['Valuta'];
            $valutaKoersKosten=$kosten['Valutakoers'];
            /*
		        if($kostenValuta == "EUR")
		          $valutaKoersKosten = 1;
		        elseif($kostenValuta == $mutaties['Valuta'])
		          $valutaKoersKosten=$valutaKoers;
		        else
		        {
		          $query = "SELECT Valutakoersen.Koers FROM Valutakoersen WHERE ".
							    		 " Valutakoersen.Valuta = '".$mutaties['Valuta']."' AND ".
				              " Valutakoersen.Datum <= '".$mutaties['Boekdatum']."' ORDER BY Valutakoersen.Datum DESC LIMIT 1 ";
					    $DB4->SQL($query);
		          $valutaKoersData = $DB4->lookupRecord();
		          $valutaKoersKosten = $valutaKoersData['Koers'];
		        }
            */
		          $kostenTransactie = $kosten['Debet']*$valutaKoersKosten;
		          $fondsKostenTotaal += $kostenTransactie;
		          $kostenTransactieValuta = $kosten['Debet'];
		          $fondsKostenTotaalValuta += $kostenTransactieValuta;
		      }
		    }
		    else
		    {
		        $fondsKostenTotaal = 0;
		        $fondsKostenTotaalValuta =0;
		        $kostenTransactie=0;
		        $kostenTransactieValuta=0;
		    }

		    if($mutaties['OptieBovenliggendFonds']=='')
		    { //Fonds
		      $verkregenAantal += $mutaties['Aantal'];
		      if($this->pdf->debug == true)
		        $this->kostPrijsDebug[$fonds][$mutaties['Boekdatum']][]=$mutaties['Aantal'];
		      $verkregenWaarde += ($fondsWaarde + $kostenTransactie);
		      $verkregenWaardeValuta += ($fondsWaardeValuta + $kostenTransactieValuta);
		      $verkregenKoers = ($verkregenWaarde / ($verkregenAantal * $mutaties['Fondseenheid'])) / $valutaKoers ; // in valuta
		    }
		    else
		    { //Optie
	        $verkregenWaarde += ($fondsWaarde + $kostenTransactie);
	        $verkregenWaardeValuta += ($fondsWaardeValuta + $kostenTransactieValuta);
          $verkregenKoers = ($verkregenWaarde / $verkregenAantal ) / $valutaKoers ;
	      }
		  }
		  $line ++;
		}

		$verkregenValutaKoers=$verkregenWaarde/$verkregenWaardeValuta;
		$tmp=array('koers'=>$verkregenKoers,'waarde'=>$verkregenWaarde,'historischeValutaKoers'=>$verkregenValutaKoers,'verkregenWaardeValuta'=>$verkregenWaardeValuta,'verkregenAantal'=>$verkregenAantal);
    return $tmp;
	}
}
?>
