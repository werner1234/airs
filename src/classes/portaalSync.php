<?php
/*
AE-ICT source module
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/18 14:50:53 $
File Versie					: $Revision: 1.8 $

$Log: portaalSync.php,v $
Revision 1.8  2020/07/18 14:50:53  rvv
*** empty log message ***

Revision 1.7  2019/06/12 15:18:49  rvv
*** empty log message ***

Revision 1.6  2019/01/19 13:51:39  rvv
*** empty log message ***

Revision 1.5  2019/01/16 16:33:01  rvv
*** empty log message ***

Revision 1.4  2018/10/13 17:13:51  rvv
*** empty log message ***

Revision 1.3  2018/08/18 12:40:13  rvv
php 5.6 & consolidatie

Revision 1.2  2018/03/10 18:18:16  rvv
*** empty log message ***

Revision 1.1  2018/03/07 16:49:11  rvv
*** empty log message ***


*/

class portaalSync
{
  function portaalSync()
  {

  }

  function valid_email_quick($address)
  {
    $multipleEmail=explode(";",$address);
    foreach ($multipleEmail as $address)
    {
      $address=trim($address);
      if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$/i", $address) || (strlen($address)==0))
        return false;
    }
    return true;
  }


  function CRM_syncPortaalPortefeuilleClusters($echoData=true,$auto=false)
  {
    global $USR;
    $queries=array();
    $portaalUpdates=array();
    $toevoegenIds=array();
    $verwijderIds=array();
    $db=new DB();
    $query="SELECT * FROM portefeuilleClusters WHERE portaal=1 ORDER BY cluster";
    $db->SQL($query);
    $db->Query();
    if($db->records()==0)
      return 0;
    $clusterDataPortefeuille=array();
    $clusterPortefeuillesById=array();
    while($data=$db->nextRecord())
    {
      if($data['actief']==1)
        $data['geblokkeerd']=0;
      else
        $data['geblokkeerd']=1;
      unset($data['actief']);

      $data['portefeuille']="CLU:".$data['cluster'];//str_pad($data['cluster'], 6, '0', STR_PAD_LEFT);
      $data['email']=$data['emailAdres'];
      $data['password']=$data['wachtwoord'];
      $data['name']=$data['clusterOmschrijving'];


      $portefeuillesTxt='';
      for($i=1;$i<31;$i++)
      {
        if($data['portefeuille'.$i] <> '')
        {
          $portefeuillesTxt.=$data['portefeuille'.$i].",";
        }
      }

      $data['portefeuilles']=$portefeuillesTxt;
      $clusterDataPortefeuille[$data['portefeuille']]=$data;
      $clusterPortefeuillesById[$data['id']]=$data['portefeuille'];
    }

    $dbPortaal = new DB(DBportaal);
    $query="SELECT id,name,name1,email,password,geblokkeerd,portefeuilles,verzendAanhef, portefeuille FROM clusterPortefeuilles";
    $dbPortaal->SQL($query);
    $dbPortaal->Query();
    $aanwezigeClusters=array();
    while($data=$dbPortaal->nextRecord())
    {
      $emails = explode(';', $data['email']);
      $data['email'] = $emails[0];
      $aanwezigeClusters[$data['portefeuille']]=$data;
    }

    $html='<br><table><tr><td><b>cluster</b></td><td><b>naam</b></td><td><b>check</b></td></tr>';
    foreach($clusterDataPortefeuille as $portefeuille=>$clusterData)
    {
      //$query="SELECT id,name,name1,email,password,geblokkeerd,portefeuilles,verzendAanhef FROM clusterPortefeuilles WHERE portefeuille='".$clusterData['portefeuille']."'";
      //$dbPortaal->SQL($query);
      //$portaalData=$dbPortaal->lookupRecord();
      $portaalData=$aanwezigeClusters[$clusterData['portefeuille']];
      unset($aanwezigeClusters[$clusterData['portefeuille']]);
      $update=array();
      $html.='<tr><td>'.$clusterData['portefeuille'].'</td><td>'.$clusterData['name'].'</td><td>';
      foreach($portaalData as $key=>$value)
      {
        if($key <> 'id' && $clusterData[$key] <> $value && ($clusterData[$key]<>'' || $key=='geblokkeerd'))
        {
          if($key=='password')
          {
            if(strlen($clusterData['password'])>5)
            {
              $update[$key]="'".mysql_real_escape_string($clusterData[$key])."'";
              $update['passwordChange']='now()';
              $html.='('.$key.':'.$clusterData[$key].' <> '.$value.') ';
            }
            else
              $html.='('.$key.':ongeldig wachtwoord) ';
          }
          elseif($key=='email')
          {
            //$emails=explode(';',$clusterData['email']);
            //$clusterData['email']=$emails[0];
            if($this->valid_email_quick($clusterData['email']))
            {
              $update[$key]="'".mysql_real_escape_string($clusterData[$key])."'";
              $html.='('.$key.':'.$clusterData[$key].' <> '.$value.') ';
            }
            else
              $html.='('.$key.':ongeldig emailadres) ';
          }
          else
          {
            $update[$key]="'".mysql_real_escape_string($clusterData[$key])."'";
            $html.='('.$key.':'.$clusterData[$key].' <> '.$value.') ';
          }
        }
      }

      if($portaalData['id'] > 0)
      {
        if(count($update)>0)
          $portaalUpdates['update'][$portaalData['id']]=$update;
        else
        {
          $html.='Geen update';
        }
      }
      else
      {
        $html.='Nog niet in portaal.';
        //$emails=explode(';',$clusterData['email']);
        //$clusterData['email']=$emails[0];
        if(strlen($clusterData['password'])>5  && $this->valid_email_quick($clusterData['email']))
        {
          $html.=" <input type='checkbox' name='ctoevoegen_id_".$clusterData['id']."' value='1'> Toevoegen.";
          $toevoegenIds[]=$clusterData['id'];
        }
        else
        {
          if(strlen($clusterData['password']) < 6)
            $html.="Wachtwoordlengte:".strlen($clusterData['password'])." tekens. Minimaal 6 tekens nodig.";
          if($this->valid_email_quick($clusterData['email'])==0)
            $html.="Geen geldig email adres.";
        }


      }
      $html.='</td></tr>';
    }
    foreach($aanwezigeClusters as $clusterdata)
    {
      $html.="<tr><td>".$clusterdata['portefeuille']."</td><td>".$clusterdata['name']."</td><td>Nog in portaal  <input type='checkbox' name='cverwijder_id_".$clusterdata['id']."' value='1'> Verwijderen</td></tr>\n";
      $verwijderIds[]=$clusterdata['id'];
    }

    if($echoData==false)
    {
      if ($auto == false)
      {
        $toevoegenIds = array();
        $verwijderIds =array();
        foreach ($_POST as $key => $value)
        {
          if (substr($key, 0, 13) == 'ctoevoegen_id')
          {
            $clusterId = substr($key, 14);
            $toevoegenIds[] = $clusterId;
          }
          if (substr($key, 0, 13) == 'cverwijder_id')
          {
            $clusterId = substr($key, 14);
            $verwijderIds[] = $clusterId;
          }
        }
      }

      foreach ($toevoegenIds as $clusterId)
      {
        $portefeuille = $clusterPortefeuillesById[$clusterId];
        $portefeuilleData = $clusterDataPortefeuille[$portefeuille];
        if (count($portefeuilleData) > 0 && $dbPortaal->QRecords("SELECT id FROM clusterPortefeuilles WHERE portefeuille='" . mysql_real_escape_string($portefeuilleData['portefeuille']) . "'") < 1)
        {
          $queries['inserts'][] = "INSERT INTO clusterPortefeuilles SET portefeuille='" . mysql_real_escape_string($portefeuilleData['portefeuille']) . "',
                                          name='" . mysql_real_escape_string($portefeuilleData['name']) . "',
                                          name1='" . mysql_real_escape_string($portefeuilleData['name1']) . "',
                                          verzendAanhef ='" . mysql_real_escape_string($portefeuilleData['verzendAanhef']) . "',
                                          email='" . mysql_real_escape_string($portefeuilleData['email']) . "',
                                          portefeuilles='" . mysql_real_escape_string($portefeuilleData['portefeuilles']) . "',
                                          password='" . mysql_real_escape_string($portefeuilleData['password']) . "',
                                          geblokkeerd='" . mysql_real_escape_string($portefeuilleData['geblokkeerd']) . "',
                                          add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
        }
      }
      foreach($verwijderIds as $clusterId)
      {
        $queries['deletes'][$clusterId]="DELETE FROM clusterPortefeuilles WHERE id='$clusterId'";
      }
  
      $deleteLog='';
      if (count($queries) > 0)
      {
        foreach ($queries['inserts'] as $insertQuery)
        {
          $dbPortaal->SQL($insertQuery);
          $dbPortaal->Query();
          $lastId = $dbPortaal->last_id();
          $portaalUpdates['insert'][$lastId] = $insertQuery;
        }
        
        foreach ($queries['deletes'] as $clusterId=>$deleteQuery)
        {
          $dbPortaal->SQL($deleteQuery);
          $dbPortaal->Query();
          $portaalUpdates['deletes'][$clusterId] = $deleteQuery;
          $deleteLog.="$deleteQuery\n";
        }
      }
      if($deleteLog<>'')
        logIt("Portaal cluster delete: ".$deleteLog);
    }
  
    if($echoData==true)
      echo $html;

    return $portaalUpdates;
  }

