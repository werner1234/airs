<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/12 18:15:06 $
File Versie					: $Revision: 1.2 $

$Log: RapportAFM_L35.php,v $
Revision 1.2  2020/04/12 18:15:06  rvv
*** empty log message ***

Revision 1.1  2020/04/12 11:49:05  rvv
*** empty log message ***

Revision 1.5  2020/04/11 16:33:41  rvv
*** empty log message ***

Revision 1.32  2019/03/09 18:46:18  rvv
*** empty log message ***

Revision 1.31  2019/01/26 19:33:28  rvv
*** empty log message ***

Revision 1.30  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.29  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.28  2017/07/29 17:18:20  rvv
*** empty log message ***

Revision 1.27  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.26  2016/06/01 19:48:58  rvv
*** empty log message ***

Revision 1.25  2016/05/29 10:19:26  rvv
*** empty log message ***

Revision 1.24  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.23  2015/03/14 17:01:49  rvv
*** empty log message ***

Revision 1.22  2014/12/31 18:09:06  rvv
*** empty log message ***

Revision 1.21  2014/12/06 18:13:44  rvv
*** empty log message ***

Revision 1.20  2014/07/06 12:34:34  rvv
*** empty log message ***

Revision 1.19  2014/03/19 16:39:09  rvv
*** empty log message ***

Revision 1.18  2013/11/13 15:06:41  rvv
*** empty log message ***

Revision 1.17  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.16  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.15  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.14  2013/07/28 09:59:15  rvv
*** empty log message ***

Revision 1.13  2013/07/13 15:19:44  rvv
*** empty log message ***

Revision 1.12  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.11  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.10  2013/03/20 16:56:53  rvv
*** empty log message ***

Revision 1.9  2013/03/17 10:58:29  rvv
*** empty log message ***

Revision 1.8  2013/03/13 17:01:08  rvv
*** empty log message ***

Revision 1.7  2013/02/20 15:12:14  rvv
*** empty log message ***

Revision 1.6  2013/02/10 10:06:07  rvv
*** empty log message ***

Revision 1.5  2013/02/06 19:06:11  rvv
*** empty log message ***

Revision 1.4  2013/02/03 09:04:21  rvv
*** empty log message ***

Revision 1.3  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.2  2013/01/20 13:27:16  rvv
*** empty log message ***

Revision 1.1  2013/01/13 13:35:39  rvv
*** empty log message ***

Revision 1.11  2013/01/06 10:09:57  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportAFM_L35
{

	function RapportAFM_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "AFM";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Resultaat- en rendementsberekening ".date("j",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".
      date("j",$this->pdf->rapport_datum)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    
//    $this->rapportageDatumVanaf=db2jul($this->rapport->rapportageDatumVanaf);
    $this->rapport_datum=$this->pdf->rapport_datum;
    $this->rapport_jaar=date('Y',$this->pdf->rapport_datumvanaf) ;

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



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars['AFMPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['AFMPaginas']=$this->pdf->rapport_titel;

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
      if($data['Grootboekrekening']=='RENTE')
        $data['Omschrijving']="Rente (spaar)rekeningen";
      
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
  

    $this->indexPerformance=false;
    $this->waarden['Periode']=$this->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
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
  $header=array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal));
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
    $header[]=$this->categorien[$categorie];
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
  $effectenmutaties=array("",vertaalTekst("Mutaties gedurende verslagperiode",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $rendementExcl=array("",vertaalTekst("Rendement exclusief liquiditeiten",$this->pdf->rapport_taal));
  $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
  $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fondsresultaten",$this->pdf->rapport_taal)); //
$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Resultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
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
    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],0,true);
    $mutwaarde[]='';
    
    if($categorie=='totaal')
    {
      $effectenmutaties[]='';
      $effectenmutaties[]=''; 
     //$stort=getStortingen($this->portefeuille, $datumBegin, $datumEind)
     //$onttr=getOnttrekkingen($this->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
      $onttrekking[]='';
      $rendementExcl[]=$this->formatGetal($perfWaarden['procentBruto'],2).' %';
      $rendementExcl[]='';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],0);
      $effectenmutaties[]='';
      $stortingen[]='';//'$this->formatGetal($perfWaarden['kosten'],0);
      $stortingen[]='';
      $onttrekking[]='';//$this->formatGetal($perfWaarden['opbrengst'],0);
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
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $resultaat[]='';
    $rendement[]=$this->formatGetal($perfWaarden['procent'],2).' %';
    $rendement[]='';

    $ongerealiseerdFonds[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat'],0);
    $ongerealiseerdFonds[]='';
    $ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
    $ongerealiseerdValuta[]='';
    $gerealiseerdFonds[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat'],0);
    $gerealiseerdFonds[]='';
    $gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
    $gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],0);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],0);
    $rente[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,0);
    $totaalOpbrengst[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],0);
    $totaalKosten[]='';
    $totaal[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $totaal[]='';
    
    
    
    foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;  
    
  } 

  $cellWidth=27;
  $cellWidthP=2;
  	$this->pdf->widthB = array(0,62,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP,$cellWidth,$cellWidthP);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
		$this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R');
  

