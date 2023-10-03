<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_125/ATTberekening_L125.php");

class RapportPERF_L125
{
	function RapportPERF_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Specificaties";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L125($this);
	}
  
  
  function formatGetal($waarde, $dec, $teken='')
  {
    return formatGetal_L125($waarde, $dec, $teken);
  }


	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		$this->pdf->widthB = array(280);
		$this->pdf->alignB = array('L');


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    
    subHeader_L125($this->pdf,28,array(280),array('Resultaat over het lopende jaar'));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->addResultaat();

	}
  


function getGrootboeken()
{
  $vertaling=array();
  $db=new DB();
  $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
  }
  return $vertaling;
}



function addResultaat()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  
   $vetralingGrootboek=$this->getGrootboeken();
  
    //$att=new ATTberekening_L42($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie');
    $categorien=array_keys($this->waarden['Periode']);
    

  $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("",'');
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
  
  foreach($categorien as $categorie)
  {
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';    
    $fillArray[]=1;
    $fillArray[]=1;
    $header[]=$this->att->categorien[$categorie];
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
   // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }

  $perbegin=array("",vertaalTekst("Portefeuillewaarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Portefeuillewaarde per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Verschil",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Stortingen",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Opnames",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Effectenmutaties",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement",$this->pdf->rapport_taal));
  $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
  $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal));//
$totaalOpbrengst=array("","");//totaalOpbrengst

    $totaalKosten=array("","");   //totaalKosten 
    $totaal=array("","");   //totaalOpbrengst-totaalKosten 


foreach($categorien as $categorie)
{
  unset($this->waarden['Periode'][$categorie]['perfWaarden']);
}

  //listarray($this->waarden['Periode']);exit;
  foreach($categorien as $categorie)
  {
    $perfWaarden=$this->waarden['Periode'][$categorie];
    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,'€');
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,'€');
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,'€');
    $mutwaarde[]='';
    
    if($categorie=='totaal')
    {
      $effectenmutaties[]='';
      $effectenmutaties[]=''; 
     //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
     //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0,'€');
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0,'€');
      $onttrekking[]='';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal(($perfWaarden['onttrekking']+$perfWaarden['storting'])*-1,0,'€');
      $effectenmutaties[]='';
      $stortingen[]='';
      $stortingen[]='';
      $onttrekking[]='';
      $onttrekking[]='';     
    }
    
    $totaalOpbrengstEUR=$perfWaarden['opbrengst']+
                        $perfWaarden['ongerealiseerdFondsResultaat']+
                        $perfWaarden['ongerealiseerdValutaResultaat']+
                        $perfWaarden['gerealiseerdFondsResultaat']+
                        $perfWaarden['gerealiseerdValutaResultaat']+
                        $perfWaarden['opgelopenrente'];
                        
    $perfWaarden['resultaatValuta']=$perfWaarden['resultaat']-($totaalOpbrengstEUR+$perfWaarden['kosten']);
    $totaalOpbrengstEUR+=$perfWaarden['resultaatValuta'];
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0,'€');
    $resultaat[]='';

    if($categorie=='Liquiditeiten'||$categorie=='H-Liq')
    {
      $rendement[]='';
      $rendement[]='';
    }
    else
    {
      $rendement[]=$this->formatGetal($perfWaarden['procent'],2,'%');
      $rendement[]='';
    }
    
    if($categorie == 'totaal')
    {
    $ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0,'€');
    $ongerealiseerdFonds[]='';
    $ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0,'€');
    $ongerealiseerdValuta[]='';
    $gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0,'€');
    $gerealiseerdFonds[]='';
    $gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0,'€');
    $gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0,'€');
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0,'€');
    $rente[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0,'€');
    $totaalOpbrengst[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0,'€');
    $totaalKosten[]='';
    $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0,'€');
    $totaal[]='';
    
    
    
    foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;  
    }
  } 

  $kol=30;
  $fil=2;
  $this->pdf->widthB = array(20-$this->pdf->marge,75,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil,$kol,$fil);
	$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
  $this->pdf->widthA = $this->pdf->widthB;
	$this->pdf->alignA = $this->pdf->alignB;
  
  $this->pdf->ln(15);
  $this->pdf->SetWidths($this->pdf->widthB);
	$this->pdf->SetAligns($this->pdf->alignB);
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	$this->pdf->row($header);
	$this->pdf->ln();
	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	$this->pdf->row($perbegin);
  $this->pdf->ln();
	$this->pdf->row($waardeRapdatum);
	$this->pdf->ln();
  $this->pdf->Line(20,$this->pdf->GetY() ,$this->pdf->w-20,$this->pdf->GetY(),array('color'=>$this->pdf->textGrijs));
  $this->pdf->ln();
  $this->pdf->row($mutwaarde);
  $this->pdf->ln();
  $this->pdf->row($effectenmutaties);
  $this->pdf->ln();
  $this->pdf->Line(20,$this->pdf->GetY() ,$this->pdf->w-20,$this->pdf->GetY(),array('color'=>$this->pdf->textGrijs));
  
  $this->pdf->ln();
	$this->pdf->row($stortingen);
  $this->pdf->ln();
  $this->pdf->row($onttrekking);
  $this->pdf->ln();
  $this->pdf->Line(20,$this->pdf->GetY() ,$this->pdf->w-20,$this->pdf->GetY(),array('color'=>$this->pdf->textGrijs));
  $this->pdf->ln();
  $this->pdf->ln();
  $this->pdf->CellFontStyle=array('',array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+6),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
  $this->pdf->CellFontColor=array('','',array('r'=>$this->pdf->textGroen[0],'g'=>$this->pdf->textGroen[1],'b'=>$this->pdf->textGroen[2]),array('r'=>0,'g'=>0,'b'=>0));
	$this->pdf->row($resultaat);
	$this->pdf->ln();
  $this->pdf->ln();
  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	$this->pdf->row($rendement);
	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  unset($this->pdf->CellFontStyle);
  unset($this->pdf->CellFontColor);
 
}



}
?>