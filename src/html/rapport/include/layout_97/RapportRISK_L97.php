<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/25 16:43:07 $
File Versie					: $Revision: 1.3 $

$Log: RapportPERFG_L97.php,v $


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

class RapportRISK_L97
{
	function RapportRISK_L97($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;
	}
  
  
  function ophalenCRMRecord()
  {
    $gebruikteCrmVelden=array('naam','logo');
       $data = array();
        foreach($this->pdf->portefeuilles as $portefeuille)
        {
          $db = new DB();
          $query = "SELECT CRM_naw.id FROM CRM_naw WHERE CRM_naw.portefeuille='" . $portefeuille . "'";
          $db->SQL($query);
          $crmData = $db->lookupRecord();
          $naw = new NAW();
          $naw->getById($crmData['id']);
          
          $data[$portefeuille]=array();
          foreach ($gebruikteCrmVelden as $veld)
          {
            if (substr($veld, 0, 9) == 'Beheerder')
            {
              $data[$portefeuille][substr($veld, 9, 4)][$veld] = array('omschrijving' => $naw->data['fields'][$veld]['description'], 'waarde' => $naw->data['fields'][$veld]['value']);
            }
            else
            {
              $data[$portefeuille][$veld] = $naw->data['fields'][$veld]['value'];
            }
          }
  
            $DB = new DB();
            $query = "SELECT kleurcode FROM Portefeuilles where portefeuille='" . mysql_real_escape_string($portefeuille) . "'";
            $DB->SQL($query);
            $DB->Query();
            $kleur = $DB->nextRecord();
            $data[$portefeuille]['kleur']=unserialize($kleur['kleurcode']);
        }
       
    return $data;
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

	function writeConsolidatie()
  {
    $this->pdf->rapport_type = "RISK";
    $this->pdf->AddPage();
    $this->pdf->templateVars['PERFDPaginas']=$this->pdf->page;
    $crmData = $this->ophalenCRMRecord();
    $this->addVerdeling($crmData);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, 118,$this->pdf->w-$this->pdf->marge*2, 0.25 , 'F');
    
    $this->addGrafieken();
    
    //$this->addGrafiek();
  }
  
  function addGrafieken()
  {
    $riskDataTotaal=array();
    $perioden=array();
    $drawDown=array();
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $stdev = new rapportSDberekening($portefeuille, $this->rapportageDatum, 0);
      $benchmark=getSpecifiekeIndex($portefeuille,$this->rapportageDatum);
     // $stdev->settings['gebruikHistorischePortefeuilleIndex'] = false;
      $stdev->addReeks('totaal');
      $stdev->addReeks('afm');
      $stdev->addReeks('benchmarkTot',$benchmark);
      $stdev->berekenWaarden();
      $drawDown[$portefeuille]['totaal'] = $stdev->berekenMaxDrawdown('totaal');
      
      $riskData = $stdev->riskAnalyze('totaal', 'benchmarkTot', true,true);
      //$riskBenchmark = $stdev->riskAnalyze('benchmarkTot', 'totaal', true);
      $riskDataTotaal[$portefeuille]['riskData'] = $riskData;
      //$riskData[$portefeuille]['riskBenchmark'] = $riskBenchmark;
  
      foreach($riskData as $data)
        $perioden[$data['laatsteMeting']] = $data['laatsteMeting'];
        //$grafiekData['sharpe']['datum'][$datum]= date("d-m-Y",db2jul($datum));
        // $grafiekData['sharpe']['portefeuille'][$datum]=0;
        // $grafiekData['sharpe']['specifiekeIndex'][$datum]=0;
      
    }
    $perioden=array_values($perioden);
    sort($perioden);
    $tmp=array();
    foreach ($perioden as $datum)
    {
      $tmp['Datum'][$datum] = $datum;
      $tmp['Index'][$datum]=array();
    }
    $grafiekData['standaarddeviatie']=array();

//  listarray($riskDataTotaal);
    $grafiekData['standaarddeviatie']=$tmp;
    
