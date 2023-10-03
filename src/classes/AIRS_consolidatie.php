<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/30 15:26:05 $
    File Versie         : $Revision: 1.17 $

    $Log: AIRS_consolidatie.php,v $
    Revision 1.17  2020/05/30 15:26:05  rvv
    *** empty log message ***

    Revision 1.16  2020/01/11 14:33:45  rvv
    *** empty log message ***

    Revision 1.15  2019/09/21 16:26:52  rvv
    *** empty log message ***

    Revision 1.14  2019/05/04 18:15:50  rvv
    *** empty log message ***

    Revision 1.13  2018/12/14 16:36:19  rvv
    *** empty log message ***

    Revision 1.12  2018/12/12 16:15:37  rvv
    *** empty log message ***

    Revision 1.11  2018/12/05 16:30:57  rvv
    *** empty log message ***

    Revision 1.10  2018/11/24 19:06:05  rvv
    *** empty log message ***

    Revision 1.9  2018/11/10 18:22:21  rvv
    *** empty log message ***

    Revision 1.8  2018/10/17 15:22:04  rvv
    *** empty log message ***

    Revision 1.7  2018/10/13 17:13:50  rvv
    *** empty log message ***

    Revision 1.6  2018/09/19 17:20:00  rvv
    *** empty log message ***

    Revision 1.5  2018/09/12 15:55:59  rvv
    *** empty log message ***

    Revision 1.4  2018/09/05 15:47:24  rvv
    *** empty log message ***

    Revision 1.3  2018/08/27 17:17:56  rvv
    *** empty log message ***

    Revision 1.2  2018/08/19 08:48:51  rvv
    *** empty log message ***

    Revision 1.1  2018/08/18 12:40:13  rvv
    php 5.6 & consolidatie



*/

class AIRS_consolidatie
{

  function AIRS_consolidatie()
  {
    $this->db=new DB();
    $this->mutatieAantal=0;
    $this->debug=false;
    $this->updateClient=false;
    $this->insertClient=false;
    $this->insertPortefeuille=false;
    $this->showOnly=false;
  }

  function ophalenVPsViaRekening($rekening)
  {
    $portefeuilles=array();
    $query="SELECT Portefeuille FROM Rekeningen WHERE Rekeningen.consolidatie=0 AND Rekeningen.Rekening='".mysql_real_escape_string($rekening)."'";
    $this->db->SQL($query);
    $this->db->Query();
    while($rdata = $this->db->nextRecord())
    {
      $portefeuilles[]=$rdata['Portefeuille'];
    }
    return $this->ophalenVPsViaPortefeuille($portefeuilles);
  }

  function ophalenVPsViaPortefeuille($portefeuille)
  {
    $VPs=array();
    if(is_array($portefeuille))
    {
      $PortefeuilleNummers = $portefeuille;
    }
    else
    {
      $PortefeuilleNummers = array($portefeuille);
    }
    
    $consolidatieWhere = "(";
    for($i=1;$i<41;$i++)
    {
      $consolidatieWhere .= "GeconsolideerdePortefeuilles.Portefeuille".$i."  =  Portefeuilles.Portefeuille";
      if($i < 40)
        $consolidatieWhere .= " OR \n";
    }
    $consolidatieWhere .= ")";

    $portefeuilleSelectie = implode('\',\'',$PortefeuilleNummers);
    $query = "SELECT GeconsolideerdePortefeuilles.VirtuelePortefeuille FROM (GeconsolideerdePortefeuilles, Portefeuilles) 
     WHERE $consolidatieWhere AND Portefeuilles.Portefeuille IN('$portefeuilleSelectie')
     GROUP BY VirtuelePortefeuille";
    $this->db->SQL($query);
    $this->db->Query();
    while($cdata = $this->db->nextRecord())
    {
      $VPs[]=$cdata['VirtuelePortefeuille'];
    }
    
    $query="SELECT VirtuelePortefeuille FROM PortefeuillesGeconsolideerd WHERE Portefeuille IN('".implode("','",$PortefeuilleNummers)."') GROUP BY VirtuelePortefeuille";
    $this->db->SQL($query);
    $this->db->Query();
    while($cdata = $this->db->nextRecord())
    {
      if(!in_array($cdata['VirtuelePortefeuille'],$VPs))
      {
        $VPs[] = $cdata['VirtuelePortefeuille'];
      }
    }
    
    return $VPs;
  }
  
