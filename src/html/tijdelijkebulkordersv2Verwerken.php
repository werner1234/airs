<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_cls_FIXtransport.php");
include_once('orderControlleRekenClassV2.php');

$content['style'] = $editcontent['style'];

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

$orderLog=new orderLogs();
$db=new DB();
if($_POST['verwerk'] > 0)
{
  $nieuweAantallen=array();
  $validateIds=array();
  $aantalIds=array();
  foreach ($_POST as $key=>$value)
  {

    if(substr($key,0,6)=='check_')
    {
      $ids[]=substr($key,6);
    }
    if(substr($key,0,24)=='order_controle_checkbox_')
    {
      $validateIds[]=$value;
      $end=substr($key,24);
      $check=explode("_",$end);
      $validateIdKeys[$value][$check[0]]=1;
    }
  }
  $aantalIds=count($ids);

  $where="TijdelijkeBulkOrdersV2.id IN('".implode("','",$ids)."')";

  if($_POST['verwerk'] == 1)
  {
    $_SESSION['bulkorder']=array();
    $verwerk = new bulkOrderRegelsAanmakenV2();
    if (checkOrderAcces('verwerkenBulk_genereren') === true)
    {
      foreach ($ids as $id)
      {
        $verwerk->verzamel($id);
      }
      $teVerwerkenAantal=count($ids);
      if($aantalIds>0 && count($verwerk->orderData) > 0)
      {
        $orderIds=$verwerk->makeOrders();
        $verwerk->maakCombies($orderIds);
        $_SESSION['bulkorder']['laatsteIds']=$orderIds;
        $regelInfo = $verwerk->counter." van de ".$teVerwerkenAantal." regels verwerkt.";
        header("Location: bulkordersv2verwerken.php?message=".urlencode($regelInfo));
      }
      else
        $regelInfo = "Geen orderregels gevonden. <br>\n";
    }
    else
      $regelInfo = "Geen rechten om orders te verwerken. <br>\n";

  }

}

$query="SELECT Fondsen.Fonds FROM Fondsen
JOIN TijdelijkeBulkOrdersV2 ON Fondsen.Fonds=TijdelijkeBulkOrdersV2.Fonds
WHERE Fondsen.orderinlegInBedrag=1";
$db->SQL($query);
$db->Query();
$nominaalFonds=array();
while($data=$db->nextRecord())
  $nominaalFonds[]=$data['Fonds'];

$subHeader     = "";
$mainHeader    = " Verwerk geselecteerde fondsregels tot orders.";

$editScript = "TijdelijkeBulkOrdersV2Edit.php";
$allow_add  = false;

$list = new MysqlList2();
//$list->idField = "id";
$list->idTable="TijdelijkeBulkOrdersV2";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

if($_GET['setBulkFilter']==1)
{
  //$listWhere="AND TijdelijkeBulkOrdersV2.bron=''";
   unset($_SESSION['tijdelijkebulkordersv2VerwerkIdFilter']);
   $_POST['filter_0_veldnaam']='TijdelijkeBulkOrdersV2.bron';
   $_POST['filter_0_methode']='gelijk';
   $_POST['filter_0_waarde']='bulkInvoer';
   $_POST['filter_0_hidden'] = true;
   $list->hideFilter=true;
}
$listWhere="TijdelijkeBulkOrdersV2.depotbank NOT IN('NB','') ";
if($_POST['verwerk']==10)
{
 $_SESSION['tijdelijkebulkordersv2VerwerkIdFilter']=$ids;
}
if(isset($_SESSION['tijdelijkebulkordersv2VerwerkIdFilter']))
  $listWhere.= "AND TijdelijkeBulkOrdersV2.id IN('".implode("','",$_SESSION['tijdelijkebulkordersv2VerwerkIdFilter'])."')";

