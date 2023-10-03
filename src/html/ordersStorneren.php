<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/07/22 13:31:35 $
    File Versie         : $Revision: 1.1 $

    $Log: ordersStorneren.php,v $
    Revision 1.1  2020/07/22 13:31:35  rvv
    *** empty log message ***

*/

include_once("wwwvars.php");
$db=new DB();

if(!isset($_GET['ordersVanaf']))
  $_GET['ordersVanaf']=date('01-01-Y');
if(!isset($_GET['ordersTot']))
  $_GET['ordersTot']=date('d-m-Y');

function createSelect($name,$values,$selectedValue)
{
  $html="<select name='$name'>";
  if(!in_array($selectedValue,$values))
  {
    $values=array_reverse($values);
    $values[] = '';
    $values=array_reverse($values);
    $selectedValue='';
  }
  foreach($values as $value)
  {
    if($value==$selectedValue)
      $html.="<option selected value='$value'>$value</option>";
    else
      $html.="<option value='$value'>$value</option>";
  }
  $html.="</select>\n";
  return $html;
}

$content['javascript']='function checkStatus()
{
  var theForm = document.selectForm.elements, z = 0, toonVerwerken=0 ;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == \'checkbox\' && theForm[z].name.substr(0,6) == \'check_\')
   {
      if(theForm[z].checked==true)
      {
        toonVerwerken++;
      }
   }
  }

  if(toonVerwerken>0)
  {
    $(\'#submitKnop\').val(\'Storneringen aanmaken\');
  }
  else
  {
    $(\'#submitKnop\').val(\'Ophalen orderregels\');
  }
}
';
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>Orders storneren</b>
</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);
$verwerken=array();
foreach ($_GET as $key=>$value)
{
  if(substr($key,0,6)=='check_')
  {
    if($value==1)
      $verwerken[]=substr($key,6 );
  }
}
if(count($verwerken)>0)
{
  $selectWhere="OrderRegelsV2.id IN('".implode("','",$verwerken)."')";
  
  $ordersV2Velden=array('fonds','ISINCode','fondsOmschrijving','transactieType','transactieSoort','fondseenheid','fondsValuta','memo','fondsBankcode','optieSymbool','optieType','optieUitoefenprijs','optieExpDatum','beurs','tijdsLimiet','tijdsSoort','koersLimiet','depotbank','orderSoort','giraleOrder','fixOrder','fondssoort','careOrder','batchId');
  $orderRegelsV2Velden=array('portefeuille','client','rekening','aantal','bedrag','externeBatchId');
  $query = "SELECT
OrdersV2.id,
OrdersV2.fonds,
OrdersV2.ISINCode,
OrdersV2.fondsOmschrijving,
OrdersV2.transactieType,
OrdersV2.transactieSoort,
OrdersV2.fondseenheid,
OrdersV2.fondsValuta,
OrdersV2.fondsBankcode,
OrdersV2.optieSymbool,
OrdersV2.optieType,
OrdersV2.optieUitoefenprijs,
OrdersV2.optieExpDatum,
OrdersV2.beurs,
OrdersV2.tijdsLimiet,
OrdersV2.tijdsSoort,
OrdersV2.koersLimiet,
OrdersV2.depotbank,
OrdersV2.orderSoort,
OrdersV2.giraleOrder,
OrdersV2.fixOrder,
OrdersV2.fondssoort,
OrdersV2.careOrder,
OrdersV2.memo,
OrdersV2.notaValutakoers,
OrderRegelsV2.portefeuille,
OrderRegelsV2.rekening,
OrderRegelsV2.aantal,
OrderRegelsV2.bedrag,
OrderRegelsV2.id AS orderregelId,
OrderRegelsV2.orderid,
OrderRegelsV2.add_date,
OrderRegelsV2.client,
OrderRegelsV2.externeBatchId
FROM
OrderRegelsV2
INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE " . $selectWhere;
  $db->SQL($query);
  $db->Query();
  $nieuweOrders = array();
  $spiegelDetails=array();
  
  $batchId="S-AIRS".date('ymd\This').'';
  $batchIdNew="C-AIRS".date('ymd\This').'';
  
  $orderTypen=array('storno'=>array('transactieSoort'=>'flip','externeBatchId'=>$batchId),
                    'portnullaankoop'=>array('transactieSoort'=>'A','portefeuille'=>'0000-0000-0000-0','rekening'=>'0000-0000-0000-0EUR','client'=>'VRYMPF','externeBatchId'=>$batchId),
                    'portnullverkoop'=>array('transactieSoort'=>'V','portefeuille'=>'0000-0000-0000-0','rekening'=>'0000-0000-0000-0EUR','client'=>'VRYMPF','externeBatchId'=>$batchId));
  if($_GET['nieuweOrder']=='J')
  {
    $orderTypen['nieuweOrder'] = array('externeBatchId'=>$batchIdNew);
    $orderTypen['nieuweOrderNullAank'] = array('transactieSoort'=>'A','portefeuille'=>'0000-0000-0000-0','rekening'=>'0000-0000-0000-0EUR','client'=>'VRYMPF','externeBatchId'=>$batchIdNew);
    $orderTypen['nieuweOrderNullVerk'] = array('transactieSoort'=>'V','portefeuille'=>'0000-0000-0000-0','rekening'=>'0000-0000-0000-0EUR','client'=>'VRYMPF','externeBatchId'=>$batchIdNew);
  }
  $orderData=array();
  while ($data = $db->nextRecord())
  {
    $orderData[$data['id']][]=$data;
  }
  
  $nullAankopenVerkopen=array();
  foreach($orderData as $orderId=>$orderregels)
  {
    
    foreach ($orderTypen as $nieuweOrderIndex => $transfromData)
    {
      $orderInsert=false;
      foreach($orderregels as $data)
      {

        $newData = $data;
        foreach ($transfromData as $transfromVeld => $transfromValue)
        {
          if ($transfromVeld == 'transactieSoort')
          {
            if ($transfromValue == 'flip')
            {
              if ($data['transactieSoort'] == 'A')
              {
                $newData['transactieSoort'] = 'V';
              }
              elseif ($data['transactieSoort'] == 'V')
              {
                $newData['transactieSoort'] = 'A';
              }
            }
            else
            {
              $newData['transactieSoort'] = $transfromData['transactieSoort'];
            }
          }
          elseif ($transfromVeld == 'externeBatchId')
          {
            $newData['externeBatchId'] = substr($transfromValue, 0, 1) . $data['transactieSoort'] . substr($transfromValue, 1);
        
          }
          else
          {
            $newData[$transfromVeld] = $transfromValue;
          }
        }
        if($newData['memo']<>'')
        {
          $newData['memo'] .= "\n";
        }
        $newData['memo'].="Storno order $orderId";
        
        //listarray($newData['orderSoort']);
        if($newData['orderSoort']<>'M' || count($orderregels)==1)
          $newData['orderSoort'] = 'E';
    
        if($orderInsert==false)
        {
          $cfg=new AE_config();
          $newBatchId=$cfg->getData('lastOrderBatchId')+1;
          $cfg->addItem('lastOrderBatchId',$newBatchId);
          $newData['batchId']=$newBatchId;
          $orderV2insert = 'INSERT INTO OrdersV2 SET ';
          foreach ($ordersV2Velden as $i => $veld)
          {
            $orderV2insert .= " $veld = '" . mysql_real_escape_string($newData[$veld]) . "', ";
          }
          $orderV2insert .= "add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
          $orderInsert=true;
          if($_GET['debug']==1)
          {
            echo "<br>\n $orderV2insert <br>\n ";
            $lastId=-1;
          }
          else
          {
            //echo "<br>\n $orderV2insert <br>\n ";
            $db->SQL($orderV2insert);
            $db->Query();
            $lastId=$db->last_id();
          }
          if($nieuweOrderIndex=='storno' || ($nieuweOrderIndex=='portnullaankoop' && $data['transactieSoort']=='A') || ($nieuweOrderIndex=='portnullverkoop' && $data['transactieSoort']=='V') )
          {
            $spiegelDetails[$nieuweOrderIndex][$orderId]['order']=$lastId;
          }

        }
        $orderRegelsV2insert = 'INSERT INTO OrderRegelsV2 SET ';
        foreach ($orderRegelsV2Velden as $i => $veld)
        {
          $orderRegelsV2insert .= " $veld = '" . mysql_real_escape_string($newData[$veld]) . "', ";
        }
        $orderRegelsV2insert .= "orderId='$lastId', add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
        if($_GET['debug']==1)
        {
          echo "$orderRegelsV2insert <br>\n";
          $lastId=-1;
          $lastRegelId=-1;
        }
        else
        {
          //echo "$orderRegelsV2insert <br>\n ";
          $db->SQL($orderRegelsV2insert);
          $db->Query();
          $lastRegelId=$db->last_id();
        }
        if($nieuweOrderIndex=='storno' || ($nieuweOrderIndex=='portnullaankoop' && $data['transactieSoort']=='A') || ($nieuweOrderIndex=='portnullverkoop' && $data['transactieSoort']=='V') )
        {
          $spiegelDetails[$nieuweOrderIndex][$orderId]['orderregels'][$data['orderregelId']]=$lastRegelId;
        }
        if($nieuweOrderIndex=='portnullaankoop' || $nieuweOrderIndex=='portnullverkoop' || $nieuweOrderIndex=='nieuweOrderNullAank' || $nieuweOrderIndex=='nieuweOrderNullVerk')
          $nullAankopenVerkopen[$lastId][]=$lastRegelId;
      }
    }
  }

  if(count($spiegelDetails)>0)
  {
    $queries=array();
    foreach($spiegelDetails as $OrderIndex=>$details)
    {
      foreach ($details as $origineleOrderId => $stornoData)
      {
        if ($stornoData['order'] == '-1' && $_GET['debug'] <> 1)
        {
          continue;
        }
        
        $queries[] = "UPDATE OrdersV2 SET orderStatus=2,orderSubStatus=2 WHERE id='" . $origineleOrderId . "' ";
        $queries[] = "UPDATE OrdersV2 SET orderStatus=2,orderSubStatus=2 WHERE id='" . $stornoData['order'] . "' ";
  
  
        $orderaantal=0;
        foreach ($stornoData['orderregels'] as $origineleOrderRegelId => $nieuweOrderregelId)
        {
          $query = "SELECT kosten,brokerkosten,opgelopenRente,brutoBedrag,nettoBedrag as nettoBedrag,brokerkosten,orderbedrag,orderaantal,regelNotaValutakoers,aantal FROM OrderRegelsV2 WHERE id='$origineleOrderRegelId'";
          $db->SQL($query);
          //echo "$query <br>\n";
          $db->Query();
          $velden = $db->nextRecord();
          $orderaantal+=$velden['aantal'];
          $update = "UPDATE OrderRegelsV2 SET ";
          
          foreach ($velden as $veld => $waarde)
          {
            $update .= " $veld = '" . mysql_real_escape_string($waarde) . "', ";
          }
          $update .= " orderregelStatus=2 WHERE id ='$nieuweOrderregelId'";
          $queries[] = $update;
        }
  
        $uitvoeringen = array();
        $query = "SELECT uitvoeringsAantal,now() as uitvoeringsDatum,uitvoeringsPrijs,nettokoers,opgelopenrente,brokerkostenTotaal FROM OrderUitvoeringV2 WHERE orderid='$origineleOrderId'";
        $db->SQL($query);
        $db->Query();
        while ($data = $db->nextRecord())
        {
          $uitvoeringen[] = $data;
        }
  
        foreach ($uitvoeringen as $velden)
        {
          if(count($uitvoeringen)==1 && $orderaantal<>0)
          {
            $velden['uitvoeringsAantal'] = $orderaantal;
          }
          $insert = "INSERT INTO OrderUitvoeringV2 SET ";
          foreach ($velden as $veld => $waarde)
          {
            $insert .= " $veld = '" . mysql_real_escape_string($waarde) . "', ";
          }
          $insert .= "orderId='" . $stornoData['order'] . "', add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
          $queries[] = $insert;
    
        }
    
      }
    }
    if($_GET['debug']==1)
    {
      listarray($queries);
      $nullAankopenVerkopen=array();//Geen debug voor deze nabewerking.
    }
    else
    {
      foreach($queries as $query)
      {
        $db->SQL($query);
        $db->Query();
      }
    }
  }

  foreach($nullAankopenVerkopen as $orderId=>$orderregelIds)
  {
    $samenvoegen=false;
    if(count($orderregelIds)>1)
    {
      $query="SELECT id,aantal,bedrag,kosten,brokerkosten,opgelopenRente,brutoBedrag,nettoBedrag,orderbedrag,orderaantal FROM OrderRegelsV2 WHERE orderid='$orderId'";
      $db->SQL($query);
      $db->Query();
      $totalen=array();
      $regelIds=array();
      $samenvoegen=false;
      while ($data = $db->nextRecord())
      {
        foreach($data as $veld=>$waarde)
        {
          if($veld <> 'id')
          {
            $totalen[$veld]+=$waarde;
          }
          $regelIds[$data['id']]=$data['id'];
        }
      }
      if(count($orderregelIds)==count($regelIds)) // Verwachte aantal regels.
      {
        $samenvoegen=true;
        foreach($orderregelIds as $id)
        {
          if(!isset($regelIds[$id]))
          {
            echo "OrderregelId $id niet meer aanwezig?<br>\n";
            $samenvoegen=false;
          }
        }
      }
      if($samenvoegen==true)
      {
        $query="UPDATE OrderRegelsV2 SET ";
        foreach($totalen as $veld=>$waarde)
          $query.="$veld='$waarde', ";
        $query.=" change_date=now() WHERE id='".$orderregelIds[0]."'";
        $db->SQL($query);
        logIt($query);
        //echo $query."<br>\n";
        $db->Query();
        unset($orderregelIds[0]);
        $query="DELETE FROM OrderRegelsV2 WHERE id IN('".implode("','",$orderregelIds) ."')";
        $db->SQL($query);
        logIt($query);
        $db->Query();
        //echo $query."<br>\n";
      }
      else
      {
        echo "Samenvoegen niet mogelijk.<br>\n".count($orderregelIds)."|".count($regelIds)."<br>\n";
      }
    }
    else
    {
      echo "Samenvoegen niet nodig. Slechts 1 order regel gevonden.<br>\n";
    }
  }
  
  echo "<br><b>".count($verwerken)." regel(s) verwerkt.</b></br></br></br>";
}


