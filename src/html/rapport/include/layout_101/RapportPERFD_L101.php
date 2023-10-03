<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/09 16:39:39 $
File Versie					: $Revision: 1.10 $

$Log: RapportPERFD_L101.php,v $
Revision 1.10  2019/11/09 16:39:39  rvv
*** empty log message ***

Revision 1.9  2019/11/06 16:11:20  rvv
*** empty log message ***

Revision 1.8  2017/10/25 15:59:31  rvv
*** empty log message ***

Revision 1.7  2017/07/19 19:30:24  rvv
*** empty log message ***

Revision 1.6  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.5  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.4  2017/06/10 18:09:58  rvv
*** empty log message ***

Revision 1.3  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.2  2017/05/17 15:57:50  rvv
*** empty log message ***

Revision 1.1  2017/05/13 16:27:35  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.8  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.7  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.6  2012/10/21 12:44:08  rvv
*** empty log message ***

Revision 1.5  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.4  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.2  2012/09/13 15:58:37  rvv
*** empty log message ***

Revision 1.5  2012/08/11 13:17:53  rvv
*** empty log message ***

Revision 1.4  2012/07/11 11:33:23  rvv
*** empty log message ***

Revision 1.3  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.2  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportPERFD_L101
{
	function RapportPERFD_L101($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Resultaat vanaf start";

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

		$this->rapportageDatumVanaf  =substr($this->pdf->PortefeuilleStartdatum,0,10);
	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    $q="SELECT
CategorienPerHoofdcategorie.Vermogensbeheerder,
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
Beleggingscategorien.Omschrijving
FROM
CategorienPerHoofdcategorie
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='$beheerder' ORDER BY Beleggingscategorien.AfdrukVolgorde";
    $DB->SQL($q);
    $DB->Query();
    $hoofdcategorieKoppeling=array();
    while($data=$DB->NextRecord())
    {
      $hoofdcategorieKoppeling[$data['Beleggingscategorie']]=$data['Hoofdcategorie'];
      $hoofdcategorieOmschrijving[$data['Hoofdcategorie']]=$data['Omschrijving'];
  
      $this->categorieVolgorde[$data['Hoofdcategorie']]=$data['Hoofdcategorie'];
      $this->categorieOmschrijving[$data['Hoofdcategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
    }

//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
		$this->pdf->templateVars['PERFDPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['PERFDPaginas']=$this->pdf->rapport_titel;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);//,'','jaar');


    $i=0;
    $laatsteDatum='leeg';
		$rendamentWaarden=array();
		$jaarSom=array();
		$somVelden=array('stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','ongerealiseerd','rente','gerealiseerd');
		$laatsteVelden=array('index','specifiekeIndex','valuta','waardeHuidige','extra','datum','specifiekeIndexPerformance');
		//$indexPerf=0;
foreach ($indexData as $index=>$data)
{
	$laatste=($data['datum']==$this->rapportageDatum?true:false);
	$maandDag=substr($data['datum'],5,5);
	$eindJaar=($maandDag=='12-31'?true:false);
	$eindKwartaal=(($maandDag=='03-31'||$maandDag=='06-30'||$maandDag=='09-30'||$maandDag=='12-31')?true:false);
	$jaar=substr($data['datum'],0,4);




  if($data['datum'] != '0000-00-00')
  {
		if(!isset($jaarSom[$jaar]['waardeBegin']))
		  $jaarSom[$jaar]['waardeBegin']=$data['waardeBegin'];
		foreach($somVelden as $veld)
			$jaarSom[$jaar][$veld]+=$data[$veld];
		foreach($laatsteVelden as $veld)
			$jaarSom[$jaar][$veld]=$data[$veld];
		$jaarSom[$jaar]['performance'] = ((1 + $jaarSom[$jaar]['performance'] / 100) * (1 + $data['performance'] / 100) - 1) * 100;

		//$indexPerf = ((1 + $indexPerf / 100) * (1 + $data['performance'] / 100) - 1) * 100;
		//$jaarSom[$jaar]['index'] = 100+$indexPerf;


    if($eindJaar==true || $laatste==true)
		{
			$rendamentWaarden[] = $jaarSom[$jaar];
		}
		if($eindKwartaal==true || $laatste==true)
		{
			$grafiekData['Datum'][] = $data['datum'];
			$grafiekData['Index'][] = $data['index'] - 100;
			$grafiekData['benchmarkIndex'][] = $data['specifiekeIndex'] - 100;
		}
		if($eindJaar||$laatste)
		{
			$barGraph['Index'][$data['datum']]['leeg'] = 0;

			foreach ($data['extra']['cat'] as $categorie => $waarde)
			{
				if ($categorie == 'LIQ' || $categorie == 'H-Liq')
				{
					$categorie = 'Liquiditeiten';
				}
    
				$barGraph['Index'][$data['datum']][$hoofdcategorieKoppeling[$categorie]] += $waarde / $data['waardeHuidige'] * 100;
        $barGraph['absoluut'][$data['datum']][$hoofdcategorieKoppeling[$categorie]] += $waarde;
				if ($waarde <> 0)
				{
					$categorien[$categorie] = $categorie;
				}
			}
		}
  }


		$huidigeJaarGrafiek[$data['datum']]['performance'] = $data['performance'];
		$huidigeJaarGrafiek[$data['datum']]['performanceCumu'] = ((1 + $huidigeJaarGrafiek[$laatsteDatum]['performanceCumu'] / 100) * (1 + $data['performance'] / 100) - 1) * 100;
		$laatsteDatum = $data['datum'];


}
    
    $stdev=getFondsPerformanceGestappeld2($this->pdf->portefeuilledata['SpecifiekeIndex'],$this->portefeuille,$this->rapportageDatumVanaf , $this->rapportageDatum,'maanden',false,true,true);
    $laatsteDatum='leeg';
    foreach($stdev->reeksen['benchmark'] as $datum=>$rendementDetails)
    {
        $huidigeJaarGrafiek[$datum]['benchmark'] = $rendementDetails['perf'];
        $huidigeJaarGrafiek[$datum]['benchmarkCumu'] = ((1 + $huidigeJaarGrafiek[$laatsteDatum]['benchmarkCumu'] / 100) * (1 + $rendementDetails['perf'] / 100) - 1) * 100;
        $laatsteDatum=$datum;
    }

    foreach($huidigeJaarGrafiek as $datum=>$waarden)
    {
			$maandDag=substr($datum,5,5);
			$laatste=($datum==$this->rapportageDatum?true:false);
			$eindKwartaal=(($maandDag=='03-31'||$maandDag=='06-30'||$maandDag=='09-30'||$maandDag=='12-31')?true:false);

      if(!$laatste && !$eindKwartaal)
      {
        unset($huidigeJaarGrafiek[$datum]);
      }
    }
//listarray($huidigeJaarGrafiek);

    /*
		$q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie <>'' GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}
    $this->categorieVolgorde['Liquiditeiten']='Liquiditeiten';
    $this->categorieOmschrijving['Liquiditeiten']=vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal);
*/
   $grafiekData['Datum'][]="$RapStartJaar-12-01";
   
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       $this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(200,240,255);

       // $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);


        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);

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
		      $this->pdf->row(array(date("Y",$datum) ,//.' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal)
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['rente'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0),
		                           $this->formatGetal($row['performance'],2),
		                           $this->formatGetal($row['index']-100,2)));
                               
                               

		                           if(!isset($waardeBegin))
		                             $waardeBegin=$row['waardeBegin'];
		                           $totaalWaarde = $row['waardeHuidige'];
		                           $totaalResultaat += $row['resultaatVerslagperiode'];
		                           $totaalGerealiseerd += $row['gerealiseerd'];
		                           $totaalOngerealiseerd += $row['ongerealiseerd'];
		                           $totaalOpbrengsten += $row['opbrengsten'];
		                           $totaalKosten += $row['kosten'];
		                           $totaalRente += $row['rente'];
		                           $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
		                           $totaalRendament = $row['index'];

		    $n++;
        $i++;
		    }
		    $this->pdf->fillCell=array();


            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalRente,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),
		                           '',
		                           $this->formatGetal($totaalRendament-100,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }


		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY($this->pdf->marge,112)		;//112
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->Cell(138, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 0);
	    	$this->pdf->Cell(100, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
	      $this->pdf->SetXY(152,180)		;//112
	      $this->VBarDiagram(80, 60, $barGraph['Index']);
		      $this->pdf->SetXY(10,180)		;//112
        $this->VBarDiagram(80, 60, $barGraph['absoluut'],true);
        //$this->VBarDiagram2(120,60,$huidigeJaarGrafiek,true);
		  }

		  $this->pdf->SetXY(8, 155);//165


	   $this->pdf->fillCell = array();



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



function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['Datum'];
    $data1 = $data['Index1'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/count($data) );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(116,95,71);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
    $unit = $lDiag / (count($data));

   // if($jaar)
   //   $unit = $lDiag / 12;

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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }

    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
   // $color=array(200,0,0);
   
   
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
      $yval = $yval2;
    }

    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        
        $this->pdf->Text($XDiag+($i+1)*$unit-0.5,$yval2-4.5,$data1[$i]);
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($w, $h, $data,$absoluut=false)
  {
      global $__appvar;
      $legendaWidth = 00;
      $grafiekPunt = array();
      $verwijder=array();
     // listarray($data);//exit;
      $maxVal=100;
      $minVal=0;

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;


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
          if($absoluut==false)
          {
            $maxVal = max(array($maxVal, $waarden[$categorie]));
            if($waarden[$categorie]<0)
              $minVal = min(array($minVal, $waarden[$categorie]));
          }
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
        if($absoluut==true)
        {
          //echo "$datum ".array_sum($waarden)." $maxVal <br>\n";
          //listarray($waarden);
          $minVal = min(array($minVal, min($waarden)));
          $maxVal = max(array($maxVal, array_sum($waarden)));
        }
      }


     // if(count($data)==1)
        $numBars = count($legenda);
     // else
     //   $numBars = count($legenda)-1;
    //  $numBars=10;

      if($color == null)
      {
        $color=array(155,155,155);
      }
  
    $horDiv = 5;

      if($maxVal <= 100)
      {
        $maxVal = 100;
      }
      elseif($maxVal < 125)
      {
        $maxVal = 125;
      }
      elseif($maxVal < 200)
      {
        $maxVal = 200;
      }
      elseif($maxVal < 300)
      {
        $maxVal = 300;
      }
      elseif($maxVal < 400)
      {
        $maxVal = 400;
      }
  
  
    if($minVal >= 0)
      {
        $minVal = 0;
      }
      elseif($minVal > -25)
      {
        $minVal = -25;
        $horDiv++;
      }
      elseif($minVal > -200)
      {
        $minVal = -200;
        $horDiv+=4;
      }
    elseif($minVal > -300)
    {
      $minVal = -300;
      $horDiv+=7;
    }
    elseif($minVal > -400)
    {
      $minVal = -300;
      $horDiv+=11;
    }
   
   //echo "$minVal $maxVal <br>\n";exit;

      if($absoluut)
      {
        $band=round($maxVal)+abs(round($minVal));
        $decimalen=strlen($band);
        $max=pow(10,$decimalen);

        
       // echo "$min -> $decimalen2 | $max -> $decimalen <br>\n";
        if($band<$max/8)
        {
          $band = $max / 8;
        }
        elseif($band<$max/5)
        {
          $band = $max / 5;
        }
        elseif($band<$max/4)
        {
          $band = $max / 4;
        }
        elseif($band<$max/2)
        {
          $band = $max / 2;
        }
  
        $stap=$band/$horDiv;
        $minVal=floor($minVal/$stap)*$stap;
        $maxVal=ceil($maxVal/$stap)*$stap;
        $horDiv=round($maxVal/$stap)-($minVal/$stap);
  
    
//        $minVal
  
    //    echo "$minVal $maxVal <br>\n";exit;
        
      }

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $legendaWidth=max(array($this->pdf->GetStringWidth($maxVal),$this->pdf->GetStringWidth($minVal)));
      
      $XPage = $this->pdf->GetX()+$legendaWidth;
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda

      $n=0;
      if(count($grafiekCategorie)>10)
        $regelhoogte=5;
      else
        $regelhoogte=7;

      foreach (($this->categorieVolgorde) as $categorie)//array_reverse
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*$regelhoogte+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*$regelhoogte+1.5 );
          $this->pdf->MultiCell(65, 4,$this->categorieOmschrijving[$categorie],0,'L');
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
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1).($absoluut==true?'':" %"),0,0,'R');
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
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte).($absoluut==true?'':" %"),0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + .5));
        $bGrafiek = $vBar * ($this->pdf->NbVal + .5);
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
          if(abs($hval) > 3 && $absoluut==false)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            if($eBaton>5)
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-4,$YstartGrafiek+8,$legenda[$datum],25);

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
          if(abs($hval) > 3 && $absoluut==false)
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


  function VBarDiagram2($w, $h, $data,$metLijn=false)
  {
    global $__appvar;

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1);

    //$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
    $color=array(155,155,155);

    $maxVal=0;
    $minVal=0;
    $maanden=array();
    $aantalStaven=0;
    foreach($data as $maand=>$maandData)
    {
      if($aantalStaven==0)
        $aantalStaven=count($maandData);
      $maanden[$maand]=$maand;
      foreach($maandData as $type=>$waarde)
      {
        if($waarde > $maxVal)
          $maxVal = $waarde;
        if($waarde < $minVal)
          $minVal = $waarde;
      }
    }
    if($metLijn==true)
      $aantalStaven=$aantalStaven/2;
    if($maxVal > 1)
      $maxVal=ceil($maxVal);
    if($minVal < -1)
      $minVal=floor($minVal);
    $minVal = $minVal * 1.1;
    $maxVal = $maxVal * 1.1;
    if ($maxVal <0)
      $maxVal=0;

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

    $horDiv = 10;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);

    $stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;

    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XstartGrafiek-7, $i, -1*$n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }

    $numBars=count($data);
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;


    $this->pdf->SetFillColor();

    $colors=array('performance'=>array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']),
                  'benchmark'=>array(50,50,100));//,'totaalEffect'=>array(0, 52, 121)); //



    $vBar = ($bGrafiek / ($this->pdf->NbVal))/($aantalStaven+1); //4
    $bGrafiek = $vBar * ($this->pdf->NbVal);
    $eBaton = ($vBar * 80 / 100);
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $lastXY=array();
    foreach($data as $periode=>$maandData)
    {

      foreach($maandData as $type=>$val)
      {
        if($metLijn==true)
        {
          //listarray($type);
          if(substr($type,-4)=='Cumu')
          {
            continue;
          }

        }

        $color=$colors[$type];
        $legenda[$type]=$color;
        //Bar
        $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiek + $nulYpos;
        $hval = ($val * $unit);
//        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
        /*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3 && $eBaton > 4)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        */
        $i++;
      }
      $i++;
      if(strlen($periode)==4)
        $xLegenda=$periode;
      else
        $xLegenda=date('M',db2jul($periode));

      $this->pdf->Text($XstartGrafiek + ($i-1) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,$xLegenda);

    }

    $xPos=$XstartGrafiek;
    foreach($data as $periode=>$maandData)
    {

      foreach($maandData as $type=>$val)
      {
        if($metLijn==true)
        {
          //listarray($type);
          if(substr($type,-4)=='Cumu')
          {
            //echo "$type $periode $val <br>\n";

            $color=$colors[substr($type,0,-4)];
            if(!isset($lastXY[$type]))
              $lastXY[$type]=array($XstartGrafiek,$YstartGrafiek + $nulYpos);

            $newXY=array($xPos + $vBar ,$YstartGrafiek + $nulYpos+($val * $unit));

            if($type=='benchmarkCumu')
              $this->pdf->Line($lastXY[$type][0], $lastXY[$type][1] , $newXY[0],$newXY[1] ,array('dash' => "1,2",'color'=>array(0,0,0)));
            else
              $this->pdf->Line($lastXY[$type][0], $lastXY[$type][1] , $newXY[0],$newXY[1] ,array('dash' => 0,'color'=>array(0,0,0)));
            $this->pdf->setDash(0);
            $this->pdf->setDrawColor(0);
            $this->pdf->Rect($newXY[0]-0.5, $newXY[1]-0.5 , 1, 1, 'DF',null,array(0,0,0));


            $lastXY[$type]=array( $newXY[0],$newXY[1]);
            $xPos+=$vBar;
          }

        }

      }
      $xPos+=$vBar;
    }

    $n=0;
    $omschrijvingen=array('performance'=>'Portefeuille','benchmark'=>'Benchmark');
    $this->pdf->setDash(0);
    foreach($legenda as $type=>$kleur)
    {
      if($type=='benchmark')
      {
        $this->pdf->Line($XstartGrafiek+6, $YstartGrafiek+10+($n*4)+1 , $XstartGrafiek+8, $YstartGrafiek+10+($n*4)+1,array('dash' => "1,2",'color'=>array(0,0,0)));
        $this->pdf->setDash(0);
      }
      else
      {
        $this->pdf->Line($XstartGrafiek+6, $YstartGrafiek+10+($n*4)+1 , $XstartGrafiek+8, $YstartGrafiek+10+($n*4)+1,array('dash' => 0,'color'=>array(0,0,0)));
      }


      $this->pdf->Rect($XstartGrafiek+10, $YstartGrafiek+10+($n*4), 2, 2, 'DF',null,$kleur);

      $this->pdf->Text($XstartGrafiek+14, $YstartGrafiek+10+($n*4)+1.5, vertaalTekst($omschrijvingen[$type],$this->pdf->rapport_taal));

      $n++;
    }



    // $color=array(155,155,155);
    // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }
}
?>