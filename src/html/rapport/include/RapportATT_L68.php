<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/25 16:43:07 $
File Versie					: $Revision: 1.54 $

$Log: RapportATT_L68.php,v $
Revision 1.54  2020/03/25 16:43:07  rvv
*** empty log message ***

Revision 1.53  2020/01/25 16:36:35  rvv
*** empty log message ***

Revision 1.52  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.51  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.50  2019/09/18 14:52:23  rvv
*** empty log message ***

Revision 1.49  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.48  2019/09/11 15:48:05  rvv
*** empty log message ***

Revision 1.47  2019/08/25 11:29:05  rvv
*** empty log message ***

Revision 1.46  2019/08/24 16:59:19  rvv
*** empty log message ***

Revision 1.45  2019/06/26 15:11:21  rvv
*** empty log message ***

Revision 1.44  2019/06/22 16:32:52  rvv
*** empty log message ***

Revision 1.43  2019/06/08 16:06:01  rvv
*** empty log message ***

Revision 1.41  2019/05/11 16:48:39  rvv
*** empty log message ***

Revision 1.40  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.39  2019/03/02 18:21:47  rvv
*** empty log message ***

Revision 1.38  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.37  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.36  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.35  2018/12/30 08:15:11  rvv
*** empty log message ***

Revision 1.34  2018/12/29 13:57:23  rvv
*** empty log message ***

Revision 1.33  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.32  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.31  2018/09/01 16:53:24  rvv
*** empty log message ***

Revision 1.30  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.29  2018/07/28 14:45:48  rvv
*** empty log message ***

Revision 1.28  2018/06/27 16:13:50  rvv
*** empty log message ***

Revision 1.27  2018/06/20 16:40:16  rvv
*** empty log message ***

Revision 1.26  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.25  2018/05/26 17:23:51  rvv
*** empty log message ***

Revision 1.24  2018/04/25 16:45:28  rvv
*** empty log message ***

Revision 1.23  2017/11/08 17:12:56  rvv
*** empty log message ***

Revision 1.22  2017/09/17 15:03:24  rvv
*** empty log message ***

Revision 1.21  2017/07/26 20:14:48  rvv
*** empty log message ***

Revision 1.20  2017/07/19 19:30:54  rvv
*** empty log message ***

Revision 1.19  2017/07/01 11:16:18  rvv
*** empty log message ***

Revision 1.18  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.17  2017/04/27 06:12:33  rvv
*** empty log message ***

Revision 1.16  2017/04/26 15:19:25  rvv
*** empty log message ***

Revision 1.15  2017/04/12 15:38:14  rvv
*** empty log message ***

Revision 1.14  2017/04/05 15:39:45  rvv
*** empty log message ***

Revision 1.13  2017/04/02 10:12:45  rvv
*** empty log message ***

Revision 1.12  2017/02/19 10:59:55  rvv
*** empty log message ***

Revision 1.11  2017/01/19 11:41:18  rvv
*** empty log message ***

Revision 1.10  2017/01/15 08:01:57  rvv
*** empty log message ***

Revision 1.9  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.8  2016/11/09 17:05:19  rvv
*** empty log message ***

Revision 1.7  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.6  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.5  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.2  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***

Revision 1.3  2013/06/26 15:55:41  rvv
*** empty log message ***

Revision 1.2  2013/06/12 18:46:36  rvv
*** empty log message ***

Revision 1.1  2013/05/26 13:54:49  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once("rapport/include/ATTberekening_L68.php");