  function ophalenPortefeuillesViaVp($portefeuille)
  {
    $Ps=array();
    $query = "SELECT * FROM GeconsolideerdePortefeuilles
     WHERE  GeconsolideerdePortefeuilles.VirtuelePortefeuille = '$portefeuille'";
    $this->db->SQL($query);
    $this->db->Query();
    $cdata = $this->db->nextRecord();
    for($i=1;$i<41;$i++)
    {
      if($cdata["Portefeuille".$i] <> '')
        $Ps[]=$cdata['VirtuelePortefeuille'];
    }
  
    $query="SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille = '$portefeuille' GROUP BY Portefeuille";
    $this->db->SQL($query);
    $this->db->Query();
    while($cdata = $this->db->nextRecord())
    {
      if(!in_array($cdata['Portefeuille'],$Ps))
      {
        $Ps[] = $cdata['Portefeuille'];
      }
    }
    
    return $Ps;
  }
  
  function bepaalConsolidaties($VPs=array(),$deleteCheck=false)
  {
    $consolidatiesNieuw=array();
    $consolidatiesHuidig=array();
    $geconsolideerdePortefeuilles=array();

    if (count($VPs) >0)
    {
      $where = "WHERE VirtuelePortefeuille IN('" . implode("','",$VPs) . "')";
    }
    else
    {
      $where = '';
    }
    $query = "SELECT * FROM GeconsolideerdePortefeuilles $where";
    $this->db->SQL($query);
    $this->db->Query();

    $clientVelden = array('Client', 'Naam', 'Naam1');
    $portefeuilleVelden = array('VirtuelePortefeuille' => 'Portefeuille', 'Vermogensbeheerder' => 'Vermogensbeheerder');//, 'Client' => 'Client', 'Risicoprofiel' => 'Risicoklasse', 'SoortOvereenkomst' => 'SoortOvereenkomst', 'SpecifiekeIndex'      => 'SpecifiekeIndex', 'ModelPortefeuille' => 'ModelPortefeuille', 'ZpMethode' => 'ZpMethode', 'Einddatum' => 'Einddatum', 'Startdatum' => 'Startdatum'
    while ($data = $this->db->NextRecord())
    {
      $geconsolideerdePortefeuilles[] = $data['VirtuelePortefeuille'];
      $portefeuilles = array();
      for ($i = 0; $i < 41; $i++)
      {

        if ($data['Portefeuille' . $i] <> '')
        {
          $portefeuilles[] = $data['Portefeuille' . $i];
        }

        unset($data['Portefeuille' . $i]);
      }
      foreach ($clientVelden as $veld)
      {
        $consolidatiesNieuw[$data['VirtuelePortefeuille']]['Client'][$veld] = $data[$veld];
      }
      foreach ($portefeuilleVelden as $vveld => $pveld)
      {
        $consolidatiesNieuw[$data['VirtuelePortefeuille']]['Portefeuille'][$pveld] = $data[$vveld];
      }
      $consolidatiesNieuw[$data['VirtuelePortefeuille']]['Portefeuilles'] = $portefeuilles;
    }
  
  
    $query = "SELECT VirtuelePortefeuille, Portefeuille FROM PortefeuillesGeconsolideerd $where";
    $this->db->SQL($query);
    $this->db->Query();
    while ($data = $this->db->NextRecord())
    {
      if(!in_array($data['VirtuelePortefeuille'],$geconsolideerdePortefeuilles))
      {
        $geconsolideerdePortefeuilles[] = $data['VirtuelePortefeuille'];
      }
      if(!in_array($consolidatiesNieuw[$data['VirtuelePortefeuille']]['Portefeuilles'],$data['Portefeuille']))
        $consolidatiesNieuw[$data['VirtuelePortefeuille']]['Portefeuilles'][] = $data['Portefeuille'];
    }
    
    

    foreach ($geconsolideerdePortefeuilles as $virtuelePortefeuille)
    {
      if(($this->updateClient==false && $this->insertPortefeuille==false )|| $deleteCheck==true)
      {$select = "id,Startdatum,Einddatum,Portefeuille,Client,Vermogensbeheerder,consolidatieVasteStart,consolidatieVasteEind,Depotbank ";}
      else
      {$select='*';}

      $query = "SELECT $select FROM Portefeuilles WHERE Portefeuille='" . mysql_real_escape_string($virtuelePortefeuille) . "' AND consolidatie=1";
      $this->db->SQL($query);
      $this->db->Query();
      if($this->db->records()>1){echo "Meerdere records voor $virtuelePortefeuille gevonden.<br>";}
      while ($data = $this->db->NextRecord())
      {
        if($this->updateClient==true || $this->insertClient==true || $this->insertPortefeuille==true)
          $data['Risicoprofiel']=$data['Risicoklasse'];
        $consolidatiesHuidig[$virtuelePortefeuille]['Portefeuilles'][] = $data['Portefeuille'];
        $consolidatiesHuidig[$virtuelePortefeuille]['Portefeuille'] = $data;
        $oldClient = $data['Client'];
      }
      if($deleteCheck==true)
        {$select = "id,Rekening ";}
      else
        {$select ='*';}
      $query = "SELECT $select FROM Rekeningen WHERE Portefeuille='" . mysql_real_escape_string($virtuelePortefeuille) . "' AND consolidatie=1";
      $this->db->SQL($query);
      $this->db->Query();
      while ($data = $this->db->NextRecord())
      {
        $consolidatiesHuidig[$virtuelePortefeuille]['Rekeningen'][$data['Rekening']] = $data;
      }
      if($this->updateClient || $this->insertClient)
      {
        if($deleteCheck==true)
          {$select = "id,Client ";}
        else
          {$select ='*';}
        $query = "SELECT $select FROM Clienten WHERE Client='" . mysql_real_escape_string($oldClient) . "' ";//AND consolidatie=1
        $this->db->SQL($query);
        $this->db->Query();
        if ($this->db->records() > 1)
        {
          echo "Meerdere records voor $oldClient gevonden. $query <br>";
        }
        while ($data = $this->db->NextRecord())
        {
          $consolidatiesHuidig[$virtuelePortefeuille]['Client'] = $data;
        }
      }
    }

    if($deleteCheck==false)
    {
      $unsetRekeningVelden=array('id','Portefeuille','consolidatie','add_date','add_user','change_date','change_user');
      if($this->updateClient || $this->insertClient || $this->insertPortefeuille)
      {
        $unsetPortefeuilleVelden = array('id', 'Portefeuille', 'consolidatie', 'add_date', 'add_user', 'change_date', 'change_user', 'FactuurMemo', 'BeheerfeeBasisberekening', 'BeheerfeeMethode', 'BeheerfeeKortingspercentage', 'BeheerfeePercentageVermogen',
        'BeheerfeeBedrag', 'BeheerfeePerformancePercentage', 'BeheerfeeTeruggaveHuisfondsenPercentage', 'BeheerfeeRemisiervergoedingsPercentage', 'BeheerfeeToevoegenAanPortefeuille', 'BeheerfeeAantalFacturen', 'BeheerfeeStaffel1',
        'BeheerfeeStaffel2', 'BeheerfeeStaffel3', 'BeheerfeeStaffel4', 'BeheerfeeStaffel5', 'BeheerfeeStaffelPercentage1', 'BeheerfeeStaffelPercentage2', 'BeheerfeeStaffelPercentage3', 'BeheerfeeStaffelPercentage4', 'BeheerfeeStaffelPercentage5',
        'BeheerfeeBTW', 'Memo', 'Risicoprofiel','AantalAfdrukken','BeheerfeeAdministratieVergoeding','HistorischeInfo','Taal');
      }
   

      foreach($consolidatiesNieuw as $vPortefeuille=>$vpData)
      {
        if($this->insertPortefeuille==true)
          $velden='Startdatum,Einddatum,SpecifiekeIndex,SoortOvereenkomst,SpecifiekeIndex,ModelPortefeuille,ZpMethode,Risicoprofiel,consolidatie,Client,Depotbank';
        else
          $velden='Startdatum,Einddatum';
      $query = "SELECT $velden FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$vpData['Portefeuilles']) ."') ORDER BY Portefeuille";
      $this->db->SQL($query);
      $this->db->Query();
      $startJul=db2jul($vpData['Portefeuille']['Startdatum']);
      $endJul=db2jul($vpData['Portefeuille']['Einddatum']);
      while($data=$this->db->NextRecord())
      {
        $pstartJul=db2jul($data['Startdatum']);
        $pendJul=db2jul($data['Einddatum']);
//consolidatieVasteStart
        if($consolidatiesHuidig[$vPortefeuille]['Portefeuille']['consolidatieVasteStart']==0)
        {
          if ($pstartJul > 1 && ($pstartJul < $startJul || $startJul <= 1) )
          {
            $consolidatiesNieuw[$vPortefeuille]['Portefeuille']['Startdatum'] = $data['Startdatum'];
            $startJul = $pstartJul;
          }
        }
        if($consolidatiesHuidig[$vPortefeuille]['Portefeuille']['consolidatieVasteEind']==0)
        {
          if ($pendJul > $endJul)
          {
            $consolidatiesNieuw[$vPortefeuille]['Portefeuille']['Einddatum'] = $data['Einddatum'];
            $endJul = $pendJul;
          }
        }
        if($this->updateClient || $this->insertClient || $this->insertPortefeuille)
        {
          foreach ($data as $key => $value)
          {
            if (!isset($consolidatiesNieuw[$vPortefeuille]['Portefeuille'][$key]) && !in_array($key, $unsetPortefeuilleVelden))
            {
              $consolidatiesNieuw[$vPortefeuille]['Portefeuille'][$key] = $value;
            }
          }
        }
      }
      $query = "SELECT * FROM Rekeningen WHERE Portefeuille IN('".implode("','",$vpData['Portefeuilles']) ."')";
      $this->db->SQL($query);
      $this->db->Query();
      $rekeningen=array();
      while($data=$this->db->NextRecord())
      {

        foreach($unsetRekeningVelden as $veld)
          unset($data[$veld]);
        $data['Portefeuille']=$vPortefeuille;
        $rekeningen[$data['Rekening']]=$data;
      }
      $consolidatiesNieuw[$vPortefeuille]['Rekeningen']=$rekeningen;
      }
    }
    else
    {
        foreach($consolidatiesNieuw as $vPortefeuille=>$vpData)
        {
          $query = "SELECT Rekening FROM Rekeningen WHERE Portefeuille IN('".implode("','",$vpData['Portefeuilles']) ."')";
          $this->db->SQL($query);
          $this->db->Query();
          $rekeningen=array();
          while($data=$this->db->NextRecord())
          {
            $rekeningen[$data['Rekening']]=$data['Rekening'];
          }
          $consolidatiesNieuw[$vPortefeuille]['Rekeningen']=$rekeningen;
        }
      
    }
    
    return array('consolidatiesHuidig'=>$consolidatiesHuidig,'consolidatiesNieuw'=>$consolidatiesNieuw);
  }

