<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/24 14:44:04 $
File Versie					: $Revision: 1.7 $

$Log: RapportHUIS_L24.php,v $
Revision 1.7  2019/04/24 14:44:04  rvv
*** empty log message ***

Revision 1.6  2019/04/20 16:59:35  rvv
*** empty log message ***

Revision 1.5  2019/04/06 17:12:28  rvv
*** empty log message ***

Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.2  2015/06/20 15:34:20  rvv
*** empty log message ***

Revision 1.1  2014/07/30 15:36:14  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportHUIS_L24
{
	function RapportHUIS_L24($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
	}

	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$portefeuilles=array();
		$query = "SELECT Fondsen.Portefeuille,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
              FROM TijdelijkeRapportage 
              JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
              INNER JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND FondsenBuitenBeheerfee.huisfonds=1
              WHERE rapportageDatum ='".$this->rapportageDatum."' AND 
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND Fondsen.Portefeuille<>'' "
						  .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
		$DB->SQL($query);//Fondsen.Huisfonds=1 AND
		$DB->Query();
		while($data = $DB->NextRecord())
 	  {
		  $portefeuilles[$data['Portefeuille']]=$data;
    }
    //listarray($portefeuilles);exit;
   // $this->pdf->rapport_datumvanaf
   // ; 
    $kopBackup=$this->pdf->rapport_koptext;
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $rapportageDatum['a'] = date("Y-m-d",$this->pdf->rapport_datumvanaf); 
      $rapportageDatum['b'] = date("Y-m-d",$this->pdf->rapport_datum);

	    if($this->pdf->rapport_datumvanaf < db2jul($pdata['Startdatum']))
	      $rapportageDatum['a'] = $pdata['Startdatum'];
	  
  	  if($this->pdf->rapport_datum > db2jul($pdata['Einddatum']))
  	  {
	    	//echo "<b>Fout: Portefeille $portefeuille heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
	  	  continue;
	    }
    	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
	    {
	    	//echo "<b>Fout: $portefeuille Van datum kan niet groter zijn dan  T/m datum! </b>";
		    continue;
	    }

      if(substr($rapportageDatum['a'],5,2)=='01' && substr($rapportageDatum['a'],8,2)=='01')
        $startjaar=true;
      else
        $startjaar=false;  
      
     	$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['a'],$startjaar,$pdata['RapportageValuta'],$rapportageDatum['a']);
	    $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],0,$pdata['RapportageValuta'],$rapportageDatum['a']);
     	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatum['a']);
	    vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
      $portefeuilleWaarde=0;
      foreach($fondswaarden['b'] as $fonds)
        $portefeuilleWaarde+=$fonds['actuelePortefeuilleWaardeEuro'];
      
      
      
      $rapport=new RapportPERFG_L24($this->pdf, $portefeuille, $rapportageDatum['a'], $rapportageDatum['b']);
      $this->pdf->rapport_titel='Huisfonds-overzicht';
      $this->pdf->rapport_koptext="\n \n".$pdata['Omschrijving']."\n \n";
      $rapport->aandeel=$pdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
      $rapport->fondsWaarden=$fondswaarden['b'];
      $rapport->totaalHuisWaarde=$portefeuilleWaarde;
      $rapport->writeRapport();
      
     

       }
    $this->pdf->rapport_koptext=$kopBackup;
    
	}
}  
  
 class RapportPERFG_L24
{
	function RapportPERFG_L24($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
	} 
  
  
  function writeRapport()
  {
      
      $this->pdf->AddPage();

       $index = new indexHerberekening();
       $indexWaarden = $index->getWaarden($this->rapportageDatumVanaf,$this->rapportageDatum,$this->portefeuille);
       
       //listarray($indexWaarden);
       foreach ($indexWaarden as $index=>$data)
       {
         if($data['datum'] != '0000-00-00')
         {
           $lineData['Datum'][] = $data['datum'];
           $lineData['Index'][] = $data['index']-100;
           $barData[$data['datum']] = $data['performance'];          
           
         }
       }
   
       if (count($lineData) > 1)
		   {
		    
        $this->pdf->SetXY(8,50);//104
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
  		  $this->pdf->Cell(0, 5, 'Rendement (cumulatief)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),15+120,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,60)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(120, 40, $lineData,$this->pdf->rapport_grafiek_color,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 80);

        $this->pdf->SetXY(150,50);//104
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
        $this->pdf->Cell(0, 5, 'Maandelijks rendement', 0, 1);      
        $this->pdf->Line(150, $this->pdf->GetY(),278,$this->pdf->GetY());  
        $this->VBarDiagram($indexWaarden,150,60,120,40);
		   }
       
       $fondsenArray=array();
       foreach($this->fondsWaarden as $index=>$fondsData)
       {
         $fondsenArray[$index]=$fondsData['actuelePortefeuilleWaardeEuro'];
         arsort($fondsenArray);
       }
       
       $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
       $this->pdf->SetXY($this->pdf->marge,120);
       $this->pdf->SetAligns(array('L','R','R'));
       $this->pdf->SetWidths(array(50,50,25));
       $this->pdf->Row(array('De 10 grootste componenten.'));
       $this->pdf->Row(array('Fonds','Aandeel fonds','Waarde'));
       $this->pdf->SetWidths(array(75,25,25));
       $this->pdf->SetAligns(array('L','R','R'));
       $n=0;
       $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
       foreach($fondsenArray as $index=>$fondsWaarde)
       {
        $this->pdf->Row(array($this->fondsWaarden[$index]['fondsOmschrijving'],
        $this->formatGetal($this->fondsWaarden[$index]['actuelePortefeuilleWaardeEuro']/$this->totaalHuisWaarde*100,1)."%",
        $this->formatGetal($this->fondsWaarden[$index]['actuelePortefeuilleWaardeEuro']*$this->aandeel,0)));
                              
        $n++;
        if($n>9)
          break;
       }
       //listarray($fondsenArray);
       
       //listarray($this->fondsWaarden);
  }
  
  function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  function VBarDiagram($indexWaarden,$xPositie,$yPositie,$width,$height,$title='')
	{
    //$this->pdf->setXY($xPositie,$yPositie-10);
    //$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    //$this->pdf->Multicell($w,5,$title,'','C');
    //$this->pdf->setXY($xPositie,$yPositie-5);
    //$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    //$this->pdf->Multicell($w,5,vertaalTekst('inclusief stortingen en onttrekkingen',$this->pdf->rapport_taal),'','C');

    //$this->pdf->setXY($XDiag+$w+2,$yPositie-10);
    //$this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    //$this->pdf->Multicell($w,5,'X 1.000','','R');

    $this->pdf->setXY($xPositie,$yPositie);

    $this->pdf->SetLineStyle(array('color'=>array(0,44,90),'dash' => 0));
    
    $aantalWaarden = count($indexWaarden);
    //echo $aantalWaarden;exit;
    $n=0;
    if($aantalWaarden < 13) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
      $aantalWaarden=12;
    }
    elseif ($aantalWaarden < 49) // < 4 jaar gebruik kwartalen
    {
      $maandFilter=array(3,6,9,12);
    }
    else // gebruik jaren
    {
      $maandFilter=array(12);
    }

    foreach ($indexWaarden as $id=>$data)
    {

      //$grafiekData['portefeuille'][$n]=$data['waardeHuidige'];
      $grafiekData['performance'][$n]+=($data['performance']);
      $datumArray[$n]=$data['datum'];
      $maand=date('m',db2jul($data['datum']));
      if(in_array($maand,$maandFilter))
        $n++;
    }


    $minVal = -1;
    $maxVal = 1;


    foreach ($grafiekData as $type=>$maxData)
    {
      foreach ($maxData as $waarde)
      {
        $maxVal=max($maxVal,$waarde);
        $minVal=min($minVal,$waarde);
      }
    }

    $w=$width;
    $h=$height;
    $horDiv = 1;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    /*
    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 0.10;
    */
    
    $band=($maxVal - $minVal);
    $stepSize=ceil($band / $horDiv);
    //echo "<br>\n $band / $horDiv = ".$stepSize.""; ob_flush();
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize))/2;

    //echo "<br>\n".$stepSize."<br>\n"; ob_flush();

    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    $horDiv=($maxVal - $minVal)/$stepSize*2;
    if($horDiv > 10)
      $horDiv=($maxVal - $minVal)/$stepSize;

    $legendYstep = round(($maxVal - $minVal) / $horDiv);
    $vBar = ($lDiag / ($aantalWaarden+ 1));
    $bGrafiek = $vBar * ($aantalWaarden + 1);
    $eBaton = ($vBar * .5);

    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'DF','',array(240,240,240));
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    $nulpunt = $YDiag + ($maxVal * $unith);
    $n=0;

    $this->pdf->Line($XDiag, $nulpunt, $XPage+$w ,$nulpunt,array('dash' => 1,'color'=>array(128,128,128)));
     for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$legendYstep)
     {
       $skipNull = true;
       $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
       $this->pdf->Text($XDiag+$w+2, $i, $this->formatGetal(0-($n*$legendYstep))."%");
       $n++;
       if($n >20)
        break;
     }

     $n=0;
     for($i=$nulpunt; $i > $top; $i-= $absUnit*$legendYstep)
     {
       $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('width' => 0.1,'dash' => 1,'color'=>array(128,128,128)));
       if($skipNull == true)
         $skipNull = false;
       else
         $this->pdf->Text($XDiag+$w+2, $i, ($this->formatGetal($n*$legendYstep)."%"));
       $n++;
       if($n >20)
         break;
     }
     $n=0;
     $laatsteI = count($datumArray)-1;
     $lijnenAantal = count($grafiekData);

          $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.1));
               $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');

     foreach ($grafiekData['performance'] as $i=>$waarde)
     {
       $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
       $yval = $yval2;
       $xval = $XDiag + (1 + $i ) * $unitw - ($eBaton / 2);
       $lval = $eBaton;
       $hval = ($waarde * $unit);
       $hval =$nulpunt-$yval;
       $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array(0,44,90)); //  //0,176,88
       
       if(!isset($datumPrinted[$i]))
       {
         $datumPrinted[$i] = 1;
         $julDatum=db2jul($datumArray[$i]);
         $this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,date("d-M-Y",$julDatum),25);
         
       //  $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,date("d-M-Y",$julDatum),25);

     
       }
     }
     unset($yval);

     $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
     $this->pdf->SetFillColor(0,0,0);
     $this->pdf->CellBorders = array();
	}
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['Datum'];
    $data = $data['Index'];
    $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'DF','',array(240,240,240));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }


    $color1=array(155,155,155);
    if($color == null)
      $color=array(0,44,90);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

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

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);

    if($jaar)
      $unit = $lDiag / 12;

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>$color1));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>$color1));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }


    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
    for ($i=0; $i<count($data); $i++)
    {
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,date("d-M-Y",db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }
  
  

}
?>
