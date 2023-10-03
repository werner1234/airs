<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_105/ATTberekening_L105.php");

class RapportOIB_L105
{
	function RapportOIB_L105($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Verloop asset allocatie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";


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
    
    
    
    $index = new indexHerberekening();

    
    $dagen=$index->getDagen2(db2jul($this->rapportageDatumVanaf) ,db2jul($this->rapportageDatum));
    $dagVerdeling=array();
    
    
  $verdelingCategorieen=array();
  $verdelingTmp=array();
  foreach($dagen as $periode)
  {
    foreach($periode as $datum)
    {
      if(!isset($verdelingTmp[$datum]))
      {
        $regels=berekenPortefeuilleWaarde($this->portefeuille, $datum, (substr($datum,5,5)=='01-01'?true:false), $this->pdf->portefeuilledata['RapportageValuta'], $datum);
        foreach($regels as $regel)
        {
          $verdelingTmp[$datum][$regel['hoofdcategorie']]+=$regel['actuelePortefeuilleWaardeEuro'];
          $verdelingCategorieen[$regel['hoofdcategorie']]=0;
        }
      }
    }
  }
  
  foreach($verdelingTmp as $dag=>$verdeling)
  {
    $totaleWaarde=array_sum($verdeling);
    $verdelindProcent=$verdelingCategorieen;
    foreach($verdeling as $cat=>$eur)
    {
      $verdelindProcent[$cat]=$eur/$totaleWaarde*100;
    }
    $dagVerdeling[$dag]=$verdelindProcent;
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



     if (count($dagVerdeling) > 0)
     {
       $this->pdf->SetXY($this->pdf->marge,32)		;//112
       $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
       $this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
       $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
       $this->pdf->SetXY(15,185)		;//112
       $this->LineDiagram(220, 145, $dagVerdeling,$risico);
     }
     $this->pdf->fillCell = array();
	}
	
	function LineDiagram($w,$h,$data,$risico)
  {
    $minVal=0;
    $maxVal=100;
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
  
  
    $horDiv = 10;
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
    $mod=ceil($aantalWaarden/30);
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