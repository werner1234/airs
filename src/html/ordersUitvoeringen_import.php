<?php

/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/06/06 15:46:39 $
    File Versie         : $Revision: 1.3 $

    $Log: ordersEditBulkV2_import.php,v $
    Revision 1.3  2020/06/06 15:46:39  rvv
    *** empty log message ***

    Revision 1.2  2020/02/26 16:10:59  rvv
    *** empty log message ***

    Revision 1.1  2020/02/12 16:41:25  rvv
    *** empty log message ***



*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_FIXtransport.php");
include_once('orderControlleRekenClassV2.php');
include_once('../config/ordersVars.php');

function getalCheck($getal)
{
  $melding='';
  if(substr_count($getal,'.')>1)
  {
    $melding="Ongeldig getal, meer dan één . gevonden in $getal";
  }
  if(substr_count($getal,',')>1)
  {
    $melding="Ongeldig getal, meer  dan één , gevonden in $getal";
  }
  if(substr_count($getal,'.')>=1 && substr_count($getal,',')>=1)
  {
    $melding="Ongeldig getal, meer dan één . of , gevonden in $getal";
  }
  
  $fixedGetal =str_replace(",",'.',$getal);
  
  return array($fixedGetal,$melding);
}

function datumCheck($datum)
{
  $melding='';
  $parts=explode('-',$datum);
  if(strlen($datum)<>10)
  {
    $melding = "Ongeldig datum, verwacht 10 tekens.";
  }
  elseif(count($parts)<>3)
  {
    $melding = "Ongeldige datum, verwacht 2 - tekens.";
  }
  elseif(strlen($parts[0])==4&&strlen($parts[1])==2&&strlen($parts[2])==2)
  {
    $fixedDatum=$parts[0].'-'.$parts[1].'-'.$parts[2];
  }
  elseif(strlen($parts[0])==2&&strlen($parts[1])==2&&strlen($parts[2])==4)
  {
    $fixedDatum=$parts[2].'-'.$parts[1].'-'.$parts[0];
  }
  
  return array($fixedDatum,$melding);
}

