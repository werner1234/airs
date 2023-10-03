<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/31 13:13:05 $
File Versie					: $Revision: 1.13 $

$Log: RapportINDEX_L83.php,v $
Revision 1.13  2019/08/31 13:13:05  rvv
*** empty log message ***

Revision 1.12  2019/07/13 17:50:20  rvv
*** empty log message ***

Revision 1.11  2019/06/15 20:53:56  rvv
*** empty log message ***

Revision 1.10  2019/05/29 15:45:42  rvv
*** empty log message ***

Revision 1.9  2019/05/25 16:22:55  rvv
*** empty log message ***

Revision 1.8  2019/05/15 15:31:34  rvv
*** empty log message ***

Revision 1.7  2019/05/11 16:49:13  rvv
*** empty log message ***

Revision 1.6  2019/05/04 18:23:53  rvv
*** empty log message ***

Revision 1.5  2019/04/27 18:27:04  rvv
*** empty log message ***

Revision 1.4  2019/04/24 14:42:25  rvv
*** empty log message ***

Revision 1.3  2019/04/20 16:59:05  rvv
*** empty log message ***

Revision 1.2  2019/04/14 15:42:05  rvv
*** empty log message ***

Revision 1.1  2019/04/13 17:42:24  rvv
*** empty log message ***

Revision 1.20  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.19  2015/06/27 15:52:41  rvv
*** empty log message ***

Revision 1.18  2014/02/12 15:55:51  rvv
*** empty log message ***

Revision 1.17  2014/02/05 16:02:14  rvv
*** empty log message ***

Revision 1.16  2012/09/30 11:18:17  rvv
*** empty log message ***

Revision 1.15  2012/07/29 10:24:33  rvv
*** empty log message ***

Revision 1.14  2012/04/21 15:38:14  rvv
*** empty log message ***

Revision 1.13  2012/02/19 16:13:11  rvv
*** empty log message ***

Revision 1.12  2011/06/08 18:19:04  rvv
*** empty log message ***

Revision 1.11  2011/05/04 16:32:00  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_83/ATTberekening_L83.php");

class RapportINDEX_L83
{
	function RapportINDEX_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "INDEX";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = vertaalTekst("Attributie-analyse en vergelijkingsmaatstaven in",$this->pdf->rapport_taal).' '. vertaalTekst($this->pdf->valutaOmschrijvingen[$this->pdf->portefeuilledata['RapportageValuta']],$this->pdf->rapport_taal);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->tweedePerformanceStart=$rapportageDatumVanaf;
		$this->percDecimalen=2;
    
    
    
