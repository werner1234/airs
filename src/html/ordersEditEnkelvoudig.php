<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 	Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 	File Versie					: $Revision: 1.2 $

 	$Log: ordersEditEnkelvoudig.php,v $
 	Revision 1.2  2018/08/18 12:40:14  rvv
 	php 5.6 & consolidatie
 	
 	Revision 1.1  2014/02/02 10:48:31  rvv
 	*** empty log message ***
 	
 
*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_dateFrom.php");
include_once("./orderControlleRekenClass.php");
include_once("./rapport/rapportRekenClass.php");
include_once("./rapport/Zorgplichtcontrole.php");

session_start();
$__funcvar['listurl']  = "ordersList.php";
$__funcvar['location'] = "ordersEditEnkelvoudig.php";

$_GET = array_merge($_POST,$_GET);
$currentBatch=$_GET['batchId'];

$object = new Orders();
$object2 = new OrderRegels();

$query="SELECT Vermogensbeheerders.Vermogensbeheerder,Vermogensbeheerders.OrderStandaardType, Vermogensbeheerders.OrderStandaardMemo , 
  Vermogensbeheerders.OrderStandaardTijdsSoort, Vermogensbeheerders.OrderStatusKeuze, Vermogensbeheerders.OrderOrderdesk
   FROM Vermogensbeheerders
  Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
$db=new DB();
$db->SQL($query);
$standaard=$db->lookupRecord();
//  $object->setOption('tijdsSoort','default_value',$standaard['OrderStandaardTijdsSoort']);//DAT

$_GET['orderSelectieType']='enkelvoudig';
$object->setOption("transactieType","default_value","B");
$object->setOption("tijdsSoort","default_value","GTC");
$object->setDefaults();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;

$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

$koppelObject = array();
$koppelObject[0] = new Koppel("Fondsen","editForm");
$koppelObject[0]->addFields("ISINCode","fondsCode",true,true);
$koppelObject[0]->addFields("Fonds","fonds",false,true);
$koppelObject[0]->addFields("Omschrijving","fondsOmschrijving",true,true);
$koppelObject[0]->action = "fondsSelected();";
$koppelObject[0]->name = "fonds";
$koppelObject[0]->focus = "portefeuille";
$koppelObject[0]->extraQuery = " AND (EindDatum > NOW() OR EindDatum = '0000-00-00') ORDER BY Fondsen.OptieBovenliggendFonds";

$editcontent['javascript']=str_replace('document.editForm.submit();','if(checkPage())document.editForm.submit();',$editcontent['javascript']);
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/ordersEdit.js\" type=text/javascript></script>\n";
$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();

if($_GET['action']=='new' && $_GET['batchId'] !='')
  $editcontent['eigenFocus']="if(document.getElementById('fondsCode')){try{document.getElementById('fondsCode').focus(); break;} catch(err) { }}";

