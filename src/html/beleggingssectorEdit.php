<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.6 $
 		
 		$Log: beleggingssectorEdit.php,v $
 		Revision 1.6  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.5  2015/08/30 11:42:24  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2006/12/21 16:13:11  rvv
 		attributie
 		
 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("beleggingssector muteren");

$__funcvar['listurl']  = "beleggingssectorList.php";
$__funcvar['location'] = "beleggingssectorEdit.php";

$object = new Beleggingssector();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];
$db=new DB();
if($data['id'] > 0)
{
  $query="SELECT id,Beleggingssector FROM Beleggingssectoren WHERE id='".$data['id']."'";
  $db->SQL($query);
  $dbRecord=$db->lookupRecord();
}

//$editObject->usetemplate = true;
$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $huidigeCategorie=$object->get('Beleggingssector');
  if($huidigeCategorie <> '' && $huidigeCategorie <> $dbRecord['Beleggingssector'])
  {
    updateVermogensbeheerderKleuren('OIS',$dbRecord['Beleggingssector'],$huidigeCategorie);
  }
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>