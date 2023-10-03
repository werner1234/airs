<?php

$orderstatus = (isset($_GET["status"])?$_GET["status"]:'');
$orderstatus = (isset($_POST["status"])?$_POST["status"]:$orderstatus);

unset($_GET["status"]);
$_SERVER['QUERY_STRING'] = '';

include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");
foreach($__ORDERvar["orderStatus"]  as $index=>$statusTxt)
{
  if($statusTxt==$orderstatus)
    $huidigeStatusIndex=$index;
}

if ($_POST['kolUpdate'] == 2)
{
  $orderStatusRemoved = false;
  foreach ($_SESSION['OrderListV2']['filter'] as $index => $velden)
  {
    if ($velden['veldnaam'] == 'OrdersV2.orderStatus')
    {
      unset($_SESSION['OrderListV2']['filter'][$index]);
      $orderStatusRemoved = true;
    }
  }
  if ($orderStatusRemoved == true)
  {
    sort($_SESSION['OrderListV2']['filter']);
  }
}
$AEMessage = new AE_Message();
echo $AEMessage->getFlash();

$__appvar['rowsPerPage'] = 1000;

/** DBS: 2796 **/
if (isset($_GET['resetFilter']) && $_GET['resetFilter'] == 1)
{
  unset($_SESSION['OrderListV2']);
  unset($_GET['resetFilter']);
}

$ids = array();
foreach ($_POST as $key => $value)
{
  if (substr($key, 0, 3) == 'id_')
  {
    $ids[] = substr($key, 3);
  }
}
sort($ids);

if ($_GET['removeLocks'] == 1)
{
  removeLocks('OrdersV2');
}

