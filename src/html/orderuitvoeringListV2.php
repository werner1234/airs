<?php
/*
    AE-ICT CODEX source module versie 1.6, 19 september 2009
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2018/03/16 16:00:17 $
    File Versie         : $Revision: 1.28 $

    $Log: orderuitvoeringListV2.php,v $
    Revision 1.28  2018/03/16 16:00:17  rm
    opgelopenrente toevoegen onder ordertransrep

    Revision 1.27  2018/02/16 16:09:00  rm
    6562

    Revision 1.26  2017/03/29 14:24:03  rm
    no message

    Revision 1.25  2017/03/05 12:05:38  rvv
    *** empty log message ***

    Revision 1.24  2017/02/08 14:34:12  rm
    5631

    Revision 1.23  2017/02/05 16:24:16  rvv
    *** empty log message ***

    Revision 1.22  2016/12/07 14:54:13  rm
    OrdersV2

    Revision 1.21  2016/11/23 15:40:05  rm
    OrdersV2

    Revision 1.20  2016/10/26 14:37:01  rm
    Ordersv2

    Revision 1.19  2016/09/07 06:16:25  rvv
    *** empty log message ***

    Revision 1.18  2016/07/30 15:13:08  rvv
    *** empty log message ***

    Revision 1.17  2016/07/14 06:40:34  rm
    OrdersV2

    Revision 1.16  2016/06/17 12:19:24  rm
    no message

    Revision 1.15  2016/06/09 08:18:21  rm
    Nieuwe notifier

    Revision 1.14  2016/06/03 15:01:19  rm
    Orders

    Revision 1.13  2016/04/17 17:13:43  rvv
    *** empty log message ***

    Revision 1.12  2016/04/08 14:18:51  rm
    no message

    Revision 1.11  2016/02/21 17:21:12  rvv
    *** empty log message ***

    Revision 1.10  2016/02/19 16:01:17  rm
    orders v2

    Revision 1.9  2015/12/09 15:49:49  rm
    OrdersV2

    Revision 1.8  2015/11/20 08:57:58  rm
    no message

    Revision 1.7  2015/08/12 11:12:31  rvv
    *** empty log message ***

    Revision 1.6  2015/08/09 15:03:35  rvv
    *** empty log message ***

    Revision 1.5  2015/07/24 14:51:19  rm
    ajax voet voor lijst

    Revision 1.4  2015/06/26 07:07:55  rm
    Orders v2

    Revision 1.3  2015/06/20 12:37:34  rm
    Orders

    Revision 1.2  2015/06/20 10:08:59  rm
    Orders

    Revision 1.1  2015/05/25 10:00:21  rvv
    *** empty log message ***

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
include_once("../classes/mysqlListClean.php");

$db= new DB();
$AENumber = new AE_Numbers();

if( isset ($_GET['action']) && $_GET['action'] == 'newOrder')
{
  $unsetVelden=array('id','orderid','status','laatsteStatus','controle_datum','add_date','add_user','change_date','change_user','batchId','aantal','status','brutoBedrag','controle_regels','CheckResult');
  $query="SELECT * FROM OrdersV2 WHERE Orders.id = '".$_GET['orderid']."'";
  $db->SQL($query);
  $orderData=$db->lookupRecord();
  $hoofdorderId=$orderData['id'];
  $hoofdorderIdSamengesteld=$orderData['orderid'];
  
  $orderAantal=$orderData['aantal'];
  foreach($unsetVelden as $veld)
    if(isset($orderData[$veld]))
      unset($orderData[$veld]);
      
  $query="SELECT * FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid = '".$_GET['orderid']."'";
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
  
  $query="SELECT SUM(uitvoeringsAantal) as aantal,SUM(uitvoeringsAantal * uitvoeringsPrijs) as waarde FROM OrderUitvoeringV2 WHERE orderid = '".$_GET['orderid'] ."'";
  $db->SQL($query);
  $uitvoeringsData = $db->lookupRecord();
  
  if($orderAantal<>$uitvoeringsData['aantal'])
  {
    $newAantal=$orderAantal-$uitvoeringsData['aantal'];
    $addData=" add_date=now(),add_user='$USR',change_date=now(),change_user='$USR' ";
    
    $orderData['aantal']=$newAantal;
    $orderData['laatsteStatus']=0;
    $orderData['status']=date("Ymd_Hi")."/$USR Aangemaakt via restant order $hoofdorderIdSamengesteld";
    
    $orderQuery="INSERT INTO OrdersV2 SET ";
    foreach($orderData as $key=>$value)
      $orderQuery.=" `$key`='".mysql_real_escape_string($value)."',";
    $orderQuery.=$addData;  
    
    $db=new DB();
    $db->SQL($orderQuery); 
    $db->Query();
    $orderId=$db->last_id();
    $samengesteldeId=$orderData['vermogensBeheerder'].$orderId;
    $extraLog=date("Ymd_Hi")."/$USR Aantal aangepast ivm deels uitgevoerd. ($orderAantal -> ".$uitvoeringsData['aantal'].")\n";
    $query="UPDATE OrdersV2 SET id='$samengesteldeId' WHERE id='$orderId'"; 
    $db->SQL($query);
    $db->Query();
      
    foreach($orderRegels as $regel)
    { 
      $regel['status']=0;
      if(count($orderRegels)==1)
        $regel['aantal']=$newAantal;
          
      $regel['orderid']=$samengesteldeId;    
      $orderQuery="INSERT INTO OrderRegelsV2 SET ";
      foreach($regel as $key=>$value)
        $orderQuery.=" `$key`='".mysql_real_escape_string($value)."',";
      $orderQuery.=$addData;
      $orderQueries[]=$orderQuery;
      $db->SQL($orderQuery);
      $db->Query();
    }
  }
echo "<script language=\"JavaScript\" TYPE=\"text/javascript\">
parent.location='ordersEditV2.php?action=edit&id=$orderId';
</script>";

echo " Nieuwe order <a href=\"javascript:parent.location='ordersEditV2.php?action=edit&id=$orderId';\"><b> $samengesteldeId </b></a> aangemaakt.<br>\n";
exit;
}


$query="SELECT SUM(OrderRegelsV2.aantal) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid = '".$_GET['orderid']."'";
$db->SQL($query);
$orderData=$db->lookupRecord();

$query="SELECT SUM(uitvoeringsAantal) as aantal,SUM(uitvoeringsAantal * uitvoeringsPrijs) as waarde FROM OrderUitvoeringV2 WHERE orderid = '".$_GET['orderid'] ."'";
$db->SQL($query);
$uitvoeringsData = $db->lookupRecord();

$currentAmount = $orderData['aantal'];

$toAdd = $orderData['aantal'] - $uitvoeringsData['aantal'];

$ordersObj = new OrdersV2();
$orderData = $ordersObj->parseById($_GET['orderid']);

$orderStatus = null;
if ( isset ($_GET['selectedStatus']) && ! empty ($_GET['selectedStatus']) ) {
  $orderStatus = $_GET['selectedStatus'];
} elseif ( isset ($orderData['orderStatus']) && ! empty ($orderData['orderStatus']) ) {
  $orderStatus = $orderData['orderStatus'];
}

if($toAdd > 0 && $orderData['fixOrder'] != 1 ) //
{
  $subHeader     = '<a href="javascript:addRecord();" ><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;toevoegen</a>';
}

$mainHeader    = "Uitvoeringen";

$editScript = "orderuitvoeringEditV2.php";
$allow_add  = true;


if( requestType('ajax') ) {
  $content = '';
    /** selecteer ajax templates **/
    $__appvar['templateContentHeader'] = 'templates/ajax_head.inc';
    $__appvar['templateRefreshFooter'] = 'templates/ajax_voet_list.inc';
    $subHeader = '';
