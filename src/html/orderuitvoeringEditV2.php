<?php
/*
    AE-ICT CODEX source module versie 1.6, 19 september 2009
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2018/03/16 15:58:06 $
    File Versie         : $Revision: 1.16 $

    $Log: orderuitvoeringEditV2.php,v $
    Revision 1.16  2018/03/16 15:58:06  rm
    Opgelopenrente voor ordertransrep tonen

    Revision 1.15  2017/04/02 05:50:28  rvv
    *** empty log message ***

    Revision 1.14  2017/03/05 12:05:38  rvv
    *** empty log message ***

    Revision 1.13  2016/10/16 14:35:41  rvv
    *** empty log message ***

    Revision 1.12  2016/09/28 13:58:32  rvv
    *** empty log message ***

    Revision 1.11  2016/09/24 12:03:24  rvv
    *** empty log message ***

    Revision 1.10  2016/09/22 14:28:04  rm
    no message

    Revision 1.9  2016/09/14 14:43:19  rm
    opslaan naar nota

    Revision 1.8  2016/09/14 13:37:25  rm
    Ordersv2

    Revision 1.7  2016/07/27 15:56:14  rvv
    *** empty log message ***

    Revision 1.6  2016/07/18 14:51:02  rm
    5137

    Revision 1.5  2016/07/06 16:05:51  rvv
    *** empty log message ***

    Revision 1.4  2016/06/03 15:01:19  rm
    Orders

    Revision 1.3  2015/08/09 15:03:35  rvv
    *** empty log message ***

    Revision 1.2  2015/06/26 07:05:52  rm
    Orders v2

    Revision 1.1  2015/06/20 10:08:59  rm
    Orders

    Revision 1.10  2014/12/24 09:54:51  cvs
    call 3105

    Revision 1.9  2013/05/26 13:57:17  rvv
    *** empty log message ***

    Revision 1.8  2013/04/20 16:28:49  rvv
    *** empty log message ***

    Revision 1.7  2013/04/07 16:08:24  rvv
    *** empty log message ***

    Revision 1.6  2013/03/30 12:21:17  rvv
    *** empty log message ***

    Revision 1.5  2012/01/28 16:13:06  rvv
    *** empty log message ***

    Revision 1.4  2011/11/12 18:32:28  rvv
    *** empty log message ***

    Revision 1.3  2011/09/14 09:26:56  rvv
    *** empty log message ***

    Revision 1.2  2009/10/07 16:17:58  rvv
    *** empty log message ***

    Revision 1.1  2009/10/07 10:00:56  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$AETemplate = new AE_template();

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "orderuitvoeringListV2.php";
$__funcvar['location'] = "orderuitvoeringEditV2.php";

$object = new OrderUitvoeringV2();


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$db=new DB();

$editObject->formTemplate = "orderUitvoeringsTemplateV2.html";

$query="SELECT max(check_module_ORDERNOTAS) as check_module_ORDERNOTAS, max(orderTransRep) as orderTransRep FROM Vermogensbeheerders";
$db->SQL($query);
$verm=$db->lookupRecord();
if($verm['check_module_ORDERNOTAS']==1) {
  $editObject->formTemplate = "orderUitvoeringsTemplateNotaV2.html";
} else {
  if( (int) $verm['orderTransRep'] == 0 ) {
    $object->data['fields']['opgelopenrente']['form_visible'] = false;
    $object->data['fields']['opgelopenrente']['description'] = '';
  }
}
$editObject->usetemplate = true;


$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask-masks');
//$editcontent[pageHeader] = "<b>".$mainHeader."</b>";


$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = false;  // geen templateheaders in $editObject->output toevoegen

if( ! requestType('ajax') ) {
  $editObject->formVars["submit"]='<a href="javascript:editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;opslaan</a>
  <a href="javascript:editForm.action.value=\'delete\';editForm.submit();" onClick=""><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>
  <a href="javascript:window.history.back();" ><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>'
  ;
} else {
  $editObject->formVars['formName'] = 'orderUitvoeringEdit';
  $editObject->formVars['formId'] = 'orderUitvoeringEditForm';
  $__appvar['templateContentHeader'] = 'templates/ajax_head.inc';
  $__appvar['templateRefreshFooter'] = 'templates/ajax_voet.inc';
}
//listarray('orderid '.$editObject->object->data['fields']['orderid']['value']);
//listarray('uitvoeringsAantal '.$editObject->object->data['fields']['uitvoeringsAantal']['value']);
$editObject->controller($action,$data);
//listarray('orderid '.$editObject->object->data['fields']['orderid']['value']);
//listarray('uitvoeringsAantal '.$editObject->object->data['fields']['uitvoeringsAantal']['value']);
//listarray($editObject);
/** als request type = ajax return json voor jquery bij update of verwijderen **/
if( requestType('ajax') ) {
  $AEJson = new AE_Json();
  $editcontent['javascript'] = '';
  if ($action == 'update' || $action == 'delete')
  {
    if ($editObject->object->error == false)
    {
      if($action=='delete')
      {
        $orderLogs = new orderLogs();
        $orderLogs->addToLog($object->get('orderid'), null, "Uitvoering " . $object->get('id') . " verwijderd.");
      }
      $uitvoeringsValutakoers=$object->uitvoeringsValutakoers($object->get('orderid'));
      echo $AEJson->json_encode(array(
          'success' => true,
          'uitvoeringsValutaKoers' => $uitvoeringsValutakoers,
          'saved'   => true,
          'message'   => $editObject->_error
        )); //let ajax know the request ended in success
      checkUitvoeringenComplete();
        exit();
    }
    else
    {

      echo $AEJson->json_encode(array(
          'success'                => true,
          'saved'                  => false,
          'message'                => $editObject->_error,
          'errors'                 => $object->getErrors()
        )); //let ajax know the request ended in failure
    }
    exit();
  }
  /** if ajax disable header and footer **/

  $editObject->includeHeaderInOutput = false;
}

