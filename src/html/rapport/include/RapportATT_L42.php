<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/13 14:50:15 $
File Versie					: $Revision: 1.29 $

$Log: RapportATT_L42.php,v $
Revision 1.29  2019/02/13 14:50:15  rvv
*** empty log message ***

Revision 1.28  2019/02/09 18:40:16  rvv
*** empty log message ***

Revision 1.27  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.26  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.25  2014/12/17 16:14:40  rvv
*** empty log message ***

Revision 1.24  2014/12/06 18:13:44  rvv
*** empty log message ***

Revision 1.23  2014/08/23 15:45:01  rvv
*** empty log message ***

Revision 1.22  2014/06/29 15:38:56  rvv
*** empty log message ***

Revision 1.21  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.20  2013/11/13 15:06:41  rvv
*** empty log message ***

Revision 1.19  2013/11/06 16:55:30  rvv
*** empty log message ***

Revision 1.18  2013/10/23 09:58:32  rvv
*** empty log message ***

Revision 1.17  2013/10/16 15:35:04  rvv
*** empty log message ***

Revision 1.16  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.15  2013/08/18 12:23:35  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L42.php");

class RapportATT_L42
{
	function RapportATT_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_jaar  =date('Y',$this->pdf->rapport_datumvanaf);
		$this->pdf->excelData 	= array();
    $this->jaarGeleden=date("Y-m-d",mktime(0,0,0,date('m',$this->pdf->rapport_datum),date('d',$this->pdf->rapport_datum),date('Y',$this->pdf->rapport_datum)-1));
	//	$this->pdf->rapport_titel = "Performance overzicht";
		$this->pdf->rapport_titel = vertaalTekst("Performance overzicht ",$this->pdf->rapport_taal).date("j",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".
      date("j",$this->pdf->rapport_datum)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datum);
      
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
	}

	function formatGetal($waarde, $dec,$procent=false,$nullNietTonen=false)
	{
	  if($nullNietTonen==true && $waarde==0)
      return '';
      
		$data=number_format($waarde,$dec,",",".");
		if($procent==true)
		  $data.="%";
		return $data;
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
	  if($waarde==0)
	    return;
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



	function printSubTotaal($lastCategorieOmschrijving,$allData,$style='')
	{
	  if($lastCategorieOmschrijving != 'Totaal')
	  {
	    $prefix='Subtotaal ';
	    $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','TS','TS','TS','TS');
	  }
	  else
	  {
	    $prefix='';
	    $this->pdf->CellBorders = array('',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),'',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
	  }
    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,vertaalTekst("$prefix",$this->pdf->rapport_taal).vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),0,'L');
    $this->pdf->setX($this->pdf->marge);

    $data=$allData['perf'];
   	$this->pdf->row(array(substr(vertaalTekst($categorieData['omschrijving'],$this->pdf->rapport_taal),0,25),
												$this->formatGetal($data['beginwaarde'],0),
												$this->formatGetal($data['stort'],0),
                        $this->formatGetal($data['eindwaarde'],0),
												$this->formatGetal($data['resultaat'],0),
                        '', //$this->formatGetal($data['resultaat']/$data['gemWaarde']*100,2).'%'
                        $this->formatGetal($data['bijdrage'],2,true)));

    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,vertaalTekst($title,$this->pdf->rapport_taal),0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}


	function writeRapport()
	{
		global $__appvar,$USR;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving['ATTPaginas']=$this->pdf->rapport_titel;
		//$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'],$this->pdf->rapport_lijn_rood['g'],$this->pdf->rapport_lijn_rood['b']);
		//$this->pdf->SetLineWidth(0.1);

		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];

    $att=new ATTberekening_L42($this);
    $att->indexPerformance=false;
    $this->waarden['sector']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'sector');
    foreach($this->waarden['sector'] as $sector=>$perfData)
    {
      if($sector <> 'totaal' && $att->categorien[$sector] <> '')
      {
        if($perfData['beginwaarde'] <> 0 || $perfData['eindwaarde'] <> 0 || $perfData['resultaat'] <> 0 )
        {
        unset($perfData['perfWaarden']);
        $var=$perfData;//array();
        $var['beginwaarde']=$perfData['beginwaarde'];
        $var['eindwaarde']=$perfData['eindwaarde'];
        $var['stort']=$perfData['stort'];
        $var['stortEnOnttrekking']=$perfData['storting']+$perfData['onttrekking'];
        $var['resultaat']=$perfData['resultaat'];
        $var['kosten']=$perfData['kosten'];
        $var['weging']=$perfData['weging'];
        $var['bijdrage']=$perfData['bijdrage'];
        $perSector[$sector]['omschrijving']=$att->categorien[$sector];
        $perSector[$sector]['perf']=$var;
        }
      }
    }
	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


  foreach ($perSector as $sector=>$sectorData)
  {
  $data=$sectorData['perf']; //echo "$sector ".$data['gemWaarde']." ".$data['weging']."<br>\n";
  $totaalSom['beginwaarde'] += $data['beginwaarde'];
  $totaalSom['eindwaarde'] += $data['eindwaarde'];
  $totaalSom['stort'] += $data['stort'];
  $totaalSom['gerealiseerd'] += $data['gerealiseerd'];
  $totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
  $totaalSom['kosten'] += $data['kosten'];
  $totaalSom['resultaat'] += $data['resultaat'];
  $totaalSom['gemWaarde'] += $data['gemWaarde'];
  $totaalSom['weging'] += $data['weging'];
  $totaalSom['bijdrage'] += $data['bijdrage'];//($data['resultaat']/$data['gemWaarde']*$data['weging']*100);//
  }
