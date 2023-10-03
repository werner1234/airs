<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 november 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/02/22 18:43:08 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: signaleringstortingenEdit.php,v $
    Revision 1.2  2020/02/22 18:43:08  rvv
    *** empty log message ***

    Revision 1.1  2020/01/11 19:42:11  rvv
    *** empty log message ***

    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/11/25 20:22:26  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "signaleringstortingenList.php";
$__funcvar['location'] = "signaleringstortingenEdit.php";

$object = new SignaleringStortingen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "signaleringstortingenEditTemplate.html";


$editObject->controller($action,$data);

$vermogensbeheerder='';
$toelichtingen=array();
$db=new DB();
if($object->get('portefeuille') <> '');
{
  $query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".mysql_real_escape_string($object->get('portefeuille'))."'";
  $db->SQL($query);
  $verm=$db->lookupRecord();
  $vermogensbeheerder=$verm['Vermogensbeheerder'];
}
if($vermogensbeheerder<>'')
{
  $query="SELECT waarde FROM KeuzePerVermogensbeheerder WHERE vermogensbeheerder='$vermogensbeheerder' AND categorie='toelichtingStortOnttr' ORDER BY Afdrukvolgorde,waarde";
  $db->SQL($query);
  $db->query();
  while($dbdata=$db->nextRecord())
  {
    $toelichtingen[$dbdata['waarde']]=$dbdata['waarde'];
  }
}
if(count($toelichtingen)==0)
{
  $query="SELECT toelichting FROM toelichtingStortOnttr ORDER BY toelichting";
  $db->SQL($query);
  $db->query();
  while($dbdata=$db->nextRecord())
  {
    $toelichtingen[$dbdata['toelichting']]=$dbdata['toelichting'];
  }
}
$object->setOption('toelichting','form_options',$toelichtingen);

//toelichting


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