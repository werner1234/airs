<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/06 16:07:12 $
File Versie					: $Revision: 1.7 $

$Log: RapportKERNZ_L33.php,v $
Revision 1.7  2019/02/06 16:07:12  rvv
*** empty log message ***

Revision 1.6  2013/07/17 15:53:14  rvv
*** empty log message ***

Revision 1.5  2013/05/19 11:02:29  rvv
*** empty log message ***

Revision 1.4  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.3  2013/04/10 15:58:01  rvv
*** empty log message ***

Revision 1.2  2013/04/03 14:58:34  rvv
*** empty log message ***

Revision 1.1  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.32  2013/03/06 16:59:51  rvv
*** empty log message ***

Revision 1.31  2013/03/03 10:34:49  rvv
*** empty log message ***

Revision 1.30  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.29  2012/12/30 14:27:11  rvv
*** empty log message ***

Revision 1.28  2012/09/05 18:19:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportKERNZ_L33
{
	function RapportKERNZ_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_startDatum = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->zakelijk=true;

    $this->db=new DB();  
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
      $this->vastWhere="";
      $this->pdf->rapport_titel = vertaalTekst("Kerngegevens risicodragende portefeuille in",$this->pdf->rapport_taal)." ".$this->pdf->rapportageValuta;
 	    $this->rapportZakelijk();
  }



  
  function rapportZakelijk()
  {
    global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars['KERNZPaginas'] = $this->pdf->customPageNo;//+$this->pdf->extraPage
    $DB=new DB();
    
   	$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
  	$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
  	$DB->SQL($q);
	  $DB->Query();
	  $kleuren = $DB->LookupRecord();
	  $this->allekleuren = unserialize($kleuren['grafiek_kleur']);
 
    $this->vastWhere=" AND TijdelijkeRapportage.hoofdcategorie='G-RISD'";
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, rapportageDatum ".
					 "FROM TijdelijkeRapportage 
           WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'].$this->vastWhere." GROUP BY rapportageDatum";
	  debugSpecial($query,__FILE__,__LINE__);
  	$DB->SQL($query); //echo $query."<br>\n";
  	$DB->Query();
  	while($totaal = $DB->nextRecord())
    {
  	  $totaalWaarde[$totaal['rapportageDatum']]=$totaal['totaal'];
    }
    
  
    foreach($this->allekleuren['OIS'] as $secotr=>$kleurdata)
    {
      if($kleurdata['R']['value'] <> 0 && $kleurdata['G']['value'] <> 0 && $kleurdata['B']['value'] <> 0)
      {
        $standaardOISkleur = $kleurdata;
        break;
      }
    }
    foreach($this->allekleuren['OIR'] as $secotr=>$kleurdata)
    {
      if($kleurdata['R']['value'] <> 0 && $kleurdata['G']['value'] <> 0 && $kleurdata['B']['value'] <> 0)
      {
        $standaardOIRkleur = $kleurdata;
        break;
      }
    }

    
    $rapportageDatum = $this->rapportageDatum;
  	$query="SELECT beleggingssector,beleggingssectorOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere." 
    GROUP BY beleggingssector ORDER BY actuelePortefeuilleWaardeEuro desc ";
	  $DB->SQL($query); 
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['beleggingssector']=='')
        $fonds['beleggingssector']='Geen sector';
      if($fonds['beleggingssectorOmschrijving']=='')
        $fonds['beleggingssectorOmschrijving']='Geen';
      
      if($fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]<0.01)
      {
        $fonds['beleggingssectorOmschrijving']='Overige';
        $fonds['beleggingssector']='Overige';
      }
    
      if($this->allekleuren['OIS'][$fonds['beleggingssector']]['R']['value'] == 0 && $this->allekleuren['OIS'][$fonds['beleggingssector']]['G']['value'] == 0 && $this->allekleuren['OIS'][$fonds['beleggingssector']]['B']['value'] == 0)
        $data['sectorverdeling']['kleurData'][$fonds['beleggingssectorOmschrijving']]=$standaardOISkleur;
      else
        $data['sectorverdeling']['kleurData'][$fonds['beleggingssectorOmschrijving']]=$this->allekleuren['OIS'][$fonds['beleggingssector']];
      $data['sectorverdeling']['percentage'][$fonds['beleggingssectorOmschrijving']]+=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]*100;
	  }  

  	$query="SELECT regio,regioOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere." 
    GROUP BY regio ORDER BY actuelePortefeuilleWaardeEuro desc";
	  $DB->SQL($query); 
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['regio']=='')
        $fonds['regio']='Geen';
      if($fonds['regioOmschrijving']=='')
        $fonds['regioOmschrijving']='Geen';
    
      if($fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]<0.01)
      {
        $fonds['regioOmschrijving']='Overige';
        $fonds['regio']='Overige';
      }
    
      if($this->allekleuren['OIR'][$fonds['regio']]['R']['value'] == 0 && $this->allekleuren['OIR'][$fonds['regio']]['G']['value'] == 0 && $this->allekleuren['OIR'][$fonds['regio']]['B']['value'] == 0)
        $data['regioverdeling']['kleurData'][$fonds['regioOmschrijving']]=$standaardOIRkleur;
      else
        $data['regioverdeling']['kleurData'][$fonds['regioOmschrijving']]=$this->allekleuren['OIR'][$fonds['regio']];
      $data['regioverdeling']['percentage'][$fonds['regioOmschrijving']]+=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]*100;
	  
     
	  } 
    
    $max=max(count($data['regioverdeling']['percentage']),count($data['sectorverdeling']['percentage']));
    for($i=count($data['regioverdeling']['percentage']); $i<$max; $i++)
      $data['regioverdeling']['percentage'][]=0;
    for($i=count($data['sectorverdeling']['percentage']); $i<$max; $i++)
      $data['sectorverdeling']['percentage'][]=0;   
  