  function CRM_syncPortaal($echoData=true)
  {
    global $USR;
    $insert=array();
    $portaalUpdates=array();
    $db=new DB();
  
    $veldenKoppeling=array('name'=>'naam','name1'=>'naam1','email'=>'email','password'=>'wachtwoord','verzendAanhef'=>'verzendAanhef','depotbank'=>'Depotbank',
                           'accountmanagerNaam'=>'accountmanagerNaam','accountmanagerGebruikerNaam'=>'accountmanagerGebruikerNaam','accountmanagerTelefoon'=>'accountmanagerTelefoon',
                           'accountmanagerEmail'=>'accountmanagerEmail','geblokkeerd'=>'aktief','rel_id'=>'crmId');
   
    $query="SELECT CRM_naw.id,
CRM_naw.naam as name,
CRM_naw.naam1 as name1,
CRM_naw.email,
CRM_naw.wachtwoord as password,
CRM_naw.portefeuille,
CRM_naw.verzendAanhef,
CRM_naw.CRMGebrNaam,
CRM_naw.aktief,
CRM_naw.id as rel_id,
Portefeuilles.depotbank,
Portefeuilles.risicoKlasse,
Portefeuilles.soortOvereenkomst,
Accountmanagers.Naam as accountmanagerNaam,
Gebruikers.Naam as accountmanagerGebruikerNaam,
Gebruikers.mobiel as accountmanagerTelefoon,
Gebruikers.emailAdres as accountmanagerEmail
FROM CRM_naw
LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille
LEFT JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager
LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
WHERE CRM_naw.portefeuille <> '' OR CRM_naw.CRMGebrNaam <> '' ORDER BY CRM_naw.zoekveld,CRM_naw.naam";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if($data['aktief']==1)
        $data['geblokkeerd']=0;
      else
        $data['geblokkeerd']=1;
      unset($data['aktief']);

      if(trim($data['portefeuille']) == '' && $data['CRMGebrNaam'] <> '')
        $data['portefeuille']='P'.str_pad($data['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
  
      $emails = explode(';', $data['email']);
      $data['email'] = $emails[0];
      
      $crmPortefeuilles[$data['portefeuille']]=$data;
      $crmPortefeuillesById[$data['id']]=$data['portefeuille'];
    }

    $dbPortaal = new DB(DBportaal);
    if($echoData==false)
    {
      foreach ($_POST as $key => $value)
      {
        if (substr($key, 0, 12) == 'toevoegen_id')
        {
          $crmId = substr($key, 13);
          //SELECT id,name,name1,email,password FROM  WHERE portefeuille='".$crmPortefeuilleData['portefeuille']."'
          $portefeuille = $crmPortefeuillesById[$crmId];
          $crmData = $crmPortefeuilles[$portefeuille];
          if (count($crmData) > 0 && $dbPortaal->QRecords("SELECT id FROM clienten WHERE rel_id='" . mysql_real_escape_string($crmData['rel_id']) . "'")<1)
          {
  
            
            $insert[] = "INSERT INTO clienten SET portefeuille='" . mysql_real_escape_string($crmData['portefeuille']) . "', 
                                          rel_id='" . mysql_real_escape_string($crmData['rel_id']) . "',
                                          name='" . mysql_real_escape_string($crmData['name']) . "',
                                          name1='" . mysql_real_escape_string($crmData['name1']) . "',
                                          email='" . mysql_real_escape_string($crmData['email']) . "',
                                          verzendAanhef='" . mysql_real_escape_string($crmData['verzendAanhef']) . "',
                                          password='" . mysql_real_escape_string($crmData['password']) . "',
                                          geblokkeerd='" . mysql_real_escape_string($crmData['geblokkeerd']) . "',
                                          add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
          }
        }
      }

      if(count($insert)>0)
      {
        foreach ($insert as $insertQuery)
        {
          $dbPortaal->SQL($insertQuery);
          $dbPortaal->Query();
          $lastId = $dbPortaal->last_id();
          $portaalUpdates['insert'][$lastId] = $insertQuery;
        }
      }
    }
  
  
    
    
    $html='<table><tr><td><b>portefeuille</b></td><td><b>naam</b></td><td><b>check</b></td></tr>';
    foreach($crmPortefeuilles as $portefeuille=>$crmPortefeuilleData)
    {
      $query="SELECT id,rel_id,name,name1,email,password,geblokkeerd,verzendAanhef,depotbank,accountmanagerNaam,accountmanagerGebruikerNaam,accountmanagerTelefoon,accountmanagerEmail,portefeuille,risicoKlasse,soortOvereenkomst FROM clienten WHERE rel_id='".$crmPortefeuilleData['rel_id']."'";
      $dbPortaal->SQL($query);
      $portaalData=$dbPortaal->lookupRecord();

      $update=array();
      $html.='<tr><td>'.$crmPortefeuilleData['portefeuille'].'</td><td>'.$crmPortefeuilleData['name'].'</td><td>';
      foreach($portaalData as $key=>$value)
      {
        if($key <> 'id' && $crmPortefeuilleData[$key] <> $value && ($crmPortefeuilleData[$key]<>'' || $key=='geblokkeerd'))
        {
          if($key=='password')
          {
            if(strlen($crmPortefeuilleData['password'])>5)
            {
              $update[$key]="'".mysql_real_escape_string($crmPortefeuilleData[$key])."'";
              $update['passwordChange']='now()';
              $html.='('.$key.':'.$crmPortefeuilleData[$key].' <> '.$value.') ';
            }
            else
              $html.='('.$key.':ongeldig wachtwoord) ';
          }
          elseif($key=='email')
          {
            //$emails=explode(';',$crmPortefeuilleData['email']);
            //$crmPortefeuilleData['email']=$emails[0];
            if($this->valid_email_quick($crmPortefeuilleData['email']))
            {
              $update[$key]="'".mysql_real_escape_string($crmPortefeuilleData[$key])."'";
              $html.='('.$key.':'.$crmPortefeuilleData[$key].' <> '.$value.') ';
            }
            else
              $html.='('.$key.':ongeldig emailadres) ';
          }
          else
          {
            $update[$key]="'".mysql_real_escape_string($crmPortefeuilleData[$key])."'";
            $html.='('.$key.':'.$crmPortefeuilleData[$key].' <> '.$value.') ';
          }
        }
      }

      if($portaalData['id'] > 0)
      {
        if(count($update)>0)
          $portaalUpdates['update'][$portaalData['id']]=$update;
        else
        {
          $html.='Geen update';
        }
      }
      else
      {
        $html.='Nog niet in portaal.';
        $emails=explode(';',$crmPortefeuilleData['email']);
        $crmPortefeuilleData['email']=$emails[0];
        if(strlen($crmPortefeuilleData['password'])>5  && $this->valid_email_quick($crmPortefeuilleData['email']))
        {
          $html.=" <input type='checkbox' name='toevoegen_id_".$crmPortefeuilleData['id']."' value='1'> Toevoegen.";
        }


      }
      $html.='</td></tr>';
    }

    if($echoData==true)
      echo $html;

    return $portaalUpdates;
  }

  function updatePortaal($portaalUpdates)
  {
    global $USR;
    $dbPortaal = new DB(DBportaal);
    foreach($portaalUpdates['update'] as $clientId=>$update)
    {
      $query="UPDATE clienten SET change_date=now(),change_user='$USR' ";
      foreach($update as $key=>$value)
        $query.=",$key=$value";
      $query.=" WHERE id='$clientId'";
      $dbPortaal->SQL($query);
      if(!$dbPortaal->Query())
      {
        echo "Client update in portaal mislukt.";exit;
      }
    }
    $melding="(".count($portaalUpdates['insert']).") relaties toegevoegd.<br>\n";
    $melding.="(".count($portaalUpdates['update']).") aanpassingen doorgevoerd.";

   return $melding;
  }

  function updatePortaalClusters($portaalUpdates)
  {
    global $USR;
    if(is_array($portaalUpdates))
    {
      $dbPortaal = new DB(DBportaal);
      foreach ($portaalUpdates['update'] as $clientId => $update)
      {
        $query = "UPDATE clusterPortefeuilles SET change_date=now(),change_user='$USR' ";
        foreach ($update as $key => $value)
        {
          $query .= ",$key=$value";
        }
        $query .= " WHERE id='$clientId'";
        $dbPortaal->SQL($query);
        if (!$dbPortaal->Query())
        {
          echo "clusterPortefeuilles update in portaal mislukt.";
          exit;
        }
      }
      $melding = "(" . count($portaalUpdates['insert']) . ") clusters toegevoegd.<br>\n";
      $melding .= "(" . count($portaalUpdates['update']) . ") cluster aanpassingen doorgevoerd.<br>\n";
      $melding .= "(" . count($portaalUpdates['deletes']) . ") clusters verwijderd.";
    }
    return $melding;
  }


}

?>