if (count($ids) > 0)
{
  $db = new DB();
  $query = "SELECT Vermogensbeheerders.OrderStandaardType, Vermogensbeheerders.OrderStandaardMemo , Vermogensbeheerders.OrderStatusKeuze , Vermogensbeheerders.orderGeenHervalidatie 
  FROM Vermogensbeheerders
  Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  $db->SQL($query);
  $standaard = $db->lookupRecord();
  $vermogensbeheerderKeuze = unserialize($standaard['OrderStatusKeuze']);
  if (is_array($vermogensbeheerderKeuze))
  {
    foreach ($vermogensbeheerderKeuze as $index => $checkData)
    {
      if ($checkData['checked'] == 1)
      {
        unset($__ORDERvar["orderStatus"][$index]);
      }
    }
  }
  $statusItems = count($__ORDERvar["orderStatus"]);
  $n = 0;
  foreach ($__ORDERvar["orderStatus"] as $index => $waarde)
  {
    $indexHuidigeStatusLookup[$index] = $n;
    $indexLookup[$n] = $index;
    $n++;
  }
  $statusItems = count($indexLookup);


  $vierOgen = checkOrderAcces('orderVierOgen');
  $orderGeenHervalidatie = checkOrderAcces('orderGeenHervalidatie');
  if ($_GET['vervallen'] == 1)
  {
    $orderGeenHervalidatie = true;
  }

  foreach ($ids as $id)
  {
    $adviceMailRequired = false;
    $query = "SELECT orderStatus,fixOrder,add_user,orderSoort FROM OrdersV2 WHERE id='$id'";
    $db->SQL($query);
    $tmp = $db->lookupRecord();
    if ($_GET['vervallen'] == 1)
    {
      $tmp['fixOrder'] = 0;
    }

    /**
     * Wanneer het een advies relatie is eerst controlleren of de mail verzonden is.
     */
    $orderSoort = $tmp['orderSoort'];
    $adviesStatus = checkOrderAcces('orderAdviesNotificatie');
    $huidigeStatus = $tmp['orderStatus'];

    if (
      ($adviesStatus != 5 && $adviesStatus != 0)
      && ($orderSoort === 'E' || $orderSoort === 'C' || $orderSoort === 'N')
      && $huidigeStatus < 1
    )
    {
      $orderRegelObj = new OrderRegelsV2();
      $orderRegelDatas = $orderRegelObj->parseBySearch(array('orderid' => $id), 'all', null, -1);

      foreach ($orderRegelDatas as $orderRegelData)
      {
        if (adviesRelatieCheck($orderRegelData['portefeuille']) === true)
        {
          if ($orderRegelData['mailBevestigingVerzonden'] == '0000-00-00 00:00:00')
          {
            $adviceMailRequired = true;
          }
        }
      }
    }


    $indexHuidigeStatus = $indexHuidigeStatusLookup[$tmp['orderStatus']];
    $volgendeStatus = $indexLookup[$indexHuidigeStatus + 1];

    $skip = false;
    if ($volgendeStatus < 5)
    {
      if ($_GET['vervallen'] == 1)
      {
        $volgendeStatus = 5;
      }

      if ($adviceMailRequired === true)
      {
        echo "Order $id: Orderadvies dient eerst te worden verzonden.<br>\n";
        $skip = true;
      }
      elseif ($vierOgen === false || ($vierOgen === true && $tmp['add_user'] != $USR) || $tmp['orderStatus'] == -1)
      {
        if ($tmp['orderStatus'] == 0)
        {
          if ($orderGeenHervalidatie == true)
          {
            $skip = false;
            echo "Order $id: hervalidatie overgeslagen.<br>\n";
          }
          else
          {
            $orderObject = new OrdersV2();
            $check = $orderObject->OrderValidatie($id);
            if ($check['recheck'] == 1 || $check['controleStatus'] <> 0)
            {
              echo "Order $id: dient opnieuw gevalideerd te worden.<br>\n";
              $skip = true;
            }
          }
        }
        $lockData = checkLock('OrdersV2', $id);
        if ($lockData['locked'] == 1)
        {
          echo "Order $id: is momenteel geopend door " . $lockData['user'] . " om " . $lockData['change_date'] . ". Status aanpassing niet mogelijk.<br>\n";
          $skip = true;
        }

        if ($skip == false)
        {
          $order = new OrdersV2();
          $order->getById($id);

          if ((int)$_GET['sendFix'] === 1)
          {
            if ($tmp['fixOrder'] == 1 && checkOrderAcces('handmatig_verzenden'))
            {
              if ($tmp['orderStatus'] == 0)
              {
                $order->verzendFix();
                echo "Order $id: is verzonden<br>\n";
              }
              else
              {
                echo "Order $id: Status '" . $__ORDERvar["status"][$indexHuidigeStatus] . "' is al verzonden.<br>\n";
              }
            }
            else
            {
              if (!checkOrderAcces('handmatig_verzenden'))
              {
                echo "Order $id: Geen rechten om order te verzenden.<br>\n";
              }
              else
              {
                echo "Order $id: is geen fix order<br>\n";
              }
            }
          }
          else
          {
            if ($tmp['fixOrder'] == 1 && checkOrderAcces('handmatig_verzenden'))
            {
              if ($tmp['orderStatus'] == 0)
              {
                //$order->verzendFix();
                echo "Order $id: fixorder dient veronden te worden.<br>\n";//naar " . $__ORDERvar["status"][$volgendeStatus] . "<br>\n";
              }
              elseif ($tmp['orderStatus'] >= 2)
              {
                $order->set('orderStatus', $volgendeStatus);
                $order->save();
                $order->updateOrderregelStatus();
                $orderLog = new orderLogs();
                $orderLog->addToLog($id, '', "Status naar " . $__ORDERvar["status"][$volgendeStatus]);
                echo "Order $id: Status naar " . $__ORDERvar["status"][$volgendeStatus] . "<br>\n";
              }
              else
              {
                echo "Order $id: Status '" . $__ORDERvar["status"][$indexHuidigeStatus] . "' niet handmatig te verhogen voor een fix order.<br>\n";
              }
            }
            elseif (checkOrderAcces('handmatig_volgendeStatus'))
            {
              $order->set('orderStatus', $volgendeStatus);
              $order->save();
              $order->updateOrderregelStatus();
              $orderLog = new orderLogs();
              $orderLog->addToLog($id, '', "Status naar " . $__ORDERvar["status"][$volgendeStatus]);
              echo "Order $id: Status naar " . $__ORDERvar["status"][$volgendeStatus] . "<br>\n";
            }
            else
            {
              echo "Order $id: Geen rechten om order status te wijzigen.<br>\n";
            }
          }
        }
      }
      else
      {
        echo "Order $id: Geen rechten eigen order status te wijzigen.<br>\n";
      }
    }
  }
}


$subHeader = "";
$mainHeader = vt("Order overzicht");

$editScript = "ordersEditV2.php";

if (checkOrderAcces('handmatig_opslaan') == true)
{
  $allow_add = true;
}
else
{
  $allow_add = false;
}


