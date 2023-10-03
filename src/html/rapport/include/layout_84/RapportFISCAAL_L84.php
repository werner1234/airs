<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/07 17:49:22 $
File Versie					: $Revision: 1.5 $

$Log: RapportFISCAAL_L84.php,v $
Revision 1.5  2019/12/07 17:49:22  rvv
*** empty log message ***

Revision 1.4  2019/09/18 14:53:13  rvv
*** empty log message ***

Revision 1.3  2019/07/31 14:46:28  rvv
*** empty log message ***

Revision 1.2  2019/07/27 18:01:41  rvv
*** empty log message ***

Revision 1.1  2019/06/05 16:40:11  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFISCAAL_L84
{
	function RapportFISCAAL_L84($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FISCAAL";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Fiscaal overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->rapport_bedrag_decimalen=0;


		$this->pdf->excelData[]=array("Fondsomschrijving",'Aantal','Per stuk in valuta','Portefeuille in valuta',"Portefeuille in ".$this->pdf->rapportageValuta,'Per stuk in valuta','Portefeuille in valuta',
			"Portefeuille in ".$this->pdf->rapportageValuta,"Fiscale Waardering",'Reserve Herwaardering','Afboeken naar lagere marktwaarde');
    
    $this->pdf->underlinePercentage=0.9;
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


	function printTotaal($totaalData)
	{
		//($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF=0, $grandtotaal=false
   // listarray($totaalData);
    //'title'=>$title,
		//a 'totaalhistorisch'=>$totaalhistorisch,
		//b 'totaalactueel'=>$totaalactueel,
		//c 'totaalReserveHerwaardering'=>$totaalReserveHerwaardering,
	  //d 'totaalfiscaleWaardering'=>$totaalfiscaleWaardering ,
		//e 'totaalAfboekenNaarLagereMarktwaarde'=>$totaalAfboekenNaarLagereMarktwaarde)
    $posities=array('totaalhistorisch'=>4,'totaalactueel'=>8,'totaalReserveHerwaardering'=>11,'totaalfiscaleWaardering'=>10,'totaalAfboekenNaarLagereMarktwaarde'=>12);
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();

    $borders=array();
    $dataRow=array();
    for($i=0;$i<15;$i++)
      $dataRow[$i]='';

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetX(0);
    $this->pdf->Cell( $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3],4, $totaalData['title'], 0,0, "R");
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    foreach($posities as $veld=>$positie)
		{
			if(!empty($totaalData[$veld]))
			{
				$dataRow[$positie]=$this->formatGetal($totaalData[$veld],$this->rapport_bedrag_decimalen);
        $borders[$positie]='SUB';
        if($totaalData['grandTotaal']==true)
          $borders[$positie]=array('UU','TS');
        else
          $borders[$positie]='SUB';
			}
		}
    $this->pdf->CellBorders = $borders;
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->row($dataRow);
    $this->pdf->ln();
    unset( $this->pdf->CellBorders);
    return $totaalData['totaalactueel'];
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
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();


		$this->pdf->widthB = array(0.1,45,18,19,20,25,25,20,20,23,23,23,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');
  

		$this->pdf->AddPage();
    $this->pdf->templateVars['FISCAALPaginas']=$this->pdf->page;

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
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.hoofdcategorieOmschrijving as Omschrijving, TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.hoofdcategorie as beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers) AS subtotaalhistorisch, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.hoofdcategorie ".//, TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc";//,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

    $fiscaleWaardeEffecten=0;
    $totaalfiscaleWaardering=0;
    $totaalReserveHerwaardering=0;
    $totaalAfboekenNaarLagereMarktwaarde =0;
    $totaalhistorisch = 0;
    $totaalactueel = 0;
    $subtotaal=array();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if(!empty($lastCategorie) && $lastCategorie <> $categorien['Omschrijving'] )
			{

					$percentageVanTotaal = "";
			

				$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				if($totaalhistorisch < 0)
					$procentResultaat = -1 * $procentResultaat;


				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
				//function $this->printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)
			  $actueleWaardePortefeuille += $this->printTotaal(array('title'=>$title,'totaalhistorisch'=>$totaalhistorisch,'totaalactueel'=>$totaalactueel,'totaalReserveHerwaardering'=>$totaalReserveHerwaardering,
																													 'totaalfiscaleWaardering'=>$totaalfiscaleWaardering ,'totaalAfboekenNaarLagereMarktwaarde'=>$totaalAfboekenNaarLagereMarktwaarde));

				$totaalhistorisch = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
				$totaalfondsresultaat = 0;
				$procentResultaat = 0 ;
        $totaalfiscaleWaardering=0;
				$totaalGecombeneerdResultaat =0;
        $totaalReserveHerwaardering=0;
        $totaalAfboekenNaarLagereMarktwaarde =0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
			{
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
			}
      $vorigJaar=substr($this->rapportageDatum,0,4)-1;

			// print detail (select from tijdelijkeRapportage)
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
        " TijdelijkeRapportage.fonds, ".
        " TijdelijkeRapportage.valuta, ".
        " TijdelijkeRapportage.fondsEenheid, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.hoofdcategorie =  '".$categorien['beleggingscategorie']."' AND ".
		//	" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
   
			while($subdata = $DB2->NextRecord())
			{
				$ultimoKoers=globalGetFondsKoers($subdata['fonds'],$vorigJaar.'-12-31');
				$ultimoValutaKoers=globalGetValutaKoers($subdata['valuta'],$vorigJaar.'-12-31');
        $ultimoWaarde=$subdata['totaalAantal']*$subdata['fondsEenheid']*$ultimoKoers*$ultimoValutaKoers;
				
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['historischeWaardeTotaal']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['historischeWaardeTotaal']) * 100;

				if($subdata['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->rapport_bedrag_decimalen_proc);
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta']) / ($subdata['historischeWaardeTotaalValuta'] /100));
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($subdata['historischeWaardeTotaalValuta'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->rapport_bedrag_decimalen_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->rapport_bedrag_decimalen);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->rapport_bedrag_decimalen,$this->rapport_bedrag_decimalen_proc);


				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->rapport_bedrag_decimalen_proc);
			

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

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				$percentageTotaalTekst = "";
        
        

        
        $fiscaleWaardering=min($subdata['historischeWaardeTotaalValuta'],$subdata['actuelePortefeuilleWaardeEuro']);
        $fiscaleWaarderingUltimo=min($subdata['historischeWaardeTotaalValuta'],$ultimoWaarde);
        $reserveHerwaardering=($subdata['actuelePortefeuilleWaardeEuro']-$fiscaleWaardering);
        $afboekenNaarLagereMarktwaarde=$fiscaleWaardering-$fiscaleWaarderingUltimo;
        if($afboekenNaarLagereMarktwaarde<0)
          $afboekenNaarLagereMarktwaarde=0;

				  $this->pdf->row(array("",
												"",
												$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_VHO_aantalVierDecimaal),
												$this->formatGetal($subdata['historischeWaarde'],2),
												$this->formatGetal($subdata['historischeWaardeTotaalValuta'],$this->rapport_bedrag_decimalen),
                            $this->formatGetal($ultimoKoers,2),$this->formatGetal($ultimoWaarde,$this->rapport_bedrag_decimalen),
												$this->formatGetal($subdata['actueleFonds'],2),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->rapport_bedrag_decimalen),
                            $this->formatGetal($fiscaleWaarderingUltimo,$this->rapport_bedrag_decimalen),
												$this->formatGetal($fiscaleWaardering,$this->rapport_bedrag_decimalen),
                        $this->formatGetal($reserveHerwaardering, $this->rapport_bedrag_decimalen),
                        $this->formatGetal($afboekenNaarLagereMarktwaarde,$this->rapport_bedrag_decimalen)
												
												));
				$this->pdf->excelData[]=array($subdata['fondsOmschrijving'],
					round($subdata['totaalAantal'],6),
					round($subdata['historischeWaarde'],2),
					round($subdata['historischeWaardeTotaal'],0),
					round($subdata['historischeWaardeTotaalValuta'],0),
					round($subdata['actueleFonds'],2),
					round($subdata['actuelePortefeuilleWaardeInValuta'],0),
					round($subdata['actuelePortefeuilleWaardeEuro'],0),
          round($fiscaleWaardering,0),
          round($reserveHerwaardering,0),
          round($afboekenNaarLagereMarktwaarde,0));
			

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

				$subtotaal['fiscaleWaardering'] +=  $fiscaleWaardering;
        $subtotaal['reserveHerwaardering'] +=  $reserveHerwaardering;
        $subtotaal['afboekenNaarLagereMarktwaarde'] +=  $afboekenNaarLagereMarktwaarde;
        $fiscaleWaardeEffecten+=$fiscaleWaardering;
				$subtotaal['valutaResultaat'] = $subtotaal['valutaResultaat'] + $valutaResultaat;
				$subtotaal['gecombeneerdResultaat'] += $gecombeneerdResultaat;
			}


				$percentageVanTotaal = "";
			

			$procentResultaat = (($categorien['subtotaalactueel'] - $categorien['subtotaalhistorisch']) / ($categorien['subtotaalhistorisch'] /100));
			if($categorien['subtotaalhistorisch'] < 0)
				$procentResultaat = -1 * $procentResultaat;

    //  $this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalhistorisch'], $categorien['subtotaalactueel'],'', $subtotaal['fiscaleWaardering']);
			// totaal op categorie tellen
			$totaalhistorisch += $categorien['subtotaalhistorisch'];
			$totaalactueel += $categorien['subtotaalactueel'];

			$totaalfiscaleWaardering += $subtotaal['fiscaleWaardering'];
      $totaalReserveHerwaardering += $subtotaal['reserveHerwaardering'];
      $totaalAfboekenNaarLagereMarktwaarde += $subtotaal['afboekenNaarLagereMarktwaarde'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];

		  $totaalGecombeneerdResultaat += $subtotaal['gecombeneerdResultaat'];


			$lastCategorie = $categorien['Omschrijving'];
			$subtotaal = array();
		}


			$percentageVanTotaal = "";
	

		// totaal voor de laatste categorie
		$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
		if($totaalhistorisch < 0)
			$procentResultaat = -1 * $procentResultaat;
