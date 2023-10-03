<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/29 14:00:25 $
 		File Versie					: $Revision: 1.3 $

 		$Log: benchmarkverdelingBerekeningV2.php,v $
 		Revision 1.3  2018/12/29 14:00:25  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/08/12 12:00:33  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/08/09 16:08:13  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2017/07/19 19:21:31  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/07/16 10:52:15  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/07/01 17:06:01  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/06/28 15:16:23  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/03/04 19:17:07  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/02/18 16:37:27  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/02/01 16:47:14  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/12/24 16:29:40  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/12/10 19:25:50  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/11/27 11:06:12  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/06/08 07:51:41  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/06/04 16:10:51  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/05/21 15:18:07  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/05/17 16:32:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/07/18 15:19:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/12/12 15:25:15  rvv
 		*** empty log message ***

 		Revision 1.1  2010/12/05 09:45:58  rvv
 		*** empty log message ***

 		Revision 1.16  2010/11/21 13:04:55  rvv
 		*** empty log message ***

*/
class benchmarkverdelingBerekeningV2
{
  function benchmarkverdelingBerekeningV2($benchmark)
  {
    $this->benchmarks=array();
    $this->nietBijgewerkteRecords=array();
    $this->laatsteKoers=array();
    $this->fondsPercentages=array();
    $this->fondsKoersen=array();
    $this->benchmarkRentement=array();
    $this->verdelingen=array();
    $this->error=array();
    if($benchmark)
      $this->benchmarks[]=$benchmark;
    else
    {
      $db=new DB();
      $query="SELECT benchmark FROM benchmarkverdelingVanaf GROUP BY benchmark";
      $db->SQL($query);
      $db->Query();
      while($data=$db->nextRecord())
        $this->benchmarks[]=$data['benchmark'];
    }
  }

  function getBenchmarks()
  {
    return $this->benchmarks;
  }

  function bereken($benchmark,$vanaf='')
  {
    $this->verdelingen[$benchmark] = array();
    $db = new DB();
    $query = "SELECT fonds,percentage,vanaf FROM benchmarkverdelingVanaf WHERE benchmark='" . $benchmark . "' order by vanaf";
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $vanafJul = db2jul($data['vanaf']);
      if (!in_array($vanafJul, $this->verdelingen[$benchmark]))
      {
        $this->verdelingen[$benchmark][] = $vanafJul;
      }
      $this->fondsPercentages[$benchmark][$vanafJul][$data['fonds']] = $data['percentage'];
    }
//    listarray($this->verdelingen);

    $vanafFilter = '';
    if ($vanaf <> '')
    {
      $query = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='" . $benchmark . "' ORDER BY Datum ASC limit 1";
      $db->SQL($query);
      $db->Query();
      $eerste = $db->nextRecord();
      if ($eerste['Datum'] == '')
      {
        echo "Geen beginkoers voor '$benchmark' gevonden.";
      }
    
      if (db2jul($vanaf) < db2jul($eerste['Datum']))
      {
        $vanaf = $eerste['Datum'];
        $this->error[] = "Voor '$benchmark' begindatum aangepast naar $vanaf";
      }
      $vanafFilter = " AND Datum <= '$vanaf'";
    }


    $query = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='" . $benchmark . "' $vanafFilter ORDER BY Datum DESC limit 1";
    $db->SQL($query);
    $db->Query();
    $data = $db->nextRecord();
    $this->laatsteKoers[$benchmark] = $data;

