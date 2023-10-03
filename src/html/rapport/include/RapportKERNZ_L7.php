<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/03 11:17:45 $
File Versie					: $Revision: 1.6 $

$Log: RapportKERNZ_L7.php,v $
Revision 1.6  2020/05/03 11:17:45  rvv
*** empty log message ***

Revision 1.5  2020/05/02 15:57:50  rvv
*** empty log message ***

Revision 1.4  2020/04/08 15:42:42  rvv
*** empty log message ***

Revision 1.3  2020/04/04 17:43:15  rvv
*** empty log message ***

Revision 1.2  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.1  2020/02/22 18:46:19  rvv
*** empty log message ***

Revision 1.19  2020/02/15 18:29:05  rvv
*** empty log message ***

Revision 1.18  2020/02/08 10:33:21  rvv
*** empty log message ***

Revision 1.17  2019/12/21 14:08:32  rvv
*** empty log message ***

Revision 1.16  2019/12/07 17:48:23  rvv
*** empty log message ***

Revision 1.15  2019/11/24 14:27:15  rvv
*** empty log message ***

Revision 1.14  2019/11/23 18:36:42  rvv
*** empty log message ***

Revision 1.13  2019/11/09 16:39:21  rvv
*** empty log message ***

Revision 1.12  2019/04/13 17:42:49  rvv
*** empty log message ***

Revision 1.11  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.10  2019/02/20 16:51:10  rvv
*** empty log message ***

Revision 1.7  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.6  2018/07/12 06:37:55  rvv
*** empty log message ***

Revision 1.5  2018/07/11 16:16:40  rvv
*** empty log message ***

Revision 1.4  2018/06/28 05:39:31  rvv
*** empty log message ***

Revision 1.3  2018/06/27 16:13:50  rvv
*** empty log message ***

Revision 1.2  2018/06/24 12:47:04  rvv
*** empty log message ***

Revision 1.1  2018/05/26 17:24:24  rvv
*** empty log message ***

Revision 1.13  2017/12/09 17:54:25  rvv
*** empty log message ***

Revision 1.12  2017/10/01 14:29:55  rvv
*** empty log message ***

Revision 1.11  2017/04/12 15:38:14  rvv
*** empty log message ***

