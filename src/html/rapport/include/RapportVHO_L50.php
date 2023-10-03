<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
File Versie					: $Revision: 1.5 $

$Log: RapportVHO_L50.php,v $
Revision 1.5  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.4  2017/04/22 16:44:09  rvv
*** empty log message ***

Revision 1.3  2016/12/24 16:34:19  rvv
*** empty log message ***

Revision 1.2  2013/07/20 16:26:07  rvv
*** empty log message ***

Revision 1.1  2013/06/30 15:07:33  rvv
*** empty log message ***

Revision 1.11  2012/10/31 16:59:18  rvv
*** empty log message ***

Revision 1.10  2012/09/05 18:19:11  rvv
*** empty log message ***

Revision 1.9  2012/05/02 15:53:13  rvv
*** empty log message ***

Revision 1.8  2012/04/25 15:20:45  rvv
*** empty log message ***

Revision 1.7  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.6  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.5  2012/03/21 19:08:58  rvv
*** empty log message ***

Revision 1.4  2012/03/17 11:58:16  rvv
*** empty log message ***

Revision 1.3  2012/03/14 17:30:11  rvv
*** empty log message ***

Revision 1.2  2012/03/11 17:19:57  rvv
*** empty log message ***

Revision 1.1  2012/02/29 16:52:49  rvv
*** empty log message ***

