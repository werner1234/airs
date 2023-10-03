<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "rekeningafschriftenList.php";
$__funcvar['location'] = "rekeningafschriftenEdit.php";

$object = new Rekeningafschriften();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['jsincludes'] = "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent['body'] = "onLoad='document.editForm.Client.focus();'";
$editObject->template = $editcontent;

// controlleer of het een memoriaal rekening is. Include dan een andere Template
if($memoriaal)
{
	$selectMemoriaal = 1;
	$template = "rekeningafschriftenMemoriaalTemplate.html";
}
else
{
	$selectMemoriaal = 0;
	$template = "rekeningafschriftenTemplate.html";
}

$editObject->formTemplate = $template;
$editObject->usetemplate = true;

$data = $_GET;
$action = $data['action'];

// Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Client


$DB = new DB();
if($action <> 'edit')
{
  $query = " SELECT DISTINCT(Client) FROM Portefeuilles,Rekeningen WHERE ".
  				 " Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND Portefeuilles.consolidatie=0 AND ".
  				 " Rekeningen.Memoriaal = '".$selectMemoriaal."' ORDER BY Client ";
  $DB->SQL($query);
  $DB->Query();
  $editObject->formVars['Client_options']	= "\n<option value=\"\">----------------</option>";
  while($clientdata = $DB->NextRecord())
  {
	  $editObject->formVars['Client_options']	.= "\n<option value=\"".$clientdata['Client']."\">".$clientdata['Client']."</option>";
  }
}

$editObject->formVars['Rekening_options'] = "\n<option value=\"{Rekening_value}\" selected>{Rekening_value}</option>";



$editObject->controller($action,$data);


if($action=='edit')
{
  $query="SELECT Client FROM Portefeuilles Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.consolidatie=0 AND Rekeningen.Rekening='".$object->get('Rekening')."'";
  $DB->SQL($query);
  $clientData=$DB->lookupRecord();
  $editObject->formVars['Client_options'] = "\n<option value=\"".$clientData['Client']."\" selected>".$clientData['Client']."</option>";
}

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	if($editObject->data['action'] == "update")
		header("Location: rekeningmutatiesEdit.php?action=new&afschrift_id=".$object->get("id"));
	else
		header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>