    $grafiekData['sharpeRatio']=$tmp;
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $riskData=$riskDataTotaal[$portefeuille]['riskData'];
      //$riskBenchmark=$riskData[$portefeuille]['riskBenchmark'];
      foreach($drawDown[$portefeuille]['totaal'] as $drawDownData)
      {
        $grafiekData['maxDrawdown']['Datum'][$drawDownData['laatsteMeting']][$portefeuille]=$drawDownData['laatsteMeting'];
        $grafiekData['maxDrawdown']['Index'][$drawDownData['laatsteMeting']][$portefeuille]=$drawDownData['maxDrawdown'];
      }
      if(is_array($riskData) && count($riskData) > 0)
      {
        foreach($riskData as $data)
        {
          $grafiekData['standaarddeviatie']['Index'][$data['laatsteMeting']][$portefeuille] = $data['standaarddeviatie'];
          //$grafiekData['maxDrawdown']['Index'][$data['laatsteMeting']][$portefeuille] = $data['maxDrawdown2'];
          $grafiekData['sharpeRatio']['Index'][$data['laatsteMeting']][$portefeuille] = $data['sharpeRatio'];
        }
    

      }
      
    }

    $kleuren=array();
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $DB = new DB();
      $query = "SELECT kleurcode FROM Portefeuilles where portefeuille='" . mysql_real_escape_string($portefeuille) . "'";
      $DB->SQL($query);
      $DB->Query();
      $kleur = $DB->nextRecord();
      $kleuren[$portefeuille] = unserialize($kleur['kleurcode']);
    }
    
    $n=0;
    $x=20;
    $y=125;
    $step=95;
    $koppen=array('standaarddeviatie'=>'Standaarddeviatie','maxDrawdown'=>'Drawdown door de tijd','sharpeRatio'=>'Sharpe Ratio');
    foreach($grafiekData as $type => $data)
    {
      $this->pdf->setXY($x+$n*$step,$y-3);
     // listarray($grafiekData);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(75,0,$koppen[$type],0,0,'L');
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->setXY($x+$n*$step,$y);
      if($type=='sharpeRatio')
        $skipNull=false;
      else
        $skipNull=true;
      //echo "$type $skipNull <br>\n";
      $this->LineDiagramConsolidatie(75, 55, $data,$kleuren,0,0,8,5,1,$skipNull);//50
      $n++;
    }
    
   // listarray($grafiekData);
  }
  
  function addVerdeling($crmData)
  {
    global $__appvar;
    $x=20;
    $y=33;
    $step=95;
  
    $names=array('AbnAmro','actiam','BankSafra','BankTenCate','BAvanDoorn','bondcapital','capitael','dexxi','DoubleDividend','HeerenVermogensbeheer','helliot','ibeleggen','IBS','ING','junior','kempen','mercurius','Mpartners','optimix','robeco','stoic','TIP','vanEck','vanLieshout');
    $aantal=count($names)-1;
//    $loop=range(0,$aantal);
    $loop=$this->pdf->portefeuilles;
    foreach($loop as $portefeuille)
    {
      //listarray($crmData);exit;
      $this->pdf->setXY($x+3,$y+22);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(70-6,0,($crmData[$portefeuille]['naam']<>''?$crmData[$portefeuille]['naam']:$portefeuille),0,0,'C');
      $this->pdf->SetFillColor($crmData[$portefeuille]['kleur'][0],$crmData[$portefeuille]['kleur'][1],$crmData[$portefeuille]['kleur'][2]);
      $this->pdf->Rect($x+35,$y+21+4,2,2,'F');//array('color'=>));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->setDrawColor(0);
      //$names=array('BankTenCate','bondcapital','ibeleggen','IBS','ING','optimix','stoic');
      //$crmData[$portefeuille]['logo']=$names[$portefeuille];//$names[rand(0,2)];
      //$crmData[$portefeuille]['logo']=$names[rand(0,$aantal)];
      $logo=$__appvar["basedir"].'/html/rapport/include/layout_97/logo/'.$crmData[$portefeuille]['logo'].'.png';

      if(is_file($logo))
      {
        $img=getimagesize($logo);
        
        $imgx=$img[0];
        $imgy=$img[1];
        $verhouding=$imgx/$imgy;
        if($verhouding<2.2)
          $width=40*$verhouding/2.2;
        else
          $width=40;
        
        if($verhouding>2.5)
          $ypos=$y+$verhouding*1.2;
        else
          $ypos=$y;
   
        $this->pdf->Image($logo,$x+$step/2-$width/2-12,$ypos,$width);
      }
      
      $gebruikteCategorie=$this->addZorgBar($portefeuille);
      $this->plotZorgBar2($x-5,$y+15,3,40,$gebruikteCategorie,$portefeuille);
      $x+=$step;
      //if($portefeuille%3==0)
      //{
      //  $this->pdf->addPage();
      //  $x=20;
      //}
    }
  }
  
  function addZorgBar($portefeuille)
  {
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
    $pdata=$this->pdf->portefeuilledata;
    $pdata['Portefeuille']=$portefeuille;//$this->portefeuille;
    
    $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum);
   // listarray($pdata);
   //listarray($zpwaarde);
    $gebruikteCategorien=array();
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
//      if(!isset($data['fondsGekoppeld']))
//      {
        $gebruikteCategorien[$categorie]=$data;
