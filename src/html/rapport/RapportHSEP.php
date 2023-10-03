<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/08/11 15:38:23 $
File Versie					: $Revision: 1.24 $

$Log: RapportHSEP.php,v $
Revision 1.24  2011/08/11 15:38:23  rvv
*** empty log message ***

Revision 1.23  2011/08/07 09:04:22  rvv
*** empty log message ***

Revision 1.22  2010/04/21 14:00:56  rvv
*** empty log message ***

Revision 1.21  2010/04/18 10:40:20  rvv
*** empty log message ***

Revision 1.20  2010/03/24 16:24:13  rvv
*** empty log message ***

Revision 1.19  2010/03/17 14:59:31  rvv
*** empty log message ***

Revision 1.18  2010/03/10 10:56:10  rvv
*** empty log message ***

Revision 1.17  2009/01/20 17:44:08  rvv
*** empty log message ***

Revision 1.16  2008/10/01 10:22:54  rvv
*** empty log message ***

Revision 1.15  2008/09/26 08:09:56  rvv
*** empty log message ***

Revision 1.14  2008/09/03 09:02:51  rvv
*** empty log message ***

Revision 1.13  2008/07/02 12:56:53  rvv
*** empty log message ***

Revision 1.12  2008/07/02 11:37:40  rvv
*** empty log message ***

Revision 1.11  2008/07/02 07:30:57  rvv
*** empty log message ***

Revision 1.10  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.9  2008/05/16 08:12:57  rvv
*** empty log message ***

Revision 1.8  2007/04/20 12:21:16  rvv
*** empty log message ***

Revision 1.7  2006/11/03 11:24:04  rvv
Na user update

Revision 1.6  2006/10/31 12:04:41  rvv
Voor user update

Revision 1.5  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.4  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.3  2005/09/30 09:45:45  jwellner
rapporten aangepast.

Revision 1.2  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.1  2005/07/15 11:34:42  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.2  2005/07/12 07:09:50  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd
*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSEP
{
	function RapportHSEP($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSEP";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = '';//"Huidige samenstelling effectenportefeuille";

		$this->pdf->rapport_koptextBackup = $this->pdf->rapport_koptext;
		$this->pdf->rapport_fontBackup = $this->pdf->rapport_font;
		$this->pdf->rapport_koptext = '';
	//	$this->pdf->rapport_font = 'Arial';

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
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

	function printSubTotaal($title, $totaalA, $totaalB, $totaalC)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] ;
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$verschil = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5];

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($begin+2,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		$this->pdf->Line($verschil+2,$this->pdf->GetY(),$verschil + $this->pdf->widthB[6],$this->pdf->GetY());

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,2);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,2);

		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,2);

		$this->pdf->SetX(0);
		$this->pdf->Cell($begin ,4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,$totaalCtxt, 0,1, "R");
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		// lege regel
		$this->pdf->ln();

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] ;
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] ;
		$verschil = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5];

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetal($totaalA,2);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,2);

		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,2);

		$this->pdf->Line($begin+2,$this->pdf->GetY(),$begin + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());
		$this->pdf->Line($verschil+2,$this->pdf->GetY(),$verschil + $this->pdf->widthB[6],$this->pdf->GetY());

		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->SetX(0);
		$this->pdf->Cell($begin ,4, "", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6],4,$totaalCtxt, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->setDash(1,1);


		if(!empty($totaalA))
			$this->pdf->Line($begin+2,$this->pdf->GetY(),$begin + $this->pdf->widthB[4],$this->pdf->GetY());

		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[5],$this->pdf->GetY());

		$this->pdf->setDash();

		$this->pdf->ln();

		return $totaalA;
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


		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
		global $__appvar;
		$DB = new DB();

		// voor data

		$this->pdf->widthB = array(0,23,72,30,30,10,30);
		$this->pdf->alignB = array('L','R','L','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65,20,20,25,25,35);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R');

	  $this->pdf->startPagina = $this->pdf->customPageNo;

    if($this->pdf->rapportToonRente == false)
      $renteFilter=" AND Type <> 'rente' ";
    else
      $renteFilter='';
//// liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.valuta ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$totaalLiquiditeitenInValuta = 0;
		$rekeningAantal = 0;
		while($data = $DB->NextRecord())
		{
			$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

			$liquiditeitenData[]=array("","",
											" ".$data['fondsOmschrijving'],
											"",
											$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],2),
											$data['valuta'],
											$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2));
			$rekeningAantal ++;

		}

////

