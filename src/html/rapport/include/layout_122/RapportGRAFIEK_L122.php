<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"] . "/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L122
{
  function RapportGRAFIEK_L122($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "GRAFIEK";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Rendement";
  
    if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
      $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;
  
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

  }
  
  function writeRapport()
  {
    global $__appvar;
    
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
  
    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      "IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
      " TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
				   TijdelijkeRapportage.portefeuille ".
      " FROM TijdelijkeRapportage WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type =  'fondsen' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    while($fondsen = $DB->nextRecord())
    {
      $huidigeFondsen[$fondsen['fonds']]=$fondsen;
    }
    $fondsKoersen=array();
    $fondsRendementen=array();
    $fondsIndex=array();
  
    $indexObject = new indexHerberekening();
    
    foreach($huidigeFondsen as $fonds=>$details)
    {
      $query="SELECT MIN(boekdatum) as start FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Fonds='".$fonds."'";
      $DB->SQL($query);
      $DB->Query();
      $start=$DB->lookupRecord();
      $maanden=$indexObject->getMaanden(db2jul($start['start']),$this->pdf->rapport_datum);
      $index=0;
      foreach($maanden as $periode)
      {
        if(!isset($fondsKoersen[$fonds][$periode['start']]))
          $fondsKoersen[$fonds][$periode['start']]=globalGetFondsKoers($fonds,$periode['start']);
        if(!isset($fondsKoersen[$fonds][$periode['stop']]))
          $fondsKoersen[$fonds][$periode['stop']]=globalGetFondsKoers($fonds,$periode['stop']);
  
        $rendement=($fondsKoersen[$fonds][$periode['stop']]-$fondsKoersen[$fonds][$periode['start']])/$fondsKoersen[$fonds][$periode['start']];
        $fondsRendementen[$fonds][$periode['stop']]=$rendement;
  
        $index=((1+$index)*(1+$rendement))-1;
        if(!isset($fondsIndex[$fonds][$periode['start']]))
          $fondsIndex[$fonds][$periode['start']]=0;
        $fondsIndex[$fonds][$periode['stop']]=$index*100;
        
      }
    }
   // listarray($fondsKoersen);
    //listarray($fondsRendementen);
    //listarray($fondsIndex);
    
    $n=0;
   foreach($fondsIndex as $fonds=>$fondsdata)
   {
     if($n==2)
     {
       $this->pdf->affPage();
       $n=0;
     }
     if($n==0)
       $this->pdf->setXY(15,30);
     else
       $this->pdf->setXY(150,30);
     $this->lineGrafiek(120, 60, array('portefeuille'=>$fondsdata),$huidigeFondsen[$fonds]['fondsOmschrijving']);
     $n++;
   }
  

  }
  
  
  function lineGrafiek($w, $h, $grafiekData,$omschrijving)
  {
    
    $horDiv=5;
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $color=array(0,255,255);
    
    $this->pdf->SetLineWidth(0.3);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 5;
    $maxVal=max($grafiekData['portefeuille']);
    $minVal=min($grafiekData['portefeuille']);
    $maxVal = $maxVal * (1 + ($procentWhiteSpace/100));
    if($minVal>0)
      $minVal = $minVal * (1 - ($procentWhiteSpace/100));
    else
      $minVal = $minVal * (1 + ($procentWhiteSpace/100));
    

    
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    
    $unit = $lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0);
    $this->pdf->SetDrawColor(128,128,128);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'D','',array(128,128,128));
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / abs($maxVal - $minVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + ($maxVal * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; round($i,1) >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      $skipNull = true;
    
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte) ." %");
    
      $n++;
      if($n >20)
        break;
    }
  
  
    $n=0;
  
    for($i=$nulpunt; round($i,1)<= $bodem; $i+= $absUnit*$stapgrootte)
    {
    
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(128,128,128)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)*-1 ." %");
      $n++;
      if($n >20)
        break;
    }
    $n=0;
    // $laatsteI = count($datumArray)-1;
    // $lijnenAantal = count($grafiekData);
    
     //listarray($grafiekData);exit;
    
    // if($this->pdf->debug==1)
    $cubic=true;
    // else
    //   $cubic=false;
    foreach ($grafiekData as $fonds=>$data)
    {
      $oldData=$data;
      $data=array();
      foreach ($oldData as $datum=>$value)
      {
        $datumArray[] = $datum;
        $data[] = $value;
      }
      //$kleur = array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);//55.96.145
      $kleur = array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);//  $this->pdf->rapport_tekstkleur_blauw;
      $yval=$YDiag + (($maxVal-100) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $kleur);
      
      if($cubic==true)
      {
        $Index = 1;
        $XLast = -1;
        foreach ( $data as $Key => $Value )
        {
          $XIn[$Key] = $Index;
          $YIn[$Key] = $Value;
          $Index++;
        }
        
        $Index--;
//         $Index=count($data);
        $Yt[0] = 0;
        $Yt[1] = 0;
        $U[1]  = 0;
        for($i=1;$i<=$Index-1;$i++)
        {
          $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
          $p      = $Sig * $Yt[$i-1] + 2;
          $Yt[$i] = ($Sig - 1) / $p;
          $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
          $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
        }
        $qn = 0;
        $un = 0;
        $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);
        
        for($k=$Index-1;$k>=1;$k--)
          $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];
        
        
        $Accuracy=0.1;
        for($X=1;$X<=$Index;$X=$X+$Accuracy)
        {
          $klo = 1;
          $khi = $Index;
          $k   = $khi - $klo;
          while($k > 1)
          {
            $k = $khi - $klo;
            If ( $XIn[$k] >= $X )
              $khi = $k;
            else
              $klo = $k;
          }
          $klo = $khi - 1;
          
          $h     = $XIn[$khi] - $XIn[$klo];
          $a     = ($XIn[$khi] - $X) / $h;
          $b     = ($X - $XIn[$klo]) / $h;
          $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;
          
          // echo "$Value <br>\n";
          
          //$YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);
          $YPos = $YDiag + (($maxVal-$Value) * $waardeCorrectie) ;
          $XPos = $XDiag+($X-1)*$unit;
          
          
          if($X==1)
          {
            $XLast=$XPos;
            $YLast=$YPos;
          }
          
          $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
          $XLast = $XPos;
          $YLast = $YPos;
          
        }
        
        
      }
      
      
      //  listarray($Yt);
      
      //listarray($data);
      $laatsteX=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      for ($i=0; $i<count($data); $i++)
      {
        if(!isset($datumPrinted[$i]))
        {
          $datumPrinted[$i] = 1;
          if(substr($datumArray[$i],5,5)=='06-30')//  || $i==0)
          {
            $xPositie=$XDiag+($i)*$unit;
            if($xPositie-$laatsteX<4)
              continue;
            
            //$this->pdf->line($xPositie, $YDiag+$hDiag, $xPositie, $YDiag+$hDiag-1,$lineStyle );
            $this->pdf->setXY($xPositie,$YDiag+$hDiag);
            $this->pdf->Cell($unit,4,date("Y",db2jul($datumArray[$i])),0,0,"C");
            //$this->pdf->TextWithRotation($xPositie,$YDiag+$hDiag+10,substr(vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($datumArray[$i]))],$this->pdf->rapport_taal),0,3).date("-Y",db2jul($datumArray[$i])),45);
            $laatsteX=$xPositie;
          }
          
          if(substr($datumArray[$i],5,5)=='12-31')// || $i==0)
          {
            $xPositie=$XDiag+($i)*$unit;
            if($xPositie-$laatsteX<4)
              continue;
            
            $this->pdf->line($xPositie, $YDiag+$hDiag, $xPositie, $YDiag+$hDiag+3,array('width' => 0.1, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $kleur) );
          }
        }
        
        if($data[$i] != 0)
        {
          $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
          $xval=$XDiag+($i)*$unit;
          
          if($i==0)
            $XvalLast=$XDiag;
          if($cubic == false)
            $this->pdf->line($XvalLast, $yval, $xval, $yval2,$lineStyle );
          $yval = $yval2;
          $XvalLast=$xval;
        }
        
      }
      
    }
    $this->pdf->setXY($XDiag,$YDiag+$hDiag+6);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->MultiCell($w,4,$omschrijving,0,"L");
    $this->pdf->SetTextColor(0,0,0);
  }
}
