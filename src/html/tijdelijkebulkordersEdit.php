<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 11 september 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: tijdelijkebulkordersEdit.php,v $
    Revision 1.6  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.5  2015/08/30 11:42:24  rvv
    *** empty log message ***

    Revision 1.4  2014/11/05 16:51:18  rvv
    *** empty log message ***

    Revision 1.3  2013/11/06 15:52:25  rvv
    *** empty log message ***

    Revision 1.2  2013/09/22 15:23:37  rvv
    *** empty log message ***

    Revision 1.1  2013/09/18 15:37:28  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
include_once("orderControlleRekenClass.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "tijdelijkebulkordersList.php";
$__funcvar['location'] = "tijdelijkebulkordersEdit.php";

$object = new TijdelijkeBulkOrders();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$editObject->formTemplate = "tijdelijkebulkordersTemplate.html";

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$object->getById($_GET["id"]);

$db=new DB();
if ($action == "update" || $action == "edit")
{
  if($object->get('status') < 1)
  {
    $order = new orderControlleBerekening(true);
    if($data['id'] != "" && $data['portefeuille'] !="" )
    {
      $order->setdata($data['id'],$data['portefeuille'],'EUR',$data['aantal'],	false,true);
      $query = "	SELECT Vermogensbeheerder FROM Portefeuilles 	WHERE portefeuille = '".$data['portefeuille']."'";
    }
    else
    {
      if($object->data['fields']['valuta']['value'] <> '')
        $valuta=$object->data['fields']['valuta']['value'];
      else
        $valuta='EUR';  
      $order->setdata($object->data['fields']['id']['value'],$object->data['fields']['portefeuille']['value'],$valuta,$object->data['fields']['aantal']['value'],	false,true);
      $query = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille = '".$object->data['fields']['portefeuille']['value']."'";
    }
    $db->SQL($query);
  	$vermogenbeheerder = $db->lookupRecord();
  	$vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];

    $checks = $order->getchecks($vermogenbeheerder);

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
if ($action == "update" )
{
	foreach ($__ORDERvar["orderControles"] as $key=>$value)
	{
		$export['checkResultRegels'][$key]['checked'] = $data["order_controle_checkbox_".$key];
	}
  

	$oldcheckResultRegels=unserialize($object->get("checkResultRegels"));

	//$object->set("checkResultRegels",serialize($export["checkResultRegels"]));
  $data['checkResultRegels']=serialize($export["checkResultRegels"]);
  $order->setdata(	$data['id'],$data['portefeuille'],$data['valuta'],$data['aantal'],true);
	$order->setregels($export["checkResultRegels"]);
	$data["checkResult"]=$order->checkmax();
  


	$newChecks='';

 	if($object->data['fields']['portefeuille']['value'] <>'')
	  $logPort=$object->data['fields']['portefeuille']['value'];
	else
	  $logPort=$data['portefeuille'];

	foreach ($export["checkResultRegels"] as $key=>$value)
	{
    if($oldcheckResultRegels[$key]['checked'] != $value['checked'])
      $newChecks .= "\n".date("Ymd_Hi")."/$USR Check ".$logPort." $key ".$oldcheckResultRegels[$key]['checked']." -> ".$value['checked']."";
	}

	if($newChecks <> '')
	{
    $data['statusLog']=$object->get('statusLog').$newChecks;
  }

}
else
{
	$export['checkResultRegels'] = unserialize($object->get("checkResultRegels"));
}



if (($action == "update" || $action == "edit"))
{
    $editObject->formVars["controlle_chk"] .= "
    <table border=1>";
    foreach ($controles as $key=>$value) //maak chk boxes voor deze vermogensbeheerder.
    {
      if($order->checksKort[$key] > 0)
      {
        if($export['checkResultRegels'][$key]['checked']==1)
          $error='';
        else
          $error='class="input_error"';

        $checkbox="<input type=\"checkbox\" $error value=\"1\" id=\"order_controle_checkbox_".$key."\" name=\"order_controle_checkbox_".$key."\" ".(($export['checkResultRegels'][$key]['checked']==1)?"checked":"").">";

      }
      else
        $checkbox='&nbsp;';

      $editObject->formVars["controlle_chk"] .= "<tr> <td> $checkbox</td><td width=200> <div>  <label for=\"order_controle_checkbox_".$key."\" title=\"".$value."\">".$value." </label></div></td>\n";
	    $editObject->formVars['controlle_chk'] .= "<td>".$resultaat[$key] ." </td></tr>\n ";
    }
    $editObject->formVars["controlle_chk"] .= "</table>";
    
 
}
//else
// $editObject->formVars["controlle_chk"]=$object->get('CheckResult');

$editObject->controller($action,$data);
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>