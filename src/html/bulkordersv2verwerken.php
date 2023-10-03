<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once('orderControlleRekenClassV2.php');

session_start();
$__appvar['rowsPerPage']=10000;

if(isset($__appvar['extraOrderLogging']))
  $extraLog=$__appvar['extraOrderLogging'];
else
  $extraLog=false;

if($extraLog==true)
{
  $tmpLog='POST_log |';
  foreach($_POST as $key=>$value)
    $tmpLog.="$key:$value;";
  logit($tmpLog);
}

$db=new DB();

 if($_POST['verwerk'] > 0)
 {
  $nieuweWaarden=array();
  foreach ($_POST as $key=>$value)
  {
    if(substr($key,0,15)=='transactieType_')
      $nieuweWaarden[substr($key,15)]['transactieType']=$value;
    elseif(substr($key,0,12)=='tijdsLimiet_')
      $nieuweWaarden[substr($key,12)]['tijdsLimiet']=date('Y-m-d',form2jul($value));
    elseif(substr($key,0,11)=='tijdsSoort_')
      $nieuweWaarden[substr($key,11)]['tijdsSoort']=$value;
    elseif(substr($key,0,12)=='koersLimiet_')
      $nieuweWaarden[substr($key,12)]['koersLimiet']=$value;
    elseif(substr($key,0,9)=='fixOrder_')
      $nieuweWaarden[substr($key,9)]['fixOrder']=$value;
    elseif(substr($key,0,6)=='beurs_')
      $nieuweWaarden[substr($key,6)]['beurs']=$value;
    elseif(substr($key,0,16)=='transactieSoort_')
      $nieuweWaarden[substr($key,16)]['transactieSoort']=$value;
    elseif(substr($key,0,10)=='careOrder_')
      $nieuweWaarden[substr($key,10)]['careOrder']=$value;
  }
}


$subHeader     = "";
if(isset($_GET['message']))
  $subHeader=$_GET['message'];
$mainHeader    = vt("bulkorders controleren.");

$editScript = "TijdelijkeBulkOrdersV2Edit.php";
$allow_add  = false;

$list = new MysqlList2();
//$list->idField = "id";
$list->idTable="OrdersV2";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addColumn("","sel",array("list_width"=>"30","search"=>false));
$list->addColumn("","regels",array("list_width"=>"50","search"=>false));
$list->addColumn("","Aantal/Bedrag",array("sql_alias"=>'(select sum(OrderRegelsV2.aantal+OrderRegelsV2.bedrag) from OrderRegelsV2 where OrderRegelsV2.orderid=OrdersV2.id)',"list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","fondsOmschrijving",array("list_width"=>"200","search"=>false));
$list->addFixedField("OrdersV2","depotbank",array("list_width"=>"50","search"=>false));
$list->addFixedField("OrdersV2","transactieSoort",array("list_width"=>"50","search"=>false));
$list->addFixedField("OrdersV2","transactieType",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","tijdsLimiet",array("list_width"=>"150","search"=>false));
$list->addFixedField("OrdersV2","tijdsSoort",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","koersLimiet",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","beurs",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","fixOrder",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","careOrder",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","fixVerzenddatum",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","OrderSoort",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('OrdersV2'),'BulkOrdersV2List');
//listarray($_SESSION['bulkorder']['laatsteIds']);
//$_SESSION['bulkorder']['laatsteIds']=array(308,309);
$list->setWhere("OrdersV2.id IN('".implode("','",$_SESSION['bulkorder']['laatsteIds'])."') AND OrdersV2.orderStatus<1");

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);


// select page
$list->selectPage($_GET['page']);


$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$_SESSION['submenu'] = New Submenu();
//if($__debug)
//  $_SESSION['submenu']->addItem('Alle tijdelijkeorderegels',"tijdelijkebulkordersv2List.php");
$_SESSION['submenu']->addItem("excel uitvoer",'javascript:parent.frames[\'content\'].exportExcel();');

//$_SESSION['submenu']->addItem('Orders controleren',"bulkordersv2verwerken.php");
$_SESSION['submenu']->addItem($html,"");

$content['style2']='<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';//<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div>
</div>
";
/*
<div id=\"wrapper\" style=\"overflow:hidden;\">
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> Alles selecteren</div>
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> Niets selecteren</div>
<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> Selectie omkeren</div>
</div>
<br>";
*/
//<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"javascript:document.listForm.verwerk.value=1;document.listForm.submit();\">&nbsp;&nbsp;<img src='icon/16/add.png' class='simbisIcon' />Orders aanmaken</div>

$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/ordersEditV2.js\" type=text/javascript></script>";


$nu=date('d-m-Y');

$content['javascript'] .= "

function showLoading(text) {
  // add the overlay with loading image to the page
  $('#overlay').remove();
  var over = '<div id=\"overlay\"><div id=\"loading-box\">' +
          '<div id=\"loading-txt\">' + text + '</div>' +
          '<img id=\"loading-img\" src=\"images/ajax-loader.gif\">' +
          '</div></div>';
  $(over).appendTo('body');
}
;
function removeLoading() {
  $('#overlay').remove();
}

function exportExcel()
{
  var targetBackup=document.listForm.target;
  document.listForm.action='orders2xls.php?xls=1';
  document.listForm.target='_blank';
  document.listForm.submit();
  document.listForm.action='$PHP_SELF';
  document.listForm.action=targetBackup;
}

function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function checkAll(optie)
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
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

function selectFixCheckbox()
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
    if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,9) == 'fixOrder_')
    {
      if(theForm[z].disabled==false)
      {
        theForm[z].checked=true;
      }
    }
  }
  changeFix();
}

