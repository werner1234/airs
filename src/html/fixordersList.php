<?php
/*
    AE-ICT CODEX source module versie 2.0 (simbis), 09-06-2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/25 16:41:15 $
    File Versie         : $Revision: 1.6 $

    $Log: fixordersList.php,v $
    Revision 1.6  2020/03/25 16:41:15  rvv
    *** empty log message ***

    Revision 1.5  2015/12/06 18:16:46  rvv
    *** empty log message ***

    Revision 1.4  2015/11/22 14:27:06  rvv
    *** empty log message ***

    Revision 1.3  2015/11/15 12:19:09  rvv
    *** empty log message ***

    Revision 1.2  2015/11/01 18:00:22  rvv
    *** empty log message ***

    Revision 1.1  2015/09/30 08:34:20  rvv
    *** empty log message ***


*/
include_once('wwwvars.php');
include_once($__appvar['basedir'].'/classes/mysqlList.php');
session_start();

if (isset($_GET['resetFilter']) && $_GET['resetFilter'] == 1) {
  
  unset($_SESSION['FixOrderslist']);
  unset($_GET['resetFilter']);
}


$subHeader     = "";
$mainHeader    = vt('FIX orders overzicht');

$editScript = 'fixordersEdit.php';
$allow_add  = true;

if($_GET['openOrders']==1)
{
   $_POST['filter_0_veldnaam']=' fixOrders.laatsteStatus';
   $_POST['filter_0_methode']='inlijst';
   $_POST['filter_0_waarde']="cp,0,1";
   $_POST['filter_0_hidden'] = true;
}


$list = new MysqlList2();
$list->idField    = 'id';
$list->editScript = $editScript;
$list->perPage    = $__appvar['rowsPerPage'];

//$list->addColumn('FixOrders','id',array('description'=>'id','search'=>false));
$list->addFixedField('FixOrders','orderid',array('description'=>'orderid','search'=>true));
$list->addFixedField('FixOrders','aantal',array('description'=>'aantal','search'=>false));
$list->addFixedField('FixOrders','portefeuille',array('description'=>'portefeuille','search'=>true));
$list->addFixedField('FixOrders','client',array('description'=>'client','search'=>false));
$list->addFixedField('FixOrders','rekeningnr',array('description'=>'rekNr','search'=>false));
$list->addFixedField('FixOrders','vermogensBeheerder',array('description'=>'VB','search'=>false));
$list->addFixedField('FixOrders','Depotbank',array('description'=>'Depotbank','search'=>true));


$list->addFixedField('FixOrders','fondsCode',array('description'=>'fondsCode','search'=>false));
$list->addFixedField('FixOrders','bankfondsCode',array('description'=>'bankCode','search'=>true));
$list->addFixedField('FixOrders','fondsOmschrijving',array('description'=>'fondsOmschrijving','search'=>false));
$list->addFixedField('FixOrders','transactieType',array('description'=>'richting','search'=>false));
$list->addFixedField('FixOrders','transactieSoort',array('description'=>'soort','search'=>false));
$list->addFixedField('FixOrders','tijdsLimiet',array('description'=>'expire','search'=>false));
//$list->addColumn('FixOrders','tijdsSoort',array('description'=>'tijdsSoort','search'=>false));
$list->addFixedField('FixOrders','koersLimiet',array('description'=>'prijs','search'=>false));
$list->addFixedField('FixOrders','laatsteStatus',array('description'=>'status','search'=>false));

//$list->addColumn('FixOrders','uitvoeringsPrijs',array('description'=>'uitvoeringsPrijs','search'=>false));
//$list->addColumn('FixOrders','uitvoeringsDatum',array('description'=>'uitvoeringsDatum','search'=>false));
//$list->addColumn('FixOrders','aantalUitgevoerd',array('description'=>'aantalUitgevoerd','search'=>false));
$list->addFixedField('FixOrders','meldingen',array('description'=>'meldingen','search'=>false));
//$list->addColumn('FixOrders','verwerkt',array('description'=>'verwerkt','search'=>false));
//$list->addColumn('FixOrders','verwerktStamp',array('description'=>'verwerktStamp','search'=>false));
//$list->addColumn('FixOrders','verwerktResult',array('description'=>'verwerktResult','search'=>false));

$html = $list->getCustomFields(array('FixOrders','OrderRegelsV2','OrdersV2'),'FixOrderslist');

if(count($list->sortOptions)<1)
{
  $list->sortOptions=array(array('veldnaam'=>'fixOrders.id','methode'=>'DESC'));
  $list->hideFilter=true;
} 

foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'OrderRegelsV2' || $colData['objectname'] == 'OrdersV2')
  {
    $joinOrderRegelsV2=" JOIN OrderRegelsV2 ON fixOrders.AIRSorderReference = OrderRegelsV2.orderId ";
  }
  if($colData['objectname'] == 'OrdersV2')
  {
    $joinOrdersV2=" JOIN OrdersV2 ON OrderRegelsV2.orderId = OrdersV2.id ";
  }
}

  $list->ownTables=array('fixOrders');
  $list->setJoin("$joinOrderRegelsV2 $joinOrdersV2 ");

// set default sort
// $_GET['sort'][]      = "tablename.field";
// $_GET['direction'][] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);

// set searchstring
$list->setSearch($_GET['selectie']);

// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
$_SESSION['NAV']->items['navsearch']->placeholder = "zoek FIX orders";

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

$content['pageHeader'] = "<br />
<div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div>
<br /><br />";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
	echo $list->buildRow($data);
}
?>

</table>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}

echo template($__appvar["templateRefreshFooter"],$content);

?>