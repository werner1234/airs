<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "beleggingscategorieList.php";
$__funcvar['location'] = "beleggingscategorieEdit.php";

$object = new Beleggingscategorie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$db=new DB();
if($data['id'] > 0)
{
  $query="SELECT id,Beleggingscategorie FROM Beleggingscategorien WHERE id='".$data['id']."'";
  $db->SQL($query);
  $dbRecord=$db->lookupRecord();
}

$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $huidigeCategorie=$object->get('Beleggingscategorie');
  if($huidigeCategorie <> '' && $huidigeCategorie <> $dbRecord['Beleggingscategorie'])
  {
    updateVermogensbeheerderKleuren('OIB',$dbRecord['Beleggingscategorie'],$huidigeCategorie);
  }
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>