if(!isset($_GET['batchId']))
{
  $cfg=new AE_config();
  $newBatchId=$cfg->getData('lastOrderBatchId')+1;
  $cfg->addItem('lastOrderBatchId',$newBatchId);
  $_GET['batchId']=$newBatchId;
}

  $query="SELECT batchId FROM Orders WHERE id = '".$_GET['id']."' AND batchId > 0 ORDER BY id";
  $db->SQL($query);
  $batchId=$db->lookupRecord();
  $query="SELECT id FROM Orders WHERE batchId = '".$batchId['batchId']."' AND batchId > 0 ORDER BY id";
  if($db->QRecords($query) > 1)
  {
    if($_GET['orderSelectieType'] == '')
      $_GET['orderSelectieType']='combinatie';
    $noInsert=true;
  }
  $query="SELECT OrderRegels.* FROM Orders JOIN OrderRegels ON OrderRegels.orderid = Orders.orderid WHERE Orders.id = '".$_GET['id']."'";
  if($db->QRecords($query) == 1)
  {
    if($_GET['orderSelectieType'] == '')
      $_GET['orderSelectieType']='enkelvoudig';
    $regel=$db->nextRecord();
    foreach ($regel as $key=>$value)
    {
      if($key=='id')
        $orderregelId=$value;
      if($_GET[$key] == '' && !in_array($key,array('add_date','add_user','change_date','change_user','id')))
        $_GET[$key]=$value;
    }
    $noInsert=true;
  }
  
   $db=new DB();
  if($_GET['portefeuille'] <> '')
  {
    $query="SELECT Risicoklasse,Depotbank FROM Portefeuilles WHERE portefeuille='".$_GET['portefeuille']."'";
    $db->SQL($query);
    $db->Query();
    $portefeuilleData=$db->nextRecord();
  }

  $editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/orderRegelEdit.js\" type=text/javascript>";
  $editObject->formVars['Depotbank'] = '<input class="" type="hidden" value="'.$portefeuilleData['Depotbank'].'" name="Depotbank" id="Depotbank">';
  $editObject->formVars['DepotbankOld'] = '<input class="" type="hidden" value="'.$portefeuilleData['Depotbank'].'" name="DepotbankOld" id="DepotbankOld">';
  $editObject->formVars['Risicoklasse'] = '<input class="" type="hidden" value="'.$portefeuilleData['Risicoklasse'].'" name="Risicoklasse" id="Risicoklasse">';



  $editObject->formVars["batchId"]=-1;
  $enkelvoudig="checked";
  $_GET['OrderSoort']='E';


  if($_GET['portefeuille']=='')
    $portefeuilleOption='<a href="javascript:lookupPort()"><img src="images/16/lookup.gif" border="0" height="18" align="middle"></a> <input class="" type="text" size="10" onchange="lookupPort()"';
  else
    $portefeuilleOption='<input class="" readonly type="text" size="10" value="'.$_GET['portefeuille'].'"';

  if($_GET['rekeningnr']=='')
    $rekeningOption='<select class="" type="select"  name="rekeningnr" id="rekeningnr" id="rekeningnr" onBlur="preSearch(this.value,\'valuta\',\'EUR\')"  > </select> ';
  else
   $rekeningOption='<input class="" readonly type="text"  name="rekeningnr" id="rekeningnr" id="rekeningnr" value="'.$_GET['rekeningnr'].'">';

   if($_GET['valuta']=='')
     $valutaOption='<select class="" type="select"  name="valuta" id="valuta" id="valuta" > <option value="EUR">EUR</option></select>' ;
   else
     $valutaOption='<input type="text" readonly name="valuta" id="valuta" id="valuta" value="'.$_GET['valuta'].'" >';

  if($enkelvoudig=='checked')
  {

  $editObject->formVars["newOrder"].='
<fieldset>
  <table border="0" cellpadding="5" cellspacing="0">
<tr>

  <td><label for="portefeuille">Portefeuille</label></td>
  <td> Client</td>
  <td><label for="rekeningnr">Rekeningnr</label></td>
  <td><label for="valuta">Valuta</label></td>
  <td><label for="Depotbank">Depotbank</label></td>
  <td><label for="Profiel">Profiel</label></td>
</tr>
<tr>
  <td> '.$portefeuilleOption.' name="portefeuille" id="portefeuille" ></td>
  <td><a id="clientNaam">'.$_GET['client'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></td>

  <td> '.$rekeningOption.'</td>
  <td>'.$valutaOption.' <sup>* valuta\'s hebben geen bestaande rekening</sup> </td>
  <td><span id="DepotbankHTML"> </span></td>
  <td><span id="ProfielHTML"> </span></td>
</tr>
</table>
</fieldset>

 <input type="hidden" name="client" value="'.$_GET['client'].'">
   <input class="" type="hidden"  value="'.$_GET['vermogensBeheerder'].'" name="vermogensBeheerder" >
';
    $editObject->formVars["newOrder"].=' <input type="hidden" name="batchId" value="'.$_GET['batchId'].'">';


  
$type='portefeuille';  
if(!checkAccess($type))
{
   if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	 else
	 {
    	$join=" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
	    $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}
$koppelObject[1] = new Koppel("Portefeuilles","editForm",'LEFT JOIN CRM_naw on Portefeuilles.Portefeuille=CRM_naw.portefeuille '.$join);
$koppelObject[1]->addFields("Portefeuilles.Portefeuille","portefeuille",true,true);
$koppelObject[1]->addFields("Depotbank","Depotbank",false,false);
$koppelObject[1]->addFields("Risicoklasse","Risicoklasse",false,false);
$koppelObject[1]->addFields("Client","client",true,true);
if($_SESSION['usersession']['gebruiker']['CRMlevel'] > 0)
  $koppelObject[1]->addFields("CRM_naw.zoekveld","zoekveld",true,true);

$koppelObject[1]->name = "port";
$koppelObject[1]->extraQuery = " AND Portefeuilles.einddatum > NOW() $beperktToegankelijk";
$koppelObject[1]->action = "preSearch(portefeuille,'rekeningnr');parent.getKoers();";
$koppelObject[1]->focus = "rekeningnr";
$editcontent['javascript'] .= "\n".$koppelObject[1]->getJavascript();

 
  }
//}
$editObject->__appvar = $__appvar;
$data = $_GET;
$action = $data['action'];
$redirectUrl = "orderregelsList.php";
$editObject->usetemplate = true;
//
// Bij opslaan order checken op wijzigingen, zoja doorsturen naar herberekening


//ordercheckboxes
if (($action == "update" || $action == "edit") && ($enkelvoudig=="checked") && $object->get('laatsteStatus') < 1)
{
  $order = new orderControlleBerekening();

  if($data['id'] != "" && $data['portefeuille'] !="" && $data['valuta'] != "")
  {
    $order->setdata($data['id'],$data['portefeuille'],$data['valuta'],$data['aantal'],	false);
    $query = "SELECT Vermogensbeheerder FROM Portefeuilles 	WHERE portefeuille = '".$data['portefeuille']."'";
    $checks = $order->getchecks();
    $db->SQL($query);
  	$vermogenbeheerder = $db->lookupRecord();
  	$vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];

    $checks = unserialize($checks[$vermogenbeheerder]);
    $order->setchecks($checks);
    $resultaat = $order->check();
    foreach ($checks as $key=>$value)//Set welke chk box getoond moet worden voor deze vermogensbeheerder
    {
      if ($value['checked']==1)
        $controles[$key]=$__ORDERvar["orderControles"][$key];
    }
  }
}

