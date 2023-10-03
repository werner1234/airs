<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/20 17:13:48 $
File Versie					: $Revision: 1.29 $

$Log: RapportPERFG_L68.php,v $
Revision 1.29  2020/05/20 17:13:48  rvv
*** empty log message ***

Revision 1.28  2020/03/25 16:43:07  rvv
*** empty log message ***

Revision 1.27  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.26  2019/09/18 14:52:23  rvv
*** empty log message ***

Revision 1.25  2019/09/11 15:48:05  rvv
*** empty log message ***

Revision 1.24  2019/08/25 11:29:05  rvv
*** empty log message ***

Revision 1.23  2019/08/24 16:59:19  rvv
*** empty log message ***

Revision 1.22  2019/08/21 10:41:17  rvv
*** empty log message ***

Revision 1.21  2019/06/30 11:29:43  rvv
*** empty log message ***

Revision 1.20  2019/06/29 18:24:12  rvv
*** empty log message ***

Revision 1.19  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.18  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.17  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.16  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.15  2018/09/22 17:12:17  rvv
*** empty log message ***

Revision 1.14  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.13  2018/06/20 16:40:16  rvv
*** empty log message ***

Revision 1.12  2017/10/04 16:09:09  rvv
*** empty log message ***

Revision 1.11  2017/09/20 13:13:13  rvv
*** empty log message ***

Revision 1.10  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.9  2017/07/01 11:16:18  rvv
*** empty log message ***

Revision 1.8  2017/03/15 16:36:10  rvv
*** empty log message ***

Revision 1.7  2017/02/25 18:02:29  rvv
*** empty log message ***

Revision 1.6  2017/01/15 08:01:57  rvv
*** empty log message ***

Revision 1.5  2017/01/04 16:22:50  rvv
*** empty log message ***

Revision 1.4  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.3  2016/11/16 16:51:17  rvv
*** empty log message ***

Revision 1.2  2016/11/12 20:21:18  rvv
*** empty log message ***

Revision 1.1  2016/11/05 17:51:44  rvv
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

class RapportPERFG_L68
{
	function RapportPERFG_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;

		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Langjarig rendement";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	 // $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";
    $this->att=new ATTberekening_L68($this);
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
    $this->categorieKleuren['G-LIQ']= $this->categorieKleuren['Liquiditeiten'];


		
		$q="SELECT hoofdcategorie,hoofdcategorieOmschrijving as Omschrijving,hoofdcategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND hoofdcategorie <>'' GROUP BY hoofdcategorie  ORDER BY hoofdcategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['hoofdcategorie']]=0;//$data['hoofdcategorie'];
		  $this->categorieOmschrijving[$data['hoofdcategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}
    $this->categorieVolgorde['G-LIQ']='Liquiditeiten';
    $this->categorieOmschrijving['G-LIQ']=vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal);



//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars['PERFGPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['PERFGPaginas']=$this->pdf->rapport_titel;


    
    $indexDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);
    
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
      $hpiGebruik=true;
    else
      $hpiGebruik=false;
    
    $this->waarden['historie'] = $this->att->bereken($indexDatum, $this->rapportageDatum, 'Hoofdcategorie',$hpiGebruik);
  //   listarray($this->waarden['historie'] );
    
