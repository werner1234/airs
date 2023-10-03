<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 oktober 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/03/02 18:20:23 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: doorkijk_categoriePerVermogensbeheerderEdit.php,v $
    Revision 1.4  2019/03/02 18:20:23  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("doorkijk categorien per vermogensbeheerder");
$mainHeader   = vt("muteren");

$__funcvar['listurl']  = "doorkijk_categoriePerVermogensbeheerderList.php";
$__funcvar['location'] = "doorkijk_categoriePerVermogensbeheerderEdit.php";

$object = new doorkijk_categoriePerVermogensbeheerder();
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";
$editcontent['body']="onload='checkDoorkijkCategoriesoort();'";
$editObject->template = $editcontent;

$data = array_merge($_GET, $_POST);
$action = $data['action'];

if ( $action === 'update' )
{
	$data['grafiekKleur'] = serialize($data['grafiekKleur']);
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject-;>output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "doorkijk_categoriePerVermogensbeheerderEditTemplate.html";

$editObject->controller($action,$data);

if ($object->get('id') > 0 )
{
 	$grafiekKleur = unserialize($object->get('grafiekKleur'));

  $editObject->formVars['Rood']  = $grafiekKleur['0'];
  $editObject->formVars['Groen'] = $grafiekKleur['1'];
  $editObject->formVars['Blauw'] = $grafiekKleur['2'];
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