//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $this->headerTop=$this->pdf->GetY();
		$this->pdf->row($header);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
//    $this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->row($perbegin);
	  //,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
			// subtotaal
	//	$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->CellBorders = $subOnder;
    $this->pdf->row($effectenmutaties);
    $this->pdf->ln();
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();

    $this->pdf->CellBorders = $volOnder;
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
    $this->pdf->row($rendementExcl);
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();
//listarray($this->pdf->widthB);

		$this->pdf->SetWidths(array(0,100));
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		$this->pdf->row($samenstelling);//,"","","",""));
    $this->pdf->SetWidths($this->pdf->widthB);
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->hoogteBeleggingsresultaat=$this->pdf->getY();
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->row($ongerealiseerdFonds);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		$this->pdf->row($ongerealiseerdValuta);
    $this->pdf->row($gerealiseerdFonds);
    $this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
	//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
	  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
		$keys=array();
		//foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		//  $keys[]=$key;

 
    
    foreach ($opbrengstCategorien as $grootboek)
	  {
		    $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
       // foreach($perfWaarden as $port=>$waarden)
       
        foreach($categorien as $categorie)
        {
          $perfWaarden=$this->waarden['Periode'][$categorie];
          $tmp[]=$this->formatGetal($perfWaarden['grootboekOpbrengsten'][$grootboek],0);
          $tmp[]='';
        }
		  //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			  $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}

    $this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		//$this->pdf->ln();
    $this->pdf->CellBorders = array();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		foreach ($kostenCategorien as $grootboek)
		{
      $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
        foreach($categorien as $categorie)
        {
          $perfWaarden=$this->waarden['Periode'][$categorie];
       
        $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],0);
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
    $this->pdf->CellBorders = $subBoven;
  	$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));
		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = array();
    //$this->pdf->CellBorders = $volOnder;
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

}



  function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.2f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }
  
  function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $legendWidth=50;
      $YDiag = $YPage + $margin;
      $hDiag = floor($h - $margin * 2);
      $XDiag = $XPage + $margin * 2 + $legendWidth;
      $lDiag = floor($w - $margin * 3 - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      if($minVal >0)
        $minVal=0;

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = ($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;
      
      $legendaStep=$unit/$nbDiv*$bandBreedte;
      //echo "$bandBreedte / $legendaStep = ".$bandBreedte/$legendaStep." ".$nbDiv;exit;
      //if($bandBreedte/$legendaStep > $nbDiv)

      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*5;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*2;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth($this->pdf->lineWidth);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

      $nullijn=$XDiag - ($offset * $unit) +$margin;

      $i=0;
      $nbDiv=10;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
        }

        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $i=0;

      //$this->pdf->SetXY(0, $YDiag);
      //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
      //$this->pdf->SetXY($nullijn, $YDiag);
      //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, 'Contributie rendement',0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
          //Bar
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          //Legend
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";

      for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
      {
          $xpos = $XDiag +  $i;
          $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
          $val = $i * $valIndRepere;
          $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
          $ypos = $YDiag + $hDiag - $margin;
          $this->pdf->Text($xpos, $ypos, $val);
      }
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $hLegend = 2;
      $radius = min($w - $margin * 4  , $h - $margin * 2); //
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
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
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

      $x1 = $XPage - $radius - 22 ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag - $radius + $hLegend*2;

      for($i=0; $i<$this->pdf->NbVal; $i++) {
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
          $y1+=$hLegend + $hLegend;
      }

  }
  
  
  function bereken($van,$tot,$verdeling='Hoofdcategorie')
  {
    global $__appvar;
    $DB=new DB();
    $this->categorien=array('totaal'=>'Totaal');
    
    if($verdeling=='Hoofdcategorie')
    {
      $categorieFilter='Hoofdcategorie';
      //$categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }
    elseif($verdeling=='totaal')
    {
      $categorieFilter='geen';
    }
    elseif($verdeling=='sector')
    {
      $categorieFilter='Beleggingssectoren';
      $join="LEFT JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector";
      $selectOmschrijving=',Beleggingssectoren.Omschrijving';
    }
    else
    {
      $categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }
    
    $query="SELECT waarde $selectOmschrijving FROM KeuzePerVermogensbeheerder $join
    WHERE categorie='$categorieFilter' AND
    Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
    ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $tmp=array();
    while($data=$DB->nextRecord())
    {
      $tmp[$data['waarde']]=array('categorie'=>$data['waarde'],'omschrijving'=>$data['Omschrijving']);
    }
    $perHoofdcategorie=$tmp;
    $perRegio=$tmp;
    $perSector=$tmp;
    $perCategorie=$tmp;
    /*
        $query="SELECT
        Beleggingscategorien.Beleggingscategorie,
    Beleggingscategorien.Omschrijving AS categorieOmschrijving
    FROM
    Beleggingscategorien
    INNER JOIN ZorgplichtPerBeleggingscategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
    INNER JOIN ZorgplichtPerPortefeuille ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht
    WHERE
    ZorgplichtPerPortefeuille.Portefeuille = '".$this->portefeuille."' AND ZorgplichtPerPortefeuille.extra=0 AND
    ZorgplichtPerPortefeuille.norm > 0
    ORDER BY Beleggingscategorien.Afdrukvolgorde";
    echo $query;exit;
        $DB->SQL($query);
        $DB->Query();
        while($data=$DB->nextRecord())
        {
          $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
          $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
          $perCategorie[$data['Beleggingscategorie']]['fondsen']=array();
          $perCategorie[$data['Beleggingscategorie']]['fondsValuta']=array();
        }
    */
    $query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
BeleggingssectorPerFonds.Beleggingssector,
Beleggingssectoren.Omschrijving as sectorOmschrijving,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->PortefeuilleStartdatum."' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Beleggingssectoren.Afdrukvolgorde,Fondsen.Omschrijving ";
    
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='Geen H-cat';
      
      if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];
        }
        else
        {
          $data['Beleggingssector']='Geen sector';
          $data['sectorOmschrijving']=$data['Geen sector'];
        }
      }
      
      
      
      if($data['Beleggingscategorie']=='')
        $data['Beleggingscategorie']='Geen cat';
      
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
      $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
      $perCategorie[$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $alleData['fondsen'][]=$data['Fonds'];
      
    }
    
    $query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening AND Rekeningen.Memoriaal=0
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->PortefeuilleStartdatum."' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";//
    
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='Geen H-cat';
      if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];
        }
        else
        {
          $data['Beleggingssector']='Geen sector';
          $data['sectorOmschrijving']=$data['Geen sector'];
        }
      }
      if($data['Beleggingscategorie']=='')
        $data['Beleggingscategorie']='Geen cat';
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
      $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['rekeningen'][]=$data['rekening'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $alleData['rekeningen'][]=$data['rekening'];
    }
    
    
    //$this->totalen['gemiddeldeWaarde']=0;
    //$perfTotaal=$this->fondsPerformance($alleData,$van,$tot,false,true);
    
    if($verdeling=='Hoofdcategorie')
      $categorien=$perHoofdcategorie;
    elseif($verdeling=='totaal')
      $categorien=array();
    elseif($verdeling=='sector')
      $categorien=$perSector;
    else
      $categorien=$perCategorie;
  
  
    $this->huidigeCategorie='totaal';
    $perfData['totaal'] = $this->fondsPerformance($alleData,$van,$tot,true,'totaal');
  //  $this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];
    foreach ($categorien as $categorie=>$categorieData)
    {
      $this->huidigeCategorie=$categorie;
      $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,true,$categorie);
      if($categorieData['omschrijving']=='')
        $categorieData['omschrijving']=$categorie;
      $this->categorien[$categorie]=$categorieData['omschrijving'];
    }
    $this->huidigeCategorie='Liquiditeiten';
    $perfData['Liquiditeiten'] = $this->fondsPerformance($alleData,$van,$tot,true,'Liquiditeiten');
    $this->categorien['Liquiditeiten']='Liquiditeiten';
    //$this->categorien['totaal']='Totaal';
    //listarray($perfData);
    
    return $perfData;
  }

  
  
  function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$categorie='')
  {
    global $__appvar;
    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
    {
      if($htis->perioden=='kwartalen')
        $perioden=$this->getKwartalen(db2jul($van),db2jul($tot));
      else
        $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    }
    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');
    
    if($categorie=='Liquiditeiten')
      $fondsData['fondsen']=array('geen');
    if($categorie <> 'Liquiditeiten' && $categorie <> 'Liquiditeiten')
      $fondsData['rekeningen']=array('geen');
    
    
    //
    //    $grootboekKostenFilter='OR Grootboekrekeningen.Kosten =1';
    // else
    //   $grootboekKostenFilter='';
    
    global $__appvar;
    $DB=new DB();
    $this->liqWaarden=array();
    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
        if(substr($rapDatum,5,5)=='01-01')
          $startJaar=1;
        else
          $startJaar=0;
        if(!isset($this->totalen[$rapDatum]))
        {
          $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $rapDatum,$startJaar);
          foreach($fondswaarden as $id=>$fondsWaarde)
          {
            if($fondsWaarde['type']=='fondsen')
              $instrument=$fondsWaarde['fonds'];
            elseif($fondsWaarde['type']=='rente')
              $instrument=$fondsWaarde['fonds'];
            elseif($fondsWaarde['type']=='rekening')
            {
              $instrument = $fondsWaarde['rekening'];
              $this->liqWaarden[$rapDatum]['WaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
            }
            else
              $instrument='geen';
            $this->totalen[$rapDatum]['totaalWaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
            $this->totalen[$rapDatum]['WaardeEur'][$instrument]+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
          }
        }
        if(!isset($this->totalen[$rapDatum]['WaardeEur'][$categorie]))
        {
          foreach($this->totalen[$rapDatum]['WaardeEur'] as $instrument=>$waarde)
          {
            if(in_array($instrument,$fondsData['fondsen']) || in_array($instrument,$fondsData['rekeningen']))
            {
              $this->totalen[$rapDatum]['WaardeEur'][$categorie]+=$waarde;
            }
          }
        }
      }
    }
    
    
    foreach ($perioden as $periode)
    {
      $grootboekKosten=array();
      $grootboekOpbrengsten=array();
      $FondsDirecteKostenOpbrengsten=array();
      $RekeningDirecteKostenOpbrengsten=array();
      $datumBegin=$periode['start'];
      $datumEind=$periode['stop'];
      
      if($portefeuilleStartJul > db2jul($datumBegin) && $portefeuilleStartJul < db2jul($datumEind))
      {
        $datumBegin=substr($this->pdf->PortefeuilleStartdatum,0,10);
        $weegDatum=$datumBegin;
      }
      
      if(substr($this->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
        $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
      else
        $weegDatum=$datumBegin;
      
      
      $portefeuilleStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
      
      $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
      $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];
      
      
      $query="SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1";
      $DB->SQL($query);
      $DB->Query();
      $grootboekrekeningen=array();
      while($grootboekrekening=$DB->nextRecord())
        $grootboekrekeningen[]=$grootboekrekening['Grootboekrekening'];
      
      $query = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
        "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal
      ".
        "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        "Rekeningmutaties.Grootboekrekening IN ('".implode("','",$grootboekrekeningen)."')";
      $DB->SQL($query);
      $DB->Query();
      $storting = $DB->NextRecord();
      $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];
      $this->totaalGemiddelde=$totaalGemiddelde;
//echo "$categorie $totaalGemiddelde = $totaalBeginwaarde - ".$storting['gewogen']."<br>\n";
      if($categorie=='totaal')
      {
        $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
        $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];
        $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal']) / $this->totaalGemiddelde);
        $stortingen 			 	= getStortingen($this->portefeuille,$datumBegin,$datumEind);
        $onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$datumBegin,$datumEind);
        $AttributieStortingenOntrekkingen['storting']=$stortingen;
        $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
        $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
        $gemiddelde = $totaalGemiddelde;
        
        
 // listarray($this->liqWaarden);
        $beginwaardeLiq=$beginwaarde-$this->liqWaarden[$datumBegin]['WaardeEur'];
        $eindwaardeLiq=$eindwaarde-$this->liqWaarden[$datumEind]['WaardeEur'];
 // echo "normale eind en beginwaarde= ((($eindwaarde- $beginwaarde) );<br>\n";
        $gemiddelde = $beginwaarde - $this->liqWaarden[$datumBegin]['WaardeEur'];
        $performanceBruto = ((($eindwaarde- $beginwaarde) - $storting['totaal']) / $gemiddelde);
  //listarray($storting);
        //$gemiddelde=$beginwaardeLiq+ $storting['gewogen'];
       // $performanceBruto = ((($eindwaardeLiq- $beginwaardeLiq) - $storting['totaal']) / $gemiddelde);
        
        //echo "$performance = ((($eindwaarde- $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde)<br>\n";
        //echo "$datumBegin,$datumEind -> $performance <br>\n";ob_flush();//exit;
      }
      else
      {
        $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
        $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
        $beginwaarde = $this->totalen[$datumBegin]['WaardeEur'][$categorie];
        $eindwaarde = $this->totalen[$datumEind]['WaardeEur'][$categorie];//$eind['actuelePortefeuilleWaardeEuro'];
        //echo "$categorie $datumEind $eindwaarde <br>\n";
        
        $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
          "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
          "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND ". //OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
        $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
        $DB->Query();
        $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
        
        
        $queryRekeningDirecteKostenOpbrengsten = "SELECT
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
        $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
        $DB->Query();
        $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
        
        $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
        $DB->SQL($queryFondsDirecteKostenOpbrengsten);
        $DB->Query();
        $FondsDirecteKostenOpbrengsten = $DB->NextRecord();

//begin
        $queryAttributieStortingenOntrekkingen = "SELECT ".
          "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
          "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
          "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers,0))  AS onttrekking ".
          "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
          "WHERE ".
          "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
          "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
          "Rekeningmutaties.Fonds <> '' AND $rekeningFondsenWhere ";//
        $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
        //$DB->SQL($queryAttributieStortingenOntrekkingen." AND Rekeningmutaties.Grootboekrekening='FONDS' "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
//if($categorie=='G-RISM')
//  listarray($queryAttributieStortingenOntrekkingen);
        $DB->Query();
        $AttributieStortingenOntrekkingen = $DB->NextRecord();
        
        $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
        $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
        $DB->Query();
        $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
        
        
        $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
        
        $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen,
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles)
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                
	              WHERE
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
        $DB->SQL($query);
        //echo "$query <br>\n";
        $DB->Query();
        $data = $DB->nextRecord();
        $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
        $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
        $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
        $AttributieStortingenOntrekkingen['gewogen'] +=$data['gewogen'];
        
        if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
          $DB->SQL($query);
        else
          $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
        $DB->Query();
        $data = $DB->nextRecord();
        
        $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
        $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
        $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];
        $AttributieStortingenOntrekkingenBruto['gewogen'] +=$data['gewogen'];