function tsChanged(veldId)
{
  if( $('#tijdsSoort_'+veldId).val() == 'GTC')
  {
    $('#tijdsLimiet_'+veldId).val('');
    $('#tijdsLimiet_'+veldId).prop('readonly', true);
    $('#tijdsLimiet_'+veldId).datepicker('disable');
    $('#tijdsLimiet_'+veldId).addClass('notEditable');
  }
  else
  {
    $('#tijdsLimiet_'+veldId).prop('readonly', false);
    $('#tijdsLimiet_'+veldId).prop('disabled', false);
    $('#tijdsLimiet_'+veldId).datepicker('enable');
    $('#tijdsLimiet_'+veldId).removeClass('notEditable');
    $('#tijdsLimiet_'+veldId).val('".$nu."');
  }
}

function transFocus(veldId,fondsType) {
  var curVal=$('#transactieSoort_'+veldId).val();
  $('#transactieSoort_'+veldId+' option').prop('disabled', true);
  $('#transactieSoort_'+veldId+' option:first-child').prop('disabled', true);

  if (fondsType == 'OPT') {
    $('#transactieSoort_'+veldId+' option[value=\'AO\']').prop('disabled', false);
    $('#transactieSoort_'+veldId+' option[value=\'VO\']').prop('disabled', false);
    $('#transactieSoort_'+veldId+' option[value=\'AS\']').prop('disabled', false);
    $('#transactieSoort_'+veldId+' option[value=\'VS\']').prop('disabled', false);
  } else {
  //  $('#transactieSoort_'+veldId+' option:first-child').prop('disabled', true);
    $('#transactieSoort_'+veldId+' option[value=' + curVal + ']').prop('disabled', false);
   // $('#transactieSoort_'+veldId+' option[value=\'A\']').prop('disabled', false);
   // $('#transactieSoort_'+veldId+' option[value=\'V\']').prop('disabled', false);
  }
}

function changeBeurs(veldId,beursVerplicht,fixOk)
{
  var curVal=$('#beurs_'+veldId).val();
  
  if((curVal != '' || beursVerplicht == 0) && fixOk == 1 )
  {
    $('#fixOrder_'+veldId).prop('disabled', false);
  }
  else
  {
    $('#fixOrder_'+veldId).prop('checked', false);
    $('#fixOrder_'+veldId).prop('disabled', true);
  }
  changeFix();
}