echo "<form name='selectForm'>";
echo '
<script>
function checkAll(optie)
{
  var theForm = document.selectForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == \'checkbox\' && theForm[z].name.substr(0,6) == \'check_\')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
  checkStatus();
}
</script>
';
echo "<select name='methode' onchange='selectForm.submit();'>
<option value=''>---</option>
<option value='perOrder'".(($_GET['methode']=='perOrder')?'selected':'').">Per order</option>
<option value='perPortefeuille' ".(($_GET['methode']=='perPortefeuille')?'selected':'').">Per portefeuille</option>
</select>";

echo "<br><br><table>";
if($_GET['methode']=='perOrder')
{
  echo "<tr><td>Ordernummer</td><td><input name='ordernummer' value='".(($_GET['ordernummer']<>'')?$_GET['ordernummer']:'')."'></td></tr>
<tr><td>Storno datum</td><td> <input name='stornodatum' value='".(($_GET['stornodatum']<>'')?$_GET['stornodatum']:date('d-m-Y'))."'></td></tr>
<tr><td>Nieuwe Order</td><td> <select name='nieuweOrder'><option value=''>---</option><option value='J'".(($_GET['nieuweOrder']=='J')?'selected':'').">Ja</option><option value='N'".(($_GET['nieuweOrder']=='N')?'selected':'').">Nee</option></td></tr></select>";
}
elseif($_GET['methode']=='perPortefeuille')
{
  $orderFiler='';
  if($_GET['ordersVanaf'])
    $orderFiler = " AND date(OrderRegelsV2.add_date)>='" . mysql_real_escape_string(formdate2db($_GET['ordersVanaf'])) . "' AND date(OrderRegelsV2.add_date)<='" . mysql_real_escape_string(formdate2db($_GET['ordersTot'])) . "'";
 
  $query="SELECT OrderRegelsV2.portefeuille FROM OrderRegelsV2 INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE OrderRegelsV2.orderregelStatus > 0 AND OrderRegelsV2.orderregelStatus < 6  $orderFiler GROUP BY OrderRegelsV2.portefeuille ORDER BY OrderRegelsV2.portefeuille";
  $db->SQL($query);
  $db->Query();
  $portefeuilles=array();
  while($data=$db->nextRecord())
  {
    $portefeuilles[]=$data['portefeuille'];
  }
  $portefeuilleSelect=createSelect('portefeuille',$portefeuilles,$_GET['portefeuille']);
  echo "<tr><td>Portefeuille</td><td>".$portefeuilleSelect."</td></tr>
<tr><td>Orders vanaf</td><td> <input name='ordersVanaf' value='".(($_GET['ordersVanaf']<>'')?$_GET['ordersVanaf']:date('01-01-Y'))."'></td></tr>
<tr><td>Orders tot</td>  <td> <input name='ordersTot'   value='".(($_GET['ordersTot']<>'')?$_GET['ordersTot']:date('d-m-Y'))."'></td></tr>
<tr><td>Nieuwe Order</td><td> <select name='nieuweOrder'><option value=''>---</option><option value='J'".(($_GET['nieuweOrder']=='J')?'selected':'').">Ja</option><option value='N'".(($_GET['nieuweOrder']=='N')?'selected':'').">Nee</option></td></tr></select>";
}
else
{
  echo "&nbsp; Selecteer een selectie methode.";
}