//end

//       echo $query;
//listarray($AttributieStortingenOntrekkingen);
        
        
        
        $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
        $DB->SQL($queryKostenOpbrengsten);
        $DB->Query();
        $nietToegerekendeKosten = $DB->NextRecord();
        
        
        //  echo $categorie. " ".count($fondsData['rekeningen'])."<br>\n";
        
        if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
        {
          
          //     $RekeningDirecteKostenOpbrengsten['kostenTotaal']+= $nietToegerekendeKosten['kostenTotaal'];
          $AttributieStortingenOntrekkingen['totaal']+= $nietToegerekendeKosten['kostenTotaal'];
          $AttributieStortingenOntrekkingen['onttrekking']+= $nietToegerekendeKosten['kostenTotaal'];
          
        }


//	     $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];

          $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
          $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
         // echo "$datumBegin $datumEind $categorie $performance = ((($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde);<br>\n";
        if($categorie<>'totaal')
        {
      //  $gemiddeldeBruto  = $beginwaarde - $AttributieStortingenOntrekkingenBruto['gewogen'];
        $performanceBruto = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingenBruto['totaal']- $RekeningDirecteKostenOpbrengsten['kostenTotaal']) / $gemiddelde);
        }
        if($categorie=='G-RISM')
        {
          
          //    listarray($AttributieStortingenOntrekkingen);
          //  echo " $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
          
        }