function changeTransactieType(veldId)
{
  var curVal=$('#transactieType_'+veldId).val();
  if(curVal == 'B')
  {
    $('#tijdsSoort_'+veldId).val('GTC');
    $('#koersLimiet_'+veldId).val('0.00000');
    $('#tijdsLimiet_'+veldId).val('');
    $('#koersLimiet_'+veldId).prop('readonly', true);
  }
  else if(curVal == 'L')
  {
    $('#tijdsSoort_'+veldId).val('DAT');
    $('#koersLimiet_'+veldId).val('0.00000');
    $('#tijdsLimiet_'+veldId).val('');
    $('#koersLimiet_'+veldId).prop('readonly', false);
  }
}

function changeFix(careorderVerplicht)
{
  var theForm = document.listForm.elements, z = 0;
  var toonVerzend=false;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,9) == 'fixOrder_')
   {
      veldId=theForm[z].name.substr(9);
      if(theForm[z].checked == true)
      {
        toonVerzend=true;
        if(careorderVerplicht==1)
        {
          $('#careOrder_'+veldId).prop('checked', true);
        }
        $('#careOrder_'+veldId).removeAttr('disabled');
      }
      else
      {
        $('#careOrder_'+veldId).attr('disabled', true);
        $('#careOrder_'+veldId).removeAttr('checked');
      }
      tsChanged(veldId);
   }
  }
  if(toonVerzend==true)
  {
    $('#divVerzendFix').show();
  }
  else
  {
    $('#divVerzendFix').hide();
  }
}


function tlChanged(veldId)
{
  if ( $('#tijdsLimiet_'+veldId).val() ) {
    var now = new Date();
    var d1 = (Math.floor(now.getTime()/86400000));
    var nowAndSixMonthsDate = new Date(new Date(now).setMonth(now.getMonth()+6));
    var nowAndSixMonthsInt = Math.floor(nowAndSixMonthsDate.getTime()/86400000);
    
    var dateParts=$('#tijdsLimiet_'+veldId).val().split('-');
    var formDate = new Date(dateParts[2], dateParts[1]-1, dateParts[0],12);
    var d2=(Math.floor(formDate.getTime()/86400000));

    if ( d1 > d2 ) {
      alert('Datum mag niet in het verleden liggen.');
      $('#tijdsLimiet_'+veldId).val('".$nu."');
      return false;
    } else {
      if (d2 > getDateFromFormat('01-02-'+(now.getFullYear()+1),'dd-MM-yyyy') && d1 < getDateFromFormat('01-12-'+now.getFullYear(),'dd-MM-yyyy')) {
        alert('Datum moet voor 01-02-'+(now.getFullYear()+1)+' liggen.');
        $('#tijdsLimiet_'+veldId).val('".$nu."');
        return false;
      } else if (d2 > nowAndSixMonthsInt) {
        alert('Datum moet voor '+nowAndSixMonthsDate.getDate()+'-'+(nowAndSixMonthsDate.getMonth()+1)+'-'+nowAndSixMonthsDate.getFullYear()+' liggen.');
        $('#tijdsLimiet_'+veldId).val('".$nu."');
        return false;
      }
      //tsChanged(veldId);
      return true;
    }
  }
}

function klChanged(veldId)
{
console.log(veldId+' '+$('#koersLimietHidden_'+veldId).val() );
  if( $('#koersLimietHidden_'+veldId).val() != '')
  {
    if( isNumber ($('#koersLimiet_'+veldId).val()) && isNumber ($('#koersLimietHidden_'+veldId).val()))
    {
     tmp = ($('#koersLimiet_'+veldId).val() / $('#koersLimietHidden_'+veldId).val()) *100;
     if(tmp < 90 || tmp > 110)
       AEMessage('Limiet wijkt meer dan 10% van de laatst bekende koers af. ('+$('#koersLimietHidden_'+veldId).val()+')','Limiet afwijking',function (){ tmp=false;}  );
    }
    else {
      alert('Geen referentiekoers kunnen vergelijken.');
    }
  }
}

