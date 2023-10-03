<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 augustus 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/01/19 15:43:34 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: vrageningevuldEditcvs.php,v $
    Revision 1.1  2018/01/19 15:43:34  cvs
    x

    Revision 1.6  2014/11/19 16:41:12  rvv
    *** empty log message ***

    Revision 1.5  2014/08/25 10:12:01  rvv
    *** empty log message ***

    Revision 1.4  2014/08/23 15:36:34  rvv
    *** empty log message ***

    Revision 1.3  2014/08/09 15:05:41  rvv
    *** empty log message ***

    Revision 1.2  2014/08/03 13:21:03  rvv
    *** empty log message ***

    Revision 1.1  2014/08/03 13:14:10  rvv
    *** empty log message ***

 	
*/


////////////////////////
///

include_once("wwwvars.php");
include_once("../classes/editObject.php");



$subHeader = "";
$mainHeader    = vt("Vragenlijst");

$__funcvar['listurl']  = "vrageningevuldList.php";
$__funcvar['location'] = "vrageningevuldEdit.php";

$object = new VragenIngevuld();

$editObject = new editObject($object);

$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$db=new DB();

if($action == 'edit')
{
  $query="SELECT vragenlijstId,relatieId,date(add_date) as datum FROM VragenIngevuld WHERE id='".$data['id']."'";
  $tmp=$db->lookupRecordByQuery($query);
  $data['vragenlijstId']=$tmp['vragenlijstId'];
  $data['relatieId']=$tmp['relatieId'];
  $data['datum']=$tmp['datum'];
  $_SESSION["vrageningevuldData"] = $data;
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "vrageningevuldEditTemplate.html";

$editObject->controller($action,$data);


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