// echo $categorie." $datumEind  $performance = ((($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde)<br>\n";
        // listarray($AttributieStortingenOntrekkingen);
        // listarray($AttributieStortingenOntrekkingenBruto);
      }
      

      $weging=$gemiddelde/$this->totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      //echo "$categorie $weging=$gemiddelde/".$this->totaalGemiddelde.";<br>\n";
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      //  echo $categorie.' '.$datumEind.' '.$aandeelOpTotaal.' '.$eindwaarde.'/'.$totaalEindwaarde."<br>\n";
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;
      //echo "$categorie $bijdrage=$resultaat/$gemiddelde*$weging; <br>\n";
      //$overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      //$relContrib=(($performance*$weging)-($indexData['perf']*$indexData['percentage']));//$overPerfPeriode*$weging;
      //$verschilWeging=($weging-$indexData['percentage']);
      //$gerealiseerd=$mutatieData['totalen']['gerealiseerdResultaat'] ;
      //$ongerealiseerd=$ongerealiseerdResultaat - $renteResultaat;//$FondsDirecteKostenOpbrengsten['RENMETotaal'];
      //$resultaatValuta=$resultaat-$gerealiseerd-$ongerealiseerd-
      //           $FondsDirecteKostenOpbrengsten['kostenTotaal']-
      //           $RekeningDirecteKostenOpbrengsten['kostenTotaal']-
      //           $FondsDirecteKostenOpbrengsten['opbrengstTotaal']-
      //           $RekeningDirecteKostenOpbrengsten['opbrengstTotaal']-
      //           $renteResultaat;
      //echo $indexData['categorie']." ".($performance*$weging)." - ".($indexData['perf']*$indexData['percentage'])." <br>\n";
      
      $waarden[$datumEind]=array(
        'beginwaarde'=>$beginwaarde,
        'eindwaarde'=>$eindwaarde,
        'procent'=>$performance,
        'procentBruto'=>$performanceBruto,
        'stort'=>$AttributieStortingenOntrekkingen['totaal'],
        'storting'=>$AttributieStortingenOntrekkingen['storting'],
        'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal'],
        'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
        'resultaat'=>$resultaat,
        'gemWaarde'=>$gemiddelde,
        'weging'=>$weging,
        'aandeelOpTotaal'=>$aandeelOpTotaal,
        'bijdrage'=>$bijdrage);
    }
    
    
    $stapelItems=array('procent','bijdrage','procentBruto');
    $avgItems=array('weging','gemWaarde');
    $somItems=array('resultaat','storting','onttrekking','kosten','opbrengst','stort');
    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=1;
    
    $eersteDatum=true;
    foreach ($waarden as $datum=>$waarde)
    {
      if($eersteDatum==true)
      {
        $perfData['totaal']['beginwaarde']=$waarde['beginwaarde'];
        $eersteDatum=false;
      }
      $perfData['totaal']['eindwaarde']=$waarde['eindwaarde'];
      $perfData['totaal']['aandeelOpTotaal']=$waarde['aandeelOpTotaal'];
      
      
      
      foreach ($somItems as $item)
        $perfData['totaal'][$item] +=$waarde[$item];
      foreach ($stapelItems as $item)
        $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
      foreach ($avgItems as $item)
        $sum[$item] += $waarde[$item];
    }
    foreach ($avgItems as $item)
      $perfData['totaal'][$item]=$sum[$item]/count($waarden);
    
    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
    $perfData['totaal']['categorie']=$fondsData['categorie'];