$this->pdf->saldoGeldrekeningen= $this->formatGetal($totaalLiquiditeitenEuro,2);

		$this->pdf->AddPage('P');
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

				// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'".$renteFilter
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT Beleggingscategorien.Omschrijving,
						 TijdelijkeRapportage.valuta,
						 TijdelijkeRapportage.beleggingscategorie,
						 SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin,
						 SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel
				FROM  TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)
					  LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie)
				WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'
				AND TijdelijkeRapportage.type = 'fondsen'
				AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
				.$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
				" GROUP BY TijdelijkeRapportage.beleggingscategorie ". //, TijdelijkeRapportage.valuta
				" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			$subtotaalverschil = 0;
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$categorien['Omschrijving'] = strtoupper($categorien['Omschrijving']);
			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
			  $actueleWaardePortefeuille += $totaalactueel;
				$totalen[$categorien['beleggingscategorie']] = array('omschrijving'=>$lastCategorie,
				                                                     'totaal'=>$totaalactueel);

				$title = "Subtotaal ".$lastCategorie;
			//	$actueleWaardePortefeuille += $this->printTotaal($title, $totaalactueel,$totaalbegin, $totaalverschil);
				$totaalbegin = 0;
				$totaalactueel = 0;
				$totaalverschil = 0;

			}

			if($lastCategorie <> $categorien[Omschrijving])
			{
				$this->printKop($categorien[Omschrijving], "bi");
			}
			// subkop (valuta)
