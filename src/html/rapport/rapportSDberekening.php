<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/11 05:31:32 $
File Versie					: $Revision: 1.47 $

$Log: rapportSDberekening.php,v $
Revision 1.47  2020/06/11 05:31:32  rvv
*** empty log message ***

Revision 1.46  2020/05/16 15:56:10  rvv
*** empty log message ***

Revision 1.45  2019/11/23 18:45:26  rvv
*** empty log message ***

Revision 1.44  2019/07/03 13:16:19  rvv
*** empty log message ***

Revision 1.43  2019/05/30 05:53:58  rvv
*** empty log message ***

Revision 1.42  2019/05/15 15:33:13  rvv
*** empty log message ***

Revision 1.41  2019/05/11 16:46:29  rvv
*** empty log message ***

Revision 1.40  2018/10/25 05:45:03  rvv
*** empty log message ***

Revision 1.39  2018/09/02 12:01:30  rvv
*** empty log message ***

Revision 1.38  2018/07/21 15:52:22  rvv
*** empty log message ***

Revision 1.37  2018/04/21 17:54:42  rvv
*** empty log message ***

Revision 1.36  2017/10/28 17:59:16  rvv
*** empty log message ***

Revision 1.35  2017/10/25 15:56:31  rvv
*** empty log message ***

Revision 1.34  2017/10/07 16:52:41  rvv
*** empty log message ***

Revision 1.33  2017/10/01 14:31:41  rvv
*** empty log message ***

Revision 1.32  2017/09/25 10:40:36  rvv
*** empty log message ***

Revision 1.31  2017/09/23 18:10:34  rvv
*** empty log message ***

Revision 1.30  2017/09/23 17:41:12  rvv
*** empty log message ***

Revision 1.29  2017/09/13 15:44:03  rvv
*** empty log message ***

Revision 1.28  2017/09/06 16:29:31  rvv
*** empty log message ***

Revision 1.27  2017/09/02 17:15:52  rvv
*** empty log message ***

Revision 1.26  2017/08/09 16:10:13  rvv
*** empty log message ***

Revision 1.25  2017/07/23 13:38:14  rvv
*** empty log message ***

Revision 1.24  2017/07/09 11:56:59  rvv
*** empty log message ***

Revision 1.23  2017/05/26 04:54:20  rvv
*** empty log message ***

Revision 1.22  2017/05/15 09:18:59  rvv
*** empty log message ***

Revision 1.21  2017/04/26 14:37:40  rvv
*** empty log message ***

Revision 1.20  2017/04/19 15:38:47  rvv
*** empty log message ***

Revision 1.19  2017/04/09 10:13:32  rvv
*** empty log message ***

Revision 1.18  2017/03/29 15:56:14  rvv
*** empty log message ***

Revision 1.17  2017/03/22 16:51:26  rvv
*** empty log message ***

Revision 1.16  2017/03/18 20:29:47  rvv
*** empty log message ***

Revision 1.15  2017/02/25 18:01:53  rvv
*** empty log message ***

Revision 1.14  2017/01/15 11:42:51  rvv
*** empty log message ***

Revision 1.13  2016/11/23 13:05:59  rvv
*** empty log message ***

Revision 1.12  2016/09/08 07:01:03  rvv
*** empty log message ***

Revision 1.11  2016/07/31 10:39:32  rvv
*** empty log message ***

Revision 1.10  2016/04/11 06:28:43  rvv
*** empty log message ***

Revision 1.9  2016/04/11 05:55:22  rvv
*** empty log message ***

Revision 1.8  2016/04/10 15:46:58  rvv
*** empty log message ***

Revision 1.7  2016/02/20 15:17:13  rvv
*** empty log message ***

Revision 1.6  2015/12/30 18:58:34  rvv
*** empty log message ***

Revision 1.5  2015/12/02 16:17:27  rvv
*** empty log message ***

Revision 1.4  2015/11/29 13:12:20  rvv
*** empty log message ***

Revision 1.3  2015/11/25 16:45:01  rvv
*** empty log message ***

Revision 1.2  2015/11/01 17:22:44  rvv
*** empty log message ***

Revision 1.1  2015/10/28 16:41:18  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

class rapportSDberekening
{

	function rapportSDberekening($portefeuille,$rapportageDatum,$gebruikStartdatumMeerjarenrendement=1)
	{
	  global $__appvar;
	  $this->db = new DB();
    $this->filterJaarovergang=true;
    $this->consolidatiePortefeuilles=array();
    $this->indexberekening=new indexHerberekening();
    $this->dagenPerFrequentie=array('w'=>7,'2w'=>14,'m'=>30.5);
    $query="SELECT Vermogensbeheerders.SdFrequentie,
    Vermogensbeheerders.SdMethodiek,
    Vermogensbeheerders.SdWaarnemingen,
    Vermogensbeheerders.SdOpbouw,
    Vermogensbeheerders.Vermogensbeheerder,
    Portefeuilles.Portefeuille,
    Portefeuilles.Startdatum,
    date(Portefeuilles.startdatumMeerjarenrendement) as startdatumMeerjarenrendement,
    Portefeuilles.Risicoklasse,
    Portefeuilles.Vermogensbeheerder 
    FROM Vermogensbeheerders 
    JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder 
    WHERE Portefeuilles.Portefeuille='$portefeuille'";
    $this->db->SQL($query);
    $this->db->Query();
    $this->settings=$this->db->nextRecord();
    $startdatumMeerjarenrendementJul=db2jul($this->settings['startdatumMeerjarenrendement']);
//$this->settings['Startdatum']='2014-06-01';

    $query="SELECT count(*) as aantal FROM HistorischePortefeuilleIndex WHERE portefeuille='$portefeuille'";
    $this->db->SQL($query);
    $this->db->Query();
    $data=$this->db->nextRecord();
    if($data['aantal'] > 0)
      $this->settings['gebruikHistorischePortefeuilleIndex']=true;

    if($rapportageDatum=='')
      $rapportageDatum=getLaatsteValutadatum();
    $this->settings['RapportageDatum']=$rapportageDatum;
    if($this->settings['SdFrequentie']=='')
      $this->settings['SdFrequentie']='m';
    if($this->settings['SdMethodiek']=='')
      $this->settings['SdMethodiek']='s';
    if($this->settings['SdWaarnemingen']=='')
      $this->settings['SdWaarnemingen']=36;
//$this->settings['SdWaarnemingen']=3;  
    if($this->settings['SdFrequentie']=='m')
      $this->settings['aantalPerJaar']=12;
    if($this->settings['SdFrequentie']=='w')
      $this->settings['aantalPerJaar']=52;
    if($this->settings['SdFrequentie']=='2w')
      $this->settings['aantalPerJaar']=26;
    
   $this->settings['correctie']=sqrt($this->settings['aantalPerJaar']);
          
   $this->settings['julStartdatum']=db2jul($this->settings['Startdatum']);
   if(($gebruikStartdatumMeerjarenrendement==1  && $startdatumMeerjarenrendementJul > $this->settings['julStartdatum']) ||
      ($gebruikStartdatumMeerjarenrendement==2  && $this->settings['startdatumMeerjarenrendement'] <> '0000-00-00'))
   {
     $this->settings['julStartdatum']=$startdatumMeerjarenrendementJul;
     $this->settings['Startdatum']=date('Y-m-d',$startdatumMeerjarenrendementJul);
   }
   $this->settings['julRapportageDatum']=db2jul($this->settings['RapportageDatum']);
   $this->indexWaarden=array();

	}

  function setStartdatum($startdatum)
  {
    $this->settings['julStartdatum']=db2jul($startdatum);
    $this->settings['Startdatum']=date('Y-m-d',$this->settings['julStartdatum']);
    $this->noTotaal=true;
  }
  