$db = new DB();
$query = "SELECT MAX(Vermogensbeheerders.check_module_ORDERNOTAS) AS ordernota FROM
Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
$db->SQL($query);
$rechten = $db->lookupRecord();

$query="SELECT
tableLocks.`user`,
tableLocks.change_date,
OrdersV2.orderSoort,
combi.id
FROM
tableLocks
INNER JOIN OrdersV2 ON tableLocks.tableId = OrdersV2.id
INNER JOIN OrdersV2 as combi ON OrdersV2.batchId = combi.batchId
WHERE `table`='OrdersV2'";
$db->SQL($query);
$db->query();
$lockedCombiIds=array();
while($locked=$db->nextRecord())
{
  $lockedCombiIds[]=$locked['id'];
}

$list = new MysqlList2();
$list->idField = "id";
$list->idTable = 'OrdersV2';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("OrdersV2","id",array("list_width"=>"120","search"=>false));
//$list->addColumn("","regels",array("list_width"=>"65","description"=>" "));
$list->addColumn("", "regels", array("list_width" => "85", "description" => " ", 'list_nobreak' => true, 'list_order' => false));
$list->addFixedField("OrdersV2", "id", array("list_width" => "80", "description" => "kenmerk"));
//$list->addFixedField("OrdersV2","aantal",array("list_width"=>"80","search"=>false));
$list->addColumn("", "aantal", array("description"=>'Aantal/Bedrag',"sql_alias" => "round(orderV2tijdelijk.aantal,6)", "list_width" => "80", "search" => true, 'list_align' => 'right'));
$list->addFixedField("OrdersV2", "fondsOmschrijving", array("list_width" => "", "search" => false));
$list->addFixedField("OrdersV2", "depotbank", array("list_width" => "", "description" => "depotbank"));
//$list->addFixedField("OrdersV2", "transactieType", array("list_width" => "", "search" => false));
//$list->addFixedField("OrdersV2", "koersLimiet", array("list_width" => "70", "search" => false));
$list->addFixedField("OrdersV2", "transactieSoort", array("list_width" => "100", "search" => false));
//$list->addFixedField("OrdersV2", "tijdsSoort", array("list_width" => "120", "search" => false));
//$list->addFixedField("OrdersV2", "tijdsLimiet", array("list_width" => "80", "search" => false));
$list->addFixedField("OrdersV2", "orderStatus", array("list_width" => "", "search" => false));
//$list->addFixedField("OrdersV2", "OrderSoort", array("list_width" => "120", "search" => false));
$list->addFixedField("OrdersV2", "fixOrder", array("list_width" => "100", "search" => false));
//$list->addFixedField("OrderRegelsV2","aantal",array("list_width"=>"100","search"=>false));
$list->addColumn("", "controleStatus", array("list_invisible" => 'true', "sql_alias" => "orderV2tijdelijk.controleStatus", "list_width" => "80", "search" => false));
//listarray($_POST);

if ($orderstatus <> '')
{
  if ($orderstatus == 'annuleerVerzoek')
  {
    $_POST['filter_0_veldnaam'] = 'OrdersV2.orderStatus';
    $_POST['filter_0_methode'] = 'nietGelijk';
    $_POST['filter_0_waarde'] = 6;
    $_POST['filter_0_hidden'] = true;
    $_POST['filter_1_veldnaam'] = 'OrdersV2.fixAnnuleerdatum';
    $_POST['filter_1_methode'] = 'nietGelijk';
    $_POST['filter_1_waarde'] = '0000-00-00 00:00:00';
    $_POST['filter_1_hidden'] = true;
    $subHeader = "met status annuleer verzoek.";
  }
  else
  {
    foreach ($__ORDERvar["orderStatus"] as $key => $value)
    {
      if ($value == $orderstatus)
      {
        // $list->setWhere("Orders.laatsteStatus = $key");

        foreach ($_SESSION['OrderListV2']['filter'] as $index => $filter)
        {
          if (!isset($_POST['filter_' . ($index) . '_veldnaam']) && $filter['veldnaam'] <> 'OrdersV2.orderStatus')//&& $filter['verwijder'] <> 1
          {
            $_POST['filter_' . ($index) . '_veldnaam'] = $filter['veldnaam'];
            $_POST['filter_' . ($index) . '_methode'] = $filter['methode'];
            $_POST['filter_' . ($index) . '_waarde'] = $filter['waarde'];
            if ($filter['uitschakelen'])
            {
              $_POST['filter_' . ($index) . '_uitschakelen'] = $filter['uitschakelen'];
            }
            unset($_POST['filter_' . ($index) . '_hidden']);
          }

        }
        for ($i = 0; $i < 20; $i++)
        {
          if (!isset($_POST['filter_' . ($i) . '_veldnaam']))
          {
            $volgendeIndex = $i;
            break;
          }
        }

        $_POST['filter_' . ($volgendeIndex) . '_veldnaam'] = 'OrdersV2.orderStatus';
        $_POST['filter_' . ($volgendeIndex) . '_methode'] = 'gelijk';
        $_POST['filter_' . ($volgendeIndex) . '_waarde'] = $key;
        $_POST['filter_' . ($volgendeIndex) . '_hidden'] = true;

        $subHeader = "met status " . $orderstatus;

      }
    }

  }
}
else
{
  $disableEdit = true;

}
$extraTabellen=array('OrdersV2', 'OrderRegelsV2', 'OrderUitvoeringV2', 'Fonds','Portefeuilles');
foreach($extraTabellen as $tabel)
  $list->categorieVolgorde[$tabel]=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels','Recordinfo');
