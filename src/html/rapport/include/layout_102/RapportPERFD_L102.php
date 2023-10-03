<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/11/18 18:58:17 $
File Versie					: $Revision: 1.1 $

$Log: RapportATT_L102.php,v $

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFD_L102
{
	function RapportPERFD_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		
		
		$this->pdf->rapport_titel = "Vermogensverdeling - lange termijn";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;
    $this->categorieKleuren=array();

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
/*
	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
*/
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	   if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	   {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      $this->extraVulling = true;
	   }
	  }
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

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}



	function writeRapport()
  {
    global $__appvar;
    
    
    //Kleuren instellen
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q = "SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    //$this->extraVerdeling=
    if(isset($this->extraVerdeling) && $this->extraVerdeling=='AttributieCategorie')
    {
      $this->categorieKleuren = $allekleuren['ATT'];
      $verdeling='AttributieCategorie';
      $verdelingOmschrijvng=$verdeling;
      $verdelingKeuze='AttributieCategorien';
      $this->categorieVolgorde['Liquiditeiten']='Liquiditeiten';
      $this->categorieOmschrijving['Liquiditeiten']='Liquiditeiten';
    }
    else
    {
      $this->categorieKleuren = $allekleuren['OIB'];
      $this->extraVerdeling='hoofdcategorie';
      $verdelingOmschrijvng='beleggingscategorie';
      $verdelingKeuze='Beleggingscategorien';
      $query="SELECT hoofdcategorie FROM CategorienPerHoofdcategorie JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.hoofdcategorie=Beleggingscategorien.Beleggingscategorie ORDER BY Beleggingscategorien.afdrukVolgorde ";
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $this->categorieVolgorde[$data['hoofdcategorie']] = $data['hoofdcategorie'];
        $this->categorieOmschrijving[$data['hoofdcategorie']] = vertaalTekst($data['hoofdcategorie'], $this->pdf->rapport_taal);
      }
  
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    

    
    

    $query="SELECT
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
WHERE
KeuzePerVermogensbeheerder.vermogensbeheerder='$beheerder' AND
KeuzePerVermogensbeheerder.categorie='$verdelingKeuze'
ORDER BY
KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->nextRecord())
    {
      $this->categorieVolgorde[$data['waarde']] = $data['waarde'];
      $this->categorieOmschrijving[$data['waarde']] = vertaalTekst($data['waarde'], $this->pdf->rapport_taal);
    }
    
    $q="SELECT $verdelingOmschrijvng as Beleggingscategorie, Omschrijving FROM $verdelingKeuze";
    $DB->SQL($q);
    $DB->Query();
    while ($data = $DB->nextRecord())
    {
      //$this->categorieVolgorde[$data['Beleggingscategorie']] = $data['Beleggingscategorie'];
      if(isset($this->categorieOmschrijving[$data['Beleggingscategorie']]))
        $this->categorieOmschrijving[$data['Beleggingscategorie']] = vertaalTekst($data['Omschrijving'], $this->pdf->rapport_taal);
    }
    $start=$this->getGrafiekStart('kwartaal');
    if(isset($this->extraVerdeling) && $this->extraVerdeling=='AttributieCategorie')
      $this->pdf->rapport_titel = "Risicoverdeling - lange  termijn";
    else
      $this->pdf->rapport_titel = "Vermogensverdeling - lange termijn";
    //$this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date('d-m-Y',db2jul($start))." t/m ".date('d-m-Y',$this->pdf->rapport_datum);
    $this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date("j",db2jul($start))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->rapport_taal)." ".date("Y",db2jul($start))." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
    
    $langeTermijnData=$this->addGrafieken('kwartaal',$start);
  
    $start=$this->getGrafiekStart('maanden');
    if(isset($this->extraVerdeling) && $this->extraVerdeling=='AttributieCategorie')
    {
      $risicoScore=array();
      foreach($langeTermijnData['Index'] as $datum=>$categorieData)
      {
        foreach($categorieData as $categorie=>$percentage)
        {
          $parts=explode('-',$categorie);
          if(count($parts)==2)
            $score=intval($parts[0]);
          else
            $score=0;
          $risicoScore[$datum]+=$score*$percentage/100;
        }
      }
      $this->pdf->rapport_titel = "Risicoverdeling - kortere  termijn";
      //$this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date('d-m-Y',db2jul($start))." t/m ".date('d-m-Y',$this->pdf->rapport_datum);
      $this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date("j",db2jul($start))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->rapport_taal)." ".date("Y",db2jul($start))." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    }
    else
    {
      $this->pdf->rapport_titel = "Vermogensverdeling - kortere  termijn";
      //$this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date('d-m-Y',db2jul($start))." t/m ".date('d-m-Y',$this->pdf->rapport_datum);
      $this->pdf->subtitel=vertaalTekst("Verslagperiode",$this->pdf->rapport_taal)." ".date("j",db2jul($start))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($start))],$this->pdf->rapport_taal)." ".date("Y",db2jul($start))." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    }
    $this->pdf->addPage();
    $this->addGrafieken('maanden',$start);
  
    $this->pdf->subtitel='';
    if(isset($risicoScore) && count($risicoScore)>0)
    {
      $this->addRisico($risicoScore);
    }
  }
  
  function addRisico($risicoScore)
  {
    $this->pdf->rapport_titel = "Risicoverdeling";
    $this->pdf->addPage();
    $pageX=$this->pdf->getX();
    $pageY=$this->pdf->getY();
    $this->pdf->setY($pageY+10);
    $this->pdf->setWidths(array(60));
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Legenda",$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach (array_reverse($this->categorieVolgorde) as $categorie)
    {
      if(count(explode('-',$categorie))>1)
        $this->pdf->row(array($this->categorieOmschrijving[$categorie]));
    }
    //$this->pdf->SetFont($this->pdf->rapport_font, 'I', $this->pdf->rapport_fontsize);
    //$this->pdf->ln();
    //$this->pdf->row(array('Deze wegingen komen overeen met de courante risicowegingen in fondsenfiches'));
    $this->pdf->setXY(80,$pageY+10);
    $this->LineDiagram(200, 100, $risicoScore);
  }
  
  function getGrafiekStart($perioden)
  {
    $start=$this->pdf->PortefeuilleStartdatum;
    //  else
    //    $start=$this->rapportageDatumVanaf;
    if($perioden=='maanden')
    {
      $jul = db2jul($this->rapportageDatum);
      $maand = date('m', $jul);
      $jaar = date('Y', $jul);
      $driejaarStart = mktime(0, 0, 0, $maand, 0, $jaar - 2);
      if (db2jul($start) < $driejaarStart)
      {
        $start = date('Y-m-d', $driejaarStart);
      }
    }
    return $start;
  }
  
  function addGrafieken($perioden,$start)
  {
    $index=new indexHerberekening();
    if(isset($this->extraVerdeling))
    {
      $index->extraVerdeling = $this->extraVerdeling;
    }
    $index->voorStartdatumNegeren=true;
    //if($perioden=='kwartaal')

    
    $indexData = $index->getWaarden($start,$this->rapportageDatum ,$this->portefeuille,'',$perioden);
 // listarray($indexData);
  
    $this->jaarWaarden=array();
    $barData=array();
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
 //  listarray($data['extra']);
 //  echo $data['datum']." ".array_sum($data['extra']['cat']).' '.array_sum($data['extra']['AttributieCategorie'])."<br>\n";
        $barGraph['Index'][$data['datum']]['leeg']=0;
        if(isset($this->extraVerdeling))
          $categorieen=$data['extra'][$this->extraVerdeling];
        else
          $categorieen=$data['extra']['cat'];
        
        foreach ($categorieen as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
  
          if($categorie=='VAR')
            $categorie='OBL';
          
          if(isset($barGraph['Index'][$data['datum']][$categorie]))
            $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
          else
            $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
        
          if($waarde <> 0)
          {
            $categorien[$categorie] = $categorie;
            if(!isset($this->categorieVolgorde[$categorie]))
            {
              $this->categorieVolgorde[$categorie] = $categorie;
              $this->categorieOmschrijving[$categorie] = $categorie;
            }
          }
        
          $barData[$data['datum']][$categorie] += ($waarde / $data['waardeHuidige'] * 100);
          
        }
        $this->jaarWaarden['Totaal'][$data['datum']]['waarde']+=$data['waardeHuidige'];
      }
    }
  /*
    if(isset($this->extraVerdeling) && $this->extraVerdeling=='AttributieCategorie')
    {
      ksort($this->categorieVolgorde);
    }
  */
    if($perioden=='maanden')
    {
      $titel=vertaalTekst('Ontwikkeling vermogen',$this->pdf->rapport_taal);
    }
    else
    {
      $titel=vertaalTekst('Ontwikkeling vermogen sinds start',$this->pdf->rapport_taal);
    }

    $this->VBarDiagramTop(20,100,220,55,$barData,$titel);
    
   // listarray($barData);
		  if (count($barGraph) > 0)
		  {
		      $this->pdf->SetXY(20,185)		;//112
		      $this->VBarDiagram(220, 55, $barGraph['Index'],vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal));
		  }


	   $this->pdf->fillCell = array();


     return $barGraph;
	}
  
  function VBarDiagramTop($x,$y,$w, $h, $data,$title='')
  {
    global $__appvar;
    $legendaWidth = 0;
    $this->pdf->setXY($x,$y-$h);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->Multicell($w,4,$title,'','L');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $grafiekPunt = array();
    $verwijder=array();
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0,'width'=>0.01));
    
    $aanwezigeCategorieen=array();
    foreach ($data as $datum=>$waarden)
    {
      foreach ($waarden as $categorie => $waarde)
      {
        $aanwezigeCategorieen[$categorie]=$categorie;
      }
    }
    $beginWaarden=array();
    foreach($this->categorieVolgorde as $categorie)
      if(in_array($categorie,$aanwezigeCategorieen))
        $beginWaarden[$categorie]=0;
  
    $grafiekCategorie=array();
    $grafiek=array();
    foreach ($data as $datum=>$waarden)
    {
      if(!isset($grafiek[$datum]))
        $grafiek[$datum]=$beginWaarden;
      
      $legenda[$datum] = date('d-m-Y',db2jul($datum));
      $n=0;
  
      foreach (($this->categorieVolgorde) as $categorie)//array_reverse
      {
        //foreach ($waarden as $categorie=>$waarde)
        //{
        if(!in_array($categorie,$aanwezigeCategorieen))
          continue;
        $waarde=$waarden[$categorie];
     // foreach ($waarden as $categorie=>$waarde)
    //  {
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';
        $grafiek[$datum][$categorie]+=$waarde;
        $grafiekCategorie[$categorie][$datum]+=$waarde;
        $categorien[$categorie] = $n;
        $categorieId[$n]=$categorie ;
        
        if($waarde < 0)
        {
        //  $verwijder[$datum]=$datum;
          $grafiek[$datum][$categorie]=0;
          $grafiekCategorie[$categorie][$datum]=0;
        }
        
        if(!isset($colors[$categorie]))
        {
          if($this->categorieKleuren[$categorie])
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          else
            $colors[$categorie]=array(rand(20,80),rand(20,80),rand(20,250));//array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
        }
        $n++;
      }
    }
    
    
    foreach ($verwijder as $datum)
    {
      foreach ($data[$datum] as $categorie=>$waarde)
      {
        if($categorie=='LIQ')
          $categorie='Liquiditeiten';
        $grafiek[$datum][$categorie]=0;
        $grafiekCategorie[$categorie][$datum]=0;
      }
    }
    
    $numBars = count($grafiek);

    $maxVal=100;
    foreach ($this->jaarWaarden['Totaal'] as $jaar=>$waarden)
      $maxVal=max($maxVal,$waarden['waarde']);
    
    $maxVal=round(ceil($maxVal/5000000))*5000000;
    $minVal = 0;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda
    
    
    $this->pdf->line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek+$bGrafiek, $YstartGrafiek);
    $this->pdf->line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek, $YstartGrafiek-$hGrafiek);
    
    
    $n=0;
    foreach (array_reverse($this->categorieVolgorde) as $categorie)
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
        $n++;
      }
    }
    
    $unit = $hGrafiek / $maxVal * -1;
    $nulYpos =0;
    
    $horDiv = 5;
    $bereik = $hGrafiek/$unit;
    
    
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = (abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    
    $n=0;
    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->TextWithRotation($XstartGrafiek-8,$YPage-$hGrafiek/2,vertaalTekst('(x 1 miljoen)',$this->pdf->rapport_taal),90);
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek+$bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      //$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek-1 ,$i,array('color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte/1000000,0)."",0,0,'R');
      $n++;
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / ($numBars + 1));
    $bGrafiek = $vBar * ($numBars + 1);
    $eBaton = ($vBar * 50 / 100);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
  //  $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $div=1;
    if(count($grafiek) > 24)
      $div=floor(count($grafiek)/24);
    
    $legendaPrinted=array();
    foreach ($grafiek as $datum=>$data)
    {
      //$data=array_reverse($data,true);
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
        //Bar
        $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
        $hval = ($val * $unit * $this->jaarWaarden['Totaal'][$datum]['waarde']/100);
        //echo  "$datum  ($val * $unit * ".$this->jaarWaarden['Totaal'][$datum]['waarde']."/100); <br>\n";
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
        $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
        /*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,0,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        */
        if($legendaPrinted[$datum] != 1 && $i%$div==0)
          $this->pdf->TextWithRotation($xval-1,$YstartGrafiek+8,$legenda[$datum],30);
         // $this->pdf->TextWithRotation($xval-1,$YstartGrafiek+7,'Q'.ceil(substr($legenda[$datum],5,2)/3).'-'.substr($legenda[$datum],0,4),30);
        
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
    /*
    $xval=$x+10;
    $yval=$y+15;
    $colors=array_reverse($colors,true);
    foreach ($colors as $cat=>$color)
    {
      $this->pdf->Rect($xval, $yval, 5, 5, 'DF',null,$colors[$cat]);
      $this->pdf->TextWithRotation($xval+7,$yval+2.5,$cat,0);
      $xval=$xval+22;
    }
    */
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
  
  function LineDiagram($w, $h, $data)
  {
    global $__appvar;
    $maxVal=7;
    $minVal=0;
    $horDiv=7;
    $verDiv=4;
    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data2 = $data['extra'];
   // $data = $data['portefeuille'];
    
  
    $bereikdata=array();
    $bereikdata[]=0;
    $bereikdata[]=max($data);
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
    
    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(255,255,255));

    $color=array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $verInterval = ($lDiag / $verDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);
    
  
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    $yAs='';
    for($i=$nulpunt; round($i)<=round($bodem); $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) .$yAs);
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= round($top); $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 .$yAs);
      
      $n++;
      if($n >20)
        break;
    }
    

    
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=floor(count($data)/24);
    $i=0;
    foreach($data as $datum=>$waarde)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,date('d-m-Y',db2jul($datum)),25);
      $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
      
      if ($i>0)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }
      
      $yval = $yval2;
      $i++;
    }
    

    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
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



  function VBarDiagram($w, $h, $data,$title)
  {
      global $__appvar;
      $grafiekPunt = array();
 
    $aanwezigeCategorieen=array();
    foreach ($data as $datum=>$waarden)
    {
      foreach ($waarden as $categorie => $waarde)
      {
        $aanwezigeCategorieen[$categorie]=$categorie;
      }
    }

      
    
      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach (($this->categorieVolgorde) as $categorie)//array_reverse
        {
        //foreach ($waarden as $categorie=>$waarde)
        //{
          if(!in_array($categorie,$aanwezigeCategorieen))
            continue;
            
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarden[$categorie];
          $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarden[$categorie]));
          $minVal=min(array($minVal,$waarden[$categorie]));

          if($waarden[$categorie] < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }
  
 
    $numBars = count($legenda);


      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;

      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w);//- $margin * 1) - ($w/12)*2; // - legenda
  
  
    $this->pdf->setXY($XPage,$YPage-$hGrafiek-8);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->Multicell($w,4,$title,'','L');
    $this->pdf->setXY($XPage,$YPage);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
          $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
          $n++;
        }
      }
  
   // $this->pdf->line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek+$bGrafiek, $YstartGrafiek);
    $this->pdf->line($XstartGrafiek, $YstartGrafiek, $XstartGrafiek, $YstartGrafiek-$hGrafiek);
  
  
  
  
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

      $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }
  
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);

    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($numBars+1));
     //   $bGrafiek = $vBar * ($this->pdf->NbVal);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $i=0;
  
  
    $div=1;
    if(count($grafiek) > 24)
      $div=floor(count($grafiek)/24);
    $legendaPrinted=array();
    

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
          /*
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
          */
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1 && $i%$div==0)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+8,$legenda[$datum],30);
  
        

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
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
          /*
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
          */
         $this->pdf->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
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