  function addReeks($verdeling='totaal',$fonds='',$verdelingOpDatum=false)
  {
    $this->getReeksen($verdeling,$fonds,$verdelingOpDatum);

    if($_POST['debug']==1)
    {
     //listarray($this->reeksen);
     foreach($this->reeksen as $categorie=>$datumData)
       foreach($datumData as $datum=>$perfData)
         echo $categorie.";".$datum.";".$perfData['perf'].";<br>\n";
    }
  }
  
  function berekenWaarden($verloop=false)
  {
    if($this->settings['SdMethodiek']=='r')
      $stdev=$this->rollingPeriodSD($this->reeksen);
    else
      $stdev=$this->heleReeksSD($this->reeksen);  

    if($verloop==false)
       $this->uitvoer=$this->getLast($stdev);
    else    
       $this->uitvoer;
  }
  
  
  function getUitvoer()
  {
    return $this->uitvoer;     
  }
  
  function getLast($stdev)
  {
    $last=array();
    foreach($stdev as $catgorie=>$stdevData)
    {
      $tmp = array_pop($stdevData); 
      $last[$catgorie]=$tmp['stdev'];
      //foreach($stdevData as $datum=>$velden)
     // $tmp[$catgorie]=$velden['stdev'];
    }

    return $last;
  }
  
  function double_standard_deviation($aValues)
  {
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= count($aValues);
    $first=sqrt($fVariance);
    $tmp=array();
    foreach ($aValues as $i)
    {
      if($i < 2*$first && $i > -2*$first)
        $tmp[]=$i;
    }   
    return standard_deviation($tmp);
  }
  
  function heleReeksSD($reeksen)
  {
     $this->standaardDeviatieReeksen=array();

     foreach($reeksen as $cat=>$catData)
     {  
       if($cat=='afm')
       {
         $this->standaardDeviatieReeksen[$cat]=$catData;
       }
       else
       {
         $buffer=array();
     
         foreach($catData as $tmp)
         {
           if(!isset($eersteDatum))
             $eersteDatum=$tmp['datum'];
           if($tmp['uitsluitenStdev'])
             continue;

           $buffer[]=$tmp['perf'];

           $datum=$tmp['datum'];
           //$tmp=array('stdev'=>standard_deviation($buffer)*$this->settings['correctie']);
           $tmp=array('eersteMeting'=>$eersteDatum,
                      'laatsteMeting'=>$datum,
                      'stdev'=>standard_deviation($buffer)*$this->settings['correctie']);
           $this->standaardDeviatieReeksen[$cat][$datum]=$tmp;
         }
       }
     }
     return $this->standaardDeviatieReeksen;
  }

 function rollingPeriodSD($reeksen)
 {
    $this->standaardDeviatieReeksen=array();
    $catId=-1;
    foreach($reeksen as $cat=>$catData)
    {
      if($cat=='afm')
      {
        $this->standaardDeviatieReeksen[$cat]=$catData;
      }
      else
      {
        $buffer=array();
        $bufferIndex=array();
        $bufferStartIndex=0;
        $indexCounter=0;
        $catId=$catId+2;
        $catData=array_values($catData);
        $tmp=array();
        foreach($catData as $index=>$waarden)
        {
          if ($waarden['uitsluitenStdev'])
          {
            continue;
          }
          $tmp[]=$waarden;
        }
        $catData=$tmp;
        foreach($catData as $index=>$waarden)
        {

          //echo $cat." ".$waarden['datum']." ".count($buffer)." -> ".$waarden['perf']."<br>\n";
          $buffer[$index]=$waarden['perf'];
          $indexCounter++;
          if(count($buffer)==$this->settings['SdWaarnemingen'])
          {
           $meetDatum=$catData[$bufferStartIndex+($this->settings['SdWaarnemingen']-1)]['datum'];
           $tmp=array('eersteMeting'=>$catData[$bufferStartIndex]['datum'],
                    'laatsteMeting'=>$meetDatum,
                    'stdev'=>standard_deviation($buffer)*$this->settings['correctie']);
           $this->standaardDeviatieReeksen[$cat][$meetDatum]=$tmp;
           unset($buffer[$bufferStartIndex]);
           unset($bufferIndex[$bufferStartIndex]);
           $bufferStartIndex++;
          }
        }
      }
    }

    return $this->standaardDeviatieReeksen;
 }

  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$fonds."' AND Datum <= '".$datum."' ORDER BY Datum DESC limit 1 ";
    $db->SQL($query);
    $koers = $db->lookupRecord();
    return $koers['Koers'];
  }

  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
