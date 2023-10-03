<?php
/*
    AE-ICT CODEX source module versie 1.6, 19 september 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2013/11/30 14:21:56 $
    File Versie         : $Revision: 1.7 $

    $Log: orderuitvoeringList.php,v $
    Revision 1.7  2013/11/30 14:21:56  rvv
    *** empty log message ***

    Revision 1.6  2012/12/22 15:31:52  rvv
    *** empty log message ***

    Revision 1.5  2012/12/19 17:00:08  rvv
    *** empty log message ***

    Revision 1.4  2012/01/28 16:13:06  rvv
    *** empty log message ***

    Revision 1.3  2011/08/31 14:37:40  rvv
    *** empty log message ***

    Revision 1.2  2009/10/07 16:17:58  rvv
    *** empty log message ***

    Revision 1.1  2009/10/07 10:00:56  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$db= new DB();

if($_GET['action'] == 'newOrder')
{
  $unsetVelden=array('id','orderid','status','laatsteStatus','controle_datum','add_date','add_user','change_date','change_user','batchId','aantal','status','brutoBedrag','controle_regels','CheckResult');
  $query="SELECT * FROM Orders WHERE Orders.orderid = '".$_GET['orderid']."'";
  $db->SQL($query);
  $orderData=$db->lookupRecord();
  $hoofdorderId=$orderData['id'];
  $hoofdorderIdSamengesteld=$orderData['orderid'];
  
  $orderAantal=$orderData['aantal'];
  foreach($unsetVelden as $veld)
    if(isset($orderData[$veld]))
      unset($orderData[$veld]);
      
  $query="SELECT * FROM OrderRegels WHERE OrderRegels.orderid = '".$_GET['orderid']."'";
  $db->SQL($query);
  $db->Query();
  $OrderRegels=array();
  $orderQueries=array();
  while($data=$db->nextRecord())
  {
    foreach($unsetVelden as $veld)
      if(isset($data[$veld]))
        unset($data[$veld]);
    $orderRegels[]=$data;
  }
  
  $query="SELECT SUM(uitvoeringsAantal) as aantal,SUM(uitvoeringsAantal * uitvoeringsPrijs) as waarde FROM OrderUitvoering WHERE orderid = '".$_GET['orderid'] ."'";
  $db->SQL($query);
  $uitvoeringsData = $db->lookupRecord();
  
  if($orderAantal<>$uitvoeringsData['aantal'])
  {
    $newAantal=$orderAantal-$uitvoeringsData['aantal'];
    $addData=" add_date=now(),add_user='$USR',change_date=now(),change_user='$USR' ";
    
    $orderData['aantal']=$newAantal;
    $orderData['laatsteStatus']=0;
    $orderData['status']=date("Ymd_Hi")."/$USR Aangemaakt via restant order $hoofdorderIdSamengesteld";
    
    $orderQuery="INSERT INTO Orders SET ";
    foreach($orderData as $key=>$value)
      $orderQuery.=" `$key`='".mysql_real_escape_string($value)."',";
    $orderQuery.=$addData;  
    
    $db=new DB();
    $db->SQL($orderQuery); 
    $db->Query();
    $orderId=$db->last_id();
    $samengesteldeId=$orderData['vermogensBeheerder'].$orderId;
    $extraLog=date("Ymd_Hi")."/$USR Aantal aangepast ivm deels uitgevoerd. ($orderAantal -> ".$uitvoeringsData['aantal'].")\n";
    $query="UPDATE Orders SET orderid='$samengesteldeId' WHERE id='$orderId'"; 
    $db->SQL($query);
    $db->Query();
      
    foreach($orderRegels as $regel)
    { 
      $regel['status']=0;
      if(count($orderRegels)==1)
        $regel['aantal']=$newAantal;
          
      $regel['orderid']=$samengesteldeId;    
      $orderQuery="INSERT INTO OrderRegels SET ";
      foreach($regel as $key=>$value)
        $orderQuery.=" `$key`='".mysql_real_escape_string($value)."',";
      $orderQuery.=$addData;
      $orderQueries[]=$orderQuery;
      $db->SQL($orderQuery);
      $db->Query();
    }
    $query="UPDATE Orders SET aantal='".$uitvoeringsData['aantal']."', status=concat(status,'$extraLog') WHERE id='$hoofdorderId'"; 
    $db->SQL($query);
    $db->Query();
    if(count($orderRegels)==1)
    {
      $query="UPDATE OrderRegels SET aantal='".$uitvoeringsData['aantal']."' WHERE orderid='$hoofdorderIdSamengesteld'"; 
      $db->SQL($query); 
      $db->Query();     
    }
    
  }
echo "<script language=\"JavaScript\" TYPE=\"text/javascript\">
parent.location='ordersEdit.php?action=edit&id=$orderId';
</script>";

echo " Nieuwe order <a href=\"javascript:parent.location='ordersEdit.php?action=edit&id=$orderId';\"><b> $samengesteldeId </b></a> aangemaakt.<br>\n";
exit;
}


$query="SELECT Orders.aantal FROM Orders WHERE Orders.orderid = '".$_GET['orderid']."'";
$db->SQL($query);
$orderData=$db->lookupRecord();

$query="SELECT SUM(uitvoeringsAantal) as aantal,SUM(uitvoeringsAantal * uitvoeringsPrijs) as waarde FROM OrderUitvoering WHERE orderid = '".$_GET['orderid'] ."'";
$db->SQL($query);
$uitvoeringsData = $db->lookupRecord();

$toAdd=$orderData['aantal']-$uitvoeringsData['aantal'];

if($toAdd > 0)
  $subHeader     = '<a href="javascript:addRecord();" ><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;toevoegen</a>';
$mainHeader    = "Uitvoeringen";

$editScript = "orderuitvoeringEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 100;

$list->addColumn("OrderUitvoering","id",array("list_width"=>"100","search"=>false));

$list->addColumn("OrderUitvoering","uitvoeringsAantal",array("list_width"=>"100","search"=>false));
$list->addColumn("OrderUitvoering","uitvoeringsDatum",array("list_width"=>"100","search"=>false));
$list->addColumn("OrderUitvoering","uitvoeringsPrijs",array("list_width"=>"100","search"=>false));

$list->setWhere("orderid='".$_GET['orderid']."'");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

//$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
//$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
//$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content[pageHeader] = "<div class='edit_actionTxt'> <b>$mainHeader</b> $subHeader</div><br>";

$content[javascript] .= "
function addRecord()
{
	document.location = '".$editScript."?action=new&orderid=".$_GET['orderid']."&toAdd=$toAdd';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	echo $list->buildRow($data);
}
?>
</table>
<br />

<?
if($toAdd > 0)
  echo '<a href="?action=newOrder&orderid='.$_GET['orderid'].'&toAdd='.$toAdd.'" ><img src="icon/16/add.png"> Restant order naar nieuwe order.</a>';
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>