if($orderregelId)
{
  $object2->getById($orderregelId);
  if($action == "edit")
  	$export['controle_regels'] = unserialize($object2->get("controle_regels"));
  else
    $oldControle_regels=unserialize($object2->get("controle_regels"));
}



$editObject->controller($action,$data);


if($object2->get('id') > 0 && $object->get('laatsteStatus') > 0)
{
  $editObject->formVars["controlle_chk"]=$object2->get('CheckResult');
}
elseif ($action == "update" || $action == "edit" && $object->get('fondsCode') <> '')
{
  if(count($controles) > 0)
  {
    $editObject->formVars["controlle_chk"] .= "
    <fieldset>
    <legend><b>Controles</b></legend>
    <table border=1>";
    foreach ($controles as $key=>$value) //maak chk boxes voor deze vermogensbeheerder.
    {
      if($order->checksKort[$key] > 0)
      {
        if($export['controle_regels'][$key]['checked']==1)
          $error='';
        else
          $error='class="input_error"';
        $checkbox="<input type=\"checkbox\" $error value=\"1\" id=\"order_controle_checkbox_".$key."\" name=\"order_controle_checkbox_".$key."\" ".(($export['controle_regels'][$key]['checked']==1)?"checked":"").">";
      }
      else
        $checkbox='&nbsp;';

      $editObject->formVars["controlle_chk"] .= "<tr> <td> $checkbox</td><td width=200> <div>  <label for=\"order_controle_checkbox_".$key."\" title=\"".$value."\">".$value." </label></div></td>\n";
	    $editObject->formVars['controlle_chk'] .= "<td>".$resultaat[$key] ." </td></tr>\n ";
    }
    $editObject->formVars["controlle_chk"] .= "</table></fieldset>";

    if($object2->get('id') > 0)
    {
      if($object->get('laatsteStatus') < 1)
      {
        $object2->set('CheckResult',$editObject->formVars["controlle_chk"]);
        $object2->save();
      }


    }

  }
}
//
//
if($object->get('tijdsLimiet') == '0000-00-00')
  $object->set('tijdsLimiet','');

$object->setOption("transactieType","form_extra","onChange='changeTransaction()' ");
$object->setOption("tijdsSoort","form_extra","onChange='tijdsSoortChanged()' ");
$object->setOption("tijdsLimiet","form_extra","onChange='tijdslimietChange()' ");
$object->setOption("koersLimiet","form_extra","onChange='koersLimietChange()' ");
$object->setOption("transactieSoort","form_extra"," id='transactieSoort' onChange='setClass()' "); 


