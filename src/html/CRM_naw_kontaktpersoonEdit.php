<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.6 $

    $Log: CRM_naw_kontaktpersoonEdit.php,v $
    Revision 1.6  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.5  2017/12/16 18:42:38  rvv
    *** empty log message ***

    Revision 1.4  2011/10/23 13:32:25  rvv
    *** empty log message ***

    Revision 1.3  2009/10/21 16:06:28  rvv
    *** empty log message ***

    Revision 1.2  2008/02/20 12:04:30  rvv
    GET->POST omzetting

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.2  2005/11/21 10:08:25  cvs
    *** empty log message ***

    Revision 1.1  2005/11/17 08:09:45  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader   = "";

$__funcvar[listurl]  = "CRM_naw_kontaktpersoonList.php";
$__funcvar[location] = "CRM_naw_kontaktpersoonEdit.php";

$object = new CRM_naw_kontaktpersoon();

$data = array_merge($_GET,$_POST);
$action = $data[action];

if ($action == "new")
  $mainHeader  = "Relatie toevoegen";
else
  $mainHeader  = "Relatie muteren";

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/CRM_naw_kontaktpersoonEdit.js\" type=text/javascript></script>";
$editObject->template = $editcontent;

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$db=new db();
$query="SELECT max(Vermogensbeheerders.CRM_eigenTemplate) as CRM_eigenTemplate
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();

if($gebruikPortefeuilleInformatie['CRM_eigenTemplate']==1 && file_exists('CRM_naw_ContactpersoonEditTemplate_custom.html'))
  $editObject->formTemplate = "CRM_naw_ContactpersoonEditTemplate_custom.html";
else
  $editObject->formTemplate = 'CRM_naw_kontaktpersoonEditTemplate.html';
$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

$object->setOption("tel1_oms"         ,"form_options",GetSelectieVelden("telefoon",false));
$object->setOption("tel2_oms"         ,"form_options",GetSelectieVelden("telefoon",false));


if ($action == "new")
{
  $object->setOption("rel_id","value",$_GET[rel_id]);
}
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