//      }
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
  
  
  function plotZorgBar2($x,$y,$barWidth,$height,$zorgdata,$portefeuille)
  {
    $DB=new DB();
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      $categorien[$data['Zorgplicht']]=$data['Omschrijving'];
    }
    $xBegin=$x;
    $yBegin=$y;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($xBegin+45,$yBegin+9);
    //$this->pdf->Cell(5,5,"Mandaat controle",0,0,'C');
    $this->pdf->Rect($xBegin+5,$yBegin+14,70,8+count($zorgdata)*10);
  
    $this->pdf->setXY($xBegin+5,$yBegin+16);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(70,0,'Bandbreedtes',0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($xBegin+15,$yBegin+24);

    
    $hProcent=$height/100;

    $marge=1;
    $extraY=8;
    $xPage=$this->pdf->getX()+10;
    $yPage=$this->pdf->getY();
  
   // listarray($zorgdata);
   // listarray($categorien);
    
    foreach($zorgdata as $categorie=>$data)
    {
      
     // $data['percentage']=str_replace(',','.',$data['percentage']);
      
      $this->pdf->setXY($xPage-$marge-4,$yPage-2);
      $this->pdf->Rect($xPage, $yPage, $hProcent*100, $barWidth, 'D');
     // $this->pdf->Rect($xPage, $yPage+$extraY,$hProcent*100, $barWidth,  'D');
      $this->pdf->setXY($xPage-4,$yPage);
      $this->pdf->cell(4,4,($categorien[$categorie]<>''?$categorien[$categorie]:$categorie),0,0,'R');
      $this->pdf->setXY($xPage-2,$yPage-$marge-4);
      $this->pdf->cell(4,4,"0",0,0,'C');
      $this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4);
      $this->pdf->cell(4,4,"100%",0,0,'C');
      $this->pdf->setXY($xPage+$hProcent*$data['Minimum']-2,$yPage-$marge-4);
      if($data['Minimum']<>0)
        $this->pdf->cell(4,4,"".$data['Minimum'].'',0,0,'C');
      $this->pdf->setXY($xPage+$hProcent*$data['Maximum']-2,$yPage-$marge-4);
      if($data['Maximum']<>100)
        $this->pdf->cell(4,4,"".$data['Maximum'].'',0,0,'C');
      
      //$this->pdf->setXY($xPage+$hProcent*$data['Norm']-2,$yPage+$marge+5);
      //$this->pdf->cell(4,4,"Norm ".$data['Norm'],0,0,'R');
      
      //$this->pdf->SetFillColor(239,86,61);
      //$this->pdf->Rect($xPage, $yPage, $hProcent*$data['Minimum'], $barWidth,  'D');
      $this->pdf->SetFillColor(27,159,17);
      $this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage,$hProcent*($data['Maximum']-$data['Minimum']), $barWidth,   'DF');
      //$this->pdf->SetFillColor(239,86,61);
      //$this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage, $hProcent*(100-$data['Maximum']),$barWidth,  'D');
      
      //$this->pdf->Line($xPage+$hProcent*$data['Norm'], $yPage,$xPage+$hProcent*$data['Norm'],$yPage+$barWidth);
      /*
      if($data['conclusie']=='Voldoet')
        $this->pdf->SetFillColor(27,159,17);
      else
        $this->pdf->SetFillColor(239,86,61);
      $this->pdf->Rect($xPage,$yPage+$extraY , $hProcent*$data['percentage'], $barWidth,  'DF');
      $this->pdf->setXY($xPage+$hProcent*$data['percentage']-2,$yPage+$barWidth+$marge+$extraY);
      $this->pdf->cell(4,4,$this->formatGetal($data['percentage'],1).'% werkelijk',0,0,'L');
      */
      $yPage+=10;
    }
  }
  
  
  
  function addGrafiek()
  {
    $width=(297-$this->pdf->marge*2);
    $ystart=110;
    $this->pdf->setXY($this->pdf->marge,$ystart);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($width, 5, vertaalTekst('Historisch rendement',$this->pdf->rapport_taal), 0, 1,'C');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $grafiekData=array();
    $kleuren=array();
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $DB = new DB();
      $query="SELECT kleurcode FROM Portefeuilles where portefeuille='".mysql_real_escape_string($portefeuille)."'";
      $DB->SQL($query);
      $DB->Query();
      $kleur = $DB->nextRecord();
      $kleuren[$portefeuille]=unserialize($kleur['kleurcode']);
      
      $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille = '".$portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
      $DB->SQL($query);
      $DB->Query();
      $datum = $DB->nextRecord();
  
  
      if($this->pdf->lastPOST['perfPstart'] == 1)
      {
        if($datum['id'] > 0)
        {
          if ($datum['month'] < 10)
          {
            $datum['month'] = "0" . $datum['month'];
          }
          $start = $datum['year'] . '-' . $datum['month'] . '-01';
          if(db2jul($start)>db2jul($this->pdf->PortefeuilleStartdatum))
            $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
        }
        else
          $start = substr($this->pdf->PortefeuilleStartdatum,0,10);
      }
      else
        $start = $this->rapportageDatumVanaf;//substr($this->pdf->PortefeuilleStartdatum,0,10);
      $eind = $this->rapportageDatum;
  
  //echo  $this->rapportageDatumVanaf;exit;
      $index = new indexHerberekening();
      $indexData = $index->getWaarden($start,$eind,$portefeuille,'');
  
      foreach ($indexData as $index=>$data)
      {
        if($data['datum'] != '0000-00-00')
        {
          $rendamentWaarden[] = $data;
          $grafiekData['Datum'][$data['datum']] = $data['datum'];
          $grafiekData['Index'][$data['datum']][$portefeuille] = $data['index']-100;
      
        }
      }
    }
  
  //  $kleuren=array(array(74,166,77),array(61,59,56));
    $this->pdf->setX($this->pdf->getX()+5);
    $this->LineDiagramConsolidatie(270, 60, $grafiekData,$kleuren,0,0,8,5,1);//50
    $this->pdf->SetXY($this->pdf->getX()+5, $ystart + 75);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($kleuren as $portefeuille=>$kleur)
    {
      $this->pdf->rect($this->pdf->getX()-2,$this->pdf->getY()+1,2,2,'F','',$kleur);
      $this->pdf->Cell(50, 4, $portefeuille, 0, 0, "L");
    }
    
  }

  function writeRapport()
  {
      $this->writeConsolidatie();
  }