//echo $totaalGecombeneerdResultaat ."<br>";

    $title=vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
    $actueleWaardePortefeuille += $this->printTotaal(array('title'=>$title,'totaalhistorisch'=>$totaalhistorisch,'totaalactueel'=>$totaalactueel,'totaalReserveHerwaardering'=>$totaalReserveHerwaardering,
                                                           'totaalfiscaleWaardering'=>$totaalfiscaleWaardering ,'totaalAfboekenNaarLagereMarktwaarde'=>$totaalAfboekenNaarLagereMarktwaarde));


    $title=vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Fiscale waarde effecten",$this->pdf->rapport_taal);
    $this->printTotaal(array('title'=>$title,'totaalfiscaleWaardering'=>$fiscaleWaardeEffecten));



		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersStart." subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." subtotaalactueel FROM ".
		" TijdelijkeRapportage  ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
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
					" TijdelijkeRapportage.type = 'rente'   ".
				//	" AND TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
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
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['rentedatum']));
							if($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
								$rentePeriodetxt .= " / ".$subdata['renteperiode'];
						}

						$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

						$percentageTotaalTekst = "";



						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor[r],$this->pdf->rapport_fonds_fontcolor[g],$this->pdf->rapport_fonds_fontcolor[b]);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'].$rentePeriodetxt );

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						$this->pdf->row(array("","","","","","","",
														'',//$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->rapport_bedrag_decimalen),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->rapport_bedrag_decimalen),
														$percentageTotaalTekst));
				
		
					}

						$percentageVanTotaal = 0;
					//	$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, $percentageVanTotaal, "", "");

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
				}
			}

			$percentageVanTotaal = 0;

	
      $title=vertaalTekst("Subtotaal Opgelopen rente:",$this->pdf->rapport_taal);
      $actueleWaardePortefeuille += $this->printTotaal(array('title'=>$title,'totaalactueel'=>$totaalRenteInValuta));
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
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

				$percentageVanTotaalTekst = "";

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


										  $this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												'',//$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->rapport_bedrag_decimalen),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->rapport_bedrag_decimalen),
												$percentageVanTotaalTekst));
			
			}
		}



			$percentageVanTotaal = 0;

		// totaal liquiditeiten

    $title='';//vertaalTekst("Subtotaal Opgelopen rente:",$this->pdf->rapport_taal);
    $actueleWaardePortefeuille += $this->printTotaal(array('title'=>$title,'totaalactueel'=>$totaalLiquiditeitenEuro));

/**/
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



    
    $title=vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal);
    $this->printTotaal(array('title'=>$title,'totaalactueel'=>$actueleWaardePortefeuille,'grandTotaal'=>true));
    
    
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
		if($this->pdf->portefeuilledata['AEXVergelijking'] > 0 && $this->pdf->rapport_VHO_indexUit == 0)
		{
		  if(!$this->pdf->rapport_VHO_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

	}
}
?>
