<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.12 $

$Log: RapportATT_L18.php,v $
Revision 1.12  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.11  2015/02/22 09:55:14  rvv
*** empty log message ***

Revision 1.10  2014/07/09 16:12:34  rvv
*** empty log message ***

Revision 1.9  2014/06/14 16:40:37  rvv
*** empty log message ***

Revision 1.8  2014/05/14 15:28:41  rvv
*** empty log message ***

Revision 1.7  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.6  2009/11/08 14:11:55  rvv
*** empty log message ***

Revision 1.5  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.4  2008/09/15 08:04:05  rvv
*** empty log message ***

Revision 1.3  2008/07/01 07:12:34  rvv
*** empty log message ***

Revision 1.2  2008/05/16 08:13:26  rvv
*** empty log message ***

Revision 1.1  2008/03/18 09:56:48  rvv
*** empty log message ***

Revision 1.5  2008/01/23 07:39:13  rvv
*** empty log message ***

Revision 1.4  2007/11/16 11:25:30  rvv
*** empty log message ***

Revision 1.2  2007/10/04 12:09:12  rvv
*** empty log message ***

Revision 1.1  2007/09/26 15:31:29  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L18.php");


class RapportATT_L18
{
	function RapportATT_L18($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Rendementsoverzicht";

			$this->pdf->rapport_header = array('','Looptijd',"Startwaarde","Netto\nCash Flow","Eind\nWaarde","Geinvesteerd\nKapitaal","Resultaat","Rendement");


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));
    
    $this->att=new ATTberekening_L18($this);

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}


	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));



	 		 $DB = new DB();

	 		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde[totaal];


	 $this->pdf->subTitle = 'Totale waarde van vermogen op '.date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum)."   EUR ".$this->formatGetal($totaalWaarde);


//$rendamentWaarden = $this->berekenIndexWaarden($this->rapportageDatumVanaf,$this->rapportageDatum,$this->portefeuille);


 $index=new indexHerberekening();
$rendamentWaarden = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille,$specifiekeIndex);
// $index->pdata['pdf'] = true;
// $rendamentWaarden = $index->getWaardenATT($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

$this->pdf->addPage();
$this->pdf->templateVars['ATTPaginas']=$this->pdf->customPageNo+$this->pdf->extraPage;
$this->pdf->switchFont('fonds');

	  $this->pdf->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
	  	  $backupWidths = $this->pdf->widths;
	    $this->pdf->widths=array(3,55+32+34,34,34,34,32+24);//array(32,58+25+25,20,25,25,28);
    $this->pdf->Row(array('','Sinds Januari Waardering in EUR','','','',''));
     $this->pdf->widths = $backupWidths;

    $this->pdf->rapport_style['fondsATT'] =  $this->pdf->rapport_style['fonds'];
    $this->pdf->rapport_style['fondsATT']['rowHeight'] = 6;

    $this->pdf->switchFont('fondsATT');

$aantal = count($rendamentWaarden);
$n=1;
$totaalRendament =100;
foreach ($rendamentWaarden as $row)
{
   if($n == $aantal)
     $this->pdf->switchFont('rodelijn');
   $this->pdf->rowHeight=6;
	 $this->pdf->row(array('',
		                           $row['periodeForm'] ,
		                           $this->formatGetal($row['waardeBegin']),
		                           $this->formatGetal($row['waardeMutatie']),
		                           $this->formatGetal($row['waardeHuidige']),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen']),
		                           $this->formatGetal($row['resultaatVerslagperiode']),
		                           $this->formatGetal($row['performance'],1)
		                           ));

		                           $totaalWaarde = $row['PortefeuilleWaarde'];
		                           $totaalResultaat += $row['resultaatVerslagperiode'];
		                           $totaalStortingenOntrekkingen += $row['Stortingen']-$row['Onttrekkingen'];
		                           $totaalRendament = $row['index']-100;


		    $n++;
}
$this->pdf->switchFont('rodelijn');

   $this->pdf->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
	 $this->pdf->row(array('',
		                          'Totaal','','', '','',
		                           $this->formatGetal($totaalResultaat),
		                           $this->formatGetal($totaalRendament,1)
		                           ));



$backup = $this->pdf->marge;
$this->pdf->marge = 20;
$this->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf,$this->rapportageDatum);
$this->pdf->marge =$backup;
//$this->pdf->SetAutoPageBreak(true,30);

$this->pdf->last_rapport_type = $this->pdf->rapport_type;
$this->pdf->last_rapport_titel = $this->pdf->rapport_titel;


$this->addResultaat();
//$this->pdf->SetAutoPageBreak(true,15);
	}
  
  
  

