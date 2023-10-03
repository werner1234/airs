<?

include_once('../indexBerekening.php');

class RapportRISK_L12
{

  function RapportRISK_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "";
    
    $this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->underlinePercentage=0.8;

    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $ultimoVorigJaar = (date("Y", db2jul($this->rapportageDatumVanaf))-1)."-12-31";
    
    if(db2jul($ultimoVorigJaar) > db2jul($this->pdf->PortefeuilleStartdatum))
      $perioden=array($ultimoVorigJaar);
    else
      $perioden=array();
    
    if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
      $beginMaand = date("m", db2jul($this->tweedePerformanceStart))-1;
      $beginKwartaal=ceil($beginMaand/3);
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
      $beginKwartaal=1;
      $beginMaand=0;
    }
    $this->pdf->tweedePerformanceStart= db2jul($this->tweedePerformanceStart);
    
    $RapMaand = date("m", db2jul($this->rapportageDatum));
    $rapKwartaal=ceil($RapMaand/3);
    $kwartaalDatum='';
    
    for($i=$beginKwartaal;$i<$rapKwartaal;$i++)
    {
      $kwartaalDatum=date('Y-m-d',mktime(0,0,0,$i*3+1,0,$RapStartJaar));
      if(db2jul($kwartaalDatum) > db2jul($this->pdf->PortefeuilleStartdatum))
      {
        $perioden[] = $kwartaalDatum;
      }
      else
      {
        $kwartaalDatum='';
      }
    }
   
    if($kwartaalDatum=='')
      $laatsteMaand=$beginMaand;
    else
      $laatsteMaand=date('m',db2jul($kwartaalDatum));

    for($i=$laatsteMaand;$i<$RapMaand;$i++)
    {
      $julDate=mktime(0,0,0,$i+2,0,$RapStartJaar);
      if($julDate>$this->pdf->rapport_datum)
        $julDate=$this->pdf->rapport_datum;
      $maandDatum=date('Y-m-d',$julDate);
      $perioden[]=$maandDatum;
    }
    if(count($perioden)==0)
      $perioden[]=$this->rapportageDatum;
    $this->perioden=$perioden;

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function writeRapport()
  {
    global $__appvar;
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $allekleuren['OIS']['Spaarrekeningen']=array('R'=>array('value'=>160),'G'=>array('value'=>192),'B'=>array('value'=>200));
  
  
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[	$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
  

    $verdelingenEUR=array();
    $totalenEUR=array();
    $volgorden=array();
    $omschrijvingen=array('hoofdcategorie'=>array('H-Alter'=>'Alternatieven','H-Obl' => 'Vastrentende Waarden'));
  
    
    foreach($this->perioden as $datum)
    {

      $tmp=berekenPortefeuilleWaarde($this->portefeuille,$datum,((substr($datum,5,5)=='01-01')?true:false),$this->pdf->rapportageValuta,$this->rapportageDatumVanaf);
      foreach($tmp as $fondsWaarden)
      {
        if($fondsWaarden['type']=='rekening' && $fondsWaarden['beleggingscategorie']<>'Liquiditeiten')
        {
          $fondsWaarden['beleggingssector']='Spaarrekeningen';
          $fondsWaarden['beleggingssectorOmschrijving']='Spaarrekeningen';
        }
        $verdelingenEUR[$datum]['hoofdcategorie'][$fondsWaarden['hoofdcategorie']]+=$fondsWaarden['actuelePortefeuilleWaardeEuro'];
        $verdelingenEUR[$datum]['beleggingssectorPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']][$fondsWaarden['beleggingssector']]+=$fondsWaarden['actuelePortefeuilleWaardeEuro'];
        $verdelingenEUR[$datum]['regioPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']][$fondsWaarden['Regio']]+=$fondsWaarden['actuelePortefeuilleWaardeEuro'];
        
        $totalenEUR[$datum]['hoofdcategorie']+=$fondsWaarden['actuelePortefeuilleWaardeEuro'];
        $totalenEUR[$datum]['beleggingssectorPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']]+=$fondsWaarden['actuelePortefeuilleWaardeEuro'];
        $totalenEUR[$datum]['regioPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']]+=$fondsWaarden['actuelePortefeuilleWaardeEuro'];
  
        $volgorden['hoofdcategorie'][$fondsWaarden['hoofdcategorie']]=$fondsWaarden['hoofdcategorieVolgorde'];
        $volgorden['beleggingscategorie'][$fondsWaarden['beleggingscategorie']]=$fondsWaarden['beleggingscategorieVolgorde'];
        $volgorden['beleggingssectorPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']][$fondsWaarden['beleggingssector']]=$fondsWaarden['beleggingssectorVolgorde'];
        $volgorden['regioPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']][$fondsWaarden['Regio']]=$fondsWaarden['regioVolgorde'];

        $omschrijvingen['hoofdcategorie'][$fondsWaarden['hoofdcategorie']]=$fondsWaarden['hoofdcategorieOmschrijving'];
        $omschrijvingen['beleggingscategorie'][$fondsWaarden['beleggingscategorie']]=$fondsWaarden['beleggingscategorieOmschrijving'];
        $omschrijvingen['regioPerHoofdcategorie'][$fondsWaarden['hoofdcategorie']][$fondsWaarden['Regio']]=$fondsWaarden['regioOmschrijving'];
        $omschrijvingen['beleggingssector'][$fondsWaarden['beleggingssector']]=$fondsWaarden['beleggingssectorOmschrijving'];
      }
    }
