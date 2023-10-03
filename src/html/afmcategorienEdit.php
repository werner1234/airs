<?php
/*
    AE-ICT CODEX source module versie 1.6, 14 december 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.6 $

    $Log: afmcategorienEdit.php,v $
    Revision 1.6  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.5  2015/08/30 11:42:24  rvv
    *** empty log message ***

    Revision 1.4  2015/06/14 13:47:36  rvv
    *** empty log message ***

    Revision 1.3  2015/05/16 09:31:45  rvv
    *** empty log message ***

    Revision 1.2  2011/12/18 14:25:43  rvv
    *** empty log message ***

    Revision 1.1  2011/12/14 19:32:10  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "afmcategorienList.php";
$__funcvar['location'] = "afmcategorienEdit.php";

$object = new AfmCategorien();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;

$db=new DB();
if($data['id'] > 0)
{
  $query="SELECT id,afmCategorie FROM afmCategorien WHERE id='".$data['id']."'";
  $db->SQL($query);
  $afmRecord=$db->lookupRecord();
}
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

if($__appvar["bedrijf"] == "HOME")
  $editObject->formTemplate = "afmcategorienEditTemplateHome.html";
else
  $editObject->formTemplate = "afmcategorienEditTemplate.html";

if($action=='update' && $__appvar["bedrijf"] == "HOME")
{
  $correlatie=array();
  foreach ($data as $key=>$value)
  {
    if(substr($key,0,6)=='afmId_')
    {
      $afmId=substr($key,6);
      $correlatie[$afmId]=$value;
    }
  }
  $data['correlatie']=serialize($correlatie);
}

$editObject->controller($action,$data);

if($__appvar["bedrijf"] == "HOME")
{
  $correlatie 	= unserialize($object->get("correlatie"));
  $db=new DB();
  $query="SELECT id,afmCategorie FROM afmCategorien ORDER BY afmCategorie";
  $db->SQL($query);
  $db->Query();
  $editObject->formVars["correlatie"]='<table>';
  while($dbdata=$db->nextRecord())
  {
    $editObject->formVars["correlatie"].="<tr><td>".$dbdata['afmCategorie']."</td><td><input type='text' value='".$correlatie[$dbdata['id']]."' name='afmId_".$dbdata['id']."' size='3'></td>";
  }
  $editObject->formVars["correlatie"].='</table>';
}

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
//echo $editObject->getTemplate();exit;
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $huidigeCategorie=$object->get('afmCategorie');
  if($huidigeCategorie <> '' && $huidigeCategorie <> $afmRecord['afmCategorie'])
  {
    updateVermogensbeheerderKleuren('AFM',$afmRecord['afmCategorie'],$huidigeCategorie);
  }
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>