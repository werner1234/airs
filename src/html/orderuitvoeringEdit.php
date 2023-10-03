<?php
/*
    AE-ICT CODEX source module versie 1.6, 19 september 2009
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2014/12/24 09:54:51 $
    File Versie         : $Revision: 1.10 $

    $Log: orderuitvoeringEdit.php,v $
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

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "orderuitvoeringList.php";
$__funcvar['location'] = "orderuitvoeringEdit.php";

$object = new OrderUitvoering();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$db=new DB();

$editObject->formTemplate = "orderUitvoeringsTemplate.html";

$query="SELECT max(check_module_ORDERNOTAS) as check_module_ORDERNOTAS FROM Vermogensbeheerders";
$db->SQL($query);
$verm=$db->lookupRecord();
if($verm['check_module_ORDERNOTAS']==1)
  $editObject->formTemplate = "orderUitvoeringsTemplateNota.html";

$editObject->usetemplate = true;

//$editcontent[pageHeader] = "<b>".$mainHeader."</b>";


$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = false;  // geen templateheaders in $editObject->output toevoegen

$editObject->formVars["submit"]='<a href="javascript:editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;opslaan</a>
<a href="javascript:editForm.action.value=\'delete\';editForm.submit();" onClick=""><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>
<a href="javascript:window.history.back();" ><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>'
;

$editObject->controller($action,$data);

if($object->get('orderid') <> '')
  $orderId= $object->get('orderid');
else
  $orderId=$_GET['orderid'];

$query="SELECT koersLimiet,transactieSoort,Fondseenheid,Orders.fonds FROM Orders LEFT JOIN Fondsen ON Orders.fonds = Fondsen.Fonds WHERE orderId='".$orderId."'";
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

$editcontent['javascript']="

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
  $object->set('uitvoeringsDatum',date("Y-m-d"));
}



echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($action == 'update')
  {
    $db=new DB();
    $query="SELECT SUM(uitvoeringsAantal) as aantal FROM OrderUitvoering WHERE orderid='".$_GET['orderid']."'";
    $db->SQL($query);
    $OrderUitvoering=$db->lookupRecord();
    $query="SELECT SUM(Aantal) as aantal FROM OrderRegels WHERE orderid='".$_GET['orderid']."'";
    $db->SQL($query);
    $OrderRegels=$db->lookupRecord();
    if($OrderUitvoering['aantal'] == $OrderRegels['aantal'] && $OrderUitvoering['aantal'] > 0)
    {
      $query="SELECT id FROM OrderRegels WHERE orderid='".$_GET['orderid']."'";
      $db->SQL($query);
      $db->Query();
      $db2=new DB();
      while($orderRegelsData=$db->nextRecord())
      {
        listarray($orderRegelsData);
        updateBrutoWaarde($orderRegelsData['id'],true);
      }
    }
  }
  header("Location: orderuitvoeringList.php?orderid=".$object->get('orderid'));
}
else
{
 echo $_error = $editObject->_error;
}
?>