function addResultaat()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  

  
   //$vetralingGrootboek=$this->getGrootboeken();

    //$att=new ATTberekening_L42($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
    
    $categorien=array();
    foreach($this->waarden['Periode'] as $categorie=>$waarden)
    {
      if($waarden['beginwaarde']!=0 || $waarden['eindwaarde']!=0 || $waarden['procent']!=0)
        $categorien[$categorie]=$this->att->categorien[$categorie];
    }
    
    $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('','U');
  $subBoven=array('','');
  $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
  
  // Beleggingsfondsen
  $headerVertalingen=array('Beleggingsfondsen'=>"Beleggings\nfondsen");
  foreach($categorien as $categorie=>$categorieOmschrijving)
  {
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';    
    $fillArray[]=1;
    $fillArray[]=1;
    
    if(isset($headerVertalingen[$categorieOmschrijving]))
      $header[]=$headerVertalingen[$categorieOmschrijving];
    else
      $header[]=$categorieOmschrijving;
    
    $header[]='';  
    $samenstelling[]='';
    $samenstelling[]='';
   // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }

  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
  
  $opbrengsten=array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
  $brutoResultaat=array("",vertaalTekst("Bruto resultaat",$this->pdf->rapport_taal));
  $brutoRendement=array("",vertaalTekst("Bruto rendement",$this->pdf->rapport_taal));
  $kosten=array("",vertaalTekst("Toe te rekenen kosten",$this->pdf->rapport_taal));
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fonds resultaten",$this->pdf->rapport_taal)); //
  $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valuta resultaten",$this->pdf->rapport_taal)); //
  
$totaalOpbrengst=array("","");//totaalOpbrengst

    $totaalKosten=array("","");   //totaalKosten 
    $totaal=array("","Netto resultaat");   //totaalOpbrengst-totaalKosten 


foreach($categorien as $categorie=>$categorieOmschrijving)
{
  unset($this->waarden['Periode'][$categorie]['perfWaarden']);
}

  //listarray($this->waarden['Periode']);exit;
  foreach($categorien as $categorie=>$categorieOmschrijving)
  {
    $perfWaarden=$this->waarden['Periode'][$categorie];

    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
    $mutwaarde[]='';
    
        
    $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
                        $perfWaarden['ongerealiseerdFondsResultaat']+
                        $perfWaarden['ongerealiseerdValutaResultaat']+
                        $perfWaarden['gerealiseerdFondsResultaat']+
                        $perfWaarden['gerealiseerdValutaResultaat']+
                        $perfWaarden['opgelopenrente'];
                        
    $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
    $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
    

    if($categorie=='totaal')
    {
      $effectenmutaties[]='';
      $effectenmutaties[]=''; 
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
      $onttrekking[]='';
      
      $opbrengsten[]='';
      $opbrengsten[]='';
      $kosten[]='';//$this->formatGetal($perfWaarden['kosten']*-1,0);
      $kosten[]='';
      $brutoResultaat[]='';//$this->formatGetal($perfWaarden['resultaatBruto'],0);
      $brutoResultaat[]='';
      $brutoRendement[]='';//$this->formatGetal($perfWaarden['procentBruto'],2);
      $brutoRendement[]='';
      
      $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $resultaat[]='';
      $rendement[]=$this->formatGetal($perfWaarden['procent'],2);
      $rendement[]='%';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal(($perfWaarden['onttrekking']+$perfWaarden['storting'])*-1,0);
      $effectenmutaties[]='';
      $stortingen[]='';
      $stortingen[]='';
      $onttrekking[]='';
      $onttrekking[]='';     
      
      if($categorie=='Liquiditeiten')
      {
        $opbrengsten[]=$this->formatGetal($perfWaarden['resultaatBruto'],0);
      }
      else
      {
        $opbrengsten[]=$this->formatGetal($perfWaarden['opbrengst'],0);
      }
      $opbrengsten[]='';
      //$kosten[]=$this->formatGetal($perfWaarden['kosten']*-1,0);
      $kosten[]=' ';
      $kosten[]='';
      $brutoResultaat[]=$this->formatGetal($perfWaarden['resultaatBruto'],0);
      //$brutoResultaat[]=' ';
      $brutoResultaat[]='';
      $brutoRendement[]=$this->formatGetal($perfWaarden['procentBruto'],2);
      //$brutoRendement[]=' ';
      $brutoRendement[]=' ';
      
      //$resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $resultaat[]='';
      $resultaat[]='';
      //$rendement[]=$this->formatGetal($perfWaarden['procent'],2);
      $rendement[]='';
      $rendement[]=' ';
    
    }

    

    
    if($categorie == 'totaal')
    {
   
      $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
      $totaal[]='';
    

    }
  } 

    $kol=30;
    $fil=2;
  	$this->pdf->widthB = array(15,80,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;
    $this->pdf->widthC = array(100,65,$kol);
  
  $this->pdf->ln();
//listarray($perfWaarden);


//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
		//$this->pdf->row($header);
    

  
    $this->pdf->rapport_header=$header;
    $this->pdf->subTitle='';
    $this->pdf->AddPage();
    
          $this->pdf->rowHeight=6;
  $this->pdf->CellBorders = array();
    
   		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize); 
    
    
    
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->switchFont('fondsATT');
		$this->pdf->row($perbegin);
	  //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
			// subtotaal
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    //$this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    
    $this->pdf->row($opbrengsten);
    $this->pdf->row($brutoResultaat);
  //  $this->pdf->CellBorders = $subOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->switchFont('rodelijn');
    $this->pdf->row($brutoRendement);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(-4);
  //  $this->pdf->CellBorders = array();
    
    //$this->pdf->row($kosten);
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
  	$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

}



	//
	function printAEXVergelijking($vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $this->pdf->AutoPageBreak=false;
		$query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB  = new DB();
		$DB2 = new DB();
		$rowHeight = 5;

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();


		$this->pdf->ln(3);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
		$this->pdf->Rect(8,$this->pdf->getY(),255+24,11,'F');
		$this->pdf->SetFillColor(0);
		$this->pdf->Rect(8,$this->pdf->getY(),255+24,11);
		$this->pdf->ln(1);
		$this->pdf->SetX(8);
		$add=34/2;
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_kop4_font,$this->pdf->rapport_kop4_fontstyle,$this->pdf->rapport_kop4_fontsize);
		$this->pdf->Cell(50+$add,$rowHeight, vertaalTekst("Index-vergelijking",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[r],$this->pdf->rapport_kop_fontcolor[g],$this->pdf->rapport_kop_fontcolor[b]);
		$this->pdf->Cell(23,$rowHeight, date("d-m-Y",db2jul($rapportageDatumVanaf)), 0,0, "R");
		$this->pdf->Cell(23,$rowHeight, date("d-m-Y",db2jul($rapportageDatum)), 0,0, "R");
		$this->pdf->Cell(23,$rowHeight, vertaalTekst("Performance",$this->pdf->rapport_taal), 0,0, "R");
		$this->pdf->Cell(5,$rowHeight,"  ");
		$this->pdf->Cell(50+$add,$rowHeight, vertaalTekst("Index",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell(23,$rowHeight, date("d-m-Y",db2jul($rapportageDatumVanaf)), 0,0, "R");
		$this->pdf->Cell(23,$rowHeight, date("d-m-Y",db2jul($rapportageDatum)), 0,0, "R");
		$this->pdf->Cell(23,$rowHeight, vertaalTekst("Performance",$this->pdf->rapport_taal), 0,1, "R");
		//$this->pdf->SetX($this->pdf->marge);
    $this->pdf->SetX(8);
    //$add=24/8;
		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
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

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();


			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );

			$this->pdf->Cell(50+$add,$rowHeight, $perf['Omschrijving'], 0,0, "L");
		  $this->pdf->Cell(23,$rowHeight, $this->pdf->formatGetal($koers1['Koers'],2), 0,0, "R");
			$this->pdf->Cell(23,$rowHeight, $this->pdf->formatGetal($koers2['Koers'],2), 0,0, "R");
		  $this->pdf->Cell(23,$rowHeight, $this->pdf->formatGetal($performance,2), 0,0, "R");
		  $this->pdf->Cell(5,$rowHeight," ");
		}


    $this->pdf->AutoPageBreak=true;
	}


	//

	function berekenIndexWaarden($datumBegin,$datumEind,$portefeuille)
	{

  $julBegin = db2jul($datumBegin);
  $julEind = db2jul($datumEind);

 	$eindjaar = date("Y",$julEind);
	$eindmaand = date("m",$julEind);
	$beginjaar = date("Y",$julBegin);
	$startjaar = date("Y",$julBegin);
	$beginmaand = date("m",$julBegin);

	$ready = false;
	$i=0;
	$totaalPerf = 100;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$datum == array();

	while ($ready == false)
	{
	  if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
	  {
	    $ready = true;
		}
		else
		{
		  if($i==0)
        $datum[$i]['start']=$datumBegin;
	    else
	    {
		    $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	    }
	    $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
	    $i++;
		}
	}
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;


	$index=new indexHerberekening();
	foreach ($datum as $periode)
	{
	  $data=$index->BerekenMutaties($periode['start'],$periode['stop'],$this->portefeuille);
    $rendamentWaarden[]=$data;
    $data['waardeHuidige'] = $data['waardeBegin'];
	}
	return $rendamentWaarden;

	}




}
?>