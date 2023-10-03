<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/22 16:36:07 $
 		File Versie					: $Revision: 1.8 $

 		$Log: RapportOIB_L83.php,v $
 		Revision 1.8  2020/07/22 16:36:07  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2020/07/18 14:57:18  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/09/21 16:32:19  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/09/04 15:33:34  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/09/01 12:04:35  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/08/14 16:32:47  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/07/05 16:48:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/06/29 18:24:40  rvv
 		*** empty log message ***
 		

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L83
{
	function RapportOIB_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_deel = 'overzicht';
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->RapStartJaar = date("Y", $this->pdf->rapport_datumvanaf);
    if(is_array($this->pdf->portefeuilles))
      $this->portefeuilles=$this->pdf->portefeuilles;
    else
      $this->portefeuilles=array($portefeuille);
    
    if($this->RapStartJaar <> date("Y", $this->pdf->rapport_datum))
    {
      echo "Begin en einddatum moeten in hetzelfde jaar liggen";
      exit;
    }

	  $this->pdf->rapport_titel = "Huidige- en doelverdeling beleggingen";



		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->perioden['start'] = $this->rapportageDatumVanaf;
		$this->perioden['eind'] = $this->rapportageDatum;



    
  }

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
	  {
	    $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
	  }
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}

	function formatGetal($waarde, $dec, $percent = false,$limit = false,$nulTonen=true)
	{
	  if($waarde == '')
    {
    	if($nulTonen==false)
        return '';
    }

	    if($percent == true)
	    {
	      if($limit)
	      { //echo "$waarde <br>";
	        if($waarde >= $limit || $waarde <= $limit * -1)
	          return "p.m.";
	      }
	      return number_format($waarde,$dec,",",".").'%';
	    }

		  else
		    return number_format($waarde,$dec,",",".");
	 
	}

	function formatGetalKoers($waarde, $dec, $percent = false, $limit = false , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;
	  }
	  return $this->formatGetal($waarde, $dec, $percent = false,$limit = false);
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{

	  if ($VierDecimalenZonderNullen)
	  {

	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	       //  echo $this->portefeuille." $waarde <br>";exit;
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
  {
    global $__appvar;
        $gebruikteCrmVelden = array(
      'Portefeuillesoort',
      'PortefeuilleNaam');
    
    $db = new DB();
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $db->SQL($q);
    $db->Query();
    $kleuren = $db->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden = array();
    while ($data = $db->nextRecord())
    {
      $crmVelden[] = strtolower($data['Field']);
    }
    
    $nawSelect = '';
    $nietgevonden = array();
    foreach ($gebruikteCrmVelden as $veld)
    {
      if (in_array(strtolower($veld), $crmVelden))
      {
        $nawSelect .= ",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[] = $veld;
      }
    }

    $totalenPerPortefeuille=array();
    $totalenPerCategorie=array();
    $totalen=array();
    $hoofdcategorieen=array();
    $hoofdcategorieOmschrijving=array();
    $perioden=array($this->rapportageDatum);
    $pieData=array();
    
    $query="SELECT Beleggingscategorie,Normweging FROM NormwegingPerBeleggingscategorie WHERE portefeuille='$this->portefeuille'";
    $db->SQL($query);
    $db->Query();
    $normWeging = array();
    while ($data = $db->nextRecord())
    {
    	$normWeging[$data['Beleggingscategorie']]=$data['Normweging'];
    }
  
    $header=array('','Categorie','Perc.',date("d-m-Y",$this->pdf->rapport_datum)."\nWaarde",'','Categorie','Doel '.(date("Y",$this->pdf->rapport_datum)+3).' Perc.');
    
    foreach ($this->portefeuilles as $portefeuille)
    {

    	foreach($perioden as $datum)
			{
        $fondsRegels=berekenPortefeuilleWaarde($portefeuille, $datum, (substr($datum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
       // listarray($fondsRegels);
        foreach($fondsRegels as $regel)
        {
          if ($regel['type'] == 'rekening')
          {
         //   $regel['hoofdcategorie'] = 'G-LIQ';
          }
          if($regel['beleggingscategorie']=='EFFECT')
          {
            $regel['hoofdcategorie']='effecten';
          }
  
          $totalen[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
          $totalenPerCategorie[$portefeuille][$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
          $totalenPerPortefeuille[$portefeuille] += $regel['actuelePortefeuilleWaardeEuro'];
          if($portefeuille<>$this->portefeuille)
          {
            $totalenPerCategorie[$this->portefeuille][$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
            $totalenPerPortefeuille[$this->portefeuille] += $regel['actuelePortefeuilleWaardeEuro'];
          }
          $hoofdcategorieen[$regel['beleggingscategorie']] = $regel['beleggingscategorieVolgorde'];
          $hoofdcategorieOmschrijving[$regel['beleggingscategorie']] = $regel['beleggingscategorieOmschrijving'];
          
  
        }
      }
    }
    asort($hoofdcategorieen);
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetWidths(array(10,50,25,35,40,50,25));
    $this->pdf->SetAligns(array('L','L','R','R','C','L','R'));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
  
    $this->pdf->ln(2);
    foreach($hoofdcategorieen as $categorie=>$volgorde)
		{
   
   //   $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIR'][$cat['regio']];
    //  $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaardeAAND*100;
			$percentage=round($totalenPerCategorie[$this->portefeuille][$categorie]/$totalenPerPortefeuille[$this->portefeuille]*100,2);
      $pieData['kleurData'][$hoofdcategorieOmschrijving[$categorie]]=$allekleuren['OIB'][$categorie];
      $pieData['kleurData'][$hoofdcategorieOmschrijving[$categorie]]['percentage']=$percentage;
      $pieData['data'][$hoofdcategorieOmschrijving[$categorie]]=$percentage;
      
      $pieDataDoel['kleurData'][$hoofdcategorieOmschrijving[$categorie]]=$allekleuren['OIB'][$categorie];
      $pieDataDoel['kleurData'][$hoofdcategorieOmschrijving[$categorie]]['percentage']=$normWeging[$categorie];
      $pieDataDoel['data'][$hoofdcategorieOmschrijving[$categorie]]=$normWeging[$categorie];
      //$normWeging
      
      $this->pdf->SetFillColor($allekleuren['OIB'][$categorie]['R']['value'],$allekleuren['OIB'][$categorie]['G']['value'],$allekleuren['OIB'][$categorie]['B']['value']);
      $this->pdf->Rect($this->pdf->marge+5, $this->pdf->getY()+0.5, 3, 3 , 'F');
      $this->pdf->Rect($this->pdf->marge+155, $this->pdf->getY()+0.5, 3, 3 , 'F');
      
			$row=array('',$hoofdcategorieOmschrijving[$categorie],
        $this->formatGetal($totalenPerCategorie[$this->portefeuille][$categorie]/$totalenPerPortefeuille[$this->portefeuille]*100,0).'%',
				'€'.$this->formatGetal($totalenPerCategorie[$this->portefeuille][$categorie],0),'',$hoofdcategorieOmschrijving[$categorie],$this->formatGetal($normWeging[$categorie],0).'%');
      $this->pdf->row($row);
      $this->pdf->ln(2);
		}
  
    $row=array('','Totaal','100%','€'.$this->formatGetal($totalenPerPortefeuille[$this->portefeuille],0),'','Totaal','100%');

    $this->pdf->ln(2);
    $this->pdf->row($row);
    $this->pdf->ln(2);
  
    $yPos=$this->pdf->getY()+10;
  
    $this->pdf->setXY(30,$yPos);
    
    //$this->printPie($pieData['data'],$pieData['kleurData'],'Verdeling '.date("d-m-Y",$this->pdf->rapport_datum),50,50);
    //$this->pdf->wLegend=0;
  
  
    //$this->pdf->setXY(180,$yPos);
  
    //$this->printPie($pieDataDoel['data'],$pieDataDoel['kleurData'],'Doel verdeling '.(date("Y",$this->pdf->rapport_datum)+3),50,50);
    //$this->pdf->wLegend=0;

    //  listarray($waarden[$categorie]);echo "test $categorie";

    $this->pdf->setXY(100,$yPos);
    
    
    $h=$this->pdf->PageBreakTrigger-$yPos-10;
  
    
    $this->BarDiagram(100,$h,array($pieData['data'],$pieDataDoel['data']),'',array($pieData['kleurData'],$pieDataDoel['kleurData']));
  
  
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  }
  
  
  
  
  
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
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
    
    while (list($key, $value) = each($pieData))
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX,$y-4);
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
    $hoogte = ($this->pdf->getY() - $y) + 8;
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
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
    
    $x1 = $XPage ;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YDiag + ($radius) + $margin;
    /*
          for($i=0; $i<$this->pdf->NbVal; $i++) {
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
              $this->pdf->SetXY($x2,$y1);
              $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
              $y1+=$hLegend + 2;
          }
    */
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
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
  function BarDiagram($w, $h, $data, $format, $colorArray=null, $maxVal=0, $nbDiv=4)
  {
    $reeksenPerCategorie=1;
    if(count($data) > 1)
    {
      $newData=array();
      $newColors=array();
      $minVal=0;
      $maxVal=0;
      $reeksenPerCategorie=count($data) ;
      foreach($data as $set=>$waarden)
      {
        foreach($waarden as $groep=>$percentage)
        {
          $newData[$groep][$set] = $percentage;
          if ($percentage * 1.1 > $maxVal)
          {
            $maxVal = $percentage * 1.1;
          }
          if ($percentage * 1.1 < $minVal)
          {
            $minVal = $percentage * 1.1;
          }
        }
      }
      foreach($colorArray as $set=>$waarden)
      {
        foreach($waarden as $groep=>$kleuren)
          $newColors[$groep]=array($kleuren['R']['value'],$kleuren['G']['value'],$kleuren['B']['value']);
      }
      $data=$newData;
      $colorArray=$newColors;

    }
    
    
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
   // $this->pdf->SetLegends($data,$format);
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage;
    $lDiag = floor($w);
    if($color == null)
      $color=array(155,155,155);
   // listarray($colorArray);
    $aantalRegels=count($colorArray);
    $aantalStaven=$aantalRegels*$reeksenPerCategorie+1;

    $minVal=0;
    $offset=$minVal;
    $valIndRepere = ceil(($maxVal-$minVal) / $nbDiv);
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    if($aantalStaven*3*$reeksenPerCategorie < $h)
      $hDiag=$aantalStaven*3*$reeksenPerCategorie;
    
    $hBar = floor($hDiag / ($aantalStaven));
  //  $hDiag = $hBar * ($aantalStaven + 1 +$reeksenPerCategorie*1);
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
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
      for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
      }
      
      for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,1),0,0,'C');
      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $i=0;
    
    //$this->pdf->SetXY(0, $YDiag);
    //$this->pdf->Cell($nullijn, $hval-4, 'Onderwogen',0,0,'R');
    //$this->pdf->SetXY($nullijn, $YDiag);
    //$this->pdf->Cell(60, $hval-4, 'Overwogen',0,0,'L');
    $this->pdf->SetXY($XDiag, $YDiag);
   // $this->pdf->Cell($lDiag, $hval-4, 'Verdeling',0,0,'C');
    $extraY=0;
    foreach($data as $categorie=>$staven)
    {
      $yval=0;
      foreach($staven as $val)
      {
        $this->pdf->SetFillColor($colorArray[$categorie][0],$colorArray[$categorie][1],$colorArray[$categorie][2]);
        //Bar
        $xval = $nullijn;
        $lval = ($val * $unit);
        $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2+$extraY;
        $hval = $eBaton;
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
        //Legend
        $this->pdf->SetXY(0, $yval);
        $this->pdf->Cell(90, $hval, $this->formatGetal($val,0,true), 0, 0, 'R');
        $i++;
      }
      $this->pdf->SetXY(0, $yval-$hBar/2);
      $this->pdf->Cell(75, $hval, $categorie, 0, 0, 'R');
      $extraY=$extraY+2;
    }
    
    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);
    
    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
    
    for ($i = $nullijn+$XDiag; $i <= $maxVal; $i=$i+$unit)
    {
      $xpos = $XDiag +  $i;
      $this->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
      $val = $i * $valIndRepere;
      $xpos = $XDiag +  $i - $this->pdf->GetStringWidth($val) / 2;
      $ypos = $YDiag + $hDiag - $margin;
      $this->pdf->Text($xpos, $ypos, $val);
    }
  }

	

}
?>