//listarray($totaalSom);
      $perfTotaal=$totaalSom;
    $this->pdf->ln(-4);
      $dataWidth=array(50,20,20,20,20,20,20);
      $this->pdf->pageYstart=$this->pdf->GetY();
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(array_sum($dataWidth),4,"Rendement sub-beleggingscategorieën",0,1,'C');
   	  $this->pdf->SetWidths($dataWidth);
	    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $lastColors=$this->pdf->CellFontColor;
      unset($this->pdf->CellFontColor);
      $this->pdf->Row(array("\n".vertaalTekst("Sub-beleggingscategorie",$this->pdf->rapport_taal),
      "\n".date('d-m-Y',$this->pdf->rapport_datumvanaf),
      "\n".vertaalTekst("Mutaties",$this->pdf->rapport_taal),
      "\n".date('d-m-Y',$this->pdf->rapport_datum),
      "\n".vertaalTekst("Resultaat",$this->pdf->rapport_taal),
      "\n".vertaalTekst("Rendement",$this->pdf->rapport_taal),
      "".vertaalTekst("Bijdrage",$this->pdf->rapport_taal)."\n".vertaalTekst("rendement",$this->pdf->rapport_taal)));
      $this->pdf->CellFontColor=$lastColors;
      $this->pdf->Line(($this->pdf->marge),$this->pdf->GetY(),$this->pdf->marge + array_sum($dataWidth),$this->pdf->GetY());
	    $this->pdf->SetLineWidth(0.1);
      
      
   foreach ($perSector as $sector=>$sectorData)
   {
     $data=$sectorData['perf'];
     $this->pdf->row(array($sectorData['omschrijving'],
												$this->formatGetal($data['beginwaarde'],0),
												$this->formatGetal($data['stort'],0),
												$this->formatGetal($data['eindwaarde'],0),
												$this->formatGetal($data['resultaat'],0),
                        $this->formatGetal($data['procent'],2,true),//$data['resultaat']/$data['gemWaarde']*100
                        $this->formatGetal($data['bijdrage'],2,true)));
   }
   $this->printSubTotaal('Totaal',array('perf'=>$perfTotaal),'B');
   
   


   $netto=$this->waarden['sector']['totaal'];
   $this->pdf->CellBorders = array('','','US','','US','','US');
   $this->pdf->row(array('Niet toerekenbare kosten','',
												$this->formatGetal($perfTotaal['stort']-$netto['stort'],0),'',
												$this->formatGetal($perfTotaal['stort']-$netto['stort'],0),'',
                        $this->formatGetal($netto['procent']-$perfTotaal['bijdrage'],2,true)));
   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);   
   $this->pdf->CellBorders = array('','','UU','','UU','','UU');
   $this->pdf->Ln();               
   $this->pdf->row(array('Netto bedragen','',
												$this->formatGetal($netto['stort'],0,false),'',
												$this->formatGetal($netto['resultaat'],0),'',
                        $this->formatGetal($netto['procent'],2,true)));                      
  // listarray($this->waarden['sector']['totaal']);exit;
