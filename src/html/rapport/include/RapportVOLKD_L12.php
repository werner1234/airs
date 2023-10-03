<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLKD_L12
{
	function RapportVOLKD_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLKD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingssector";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->totaalWaarde=0;
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

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7]+ $this->pdf->widthB[8] ;

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_OIS_decimaal);
    else
      return 0;  

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($actueel,4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		//$this->pdf->Cell($this->pdf->widthB[6],4, "", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");

		$this->pdf->Cell($this->pdf->widthB[10],4,$procent, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
		//	$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		//	$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
		}
		else
		{
		//	$this->pdf->setDash(1,1);
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			$this->pdf->setDash();
		}

		//$this->pdf->ln();

		return $totaalB;
	}

	function addRente($categorie)
	{
		global $__appvar;
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente' AND TijdelijkeRapportage.Beleggingscategorie='$categorie' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
			" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$actueleWaardePortefeuille=0;
		if($DB->records() > 0)
		{

			$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS rentetotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rente'  AND TijdelijkeRapportage.Beleggingscategorie='$categorie'  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($q,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($q);
			$DB3->Query();
			$subtotaal = $DB3->nextRecord();
			$subtotaal = $subtotaal['rentetotaal'];


			$percentageVanTotaal = $subtotaal/ ($this->totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1)." %";


			//$percentageVanTotaal = $categorien[subtotaalactueel]/ ($totaalWaarde/100);
			$this->printKop(vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal),$percentageVanTotaal ,"bu");
      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
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
						" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,TijdelijkeRapportage.beleggingscategorie,
			      TijdelijkeRapportage.Bewaarder ".
						" FROM TijdelijkeRapportage WHERE ".
						" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
						" TijdelijkeRapportage.type = 'rente'  AND ".
						" TijdelijkeRapportage.beleggingscategorie = '".$categorien['beleggingscategorie']."' AND ".
						" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
						.$__appvar['TijdelijkeRapportageMaakUniek'].
						" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{
						if($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
						{
							$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($this->totaalWaarde/100);
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
						$this->pdf->row(array("","",$subdata['fondsOmschrijving'],
															$subdata['Bewaarder'],
															"","",
															'',
															$subdata['valuta'],
															"",
															$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
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
		return $actueleWaardePortefeuille;
	}

	function printKop($title, $procent, $type="default")
	{
		switch($type)
		{
			case "b" :
      case "bu" :
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);

		if($this->pdf->rapport_layout == 12)
		 $afronding=1;
		else
		 $afronding=0;

		$procenttxt = $this->formatGetal($procent,$afronding)." %";



		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell($this->pdf->widthB[0],4, $procenttxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[1],4, $title, 0,1, "L");
    
    if($type=='bu')
      $this->pdf->line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->getY(),$this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1],$this->pdf->getY(),array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
	}

	function writeRapport()
	{
		global $__appvar;
		// voor data
		$this->pdf->widthB = array(12,55,68-3,17,25,20+3,20,20,2,25,13);
		$this->pdf->alignB = array('R','L','L','L','L','R','R','L','R','R','R');

		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = array('R','L','L','L','L','R','R','L','R','R','R');


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
		$totaalWaarde = $totaalWaarde['totaal'];
		$this->totaalWaarde=$totaalWaarde;


		$actueleWaardePortefeuille = 0;

		$query = "SELECT Beleggingscategorien.Omschrijving, Beleggingssectoren.Omschrijving AS secOmschrijving , ".
		" TijdelijkeRapportage.beleggingssector, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM (TijdelijkeRapportage, Valutas) ".
		" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
		" LEFT JOIN Beleggingssectoren on (TijdelijkeRapportage.beleggingssector = Beleggingssectoren.Beleggingssector) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta AND ".
		" TijdelijkeRapportage.type IN('fondsen','rekening') AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie  ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Beleggingssectoren.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query); //echo "catt $query<br>\n";exit;
		$DB->Query();

		$lastBeleggingscategorie='leeg';
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			// print totaal op hele categorie.

			if($lastCategorie2 <> $categorien['Omschrijving'] && !empty($lastCategorie2) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal);
				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel);
				$percentageVanTotaal_totaal = 0;
				$totaalbegin = 0;
				$totaalactueel = 0;
				$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);

			}

			if($lastCategorie2 <> $categorien['Omschrijving'])
			{
				$percentageVanTotaal = $categorien['subtotaalactueel']/ ($totaalWaarde/100);
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal),$percentageVanTotaal, "bu");
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
			" TijdelijkeRapportage.beleggingscategorie,
			  TijdelijkeRapportage.Bewaarder, ".
			" TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.fonds,
			  TijdelijkeRapportage.rekening,
			  TijdelijkeRapportage.type,
			  Fondsen.ISINcode,
			  if(type='rekening', SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3),'') as rekVolgorde,
			  if(type='rekening', TijdelijkeRapportage.valutaVolgorde,'') as rekVolgorde2,".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage ".
			" LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds=Fondsen.fonds".
			" WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.type IN('fondsen','rekening') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.Lossingsdatum, rekVolgorde, rekVolgorde2, TijdelijkeRapportage.fondsOmschrijving asc";

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
							 " TijdelijkeRapportage.type IN('fondsen','rekening') AND ".
							 " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
					debugSpecial($q,__FILE__,__LINE__);
					$DB3 = new DB();
					$DB3->SQL($q); //echo "q $q <br>\n";
					$DB3->Query();
					$subtotaal = $DB3->nextRecord();
					$subtotaal = $subtotaal['sectortotaal'];


						$percentageVanTotaal =round($subtotaal/ ($categorien['subtotaalactueel']/100),1);
						//echo $categorien[Omschrijving]. " ".$percentageVanTotaal."<br>\n";
						if ($this->pdf->rapport_layout <> 12)
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


					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
					$this->pdf->Cell($this->pdf->widthB[0],4, $percentageVanTotaaltxt, 0,0, "R");
					$this->pdf->Cell($this->pdf->widthB[1],4, $subdata['secOmschrijving'], 0,0, "L");

					$this->pdf->SetX($this->pdf->marge);
				}

				if($this->pdf->rapport_layout == 12)
				{
					$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
				}

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       
        if($subdata['type']=='rekening')
        {
          $omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
          $omschrijving = vertaalTekst(str_replace("{Rekening}",$subdata['rekening'],$omschrijving),$this->pdf->rapport_taal);
          $omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($subdata['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
          $omschrijving = vertaalTekst(str_replace("{Valuta}",$subdata['valuta'],$omschrijving),$this->pdf->rapport_taal);
          $subdata['totaalAantal']=$subdata['actuelePortefeuilleWaardeInValuta'];
          $koers='';
        }
        else
        {
          $omschrijving=$subdata['fondsOmschrijving'];
          $koers=$this->formatGetal($subdata['actueleFonds'],2);
        }
        
        if($subdata['type']=='rekening')
          $aantalDecimalen=$this->pdf->rapport_OIS_decimaal;
        else
          $aantalDecimalen=$this->pdf->rapport_OIS_aantalVierDecimaal;

				if ( $this->pdf->rapport_layout == 12 && $fondsPercentageweergeven == true)
				{

				$this->pdf->row(array($this->formatGetal(($subdata['actuelePortefeuilleWaardeEuro']/$categorien['subtotaalactueel'])*100,$afronding).' %',
												"",
                          $omschrijving,
													$subdata['Bewaarder'],
												$subdata['ISINcode'],
												$this->formatAantal($subdata['totaalAantal'],$aantalDecimalen,$this->pdf->rapport_OIS_aantalVierDecimaal),
												$koers,
												$subdata['valuta'],
												"",
												$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
												$percentageVanTotaaltxt));
				}
				else
				{
				$this->pdf->row(array("",
												"",
                          $omschrijving,
													$subdata['Bewaarder'],
													$subdata['ISINcode'],
												$this->formatAantal($subdata['totaalAantal'],$aantalDecimalen,$this->pdf->rapport_OIS_aantalVierDecimaal),
                          $koers,
												$subdata['valuta'],
												"",
												$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
												$percentageVanTotaaltxt));

				}
				$percentageVanTotaal_totaal += $percentageVanTotaal;

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$lastCategorie = $subdata['secOmschrijving'];
			}
			$lastBeleggingscategorie=$categorien['beleggingscategorie'];

			// print categorie footers
			//$this->printSubTotaal("Subtotaal:", $categorien[subtotaalbegin], $categorien[subtotaalactueel]);

			// totaal op categorie tellen
			$totaalbegin += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$lastCategorie2 = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie
		//$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$percentageVanTotaal_totaal);

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel);


		$percentageVanTotaal_totaal = 0;


		$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);

		// Liquiditeiten
		$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS liqtotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rekeningUIT'  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($q,__FILE__,__LINE__);
		$DB3 = new DB();
		$DB3->SQL($q);
		$DB3->Query();
		$subtotaal = $DB3->nextRecord();
		$subtotaal = $subtotaal['liqtotaal'];


			$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";


		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, 
			  TijdelijkeRapportage.beleggingscategorie,
			  TijdelijkeRapportage.Bewaarder".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekeningUIT'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3), TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.fondsOmschrijving";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		if($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$percentageVanTotaal,"bu");

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
												$omschrijving,"",
												"",
												$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_OIS_decimaal),
												$data['valuta'],
                          "","",
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

		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille , $totaalTxt, true);
/*
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
    
    printIndex($this);
*/
	}

  
    function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
}
?>