if($object->get('orderid') <> '')
  $orderId= $object->get('orderid');
else
  $orderId=$_GET['orderid'];

$query="SELECT koersLimiet,transactieSoort,Fondsen.Fondseenheid,OrdersV2.fonds,OrdersV2.orderSoort FROM OrdersV2 LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds WHERE OrdersV2.id='".$orderId."'";
$db->SQL($query);
$koers=$db->lookupRecord();
if($koers['Fondseenheid']==0)
  $koers['Fondseenheid']=1;

if($koers['koersLimiet'] <> 0)
{
  if(substr($koers['transactieSoort'],0,1) == 'A')
    $comp='>';
  if(substr($koers['transactieSoort'],0,1) == 'V')
    $comp='<';

  if ( ! requestType('ajax')) {
    $editcontent['javascript'] = "

      function isNumber( value )
      {
      return isFinite( (value * 1.0) );
      }

      function uitvoeringsPrijsChange()
      {
        if(isNumber(editForm.uitvoeringsPrijs.value))
        {
          if(editForm.uitvoeringsPrijs.value $comp ".$koers['koersLimiet'].")
          {
             alert(\"Waarde wijkt af van de opgegeven limiet.\");
          }
        }
        else
        {
          alert(\"Geen getal opgegeven.\");
        }
      }";
  } else {
    $editcontent['javascript'] = "
      function isNumber( value )
      {
      return isFinite( (value * 1.0) );
      }

      function uitvoeringsPrijsChange()
      {
        var uitvoeringsPrijsVal = $('#".$editObject->formVars['formId']." #uitvoeringsPrijs').val();
        if ( isNumber( uitvoeringsPrijsVal ) ) {
          if ( uitvoeringsPrijsVal " . $comp . " ".$koers['koersLimiet'].") {
            alert(\"Waarde wijkt af van de opgegeven limiet.\");
          }
        } else {
          alert(\"Geen getal opgegeven.\");
        }
      }";
  }

$object->setOption('uitvoeringsPrijs','form_extra','onChange="javascript:uitvoeringsPrijsChange();"');

}
$editObject->template = $editcontent;

$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

if ($action != 'update' || $object->error)
  echo template($__appvar["templateContentHeader"],$editcontent);

if($action == 'new')
{
  $object->set('orderid',$_GET['orderid']);
  $object->set('uitvoeringsAantal',$_GET['toAdd']);
  $object->set('uitvoeringsDatum',date("Y-m-d H:i:s"));
}



echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($action == 'update')
  {
    checkUitvoeringenComplete();
  }
  header("Location: orderuitvoeringList.php?orderid=".$object->get('orderid'));
}
else
{
 echo $_error = $editObject->_error;
}

function checkUitvoeringenComplete()
{
    $db=new DB();
    $query="SELECT SUM(uitvoeringsAantal) as aantal FROM OrderUitvoeringV2 WHERE orderid='".$_GET['orderid']."'";
    $db->SQL($query);
    $OrderUitvoering=$db->lookupRecord();
    $query="SELECT SUM(Aantal) as aantal FROM OrderRegelsV2 WHERE orderid='".$_GET['orderid']."'";
    $db->SQL($query);
    $OrderRegels=$db->lookupRecord();
    $query="SELECT orderStatus FROM OrdersV2 WHERE id='".$_GET['orderid']."'";
    $db->SQL($query);
    $OrderStatus=$db->lookupRecord();

    if($OrderUitvoering['aantal'] == $OrderRegels['aantal'] && $OrderUitvoering['aantal'] > 0)
    {
      $query="SELECT id FROM OrderRegelsV2 WHERE orderid='".$_GET['orderid']."'";
      $db->SQL($query);
      $db->Query();
      while($orderRegelsData=$db->nextRecord())
      {
        //listarray($orderRegelsData);
        updateBrutoWaarde($orderRegelsData['id'],true);
      }
      $fix=new AE_FIXtransport();
      $fix->verzendStatusMail($_GET['orderid'],$OrderStatus['orderStatus'],$OrderStatus['orderStatus']);
    }
}

/** ajax functionaliteit voor jquery modal **/

if( requestType('ajax') )
{
  $actions = '';

  $actions .= '<a href="#" id="orderUitvoeringSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan') . '</a>';

  if ( checkOrderAcces('notaModule') === true && $koers['orderSoort'] != 'M') {
    $actions .= '<a href="#" id="orderUitvoeringSaveDataToNota"><img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan naar nota\'s') . '</a>';
  }

  if ( $action !== 'new' ) {
    $actions .= '<a href="#" id="orderUitvoeringRemoveData"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;' . vt('verwijder') . '</a>';
  }
  $actions .= '
    <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;' . vt('terug') . '</a>
    <span style="float:left"><a href="#" id="orderUitvoeringSaveDataToDoorgegeven"> &nbsp;&nbsp;&nbsp; <img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;' . vt('Opslaan -> Doorgeg. orders') . '</a></span>
    <input type="hidden" name="frame" value="1">
  ';

  echo $AETemplate->parseFile('jqueryDialog/orderUitvoeringEditDialogData.html', array(
    'actions'       => $actions,
    'javascript'    => $editcontent['javascript'],
    'refreshUrl'    => "orderuitvoeringListV2.php?orderid=".$object->get('orderid')
  ));

  echo template('templates/ajax_voet.inc', $editObject->template);
}