    $barGraph=array();
    $resetPlan=false;
    $gebruikteCategorie=array();
    foreach ($this->waarden['historie'] as $categorie=>$perfData)//['totaal']['perfWaarden'] as $maand=>$details)
    {
      if($categorie<>'totaal')
      {
        if($categorie=='G-LIQ')
          $categorie='VAR';
        foreach ($perfData['perfWaarden'] as $maand => $perfWaarden)
        {
          $barGraph['Index'][$maand][$categorie] += $perfWaarden['eindwaarde'] / $this->waarden['historie']['totaal']['perfWaarden'][$maand]['eindwaarde'] * 100;
          if($perfWaarden['eindwaarde'] <> 0)
            $gebruikteCategorie[$categorie]=$categorie;
          
        }
      }
      else
      {
        foreach ($perfData['perfWaarden'] as $maand => $perfWaarden)
        {
          $barGraph['Plan'][$maand] = $perfWaarden['planTotalen']['ZAK']*100;
          if(round(array_sum($perfWaarden['planTotalen']),2)<>1.00)
          {
            $resetPlan = true;
          }
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
      if($som>150)
      {
        foreach($categorieData as $categorie=>$waarde)
          $barGraph['Index'][$datum][$categorie]=0;
      }
    }
  
  
$nieuweVolgorde=array();
foreach($this->categorieVolgorde as $categorie=>$waarde)
{
  if(in_array($categorie,$gebruikteCategorie))
    $nieuweVolgorde[$categorie]=$categorie;
}
		//rvv
    
    $somVelden=array('storting','onttrekking','gerealiseerd','ongerealiseerd','opbrengst','kosten','rente','resultaat');
    $periodeWaarden=array();
    
    $index=0;
    $laatsteBenchmark=0;
    $indexBenchmark=0;
//listarray($this->waarden['historie']);
    foreach ($this->waarden['historie']['totaal']['perfWaarden'] as $datum=>$data)
    {
      if($datum != '0000-00-00')
      {
      $data['procent']=$data['procent']*100;
      $data['indexPerf']=$data['indexPerf']*100;
      
      $index = ( (1 + ($index / 100)) * (1 + ($data['procent'] / 100)) - 1) * 100;
      $data['index'] = $index;
      $laatsteIndex=$index;// ;
      
      $laatsteBenchmark = ((1 + ($laatsteBenchmark / 100)) * (1 + ($data['indexPerf'] / 100)) - 1) * 100;
      
      
      $grafiekData['Datum'][] = $datum;
      $grafiekData['Index'][] = $data['index'];
      $grafiekData['benchmarkIndex'][] = $laatsteBenchmark;
      

        $jaar=substr($datum,0,4);
        $maand=substr($datum,5,2);
        $kwartaal=ceil($maand/3);
        $jaarKwartaal=$jaar.$kwartaal;
        
        if(1)//$jaar < $rapportageJaar)
        {
          $data['periode']=$jaar;
          if(!isset($periodeWaarden['jaar'][$jaar]))
          {
            //$data['performance']=$perf;
            $periodeWaarden['jaar'][$jaar] = $data;
            //echo $data['datum']." ". $perf."<br>\n";
            $periodeWaarden['jaar'][$jaar]['benchmarkTotaal'] = $laatsteBenchmark;
          }
          else
          {
            foreach($somVelden as $veld)
              $periodeWaarden['jaar'][$jaar][$veld] += $data[$veld];
            
						if(!isset($periodeWaarden['jaar'][$jaar]['beginwaarde']))
							$periodeWaarden['jaar'][$jaar]['beginwaarde']=$data['beginwaarde'];

						$periodeWaarden['jaar'][$jaar]['eindwaarde'] = $data['eindwaarde'];
            $periodeWaarden['jaar'][$jaar]['index'] = $data['index'];
            $periodeWaarden['jaar'][$jaar]['datum'] = $data['datum'];
            $periodeWaarden['jaar'][$jaar]['benchmarkTotaal'] = $laatsteBenchmark;
            $periodeWaarden['jaar'][$jaar]['indexPerf'] = (( (1+$periodeWaarden['jaar'][$jaar]['indexPerf']/100)  * (1+$data['indexPerf']/100))-1)*100;
            $periodeWaarden['jaar'][$jaar]['procent'] = (( (1+$periodeWaarden['jaar'][$jaar]['procent']/100)  * (1+$data['procent']/100))-1)*100;
            //	echo $data['datum']." ". $periodeWaarden['jaar'][$jaar]['performance']." = (( (1+".$periodeWaarden['jaar'][$jaar]['performance']."/100)  * (1+".$data['performance']."/100))-1)*100;<br>\n";
          }
        }
        if(1)
        {
          $data['periode']=$jaar.'Q'.$kwartaal;
          if(!isset($periodeWaarden['kwartaal'][$jaarKwartaal]))
          {
          $periodeWaarden['kwartaal'][$jaarKwartaal] = $data;
          }
          else
          {
            foreach ($somVelden as $veld)
            {
              $periodeWaarden['kwartaal'][$jaarKwartaal][$veld] += $data[$veld];
            }
            $periodeWaarden['kwartaal'][$jaarKwartaal]['index'] = $data['index'];
            $periodeWaarden['kwartaal'][$jaarKwartaal]['datum'] = $data['datum'];
            $periodeWaarden['kwartaal'][$jaarKwartaal]['eindwaarde'] = $data['eindwaarde'];
            $periodeWaarden['kwartaal'][$jaarKwartaal]['benchmarkTotaal'] = $laatsteBenchmark;
            $periodeWaarden['kwartaal'][$jaarKwartaal]['indexPerf'] = (( (1+$periodeWaarden['kwartaal'][$jaarKwartaal]['indexPerf']/100)  * (1+$data['indexPerf']/100))-1)*100;
            $periodeWaarden['kwartaal'][$jaarKwartaal]['procent'] = (( (1+$periodeWaarden['kwartaal'][$jaarKwartaal]['procent']/100)  * (1+$data['procent']/100))-1)*100;
            
          }
					//echo $data['datum']." $jaarKwartaal ".$data['indexPerf']." ".$periodeWaarden['kwartaal'][$jaarKwartaal]['indexPerf']." <br>\n";
          //	listarray($periodeWaarden['kwartaal'][$jaarKwartaal]);
        }
      }
    }
//listarray($periodeWaarden);

    
   

$grafiekData['Datum'][]="$RapStartJaar-12-01";
$firstMonth=true;

   if(count($periodeWaarden['kwartaal']) <13 )
     $verdeling='kwartaal';
   else
     $verdeling='jaar';

   if(count($periodeWaarden[$verdeling]) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       // $this->pdf->SetFillColor(221,224,229);
		    $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
        $totaalRendament=100;
		    foreach ($periodeWaarden[$verdeling] as $row)
		    {
		      if($row['datum']=='')
            $jaar=substr($row['periode'],0,4);
		      else
          $jaar=substr($row['datum'],0,4);
          $datumTxt=$row['periode'];


          if($verdeling=='kwartaal')
          {
            if(isset($laatsteJaar)&& $laatsteJaar<>$jaar)//
            {
              $this->pdf->fillCell=array();
               $subData=$periodeWaarden['jaar'][$laatsteJaar];
              $this->pdf->ln(1);
              if($resetPlan==true)
              {
                $benchmark = '';
                $benchmarkSub='';
                
              }
              else
              {
                $benchmark = $this->formatGetal($subData['indexPerf'], 2) . '%';
                $benchmarkSub='TS';
              }
              $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS',$benchmarkSub,'TS','TS');

							$this->pdf->row(array($laatsteJaar ,
																$this->formatGetal($subData['beginwaarde'],0),
																$this->formatGetal($subData['storting']-$subData['onttrekking'],0),
																//$this->formatGetal($subData['gerealiseerd']+$subData['ongerealiseerd']+$subData['rente'],0),
                                $this->formatGetal($subData['resultaat']-$subData['kosten']-$subData['opbrengst'],0),
																$this->formatGetal($subData['opbrengst'],0),
																$this->formatGetal($subData['kosten'],0),
																$this->formatGetal($subData['resultaat'],0),
																$this->formatGetal($subData['eindwaarde'],0),
                                $benchmark,
																$this->formatGetal($subData['procent'],2).'%',
																$this->formatGetal($subData['index'],2).'%'));

							$this->pdf->ln(3);
              $this->pdf->CellBorders = array();
            }
          }


          if($n%2==0)
            $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
          else
            $this->pdf->fillCell=array();
      
          if($resetPlan==true)
            $benchmark='';
          else
            $benchmark=$this->formatGetal($row['indexPerf'],2).'%';
          
					//if($jaar>$rapportageJaar-2)
          
					$this->pdf->row(array($datumTxt ,
														$this->formatGetal($row['beginwaarde'],0),
														$this->formatGetal($row['storting']-$row['onttrekking'],0),
														//$this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd']+$row['rente'],0),
                            $this->formatGetal($row['resultaat']-$row['kosten']-$row['opbrengst'],0),
														$this->formatGetal($row['opbrengst'],0),
														$this->formatGetal($row['kosten'],0),
														$this->formatGetal($row['resultaat'],0),
														$this->formatGetal($row['eindwaarde'],0),
                              $benchmark,
														$this->formatGetal($row['procent'],2).'%',
														$this->formatGetal($row['index'],2).'%'));

		                           if(!isset($totaal['beginwaarde']))
                                 $totaal['beginwaarde']=$row['beginwaarde'];
		                           $totaal['eindwaarde'] = $row['eindwaarde'];
		                           $totaal['resultaat'] += $row['resultaat'];
                               $totaal['koersresultaten']+=($row['resultaat']-$row['kosten']-$row['opbrengst']);
		                           $totaal['gerealiseerd'] += $row['gerealiseerd'];
		                           $totaal['ongerealiseerd'] += $row['ongerealiseerd'];
		                           $totaal['opbrengst'] += $row['opbrengst'];
		                           $totaal['kosten'] += $row['kosten'];
		                           $totaal['rente'] += $row['rente'];
		                           $totaal['storting'] += $row['storting'];
                               $totaal['onttrekking'] += $row['onttrekking'];
		                           $totaal['index'] = $row['index'];
				                       $totaal['benchmark'] = $row['benchmarkTotaal'];

        $laatsteJaar=$jaar;
		    $n++;
		    }
		    $this->pdf->fillCell=array();

		 $subData=$periodeWaarden['jaar'][$laatsteJaar];

		 if($verdeling <>'jaar')
		 {
      
       if($resetPlan==true)
       {
         $benchmark = '';
         $benchmarkSub='';

       }
       else
       {
         $benchmark = $this->formatGetal($subData['indexPerf'], 2) . '%';
         
         $benchmarkSub='TS';
       }
		 $this->pdf->ln(1);
		 $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS',$benchmarkSub,'TS','TS');
		 $this->pdf->row(array($laatsteJaar ,
											 $this->formatGetal($subData['beginwaarde'],0),
											 $this->formatGetal($subData['storting']-$subData['onttrekking'],0),
											 //$this->formatGetal($subData['gerealiseerd']+$subData['ongerealiseerd']+$subData['rente'],0),
                       $this->formatGetal($subData['resultaat']-$subData['kosten']-$subData['opbrengst'],0),
											 $this->formatGetal($subData['opbrengst'],0),
											 $this->formatGetal($subData['kosten'],0),
											 $this->formatGetal($subData['resultaat'],0),
											 $this->formatGetal($subData['eindwaarde'],0),
                       $benchmark,
											 $this->formatGetal($subData['procent'],2).'%',
											 $this->formatGetal($subData['index'],2).'%'));


        $this->pdf->ln(3);
		 $this->pdf->CellBorders = array();
		 }
  
     if($resetPlan==true)
     {
       $benchmark = '';
       $benchmarkSub='';
       unset($grafiekData['benchmarkIndex']);
     }
     else
     {
       $benchmark = $this->formatGetal($totaal['benchmark'], 2) . '%';
       $benchmarkSub='UU';
     }
        $this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU',$benchmarkSub,'','UU');

		    $this->pdf->row(array('Samenvatting',
											 $this->formatGetal($totaal['beginwaarde'],0),
											 $this->formatGetal($totaal['storting']-$totaal['onttrekking'],0),
											 $this->formatGetal($totaal['koersresultaten'],0),
											 $this->formatGetal($totaal['opbrengst'],0),
											 $this->formatGetal($totaal['kosten'],0),
											 $this->formatGetal($totaal['resultaat'],0),
											 $this->formatGetal($totaal['eindwaarde'],0),
                       $benchmark,'',
											 $this->formatGetal($totaal['index'],2).'%'));
		   $this->pdf->CellBorders = array();

		  }
		//  listarray($periodeWaarden);exit;
/*
		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY($this->pdf->marge,102)		;//112
		    	$this->pdf->Cell(0, 5, 'Vermogensverdeling', 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		      $this->pdf->SetXY(15,140)		;//112
		      $this->VBarDiagram(270, 30, $barGraph['Index'],$barGraph['Plan']);
		  }
*/
		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(8,109+7);//104
  		  $this->pdf->Cell(0, 5, 'Rendement (cumulatief)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+6)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(270-50, 60, $grafiekData,array($this->pdf->rapport_grafiek_pcolor,$this->pdf->rapport_grafiek_icolor),0,0,6,5,false);//50
        $this->pdf->SetXY($valX, $valY + 80);
		  }
		  $this->pdf->SetXY(8, 155);//165


		$this->pdf->ln(10);
		$this->pdf->SetX(108);


	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);


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

		if($color == null)
			$color=array(23,55,94);


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
    $unit = $lDiag / (count($data)+1);

    if($jaar)
      $unit = $lDiag / 13;

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
			//$this->pdf->Cell($unit,4,date("d-m-Y",db2jul($legendDatum[$i])),0,0,'C');//
      $this->pdf->TextWithRotation($xStart+0.5*$unit,$YDiag+$hDiag+8,date("d-m-Y",db2jul($legendDatum[$i])),30);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($xStart, $yval,$xEind, $yval2,$lineStyle );
      if ($i>0)
      {
        $this->pdf->Rect($xStart-0.5, $yval-0.5, 1, 1 ,'F','',$color);
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
          $this->pdf->Rect($xStart-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
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

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
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
      $numBars=12;

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
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      
      
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
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
     
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
		 if($plan[$datum] <> 0)
		 {
			 $planY= $plan[$datum] * $unit + $YstartGrafiek;
			 $planX= $XstartGrafiek + (1 + $i ) * $vBar ;
			 if($lastPlanY <> 0)
			 {
				 $this->pdf->SetDrawColor(128);
				 $this->pdf->line($lastPlanX, $lastPlanY,$planX, $planY);
				 $this->pdf->SetDrawColor(0);
				 //	 echo "$lastX,$lastPlanY,$xval+.5*$eBaton, $planY <br>\n";
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