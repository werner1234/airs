<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:36:13 $
File Versie					: $Revision: 1.11 $

$Log: RapportKERNV_L51.php,v $
Revision 1.11  2020/07/25 15:36:13  rvv
*** empty log message ***

Revision 1.10  2020/06/13 15:12:04  rvv
*** empty log message ***

Revision 1.9  2020/05/16 15:57:02  rvv
*** empty log message ***

Revision 1.8  2020/05/09 16:56:11  rvv
*** empty log message ***

Revision 1.7  2020/04/25 17:15:30  rvv
*** empty log message ***

Revision 1.6  2020/04/04 17:43:15  rvv
*** empty log message ***

Revision 1.5  2020/04/01 16:54:10  rvv
*** empty log message ***

Revision 1.3  2020/02/29 16:23:08  rvv
*** empty log message ***

Revision 1.2  2019/10/30 16:45:39  rvv
*** empty log message ***

Revision 1.1  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.12  2018/03/17 18:47:40  rvv
*** empty log message ***

Revision 1.11  2018/03/11 10:52:28  rvv
*** empty log message ***

Revision 1.10  2018/02/19 13:56:33  rvv
*** empty log message ***

Revision 1.9  2018/02/18 14:58:56  rvv
*** empty log message ***

Revision 1.8  2018/02/05 07:36:46  rvv
*** empty log message ***

Revision 1.7  2018/02/04 15:46:22  rvv
*** empty log message ***

Revision 1.6  2018/01/24 17:06:34  rvv
*** empty log message ***

Revision 1.5  2017/12/16 18:43:36  rvv
*** empty log message ***

Revision 1.4  2017/12/02 19:13:04  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L51.php");
include_once("rapport/Zorgplichtcontrole.php");

class RapportKERNV_L51
{
	function RapportKERNV_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = $pdf;
		$this->pdf->rapport_type = "KERNV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
  	$this->pdf->rapport_titel = "Dashboard vermogen samenstelling";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
		if($this->pdf->lastPOST['debug'])
		  $this->debug=true;
		else
			$this->debug=false;
		$this->debugData=array();
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