if($_POST['import']=='true')
{
  global $__appvar;
  $fix=new AE_FIXtransport();
  if (!$handle = @fopen($_FILES['importfile']['tmp_name'], "r"))
  {
    echo "FOUT bestand is niet leesbaar";exit;
  }
  $csvData=array();
  $i=0;
  while ($data = fgetcsv($handle, 10000, ";"))
  {
    if($i==0)
    {
      $header = $data;
      $headerLookup=array();
      foreach ($header as $index=>$veld)
        $headerLookup[$index]=trim($veld);
    }
    else
    {
      $csvData[] = $data;
    }
    $i++;
  
  }
  $headerVelden=array('orderid','uitvoeringsAantal','uitvoeringsDatum','uitvoeringsPrijs');

  $afbreken=false;
  foreach($headerVelden as $headerVeld)
  {
    if(!in_array($headerVeld,$headerLookup))
    {
      echo "Afwijking in verwachte velden. Veld $headerVeld niet gevonden.<br>\n";
      $afbreken=true;
    }
  }
  if($afbreken==true)
    exit;
  $db=new DB();
  if($_POST['action']=='test')
  {
    echo "<table border='0'>";
    echo "<tr><td><b>OrderId</b></td><td><b>uitvoeringsAantal</b></td><td><b>uitvoeringsDatum</b></td><td><b>uitvoeringsPrijs</b></td><td><b>orderAantal</b></td><td><b>Fonds</b></td><td><b>ISIN</b></td><td><b>Fondskoers</b></td><td><b>AanwezigeUitvoeringen</b></td></tr>\n";
  }
  foreach($csvData as $dataRegel)
  {
    $object=new OrderUitvoeringV2();//Fonds();
    $editObject=new editObject($object);
    $editObject->__appvar=$__appvar;
    $opslaangelukt=false;
    foreach($headerLookup as $index=>$veld)
    {
      $dataRegel[$veld]=$dataRegel[$index];
    }
    
    if(!isset($dataRegel['orderid']))
    {
      echo "orderid niet gevonden. Import afgebroken.<br>\n";
      exit;
    }
    else
    {
      $query="SELECT id,orderstatus,fonds,ISINCode FROM OrdersV2 WHERE id='".mysql_real_escape_string($dataRegel['orderid'])."'";
      $db->SQL($query);
      $status=$db->lookupRecord();
      if(!isset($status['orderstatus']))
      {
        echo "Geen order met orderid ".$dataRegel['orderid']." gevonden.<br>\n";
        continue;
      }
      elseif($status['orderstatus']<>1)
      {
        echo "orderid ".$dataRegel['orderid']." heeft niet de status doorgegeven. (".$__ORDERvar["status"][$status['orderstatus']].") Import afgebroken.<br>\n";
        continue;
      }
  
      if($_POST['action']=='test')
      {
        $query="SELECT Koers FROM Fondskoersen WHERE Fonds='".mysql_real_escape_string($status['fonds'])."' ORDER BY Datum desc limit 1";
        $db->SQL($query);
        $tmp=$db->lookupRecord();
        $status['koers']=$tmp['Koers'];
        if($status['ISINCode'] == '')
        {
          $query="SELECT ISINCode FROM Fondsen WHERE Fonds='".mysql_real_escape_string($status['fonds'])."'";
          $db->SQL($query);
          $tmp=$db->lookupRecord();
          $status['ISINCode']=$tmp['ISINCode'];
        }
        $query="SELECT round(SUM(Aantal),4) as aantal FROM OrderRegelsV2 WHERE orderid='".mysql_real_escape_string($dataRegel['orderid'])."'";
        $db->SQL($query);
        $tmp=$db->lookupRecord();
        $status['orderAantal']=$tmp['aantal'];
        $query="SELECT round(SUM(uitvoeringsAantal),4) as aantal FROM OrderUitvoeringV2 WHERE orderid='".mysql_real_escape_string($dataRegel['orderid'])."'";
        $db->SQL($query);
        $tmp=$db->lookupRecord();
        $status['aanwezigeUitvoeringen']=$tmp['aantal'];
      }
    }
  
    foreach(array('uitvoeringsAantal','uitvoeringsPrijs') as $veld)
    {
      $dataRegel[$veld] = str_replace(",", '.', $dataRegel[$veld]);
      $tmp = getalCheck($dataRegel[$veld]);
      if ($tmp[1] <> '')
      {
        echo $tmp[1] . " Regel wordt niet geimporteerd.<br>\n";
        continue;
      }
      else
      {
        $dataRegel[$veld] = $tmp[0];
      }
    }
  
    $tmp = datumCheck($dataRegel['uitvoeringsDatum']);
    if ($tmp[1] <> '')
    {
      echo $tmp[1] . " Regel wordt niet geimporteerd.<br>\n";
      continue;
    }
    else
    {
      $dataRegel['uitvoeringsDatum'] = $tmp[0];
    }

    if($_POST['action']=='import')
    {
      echo "Verwerken uitvoering voor orderId:" . $dataRegel['orderid'] . "<br>\n";
      $editObject->controller('update', $dataRegel);
  
      if ($editObject->object->error == true)
      {
        foreach ($editObject->object->data['fields'] as $veld => $details)
        {
          if (isset($details['error']))
          {
            echo "$veld " . $details['value'] . " " . $details['error'] . "<br>\n";
          }
          $dataRegel[$veld] = $details['value'];
        }
        $object = new OrderUitvoeringV2();//Fonds();
        $editObject = new editObject($object);
        $editObject->__appvar = $__appvar;
        $editObject->controller('update', $dataRegel);
        if ($editObject->object->error == true)
        {
          echo "Tweede poging.<br>\n";
          foreach ($editObject->object->data['fields'] as $veld => $details)
          {
            if (isset($details['error']))
            {
              echo "$veld " . $details['value'] . " " . $details['error'] . "<br>\n";
            }
          }
        }
        else
        {
          echo "Tweede poging na correctie gelukt.<br>\n";
          $opslaangelukt = true;
          $orderId = $editObject->object->get('orderid');
        }
      }
      else
      {
        $opslaangelukt = true;
        $orderId = $editObject->object->get('orderid');
    
      }
  
      if ($opslaangelukt == true)
      {
        echo "Opslag van record voor $orderId gelukt.<br>\n";
        checkUitvoeringenComplete($orderId);
      }
    }
    elseif($_POST['action']=='test')
    {
      if(($dataRegel['uitvoeringsAantal']+$status['aanwezigeUitvoeringen']) <> $status['orderAantal'])
      {
        $kleur="bgcolor='red'";
      }
      else
      {
        $kleur='';
      }
      echo "<tr>
<td>".$dataRegel['orderid']."</td>
<td align='right' $kleur >".number_format($dataRegel['uitvoeringsAantal'],4,',','.')."</td>
<td>".$dataRegel['uitvoeringsDatum']."</td>
<td align='right'>".number_format($dataRegel['uitvoeringsPrijs'],2,',','.')."</td>
<td align='right'>".number_format($status['orderAantal'],4,',','.')."</td>
<td>".$status['fonds']."</td>
<td>".$status['ISINCode']."</td>
<td align='right'>".number_format($status['koers'],4,',','.')."</td>
<td align='right'>".number_format($status['aanwezigeUitvoeringen'],4,',','.')."</td>
</tr>";
    }
  }
  if($_POST['action']=='test')
  {
    echo "</table>";
  }
  
  if($_POST['action']=='import')
  {
    echo "<button onclick=\"window.location.href = 'ordersListV2.php';\">Terug naar order lijst</button>";
    exit;
  }
  
}