//echo "$fonds<br>\n";
    if(is_array($fonds))
    {
      $verdeling=$fonds;
    }
    else
    {
     $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
      $DB->SQL($query);
      $DB->Query();
      $verdeling=array();
      while($data=$DB->nextRecord())
        $verdeling[$data['fonds']]=$data['percentage'];

      if(count($verdeling)==0)
        $verdeling[$fonds]=100;
    }

    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      //$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	//$DB->SQL($query);
      //$janKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	    $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      //$perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      //$perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfPeriode=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);

      //$perf=$perfJaar-$perfVoorPeriode;
  //    echo "$van->$tot $fonds $perfPeriode*$percentage  | $perfPeriode=(".$eindKoers['Koers']." - ".$startKoers['Koers'].") / (".$startKoers['Koers'].");<br>\n";
      $totalPerf+=($perfPeriode*$percentage);
    }
    //listarray($verdeling);
   // echo "totaal $totalPerf $van,$tot<br>\n<br>\n";flush();

    return $totalPerf;
  }

  function getHistorischePortefeuilleIndexWaarde($start,$stop,$portefeuille)
  {
    $indexData=false;
    $dagen=((db2jul($stop)-db2jul($start))/86400);

    $periode='';
    if($dagen>25 && $dagen < 35)
      $periode='m';
    elseif($dagen>25 && $dagen < 100)
      $periode='k';
    elseif($dagen<8)
      $periode='w';
    $query = "SELECT indexWaarde as performance, indexWaarde as performanceSquared,Periode FROM HistorischePortefeuilleIndex WHERE Portefeuille='$portefeuille' AND Datum='$stop' AND categorie='Totaal'";
    $this->db->SQL($query);
    $this->db->Query();
    if($this->db->records())
    {
      while($indexData = $this->db->nextRecord())
      {
        if ($indexData['Periode'] == $periode)
        {
          return $indexData;
        }
        elseif ($indexData['Periode'] == 'm' && $periode == 'w')
        {
          $indexData['performanceSquared'] = ($indexData['performanceSquared'] / 4.3333) * pow(4.3333, 0.5);
          $indexData['uitsluitenStdev'] = true;
          $indexData['performance'] = $indexData['performance'] / 4.3333;
    
          return $indexData;
        }
        elseif ($indexData['Periode'] == 'k' && $periode == 'w')
        {
          $indexData['performanceSquared'] = ($indexData['performanceSquared'] / 13) * pow(13, 0.5);
          $indexData['uitsluitenStdev'] = true;
          $indexData['performance'] = $indexData['performance'] / 13;
    
          return $indexData;
        }
      }
    }

    if($periode=='w')
    {
      $tmp=db2jul($stop);
      $n=ceil(date('n',$tmp)/3);
      $maand=sprintf('%02d',$n*3);
      $laatsteDag=array('03'=>'31','06'=>'30','09'=>'30','12'=>'31');
      $stop2=date("Y",$tmp).'-'.$maand.'-'.$laatsteDag[$maand];
      
      $query = "SELECT indexWaarde/13 as performance, indexWaarde as performanceSquared FROM HistorischePortefeuilleIndex WHERE Portefeuille='$portefeuille' AND Datum='$stop2' AND categorie='Totaal' AND Periode='k'";
      $this->db->SQL($query);
      $this->db->Query();
      if($this->db->records())
      {
        $indexData = $this->db->nextRecord();
        $indexData['performanceSquared']=($indexData['performanceSquared']/13)*pow(13,0.5);
        $indexData['uitsluitenStdev']=true;
        return $indexData;
      }
      $n=ceil(date('n',$tmp));
      $stop2=date("Y-m-d",mktime(0,0,0,($n+1),0,date('Y',$tmp)));
      $query = "SELECT indexWaarde/4.3333 as performance, indexWaarde as performanceSquared FROM HistorischePortefeuilleIndex WHERE Portefeuille='$portefeuille' AND Datum='$stop2' AND categorie='Totaal' AND Periode='m'";
      $this->db->SQL($query);
      $this->db->Query();
      if($this->db->records())
      {
        $indexData = $this->db->nextRecord();
        $indexData['performanceSquared']=($indexData['performanceSquared']/4.3333)*pow(4.3333,0.5);
        $indexData['uitsluitenStdev']=true;
        return $indexData;
      }

    }
    return $indexData;
  }

  function getReeksen($verdeling='totaal',$fonds='',$verdelingOpDatum=false)
  {
     global $__appvar;
     $db=new DB();
     $perioden=$this->getPeriodeInstellingen();
     if(!is_array($this->reeksen))
       $this->reeksen=array();

     if(!isset($this->reeksen['totaal']) && !isset($this->noTotaal))
     {
       foreach($perioden as $periode)
       {
         /*
         $startJaar=substr($periode['start'],0,4);
         if(substr($periode['start'],5,5)=='12-31')
         {

          $periode['start']=($startJaar+1)."-01-01";
         }
         */
         if($this->settings['gebruikHistorischePortefeuilleIndex']==true)
         {
           $indexData=$this->getHistorischePortefeuilleIndexWaarde($periode['start'],$periode['stop'],$this->settings['Portefeuille']);
           if($indexData==false)
             $indexData=$this->indexberekening->BerekenMutaties($periode['start'],$periode['stop'],$this->settings['Portefeuille']);
         }
         else
           $indexData=$this->indexberekening->BerekenMutaties($periode['start'],$periode['stop'],$this->settings['Portefeuille']);

         if($indexData['uitsluitenStdev'])
           $this->reeksen['totaal'][$periode['stop']]['uitsluitenStdev']=$indexData['uitsluitenStdev'];
         $this->reeksen['totaal'][$periode['stop']]['perf']=$indexData['performance'];
         $this->reeksen['totaal'][$periode['stop']]['start']=$periode['start'];
         $this->reeksen['totaal'][$periode['stop']]['datum']=$periode['stop'];
       }
     }
     if(substr($verdeling,0,9)=='benchmark')
     {
       $fondsArray=array();
       if(is_array($fonds) && count($fonds)>0)
         $fondsArray=$fonds;

        if(count($fondsArray)>0 || $fonds != '' || $verdelingOpDatum==true)
	      {
	        foreach($perioden as $periode)
          {
            if(count($fondsArray) > 0 || ($verdelingOpDatum==true && count($this->consolidatiePortefeuilles)>0))
            {
              if($verdelingOpDatum==true)
              {
                $benchmarkVerdeling=array();
                $herIndex=false;
                foreach($this->consolidatiePortefeuilles as $cPortefeuille)
                {
                  $query="SELECT aandeel*100 as aandeel,datum FROM tempVerdeling WHERE hoofdPortefeuille='".$this->settings['Portefeuille']."' AND  portefeuille='$cPortefeuille' AND datum <='" .$periode['stop']. "' ORDER BY Datum desc limit 1";
                  $db->SQL($query);
                  $db->Query();
                  $aandeel = $db->lookupRecord();
                  if($aandeel['aandeel']==0)
                  {
                    $query="SELECT aandeel*100 as aandeel,datum FROM tempVerdeling WHERE hoofdPortefeuille='".$this->settings['Portefeuille']."' AND  portefeuille='$cPortefeuille' AND datum >='" .$periode['stop']. "' ORDER BY Datum limit 1";
                    $db->SQL($query);
                    $db->Query();
                    $aandeel = $db->lookupRecord();
                    //if($aandeel['aandeel']==0)
                   //   $aandeel['aandeel'] = 101;
                  }
                  if($aandeel['aandeel'] > 100)
                    $herIndex=true;
                  /*
                  $query = "SELECT SpecifiekeIndex FROM Portefeuilles WHERE Portefeuille='" . $cPortefeuille . "'";
                  $db->SQL($query);
                  $db->Query();
                  $index = $db->lookupRecord();
                  */
                  $index['SpecifiekeIndex']=getSpecifiekeIndex($cPortefeuille,$periode['stop']);
                  if(isset($benchmarkVerdeling[$index['SpecifiekeIndex']]))
                    $benchmarkVerdeling[$index['SpecifiekeIndex']] += $aandeel['aandeel'];
                  else
                    $benchmarkVerdeling[$index['SpecifiekeIndex']] = $aandeel['aandeel'];
                }

                $benchmarkFonds=$benchmarkVerdeling;
                if($herIndex==true || array_sum($benchmarkVerdeling) <> 100)
                {
                  $sum=0;
                  foreach($benchmarkFonds as $fonds=>$percentage)
                    $sum+=abs($percentage);
                  foreach($benchmarkFonds as $fonds=>$percentage)
                    $benchmarkFonds[$fonds]=abs($percentage)/$sum*100;
                  // echo array_sum($benchmarkFonds);
                }
                $fondsVerdeling=array();
                foreach($benchmarkFonds as $fondsDeel=>$aandeel)
                {

                  $query = "SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fondsDeel'";
                  $db->SQL($query);
                  $db->Query();
                  if($db->records())
                  {
                    while ($data = $db->nextRecord())
                    {
                      // echo  $data['percentage']."*$aandeel/100 <br>\n";
                      // listarray($data);
                      if (isset($fondsVerdeling[$data['fonds']]))
                      {
                        $fondsVerdeling[$data['fonds']] += $data['percentage'] * $aandeel / 100;
                      }
                      else
                      {
                        $fondsVerdeling[$data['fonds']] = $data['percentage'] * $aandeel / 100;
                      }
                    }
                  }
                  else
                    $fondsVerdeling[$fondsDeel] = $aandeel;
                }

                $fonds=$fondsVerdeling;
              }
              else
              {
                $fonds=$fondsArray;
              }
            }
            elseif($verdeling=='benchmarkTot')
            {
              $fonds = getSpecifiekeIndex($this->settings['Portefeuille'], $periode['stop']);
            }
//echo "$fonds ".$this->settings['Portefeuille']." ".$periode['stop']."<br>\n";
            $this->reeksen[$verdeling][$periode['stop']]['perf']=$this->fondsPerf($fonds,$periode['start'],$periode['stop']);
            //listarray($fonds);
            //echo $periode['start']."->".$periode['stop']."=".$this->reeksen[$verdeling][$periode['stop']]['perf']."<br>\n";
            $this->reeksen[$verdeling][$periode['stop']]['start']=$periode['start'];
            $this->reeksen[$verdeling][$periode['stop']]['datum']=$periode['stop'];  
          }
	      }
     }
     elseif($verdeling=='afm')
     {
       foreach($perioden as $periode)
       {
         $query="SELECT * FROM TijdelijkeRapportage 
                 WHERE add_date > (now() - interval 10 minute) AND portefeuille='".$this->settings['Portefeuille']."' AND rapportageDatum='".$periode['stop']."'
                 ".$__appvar['TijdelijkeRapportageMaakUniek'];
         if($db->QRecords($query)==0)
         {
           if(substr($periode['stop'],5,5)=='01-01')
             $startJaar=true;
           else
             $startJaar=false;  

           vulTijdelijkeTabel(berekenPortefeuilleWaarde($this->settings['Portefeuille'],$periode['stop'],$startJaar),$this->settings['Portefeuille'],$periode['stop']);
         }
         $afm=AFMstd($this->settings['Portefeuille'],$periode['stop']);
         $this->reeksen[$verdeling][$periode['stop']]['stdev']=$afm['std'];
         $this->reeksen[$verdeling][$periode['stop']]['datum']=$periode['stop'];  
       }
     }
     elseif($verdeling!='totaal')
     {
       if(!isset( $this->reeksen[$verdeling]))
       {
         $categorieVerdeling=$this->getCategorieVerdeling($verdeling);
         foreach($perioden as $periode)
         {
           foreach($categorieVerdeling as $categorie=>$fondsData)
           {
             $indexData=$this->fondsPerformance($fondsData,$periode['start'],$periode['stop'],$categorie);
             $this->reeksen[$categorie][$periode['stop']]['perf']=$indexData['procent'];
             $this->reeksen[$categorie][$periode['stop']]['aandeelOpTotaal']=$indexData['aandeelOpTotaal'];
             $this->reeksen[$categorie][$periode['stop']]['resultaat']=$indexData['resultaat'];
             $this->reeksen[$categorie][$periode['stop']]['gemWaarde']=$indexData['gemWaarde'];
             $this->reeksen[$categorie][$periode['stop']]['totaalGemWaarde']=$indexData['totaalGemWaarde'];

             $this->reeksen[$categorie][$periode['stop']]['datum']=$periode['stop'];
           }
         }
       }
     }
     
     return $this->reeksen;
  }
  
  function berekenMaxDrawdown($reeks='totaal')
  {
    $buffer=array();
    $output=array();
    $perfCumulatief=0;
    foreach($this->reeksen[$reeks] as $datum=>$waarden)
    {
      if(!isset($start))
        $start=$datum;
      $perfCumulatief=((1+$perfCumulatief/100)*(1+$waarden['perf']/100)-1)*100;
      //echo "$datum|$perfCumulatief|<br>\n";
      $buffer[]=$perfCumulatief;
      $maxDrawdown=$this->maxDrawdown2($buffer);
      $output[]=array('eersteMeting'=>$start,'laatsteMeting'=>$datum,'maxDrawdown'=>$maxDrawdown);
    }
    return $output;
  }
  
  function riskAnalyze($eerste='totaal',$tweede='benchmark',$verloop=false,$allowMissing=false)
  {
    $standaarddeviatie=$this->uitvoer[$eerste];
    $standaarddeviatieBenchmark=$this->uitvoer[$tweede];
    $standaarddeviatieAFM=$this->uitvoer['afm'];
    
    $query="SELECT verwachtRendement FROM Risicoklassen 
    WHERE Vermogensbeheerder='".$this->settings['Vermogensbeheerder']."' AND Risicoklasse='".$this->settings['Risicoklasse']."'";
    $db=new DB();
    $db->SQL($query);
    $verwachtRendement=$db->lookupRecord();

    $this->indexWaarden=array();

    $index=100;
   // echo "<br>\ndatum;perf;benchmark<br>\n";
    foreach($this->reeksen[$eerste] as $datum=>$waarden)
    {
      if(isset($this->reeksen[$tweede][$datum]) || $allowMissing==true)
      {
        $index=($index*(100+$waarden['perf'])/100);
        if($this->reeksen[$tweede][$datum]['uitsluitenStdev'])
          continue;
        if($waarden['uitsluitenStdev'])
          continue;

          $benchmarkPerf=$this->reeksen[$tweede][$datum]['perf'];
          $perf=$waarden['perf'];

        $this->indexWaarden[]=array('datum'=>$datum,
                              'performance'=>$perf,
                              'performanceCorrected'=>$waarden['perf'],
                              'index'=>$index,
                              'specifiekeIndexPerformance'=>$benchmarkPerf);
      }
      
    }
  //  listarray( $this->indexWaarden);
    if($this->settings['SdMethodiek']=='r' || $verloop==true) // && $verloop <> 2
    {
      $buffer=array();
      $bufferBenchmark=array();
      $bufferIndex=array();
      $bufferOverPerfSquare=array();
      $bufferOverPerf=array();
      $bufferStartIndex=0;
      $indexCounter=0;
      $standaardDeviatieReeksen=array();
  //    echo "<table border=1><tr><td>datum</td><td>(som overperf)/((stdev overperf)*correctie)</td><td>= sharpe ratio</td><td>overperf array</td> <td>stdev perf</td></tr>";
      foreach($this->indexWaarden as $index=>$waarden)
      {
        $buffer[$index]=$waarden['performance'];
        $bufferDatum[$index]=$waarden['datum'];
        $bufferIndex[$index]=$waarden['index'];
        $bufferBenchmark[$index]=$waarden['specifiekeIndexPerformance'];
        $bufferOverPerfSquare[$index]=pow(($waarden['performance']-$waarden['specifiekeIndexPerformance']),2);
        $bufferOverPerf[$index]=$waarden['performance']-$waarden['specifiekeIndexPerformance'];
        $bufferOverPerfFixedArray[$index]=$waarden['performance'];//-(1/$this->settings['aantalPerJaar']);
        $bufferOverPerfCorrectedArray[$index]=$waarden['performanceCorrected'];//-(1/$this->settings['aantalPerJaar']);
        $indexCounter++;
        if(count($buffer)>=$this->settings['SdWaarnemingen'])
        {
          $perfStapeling=100;
          //$perfStapelingArray=array();
          $benchmarkStapeling=100;
          for($i=$bufferStartIndex;$i<$indexCounter;$i++)
            $perfStapeling=($perfStapeling*(100+$buffer[$i])/100);
          for($i=$bufferStartIndex;$i<$indexCounter;$i++)
            $benchmarkStapeling=($benchmarkStapeling*(100+$bufferBenchmark[$i])/100);
            
        // echo $waarden['datum']." ($bufferStartIndex -> $indexCounter) ".$perfStapeling."/".($this->settings['SdWaarnemingen']/$this->settings['aantalPerJaar'])."<br>\n";
         //      listarray($buffer);      
         $overPerfFixedStd=round(standard_deviation($bufferOverPerfFixedArray)*$this->settings['correctie'],8);
         $overPerfStd=round(standard_deviation($bufferOverPerf)*$this->settings['correctie'],8);
         $maxDrawdown=$this->maxDrawdown($bufferIndex);
         $maxDrawdown2=$this->maxDrawdown2($bufferIndex);
         
         $standaarddeviatie=standard_deviation($buffer)*$this->settings['correctie'];
       //   echo "<tr><td>".$waarden['datum']."</td><td>".round(array_sum($bufferOverPerfFixedArray),2)." / (".round($overPerfFixedStd/$this->settings['correctie'],2)." * ".round($this->settings['correctie'],2).")</td><td>".round(array_sum($bufferOverPerfFixedArray)/$overPerfFixedStd,2)."</td><td> ";
      //  listarray($bufferOverPerfFixedArray);
       //   echo  " </td><td> ".round($standaarddeviatie,2)."</td></tr>\n";


         $VaR=100+$verwachtRendement['verwachtRendement']-(2*$standaarddeviatie);
  //echo $waarden['datum']." $eerste ".(array_sum($bufferOverPerfCorrectedArray)/$overPerfFixedStd)."=".array_sum($bufferOverPerfCorrectedArray)."/$overPerfFixedStd <br>\n";
          if(isset($bufferDatum[$bufferStartIndex+($this->settings['SdWaarnemingen']-1)]))
            $laatsteMeting=$bufferDatum[$bufferStartIndex+($this->settings['SdWaarnemingen']-1)];
          else
            $laatsteMeting=$bufferDatum[$bufferStartIndex];
         $tmp=array('eersteMeting'=>$bufferDatum[$bufferStartIndex],
                    'laatsteMeting'=>$laatsteMeting,
                    'standaarddeviatie'=>$standaarddeviatie,
                    'jaarPerf'=>($perfStapeling-100)/($this->settings['SdWaarnemingen']/$this->settings['aantalPerJaar']),
                    'jaarPerfBenchmark'=>($benchmarkStapeling-100)/($this->settings['SdWaarnemingen']/$this->settings['aantalPerJaar']),
                    'valueAtRisk'=>$VaR,
                    'standaarddeviatieBenchmark'=>standard_deviation($bufferBenchmark)*$this->settings['correctie'],
                    'maxDrawdown'=>$maxDrawdown,
                    'maxDrawdown2'=>$maxDrawdown2,
                    'sharpeRatio'=>array_sum($bufferOverPerfCorrectedArray)/$overPerfFixedStd,
                    'trackingError'=>pow(array_sum($bufferOverPerfSquare)/count($bufferOverPerfSquare),0.5),
                    'informatieratio'=>array_sum($bufferOverPerf)/$overPerfStd);

//echo "$bufferStartIndex <br>\n";
          /*
          if($bufferStartIndex > 53)
          {
         //   listarray($bufferOverPerfCorrectedArray);
         //   echo $tmp['informatieratio'] . "=array_sum($bufferOverPerfCorrectedArray)/$overPerfFixedStd;<br>\n";
          }
*/
         if(isset($this->standaardDeviatieReeksen['afm'][$waarden['datum']]['stdev']))        
           $tmp['standaarddeviatieAFM']=$this->standaardDeviatieReeksen['afm'][$waarden['datum']]['stdev']; 
         if(isset($this->standaardDeviatieReeksen['bechmark'][$waarden['datum']]['stdev']))        
           $tmp['standaarddeviatieBenchmark']=$this->standaardDeviatieReeksen['bechmark'][$waarden['datum']]['stdev'];

         if($eerste=='benchmark')
         {
           unset($tmp['standaarddeviatieBenchmark']);
           unset($tmp['jaarPerfBenchmark']);
           unset($tmp['trackingError']);
           unset($tmp['informatieratio']);
         }
         $standaardDeviatieReeksen[]=$tmp;
         if($this->settings['SdMethodiek']=='r')
         {
           unset($bufferDatum[$bufferStartIndex]);
           unset($buffer[$bufferStartIndex]);
           unset($bufferOverPerfCorrectedArray[$bufferStartIndex]);
           unset($bufferBenchmark[$bufferStartIndex]);
           unset($bufferOverPerfSquare[$bufferStartIndex]);
           unset($bufferOverPerf[$bufferStartIndex]);
           unset($bufferOverPerfFixedArray[$bufferStartIndex]);
         }
         $bufferStartIndex++;
        }
      }

      if($verloop==true)
        return $standaardDeviatieReeksen;
      else
        return $standaardDeviatieReeksen[count($standaardDeviatieReeksen)-1];  
    }
  
    $perfCumArray=array();
    $overPerfSquareArray=array();
    $overPerfFixedArray=array();
    $overPerfArray=array();
    foreach ($this->indexWaarden as $id=>$waarden)
    {
      $overPerfSquareArray[$waarden['datum']]=pow(($waarden['performance']-$waarden['specifiekeIndexPerformance']),2);
      $indexModelArray[$waarden['datum']]=100+$waarden['specifiekeIndexPerformance'];
      $perfCumArray[]=$waarden['index'];
      $overPerfArray[$waarden['datum']]=$waarden['performance']-$waarden['specifiekeIndexPerformance'];
      $overPerfFixedArray[$waarden['datum']]=$waarden['performance']-(1/$this->settings['aantalPerJaar']);
      $indexArray[$waarden['datum']]=100+$waarden['performance'];
    }
    $maxDrawdown=$this->maxDrawdown($perfCumArray);
    $maxDrawdown2=$this->maxDrawdown2($perfCumArray);
    //$portPerfAvg=$portPerfAvg/count($this->indexWaarden);
    //$modelPerfAvg=$modelPerfAvg/count($this->indexWaarden);
    //$overPerfAvg=$overPerf/count($this->indexWaarden);
    $trackingError=pow(array_sum($overPerfSquareArray)/count($overPerfSquareArray),0.5);

    $overPerfStd=standard_deviation($overPerfArray)*$this->settings['correctie'];
    $overPerfFixedStd=standard_deviation($overPerfFixedArray)*$this->settings['correctie'];

    $sharpeRatio=array_sum($overPerfFixedArray)/$overPerfFixedStd;
    $informatieratio=array_sum($overPerfArray)/$overPerfStd;
    $VaR=100+$verwachtRendement['verwachtRendement']-(2*$standaarddeviatie);

    $data=array('standaarddeviatie'=>$standaarddeviatie,
                'standaarddeviatieBenchmark'=>$standaarddeviatieBenchmark,
                'standaarddeviatieAFM'=>$standaarddeviatieAFM,
                'valueAtRisk'=>$VaR,
                'maxDrawdown'=>$maxDrawdown,
                'maxDrawdown2'=>$maxDrawdown2,
                'trackingError'=>$trackingError,
                'sharpeRatio'=>$sharpeRatio,
                'informatieratio'=>$informatieratio);
    if($eerste=='benchmark')
    {
      unset($data['standaarddeviatieBenchmark']);
      unset($data['jaarPerfBenchmark']);
      unset($data['trackingError']);
      unset($data['informatieratio']);
    }
    return $data;          
    
  }
  
  function maxDrawdown($perfCumArray)
  {// listarray($perfCumArray);
    $maxDrawdownArray=array();
    $aantal=count($perfCumArray)-1;
    foreach($perfCumArray as $index=>$waarde)
    {
      $min=1000;
      $max=100;
      for($i=0;$i<=$index;$i++)
      {
        if($perfCumArray[$i] > $max)
          $max=$perfCumArray[$i];
      }
      for($i=$index;$i<=$aantal;$i++)
      {
       
        if($perfCumArray[$i] < $min)
          $min=$perfCumArray[$i];
      }
      $maxDrawdownArray[$index]=($max-$min)/($max/100);
    }
   // listarray($maxDrawdownArray);
    $maxDrawdown=max($maxDrawdownArray);
    return $maxDrawdown;  
  }
  
  function maxDrawdown2($perfCumArray)
  {
    $maxWaarde=max($perfCumArray);
    $drawDown=$perfCumArray[count($perfCumArray)-1]-$maxWaarde;
    return $drawDown;
  }
     
  function getPeriodeInstellingen()
  {
    
    $maximaleDagen=($this->settings['julRapportageDatum']-$this->settings['julStartdatum'])/86400;

    $MinimaalDagenPerPeriode=0;
    $periode=array();
    if($this->settings['SdOpbouw']==1)
    {
      $MinimaalDagenPerPeriode=$maximaleDagen/$this->settings['SdWaarnemingen'];
      if($MinimaalDagenPerPeriode<$this->dagenPerFrequentie[$this->settings['SdFrequentie']])
      {
        $nieuweFrequentie='';
        foreach($this->dagenPerFrequentie as $frequentie=>$dagen)
        {
          if($MinimaalDagenPerPeriode>$dagen && $dagen <= $this->dagenPerFrequentie[$this->settings['SdFrequentie']])
            $nieuweFrequentie=$frequentie;
        }
        if($nieuweFrequentie=='')
        {
          //echo "Te korte periode voor berekening. ($maximaleDagen dagen)";
          return $periode;
        }  
        else
        {
          //echo "Frequentie aangepast van ".$this->settings['SdFrequentie']." naar $nieuweFrequentie<br>\n";
          $this->settings['SdFrequentie']=$nieuweFrequentie;
        }  
      }
    }
    if($this->settings['SdFrequentie']=='m')
      $periode=$this->indexberekening->getMaanden($this->settings['julStartdatum'],$this->settings['julRapportageDatum']);
    elseif($this->settings['SdFrequentie']=='2w')
      $periode=$this->indexberekening->getHalveMaanden($this->settings['julStartdatum'],$this->settings['julRapportageDatum']);
    elseif($this->settings['SdFrequentie']=='w')
      $periode=$this->indexberekening->getWeken($this->settings['julStartdatum'],$this->settings['julRapportageDatum']);
    elseif($this->settings['SdFrequentie']=='wv')
      $periode=$this->indexberekening->getWeken($this->settings['julStartdatum'],$this->settings['julRapportageDatum'],2);

    foreach($periode as $index=>$tmp)
    {
      if(substr($tmp['start'],5,5)=='12-31')
      {
        $jaar=substr($tmp['start'],0,4);
        $periode[$index]['start']=($jaar+1)."-01-01";
      }
      $startJaar=substr($periode[$index]['start'],0,4);
      $eindJaar=substr($tmp['stop'],0,4);
      if($this->filterJaarovergang==true && $startJaar <> $eindJaar)
        unset($periode[$index]);
    }

    $periode=array_values($periode);
    return $periode;
  }
  
  function getCategorieVerdeling($verdeling)
  {
	  global $__appvar;
 		$DB=new DB();
    $this->categorien=array('totaal'=>'Totaal');
    
    if($verdeling=='hoofdCategorie')
    {
      $categorieFilter='hoofdCategorie';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }
    elseif($verdeling=='totaal')  
    {  
      $categorieFilter='geen';
    }
    elseif($verdeling=='sector')  
    {
      $categorieFilter='Beleggingssectoren';
      $join="LEFT JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector";
      $selectOmschrijving=',Beleggingssectoren.Omschrijving';
    }
    elseif($verdeling=='afmCategorie')  
    {
      $categorieFilter='afmCategorien';
      $join="LEFT JOIN afmCategorien ON KeuzePerVermogensbeheerder.waarde = afmCategorien.AfmCategorie";
      $selectOmschrijving=',afmCategorien.Omschrijving';
    }
    else
    { 
      $categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }

    $query="SELECT waarde $selectOmschrijving FROM KeuzePerVermogensbeheerder $join
    WHERE categorie='$categorieFilter' AND 
    Vermogensbeheerder='".$this->settings['Vermogensbeheerder']."'
    ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query); 
    $DB->Query();
    $tmp=array();
    while($data=$DB->nextRecord())
    {
      $tmp[$data['waarde']]=array('categorie'=>$data['waarde'],'omschrijving'=>$data['Omschrijving']);
    }
    $perHoofdcategorie=$tmp;
    $perRegio=$tmp;
    $perSector=$tmp;
    $perCategorie=$tmp;  
    $perAfmCategorie=$tmp;  

    if($categorieFilter=='Beleggingscategorien')
    {
    $query="SELECT
    Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving
FROM
Beleggingscategorien
INNER JOIN ZorgplichtPerBeleggingscategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN ZorgplichtPerPortefeuille ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht
WHERE 
ZorgplichtPerPortefeuille.Portefeuille = '".$this->settings['Portefeuille']."' AND ZorgplichtPerPortefeuille.extra=0 AND
ZorgplichtPerPortefeuille.norm > 0
ORDER BY Beleggingscategorien.Afdrukvolgorde";

    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen']=array();
      $perCategorie[$data['Beleggingscategorie']]['fondsValuta']=array();
    } 
    }

		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
