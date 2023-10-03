<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/23 13:34:01 $
 		File Versie					: $Revision: 1.12 $

 		$Log: PDFRapport_headers_L79.php,v $
 		Revision 1.12  2019/10/23 13:34:01  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/10/09 15:11:04  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2019/09/28 17:20:17  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2019/07/03 15:37:22  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/02/06 16:07:12  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/01/20 12:14:00  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/12/29 13:57:23  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/12/15 17:49:14  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/12/01 19:51:30  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/11/24 19:10:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/09/08 17:43:29  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.8  2017/12/16 18:44:16  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/12/09 17:54:25  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/10/19 10:58:45  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/10/16 15:14:53  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/08/27 16:26:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/06/15 15:58:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/04/03 10:58:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/03/19 16:51:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/01/14 12:34:42  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/11/01 22:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/10/29 16:47:19  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/09/17 15:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/08/25 08:50:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/18 12:24:51  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/08/10 15:48:01  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/07/28 09:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/06/09 18:01:53  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/06/05 15:56:07  rvv
 		*** empty log message ***
 		
*/

 function Header_basis_L79($object)
 { 
   $pdfObject = &$object;

	 if($pdfObject->lastPortefeuille2 != $pdfObject->portefeuilledata['Portefeuille'] || empty($pdfObject->lastPortefeuille2) )
		 $pdfObject->rapportNewPage = $pdfObject->page;
	 $pdfObject->lastPortefeuille2 = $pdfObject->portefeuilledata['Portefeuille'];

	 if ($pdfObject->rapport_type == "MANDAATCONTROLE")
	 {

	 }
   elseif ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		  $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
	  	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	  //	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast  && $pdfObject->rapport_layout != 16)
  	//  	$pdfObject->customPageNo = 0;
      $pdfObject->rapportNewPage = $pdfObject->page;
    }
    else
    {
     // if ($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
     // {
     //   $pdfObject->customPageNo = 0;
    //  }
      $pdfObject->customPageNo++;
  
      $pdfObject->SetLineWidth($pdfObject->lineWidth);
  
      if (empty($pdfObject->top_marge))
      {
        $pdfObject->top_marge = $pdfObject->marge;
      }
      $pdfObject->SetY($pdfObject->top_marge);
  
      $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'], $pdfObject->rapport_kop2_fontcolor['g'], $pdfObject->rapport_kop2_fontcolor['b']);
      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
      $pdfObject->SetX($pdfObject->marge);
     
      $kwartaal=intval(ceil(date("n",$pdfObject->rapport_datum)/3));
      $kwartalen=array('1'=>'eerste','2'=>'tweede','3'=>'derde','4'=>'vierde');
      
      $periode='Beheerrapportage '.$kwartalen[$kwartaal].' kwartaal '.date('Y',$pdfObject->rapport_datum);
      if($pdfObject->portefeuilledata['ClientVermogensbeheerder']<>'')
        $periode.= " (".$pdfObject->portefeuilledata['ClientVermogensbeheerder'].")";
     // listarray($pdfObject->portefeuilledata);
      $pdfObject->Cell(90, 4,$periode, 0, 0,'L');
      $pdfObject->Cell(297-90-$pdfObject->marge*2, 4,$pdfObject->portefeuilledata['VermogensbeheerderNaam'], 0, 0,'R');
      $pdfObject->ln();
   
      $pdfObject->SetDrawColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
      $pdfObject->line($pdfObject->marge,$pdfObject->getY(),297-$pdfObject->marge,$pdfObject->getY());
      $pdfObject->ln($pdfObject->rowHeight*2);

      $pdfObject->SetX($pdfObject->marge);
      $pdfObject->SetFont($pdfObject->rapport_font, 'B', $pdfObject->rapport_fontsize+4);

      $pdfObject->Cell(297-$pdfObject->marge*2, 4,$pdfObject->rapport_titel, 0, 0,'L');
      $pdfObject->ln();
      
      $x=$pdfObject->getX();
      $y=$pdfObject->getY();
  
      $pdfObject->AutoPageBreak=false;
      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
      $pdfObject->setXY(297-$pdfObject->marge-90,210-$pdfObject->marge);
      $pdfObject->Cell(90, 4,$pdfObject->customPageNo, 0, 0,'R');
      $pdfObject->AutoPageBreak=true;
      $pdfObject->setXY($x,$y);
      
    }
   $pdfObject->headerStart=$pdfObject->getY()+15;
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
 }


function HeaderMANDAATCONTROLE_L79($object)
	{
    $pdfObject = &$object;

	}
function HeaderPERF_L79($object)
{
  $pdfObject = &$object;
  
}

function HeaderPERFG_L79($object)
{
  $pdfObject = &$object;
  
}

function HeaderPERFD_L79($object)
{
  $pdfObject = &$object;

}