//listarray($perfTotaal);
   $this->indexVergelijking();
   $att=new ATTberekening_L42($this);
   $att->indexPerformance=false;
  // $this->rapportageDatumVanaf
   $this->waarden['Hoofdcategorie']=$att->bereken($this->jaarGeleden,$this->rapportageDatum,'Hoofdcategorie');

$this->pdf->SetY(120);
$this->pdf->SetTextColor(0,0,0);
$colWidth=(160)/9;//(287-(2*$this->pdf->marge))/9;

$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
$widths=array();
for($i=0;$i<9;$i++)
{
  if($i==6)
    $widths[]=10;
  $widths[]=$colWidth;
}
$this->pdf->SetWidths($widths);


$this->pdf->SetX($this->pdf->marge);
    $this->pdf->Ln();
    $this->pdf->Ln(6);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->Cell(array_sum($this->pdf->widths),4,"Rendement laatste 12 maanden",0,0,'C');


$this->pdf->SetX((170+$this->pdf->marge)-3*$colWidth);
$this->pdf->Cell(3*$colWidth,4,"Rendement",0,0,'C');
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->SetX($this->pdf->marge);
$this->pdf->CellBorders = array('','','','','','','',array('LU','U'),'U','U',array('RU','U'));
$this->pdf->row(array('','','','','','','','','','',''));
$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
$this->pdf->row(array("Maand", "Begin\nvermogen", "Stortingen","Onttrek-\nkingen","Eind\nvermogen","Resultaat",'',"Per maand","Cumulatief","Per kwartaal"));
unset($this->pdf->CellBorders);
$cumu=1;
$kwartaal=1;
$grafiekData=array();
foreach($this->waarden['Hoofdcategorie']['totaal']['perfWaarden'] as $datum=>$perfWaarden)
{ 
  $juldatum=db2jul($datum);
  $maand=date('m',$juldatum);
  $cumu=$cumu*(1+$perfWaarden['procent']);
  $kwartaal=$kwartaal*(1+$perfWaarden['procent']);
  if($maand==3||$maand==6||$maand==9||$maand==12)
  {
    $kwartaalTxt=$this->formatGetal(($kwartaal-1)*100,2,true);
    $kwartaal=1;
  }
  else
  {
    $kwartaalTxt='';
  }
    
 $perf=($cumu-1)*100;
 $datum=date('m-Y',$juldatum);
  $this->pdf->row(array($datum,
     $this->formatGetal($perfWaarden['beginwaarde']),
     $this->formatGetal($perfWaarden['storting']),
     $this->formatGetal($perfWaarden['onttrekking']),
     $this->formatGetal($perfWaarden['eindwaarde']),
     $this->formatGetal($perfWaarden['resultaat']),
     '',
     $this->formatGetal($perfWaarden['procent']*100,2,true),
     $this->formatGetal($perf,2,true),
     $kwartaalTxt
     ));
    $grafiekData['portefeuille'][]=$perf;
    $grafiekData['datum'][]=$datum;
    
}


$this->pdf->setXY(195,132);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->Cell(80,4,"Rendement laatste 12 maanden",0,0,'C');
$this->pdf->setXY(195,137);
$this->LineDiagram(80,50,$grafiekData);
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

//listarray($verdeling);
			//$this->pdf->SetXY(210, $this->pdf->headerStart);


