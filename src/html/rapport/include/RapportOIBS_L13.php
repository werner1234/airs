<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2010/04/21 13:50:19 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIBS_L13.php,v $
Revision 1.2  2010/04/21 13:50:19  rvv
*** empty log message ***

Revision 1.1  2010/04/21 12:54:50  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIBS_L13
{
	function RapportOIBS_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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


			$subtotaalverschil = 0;
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			$categorien['Omschrijving'] = "INSTRUMENTEN";
			if($lastCategorie <> $categorien['Omschrijving'])
			{
			//	$this->printKop($categorien['Omschrijving'], "bi");
			}
			// subkop (valuta)
//			$this->printKop("Waarden ".$categorien[valuta], "");

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, if(Fondsen.OptieBovenliggendFonds <> '' ,Fondsen.OptieBovenliggendFonds,Fondsen.Fonds) as sortering,".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
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
		//	" TijdelijkeRapportage.beleggingscategorie =  '".$categorien[beleggingscategorie]."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].$renteFilter.
			" ORDER BY  sortering, Fondsen.OptieBovenliggendFonds";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();//exit;
			while($subdata = $DB2->NextRecord())
			{//listarray($subdata); echo $subquery; exit;
			  if($subdata['sortering'] <> $lastSortering)
			    $this->pdf->ln(1);
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
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

				$totaalactueel += $subdata[actuelePortefeuilleWaardeEuro];
				$lastSortering = $subdata['sortering'];
			}




		// totaal voor de laatste categorie

		$totalen[$lastCategorie] = array('omschrijving'=>$lastCategorie,
				                        'totaal'=>$totaalactueel);
		$actueleWaardePortefeuille +=$totaalactueel;// $this->printTotaal("Subtotaal ".$lastCategorie, $totaalactueel,$totaalbegin, $totaalverschil);


		if($this->pdf->rapportToonRente)
	  	$this->printKop("OPGELOPEN RENTE","bi");

		$totaalRenteInValuta = 0 ;

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



		for($i=$this->pdf->startPagina;$i<=$this->pdf->PageNo();$i++)
		  $this->pdf->pages[$i]= str_replace('{LastPage}',$this->pdf->customPageNo,$this->pdf->pages[$i]);

 $this->restoreKoptekst();

  }

  function restoreKoptekst()
  {
    $this->pdf->rapport_koptext = $this->pdf->rapport_koptextBackup;
    $this->pdf->rapport_font    = $this->pdf->rapport_fontBackup;
  }
}
?>