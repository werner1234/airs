<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/12/21 08:25:48 $
    File Versie         : $Revision: 1.38 $
*/
$orderstatus = (isset($_GET["status"]) ? $_GET["status"] : '');
unset($_GET["status"]);
$_SERVER['QUERY_STRING']='';

include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");
session_start();
$__appvar['rowsPerPage']=1000;

/** DBS: 2796 **/
if (isset($_GET['resetFilter']) && $_GET['resetFilter'] == 1) {
  unset($_SESSION['OrderList']);
  unset($_GET['resetFilter']);
}

$ids=array();
foreach ($_POST as $key=>$value)
{
  if(substr($key,0,3)=='id_')
  {
    $ids[]=substr($key,3);
  }
}

if(count($ids)>0)
{
  
  $query="SELECT Vermogensbeheerders.OrderStandaardType, Vermogensbeheerders.OrderStandaardMemo , Vermogensbeheerders.OrderStatusKeuze FROM Vermogensbeheerders
  Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  $db=new DB();
  $db->SQL($query); 
  $standaard=$db->lookupRecord();
$vermogensbeheerderKeuze=unserialize($standaard['OrderStatusKeuze']);
if(is_array($vermogensbeheerderKeuze))
{
  foreach ($vermogensbeheerderKeuze as $index=>$checkData)
  {
    if($checkData['checked']==1)
    {
      unset($__ORDERvar["status"][$index]);
    }
  }
}

$statusItems = count($__ORDERvar["status"]);
$n=0;
foreach ($__ORDERvar["status"] as $index=>$waarde)
{
  $indexHuidigeStatusLookup[$index]=$n;
  $indexLookup[$n]=$index;
  $n++;
}
$statusItems = count($indexLookup);

foreach($ids as $id)
{
  $query="SELECT laatsteStatus,status FROM Orders WHERE id='$id'";
  $db->SQL($query);
  $tmp=$db->lookupRecord();
  $indexHuidigeStatus=$indexHuidigeStatusLookup[$tmp['laatsteStatus']];
  $volgendeStatus=$indexLookup[$indexHuidigeStatus+1];
  if($volgendeStatus<5)
  {
    $txt .= addslashes($tmp['status']."\n".date("Ymd_Hi")."/$USR - laatsteStatus naar ".$__ORDERvar["status"][$volgendeStatus]."\n");
    //$query="UPDATE Orders SET status='$txt', laatsteStatus='$volgendeStatus',change_date=now(),change_user='$USR' WHERE id='$id' AND laatsteStatus='".$tmp['laatsteStatus']."'";  
    $query="UPDATE Orders 
JOIN OrderRegels ON Orders.orderid  = OrderRegels.orderid
SET Orders.status='$txt', Orders.laatsteStatus='$volgendeStatus',Orders.change_date=now(),Orders.change_user='$USR',
OrderRegels.`status`='$volgendeStatus',OrderRegels.change_date=now(),OrderRegels.change_user='$USR'
WHERE Orders.id='$id' AND Orders.laatsteStatus='".$tmp['laatsteStatus']."'"; 
    $db->SQL($query);
    $db->Query();
  }
}

}

//

$subHeader     = "";
$mainHeader    = vt('Order overzicht');

$editScript = "ordersEdit.php";

if($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==1)
  $allow_add=false;
else
  $allow_add=true;

$db = new DB();
$query="SELECT MAX(Vermogensbeheerders.check_module_ORDERNOTAS) AS ordernota FROM
Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
$db->SQL($query);
$rechten=$db->lookupRecord();