function openModal (htmlData) {
  $('#modelContent').html(htmlData);
  $('#uiModalDiv').dialog({
    width: 700,
    autoOpen: false,
    dialogClass: \"test\",
    modal: true,
    responsive: true
  });
  
  $('#uiModalDiv').dialog(\"open\");
}

function checkFormulier()
{
  var theForm = document.listForm.elements, z = 0, formOke = true, melding='';
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'text')
   {
     if(theForm[z].name.substr(0,12) == 'koersLimiet_')
     {
       var orderId=theForm[z].name.substr(12);
       if($('#transactieType_'+orderId).val() == 'L' && theForm[z].value == 0.0 )
       {
         melding+='De limiet koers van bij order '+orderId+' is '+theForm[z].value+'!\\n';
         formOke=false;
       }
        if($('#tijdsSoort_'+orderId).val() == 'DAT' && $('#tijdsLimiet_'+orderId).val() == '')
       {
        melding+='De limiet datum van bij order '+orderId+' is niet gevuld!\\n';
        formOke=false;
       }
     }
   }

  }
  if(melding!='')
  {
    alert('Corrigeer problemen en probeer opnieuw.\\n'+melding);
  }
  return formOke;
}

function checkBegin()
{
  var theForm = document.listForm.elements, z = 0, formOke = true, melding='';
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'text')
   {
     if(theForm[z].name.substr(0,12) == 'koersLimiet_')
     {
       var orderId=theForm[z].name.substr(12);
       tsChanged(orderId);
     }
   }
  }
 

}


";
echo template($__appvar["templateContentHeader"],$content);
$disableEdit=true;