$huidigeStatus = $object->get("laatsteStatus");
if($huidigeStatus=='')
  $huidigeStatus=0;

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
  if($index==$huidigeStatus)
    $indexHuidigeStatus=$n;
  $indexLookup[$n]=$index;
  $n++;
}
$statusItems = count($indexLookup);

if ($indexHuidigeStatus < $statusItems)//&& $action == 'edit'
{
  if($_SESSION['usersession']['gebruiker']['ordersNietVerwerken']==1 && $huidigeStatus==0)
    $selectStatus=array(($indexLookup[$indexHuidigeStatus])   => $__ORDERvar["status"][$indexLookup[$indexHuidigeStatus]]);
  elseif($huidigeStatus<2)
    $selectStatus = array(($indexLookup[$indexHuidigeStatus])   => $__ORDERvar["status"][$indexLookup[$indexHuidigeStatus]],
                         ($indexLookup[$indexHuidigeStatus+1]) => $__ORDERvar["status"][$indexLookup[$indexHuidigeStatus+1]],
                         ($indexLookup[$statusItems-2]) => $__ORDERvar["status"][$indexLookup[$statusItems-2]],
                         ($indexLookup[$statusItems-1]) => $__ORDERvar["status"][$indexLookup[$statusItems-1]]);
  elseif($huidigeStatus>3)
    $selectStatus = array($indexLookup[$indexHuidigeStatus]   => $__ORDERvar["status"][$indexLookup[$indexHuidigeStatus]]);
  else
    $selectStatus = array(($huidigeStatus)   => $__ORDERvar["status"][$huidigeStatus],
                         ($huidigeStatus+1) => $__ORDERvar["status"][$huidigeStatus+1]);
}

$object->setOption("laatsteStatus","form_options",$selectStatus);


if($action=='new')//$huidigeStatus <2)
{
  $opslaanKnop='Opslaan en stukkenlijst muteren';
  $redir='document.editForm.redirect.value=1;';
}
else
{
  $opslaanKnop='Opslaan';
  $redir='';
}

if($object->checkAccess())
{
if($combinatie=='checked')
{
  $opslaanNieuw="";
  if($currentBatch==$_GET['batchId'] || $date['id']==0)
    $opslaanNieuw.=' <input type="button" style="border:outset 3px #111;font-weight:bold;padding: 3px 3px;" onclick="editForm.orderSelectieType[2].disabled=false;editForm.redirect.value=3;submitForm();" value="Opslaan en toevoegen">';
  $editObject->formVars["knoppen"] = '<input type="hidden" name="opslaan" value="">'.$opslaanNieuw.'
    <input type="button" style="border:outset 3px #111;font-weight:bold;padding: 3px 3px;" onclick="editForm.orderSelectieType[2].disabled=false;editForm.redirect.value=0;submitForm();" value="Opslaan en afronden">';
}
elseif($enkelvoudig=='checked')
  $editObject->formVars["knoppen"] = '<input type="button" style="border:outset 3px #111;font-weight:bold;padding: 3px 3px;" onclick="submitForm()" value="Opslaan">';
else
  $editObject->formVars["knoppen"] = '<input type="button" style="border:outset 3px #111;font-weight:bold;padding: 3px 3px;" onclick="'.$redir.'submitForm()" value="'.$opslaanKnop.'">';
}
$object->set("vermogensBeheerder",$__appvar['bedrijf']);
if ($action == "new")
{
  $object->set("tijdsLimiet",jul2sql(time()));
  $object->setOption("laatsteStatus","form_options",array(0=>$__ORDERvar["status"][0]));
  $object->set("status",date("Ymd_Hi")."/$USR ** aanmaken order");
  $object->set("memo",$standaard['OrderStandaardMemo']);
}
else
{
  $db = new DB();
  $query = "SELECT sum(aantal) as totaal FROM OrderRegels WHERE orderid='".$object->get("orderid")."' ";
  $db->SQL($query);
  $regelsRec = $db->lookupRecord();
  if (round($object->get("aantal"),4) != round(($regelsRec["totaal"]),4) && $object->get('id') > 0)
  {
    if(round(($regelsRec["totaal"]),4)==0.0)
    {
   
      $object->setOption("laatsteStatus","form_options", array(($huidigeStatus)   => $__ORDERvar["status"][$huidigeStatus],
                                                               6   => $__ORDERvar["status"][6]));
      $object->setOption("laatsteStatus","description","status ophogen onmogelijk aantal in stukkenlijst is 0");
    }
    else
    {
      $object->setOption("laatsteStatus","form_extra"," disabled ");
      $object->setOption("laatsteStatus","description","status wijzigen onmogelijk aantal <> stukkenlijst");
    }
  }
}