$html = $list->getCustomFields($extraTabellen, 'OrderListV2');



$query = "CREATE TEMPORARY TABLE orderV2tijdelijk
        SELECT OrdersV2.id as orderid, sum(OrderRegelsV2.aantal+OrderRegelsV2.bedrag) as aantal, max(OrderRegelsV2.controleStatus) as controleStatus
        FROM OrdersV2 LEFT JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        GROUP BY OrdersV2.id";
$db->SQL($query);
$db->Query();
$query = "ALTER TABLE orderV2tijdelijk ADD INDEX( orderid )";
$db->SQL($query);
$db->Query();
$joinAantallen = " JOIN orderV2tijdelijk ON OrdersV2.id = orderV2tijdelijk.orderid ";

$joinPortefeuilles='';
foreach ($list->columns as $colData)
{
  if ($colData['objectname'] == 'Portefeuilles' && !isset($portefeuillesAdded))
  {
    $portefeuillesAdded = true;
    $joinPortefeuilles.= " LEFT JOIN Portefeuilles ON OrderRegelsV2.Portefeuille = Portefeuilles.Portefeuille ";
  }
  if (($portefeuillesAdded || $colData['objectname'] == 'OrderRegelsV2') && !isset($enkeleOrderRegelsAdded))
  {
    $enkeleOrderRegelsAdded = true;
    $query = "CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegelsV2.*
        FROM OrdersV2 INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        WHERE OrdersV2.OrderSoort NOT IN('M','O')
        GROUP BY OrdersV2.id  ";
    $db->SQL($query);
    $db->Query();
    $query = "ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
    $db->SQL($query);
    $db->Query();
    $joinDossier = "LEFT JOIN enkeleOrderRegels as OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid ";
  }
  if ($colData['objectname'] == 'Portefeuilles' && !isset($portefeuillesAdded))
  {
    $portefeuillesAdded = true;
    $joinPortefeuilles.= " LEFT JOIN Portefeuilles ON OrderRegelsV2.Portefeuille = Portefeuilles.Portefeuille ";
  }
  if ($colData['objectname'] == 'OrderUitvoeringV2' && !isset($orderUitvoeringsAdded))
  {
    $orderUitvoeringsAdded = true;
    $joinOrderUitvoering .= " LEFT JOIN OrderUitvoeringV2 ON OrdersV2.id = OrderUitvoeringV2.orderid ";
  }
  if ($colData['objectname'] == 'Fonds')
  {
    $joinFondsen = " LEFT JOIN Fondsen ON OrdersV2.Fonds = Fondsen.Fonds";
  }
}



$list->ownTables = array('OrdersV2');

if (!isset ($joinDossier))
{
  $joinDossier = null;
}
if (!isset ($joinOrderUitvoering))
{
  $joinOrderUitvoering = null;
}
if (!isset ($joinFondsen))
{
  $joinFondsen = null;
}
$list->setJoin("$joinAantallen $joinDossier $joinOrderUitvoering $joinFondsen $joinPortefeuilles");


