<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_105/ATTberekening_L105.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
class RapportPERFD_L105
{

	function RapportPERFD_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Maandelijkse performance";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    //$this->att=new ATTberekening_L105($this);
	}

	function formatGetal($waarde, $dec,$color=false)
	{
	  if($color)
    {
      if($waarde<0)
        $this->pdf->SetTextColor(255,13,13);
      else
        $this->pdf->SetTextColor(0,0,0);
    }
		return number_format($waarde,$dec,",",".");
	}

	function getKleur($percentage,$jaar=false)
  {
    $kleur=array();
    if($jaar==false)
    {
      
      $data=array(-15=>array(255,13,13),
                  -12=>array(255,101,101),
                  -9=>array(255,71,71),
                  -6=>array(255,125,125),
                  -3=>array(255,163,163),
                  -1=>array(255,192,192),
                   0=>array(255,217,217),
                   1=>array(218,250,220),
                   3=>array(192,246,196),
                   6=>array(124,236,132),
                   9=>array(74,228,85),
                  12=>array(23,214,45),
                  15=>array(27,138,38),
                1000=>array(21,143,30));
    }
    else
    {
      $data=array(-36=>array(255,13,13),
                  -28=>array(255,101,101),
                  -20=>array(255,71,71),
                  -12=>array(255,125,125),
                   -4=>array(255,163,163),
                    4=>array(255,192,192),
                    8=>array(255,217,217),
                   12=>array(218,250,220),
                   20=>array(192,246,196),
                   28=>array(124,236,132),
                   36=>array(74,228,85),
                   44=>array(23,214,45),
                   50=>array(27,138,38),
                10000=>array(21,143,30));
    }
    foreach($data as $check=>$checkKleur)
    {
      if($percentage<$check)
      {
        $kleur = $checkKleur;
        break;
      }
    }
    return $kleur;
  }
  
  function berekenRendement($maandRendementen)
  {
    $jaarPerf=0;
    foreach($maandRendementen as $rendement)
    {
      $jaarPerf = ((1 + $jaarPerf / 100) * (1 + $rendement / 100) - 1) * 100;
    }
    return $jaarPerf;
  }

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type .'Paginas']=$this->pdf->rapport_titel;
    
    
    $index = new indexHerberekening();
    if($this->pdf->portefeuilledata['PerformanceBerekening']==3 && intval(substr($this->rapportageDatum,0,4))>=2020)
    {
      $old=false;
    }
    else
    {
      $old=true;
    }

    if($old==true)
    {
      $index->voorStartdatumNegeren=true;
      $maandHistorie = $index->getWaarden($this->pdf->PortefeuilleStartdatum, $this->rapportageDatum , $this->portefeuille, '', 'maanden');
    }
    else
    {
      $this->att = new ATTberekening_L105($this);
      $maandHistorie = $this->att->getPerf($this->portefeuille, $this->pdf->PortefeuilleStartdatum, $this->rapportageDatum, $this->pdf->portefeuilledata['RapportageValuta'], true);
    }
   // listarray($maandHistorie);exit;
   
    $jaarRegels=array();
    foreach($maandHistorie as $i=>$maandData)
    {
      //listarray($maandData);
      $jaar=substr($maandData['datum'],0,4);
      $maand=intval(substr($maandData['datum'],5,2));
      // echo "$jaar $maand $perf <br>\n";
      $jaarRegels[$jaar][$maand]['perf']=$maandData['performance'];
      $jaarRegels[$jaar][$maand]['resultaat']=$maandData['resultaatVerslagperiode'];
      $jaarRegels[$jaar]['stort']+=$maandData['stortingen']-$maandData['onttrekkingen'];
 // listarray($maandData);
  //   listarray($jaarRegels[$jaar]['stort']);

      $jaarRegels[$jaar]['index']=$maandData['index'];
    }
    
    $header=array('','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec','','Jaar (in%)','','Cumulatief');
    $w=15;
    $widths=array(20,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,5,20,5,20,5,20);
    $this->pdf->ln();
    for($i=0;$i<17;$i++)
    {
      $this->pdf->Cell($widths[$i],4, $header[$i], 0,0, "C");
    }
    $this->pdf->ln();
    
    $maandStats=array();
    $jaarBuffer=array();
    $jaarPerioden=array(1,3,5,10);
    $n=0;
    $cumulatiefRendement=0;
    foreach($jaarRegels as $jaar=>$maandData)
    {
      
      $this->pdf->Cell($widths[0],4, $jaar, 0,0, "C");
      $jaarPerf=0;
      for($i=1;$i<13;$i++)
      {
        if(isset($maandData[$i]['perf']))
          $kleur=$this->getKleur($maandData[$i]['perf'],false);
        else
          $kleur=0;
        if(isset($maandData[$i]['perf']))
        {
          $txt = $this->formatGetal($maandData[$i]['perf'], 2) . '%';
          foreach($jaarPerioden as $aantalJaar)
          {
            $aantalMaanden=$aantalJaar*12;
            $jaarBuffer[$aantalJaar][$n] = $maandData[$i]['perf'];
            if (count($jaarBuffer[$aantalJaar]) > $aantalMaanden)
            {
              unset($jaarBuffer[$aantalJaar][$n - $aantalMaanden]);
            }
  
            if (count($jaarBuffer[$aantalJaar]) == $aantalMaanden)
            {
              $bufferRendement[$aantalJaar] = $this->berekenRendement($jaarBuffer[$aantalJaar]);
              if (!isset($maandStats['max' . $aantalMaanden . 'Maanden']) || $bufferRendement[$aantalJaar] > $maandStats['max' . $aantalMaanden . 'Maanden'])
              {
                $maandStats['max' . $aantalMaanden . 'Maanden'] = $bufferRendement[$aantalJaar];
                $maandStats['max' . $aantalMaanden . 'MaandenJaar'] = (pow(1 + ($bufferRendement[$aantalJaar] / 100), 1 / $aantalJaar) - 1) * 100;
              }
              if (!isset($maandStats['min' . $aantalMaanden . 'Maanden']) || $bufferRendement[$aantalJaar] < $maandStats['min' . $aantalMaanden . 'Maanden'])
              {
                $maandStats['min' . $aantalMaanden . 'Maanden'] = $bufferRendement[$aantalJaar];
                $maandStats['min' . $aantalMaanden . 'MaandenJaar'] = (pow(1 + ($bufferRendement[$aantalJaar] / 100), 1 / $aantalJaar) - 1) * 100;
              }
            }
          }
          $maandStats['aantalMaanden']++;
          if($maandData[$i]['perf']>=0)
            $maandStats['aantalPositief']++;
          else
            $maandStats['aantalNegatief']++;

            
          $n++;
        }
        else
          $txt='';
        $this->pdf->Cell($widths[$i],4, $txt, 1,0, "C",$kleur);
        $jaarPerf=((1+$jaarPerf/100)*(1+$maandData[$i]['perf']/100)-1)*100;
      }
      $this->pdf->Cell($widths[13],4, '', 0,0, "C");
      //$kleur=$this->getKleur($jaarPerf,false);
      $this->pdf->Cell($widths[14],4, $this->formatGetal($jaarPerf,2,true).'%', 1,0, "C",0);
      $this->pdf->SetTextColor(0,0,0);
      $this->pdf->Cell($widths[15],4, '', 0,0, "C");
      $this->pdf->Cell($widths[16],4, $this->formatGetal($maandData['index']-100,2).'%', 1,0, "C");
      $cumulatiefRendement=(($maandData['index']-100)/100);
      $this->pdf->ln();
    }
    //listarray($jaarBuffer);
    //listarray($maandStats);
    $this->pdf->ln();
    $header[]='';
    $header[]='';
    $header=array('','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec','','Jaar (in €)','','Cumulatief','','Mutaties');
    for($i=0;$i<19;$i++)
    {
      $this->pdf->Cell($widths[$i],4, $header[$i], 0,0, "C");
    }
    $this->pdf->ln();
    
    $totaalResultaat=0;
    foreach($jaarRegels as $jaar=>$maandData)
    {
      $this->pdf->SetTextColor(0,0,0);
      $this->pdf->Cell($widths[0],4, $jaar, 0,0, "C");
      $jaarResultaat=0;
      for($i=1;$i<13;$i++)
      {
        if(isset($maandData[$i]['resultaat']))
        {
          $this->pdf->Cell($widths[$i], 4, $this->formatGetal($maandData[$i]['resultaat'], 0, true), 1, 0, "C", 0);
          $jaarResultaat += $maandData[$i]['resultaat'];
          $totaalResultaat += $maandData[$i]['resultaat'];
        }
        else
        {
          $this->pdf->Cell($widths[$i], 4, '', 1, 0, "C", 0);
        }
      }
      $this->pdf->Cell($widths[13],4, '', 0,0, "C");
      $this->pdf->Cell($widths[14],4, $this->formatGetal($jaarResultaat,0,true), 1,0, "C",0);
      $this->pdf->Cell($widths[15],4, '', 0,0, "C");
      $this->pdf->Cell($widths[16],4, $this->formatGetal($totaalResultaat,0,true), 1,0, "C");
      $this->pdf->Cell($widths[15],4, '', 0,0, "C");
      $this->pdf->Cell($widths[16],4, $this->formatGetal($maandData['stort'],0,true), 1,0, "C");
      $this->pdf->ln();
    }
    $this->pdf->SetTextColor(0,0,0);
    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);
    $this->pdf->ln();
    $this->pdf->setWidths(array(5,40,20,15,15,40,15,15,30,15,15,35,15));
    $this->pdf->setAligns(array('L','L','R','L','C','L','R','C','L','R','C','L','R'));
    if($maandStats['aantalMaanden'] > 11)
    {
      $indexGeanualiseerd=$cumulatiefRendement+1;
      $geanualiseerdRendement=(pow($indexGeanualiseerd, (12 / $maandStats['aantalMaanden']))-1)*100;
      $positiefProcent='('.$this->formatGetal($maandStats['aantalPositief']/$maandStats['aantalMaanden']*100,1).'%)';
      $negatiefProcent='('.$this->formatGetal($maandStats['aantalNegatief']/$maandStats['aantalMaanden']*100,1).'%)';
      $this->pdf->SetTextColor(0,0,0);
      $this->pdf->row(array('','Gemiddeld jaarrendement',$this->formatGetal($geanualiseerdRendement,2).'%','',''
                              ,'Slechtste 12 maands periode',$this->formatGetal($maandStats['min12Maanden'],2).'%',''
                              ,(isset($maandStats['max36MaandenJaar'])?'Beste 3 jaars periode':''),(isset($maandStats['max36MaandenJaar'])?$this->formatGetal($maandStats['max36MaandenJaar'],2).'%':''),''
                              ,(isset($maandStats['min36MaandenJaar'])?'Slechtste 3 jaars periode':''),(isset($maandStats['min36MaandenJaar'])?$this->formatGetal($maandStats['min36MaandenJaar'],2).'%':'')));
      $this->pdf->row(array('','Aantal maanden positief',$maandStats['aantalPositief'].' van '.$maandStats['aantalMaanden'],$positiefProcent,''
                              ,'Beste 12 maands periode',$this->formatGetal($maandStats['max12Maanden'],2).'%',''
                              ,(isset($maandStats['max60MaandenJaar'])?'Beste 5 jaars periode':''),(isset($maandStats['max60MaandenJaar'])?$this->formatGetal($maandStats['max60MaandenJaar'],2).'%':''),''
                              ,(isset($maandStats['min60MaandenJaar'])?'Slechtste 5 jaars periode':''),(isset($maandStats['min60MaandenJaar'])?$this->formatGetal($maandStats['min60MaandenJaar'],2).'%':'')));
      $this->pdf->row(array('','Aantal maanden negatief',$maandStats['aantalNegatief'].' van '.$maandStats['aantalMaanden'],$negatiefProcent,''
                              ,'AFM Standaardeviatie',$this->formatGetal($afm['std'],2).'%',''
                              ,(isset($maandStats['max120MaandenJaar'])?'Beste 10 jaars periode':''),(isset($maandStats['max120MaandenJaar'])?$this->formatGetal($maandStats['max120MaandenJaar'],2).'%':''),''
                              ,(isset($maandStats['min120MaandenJaar'])?'Slechtste 10 jaars periode':''),(isset($maandStats['min120MaandenJaar'])?$this->formatGetal($maandStats['min120MaandenJaar'],2).'%':'')));
      
       //  $this->pdf->row(array('', vertaalTekst('Geannualiseerde standaarddeviatie',$this->pdf->rapport_taal).' ' . vertaalTekst($periodeTxt,$this->pdf->rapport_taal), $this->formatGetal(standard_deviation($rendementen) * $correctie, 2) . "%"));
    }
    
    //listarray($maandHistorie);
  

  }






}
?>