echo "<script>showLoading('Verwerken');</script>";
echo str_repeat(" ",4096)."\n";
flush();
ob_flush();
$loadingActief = true;

  $totaalLog='';
  if($_POST['verwerk'] > 0)
  {
    $fixorderIds=array();
    $verzendIdsSorted=array();
    $log=new orderLogs();
    foreach($nieuweWaarden as $id=>$velden)
    {
      if($velden['tijdsSoort']=='GTC')
      {
        $jaarLater=mktime(0,0,0,date('m'),date('d'),date('Y')+1);
        $dagVanWeek=date('w',$jaarLater);
        if($dagVanWeek==0 || $dagVanWeek==6)
          $jaarLater=$jaarLater-3*86400;
        $velden['tijdsLimiet'] = date('Y-m-d',$jaarLater-86400);
      }
      $selectvelden=array_keys($velden);
      $query="SELECT ".implode(',',$selectvelden)." , sum(OrderRegelsV2.aantal) as aantal FROM OrdersV2 LEFT JOIN OrderRegelsV2 ON OrdersV2.id=OrderRegelsV2.orderId WHERE OrdersV2.id='$id' GROUP BY OrdersV2.id";
      $db->SQL($query);
      $old=$db->lookupRecord();
      $update=array();
      foreach($velden as $key=>$value)
        if($old[$key]<>$value)
          $update[$key]=$value;
      if(count($update) > 0)
      {
        /*
        if($velden['fixOrder']==1 && $old['aantal'] <> floor($old['aantal']))
        {
          $totaalLog.="Fix order $id bevat decimalen in het aantal. Opslaan niet mogelijk.<br>\n";
          continue;
        }
        */
        $logTxt='';
        $query="UPDATE OrdersV2 SET change_date=now(),change_user='$USR'";
        foreach($update as $key=>$value)
        {
          $query.=", $key='".mysql_real_escape_string($value)."'";
          $logTxt.='('.$key.'->'.$value.')';
        }
        $query.=" WHERE id='$id' AND OrdersV2.orderStatus<1";  
        $db->SQL($query);
        $db->Query();
        if($db->mutaties())
        {
          $log->addToLog($id,'',$logTxt);
          $totaalLog.="Order $id (".count($update).") mutatie(s) verwerkt.<br>\n";
        }
       }
      if($velden['fixOrder']==1)
      {
        $fixorderIds[$id]=$id;
      }
    }
    $query="SELECT OrdersV2.id,OrdersV2.transactieSoort,OrdersV2.fixOrder, OrderSoort FROm OrdersV2 WHERE id IN('".implode("','",$fixorderIds) ."') AND OrdersV2.fixVerzenddatum='0000-00-00' AND OrdersV2.fixOrder=1 ORDER BY OrdersV2.transactieSoort desc,OrdersV2.id ";
    $db->SQL($query);
    $db->Query();
    while($fixRecord=$db->nextRecord())
    {
      $verzendIdsSorted[$fixRecord['id']]=$fixRecord;
    }
  }
  if($_POST['verwerk'] == 2)
  {
    $db=new DB();
  
    $adviesStatus = checkOrderAcces ('orderAdviesNotificatie');
    
    foreach($verzendIdsSorted as $orderid=>$velden)
    {
      if($velden['fixOrder']==1)
      {
        $canSendOrder=true;
        $orderObject=new OrdersV2();
        $orderObject->getById($orderid);
        $query="select sum(OrderRegelsV2.aantal) as aantal ,sum(OrderRegelsV2.bedrag) as bedrag, portefeuille, count(id) as aantalRegels from OrderRegelsV2 where OrderRegelsV2.orderid='$orderid'";
        $db->SQL($query);
        $orderregel=$db->lookupRecord();
        /*
        if($orderregel['aantal'] <> floor($orderregel['aantal']))
        {
          $totaalLog.="Fix order $orderid bevat decimalen in het aantal. Opslaan en verzenden niet mogelijk.<br>\n";
          continue;
        }
        */
        if ( checkOrderAcces('orderVierOgen') == false ||  ( checkOrderAcces('orderVierOgen') == true && $orderObject->get('add_user') !== $USR) )
        {
          if($__appvar["bedrijf"]=='ANO')//Als test alleen voor ANO.
          {
            if ($orderregel['aantalRegels'] == 1 && isset($orderregel['portefeuille']) && !empty ($orderregel['portefeuille']))
            {
              $isAdviceRelation = isAdviesRelatie($orderregel['portefeuille']);
              if ($isAdviceRelation === true && in_array($adviesStatus, array(1, 3)) && ($velden['OrderSoort'] === 'E'))
              {
                $canSendOrder = false;
              }
            }
            if ($canSendOrder === true)
            {
              logIt("Voor verzendFix() van order $orderid (via bulkorder)");
              if ($orderObject->verzendFix())
              {
                $totaalLog .= "Fix order $orderid verzonden.<br>\n";
              }
              logIt("Na verzendFix() van order $orderid (via bulkorder)");
            }
            else
            {
              echo "<script> alert('Order " . $orderid . ", Adviesrelatie niet via FIX verstuurd, handmatig verwerken!');</script>";
              $totaalLog .= "Order $orderid, Adviesrelatie niet via FIX verstuurd, handmatig verwerken!.<br>\n";
            }
          }
          else // Huidige functionaliteit
          {
            logIt("Voor verzendFix() van order $orderid (via bulkorder)");
            if ($orderObject->verzendFix())
            {
              $totaalLog .= "Fix order $orderid verzonden.<br>\n";
            }
            logIt("Na verzendFix() van order $orderid (via bulkorder)");
          }
          
        }
        else
        {
          $totaalLog.="Onvoldoende rechten om fix order $orderid te verzenden.<br>\n";
        }

      }
    }
  }

if($loadingActief==true)
{
  echo "<script>removeLoading();</script>";
}

?>

