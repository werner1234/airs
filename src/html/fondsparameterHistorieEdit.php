<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 24 januari 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: fondsparameterHistorieEdit.php,v $
    Revision 1.4  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.3  2017/09/16 18:05:29  rvv
    *** empty log message ***

    Revision 1.2  2015/02/18 17:08:08  rvv
    *** empty log message ***

    Revision 1.1  2015/02/15 10:17:10  rvv
    *** empty log message ***

    Revision 1.1  2015/01/24 19:51:53  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "fondsparameterHistorieList.php";
$__funcvar['location'] = "fondsparameterHistorieEdit.php";

$object = new FondsParameterHistorie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


if($_GET['action']=='kopieerFondsparametes')
{

//  $_SESSION['NAV']->returnUrl =$_SERVER['HTTP_REFERER'];
 // listarray($_SERVER);
  $_GET['action']='edit';
  $_GET['id']='';
  $dateFields=array('date','datetime');
  foreach($object->data['fields'] as $key=>$fieldValues)
  {
    if(in_array($fieldValues['db_type'],$dateFields))
      $value=implode("-",array_reverse(explode("-",$_GET[$key])));
    else
      $value=$_GET[$key];
      
    if($key=='GebruikTot')
      $value=date("Y-m-d",time()-86400);
      
    $object->set($key,$value);
  }
}

$editcontent['javascript']=str_replace('//check values ?','var theForm = document.editForm.elements, z = 0;for(z=0; z<theForm.length;z++){if(theForm[z].disabled == true){theForm[z].disabled = false;}}',$editcontent['javascript']);
//listarray($editcontent);
//listarray($_GET);exit;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

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