  function bijwerkenConsolidaties($virtuelePortefeuille='')
  {
    logIt("bijwerkenConsolidaties voor VP:$virtuelePortefeuille.");
    global $USR;
    $this->db=new DB();

    $VPs=array();
    if(is_array($virtuelePortefeuille))
    {
      foreach($virtuelePortefeuille as $VP)
        $VPs[]=mysql_real_escape_string($VP);
    }
    elseif($virtuelePortefeuille<>'')
    {
      $VPs=array(mysql_real_escape_string($virtuelePortefeuille));
    }

    $consolidatieVerdeling=$this->bepaalConsolidaties($VPs);
    $consolidatiesHuidig=$consolidatieVerdeling['consolidatiesHuidig'];
    $consolidatiesNieuw=$consolidatieVerdeling['consolidatiesNieuw'];


    $queries=array();
    $insertDefault="consolidatie=1,add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
    $updateDefault="consolidatie=1,change_date=now(),change_user='$USR'";
    foreach($consolidatiesNieuw as $vPortefeuille=>$vpData)
    {
      if($this->updateClient || $this->insertClient)
      {// listarray($consolidatiesHuidig[$vPortefeuille]['Portefeuille']);exit;
        if (!isset($consolidatiesHuidig[$vPortefeuille]['Portefeuille']['Client']))
        {
          $tmpQuery = 'INSERT INTO Clienten SET ';
          foreach ($vpData['Client'] as $key => $value)
          {
            $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
          }
          $tmpQuery .= $insertDefault;
          if($this->insertClient==true)
          {
  
            $query = "SELECT id FROM Clienten WHERE Client='" . mysql_real_escape_string($vpData['Client']['Client']) . "' ";
            $this->db->SQL($query);
            $this->db->Query();
            if ($this->db->records() > 0)
            {
              echo "Client ".$vpData['Client']['Client']." al aanwezig gevonden. Geen nieuw record aanmaken.<br>";
            }
            else
            {
              $queries[] = $tmpQuery;
            }
          }
        }
        else
        {
          $update = false;
          foreach ($vpData['Client'] as $key => $value)
          {
            if ($value <> $consolidatiesHuidig[$vPortefeuille]['Client'][$key])
            {
              // echo "|$value| <> |".$consolidatiesHuidig[$vPortefeuille]['Client'][$key]."|<br>\n";
              $update = true;
            }
          }
          if ($update == true)
          {
            $tmpQuery = 'UPDATE Clienten SET ';
            foreach ($vpData['Client'] as $key => $value)
            {
              $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
              if($this->showOnly==false)
              {
                if($consolidatiesHuidig[$vPortefeuille]['Client'][$key] <> $value)
                {
                  addTrackAndTrace('Clienten', $consolidatiesHuidig[$vPortefeuille]['Client']['id'], $key, $consolidatiesHuidig[$vPortefeuille]['Client'][$key], $value, 'sys');
                }
              }
            }
            $tmpQuery .= $updateDefault . " WHERE id='" . $consolidatiesHuidig[$vPortefeuille]['Client']['id'] . "'";
            if($this->updateClient==true)
            {
              $queries[] = $tmpQuery;
            }
          }
        }
      }
      if(!isset($consolidatiesHuidig[$vPortefeuille]['Portefeuille']))
      {
        $tmpQuery = 'INSERT INTO Portefeuilles SET ';
        foreach ($vpData['Portefeuille'] as $key => $value)
          $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
        $tmpQuery .= $insertDefault;
        if($this->insertPortefeuille==true)
        {
          $queries[] = $tmpQuery;
        }
      }
      else
      {
        $update=false;
        foreach($vpData['Portefeuille'] as $key=>$value)
        {
          $value=trim($value);
          $huidigeConsolidatieValue=trim($consolidatiesHuidig[$vPortefeuille]['Portefeuille'][$key]);
          if($value <> $huidigeConsolidatieValue)
          {
            //echo "$key|$value| <> |".$consolidatiesHuidig[$vPortefeuille]['Portefeuille'][$key]."| <br>\n";
            $update=true;
          }
        }
        if($update==true)
        {
          $tmpQuery='UPDATE Portefeuilles SET ';
          foreach($vpData['Portefeuille'] as $key=>$value)
          {
            $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
            if($this->showOnly==false)
            {
              if($consolidatiesHuidig[$vPortefeuille]['Portefeuille'][$key] <> $value)
              {
                addTrackAndTrace('Portefeuilles', $consolidatiesHuidig[$vPortefeuille]['Portefeuille']['id'], $key, $consolidatiesHuidig[$vPortefeuille]['Portefeuille'][$key], $value, 'sys');
              }
            }
          }
          $tmpQuery.=$updateDefault." WHERE id='".$consolidatiesHuidig[$vPortefeuille]['Portefeuille']['id']."'";
          $queries[]=$tmpQuery;


        }
      }

      if(!isset($consolidatiesHuidig[$vPortefeuille]['Rekeningen']))
      {
        foreach ($vpData['Rekeningen'] as $rekening => $rekeningData)
        {
          $tmpQuery = 'INSERT INTO Rekeningen SET ';
          foreach ($rekeningData as $key => $value)
            $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
          $tmpQuery .= $insertDefault;
          $queries[] = $tmpQuery;
        }
      }
      else
      {
        foreach ($vpData['Rekeningen'] as $rekening => $rekeningData)
        {
          if(!isset($consolidatiesHuidig[$vPortefeuille]['Rekeningen'][$rekening]))
          {
            $tmpQuery = 'INSERT INTO Rekeningen SET ';
            foreach ($rekeningData as $key => $value)
              $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
            $tmpQuery .= $insertDefault;
            $queries[] = $tmpQuery;
          }
          else
          {
            $update=false;
            foreach($rekeningData as $key=>$value)
            {
              if($value <> $consolidatiesHuidig[$vPortefeuille]['Rekeningen'][$rekening][$key])
              {
                $update=true;
              }
            }
            if($update==true)
            {
              $tmpQuery='UPDATE Rekeningen SET ';
              foreach($rekeningData as $key=>$value)
              {
                $tmpQuery .= "$key='" . mysql_real_escape_string($value) . "',";
                if($this->showOnly==false)
                {
                  if($consolidatiesHuidig[$vPortefeuille]['Rekeningen'][$rekening][$key] <> $value)
                  {
                    addTrackAndTrace('Rekeningen', $consolidatiesHuidig[$vPortefeuille]['Rekeningen'][$rekening]['id'], $key, $consolidatiesHuidig[$vPortefeuille]['Rekeningen'][$rekening][$key], $value, 'sys');
                  }
                }
              }
              $tmpQuery.=$updateDefault." WHERE id='".$consolidatiesHuidig[$vPortefeuille]['Rekeningen'][$rekening]['id']."'";
              $queries[]=$tmpQuery;
            }
          }
        }
      }
    }


    foreach($consolidatiesHuidig as $vPortefeuille=>$vpData)
    {
      foreach($vpData['Rekeningen'] as $rekening=>$rekeningData)
      {
        if(!isset($consolidatiesNieuw[$vPortefeuille]['Rekeningen'][$rekening]))
        {
          $query="DELETE FROM Rekeningen WHERE id='".$rekeningData['id']."'";
          $queries[]=$query;
        }
      }
    }

    if($this->debug)
    {
      echo "<br>\nDebug queries:<br>\n";
      listarray($queries);
    }
    if($this->showOnly==false)
    {
      foreach ($queries as $query)
      {
    
        $this->db->SQL($query);
        $this->db->Query();
      }
    }
    $this->mutatieAantal=count($queries);
    $this->verwijderOudeConsolidaties();

  }

