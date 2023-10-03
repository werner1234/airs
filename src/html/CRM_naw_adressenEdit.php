<?php
/*
    AE-ICT CODEX source module versie 1.6, 3 februari 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $

    $Log: CRM_naw_adressenEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2011/10/23 13:32:25  rvv
    *** empty log message ***

    Revision 1.1  2010/02/03 17:04:59  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt('muteren');

$__funcvar['listurl']  = "CRM_naw_adressenList.php";
$__funcvar['location'] = "CRM_naw_adressenEdit.php";

$object = new CRM_naw_adressen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$db=new db();
$query="SELECT max(Vermogensbeheerders.CRM_eigenTemplate) as CRM_eigenTemplate
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();

if($gebruikPortefeuilleInformatie['CRM_eigenTemplate']==1 && file_exists('CRM_naw_AdressenEditTemplate_custom.html'))
  $editObject->formTemplate = "CRM_naw_AdressenEditTemplate_custom.html";
else
  $editObject->formTemplate = 'CRM_naw_adressenEdit_template.html';

$editObject->controller($action,$data);

if ($action == "new")
{
  $object->setOption("rel_id","value",$_GET['rel_id']);
}
//echo $editObject->getTemplate();exit;
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