//    if ( checkOrderAcces('handmatig_uitvoeringenMuteren') === true && $orderData['fixOrder'] != 1  && (int)$orderStatus === 2 ) {
//      $subHeader = '<a id="orderUitvoeringAdd" href="' . $editScript . '?action=new&orderid=' . $_GET['orderid'] . '&toAdd=' . $toAdd . '" ><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;toevoegen</a>';
//    }
    
//    $content['inlineStyle'] = "
//      #orderUitvoeringAdd {
//        float: right;
//        border-left: 1px solid lightgray;
//        line-height: 26px;
//        padding-right: 8px;
//        margin-top: -5px;
//        padding-left: 10px;
//      }
//    ";
    
    $content['script_voet'] = "
      $('#orderUitvoeringAdd').on('click', function (event) {
        event.preventDefault();
        console.log(encodeURI($(this).attr('href')));
        $('#modelContent').load(encodeURI($(this).attr('href')));
      });
      
      $('#uitvoeringen .editButton').on('click', function (event) {
        event.preventDefault();
        $('#modelContent').load(encodeURI($(this).attr('href')));
      });
    ";

    if ( isset ($_GET['autoOpenNew']) && (bool)$_GET['autoOpenNew'] === true ) {
      $content['script_voet'] .= "
        $('#orderUitvoeringAdd').click();
      ";
    }
  }