$object->setOption("fondsCode","form_extra","id=\"fondsCode\" onChange=\"javascript:select_fonds(document.editForm.fondsCode.value,600,400);\"");
if($bereken=='true')
  $editObject->formVars["iframe"] = "orderregelscontrole.php?listonly=1&orderid=".$object->get("orderid")."&id=".$object->get("id");
elseif($action <> 'new')
  $editObject->formVars["iframe"] = "orderregelsList.php?listonly=1&orderid=".$object->get("orderid")."&id=".$object->get("id")."&action=new";
else
  $editObject->formVars["iframe"] = "orderregelsList.php?listonly=1&orderid=".$object->get("orderid")."&id=".$object->get("id")."&action=new&batchId=".$editObject->formVars["batchId"];

if($editObject->object->locked)
  $editObject->formVars["knoppen"] ='';


$huidigeStatus = $object->get("laatsteStatus");

$editObject->formTemplate = "ordersEditEnkelvoudigTemplate.html";
if ($huidigeStatus < 1)
{
  $editObject->formVars["fondsSelect"]='<a href="javascript:select_fonds(document.editForm.fondsCode.value,600,400);"><img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>';
  $editcontent['body'] = " onLoad=\"javascript:initPage();\" ";
  $object->setOption("laatsteStatus","form_extra"," onChange='laatsteStatusChange()' ");
}
elseif ($huidigeStatus == 1)
{
  $object->setOption('fondsCode','form_extra',"READONLY ");
  $object->setOption('fonds','form_extra',"READONLY ");
  $object->setOption('aantal','form_extra',"READONLY ");
  $object->setOption('fondsOmschrijving','form_extra',"READONLY ");
  $object->setOption("laatsteStatus","form_extra"," onChange='laatsteStatusChange()' ");
  $object->setOption('transactieSoort','form_extra',"DISABLED ");
  $object->setOption('tijdsSoort','form_extra',"DISABLED ");
}
elseif ($huidigeStatus > 1)
{
  $object->setOption('fondsCode','form_extra',"READONLY ");
  $object->setOption('fonds','form_extra',"READONLY ");
  $object->setOption('aantal','form_extra',"READONLY ");
  $object->setOption('koersLimiet','form_extra',"READONLY ");
  $object->setOption('transactieSoort','form_extra',"DISABLED ");
  $object->setOption('tijdsSoort','form_extra',"DISABLED ");
  $object->setOption('tijdsLimiet','form_extra',"DISABLED");
  $editcontent['body'] = " onLoad=\"javascript:laatsteStatusChange();\" ";
}
elseif ($huidigeStatus > 2)
  $object->setOption('transactieType','form_extra',"DISABLED ");

$editObject->template = $editcontent;

echo $editObject->getOutput();