    $this->rapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $this->rapJaar = date("Y", db2jul($this->rapportageDatum));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapJaar."-01-01"))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    else
      $this->tweedePerformanceStart = $this->rapJaar."-01-01";
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
  
  
  
  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  function getValutaKoers($valuta,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  function indexKader()
  {
    
    $db=new DB();
    $query="SELECT IndexPerBeleggingscategorie.Fonds,IndexPerBeleggingscategorie.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving, Fondsen.Omschrijving as fondsOmschrijving
 FROM IndexPerBeleggingscategorie
JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie =Beleggingscategorien.Beleggingscategorie
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
 (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='".$this->pdf->portefeuilledata['Portefeuille']."') ORDER BY Beleggingscategorien.Afdrukvolgorde,IndexPerBeleggingscategorie.id"; //ALP
    $db->SQL($query);
    $db->Query();
    $categorien=array();
    //$kwartaal=ceil(date('m',$object->rapport_datum)/3);
    //$begindagen=array(1=>'01-01',2=>'03-31',3=>'06-30',4=>'09-31');
    //$beginDatum=date('Y',$object->rapport_datum).'-'.$begindagen[$kwartaal];

    if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
    {
      $kwartaal = ceil(date('m', db2jul($this->rapportageDatum)) / 3);
      $beginMaand = ($kwartaal - 1) * 3 + 1;
      $start = date('Y-m-d', mktime(0, 0, 0, $beginMaand, 0, $this->rapJaar));
      $startPeriodeTxt='QTD';
    }
    else
    {
      $start=$this->rapportageDatumVanaf;
      $startPeriodeTxt=date('d-m-Y',db2jul($this->rapportageDatumVanaf)).' - '.date('d-m-Y',db2jul($this->rapportageDatum));
    }
    
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($start))
      $start= $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->rapJaar."-01-01")> db2jul($start))
      $start= $this->rapJaar."-01-01";
    
    
   // $beginDatum=date('Y-m-d',$this->pdf->rapport_datumvanaf);
    $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$start,'eind'=>date('Y-m-d',$this->pdf->rapport_datum));
    
    while($data=$db->nextRecord())
    {
      foreach($perioden as $periode=>$datum)
        $data[$periode]=$this->getFondsKoers($data['Fonds'],$datum);
      
      $data['ytd']= ($data['eind'] - $data['jan']) / ($data['jan']/100 );
      $data['kwartaal']= ($data['eind'] - $data['begin']) / ($data['begin']/100 );
      
      $categorien[$data['categorieOmschrijving']][$data['Fonds']]=$data;
    }
    //listarray($categorien);
    
    //$eindDatum=date('d/m/Y',$this->pdf->rapport_datum);
    
    $this->pdf->ln(6);
    $i=0;
    foreach($categorien as $categorie=>$fondsRegels)
    {
      
      if($this->pdf->getY()>170)
        $this->pdf->addPage();
      if($i==0)
      {
        $this->pdf->setAligns(array('L', 'L', 'R', 'R', 'R', 'R'));
        $this->pdf->setWidths(array(40, 80, 40, 80, 40, 40));
        $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
        //$this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);// blauw
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']); //grijs
      //  $this->pdf->SetDrawColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      
        $this->pdf->CellBorders = array('U', 'U', 'U', 'U', 'U');
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->row(array(vertaalTekst('Categorie',$this->pdf->rapport_taal), vertaalTekst('Index',$this->pdf->rapport_taal), vertaalTekst("Rendement",$this->pdf->rapport_taal). "\n$startPeriodeTxt",
                          vertaalTekst("Cumulatief",$this->pdf->rapport_taal)."\n".vertaalTekst("rendement YTD",$this->pdf->rapport_taal), vertaalTekst('Indexstand',$this->pdf->rapport_taal)));
        $i++;
      }
      else
      {
        //$this->pdf->CellBorders = array('U', 'U', 'U', 'U', 'U');
        $this->pdf->row(array('', '', "", "", ''));
      }
      unset($this->pdf->CellBorders);
      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
      $this->pdf->SetDrawColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      
      foreach($fondsRegels as $fonds=>$fondsData)
      {
        $this->pdf->setAligns(array('L','L','R','R','R','R'));
        $this->pdf->setWidths(array(40,80,40,80, 40, 40));
        $this->pdf->row(array(vertaalTekst($categorie ,$this->pdf->rapport_taal),$fondsData['fondsOmschrijving'],
                          $this->formatGetal($fondsData['kwartaal'],	$this->percDecimalen).'%',
                          $this->formatGetal($fondsData['ytd'],	$this->percDecimalen).'%',
                          $this->formatGetal($fondsData['eind'],	$this->percDecimalen)));
      }
  
     // $this->pdf->ln();
    }
    
    
  }

	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
    
    
    
    $kwartaal=ceil(date('m',db2jul($this->rapportageDatum))/3);
    $beginMaand=($kwartaal-1)*3+1;
    
    if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
    {
      $start = date('Y-m-d', mktime(0, 0, 0, $beginMaand, 0, $this->rapJaar));
      $periodeVanafTxt='QTD';
    }
    else
    {
      $start=$this->rapportageDatumVanaf;
      $periodeVanafTxt=date('d-m-Y',db2jul($this->rapportageDatumVanaf)).' '.date('d-m-Y',db2jul($this->rapportageDatum));
    }
    
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($start))
      $start= $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->rapStartJaar."-01-01")> db2jul($start))
      $start= $this->rapStartJaar."-01-01";
    
    
    
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->ln();
		$this->pdf->SetWidths(array(80,40,40,40, 40, 40));
		$this->pdf->SetAligns(array('L','R','R','R','R','R'));
    $y=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widths), 8, 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
		$this->pdf->row(array(vertaalTekst('Categorie',$this->pdf->rapport_taal),
                             vertaalTekst('Portefeuilleweging',$this->pdf->rapport_taal),
                             vertaalTekst('Rendement',$this->pdf->rapport_taal).' '.vertaalTekst($periodeVanafTxt,$this->pdf->rapport_taal),
		                         vertaalTekst('Contributie',$this->pdf->rapport_taal).' '.vertaalTekst($periodeVanafTxt,$this->pdf->rapport_taal),
                             vertaalTekst('Cumulatief rendement YTD',$this->pdf->rapport_taal),
                             vertaalTekst('Contributie cumulatief YTD',$this->pdf->rapport_taal)));
    $this->pdf->SetTextColor(0);
    unset($this->pdf->CellBorders);

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";
/*
        $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery)) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND Rekeningmutaties.Fonds='' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening";

    $DB=new DB();
 		$DB->SQL($query);
		$DB->Query();
    $totalen=array();
		while($data = $DB->NextRecord())
		{
      $totalen['rapportagePeriode']['kosten'] += $data['waarde'];
		}

		        $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery)) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND Rekeningmutaties.Fonds='' AND
Rekeningmutaties.Boekdatum > '".$this->tweedePerformanceStart."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening";

    $DB=new DB();
 		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
      $totalen['lopendeJaar']['kosten'] += $data['waarde'];
		}
*/
    unset($this->pdf->CellBorders);

    $query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingscategorien.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
