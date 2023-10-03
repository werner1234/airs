<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/10/15 15:22:13 $
File Versie					: $Revision: 1.8 $

$Log: RapportVOLK_L53.php,v $
Revision 1.8  2015/10/15 15:22:13  rvv
*** empty log message ***

Revision 1.7  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.6  2014/11/08 18:37:31  rvv
*** empty log message ***

Revision 1.5  2014/10/19 08:52:15  rvv
*** empty log message ***

Revision 1.4  2014/06/18 15:48:59  rvv
*** empty log message ***

Revision 1.3  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.2  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.1  2014/02/02 10:49:59  rvv
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

class RapportVOLK_L53
{
	function RapportVOLK_L53($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	  $this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
	  $this->pdf->Cell($writerow,4,$data, 0,0, "R");
		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{

			if($type == "grandtotaal")
			{
			 			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
      
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
			else if($type == "totaal")
			{
			//	$this->pdf->setDash(1,1);
			//	$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}

		}
		$this->pdf->setY($y);
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

		$this->pdf->ln();

		if($grandtotaal == true)
			$grandtotaal = "grandtotaal";
		else
			$grandtotaal = "totaal";

//
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->printCol(3,$title,"tekst");
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
			if($totaalB <>0)
				$this->printCol(6,$this->formatGetal($totaalB,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalA <>0)
				$this->printCol(4,$this->formatGetal($totaalA,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
			if($totaalC <>0)
				$this->printCol(9,$this->formatGetal($totaalC,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
			if($totaalD <>0)
				$this->printCol(7,$this->formatGetal($totaalD,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
        /*
			if($totaalE <>0)
				$this->printCol(13,$this->formatGetal($totaalE,$this->pdf->rapport_VOLK_decimaal),$grandtotaal);
        */
			if($totaalF <>0)
				$this->printCol(8,$this->formatGetal($totaalF,$this->pdf->rapport_VOLK_decimaal_proc)." %",$grandtotaal);
        
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

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


			$fondsresultwidth = 5;
			$omschrijvingExtra = 10;


			$this->pdf->widthB = array(10,50+$omschrijvingExtra,25,25,28,25,28,30,20,25);
			$this->pdf->alignB = array('R','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
			$this->pdf->widthA = array(60+$omschrijvingExtra,25,25,28,25,28,30,20,25);
			$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->AddPage();
    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;

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
 
    if(isset($this->pdf->__appvar['consolidatie']))
      $consolidatieFilter='AND Rekeningen.consolidatie=1';
    else
      $consolidatieFilter='';  
  
			$query = "SELECT TijdelijkeRapportage.hoofdcategorie,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.hoofdcategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorieOmschrijving AS Omschrijving,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.type,
TijdelijkeRapportage.rekening,
Rekeningen.Tenaamstelling,
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
      "LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening=Rekeningen.rekening $consolidatieFilter ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type IN('fondsen','rekening') AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.type, TijdelijkeRapportage.hoofdcategorieVolgorde asc, 
      TijdelijkeRapportage.beleggingscategorieVolgorde asc, 
      TijdelijkeRapportage.fondsOmschrijving asc";


		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
    $DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();

  $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]);

		while($fonds = $DB->NextRecord())
		{
		  if( $fonds['hoofdcategorieOmschrijving'] == '')
		    $fonds['hoofdcategorieOmschrijving'] ='Geen hoofdcategorie';
		  if($fonds['Omschrijving']=='')
		    $fonds['Omschrijving']='Geen categorie';
		  //if($fonds['beleggingssectorOmschrijving']=='')
		    $fonds['beleggingssectorOmschrijving']='';//'Geen sector';

      if($fill==true)
      {
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
        $fill=false;
      }
      else
      {
        $this->pdf->fillCell=array();
         $fill=true;
      }

			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $fonds['Omschrijving'] && !empty($lastCategorie) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
        $procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		    if($totaalbegin < 0)
					$procentResultaat = -1 * $procentResultaat;

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

			if($lastHCategorie <> $fonds['hoofdcategorieOmschrijving'])
			{
		//         
		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'  AND hoofdcategorieOmschrijving='$lastHCategorie'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query,__FILE__,__LINE__);
		$DB2->SQL($query);
		$DB2->Query();
  	if($DB2->records() > 0)
		{
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
			$totaalRenteInValuta = 0 ;
			while($categorien = $DB2->NextRecord())
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
      $hcatTotaal['actuelePortefeuilleWaardeEuro'] +=$totaalRenteInValuta;
      $hcatTotaal['percentageVanTotaal'] +=$subtotaalPercentageVanTotaal;
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}         
  //          

        if(isset($lastHCategorie))
        {
          
          $procentResultaat = (($hcatTotaal['actuelePortefeuilleWaardeEuro'] - $hcatTotaal['beginPortefeuilleWaardeEuro']) / ($hcatTotaal['beginPortefeuilleWaardeEuro'] /100));
				  if($hcatTotaal['beginPortefeuilleWaardeEuro'] < 0)
					  $procentResultaat = -1 * $procentResultaat;
          
          $this->pdf->SetTextColor(255,255,255);
          $this->pdf->SetFillColor(127); 
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
   				$this->pdf->row(array("",'','','',
                          $this->formatGetal($hcatTotaal['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),'',
													$this->formatGetal($hcatTotaal['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($hcatTotaal['fondsResultaat'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal)." %",
													$this->formatGetal($hcatTotaal['percentageVanTotaal'],$this->pdf->rapport_VOLK_decimaal)." %"	)
													);
           $this->pdf->fillCell = array();  
           
          $this->pdf->Ln($this->pdf->rowHeight*-1);                  
          $this->pdf->Cell($this->pdf->widthB[1],4,'Totaal '.$lastHCategorie,null,1,null,null,null);   
          $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['R']);                      
          $hcatTotaal=array();
          $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]); 
          $this->pdf->Ln();
          }
					$this->printKop(vertaalTekst($fonds['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal), "bi");
			}

			if($lastCategorie <> $fonds['Omschrijving'])
			{
					$this->printKop(vertaalTekst('    '.$fonds['Omschrijving'],$this->pdf->rapport_taal), "b");
			}
			if($lastSector <> $fonds['beleggingssectorOmschrijving'])
			{
					$this->printKop(vertaalTekst('       '.$fonds['beleggingssectorOmschrijving'],$this->pdf->rapport_taal), "b");
			}



				$fondsResultaat = ($fonds[actuelePortefeuilleWaardeInValuta] - $fonds[beginPortefeuilleWaardeInValuta]) * $fonds[actueleValuta] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $fonds[beginPortefeuilleWaardeEuro]) * 100;
				$valutaResultaat = $fonds[actuelePortefeuilleWaardeEuro] - $fonds[beginPortefeuilleWaardeEuro] - $fondsResultaat;

				$procentResultaat = (($fonds[actuelePortefeuilleWaardeEuro] - $fonds[beginPortefeuilleWaardeEuro]) / ($fonds[beginPortefeuilleWaardeEuro] /100));
				if($fonds[beginPortefeuilleWaardeEuro] < 0)
					$procentResultaat = -1 * $procentResultaat;

				$percentageVanTotaal = ($fonds[actuelePortefeuilleWaardeEuro]) / ($totaalWaarde/100);
        
       // echo $fonds['fondsOmschrijving']." $percentageVanTotaal = (".$fonds[actuelePortefeuilleWaardeEuro].") / ($totaalWaarde/100) <br>\n";
        
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VOLK_decimaal_proc)." %";


				$fondsResultaattxt = "";
				$valutaResultaattxt = "";

				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VOLK_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VOLK_decimaal);

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        if($fonds['type']=='rekening')
        {
          if($data['Tenaamstelling']<>'')
            $omschrijving=$fonds['Tenaamstelling'];
          else
            $omschrijving=$fonds['rekening'];
            
          $fondsResultaat=0;
          $this->pdf->row(array("",$omschrijving,
													'','','','',
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													'','',
													$percentageVanTotaaltxt	)
													);         
        }
        else
          $this->pdf->row(array("",$fonds['fondsOmschrijving'],
													$this->formatAantal($fonds['totaalAantal'],$this->pdf->rapport_VOLK_aantal_decimaal,$this->pdf->rapport_VOLK_aantalVierDecimaal),
													$fonds['valuta'],
													$this->formatGetal($fonds['beginPortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($fonds['actueleFonds'],2),
													$this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$fondsResultaattxt,
													$procentResultaattxt,
													$percentageVanTotaaltxt	)
													);
      //  $this->pdf->Ln($this->pdf->rowHeight*-1);                  
      //  $this->pdf->Cell($this->pdf->widthB[1],4,,null,1,null,null,null);

				$valutaWaarden[$categorien['valuta']] = $fonds['actueleValuta'];

				$hcatTotaal['percentageVanTotaal'] +=$percentageVanTotaal;
				$hcatTotaal['fondsResultaat'] +=$fondsResultaat;
				$hcatTotaal['valutaResultaat'] +=$valutaResultaat;
				$hcatTotaal['totaalResultaat'] +=$subTotaalResultaat;
				$hcatTotaal['totaalBijdrage'] += $subTotaalBijdrage;
        $hcatTotaal['beginPortefeuilleWaardeEuro'] +=$fonds['beginPortefeuilleWaardeEuro'];
        $hcatTotaal['actuelePortefeuilleWaardeEuro'] +=$fonds['actuelePortefeuilleWaardeEuro'];


			// totaal op categorie tellen
			$totaalbegin   += $fonds['beginPortefeuilleWaardeEuro'];
			$totaalactueel += $fonds['actuelePortefeuilleWaardeEuro'];

			$totaalfondsresultaat  += $fondsResultaat;
			$totaalvalutaresultaat += $valutaResultaat;
      
			$totaalpercentage      += $percentageVanTotaal; 

			$lastCategorie = $fonds['Omschrijving'];
			$lastHCategorie = $fonds['hoofdcategorieOmschrijving'];
			$lastSector = '';//$fonds['beleggingssectorOmschrijving'];


			$grandtotaalvaluta += $valutaResultaat;
			$grandtotaalfonds  += $fondsResultaat;

			$totaalResultaat +=	$subTotaalResultaat ;
			$totaalBijdrage  += $subTotaalBijdrage ;
			$grandtotaalResultaat  +=	$subTotaalResultaat ;
			$grandtotaalBijdrage   += $subTotaalBijdrage ;

			$subtotaal = array();
		}

		$procentResultaat = (($totaalactueel - $totaalbegin) / ($totaalbegin /100));
		if($totaalbegin < 0)
			$procentResultaat = -1 * $procentResultaat;

		// totaal voor de laatste categorie
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$totaalpercentage,$totaalfondsresultaat,$totaalvalutaresultaat,$procentResultaat);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. " as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as subtotaalactueel FROM ".
		" TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'  AND hoofdcategorieOmschrijving='$lastHCategorie'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde ";
		debugSpecial($query,__FILE__,__LINE__);
		$DB2->SQL($query);
		$DB2->Query();
  	if($DB2->records() > 0)
		{
			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal), "bi");
			$totaalRenteInValuta = 0 ;
			while($categorien = $DB2->NextRecord())
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
			$subtotaalPercentageVanTotaal  = ($totaalRenteInValuta) / ($totaalWaarde/100);
      $hcatTotaal['actuelePortefeuilleWaardeEuro'] +=$totaalRenteInValuta;
      $hcatTotaal['percentageVanTotaal'] +=$subtotaalPercentageVanTotaal;
			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$subtotaalPercentageVanTotaal,"","");
		}


        if(isset($lastHCategorie))
        {
          $this->pdf->SetTextColor(255,255,255);
          $this->pdf->SetFillColor(127); 
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
   				$this->pdf->row(array("",'','','','','',
													$this->formatGetal($hcatTotaal['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													$this->formatGetal($hcatTotaal['fondsResultaat'],$this->pdf->rapport_VOLK_decimaal),
													'',
													$this->formatGetal($hcatTotaal['percentageVanTotaal'],$this->pdf->rapport_VOLK_decimaal)."%")
													);
           $this->pdf->fillCell = array();   
                  
           $this->pdf->Ln($this->pdf->rowHeight*-1);                  
          $this->pdf->Cell($this->pdf->widthB[1],4,'Totaal '.$lastHCategorie,null,1,null,null,null);   
          $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['R']);                      
          $hcatTotaal=array();
          $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]); 
          $this->pdf->Ln();
          unset($lastHCategorie);
          }
		// Liquiditeiten
/*
		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			Rekeningen.Deposito, Rekeningen.Termijnrekening, Rekeningen.Memoriaal, Rekeningen.Tenaamstelling".
			" FROM TijdelijkeRapportage JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB1 = new DB();
		//$DB1->SQL($query);
		//$DB1->Query();
    $lastHCategorie='Liquiditeiten';
		if($DB1->records() > 0)
		{
			$totaalLiquiditeitenInValuta = 0;
			$this->printKop(vertaalTekst($lastHCategorie,$this->pdf->rapport_taal), "bi");
			while($data = $DB1->NextRecord())
			{
			  $liqiteitenBuffer[] = $data;
			}
			foreach($liqiteitenBuffer as $data)
			{
			  if($data['Tenaamstelling']<>'')
          $omschrijving=$data['Tenaamstelling'];
        else
          $omschrijving=$data['rekening'];
		
				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];
				$subtotaalPercentageVanTotaal  = ($data['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				$subtotaalPercentageVanTotaaltxt = $this->formatGetal($subtotaalPercentageVanTotaal,$this->pdf->rapport_VOLK_decimaal_proc)." %";
  			$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);
				$this->pdf->setX($this->pdf->marge);
				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",$omschrijving,"","","","",
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),'','',
												$subtotaalPercentageVanTotaaltxt));
        $hcatTotaal['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
        $hcatTotaal['percentageVanTotaal']+=$subtotaalPercentageVanTotaal;
			}

			$subtotaalPercentageVanTotaal  = ($totaalLiquiditeitenEuro) / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $hcatTotaal['actuelePortefeuilleWaardeEuro'];//$this->printTotaal("", "", $totaalLiquiditeitenEuro,$subtotaalPercentageVanTotaal,"","");
		} 
    
    */
      if(isset($lastHCategorie))
        {
          $this->pdf->Ln();
          $this->pdf->SetTextColor(255,255,255);
          $this->pdf->SetFillColor(127); 
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
   				$this->pdf->row(array("",'','','','','',
													$this->formatGetal($hcatTotaal['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VOLK_decimaal),
													'',
													'',
													$this->formatGetal($hcatTotaal['percentageVanTotaal'],$this->pdf->rapport_VOLK_decimaal)."%")
													);
           $this->pdf->fillCell = array();   
                  
           $this->pdf->Ln($this->pdf->rowHeight*-1);                  
          $this->pdf->Cell($this->pdf->widthB[1],4,'Totaal '.$lastHCategorie,null,1,null,null,null);   
          $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['R']);                      
          $hcatTotaal=array();
          $this->pdf->SetFillColor($this->pdf->rapport_regelKleur[0],$this->pdf->rapport_regelKleur[1],$this->pdf->rapport_regelKleur[2]); 
          $this->pdf->Ln();
          unset($lastHCategorie);
          }

		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,$grandtotaalfonds,$grandtotaalvaluta,"",true);
		$this->pdf->ln();




$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);





	}
}
?>