$list = new MysqlList2();
$list->idField = "id";
$list->idTable ='Orders';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("Orders","id",array("list_width"=>"120","search"=>false));
//$list->addColumn("","regels",array("list_width"=>"65","description"=>" "));
$list->addColumn("","regels",array("list_width"=>"85","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addFixedField("Orders","id",array("list_width"=>"80","description"=>"kenmerk"));
$list->addFixedField("Orders","aantal",array("list_width"=>"80","search"=>false));
$list->addFixedField("Orders","fondsOmschrijving",array("list_width"=>"","search"=>false));
$list->addFixedField("Orders","Depotbank",array("list_width"=>"","description"=>"depotbank"));
$list->addFixedField("Orders","transactieType",array("list_width"=>"","search"=>false));
$list->addFixedField("Orders","koersLimiet",array("list_width"=>"70","search"=>false));
$list->addFixedField("Orders","transactieSoort",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orders","tijdsSoort",array("list_width"=>"120","search"=>false));
$list->addFixedField("Orders","tijdsLimiet",array("list_width"=>"80","search"=>false));
$list->addFixedField("Orders","laatsteStatus",array("list_width"=>"","search"=>false));
$list->addFixedField("Orders","OrderSoort",array("list_width"=>"20","search"=>false));

$list->addFixedField("","OrderSoort",array("list_width"=>"20","search"=>false));

if ($orderstatus <> '')
{
  foreach($__ORDERvar["status"] as $key => $value)
  {
    if ($value == $orderstatus)
    {
     // $list->setWhere("Orders.laatsteStatus = $key");
      $_POST['filter_0_veldnaam'] ='Orders.laatsteStatus';
      $_POST['filter_0_methode'] ='gelijk';
      $_POST['filter_0_waarde'] = $key;

      $subHeader = "met status ".$orderstatus;

    }
  }
}
else
{
 $disableEdit = true;
 if($_GET['resetFilter'])
   $_POST['filter_0_verwijder'] = 1;
}

$html = $list->getCustomFields(array('Orders','OrderRegels','OrderUitvoering','Fonds'),'OrderList');




  foreach ($list->columns as $colData)
  {
    if($colData['objectname'] == 'OrderRegels' && !isset($enkeleOrderRegelsAdded))
    {
      $enkeleOrderRegelsAdded=true;
      $query="CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegels.*
        FROM Orders INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid 
        WHERE Orders.OrderSoort <> 'M'
        GROUP BY Orders.orderid  ";
      $db->SQL($query); 
      $db->Query();
      $query="ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
      $joinDossier="LEFT JOIN enkeleOrderRegels as OrderRegels ON Orders.orderid = OrderRegels.orderid ";
    }
    if($colData['objectname'] == 'OrderUitvoering' && !isset($orderUitvoeringsAdded))
    {
      $orderUitvoeringsAdded=true;
      /*
      $query="CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegels.*
        FROM Orders INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid 
        WHERE Orders.OrderSoort <> 'M'
        GROUP BY Orders.orderid  ";
      $db->SQL($query); 
      $db->Query();
      $query="ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
      $joinDossier="LEFT JOIN enkeleOrderRegels as OrderRegels ON Orders.orderid = OrderRegels.orderid ";
      */
      $joinOrderUitvoering.=" LEFT JOIN OrderUitvoering ON Orders.orderid = OrderUitvoering.orderid ";
    }    
    if($colData['objectname'] == 'Fonds')
    {
      $joinFondsen=" LEFT JOIN Fondsen ON Orders.Fonds = Fondsen.Fonds";
    }
  }
  
  $list->ownTables=array('Orders');
  $list->setJoin("$joinDossier $joinOrderUitvoering $joinFondsen");
 
  
  
$_SESSION['submenu'] = New Submenu();
/*
if($rechten['ordernota'])
{
  $_SESSION['submenu']->addItem("Print definitieve nota's",'printNotaPDF.php');
}
*/
if($__appvar["bedrijf"]=='FDX' || $__appvar["bedrijf"]=='ANO' || $__appvar["bedrijf"]=='VEC' )//||$__appvar["bedrijf"]=='HOME'
{
//  $_SESSION['submenu']->addItem("order csv export",'javascript:parent.frames[\'content\'].orderExport();');
//  $_SESSION['submenu']->addItem("order csv export v2",'javascript:parent.frames[\'content\'].orderExport(\'?type=v2\');');
  $_SESSION['submenu']->addItem("order csv export v3",'javascript:parent.frames[\'content\'].orderExport(\'?type=v3\');');
//  $_SESSION['submenu']->addItem("optie csv export v2",'javascript:parent.frames[\'content\'].orderExport(\'?type=v2Optie\');');
  $_SESSION['submenu']->addItem("optie export FRANK",'javascript:parent.frames[\'content\'].orderExport(\'?type=v3Optie\');');
  $_SESSION['submenu']->addItem("optie export nieuw",'javascript:parent.frames[\'content\'].orderExport(\'?type=v4Optie\');');
  $_SESSION['submenu']->addItem("AIRS export",'javascript:parent.frames[\'content\'].exportAirs();');
}
if(checkOrderAcces('orderTransRep')==true)
  $_SESSION['submenu']->addItem(vt("AFM Trans Rep"),'javascript:parent.frames[\'content\'].orderExport(\'orderTransRep\');');

foreach($__ORDERvar["status"] as $key=>$value)
{
  $_SESSION['submenu']->addItem("".$value,"ordersList.php?status=".urlencode($value));
}
$_SESSION['submenu']->addItem($html,"");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
$list->setWhere('laatsteStatus >= 0');
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);


$_SESSION['NAV'] = new NavBar($PHP_SELF);// getenv("QUERY_STRING")
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
//$_SESSION[orderListURL] = $_SESSION["NAV"]->currentScript."?".$_SESSION["NAV"]->currentQueryString;



$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function exportAirs()
{
  document.selectForm.action='orderExportAIRS.php';
  document.selectForm.submit();
  document.selectForm.action='$PHP_SELF';
}

function orderExport(options)
{
  if(options=='orderTransRep')
  {
    document.selectForm.action='orderExportAFM.php?versie=1';
  }
  else
  {
    document.selectForm.action='orderExport.php'+options;
  }
  document.selectForm.submit();
  document.selectForm.action='$PHP_SELF';
}

function checkAll(optie)
{
  var theForm = document.selectForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,3) == 'id_')
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
}


