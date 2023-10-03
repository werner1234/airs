<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.2 $

$Log: RapportPERF_L47.php,v $
Revision 1.2  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.1  2013/05/04 15:59:49  rvv
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
include_once("rapport/include/ATTberekening_L42.php");

class RapportPERF_L47
{

	function RapportPERF_L47($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Resultaat en rendementsberekening";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L42($this);
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
    $this->pdf->templateVars['PERFPaginas0']=$this->pdf->page;
    $this->pdf->templateVars['PERFPaginas0']=$this->pdf->page;
    $this->pdf->templateVars['PERFPaginas0']=$this->pdf->page;
    

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    $this->addResultaat();

    
    $this->getOIBdata($this->rapportageDatum);
    $this->toonPie();
    
    $gebruikteCategorie=$this->addZorgBar();
    $this->plotZorgBar2(4,50,$gebruikteCategorie);
	}
  
  function getKleuren()
  {
    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
  }
  
  function toonPie()
  {
    $DB = new DB();
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];

		$query = "SELECT Beleggingscategorie,Omschrijving as value FROM Beleggingscategorien";
    $DB->SQL($query);
    $DB->Query();
	  while($data=$DB->nextRecord())
      $omschrijving[$data['Beleggingscategorie']]=$data['value'];
    $omschrijving['geenCat']='Geen categorie';
    $omschrijving['geen-Hcat']='Geen categorie';
    $omschrijving['totaal']='Totaal';


    $this->pdf->SetWidths(array(60,20,20));

 	  $grafiekData=array();
 	  $grafiekKleurData=array();
	  foreach ($this->tabelData[$this->rapportageDatum] as $hcategorie=>$catData)
	  {
      foreach ($catData as $categorie=>$data)
      {
  	    $grafiekData[$omschrijving[$categorie]]=round($data['precentage']*100,1);
	      $grafiekKleurData[]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
      }
	  }
	  if(round($this->portTotaal[$this->rapportageDatum])!=0)
	  {
	    $this->pdf->setXY(215,50-5);
	    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	    $this->pdf->Cell(50,4,"Portefeuille ".date("d/m/Y",$this->pdf->rapport_datum),0,0,'C');
	    $this->pdf->setXY(220,50);
	    $this->pdf->PieChart(50, 50, $grafiekData, '%l (%p)',$grafiekKleurData);
	  }
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


