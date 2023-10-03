<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/28 09:58:52 $
 		File Versie					: $Revision: 1.7 $

 		$Log: RapportVOLK_L18.php,v $
 		Revision 1.7  2017/05/28 09:58:52  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/07/12 15:30:53  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/05/14 15:28:41  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/09/25 16:23:28  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/29 16:52:23  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.12  2011/04/19 16:41:39  rvv
 		*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVOLK_L18
{
	function RapportVOLK_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->rapportageJaar = date("Y",$this->pdf->rapport_datum);

		$this->pdf->rapport_titel = "Obligaties & Vastrentende Waarden";
		$this->pdf->rapport_header = array('','Munt',"Bedrag/\nAantal","Omschrijving","Aankoop\nPrijs","Prijs Ultimo\n".($this->rapportageJaar-1),"Marktprijs\nin Valuta","Marktwaarde\nin EUR","Opgelopen\nRente in EUR","% Vermogen");

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}


	function formatGetal($waarde, $dec,$procent = false)
	{
	  if($waarde <> 0)
	  {
		  $waarde = number_format($waarde,$dec,",",".");
		  if($procent == true)
		    $waarde .= " %";
		    return $waarde;
	  }

	}

	function shrinkString($string,$maxSize)
	{
    $maxSize=$maxSize-2-ceil($this->pdf->CurrentFont['cw']['.']*$this->pdf->FontSize/1000*3);
	  $stringWidth=$this->pdf->GetStringWidth($string);
	  if($stringWidth > $maxSize)
	  {
	    $cw=&$this->pdf->CurrentFont['cw'];
      $l=strlen($string);
	      $w=0;
	      for($i=0;$i<$l;$i++)
	      {
		       $w+=$cw[$string{$i}];
		       if(($w*$this->pdf->FontSize/1000) < $maxSize)
		         $newString.=$string{$i};
	      }
	      return $newString."...";
	  }
	  else
	    return   $string;
	}

	function subTotaal()
	{
/*
    $backupWidths = $this->pdf->widths;
	  $this->pdf->switchFont('rodelijn');
	  $this->pdf->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
	  if($this->pdf->rapport_deel == 'AAND')
	  {

	    $this->pdf->widths = array(15,17+17+71,20,26,20,18,18,28,20);//array(32,12+20+56,20,20,20,20,28,20);
	    $this->pdf->Row(array('','Subtotaal','','','','','',$this->formatGetal($this->totalen['marktwaarde']),$this->formatGetal($this->totalen['percentage'],1)." %"));
	  }
	  else
	  {
	    $this->pdf->widths = array(15,15+22+72,21,26,21,28,24,26);//array(32,12+20+56,25,25,25,28,25);
  	  $this->pdf->Row(array('','Subtotaal','','','',$this->formatGetal($this->totalen['marktwaarde']),$this->formatGetal($this->totalen['opgelopenRente']),$this->formatGetal($this->totalen['percentage'],1)." %"));
	  }
    */
	  $this->gTotaal['marktwaarde'] += $this->totalen['marktwaarde'];
	  $this->gTotaal['opgelopenRente'] += $this->totalen['opgelopenRente'];
	  $this->gTotaal['percentage'] += $this->totalen['percentage'];
	  $this->totalen = array();

	// $this->pdf->widths = $backupWidths;
	// $this->aligns = $backupAligns;

	}

	function gTotaal()
	{
	  $backupWidths = $this->pdf->widths;
	  $this->pdf->switchFont('rodelijn');
	  $this->pdf->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
	  if($this->pdf->rapport_deel == 'AAND')
	  {
	    $this->pdf->widths = array(15,17+17+71,20,26,20,18,18,28,20);//array(32,12+20+56,20,20,20,20,28,20);
	    $this->pdf->Row(array('','Totaal','','','','','',$this->formatGetal($this->gTotaal['marktwaarde']),$this->formatGetal($this->gTotaal['percentage'],1)." %"));
	  }
	  else
	  {
	    $this->pdf->widths = array(15,15+22+72,21,26,21,28,24,26);
	    $this->pdf->Row(array('','Totaal','','','',$this->formatGetal($this->gTotaal['marktwaarde']),$this->formatGetal($this->gTotaal['opgelopenRente']),$this->formatGetal($this->gTotaal['percentage'],1)." %"));
	  }
    $this->pdf->switchFont('fondsLaag');
    $this->gTotaal=array();
    $this->pdf->widths = $backupWidths;
	}

	function SectorTotaal($sector='Geen sector')
	{
	 /*
	  $backupWidths = $this->pdf->widths;
	  $this->pdf->switchFont('rodelijn');
	  $this->pdf->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
	  if($this->pdf->rapport_deel == 'AAND')
	  {
	    $this->pdf->widths = array(15,17+17+71,20,26,20,18,18,28,20);//array(32,12+20+56,20,20,20,20,28,20);
	    if($this->sTotaal['marktwaarde'] <> 0)
  	    $this->pdf->Row(array('','Sub Totaal','','','','','',$this->formatGetal($this->sTotaal['marktwaarde']),$this->formatGetal($this->sTotaal['percentage'],1)." %"));

  	    $this->pdf->SetDrawColor($this->pdf->rapport_style['fonds']['line']['color'][0],$this->pdf->rapport_style['fonds']['line']['color'][0],$this->pdf->rapport_style['fonds']['line']['color'][2]);

  	  $this->pdf->Row(array('',$sector,'','','','','','',''));

	  }
	  else
	  {
	    $this->pdf->widths = array(15,15+22+72,21,26,21,28,24,26);//array(32,12+20+56,25,25,25,28,25);
	    if($this->sTotaal['marktwaarde'] <> 0)
         $this->pdf->Row(array('','Sub Totaal','','','',$this->formatGetal($this->gTotaal['marktwaarde']),$this->formatGetal($this->gTotaal['opgelopenRente']),$this->formatGetal($this->gTotaal['percentage'],1)." %"));

       $this->pdf->SetDrawColor($this->pdf->rapport_style['fonds']['line']['color'][0],$this->pdf->rapport_style['fonds']['line']['color'][0],$this->pdf->rapport_style['fonds']['line']['color'][2]);
     $this->pdf->Row(array('',$sector,'','','','','',''));
 	  }
    $this->pdf->switchFont('fonds');
    $this->sTotaal=array();
    $this->pdf->widths = $backupWidths;
    */
	}



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();
    $DB2= new DB();
    
    $this->paginaHoogte=165;


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

	$query ="
	SELECT

	TijdelijkeRapportage.fondsOmschrijving,
  TijdelijkeRapportage.beleggingscategorie,
  TijdelijkeRapportage.valuta,
  TijdelijkeRapportage.type,
  TijdelijkeRapportage.fonds,
	TijdelijkeRapportage.actueleValuta,
	TijdelijkeRapportage.totaalAantal,
	TijdelijkeRapportage.historischeWaarde,
	TijdelijkeRapportage.historischeValutakoers,
	TijdelijkeRapportage.fondsEenheid,
	(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
	(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta,
	TijdelijkeRapportage.beginwaardeLopendeJaar,
	TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
	TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
	TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /1 as actuelePortefeuilleWaardeEuro,
	TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille
	FROM TijdelijkeRapportage
	WHERE
	TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
	(TijdelijkeRapportage.type = 'fondsen'  OR TijdelijkeRapportage.type = 'rente'  ) AND
	TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
	".$__appvar['TijdelijkeRapportageMaakUniek']."
	ORDER BY TijdelijkeRapportage.fondsOmschrijving asc, TijdelijkeRapportage.totaalAantal
	";

	$DB->SQL($query);
	$DB->Query();
	while ($dbdata=$DB->nextRecord())
	{
	  if($dbdata['type']=='fondsen')
	  {
	    $fondsResultaat = ($dbdata['actuelePortefeuilleWaardeInValuta'] - $dbdata['historischeWaardeTotaal']) * $dbdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
			$fondsResultaatprocent = ($fondsResultaat / $dbdata['historischeWaardeTotaal']) * 100;
			if($subdata['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
			  $fondsResultaatprocent = -1 * $fondsResultaatprocent;
			$valutaResultaat = $dbdata['actuelePortefeuilleWaardeEuro'] - $dbdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
			$procentResultaat = (($dbdata['actuelePortefeuilleWaardeEuro'] - $dbdata['historischeWaardeTotaalValuta']) / ($dbdata['historischeWaardeTotaalValuta'] /100));
      $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;
			if($dbdata['historischeWaardeTotaalValuta'] < 0)
				$procentResultaat = -1 * $procentResultaat;

			if($procentResultaat > 1000 || $procentResultaat < -1000)
				$procentResultaattxt = "p.m.";
			else
				$procentResultaattxt = $this->formatGetal($procentResultaat,1);

		  $query = "SELECT Rekeningmutaties.id FROM Rekeningmutaties, Rekeningen WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Boekdatum = '".$this->rapportageJaar."-01-01' AND Rekeningmutaties.fonds = '".$dbdata['fonds']."' AND Rekeningen.Portefeuille = '".$this->portefeuille."'";
			$DB2->SQL($query);
			$DB2->Query();
			if($DB2->records() > 0)
			{
        $query="SELECT Fondskoersen.Koers FROM Fondskoersen WHERE Fondskoersen.Fonds = '".$dbdata['fonds']."'
                AND Fondskoersen.Datum <  '".$this->rapportageJaar."-01-01"."'  ORDER BY Fondskoersen.Datum DESC LIMIT 1 ";
			  $DB2->SQL($query);
			  $koers = $DB2->lookupRecord();
			  $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['prijsUltimo']=$koers['Koers'];//$dbdata['beginwaardeLopendeJaar'];
			}

	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['sector']=$dbdata['beleggingssector'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['sectorOmschrijving']=$dbdata['sectorOmschrijving'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['munt']=$dbdata['valuta'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['aantal']=$dbdata['totaalAantal'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['omschrijving']=$dbdata['fondsOmschrijving'];//substr('',0,25);
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['aankoopprijs']=$dbdata['historischeWaarde'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['marktprijs']=$dbdata['actueleFonds']* $dbdata['actueleValuta'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['marktprijsValuta']=$dbdata['actueleFonds'];

	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['marktwaarde']=$dbdata['actuelePortefeuilleWaardeEuro'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['beginkoers']=$dbdata['beginwaardeLopendeJaar'];
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['winst']=($dbdata['actuelePortefeuilleWaardeEuro']-$dbdata['beginPortefeuilleWaardeEuro']);
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['winstpercentage']=$procentResultaattxt;

	    $totaalWaardeCategorie[$dbdata['beleggingscategorie']] += $dbdata['actuelePortefeuilleWaardeEuro'];
	  }

	  if($dbdata['type']=='rente')
	  {
	    $data[$dbdata['beleggingscategorie']][$dbdata['fonds']]['opgelopenRente']=$dbdata['actuelePortefeuilleWaardeEuro'];
	  }

	}
	//listarray($data);
$aantal = count($data['VAR']);

	if($aantal >0)
	{
	  $this->pdf->rapport_deel = 'VAR';
		$this->pdf->AddPage();
		$this->pdf->templateVars['VHOPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;
		$this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;
    	$this->pdf->switchFont('fondsLaag');



	$n=1;
	foreach ($data['VAR'] as $fonds)
	{
	  /*
	  $this->pdf->Row(array('',$fonds['munt'],
	                           $this->formatGetal($fonds['aantal']),
	                           $fonds['omschrijving'],
	                           $this->formatGetal($fonds['aankoopprijs'],2),
	                           $this->formatGetal($fonds['marktprijs'],2),
	                           $this->formatGetal($fonds['marktwaarde']),
	                           $this->formatGetal($fonds['opgelopenRente']),
	                           $this->formatGetal($fonds['marktwaarde']/$totaalWaarde*100,1)." %"));
*/




	  	  $this->pdf->Row(array('',$fonds['munt'],
	                           $this->formatGetal($fonds['aantal']),
	                           $this->shrinkString($fonds['omschrijving'],$this->pdf->widths[3]),
	                           $this->formatGetal($fonds['aankoopprijs'],2),
	                            $this->formatGetal($fonds['prijsUltimo'],2),
	                           $this->formatGetal($fonds['marktprijsValuta'],2),

	                           $this->formatGetal($fonds['marktwaarde']),
	                           $this->formatGetal($fonds['opgelopenRente']),
	                           $this->formatGetal($fonds['marktwaarde']/$totaalWaardeCategorie['VAR']*100,1)." %")); //volk

	  $this->totalen['marktwaarde']+=$fonds['marktwaarde'];
	  $this->totalen['opgelopenRente']+=$fonds['opgelopenRente'];
	  $this->totalen['percentage']+=$fonds['marktwaarde']/$totaalWaardeCategorie['VAR']*100;

	  if($this->pdf->GetY() >$this->paginaHoogte && ($n < $aantal))
	  {
     $this->subtotaal();
     $this->pdf->addPage();
	   $this->pdf->switchFont('fondsLaag');
	  }
	}

	$this->subtotaal();
	$this->gTotaal();


  if (($this->pdf->customPageNo+$this->pdf->extraPage) <> $this->pdf->templateVars['VHOPaginas'])
    $this->pdf->templateVars['VHOPaginas'] .= " - ".($this->pdf->customPageNo+$this->pdf->extraPage);
}



  $this->pdf->rapport_titel = "Aandelen en Vergelijkbare Beleggingen";
	$this->pdf->rapport_header = array('','Munt',"Bedrag/\nAantal","Omschrijving","Aankoop\nPrijs","Prijs Ultimo\n".($this->rapportageJaar-1),"Marktprijs\nin Valuta","Prijs %\nVerschil","Winst/\nVerlies","Marktwaarde\nin EUR","% Vermogen");
  $this->pdf->rapport_deel = 'AAND';
	$aantal = count($data['AAND']);
	if($aantal > 0)
	{
	  $this->pdf->addPage();
 	  $this->pdf->last_rapport_type = $this->pdf->rapport_type;
    $this->pdf->last_rapport_titel = $this->pdf->rapport_titel;
    $this->pdf->templateVars['VHO2Paginas']=$this->pdf->customPageNo+$this->pdf->extraPage;
	  $this->pdf->switchFont('fondsLaag');

	$n=1;
	$eindSub =false;
	foreach ($data['AAND'] as $fonds)
	{

	  	if($fonds['sector'] =='')
			{
			  $fonds['sector'] = 'Geen Sector';
			  $fonds['sectorOmschrijving'] = 'Geen sector';
			}

	  if($fonds['sector'] <> $oldSector)
	  {
	  //  echo $fonds['omschrijving'].' '.$fonds['sector'].' <> '.$oldSector."<br>";
	    $this->SectorTotaal($fonds['sectorOmschrijving']);
	  }
	 //  listarray($fonds);
	  /*
	  $this->pdf->Row(array('',$fonds['munt'],
	                           $this->formatGetal($fonds['aantal']),
	                           $fonds['omschrijving'],
	                           $this->formatGetal($fonds['aankoopprijs'],2),
	                           $this->formatGetal($fonds['marktprijs'],2),
	                           $this->formatGetal($fonds['winstpercentage'],1),
	                           $this->formatGetal($fonds['winst']),
	                           $this->formatGetal($fonds['marktwaarde']),
	                           $this->formatGetal($fonds['marktwaarde']/$totaalWaarde*100,1)." %"));
*/

		  $this->pdf->Row(array('',$fonds['munt'],
	                           $this->formatGetal($fonds['aantal']),
	                           $this->shrinkString($fonds['omschrijving'],$this->pdf->widths[3]),
	                          $this->formatGetal($fonds['aankoopprijs'],2),
	                          $this->formatGetal($fonds['prijsUltimo'],2),

	                           $this->formatGetal($fonds['marktprijsValuta'],2),
	                           '',
	                           $this->formatGetal($fonds['winst']),
	                           $this->formatGetal($fonds['marktwaarde']),
	                           $this->formatGetal($fonds['marktwaarde']/$totaalWaardeCategorie['AAND']*100,1)." %"));
	  $this->totalen['marktwaarde']+=$fonds['marktwaarde'];
	  $this->totalen['opgelopenRente']+=$fonds['opgelopenRente'];
	  $this->totalen['percentage']+=$fonds['marktwaarde']/$totaalWaardeCategorie['AAND']*100;

	  $this->sTotaal['marktwaarde'] += $fonds['marktwaarde'];
	  $this->sTotaal['opgelopenRente'] += $fonds['opgelopenRente'];
	  $this->sTotaal['percentage'] += $fonds['marktwaarde']/$totaalWaardeCategorie['AAND']*100;

	  if($this->pdf->GetY() >$this->paginaHoogte && ($n < $aantal))
	  {
    // $this->subtotaal();
     $this->pdf->addPage();

	   $this->pdf->switchFont('fondsLaag');
	   $eindSub = true;
	  }
	  $n++;
	  $oldSector = $fonds['sector'];
	}
	if($eindSub)
	{
	  $this->gTotaal['marktwaarde'] = $this->totalen['marktwaarde'];
	  $this->gTotaal['opgelopenRente'] = $this->totalen['opgelopenRente'];
	  $this->gTotaal['percentage'] = $this->totalen['percentage'];

	//  $this->subtotaal();
	}
	else
	{
 	  $this->gTotaal['marktwaarde'] += $this->totalen['marktwaarde'];
	  $this->gTotaal['opgelopenRente'] += $this->totalen['opgelopenRente'];
	  $this->gTotaal['percentage'] += $this->totalen['percentage'];
	  $this->totalen = array();
	}
	$this->gTotaal();

	if (($this->pdf->customPageNo+$this->pdf->extraPage) <> $this->pdf->templateVars['VHO2Paginas'])
	  $this->pdf->templateVars['VHO2Paginas'] .=" - ".($this->pdf->customPageNo+$this->pdf->extraPage);
	}


	}
}
?>