  function verwijderOudeConsolidaties()
  {

    $opschoonQueries=array();
    //$opschoonQueries['Clienten']="DELETE Clienten FROM Clienten LEFT JOIN GeconsolideerdePortefeuilles ON Clienten.Client=GeconsolideerdePortefeuilles.Client WHERE consolidatie=1 AND GeconsolideerdePortefeuilles.id is null";
    $opschoonQueries['Portefeuilles']="UPDATE Portefeuilles LEFT JOIN GeconsolideerdePortefeuilles ON Portefeuilles.Portefeuille = GeconsolideerdePortefeuilles.VirtuelePortefeuille
LEFT JOIN PortefeuillesGeconsolideerd ON Portefeuilles.Portefeuille=PortefeuillesGeconsolideerd.VirtuelePortefeuille
SET Portefeuilles.Einddatum = NOW() WHERE consolidatie = 1 AND Portefeuilles.Einddatum > NOW() AND
GeconsolideerdePortefeuilles.id IS NULL AND PortefeuillesGeconsolideerd.id IS NULL AND Portefeuilles.change_date < now()-interval 1 day";
    $opschoonQueries['Rekeningen']="DELETE Rekeningen FROM Rekeningen
LEFT JOIN GeconsolideerdePortefeuilles ON Rekeningen.Portefeuille=GeconsolideerdePortefeuilles.VirtuelePortefeuille
LEFT JOIN PortefeuillesGeconsolideerd ON Rekeningen.Portefeuille=PortefeuillesGeconsolideerd.VirtuelePortefeuille
WHERE consolidatie=1 AND GeconsolideerdePortefeuilles.id is null AND PortefeuillesGeconsolideerd.id is null";

    $consolidatieVerdeling=$this->bepaalConsolidaties(null,true);

    foreach($consolidatieVerdeling['consolidatiesHuidig'] as $vPortefeuille=>$vpData)
    {
      if(is_array($vpData['Rekeningen']))
      {
        foreach ($vpData['Rekeningen'] as $rekening => $rekeningData)
        {
          if (isset($consolidatieVerdeling['consolidatiesNieuw'][$vPortefeuille]['Rekeningen']) && !isset($consolidatieVerdeling['consolidatiesNieuw'][$vPortefeuille]['Rekeningen'][$rekening]))
          {
            $query = "DELETE FROM Rekeningen WHERE consolidatie=1 AND id='" . $rekeningData['id'] . "'";
            $opschoonQueries[] = $query;
          }
        }
      }
    }

    if($this->debug)
    {
      listarray($opschoonQueries);
    }
    if($this->showOnly==false)
    {
      foreach ($opschoonQueries as $query)
      {
        $this->db->SQL($query);
        $this->db->Query();
      }
    }

    logIt("Consolidaties opschonen. (".count($opschoonQueries).") queries uitgevoerd.");

  }
}


?>