BeleggingscategoriePerFonds.afmCategorie,
afmCategorien.Omschrijving as afmCategorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
BeleggingssectorPerFonds.Beleggingssector,
Beleggingssectoren.Omschrijving as sectorOmschrijving,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->settings['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join afmCategorien ON BeleggingscategoriePerFonds.afmCategorie = afmCategorien.afmCategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->settings['Vermogensbeheerder']."'
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->settings['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->settings['Portefeuille']."'  AND
Rekeningmutaties.Boekdatum >= '".substr($this->settings['Startdatum'],0,4)."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->settings['RapportageDatum']."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Beleggingssectoren.Afdrukvolgorde,Fondsen.Omschrijving ";

			$DB->SQL($query); 
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    if($data['Hoofdcategorie']=='')
          $data['Hoofdcategorie']='Geen H-cat';

 		  if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];  
        }
        else
        {  
          $data['Beleggingssector']='Geen sector'; 
          $data['sectorOmschrijving']=$data['Geen sector'];   
        }
 		  }

        
        
        if($data['Beleggingscategorie']=='')
          $data['Beleggingscategorie']='Geen cat';     
                            
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
        $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
		    $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
		    $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		    $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
        $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
		    $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
        $perAfmCategorie[$data['afmCategorie']]['omschrijving']=$data['afmCategorieOmschrijving'];
		    $perAfmCategorie[$data['afmCategorie']]['fondsen'][]=$data['Fonds'];
		    $perAfmCategorie[$data['afmCategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
		    $perAfmCategorie[$data['afmCategorie']]['fondsValuta'][]=$data['Valuta'];
		    $perAfmCategorie[$data['afmCategorie']]['categorie']=$data['afmCategorie'];
        $alleData['fondsen'][]=$data['Fonds'];

		  }

		$query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->settings['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->settings['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->settings['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->settings['Portefeuille']."'  AND
Rekeningmutaties.Boekdatum >= '".substr($this->settings['Startdatum'],0,4)."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->settings['RapportageDatum']."'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
		  if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='Geen H-cat';
 		  if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];  
        }
        else
        {  
          $data['Beleggingssector']='Geen sector'; 
          $data['sectorOmschrijving']=$data['Geen sector'];   
        }
 		  }
      if($data['Beleggingscategorie']=='')
        $data['Beleggingscategorie']='Geen cat';  
		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
		  $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['rekeningen'][]=$data['rekening'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $data['afmCategorie']='01liquiditeiten';
      $data['afmCategorieOmschrijving']='Liquiditeiten';
		  $perAfmCategorie[$data['afmCategorie']]['omschrijving']=$data['afmCategorieOmschrijving'];
		  $perAfmCategorie[$data['afmCategorie']]['rekeningen'][]=$data['rekening'];
		  $perAfmCategorie[$data['afmCategorie']]['categorie']=$data['afmCategorie'];      
      
		  $alleData['rekeningen'][]=$data['rekening'];
	  }

 
    if($verdeling=='afmCategorie')  
      $categorien=$perAfmCategorie;
    elseif($verdeling=='hoofdCategorie')
      $categorien=$perHoofdcategorie;
    elseif($verdeling=='totaal')  
      $categorien=array();
    elseif($verdeling=='sector')  
      $categorien=$perSector;
    else
      $categorien=$perCategorie;
      
    $this->categorien=$categorien;
      
    return $categorien;
      
   }
   
   
  function AFMstd($portefeuille,$datum,$portefeuilleWaarden='',$filter='',$debug=false)
  {
    $db=new DB();
  
    $totaalWaarde=array();
    $afmVerdeling=array();
    if(!is_array($portefeuilleWaarden))
    {
      $query="SELECT SUM(actuelePortefeuilleWaardeEuro) as waarde FROM TijdelijkeRapportage WHERE Portefeuille='$portefeuille' AND rapportageDatum='$datum'";
      $db->SQL($query); 
      $totaal=$db->lookupRecord();
      if($totaal['waarde'] <> 0)
        $totaalWaarde['totaal']=$totaal['waarde'];
      else
        $totaalWaarde['totaal']=1;

      $filterCategorie=',1'; 
      if($filter!='')
        $filterCategorie=',TijdelijkeRapportage.'.$filter;  

      $query="SELECT TijdelijkeRapportage.afmCategorie ".$filterCategorie." as categorie,
      SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/ ".$totaalWaarde['totaal']." as percentage
      FROM TijdelijkeRapportage 
      WHERE Portefeuille='$portefeuille' AND rapportageDatum='$datum'
      GROUP BY TijdelijkeRapportage.afmCategorie ".$filterCategorie."";

      $db->SQL($query);
      $db->Query();
      while($data=$db->nextRecord())
      {
        if($filter=='')
          $afmVerdelingCategorien['totaal'][$data['afmCategorie']]=$data['percentage'];
        else
          $afmVerdelingCategorien[$data['categorie']][$data['afmCategorie']]=$data['percentage'];    
      }
    }
    else
    {
      if($filter=='')
      {
        foreach($portefeuilleWaarden as $regel)
          $afmVerdelingCategorien['totaal']+=$regel['actuelePortefeuilleWaardeEuro'];
        foreach($portefeuilleWaarden as $regel)  
          $afmVerdelingCategorien['totaal'][$regel['afmCategorie']]+=($regel['actuelePortefeuilleWaardeEuro']/$totaalWaarde['totaal']);
      }
      else
      {
        foreach($portefeuilleWaarden as $regel)
          $totaalWaarde[$regel[$filter]]+=$regel['actuelePortefeuilleWaardeEuro'];
        foreach($portefeuilleWaarden as $regel)  
          $afmVerdelingCategorien[$regel[$filter]][$regel['afmCategorie']]+=($regel['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$regel[$filter]]);  
      }
    }  
   $afmCategorien=array();
   $afmCategorieCorrelatie=array();
   $afmCategorienStd=array();
   if(!isset($this->afmCategorieCorrelatie))
   {
     $query="SELECT id,afmCategorie,omschrijving,standaarddeviatie,correlatie FROM afmCategorien ORDER BY id";
     $db->SQL($query);
     $db->Query();
     while($data=$db->nextRecord())
     {
       $afmCategorien[$data['afmCategorie']]=$data['id'];
       $data['correlatie']=unserialize($data['correlatie']);
       $afmCategorienStd[$data['id']]=$data['standaarddeviatie'];
       foreach ($data['correlatie'] as $id=>$correlatie)
       {
         if($correlatie <> '')
         {
           $afmCategorieCorrelatie[$data['id']][$id]=$correlatie;
           $afmCategorieCorrelatie[$id][$data['id']]=$correlatie;
         }
       }
     }
     $this->afmCategorieCorrelatie=$afmCategorieCorrelatie;
     $this->afmCategorienStd=$afmCategorienStd;
     $this->afmCategorien=$afmCategorien;
   }
   else
   {
     $afmCategorieCorrelatie=$this->afmCategorieCorrelatie;
     $afmCategorienStd= $this->afmCategorienStd;
     $afmCategorien=$this->afmCategorien;
   }
  
    
foreach($afmVerdelingCategorien as $filterCategorie=>$afmVerdeling)
{
   $afmVerdelingId=array();
   foreach ($afmVerdeling as $categorie=>$percentage)
   {
     $afmVerdelingId[$afmCategorien[$categorie]]=$percentage;
   }

   $afmVerdelingIdKeys=array_keys($afmVerdelingId);

   $var=0;
   $debugTxt.="relatie tussen categorie => berekening\n";
   foreach ($afmVerdelingIdKeys as $id)
   {
     foreach ($afmVerdelingId as $key2=>$percentage)
     {
       if($afmCategorieCorrelatie[$id][$key2] <> 0)
       {
         $relatieVar[$id.'_'.$key2]=$percentage*$afmCategorienStd[$id];
         $relatieVarDebug[$id.'_'.$key2]=round($percentage,4)." * ".$afmCategorienStd[$id]." ";
         if($id == $key2)
         {
           if($debug)
             $debugTxt.=$id.'_'.$key2." =>  ".round($percentage,4)."^2 * ".$afmCategorieCorrelatie[$id][$key2]."^2 * ".$afmCategorienStd[$id]."^2 =".pow($percentage,2)*pow($afmCategorieCorrelatie[$id][$key2],2)*pow($afmCategorienStd[$id],2)."\n";
           $var+=pow($percentage,2)*pow($afmCategorieCorrelatie[$id][$key2],2)*pow($afmCategorienStd[$id],2);
         }
         else
         {
           if(isset($relatieVar[$key2.'_'.$id]))
           {
             if($debug)
               $debugTxt.= $id.'_'.$key2." => 2 * ".$relatieVarDebug[$key2.'_'.$id]." * ".round($percentage,4)." * ".$afmCategorieCorrelatie[$id][$key2]." * ".$afmCategorienStd[$id]."\n";
             $var+=2* $relatieVar[$key2.'_'.$id]* $percentage*$afmCategorieCorrelatie[$id][$key2]*$afmCategorienStd[$id];
           }
         }
       }
     }
   }
   if($debug)
    $debugTxt.= "var=$var\n";
   $afmstd[$filterCategorie]=pow($var,0.5);
   if($debug)
     $debugTxt.= "afmStd=$afmstd\n";
     
  
}
  
    if($debug)
    {
      $debugArray['debugTxt']=$debugTxt;
      $debugArray['verdeling']=$afmVerdelingId;
      $debugArray['std']=$afmCategorienStd;
      $debugArray['correlatie']=$afmCategorieCorrelatie;
    }

    return array('std'=>$afmstd,'debug'=>$debugArray);

  }
   
	function fondsPerformance($fondsData,$van,$tot,$categorie='')
  {
    global $__appvar;
    if(substr($van,5,5)=='12-31')
      $van=(substr($van,0,4)+1).'-01-01';
  

    $dagen = (db2jul($tot) - db2jul($van)) / 86400;
    if ($dagen < 10)
    {
      $periode = 'w';
    }
    elseif ($dagen < 20)
    {
      $periode = '2w';
    }
    elseif ($dagen < 40)
    {
      $periode = 'm';
    }
    elseif ($dagen < 100)
    {
      $periode = 'k';
    }
    else
    {
      $periode = 'j';
    }
  
    $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, stortingen, onttrekkingen, opbrengsten, kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra,gemiddelde
	  	            FROM HistorischePortefeuilleIndex
	  	            WHERE periode='$periode' AND
	  	            portefeuille = '" . $this->settings['Portefeuille']. "' AND
	  	            Datum = '" . substr($tot, 0, 10) . "' AND Categorie='".mysql_real_escape_string($categorie)."'";
    $DB=new DB();
    if ($DB->QRecords($query) > 0)
    {
      $data = $DB->nextRecord();
 
      $waarden=array(
        'beginwaarde'=>round($data['PortefeuilleBeginWaarde'], 2),
        'eindwaarde'=>round($data['waardeHuidige'], 2),
        'procent'=>$data['indexWaarde'],
        'procentBruto'=>$data['indexWaarde'],
        'stort'=>$data['indexWaarde'],
        'storting'=>$data['stortingen'],
        'onttrekking'=>$data['onttrekkingen'],
        'kosten'=>$data['kosten'],
        'opbrengst'=>$data['opbrengsten'],
        'resultaat'=>round($data['waardeMutatie'] - $data['stortingen'] + $data['onttrekkingen'], 2),
        'gemWaarde'=>0,'totaalGemWaarde'=>0,
        'aandeelOpTotaal'=>0,
        'bijdrage'=>0);
  
      return $waarden;
    }

      
    $perioden[]=array('start'=>$van,'stop'=>$tot);
    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');
    $bFilter=" AND Rekeningmutaties.Transactietype <> 'B' ";

	  $DB=new DB();
    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      { 
        if(substr($rapDatum,5,5)=='01-01')
          $startJaar=1;
        else
          $startJaar=0;
        if(!isset($this->totalen[$rapDatum]))
        {  
	        $fondswaarden =  berekenPortefeuilleWaarde($this->settings['Portefeuille'], $rapDatum,$startJaar);
          foreach($fondswaarden as $id=>$fondsWaarde)
          {
            if($fondsWaarde['type']=='fondsen')
              $instrument=$fondsWaarde['fonds'];
            elseif($fondsWaarde['type']=='rente')
              $instrument=$fondsWaarde['fonds'];              
            elseif($fondsWaarde['type']=='rekening')
              $instrument=$fondsWaarde['rekening'];  
            else
              $instrument='geen';  
            $this->totalen[$rapDatum]['totaalWaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
            $this->totalen[$rapDatum]['WaardeEur'][$instrument]+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
          }
        }
        if(!isset($this->totalen[$rapDatum]['WaardeEur'][$categorie]))
        {
          foreach($this->totalen[$rapDatum]['WaardeEur'] as $instrument=>$waarde)
          {
            if(in_array($instrument,$fondsData['fondsen']) || in_array($instrument,$fondsData['rekeningen']))
            {
              $this->totalen[$rapDatum]['WaardeEur'][$categorie]+=$waarde;
            }
          }
        }
     }
   }


  foreach ($perioden as $periode)
  {
    $grootboekKosten=array();
    $grootboekOpbrengsten=array();
    $FondsDirecteKostenOpbrengsten=array();
    $RekeningDirecteKostenOpbrengsten=array();
    $datumBegin=$periode['start'];
    $datumEind=$periode['stop'];
    
    if(substr($datumBegin,0,4) <> substr($datumEind,0,4) && $this->filterJaarovergang==true)
      continue;
    
    $portefeuilleStartJul=db2jul($this->rapport->pdf->PortefeuilleStartdatum);
    if($portefeuilleStartJul > db2jul($datumBegin) && $portefeuilleStartJul < db2jul($datumEind))
    {
      $datumBegin=substr($this->rapport->pdf->PortefeuilleStartdatum,0,10);
      $weegDatum=$datumBegin;
    }
    
    if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
      $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
    else
      $weegDatum=$datumBegin;
    
    

    
	  $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
	  $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur']; 
    
    
     $query="SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1";
     $DB->SQL($query);
     $DB->Query();
     $grootboekrekeningen=array();
     while($grootboekrekening=$DB->nextRecord())
       $grootboekrekeningen[]=$grootboekrekening['Grootboekrekening'];
      
   	  $query = "SELECT ".
	    "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	    "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	    "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
	    "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal
      ".
	    "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	    "WHERE ".
      "Rekeningen.Portefeuille = '".$this->settings['Portefeuille']."' AND ".
	    "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	    "Rekeningmutaties.Verwerkt = '1' AND ".
	    "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	    "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	    "Rekeningmutaties.Grootboekrekening IN ('".implode("','",$grootboekrekeningen)."')";
      $DB->SQL($query);
      $DB->Query();
      $storting = $DB->NextRecord();
      $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];
      $this->totaalGemiddelde=$totaalGemiddelde;

    if($categorie=='totaal')
    {
      $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
	    $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];  
      $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal']) / $this->totaalGemiddelde);
      $stortingen 			 	= getStortingen($this->settings['Portefeuille'],$datumBegin,$datumEind);
	  	$onttrekkingen 		 	= getOnttrekkingen($this->settings['Portefeuille'],$datumBegin,$datumEind);
      $AttributieStortingenOntrekkingen['storting']=$stortingen;
      $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
      $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
      $gemiddelde = $totaalGemiddelde;
    }
    else
    {
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $beginwaarde = $this->totalen[$datumBegin]['WaardeEur'][$categorie];
	    $eindwaarde = $this->totalen[$datumEind]['WaardeEur'][$categorie];//$eind['actuelePortefeuilleWaardeEuro'];
      
      if($beginwaarde==0)
      {
        $query="SELECT Rekeningmutaties.Boekdatum FROM (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening 
                  WHERE 
                 Rekeningen.Portefeuille = '".$this->settings['Portefeuille']."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningFondsenWhere ORDER BY Rekeningmutaties.Boekdatum asc limit 1";
        $DB->SQL($query);
	      $DB->Query();
        $datum=$DB->nextRecord();
        $weegDatum=$datum['Boekdatum'];
      }

	    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND ". //OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere $bFilter";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

	     $queryRekeningDirecteKostenOpbrengsten = "SELECT 
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere $bFilter";
	    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
	    $DB->Query(); 
	    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
    
      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->settings['Portefeuille']."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere $bFilter";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten);
       $DB->Query();  
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();

	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers,0))  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->settings['Portefeuille']."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Fonds <> '' AND $rekeningFondsenWhere $bFilter";//
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();
 
       $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
       $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
     
       
 	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT 
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles) 
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening 
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                 
	              WHERE 	 
                Rekeningen.Portefeuille = '".$this->settings['Portefeuille']."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND                
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  ) $bFilter";
	     $DB->SQL($query);
       
	     $DB->Query(); 
	     $data = $DB->nextRecord();
 	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];

       if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
         $DB->SQL($query);
       else
         $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
	     $DB->Query(); 
	     $data = $DB->nextRecord();

	     $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];

      $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->settings['Portefeuille']."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere $bFilter";
	     $DB->SQL($queryKostenOpbrengsten);
	     $DB->Query();
	     $nietToegerekendeKosten = $DB->NextRecord();

       
       if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
       {
         $AttributieStortingenOntrekkingen['totaal']+= $nietToegerekendeKosten['kostenTotaal'];
         $AttributieStortingenOntrekkingen['onttrekking']+= $nietToegerekendeKosten['kostenTotaal'];
       }