//stapelen verwijderen
    $stapelenVerwijderen=false;
    if($stapelenVerwijderen==true)
    {
      $perfData['totaal']['procent']=$perfData['totaal']['resultaat']  / $perfData['totaal']['gemWaarde'];
      $perfData['totaal']['bijdrage']=$perfData['totaal']['resultaat']  / $perfData['totaal']['gemWaarde'] * $perfData['totaal']['weging'] *100;
      
      if($categorie=='totaal')
      {
        $perfData['totaal']['procent']=$perfData['totaal']['procent']*100;
        $perfData['totaal']['bijdrage']=$perfData['totaal']['bijdrage']*100;
      }
    }
    if($stapeling == true)
    {
      
      $mutaties=$this->genereerMutatieLijst($van,$tot, $fondsData['fondsen']);
      $perfData['totaal']['gerealiseerdFondsResultaat']=$mutaties['totalen']['fonds'];
      $perfData['totaal']['gerealiseerdValutaResultaat']=$mutaties['totalen']['valuta'];
      
      //historischeWaarde
      $fondsenWhere="AND Fonds IN('".implode('\',\'',$fondsData['fondsen'])."')";
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro - beginPortefeuilleWaardeEuro) AS resultaatEUR,
  SUM(totaalAantal*fondsEenheid*(actueleFonds-beginwaardeLopendeJaar)*actueleValuta) as fondsresultaatEUR".
        " FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='$tot' $fondsenWhere AND".
        " portefeuille = '".$this->portefeuille."' AND "
        ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query); //echo $query;exit;
      $DB->Query();
      $totaal = $DB->nextRecord();
      $ongerealiseerdFondsResultaat = $totaal['fondsresultaatEUR'] ;
      $ongerealiseerdValutaResultaat = $totaal['resultaatEUR']-$totaal['fondsresultaatEUR'] ;
      $perfData['totaal']['ongerealiseerdFondsResultaat']=$ongerealiseerdFondsResultaat;
      $perfData['totaal']['ongerealiseerdValutaResultaat']=$ongerealiseerdValutaResultaat;
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$tot' AND portefeuille = '".$this->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaalA = $DB->nextRecord();
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$van' AND portefeuille = '".$this->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaalB = $DB->nextRecord();
      $perfData['totaal']['opgelopenrente'] = ($totaalA['totaal'] - $totaalB['totaal']) ;
      
      if($categorie=='totaal')
        $filter='';
      else
        $filter=$fondsenWhere;
      $query = "SELECT  Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten, Grootboekrekeningen.Grootboekrekening,".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
         JOIN Grootboekrekeningen ON Grootboekrekeningen.Grootboekrekening=Rekeningmutaties.Grootboekrekening ".
        "WHERE Rekeningen.Portefeuille = '".$this->portefeuille."'  AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$van."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$tot."' $filter AND
        (Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Kosten = '1') GROUP BY  Grootboekrekeningen.Grootboekrekening
        ORDER BY Grootboekrekeningen.Afdrukvolgorde";
      
      $DB2 = new DB();
      $DB2->SQL($query);
      $DB2->Query();
      
      while($grootboek = $DB2->nextRecord())
      {
        if($grootboek['Opbrengst']==1)
        {
          $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] =  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
          $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
        if($grootboek['Kosten']==1)
        {
          $kostenPerGrootboek[$grootboek['Grootboekrekening']] =  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
          $totaalKosten += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
      }
      
      if($categorie <> 'totaal')
      {
        $filter=$rekeningRekeningenWhere;
        $query = "SELECT Rekeningmutaties.Grootboekrekening,".
          "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
          "FROM Rekeningmutaties
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille ".
          "WHERE Rekeningen.Portefeuille = '".$this->portefeuille."'  AND ".
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$van."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$tot."' AND $filter AND
        (Rekeningmutaties.Grootboekrekening='RENTE') GROUP BY  Rekeningmutaties.Grootboekrekening
        ";
        $DB2->SQL($query);
        $DB2->Query();
        while($grootboek = $DB2->nextRecord())
        {
          $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] +=  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
          $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
      }
      
      $perfData['totaal']['opbrengst']=$totaalOpbrengst;
      $perfData['totaal']['grootboekOpbrengsten']=$opbrengstenPerGrootboek;
      $perfData['totaal']['kosten']=$totaalKosten;
      $perfData['totaal']['grootboekKosten']=$kostenPerGrootboek;
      
      
      
      
      
      $perfData['totaal']['perfWaarden']=$waarden;
      return $perfData['totaal'];
    }
    else
      return array(
        'beginwaarde'=>$beginwaarde,
        'eindwaarde'=>$eindwaarde,
        'procent'=>$performance*100,
        'stort'=>$AttributieStortingenOntrekkingen['totaal'],
        'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
        'storting'=>$AttributieStortingenOntrekkingen['storting'],
        'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
        'resultaat'=>$resultaat,
        'gemWaarde'=>$gemiddelde,
        'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
        'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
        'weging'=>$weging,
        'bijdrage'=>$bijdrage*100);
  }
  
  
  function getKwartalen($julBegin, $julEind)
  {
    if($julBegin > $julEind )
      return array();
    $beginjaar = date("Y",$julBegin);
    $eindjaar = date("Y",$julEind);
    $maandenStap=3;
    $stap=1;
    $n=0;
    $teller=$julBegin;
    $kwartaalGrenzen=array();
    $datum=array();
    while ($teller < $julEind)
    {
      $teller = mktime (0,0,0,$stap,0,$beginjaar);
      $stap +=$maandenStap;
      if($teller > $julBegin && $teller < $julEind)
      {
        $grensDatum=date("d-m-Y",$teller);
        $kwartaalGrenzen[] = $teller;
      }
    }
    if(count($kwartaalGrenzen) > 0)
    {
      $datum[$n]['start']=date('Y-m-d',$julBegin);
      foreach ($kwartaalGrenzen as $grens)
      {
        $datum[$n]['stop']=date('Y-m-d',$grens);
        $n++;
        $start=date('Y-m-d',$grens);
        if(substr($start,-5)=='12-31')
          $start=(substr($start,0,4)+1).'-01-01';
        
        $datum[$n]['start']=$start;
      }
      $datum[$n]['stop']=date('Y-m-d',$julEind);
    }
    else
    {
      $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
    }
    return $datum;
  }
  
  function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    
    $i=0;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;
      
      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      else
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
      
      $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      
      if(substr($datum[$i]['start'],5,5)=='12-31')
        $datum[$i]['start']=(substr($datum[$i]['start'],0,4)+1)."-01-01";
      
      $i++;
    }
    return $datum;
  }
  
  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='')
  {
    // loopje over Grootboekrekeningen Opbrengsten = 1
    if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='1';
    
    
    $query = "SELECT Rekeningmutaties.id, Fondsen.Omschrijving, ".
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
      "Rekeningmutaties.Valutakoers ".
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
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id";
    $DB = new DB();
    $DB->SQL($query);
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
      $mutaties['Aantal'] = abs($mutaties['Aantal']);
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
      $mutaties['Rapportagekoers']=1;
      
      switch($mutaties['Transactietype'])
      {
        case "A" :
        case "A/O" :
        case "A/S" :
        case "D" :
        case "S" :
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
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
        case "L" :
        case "V" :
        case "V/O" :
        case "V/S" :
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
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
      if($mutaties['Transactietype'] == "L" || $mutaties['Transactietype'] == "V" || $mutaties['Transactietype'] == "V/S" || $mutaties['Transactietype'] == "A/S")
      {
        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,$rapportageDatumVanaf,$mutaties['id']);
        if($mutaties['Transactietype'] == "A/S")
          $rekenAantal=($mutaties['Aantal'] * -1) ;
        else
          $rekenAantal=$mutaties['Aantal'];
        
        $historischekostprijs = $rekenAantal       * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
        $beginditjaar         = $rekenAantal       * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        
        
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
        
        if($historie['voorgaandejarenActief'] == 0)
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
        $result_historischkostprijs = 0;
        $result_voorgaandejaren = 0;
        $result_lopendejaar = 0;
      }
      
      $fondsResultaat=0;
      $valutaResultaat=0;
      
      if($verkoop_koers <> '' )
      { //listarray($historie);
        //   $historischekostprijsValuta = $mutaties['Aantal']*$historie['historischeWaarde']* $mutaties['Fondseenheid'];//$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
        //   $historischekostprijsValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];
        
        $beginwaardeLopendeJaarValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];
        $fondsResultaat = ($t_verkoop_waardeinValuta-$beginwaardeLopendeJaarValuta)*getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum']);
        
        //   $fondsResultaat = ($t_verkoop_waardeinValuta-$historischekostprijsValuta)*getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum']);
        $valutaResultaat=	($resultaatlopende)-$fondsResultaat;  //$resultaatvoorgaande
      }
      
      if($mutaties['Aantal']==0 && $mutaties['Fondskoers']==0)
      {
        $fondsResultaat=(abs($mutaties['Credit']) - abs($mutaties['Debet']) )* $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
      }
      
      
      $data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
      $data['totalen']['fonds']+=$fondsResultaat;
      $data['totalen']['valuta']+=$valutaResultaat;
      // echo "$rapportageDatumVanaf,$rapportageDatum ($result_voorgaandejaren+$result_lopendejaar) $fondsResultaat $valutaResultaat <br>\n";
      
      // listarray($historie);
      $valutaResultaat=	($resultaatlopende)-$fondsResultaat;
      $totaalFondsResultaat+=$fondsResultaat;
      $totaalValutaResultaat+=$valutaResultaat;
//echo "$fondsResultaat = ($t_verkoop_waardeinValuta-$beginwaardeLopendeJaarValuta)*getValutaKoers(".$mutaties['Valuta']." ,".$mutaties['Boekdatum'].");<br>\n";
    
    
    }
    //   listarray($data);
    
    return $data;
  }



}
?>