//			$this->printKop("Waarden ".$categorien[valuta], "");

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
	//		" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
	//		" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds, ".
			"  TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			"  TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			"   TijdelijkeRapportage.beleggingscategorie,  ".
			"   TijdelijkeRapportage.valuta, ".
			 "  TijdelijkeRapportage.portefeuille, ".
			 " Fondsen.OptieExpDatum, ".
			 " UNIX_TIMESTAMP(Fondsen.Rentedatum) as coupondatum ".
			" FROM TijdelijkeRapportage, Fondsen WHERE ".
			" TijdelijkeRapportage.Fonds = Fondsen.Fonds AND ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
	//		" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
			" ORDER BY Fondsen.beurs, Fondsen.OptieBovenliggendFonds  ,Fondsen.OptieExpDatum , TijdelijkeRapportage.fondsOmschrijving  asc";
		//	echo "<br>".$subquery."<br>".$categorien[beleggingscategorie];
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();//exit;
			while($subdata = $DB2->NextRecord())
			{//listarray($subdata); echo $subquery; exit;
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

												//$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
												//$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],2),
				$verschil = $subdata[actuelePortefeuilleWaardeEuro] - $subdata[beginPortefeuilleWaardeEuro];

				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);



				if ($subdata['OptieExpDatum'] != '')
				  $vervalDatum = substr($subdata[OptieExpDatum],4,2).'/'.substr($subdata[OptieExpDatum],2,2);
				elseif($subdata['coupondatum'] > 0)
		      $vervalDatum = date("d/m",$subdata['coupondatum']);
				else
				  $vervalDatum = '';

				$this->pdf->row(array("",
															$this->formatAantal($subdata[totaalAantal],0,true),
												      " ".$subdata[fondsOmschrijving],
												      $vervalDatum,

												      $this->formatGetal($subdata['actueleFonds'],2),$subdata['valuta'],
												      $this->formatGetal($subdata[actuelePortefeuilleWaardeEuro]),
												      ));

				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];
			}

			$totaalactueel += $categorien[subtotaalactueel];
			$lastCategorie = $categorien[Omschrijving];
		}

		// totaal voor de laatste categorie

		$totalen[$lastCategorie] = array('omschrijving'=>$lastCategorie,
				                        'totaal'=>$totaalactueel);
		$actueleWaardePortefeuille +=$totaalactueel;// $this->printTotaal("Subtotaal ".$lastCategorie, $totaalactueel,$totaalbegin, $totaalverschil);

		// selecteer rente
		/*
		$query = "SELECT TijdelijkeRapportage.valuta, TijdelijkeRapportage.beleggingscategorie, SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.RenteBerekenen = '1' ".
		" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
*/
		if($this->pdf->rapportToonRente)
	  	$this->printKop("OPGELOPEN RENTE","bi");

		$totaalRenteInValuta = 0 ;

	//	while($categorien = $DB->NextRecord())
	//	{
			$subtotaalRenteInEUR = 0;

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rentedatum, ".
			" TijdelijkeRapportage.renteperiode, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente' ".$renteFilter.
	//		"AND TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
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

				$subtotaalRenteInEUR += $subdata['actuelePortefeuilleWaardeEuro'];
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				if($this->pdf->rapportToonRente)
			  	$this->pdf->row(array("",""," ".$subdata['fondsOmschrijving'].$rentePeriodetxt,"","",'',
				  								$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'])));

			}
			if($this->pdf->rapportToonRente)
		  	$totalen['Rente'] = array('omschrijving'=>'OPGELOPEN RENTE','totaal'=>$subtotaalRenteInEUR);

			$totaalRenteInEUR += $subtotaalRenteInEUR;
	//	}

		// totaal op rente
		$actueleWaardePortefeuille += $totaalRenteInEUR;// $this->printTotaal("Subtotaal Opgelopen rente: ", $totaalRenteInValuta,"","");


		// Liquiditeiten
//
if($rekeningAantal > 1)
{
    $this->printKop("LIQUIDITEITEN","bi");
		$totaalLiquiditeitenInValuta = 0;
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		while (list($key, $data) = each($liquiditeitenData))
		{
			$this->pdf->row($data);
		}
}
		$totalen['Liquiditeiten'] = array('omschrijving'=>'Saldo',
				                        'totaal'=>$totaalLiquiditeitenEuro);
		$actueleWaardePortefeuille += $totaalLiquiditeitenEuro;//$this->printTotaal("", $totaalLiquiditeitenEuro, "","");

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		$this->pdf->ln();


		$aantal = count($totalen)+1;
		$hoogte = $aantal * $this->pdf->rowHeight ;
		if($this->pdf->GetY() + $hoogte > $this->pdf->PageBreakTrigger)
		  $this->pdf->addPage('P');


		$beginTekst = "TOTAAL CATEGORIE";//per categorie in ".$this->pdf->rapportageValuta;
		while (list($key, $data) = each($totalen))
		{
		  $this->pdf->SetWidths(array(50,50,30,20,20));
		  $this->pdf->SetAligns(array('L','L','R','R'));
		  $this->pdf->Row(Array($beginTekst,
		                        $data['omschrijving'],
		                        $this->formatGetal($data['totaal'],2),
		                        $this->formatGetal(($data['totaal']/$actueleWaardePortefeuille)*100,2).' %'
		                        )) ;
		  $beginTekst ='';
		  $totaalCategorieEur += $data['totaal'];
		}
    $this->pdf->Line(112,$this->pdf->GetY(),138,$this->pdf->GetY());
    $this->pdf->Line(144,$this->pdf->GetY(),158,$this->pdf->GetY());
    $this->pdf->SetY($this->pdf->GetY()+1);
    $this->pdf->SetWidths(array(50,50,30,20,20));
		$this->pdf->SetAligns(array('L','L','R','R'));

    $this->pdf->Row(Array('',
		                       '',
		                        $this->formatGetal($actueleWaardePortefeuille,2),
		                        $this->formatGetal(($totaalCategorieEur/$actueleWaardePortefeuille)*100,2).' %'
		                        )) ;

//
$this->pdf->ln();
reset($totalen);

$query = "SELECT
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as inValuta,
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as inEur,
TijdelijkeRapportage.valuta,
Valutas.omschrijving
FROM TijdelijkeRapportage, Valutas
WHERE
TijdelijkeRapportage.valuta = Valutas.valuta AND
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
" .$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter."
GROUP BY TijdelijkeRapportage.valuta
ORDER BY Valutas.afdrukvolgorde asc";
$DB->SQL($query);
$DB->Query();


		$aantal = $DB->records()+1;
		$hoogte = $aantal * $this->pdf->rowHeight +10;

		if($this->pdf->GetY() + $hoogte > $this->pdf->PageBreakTrigger)
		  $this->pdf->addPage('P');

		$this->pdf->ln();
		$beginTekst = "TOTAAL VALUTA";//per valuta in ".$this->pdf->rapportageValuta;
			while($data = $DB->NextRecord())
		{
		  $this->pdf->SetWidths(array(50,50,30,20,20));
		  $this->pdf->SetAligns(array('L','L','R','R'));
		  $this->pdf->Row(Array($beginTekst,
		                        $data['omschrijving'],
		                        $this->formatGetal($data['inEur'],2),
		                        $this->formatGetal(($data['inEur']/$actueleWaardePortefeuille)*100,2).' %'
		                        )) ;
		  $valutalTotaalEur += $data['inEur'];
		  $beginTekst ='';
		}
    $this->pdf->Line(112,$this->pdf->GetY(),138,$this->pdf->GetY());
    $this->pdf->Line(144,$this->pdf->GetY(),158,$this->pdf->GetY());
    $this->pdf->SetY($this->pdf->GetY()+1);

    		$this->pdf->SetWidths(array(50,50,30,20,20));
		$this->pdf->SetAligns(array('L','L','R','R'));
    $this->pdf->Row(Array('',
		                       '',
		                        $this->formatGetal($actueleWaardePortefeuille,2),
		                        $this->formatGetal(($valutalTotaalEur/$actueleWaardePortefeuille)*100,2).' %'
		                        )) ;

		for($i=$this->pdf->startPagina;$i<=$this->pdf->PageNo();$i++)
		  $this->pdf->pages[$i]= str_replace('{LastPage}',$this->pdf->customPageNo,$this->pdf->pages[$i]);

		if($this->pdf->portefeuilledata['AEXVergelijking'] > 0)
	    $this->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);

    $this->restoreKoptekst();
  }

  function restoreKoptekst()
  {
    $this->pdf->rapport_koptext = $this->pdf->rapport_koptextBackup;
    $this->pdf->rapport_font    = $this->pdf->rapport_fontBackup;
  }



  function printAEXVergelijking($vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
    $border=0;
		$DB  = new DB();
		$DB2 = new DB();

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage('P');
			$this->pdf->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if($this->pdf->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $this->pdf->rapport_perfIndexJanuari = false;
	  }
		if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($this->pdf->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($this->pdf->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($this->pdf->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$this->pdf->ln();
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110+9+$extraX,$hoogte,'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect($this->pdf->marge,$this->pdf->getY(),110+9+$extraX,$hoogte);
		$this->pdf->SetX($this->pdf->marge);

		// kopfontcolor
		//$this->pdf->SetTextColor($this->pdf->rapport_kop4_fontcolor[r],$this->pdf->rapport_kop4_fontcolor[g],$this->pdf->rapport_kop4_fontcolor[b]);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
		$this->pdf->Cell(40,4, vertaalTekst("Index-vergelijking",$this->pdf->rapport_taal), 0,0, "L");

		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		//$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
		if($this->pdf->rapport_perfIndexJanuari == true)
			$this->pdf->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$this->pdf->Cell(26,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$this->pdf->Cell(26,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");

	  $this->pdf->Cell(26,4, vertaalTekst("Perf in %",$this->pdf->rapport_taal), $border,$perfVal, "R");
		if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		  $this->pdf->Cell(26,4, vertaalTekst("Perf in % in EUR",$this->pdf->rapport_taal), $border,$perfEur, "R");
		if($this->pdf->rapport_perfIndexJanuari == true)
			$this->pdf->Cell(26,4, vertaalTekst("Jaar Perf.",$this->pdf->rapport_taal), $border,$perfJan, "R");

		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    if($this->pdf->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
			  }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  if($this->pdf->rapport_perfIndexJanuari == true)
		  {
		    $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
		  	$DB2->SQL($q);
		  	$DB2->Query();
		  	$koers0 = $DB2->LookupRecord();
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";
			$this->pdf->Cell(40,4, $perf[Omschrijving], $border,0, "L");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		     $this->pdf->Cell(26,4, $this->pdf->formatGetal($koers0[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers1[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers2[Koers],2), $border,0, "R");
		  $this->pdf->Cell(26,4, $this->pdf->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $this->pdf->rapport_portefeuille."' ";
		$DB->SQL($query2);
		$DB->Query();

		while($perf = $DB->nextRecord())
		{

		  if($perf['Valuta'] != 'EUR')
		  {

		    if($this->pdf->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
		    }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  	if($this->pdf->rapport_perfIndexJanuari == true)
		    {
		  	  $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			    $DB2->SQL($q);
			    $DB2->Query();
			    $koers0 = $DB2->LookupRecord();
		    }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";


			$this->pdf->Cell(40,4, $perf[Omschrijving], 0,0, "L");
			if($this->pdf->rapport_perfIndexJanuari == true)
		     $this->pdf->Cell(26,4, $this->pdf->formatGetal($koers0[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers1[Koers],2), $border,0, "R");
			$this->pdf->Cell(26,4, $this->pdf->formatGetal($koers2[Koers],2), $border,0, "R");
		  $this->pdf->Cell(26,4, $this->pdf->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->pdf->rapport_printAEXVergelijkingEur == 1)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->pdf->rapport_perfIndexJanuari == true)
		    $this->pdf->Cell(26,4, $this->pdf->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
	}

}
?>