//Weging uitgezet.
//$AttributieStortingenOntrekkingen['gewogen']=$AttributieStortingenOntrekkingen['totaal'];
    
       $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
       $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] + $RekeningDirecteKostenOpbrengsten['kostenTotaal']) / $gemiddelde);
       $gemiddeldeBruto  = $beginwaarde - $AttributieStortingenOntrekkingenBruto['gewogen'];
       $performanceBruto = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingenBruto['totaal']- $RekeningDirecteKostenOpbrengsten['kostenTotaal']) / $gemiddelde);
      }

      $renteResultaat=$eind['renteWaarde']-$start['renteWaarde'];
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      //echo " $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;<br>\n";

      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']+ $RekeningDirecteKostenOpbrengsten['kostenTotaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;

      $waarden[$datumEind]=array(
   'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance*100,
  'procentBruto'=>$performanceBruto*100,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal'],
  'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,'totaalGemWaarde'=>$this->totaalGemiddelde,
  'aandeelOpTotaal'=>$aandeelOpTotaal,
  'bijdrage'=>$bijdrage);
  }
   return $waarden[$datumEind];
 }

  function getReeksRendement($reeks)
  {
    $perf=0;
    foreach($this->reeksen[$reeks] as $datum=>$perfData)
    {
      $perf=((1+$perf)*(1+$perfData['perf']/100))-1;
      //echo "$datum ".$perfData['perf']." -> ".($perf*100)." <br>\n";
    }
    return $perf*100;
  }
      
   

}
?>