    $n = 0;
    foreach ($this->fondsPercentages[$benchmark] as $datumVanafJul => $percentagesPerDatum)
    {
      if (isset($this->verdelingen[$benchmark][$n + 1]))
      {
        $volgendeFilter = "AND  Datum < '" . date("Y-m-d", $this->verdelingen[$benchmark][$n + 1]) . "'";
      }
      else
      {
        $volgendeFilter = '';
      }

      foreach ($percentagesPerDatum as $fonds => $percentage)
      {
        $query = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='$fonds' AND
                                                             Datum >= '" . $this->laatsteKoers[$benchmark]['Datum'] . "' AND 
                                                             Datum >= '" . date("Y-m-d", $datumVanafJul - 86400) . "' 
                                                             $volgendeFilter ORDER BY Datum ASC";
        $db->SQL($query);
        $this->error[] = $query;
        $db->Query();
        $koersen = array();
        while ($data = $db->nextRecord())
        {
          $koersen[$data['Datum']] = $data;
        }
        $tmp = array('fonds' => $fonds, 'percentage' => $percentage, 'koersen' => $koersen);
        $this->fondsKoersen[$benchmark][$datumVanafJul][$fonds] = $tmp;
        //echo "$benchmark  $fonds <br>\n";
      }
      $n++;
    }