Revision 1.29  2011/06/25 16:51:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L50
{
	function RapportVHO_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->underlinePercentage=0.8;
		$this->pdf->rapport_titel = "Portefeuille overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec,$nulNietTonen=false)
	{
	  if($waarde==0 && $nulNietTonen==true)
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
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
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
	
	function printTotaal($hcat,$cat,$totalen,$doubleLine=false)
  {
  
    if(strtolower($hcat) == "vastrentende waarden")
    {
      if ($cat == 'Liquiditeiten')
      {
        $this->pdf->CellBorders = array('', '', '', '', '', '', '', '', '', '', '', '', '','', array('TS'), array('TS'));
        $this->pdf->row(array('','Totaal '.$cat,'','','','','','','','','','','',$this->formatGetal($totalen['waardeEUR'],0,true),$this->formatGetal($totalen['percentage'],1,true).'%'));
      }
      else
      {
        if($doubleLine==true)
          $this->pdf->CellBorders = array('','','','','','','',array('UU'),array('UU'),array('UU'),array('UU'),array('UU'),array('UU'),array('UU'),array('UU'));
        else
          $this->pdf->CellBorders = array('','','','','','','',array('TS'),array('TS'),array('TS'),'',         array('TS'),array('TS'),array('TS'),array('TS'));
        $this->pdf->row(array('','Totaal '.$cat,'','','','','',
                          $this->formatGetal($totalen['aanschafEUR'],0,true),
                          $this->formatGetal($totalen['valutaresultaat']+$totalen['fondsResultaat'],0,true),
                          $this->formatGetal($totalen['rente'],0,true),'',
                          $this->formatGetal($totalen['ytm']/($totalen['percentage']/100),1,true),
                          $this->formatGetal($totalen['duration']/($totalen['percentage']/100),1,true),
                          $this->formatGetal($totalen['waardeEUR'],0,true),
                          $this->formatGetal($totalen['percentage'],1,true).'%'
                        ));
      }
    }
    else
    {
      if ($cat == 'Liquiditeiten')
      {
        $this->pdf->CellBorders = array('', '', '', '', '', '', '', '', '', '', '', '', array('TS'), array('TS'));
        $this->pdf->row(array('', 'Totaal ' . $cat, '', '', '', '', '', '', '', '', '', $this->formatGetal($totalen['waardeEUR'], 0, true), $this->formatGetal($totalen['percentage'], 1, true) . '%'));
      }
      else
      {
        $this->pdf->CellBorders = array('', '', '', '', '', '', '', array('TS'), array('TS',), array('TS'), array('TS'), array('TS'));
        $this->pdf->row(array('', 'Totaal ' . $cat, '', '', '', '', '',
                          $this->formatGetal($totalen['aanschafEUR'], 0, true),
                          $this->formatGetal($totalen['valutaresultaat'], 0, true),
                          $this->formatGetal($totalen['fondsResultaat'], 0, true),
                          $this->formatGetal($totalen['waardeEUR'], 0, true),
                          $this->formatGetal($totalen['percentage'], 1, true) . '%'));
      }
    }
    $this->pdf->CellBorders = array();
    $this->pdf->ln();
  }

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();
    
    //$this->pdf->widthA = array(1,65,   20,18,22,22,27,25,25,25,22);
		$this->pdf->widthA = array(1,63,12,20,18,22,22,27,25,25,25,22);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    
    //$this->pdf->widthB = array(1,60,   20,18,17,17,25,23,20,15,16,15,18,17);
    $this->pdf->widthB = array(1,60,12,15,18,17,17,22,22,18,15,16,15,18,15);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');


	  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
    $this->cashfow->genereerTransacties();
		$this->cashfow->genereerRows();


		$this->pdf->excelData[]=array('Naam',
			'rating',
			'valuta',
			'nominaal/aantal',
			"huidige koers",
			'Kostprijs',
			"aanschafwaarde in EUR",
			"ongerealiseerd valutaresultaat",
			"ongerealiseerd fondsresultaat",
			"opgelopen rente",
			"coupon datum",
			"eff. rendement",
			"duration",
			"marktwaarde",
			"% op totaal");



		$this->pdf->AddPage();
		$this->pdf->templateVars['VHOPaginas']=$this->pdf->page;

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


			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			TijdelijkeRapportage.hoofdcategorieOmschrijving,
			TijdelijkeRapportage.beleggingscategorieOmschrijving,
      TijdelijkeRapportage.hoofdcategorieVolgorde ,
      TijdelijkeRapportage.duurzaamCategorieOmschrijving ,
      TijdelijkeRapportage.`type`,
      TijdelijkeRapportage.rentedatum,
      TijdelijkeRapportage.renteperiode,
      if(Fondsen.OptieBovenliggendFonds <> '',Fondsen.OptieBovenliggendFonds,TijdelijkeRapportage.Fonds) as fondsVolgorde,
      Rating.omschrijving as Rating,
      TijdelijkeRapportage.Lossingsdatum
      FROM TijdelijkeRapportage
      LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
      Left Join Rating ON (Fondsen.rating = Rating.rating )
			WHERE TijdelijkeRapportage.`type` <> 'rente' AND".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.fondsOmschrijving";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

			$renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));
			while($subdata = $DB2->NextRecord())
			{
			  $DB->SQL("SELECT actuelePortefeuilleWaardeEuro,fonds FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.`type` =  'rente' AND TijdelijkeRapportage.fonds='".$subdata['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']);
  			$rente=$DB->lookupRecord();


        if($subdata['rentedatum'] <> '0000-00-00')
        {
    			$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['rentedatum']));
	  			if($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
		  			$rentePeriodetxt .= " / ".$subdata['renteperiode'];
        }
        else
          $rentePeriodetxt='';

          $lossingsJul = adodb_db2jul($subdata['Lossingsdatum']);
          $duration='';
          $ytm='';
        if($lossingsJul > 0)
	      {
					$koers=getRentePercentage($subdata['fonds'],$this->rapportageDatum);

	        //$this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        //$this->lossingsWaardeTotaal += $fonds['totaalAantal'] * 100 * $fonds['fondsEenheid'] * $fonds['actueleValuta'];
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $subdata['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$subdata['fonds']]['lossingskoers'];
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         //listarray($this->cashfow->waardePerFonds);
	         $duration=$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaarde'];
	         //echo $subdata['fonds']." $duration= ".$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaardeJaar']."/".$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaarde']."<br>\n";
	         $modifiedDuration=$duration/(1+$ytm/100);
	         $aandeel=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde;

           $totalen[$subdata['beleggingscategorieOmschrijving']]['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['ytm']+=$ytm*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['duration']+=$duration*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['restLooptijd']+=$restLooptijd*$aandeel;

	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['ytm']+=$ytm*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['duration']+=$duration*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['restLooptijd']+=$restLooptijd*$aandeel;

        }

			  if($subdata['hoofdcategorieOmschrijving']=='')
			    $subdata['hoofdcategorieOmschrijving']='Geen categorie';
			  if($subdata['beleggingscategorieOmschrijving']=='')
			    $subdata['beleggingscategorieOmschrijving']='Geen categorie';
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['historischeWaardeTotaal']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['historischeWaardeTotaal']) * 100;

				if($subdata['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta']) / ($subdata['historischeWaardeTotaalValuta'] /100));
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($subdata['historischeWaardeTotaalValuta'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);

				if($gecombeneerdResultaat <> 0)
				  $gecombeneerdResultaattxt = $this->formatGetal($gecombeneerdResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);

				if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
						$fondsResultaatprocenttxt = "p.m.";
				else
						$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

					$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";

				$waardePerFonds[$subdata['fondsOmschrijving']]=$subdata['actuelePortefeuilleWaardeEuro'];

					if($lastCategorie <> $subdata['beleggingscategorieOmschrijving'])
					{
					  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
					  if($lastCategorie <> '')
					  {
               $this->printTotaal($this->pdf->Hcat,$lastCategorie,$totalen[$lastCategorie]);
  			     }
  			    // $this->pdf->CellBorders =array();
				   // $this->pdf->ln();
					}

					if($lastHCategorie <> $subdata['hoofdcategorieOmschrijving'])
					{
					  $this->pdf->Hcat=$subdata['hoofdcategorieOmschrijving'];
					  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
					  if($lastHCategorie <> '')
					  {
              $this->printTotaal($lastHCategorie,$lastHCategorie,$totalenH[$lastHCategorie],true);
  				 //    $this->pdf->CellBorders =array();
					    $this->pdf->addPage();
					  }
					}

					if($lastHCategorie <> $subdata['hoofdcategorieOmschrijving'])
					   $this->pdf->row(array('',$subdata['hoofdcategorieOmschrijving']));

					if($lastCategorie <> $subdata['beleggingscategorieOmschrijving'])
					   $this->pdf->row(array('',$subdata['beleggingscategorieOmschrijving']));

					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

          if($subdata['type'] <> 'rekening')
            $verdeling[$subdata['hoofdcategorieOmschrijving']][$percentageVanTotaal*10000][]=$subdata['fondsOmschrijving'];


				  if(strtolower($this->pdf->Hcat) == "vastrentende waarden")
          {
             if($subdata['type']=='rekening')
             {
              $this->pdf->row(array('',$subdata['fondsOmschrijving'],'','','','','','','','','','','',$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),$percentageTotaalTekst));
              $fondsResultaat=0;
             }
             else
             {
                 $this->pdf->row(array('',$subdata['fondsOmschrijving'],
												$subdata['duurzaamCategorieOmschrijving'],
												$subdata['Rating'],
												$this->formatGetal($subdata['totaalAantal'],0,true),
												$this->formatGetal($subdata['actueleFonds'],2,true),
												$this->formatGetal($subdata['historischeWaarde'],2,true),
												$this->formatGetal($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal,true),
												$gecombeneerdResultaattxt,
												$this->formatGetal($rente['actuelePortefeuilleWaardeEuro'],0,true),
												$rentePeriodetxt,
												$this->formatGetal($ytm,1,true),
								        $this->formatGetal($duration,1,true),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal,true),
												$percentageTotaalTekst));
							 $this->pdf->excelData[]=array($subdata['fondsOmschrijving'],$subdata['Rating'],$subdata['valuta'],round($subdata['totaalAantal'],6),round($subdata['actueleFonds'],2),round($subdata['historischeWaarde'],2),round($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal),$valutaResultaat,$fondsResultaat,round($rente['actuelePortefeuilleWaardeEuro'],0),$rentePeriodetxt,round($ytm,1),round($duration,1),round($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),round($percentageVanTotaal,2));
              }
          }
          else
          {
            if($subdata['type']=='rekening')
            {
              $this->pdf->row(array('',$subdata['fondsOmschrijving'],'','','','','','','',$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),$percentageTotaalTekst));
              $fondsResultaat=0;
							$this->pdf->excelData[]=array($subdata['fondsOmschrijving'],$subdata['duurzaamCategorieOmschrijving'],$subdata['Rating'],$subdata['valuta'],round($subdata['totaalAantal'],6),round($subdata['actueleFonds'],2),round($subdata['historischeWaarde'],2),round($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal),$valutaResultaat,$fondsResultaat,round($rente['actuelePortefeuilleWaardeEuro'],0),$rentePeriodetxt,round($ytm,1),round($duration,1),round($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),round($percentageVanTotaal,2));
            }
            else
            {
               $this->pdf->row(array('',$subdata['fondsOmschrijving'],
                                 $subdata['duurzaamCategorieOmschrijving'],
												$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_VHO_aantalVierDecimaal,true),
												$subdata['valuta'],
												$this->formatGetal($subdata['actueleFonds'],2,true),
												$this->formatGetal($subdata['historischeWaarde'],2,true),
												$this->formatGetal($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal,true),
												$valutaResultaattxt,
												$fondsResultaattxt,
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal,true),
												$percentageTotaalTekst)
												);
							$this->pdf->excelData[]=array($subdata['fondsOmschrijving'],$subdata['Rating'],$subdata['valuta'],round($subdata['totaalAantal'],6),round($subdata['actueleFonds'],2),round($subdata['historischeWaarde'],2),round($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal),$valutaResultaat,$fondsResultaat,round($rente['actuelePortefeuilleWaardeEuro'],0),$rentePeriodetxt,round($ytm,1),round($duration,1),round($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),round($percentageVanTotaal,2));
            }
          }


					$totalenH[$subdata['hoofdcategorieOmschrijving']]['aanschafEUR']+=$subdata['historischeWaardeTotaalValuta'];
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['valutaresultaat']+=$valutaResultaat;
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['fondsResultaat']+=$fondsResultaat;
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['rente']+=$rente['actuelePortefeuilleWaardeEuro'];
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['waardeEUR']+=$subdata['actuelePortefeuilleWaardeEuro'];
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['percentage']+=$percentageVanTotaal;

					$totalen[$subdata['beleggingscategorieOmschrijving']]['aanschafEUR']+=$subdata['historischeWaardeTotaalValuta'];
					$totalen[$subdata['beleggingscategorieOmschrijving']]['valutaresultaat']+=$valutaResultaat;
					$totalen[$subdata['beleggingscategorieOmschrijving']]['fondsResultaat']+=$fondsResultaat;
					$totalen[$subdata['beleggingscategorieOmschrijving']]['rente']+=$rente['actuelePortefeuilleWaardeEuro'];
					$totalen[$subdata['beleggingscategorieOmschrijving']]['waardeEUR']+=$subdata['actuelePortefeuilleWaardeEuro'];
					$totalen[$subdata['beleggingscategorieOmschrijving']]['percentage']+=$percentageVanTotaal;
					$lastHCategorie=$subdata['hoofdcategorieOmschrijving'];
					$lastCategorie=$subdata['beleggingscategorieOmschrijving'];
	  }

	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

	   
    $this->printTotaal($this->pdf->Hcat,$lastCategorie,$totalen[$lastCategorie]);


    $this->printTotaal($this->pdf->Hcat,$lastHCategorie,$totalenH[$lastHCategorie],true);
		
		
					     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->rapport_titel = "De 10 grootste posities per categorie";

    $this->pdf->addPage();
    $this->pdf->templateVars['VHO2Paginas']=$this->pdf->page;
    $this->pdf->ln();
    $offset=75;
    $posY=$this->pdf->getY();
		foreach ($verdeling as $hcat=>$percentageData)
		{

		  $this->pdf->SetWidths(array($offset+1,65,25,25));

		  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		  $this->pdf->row(array('',$hcat,'Absoluut','in %'));
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  ksort($percentageData);
		  $percentageData=array_reverse($percentageData,true);

		  $n=0;
	    foreach ($percentageData as $percentage=>$waarden)
		  {
		    foreach ($waarden as $fonds)
		  	{
		  	  $this->pdf->row(array('',$fonds,$this->formatGetal($waardePerFonds[$fonds]),$this->formatGetal($percentage/10000,1)));
		  		$n++;
		  	}
		  	if($n>9)
		  	  break;
	    }
      $this->pdf->ln(10);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  //    $offset+=130;
  //    $this->pdf->setY($posY);
		}

		if(isset($this->pdf->__appvar['consolidatie']))
		{
      foreach ($this->pdf->portefeuilles as $portefeuille)
      {
        if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;
        $waarden=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,$startjaar,$this->rapportageDatumVanaf);
        foreach ($waarden as $waarde)
        {
          $portefeuilleWaarden[$portefeuille]+=$waarde['actuelePortefeuilleWaardeEuro'];
        }
      }
      foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
        $portefeuilleAandeel[$portefeuille]=$waarde/$totaalWaarde*100;

 		  $this->pdf->rapport_titel = "Waarde verdeling portefeuilles";
      $this->pdf->addPage();
      $this->pdf->templateVars['VHO3Paginas']=$this->pdf->page;
      $this->pdf->setXY(50,50);
      $this->pdf->PieChart(50, 50,$portefeuilleAandeel, '%l (%p)',array(array(29,29,255),array(79,148,205),array(156,199,85),array(190,115,94),array(147,108,176)));

      $this->pdf->setXY($this->pdf->marge,50);
      $this->pdf->SetWidths(array(120,40,40,25));
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		  $this->pdf->row(array('','Portefeuille','Waarde EUR','in %'));
		  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
		  {
		     $this->pdf->row(array('',$portefeuille,$this->formatGetal($waarde,0),$this->formatGetal($portefeuilleAandeel[$portefeuille],1)));
		 	}
		}



	}



  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->pdf->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $w + $radius ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag -($radius) + $margin;

      for($i=0; $i<$this->pdf->NbVal; $i++)
      {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $margin;
      }

  }
}
?>