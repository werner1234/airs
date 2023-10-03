<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "valutaList.php";
$__funcvar['location'] = "valutaEdit.php";

$object = new Valuta();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];
$db=new DB();
if($data['id'] > 0)
{
  $query="SELECT id,Valuta FROM Valutas WHERE id='".$data['id']."'";
  $db->SQL($query);
  $dbRecord=$db->lookupRecord();
}

//$editObject->usetemplate = true;
$editObject->controller($action,$data);
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $huidigeCategorie=$object->get('Valuta');
  if($huidigeCategorie <> '' && $huidigeCategorie <> $dbRecord['Valuta'])
  {
    updateVermogensbeheerderKleuren('OIV',$dbRecord['Valuta'],$huidigeCategorie);
  }
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>