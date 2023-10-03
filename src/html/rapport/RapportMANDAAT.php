<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.7 $

$Log: RapportMANDAAT.php,v $
Revision 1.7  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.6  2017/10/22 11:11:54  rvv
*** empty log message ***

Revision 1.5  2017/10/11 14:54:17  rvv
*** empty log message ***

Revision 1.4  2017/09/14 05:46:43  rvv
*** empty log message ***

Revision 1.3  2017/09/13 15:44:03  rvv
*** empty log message ***

Revision 1.2  2017/08/19 18:19:17  rvv
*** empty log message ***

Revision 1.41  2014/12/21 13:24:42  rvv
*** empty log message ***

Revision 1.40  2012/03/14 17:29:35  rvv
*** empty log message ***

Revision 1.39  2012/01/15 11:03:37  rvv
*** empty log message ***

Revision 1.38  2011/12/24 16:36:57  rvv
*** empty log message ***

Revision 1.37  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.36  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.35  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.34  2010/09/15 16:27:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportMANDAAT
{
	function RapportMANDAAT($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MANDAAT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_HSE_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_HSE_titel;
		else
			$this->pdf->rapport_titel = "Contractuele controles - restricties (Positioneel)";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->excelData[]=array("beleggingscategorie","Fonds","Aantal","Per stuk in valuta","Portefeuille in valuta","Portefeuille in ".$this->pdf->rapportageValuta,
			"Per stuk in valuta","Portefeuille in valuta", "Portefeuille in ".$this->pdf->rapportageValuta,'conclusie','detail');
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
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

	function printSubTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

	  if(!empty($totaalA))
		  $totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal,true);//Koers
	  if(!empty($totaalB))
		  $totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		if(!empty($totaalA))
			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());

		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor['r'],$this->pdf->rapport_subtotaal_omschr_fontcolor['g'],$this->pdf->rapport_subtotaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_omschr_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->Cell($begin,4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_fontcolor['r'],$this->pdf->rapport_subtotaal_fontcolor['g'],$this->pdf->rapport_subtotaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");


		if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
		$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
	}


	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		// lege regel
		$this->pdf->ln();

		  if(!empty($totaalA))
			  $totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal,true);//Koers
	    if(!empty($totaalB))
			  $totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			if(!empty($totaalA))
				$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());


		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

			$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
				if(!empty($totaalA))
				{
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
					$this->pdf->Line($begin,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
				}
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);

		}
		else
		{
			$this->pdf->setDash(1,1);
			if(!empty($totaalA))
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

	  $query="SELECT Vermogensbeheerders.VerouderdeKoersDagen
    FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];


	  $this->pdf->widthB = array(10,55,20,20,30,30,5,20,30,30,30);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65   ,20,20,30,30,5,20,30,30,30);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars['MANDAATPaginas']=$this->pdf->page;
		// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;


		$query="SELECT