INNER JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'
ORDER BY
Beleggingscategorien.Afdrukvolgorde"; //KeuzePerVermogensbeheerder.Afdrukvolgorde,
    $DB->SQL($query);
    $DB->Query();
    $categorieVolgorde=array();
    while($data = $DB->NextRecord())
    {
      $categorieVolgorde[$data['Beleggingscategorie']] = $data['Omschrijving'];
    }
		
    $att=new ATTberekening_L83($this);
    $this->waarden=array();
    $this->waarden['rapportagePeriode']=$att->bereken($start,  $this->rapportageDatum,$this->pdf->rapportageValuta);
    $this->waarden['lopendeJaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->rapportageValuta);

    $totalen['rapportagePeriode']['perf'] =performanceMeting($this->portefeuille,$start,  $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
    $totalen['lopendeJaar']['perf']     =performanceMeting($this->portefeuille,$this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

    //listarray($this->waarden);
     $lnHoogte=0;
    $wegingTotaal=0;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      foreach ($categorieVolgorde as $categorie=>$categorieOmschrijving)
      {
        if($this->waarden['rapportagePeriode'][$categorie]['weging']<>0 || $this->waarden['lopendeJaar'][$categorie]['weging'] ||
           $this->waarden['rapportagePeriode'][$categorie]['procent']<>0 || $this->waarden['lopendeJaar'][$categorie]['procent'] )
        {
          $wegingTotaal+=$this->waarden['rapportagePeriode'][$categorie]['wegingEind'];
          $this->pdf->row(array(vertaalTekst($categorieOmschrijving, $this->pdf->rapport_taal),
                            $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['wegingEind'] * 100, 	1). '%',
                            $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procentBruto'], 	$this->percDecimalen) . '%',
                            $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['bijdrage'], 	$this->percDecimalen) . '%',
                            $this->formatGetal($this->waarden['lopendeJaar'][$categorie]['procentBruto'], 	$this->percDecimalen) . '%',
                            $this->formatGetal($this->waarden['lopendeJaar'][$categorie]['bijdrage'], 	$this->percDecimalen) . '%'));
        //  $totalen['rapportagePeriode']['bijdrage'] += $this->waarden['rapportagePeriode'][$categorie]['bijdrage'];
        //  $totalen['lopendeJaar']['bijdrage'] += $this->waarden['lopendeJaar'][$categorie]['bijdrage'];
          $this->pdf->ln($lnHoogte);
        }


      }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T');
    $this->pdf->Line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->getY());
    $this->pdf->ln($lnHoogte);

    $this->pdf->row(array(vertaalTekst("Bruto",$this->pdf->rapport_taal),
                      $this->formatGetal($wegingTotaal * 100, 1). '%',
                      $this->formatGetal($this->waarden['rapportagePeriode']['totaal']['procentBruto'], 	$this->percDecimalen) . '%',
      		            $this->formatGetal($this->waarden['rapportagePeriode']['totaal']['bijdrage'],	$this->percDecimalen).'%',
                      $this->formatGetal($this->waarden['lopendeJaar']['totaal']['procentBruto'], 	$this->percDecimalen) . '%',
                      $this->formatGetal($this->waarden['lopendeJaar']['totaal']['bijdrage'],	$this->percDecimalen).'%'));
    $this->pdf->ln($lnHoogte);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 unset($this->pdf->CellBorders);

    $this->pdf->row(array( vertaalTekst("Kosten",$this->pdf->rapport_taal),'','',
                      $this->formatGetal(round($totalen['rapportagePeriode']['perf'],2)-round($this->waarden['rapportagePeriode']['totaal']['bijdrage'],2),	$this->percDecimalen).'%',	'',
      		$this->formatGetal(round($totalen['lopendeJaar']['perf'],2)-round($this->waarden['lopendeJaar']['totaal']['bijdrage'],2),	$this->percDecimalen).'%'));
      		$this->pdf->ln($lnHoogte);


		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+array_sum($this->pdf->widths),$this->pdf->getY());
		$this->pdf->ln($lnHoogte);
		$this->pdf->row(array(vertaalTekst("Netto",$this->pdf->rapport_taal),'','',
      		$this->formatGetal($totalen['rapportagePeriode']['perf'],	$this->percDecimalen).'%','',	$this->formatGetal($totalen['lopendeJaar']['perf'],	$this->percDecimalen).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);

    $this->indexKader();
    

  }
  
  
}
?>