<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/07/22 18:20:50 $
    File Versie         : $Revision: 1.29 $

    $Log: orderregelsList.php,v $
    Revision 1.29  2017/07/22 18:20:50  rvv
    *** empty log message ***

    Revision 1.28  2015/09/30 15:51:59  rvv
    *** empty log message ***

    Revision 1.27  2015/03/07 17:27:08  rvv
    *** empty log message ***

    Revision 1.26  2013/04/07 16:08:24  rvv
    *** empty log message ***

    Revision 1.25  2013/03/31 12:39:16  rvv
    *** empty log message ***

    Revision 1.24  2012/12/22 15:31:52  rvv
    *** empty log message ***

    Revision 1.23  2012/11/28 17:02:32  rvv
    *** empty log message ***

    Revision 1.22  2012/04/11 17:14:52  rvv
    *** empty log message ***

    Revision 1.21  2012/03/11 17:19:04  rvv
    *** empty log message ***

    Revision 1.20  2012/01/28 16:13:06  rvv
    *** empty log message ***

    Revision 1.19  2011/12/21 19:18:08  rvv
    *** empty log message ***

    Revision 1.18  2011/12/18 14:54:28  rvv
    *** empty log message ***

    Revision 1.17  2011/12/18 14:25:43  rvv
    *** empty log message ***

    Revision 1.16  2011/10/30 13:31:24  rvv
    *** empty log message ***

    Revision 1.15  2011/10/08 14:46:37  rvv
    *** empty log message ***

    Revision 1.14  2011/08/31 14:37:40  rvv
    *** empty log message ***

    Revision 1.13  2009/10/17 16:00:33  rvv
    *** empty log message ***

    Revision 1.12  2009/10/07 16:17:58  rvv
    *** empty log message ***

    Revision 1.11  2009/10/07 11:40:21  rvv
    *** empty log message ***

    Revision 1.9  2009/09/12 10:38:04  rvv
    *** empty log message ***

    Revision 1.8  2009/01/20 17:46:01  rvv
    *** empty log message ***

    Revision 1.7  2007/11/26 15:17:15  rvv
    *** empty log message ***

    Revision 1.6  2006/10/18 06:56:43  rvv
    *** empty log message ***

    Revision 1.5  2006/10/17 06:16:11  rvv
    ordercontrole

    Revision 1.4  2006/09/15 12:41:55  rvv
    ordercontrolle functies gemaakt.
    aanwezigheid, short-positie, liquiditeiten.

    Revision 1.3  2006/07/05 07:56:36  cvs
    *** empty log message ***

    Revision 1.2  2006/06/09 11:28:56  cvs
    *** empty log message ***

    Revision 1.1  2006/06/08 14:47:14  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");
include_once("./rapport/rapportRekenClass.php");
include_once("./orderControlleRekenClass.php");
session_start();
$__appvar['rowsPerPage']=1000;


$db = new DB();
$query="SELECT MAX(Vermogensbeheerders.check_module_ORDERNOTAS) AS ordernota FROM
Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
$db->SQL($query);
$rechten=$db->lookupRecord();

if($_GET['batchId'] > 0)
{
  $query="SELECT Orders.orderId FROM Orders WHERE Orders.batchId = '".$_GET['batchId']."'";
  $db->SQL($query);
  $db->Query();
  $orderIds=array();
  while($data=$db->nextRecord())
  {
    $orderIds[]=$data;
  }
}