//listarray($verdelingenEUR);

    foreach($volgorden as $verdeling=>$waarden)
    {
      asort($volgorden[$verdeling]);
      foreach($waarden as $subcategorie=>$subcategorieData)
      {
        if(is_array($subcategorieData))
        {
          asort($volgorden[$verdeling][$subcategorie]);
        }
      }
    }
  //listarray($volgorden);exit;

    $grafiekData=array();
    $categorieVerdelingen=array('H-Alter','H-Obl');
    foreach($this->perioden as $datum)
    {
      foreach ($volgorden as $categorie => $waarden)
      {
        if ($categorie == 'hoofdcategorie')
        {
          foreach ($waarden as $hoofdcategorie => $volgorde)
          {
            $waardeEur= $verdelingenEUR[$datum]['hoofdcategorie'][$hoofdcategorie];
            $grafiekData['hoofdcategorie']['percentage'][$datum][$hoofdcategorie] = $waardeEur / $totalenEUR[$datum]['hoofdcategorie']*100;
            $grafiekData['hoofdcategorie']['omschrijving'][$hoofdcategorie] = $omschrijvingen['hoofdcategorie'][$hoofdcategorie];
            $grafiekData['hoofdcategorie']['kleuren'][$hoofdcategorie] = array($allekleuren['OIB'][$hoofdcategorie]['R']['value'],$allekleuren['OIB'][$hoofdcategorie]['G']['value'],$allekleuren['OIB'][$hoofdcategorie]['B']['value']);
          }
        }
        elseif ($categorie == 'regioPerHoofdcategorie')
        {
          foreach ($waarden['H-Aand'] as $regio => $volgorde)
          {
            $waardeEur= $verdelingenEUR[$datum]['regioPerHoofdcategorie']['H-Aand'][$regio];
            $grafiekData['regioAandelen']['percentage'][$datum][$regio] = $waardeEur / $totalenEUR[$datum]['regioPerHoofdcategorie']['H-Aand']*100;
            $grafiekData['regioAandelen']['omschrijving'][$regio] = $omschrijvingen['regioPerHoofdcategorie']['H-Aand'][$regio];
            $grafiekData['regioAandelen']['kleuren'][$regio] = array($allekleuren['OIR'][$regio]['R']['value'],$allekleuren['OIR'][$regio]['G']['value'],$allekleuren['OIR'][$regio]['B']['value']);
          }
        }
        elseif ($categorie == 'beleggingssectorPerHoofdcategorie')
        {
          foreach ($waarden as $hoofdcategorie=>$belSectorData)
          {
            foreach($belSectorData as $belSector=>$volgorde)
            {
              $waardeEur = $verdelingenEUR[$datum]['beleggingssectorPerHoofdcategorie'][$hoofdcategorie][$belSector];
              $grafiekData['cateogorie_'.$hoofdcategorie]['percentage'][$datum][$belSector] = $waardeEur / $totalenEUR[$datum]['beleggingssectorPerHoofdcategorie'][$hoofdcategorie] * 100;
              $grafiekData['cateogorie_'.$hoofdcategorie]['omschrijving'][$belSector] = $omschrijvingen['beleggingssector'][$belSector];
              $grafiekData['cateogorie_'.$hoofdcategorie]['kleuren'][$belSector] = array($allekleuren['OIS'][$belSector]['R']['value'], $allekleuren['OIS'][$belSector]['G']['value'], $allekleuren['OIS'][$belSector]['B']['value']);
            }
          }
        }
      }
    }
  
    $xStap=140;
    $yStap=82;
    $grafiekW=120;
    $grafiekH=50;
    $this->pdf->setXY(20,90);
    $this->VBarDiagram($grafiekW,$grafiekH,$grafiekData['hoofdcategorie'],'Portefeuille');
  
    $this->pdf->setXY(20+$xStap,90);
    $this->VBarDiagram($grafiekW,$grafiekH,$grafiekData['regioAandelen'],'Aandelen');

    $n=0;
    foreach($categorieVerdelingen as $hoofdcategorie)
    {
      $this->pdf->setXY(20+$xStap*$n,90+$yStap);
      $this->VBarDiagram($grafiekW,$grafiekH, $grafiekData['cateogorie_'.$hoofdcategorie], ucfirst(strtolower($omschrijvingen['hoofdcategorie'][$hoofdcategorie])));
      $n++;
    }

  }
  
  
  function VBarDiagram($w, $h, $data,$titel='')
  {

    $grafiekPunt = array();
    
    $minVal=0;
    $maxVal=100;
    $colors=array();
    $legenda=array();
    $legendaPrinted=array();
    $grafiekNegatief=array();
    $maxTotal=array();
    $minTotal=array();
    $grafiek=array();
    foreach ($data['percentage'] as $datum=>$waarden)
    {
      $legenda[$datum] = jul2form(db2jul($datum));
     
      $n=0;
  

      foreach($waarden as $key=>$value)
      {
        if($value <0)
          $minTotal[$datum]+=$value;
        else
          $maxTotal[$datum]+=$value;
      }
  
  
      foreach($waarden as $categorie=>$waarde)
      {
        if($maxTotal[$datum] > 100)
          $maxVal=max(array($maxVal,$maxTotal[$datum]));
        if($minTotal[$datum] < 0)
          $minVal=min(array($minVal,$minTotal[$datum]));
        
        if($waarden[$categorie] < 0)
        {
          unset($grafiek[$datum][$categorie]);
          $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          $grafiekPositief[$datum][$categorie] = 0;
        }
        else
        {
          $grafiekNegatief[$datum][$categorie] = 0;
          $grafiekPositief[$datum][$categorie]=$waarden[$categorie];
        }
        $n++;
      }
     
    }
    $colors=$data['kleuren'];
   

    $numBars=7;
    
//echo $titel."<br>\n";ob_flush();
    
    if(round($maxVal,1) <= 100)
      $maxVal=100;
    elseif($maxVal < 112.5)
      $maxVal=112.5;
    elseif($maxVal < 125)
      $maxVal=125;
    elseif($maxVal < 150)
      $maxVal=150;
    
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -12.5)
      $minVal=-12.5;
    elseif($minVal > -25)
      $minVal=-25;
    elseif($minVal > -50)
      $minVal=-50;
  

    
   

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
  
  
    $this->pdf->SetXY($XPage,$YPage-$h-6);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    $this->pdf->MultiCell($w, 4,$titel,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) ; // - legenda
    
    $n=0;
    $extraY=0;
 
    foreach ($data['omschrijving'] as $categorie=>$omschrijving)//
    {
      //echo "$XstartGrafiek-3+$n*35 ,$YstartGrafiek+$extraY+1.5  <br>";
      if($n%2==0)
      {
        $extraY += 5;
        $n=0;
      }
        $this->pdf->Rect($XstartGrafiek-6+$n*50 , $YstartGrafiek+$extraY+2, 2, 2, 'F',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek-3+$n*50 ,$YstartGrafiek+$extraY+1.5 );
        $this->pdf->MultiCell(60, 4,$omschrijving,0,'L');
        $n++;
    }
    
    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;
    }
    
    
    $horDiv = 5;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
 
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
  
    $vBar = ($bGrafiek / ($this->pdf->NbVal));
  
    $eBaton = ($vBar * 50 / 100);
    
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek+$eBaton/2, $i, $XstartGrafiek + $w - $eBaton/2 ,$i,array('dash' => 1,'color'=>array(206,215,222)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1,0)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
     // echo "$XstartGrafiek, $i, $XstartGrafiek + $w ,$i <br>\n";
   
    }
  
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek+$eBaton/2, $i, $XstartGrafiek + $w -$eBaton/2 ,$i,array('dash' => 1,'color'=>array(206,215,222)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,0)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
  
    //  echo "$XstartGrafiek, $i, $XstartGrafiek + $w ,$i <br>\n";
    }


    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor(127);
    $i=0;
    $this->pdf->SetTextColor(0,0,0);
  
  
    $aantalMaanden=count($data['percentage'])-1;
   
    foreach ($grafiekPositief  as $datum=>$pdata)
    {
      //$pdata=array_reverse($pdata,true);
      $aantal=count($pdata);
      $n=1;
      foreach($pdata as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (0.5 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
      //  $this->pdf->Line($xval,$yval,$xval,$yval+$hval);
      //  $this->pdf->Line($xval+$lval,$yval,$xval+$lval,$yval+$hval);
        if($aantal==$n)
        {
         // $this->pdf->Line($xval, $yval + $hval, $xval + $lval, $yval + $hval);
         // $this->pdf->Line($xval,$nulpunt,$xval+$lval,$nulpunt);
         // $this->pdf->Line($xval,$nulpunt,$xval,$yval+$hval);
         // $this->pdf->Line($xval+$lval,$nulpunt,$xval+$lval,$yval+$hval);
        }
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        //echo "$aantalMaanden==$n | $i <br>\n";
        if($aantalMaanden==$i && abs($hval) > 3)
        {
          if( array_sum($colors[$categorie]) > 128*3)
          {
            $this->pdf->SetTextColor(0,0,0);
          }
          else
          {
            $this->pdf->SetTextColor(255,255,255);
          }
          
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        /*  */
        if($legendaPrinted[$datum] != 1)
          $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'F',null,array(194,179,157));
          //if($lastX)
          //  $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
        $legendaPrinted[$datum] = 1;
        $n++;
      }
      $i++;
    }

    $i=0;
    $YstartGrafiekLast=array();

    foreach ($grafiekNegatief as $datum=>$pdata)
    {
      foreach($pdata as $categorie=>$val)
      {
        if($val == 0)
          continue;
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (0.5 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit);
        
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
  
  
        if($aantalMaanden==$i && abs($hval) > 3)
        {
          if( array_sum($colors[$categorie]) > 128*3)
          {
            $this->pdf->SetTextColor(0,0,0);
          }
          else
          {
            $this->pdf->SetTextColor(255,255,255);
          }
    
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        
        if($grafiekPunt[$categorie][$datum])
        {
          $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'F',null,array(194,179,157));
          if($lastX)
            $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
          $lastX = $xval+.5*$eBaton;
          $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
        }
      }
      $i++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }




}
?>