function HeaderATT_L79($object)
{
  $pdfObject = &$object;
  
}

function HeaderOIH_L79($object)
{
  $pdfObject = &$object;
  
}
function HeaderOIB_L79($object)
{
  $pdfObject = &$object;
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  //$pdfObject->headerOIB();
}

function HeaderOIR_L79($object)
{
  $pdfObject = &$object;
 // $pdfObject->headerOIR();
}


function HeaderTRANS_L79($object)
{
  $pdfObject = &$object;
  $pdfObject->headerTRANS();
}

function HeaderMUT_L79($object)
{
  $pdfObject = &$object;
  $pdfObject->headerMUT();
}

function HeaderVOLK_L79($object)
{
  $pdfObject = &$object;
  $pdfObject->ln($pdfObject->rowHeight*3);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
 
  $pdfObject->fillCell=array();
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);

  //$teken=$pdfObject->rapportageValuta;
  
  $pdfObject->row(array('','','',
                    vertaalTekst("Huidige\nkoers",$pdfObject->rapport_taal),
                    '','',
                    '',
                    vertaalTekst("Rendement %\nSinds opname",$pdfObject->rapport_taal)
                  ));
  $pdfObject->ln(-6);
  $pdfObject->row(array(vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    '',
                    '',
                    vertaalTekst("Waarde in euro",$pdfObject->rapport_taal),
                    vertaalTekst("Gewicht",$pdfObject->rapport_taal)." %",
                    vertaalTekst("Rendement %",$pdfObject->rapport_taal).' '.date("Y",$pdfObject->rapport_datum)
                  ));
  
  $pdfObject->ln();
}


if(!function_exists('PieChart_L79'))
{
  function PieChart_L79($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data,$format);
    
    
    $XPage = $pdfObject->GetX();
    $YPage = $pdfObject->GetY();
    
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
      $pdfObject->Rect($XPage,$YPage,$w,$h);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
    $pdfObject->setXY($XPage,$YPage);
    $pdfObject->SetFont($pdfObject->rapport_font, 'B', 8.5);
    $pdfObject->Cell($w,4,$titel,0,1,'L');
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
    $YPage=$YPage+$pdfObject->rowHeight+4;
    $pdfObject->setXY($XPage,$YPage);
    $margin = 4;
    $hLegend = 2;
    $radius = min($w, $h); //
    $radius = ($radius / 2)-4;
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $pdfObject->NbVal; $i++) {
        $gray = $i * intval(255 / $pdfObject->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $pdfObject->SetDrawColor(255,255,255);
    $pdfObject->SetLineWidth(0.1);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $factor =$radius+4;
    $pdfObject->SetFont($pdfObject->rapport_font, '', 7);
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      //$pdfObject->SetDrawColor(255,255,0);
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        
        //$lineAngle=($angleEnd)/180*M_PI;
        //$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$factor), $YDiag-(cos($lineAngle)*$factor));
        //echo ($angleEnd-$angleStart)."= ( $angleEnd-$angleStart ) $val  <br>\n";ob_flush();
        
        if(round($angleEnd,1)==360)
          $angleEnd=360;
        //    echo "$val : $XDiag, $YDiag, $radius, $angleStart, $angleEnd <br>\n";
        if(abs($angleEnd-$angleStart) > 1)
          $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd,'F');
        
        if($val > 2)
        {
          //$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
          if($pdfObject->debug==true)
          {
            $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255)));
            $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor));
          }
          $pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
          $pdfObject->Cell(10,4,number_format($val,0,',','.').'%',0,0,'C');
        }
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360,'F');
    }
    
    
    $i = 0;
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.3527,'color'=>array(255,255,255)));
      if ($angle != 0 && $angle != 360)
      {
        $angleEnd = $angleStart + $angle;
        $lineAngle=($angleEnd)/180*M_PI;
        $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$radius), $YDiag-(cos($lineAngle)*$radius));
        $angleStart += $angle;
      }
      $i++;
    }
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor(0,0,0);
    
    //Legends
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
    $x1 = $XPage + $margin;
    $x2 = $x1 + $hLegend + 2 ;
    $y1 = $YDiag + ($radius) + $margin +5;
    
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YDiag + ($radius) + $margin,$XPage+2,$YDiag + ($radius) + $margin +5);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
    
    if(is_array($legendaStart))
    {
      $x1=$legendaStart[0];
      $y1=$legendaStart[1];
      $x2 = $x1 + $hLegend + 2 ;
      
    }
    elseif($legendaStart=='geen')
    {
      return '';
    }
    
    for($i=0; $i<$pdfObject->NbVal; $i++) {
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
      $pdfObject->SetXY($x2,$y1);
      $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
      $y1+=$hLegend*2;
    }
    
  }
}


?>