$extraOptions = array();
if (requestType('ajax')) { // if ajax zet zoeken en sorteren uit in de tabel
  $extraOptions = array('search' => false, 'list_order' => false);
}

$list = new mysqlListClean();
//$list = new MysqlList2();

$list->editable = false;
$disableEdit=true;


//controlleren of we wijzig rechten hebben
if ( checkOrderAcces('handmatig_uitvoeringenMuteren') === true) {
  $list->editable = true;
  $disableEdit=false;
}


/** fixorder niet wijzigen **/

if ( intval($orderData['fixOrder']) === 1 ) {
  $list->editable = false;
  $disableEdit=true;
}


$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 100;

$list->addColumn("OrderUitvoeringV2","id", $extraOptions + array('list_visible' => false,"search"=>false));
$list->addColumn("OrderUitvoeringV2","uitvoeringsAantal",$extraOptions + array("search"=>false));
$list->addColumn("OrderUitvoeringV2","uitvoeringsPrijs",$extraOptions + array("search"=>false, "list_align"=>"right"));
$list->addColumn("OrderUitvoeringV2","uitvoeringsDatum",$extraOptions + array("search"=>false,'list_width'=>200));

//orderTransRep

$query="SELECT max(check_module_ORDERNOTAS) as check_module_ORDERNOTAS, max(orderTransRep) as orderTransRep FROM Vermogensbeheerders";
$db->SQL($query);
$verm=$db->lookupRecord();
if( (int) $verm['check_module_ORDERNOTAS'] === 1 ) {
  $list->addColumn("OrderUitvoeringV2","nettokoers",$extraOptions + array("search"=>false, "list_align"=>"right"));
  $list->addColumn("OrderUitvoeringV2","opgelopenrente",$extraOptions + array("search"=>false, "list_align"=>"right"));
} else {
  if( (int) $verm['orderTransRep'] == 1 ) {
    $list->addColumn("OrderUitvoeringV2","opgelopenrente",$extraOptions + array("search"=>false, "list_align"=>"right"));
  }
}




//$list->addColumn("","vulling",$extraOptions + array("search"=>false));

$list->setWhere("orderid='".$_GET['orderid']."'");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder((isset ($_GET['sort']) ? $_GET['sort'] : null), (isset ($_GET['direction']) ? $_GET['direction'] : null));
// set searchstring
$list->setSearch((isset ($_GET['selectie']) ? $_GET['selectie'] : null));
// select page
$list->selectPage((isset ($_GET['page']) ? $_GET['page'] : null));
$content['javascript'] = '';
$content['pageHeader'] = '';

$content['javascript'] .= "
function addRecord()
{
	document.location = '".$editScript."?action=new&orderid=".$_GET['orderid']."&toAdd=$toAdd';
}
";