class RapportATT_L68
{
	function RapportATT_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));


    if($RapStartJaar==$RapStopJaar)
	    $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
  $this->att=new ATTberekening_L68($this);


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


  
  
  function getDoorkijkfondsen()
  {
    $DB=new DB();
    $query="SELECT
Fondsen.Fonds,
Fondsen.Portefeuille,
Portefeuilles.Vermogensbeheerder
FROM
Fondsen
INNER JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
LEFT JOIN Beleggingsplan ON Portefeuilles.Portefeuille = Beleggingsplan.Portefeuille
WHERE Fondsen.Portefeuille<>'' AND Portefeuilles.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $huisfondsen[$data['Fonds']]=$data['Portefeuille'];
    }
    $this->doorkijkfondsen=$huisfondsen;
  }

	function writeRapport()
	{
	  global $__appvar;



	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
    $this->categorieKleuren['G-LIQ']= $this->categorieKleuren['Liquiditeiten'];

		// voor data
		$cw=25;
		$cv=5;
		$this->pdf->widthA = array(1,95,$cw,$cv,$cw,$cv,$cw,$cv,$cw,$cv,$cw,$cv,$cw,$cv,$cw,$cv);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];



		if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
		  $indexDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);
		else
		  $indexDatum=$this->rapportageDatumVanaf;
		if(substr($indexDatum,8,2)<>'01')
      $indexDatum=date('Y-m-d',db2jul(substr($indexDatum,0,8).'01')-3600*24);//substr($indexDatum,0,8).'01';

		$jaarTerug=date('Y-m-d',db2jul( (substr($this->rapportageDatum,0,4)-1).'-'.substr($this->rapportageDatum,5,3).'01')-3600*24);

		if(db2jul($indexDatum)<db2jul($jaarTerug))
    {
      $indexDatum=$jaarTerug;
    }
    $this->pdf->rapport_datumvanaf = db2jul($indexDatum);

  $index=new indexHerberekening();
  $maanden=$index->getMaanden(db2jul($indexDatum),db2jul($this->rapportageDatum));
  //listarray($maanden);  echo $indexDatum." ".$this->pdf->PortefeuilleStartdatum;exit;
  $indexData=array();
  foreach ($maanden as $periode)
  {
    $indexData[]=array('datum'=>$periode['stop'],'index'=>100,'waardeHuidige'=>0,'specifiekeIndex'=>100,'extra'=>array('cat'=>array('VAR'=>0,'ZAK'=>0)));
  }
    
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['ATTPaginas']=$this->pdf->rapport_titel;
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
      $hpiGebruik=true;
    else
      $hpiGebruik=false;
    //$indexDatum='2018-12-01';
    
    $this->waarden['historie'] = $this->att->bereken($indexDatum, $this->rapportageDatum, 'Hoofdcategorie',$hpiGebruik);//
//listarray($this->waarden['historie']);
		$barGraph=array();
		$resetPlan=false;
    $gebruikteCategorie=array();
    $maandTotalen=array();
    foreach ($this->waarden['historie'] as $categorie=>$perfData)
    {
      if($categorie<>'totaal')
      {
        foreach ($perfData['perfWaarden'] as $maand => $perfWaarden)
        {
          $maandTotalen[$maand]+=$perfWaarden['eindwaarde'];
        }
      }
    }
    foreach ($this->waarden['historie'] as $categorie=>$perfData)//['totaal']['perfWaarden'] as $maand=>$details)
    {
      if($categorie<>'totaal')
      {
        if($categorie=='G-LIQ')
          $categorie='VAR';
        foreach ($perfData['perfWaarden'] as $maand => $perfWaarden)
        {
          $barGraph['Index'][$maand][$categorie] += $perfWaarden['eindwaarde'] / $maandTotalen[$maand] *100;//$this->waarden['historie']['totaal']['perfWaarden'][$maand]['eindwaarde'] * 100;
          if($perfWaarden['eindwaarde'] <> 0)
            $gebruikteCategorie[$categorie]=$categorie;
          
        }
      }
      else
      {
        foreach ($perfData['perfWaarden'] as $maand => $perfWaarden)
        {
          $barGraph['Plan'][$maand] = $perfWaarden['planTotalen']['ZAK']*100;
          //if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($maand)+31*24*3600)
          //{
            if(round(array_sum($perfWaarden['planTotalen']),2)<>1.00)
            {//echo $this->pdf->PortefeuilleStartdatum." ".$maand."<br>";ob_flush();
              $resetPlan = true;
            }
         // }
        }
      }
    }

		foreach($barGraph['Index'] as $datum=>$categorieData)
		{
			$som=0;
			foreach($categorieData as $categorie=>$waarde)
			{
				$som+=abs($waarde);
			}
			if($som>300)
			{
				foreach($categorieData as $categorie=>$waarde)
					$barGraph['Index'][$datum][$categorie]=0;
			}
		}
    
    

	if($resetPlan==true)
		$barGraph['Plan']=array();