function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}



	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='')
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
	$vorigeIndex = 100;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$datum = array();

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

	$i=1;
	$indexData['index']=100;
	$db=new DB();
	foreach ($datum as $periode)
	{
	 	$indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille));
	 	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
 	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	  $data[$i] = $indexData;
    $i++;
	}
	return $data;
	}



	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();
    $koersQuery='';
    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query); 
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;


	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']=array('cat'=>$categorieVerdeling);
    return $data;

	}
  
  
  function LineDiagramConsolidatie($w, $h, $data, $colors=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0,$skipNul=false)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];
    $data = $data['Index'];
    $bereikdata=array();
    foreach($data as $datum=>$waarden)
      foreach($waarden as $port=>$waarde)
        $bereikdata[]=$waarde;
      

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
    

    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  //  $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    //  if ($minVal > 0)
     //   $minVal =-1;
    }
    
 //   listarray($bereikdata);
    
    $minVal = floor(($minVal-1) * 1.2);
    $maxVal = ceil(($maxVal+1) * 1.2);

    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($legendDatum);
    
    if($jaar && count($legendDatum) < 12)
      $unit = $lDiag / 12;
    

    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
  
  //  echo "($maxVal - $minVal) <br>\n"; ob_flush();

    $n=0;
    $skipNull=false;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      if($i)
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
      if($i > $bodem)
      {
        $n++;
        continue;
      }
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      
      $n++;
      if($n >20)
        break;
   //   echo "2 $n $i <br>\n";
    }
 //   echo "$bodem - $nulpunt - $top <br>\n $bodem";exit;

    $lijnPositie=array();
    foreach($data as $datum=>$waarden)
    {
      foreach($waarden as $port=>$waarde)
        $lijnPositie[$port]['yval'] = $YDiag + (($maxVal) * $waardeCorrectie);
    }
    $i=0;
    $xLegendaPrinted=array();
    
    $maanden=count($data);
    if($maanden>12)
    {
      $tonen = round($maanden / 6);
    }
    else
    {
      $tonen=1;
    }
    
    foreach($data as $datum=>$waarden)
    {
      foreach($waarden as $port=>$waarde)
      {
        $color=$colors[$port];
        
        $lineStyle = array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
        
        $extrax = ($unit * 0.1 * -1);
        if ($i <> 0)
        {
          $extrax1 = ($unit * 0.1 * -1);
        }
        
        if(!isset($xLegendaPrinted[$i]))
        {
          if($i%$tonen==0 || $i==$maanden-1)
            $this->pdf->TextWithRotation($XDiag + ($i) * $unit - 10 + $unit, $YDiag + $hDiag + 8, jul2form(db2jul($datum)), 25);
          $xLegendaPrinted[$i]=true;
        }
        $yval2 = $YDiag + (($maxVal - $waarde) * $waardeCorrectie);
        if($skipNul==false || $i >0)
          $this->pdf->line($XDiag + $i * $unit + $extrax1, $lijnPositie[$port]['yval'], $XDiag + ($i + 1) * $unit + $extrax, $yval2, $lineStyle);
       // $this->pdf->Rect($XDiag + ($i + 1) * $unit - 0.5 + $extrax, $yval2 - 0.5, 1, 1, 'F', '', $color);
    
        if ($waarde <> 0 && ($i%$tonen==0 || $i==$maanden-1))
        {
         // $this->pdf->Text($XDiag + ($i + 1) * $unit - 1 + $extrax, $yval2 - 2.5, $this->formatGetal($waarde, 1));
        }
  
  
        $lijnPositie[$port]['yval'] = $yval2;
        
      }
      $i++;
    }
  
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

  
  

}
?>