if(count($orderIds) > 0 && $_GET['batchId'] > 0)
{
$subHeader     = "";
$mainHeader    = vt("Stukkenlijst bij");

$editScript = "orderregelsEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->perPage = '1000';

$list->addColumn("Orders","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Orders","fondsOmschrijving",array("list_width"=>"250","search"=>false));
$list->addColumn("Orders","fondsCode",array('description'=>'ISIN-code',"list_width"=>"100","search"=>false));
$list->addColumn("OrderRegels","aantal",array("list_width"=>"100","search"=>false));
$list->addColumn("Orders","transactieSoort",array("list_width"=>"100","search"=>false));
$list->addColumn("Orders","transactieType",array("list_width"=>"100","search"=>false));

$list->setWhere("OrderRegels.orderid = Orders.orderid AND Orders.batchId='".$_GET['batchId']."'");
$list->setOrder($_GET['sort'],$_GET['direction']);
$list->setSearch($_GET['selectie']);
$list->selectPage($_GET['page']);


echo template($__appvar["templateContentHeader"],$editcontent);
?>
<br>

<table class="list_tabel" >
<?=$list->printHeader(true);?>
<?
$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem("<br>","");
$_SESSION[submenu]->addItem($html,"");

while($data = $list->getRow())
{
  $data['disableEdit']=true;

  $data['transactieType']['value']=$__ORDERvar["transactieType"][$data['transactieType']['value']];
   $data['transactieSoort']['value']=$__ORDERvar["transactieSoort"][$data['transactieSoort']['value']];


 echo $list->buildRow($data);
}
logAccess();
?>
</table>

<?
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$editcontent);

}
else if ($_GET["rel_id"] <> "")
{
  $list = new MysqlList();
  $list->idField = "id";
  $editScript = "orderregelsEdit.php";
  $list->editScript = $editScript;
  $list->perPage = $__appvar['rowsPerPage'];

  $db=new DB();
  $query="SELECT portefeuille FROM CRM_naw WHERE id='".$_GET["rel_id"]."'";
  $db->SQL($query);
  $data=$db->lookupRecord();

  $list->addColumn("OrderRegels","id",array("list_width"=>"100","search"=>false));
  $list->addColumn("Orders","add_date",array("list_width"=>"100","search"=>false,"description"=>"datumInvoer"));
  $list->addColumn("Orders","fondsOmschrijving",array("list_width"=>"250","search"=>false));
  $list->addColumn("OrderRegels","orderid",array("list_invisible"=>true,"list_width"=>"100","search"=>false,"description"=>"kenmerk"));
  $list->addColumn("OrderRegels","aantal",array("list_width"=>"70","search"=>true));
  $list->addColumn("OrderRegels","portefeuille",array("list_width"=>"100","search"=>true));
  $list->addColumn("OrderRegels","valuta",array("list_width"=>"70","search"=>true));
  $list->addColumn("OrderRegels","status",array("list_width"=>"100","search"=>false));
  $list->addColumn("","uitvoeringsprijs",array("list_width"=>"100","search"=>false,"list_numberformat"=>4,"list_align"=>"right"));
  $list->addColumn("OrderRegels","memo",array("list_width"=>"100","search"=>false));
  $list->addColumn("OrderRegels","controle",array("list_invisible"=>true));
  $list->setWhere("Orders.orderid=OrderRegels.orderid AND OrderRegels.portefeuille='".$data['portefeuille']."'");

  if(!isset($_GET['sort']))
  {
    $_GET['sort'][]='add_date';
    $_GET['direction'][]='DESC';
  }
  $list->setOrder($_GET['sort'],$_GET['direction']);
  $list->setSearch($_GET['selectie']);
  $list->selectPage($_GET['page']);

  $_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],false));
  $_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
  echo template($__appvar["templateContentHeader"],$content);
  ?><table class="list_tabel" cellspacing="0">
  <?=$list->printHeader();?>
  <?php
  while($data = $list->getRow())
  {
    $data['status']['value']=  $__ORDERvar['laatsteStatus'][$data['status']['value']];
    $query="SELECT uitvoeringsAantal,uitvoeringsPrijs FROM OrderUitvoering WHERE orderid='".$data['orderid']['value']."' ";
    $db->SQL($query);
    $db->Query();
    $uitvoeringen=array();
    while($uitvoering=$db->nextRecord())
    {
      $uitvoeringen['aantal']+=$uitvoering['uitvoeringsAantal'];
      $uitvoeringen['waarde']+=$uitvoering['uitvoeringsPrijs']*$uitvoering['uitvoeringsAantal'];
    }
    $data['uitvoeringsprijs']['value']=$uitvoeringen['waarde']/$uitvoeringen['aantal'];
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
}
else if ($_GET["orderid"] == "" && $_GET['action'] <> 'new')
{

$list = new MysqlList2();
$list->editScript = 'orderregelsEdit.php';
$list->perPage = 100;

$list->addFixedField("OrderRegels","portefeuille",array("list_width"=>"100","search"=>false));


$list->categorieVolgorde=array('OrderRegels'=>array("Algemeen"),
                               'Orders'=>array('Algemeen'),
                               'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),
                               'OrderUitvoering'=>array('Algemeen')  );

$html = $list->getCustomFields(array('OrderRegels','Orders','Portefeuilles','OrderUitvoering'),"orders");


$list->ownTables=array('OrderRegels');
$list->setJoin("LEFT JOIN Orders ON OrderRegels.orderid = Orders.orderid
                LEFT JOIN Portefeuilles ON Portefeuilles.Portefeuille = OrderRegels.Portefeuille AND Portefeuilles.consolidatie=0 
                LEFT JOIN OrderUitvoering ON Orders.orderid = OrderUitvoering.orderid  ");

$list->setOrder($_GET['sort'],$_GET['direction']);
$list->setSearch($_GET['selectie']);
$list->selectPage($_GET['page']);

  $_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $list->perPage ,false));
  $_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

