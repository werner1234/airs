<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "depotbankList.php";
$__funcvar[location] = "depotbankEdit.php";

$object = new Depotbank();
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$editcontent["body"] = " onload=\"placeFocus();\" ";
$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];
$db=new DB();
if($data['id'] > 0)
{
  $query="SELECT id,Depotbank FROM Depotbanken WHERE id='".$data['id']."'";
  $db->SQL($query);
  $dbRecord=$db->lookupRecord();
}

//$editObject->usetemplate = true;
$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $huidigeCategorie=$object->get('Depotbank');
  if($huidigeCategorie <> '' && $huidigeCategorie <> $dbRecord['Depotbank'])
  {
    updateVermogensbeheerderKleuren('DEP',$dbRecord['Depotbank'],$huidigeCategorie);
  }
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>