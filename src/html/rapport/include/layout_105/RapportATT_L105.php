<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_105/ATTberekening_L105.php");

class RapportATT_L105
{
	function RapportATT_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performance en verloop asset allocatie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
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
  
  
  function getZorgplichtCategorien()
  {
    $this->zorgplichtCategorien=array();
    $db=new DB();
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $this->zorgplichtCategorien[$data['Zorgplicht']]=$data['Omschrijving'];
    }
    return $this->zorgplichtCategorien;
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


	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');


    
    $query="SELECT Minimum,Maximum FROM ZorgplichtPerRisicoklasse WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Risicoklasse ='".$this->pdf->portefeuilledata['Risicoklasse']."' AND Zorgplicht IN('Aandelen','ZAK') ORDER BY Risicoklasse desc";
    $DB->SQL($query);
    $DB->Query();
    $risico = $DB->LookupRecord();

//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type .'Paginas']=$this->pdf->rapport_titel;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    
    
    $this->getZorgplichtCategorien();
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
      $indexData = $index->getWaarden($this->rapportageDatumVanaf, $this->rapportageDatum, $this->portefeuille);
    }
    else
    {
      $this->att = new ATTberekening_L105($this);
      $indexData = $this->att->getPerf($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['RapportageValuta'], true);
    }
    
   


    $query="SELECT
CategorienPerHoofdcategorie.Vermogensbeheerder,
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
Beleggingscategorien.Omschrijving,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingscategorien.Afdrukvolgorde
FROM
CategorienPerHoofdcategorie
INNER JOIN KeuzePerVermogensbeheerder ON CategorienPerHoofdcategorie.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='$beheerder' AND CategorienPerHoofdcategorie.Vermogensbeheerder='$beheerder'
ORDER BY
Beleggingscategorien.Afdrukvolgorde,KeuzePerVermogensbeheerder.Afdrukvolgorde
 ";
    
    $DB->SQL($query);
    $DB->Query();
    $conversie=array('LIQ'=>'H-Liq');
    $legeCategorieen=array();
    while($data=$DB->NextRecord())
    {
      $legeCategorieen[$data['Hoofdcategorie']]=0;
      $conversie[$data['Beleggingscategorie']]=$data['Hoofdcategorie'];
  
      $this->categorieVolgorde[$data['Hoofdcategorie']]=$data['Hoofdcategorie'];
      $this->categorieOmschrijving[$data['Hoofdcategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
    }

$i=0;
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
  
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      //echo "$categorie || ".$conversie[$categorie]." <br>\n";
      if(isset($conversie[$categorie]))
        $categorie=$conversie[$categorie];
    
      if(!isset($barGraph['Index'][$data['datum']]))
        $barGraph['Index'][$data['datum']]=$legeCategorieen;
    
      if($categorie=='LIQ')
        $categorie='Liquiditeiten';
    
      if(isset($barGraph['Index'][$data['datum']][$categorie]))
        $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
      else
        $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
    
      if($waarde <> 0)
        $categorien[$categorie]=$categorie;
    
    }
    
  }
}