$this->pdf->setXY(30,66);
$this->BarDiagram(100,70,$data['regioverdeling']['percentage'],'%l (%p)',$data['regioverdeling']['kleurData'],vertaalTekst("Regioverdeling",$this->pdf->rapport_taal));

$this->pdf->setXY(180,66);
$this->BarDiagram(100,70,$data['sectorverdeling']['percentage'],'%l (%p)',$data['sectorverdeling']['kleurData'],vertaalTekst("Sectorverdeling",$this->pdf->rapport_taal));


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
          if($val <> 0)
          {
            $p=sprintf('%.1f',$val).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          }
          else
            $legend='';
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
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      if($minVal > 0)
        $minVal=0;
      $maxVal=ceil($maxVal/10)*10;  

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100; 
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      
      //echo "$hBar <br>\n";
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;

      $legendaStep=$unit/$nbDiv*$bandBreedte;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*5;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep*2;
      if($bandBreedte/$legendaStep > $nbDiv)
        $legendaStep=$legendaStep/2*5;
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
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
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
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $i=0;

      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      
   
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key]['R']['value'],$colorArray[$key]['G']['value'],$colorArray[$key]['B']['value']);
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

 
 


	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100,$hcat)
	{

	  $col1=array(255,0,0); // rood
	  $col2=array(0,255,0); // groen
	  $col3=array(255,128,0); // oranje
	  $col4=array(0,0,255); // blauw
	  $col5=array(255,255,0); // geel
	  $col6=array(255,0,255); // paars
	  $col7=array(128,128,128); // grijs
	  $col8=array(128,64,64); // bruin
	  $col9=array(255,255,255); // wit
	  $col0=array(0,0,0); //zwart
	  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();

		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
  			if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
		  	$pieData[$key] = $value['percentage'];
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
			$this->pdf->setXY($startX+5,$y-3);
			$this->pdf->Cell(130,4,$title,0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren,$hcat);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null,$hcat)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

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
      $aantal=count($data);
      foreach($data as $val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $radius*2 + 25 ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag - ($radius) + $margin+5;

$this->pdf->SetXY($this->pdf->GetX(),$y1-5);

      for($i=0; $i<$this->pdf->NbVal; $i++)
      {
          //$this->pdf->SetXY($x2-30,$y1);
          $this->pdf->SetX($x2-20);
          if($hcat[$i] <> $lastHcat)
          {
            if(isset($lastHcat))
            {
              $extraY=8;
              //$y1+=3;
            }

            $this->pdf->SetXY($this->pdf->GetX(),$this->pdf->GetY()+$extraY);
            $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
            $this->pdf->Cell(0,$hLegend,$hcat[$i]);
            $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          }
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1+$extraY);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);

          $y1+=$hLegend + 2;
          $lastHcat=$hcat[$i];
      }
      $this->pdf->SetFillColor(0,0,0);

  }

    function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array(vertaalTekst($l,$this->pdf->rapport_taal),$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }

  function renteResultaat($portefeuille,$startDatum,$eindDatum)
  {
    global $__appvar;
    $DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$eindDatum' AND ".
						 " portefeuille = '$portefeuille' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$startDatum' AND ".
						 " portefeuille = '$portefeuille' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
       $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
    else
       $koers=1;

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $koers;
		return $opgelopenRente;
  }

  function perfG($xPositie,$yPositie,$width,$height,$title='')
	{
    $this->pdf->setXY($xPositie,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,5,$title,'','C');
    $this->pdf->setXY($xPositie,$yPositie-5);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $this->pdf->Multicell($w,5,vertaalTekst('inclusief stortingen en onttrekkingen',$this->pdf->rapport_taal),'','C');

    $this->pdf->setXY($XDiag+$w+2,$yPositie-10);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->Multicell($w,5,'X 1.000','','R');

    $this->pdf->setXY($xPositie,$yPositie);

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    $DB = new DB();
    if(isset($this->pdf->portefeuilles))
      $port= "IN('".implode("','",$this->pdf->portefeuilles)."') ";
    else
      $port= "= '".$this->portefeuille."'";
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE periode='m' AND Portefeuille $port AND Categorie = 'Totaal'  ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    $datum = $DB->nextRecord();

    if($datum['id'] > 0 )//&& $this->pdf->lastPOST['perfPstart'] == 1
    {
      if($datum['month'] <10)
        $datum['month'] = "0".$datum['month'];
      $start = $datum['year'].'-'.$datum['month'].'-01';
    }
    else
      $start = $this->rapportageDatumVanaf;
    $eind = $this->rapportageDatum;

    $datumStart = db2jul($start);
    $datumStop  = db2jul($eind);

    $index = new indexHerberekening();
    $indexWaarden = $index->getWaarden($start,$eind,array($this->portefeuille,$this->pdf->portefeuilles));
    $aantalWaarden = count($indexWaarden);
    //echo $aantalWaarden;exit;
    $n=0;
    if($aantalWaarden < 13) // < dan een jaar gebruik maanden
    {
      $maandFilter=array(1,2,3,4,5,6,7,8,9,10,11,12);
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
      if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
        $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
      else
        $koers=1;
      $grafiekData['portefeuille'][$n]=$data['waardeHuidige']/$koers;
      $grafiekData['storingen'][$n]+=($data['stortingen']-$data['onttrekkingen'])/$koers;
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
    $horDiv = 10;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.3);
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $procentWhiteSpace = 0.10;

    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))))*pow(10,strlen($stepSize));

    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - ($procentWhiteSpace))/$stepSize)*$stepSize;
    $horDiv=($maxVal - $minVal)/$stepSize*2;
    if($horDiv > 10)
      $horDiv=($maxVal - $minVal)/$stepSize;

    $legendYstep = round(($maxVal - $minVal) / $horDiv);
    $vBar = ($lDiag / (count($grafiekData['portefeuille'])+ 1));
    $bGrafiek = $vBar * (count($grafiekData['portefeuille']) + 1);
    $eBaton = ($vBar * .5);

    $unith = $hDiag / ($maxVal - $minVal);
    $unitw = $vBar;//$lDiag / count($grafiekData['portefeuille']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag,'FD','',array(245,245,245));
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
       $this->pdf->Text($XDiag+$w+2, $i, $this->formatGetal(0-($n*$legendYstep/1000)));
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
         $this->pdf->Text($XDiag+$w+2, $i, ($this->formatGetal($n*$legendYstep/1000)));
       $n++;
       if($n >20)
         break;
     }
     $n=0;
     $laatsteI = count($datumArray)-1;
     $lijnenAantal = count($grafiekData);

          $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.1));
     foreach ($grafiekData['storingen'] as $i=>$waarde)
     {
       $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
       $yval = $yval2;
       $xval = $XDiag + (1 + $i ) * $unitw - ($eBaton / 2);
       $lval = $eBaton;
       $hval = ($waarde * $unit);
       $hval =$nulpunt-$yval;
       $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,array(145,182,215)); //  //0,176,88
     }
     unset($yval);

     $lineStyle = array('width' => 0.75, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
     $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
     foreach ($grafiekData['portefeuille'] as $i=>$waarde)
     {
         if(!isset($datumPrinted[$i]))
         {
           $datumPrinted[$i] = 1;
           //if(substr($datumArray[$i],5,5)=='12-31' || $i == $laatsteI || $i==0)
           $julDatum=db2jul($datumArray[$i]);
           $this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);
         }
         if($waarde)
         {
           $yval2 = $YDiag + (($maxVal-$waarde) * $absUnit) ;
           if($yval)
           {
             $markerSize=0.5;
             $this->pdf->line($XDiag+$i*$unitw, $yval, $XDiag+($i+1)*$unitw, $yval2,$lineStyle );
             $this->pdf->Rect($XDiag+$i*$unitw-0.5*$markerSize, $yval-0.5*$markerSize, $markerSize, $markerSize, 'DF',null,array(0,176,88));
           }
           $yval = $yval2;
         }
     }


     $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
     $this->pdf->SetFillColor(0,0,0);
     $this->pdf->CellBorders = array();
	}

	function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();

      $xPositie=$this->pdf->getX();
      $yPositie=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
      $this->pdf->setXY($xPositie-20,$yPositie-$h-8);
      $this->pdf->Multicell($w,5,'Kasstroom uit de vastrentende portefeuille','','C');
      $this->pdf->setXY($xPositie+110,$yPositie-$h-8);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
      $this->pdf->Multicell(20,5,'X 1.000','','L');
      $this->pdf->setXY($xPositie,$yPositie);


      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = $datum;
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $datumTotalen[$datum]+=$waarde;
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          if($waarde < 0)
          {
            $verwijder[$datum]=$datum;
            $grafiek[$datum][$categorie]=0;
            $grafiekCategorie[$categorie][$datum]=0;
          }


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;


        }
      }

      $colors=array('lossing'=>array($this->allekleuren['OIB']['OBL-ST']['R']['value'],$this->allekleuren['OIB']['OBL-ST']['G']['value'],$this->allekleuren['OIB']['OBL-ST']['B']['value']),
      'rente'=>array($this->allekleuren['OIB']['Liquiditeiten']['R']['value'],$this->allekleuren['OIB']['Liquiditeiten']['G']['value'],$this->allekleuren['OIB']['Liquiditeiten']['B']['value']));

      foreach ($verwijder as $datum)
      {
        foreach ($data[$datum] as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
      }

      $numBars = count($legenda);


      if($color == null)
      {
        $color=array(155,155,155);
      }
      $maxVal=max($datumTotalen);
      $minVal = 0;


      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*10+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*10+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }
      $maxmaxVal=ceil($maxVal/(pow(10,strlen(round($maxVal)))))*pow(10,strlen(round($maxVal)));

      if($maxmaxVal/8 > $maxVal)
        $maxVal=$maxmaxVal/8;
      elseif($maxmaxVal/4 > $maxVal)
        $maxVal=$maxmaxVal/4;
      elseif($maxmaxVal/2 > $maxVal)
        $maxVal=$maxmaxVal/2;
      else
        $maxVal=$maxmaxVal;

      $unit = $hGrafiek / $maxVal * -1;

      $nulYpos =0;

      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = (abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $bGrafiek, $hGrafiek,'FD','',array(245,245,245));

      $n=0;

      for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+1, $i-1.5);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', 6);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte/1000)."",0,0,'L');
        $n++;
      }

    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
         //   $this->pdf->SetXY($xval, $yval+($hval/2)-2);
         //   $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-0.75,$YstartGrafiek+5.25,$legenda[$datum],45);

           //$this->pdf->TextWithRotation($XDiag+($i+1)*$unitw-6,$YDiag+$hDiag+10,vertaalTekst($maanden[date("n",$julDatum)],$pdf->rapport_taal).'-'.date("Y",$julDatum),45);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }


   $x1=$xval-50;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']='Coupons';
   $vertaling['lossing']='Lossingen';

         foreach ($colors as $categorie=>$color)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1  ,$y1);
          $this->pdf->Cell(0,4,$vertaling[$categorie]);
         // $y1+= $hLegend + $legendaMarge;
          $x1+=40;
         $i++;

      }

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>