if($_GET['methode']=='perOrder' || $_GET['methode']=='perPortefeuille')
{
  echo "<tr><td>&nbsp;</td><td><br><input type='submit' id='submitKnop' value='Ophalen orderregels'> <input type='checkbox' name='debug' value='1' checked>debug <br> <br></td></tr>";
}


if(($_GET['methode']=='perOrder' && $_GET['ordernummer']<>'') || ($_GET['methode']=='perPortefeuille' && $_GET['portefeuille']<>''))
{
  if ($_GET['methode'] == 'perOrder' && $_GET['ordernummer'] <> '')
  {
    $orderFiler = "OrderRegelsV2.orderId='" . intval($_GET['ordernummer']) . "'";
  }
  elseif ($_GET['methode'] == 'perPortefeuille' && $_GET['portefeuille'] <> '')
  {
    $orderFiler = "OrderRegelsV2.portefeuille='" . mysql_real_escape_string($_GET['portefeuille']) . "' AND date(OrderRegelsV2.add_date)>='" . mysql_real_escape_string(formdate2db($_GET['ordersVanaf'])) . "' AND date(OrderRegelsV2.add_date)<='" . mysql_real_escape_string(formdate2db($_GET['ordersTot'])) . "'";
  }
  
  $query = "SELECT
OrdersV2.fonds,
OrdersV2.ISINCode,
OrdersV2.fondsOmschrijving,
OrdersV2.transactieType,
OrdersV2.transactieSoort,
OrderRegelsV2.portefeuille,
OrderRegelsV2.rekening,
OrderRegelsV2.aantal,
OrderRegelsV2.id as orderregelId,
OrderRegelsV2.orderid,
OrderRegelsV2.add_date
FROM
OrderRegelsV2
INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE  OrderRegelsV2.orderregelStatus > 0 AND OrderRegelsV2.orderregelStatus < 6 AND " . $orderFiler;
  $db->SQL($query);
  $db->Query();
  $orderregels = array();
  $orderregelTable="<table><tr><td><b><a href='javascript:checkAll(-1);'><u>Selecteren</u></a></b></td><td><b>orderid</b></td><td><b>orderregelId</b></td><td><b>portefeuille</b></td><td><b>transactieSoort</b></td><td><b>aantal</b></td><td><b>fondsOmschrijving</b></td><td><b>add_date</b></td></tr>";
  while ($data = $db->nextRecord())
  {
    $orderregels[] = $data;
    $orderregelTable.="<tr><td><input type='checkbox' name='check_".$data['orderregelId']."' value='1' onclick='checkStatus();'></td>
<td>".$data['orderid']."</td><td>".$data['orderregelId']."</td><td>".$data['portefeuille']."</td><td>".$data['transactieSoort']."</td><td>".$data['aantal']."</td><td>".$data['fondsOmschrijving']."</td><td>".$data['add_date']."</td></tr>";
  }
  $orderregelTable.="</table>";
  echo "<tr><td>Orderregels</td><td>$orderregelTable</td></tr>";
  

}

echo "</table>";


echo "</form>";
echo template($__appvar["templateRefreshFooter"],$content);
?>