$_SESSION['submenu'] = New Submenu();
if ($rechten['ordernota'])
{
  $_SESSION['submenu']->addItem(vt("Print definitieve nota's"), 'printNotaPDF.php');
}
if (checkOrderAcces('kasbankBrokerVerwerking') == true) //$__appvar["bedrijf"]=='FDX' || $__appvar["bedrijf"]=='ANO' || $__appvar["bedrijf"]=='VEC' || $__appvar["bedrijf"]=='TEST' || $__appvar["bedrijf"]=='HOME')//
{

  $_SESSION['submenu']->addItem(vt("order csv export"), 'javascript:parent.frames[\'content\'].orderExport(\'\');');
  $_SESSION['submenu']->addItem(vt("order csv export v2"), 'javascript:parent.frames[\'content\'].orderExport(\'&type=v2\');');
  $_SESSION['submenu']->addItem(vt("optie csv export v2"), 'javascript:parent.frames[\'content\'].orderExport(\'&type=v2Optie\');');
  if ($orderstatus == 'uitgevoerd')
  {
    $_SESSION['submenu']->addItem(vt("Indekrapport"), 'javascript:parent.frames[\'content\'].orderIndekrapport();');
  }
  $_SESSION['submenu']->addItem(vt("AIRS export"), 'javascript:parent.frames[\'content\'].exportAirs(\'\');');
  $_SESSION['submenu']->addItem(vt("Notabedragen"), 'javascript:parent.frames[\'content\'].orderNotaBedragenPdf(\'\');');
  if ($__appvar['bedrijf'] == 'VEC')
  {
    $_SESSION['submenu']->addItem(vt("AAB Ordermail"), 'javascript:parent.frames[\'content\'].AabMail(\'\');');
  }
}

if (checkOrderAcces('orderTransRep') == true)
{
  $_SESSION['submenu']->addItem(vt("AFM Trans Rep"), 'javascript:parent.frames[\'content\'].orderExport(\'orderTransRep\');');
}
if (file_exists('orders2xls_'.$__appvar['bedrijf'].'.php'))
  $_SESSION['submenu']->addItem(vt("excel"). ' '.$__appvar['bedrijf'], 'javascript:parent.frames[\'content\'].exportExcel(\''.$__appvar['bedrijf'] .'\');');
$_SESSION['submenu']->addItem(vt("excel uitvoer"), 'javascript:parent.frames[\'content\'].exportExcel(1);');
$_SESSION['submenu']->addItem(vt("excel port. uitvoer"), 'javascript:parent.frames[\'content\'].exportExcel(3);');
$_SESSION['submenu']->addItem(vt("pdf uitvoer"), 'javascript:parent.frames[\'content\'].exportExcel(2);');

$_SESSION['submenu']->addItem(vt("orderoverzicht"), "ordersListV2.php?resetFilter=1");

//if(in_array($__appvar['bedrijf'],array('ANO','TEST','VRYACC')))
//{
$_SESSION['submenu']->addItem(vt("uitvoeringen import"), "ordersUitvoeringen_import.php?action=select");
//}

if (checkOrderAcces('VermogensbeheerderOrderOrderdesk') == false && $db->QRecords('SELECT id FROM OrdersV2 WHERE orderStatus=-1 limit 1') == 0)
{
  unset($__ORDERvar["orderStatus"][-1]);
}
foreach ($__ORDERvar["orderStatus"] as $key => $value)
{
  $_SESSION['submenu']->addItem("" . $value, "ordersListV2.php?status=" . urlencode($value));
}
$_SESSION['submenu']->addItem($html, "");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
//$list->setWhere('OrdersV2.orderStatus >= 0');


// set sort
//$list->setOrder( ( isset ($_GET['sort']) ? $_GET['sort'] : array('id')), ( isset ($_GET['direction']) ? $_GET['direction'] :  array('desc') ) );
if (count($list->sortOptions) < 1)
{
  $list->sortOptions = array(array('veldnaam' => 'OrdersV2.id', 'methode' => 'DESC'));
  $list->hideFilter = true;
}
// set searchstring
$list->setSearch((isset ($_GET['selectie'])?$_GET['selectie']:''));
// select page
$list->selectPage((isset ($_GET['page'])?$_GET['page']:''));


$_SESSION['NAV'] = new NavBar($PHP_SELF);// getenv("QUERY_STRING")
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'], $allow_add));
$_SESSION['NAV']->addItem(new NavSearch((isset ($_GET['selectie'])?$_GET['selectie']:'')));
//$_SESSION[orderListURL] = $_SESSION["NAV"]->currentScript."?".$_SESSION["NAV"]->currentQueryString;


$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$editcontent['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '" . $editScript . "?action=new';
}