unset($this->pdf->CellFontColor);

		// check op totaalwaarde!
		$actueleWaardePortefeuille=0;
		foreach ($categorieTotaal as $categorie=>$waardes)
		{
		  $actueleWaardePortefeuille+=$waardes['actuelePortefeuilleWaardeEuro'];
		}
	}
  
  function indexVergelijking()
  {
    $DB=new DB();
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
     
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
	  $query="SELECT
Indices.Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as catOmschrijving
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
LEFT JOIN BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
LEFT JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}
    
    $query="SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Valuta, Fondsen.Omschrijving
    FROM Portefeuilles 
    Inner Join Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";   
    $DB->SQL($query); 
		$DB->Query();
	  while($index = $DB->nextRecord())
		{
		  foreach ($perioden as $periode=>$datum)
      {
		    $specifiekeIndex['fondsKoers_'.$periode]=$this->getFondsKoers($index['SpecifiekeIndex'],$datum);
        $specifiekeIndex['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
		  }
      $specifiekeIndex['Omschrijving']=$index['Omschrijving'];
      $specifiekeIndex['performanceJaar'] = ($specifiekeIndex['fondsKoers_eind'] - $specifiekeIndex['fondsKoers_jan'])    / ($specifiekeIndex['fondsKoers_jan']/100 );
			$specifiekeIndex['performance'] =     ($specifiekeIndex['fondsKoers_eind'] - $specifiekeIndex['fondsKoers_begin']) / ($specifiekeIndex['fondsKoers_begin']/100 );
  		$specifiekeIndex['performanceEurJaar'] = ($specifiekeIndex['fondsKoers_eind']*$specifiekeIndex['valutaKoers_eind'] - $specifiekeIndex['fondsKoers_jan']  *$specifiekeIndex['valutaKoers_jan'])/(  $specifiekeIndex['fondsKoers_jan']* $specifiekeIndex['valutaKoers_jan']/100 );
			$specifiekeIndex['performanceEur'] =     ($specifiekeIndex['fondsKoers_eind']*$specifiekeIndex['valutaKoers_eind'] - $specifiekeIndex['fondsKoers_begin']*$specifiekeIndex['valutaKoers_begin'])/($specifiekeIndex['fondsKoers_begin']*$specifiekeIndex['valutaKoers_begin']/100 );
    }  
 
		$query = "SELECT
TijdelijkeRapportage.valuta,Valutas.Omschrijving,
Valutas.Afdrukvolgorde
FROM
TijdelijkeRapportage
Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' GROUP BY Valuta
ORDER BY Valutas.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($valuta = $DB->nextRecord())
		{
		  $valutas[]=$valuta['Valuta'];
		  $indexValuta[$valuta['valuta']]=$valuta;
		  foreach ($perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum);
      }
      $indexValuta[$valuta['valuta']]['performanceJaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_jan'])    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/100 );
			$indexValuta[$valuta['valuta']]['performance'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_begin']) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/100 );
		}

		$this->pdf->SetY($this->pdf->pageYstart);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->Cell(160,4,"",0,0,'C');
$this->pdf->Cell(134,4,"Vergelijkingsmaatstaven",0,0,'C');
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  	$this->pdf->SetWidths(array(175,51.5,18,18,18.5,25,25));
  	$this->pdf->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
 	  $this->pdf->ln();
  	$this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	//$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
 	  $this->pdf->CellBorders = array('','U','U','U','U');
 	  $this->pdf->row(array("","\nIndex","Koers\n".date("d-m-Y",db2jul($perioden['begin'])),"Koers\n".date("d-m-Y",db2jul($perioden['eind'])),"\nRendement"));
  	unset($this->pdf->CellBorders);   

  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{  
      //foreach ($indexData as $fonds=>$fondsData)
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	  $this->pdf->row(array("",$categorie));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	  foreach ($fondsen as $fonds)
  	  {      
        $fondsData=$indexData[$fonds];
        $this->pdf->row(array('',$fondsData['Omschrijving'],
        $this->formatGetal($fondsData['fondsKoers_begin'],0),
        $this->formatGetal($fondsData['fondsKoers_eind'],0),
        $this->formatGetal($fondsData['performance'],2)."%"));
      }
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
 	  $this->pdf->row(array("","Valuta"));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach ($indexValuta as $valuta=>$valutaData)
  	{      
        $this->pdf->row(array('',$valuta,
            $this->formatGetal($valutaData['valutaKoers_begin'],4),
            $this->formatGetal($valutaData['valutaKoers_eind'],4),
            $this->formatGetal($valutaData['performance'],2)."%"));
    }
    
    if(isset($specifiekeIndex['Omschrijving']))
    {
    $this->pdf->ln();
 	  $this->pdf->CellBorders = array('','U','U','U','U');
 	  $this->pdf->row(array("","\nGewogen index","Koers\n".date("d-m-Y",db2jul($perioden['begin'])),"Koers\n".date("d-m-Y",db2jul($perioden['eind'])),"Rendement\nin %"));
  	unset($this->pdf->CellBorders);
    $this->pdf->row(array('',$specifiekeIndex['Omschrijving'],
        $this->formatGetal($specifiekeIndex['fondsKoers_begin'],4),
        $this->formatGetal($specifiekeIndex['fondsKoers_eind'],4),
        $this->formatGetal($specifiekeIndex['performance'],2)."%"));
    }
  }


	function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='')
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='';

      if ($this->pdf->rapportageValuta <> 'EUR')
      {
	      $koersQuery =	", (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) as Rapportagekoers ";
	      $startValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumBegin);
	      $eindValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumEind);
      }
	    else
	    {
	      $koersQuery = ", 1 as Rapportagekoers";
	      $startValutaKoers= 1;
	      $eindValutaKoers= 1;
	    }

		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers $koersQuery ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND $fondsenWhere AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
		"Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);