Revision 1.10  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.9  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.8  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.7  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.6  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.5  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.2  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportKERNZ_L7
{
	function RapportKERNZ_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "";//Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
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



	function portefeuilleWaarden($portefeuille)
  {
    $portefeuilleWaarden['belCatWaarde']=array();
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,substr($this->rapportageDatum,5,5)=='01-01'?true:false);
    foreach($gegevens as $waarde)
    {
      $portefeuilleWaarden['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];

    }

    $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatumVanaf,substr($this->rapportageDatumVanaf,5,5)=='01-01'?true:false);
    foreach($gegevens as $waarde)
    {
      $portefeuilleWaarden['totaleWaardeBegin']+=$waarde['actuelePortefeuilleWaardeEuro'];
    }
    $kruispost=true;
    $waardeEind				= $portefeuilleWaarden['totaleWaarde'];
    $waardeBegin 			 	= $portefeuilleWaarden['totaleWaardeBegin'];
    $waardeMutatie 	   	= $waardeEind - $waardeBegin;
    $stortingen 			 	= getStortingenKruis($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,$kruispost);
    $onttrekkingen 		 	= getOnttrekkingenKruis($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,$kruispost);
    $interneboeking     = $stortingen['kruispost']-$onttrekkingen['kruispost'];
    $portefeuilleWaarden['stortingen']         = $stortingen['storting'];
    $portefeuilleWaarden['onttrekkingen']      = $onttrekkingen['onttrekking'];
  
    $portefeuilleWaarden['resultaatVerslagperiode'] = $waardeMutatie - $stortingen['storting'] + $onttrekkingen['onttrekking'] -$interneboeking;
    //echo "perf: $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen <br>\n";
    if(substr($this->rapportageDatum,0,4) < 2016)
      $perfBerekening=2;
    else
      $perfBerekening=$this->pdf->portefeuilledata['PerformanceBerekening'];
  
    $portefeuilleWaarden['rendementProcent']  	= performanceMeting_L7($portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $perfBerekening,$this->pdf->rapportageValuta,$kruispost);
    return $portefeuilleWaarden;
  }
	
	
  
	function writeRapport()
	{
		global $__appvar;
		
		$DB = new DB();
   
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
    $this->pdf->AddPage();
    $this->pdf->templateVars['KERNZPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['KERNZPaginas']=$this->pdf->rapport_titel;
    

	  $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  $DB->SQL($q);
  	$DB->Query();
   	$kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
    $this->pdf->grafiekKleuren=$kleuren;
    $this->categorieKleuren=$kleuren['OIB'];

		if(is_array($this->pdf->portefeuilles))
			$consolidatie=true;
		else
			$consolidatie=false;
    
    $portefeuilleWaarden=array();

    if($consolidatie)
    {
      $aantalPortefeuilles=count($this->pdf->portefeuilles);
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $portefeuilleWaarden[$portefeuille]=$this->portefeuilleWaarden($portefeuille);
      }
      $portefeuilleWaardenTotaal[$this->portefeuille]=$this->portefeuilleWaarden($this->portefeuille);
    }
    else
    {
      $portefeuilleWaarden[$this->portefeuille]=$this->portefeuilleWaarden($this->portefeuille);
      $aantalPortefeuilles=1;
    }
    
 
  //2+35+extraw
    $categorieVolgorde=array('Beginvermogen'=>'totaleWaardeBegin','Stortingen'=>'stortingen','Onttrekkingen'=>'onttrekkingen','Resultaat'=>'resultaatVerslagperiode','Eindvermogen'=>'totaleWaarde','Rendement'=>'rendementProcent');
    $aantalCategorieen=count($categorieVolgorde);
    $paginaWidth=(35+2)+(35+2)*($aantalCategorieen);
    $maxWidth=297-$this->pdf->marge*2;
    $extraRuimte=$maxWidth-$paginaWidth;
    //echo $paginaWidth." ";
    
    $maxPortefeuilles=400;
    $extraW=$extraRuimte/($aantalCategorieen+2);
    //echo $extraW;exit;
		// voor kopjes
		$pw=14;
    $eurw=5;
		$portw=23;
		

		$this->pdf->widthA = array(30+3,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor data
    







  //  if(is_array($this->pdf->portefeuilles))
  //  {


		//  }
		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
 		$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);



		$regelDataTotaal=array();
		$totaalPercentage=0;

    $portefeuilleAantal=count($portefeuilleWaarden);
   // echo $portefeuilleAantal."<br>\n";listarray($portefeuilleWaarden);exit;
    
    $portrefeuilleDataPerBlok=array();
    $i=0;
		$blokken=ceil($portefeuilleAantal/$maxPortefeuilles);
		$n=1;
    foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
    {
      $portrefeuilleDataPerBlok[$i][$portefeuille]=$belCatData;
      if($n%$maxPortefeuilles==0)
        $i++;
      $n++;
    }
		
  
		for($i=0;$i<$blokken;$i++)
		{
      $portefeuilleWaarden= $portrefeuilleDataPerBlok[$i];
   
		  if($i>0)
		    $this->pdf->addPage();
			//Kop regel
			$regel = array();
  
			array_push($regel, 'Portefeuille');
	  

			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
      
      $barGraph=false;

      if(1)//count($this->pdf->portefeuilles) < 60)
      {
  
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
  
        $this->pdf->SetFillColor($this->pdf->rapport_kop_kleur[0],$this->pdf->rapport_kop_kleur[1],$this->pdf->rapport_kop_kleur[2]);
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor(255,255,255);
        
        $this->pdf->SetX($this->pdf->marge);
        $this->pdf->Cell(37+$extraW, 6, vertaalTekst("Portefeuille", $this->pdf->rapport_taal), 0, 0, "L",1);
  
        foreach($categorieVolgorde as $categorie=>$veld)
        {

          $this->pdf->Cell(2, 6, '', 0, 0, "C", 0);
          $this->pdf->Cell(35 + $extraW, 6,$categorie, 0, 0, "C", 1);
        }
   

        $this->pdf->Ln();
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  

     
        $categorieTotalen=array();
        foreach ($portefeuilleWaarden as $portefeuille=>$pdata)
        {

  
          $this->pdf->SetX($this->pdf->marge);
          $this->pdf->Cell(37+$extraW, 4, $portefeuille, 0, 0, "L",0);
        
          foreach($categorieVolgorde as $categorie=>$veld)
          {
           if($veld=='rendementProcent')
             $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($portefeuilleWaarden[$portefeuille][$veld],1).' %', 0, 0, "R");
           else
             $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($portefeuilleWaarden[$portefeuille][$veld],0), 0, 0, "R");
          }
          $this->pdf->ln(4);
        }
        $this->pdf->Ln(3);
  
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
        $this->pdf->Cell(37+$extraW, 4,vertaalTekst('Totaal' ,$this->pdf->rapport_taal), 0, 0, "L",0);
        foreach($categorieVolgorde as $categorie=>$volgorde)
        {
  
          foreach($categorieVolgorde as $categorie=>$veld)
          {
            if($veld=='rendementProcent')
              $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($portefeuilleWaardenTotaal[$this->portefeuille][$veld],1).' %', 0, 0, "R");
            else
              $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($portefeuilleWaardenTotaal[$this->portefeuille][$veld],0), 0, 0, "R");
          }
          
          
        
        }
  
        $randomKleuren=array();
        foreach($this->pdf->grafiekKleuren['OIB'] as $categorie=>$kleur)
          $randomKleuren[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
        $i=0;
        foreach($portefeuilleWaarden as $portefeuille=>$pData)
        {
  
          $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
          $DB->SQL($query);
          $DB->Query();
          while($tmp = $DB->nextRecord())
            $portefeuilledata[$tmp['Portefeuille']]=$tmp;
          
          
          
          //$percentage=$portefeuilleWaarden[$portefeuille]['resultaatVerslagperiode']/$portefeuilleWaardenTotaal[$this->portefeuille]['resultaatVerslagperiode']*100;
          $percentage=$portefeuilleWaarden[$portefeuille]['rendementProcent'];
          $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($percentage,1).' %', 0, 0, "R");
  
          if($percentage<0)
            $barGraph=true;
  

          
  
          $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
          //$kleur=array();
          if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
            $kleur = $randomKleuren[$i];
  
          if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
            $kleur = array(rand(0, 255), rand(0, 255), rand(0, 255));
          
          $categorieVerdeling['percentage'][$portefeuille]=$percentage;
          $categorieVerdeling['kleur'][]=$kleur;
          $categorieVerdeling['kleurBar'][$portefeuille]=$kleur;
  
          $i++;
        }
       // $this->pdf->Cell(37+$extraW, 4, 'aaabbb'.$this->formatGetal($totaalWaarde/$totaalWaarde*100,1).' %', 0, 1, "R");
        
     
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			
			
		}
    unset($this->pdf->CellFontStyle);
    
    if($aantalPortefeuilles>15)
    {
      $this->pdf->addPage();
      $grafiekY=40;
      $grafiekH=$aantalPortefeuilles*6;
    }
    else
    {
      $grafiekY = 120;
      $grafiekH=65;
    }
    $this->pdf->setY($grafiekY-10);
    $this->pdf->SetAligns(array('C','C'));
    $this->pdf->SetWidths(array(140,140));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
    $this->pdf->row(array(vertaalTekst("Rendementsverdeling",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    

    
    if($barGraph==false)
    {
      $this->pdf->setXY(20,$grafiekY);
      $this->PieChart(65, $grafiekH, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);
    }
    else
    {
      $this->pdf->setXY(50,$grafiekY);
      $this->BarDiagram(80, $grafiekH, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
    }
    
   
    
    
  }
  
  function SetLegends2($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.1f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
    }
  }
  
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {
    
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
    $this->SetLegends2($data,$format);
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $nbDiv=5;
    $legendWidth=10;
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);
    $maxVal=0;
    $minVal=0;

    if ($maxVal == 0) {
      $maxVal = max($data)*1.1;
    }
    if ($minVal == 0) {
      $minVal = min($data)*1.1;
    }
    if($minVal > 0)
      $minVal=0;
    
    $maxVal=ceil($maxVal/10)*10;
    if($maxVal<0)
      $maxVal=0;
  //  echo "$minVal $maxVal <br>\n ";exit;
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = floor($hDiag / ($this->pdf->NbVal + 1));

    if($hBar>5)
      $hBar=5;
    $hDiag = $hBar * ($this->pdf->NbVal + 1);
    
    //echo "$hBar <br>\n";
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
    
    $legendaStep=$unit/$nbDiv*$bandBreedte;
    //if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*5;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*2;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //   $legendaStep=$legendaStep/2*5;
    $valIndRepere=round($valIndRepere/$unit/5)*5;
    
    
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    
    $i=0;
    $nbDiv=10;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($legendaStep,5) <> 0.0)
    {
      //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
      for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
        $i++;
        if($i>100)
          break;
      }
      
      $i=0;
      //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
      for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
        
        $i++;
        if($i>100)
          break;
      }
    }
    
    $i=0;
    
    $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
//listarray($colorArray);listarray($data);
    foreach($data as $key=>$val)
    {
      $this->pdf->SetDrawColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      $this->pdf->SetXY($XPage, $yval);
      $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
      $i++;
    }
    
    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);
    
    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
    
    
  }
  
  function PieChart( $w, $h, $data, $format, $colors = null)
  {
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w + $radius * .5;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;

    for ($i = 0; $i < $this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      if(strpos($this->pdf->legends[$i],'||')>0)
      {
        $parts=explode("||",$this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
      $y1 += $hLegend + $margin;
    }
  }



}
?>