echo template($__appvar["templateContentHeader"],$editcontent);
?>
<br>
<?=$list->filterHeader();?>
<table class="list_tabel" >
<?=$list->printHeader();?>
<?
$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem("<br>","");
$_SESSION[submenu]->addItem($html,"");

while($data = $list->getRow())
{
 $data['Orders.laatsteStatus']['value']=$data['Orders.laatsteStatus']['form_options'][$data['Orders.laatsteStatus']['value']];
 echo $list->buildRow($data);
}
logAccess();
?>
</table>

<?
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$editcontent);

}
else
{

$db = new DB();
if ($_GET["verschil"])
{
  $query = "SELECT sum(aantal) as totaal FROM OrderRegels WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $tmp = $db->lookupRecord();
  $query = "UPDATE Orders SET aantal = ".$tmp["totaal"]." WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $db->query();
  foreach($_GET as $keyname => $value)
	{
	 if ($keyname != "verschil") $str .= "&".urlencode($keyname)."=".urlencode($value);
	}
	$url = $PHP_SELF."?orderid=".$str;

	header("location: ".$url);
  exit();
}


  $_SESSION['submenu'] = New Submenu();
  if($_SESSION["orderListURL"] <> '')
    $_SESSION['submenu']->addItem("Terug naar Orderlijst ",$_SESSION["orderListURL"]);

//  $_SESSION[submenu]->addItem("Controleer orderregels ","orderregelscontrole.php?orderid=".$_GET["orderid"],array(target=>content));


//  print_r($_SESSION[submenu]);
//cframe

$subHeader     = "";
$mainHeader    = vt("Stukkenlijst bij");

$editScript = "orderregelsEdit.php";

$order=new Orders();
$order->getById($_GET['orderRealId']);
if($order->checkAccess() && $_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==0)
  $allow_add  = true;
else
  $allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
//$list->perPage = '1000';

$list->addColumn("OrderRegels","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","regels",array("list_width"=>"30","description"=>" "));
$list->addColumn("OrderRegels","orderid",array("list_width"=>"100","search"=>false,"description"=>"kenmerk"));
$list->addColumn("OrderRegels","positie",array("list_width"=>"30","list_align"=>"right","search"=>false,"description"=>"pos"));
$list->addColumn("OrderRegels","aantal",array("list_width"=>"70","search"=>true));
$list->addColumn("OrderRegels","portefeuille",array("list_width"=>"100","search"=>true));
$list->addColumn("OrderRegels","client",array("list_width"=>"","search"=>true));
 // $list->addColumn("Portefeuilles","Accountmanager",array("list_width"=>"","search"=>true));
$list->addColumn("OrderRegels","rekeningnr",array("list_width"=>"","search"=>true));
$list->addColumn("OrderRegels","valuta",array("list_width"=>"70","search"=>true));
$list->addColumn("OrderRegels","status",array("list_width"=>"100","search"=>false));
//$list->addColumn("","controle",array("search"=>false,"description"=>"controle"));
$list->addColumn("OrderRegels","memo",array("list_width"=>"100","search"=>false));
$list->addColumn("OrderRegels","controle",array("list_invisible"=>true));

  //$list->setWhere("OrderRegels.portefeuille=Portefeuilles.portefeuille ");
if ($_GET["listonly"] == 1)
{
  $disableEdit = true;
  $list->removeColumn("orderid");
  $_SESSION[submenu]->addItem("<hr>","");
  $_SESSION[submenu]->addItem("Controleer orderregels ","ordersEdit.php?action=edit&bereken=true&id=".$_GET["id"]);
  
$db = new DB();

  $query = "SELECT Layout FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
  $db->SQL($query);
  $beheerderRec = $db->lookupRecord();
  if(file_exists('ordersPDF_L'.$beheerderRec['Layout'].'.php'))
    $pdfScript='ordersPDF_L'.$beheerderRec['Layout'].'.php';
  else
    $pdfScript='ordersPDF.php';
   $_SESSION[submenu]->addItem("print order ","$pdfScript?orderid=".$__appvar["bedrijf"].$_GET["id"],array('target'=>'_blank'));
}
else
{
  $db = new DB();
  $query = "SELECT sum(aantal) as totaal FROM OrderRegels WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $tmp = $db->lookupRecord();
  $query = "SELECT * FROM Orders WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $orderRec = $db->lookupRecord();

  $orderTxt  = '<table border="0" cellspacing="5" cellpadding="0">';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('order kenmerk') . '</b></td><td>'.$orderRec["orderid"].'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('fonds / ISIN') . '</b></td><td>'.$orderRec["fonds"].' / '.$orderRec["fondsCode"].'</td></tr>';
  if ($orderRec["transactieType"] == "L" or
      $orderRec["transactieType"] == "SL")
  {
    $trType = '&nbsp;&nbsp;&nbsp;(koers '.$orderRec["koersLimiet"].')';
  }

  if ($tmp["totaal"] <> $orderRec["aantal"])
  {
    if($orderRec['laatsteStatus'] < 2)
      $verschilTxt = "<font color=maroon><b> (verschil = ".round(($tmp["totaal"] - $orderRec["aantal"]),4).") </b></font> [<a href=\"$PHP_SELF?verschil=1&orderid=".$_GET["orderid"]."\">Verschil opheffen</a>]";
    else
      $verschilTxt = "<font color=maroon><b> (verschil = ".round(($tmp["totaal"] - $orderRec["aantal"]),4).") </b></font>";
  }

  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('transactieType') . '</b></td><td>'.$__ORDERvar["transactieType"][$orderRec["transactieType"]].$trType.'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('transactieSoort') . '</b></td><td>'.$__ORDERvar["transactieSoort"][$orderRec["transactieSoort"]].'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('aantal') . '</b></td><td>'.$orderRec["aantal"].$verschilTxt.'</td></tr>';
  if ($orderRec["tijdsSoort"] == "DAT")
  {
    $looptijd = "&nbsp;&nbsp;(".$orderRec["tijdsLimiet"].")";
  }

  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('looptijd') . '</b></td><td>'.$__ORDERvar["tijdsSoort"][$orderRec["tijdsSoort"]].$looptijd.'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('huidige status') . '</b></td><td>'.$__ORDERvar["status"][$orderRec["laatsteStatus"]].'</td></tr>';
  $orderTxt .= '</table>';
}

if ($orderRec["laatsteStatus"] > 1) //uitgevoerde orders niet meer wijzigen.
{
  $allow_add = false;
}
$list->setWhere("   orderid= '".$_GET["orderid"]."' ");

// set default sort
$_GET['sort'][]      = "OrderRegels.positie";
$_GET['direction'][] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if (!$disableEdit)
{
  $_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
  $_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
}

if($rechten['ordernota'] && $list->records()==1)
{
  $data = $list->getRow();
  header('Location: orderregelsEdit.php?action=edit&id='.$data['id']['value']);
  exit;
}

if($_GET['zonderKop']==1)
  $orderTxt='';
$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br>".$orderTxt."<br>";

$content[javascript] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new&orderid=".$_GET["orderid"]."';
}
";

