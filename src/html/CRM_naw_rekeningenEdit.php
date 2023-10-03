<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 februari 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: CRM_naw_rekeningenEdit.php,v $
    Revision 1.4  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.3  2014/08/27 15:44:57  rvv
    *** empty log message ***

    Revision 1.2  2014/08/23 15:36:34  rvv
    *** empty log message ***

    Revision 1.1  2010/02/03 17:04:59  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar[listurl]  = "CRM_naw_rekeningenList.php";
$__funcvar[location] = "CRM_naw_rekeningenEdit.php";

$object = new CRM_naw_rekeningen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_POST,$_GET);
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 

$db=new db();
$query="SELECT max(Vermogensbeheerders.CRM_eigenTemplate) as CRM_eigenTemplate
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();

if($gebruikPortefeuilleInformatie['CRM_eigenTemplate']==1 && file_exists('CRM_naw_RekeningenEditTemplate_custom.html'))
{
  $editObject->usetemplate = true;
  $editObject->formTemplate = "CRM_naw_RekeningenEditTemplate_custom.html";
}
$editObject->controller($action,$data);

if ($action == "new")
{
  $object->setOption("rel_id","value",$_GET['rel_id']);
}

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