if($_GET['action']!='select')
{
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem('Import from file', 'ordersUitvoeringen_import.php?action=select');
}

if($_GET['action']=='select'||$_POST['action']=='test')
{
  echo template($__appvar["templateContentHeader"],$content);
  if($_POST['action']=='test')
  {
    $action='import';
    $knopTxT="Import";
  }
  else
  {
    $action='test';
    $knopTxT="test import";
  }
  
  ?>
  
  <form enctype="multipart/form-data" action="ordersUitvoeringen_import.php" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="16777216" />
    <input type="hidden" name="import" value="true" />
    <input type="hidden" name="action" value="<?=$action?>" />
    <b>Importeren uit bestand</b><br><br>
    
    
    <div class="form"><div class="formblock"><div class="formlinks"></div><div class="formrechts"><input type="file" name="importfile" size="50"></div></div>
      
      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts">
       <input type="submit" value="<?=$knopTxT?>">
        </div>
      </div>
  
  </form>
  <?
  echo template($__appvar["templateRefreshFooter"],$content);
  exit;
}



function checkUitvoeringenComplete($orderId)
{
  global $USR,$__ORDERvar;
  $db=new DB();
  $query="SELECT round(SUM(uitvoeringsAantal),4) as aantal FROM OrderUitvoeringV2 WHERE orderid='".$orderId."'";
  $db->SQL($query);
  $OrderUitvoering=$db->lookupRecord();
  $query="SELECT round(SUM(Aantal),4) as aantal FROM OrderRegelsV2 WHERE orderid='".$orderId."'";
  $db->SQL($query);
  $OrderRegels=$db->lookupRecord();
  $query="SELECT orderStatus FROM OrdersV2 WHERE id='".$orderId."'";
  $db->SQL($query);
  $OrderStatusOld=$db->lookupRecord();
  
  if($OrderUitvoering['aantal'] == $OrderRegels['aantal'] && $OrderUitvoering['aantal'] > 0)
  {
    $query="UPDATE OrdersV2 SET orderStatus=2,change_date=now(),change_user='$USR' WHERE orderStatus<2 AND id='".$orderId."'";
    $db->SQL($query);
    $db->Query();
    $query="SELECT orderStatus FROM OrdersV2 WHERE id='".$orderId."'";
    $db->SQL($query);
    $OrderStatus=$db->lookupRecord();
  
    $query="UPDATE OrderRegelsV2 SET orderregelStatus=2,change_date=now(),change_user='$USR' WHERE orderregelStatus<2 AND orderid='".$orderId."'";
    $db->SQL($query);
    $db->Query();
    updateBrutoWaardeV2($orderId);
    $orderLogs = new orderLogs();
    $orderLogs->addToLog($orderId, null, "orderStatus naar " . $__ORDERvar['orderStatus'][$OrderStatus['orderStatus']] . " |" . $OrderStatusOld['orderStatus'] . "->" . $OrderStatus['orderStatus']);
    $fix=new AE_FIXtransport();
    $fix->verzendStatusMail($orderId,$OrderStatusOld['orderStatus'],$OrderStatus['orderStatus']);
    echo " checkUitvoeringenComplete($orderId);<br>\n";
  }
}

?>