  foreach ($this->fondsKoersen[$benchmark] as $vanaf => $fondsWaardenVanaf)
  {
    foreach ($fondsWaardenVanaf as $fonds => $fondsWaarden)
    {
      $n = 0;
      $laatsteKoers=0;
      foreach ($fondsWaarden['koersen'] as $datum => $koersen)
      {
        $datumOke = true;

        foreach ($this->fondsPercentages[$benchmark][$vanaf] as $fonds2 => $percentage)
        {
          if (!isset($this->fondsKoersen[$benchmark][$vanaf][$fonds2]['koersen'][$datum]))
          {
            $datumOke = false;
            $this->error[] = "Voor '$benchmark' missen er koersen voor '$fonds2' op '$datum'";
          }
        }

        if ($datumOke)
        {
          if ($n > 0)
          {
            $dagRendement = $koersen['Koers'] / $laatsteKoers;
            //$this->fondsKoersen[$benchmark][$vanaf][$fonds][$datum]['dagRendement'] = $dagRendement;
            $this->benchmarkRentement[$benchmark][$datum]['verdeling'][$fondsWaarden['fonds']]=$fondsWaarden['percentage'];
            $this->benchmarkRentement[$benchmark][$datum]['dagRendement'] += $dagRendement * $fondsWaarden['percentage'] / 100;
          }
          //echo "$fonds $datum ".$koersen['Koers']."/$laatsteKoers ($laatsteDatum) = $dagRendement <br>\n";
          $laatsteKoers = $koersen['Koers'];
          $n++;
        }
      }
    }
  }

  }

  function updateKoersen()
  {
    global $USR;
    $db=new DB();
    foreach($this->benchmarkRentement as $benchmark=>$benchmarkData)
    {
      $indexWaarde=$this->laatsteKoers[$benchmark]['Koers'];
      foreach ($benchmarkData as $datum=>$waarden)
      {
        $indexWaarde=$indexWaarde*$waarden['dagRendement'];
        $this->benchmarkRentement[$benchmark][$datum]['index']=$indexWaarde;
      }
    }

    foreach($this->benchmarkRentement as $benchmark=>$benchmarkData)
    {
      $aanwezigeRecords=array();
      $datumValues=array_keys($benchmarkData);

      $query="SELECT id,Koers,change_date FROM Fondskoersen WHERE Fonds='".$benchmark."' AND Datum>='".min($datumValues)."' AND Datum<='".max($datumValues)."'";
      $db->SQL($query);
      $db->Query();
      while($recordInfo=$db->nextRecord())
        $aanwezigeRecords[$recordInfo['id']]=$recordInfo;

      foreach ($benchmarkData as $datum=>$waarden)
      {
        $query="SELECT id,Koers FROM Fondskoersen WHERE Fonds='".$benchmark."' AND Datum='$datum'";
        if($db->QRecords($query) > 0)
        {
          $recordInfo=$db->nextRecord();
          unset($aanwezigeRecords[$recordInfo['id']]);
          if(round($recordInfo['Koers'],6) <> round($waarden['index'],6))
          {
            $query="UPDATE Fondskoersen SET Koers='".$waarden['index']."',change_date=now(),change_user='$USR' WHERE id='".$recordInfo['id']."'";
            $db->SQL($query);
            $db->Query();
            $this->error[]="Update voor $benchmark op $datum, koers van ".$recordInfo['Koers']." naar ".$waarden['index'].".";
          }
          else
            $this->error[]="Koers voor $benchmark op $datum al aanwezig met id ".$recordInfo['id']." en koers ".$recordInfo['Koers'].". Geen update nodig.";
        }
        else
        {
          $query="INSERT INTO Fondskoersen SET Fonds='".$benchmark."',Datum='$datum',Koers='".$waarden['index']."',add_date=NOW(),change_date=NOW(),add_user='$USR',change_user='$USR'";
          $this->error[]= " $query";
          $db->SQL($query);
          $db->Query();
        }
      }
      if(count($aanwezigeRecords)>0)
        $this->nietBijgewerkteRecords[$benchmark]=$aanwezigeRecords;
    }
   // listarray($this);
  }
  
  function toonOngecontroleerd()
  {
    $txt='';
    $msg='';
    if(count($this->nietBijgewerkteRecords) > 0)
    {
      foreach($this->nietBijgewerkteRecords as $benchmark=>$ongecontroleerdeRecords)
      {
        foreach($ongecontroleerdeRecords as $fondskoerdId=>$fondskoersData)
        {
          $msg.=logTxt("Voor benchmark '$benchmark' is fondskoersId:$fondskoerdId niet bijgewerkt/gecontroleerd. (koers:" . $fondskoersData['Koers'] . " change_date:" . $fondskoersData['change_date'] . ")")."<br>\n";
          $txt.="DELETE FROM Fondskoersen WHERE id=$fondskoerdId;<br>\n";
        }
      }
    }
    if($msg<>'')
      return "<br>\n".$msg."<br>\n Deze zijn te verwijderen met onderstaande queries:<br>\n".$txt;
  }

  function getLaatsteKoers($fonds)
  {
    $db=new DB();
    $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='".$fonds."' ORDER BY Datum DESC limit 1";
    $db->SQL($query);
    $db->Query();
    $data=$db->nextRecord();
    return $data;
  }

  function getKoersen($fonds,$vanaf)
  {
    $db=new DB();
    $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='".$fonds."' AND Datum>='$vanaf' ORDER BY Datum";
    $db->SQL($query);
    $db->Query();
    $tmp=array();
    while($data=$db->nextRecord())
      $tmp[$data['Datum']]=$data;
    return $tmp;
  }

  function calulateEuribor($debug=false)
  {
    global $USR;
    $cfg=new AE_config();
    $lockDatum=$cfg->getData('fondskoersLockDatum');

    $db=new DB();
    $query="SELECT Fondsen.Fonds as hoofdFonds, indices.Fonds as indexFonds, indices.OptieUitoefenPrijs as opslag
    FROM Fondsen JOIN Fondsen AS indices ON indices.OptieBovenliggendFonds=Fondsen.Fonds AND indices.fondssoort='INDEX'
    WHERE Fondsen.HeeftOptie=1 AND Fondsen.fondssoort='INDEX'";
    $db->SQL($query);
    $db->Query();
    $indices=array();
    while($data=$db->nextRecord())
      $indices[]=$data;
    $log='';
    foreach($indices as $index)
    {
      $hoofdFondsLaatsteKoers=$this->getLaatsteKoers($index['hoofdFonds']);
      $log.= "<hr>".$index['hoofdFonds']."<br>\n";
      //listarray($hoofdFondsLaatsteKoers);

      $indexFondsLaatsteKoers=$this->getLaatsteKoers($index['indexFonds']);
      $log.= $index['indexFonds']."<br>\n";
      //listarray($indexFondsLaatsteKoers);
      if(db2jul($lockDatum)>db2jul($indexFondsLaatsteKoers['Datum']))
      {
        $log.= "lockdatum $lockDatum > laatsteIndexKoers ".$indexFondsLaatsteKoers['Datum']." verwerking voor ".$index['Fonds']." overgeslagen.";
        continue;
      }

      $log.= "Lockdatum: $lockDatum <br>\n";

      $hoofdFondsKoersen=$this->getKoersen($index['hoofdFonds'], $lockDatum);
      $indexFondsKoersen=$this->getKoersen($index['indexFonds'], $lockDatum);

      $log.= "<br>\nIndex berekening: <br>\n";
      foreach($hoofdFondsKoersen as $datum=>$koersData)
      {
        if(!isset($indexKoers))
        {
          if(isset($indexFondsKoersen[$datum]['Koers']))
            $indexKoers=$indexFondsKoersen[$datum]['Koers'];
          else
          {
            $log.= "Geen beginkoers gevonden voor ".$index['indexFonds']." op $datum <br>\n";
            $afbreken=true;
            continue;
          }
          $laatsteDatum=$datum;
        }
        else
        {
          $dagen=round((db2jul($datum)-db2jul($laatsteDatum))/86400);
          $rendement = 1+(($koersData['Koers'] + $index['opslag'])/100);

          $indexKoers=($indexKoers/100)*pow($rendement,($dagen/365))*100;
          $indexFondsKoersen[$datum]['nieuweKoers']=$indexKoers;
          $log.= substr($datum,0,10)." $indexKoers=($indexKoers/100)*pow($rendement,($dagen/365))*100; rendement: $rendement= 1+((".$koersData['Koers']." + ".$index['opslag'].")/100)<br>\n";
          /*
          $rendement = ($koersData['Koers'] + $index['opslag']) * ($dagen/365);
          $indexKoers=($indexKoers/100)*(1+$rendement/100)*100;

          echo substr($datum,0,10)." $rendement=(".$koersData['Koers']." + ".$index['opslag'].") * ($dagen/365) index: $indexKoers<br>\n";
          */
          $laatsteDatum=$datum;
        }
      }

      $log.= "<br>\nVergelijking oude en nieuwe index: <br>\n";
      $bijwerken=array();
      foreach($indexFondsKoersen as  $datum=>$gegevens)
      {
        $log.= substr($datum,0,10)." old:".$gegevens['Koers']." new:".$gegevens['nieuweKoers']." <br>\n";
        if($gegevens['nieuweKoers']<> 0 && round($gegevens['Koers'],6) <> round($gegevens['nieuweKoers'],6))
        {
          $bijwerken[$datum]=round($gegevens['nieuweKoers'],6);
        }
      }

      $log.= "<br>\nDatabase bijwerken: <br>\n";
      foreach($bijwerken as $datum=>$koers)
      {
        $query="SELECT id FROM Fondskoersen WHERE Fonds='".$index['indexFonds']."' AND Datum='$datum'";
        $db->SQL($query);
        $koersRecord=$db->lookupRecord();
        if($koersRecord['id']>0)
          $query="UPDATE Fondskoersen SET Koers='$koers',change_date=now(),change_user='$USR' WHERE id='".$koersRecord['id']."'";
        else
          $query="INSERT INTO Fondskoersen SET Datum='$datum', Fonds='".mysql_real_escape_string($index['indexFonds'])."',Koers='$koers',change_date=now(),change_user='$USR',add_date=now(),add_user='$USR'";

        $db->SQL($query);
        if($debug==true)
        {
          $log.= "$query ;<br>\n";
        }
        elseif($db->Query())
        {
          $log.= "$query ; --- uitgevoerd ---<br>\n";
        }
      }
      unset($indexKoers);
    }
    if($debug==true)
      echo $log;
  }
}
?>