function exportExcel(uitvoer)
{
";
$haakSluiten='';
if(file_exists('orders2xls_'.$__appvar['bedrijf'].'.php'))
{
  $editcontent['javascript'] .= "
  if(uitvoer=='".$__appvar['bedrijf']."')
  {
    document.selectForm.action='orders2xls_".$__appvar['bedrijf'].".php?xls=1';
    document.selectForm.submit();
    document.selectForm.action='$PHP_SELF';
    //return '';
  }
  else
  {
  ";
  $haakSluiten='}';
}

$editcontent['javascript'] .= "
  document.selectForm.action='orders2xls.php?xls='+uitvoer;
  document.selectForm.submit();
  document.selectForm.action='$PHP_SELF';
  $haakSluiten
}


function exportAirs(options)
{
  document.selectForm.action='orderExportAIRS.php?orderVersie=2'+options;
  document.selectForm.submit();
  document.selectForm.action='$PHP_SELF';
}

function orderExport(options)
{
  if(options=='orderTransRep')
  {
    document.selectForm.action='orderExportAFM.php';
  }
  else
  {
  document.selectForm.action='orderExport.php?orderVersie=2'+options;
  }
  document.selectForm.submit();
  document.selectForm.action='$PHP_SELF';
}

function orderIndekrapport(options)
{
  document.selectForm.action='orderIndekrapport.php?'+options;
  document.selectForm.submit();
  document.selectForm.action='$PHP_SELF';
}

function orderNotaBedragenPdf(options)
{
  document.selectForm.action='orderNotaBedragenPdf.php?'+options;
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

function countChecks()
{
  var theForm = document.selectForm.elements, z = 0, aantal = 0;
  for(z=0; z<theForm.length;z++)
  {
    if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,3) == 'id_')
    {
      if(theForm[z].checked == true)
      {
        aantal++;
      }
    }
  }
  return aantal;
}

function orderPrintOrder(url,fix)
{
  if(fix=='1')
  {
    if(confirm('Let op: dit is een fix-order.'))
    {
      window.open(url);
    }
  }
  else
  {  
     window.open(url);
  }
}

function OrdersVolgendeStatus()
{
  AEConfirm('Weet u zeker dat u de status wilt wijzigen?', 'Order status wijzigen', function () {selectForm.submit();});
}

function OrdersVerzenden()
{
  AEConfirm('Weet u zeker dat u de geselecteerde orders wil verzenden?', 'Order status wijzigen', function () {
    document.selectForm.action= document.selectForm.action + '?sendFix=1';
    selectForm.submit();
  });
}

function OrdersVervallen()
{
  aantal=countChecks();
  AEConfirm('Weet u zeker dat u (' + aantal + ') geselecteerde order(s) wilt laten vervallen?', 'Orders laten vervallen', function () {
    document.selectForm.action= document.selectForm.action + '?vervallen=1';
    selectForm.submit();
  });
}