if ($result = $editObject->result)
{
  


	$orderid = $object->get("vermogensBeheerder").$object->get("id");
  $object->set("orderid",$orderid);
  $object->save();

  if(($_GET['orderSelectieType']=='enkelvoudig') && $_GET['action']=='update')
  {
    if($_POST['fondsCode']!='' && $object->get('laatsteStatus') < 1)
    {
    $db = new DB();
    $order = new orderControlleBerekening();
    $query = "SELECT Vermogensbeheerder FROM Portefeuilles 	WHERE portefeuille = '".$_POST['portefeuille']."'";
    $order->setdata($orderid,$_POST['portefeuille'],$_POST['valuta'],$_POST['aantal'],true);
    $checks = $order->getchecks();
    //listarray($checks);
    //$order->setdata($data['id'],$data['portefeuille'],$data['valuta'],$data['aantal'],	false);

    $db->SQL($query);
	  $vermogenbeheerder = $db->lookupRecord();
	  $vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];
    $checks = unserialize($checks[$vermogenbeheerder]);

    foreach($checks as $key=>$value) //Set welke chk box getoond moet worden voor deze vermogensbeheerder
    {
      if ($value['checked']==1)
        $controles[$key]=$__ORDERvar["orderControles"][$key];
    }


 	  foreach($__ORDERvar["orderControles"] as $key=>$value)
      $export['controle_regels'][$key]['checked'] = $data["order_controle_checkbox_".$key];

    $order->setchecks($checks);
  	$order->setregels($export['controle_regels']);
    $resultaat = $order->check();
    $maxCheck=$order->checkmax();

    }
    
    $newChecks='';
    foreach ($export["controle_regels"] as $key=>$value)
    {
      if($oldControle_regels[$key]['checked'] != $value['checked'])
        $newChecks .= "\n".date("Ymd_Hi")."/$USR Check $key ".$_POST['portefeuille']." ".$oldControle_regels[$key]['checked']." -> ".$value['checked']."";
    }

	  if($newChecks <> '')
	  {
      $object->set("status",$object->get('status').$newChecks);
      $object->save();

	  }
    
    if($_GET['orderSelectieType']=='combinatie' && $_GET['redirect'] <> 3 && $maxCheck < 1)
    {
      $query="UPDATE Orders SET laatsteStatus=0 WHERE laatsteStatus<1 AND batchId = '".$_GET['batchId']."' AND batchId > 0";
      $db = new DB();
	    $db->SQL($query);
      $db->Query();
      logIt('klaar. '.$query);
    }




if($noInsert==false)
{
    $query="INSERT INTO OrderRegels SET orderid='$orderid',positie=1,portefeuille='".$_POST['portefeuille']."',
    rekeningnr='".$_POST['rekeningnr']."',
    valuta='".$_POST['valuta']."',
    aantal='".$_POST['aantal']."',
    client='".mysql_real_escape_string($_POST['client'])."',
    controle_regels='".serialize($export["controle_regels"])."',
    controle='$maxCheck',
    status=0,
    memo='".mysql_real_escape_string($_POST['memo'])."',
    add_date=now(),
    add_user='".$USR."',
    change_date=now(),
    change_user='$USR'";
    $db = new DB();
    $db->SQL($query);
    $db->Query();
    $lastId=$db->last_id();
    updateBrutoWaarde($lastId);
}
else
{
    $updateFields=array('portefeuille','rekeningnr','valuta','aantal','client');
    $query="UPDATE OrderRegels SET ";
    foreach ($updateFields as $veld)
    {
      if($_POST[$veld] <> '')
        $query.="$veld = '".$_POST[$veld]."', ";
    }
    $query.=" controle_regels='".serialize($export["controle_regels"])."',
    controle='$maxCheck',
    change_date=now(),
    change_user='$USR' WHERE orderid='$orderid'";
    $db = new DB();
    $db->SQL($query); 
    $db->Query();
    $query="SELECT id FROM OrderRegels WHERE orderid='$orderid'";
    $db->SQL($query); 
    $orderregelId=$db->lookupRecord();
    updateBrutoWaarde($orderregelId['id']);
}

  	if($maxCheck > 0 && $_GET['orderSelectieType']=='enkelvoudig' )
  	  $data["redirect"]=4;
    elseif($maxCheck > 0 && $_GET['orderSelectieType']=='combinatie' )
      $data["redirect"]=5;

  }
  if ($data["redirect"] == '1')
	  header("Location: orderregelsList.php?orderid=".$orderid);
  elseif ($data["redirect"] == '2')
  	header("Location: ordersEdit.php?action=edit&bereken=true&id=".$object->get("id"));
  elseif ($data["redirect"] == '3')
  	header("Location: ordersEdit.php?action=new&batchId=".$_POST['batchId']."&orderSelectieType=".$_GET['orderSelectieType']."&portefeuille=".$_POST['portefeuille']."&rekeningnr=".$_POST['rekeningnr']."&valuta=".$_POST['valuta']."&client=".$_POST['client']);
  elseif ($data["redirect"] == 4)
  	header("Location: ordersEdit.php?action=edit&check=true&id=".$object->get("id")."&orderSelectieType=".$_GET['orderSelectieType']);
  elseif ($data["redirect"] == 5)
  	header("Location: ordersEdit.php?action=edit&check=true&id=".$object->get("id")."&batchId=".$_POST['batchId']."&orderSelectieType=".$_GET['orderSelectieType']."&portefeuille=".$_POST['portefeuille']."&rekeningnr=".$_POST['rekeningnr']."&valuta=".$_POST['valuta']."&client=".$_POST['client']);
  else
  	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}


?>