<div class="main_content">
  <?=( (isset($totaalLog) && ! empty ($totaalLog)) ? '<div class="alert alert-info">' . $totaalLog . ' </div>' : '');?>
  
  <div class="row"> 
    <div class="formHolder box box12">
      <div class="formTitle textB">Filters</div>
      <div class="formContent padded-10">
        <?=$list->filterHeader();?>
      </div>
    </div>
  </div>


   <div class="row">
    <div class="formHolder box box12 {fieldsetClass}">
      <div class="formTitle textB">Bulkorders</div>
      <div class="formContent">
        
        <form name="listForm" method="POST" action="bulkordersv2verwerken.php">
          <table class="list_tabel" cellspacing="0" style="margin-left:0px; width:100%;margin-bottom: 0;">
            <input type="hidden" name="verwerk" value="1">
            <?=$list->printHeader($disableEdit);?>
            <?php
            $db=new DB();
            global $__ORDERvar;
            $fixVerzendKnopTonen=false;
            $verwijderenUitSelectie=array();
            while($data = $list->getRow())
             {
              foreach($data as $key=>$value)
                $data[$key]['noClick']=$disableEdit;

              $data['disableEdit']=$disableEdit;

              $query="SELECT OrderRegelsV2.client,OrderRegelsV2.portefeuille,OrderRegelsV2.aantal,OrderRegelsV2.bedrag,OrdersV2.beurs,OrdersV2.fondsBankcode,
              fixDepotbankenPerVermogensbeheerder.depotbank,Fondsen.fonds, OrdersV2.fixVerzenddatum,OrdersV2.orderSoort,fixDepotbankenPerVermogensbeheerder.meervoudigViaFix,fixDepotbankenPerVermogensbeheerder.meervNominaalFIX
              FROM OrderRegelsV2 
              LEFT JOIN Portefeuilles ON OrderRegelsV2.portefeuille=Portefeuilles.portefeuille
              LEFT JOIN fixDepotbankenPerVermogensbeheerder ON Portefeuilles.Vermogensbeheerder=fixDepotbankenPerVermogensbeheerder.vermogensbeheerder AND Portefeuilles.depotbank = fixDepotbankenPerVermogensbeheerder.depotbank 
              JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id 
              LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
              WHERE orderId='".$data['id']['value']."' AND OrdersV2.orderStatus<1";
              $db->SQL($query);
              $db->Query();
              $table='<table><tr class=\\\'list_kopregel\\\'><td class=\\\'list_kopregel_data\\\'  width=\\\'200\\\'><b>Client</b></td><td class=\\\'list_kopregel_data\\\' width=\\\'200\\\'><b>Portefeuille</b></td><td class=\\\'list_kopregel_data\\\' width=\\\'100\\\' align=\\\'right\\\'><b>Aantal/Bedrag</b></td></tr>';
              $fixOk=0;
              $fixFatalError=false;
              $regels=0;
              while($dbdata=$db->nextRecord())
              {
                $regels++;
                $fixMelding='';
                if($dbdata['depotbank'] <> '' && $dbdata['fixVerzenddatum']=='0000-00-00 00:00:00' && ($dbdata['beurs'] <> '' || $dbdata['fondsBankcode'] <> '') )// && $dbdata['fonds'] <> ''
                {
                  $fixOk=1;
                }
                else
                {
                  $checkVelden=array('depotbank');
                  
                    if($dbdata['depotbank']=='')
                    {
                      $fixMelding.="depotbank bevat ongeldige waarde. ";
                      $fixFatalError=true;
                    }
                    if($dbdata['beurs']=='' && $dbdata['fondsBankcode']=='')
                    {
                      $fixMelding.="beurs en/of fondsBankcode moet gevuld zijn. ";
                      $fixFatalError=true;
                    }

                }
                
                if($dbdata['meervoudigViaFix']==0 && $dbdata['orderSoort']=='M')
                {
                  $fixOk=0;
                  $fixFatalError=true;  
                  $fixMelding='Meervoudige fixorder nog niet mogelijk.';
                }
                elseif($dbdata['meervNominaalFIX']==0 && $dbdata['orderSoort']=='O')
                {
                  $fixOk=0;
                  $fixFatalError=true;
                  $fixMelding='Meervoudige nominaal fixorder nog niet mogelijk.';
                }
                
                if($regels%2==0)
                  $color='#EEE';
                else
                  $color='#FFF';    
                $table.='<tr class=\\\'list_dataregel\\\'  style=\\\'background: '.$color.'\\\' ><td>'.$dbdata['client'].'</td><td>'.$dbdata['portefeuille'].'</td><td align=\\\'right\\\' >'.($dbdata['bedrag']<>0?$dbdata['bedrag']:$dbdata['aantal']).'</td> </tr>';
              }

            //  if($regels>1)
            //    $fixOk=false;
              $table.="</table>";


               $orderObject=new OrdersV2();
               $orderObject->getById($data['id']['value']);
               $fonds=$orderObject->get('fonds');
               $fondsBankcode=$orderObject->get('fondsBankcode');
               if($fondsBankcode=='')
                 $beursVerplicht=1;
               else
                 $beursVerplicht=0;
              $query="SELECT Koers,Fonds FROM Fondskoersen WHERE Fonds='".mysql_real_escape_string($fonds)."' ORDER BY Datum desc limit 1";
              $db->SQL($query);
              $koers=$db->lookupRecord();
              $koersLimietHidden='<input type="hidden" name="koersLimietHidden_'.$data['id']['value'].'" id="koersLimietHidden_'.$data['id']['value'].'" value="'.$koers['Koers'].'">';

              $query="SELECT fondssoort FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."' limit 1";
              $db->SQL($query);
              $fondssoort=$db->lookupRecord();

               $query="SELECT
fixDepotbankenPerVermogensbeheerder.careOrderVerplicht,
fixDepotbankenPerVermogensbeheerder.fixDefaultAan
FROM
OrdersV2
JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Portefeuilles ON OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN fixDepotbankenPerVermogensbeheerder ON Portefeuilles.Vermogensbeheerder = fixDepotbankenPerVermogensbeheerder.vermogensbeheerder AND Portefeuilles.Depotbank = fixDepotbankenPerVermogensbeheerder.depotbank
WHERE OrdersV2.id='".$data['id']['value']."'
limit 1";
  $db->SQL($query);
  $fixDepotbankenPerVermogensbeheerder=$db->lookupRecord();

               if($fixDepotbankenPerVermogensbeheerder['careOrderVerplicht']==1)
                 $careOrderVerplicht=1;
               else
                 $careOrderVerplicht=0;

               $db->SQL($query);
               $fondssoort=$db->lookupRecord();

              $form=new Form();

 
              $data['OrdersV2.koersLimiet']['list_nobreak']=true; 

              if($data['OrdersV2.fixVerzenddatum']['value']!='0000-00-00 00:00:00')
              {
                $editVelden=array();
              }
              else
              {
                $editVelden=array('koersLimiet','transactieType','tijdsLimiet','tijdsSoort','beurs','fixOrder', 'careOrder');
             //   if($fondssoort['fondssoort']=='OPT')
                  $editVelden[]='transactieSoort';
                  
                $orderObject->setOption('transactieType','form_extra','onchange="javascript:changeTransactieType('.$data['id']['value'].')"');
                $orderObject->setOption('tijdsSoort','form_extra','onchange="javascript:tsChanged('.$data['id']['value'].')"');
                $orderObject->setOption('tijdsLimiet','form_extra',' onchange="javascript:tlChanged('.$data['id']['value'].')"');
                $readonly='';
                if($data['OrdersV2.transactieType']['value']=='B')
                {
                  $readonly='READONLY';
                  $orderObject->set('koersLimiet','0.00000');
              
                }
                $orderObject->setOption('koersLimiet','form_extra',$readonly.' onchange="javascript:klChanged('.$data['id']['value'].')"');
                $orderObject->setOption('transactieSoort','form_extra','onfocus="javascript:transFocus('.$data['id']['value'].',\''.$fondssoort['fondssoort'].'\')"');
                $orderObject->setOption('transactieType','form_select_option_notempty',true);
         
              }
               if($fixOk==false)
               {
                $orderObject->set('fixOrder',0);
                $orderObject->setOption('fixOrder','form_extra','DISABLED onchange="changeFix('.$careOrderVerplicht.');"');

                $orderObject->set('careOrder',0);
                $orderObject->setOption('careOrder','form_extra','DISABLED "');
               }
               else
                 $orderObject->setOption('fixOrder','form_extra','onchange="changeFix('.$careOrderVerplicht.');"');
                 
               //  echo $data['id']['value']." $fixFatalError <br>\n";
               //if($fixFatalError===false)
                 $orderObject->setOption('beurs','form_extra','onchange="changeBeurs('.$data['id']['value'].','.$beursVerplicht.','.$fixOk.');"');
              //$editVelden=array('tijdsLimiet');

              if ( checkOrderAcces('handmatig_opslaan') === true)
              {
                foreach($editVelden as $veld)
                {
                  unset($data['OrdersV2.'.$veld]['list_format']);
                  unset($data['OrdersV2.'.$veld]['list_numberformat']);

                  $orderObject->data['fields'][$veld]['form_size']='8';
                  $data['OrdersV2.'.$veld]['list_nobreak']=true;
                  $data['OrdersV2.'.$veld]['value']=str_replace($veld,$veld.'_'.$data['id']['value'],$form->makeInput($veld,$orderObject));
                  $data['OrdersV2.'.$veld]['form_type']='text';
                  if($veld=='tijdsLimiet')
                    $data['OrdersV2.tijdsLimiet']['value'] = '<span class="input-group">'.$data['OrdersV2.tijdsLimiet']['value'] . '</span>';
                }
              }
              if($fixOk==false)
                $data['OrdersV2.fixOrder']['value']="<label title='Fix order niet mogelijk. ".$fixMelding."'>".$data['OrdersV2.fixOrder']['value']."</label>"; 

              if($data['OrdersV2.fixVerzenddatum']['value']!='0000-00-00 00:00:00')
              {
                $data['OrdersV2.fixOrder']['list_nobreak']=true; 
                $data['OrdersV2.fixOrder']['form_type']='text';
                $data['OrdersV2.fixOrder']['value']=date('d-m-Y H:i:s',db2jul($data['OrdersV2.fixVerzenddatum']['value']));
                $verwijderenUitSelectie[]=$data['id']['value'];
              }


             $data['.regels']['list_nobreak']=true; 
             $data['.regels']['value']="<a href=\"#\" onclick=\"openModal('".$table."');\"><img src='icon/16/form_red.png' class='simbisIcon' /> </a> $koersLimietHidden";


            // $realId=$data['id']['value'];
            // if($data[".fixVeld"]["value"]==1){$isFix=1;}else{$isFix=0;}
            // $data[".regels"]["value"] .= "<a href=\"#\" onclick=\"javascript:orderPrintOrder('ordersPrint.php?uitvoer=pdf&orderid=".$realId."','".$isFix."');\">".drawButton("afdrukken","","maak orderbon")."</a>";
            // $data[".regels"]["value"] .= "<a href=\"#\" onclick=\"javascript:orderPrintOrder('ordersPrint.php?uitvoer=xls&orderid=".$realId."','".$isFix."');\">".drawButton("xls","","maak orderbon")."</a>";
            // $data[".regels"]["value"] .= "<input type=\"checkbox\" name=\"id_$realId\" value=\"1\" > ";



             $data['tr_title']='orders';
             echo $list->buildRow($data);
             }
            ?>
          </table>
        </form>
        
        <div class="form-actions clearB " id="saveForm">
          <div class="padded-5">
          <?
            echo '<div class="btn-new btn-default" style="width:200px;" onclick="javascript:if(checkFormulier()){document.listForm.verwerk.value=1;document.listForm.submit();}"><img src=\'icon/16/refresh.png\' class=\'simbisIcon\' /> Alles opslaan.</div>';
            if ( checkOrderAcces('verwerkenBulk_verzenden') === true )
              echo '<div class="btn-new btn-default" id="divVerzendFix" style="width:200px;display:none;" onclick="javascript:if(checkFormulier()){document.listForm.verwerk.value=2;document.listForm.submit();}"><img src=\'icon/16/refresh.png\' class=\'simbisIcon\' /> Opslaan en verzenden.</div>
<div class="btn-new btn-default" style="width:210px;float:left;" onclick="javascript:selectFixCheckbox();"><img src="icon/16/navigate_right.png" class="simbisIcon" /> Fixorder(s) aanvinken</div>
   ';
            else
              echo '<div class="btn-new btn-default" id="divVerzendFix" style="width:280px;display:none;" ><img src=\'icon/16/refresh.png\' class=\'simbisIcon\' /> Onvoldoende rechten om zelf te verzenden.</div>';
          ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?
foreach($verwijderenUitSelectie as $orderId)
{
  if (($key = array_search($orderId, $_SESSION['bulkorder']['laatsteIds'])) !== false)
  {
    unset($_SESSION['bulkorder']['laatsteIds'][$key]);
  }
}

logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);

?>
<script>changeFix();
setTimeout('checkBegin();', 100);
</script>
<div style="display:none;" id="uiModalDiv">
  <div id="modelContent" ></div>
</div>        