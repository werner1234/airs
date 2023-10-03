<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/29 08:07:28 $
File Versie					: $Revision: 1.4 $

$Log: RapportPERF_L88.php,v $
Revision 1.4  2020/03/29 08:07:28  rvv
*** empty log message ***

Revision 1.3  2020/03/28 15:46:18  rvv
*** empty log message ***

Revision 1.2  2020/03/25 16:44:42  rvv
*** empty log message ***

Revision 1.1  2020/03/21 12:35:10  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_86/ATTberekening_L86.php");

class RapportPERF_L88
{

	function RapportPERF_L88($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting over de categorieën";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->att=new ATTberekening_L86($this);
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

		//$DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    $this->addResultaat();
    $indices=$this->indexVergelijking();
    $this->benchmarkGrafiek($indices);

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
    $counterStart=0;
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;
      
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
      else
      {
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
        if(substr($datum[$i]['start'],5,5)=='12-31')
          $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
      }
      
      $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      $i++;
    }
    return $datum;
  }
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];

    $data = $data['Index'];
  
    $bereikdata=array();
    $aantalPunten=0;
    if(count($data)>0)
    {
      foreach($data as $fonds=>$fondaData)
      {
        $aantalPunten=max($aantalPunten,count($fondaData));
        $bereikdata = array_merge($bereikdata, $fondaData);
      }
    }
    else
      $bereikdata =   $data;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'D','');//,array(245,245,245)
    
  //  if(is_array($color[0]))
   // {
   //   $color1= $color[1];
   //   $color = $color[0];
  //  }
    
   // if($color == null)
   //   $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
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

//$maxVal=75;
//$minVal=-25;
//echo "$maxVal - $minVal = ".($maxVal-$minVal);
    $bereik=($maxVal-$minVal);
//if($bereik)
//$bereik=60;
    $minVal=floor($minVal*0.04)/0.04;
    $maxVal=ceil($maxVal*0.04)/0.04;
    $bereik=($maxVal-$minVal);
    
    $lijnen=array(4,5,3,6,7,8);
    foreach($lijnen as $optie)
    {
      if($bereik%$optie==0)
      {
        $horDiv=$optie;
        break;
      }
    }
//echo "$minVal $maxVal $bereik $horDiv";
    
    //$minVal = floor(($minVal-1) * 1.1);
    //$maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($bereik) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($bereik);
    $unit = $lDiag / $aantalPunten;
    
    if($jaar && $aantalPunten < 12)
      $unit = $lDiag / 12;
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = (abs($bereik)/$horDiv);
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
      $this->pdf->Text($XDiag-8, $i, 100-($n*$stapgrootte) ."");
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
        $this->pdf->Text($XDiag-8, $i, 100+($n*$stapgrootte)+0 ."");
      
      $n++;
      if($n >20)
        break;
    }
   // $color= array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
   
    $k=0;
    foreach($data as $fonds=>$punten)
    {
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color[$k]);
      for ($i = 0; $i < count($punten); $i++)
      {
       // $this->pdf->line($XDiag + ($i + 1) * $unit, $YDiag + $hDiag + 2, $XDiag + ($i + 1) * $unit, $YDiag + $hDiag + 3, array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
      //  $this->pdf->TextWithRotation($XDiag + ($i) * $unit - 5 + $unit, $YDiag + $hDiag + 8, $legendDatum[$i], 25);
      //  $this->pdf->TextWithRotation($XDiag + ($i) * $unit - 5 + $unit, $YDiag + $hDiag + 12, $legendJaar[$i], 0);
        $yval2 = $YDiag + (($maxVal - $punten[$i]) * $waardeCorrectie);
        $this->pdf->line($XDiag + $i * $unit, $yval, $XDiag + ($i + 1) * $unit, $yval2, $lineStyle);
        //if ($i>0)
        //   $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
        //  if ($i==count($data1)-1)
        //   $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        $yval = $yval2;
      }
      $k++;
    }
  
    for ($i = 0; $i < $aantalPunten; $i++)
    {
      $this->pdf->line($XDiag + ($i + 1) * $unit, $YDiag + $hDiag + 2, $XDiag + ($i + 1) * $unit, $YDiag + $hDiag + 3, array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
      $this->pdf->TextWithRotation($XDiag + ($i) * $unit - 5 + $unit, $YDiag + $hDiag + 8, $legendDatum[$i], 25);
    //  $this->pdf->TextWithRotation($XDiag + ($i) * $unit - 5 + $unit, $YDiag + $hDiag + 12, $legendJaar[$i], 0);

    }
    

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    
    
    //   $XPage
    // $YPage
    
    $legendaItems=array();
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step, $YPage+$h+14, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+14);
      $this->pdf->Cell(0,3,$item);
      $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
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