?>
          <div class="formTitle textB"><?=$mainHeader . (isset($subHeader)?$subHeader:'');?></div>
          <div class="formContent">
      
  <?=template($__appvar["templateContentHeader"],$content);?>
  <table cellspacing="0" class="table table-boxed table-striped table-hover" style="width:100%">
    <?=$list->printHeader($disableEdit);?>
    <?php 
      $totalUitvoeringsAantal = 0;

      while($data = $list->getRow()) {
//        unset($data['id']);
        foreach($data as $key=>$value)
          $data[$key]['noClick']=true;
        $totalUitvoeringsAantal += $data['OrderUitvoeringV2.uitvoeringsAantal']['value'];
        $data['vulling'] = '';
        echo $list->buildRow($data);
      }
      if ( $totalUitvoeringsAantal > 0 ) {
      
      ?>
        <tfoot>
          <tr class="list_dataregel">
            <?if($disableEdit==false){echo '<td></td>';} ?>
            <td style="padding-right: 6px;" class=" textR"><?=number_format($totalUitvoeringsAantal, 4, ',', '.');?></td>
            <td></td>
            <td colspan="99999"></td>
          </tr>
        </tfoot>
<?php } ?>
  </table>
  <?php
  //wanneer het een nominaal order is
  if ( ($toAdd > 0 || $orderData['orderSoort'] === 'N') && checkOrderAcces('handmatig_uitvoeringenMuteren') === true && $orderData['fixOrder'] != 1  ) {
    echo '<a class="btn-new btn-default" style="margin: 5px;" id="orderUitvoeringAdd" href="' . $editScript . '?action=new&orderid=' . $_GET['orderid'] . '&toAdd=' . $toAdd . '" >
      <i class="fa fa-plus-circle" aria-hidden="true"></i> toevoegen
    </a>';
  }

  if( checkOrderAcces ('handmatig_uitvoeringenMuteren') === true && ( $toAdd > 0 && intval($orderData['fixOrder']) === 0) )
  {
    echo '<a id="changeOrderAmount" class="btn-new btn-default pull-right" style="margin: 5px;" href="ordersEditV2.php?action=edit&id='.$_GET['orderid'].'&copyid=-1&remove=1" >
      <i class="fa fa-refresh" aria-hidden="true"></i> Order aantal aanpassen.
    </a>';

    echo '<a id="changeOrderAmountNewOrder" class="btn-new btn-default pull-right" style="margin: 5px;" href="ordersEditV2.php?action=edit&id='.$_GET['orderid'].'&copyid='.$_GET['orderid'].'&toAdd='.$toAdd.'" >
      <i class="fa fa-plus-circle" aria-hidden="true"></i> Order aantal aanpassen en restant naar nieuwe order.
    </a>';

    echo '
      <script>
        $(function() {
          $("#changeOrderAmount").on("click", function (e) {
            $url = $("#changeOrderAmount").attr("href");
            e.preventDefault();
            AEConfirm(
              "Weet u zeker dat het orderaantal (' . $currentAmount . ' wordt ' .$totalUitvoeringsAantal . ') wordt aangepast ?", 
              "Order aantal aanpassen", 
              function () {
                window.location.href = $url;
              }, 
              function () {
                return false;
              }
            );
          });
          
          
          $("#changeOrderAmountNewOrder").on("click", function (e) {
            $url = $("#changeOrderAmountNewOrder").attr("href");
            e.preventDefault();
            AEConfirm(
              "Weet u zeker dat het  restant ('.$toAdd.') naar een nieuwe order wordt doorgezet ?", 
              "Order aantal aanpassen en nieuwe order", 
              function () {
                window.location.href = $url;
              }, 
              function () {
                return false;
              }
            );
          });
        });
      </script>
    ';


  }
  logAccess();
  if($__debug)
  {
   // echo getdebuginfo();
  }
  ?>
</div>
<?=template($__appvar["templateRefreshFooter"],$content);?>