$grafiekData['Datum'][]="$RapStartJaar-12-01";
   
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
       //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(137,188,255);
         $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);
          
        //$factor=2;
        //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*$factor,$this->pdf->rapport_kop_bgcolor['g']*$factor,$this->pdf->rapport_kop_bgcolor['b']*$factor);


        $totaalRendament=100;
        $totaalRendamentIndex=100;
        $totaal=array();
        $perioden=array('jaar','kwartaal');
        $fill=true;
        $qPerf=0;  
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
          $kwartaal = ceil(date("n",$datum)/3);

          if(isset($lastKwartaal) && $lastKwartaal!=$kwartaal)
          {
          //   $this->printTotaal($totaal,$lastKwartaal);
    
            $totaal['kwartaal']=array();
            $qPerf=0; 
         //   $fill=true;

          }
          
		      if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }

         
          
          $this->pdf->CellBorders = array();
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['kosten'],2),
		                           $this->formatGetal($row['rente'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($row['performance'],2),
		                           $this->formatGetal($row['index']-100,2)));
                               
                             
           foreach($perioden as $periode)
           {
             
		                           if(!isset($totaal[$periode]['waardeBegin']))
		                             $totaal[$periode]['waardeBegin']=$row['waardeBegin'];
		                           $totaal[$periode]['Waarde'] = $row['waardeHuidige'];
		                           $totaal[$periode]['Resultaat'] += $row['resultaatVerslagperiode'];
		                           $totaal[$periode]['Gerealiseerd'] += $row['gerealiseerd'];
		                           $totaal[$periode]['Ongerealiseerd'] += $row['ongerealiseerd'];
		                           $totaal[$periode]['Opbrengsten'] += $row['opbrengsten'];
		                           $totaal[$periode]['Kosten'] += $row['kosten'];
		                           $totaal[$periode]['Rente'] += $row['rente'];
		                           $totaal[$periode]['StortingenOntrekkingen'] += $row['stortingen']-$row['onttrekkingen'];
                               if($periode=='kwartaal')
                               {
                                 $qPerf=((1+$qPerf)*(1+$row['performance']/100))-1;
                                 $totaal[$periode]['Rendament'] = $qPerf;
                               }                               
                               else
		                             $totaal[$periode]['Rendament'] = $row['index'];
           }
		    $n++;
        $i++;
          $lastKwartaal=$kwartaal;
		    }
    
		    $this->pdf->fillCell=array();
       // $this->printTotaal($totaal,$lastKwartaal);

            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


     //   $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['jaar']['waardeBegin'],2),
		                           $this->formatGetal($totaal['jaar']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['jaar']['Gerealiseerd']+$totaal['jaar']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['jaar']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['jaar']['Kosten'],2),
		                           $this->formatGetal($totaal['jaar']['Rente'],2),
		                           $this->formatGetal($totaal['jaar']['Resultaat'],2),
		                           $this->formatGetal($totaal['jaar']['Waarde'],2),
		                           '',
		                           $this->formatGetal($totaal['jaar']['Rendament']-100,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }
    
    if (count($barGraph) > 0)
    {
      $this->pdf->SetXY($this->pdf->marge,117)		;//112
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
      $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
      $this->pdf->SetXY(15,185)		;//112
      $this->VBarDiagram(100, 60, $barGraph['Index']);
    }
    /*
     if (count($dagVerdeling) > 0)
     {
       $this->pdf->SetXY($this->pdf->marge,117)		;//112
       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
       $this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
       $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
       $this->pdf->SetXY(15,185)		;//112
       $this->LineDiagram(100, 60, $dagVerdeling,$risico);
     }
    */
    $gebruikteCategorien=$this->addZorgBar();
    $this->plotZorgBar4(65,10,$gebruikteCategorien);
    
     $this->pdf->fillCell = array();
	}
  
  function VBarDiagram($w, $h, $data)
  {
    global $__appvar;
    $legendaWidth = 00;
    $grafiekPunt = array();
    $verwijder=array();
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    
    foreach ($data as $datum=>$waarden)
    {
      $julDate=db2jul($datum);
      
      $legenda[$datum] =  vertaalTekst($maanden[date("n", $julDate)], $this->pdf->rapport_taal).date("-y",$julDate);
      $n=0;
      $minVal=0;
      $maxVal=100;
      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        //foreach ($waarden as $categorie=>$waarde)
        //{
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
    $numBars=10;
    
    if($color == null)
    {
      $color=array(155,155,155);
    }
    
    
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
    $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda
    
    $n=0;
    foreach (($this->categorieVolgorde) as $categorie)//array_reverse
    {
      if(is_array($grafiekCategorie[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
        $n++;
      }
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
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
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
        // $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3)
        {
          $colSum=array_sum($colors[$categorie]);
          if($colSum<283)
            $this->pdf->SetTextColor(255,255,255);
          else
            $this->pdf->SetTextColor(0,0,0);
          
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
    //      $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        
        if($legendaPrinted[$datum] != 1)
        {
          $this->pdf->SetXY($xval, $YstartGrafiek);
          $this->pdf->Cell($eBaton, 4, $legenda[$datum],0,0,'C');
          //$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
        }
        
        
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
        if(abs($hval) > 3)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
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
  
  function addZorgBar()
  {
    global $__appvar;
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
    $pdata=$this->pdf->portefeuilledata;
    $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum); //listarray($zpwaarde);
    $categorien=array();
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
      $data['Norm']=($data['Maximum']-$data['Minimum'])/2;
      $categorien[$categorie]=$data;
      if(!isset($data['fondsGekoppeld']))
      {
        $gebruikteCategorie=$data;
      }
    }
    // listarray($zpwaarde);
    /*
        foreach($zpwaarde['conclusie'] as $data)
        {
          if($data[0]==$gebruikteCategorie['Zorgplicht'])
          {
            $gebruikteCategorie['percentage']=$data[2];
          }
          $gebruikteCategorie['categorien'][$data[0]]=$data[2];
        }
    */
    foreach($zpwaarde['conclusie'] as $data)
    {
      foreach($categorien as $categorie=>$categorieData)
      {
        if($data[0]==$categorie)
        {
          $categorien[$categorie]['percentage']=$data[2];
        }
        // $categorien[$categorie]['categorien'][$data[0]]=$data[2];
      }
    }
//listarray($categorien);
    return $categorien;
    // return $gebruikteCategorie;
  }
  
  
  function plotZorgBar4($width,$height,$categorieData)
  {
    
    $yBegin=122;//$this->hoogteBeleggingsresultaat;
    $xBegin=195;
    $volgorde=array('ZAK','ALT','VAR');
    foreach($categorieData as $categorie=>$catData)
    {
      if (!in_array($categorie, $volgorde))
      {
        $volgorde[] = $categorie;
      }
      if(!isset($this->zorgplichtCategorien[$categorie]))
        $this->zorgplichtCategorien[$categorie]=$categorie;
    }
    $newData=array();
    
    foreach($volgorde as $categorie)
    {
      if(isset($categorieData[$categorie]))
        $newData[$categorie]=$categorieData[$categorie];
    }
//listarray($newData);
    $this->pdf->setXY($xBegin,$yBegin-5);//105 93
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($width,5,'Bandbreedtecontrole: '.$this->pdf->portefeuilledata['Risicoklasse'],0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    
    foreach($newData as $categorie=>$data)
    {
      $data['percentage']=str_replace(',','.',$data['percentage']);
      
      //echo $yBegin." ";//exit;
      $this->pdf->setXY($xBegin-$width,$yBegin+$height);//105 93
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Cell($width,5,strtolower($this->zorgplichtCategorien[$data['Zorgplicht']]),0,0,'R');
      
      //$this->pdf->Rect($xBegin-1,$yBegin+5,$width+2,$height);
      
      
      $this->pdf->setXY($xBegin+5,$yBegin+$height);
      
      $marge=1;
      $xPage=$this->pdf->getX();
      $yPage=$this->pdf->getY();
      
      $steps=200;
      $debug=0;
      $barWith=$width-10;
      $barHeight=5;
      $barStep=$barWith/$steps;
      $barYbegin=$yBegin+$height/2+$barHeight/2+2;
      
      
      for($i=0;$i<=$steps;$i++)
      {
        
        $percentage=$i/$steps*100;
        
        $rood=array(200,0,0);
        $groen=array(0,100,0);
        $geel=array(180,180,0);
        $marge=10;
        
        for($j=0;$j<$marge;$j++)
        {
          $factor=$j/$marge;
          $roodGeelOpbouw[$j]=array($rood[0]+($geel[0]-$rood[0])*$factor,$rood[1]+($geel[1]-$rood[1])*$factor,$rood[2]+($geel[2]-$rood[2])*$factor);
          $groenGeelOpbouw[$j]=array($groen[0]+($geel[0]-$groen[0])*$factor,$groen[1]+($geel[1]-$groen[1])*$factor,$groen[2]+($geel[2]-$groen[2])*$factor);
        }
        
        if($i>0)
        {
          
          if($percentage<=$data['Minimum'])//-$marge)
          {
            $fill_color=$rood;
            if($debug==1){echo "$percentage 1 rood <br>\n";}
          }
          /*
          elseif($percentage<=$data['Minimum']+$marge)
          {
            $fill_color=$geel;
            if($percentage<=$data['Minimum'])//geelopbouw;
            {
              $j=$percentage-$data['Minimum']+$marge-1;
              $fill_color=$roodGeelOpbouw[$j];
              if($debug==1){echo "$percentage 2 rood->geel $j<br>\n";}
            }
            elseif($percentage<=$data['Minimum']+$marge)
            {
              $j=$marge-($percentage-$data['Minimum']);
              $fill_color=$groenGeelOpbouw[$j];
              if($debug==1){echo "$percentage 3 geel->groen $j <br>\n";}
              // echo "$percentage $j <br>\n";
            }
          }
          */
          elseif($percentage<=$data['Maximum'])//-$marge)
          {
            $fill_color=$groen;//array(0,100,0);
            if($debug==1){echo "$percentage 4 groen <br>\n";}
            
          }
          /*
          elseif($percentage<=$data['Maximum']+$marge)
          {
            if($percentage<=$data['Maximum'])
            {
              $j = $marge - (($data['Maximum']) - $percentage)-1;
              
              $fill_color = $groenGeelOpbouw[$j];
              if($debug==1){echo "$percentage 5 groen->geel $j<br>\n";}
            }
            elseif($percentage<=$data['Maximum']+$marge)//rood opbouw;
            {
              $j=$marge-($percentage-$data['Maximum']);
              //  echo "$percentage<".$data['Maximum']." $j<br>\n";
              $fill_color=$roodGeelOpbouw[$j];
              if($debug==1){echo "$percentage 6 geel->rood $j<br>\n";}
            }
            else
            {
              $fill_color = $geel;//array(200,200,0);
              if($debug==1){echo "$percentage 7 geel <br>\n";}
            }
            
          }
          */
          else
          {
            
            $fill_color=$rood;
            if($debug==1){echo "$percentage 8 rood <br>\n";}
          }
          
          $this->pdf->SetFillColor($fill_color[0],$fill_color[1],$fill_color[2]);
          $this->pdf->rect($xBegin+5+$i*$barStep,$barYbegin,$barStep,$barHeight,'F');
          
          
        }
        
      }
      
      $this->pdf->Rect($xBegin+5,$barYbegin,$barWith,5);
      

      //  exit;
      $pstep=$data['percentage']/$steps*100;

      $percentages=array(0=>array('align'=>'L','yOffset'=>-3,'width'=>10),
                         100=>array('align'=>'R','yOffset'=>-3,'width'=>0.1),
                         $data['percentage']=>array('align'=>'L','yOffset'=>9,'xOffset'=>-3,'width'=>10,'extraText'=>' : actueel','line'=>array(-1,7)));
      $this->pdf->SetLineWidth(0.5);
      foreach($percentages as $percentage=>$options)
      {
        $pstep=$percentage/100*$steps;
        $this->pdf->setXY($xBegin+5+$pstep*$barStep+$options['xOffset'],$barYbegin+$options['yOffset']);
        $this->pdf->Cell($options['width'],1,round($percentage).'% '.$options['extraText'],0,0,$options['align']);
        if(isset($options['line']))
          $this->pdf->line($xBegin+5+$pstep*$barStep,$barYbegin+$options['line'][0],$xBegin+5+$pstep*$barStep,$barYbegin+$options['line'][1]);
      }
      $this->pdf->SetLineWidth(0.2);
      
      if($data['Minimum']<>0 && $data['Maximum'])
        $percentages=array($data['Minimum']=>array('align'=>'C','yOffset'=>-6,'width'=>0.1,'xOffset'=>1),//,'line'=>array(-4,0,-9,-11)),
                           $data['Maximum']=>array('align'=>'C','yOffset'=>-6,'width'=>0.1,'xOffset'=>1));//,'line'=>array(-4,0,-9,-11)));
      else
        $percentages=array();
      foreach($percentages as $percentage=>$options)
      {
        $pstep=$percentage/100*$steps;
        $this->pdf->setXY($xBegin+5+$pstep*$barStep+$options['xOffset'],$barYbegin+$options['yOffset']);
        $this->pdf->Cell($options['width'],1,round($percentage).'% '.$options['extraText'],0,0,$options['align']);
        if(isset($options['line']))
        {
          $this->pdf->line($xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][0], $xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][1]);
          $this->pdf->line($xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][2], $xBegin + 5 + $pstep * $barStep, $barYbegin + $options['line'][3]);
        }
      }
      /*
      $pstep=($data['Minimum'] + $data['Maximum'])/200*$steps;
      $this->pdf->setXY($xBegin+5+$pstep*$barStep,$barYbegin-15);
      $this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(0.1,1,'mandaat',0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->setDash(1,1);
      $this->pdf->line($xBegin + 5 + ($data['Minimum']/100*$steps) * $barStep, $barYbegin-12, $xBegin + 5 + ($data['Maximum']/100*$steps) * $barStep, $barYbegin-12);
      $this->pdf->setDash(0);
      */
      $yBegin+=20;
    }
    if($debug==1)
      exit;
    
  }
	
	function LineDiagram($w,$h,$data,$risico)
  {
    $minVal=0;
    $maxVal=0;
    $categorieen=array();
    $lijnData=array();
    $colors=array();
    $aantalWaarden=count($data);
    foreach($data as $datum=>$verdeling)
    {
        foreach($verdeling as $cat=>$waarde)
        {
          if ($waarde > $maxVal)
          {
            $maxVal = $waarde;
          }
          if (!isset($categorieen[$cat]))
          {
            $categorieen[$cat] = $cat;
          }
  
          if (!isset($colors[$cat]))
          {
            $colors[$cat] = array($this->categorieKleuren[$cat]['R']['value'], $this->categorieKleuren[$cat]['G']['value'], $this->categorieKleuren[$cat]['B']['value']);
          }
          $lijnData[$cat][$datum]=$waarde;
        }
    }
    
    foreach($risico as $key=>$percentage)
    {
      if ($percentage > $maxVal)
      {
        $maxVal = $percentage;
      }
    }
  
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $YstartGrafiek = $YPage;
    $hGrafiek = $h;
    $XstartGrafiek = $XPage;
    $waardeCorrectie = $hGrafiek / ($maxVal - $minVal);
    
    $n=0;
    foreach (($this->categorieVolgorde) as $categorie)//array_reverse
    {
      if(isset($categorieen[$categorie]))
      {
        $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
        $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
        $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
        $n++;
      }
    }
  
    $colors['Maximum']=array(200,100,100);
    $colors['Minimum']=array(100,200,100);
    $n++;
    foreach($risico as $key=>$percentage)
    {
     // $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'F',null,$colors[$key]);
  
      $this->pdf->Line($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+3.5, $XstartGrafiek+$w+5 , $YstartGrafiek-$hGrafiek+$n*7+3.5,array('width' => 0.5,'dash' => 2,'color'=>$colors[$key]));
      
      $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
      $this->pdf->MultiCell(45, 4,"Zakelijkewaarden ".strtolower($key),0,'L');
  
      $yvalPos = $YstartGrafiek - ($percentage * $waardeCorrectie) ;
      $this->pdf->Line($XstartGrafiek, $yvalPos, $XstartGrafiek + $w ,$yvalPos,array('width' => 0.5,'dash' => 2,'color'=>$colors[$key]));
      $n++;
    }
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.1);
    
    $xStep=$w/$aantalWaarden;
    
    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal ;
      $nulYpos =0;
    }
  
  
    $horDiv = 5;
    $bereik = $hGrafiek/$unit;
  
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
  
    $stapgrootte = $maxVal/$horDiv;//ceil(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
  
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
  
    $skipNull=false;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1,0)." %",0,0,'R');
      $n++;
      if($n >20)
        break;
    }
  
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
      {
        $skipNull = false;
      }
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,0)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
  
    $maanden=array('null','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    $mod=ceil($aantalWaarden/12);
   // echo "$aantalWaarden $mod";exit;
    $datumPrinted=array();
    foreach($lijnData as $categorie=>$data)
    {
      $color=$colors[$categorie];
      $lineStyle = array('width' => 0.75, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      $i=0;
      unset($yval);
      foreach($data as $datum=>$waarde)
      {
        $yval2 = $bodem - ($waarde * $waardeCorrectie) ;
        if(isset($yval))
          $this->pdf->line($XstartGrafiek+$i*$xStep, $yval, $XstartGrafiek+($i+1)*$xStep, $yval2,$lineStyle);
        $yval = $yval2;
        
        if(!isset($datumPrinted[$datum]))
        {
          if($i == 0 || $i == $aantalWaarden - 1 || $i%$mod==0)
          {
            $julDatum=db2jul($datum);
            $this->pdf->TextWithRotation($XstartGrafiek + ($i ) * $xStep, $YstartGrafiek +10,date("d", $julDatum).'-'. vertaalTekst($maanden[date("n", $julDatum)], $this->pdf->rapport_taal) . '-' . date("y", $julDatum), 25);
          }
          $datumPrinted[$datum]=1;
        }
        $i++;
      }
    }
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.1);
  }

function printTotaal($totaal,$kwartaal)
{//

      //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
      $this->pdf->fillCell=array();
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','TS',''); 
		    $this->pdf->row(array(vertaalTekst('Totaal Q'.$kwartaal,$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['kwartaal']['waardeBegin'],2),
		                           $this->formatGetal($totaal['kwartaal']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['kwartaal']['Gerealiseerd']+$totaal['kwartaal']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['kwartaal']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['kwartaal']['Kosten'],2),
		                           $this->formatGetal($totaal['kwartaal']['Rente'],2),
		                           $this->formatGetal($totaal['kwartaal']['Resultaat'],2),
		                           $this->formatGetal($totaal['kwartaal']['Waarde'],2),
		                           $this->formatGetal($totaal['kwartaal']['Rendament']*100,2),
                               ''
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->ln(1);
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



}
?>