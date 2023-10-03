<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/09/04 15:33:34 $
 		File Versie					: $Revision: 1.6 $

 		$Log: RapportOIV_L83.php,v $
 		Revision 1.6  2019/09/04 15:33:34  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/09/01 12:04:35  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/08/17 18:20:11  rvv
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

class RapportOIV_L83
{
	function RapportOIV_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
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

	  $this->pdf->rapport_titel = "Geconsolideerd effectenoverzicht";



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
    $q = "SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
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
    
    $totalenPerPortefeuille = array();
    $totalenPerCategorie = array();
    $totalen = array();
    $hoofdcategorieen = array();
    $hoofdcategorieOmschrijving = array();
    $pieData = array();
    $gebruiktePortefeuilles=array();
    $pdataPerPortefeuille=array();
    
    foreach ($this->portefeuilles as $portefeuille)
    {
  
      $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder $nawSelect FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $pdata = $db->lookupRecord();
      $pdataPerPortefeuille[$portefeuille]=$pdata;
  
      if($pdata['Portefeuillesoort']<>'Effecten')
        continue;
      
      $fondsRegels = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, (substr($this->rapportageDatum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $this->rapportageDatumVanaf);
      foreach ($fondsRegels as $regel)
      {
        if ($regel['type'] == 'rekening')
        {
          $regel['hoofdcategorie'] = 'G-LIQ';
        }
        if ($regel['beleggingscategorie'] == 'EFFECT')
        {
          $regel['hoofdcategorie'] = 'effecten';
        }
        
        $totalen[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
        $totalenPerCategorie[$portefeuille][$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
        $totalenPerPortefeuille[$portefeuille] += $regel['actuelePortefeuilleWaardeEuro'];
        if ($portefeuille <> $this->portefeuille)
        {
          $totalenPerCategorie[$this->portefeuille][$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
          $totalenPerPortefeuille[$this->portefeuille] += $regel['actuelePortefeuilleWaardeEuro'];
        }
        $hoofdcategorieen[$regel['beleggingscategorie']] = $regel['beleggingscategorieVolgorde'];
        $hoofdcategorieOmschrijving[$regel['beleggingscategorie']] = $regel['beleggingscategorieOmschrijving'];
        $gebruiktePortefeuilles[$portefeuille]=$portefeuille;
      }
    }
    $gebruiktePortefeuilles=array_values($gebruiktePortefeuilles);
   // listarray($totalen);
    
    if (count($gebruiktePortefeuilles) > 5)
    {
      $b = 0;
      $batches = array();
      foreach ($gebruiktePortefeuilles as $index => $portefeuille)
      {
        if ($index % 5 == 0)
        {
          $b++;
        }
        $batches[$b][] = $portefeuille;
        
      }
    }
    else
    {
      $batches = array($gebruiktePortefeuilles);
    }
    

    
    asort($hoofdcategorieen);
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
    $portefeuilleWidth = 26;
    $percentageWidth = 12;
    $widths = array(50);
    $aligns = array('L');
    for ($i = 0; $i < 6; $i++)
    {
      $widths[] = $portefeuilleWidth;
      $widths[] = $percentageWidth;
      $aligns[] = 'R';
      $aligns[] = 'R';
    }
    $this->pdf->SetWidths($widths);
    $this->pdf->SetAligns($aligns);
    $pieDataPerPortefeuille=array();
    foreach ($batches as $portefeuilleList)
    {
      $header = array('Geconsolideerd', 'Totaal', '%');
      foreach ($portefeuilleList as $portefeuille)
      {
        if ($portefeuille <> $this->portefeuille)
        {
          if($pdataPerPortefeuille[$portefeuille]['PortefeuilleNaam']<>'')
            $header[] = $pdataPerPortefeuille[$portefeuille]['PortefeuilleNaam'];
          else
            $header[] = $portefeuille;
          $header[] = '%';
        }
      }
      
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor(255, 255, 255);
      
      if($this->pdf->getY()>40)
      {
        $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
        $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8 , 'F');
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
      }
      $y=$this->pdf->getY();
      //$this->pdf->ln(2);
      $this->pdf->row($header);
      $this->pdf->setY($y+8);
      //$this->pdf->ln(2);
      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
      
      $row = array('Totaal', '€' . $this->formatGetal($totalenPerPortefeuille[$this->portefeuille], 0), '100%');
      foreach ($portefeuilleList as $portefeuille)
      {
        if ($portefeuille <> $this->portefeuille)
        {
          $row[] = '€' . $this->formatGetal($totalenPerPortefeuille[$portefeuille], 0);
          $row[] = $this->formatGetal($totalenPerPortefeuille[$portefeuille] / $totalenPerPortefeuille[$portefeuille] * 100, 0) . '%';
        }
      }
      $this->pdf->ln(2);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->row($row);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->ln(2);

      foreach ($hoofdcategorieen as $categorie => $volgorde)
      {
        
        //   $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]=$allekleuren['OIR'][$cat['regio']];
        //  $data['regioVerdeling']['kleurData'][$cat['Omschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaardeAAND*100;
        $percentage = round($totalenPerCategorie[$this->portefeuille][$categorie] / $totalenPerPortefeuille[$this->portefeuille] * 100, 2);
        $pieData['kleurData'][$hoofdcategorieOmschrijving[$categorie]] = $allekleuren['OIB'][$categorie];
        $pieData['kleurData'][$hoofdcategorieOmschrijving[$categorie]]['percentage'] = $percentage;
        $pieData['data'][$hoofdcategorieOmschrijving[$categorie]] = $percentage;
        
        $this->pdf->SetFillColor($allekleuren['OIB'][$categorie]['R']['value'],$allekleuren['OIB'][$categorie]['G']['value'],$allekleuren['OIB'][$categorie]['B']['value']);
        $this->pdf->Rect($this->pdf->marge, $this->pdf->getY()+0.5, 3, 3 , 'F');
        
        $row = array("      ".$hoofdcategorieOmschrijving[$categorie],
          '€' . $this->formatGetal($totalenPerCategorie[$this->portefeuille][$categorie], 0),
          $this->formatGetal($totalenPerCategorie[$this->portefeuille][$categorie] / $totalenPerPortefeuille[$this->portefeuille] * 100, 0) . '%');
        foreach ($portefeuilleList as $portefeuille)
        {
          if ($portefeuille <> $this->portefeuille)
          {
            $row[] = '€' . $this->formatGetal($totalenPerCategorie[$portefeuille][$categorie], 0);
            $percentage=round($totalenPerCategorie[$portefeuille][$categorie] / $totalenPerPortefeuille[$portefeuille] * 100,2);
            $row[] = $this->formatGetal($percentage, 0) . '%';
  
            $pieDataPerPortefeuille[$portefeuille]['kleurData'][$hoofdcategorieOmschrijving[$categorie]] = $allekleuren['OIB'][$categorie];
            $pieDataPerPortefeuille[$portefeuille]['kleurData'][$hoofdcategorieOmschrijving[$categorie]]['percentage'] = $percentage;
            $pieDataPerPortefeuille[$portefeuille]['data'][$hoofdcategorieOmschrijving[$categorie]] = $percentage;
          }
        }
        $this->pdf->row($row);
        $this->pdf->ln(2);
      }
    }
    $yPos=$this->pdf->getY()+30;
      $this->pdf->setXY(20, $yPos);
      
      $this->printPie($pieData['data'], $pieData['kleurData'], 'Verdeling totaal', 50, 50);
      $this->pdf->wLegend = 0;
      $x=65+38;
      foreach ($portefeuilleList as $portefeuille)
      {
        $this->pdf->setXY($x, $yPos);
          $this->printPie($pieDataPerPortefeuille[$portefeuille]['data'], $pieDataPerPortefeuille[$portefeuille]['kleurData'], '', 25, 25);
        $x+=38;
      }
      
      
      //  listarray($waarden[$categorie]);echo "test $categorie";
      
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      

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

	

}
?>
