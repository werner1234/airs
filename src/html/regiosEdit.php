<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 oktober 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: regiosEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2015/08/30 11:42:24  rvv
    *** empty log message ***

    Revision 1.1  2006/10/20 14:56:28  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar[listurl]  = "regiosList.php";
$__funcvar[location] = "regiosEdit.php";

$object = new Regios();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];
$db=new DB();
if($data['id'] > 0)
{
  $query="SELECT id,Regio FROM Regios WHERE id='".$data['id']."'";
  $db->SQL($query);
  $dbRecord=$db->lookupRecord();
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $huidigeCategorie=$object->get('Regio');
  if($huidigeCategorie <> '' && $huidigeCategorie <> $dbRecord['Regio'])
  {
    updateVermogensbeheerderKleuren('OIR',$dbRecord['Regio'],$huidigeCategorie);
  }
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>