if($_GET["orderid"] <> '')
{
  $db->SQL("SELECT count(*) as aantal FROM OrderRegels WHERE orderid='".$_GET["orderid"]."'");
  $items = $db->lookupRecord();
  if ($items['aantal'] == 0 && $_GET['listonly']==0)
  {
  ?>
  <script>
     parent.frames['content'].location  = '<?=$editScript?>?action=new&orderid=<?=$_GET["orderid"]?>';
  </script>
  <?
   exit();
  }
}
echo template($__appvar["templateContentHeader"],$content);

if ($orderRec["laatsteStatus"] > 2) //uitgevoerde orders niet meer wijzigen.
{
  $disableEdit = true;
}
?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader($disableEdit);?>
<?php
$controleData=array();
while($data = $list->getRow())
{
//  if($rechten['ordernota'])
//    $data["regels"]["value"] .= "<a target=\"orderbon\" href=\"printNotaPDF.php?regelId=".$data["id"]["value"]."\">".drawButton("afdrukken","","maak nota")."</a>";
	$data[disableEdit] = $disableEdit;
  $data["status"]["value"]   = $__ORDERvar[status][$data["status"]["value"]];
  //
  $controleData['portefeuille']		= $data['portefeuille']['value'] ;
  $controleData['eigenOrderid']		= $data['orderid']['value'];
  $controleData['transactieAantal']	= $data['aantal']['value'];
  $controleData['valuta']			= $data['valuta']['value'];


  if ($data["controle"]["value"] == 0)
  {
    $data["controle"]["value"]="Oké";
  }
  elseif ($data["controle"]["value"] == 1)
  {
    $data["tr_class"] = "list_dataregel_geel";
    $data["controle"]["value"]="Waarschuwing";
  }
  elseif ($data["controle"]["value"] == 2)
  {
    $data["tr_class"] = "list_dataregel_rood";
    $data["controle"]["value"]="Fout";
  }
//
	$rawRow = $list->buildRow($data);
	$find    = "<a href=\"orderregelsEdit.php";
	$replace = "<a target=\"content\"  href=\"orderregelsEdit.php";
	echo str_replace($find,$replace,$rawRow);

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
//echo template($__appvar["templateContentFooter"],$content);
}
?>