contractueleUitsluitingen.categoriesoort,
contractueleUitsluitingen.fonds,
contractueleUitsluitingen.portefeuille,
contractueleUitsluitingen.vermogensbeheerder,
contractueleUitsluitingen.categorie,
contractueleUitsluitingen.vanaf
FROM
contractueleUitsluitingen
WHERE ((Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Portefeuille='') OR Portefeuille='".$this->portefeuille."') 
AND vanaf < '".$this->rapportageDatum."'
AND (einddatum='0000-00-00' OR einddatum > now())";
		$DB->SQL($query); 
		$DB->Query();
		$uitgeslotenFondsen=array();
		$uitegeslotenCategorien=array();
		$koppelVertaling=array('Beleggingscategorien'=>'beleggingscategorie',
													 'Beleggingssectoren'=>'beleggingssector',
													 'Fondssoort'=>'fondssoort',
													 'Regios'=>'regio',
			                     'afmCategorien'=>'afmCategorie',
													 'Valuta'=>'valuta',
													 'Rating'=>'rating',
													 'Zorgplichtcategorien'=>'zorgplicht',
													 'Hoofdcategorien'=>'hoofdcategorie');



		while($data = $DB->nextRecord())
		{
			if($data['fonds']<>'')
		  	$uitgeslotenFondsen[$data['fonds']]=$data['fonds'];
			if($data['categoriesoort']<>'')
			  $uitegeslotenCategorien[$koppelVertaling[$data['categoriesoort']]][$data['categorie']]=$data['categorie'];
		}


		//listarray($uitegeslotenCategorien);
	//	exit;

		$beginQuery = $this->pdf->ValutaKoersBegin;

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.beleggingscategorieOmschrijving, TijdelijkeRapportage.valuta,TijdelijkeRapportage.type, 
			if(TijdelijkeRapportage.type='fondsen',1,if(TijdelijkeRapportage.type='rente',2,3)) as volgorde, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
			TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, 
			TijdelijkeRapportage.beleggingscategorie, 
			TijdelijkeRapportage.hoofdcategorie, 
			TijdelijkeRapportage.beleggingssector, 
			Fondsen.fondssoort, 
			TijdelijkeRapportage.regio, 
			TijdelijkeRapportage.valuta, 
			TijdelijkeRapportage.afmCategorie, 
			Fondsen.rating, 
			TijdelijkeRapportage.Fonds,
			if(ZorgplichtPerFonds.Zorgplicht <> null ,ZorgplichtPerFonds.Zorgplicht,ZorgplichtPerBeleggingscategorie.Zorgplicht) as zorgplicht,
			TijdelijkeRapportage.portefeuille,
			round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd ".
			" FROM TijdelijkeRapportage 
			  LEFT JOIN Fondsen on TijdelijkeRapportage.Fonds=Fondsen.Fonds 
			  LEFT JOIN ZorgplichtPerFonds ON TijdelijkeRapportage.fonds = ZorgplichtPerFonds.Fonds AND ZorgplichtPerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
			  LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
			  WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".

			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY volgorde ,TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc, 
			TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);

		$DB->SQL($subquery);
		$DB->Query();
		$lastValuta='';
		$lastCategorie='';
		$lastType='';

		while($subdata = $DB->NextRecord())
		{
				$conclusie='voldoet';
				$detail='';
        if(isset($uitgeslotenFondsen[$subdata['Fonds']]))
				{
					$conclusie='voldoet niet';
					$detail.=",Uigesloten fonds";
				}
				foreach($koppelVertaling as $check)
				{
					if(isset($uitegeslotenCategorien[$check][$subdata[$check]]))
					{
						$conclusie='voldoet niet';
						$detail.=",Uigesloten in $check";
					}
				}

        if($lastCategorie=='' || $lastCategorie <> $subdata['beleggingscategorieOmschrijving'])
				  $this->printKop(vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);
				$lastCategorie=$subdata['beleggingscategorieOmschrijving'];

				if($lastType<>$subdata['type'] && $subdata['type']=='rente')
				{
					$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);
				}
				elseif($lastType<>$subdata['type'] && $subdata['type']=='rekening')
				{
				//	$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);
				}
				$lastType=$subdata['type'];

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


				if($subdata['type']=='fondsen')
				{
					$this->pdf->row(array("",
														"",
														$this->formatAantal($subdata['totaalAantal'], 0, $this->pdf->rapport_HSE_aantalVierDecimaal),
														$this->formatGetal($subdata['beginwaardeLopendeJaar'], 2),
														$this->formatGetal($subdata['beginPortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
														$this->formatGetalKoers($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal, true),
														"",
														$this->formatGetal($subdata['actueleFonds'], 2),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
														$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
														$conclusie));
					$this->pdf->excelData[]=array($subdata['beleggingscategorieOmschrijving'],$subdata['fondsOmschrijving'],round($subdata['totaalAantal']),
						round($subdata['beginwaardeLopendeJaar'], 4),
						round($subdata['beginPortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
						round($subdata['beginPortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal, true),
						round($subdata['actueleFonds'], 4),
						round($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
						round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
						$conclusie,$detail);

				}
				elseif($subdata['type']=='rente' || $subdata['type']=='rekening')
				{
					$this->pdf->row(array("", "", "", "", "", "", "", "",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
														$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
														$conclusie));
					$this->pdf->excelData[]=array($subdata['beleggingscategorieOmschrijving'],$subdata['fondsOmschrijving'], "", "", "", "", "",
						round($subdata['actuelePortefeuilleWaardeInValuta'], $this->pdf->rapport_decimaal),
						round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_decimaal),
						$conclusie,$detail);
				}


				$valutaWaarden[$subdata['valuta']] = $subdata['actueleValuta'];
				$valutaOmschrijving[$subdata['valuta']] = $subdata['ValutaOmschrijving'];


				$actueleWaardePortefeuille+=$subdata['actuelePortefeuilleWaardeEuro'];
			}

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
	//		echo "<script>
	//		alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
	//		</script>";
	//		ob_flush();
		}


		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $totaalWaarde,100,true);

		$this->pdf->ln();
	//	}



	}
}
?>