	function toonTabel($regels,$xOffset,$titel,$totaal,$type='waarde')
	{
		$this->pdf->setWidths(array($xOffset+3,45,24,15));
		$this->pdf->setAligns(array('L','L','R','R'));
		$this->pdf->setXY($this->pdf->marge,120);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    if($type=='waarde')
	  	$this->pdf->row(array('',vertaalTekst($titel ,$this->pdf->rapport_taal),vertaalTekst('Waarde EUR',$this->pdf->rapport_taal),vertaalTekst('in %',$this->pdf->rapport_taal)));
    else
      $this->pdf->row(array('',vertaalTekst($titel ,$this->pdf->rapport_taal),vertaalTekst('Rendement',$this->pdf->rapport_taal),''));
		
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		$this->pdf->ln(1);
		$totalen=array();
	//	listarray($kleuren);

    if(isset($regels['volgorde']))
      $volgorde=array_values($regels['volgorde']);
    else
      $volgorde=array_keys($regels[$type]);
    
    if($type=='waarde')
    {
      foreach ($volgorde as $categorie)
      {
        if (isset($regels['omschrijving'][$categorie]))
        {
          $data['omschrijving'] = $regels['omschrijving'][$categorie];
        }
        else
        {
          $data['omschrijving'] = $categorie;
        }
        $data['waardeEUR'] = $regels[$type][$categorie];
        $data['percentage'] = $regels[$type][$categorie] / $totaal * 100;
        $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'F', '', $regels['kleur'][$categorie]);
        $this->pdf->row(array('', vertaalTekst($data['omschrijving'], $this->pdf->rapport_taal), $this->formatGetal($data['waardeEUR'], 0), $this->formatGetal($data['percentage'], 2)));
        $totalen['waardeEUR'] += $data['waardeEUR'];
        $totalen['percentage'] += $data['percentage'];
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('', '', 'SUB', 'SUB');
      $this->pdf->row(array('', vertaalTekst('Totaal', $this->pdf->rapport_taal), $this->formatGetal($totalen['waardeEUR'], 0), $this->formatGetal($totalen['percentage'], 2)));
    }
    else
    {
      foreach ($volgorde as $categorie)
      {
        if (isset($regels['omschrijving'][$categorie]))
        {
          $data['omschrijving'] = $regels['omschrijving'][$categorie];
        }
        else
        {
          $data['omschrijving'] = $categorie;
        }
        $data['rendement'] = $regels[$type][$categorie];
        $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 2, 2, 'F', '', $regels['kleur'][$categorie]);
        $this->pdf->row(array('', vertaalTekst($data['omschrijving'], $this->pdf->rapport_taal), $this->formatGetal($data['rendement'], 2)));
    
      }
    }
		//$this->pdf->excelData[]=array('Totaal', round($totalen['waardeEUR'], 0), round($totalen['percentage'], 2));
		//$this->pdf->excelData[]=array();
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
	}
  
  function toonZorgVerdeling()
  {
    $this->pdf->setAligns(array('L','L','R','R','R','R'));
    $this->pdf->setWidths(array(188,30,15,12,15,15));

    $query="SELECT Zorgplicht,Omschrijving  FROM Zorgplichtcategorien WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $categorieen=array();
    while ($data = $DB->nextRecord())
    {
      $categorieen[$data['Zorgplicht']] = $data['Omschrijving'];
    }

  
    $zorgplicht = new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Categorie','Minumum','Norm','Maximum','Werkelijk'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    //listarray($zpwaarde);
    $this->pdf->fillCell=array(0,0,0,0,0,1);
    foreach($zpwaarde['conclusieDetail'] as $categorie=>$details)
    {
      if(round($details['percentage'],2)>$details['maximum'] || round($details['percentage'],2)<$details['minimum'])
        $color=array(243,45,13);//array(220,120,120);
      else
        $color=array(47,175,22);//array(220,220,75);
  
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $this->pdf->row(array('',$categorieen[$categorie],$this->formatGetal($details['minimum'],0).'%',$this->formatGetal($details['norm'],0).'%',$this->formatGetal($details['maximum'],0).'%',$this->formatGetal($details['percentage'],0).'%'));
    }
    unset($this->pdf->fillCell);
  
  }
	
	function toonBenchmarkVerdeling()
  {
  
    $bechmarkVerdeling=array();
    $DB=new DB();
    $query="SELECT vanaf FROM benchmarkverdelingVanaf WHERE benchmark='".$this->pdf->portefeuilledata['SpecifiekeIndex']."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
    $DB->SQL($query);
    $DB->Query();
    if($DB->records()>0)
    {
      $datum = $DB->nextRecord();
      $query = "SELECT benchmarkverdelingVanaf.fonds,benchmarkverdelingVanaf.percentage,Fondsen.Omschrijving FROM benchmarkverdelingVanaf
        JOIN Fondsen ON benchmarkverdelingVanaf.fonds = Fondsen.Fonds
        WHERE benchmark='".$this->pdf->portefeuilledata['SpecifiekeIndex']."' AND vanaf = '" . $datum['vanaf'] . "'";
    
    }
    else
    {
      $query = "SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving
        FROM benchmarkverdeling
        JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
        WHERE benchmark='" . $this->pdf->portefeuilledata['SpecifiekeIndex'] . "'";
    }
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->nextRecord())
    {
      $bechmarkVerdeling[$data['fonds']] = $data;
    }
    if(count($bechmarkVerdeling)==0)
    {
      $query = "SELECT Fondsen.Fonds,Fondsen.Omschrijving, 100 as percentage FROM Fondsen WHERE Fondsen.Fonds='" . $this->pdf->portefeuilledata['SpecifiekeIndex'] . "'";
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $bechmarkVerdeling[$data['Fonds']] = $data;
      }
    }
  
    $this->pdf->setAligns(array('L','R'));
    $this->pdf->setWidths(array(50,20));
    if(count($bechmarkVerdeling)>0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('De benchmark is samgengesteld uit:'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($bechmarkVerdeling as $fonds=>$fondsData)
      {
        $this->pdf->row(array($fondsData['Omschrijving'],$this->formatGetal($fondsData['percentage'],0).'%'));
      }
    }

    
    
  }

	function printPie($kleurdata,$xstart,$ystart,$titel)
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
		if($kleurdata)
		{
				$sorted 		= array();
				$percentages 	= array();
				$kleur			= array();
				$valuta 		= array();

			//$kleurdata=	array_reverse($kleurdata);
		//	listarray($kleurdata);
				foreach($kleurdata as $key=>$data)
				{
					$percentages[] 	= $data['percentage'];
					$kleur[] 			= $data['kleur'];
					$valuta[] 		= $key;
				}
				//arsort($percentages);

				foreach($percentages as $key=>$percentage)
				{
					$sorted[$valuta[$key]]['kleur']=$kleur[$key];
					$sorted[$valuta[$key]]['percentage']=$percentage;
				}
				$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');

			$pieData=array();
			$grafiekKleuren = array();

			$a=0;
			foreach($kleurdata as $key=>$value)
			{
				if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
					$grafiekKleuren[]=$standaardKleuren[$a];
				else
					$grafiekKleuren[] = array($value['kleur'][0],$value['kleur'][1],$value['kleur'][2]);
				$pieData[$key] = $value['percentage'];
				$a++;
			}
		}
		else
			$grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->pdf->rapport_fontcolor['r'],$this->pdf->pdf->rapport_fontcolor['g'],$this->pdf->pdf->rapport_fontcolor['b']);

		$this->pdf->rapport_printpie = true;
		foreach($pieData as $key=>$value)
		{
			if ($value < 0)
					$this->pdf->rapport_printpie = false;
		}

    if($this->pdf->rapport_printpie)
		{
			$this->pdf->SetXY($xstart, $ystart);
			$y = $this->pdf->getY();
      PieChart_L51($this->pdf,50,50,$pieData,'%l',$grafiekKleuren,vertaalTekst($titel,$this->pdf->rapport_taal),'geen');
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
		}
		else
    {
      $this->pdf->SetXY($xstart, $ystart);
      $y = $this->pdf->getY();
      $this->BarDiagram(50,50,$pieData,'%l',$grafiekKleuren,vertaalTekst($titel,$this->pdf->rapport_taal));
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
	}
  
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $nbDiv=5;
    $legendWidth=25;
    $YDiag = $YPage+30-((count($data)*5)/2);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);
    $color=array(155,155,155);
    $maxVal = max($data)*1.1;
    $minVal=0;
    $maxVal=ceil($maxVal/10)*10;
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;

    $unit = $lDiag / $bandBreedte;
    $hBar = 5;
    $eBaton = floor($hBar * 80 / 100);
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $i=0;
    $this->pdf->setXY($XPage,$YPage);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
    $this->pdf->Cell($w,4,$titel,0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
    foreach($data as $key=>$val)
    {
      $this->pdf->SetFillColor($colorArray[$i][0],$colorArray[$i][1],$colorArray[$i][2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'F');
      $this->pdf->SetXY($XPage, $yval);
      $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
      $i++;
    }
  }
  
  
  function bepaalWegingNormaal($doorkijkSoort,$belCategorie='')
  {
    global $__appvar;
    if ($belCategorie <> '')
    {
      $fondsFilter = "AND Beleggingscategorie='$belCategorie'";
    }
    else
    {
      $fondsFilter = '';
    }
    
    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal
                  FROM TijdelijkeRapportage
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" . $__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();
    
    
    $vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
    $query = "SELECT sum(actuelePortefeuilleWaardeEuro) as waardeEUR, ".$vertaling[$doorkijkSoort]." as verdeling , ".$vertaling[$doorkijkSoort]."Omschrijving as Omschrijving
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']."
					GROUP BY ".$vertaling[$doorkijkSoort]."
					ORDER BY ".$vertaling[$doorkijkSoort]."Volgorde";
    
    $db=new DB();
    $db->SQL($query);
    $db->Query();
    
    $doorkijkVerdeling=array();
    while($row = $db->nextRecord())
    {
      $categorie=$row['Omschrijving'];
      $totaalPercentage=$row['waardeEUR']/$totaalWaarde['totaal']*100;
      $doorkijkVerdeling['categorien'][$categorie]+=$totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['percentage']+=$totaalPercentage;
      $doorkijkVerdeling['details'][$categorie]['waardeEUR']+=$row['waardeEUR'];
    }
    return $doorkijkVerdeling;
  }

	function vulPagina($data,$type)
	{
		$doorkijkTitels=array('crmVerdeling'=>'Per entiteit','depotbank'=>'Per beheerder','beleggingscategorie'=>'Beleggingscategorie');//array();Beleggingscategorie
    $categorieen=array('crmVerdeling','depotbank','beleggingscategorie');
    $index=0;
		foreach($categorieen as $verdelingSoort)
		{
			$xOffset =  $index * 98;

			$this->toonTabel($data[$verdelingSoort],$xOffset,$doorkijkTitels[$verdelingSoort],$data['totaleWaarde'],$type);
			$grafiekdata=array();
			if($type=='waarde')
      {
        foreach ($data[$verdelingSoort]['waarde'] as $categorie => $waarde) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
        {
          $percentage = $waarde / $data['totaleWaarde'] * 100;
          $kleur = $data[$verdelingSoort]['kleur'][$categorie];
          $grafiekdata[$categorie]['kleur'] = $kleur;
          $grafiekdata[$categorie]['percentage'] = $percentage;
        }
        $this->printPie( $grafiekdata,30+$xOffset,40,$doorkijkTitels[$verdelingSoort]);
      }
      else
      {
        foreach ($data[$verdelingSoort][$type] as $categorie => $percentage) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
        {
          $kleur = $data[$verdelingSoort]['kleur'][$categorie];
          $grafiekdata[$categorie]['kleur'] = $kleur;
          $grafiekdata[$categorie]['percentage'] = $percentage;
        }
        $this->pdf->setXY(10 + $xOffset, 35);
        $this->VBarDiagram2(77, 50, $grafiekdata, '', $doorkijkTitels[$verdelingSoort]);
      }
  		//listarray($grafiekdata);exit;
		  

      $index++;
		}
	}

	function writeRapport()
	{
		$db=new DB();
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder='$beheerder' " ;
    $db->SQL($query);
    $db->Query();
    $this->kleuren = array();
    $data = $db->nextRecord();
    $kleuren=unserialize($data['grafiek_kleur']);

    $standaardKleuren=array();
    foreach($kleuren as $type=>$kleurGegevens)
    {
      foreach($kleurGegevens as $cat=>$kleurData)
      {
        if($kleurData['R']['value']<>0 && $kleurData['G']['value']<>0 && $kleurData['B']['value']<>0)
        {
          $standaardKleuren[]=array($kleurData['R']['value'],$kleurData['G']['value'],$kleurData['B']['value']);
        }
      }
    }
    $standaardKleurenCrm=array(array(150,210,80),array(205,235,255),array(255,255,155),array(0,175,80),array(155,205,255),array(255,200,155),array(205,255,155),array(0,175,255));
    
    $query="DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden=array();
    while($naw = $db->nextRecord())
    {
      $crmVelden[]=$naw['Field'];
    }
    
    if(in_array('RapportageNaam',$crmVelden))
    {
      $crmVeld=',CRM_naw.RapportageNaam as crmVerdeling';
    }
    else
    {
      $crmVeld=',CRM_naw.naam as crmVerdeling';
    }
    $crmJoin="LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.Portefeuille";

    //$aantalPortefeuilles=count($this->pdf->portefeuilles);
    $totalen=array();
    $portefeuilleDetails=array();
    $i=0;
    $j=0;
    foreach($this->pdf->portefeuilles as $portefeuille)
    {
      $query="SELECT Portefeuilles.Einddatum, Portefeuilles.Depotbank, Portefeuilles.kleurcode, if(Portefeuilles.Selectieveld1 <>'',Portefeuilles.Selectieveld1 ,Depotbanken.Omschrijving) as depOmschrijving $crmVeld
FROM Portefeuilles JOIN Depotbanken ON Portefeuilles.depotbank=Depotbanken.Depotbank $crmJoin WHERE Portefeuilles.Portefeuille='$portefeuille'";
      $db->SQL($query);
      $db->Query();
      $pdata = $db->nextRecord();
      if(db2jul($pdata['Einddatum']) < db2jul($this->rapportageDatum))
        continue;
      
      
      if($pdata['crmVerdeling']=='')
        $pdata['crmVerdeling']='leeg';
      $pdata['kleurcode']=unserialize($pdata['kleurcode']);
      $portefeuilleWaarden[$portefeuille]['belCatWaarde']=array();
      $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,(substr($this->rapportageDatum,5,5)=='01-01'?true:false),'EUR',$this->rapportageDatumVanaf);
      $portefeuilleDetails[$portefeuille]=$pdata;
      foreach($gegevens as $waarde)
      {
        $totalen['crmVerdeling']['waarde'][$pdata['crmVerdeling']]+=$waarde['actuelePortefeuilleWaardeEuro'];
        if(!isset($totalen['crmVerdeling']['kleur'][$pdata['crmVerdeling']]))
        {
          if(isset($standaardKleurenCrm[$i]))
            $totalen['crmVerdeling']['kleur'][$pdata['crmVerdeling']] = $standaardKleurenCrm[$i];
          else
            $totalen['crmVerdeling']['kleur'][$pdata['crmVerdeling']] = $standaardKleuren[$i];
          $i++;
        }
        
        $totalen['depotbank']['waarde'][$pdata['depOmschrijving']]+=$waarde['actuelePortefeuilleWaardeEuro'];
        $totalen['depotbank']['omschrijving'][$pdata['depOmschrijving']]=$pdata['depOmschrijving'];
        if(!isset($totalen['depotbank']['kleur'][$pdata['depOmschrijving']]))
        {
          $totalen['depotbank']['kleur'][$pdata['depOmschrijving']] = $standaardKleuren[$j];
          $j++;
        }
  
        $totalen['beleggingscategorie']['waarde'][$waarde['beleggingscategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
        $totalen['beleggingscategorie']['volgorde'][$waarde['beleggingscategorieVolgorde']]=$waarde['beleggingscategorie'];
        $totalen['beleggingscategorie']['omschrijving'][$waarde['beleggingscategorie']]=$waarde['beleggingscategorieOmschrijving'];
        if(!isset($totalen['beleggingscategorie']['kleur'][$waarde['beleggingscategorie']]))
          $totalen['beleggingscategorie']['kleur'][$waarde['beleggingscategorie']]=array($kleuren['OIB'][$waarde['beleggingscategorie']]['R']['value'],
            $kleuren['OIB'][$waarde['beleggingscategorie']]['G']['value'],
            $kleuren['OIB'][$waarde['beleggingscategorie']]['B']['value']);
        
  
        $totalen['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
  
        $portefeuilleDetails[$portefeuille]['actuelePortefeuilleWaardeEuro']+=$waarde['actuelePortefeuilleWaardeEuro'];
      }
      $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatumVanaf,(substr($this->rapportageDatumVanaf,5,5)=='01-01'?true:false),'EUR',$this->rapportageDatumVanaf);
      foreach($gegevens as $waarde)
      {
        $totalen['beginVermogen']+=$waarde['actuelePortefeuilleWaardeEuro'];
        $portefeuilleDetails[$portefeuille]['beginVermogen']+=$waarde['actuelePortefeuilleWaardeEuro'];
      }
      $portefeuilleDetails[$portefeuille]['rendement']=performanceMeting($portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'], 'EUR');
      $query = "SELECT SUM(((TO_DAYS('".$this->rapportageDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$this->rapportageDatum."') - TO_DAYS('".$this->rapportageDatumVanaf."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1,
        SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2
        FROM  Rekeningen Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening WHERE Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
      $DB = new DB();
      $DB->SQL($query);
      $DB->Query();
      $weging = $DB->NextRecord();
      $gemiddelde = $portefeuilleDetails[$portefeuille]['beginVermogen'] + $weging['totaal1'];
      $portefeuilleDetails[$portefeuille]['gemiddelde']+=$gemiddelde;
      $totalen['crmVerdeling']['gemiddelde'][$pdata['crmVerdeling']]+=$gemiddelde;
      $totalen['depotbank']['gemiddelde'][$pdata['depOmschrijving']]+=$gemiddelde;
      $totalen['gemiddelde']+=$gemiddelde;
    }

    foreach($portefeuilleDetails as $portefeuille=>$details)
    {
      //$aandeelDepot=$portefeuilleDetails[$portefeuille]['actuelePortefeuilleWaardeEuro']/$totalen['depotbank']['waarde'][$portefeuilleDetails[$portefeuille]['depOmschrijving']];
      //$aandeelCrmVerdeling=$portefeuilleDetails[$portefeuille]['actuelePortefeuilleWaardeEuro']/$totalen['crmVerdeling']['waarde'][$portefeuilleDetails[$portefeuille]['crmVerdeling']];
  
      $aandeelDepot=$portefeuilleDetails[$portefeuille]['gemiddelde']/$totalen['depotbank']['gemiddelde'][$portefeuilleDetails[$portefeuille]['depOmschrijving']];
      $aandeelCrmVerdeling=$portefeuilleDetails[$portefeuille]['gemiddelde']/$totalen['crmVerdeling']['gemiddelde'][$portefeuilleDetails[$portefeuille]['crmVerdeling']];
  
      $totalen['crmVerdeling']['rendement'][$portefeuilleDetails[$portefeuille]['crmVerdeling']]+=$portefeuilleDetails[$portefeuille]['rendement']*$aandeelCrmVerdeling;
      $totalen['depotbank']['rendement'][$portefeuilleDetails[$portefeuille]['depOmschrijving']]+=$portefeuilleDetails[$portefeuille]['rendement']*$aandeelDepot;
      
    }

    
    if($this->pdf->portefeuilledata['SpecifiekeIndex']<>'')
    {
      $DB = new DB();
      $query = "SELECT Fondsen.Fonds,Fondsen.Omschrijving FROM Fondsen WHERE Fondsen.Fonds='" . $this->pdf->portefeuilledata['SpecifiekeIndex'] . "'";
      $DB->SQL($query);
      $DB->Query();
      $bechmarkData = $DB->nextRecord();
      $totalen['crmVerdeling']['rendement'][$bechmarkData['Omschrijving']] = getFondsPerformanceGestappeld($this->pdf->portefeuilledata['SpecifiekeIndex'], $this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
      $totalen['crmVerdeling']['kleur'][$bechmarkData['Omschrijving']] = array(100, 100, 100);
    }

    $att=new ATTberekening_L51($this);
    $perfData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'EUR','categorie','maanden');
    foreach($perfData as $categorie=>$categorieData)
    {
      $totalen['beleggingscategorie']['rendement'][$categorie]=$categorieData['procent'];
    }
  // listarray($perfData);

    ksort($totalen['beleggingscategorie']['volgorde']);
    //listarray($totalen);
    //listarray($portefeuilleDetails);
    


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
		$this->vulPagina($totalen,'waarde');

  	$this->pdf->rapport_titel = "Rendementen";
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'2Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type .'2Paginas']=$this->pdf->rapport_titel;
   
    if(isset($totalen['beleggingscategorie']['rendement']['totaal']))
      unset($totalen['beleggingscategorie']['rendement']['totaal']);
    $this->vulPagina($totalen,'rendement');
    
    $this->pdf->setXY($this->pdf->marge,160);
    $this->toonBenchmarkVerdeling();
    $this->pdf->setXY($this->pdf->marge,160);
    $this->toonZorgVerdeling();
		
	}
  
  
  function VBarDiagram2($w, $h, $data,$datalijn,$titel,$procent=true,$legendaLocatie='U')
  {
    global $__appvar;
    
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setXY($XPage,$YPage+2);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell($w,4,$titel,0,1,'L');
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->koplijn[0],$this->pdf->koplijn[1],$this->pdf->koplijn[2]),'dash'=>0));
    $this->pdf->line($XPage,$YPage+$this->pdf->rowHeight+3,$XPage+$w,$YPage+$this->pdf->rowHeight+3);
    
    $YPage=$YPage+$h+15;
    
    $maxVal=1;
    $minVal=-1;
    foreach($data as $categorie=>$waarden)
    {
      
      if($waarden['percentage'] > $maxVal)
        $maxVal=ceil($waarden['percentage'] );
      if($waarden['percentage']  < $minVal)
        $minVal=floor($waarden['percentage'] );
    }
    
    if($procent==false)
      $maxVal=ceil($maxVal/pow(10,strlen($maxVal)-1))*pow(10,strlen($maxVal)-1);
    else
      $maxVal=ceil($maxVal/5)*5;
//echo $max;exit;
//echo "$minVal <br>\n";
    $minVal=floor($minVal/.5)*.5;
//
//echo "$minVal <br>\n<br>\n";
    $numBars = 1;//count($legenda);
    $color=array(155,155,155);
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//      $XPage = $this->pdf->GetX();
//      $YPage = $this->pdf->GetY()+$h+15;
    $margin = 0;
    $margeLinks=10;
    $XPage+=$margeLinks;
    $w-=$margeLinks;
    
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda
    
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
    
    
    $horDiv = 4;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    $stapgrootte = round(abs($bereik)/$horDiv);
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    if($procent==true)
      $legendaEnd=' %';
    else
      $legendaEnd='';
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
      $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1).$legendaEnd,0,0,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte).$legendaEnd,0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek);// / ($this->pdf->NbVal + 1));
    
    $eBaton = ($vBar * .8);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $aantalCategorien=count($data);
    $catCount=0;
    
    foreach($data as $categorie=>$gegevens)
    {
      
      $val=$gegevens['percentage'];
      $lval = $eBaton/$aantalCategorien;
      $xval = $XstartGrafiek + ($catCount * $lval)+ $vBar *.1 ;
      $yval = $YstartGrafiek + $nulYpos ;
      $hval = ($val * $unit);
      
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$gegevens['kleur']);
      
      
      $catCount++;
      
    }
    
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}