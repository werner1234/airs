<?php
/* 	
     AE-ICT source module
     Author  						: $Author: rvv $
  		Laatste aanpassing	: $Date: 2007/09/26 15:32:10 $
  		File Versie					: $Revision: 1.1 $
  		
  		$Log: barGraph.php,v $
  		Revision 1.1  2007/09/26 15:32:10  rvv
  		*** empty log message ***
  		
  	
 */

class barGraph
{
  function barGraph($pdf,$data)
  {
    
    $this->pdf=&$pdf;
    
    $this->standaardKleuren=array(array(255,0,0),	array(0,255,0),array(0,0,255),array(255,255,0),array(0,255,255),
						array(255,0,255),array(128,128,255),array(128,100,64),array(22,100,64),array(222,1,64)
						,array(255,0,100),array(100,255,0),array(155,0,0),array(0,155,0),array(0,0,155));	
    
    
  }
  
  function setXY($x,$y)
  {
    $this->pdf->setXY($x,$y);
 
  }
 
  
function VBarDiagram($w, $h, $waarden=array(),$Ystep=10)
  {
      global $__appvar;
      $shadow =1.5;
      $schaduwtint = 20;
      
      if ($Ystep <=0);
      $Ystep = 10;

      $i=0;
      foreach ($waarden as $kol)
      {
        $legenda[]=$kol['omschrijving'];
        $data[]=round($kol['percentage'],2);
        if($kol['kleur']['R']['value'] || $kol['kleur']['G']['value'] || $kol['kleur']['B']['value'])
          $color[]=array($kol['kleur']['R']['value'],$kol['kleur']['G']['value'],$kol['kleur']['B']['value']);
        else 
          $color[]=$this->standaardKleuren[$i];
        $i++;
      }
       
   
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 3;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);
   
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) 
      {
        $maxVal = ceil(max($data)* 1.1/10)*10;
      } 
      if ($maxVal == 0) 
      {
        $maxVal = 50;
      } 
      

      $minVal = floor(min($data)* 1.1/10)*10;
      
      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal +  $maxVal) * -1;
        $mulYpos =  $unit * (-1 * $minVal);
        
        $this->pdf->Line($XstartGrafiek, $YstartGrafiek + $mulYpos, $XstartGrafiek + $bGrafiek ,$YstartGrafiek + $mulYpos);
        $this->pdf->Line($XstartGrafiek + $shadow, $YstartGrafiek + $mulYpos - $shadow, $XstartGrafiek + $bGrafiek + $shadow ,$YstartGrafiek + $mulYpos - $shadow);

        $this->pdf->Line($XstartGrafiek+$bGrafiek, $YstartGrafiek + $mulYpos, $XstartGrafiek+$bGrafiek + $shadow, $YstartGrafiek + $mulYpos - $shadow); //rechtsvoor
        $this->pdf->Line($XstartGrafiek, $YstartGrafiek + $mulYpos, $XstartGrafiek + $shadow, $YstartGrafiek + $mulYpos - $shadow); //linksvoor
      }
      else 
      {
        $minVal = 0;
        $unit = $hGrafiek / $maxVal * -1;
        $mulYpos =0;
      }
      
      $horDiv = 1;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;
      
      if($maxVal - $minVal > 100)
        $Ystep = floor(($maxVal - $minVal)/100)*10;
      
      $i = floor($minVal/10)*10;
      
      while($i < $maxVal) //y-as verdeling
      {
        $ypos = $YstartGrafiek - $i * $unit * -1 + $mulYpos;
        
        $this->pdf->Line($XstartGrafiek+$shadow, $ypos-$shadow, $XstartGrafiek+$bGrafiek+$shadow, $ypos-$shadow); //achter
        $this->pdf->Line($XstartGrafiek, $ypos, $XstartGrafiek+$shadow, $ypos-$shadow); //links shaduw aansluiding
        

  //      $this->pdf->Line($XstartGrafiek+$bGrafiek, $ypos, $XstartGrafiek+$bGrafiek+$shadow, $ypos-$shadow); //rechts shaduw aansluiding onder
        
   //     $this->pdf->Line($XstartGrafiek, $ypos, $XstartGrafiek+$bGrafiek, $ypos); //voor
        
        $val = number_format($i); 
      //  $this->pdf->Text($XstartGrafiek-5, $ypos, $val);
        $this->pdf->setXY($XstartGrafiek-20, $ypos-2);
        $this->pdf->Cell(20,4,$val, 0,0, "R");
        
        $i += $Ystep;
      }
      
        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 80 / 100); 
         
       $this->pdf->Line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek+$bGrafiek, $YstartGrafiek); //voor   onderlijn 



      $this->pdf->SetLineWidth(0.2);
 //     $this->pdf->Rect($XPage, $YPage - 1, $w, $h * -1); //omlijning
      $this->pdf->Rect($XstartGrafiek+$shadow, $YstartGrafiek-$shadow, $bGrafiek, -1* $hGrafiek); //achter
      
      $this->pdf->Line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek, $YstartGrafiek -$hGrafiek); //voorlinks
    //  $this->pdf->Line($XstartGrafiek+$bGrafiek, $YstartGrafiek, $XstartGrafiek+$bGrafiek, $YstartGrafiek -$hGrafiek); //rechtsvoor
      
       $this->pdf->Line($XstartGrafiek+$bGrafiek, $YstartGrafiek, $XstartGrafiek+$bGrafiek+$shadow, $YstartGrafiek-$shadow); //reschts shaduw aansluiding
      $this->pdf->Line($XstartGrafiek, $YstartGrafiek-$hGrafiek, $XstartGrafiek+$shadow, $YstartGrafiek-$hGrafiek-$shadow); //links shaduw aansluiding

      
 //     $this->pdf->Rect($XstartGrafiek, $YstartGrafiek, $bGrafiek, -1* $hGrafiek); //voor 

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
   //   $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      foreach($data as $val) 
      {

          $this->pdf->SetFillColor($color[$i][0],$color[$i][1],$color[$i][2]);  
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $mulYpos;
          $hval = ($val * $unit);
          
       //   $this->pdf->Rect($xval+$shadow, $yval-$shadow, $lval, $hval, 'DF'); //achter
           
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');//voor
          
          if($val < 0)
          $hTop = 0;
          else 
          $hTop = $hval;

          //top/bodem
          $p = array($xval,$yval+$hTop,
                     $xval+$shadow,$yval-$shadow+$hTop,
                     $xval+$shadow+$lval,$yval-$shadow+$hTop,
                     $xval+$lval,$yval+$hTop,
                     $xval,$yval+$hTop);
          
         $this->pdf->Polygon($p,'DF',"" , array($color[$i][0]-$schaduwtint,$color[$i][1]-$schaduwtint,$color[$i][2]-$schaduwtint));
         
         $p = array( $xval+$shadow+$lval,$yval-$shadow+$hval,
                     $xval+$lval,$yval+$hval,
                     $xval+$lval,$yval,
                     $xval+$shadow+$lval,$yval-$shadow,
                     $xval+$shadow+$lval,$yval-$shadow+$hval);
          
         $this->pdf->Polygon($p,'DF',"" , array($color[$i][0]-$schaduwtint,$color[$i][1]-$schaduwtint,$color[$i][2]-$schaduwtint));
          
          //Legend
          $this->pdf->SetXY(0, $yval);
  //        $this->pdf->Cell($xval - $margin, $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      $i=0;
      $hLegend =3;
      $legendaMarge = 1;
      $aantal = count($legenda);
      $legendaHoogte = $aantal * ($hLegend + $legendaMarge);


      $x1=$XPage + $w +5;
      $y1=$YPage - $h + ($h - $legendaHoogte)/2;

      foreach ($legenda as $omschrijving)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[$i][0],$color[$i][1],$color[$i][2]);
          $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1 + 5,$y1);
          $this->pdf->Cell(0,4,$omschrijving);
          $y1+= $hLegend + $legendaMarge;
          
         $i++;

      }
     
 $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      
     
  }

}