  function addZorgBar()
  {
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
	  $pdata=$this->pdf->portefeuilledata;
	  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum);
    $gebruikteCategorien=array();
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
      if(!isset($data['fondsGekoppeld']))
      {
        $gebruikteCategorien[$categorie]=$data;
      }
    }
    foreach($zpwaarde['conclusie'] as $data)
    {
      foreach($gebruikteCategorien as $categorie=>$gebruikteCategorie)
      {
        if($data[0]==$gebruikteCategorie['Zorgplicht'])
        {
          $gebruikteCategorien[$categorie]['percentage']=$data[2];
          $gebruikteCategorien[$categorie]['conclusie']=$data[5];
        }
      }
    }   
    return $gebruikteCategorien;
  }

  function getOIBdata($datum)
	{
	  global $__appvar;
	  $DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$datum."' AND ".
								 " portefeuille = '".$this->portefeuille."'"
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$portefwaarde = $DB->nextRecord();
		$portTotaal = $portefwaarde['totaal'];
		$this->portTotaal[$datum]=$portTotaal;

		$query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Omschrijving,
hoofdCat.Omschrijving as HcatOmschrijving,
hoofdCat.Afdrukvolgorde as HcatVolgorde
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Inner Join Beleggingscategorien as hoofdCat ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdCat.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY HcatVolgorde,Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
      $this->categorien[$categorien['Beleggingscategorie']]=$categorien['Omschrijving'];
      $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['Beleggingscategorie']]=array();
      $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['Beleggingscategorie']]=array();
		}

		$query = "SELECT TijdelijkeRapportage.portefeuille,
		    TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving,
			TijdelijkeRapportage.beleggingscategorie as beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, TijdelijkeRapportage.Hoofdcategorie,
		   	TijdelijkeRapportage.HoofdcategorieOmschrijving ".
			" FROM TijdelijkeRapportage
			WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$datum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY beleggingscategorie ".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  $this->hoofdcategorien[$categorien['Hoofdcategorie']]=$categorien['HoofdcategorieOmschrijving'];
		  $this->categorien[$categorien['beleggingscategorie']]=$categorien['Omschrijving'];
		  if ($categorien['beleggingscategorie']=='')
		    $categorien['beleggingscategorie']='geenCat';
		  if ($categorien['Hoofdcategorie']=='')
		    $categorien['Hoofdcategorie']='geenHCat';

		  if($categorien['beleggingscategorie']=='Rente')
		  {
		    $categorien['Omschrijving']='Opgelopen Rente';
		    $categorien['beleggingscategorie']='Opgelopen Rente';
		  }
		  //$catogorieData
    //$catogorieData[$categorien['beleggingscategorie']]['port']['waarde']+=$categorien['subtotaalactueel'];
    //$hoofdCatogorieData[$categorien['Hoofdcategorie']]['port']['waarde']+=$categorien['subtotaalactueel'];

    $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['waarde']+=$categorien['subtotaalactueel'];
    $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['precentage']=$this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['waarde']/$portTotaal;
		}
	}

  function plotZorgBar2($barWidth,$height,$zorgdata)
  {
    $DB=new DB();
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  $DB->SQL($query);
	  $DB->Query();
	  while($data = $DB->NextRecord())
    {
      $categorien[$data['Zorgplicht']]=$data['Omschrijving'];
    }

    $this->pdf->setXY(235,120);
    $this->pdf->Cell(5,5,"Mandaat controle",0,0,'C');
    $h=2*30+10;
    $this->pdf->Rect(197.5,$this->pdf->GetY()+5,75,$h);
    $this->pdf->setXY(210,$this->pdf->GetY()+19);
  
    $hProcent=$height/100;

    $marge=1;
    $extraY=8;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();   
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
     $n=0;
     foreach($zorgdata as $categorie=>$data)
     {
       if($n > 1)
         break;
       $n++;  
       $groen=array(0,71,123);
       $rood=array(231,117,65);
    $data['percentage']=str_replace(',','.',$data['percentage']);
     
    $this->pdf->setXY($xPage-$marge-4,$yPage-2);
    $this->pdf->Rect($xPage, $yPage, $hProcent*100, $barWidth, 'D');
    $this->pdf->Rect($xPage, $yPage+$extraY,$hProcent*100, $barWidth,  'D');
        $this->pdf->setXY($xPage-2,$yPage-$marge-8);
    $this->pdf->cell(4,4,$categorien[$categorie],0,0,'L');
    $this->pdf->setXY($xPage-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"0",0,0,'R');
    $this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"100",0,0,'R');
    $this->pdf->setXY($xPage+$hProcent*$data['Minimum']-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"".$data['Minimum'],0,0,'R');
    $this->pdf->setXY($xPage+$hProcent*$data['Maximum']-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"".$data['Maximum'],0,0,'R');
     
    //$this->pdf->setXY($xPage+$hProcent*$data['Norm']-2,$yPage+$marge+5);
    //$this->pdf->cell(4,4,"Norm ".$data['Norm'],0,0,'R');
    
    $this->pdf->SetFillColor($rood[0],$rood[1],$rood[2]);
    $this->pdf->Rect($xPage, $yPage, $hProcent*$data['Minimum'], $barWidth,  'DF');
    $this->pdf->SetFillColor($groen[0],$groen[1],$groen[2]);
    $this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage,$hProcent*($data['Maximum']-$data['Minimum']), $barWidth,   'DF');
    $this->pdf->SetFillColor($rood[0],$rood[1],$rood[2]);
    $this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage, $hProcent*(100-$data['Maximum']),$barWidth,  'DF');
     
    //$this->pdf->Line($xPage+$hProcent*$data['Norm'], $yPage,$xPage+$hProcent*$data['Norm'],$yPage+$barWidth);
    if($data['conclusie']=='Voldoet')
      $this->pdf->SetFillColor($groen[0],$groen[1],$groen[2]);
    else
      $this->pdf->SetFillColor($rood[0],$rood[1],$rood[2]);  
    $this->pdf->Rect($xPage,$yPage+$extraY , $hProcent*$data['percentage'], $barWidth,  'DF');
    $this->pdf->setXY($xPage+$hProcent*$data['percentage']-2,$yPage+$barWidth+$marge+$extraY);
    $this->pdf->cell(4,4,$data['percentage'].' werkelijk',0,0,'L');
    $yPage+=30;
    }
  }
  
  function RectRotate($x,$y,$w,$h,$f)
  {
   // $this->pdf->Rect($x,$y-$h,$h,$w,$f);
  }


function addResultaat()
{
  
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  
   $vetralingGrootboek=$this->getGrootboeken();
  
    $att=new ATTberekening_L42($this);
    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum);
    $categorien=array_keys($this->waarden['Periode']);
    
    //listarray($this->att->totalen);exit;
//listarray($this->waarden['Periode']);

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
    $header[]=$this->att->categorien[$categorie];
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
   // $perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
  }

  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
  $effectenmutaties=array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
  
  
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $ongerealiseerdFonds=array("",vertaalTekst("Ongerealiseerde fonds resultaten",$this->pdf->rapport_taal)); //
  $ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valuta resultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerdFonds=array("",vertaalTekst("Gerealiseerde fonds resultaten",$this->pdf->rapport_taal)); //
$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valuta resultaten",$this->pdf->rapport_taal)); //
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
     //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
     //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],0);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],0);
      $onttrekking[]='';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal($perfWaarden['onttrekking']+$perfWaarden['storting'],0);
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
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],0);
    $resultaat[]='';
    $rendement[]=$this->formatGetal($perfWaarden['procent'],2);
    $rendement[]='%';
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


  	$this->pdf->widthB = array(0,70,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,5,30,5,30,5,30,5,30,5,30,5);
		$this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R');
  
  $this->pdf->ln();
//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
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
		$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
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
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		$this->pdf->row($samenstelling);//,"","","",""));
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
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
		$this->pdf->ln();
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
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
		$actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

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



}
?>