function getGrootboeken()
{
  $vertaling=array();
  $db=new DB();
  $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    //if($data['Grootboekrekening']=='BEH')
   //   $data['Omschrijving']="Beheervergoeding Trustpartners";
    if($data['Grootboekrekening']=='BEW')
      $data['Omschrijving']="Servicekosten depotbank";
    //if($data['Grootboekrekening']=='KOST')
    //  $data['Omschrijving']="Transactiekosten bank";      
      
    $vertaling[$data['Grootboekrekening']]=$data['Omschrijving'];
  }
  return $vertaling;
}




 function addResultaat()
 {
  $bedragDecimalen=2;
  if(!isset($this->pdf->__appvar['consolidatie']))
  {
   $this->pdf->__appvar['consolidatie']=1;
   $this->pdf->portefeuilles=array($this->portefeuille);
  }
  $rapParts=explode("-",$this->rapportageDatum);
  
  $kwartaal = ceil(date("n",db2jul($this->rapportageDatum))/3);
  if($kwartaal==1)
    $beginKwartaal=$rapParts[0]."-01-01";
  elseif($kwartaal==2)
    $beginKwartaal=$rapParts[0]."-03-31";
  elseif($kwartaal==3)
    $beginKwartaal=$rapParts[0]."-06-30";
  elseif($kwartaal==4)
    $beginKwartaal=$rapParts[0]."-09-30";
  if(db2jul($beginKwartaal)<db2jul($this->pdf->PortefeuilleStartdatum))
    $beginKwartaal=$this->pdf->PortefeuilleStartdatum;
  
  $vetralingGrootboek=$this->getGrootboeken();

    $this->att->indexPerformance=false;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie');
    $this->waarden['Kwartaal']=$this->att->bereken($beginKwartaal,$this->rapportageDatum,'Hoofdcategorie');
    
   // $categorien=array_keys($this->waarden['Periode']);
    $categorien=array();
    foreach(array_keys($this->att->categorien) as $categorie)
    {
      if($this->waarden['Periode'][$categorie]['procent'] <> 0 || $this->waarden['Periode'][$categorie]['beginwaarde'] <> 0 || $this->waarden['Periode'][$categorie]['eindwaarde'] <> 0)
      {
        $categorien[]=$categorie;
      }
    }

    //listarray($this->att->totalen);exit;
//listarray($this->waarden['Periode']);

 // $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
 //   $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
 //   $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("","");
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
  $rendementKwartaal=array("",vertaalTekst("Rendement lopend kwartaal",$this->pdf->rapport_taal));
  
  $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde resultaten",$this->pdf->rapport_taal)); //
  //$ongerealiseerdValuta=array("",vertaalTekst("Ongerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
  
$gerealiseerd=array("",vertaalTekst("Gerealiseerde resultaten",$this->pdf->rapport_taal)); //
//$gerealiseerdValuta=array("",vertaalTekst("Gerealiseerde valutaresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten vreemde valuta rekeningen",$this->pdf->rapport_taal)); //
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
    $perbegin[]=$this->formatGetal($perfWaarden['beginwaarde'],$bedragDecimalen,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden['eindwaarde'],$bedragDecimalen,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden['eindwaarde']-$perfWaarden['beginwaarde'],$bedragDecimalen,true);
    $mutwaarde[]='';
    
    if($categorie=='totaal')
    {
      $effectenmutaties[]='';
      $effectenmutaties[]=''; 
     //$stort=getStortingen($this->rapport->portefeuille, $datumBegin, $datumEind)
     //$onttr=getOnttrekkingen($this->rapport->portefeuille, $datumBegin, $datumEind)
      $stortingen[]=$this->formatGetal($perfWaarden['storting'],$bedragDecimalen);
      $stortingen[]='';
      $onttrekking[]=$this->formatGetal($perfWaarden['onttrekking'],$bedragDecimalen);
      $onttrekking[]='';
    }
    else
    {
      $effectenmutaties[]=$this->formatGetal($perfWaarden['stort'],$bedragDecimalen);
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
    
    $resultaat[]=$this->formatGetal($perfWaarden['resultaat'],$bedragDecimalen);
    $resultaat[]='';
//echo "$categorie <br>\n";ob_flush();
    if($categorie=='H-Liq')
    {
      $rendement[]='';
      $rendement[]='';
      $rendementKwartaal[]='';
      $rendementKwartaal[]='';
    }
    else
    {
    $rendement[]=$this->formatGetal($perfWaarden['procent'],2);
    $rendement[]='%';
    $rendementKwartaal[]=$this->formatGetal($this->waarden['Kwartaal'][$categorie]['procent'],2);
    $rendementKwartaal[]='%';
    }
    if($categorie=='totaal')
    {
    $ongerealiseerd[]=$this->formatGetal($perfWaarden['ongerealiseerdFondsResultaat']+$perfWaarden['ongerealiseerdValutaResultaat'],$bedragDecimalen);
    $ongerealiseerd[]='';
    //$ongerealiseerdValuta[]=$this->formatGetal($perfWaarden['ongerealiseerdValutaResultaat'],0);
    //$ongerealiseerdValuta[]='';
    $gerealiseerd[]=$this->formatGetal($perfWaarden['gerealiseerdFondsResultaat']+$perfWaarden['gerealiseerdValutaResultaat'],$bedragDecimalen);
    $gerealiseerd[]='';
    //$gerealiseerdValuta[]=$this->formatGetal($perfWaarden['gerealiseerdValutaResultaat'],0);
    //$gerealiseerdValuta[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden['resultaatValuta'],$bedragDecimalen);
    $valutaResultaat[]='';
    $rente[]=$this->formatGetal($perfWaarden['opgelopenrente'],$bedragDecimalen);
    $rente[]='';
    //$totaalOpbrengst[]='';
    //$totaalOpbrengst[]='';
    $totaalOpbrengst[]=$this->formatGetal($totaalOpbrengstEUR,$bedragDecimalen);
    $totaalOpbrengst[]='';
   // $totaalKosten[]='';
   // $totaalKosten[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden['kosten'],$bedragDecimalen);
    $totaalKosten[]='';
   // $totaal[]='';
   // $totaal[]='';
    $totaal[]=$this->formatGetal($perfWaarden['resultaat'],$bedragDecimalen);
    $totaal[]='';
    
    foreach($perfWaarden['grootboekOpbrengsten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($perfWaarden['grootboekKosten'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;  
    }
    
  } 


  	$this->pdf->widthB = array(0,70,24,6,24,6,24,6,24,6,24,6,24,6,24,6,24,6);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;//array(0,65,30,6,30,6,30,6,30,6,30,6,30,6);
		$this->pdf->alignA = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
  

//listarray($perfWaarden);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  	
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,245,245);
    $this->headerTop=$this->pdf->GetY();

//    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
//    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
//		$this->pdf->Rect($this->pdf->marge+70, $this->pdf->getY(), (count($header)-2)*15, 8 , 'F');
	   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row($header);
  //  unset($this->pdf->fillCell);
//    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

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
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();

    $this->pdf->CellBorders = array();
    //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		//$this->pdf->CellBorders = $volOnder;
    $this->pdf->row($rendementKwartaal);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
		$ypos = $this->pdf->GetY();


		$this->pdf->SetY($ypos);
		$this->pdf->ln();


		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
//    $this->pdf->fillCell=$fillArray;
//    $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		$this->pdf->row($samenstelling);//,"","","",""));
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
		$this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
		//$this->pdf->row($ongerealiseerdValuta);
    $this->pdf->row($gerealiseerd);
    //$this->pdf->row($gerealiseerdValuta);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
	//	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
	  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));
		$keys=array();
		//foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		//  $keys[]=$key;

    $categorien=array('totaal');
    foreach ($opbrengstCategorien as $grootboek)
	  {
		    $tmp=array("",vertaalTekst($vetralingGrootboek[$grootboek],$this->pdf->rapport_taal));
       // foreach($perfWaarden as $port=>$waarden)
       
        foreach($categorien as $categorie)
        {
          $perfWaarden=$this->waarden['Periode'][$categorie];
          $tmp[]=$this->formatGetal($perfWaarden['grootboekOpbrengsten'][$grootboek],$bedragDecimalen);
          $tmp[]='';
        }
		  //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			  $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
		}
    $subBovenTotalen=array('','','TS');
    $this->pdf->CellBorders = $subBovenTotalen;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));

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
       
        $tmp[]=$this->formatGetal($perfWaarden['grootboekKosten'][$grootboek],$bedragDecimalen);
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
    $this->pdf->CellBorders = $subBovenTotalen;
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
  
  
  
  function benchmarkGrafiek($indices)
  {
    /*
    $db=new DB();
    $query="SELECT
    Fondsen.Fonds,
    Portefeuilles.SpecifiekeIndex,
    Portefeuilles.Portefeuille,
    Fondsen.Omschrijving as fondsOmschrijving
    FROM
    Portefeuilles
    INNER JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
    WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    
    $db->SQL($query);
    $db->Query();
    $index=$db->lookupRecord();
    */
    $indexData=array();
  
    $lijnkeuren=array();
    foreach($indices as $regel)
    {
      if($regel['Beursindex']<>'')
      {
        $kleur=unserialize($regel['grafiekKleur']);
        $regel['kleur']=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
        $indexData[$regel['Beursindex']] = $regel;
        $lijnkeuren[]=$regel['kleur'];
      }
    }

    if(count($indexData)>0)
    {
      $maanden=$this->getMaanden(db2jul('2011-01-01'),db2jul($this->rapportageDatum));
      foreach($maanden as $periode)
      {
        foreach($indexData as $fonds=>$fondsDetails)
        {
          $tmp = array('fondsKoers_eind' => $this->getFondsKoers($fonds, $periode['stop']), 'fondsKoers_begin' => $this->getFondsKoers($fonds, $periode['start']));
          $periodePerf = ($tmp['fondsKoers_eind'] - $tmp['fondsKoers_begin']) / ($tmp['fondsKoers_begin']);
          $indexMaanden[$periode['stop']][$fonds] = $periodePerf;
          
        }
      }
      
     
      $stapeling=array();//=1;
      foreach($indexMaanden as $maand=>$fondsen)
      {
  
        foreach($fondsen as $fonds=>$maandPerf)
        {
          if(!isset($stapeling[$fonds]))
            $stapeling[$fonds]=1;
          $stapeling[$fonds] = $stapeling[$fonds] * (1 + $maandPerf);
          
     
          $indexStapeling[$fonds][$maand] = ($stapeling[$fonds] - 1) * 100;
  
          $grafiekData['Index'][$fonds][] = ($stapeling[$fonds] - 1) * 100;
        }
        if(substr($maand,5,2)=='01')
          $grafiekData['Datum'][]=substr($maand,0,4);
        else
          $grafiekData['Datum'][]='';
        
      }
      //  listarray($indexMaanden);
      //  listarray($indexStapeling);
      // listarray(array_sum($indexMaanden));
      //  $this->pdf->SetXY(170,130);

      //  $this->pdf->MultiCell(110,4,$index['fondsOmschrijving'],0,'C',0);
      $y=$this->pdf->getY();
      $this->pdf->SetXY(156-30,$y+5);//145);
      //$color=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      //$color=array(0,0,0);
      $this->LineDiagram(122+30, 40+20, $grafiekData,$lijnkeuren,0,0,4,4,0);
    }
  }
 
  function indexVergelijking()
  {
    $DB=new DB();
    
	  $perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
	  $query="SELECT
Indices.Beursindex,
Indices.specialeIndex,
Fondsen.Omschrijving,
Fondsen.Valuta,
BeleggingscategoriePerFonds.grafiekKleur,
Indices.toelichting
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
LEFT JOIN BeleggingscategoriePerFonds ON Indices.Beursindex=BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
   	$DB->SQL($query);
		$DB->Query();
    $indices=array();
	  while($index = $DB->nextRecord())
      $indices[]=$index;

$query="SELECT Portefeuilles.specifiekeIndex as Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta 
FROM Portefeuilles 
Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds  
WHERE Portefeuilles.Portefeuille = '$this->portefeuille'";
   	$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
    {

      $indices[]=$index;
    }

	  foreach($indices as $index)
		{
		  if($index['specialeIndex']==1)
      {
   	    $specialeBenchmarks[]=$index['Beursindex'];
		   	$specialeIndexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
          $specialeIndexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
  	  	$specialeIndexData[$index['Beursindex']]['performance'] =     ($specialeIndexData[$index['Beursindex']]['fondsKoers_eind'] - $specialeIndexData[$index['Beursindex']]['fondsKoers_begin']) / ($specialeIndexData[$index['Beursindex']]['fondsKoers_begin']/100 );
      }
      else
      {  
		    $benchmarks[]=$index['Beursindex'];
		   	$indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
          //$indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
  	  	$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		}
      //$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}
  
    
		$this->pdf->SetY(93);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->SetWidths(array(126,65,25,25,25));
  	$this->pdf->SetAligns(array('L','L','R','R','R'));
    $this->pdf->Rect($this->pdf->marge+120,93,150,count($benchmarks)*4+4.5);
 	  $this->pdf->row(array("","Vergelijkingsmaatstaven","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
  	unset($this->pdf->CellBorders);   
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 
  	foreach ($benchmarks as $fonds)
  	{  
        $fondsData=$indexData[$fonds];
        if($fondsData['Omschrijving']=='')
        {
          $this->pdf->row(array(''));
        }
        else
        {
          $kleur=unserialize($fondsData['grafiekKleur']);
        //  listarray($kleur);
          $this->pdf->Rect($this->pdf->marge+121, $this->pdf->getY()+.5, 3, 3 ,'F','',array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']));
          $this->pdf->row(array('', $fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['fondsKoers_begin'], 2),
                            $this->formatGetal($fondsData['fondsKoers_eind'], 2),
                            $this->formatGetal($fondsData['performance'], 2) . "%"));
        }
    }
    
    
    if(count($specialeBenchmarks) > 0)
    {
   	 	$this->pdf->SetY(150);
    	 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	 	$this->pdf->SetWidths(array(150,60,20,20,20));
  	 	$this->pdf->SetAligns(array('L','L','R','R','R'));
     	$this->pdf->Rect($this->pdf->marge+150,150,120,count($specialeBenchmarks)*4+4);
 	   	$this->pdf->row(array("","Overige marktindices ter informatie","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
  	 	unset($this->pdf->CellBorders);   
  	  	 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 
  	 	foreach ($specialeBenchmarks as $fonds)
  	 	{  
        $fondsData=$specialeIndexData[$fonds];
        if($fondsData['Omschrijving']=='')
        {
          $this->pdf->row(array(''));
        }
        else
        {
        //  listarray($fondsData);
          
          $this->pdf->row(array('', $fondsData['Omschrijving'],
                            $this->formatGetal($fondsData['fondsKoers_begin'], 2),
                            $this->formatGetal($fondsData['fondsKoers_eind'], 2),
                            $this->formatGetal($fondsData['performance'], 2) . "%"));
        }
     	}
    }
    return $indices;
    
  }
  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }


}
?>