function AabMail()
{
 var inputform='<form method=\"POST\" action=\"orderAabRtf.php\" name=\"aabForm\" target=\"_blank\"><input type=\"hidden\" name=\"orderId\" id=\"orderId\" value=\"\"><table border=0><tr><td>Settlement AAB</td><td><input type=\"text\" name=\"settlementAAB\" id=\"settlementAAB\" value=\"\"></td></tr><tr><td>Settlement VEC</td><td><input type=\"text\" name=\"settlementVB\" id=\"settlementVB\" value=\"\"></td></tr><tr><td colspan=\"2\"><div id=\"AABMelding\"></div><td></tr></form>';
 $( \"#dialogMessage\" ).html('<div style=\"padding: 10px; max-width: 500px; word-wrap: break-word;\">' + inputform+ '</div>');
 $( \"#dialogMessage\" ).dialog({
    draggable: false,
    modal: true,
    resizable: false,
    width: 'auto',
    title: 'AAB mail opties',
    minHeight: 150,
    buttons: 
    {\"Genereer RTF\": function () 
      {
        var nChecked=0;
        var checkedId='';
        for(z=0; z<document.selectForm.length;z++)
        {
          if(document.selectForm[z].type == 'checkbox' && document.selectForm[z].name.substr(0,3) == 'id_' && document.selectForm[z].checked == true)
          {
            nChecked++;
            checkedId=document.selectForm[z].name.substr(3);
          }
        }
        if(nChecked==0) { $(\"#AABMelding\").html('Er is geen order geselecteerd.'); return 0; }
        else if(nChecked>1){ $(\"#AABMelding\").html('Er zijn '+nChecked+' orders geselecteerd.'); return 0; }
        document.aabForm.orderId.value=checkedId;
        document.aabForm.submit();
        $(this).dialog('destroy');
      }    
      ,\"Sluiten\": function () { $(this).dialog('destroy'); }  
    }
  });
}
";

$content['style'] .= '<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"> <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
echo template($__appvar["templateContentHeader"], $editcontent);

?>
  <br>
<?=$list->filterHeader();?>

  <div id="dialogMessage" title="Basic dialog"></div>

  <form action="<?=$PHP_SELF?>" method="POST" name="selectForm">
    <input type="hidden" name="status" value="<?php echo $orderstatus; ?>">
    <input type="hidden" name="settlementAAB" value="">
    <input type="hidden" name="settlementVB" value="">
    <table class="list_tabel" cellspacing="0">
      <? echo $list->printHeader();//$disableEdit);
      if ($orderstatus <> '')
      {
        ?>
        <div id="wrapper" style="overflow:hidden;">
          <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">
            &nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon'/> <?= vt('Alles selecteren'); ?>
          </div>
          <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">
            &nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon'/> <?= vt('Niets selecteren'); ?>
          </div>
          <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(-1);">
            &nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon'/><?= vt('Selectie omkeren'); ?>
          </div>
          <?php
           if($huidigeStatusIndex<5)
           {
             ?>
             <div class="buttonDiv" style="width:140px;float:left;text-align: center;" onclick="javascript:OrdersVolgendeStatus();">
               <?= vt('Volgende status'); ?>
             </div>
             <?
           }
          if ($orderstatus == 'ingevoerd')
          {
            echo '<div class="buttonDiv" style="width:140px;float:left;text-align: center;" onclick="javascript:OrdersVerzenden();"> ' . vt('Orders verzenden') . '</div>';
          }
          if ($orderstatus == 'in aanmaak' || $orderstatus == 'ingevoerd')
          {
            echo '<div class="buttonDiv" style="width:140px;float:left;text-align: center;" onclick="javascript:OrdersVervallen();"> ' . vt('Laten vervallen') . '</div>';
          }
          ?>

        </div>
        <br/><br/>
        <?
      }

      $db = new DB();

      $query = "SELECT Layout,orderAdviesNotificatie FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
      $db->SQL($query);
      $beheerderRec = $db->lookupRecord();
      if (file_exists('ordersV2PDF_L' . $beheerderRec['Layout'] . '.php'))
      {
        $pdfScript = 'ordersV2PDF_L' . $beheerderRec['Layout'] . '.php';
      }
      else
      {
        $pdfScript = 'ordersPrint.php';
      }
      //listarray($list->columns);

      while ($data = $list->getRow())
      {

        //listarray($data);
        // exit;
        $orderid = $__appvar["bedrijf"] . $data["OrdersV2.id"]["value"];
        $realId = $data["OrdersV2.id"]["value"];
        $realOrderStatus = $data["OrdersV2.orderStatus"]["value"];


        if ($data[".fixVeld"]["value"] == 1)
        {
          $isFix = 1;
        }
        else
        {
          $isFix = 0;
        }

        //$data["OrderRegelsV2.aantal"]["value"]= $data[".aantal"]["value"];
        $data["OrdersV2.transactieType"]["value"] = $__ORDERvar['transactieType'][$data["OrdersV2.transactieType"]["value"]];
        $data["OrdersV2.transactieSoort"]["value"] = $__ORDERvar['transactieSoort'][$data["OrdersV2.transactieSoort"]["value"]];
        $data["OrdersV2.tijdsSoort"]["value"] = $__ORDERvar['tijdsSoort'][$data["OrdersV2.tijdsSoort"]["value"]];
        $data["OrdersV2.orderStatus"]["value"] = $__ORDERvar['orderStatus'][$data["OrdersV2.orderStatus"]["value"]];
        $data[".regels"]["value"] .= "<a href=\"#\" onclick=\"javascript:orderPrintOrder('" . $pdfScript . "?uitvoer=pdf&orderid=" . $realId . "','" . $isFix . "');\">" . drawButton("afdrukken", "", "maak orderbon") . "</a>";
        $data[".regels"]["value"] .= "<a href=\"#\" onclick=\"javascript:orderPrintOrder('" . $pdfScript . "?uitvoer=xls&orderid=" . $realId . "','" . $isFix . "');\">" . drawButton("xls", "", "maak orderbon") . "</a>";
        $data[".regels"]["value"] .= "<input type=\"checkbox\" name=\"id_$realId\" value=\"1\" >";
        
        
        if ($beheerderRec['orderAdviesNotificatie'] == 1)
        {
          $query = "SELECT OrderRegelsV2.id FROM OrderRegelsV2
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
INNER JOIN SoortOvereenkomsten ON Portefeuilles.SoortOvereenkomst = SoortOvereenkomsten.SoortOvereenkomst
WHERE  OrderRegelsV2.mailBevestigingVerzonden = '0000-00-00 00:00:00' AND OrderRegelsV2.add_date>'2017-01-01'
AND SoortOvereenkomsten.adviesRelatie='J' AND OrderRegelsV2.orderid='$realId'";
          if ($db->QRecords($query) > 0)
          {
            $data["tr_style"] = 'style="background-color: #CC9AFF;"';
          }
        }

        if ($data['OrdersV2.fixOrder']['value'] == 1)
        {
          $query = "SELECT id FROM fixOrders WHERE AIRSorderReference='$realId' AND ((laatsteStatus IN('CP','') AND change_date < now() - interval 15 SECOND) OR
                                                                                     (laatsteStatus IN('A') AND change_date < now() - interval 30 SECOND))";
          if ($db->QRecords($query) > 0)
          {
            $data["tr_class"] = "list_dataregel_rood";
          }
        }

        if ($data[".controleStatus"]['value'] > 0)
        {
          $data["tr_class"] = "list_dataregel_rood";
        }

        $query = "SELECT user,change_date FROM tableLocks WHERE `table`='OrdersV2' AND tableId='$realId'";
        if ($db->QRecords($query) > 0 || in_array($realId,$lockedCombiIds))
        {
          $data["tr_class"] = "list_dataregel_geel";
        }

        $query = "SELECT orderid, sum(uitvoeringsAantal) as aantal FROM OrderUitvoeringV2 WHERE orderid='$realId' GROUP BY orderid";
        $db->SQL($query);
        $uitvoeringRec = $db->lookupRecord();
        //listarray($uitvoeringRec);

        if ($realOrderStatus == 2 && $uitvoeringRec['aantal'] > 0 && round($uitvoeringRec['aantal'], 6) != round($data[".aantal"]["value"], 6))
        {
          $data["tr_class"] = "list_dataregel_groen";
          // echo round($uitvoeringRec['aantal'],6) ." ". round($data[".aantal"]["value"],6)."<br>\n";
        }
  
        /** Controleer of de order  opnieuw is ingelegd */
        
        $controlestatus = null;
        if ( empty ($orderstatus) ) {
          foreach ( $_SESSION['OrderListV2']['filter'] as $values ) {
            if ( isset ($values['veldnaam']) && $values['veldnaam'] === 'OrdersV2.orderStatus' && $values['methode'] === 'gelijk' ) {
              $controlestatus = $values['waarde'];
            }
          }
        } elseif ( $orderstatus == 'geweigerd') {
          $controlestatus = 7;
        }
        
        if ( (int) $controlestatus === 7 && $data['OrdersV2.OrderSoort']['value'] != 'M' ) {
          $query = "SELECT OrderRegelsV2.id, kopieOrderId FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid='$realId' AND OrderRegelsV2.orderregelStatus = 7";
    
          if ($db->QRecords($query) > 0)
          {
            while ( $orderRegelDataCheck = $db->nextRecord() ) {
              if ( ! empty ($orderRegelDataCheck['kopieOrderId'])  )
              {
                $data["tr_class"] = "list_dataregel_blauw_force";
              }
            }
          }
        }
        
        
        
        if (round($data[".aantal"]["value"]) <> $data[".aantal"]["value"])
        {
          $data[".aantal"]["value"] = number_format($data[".aantal"]["value"], 6, ',', '.');
        }
        else
        {
          $data[".aantal"]["value"] = number_format($data[".aantal"]["value"], 0, ',', '.');
        }

        echo $list->buildRow($data);

      }
      ?>
    </table>
    <br/><br/>
  </form>

<?

logAccess();
if ($__debug)
{
  echo getdebuginfo();
}
if (isset ($enkeleOrderRegelsAdded))
{
  $query = "DROP TEMPORARY TABLE enkeleOrderRegels";
  $db->SQL($query);
}
echo template($__appvar["templateRefreshFooter"], $content);
?>