$list->addColumn("","sel",array("list_width"=>"30","search"=>false));
$checks=getActieveControles();
foreach($checks as $check=>$checkOmschrijving)
  $list->addFixedField("TijdelijkeBulkOrdersV2","validatie".ucfirst($check),array("list_width"=>"50","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","transactieSoort",array("list_width"=>"100","search"=>false,"list_align"=>'right'));
$list->addFixedField("TijdelijkeBulkOrdersV2","aantal",array("list_width"=>"100","search"=>false));
if(count($nominaalFonds)>0)
  $list->addFixedField("TijdelijkeBulkOrdersV2","bedrag",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","fonds",array("list_width"=>"200","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","ISINCode",array("list_width"=>"100","search"=>false));
//$list->addFixedField("TijdelijkeBulkOrdersV2","valuta",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","accountmanager",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","rekening",array("list_width"=>"130","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","add_user",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","bron",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","depotbank",array("list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","controleStatus",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","controleRegels",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","fixVerzenddatum",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$list->addFixedField("TijdelijkeBulkOrdersV2","validatieVast",array("list_invisible"=>'true',"list_width"=>"100","search"=>false));
$html = $list->getCustomFields(array('TijdelijkeBulkOrdersV2'),'TijdelijkeBulkOrdersV2List');

$list->setWhere("$listWhere");
$db->SQL('SELECT max(Vermogensbeheerders.OrderOrderdesk) as OrderOrderdesk, max(Gebruikers.orderdesk) as orderdeskMedewerker
FROM Vermogensbeheerders 
JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder  AND  VermogensbeheerdersPerGebruiker.Gebruiker = "'.$USR.'"  
JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker=Gebruikers.Gebruiker');
$gebruikersGegevens=$db->lookupRecord();
if($gebruikersGegevens['OrderOrderdesk']==1 && $gebruikersGegevens['orderdeskMedewerker']==0)
{
  if($_SESSION['usersession']['gebruiker']['Accountmanager']<>'')
    $filter="accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR (add_user='$USR' AND bron='bulkInvoer') AND $listWhere";
  else
    $filter="add_user='$USR' AND $listWhere";
  $list->setWhere($filter);
}

$editObject->formVars["BankDepotCodes"] ="''";
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

if($aantalIds>0 && $aantalIds > $list->records())
{
 $melding=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Niet alle records meegenomen. (".$list->records()." van de $aantalIds te gebruiken.)";
}
//$_SESSION['submenu']->addItem('Opnieuw valideren',"tijdelijkebulkordersv2List.php?checkOrders=1");
//$_SESSION['submenu']->addItem('Alle tijdelijkeorderegels',"tijdelijkebulkordersv2List.php");
//if($__debug)
//  $_SESSION['submenu']->addItem('Orders verzenden',"bulkordersv2verwerken.php");
//$_SESSION['submenu']->addItem("Naar XLS","bulkordersXLS.php?xls=1",array('target'=>'_self'));
//$_SESSION['submenu']->addItem("SNS XLS","bulkordersXLS.php?xls=2",array('target'=>'_self'));

$_SESSION['submenu']->addItem($html,"");


if($_POST['listGroup']!='')
  $groupSelectieStyle="style='display:none'";
//          <div class="btn-new btn-default " style="width:150px;float:left;" onclick="javascript:document.location=\'tijdelijkebulkordersv2Verwerken.php?checkOrders=1\';"><img src="icon/16/checks.png" class="simbisIcon" /> Alles opniew valideren</div>

if (checkOrderAcces('verwerkenBulk_genereren') === true)
{
  $ordersAanmakenDiv = '<div id="divOrdersAanmaken" class="btn-new btn-default " style="width:150px;float:left;display:none;" onclick="javascript:aanmakenOrders();"><img src="icon/16/add.png" class="simbisIcon" /> Orders aanmaken</div>';
}
else
{
  $ordersAanmakenDiv = '<div id="divOrdersAanmaken" class="btn-new btn-default " style="width:300px;float:left;display:none;">Onvoldoende rechten om orders aan te maken.</div>';
}

$orderValidatieOpslaanDiv = '';
if (checkOrderAcces('verwerkenBulk_valideren') === true)
{
  $orderValidatieOpslaanDiv  = '<div class="btn-new btn-default " style="width:150px;float:left;" onclick="javascript:document.listForm.verwerk.value=2;document.listForm.submit();"><img src="icon/16/refresh.png" class="simbisIcon" /> Validatie opslaan</div>';
  $checks=getActieveControles();
  if(isset($checks['akkam']))
    $orderValidatieOpslaanDiv .= '<div class="btn-new btn-default " style="width:150px;float:left;" onclick="javascript:document.listForm.verwerk.value=0;document.listForm.accountmanagerCheck.value=1;document.listForm.submit();document.listForm.accountmanagerCheck.value=0;"><img src="icon/16/refresh.png" class="simbisIcon" /> Akk.Am. Check </div>';
}

$content['javascript'].="
var submitted=false;
function aanmakenOrders()
{
  showLoading('Verwerken');
  if(submitted==false)
  {
    submitted=true;
    document.listForm.verwerk.value=1;
    $('#divOrdersAanmaken').html('Bezig met verwerken');
    document.listForm.submit();
  }
}
";

$content['pageHeader'] = '
  <br><div class="edit_actionTxt"><strong>'.$mainHeader.'</strong> '.$subHeader.'</div>

  <div class="main_content">
    <div class="row" >
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          '.$ordersAanmakenDiv.'
          '.$orderValidatieOpslaanDiv.'
          <div class="btn-new btn-default " style="width:150px;float:left;" onclick="javascript:document.location=\'tijdelijkebulkordersv2List.php\';"><img src="icon/16/navigate_left.png" class="simbisIcon" /> Terug</div>
        </div>
      </div>
    </div>
    <br />
    <div class="row" id="groupSelectie" '.$groupSelectieStyle.' >
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src="icon/16/checks.png" class="simbisIcon" /> Alles selecteren</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src="icon/16/undo.png" class="simbisIcon" /> Niets selecteren</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src="icon/16/replace2.png" class="simbisIcon" /> Selectie omkeren</div>
        </div>
      </div>
    </div>
    <br />
    <div class="row" >
      <div class="box box12" >
        <div class="btn-group" role="group" style="height:22px;">
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'\';document.listForm.submit();">Group geen</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'fonds\';document.listForm.submit();">Group fonds</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'portefeuille\';document.listForm.submit();">Group portefeuille</div>
          <div class="btn-new btn-default" style="width:150px;float:left;" onclick="document.listForm.verwerk.value=0;document.listForm.listGroup.value=\'accountmanager\';document.listForm.submit();">Group accountmanager </div>
        </div>
      </div>
  </div>
';

$AETemplate = new AE_template();
$content['script_voet'] .= $AETemplate->parseFile('orders/js/ordersEdit.js');
//$content['script_voet'] .= $AETemplate->parseFile('orders/js/orderEditBulkTemplate.js');


$validatieCheckJavascript="function validatieCheck(orderId,status)
{";
foreach($checks as $check=>$checkOmschrijving)
{
 $validatieCheckJavascript.="
 if($('#'+'order_controle_checkbox_".$check."_'+orderId).val())
 {
   $('#'+'order_controle_checkbox_".$check."_'+orderId).prop(\"checked\", status);
 }";
}
$validatieCheckJavascript.="\n}";

/*

 */

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
function checkIds(ids,state)
{
  for(z=0; z<ids.length;z++)
  {
   var fieldname='check_'+ids[z];
   $('[name='+fieldname+']').prop('checked', state);
  }
  controleerVinkjes();
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
  controleerVinkjes();
}

function controleerVinkjes()
{
  var theForm = document.listForm.elements, z = 0, toonOrdersMaken=0 ;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(theForm[z].checked==true)
      {
        toonOrdersMaken=1;
        break;
      }
   }
  }
  if(toonOrdersMaken==1)
  {
    $('#divOrdersAanmaken').show();
  }
  else
  {
     $('#divOrdersAanmaken').hide();
  }

}

function showLoading(text) {
  // add the overlay with loading image to the page
  $('#overlay').remove();
  var over = '<div id=\"overlay\"><div id=\"loading-box\">' +
          '<div id=\"loading-txt\">' + text + '</div>' +
          '<img id=\"loading-img\" src=\"images/ajax-loader.gif\">' +
          '</div></div>';
  $(over).appendTo('body');
}

function removeLoading() {
  $('#overlay').remove();
}



$validatieCheckJavascript
";
echo template($__appvar["templateContentHeader"],$content);
if(isset($regelInfo))
{
  echo $regelInfo;
  echo "<a href='bulkordersv2verwerken.php'><b>Aangemaakte bulkorders controleren.</b></a>";

}
$disableEdit=true;

?>
<div class="main_content">

  <div class="row">
    <div class="formHolder box box12">
      <div class="formTitle textB">Filters  <div class="toggleFormHolder formHolderHide"></div></div>
      <div class="formContent padded-10">
        <?=$list->filterHeader();?>
      </div>
    </div>
  </div>
  
  
  

 <div class="row">
    <div class="formHolder box box12 {fieldsetClass}">
      <div class="formTitle textB">Bulkorders <?=$melding?></div>
      <div class="formContent">

        <form name="listForm" method="POST">
          <input type="hidden" name="verwerk" value="1">
          <input type="hidden" name="accountmanagerCheck" value="0">

            <input type="hidden" id="listGroup" name="listGroup" value="<?=$_POST['listGroup']?>">
            <input type="hidden" id="openGroup" name="openGroup" value="<?=$_POST['openGroup']?>">
<?php
            if($_POST['listGroup']!='')
            {

              $listHeaderOrg=$list->printHeader($disableEdit);
              //echo $listHeaderOrg;
              $re = "/<colgroup>.*<\/colgroup>/i";
              preg_match($re, $listHeaderOrg, $matches);
              $listHeader=$matches[0].'<tr class="list_kopregel">';
              foreach($list->columns as $colData)
              {
                $column = array_merge($list->objects[$colData['objectname']]->data['fields'][$colData['name']],$colData['options']);
                //listarray($column);
                if(strpos($listHeaderOrg,$colData['name'])!==false)
                  $listHeader.='<td class="list_kopregel_data">'.$column['description'].'</td>';
              }
              $listHeader.= '</tr>';

             // listarray($list->columns);exit;
            }
            else
            {
              echo "<table>";
              echo $list->printHeader($disableEdit);
            }
          //  listarray($listHeader);exit;
$db=new DB();
$query="SELECT Fondsen.Fonds FROM Fondsen WHERE Fondsen.orderinlegInBedrag=1";
$db->SQL($query);
$db->Query();
$nominaalFonds=array();
while($data=$db->nextRecord())
  $nominaalFonds[]=$data['Fonds'];

global $__ORDERvar;
$loadingActief=false;
$okeIds=array();
$gelePortefeuilleChecks=array();
$rodePortefeuilleChecks=array();
$rodeRegelIds=array();
$rodePortefeuilles=array();
 while($data = $list->getRow())
 {
  foreach($data as $key=>$value)
    $data[$key]['noClick']=$disableEdit;
  if($data['TijdelijkeBulkOrdersV2.controleRegels']['value'] <> '')
    $regelOk=true;
  else
    $regelOk=false;
  $export['controleRegels']=unserialize($data['TijdelijkeBulkOrdersV2.controleRegels']['value']);

  $relavidateId=in_array($data['id']['value'],$validateIds);
  $relavidateAantal=in_array($data['id']['value'],$aantalIds);

  if((checkOrderAcces('orderGeenHervalidatie') == true && $data['TijdelijkeBulkOrdersV2.validatieVast']['value']==0) || $data['TijdelijkeBulkOrdersV2.controleRegels']['value'] == '' )
    $nogValideren=true;
  elseif(checkOrderAcces('orderGeenHervalidatie') == false && $_POST['verwerk'] == 10)
    $nogValideren=true;
  else
    $nogValideren=false;

//echo $_GET['checkOrders'] ." || $relavidateId || $relavidateAantal || $nogValideren - ".$data['TijdelijkeBulkOrdersV2.controleStatus']['value']."-".$data['TijdelijkeBulkOrdersV2.validatieVast']['value']."<br>\n ";
    if ($_GET['checkOrders'] == 1 || $relavidateId || $relavidateAantal || $nogValideren || ($_POST['accountmanagerCheck']==1 && checkOrderAcces('orderGeenHervalidatie') == false) )// || $_POST['verwerk'] == 10 //
    {

      if($extraLog)
        logIt("id:".$data['id']['value']."|portefeuille:".$data['TijdelijkeBulkOrdersV2.portefeuille']['value']."|revalId: $relavidateId |revalAant: $relavidateAantal | nogVal: $nogValideren | verwerk:".$_POST['verwerk']." | accountmanagerCheck:".$_POST['accountmanagerCheck']);

      if ($loadingActief == false)
      {
        echo "<script>showLoading('Valideren');</script>";
        flush();
        ob_flush();
        $loadingActief = true;
      }
      if ($data['id']['value'] != "" && $data['TijdelijkeBulkOrdersV2.portefeuille']['value'] != "")
      {
        if(is_array($_SESSION['tijdelijkebulkordersv2VerwerkIdFilter']) && count($_SESSION['tijdelijkebulkordersv2VerwerkIdFilter'])>0)
          $filter="TijdelijkeBulkOrdersV2.id IN('" . implode("','", $_SESSION['tijdelijkebulkordersv2VerwerkIdFilter']) . "')";
        else
          $filter="1";

        $ordercheck = new orderControlleBerekeningV2(true, "AND ( $filter OR validatieVast=1 )");

        if ($_POST['verwerk'] == 10 && checkOrderAcces('verwerkenBulk_valideren'))
        {
          $resetCkecks = 2;
        }
        elseif($nogValideren==true)
        {
          $resetCkecks = 1;
        }
        else
        {
          $resetCkecks = false;
        }

        $check = $ordercheck->updateChecksByBulkorderregelId($data['id']['value'], $validateIdKeys[$data['id']['value']], $resetCkecks,$_POST['accountmanagerCheck']);

        $newCheck = $check['controleRegels'];

        if ($resetCkecks == false )
        {
          foreach ($newCheck as $checkNaam => $checkData)
          {
            if ($export['controleRegels'][$checkNaam]['checked'] == 1)
            {
              $newCheck[$checkNaam]['checked'] = 1;
            }
          }
        }
        $export['controleRegels'] = $newCheck;
      }
    }
   else
   {
     if ($loadingActief == false)
     {
       echo "<script>showLoading('Laden');</script>";
       flush();
       ob_flush();
       $loadingActief = true;
     }
   }

 // listarray($export['controleRegels']);


  foreach($checks as $check=>$checkOmschrijving)
  {
    $geel=false;
    $checkVeld='TijdelijkeBulkOrdersV2.validatie'.ucfirst($check);
    if(is_array($export['controleRegels'][$check]))
    {
    $checkData=$export['controleRegels'][$check];
    $data[$checkVeld]['list_nobreak']=true;
    $data[$checkVeld]['value']='';
    $title='';
    $title=str_replace('<br>','',$checkData['resultaat']);

    $data[$checkVeld]['value'].= "<label title='".$title."'>";//substr($check,0,1)
    if($checkData['short'] > 0 || $checkData['checked']==1)
    {
      if($checkData['short']==1 && $check=='liqu')
      {
        $data[$checkVeld]['td_style'] = 'style="background-color:#FFA500;text-align:center" ';
        $gelePortefeuilleChecks[$data['TijdelijkeBulkOrdersV2.portefeuille']['value']][$data['id']['value']]="order_controle_checkbox_".$check."_".$data['id']['value'];
        $geel=true;
      }
      elseif($checkData['short']>=1)
      {
        $data[$checkVeld]['td_style'] = 'style="background-color:#FAA39A;text-align:center" ';
        if($check<>'akkam')
        {
        //  $rodePortefeuilles[$data['TijdelijkeBulkOrdersV2.portefeuille']['value']] = $data['TijdelijkeBulkOrdersV2.portefeuille']['value'];
          $rodeRegelIds[]=$data['id']['value'];
        }
        $rodePortefeuilleChecks[$data['TijdelijkeBulkOrdersV2.portefeuille']['value']][$check]="order_controle_checkbox_".$check."_".$data['id']['value'];
      }

      if($checkData['checked']==1)
      {
        $checkboxChecked='checked disabled';
        $data[$checkVeld]['td_style']='style="background-color:#66CC66;text-align:center" ';
      }
      else
      {
       $checkboxChecked='';
       $regelOk=false;
      }
      if($geel==true)
        $data[$checkVeld]['value'].="<input $checkboxChecked onclick=\"validatieCheck('".$data['id']['value']."',this.checked);validatieGeelCheck('".$data['TijdelijkeBulkOrdersV2.portefeuille']['value']."',this.checked);\" type=\"checkbox\" name=\"order_controle_checkbox_".$check."_".$data['id']['value']."\" id=\"order_controle_checkbox_".$check."_".$data['id']['value']."\" value=\"".$data['id']['value']."\">";
      else
        $data[$checkVeld]['value'].="<input $checkboxChecked onclick=\"validatieCheck('".$data['id']['value']."',this.checked);\" type=\"checkbox\" name=\"order_controle_checkbox_".$check."_".$data['id']['value']."\" id=\"order_controle_checkbox_".$check."_".$data['id']['value']."\" value=\"".$data['id']['value']."\">";

     // $data['.'.$check]['td_style']='style="background-color:#66CC66"';
     // $data['order_controle_checkbox_' . $key];
    }
    else
    {
      if($title<>'')
      {
        $data[$checkVeld]['value'].="Ok";
        $data[$checkVeld]['td_style']='style="background-color:#66CC66;text-align:center" ';
      }
      else
      {
        $data[$checkVeld]['value'].="na";
        $data[$checkVeld]['td_style']='style="background-color:#FFCC66;text-align:center"" ';
      }
    }
    $data[$checkVeld]['value'].="</label> ";
    }
    else
    {
      $data[$checkVeld]['value']="na";
    }
  }

  if($regelOk==true)
  { //controleRegels
   //$data["tr_class"] = "list_dataregel_groen";
   $data['.sel']['value']="<input onclick=\"javascript:controleerVinkjes()\" type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\">";
   $data['.sel']['list_nobreak']=true;
    $okeIds[]=$data['id']['value'];

   }

   if(in_array($data['TijdelijkeBulkOrdersV2.fonds']['value'],$nominaalFonds))
   {
     $data['TijdelijkeBulkOrdersV2.fonds']['list_nobreak']=true;
     $data['TijdelijkeBulkOrdersV2.fonds']['value']="<span style='background-color:#FFA500' title='Dit betreft een fonds waarvan de order wellicht in bedrag ingelegd dient te worden.'> ".$data['TijdelijkeBulkOrdersV2.fonds']['value']."</span>";
   }

  $data['disableEdit']=$disableEdit;
              if($_POST['listGroup']!='')
              {
                if($_POST['listGroup']=='gereed')
                  $groupOn='controleStatus';
                else
                  $groupOn=$_POST['listGroup'];
                 $groupRegels[$data['TijdelijkeBulkOrdersV2.'.$groupOn]['value']][$data['id']['value']]=$list->buildRow($data);


              }
              else
              {
                echo $list->buildRow($data);
              }
  }
            $n=1;
            $gereedVertaling=array(''=>'ongevalideerd',0=>'gereed',1=>'issue',2=>'error');
            foreach($groupRegels as $group=>$regels)
            {
               if($_POST['listGroup']=='gereed')
                  $group=$gereedVertaling[$group];
                  
               $checkOn="javascript:checkIds(['".implode("','",array_keys($regels))."'],true);";
               $checkOff="javascript:checkIds(['".implode("','",array_keys($regels))."'],false);";

              if($n==$_POST['openGroup'])
                $style='';
              else
                $style='display:none';

              echo '<div onclick="javascript:$(\'#regels_'.$n.'\').toggle(); if($(\'#regels_'.$n.'\').attr(\'style\') == \'display: none;\'){$(\'#openGroup\').val(\'0\');} else {$(\'#openGroup\').val(\''.$n.'\');}" style="cursor: pointer;">
              '.count($regels).' orderregels onder '.$group.'. </div>';

              echo '<div id="regels_'.$n.'" style="'.$style.'"> <a href="'.$checkOn.'"><b>( checks on </b></a><b>/</b><a href="'.$checkOff.'"><b> checks off)</b></a> ';
              echo '<table>';
              //echo '<tr><td colspan=10>'.$portefeuille.'</td></tr>';
              echo $listHeader;

              foreach($regels as $index=>$regelData)
              {

                echo $regelData;

              }
              echo '</table>';
              echo '</div>';
              $n++;
            }
            if($_POST['listGroup']=='')
             echo '</table>';
          //  listarray($groupRegels);
            ?>

        </form>
      </div>
    </div>
   </div>
</div>
<script>

function validatieGeelCheck(portefeuille,status)
{
<?
foreach($rodePortefeuilles as $portefeuille)
{
  if(isset($gelePortefeuilleChecks[$portefeuille]))
  {
 //   unset($gelePortefeuilleChecks[$portefeuille]);
  }
}
foreach($gelePortefeuilleChecks as $portefeuille=>$checks)
{
  echo "if(portefeuille=='$portefeuille'){";
  foreach($checks as $id=>$veld)
  {
    if(!in_array($id,$rodeRegelIds))
    {
      echo "$('#'+'$veld').prop(\"checked\",status);\n";
      if (isset($rodePortefeuilleChecks[$portefeuille]['akkam']))
      {
        echo "$('#'+'" . str_replace('liqu', 'akkam', $veld) . "').prop(\"checked\",status);\n";
      }
    }
  }
  echo "}\n";
}
?>
}

</script>
<?

if($loadingActief==true)
{
  echo "<script>removeLoading();</script>";
}
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);


$query="UPDATE TijdelijkeBulkOrdersV2 SET validatieVast=1 WHERE id IN('".implode("','",$okeIds)."')";
$db->SQL($query);
$db->Query();



class bulkOrderRegelsAanmakenV2
{
  function bulkOrderRegelsAanmakenV2()
  {
    global $USR;
    $this->USR=$USR;
    $this->db = new DB();
    $this->db2 = new DB();
    $this->counter=0;
    $this->log = array();
    $this->orderData = array();
    $this->portefeuilles = array();
    $this->fondsen = array();

    $query="SELECT Vermogensbeheerders.OrderuitvoerBewaarder as OrderuitvoerBewaarder FROM
    Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' limit 1";
    $this->db ->SQL($query);
    $this->bewaarder=$this->db ->lookupRecord();
  }

  function verwijderId($id)
  {
     $query="DELETE FROM TijdelijkeBulkOrdersV2 WHERE id='$id'";//add_user='".$this->USR."' AND
     $this->db->SQL($query);
     $this->db->Query();
     $query="DELETE FROM orderLogs WHERE orderLogs.bulkorderRecordId='$id' AND orderRecordId=0";
     $this->db->SQL($query);
     $this->db->Query();
     $this->counter++;
  }


  function verzamel($id)
  {
    $query="SELECT * FROM TijdelijkeBulkOrdersV2 WHERE id='$id'"; //add_user='".$this->USR."' AND
    $this->db->SQL($query);
    $orderData=$this->db->lookupRecord();
  
    $query = "SELECT consolidatie,vermogensbeheerder FROM Portefeuilles WHERE portefeuille='" . $orderData['portefeuille'] . "'";
    $this->db->SQL($query);
    $consolidatie = $this->db->lookupRecord();
    $vermogensbheerder=$consolidatie['vermogensbeheerder'];
    if($consolidatie['consolidatie']>0)
    {
      $query="SELECT portefeuille FROM Rekeningen WHERE consolidatie=0 AND Rekening='" . $orderData['rekening'] . "'";
      $this->db->SQL($query);
      $echtePortefeuille = $this->db->lookupRecord();
      $orderData['portefeuille']=$echtePortefeuille['portefeuille'];
    }
    

    if($orderData['depotbank']=='')
    {
      if ($this->bewaarder['OrderuitvoerBewaarder'] == 0)
      {
        $query = "SELECT depotbank FROM Portefeuilles WHERE portefeuille='" . $orderData['portefeuille'] . "'";
        $this->db->SQL($query);
        $portData = $this->db->lookupRecord();
        $orderData['depotbank']=$portData['depotbank'];
      }
    }
    
    if($orderData['beurs']=='')
    {
      $query = "SELECT beurs FROM Fondsen WHERE fonds='" . mysql_real_escape_string($orderData['fonds']) . "'";
      $this->db->SQL($query);
      $fondsData = $this->db->lookupRecord();
      $orderData['beurs']=$fondsData['beurs'];
    }
    $depotbank=$orderData['depotbank'];
    if($depotbank=='UBS' || $depotbank=='UBSL')
    {
      $query="SELECT id FROM fixDepotbankenPerVermogensbeheerder WHERE vermogensbeheerder='$vermogensbheerder' AND depotbank='$depotbank'";
      if($this->db->QRecords($query) > 0)
      {
        $depotbank = $depotbank . '_' . $id;
      }
    }
    $this->portefeuilles[$orderData['portefeuille']] = $orderData['portefeuille'];
    $this->fondsen[$orderData['fonds']]=$orderData['fonds'];
    $limietKoers=$orderData['koersLimiet'];
    if($orderData['bedrag']<>0)
    {
      $orderType='B';
    }
    else
    {
      $orderType='A';
    }
    $this->orderData[$orderData['fonds'].'_'.$orderData['fondsOmschrijving'].'_'.$orderData['ISINCode'].'_'.$orderData['fondsValuta']][$depotbank][$orderData['transactieSoort']][$limietKoers][$orderType][]=$orderData;
  }

  function makeOrders()
  {
    $orderIds=array();
    foreach ($this->orderData as $fonds=>$depotbanken)
    {
      foreach ($depotbanken as $depotbank=>$transactieSoorten)
      {
        foreach ($transactieSoorten as $transactieSoort=>$limietData)
        {
           foreach ($limietData as $limietkoers=>$aantalBedrag)
           {
             foreach ($aantalBedrag as $type=>$orderData)
             {
               $orderIds[] = $this->makeOrder($orderData);
             }
           }
        }
      }
    }
    return $orderIds;
  }

  function maakCombies($orderIds)
  {
    $query="SELECT OrdersV2.id,OrdersV2.BatchId,OrderRegelsV2.portefeuille,OrdersV2.OrderSoort FROM OrderRegelsV2 JOIN OrdersV2 ON OrderRegelsV2.orderid=OrdersV2.id 
            WHERE OrdersV2.OrderSoort='E' AND OrdersV2.id IN('".implode("','",$orderIds)."') ORDER BY OrderRegelsV2.portefeuille";
    $this->db->SQL($query);
    $this->db->Query();
    $ordersPerPortefeuille=array();
    while($data=$this->db->nextRecord())
    {
      $ordersPerPortefeuille[$data['portefeuille']][$data['id']]=$data;
    }

    foreach($ordersPerPortefeuille as $portefeuille=>$orderIdData)
    {
      if(count($orderIdData)>1) //combi aanmaken.
      {
         $batchId='';
         $ids=array();
         foreach($orderIdData as $orderId=>$orderData)
         {
           if($batchId=='')
             $batchId=$orderData['BatchId'];
           $ids[]=$orderId;

         }
         $query="UPDATE OrdersV2 SET BatchId='$batchId',OrderSoort='C' WHERE OrdersV2.id IN('".implode("','",$ids)."') AND OrdersV2.id IN('".implode("','",$orderIds)."')";
         $this->db->SQL($query);
         $this->db->Query();
      }
    }

  }


  function makeOrder($data)
  {
    global  $__appvar;
  
    if(isset($__appvar['extraOrderLogging']))
      $extraLog=$__appvar['extraOrderLogging'];
    else
      $extraLog=false;

  $orderLog=new orderLogs();
  if($data[0]['fonds'] <> '')
  {
    $query = "SELECT * FROM Fondsen WHERE Fonds='" . $data[0]['fonds'] . "'";
    $this->db->SQL($query);
    $fonds = $this->db->lookupRecord();
  }
  else
    $fonds=array('ISINCode'=>$data[0]['ISINCode'],'Fondseenheid'=>$data[0]['fondseenheid'],'Valuta'=>$data[0]['fondsValuta']);
  $symbool=$data[0]["optieSymbool"];
  $fix=new AE_FIXtransport();
  $optieCodes=$fix->getOptiecode($data[0]["depotbank"],$data[0]['fonds'],$data[0]["transactieSoort"]);
  $fondsBankcode=$fix->getFondscode($data[0]["depotbank"],$data[0]['fonds']);
  if(is_array($optieCodes))
  {
    if($symbool<>'')
      $symbool=$optieCodes['leg']['symbol'];
    $fondsBankcode=$optieCodes['bankCode'];
  }

  $query="SELECT Vermogensbeheerders.OrderStandaardTijdsSoort,Vermogensbeheerders.OrderStandaardTransactieType,Vermogensbeheerders.Vermogensbeheerder,
fixDepotbankenPerVermogensbeheerder.careOrderVerplicht,fixDepotbankenPerVermogensbeheerder.fixDefaultAan,fixDepotbankenPerVermogensbeheerder.meervoudigViaFix
  FROM Vermogensbeheerders
  JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder
  LEFT JOIN fixDepotbankenPerVermogensbeheerder ON Portefeuilles.Vermogensbeheerder = fixDepotbankenPerVermogensbeheerder.vermogensbeheerder AND Portefeuilles.Depotbank = fixDepotbankenPerVermogensbeheerder.depotbank
  WHERE Portefeuilles.Portefeuille='".$data[0]["portefeuille"]."'  limit 1";//WHERE vermogensBeheerder = '".$__appvar['bedrijf']."'
  $this->db->SQL($query);
  $vermogensbeheerder=$this->db->lookupRecord();

  if($vermogensbeheerder['OrderStandaardTijdsSoort']=='GTC')
  {
    $tijdsSoort='GTC';
    $jaarLater=mktime(0,0,0,date('m'),date('d'),date('Y')+1);
    $dagVanWeek=date('w',$jaarLater);
    if($dagVanWeek==0 || $dagVanWeek==6)
      $jaarLater=$jaarLater-3*86400;
    $tijdsLimiet="'".date('Y-m-d',$jaarLater-86400)."'";
  }
  else
  {
    $tijdsSoort='DAT';
    $tijdsLimiet='NOW()';
  }

  if($vermogensbeheerder['OrderStandaardTransactieType'] <> '')
    $transactieType=$vermogensbeheerder['OrderStandaardTransactieType'];
  else
    $transactieType='L';

  if($data[0]["koersLimiet"]<>0)
     $transactieType='L';

  if($data[0]["beurs"]=='')
  {
     $vermogensbeheerder["fixDefaultAan"]='0';
     $vermogensbeheerder["careOrderVerplicht"]='0';
  }

  $query  = "INSERT INTO OrdersV2 SET ISINCode            = '".mysql_real_escape_string($fonds["ISINCode"])."' ";
  $query .= ", fondseenheid       = '".mysql_real_escape_string($fonds["Fondseenheid"])."' ";
  $query .= ", fondsValuta        = '".mysql_real_escape_string($fonds["Valuta"])."' ";
  $query .= ", optieSymbool       = '".mysql_real_escape_string($symbool)."' ";
  $query .= ", fondsBankcode      = '".mysql_real_escape_string($fondsBankcode)."' ";
  $query .= ", optieType          = '".mysql_real_escape_string($data[0]["optieType"])."' ";
  $query .= ", optieUitoefenprijs = '".mysql_real_escape_string($data[0]["optieUitoefenprijs"])."' ";
  $query .= ", optieExpDatum      = '".mysql_real_escape_string($data[0]["optieExpDatum"])."' ";
  $query .= ", fonds              = '".mysql_real_escape_string($data[0]["fonds"])."' ";
  $query .= ", fondsOmschrijving  = '".mysql_real_escape_string($data[0]["fondsOmschrijving"])."' ";
  $query .= ", transactieSoort    = '".mysql_real_escape_string($data[0]["transactieSoort"])."' ";
  $query .= ", fondssoort         = '".mysql_real_escape_string($data[0]["fondssoort"])."' ";
  $query .= ", fixOrder           = '".mysql_real_escape_string($vermogensbeheerder["fixDefaultAan"])."' ";
  $query .= ", careOrder          = '".mysql_real_escape_string($vermogensbeheerder["careOrderVerplicht"])."' ";
  $query .= ", koersLimiet        = '".mysql_real_escape_string($data[0]["koersLimiet"])."'";
  $query .= ", transactieType     = '$transactieType' ";
  $query .= ", tijdsLimiet        =  $tijdsLimiet";
  $query .= ", tijdsSoort         = '$tijdsSoort' ";
  $query .= ", orderStatus        = 0";
  $query .= ", depotbank          = '".mysql_real_escape_string($data[0]["depotbank"])."' ";
  $query .= ", beurs              = '".mysql_real_escape_string($data[0]["beurs"])."' ";
  $query .= ", add_user           = '".$data[0]["add_user"]."' ";
  $query .= ", add_date           = NOW() ";
  $query .= ", change_user        = '".$data[0]["change_user"]."' ";
  $query .= ", change_date        = NOW() ";
  
  $this->db->SQL($query);
  $this->db->Query();
  $orderIdent = $this->db->last_id();
  $aantalTotaal=0;
  $bedrag=0;
  $x=1;
  foreach ($data as $orderRegel)
  {
    $bedrag+=$orderRegel["bedrag"];
    $ordQ  = "INSERT INTO OrderRegelsV2 SET   orderid      = '".$orderIdent."' ";
    $ordQ .= ", positie      = '".$x."' ";
    $ordQ .= ", portefeuille = '".mysql_real_escape_string($orderRegel["portefeuille"])."'";
    $ordQ .= ", rekening     = '".mysql_real_escape_string($orderRegel["rekening"])."'";
    $ordQ .= ", controleRegels = '".mysql_real_escape_string($orderRegel["controleRegels"])."'";
    $ordQ .= ", controleStatus  = '".mysql_real_escape_string($orderRegel["controleStatus"])."'";
    $ordQ .= ", aantal       = '".$orderRegel["aantal"]."'";
    $ordQ .= ", bedrag       = '".$orderRegel["bedrag"]."'";
    $ordQ .= ", client       = '".mysql_real_escape_string($orderRegel["client"])."'";
    $ordQ .= ", orderregelstatus  = '".mysql_real_escape_string($orderRegel["orderregelstatus"])."'";
    $ordQ .= ", externeBatchId = '".mysql_real_escape_string($orderRegel["externeBatchId"])."'";
    $ordQ .= ", orderbedrag = '".round($orderRegel["orderbedrag"],2)."'";
    $ordQ .= ", add_user     = '".$orderRegel["add_user"]."' ";
    $ordQ .= ", add_date     = NOW() ";
    $ordQ .= ", change_user     = '".$orderRegel["change_user"]."' ";
    $ordQ .= ", change_date     = NOW() ";

    $this->db->SQL($ordQ);
    if($this->db->Query())
    {
      if ($orderRegel["id"] <> 0)
      {
        $query = "UPDATE orderLogs SET orderRecordId='" . $orderIdent . "' WHERE bulkorderRecordId='" . $orderRegel["id"] . "'";
        $this->db->SQL($query);
        $this->db->Query();
        $this->verwijderId($orderRegel['id']);
      }
      $x++;
    }
  }
  if(count($data) > 1)
  {
    if($bedrag>0)
    {
      $orderSoort = 'O';
    }
    else
    {
      $orderSoort = 'M';
    }
  }
  else
  {
    if($bedrag>0)
    {
      $orderSoort = 'N';
    }
    elseif(count($this->portefeuilles)==1 && count($this->fondsen) > 1)
    {
      $orderSoort = 'C';
    }
    else
    {
      $orderSoort = 'E';
    }
  }

  if($this->lastPortefeuille==$orderRegel["portefeuille"] && $orderSoort=='C')
  {
    $newBatchId=$this->lastBatchId;
  }
  else
  {
    $cfg=new AE_config();
    $newBatchId=$cfg->getData('lastOrderBatchId')+1;
    $cfg->addItem('lastOrderBatchId',$newBatchId);
  }

  if($extraLog)
     logIt("$orderSoort Order $orderIdent aangemaakt P:".implode(',',$this->portefeuilles)."F:".implode(',',$this->fondsen));

  $fixOrderSet='';
  if($orderSoort=='M')
  {
    if($vermogensbeheerder['fixDefaultAan']==1 && $vermogensbeheerder['meervoudigViaFix']==1)
      $fixOrderSet=",fixOrder=1";
    else
      $fixOrderSet=",fixOrder=0";
  }
  $query  = "UPDATE OrdersV2 SET OrderSoort='$orderSoort', BatchId='$newBatchId' $fixOrderSet  ";
  //$query .= ", koersLimiet = '".$orderRegel["koers"]."'";
  $query .= " WHERE id = '".$orderIdent."'";
  $this->db->SQL($query);
  $this->db->Query();
  $orderLog->addToLog($orderIdent,'',"Order aangemaakt uit bulkorders. (".$vermogensbeheerder['Vermogensbeheerder'].")");
  $this->lastPortefeuille=$orderRegel["portefeuille"];
  $this->lastBatchId=$newBatchId;

  return $orderIdent;
  }

}
?>