";
echo template($__appvar["templateContentHeader"],$content);

?>
<br>
<?=$list->filterHeader();?>



<form action="<?=$PHP_SELF?>" method="POST" name="selectForm">
<table class="list_tabel" cellspacing="0">
<? echo $list->printHeader();//$disableEdit);

if($orderstatus <> '')
{
?>
<div id="wrapper" style="overflow:hidden;"> 
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> <?= vt('Alles selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> <?= vt('Niets selecteren'); ?></div>
<div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /><?= vt('Selectie omkeren'); ?></div>
<div class="buttonDiv" style="width:140px;float:left;text-align: center;" onclick="javascript:selectForm.submit()"> <?= vt('Volgende status'); ?></div>
</div>
<br /><br />
<?
}

$db = new DB();

  $query = "SELECT Layout FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
  $db->SQL($query);
  $beheerderRec = $db->lookupRecord();
if(file_exists('ordersPDF_L'.$beheerderRec['Layout'].'.php'))
  $pdfScript='ordersPDF_L'.$beheerderRec['Layout'].'.php';
else
  $pdfScript='ordersPDF.php';

$_SESSION['lastListQuery']=$list->sqlQuery;
while($data = $list->getRow())
{
 // listarray($data['Orders.aantal']['value']);
  $orderid=$__appvar["bedrijf"].$data["Orders.id"]["value"];
  $realId=$data["Orders.id"]["value"];

  $data["Orders.id"]["value"]=$orderid;
  $data[".regels"]["value"] = "<a href=orderregelsList.php?orderid=".$orderid."&orderRealId=$realId>".drawButton("orderregels","","stukkenlijst muteren")."</a>";
  $data["Orders.transactieType"]["value"]  = $__ORDERvar['transactieType'][$data["Orders.transactieType"]["value"]];
  $data["Orders.transactieSoort"]["value"] = $__ORDERvar['transactieSoort'][$data["Orders.transactieSoort"]["value"]];
  $data["Orders.tijdsSoort"]["value"]      = $__ORDERvar['tijdsSoort'][$data["Orders.tijdsSoort"]["value"]];
  $data["Orders.laatsteStatus"]["value"]   = $__ORDERvar['status'][$data["Orders.laatsteStatus"]["value"]];
  $data[".regels"]["value"] .= "<a target=\"orderbon\" href=\"$pdfScript?orderid=".$orderid."\">".drawButton("afdrukken","","maak orderbon")."</a>";
  $data[".regels"]["value"] .= "<a target=\"orderbon\" href=\"ordersXLS.php?orderid=".$orderid."\">".drawButton("xls","","maak orderbon")."</a>";
  $data[".regels"]["value"] .= "<input type=\"checkbox\" name=\"id_$realId\" value=\"1\" ";

  //
  $query = "SELECT sum(aantal) as totaal ,max(controle) as controle FROM OrderRegels WHERE orderid='".$orderid."' ";
  $db->SQL($query);
  $regelsRec = $db->lookupRecord();
  if ($data["Orders.aantal"]["value"] <> $regelsRec["totaal"])
    $data["tr_class"] = "list_dataregel_rood";
  if($regelsRec["controle"] > 0)
    $data["tr_class"] = "list_dataregel_rood";

   $query="SELECT user,change_date FROM tableLocks WHERE `table`='Orders' AND tableId='$realId'";
   if($db->QRecords($query) > 0)
      $data["tr_class"] = "list_dataregel_geel";

	echo $list->buildRow($data);

}


?>
</table>

<? 


 ?>
<br /><br />


</form>

<?

logAccess();
if($__debug)
{
	echo getdebuginfo();
}
if($enkeleOrderRegelsAdded)
{
  $query="DROP TEMPORARY TABLE enkeleOrderRegels";
  $db->SQL($query);
}
echo template($__appvar["templateRefreshFooter"],$content);
?>