//echo $query;exit;


		$DB->Query();

		// haal koersresultaat op om % te berekenen


		$buffer = array();
		$sortBuffer = array();

		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

	  foreach ($buffer as $mutaties)
		{
			$mutaties[Aantal] = abs($mutaties[Aantal]);
			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0 ;
      //$mutaties['Rapportagekoers']=1;

			switch($mutaties[Transactietype])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;

					break;
					case "B" :
						// Beginstorting
					break;
					case "D" :
					case "S" :
							// Deponering
						$t_aankoop_waarde 				= abs($mutaties[Debet]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties[Debet]);
						$t_aankoop_koers					= $mutaties[Fondskoers];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties[Credit]) * $mutaties[Valutakoers] / $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties[Credit]);
						$t_verkoop_koers					= $mutaties[Fondskoers];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/

			if(	$mutaties['Transactietype'] == "L" ||
					$mutaties['Transactietype'] == "V" ||
					$mutaties['Transactietype'] == "V/S" ||
					$mutaties['Transactietype'] == "A/S")
			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties[Fonds], $mutaties[Boekdatum],$this->pdf->rapportageValuta,$rapportageDatumVanaf);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties[Aantal] * -1) * $historie[historischeWaarde]      * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
					$beginditjaar          = ($mutaties[Aantal] * -1) * $historie[beginwaardeLopendeJaar] * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
				else
				{
					$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
				  $beginditjaar         = $mutaties[Aantal]        * $historie[beginwaardeLopendeJaar]  * $historie[beginwaardeValutaLopendeJaar]  * $mutaties[Fondseenheid];
				}
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
		    {
		    $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		    $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
		    }

				if($historie[voorgaandejarenActief] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
					}
				}
				else
				{
					$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
					$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}
				$result_historischkostprijs = $historischekostprijs;
				$result_voorgaandejaren = $resultaatvoorgaande;
				$result_lopendejaar = $resultaatlopende;
				$totaal_resultaat_waarde += $resultaatlopende;
			}
			else
			{
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
			}

	//	listarray($mutaties);
				$data[$mutaties['Fonds']]['mutatie']+=$aankoop_waarde-$verkoop_waarde;
				$data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
				if($mutaties['Credit'])
				  $data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
				else
			  	$data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
				$data[$mutaties['Fonds']]['aankoop']+=$aankoop_waarde;
				$data[$mutaties['Fonds']]['verkoop']+=$verkoop_waarde;
				$data[$mutaties['Fonds']]['resultaatJaren']+=$result_voorgaandejaren;
				$data[$mutaties['Fonds']]['resultaatJaar']+=$result_lopendejaar;
				$data['totalen']['gerealiseerdResultaat']+=$result_lopendejaar;//($result_voorgaandejaren+$result_lopendejaar);
				$data['totalen']['mutaties']+=$data[$mutaties['Fonds']]['mutatie'];


		}
		return $data;
	}

	function getRekeningMutaties($rekening,$van,$tot)
	{
	  $db= new DB();
	  $query = "
	  SELECT
  SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	FROM
	Rekeningmutaties ,  Rekeningen

	WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Rekening =  '$rekening'  AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '$van' AND
	Rekeningmutaties.Boekdatum <= '$tot'";

	  $db->SQL($query);
	  $db->Query();
	  $data = $db->nextRecord();
return $data['totaal'];
	}



		function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
		{
		  $DB=new DB();
		  $query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
      $DB->SQL($query); //echo "$fonds $query  <br>\n";
      $DB->Query();
      $totaalWaarde = $DB->NextRecord();

		  return $totaalWaarde['totaalWaarde'];
		}


 



  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  
function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$periode='maand')
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    $legendaItems= $data['legenda'];




    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=$this->pdf->rapport_kop_kleur;
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

    if($periode=='maand')
    {
      if(count($data)>12)
        $unit = $lDiag / count($data);
      else
        $unit = $lDiag / 12;
    }

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.6, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data1)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );

      $yval = $yval2;
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));


  //   $XPage
   // $YPage


    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }

}
?>