//$indexDataReal = $this->getWaarden($indexDatum ,$this->rapportageDatum ,$this->portefeuille);
//	foreach($indexDataReal as $data)
//		foreach($data['extra'] as $categorie=>$waarde)
//			 $categorien[$categorie]=$categorie;


		$q="SELECT CategorienPerHoofdcategorie.Hoofdcategorie,
Beleggingscategorien.Omschrijving FROM CategorienPerHoofdcategorie
 INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
 WHERE Vermogensbeheerder =  '".$beheerder."' GROUP BY  Hoofdcategorie ORDER BY Hoofdcategorie desc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
			//$categorien

			$this->categorieVolgorde[$data['Hoofdcategorie']]=0;//$data['hoofdcategorie'];
			$this->categorieOmschrijving[$data['Hoofdcategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}
//listarray($this->categorieOmschrijving);exit;
    
    $nieuweVolgorde=array();
    foreach($this->categorieVolgorde as $categorie=>$waarde)
    {
      if(in_array($categorie,$gebruikteCategorie))
        $nieuweVolgorde[$categorie]=$categorie;
    }

$huidigeJaar=substr($this->rapportageDatum,0,4);
$huidigeJaarStartJul=db2jul(substr($this->rapportageDatum,0,4)."-01-01");
$this->categorieVolgorde=$nieuweVolgorde;

$somVelden=array('storting','onttrekking','gerealiseerd','ongerealiseerd','opbrengst','kosten','rente','resultaat');

//listarray($this->waarden['historie']['totaal']['perfWaarden']);
	//	$resetPlan=false;
    $lastJaar='';
    $indexJaren=array();
    $jaarPerf=0;
    $jaarPerfBenchmark=0;
if(count($indexData)<14)
  $huidigeJaarStartJul=$this->pdf->rapport_datumvanaf-100;


		foreach ($this->waarden['historie']['totaal']['perfWaarden'] as $datum=>$data)
		{
      $data['datum']=$datum;
  if($datum != '0000-00-00')
  {
    $jaar=substr($datum,0,4);
    if(db2jul($datum) <  $huidigeJaarStartJul)
    {
      if($jaar <> $lastJaar)
      {
        $jaarPerf=1;
        $jaarPerfBenchmark=1;
        if(isset($indexJaren[$lastJaar]))
        {
          $rendamentWaarden[]=$indexJaren[$lastJaar];
          unset($indexJaren[$lastJaar]);
        }  
      }
      $jaarPerf=($jaarPerf * ($data['procent']+100)/100);
      $data['procentCum']=$jaarPerf*100;

      $jaarPerfBenchmark=($jaarPerfBenchmark * ($data['indexPerf']+100)/100);
      $data['indexPerfCum']=$jaarPerfBenchmark*100;

      if(!isset($indexJaren[$jaar]))
        $indexJaren[$jaar]['waardeBegin']=$data['waardeBegin'];
      $indexJaren[$jaar]['waardeHuidige']=$data['waardeHuidige'];
      $indexJaren[$jaar]['procent']=$data['procent']*100;//-100;
      $indexJaren[$jaar]['indexPerf']=$data['indexPerf']*100;//-100;
      $indexJaren[$jaar]['datum']=$jaar;
      
      $indexJaren[$jaar]['index']=$data['index'];
      foreach($somVelden as $veld)    
        $indexJaren[$jaar][$veld]+=$data[$veld];  
      $lastJaar=$jaar;  
      
    }
    else
    {
      $jaarPerf=((1+$jaarPerf) * (1+$data['procent']))-1;
      $data['procentCum']=$jaarPerf*100;
  
      $jaarPerfBenchmark=((1+$jaarPerfBenchmark) * (1+$data['indexPerf']))-1;
      $data['indexPerfCum']=$jaarPerfBenchmark*100;
      if(isset($indexJaren[$lastJaar]))
      {
        $rendamentWaarden[]=$indexJaren[$lastJaar];
        unset($indexJaren[$lastJaar]);
      }
      $rendamentWaarden[] = $data;    
    }
  
    
    $grafiekData['Datum'][] = $datum;
    $grafiekData['Index'][] = $data['procentCum'];
    $grafiekData['benchmarkIndex'][] = $data['indexPerfCum'];

  }
}

		if($resetPlan==true)
			$barGraph['Plan']=array();


$grafiekData['Datum'][]="$RapStartJaar-12-01";
$firstMonth=false;
   if(count($rendamentWaarden) > 0)
   {

        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       // $this->pdf->SetFillColor(221,224,229);
		    $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);

		    foreach ($rendamentWaarden as $row)
		    {
		     
          if(strlen($row['datum'])==4)
          {
            $datum = $row['datum'];
            $datumTxt = $row['datum'];
          }
          else
          {
            if($firstMonth==true)
            {
              $this->pdf->Ln(4);
              $firstMonth=false;
		        }
            $datum = db2jul($row['datum']);
            $datumTxt = date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal);
          }
		      if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }
		      
		      if($resetPlan==true)
		        $benchmark='';
		      else
		        $benchmark=$this->formatGetal($row['indexPerf']*100,2).'%';

		      $this->pdf->row(array($datumTxt ,
		                           $this->formatGetal($row['beginwaarde'],0),
		                           $this->formatGetal($row['storting']-$row['onttrekking'],0),
                            //$this->formatGetal($row['gerealiseerd'],0).'|'. $this->formatGetal($row['ongerealiseerd'],0).'|'. $this->formatGetal($row['rente'],0),
                               $this->formatGetal($row['resultaat']-$row['kosten']-$row['opbrengst'],0),
                               $this->formatGetal($row['opbrengst'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['resultaat'],0),
		                           $this->formatGetal($row['eindwaarde'],0),
                               $benchmark,
		                           $this->formatGetal($row['procent']*100,2).'%',
		                           $this->formatGetal($row['procentCum'],2).'%'));
                               
		                           if(!isset($waardeBegin))
		                             $waardeBegin=$row['beginwaarde'];
		                           $totaalWaarde = $row['eindwaarde'];
		                           $totaalKoersresultaten+=($row['resultaat']-$row['kosten']-$row['opbrengst']);
		                           $totaalResultaat += $row['resultaat'];
		                           $totaalGerealiseerd += $row['gerealiseerd'];
		                           $totaalOngerealiseerd += $row['ongerealiseerd'];
		                           $totaalOpbrengsten += $row['opbrengst'];
		                           $totaalKosten += $row['kosten'];
		                           $totaalRente += $row['rente'];
		                           $totaalStortingenOntrekkingen += $row['storting']-$row['onttrekking'];
		                           $totaalRendament = $row['procentCum'];
					                     $benchmarkTotaal=$row['indexPerfCum'];

		    $n++;
		    }
		    $this->pdf->fillCell=array();
  
     if($resetPlan==true)
     {
       $benchmark = '';
       $benchmarkSub='';
       unset($grafiekData['benchmarkIndex']);
     }
     else
     {
       $benchmark = $this->formatGetal($benchmarkTotaal, 2) . '%';
       $benchmarkSub='UU';
     }
        $this->pdf->ln(3);
        $this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU',$benchmarkSub,'','UU');
		    $this->pdf->row(array('Samenvatting',
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalKoersresultaten,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),
                               $benchmark,
												       '',
		                           $this->formatGetal($totaalRendament,2).'%'
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		                           	    $this->pdf->CellBorders = array();

		  }
//listarray($barGraph);exit;
		  if (count($barGraph) > 0)
		  {
		      $this->pdf->SetXY($this->pdf->marge,102)		;//112
		    	$this->pdf->Cell(0, 5, 'Vermogensverdeling', 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		      $this->pdf->SetXY(15,140)		;//112
		      $this->VBarDiagram(270, 30, $barGraph['Index'],$barGraph['Plan']);
		  }

		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(8,109+37);//104
  		  $this->pdf->Cell(0, 5, 'Rendement (cumulatief)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+36)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(270-50, 30, $grafiekData,array($this->pdf->rapport_grafiek_pcolor,$this->pdf->rapport_grafiek_icolor),0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 80);
		  }
		  $this->pdf->SetXY(8, 155);//165


		$this->pdf->ln(10);
		$this->pdf->SetX(108);


	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);


	   $this->pdf->fillCell = array();


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
		}

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
    $data1 = $data['benchmarkIndex'];
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
    $XDiag = $XPage ;
    $lDiag = floor($w);

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

		$legenda=array();
		if(count($data)>0)
			$legenda[]='Portefeuille';
		if(count($data1)>0)
			$legenda[]='Benchmark';

		$this->pdf->SetLineWidth(0.2);

		foreach ($legenda as $n=>$lijn)
		{
			if($n==0)
				$kleur=$color;
			else
				$kleur=$color1;
				$this->pdf->Rect($XPage+$lDiag+3 , $YPage+$margin+$n*6+2, 2, 2, 'DF',null,$kleur);
				$this->pdf->SetXY($XPage+$lDiag+6 ,$YPage+$margin+$n*6+1.5 );
				$this->pdf->Cell(20, 3,$lijn,0,0,'L');

		}


    if($color == null)
      $color=array(23,55,94);


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
    $unit = $lDiag / count($data)+1;

    if($jaar)
      $unit = $lDiag / 13;
    
    $aantal=count($data);
    if($aantal>12)
      $unit = $lDiag / ($aantal+1);

		//echo "line : $lDiag -> $unit  <br>\n";exit;

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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(150,150,150)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(150,150,150)));
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
    $laatstePunt=count($data)-1;
    for ($i=0; $i<count($data); $i++)
    {
			//	$xStart+=$unit;
				$xStart = $XDiag + ($i) * $unit;
			$xEind  = $xStart+$unit;

    //  $this->pdf->Text($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,date("d-m-Y",db2jul($legendDatum[$i])),25);

			$this->pdf->SetXY($xStart+0.5*$unit,$YDiag+$hDiag+1);
			$this->pdf->Cell($unit,4,date("d-m-Y",db2jul($legendDatum[$i])),0,0,'C');//

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($xStart, $yval,$xEind, $yval2,$lineStyle );
      if ($i>0)
      {
        $this->pdf->Rect($xStart - 0.5, $yval - 0.5, 1, 1, 'F', '', $color);
        if($i==$laatstePunt)
        {
          $this->pdf->Rect($xEind - 0.5, $yval2 - 0.5, 1, 1, 'F', '', $color);
        }
      }
      $yval = $yval2;
    }
  
    $laatstePunt=count($data1)-1;
    if(is_array($data1))
    {
     // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
				$xStart = $XDiag + ($i) * $unit;
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($xStart, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
        {
          $this->pdf->Rect($xStart - 0.5, $yval - 0.5, 1, 1, 'F', '', $color1);
          if($i==$laatstePunt)
          {
            $this->pdf->Rect($XDiag+($i+1)*$unit - 0.5, $yval2 - 0.5, 1, 1, 'F', '', $color1);
          }
        }
         $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

  function VBarDiagram($w, $h, $data,$plan)
  {
      global $__appvar;
      $legendaWidth = 50;
      $grafiekPunt = array();
      $verwijder=array();
    $minVal=0;
    $maxVal=100;
      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;

				$grafiek[$datum]=array();
        foreach ($waarden as $categorie=>$waarde)
        {
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = count($legenda);//12; //
      if($numBars<13)
        $numBars=12;
  


      if($color == null)
      {
        $color=array(155,155,155); 
      }
  
  
    if($maxVal <= 100)
      $maxVal=100;
    elseif($maxVal < 125)
      $maxVal=125;
    elseif($maxVal < 150)
      $maxVal=150;
    elseif($maxVal < 200)
      $maxVal=200;
    elseif($maxVal < 300)
      $maxVal=300;
  
    if($minVal >= 0)
      $minVal = 0;
    elseif($minVal > -25)
      $minVal=-25;
    elseif($minVal > -50)
      $minVal=-50;
    elseif($minVal > -100)
      $minVal=-100;



      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;


		foreach($colors as $categorie=>$kleur)
			if(!in_array($this->categorieVolgorde,$categorie))
			  $this->categorieVolgorde[$categorie]=$categorie;

      foreach (array_reverse($this->categorieVolgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*6+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*6+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }
      $categorieVolgordePositie = $n;

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
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(150,150,150)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(150,150,150)));
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
	//	echo "line : $bGrafiek -> $eBaton  ".($this->pdf->NbVal + 1)."<br>\n";

      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));


      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
		$lastPlanX=0;
    $beleggingsplanSet = false;
   foreach ($grafiek as $datum=>$data)
   {
		 $this->pdf->SetLineWidth(0.2);
      //foreach($data as $categorie=>$val)
      foreach($this->categorieVolgorde as $categorie)
      {
        $val=$data[$categorie];
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

         if($legendaPrinted[$datum] != 1)
         {
          $this->pdf->SetXY($xval,$YstartGrafiek+1);
          $this->pdf->Cell($eBaton,4,$legenda[$datum],0,0,'C');//$this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);
         }

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

		 if(isset($plan[$datum]) && $plan[$datum] <> -1)
		 {

			 $offset= $minVal*$unit;

			 $planY= $plan[$datum] * $unit + $YstartGrafiek-$offset;
			 $planX= $XstartGrafiek + (1 + $i ) * $vBar ;
			 if(isset($lastPlanY) && $lastPlanY <> 0)
			 {
				 $this->pdf->SetDrawColor(128);
				 $this->pdf->SetLineWidth(0.4);
				 $this->pdf->line($lastPlanX, $lastPlanY,$planX, $planY);
				 $this->pdf->SetDrawColor(0);

         if ( $beleggingsplanSet === false ) {
           $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

           $this->pdf->SetDrawColor(128);
           $this->pdf->SetLineWidth(0.4);
           $this->pdf->line(
             $XstartGrafiek+$bGrafiek+3 ,
             $YstartGrafiek-$hGrafiek+($categorieVolgordePositie+1)*6+3,
             $XstartGrafiek+$bGrafiek+5,
             $YstartGrafiek-$hGrafiek+($categorieVolgordePositie+1)*6+3
           );
           $this->pdf->SetDrawColor(0);

           $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+($categorieVolgordePositie+1)*6+1.5 );
           $this->pdf->Cell(20, 3,vertaalTekst('Strategische verdeling',$this->pdf->rapport_taal),0,0,'L');

           //reset font
           $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
           $this->pdf->SetTextColor(0,0,0);
           $beleggingsplanSet = true;
         }
					// echo "$lastPlanX,$lastPlanY,$planX, $planY <br>\n";
			 }
			 $lastPlanY=$planY;
			 $lastPlanX=$planX;
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
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
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