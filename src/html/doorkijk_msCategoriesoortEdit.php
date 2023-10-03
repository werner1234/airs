<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.5 $
 		
    $Log: doorkijk_msCategoriesoortEdit.php,v $
    Revision 1.5  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("doorkijk ms Categorie soort");
$mainHeader   = vt("muteren");

$__funcvar['listurl']  = "doorkijk_msCategoriesoortList.php";
$__funcvar['location'] = "doorkijk_msCategoriesoortEdit.php";

$object = new Doorkijk_MsCategoriesoort();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_GET, $_POST);
$action = $data['action'];

if ( $action === 'update' )
{
	$data['grafiekKleur'] = serialize($data['grafiekKleur']);
}

if($data['id']>0)
{
	$object->getById($data['id']);
	$oldMsCategorie = $object->get('msCategorie');
	$oldMsCategoriesoort = $object->get('msCategoriesoort');

	$grafiekKleur = unserialize($object->get('grafiekKleur'));
	if ( $grafiekKleur )
	{
		$editObject->formVars['Rood']  = $grafiekKleur['0'];
		$editObject->formVars['Groen'] = $grafiekKleur['1'];
		$editObject->formVars['Blauw'] = $grafiekKleur['2'];
	}
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "doorkijk_msCategoriesoortEditTemplate.html";

$editObject->controller($action,$data);

//echo $editObject->getTemplate();
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($data['key_msCategorie']==1 && $data['msCategorie'] <> $oldMsCategorie)
	{
		$query="UPDATE doorkijk_koppelingPerVermogensbeheerder  SET bronkoppeling='".mysql_real_escape_string($data['msCategorie'])."' 
		WHERE systeem='ms' AND  doorkijkCategoriesoort='".mysql_real_escape_string($oldMsCategoriesoort)."' AND bronkoppeling='".mysql_real_escape_string($oldMsCategorie)."'";
		logIt($query);
	}
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
