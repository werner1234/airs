<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/02/10 10:05:24 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIS.php,v $
Revision 1.4  2013/02/10 10:05:24  rvv
*** empty log message ***

Revision 1.3  2011/06/29 16:54:47  rvv
*** empty log message ***

Revision 1.2  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.1  2010/11/14 10:46:23  rvv
*** empty log message ***

Revision 1.28  2010/01/20 17:06:20  rvv
*** empty log message ***

Revision 1.27  2009/06/21 09:41:30  rvv
*** empty log message ***

Revision 1.26  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.25  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.24  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.23  2007/03/07 15:55:01  rvv
*** empty log message ***

Revision 1.22  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.21  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.20  2006/11/03 11:24:04  rvv
Na user update

Revision 1.19  2006/10/31 12:06:45  rvv
Voor user update

Revision 1.18  2006/10/27 08:35:50  rvv
Toevoeging layout 12

Revision 1.17  2006/10/20 14:55:53  rvv
*** empty log message ***

Revision 1.16  2006/08/10 08:39:58  cvs
totaal over meerdere pagina's

Revision 1.15  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.14  2006/04/12 07:54:47  jwellner
*** empty log message ***

Revision 1.13  2005/11/17 07:25:02  jwellner
no message

Revision 1.12  2005/11/07 10:29:17  jwellner
no message

Revision 1.11  2005/11/01 11:20:08  jwellner
diverse aanpassingen

Revision 1.10  2005/10/26 11:47:39  jwellner
no message

Revision 1.9  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.8  2005/09/29 15:00:18  jwellner
no message

Revision 1.7  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.6  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.5  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.4  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.3  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.2  2005/07/28 15:12:37  jwellner
no message

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.3  2005/07/12 07:09:50  jwellner
no message

Revision 1.2  2005/07/08 13:52:01  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportOnderverdelingSectorLayout.php");

class RapportOIS
{
	function RapportOIS($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIBS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIS_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingssector";

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
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;
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

/*
	function printSubTotaal($title, $totaalA, $totaalB)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4]+ $this->pdf->widthB[5]+ $this->pdf->widthB[6];

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[7],$this->pdf->GetY());


		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_OIS_decimaal);

		$this->pdf->SetX($actueel-$this->pdf->widthB[6]);
		$this->pdf->Cell($this->pdf->widthB[6],4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[7],4,$totaalBtxt, 0,1, "R");
		$this->pdf->ln();
	}
*/

	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] ;

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_OIS_decimaal);

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[7],$this->pdf->GetY());

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($actueel,4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		//$this->pdf->Cell($this->pdf->widthB[6],4, "", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[7],4,$totaalBtxt, 0,0, "R");

		$this->pdf->Cell($this->pdf->widthB[8],4,$procent, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[7],$this->pdf->GetY());
			$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[7],$this->pdf->GetY()+1);
		}
		else
		{
			$this->pdf->setDash(1,1);
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[7],$this->pdf->GetY());
			$this->pdf->setDash();
		}

		//$this->pdf->ln();

		return $totaalB;
	}

	function printKop($title, $procent, $type="default")
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

		if($this->pdf->rapport_layout == 12)
		 $afronding=1;
		else
		 $afronding=0;

		$procenttxt = $this->formatGetal($procent,$afronding)." %";

		if($this->pdf->rapport_OIS_onderverdelingAandeel)
		{
			if($procent <> 0)
				$procenttxt = $this->formatGetal($procent,$afronding)." %";
			else
				$procenttxt = "";
		}

		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell($this->pdf->widthB[0],4, $procenttxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[1],4, $title, 0,1, "L");
	}

	function writeRapport()
	{
		global $__appvar;
		// voor data
		$this->pdf->widthB = array(15,60,60,25,25,25,20,25,20);
		$this->pdf->alignB = array('R','L','L','R','R','L','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(15,60,60,25,25,25,20,25,20);
		$this->pdf->alignA = array('R','L','L','R','R','L','R','R','R');

		$this->pdf->AddPage();

		if($this->pdf->rapport_layout == 12)
		 $afronding=1;
		else
		 $afronding=0;


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];


		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.beleggingssectorOmschrijving AS secOmschrijving , ".
		" TijdelijkeRapportage.beleggingssector, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM (TijdelijkeRapportage) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'fondsen' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie  ".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc";
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

			if($lastCategorie2 <> $categorien[Omschrijving] && !empty($lastCategorie2) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal);

				if($this->pdf->rapport_OIS_zorgplichtpercentage)
				{
					$pvtTxt = $this->formatGetal($percentageVanTotaal_totaal,1);
					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel,$pvtTxt);
				}
				else
				{
					$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel);
				}

				$percentageVanTotaal_totaal = 0;
				$totaalbegin = 0;
				$totaalactueel = 0;
			}


			if($lastCategorie2 <> $categorien[Omschrijving])
			{
				// 123 .

				if($this->pdf->rapport_OIS_onderverdelingAandeel)
				{
					if(strtolower($categorien[Omschrijving]) == "aandelen")
						$percentageVanTotaal = 100;
					else
						$percentageVanTotaal = 0;
				}
				else
				{
					$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
				}
				$this->printKop(vertaalTekst($categorien[Omschrijving],$this->pdf->rapport_taal),$percentageVanTotaal, "bi");
				$secTel =0;
			}


			// subkop (valuta)

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.beleggingssector, ".
			" TijdelijkeRapportage.beleggingssectorOmschrijving AS secOmschrijving, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage ".
			" WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

			$lastCategorie = "xx";
			$secTel = 0;
			while($subdata = $DB2->NextRecord())
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				if($lastCategorie <> $subdata['secOmschrijving'])
				{
					// selecteer sum van deze sector... en dan :

					$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS sectortotaal FROM TijdelijkeRapportage ".
							 " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
						   " TijdelijkeRapportage.beleggingscategorie =  '".$subdata['beleggingscategorie']."' AND ".
						   " TijdelijkeRapportage.beleggingssector =  '".$subdata['beleggingssector']."' AND ".
							 " TijdelijkeRapportage.type =  'fondsen' AND ".
							 " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
					debugSpecial($q,__FILE__,__LINE__);
					$DB3 = new DB();
					$DB3->SQL($q);
					$DB3->Query();
					$subtotaal = $DB3->nextRecord();
					$subtotaal = $subtotaal['sectortotaal'];


					if($this->pdf->rapport_OIS_onderverdelingAandeel)
					{
						if(strtolower($categorien['Omschrijving']) == "aandelen")
						{
							$percentageVanTotaal = $subtotaal/ ($categorien[subtotaalactueel]/100);
							$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$afronding)." %";
						}
						else
							$percentageVanTotaaltxt = "";
					}
					else
					{
						$percentageVanTotaal =round($subtotaal/ ($categorien[subtotaalactueel]/100),1);
						//echo $categorien[Omschrijving]. " ".$percentageVanTotaal."<br>\n";
						if ($percentageVanTotaal == 100 && $this->pdf->rapport_layout == 12)
						{
						  //nog geen percentage tonen, pas later bij fondsregels
						  $percentageVanTotaaltxt = '';
						  $fondsPercentageweergeven = true;
						}
						else
						{
						  $percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$afronding)." %";
						  $fondsPercentageweergeven = false;
						}
					}

					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
					$this->pdf->Cell($this->pdf->widthB[0],4, $percentageVanTotaaltxt, 0,0, "R");
					$this->pdf->Cell($this->pdf->widthB[1],4, $subdata[secOmschrijving], 0,0, "L");
					$this->pdf->SetX($this->pdf->marge);
				}

				if($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
				{
					$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
				}
				elseif($this->pdf->rapport_OIS_zorgplichtpercentage)
				{
					// -- ophalen % in zorgplicht categorie ?
					$q = "SELECT Zorgplicht FROM ZorgplichtPerFonds, Portefeuilles ".
							 " WHERE Portefeuilles.portefeuille = '".$this->portefeuille."' AND ".
							 " Portefeuilles.Vermogensbeheerder = ZorgplichtPerFonds.Vermogensbeheerder AND ".
							 " ZorgplichtPerFonds.Fonds = '".$subdata['fonds']."' ";

					$DBz = new DB();
					$DBz->SQL($q);
					$DBz->Query();
					if($DBz->records() > 0)
					{
						$zorgplicht = $DBz->nextRecord();
						// Als dit fonds in de zorgplicht cat. zit reken op totaal %  uit.

						$q = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS zorgtotaal FROM ".
								 " TijdelijkeRapportage, Portefeuilles, ZorgplichtPerFonds ".
								 " WHERE Portefeuilles.portefeuille = '".$this->portefeuille."' AND ".
								 " TijdelijkeRapportage.portefeuille = Portefeuilles.portefeuille  AND ".
								 " Portefeuilles.Vermogensbeheerder = ZorgplichtPerFonds.Vermogensbeheerder AND ".
								 " ZorgplichtPerFonds.Fonds = TijdelijkeRapportage.fonds AND ".
								 " ZorgplichtPerFonds.Zorgplicht = '".$zorgplicht['Zorgplicht']."' AND ".
								 " TijdelijkeRapportage.type =  'fondsen' AND ".
								 " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
						debugSpecial($q,__FILE__,__LINE__);
						$DBz = new DB();
						$DBz->SQL($q);
						$DBz->Query();
						$zptotaal = $DBz->nextRecord();

						$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($zptotaal['zorgtotaal'] / 100);;
						$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
					}
					else
					{
						$percentageVanTotaal = 0;
						$percentageVanTotaaltxt = "";
					}
				}
				else
				{
					$percentageVanTotaaltxt = "";
				}


				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				if ( $this->pdf->rapport_layout == 12 && $fondsPercentageweergeven == true)
				{
				$this->pdf->row(array($this->formatGetal(($subdata['actuelePortefeuilleWaardeEuro']/$categorien['subtotaalactueel'])*100,$afronding).' %',
												"",
												$subdata['fondsOmschrijving'],
												$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_OIS_aantalVierDecimaal),
												$this->formatGetal($subdata['actueleFonds'],2),
												$subdata['valuta'],
												"",
												$this->formatGetalKoers($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_OIS_decimaal),
												$percentageVanTotaaltxt));
				}
				else
				{
				$this->pdf->row(array("",
												"",
												$subdata['fondsOmschrijving'],
												$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_OIS_aantalVierDecimaal),
												$this->formatGetal($subdata['actueleFonds'],2),
												$subdata['valuta'],
												"",
												$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
												$percentageVanTotaaltxt));

				}
				$percentageVanTotaal_totaal += $percentageVanTotaal;

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$lastCategorie = $subdata['secOmschrijving'];
			}

			// print categorie footers
			//$this->printSubTotaal("Subtotaal:", $categorien[subtotaalbegin], $categorien[subtotaalactueel]);

			// totaal op categorie tellen
			$totaalbegin += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$lastCategorie2 = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie
		//$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$percentageVanTotaal_totaal);

		if($this->pdf->rapport_OIS_zorgplichtpercentage)
		{
			$pvtTxt = $this->formatGetal($percentageVanTotaal_totaal,1);
			$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel,$pvtTxt);
		}
		else
		{
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel);
		}

		$percentageVanTotaal_totaal = 0;

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS rentetotaal FROM TijdelijkeRapportage ".
					" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($q,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($q);
			$DB3->Query();
			$subtotaal = $DB3->nextRecord();
			$subtotaal = $subtotaal['rentetotaal'];

			if($this->pdf->rapport_OIS_onderverdelingAandeel)
			{
				$percentageVanTotaal = 0;
				$percentageVanTotaaltxt = "";
			}
			else
			{
				$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$afronding)." %";
			}

			//$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$percentageVanTotaal ,"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_OIS_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.beleggingscategorie = '".$categorien['beleggingscategorie']."' AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{
						if($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
						{
							$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);
							$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
						}
						else
						{
							$percentageVanTotaaltxt = "";
						}

						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];
						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
						$this->pdf->row(array("","",$subdata['fondsOmschrijving'],"","","","",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
														$percentageVanTotaaltxt));
					}

					// print subtotaal
					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
				}
			}

			// totaal op rente
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "",
																		$totaalRenteInValuta);
		}

		// Liquiditeiten
		$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS liqtotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rekening'  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($q,__FILE__,__LINE__);
		$DB3 = new DB();
		$DB3->SQL($q);
		$DB3->Query();
		$subtotaal = $DB3->nextRecord();
		$subtotaal = $subtotaal['liqtotaal'];

		if($this->pdf->rapport_OIS_onderverdelingAandeel)
		{
			$percentageVanTotaal = 0;
			$percentageVanTotaaltxt = "";
		}
		else
		{
			$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";
		}

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
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

		$totaalLiquiditeitenInValuta = 0;

		if($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$percentageVanTotaal,"bi");

			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			while($data = $DB1->NextRecord())
			{
				if($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
				{
					$percentageVanTotaal = $data['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
				}
				else
				{
					$percentageVanTotaaltxt = "";
				}

				if($this->pdf->rapport_OIS_liquiditeiten_omschr)
					$this->pdf->rapport_liquiditeiten_omschr = $this->pdf->rapport_OIS_liquiditeiten_omschr;

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
												"",
												$omschrijving,
												"",
												"",
												"",
												"",
												$this->formatGetalKoers($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
												$percentageVanTotaaltxt));

			}
			// totaal liquiditeiten
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "", $totaalLiquiditeitenEuro);
		}

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		$actueleWaardePortefeuille = $totaalWaarde;
		// print grandtotaal

		if($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
		{
			$totaalTxt = $this->formatGetal(100,1);
		}
		else
			$totaalTxt = "";

		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille , $totaalTxt, true);

		$this->pdf->ln();


		if($this->pdf->rapport_OIS_valutaoverzicht == 1)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIS_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_OIS_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}
	}
}
?>